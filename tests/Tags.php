<?php

use artisangang\shortly\Tag;

class SubscribeTag extends Tag
{

    public function name()
    {
        return 'subscribe';
    }

    public function attributes()
    {
        return [];
    }

    public function parse($content = null)
    {
        return 'SubscribeTag';
    }

}


class GreetingsTag extends Tag
{

    public function name()
    {
        return 'greetings';
    }

    public function attributes()
    {
        return [];
    }

    public function parse($content = null)
    {
        return "Hi $content, How are you?";
    }

}


