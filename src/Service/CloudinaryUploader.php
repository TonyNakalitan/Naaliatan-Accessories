<?php

namespace App\Service;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudinaryUploader
{
    private ?Cloudinary $cloudinary = null;

    public function __construct(private string $cloudinaryUrl)
    {
    }

    private function getClient(): Cloudinary
    {
        if ($this->cloudinary === null) {
            if (empty($this->cloudinaryUrl)) {
                throw new \RuntimeException('CLOUDINARY_URL is not configured. Please add it to your Railway environment variables.');
            }
            Configuration::instance($this->cloudinaryUrl);
            $this->cloudinary = new Cloudinary();
        }

        return $this->cloudinary;
    }

    /**
     * Upload a file to Cloudinary and return the secure URL.
     */
    public function upload(UploadedFile $file, string $folder): string
    {
        $result = $this->getClient()->uploadApi()->upload(
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
        if (preg_match('/\/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $url, $matches)) {
            $this->getClient()->uploadApi()->destroy($matches[1]);
        }
    }
}
