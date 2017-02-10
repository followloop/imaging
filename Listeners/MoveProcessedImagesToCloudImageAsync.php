<?php

namespace LOOP\Imaging\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use LOOP\Imaging\Events\ImageWasProcessed;
use LOOP\Imaging\Listeners\Traits\MovesProcessedImageToCloud;

/**
 * Class MoveImageToCloudImageAsync
 * @package LOOP\Imaging\Listeners
 */
class MoveProcessedImagesToCloudImageAsync implements ShouldQueue
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
