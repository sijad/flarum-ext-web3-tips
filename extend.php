<?php

namespace TokenJenny\Web3Tips;

use Flarum\Extend;
use Flarum\Api\Serializer\PostSerializer;
use TokenJenny\Web3Tips\Api\Controllers\CreateTipController;
use TokenJenny\Web3Tips\Commands\TipsWorkerCommand;
use TokenJenny\Web3Tips\Event\PostWasTipped;
use TokenJenny\Web3Tips\Listener\SendNotificationWhenPostIsTipped;
use TokenJenny\Web3Tips\Notification\PostTippedBlueprint;
use Flarum\Likes\Event\PostWasLiked;

use Illuminate\Console\Scheduling\Event;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Settings())
        ->serializeToForum('web3tipsChainId', 'tokenjenny-web3-tips.chain_id')
        ->serializeToForum('web3tipsTokenAddress', 'tokenjenny-web3-tips.token_address')
        ->serializeToForum('web3tipsTokenDecimals', 'tokenjenny-web3-tips.token_decimals'),

    (new Extend\ApiSerializer(PostSerializer::class))
        ->attribute('tips', function ($serializer, $post) {
            return $post->tips;
        }),

    (new Extend\Routes('api'))
        ->post('/tips', 'tips.create', CreateTipController::class),

    (new Extend\Console())
        ->command(TipsWorkerCommand::class)
        ->schedule(TipsWorkerCommand::class, function (Event $event) {
            $event
                ->everyFiveMinutes()
                ->onOneServer()
                ->withoutOverlapping();
        }, []),

    (new Extend\Notification())
        ->type(PostTippedBlueprint::class, PostSerializer::class, ['alert']),

    (new Extend\Event())
        ->listen(PostWasTipped::class, SendNotificationWhenPostIsTipped::class)
];
