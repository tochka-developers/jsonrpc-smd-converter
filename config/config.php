<?php

return [
    'sources' => [
        // source name
        'oc' => [
            // api url
            'url'                 => 'http://example.com/api/v1/public/jsonrpcc',
            // путь к локальной smd схеме если null, схема будет скачиваться с url
            'path'                => 'public/smd.json',
            // файл с результатом относительно корня
            'output_file'         => 'public/example.json',
            // имя переменной которой будет заменён url (http://example.com)
            'host_var_name'       => 'example-host',
            // если для доступа к апи используется ключ, то название ключа, если не указано - заголовок не добавится
            'access_key'          => 'Example-Access-Key',
            // имя переменной для ключа
            'access_key_var_name' => 'example-key',
            // каждый контроллер - отдельная папка
            'use_folders'         => true,
        ],
    ],
    // ковертеры
    'formats' => [
        'postman' => \Tochka\JsonRpcSmdConverter\Format\Postman::class,
    ],
];