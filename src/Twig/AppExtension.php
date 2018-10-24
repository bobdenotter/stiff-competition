<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('x', [$this, 'x'], ['is_safe' => ['html']]),
        ];
    }

    public function x(string $content): string
    {
        return $content;
    }
}
