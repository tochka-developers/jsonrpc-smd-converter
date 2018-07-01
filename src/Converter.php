<?php

namespace Tochka\JsonRpcSmdConverter;

use Tochka\JsonRpcSmdConverter\Format\IFormat;
use GuzzleHttp\Exception\GuzzleException;

class Converter
{
    /**
     * @param string $source
     * @param string $format
     *
     * @throws \Throwable
     * @throws GuzzleException
     */
    public static function make(string $source, string $format): void
    {
        $config = config('jsonrpcsmdconverter.sources.' . $source);
        throw_if(empty($config), new \InvalidArgumentException('not find config for ' . $source));
        throw_if(!in_array(IFormat::class, class_implements($format)), new \RuntimeException('Bad generator'));

        if ($config['path']) {
            $schema = Loader::loadFromPath($config['path']);
        } else {
            $schema = Loader::loadFromUrl($config['url']);
        }

        $config['source_name'] = $source;
        /** @var IFormat $generator */
        $generator = new $format($schema, $config);
        Loader::save($config['output_file'], $generator->make());

    }
}