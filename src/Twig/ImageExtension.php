<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('character_image_url', [$this, 'characterImageUrl']),
            new TwigFunction('product_image_url', [$this, 'productImageUrl']),
        ];
    }

    /**
     * Returns the full URL for a character image.
     * Handles both legacy filenames and Cloudinary URLs.
     */
    public function characterImageUrl(?string $image): ?string
    {
        if (!$image) {
            return null;
        }
        if (str_starts_with($image, 'http')) {
            return $image;
        }
        return '/images/characters/' . $image;
    }

    /**
     * Returns the full URL for a product image.
     * Handles both legacy filenames and Cloudinary URLs.
     */
    public function productImageUrl(?string $image): ?string
    {
        if (!$image) {
            return null;
        }
        if (str_starts_with($image, 'http')) {
            return $image;
        }
        return '/images/products/' . $image;
    }
}
