<?php

namespace Tochka\JsonRpcSmdConverter;

use GuzzleHttp\Client;

class Loader
{
    /**
     * @param $path
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function load($path): array
    {
        $client = new Client([
            'base_uri' => $path,
        ]);
        $response = $client->request('POST', '?smd');

        return json_decode($response->getBody(), true);
    }

    public function save($path, $data)
    {
        file_put_contents(base_path($path), json_encode($data));
    }
}