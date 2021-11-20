<?php

namespace TokenJenny\Web3Tips;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Post\Post;
use Flarum\User\User;

class Tip extends AbstractModel
{
    use ScopeVisibilityTrait;

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get Transaction Hash
     *
     * @param  string  $value
     * @return string
     */
    public function getTransactionHashAttribute($value)
    {
        return '0x' . bin2hex($value);
    }

    /**
     * Set Transaction Hash
     *
     * @param  string  $value
     * @return void
     */
    public function setTransactionHashAttribute($value)
    {
        $bin = hex2bin(substr($value, 2));
        if (strlen($value) !== 66 || strpos($value, "0x") !== 0 || !$bin) {
            throw new \Exception("transaction hash is not valid");
        }

        $this->attributes['transaction_hash'] = $bin;
    }

    /**
     * Get From Address
     *
     * @param  string  $value
     * @return string
     */
    public function getFromAttribute($value)
    {
        return '0x' . bin2hex($value);
    }

    /**
     * Set From Address
     *
     * @param  string  $value
     * @return void
     */
    public function setFromAttribute($value)
    {
        if (!$value) {
            return;
        }

        $bin = hex2bin(substr($value, 2));
        if (strlen($value) !== 42 || strpos($value, "0x") !== 0 || !$bin) {
            throw new \Exception("from address is not valid");
        }

        $this->attributes['from'] = $bin;
    }

    /**
     * Get To Address
     *
     * @param  string  $value
     * @return string
     */
    public function getToAttribute($value)
    {
        return '0x' . bin2hex($value);
    }

    /**
     * Set To Address
     *
     * @param  string  $value
     * @return void
     */
    public function setToAttribute($value)
    {
        if (!$value) {
            return;
        }

        $bin = hex2bin(substr($value, 2));
        if (strlen($value) !== 42 || strpos($value, "0x") !== 0 || !$bin) {
            throw new \Exception("to address is not valid");
        }

        $this->attributes['to'] = $bin;
    }
}

