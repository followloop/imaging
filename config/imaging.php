<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver for image processing
    |--------------------------------------------------------------------------
    | The driver that you want to use to process the images.
    |
    | Accepted values: 'gd', 'imagick'
    |
    */
    'driver' => 'gd',

    /*
    |--------------------------------------------------------------------------
    | Local disk name
    |--------------------------------------------------------------------------
    |
    */
    'local_disk_name' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Local disk URL
    |--------------------------------------------------------------------------
    |
    */
    'local_disk_url' => 'http://temporal-url',

    /*
    |--------------------------------------------------------------------------
    | Cloud disk URL
    |--------------------------------------------------------------------------
    |
    */
    'cloud_disk_name' => '',

    /*
    |--------------------------------------------------------------------------
    | Cloud disk URL
    |--------------------------------------------------------------------------
    |
    */
    'cloud_disk_url' => '',

    /*
    |--------------------------------------------------------------------------
    | Final disk name
    |--------------------------------------------------------------------------
    | Specify the name of the local disk that you are using, if you do so.
    |
    | To see the name, open the config/filesystem.php and check the name that
    | you have there. You can create new disks or use the default one, which
    | is 'local' and defaults to the storage folder.
    |
    */
    //'final_disk' => 'local'

];