<?php

namespace LOOP\Imaging\Services;

/**
 * Interface ImageServiceInterface
 * @package LOOP\Imaging\Services
 */
interface ImageServiceInterface
{
    /**
     * Finds an Image via the given ID, and returns an Image instance or an array with the errors.
     *
     * @param int $imageId ID of the image to find.
     * @return mixed Image instance if success, array otherwise.
     */
    public function findImageById( $imageId );

    /**
     * Creates a new Image in the system.
     *
     * @param mixed $imageB64OrUploadedFile Anything that can create an image on the Intervention's make() function.
     * @param array $options Array of options when creating the image. The options can be:
     *                  - path => Path where the image needs to be stored.
     *                  - sizes => Associative array of sizes that this image needs to be resized to.
     *                  - type => If the image belongs to a certain type, add it here. Useful to segregate.
     * @return mixed array or false in case of error, instance of the Image, in case of success.
     */
    public function createImage( $imageB64OrUploadedFile, array $options = [] );

    /**
     * Deletes (soft deletes) an image from the database
     *
     * @param int $imageId Id of the image to delete.
     * @param bool|true $skipValidation If this is set to true, the validation will be skipped and the function won't
     * check if the entity exists in the DB.
     * @return mixed true if success, array with error otherwise.
     */
    public function deleteImage( $imageId, $skipValidation = TRUE );

    /**
     * Processes an image stored in the local disk, and resizes it to the given sizes.
     *
     * @param mixed $imageIdOrImage ID of the image, or instance of the image to process.
     * @param array $sizes associative array with the sizes that the image needs to be resized to.
     * @return mixed Image instance.
     */
    public function processImage( $imageIdOrImage, array $sizes = [] );

    /**
     * Destroy an image (and its thumbs) from the disks and from the DB. It cannot be reverted.
     *
     * @param mixed $imageIdOrImage ID or instance of the image to destroy.
     * @return mixed true if success, array with errors otherwise.
     */
    public function destroyImage( $imageIdOrImage );
}