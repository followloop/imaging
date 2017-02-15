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
    protected $options;

    /**
     * @param Image $image
     * @param array $options
     */
    public function __construct( Image $image, array $options = [] )
    {
        $this->image = $image;
        $this->options = $options;
    }

}
