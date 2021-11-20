<?php

namespace TokenJenny\Web3Tips;

use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Client;

class RpcClient {
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @param SettingsRepositoryInterface $settings
     */
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param string $method
     * @param string $to
     * @param array $params
     */
    public function call($method, $params) {
        $url = $this->settings->get("tokenjenny-web3-tips.rpc_url");

        $client = new Client();

        $res = $client->request('POST', $url, [
            'json' => [
                "jsonrpc" => "2.0",
                "method" => $method,
                "params" => $params,
                "id" => 1
            ],
        ]);

        $body = json_decode($res->getBody(), true);

        return $body['result'];
    }
}
