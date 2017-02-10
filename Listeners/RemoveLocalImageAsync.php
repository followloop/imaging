<?php

namespace LOOP\Imaging\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use LOOP\Imaging\Events\ImageWasMovedToCloud;
use LOOP\Imaging\Listeners\Traits\RemovesLocalImage;

/**
 * Class RemoveLocalImageAsync
 * @package LOOP\Imaging\Listeners
 */
class RemoveLocalImageAsync implements ShouldQueue
{
    use RemovesLocalImage;

    /**
     * @param ImageWasMovedToCloud $event
     */
    public function handle( ImageWasMovedToCloud $event )
    {
        $this->removeLocalImage( $event->image );
    }
}
