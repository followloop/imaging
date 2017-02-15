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
     * @param $options
     */
    public function processImage( Image $image, $options )
    {
        $this->imageService->processImage( $image, $options );
    }
}