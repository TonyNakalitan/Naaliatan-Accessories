<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ImageUrlExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('image_url', [$this, 'getImageUrl']),
        ];
    }

    /**
     * Converts a stored filename to a public URL path.
     *
     * Usage: {{ filename|image_url('images/profiles') }}
     */
    public function getImageUrl(?string $filename, string $folder): string
    {
        if (!$filename) {
            return '';
        }

        // If it's already a full URL (e.g. Google OAuth picture), return as-is
        if (str_starts_with($filename, 'http://') || str_starts_with($filename, 'https://')) {
            return $filename;
        }

        return '/' . ltrim($folder, '/') . '/' . $filename;
    }
}
