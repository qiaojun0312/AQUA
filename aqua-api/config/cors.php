<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |

     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'supportsCredentials' => false,
//    'allowedOrigins' => ['*'],
    'allowedOrigins' => ['http://guerlain2.wcampaign.cn'],
//    'allowedOrigins' => ['http://guerlain.wcampaign.cn'],
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['*'],
    'exposedHeaders' => [],
    'maxAge' => 0,
    'hosts' => [],
];

