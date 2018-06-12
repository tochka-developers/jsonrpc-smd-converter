<?php


namespace Tochka\JsonRpcSmdConverter;


class Command extends \Illuminate\Console\Command
{
    protected $signature = 'jsonrpc:convert {source?} {format?} ';

    protected $description = 'JsonRpcSmdConverter';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $sources = config('jsonrpcsmdconverter.sources', null);
        throw_if(is_null($sources), new \RuntimeException('No have sources in config'));
        $source = $this->findSource($sources);

        $formats = config('jsonrpcsmdconverter.formats', null);
        throw_if(is_null($sources), new \RuntimeException('No have formats in config'));
        $format = $this->findFormat($formats);

        foreach ($source as $s) {
            foreach ($format as $f) {
                Converter::make($s, $f);
                $this->output->success('Make '.$f.' for '.$s);
            }
        }

    }

    protected function findSource(array $sources): array
    {
        $sourceName = $this->argument('source');
        // если не указан соурс и соурс только один
        if(is_null($sourceName) && count($sources) === 1) {
            return array_keys($sources);
        }
        if($sourceName === 'all') {
            return array_keys($sources);
        }
        if(!is_null($sourceName) && array_key_exists($sourceName, $sources)) {
            return [$sourceName];
        }
        return [$this->choice('Select source', array_keys($sources))];
    }

    protected function findFormat(array $formats): array
    {
        $formatName = $this->argument('format');
        // если не указан формат и формат только один
        if(is_null($formatName) && count($formats) === 1) {
            return array_values($formats);
        }
        if($formatName === 'all') {
            return array_values($formats);
        }
        if(!is_null($formatName) && array_key_exists($formatName, $formats)) {
            return [$formats[$formatName]];
        }
        return [$formats[$this->choice('Select format', array_keys($formats))]];
    }
}