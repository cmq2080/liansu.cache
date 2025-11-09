<?php

namespace liansu\interfaces;

interface ICacheHandler
{
    public function getExpireSeconds(): int;

    public function set(string $key, $value);

    public function get(string $key, $default);
}