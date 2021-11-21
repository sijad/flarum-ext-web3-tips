<?php

namespace TokenJenny\Web3Tips\Event;

use Flarum\Post\Post;
use Flarum\User\User;

class PostWasTipped
{
    /**
     * @var Post
     */
    public $post;

    /**
     * @var User
     */
    public $user;

    /**
     * @param Post $post
     * @param User $user
     */
    public function __construct(Post $post, User $user)
    {
        $this->post = $post;
        $this->user = $user;
    }
}

