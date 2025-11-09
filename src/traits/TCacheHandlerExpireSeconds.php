<?php
namespace liansu\traits;

trait TCacheHandlerExpireSeconds
{
    protected $expireSeconds = -1;

    /**
     * @return string
     */
    public function getExpireSeconds(): int
    {
        return $this->expireSeconds;
    }

    /**
     * @param string $cacheDir
     * @return \liansu\interfaces\ICacheHandler
     */
    public function setExpireSeconds($expireSeconds)
    {
        $expireSeconds = (int) $expireSeconds;
        if ($expireSeconds < 0) { // 过期时间小于0则表示永不过期
            $expireSeconds = -1;
        } else if ($expireSeconds == 0) { // 过期时间不能为0
            throw new \Exception('Invalid Expiring Seconds');
        }
        $this->expireSeconds = $expireSeconds;
        return $this;
    }
}