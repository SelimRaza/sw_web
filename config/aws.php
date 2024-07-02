<?php

use Aws\Laravel\AwsServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration options set in this file will be passed directly to the
    | `Aws\Sdk` object, from which all client objects are created. This file
    | is published to the application config directory for modification by the
    | user. The full set of possible options are documented at:
    | http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html
    |
    */
    'credentials' => [
        'key'    => 'DO00LWKLWWVRYNFZXTXU',
        'secret' => 'yG9URXShzsKIGeWO5tixp14xD3Q2h8lrV4hWgVbhWvw',
    ],
    'region' => env('AWS_REGION', 'us-east-1'),
    'version' => 'latest',
    'endpoint' => 'https://sgp1.digitaloceanspaces.com/',
    'ua_append' => [
        'L5MOD/' . AwsServiceProvider::VERSION,
    ],
];
