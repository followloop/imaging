<?php

namespace LOOP\Imaging\Events;

use Illuminate\Queue\SerializesModels;
use LOOP\Imaging\Models\Image;

/**
 * Class ImageWasMovedToCloud
 * @package LOOP\Imaging\Events
 */
class ImageWasMovedToCloud extends Event
{
    use SerializesModels;

    protected $image;

    /**
     * @param Image $image
     */
    public function __construct( Image $image )
    {
        $this->image = $image;
    }

}
