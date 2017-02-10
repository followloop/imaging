<?php

namespace LOOP\Imaging\Listeners;

use LOOP\Imaging\Events\ImageWasMovedToCloud;
use LOOP\Imaging\Listeners\Traits\RemovesLocalImage;

/**
 * Class RemoveLocalImageSync
 * @package LOOP\Imaging\Listeners
 */
class RemoveLocalImageSync
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
