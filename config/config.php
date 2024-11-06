<?php

use W0s1nsk1\TelescopeElasticsearchDriver\AuthMethod;

return [
    'host'     => env('TELESCOPE_ELASTICSEARCH_HOST', 'elasticsearch:9200'),

    'auth' => [
        'method' => AuthMethod::tryFrom(env('TELESCOPE_ELASTICSEARCH_AUTH_METHOD', 'api_key')),
        'api_key' => env('TELESCOPE_ELASTICSEARCH_API_KEY', ''),
        'username' => env('TELESCOPE_ELASTICSEARCH_USERNAME', ''),
        'password' => env('TELESCOPE_ELASTICSEARCH_PASSWORD', ''),
    ],

    'index'    => env('TELESCOPE_ELASTICSEARCH_INDEX', 'telescope'),
];