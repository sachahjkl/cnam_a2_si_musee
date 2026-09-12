      env {
        APP_ENV  = "prod"
        APP_NAME = "Musée Lookout"
      }

      template {
        data = <<EOH
{{ with nomadVar "nomad/jobs/musee-lookout" }}
APP_SECRET={{ .APP_SECRET | toJSON }}
{{ end }}
EOH

        destination          = "secrets/runtime.env"
        env                  = true
        error_on_missing_key = true
        change_mode          = "restart"
      }
