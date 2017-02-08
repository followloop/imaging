<?php

namespace LOOP\Imaging\Services\Validation;

use LOOP\ValidationService\ValidatorInterface;

/**
 * Interface ImageValidatorInterface
 * @package LOOP\Imaging\Services\Validation
 */
interface ImageValidatorInterface extends ValidatorInterface
{
    const IMAGE_CREATION = 'imageCreation';
    const EXISTS_BY_ID = 'existsById';

    public function imageCreation();
    public function existsById();
}