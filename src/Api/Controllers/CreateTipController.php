<?php

namespace TokenJenny\Web3Tips\Api\Controllers;

use Carbon\Carbon;
use Flarum\Http\RequestUtil;
use Flarum\Post\CommentPost;
use Flarum\Post\PostRepository;
use Tobscure\JsonApi\Exception\InvalidParameterException;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Illuminate\Support\Arr;
use TokenJenny\Web3Tips\Tip;

class CreateTipController implements RequestHandlerInterface
{
    /**
     * @var PostRepository
     */
    protected $posts;

    /**
     * @param PostRepository $posts
     */
    public function __construct(PostRepository $posts)
    {
        $this->posts = $posts;
    }

    /**
     * @param ServerRequestInterface $request
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $data = Arr::get($request->getParsedBody(), 'data', []);

        $postId = Arr::get($data, 'post_id');
        $post = $this->posts->findOrFail($postId, $actor);

        if (! ($post instanceof CommentPost)) {
            throw new InvalidParameterException;
        }

        $tip = new Tip;
        $tip->post_id = $post->id;
        $tip->transaction_hash = Arr::get($data, 'transaction_id');
        $tip->from = "";
        $tip->to = "";
        $tip->is_confirmed = false;
        $tip->created_at = Carbon::now();
        $tip->save();

        return new EmptyResponse();
    }

}
