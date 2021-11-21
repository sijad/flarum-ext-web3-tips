<?php

namespace TokenJenny\Web3Tips\Commands;

use Carbon\Carbon;
use TokenJenny\Web3Tips\Tip;
use TokenJenny\Web3Tips\RpcClient;
use TokenJenny\Web3Tips\Utils;
use Flarum\User\User;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Console\Command;

class TipsWorkerCommand extends Command
{
    protected $signature = 'tips:process';
    protected $description = 'Process unconfirmed transactions.';

    /**
     * @var RpcClient
     */
    protected $rpcClient;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @param RpcClient $rpcClient
     */
    public function __construct(RpcClient $rpcClient, SettingsRepositoryInterface $settings)
    {
        parent::__construct();
        $this->rpcClient = $rpcClient;
        $this->settings = $settings;
    }

    public function handle()
    {
        $tips = Tip::query()
            ->where('is_confirmed', false)
            ->with(['user', 'post'])
            ->get()
            ->map(function (Tip $tip) {
                $data = $this->rpcClient->call(
                    "eth_getTransactionByHash",
                    [
                        $tip->transaction_hash,
                    ]
                );
                if (empty($data)) {
                    return;
                }

                $input = $data['input'];
                $to = $data['to'];

                if (
                    $data["value"] !== "0x0" ||
                    strpos($input, "0xa9059cbb") !== 0 ||
                    strtolower($to) !== strtolower($this->settings->get("tokenjenny-web3-tips.token_address"))
                ) {
                    return;
                }

                $from = $data['from'];
                $to = '0x' . substr($input,34,40);
                $value = '0x' . substr($input,-64);

                $decimals = intval($this->settings->get("tokenjenny-web3-tips.token_decimals")) ?: 18;
                $value = Utils::parseUnits($value, $decimals);

                $user = User::whereHas('loginProviders', function ($query) use ($from) {
                    $query->where('provider', 'web3');
                    $query->where('identifier', $from);
                })->first();
                $toProvider = $tip->post->user->loginProviders()->where(['identifier' => 'web3', 'identifier' => $to ])->first();

                if (!$user || !$toProvider) {
                    return;
                }

                $tip->user_id = $user->id;
                $tip->from = $from;
                $tip->to = $to;
                $tip->value = $value;
                $tip->block_id = hexdec($data['blockNumber']);
                $tip->is_confirmed = true;
                $tip->save();

                $tip->post->increment('tips');
            });

        $time = Carbon::now()->timestamp;
        Tip::where('created_at', '<=', date('Y-m-d H:i:s', $time - 24 * 60 * 60 * 2))
            ->where('is_confirmed', false)
            ->delete();
    }
}
