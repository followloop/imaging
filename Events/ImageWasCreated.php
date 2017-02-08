<?php

namespace LOOP\Imaging\Events;

use Illuminate\Queue\SerializesModels;
use LOOP\Imaging\Models\Image;

/**
 * Class ImageWasCreated
 * @package LOOP\Imaging\Events
 */
class ImageWasCreated extends Event
{
    use SerializesModels;

    protected $image;
    protected $sizes;

    /**
     * @param Image $image
     * @param array $sizes
     */
    public function __construct( Image $image, array $sizes = [] )
    {
        $this->image = $image;
        $this->sizes = $sizes;
    }

}
