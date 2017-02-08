<?php

namespace LOOP\Imaging\Services;

/**
 * Interface ImageProcessingServiceInterface
 * @package Editorial\Services\Images
 */
interface ImageProcessingServiceInterface
{
    public function createImageFromB64StringOrURL( $b64StringOrURL, $destinationFolder );
    public function resizeOrCropImageToSizes( $sourceLocalImage, $destinationFolder, array $sizes, array $extraSettings = [] );
}
