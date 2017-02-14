<?php

namespace LOOP\Imaging\Services;

/**
 * Interface ImageServiceInterface
 * @package LOOP\Imaging\Services
 */
interface ImageServiceInterface
{
    public function findImageById( $imageId );
    public function createImage( $imageB64OrUploadedFile, array $options = [] );
    public function deleteImage( $imageId, $skipValidation = TRUE );
    public function processImage( $imageIdOrImage, array $sizes = [] );
    public function destroyImage( $imageIdOrImage );
}