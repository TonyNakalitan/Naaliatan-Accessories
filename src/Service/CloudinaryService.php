<?php

namespace App\Service;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct(
        private string $cloudName,
        private string $apiKey,
        private string $apiSecret,
    ) {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key'    => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => ['secure' => true],
        ]);

        $this->cloudinary = new Cloudinary();
    }

    /**
     * Upload a file to Cloudinary and return the secure URL.
     */
    public function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder'         => $folder,
                'resource_type'  => 'image',
                'transformation' => [['quality' => 'auto', 'fetch_format' => 'auto']],
            ]
        );

        return $result['secure_url'];
    }

    /**
     * Delete an image from Cloudinary by its public_id (extracted from URL).
     */
    public function deleteByUrl(string $url): void
    {
        // Extract public_id from URL: everything after /upload/vXXXX/ up to the extension
        if (preg_match('/\/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $url, $matches)) {
            $this->cloudinary->uploadApi()->destroy($matches[1]);
        }
    }
}
