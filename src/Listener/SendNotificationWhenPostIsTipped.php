<?php

namespace TokenJenny\Web3Tips\Listener;

use TokenJenny\Web3Tips\Event\PostWasTipped;
use TokenJenny\Web3Tips\Notification\PostTippedBlueprint;
use Flarum\Notification\NotificationSyncer;

class SendNotificationWhenPostIsTipped
{
    /**
     * @var NotificationSyncer
     */
    protected $notifications;

    /**
     * @param NotificationSyncer $notifications
     */
    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(PostWasTipped $event)
    {
        if ($event->post->user && $event->post->user->id != $event->user->id) {
            $this->notifications->sync(
                new PostTippedBlueprint($event->post, $event->user),
                [$event->post->user]
            );
        }
    }
}
