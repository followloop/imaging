<?php

namespace LOOP\Imaging\Services;

use LOOP\Imaging\Models\Image;

/**
 * Interface ImageServiceInterface
 * @package LOOP\Imaging\Services
 */
interface ImageServiceInterface
{
    public function createImage( $imageB64OrUploadedFile, array $options = [] );
    public function removeImage( $imageId, $skipValidation = TRUE );
    public function processImage( Image $image, array $sizes = [] );
}