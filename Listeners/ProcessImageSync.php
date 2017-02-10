<?php

namespace LOOP\Imaging\Listeners;

use LOOP\Imaging\Listeners\Traits\ProcessesImage;
use LOOP\Imaging\Services\ImageServiceInterface;
use LOOP\Imaging\Events\ImageWasCreated;

/**
 * Class ProcessImageAndMoveResultsToCloudSync
 * @package LOOP\Imaging\Listeners
 */
class ProcessImageSync
{
    use ProcessesImage;

    protected $imageService;

    /**
     * @param ImageServiceInterface $imageServiceInterface
     */
    public function __construct(
        ImageServiceInterface $imageServiceInterface
    )
    {
        $this->imageService = $imageServiceInterface;
    }


    /**
     * @param ImageWasCreated $event
     */
    public function handle( ImageWasCreated $event )
    {
        $this->processImage( $event->image, $event->sizes );
    }
}
