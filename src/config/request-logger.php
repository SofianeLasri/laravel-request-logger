<?php
return [
    'cache_key' => 'requests',
    'cache_ttl' => 3600,
    'models_cache_keys' => [
        'ip_address' => 'ip_address_id_',
        'mime_type' => 'mime_type_id_',
        'url' => 'url_id_',
        'user_agent' => 'user_agent_id_'
    ]
];