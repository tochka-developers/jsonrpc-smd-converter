<?php

namespace Tochka\JsonRpcSmdConverter;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;

class Loader
{

    public static function save($path, $data)
    {
        File::put(base_path($path), json_encode($data));
    }

    public static function loadFromPath($path) {
        return \json_decode(File::get($path), true);
    }

    /**
     * @param $path
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function loadFromUrl($path) {
        $client = new Client([
            'base_uri' => $path,
        ]);
        $response = $client->request('POST', '?smd');

        return json_decode($response->getBody(), true);
    }
}