<?php

namespace LOOP\Imaging\Services;

/**
 * Interface ImageProcessingServiceInterface
 * @package LOOP\Imaging\Services
 */
interface ImageProcessingServiceInterface
{
    public function createImageFromB64StringOrURL( $b64StringOrURL, $destinationFolder );
    public function resizeOrCropImageToSizes( $sourceLocalImage, $destinationFolder, array $sizes, array $extraSettings = [] );
}
