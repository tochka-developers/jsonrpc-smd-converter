<?php

namespace Tochka\JsonRpcSmdConverter\Format;


/**
 * Class Postman
 * @package App\SmdConverter\Format
 */
class Postman implements IFormat
{
    protected $result;
    protected $schema;
    protected $config;
    protected $folders = [];
    protected $testsArray;

    public function __construct(array $schema, $config)
    {
        $this->schema = $schema;
        $this->config = $config;
    }

    public function make(): array
    {
        $this->header();
        $this->body();

        return $this->result;
    }

    /**
     * header generate
     */
    protected function header(): void
    {
        $this->result['info'] =
            [
                '_postman_id' => null,
                'name'        => $this->schema['description'],
                'schema'      => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ];
    }

    /**
     * body generate
     */
    protected function body(): void
    {
        $items = [];

        foreach ($this->schema['services'] as $service) {
            $item = [];
            $item['name'] = $service['name'];

            $request = [];
            $request['method'] = 'POST';
            $request['header'] = $this->makeRequestHeaders();
            $request['body'] = $this->makeRequestBody($service);
            $request['url'] = $this->makeRequestUrl();
            $request['description'] = $this->makeRequestDescription($service);

            $item['request'] = $request;

            $item['event'][] = $this->makeRequestTests();

            if (!empty($this->config['use_folders'])) {
                $key = $this->findFolderKey($service, $items);
                $items[$key]['item'][] = $item;

            } else {
                $items[] = $item;
            }
        }
        $this->result['item'] = $items;
    }

    /**
     * find folder or create new if not exist
     *
     * @param $service
     * @param $items
     *
     * @return int
     */
    protected function findFolderKey(array $service, &$items): int
    {
        $result = array_search($service['group'], $this->folders);
        if ($result !== false) {
            return $result;
        }
        $newKey = count($this->folders);
        $this->folders[$newKey] = $service['group'];
        $items[$newKey] =
            [
                'name'        => $service['group'],
                'description' => $service['groupName'] ?? '',
                'item'        => [],
            ];

        return $newKey;
    }

    /**
     * @return array
     */
    protected function makeRequestHeaders(): array
    {
        $headers = [
            [
                'key'   => 'Accept',
                'value' => 'application/json',
            ],
            [
                'key'   => 'Content-Type',
                'value' => 'application/json',
            ],
        ];
        if (!empty($this->config['access_key'])) {
            $headers[] = [
                'key'   => $this->config['access_key'],
                'value' => '{{' . $this->config['access_key_var_name'] . '}}',
            ];
        }

        return $headers;
    }

    /**
     * @param $service
     *
     * @return array
     */
    protected function makeRequestBody(array $service): array
    {
        $body = [
            'jsonrpc' => '2.0',
            'method'  => $service['name'],
            'params'  => $this->makeRequestParams($service['parameters']),
            'id'      => 1,
        ];

        return [
            'mode' => 'raw',
            'raw'  => json_encode($body, JSON_PRETTY_PRINT),
        ];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function makeRequestParams(array $params): array
    {
        $paramsRes = [];
        foreach ($params as $p) {
            if(!empty($p['parameters'])) {
                $paramsRes[$p['name']] = $this->makeRequestParams($p['parameters']);
            }else {
                $paramsRes[$p['name']] = $p['example'] ?? $p['types'][0];
            }
        }

        return $paramsRes;
    }

    /**
     * @return array
     */
    protected function makeRequestUrl(): array
    {
        $path = explode('/', $this->schema['target']);
        $protocolWithHost = str_replace('/' . $this->schema['target'], '', $this->config['url']);

        if ($this->config['host_var_name']) {
            $varName = '{{' . $this->config['host_var_name'] . '}}';

            return [
                'raw'  => str_replace($protocolWithHost, $varName, $this->config['url']),
                'host' => [$varName],
                'path' => $path,
            ];
        }

        list($protocol, $host) = explode('://', $protocolWithHost);

        return [
            'raw'      => $this->config['url'],
            'protocol' => $protocol,
            'host'     => [$host],
            'path'     => $path,
        ];
    }

    /**
     * @param array $service
     *
     * @return array
     */
    protected function makeRequestDescription(array $service): array
    {
        $content = $service['description'] ?? '';
        if (!empty($service['warning'])) {
            $content .= '<h5>WARNING<h5> <blockquote>' . $service['warning'] . '</blockquote>';
        }
        if (!empty($service['note'])) {
            $content .= '<h5>NOTE<h5> <blockquote>' . $service['note'] . '</blockquote>';
        }

        return [
            'content' => $content,
            'type'    => 'text/html',
        ];
    }

    /**
     * @return array
     */
    public function makeRequestTests(): array
    {
        return [
            'listen' => 'test',
            'script' => [
                'type' => 'text/javascript',
                'exec' => $this->loadTestFromFile(),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function loadTestFromFile(): array
    {
        $ds = DIRECTORY_SEPARATOR;
        if (empty($this->testsArray)) {
            $tests = file_get_contents(__DIR__ . $ds . '..' . $ds . 'extra' . $ds . 'PostmanTest.js');
            $this->testsArray = explode(PHP_EOL, $tests);
        }

        return $this->testsArray;
    }
}