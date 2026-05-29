<?php

namespace App\Service;

use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct(string $cloudinaryUrl)
    {
        $this->cloudinary = new Cloudinary($cloudinaryUrl);
    }

    /**
     * Upload a file to Cloudinary and return the secure URL.
     *
     * @param UploadedFile $file
     * @param string $folder e.g. 'products', 'profiles', 'characters'
     * @return string secure URL
     */
    public function upload(UploadedFile $file, string $folder): string
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder' => $folder,
                'resource_type' => 'image',
            ]
        );

        return $result['secure_url'];
    }

    /**
     * Delete an image from Cloudinary by its stored URL.
     * Extracts the public_id from the URL automatically.
     */
    public function delete(string $url): void
    {
        // Extract public_id from URL: everything after /upload/vXXXXX/ up to the extension
        if (preg_match('/\/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $url, $matches)) {
            $this->cloudinary->uploadApi()->destroy($matches[1]);
        }
    }
}
