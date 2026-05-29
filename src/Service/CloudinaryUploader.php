<?php

namespace App\Service;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudinaryUploader
{
    private Cloudinary $cloudinary;

    public function __construct(string $cloudinaryUrl)
    {
        Configuration::instance($cloudinaryUrl);
        $this->cloudinary = new Cloudinary();
    }

    /**
     * Upload a file to Cloudinary and return the secure URL.
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
     * Delete an image from Cloudinary by its URL.
     */
    public function deleteByUrl(string $url): void
    {
        // Extract public_id from URL: .../folder/filename.ext → folder/filename
        if (preg_match('/\/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $url, $matches)) {
            $this->cloudinary->uploadApi()->destroy($matches[1]);
        }
    }
}
