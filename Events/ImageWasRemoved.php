<?php

namespace LOOP\Imaging\Events;

/**
 * Class ImageWasRemoved
 * @package LOOP\Imaging\Events
 */
class ImageWasRemoved extends Event
{
    protected $imageId;

    /**
     * @param Image $imageId
     */
    public function __construct( $imageId )
    {
        $this->imageId = $imageId;
    }

}
