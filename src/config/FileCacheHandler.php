<?php

namespace liansu\config;

use liansu\interfaces\ICacheHandler;
use liansu\traits\TCacheHandlerDir;
use liansu\traits\TCacheHandlerExpireSeconds;

class FileCacheHandler implements ICacheHandler
{
    use TCacheHandlerExpireSeconds;
    use TCacheHandlerDir;

    public function __construct($cacheDir = null)
    {
        if (!empty($cacheDir)) {
            $this->setDir($cacheDir);
        }
    }

    /**
     * Summary of getSerializedValue
     * @param mixed $value
     * @param bool $isKey
     * @return string
     */
    protected function serialize($value, $isKey = false)
    {
        if ($isKey) {
            $value = md5($value);
            return $value;
        }
        $tag = 's';
        switch (gettype($value)) {
            case "boolean":
                $tag = 'b';
                break;
            case "integer":
                $tag = 'i';
                break;
            case "double":
                $tag = 'f';
                break;
            case "array":
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                $tag = 'a';
                break;
            case "object":
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                $tag = 'o';
                break;
        }

        $value = $tag . base64_encode($value);

        return $value;
    }

    /**
     * Summary of getDeserializedValue
     * @param string $value
     */
    protected function deserialize($value)
    {
        $tag = substr($value, 0, 1);
        $value = base64_decode(substr($value, 1));

        switch ($tag) {
            case 'b':
                $value = (bool) $value;
                break;
            case 'i':
                $value = (int) $value;
                break;
            case 'f':
                $value = (float) $value;
                break;
            case 'a':
                $value = json_decode($value, true);
                break;
            case 'o':
                $value = json_decode($value);
                break;
        }

        return $value;
    }

    public function set(string $key, $value)
    {
        $type = gettype($value);
        if (in_array($type, ['resource', 'resource (closed)', 'NULL', 'unknown type'])) {
            throw new \Exception('Not Supported Type: ' . $type);
        }
        $serializedKey = $this->serialize($key, true);
        $serializedValue = $this->serialize($value);
        $engineDir = $this->getDir();
        if (!is_dir($engineDir)) {
            mkdir($engineDir, 0777, true);
        }
        $filepath = $engineDir . '/' . $serializedKey;

        file_put_contents($filepath, $serializedValue);
    }

    public function get(string $key, $default = null)
    {
        $expireSeconds = $this->getExpireSeconds();
        $serializedKey = $this->serialize($key, true);
        $filepath = $this->getDir() . '/' . $serializedKey;
        if (!is_file($filepath)) {
            return $default;
        }

        $file_mtime = filemtime($filepath);
        if ($expireSeconds > 0 && $file_mtime + $expireSeconds < time()) { // 已经过期了
            return $default;
        }

        $serializedValue = file_get_contents($filepath);
        $content = $this->deserialize($serializedValue);
        return $content;
    }
}
