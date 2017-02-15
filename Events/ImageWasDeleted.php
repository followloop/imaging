<?php

namespace LOOP\Imaging\Events;

/**
 * Class ImageWasDeleted
 * @package LOOP\Imaging\Events
 */
class ImageWasDeleted extends Event
{
    protected $imageId;

    /**
     * @param $imageId
     */
    public function __construct( $imageId )
    {
        $this->imageId = $imageId;
    }

}
