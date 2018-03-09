<?php

namespace artisangang\shortly;

abstract class Tag
{

    public $attributes;

    abstract function name();

    abstract function attributes();

    abstract function parse($content = null);

    /**
     * @param $key
     * @param null $default
     * @return string
     */
    protected function getAttribute($key, $default = null)
    {
        if (!empty($this->attributes[$key])) {
            $default = $this->attributes[$key];
        }
        return trim($default);
    }

}