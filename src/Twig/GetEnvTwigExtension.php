<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GetEnvTwigExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getenv', [$this, 'getenv']),
        ];
    }

    public function getenv(string $which)
    {
        return getenv($which);
    }
}
