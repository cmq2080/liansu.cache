<?php
namespace liansu\traits;

trait TCacheHandlerDir
{
    protected $directory = __DIR__ . '/../../../../../runtime/tmp/cache';

    /**
     * @return string
     */
    public function getDir(): string
    {
        return $this->directory;
    }

    /**
     * @param string $cacheDir
     * @return \liansu\interfaces\ICacheHandler
     */
    public function setDir($cacheDir)
    {
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        $this->directory = $cacheDir;
        return $this;
    }
}