<?php

namespace LOOP\Imaging\Repositories\Eloquent;

use LOOP\Imaging\Models\Image;
use LOOP\Imaging\Repositories\ImageRepositoryInterface;
use LOOP\LaravelRepositories\src\EloquentRepository;

/**
 * Class ImageRepository
 * @package LOOP\Imaging\Repositories\Eloquent
 */
class ImageRepository extends EloquentRepository implements ImageRepositoryInterface
{

    /**
     * @param Image $model
     */
    public function __construct( Image $model )
    {
        parent::__construct( $model );
    }

}