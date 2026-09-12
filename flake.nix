{
  nixConfig = {
    extra-substituters = ["https://nix-community.cachix.org"];
    extra-trusted-public-keys = ["nix-community.cachix.org-1:mB9FSh9qf2dCimDSUo8Zy7bkq5CX+/rkCWyvRCYg3Fs="];
  };

  inputs = {
    nixpkgs.url = "https://flakehub.com/f/NixOS/nixpkgs/0.2605";
    flake-utils.url = "github:numtide/flake-utils";
    git-hooks = {
      url = "https://flakehub.com/f/cachix/git-hooks.nix/0.1";
      inputs.nixpkgs.follows = "nixpkgs";
    };
  };

  outputs = {
    self,
    nixpkgs,
    flake-utils,
    git-hooks,
  }:
    flake-utils.lib.eachSystem ["x86_64-linux" "aarch64-linux"] (
      system: let
        pkgs = import nixpkgs {inherit system;};
        php = pkgs.php82.withExtensions (
          {
            enabled,
            all,
          }:
            enabled
            ++ (with all; [
              curl
              intl
              mbstring
              pdo_sqlite
              sqlite3
              zip
            ])
        );
        src = pkgs.lib.cleanSourceWith {
          src = ./.;
          filter = path: _type:
            !builtins.elem (baseNameOf path) [
              ".git"
              ".jj"
              "result"
            ];
        };
        app = php.buildComposerProject2 {
          pname = "musee-lookout";
          version = "1.0.0";
          inherit src;
          vendorHash = "sha256-Z/02n9v+gMdoimQ1757HUxBB9XOmEGw9X8ojQKRjYis=";
          composerNoDev = true;
          composerNoPlugins = true;
          composerNoScripts = true;
          composerStrictValidation = false;
        };
        appRoot = "${app}/share/php/musee-lookout";
        phpFpmConfig = pkgs.writeText "php-fpm.conf" ''
          [global]
          daemonize = yes
          error_log = /proc/self/fd/2
          pid = /tmp/php-fpm.pid

          [www]
          user = nobody
          group = nobody
          listen = 127.0.0.1:9000
          pm = dynamic
          pm.max_children = 8
          pm.start_servers = 2
          pm.min_spare_servers = 1
          pm.max_spare_servers = 3
          catch_workers_output = yes
          clear_env = no
          php_admin_flag[display_errors] = off
          php_admin_flag[log_errors] = on
          php_admin_value[error_log] = /proc/self/fd/2
          php_admin_value[error_reporting] = E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED
          php_value[session.save_path] = /tmp/musee-sessions
        '';
        nginxConfig = pkgs.writeText "nginx.conf" ''
          user nobody nobody;
          daemon off;
          error_log /dev/stderr;
          pid /tmp/nginx.pid;

          events {}

          http {
            access_log /dev/stdout;
            client_body_temp_path /tmp/client-body;
            fastcgi_temp_path /tmp/fastcgi;
            include ${pkgs.nginx}/conf/mime.types;

            server {
              listen 8000 default_server;
              root ${appRoot}/public;
              index index.php;

              location / {
                try_files $uri /index.php$is_args$args;
              }

              location = /api/health {
                default_type application/json;
                return 200 '{"status":"ok"}';
              }

              location ~ \.php$ {
                include ${pkgs.nginx}/conf/fastcgi_params;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                fastcgi_pass 127.0.0.1:9000;
              }

              location ~ /\. {
                deny all;
              }
            }
          }
        '';
        start = pkgs.writeShellApplication {
          name = "musee-lookout";
          runtimeInputs = [php pkgs.coreutils pkgs.nginx];
          text = ''
            export APP_CACHE_DIR="''${APP_CACHE_DIR:-/tmp/musee-cache}"
            export APP_LOG_DIR="''${APP_LOG_DIR:-/tmp/musee-log}"
            mkdir -p "$APP_CACHE_DIR" "$APP_LOG_DIR" /tmp/musee-sessions /tmp/client-body /tmp/fastcgi /var/log/nginx
            chmod 1777 /tmp
            chown -R nobody:nobody "$APP_CACHE_DIR" "$APP_LOG_DIR" /tmp/musee-sessions /tmp/client-body /tmp/fastcgi
            php-fpm --fpm-config ${phpFpmConfig}
            exec nginx -c ${nginxConfig}
          '';
        };
        lint = pkgs.runCommand "musee-lookout-lint" {nativeBuildInputs = [php];} ''
          find ${src} -name '*.php' -type f -print0 | xargs -0 -n1 php -l >/dev/null
          touch $out
        '';
        containerCheck = pkgs.runCommand "musee-lookout-container" {nativeBuildInputs = [php];} ''
          export APP_ENV=prod
          export APP_SECRET=test
          export APP_CACHE_DIR=$TMPDIR/cache
          export APP_LOG_DIR=$TMPDIR/log
          php ${appRoot}/bin/console lint:container
          touch $out
        '';
        dockerImage = pkgs.dockerTools.buildLayeredImage {
          name = "musee-lookout";
          tag = "1.0.0";
          contents = [start pkgs.busybox pkgs.cacert pkgs.dockerTools.fakeNss];
          config = {
            Cmd = ["${start}/bin/musee-lookout"];
            Env = [
              "APP_ENV=prod"
              "APP_NAME=Musée Lookout"
              "APP_SECRET=development-only"
              "SSL_CERT_FILE=${pkgs.cacert}/etc/ssl/certs/ca-bundle.crt"
            ];
            ExposedPorts."8000/tcp" = {};
          };
        };
        preCommitCheck = git-hooks.lib.${system}.run {
          package = pkgs.prek;
          src = ./.;
          hooks = {
            actionlint.enable = true;
            alejandra.enable = true;
            check-added-large-files.enable = true;
            check-merge-conflicts.enable = true;
            check-yaml.enable = true;
            end-of-file-fixer.enable = true;
            shellcheck.enable = true;
            trim-trailing-whitespace.enable = true;
          };
        };
      in {
        packages = {
          default = app;
          inherit dockerImage;
        };
        checks = {
          build = app;
          container = containerCheck;
          inherit dockerImage lint;
          pre-commit = preCommitCheck;
        };
        formatter = pkgs.alejandra;
        devShells.default = pkgs.mkShell {
          packages = [php php.packages.composer] ++ preCommitCheck.enabledPackages;
          inherit (preCommitCheck) shellHook;
        };
      }
    );
}
