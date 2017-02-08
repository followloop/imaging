<?php

namespace LOOP\Imaging\Services\Validation\src;

use LOOP\Imaging\Services\Validation\ImageValidatorInterface;
use LOOP\ValidationService\src\LaravelValidator;

/**
 * Class ImageValidatorLaravel
 * @package LOOP\Imaging\Services\Validation\src
 */
class ImageValidatorLaravel extends LaravelValidator implements ImageValidatorInterface
{

    /**
     * @return array
     */
    public function imageCreation()
    {
        return [
            'filename' => 'required',
            'path' => 'required',
            'type' => 'sometimes|max:25'
        ];
    }

    /**
     * @return array
     */
    public function existsById()
    {
        return [
            'id' => 'required|exists:images,id,deleted_at,NULL'
        ];
    }


}