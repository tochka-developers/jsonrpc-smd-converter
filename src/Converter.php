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
     */
    public static function make(string $source, string $format): void
    {
        $config = config('jsonrpcsmdconverter.sources.' . $source);
        throw_if(empty($config), new \InvalidArgumentException('not find config for ' . $source));
        throw_if(!in_array(IFormat::class, class_implements($format)), new \RuntimeException('Bad generator'));

        $loader = new Loader();
        try {
            $schema = $loader->load($config['url']);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('schema loading error', $e);
        }
        $config['source_name'] = $source;
        /** @var IFormat $generator */
        $generator = new $format($schema, $config);
        $loader->save($config['output_file'], $generator->make());

    }
}