<?php

namespace LOOP\Imaging\Listeners\Traits;

use LOOP\Imaging\Models\Image;

/**
 * Class ProcessesImageAndMovesResultsToCloud
 * @package LOOP\Imaging\Listeners\Traits
 */
trait ProcessesImage
{
    /**
     * @param Image $image
     * @param $sizes
     */
    public function processImage( Image $image, $sizes )
    {
        if ( !is_array( $sizes ) ) $sizes = [];

        $this->imageService->processImage( $image, $sizes );
    }
}