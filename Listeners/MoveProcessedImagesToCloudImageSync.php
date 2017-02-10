<?php

namespace LOOP\Imaging\Listeners;

use LOOP\Imaging\Events\ImageWasProcessed;
use LOOP\Imaging\Listeners\Traits\MovesProcessedImageToCloud;

/**
 * Class MoveProcessedImagesToCloudImageSync
 * @package LOOP\Imaging\Listeners
 */
class MoveProcessedImagesToCloudImageSync
{
    use MovesProcessedImageToCloud;

    /**
     * @param ImageWasProcessed $event
     */
    public function handle( ImageWasProcessed $event )
    {
        $this->moveProcessedImageToCloud( $event->image );
    }
}
