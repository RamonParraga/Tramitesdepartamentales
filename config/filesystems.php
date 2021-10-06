<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [


        'imgNoticias' => [
            'driver' => 'local',
            'root' => public_path('imgNoticias'),
        ],

        'documentoConfirmacion' => [
            'driver' => 'local',
            'root' => public_path('documentoConfirmacion'),
        ],

        'diskCertificado' => [
            'driver' => 'local',
            'root' => getRutaDucumento(),
        ],

        'diskFormatoRequisitos' => [
            'driver' => 'local',
            'root' => public_path('FormatoRequisitos'),
        ],

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

        'tdFormato' => [
            'driver' => 'local',
            'root' => public_path('tdFormato'),
        ],

        'disksServidorSFTPborradores' => [
            'driver' => 'local',
            'root' => public_path('documentoLocalTemporal'),
        ],
        'disksServidorSFTPcertificado_p12' => [
            'driver' => 'local',
            'root' => public_path('archivos_certificado_p12'),
        ],

        'disksServidorSFTPdocumentos' => [
            'driver' => 'local',
            'root' => storage_path('app/documentos'),
        ],

        'disksDocLocalTemporal' => [
            'driver' => 'local',
            'root' => public_path('documentoLocalTemporal'),
        ],

        'DocumentosTramite' => [
            'driver' => 'local',
            'root' => public_path('DocumentosTramite'),
        ],
        
        'disksDocumentosFirmados' => [
            'driver' => 'local',
            'root' => public_path('documentosFirmar'),
        ],
        

    ],

];
