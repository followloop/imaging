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
    const EXISTS_BY_ID_EVEN_DELETED = 'existsByIdEvenDeleted';

    public function imageCreation();
    public function existsById();
    public function existsByIdEvenDeleted();
}