<?php

namespace artisangang\shortly;

class ServiceProvider
{

    /**
     * Allow global funcation
     * @var bool
     */
    protected $allow_global_funcs = false;

    /**
     * parse attributes only
     * @var bool
     */
    protected $parse_attributes_only = false;

    /**
     * tags collection
     * @var array
     */
    protected $tags = [];


    /**
     * turn on global functions
     */
    public function allowGlobalFunctions()
    {
        $this->allow_global_funcs = true;
    }

    /**
     * allow functions in attribute only
     * @param bool $bool
     */
    public function parseAttributesOnly($bool = true)
    {
        $this->parse_attributes_only = $bool;
    }

    /**
     * add register tag
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        $this->tags[$tag->name()] = $tag;
    }

    /**
     * parse content
     * @param $content
     * @return mixed
     */
    public function parse($content)
    {

        if ($this->allow_global_funcs === true) {
            $content = $this->parseGlobalFunctions($content);
        }

        $content = $this->parseTags($content);

        return $content;

    }

    /**
     * parse global functions only
     * @param $content
     * @param null $attributes_only
     * @return mixed
     */
    public function parseGlobalFunctions($content, $attributes_only = null)
    {


        $pattern = $this->getGlobalFunctionsPattern();

        preg_match_all($pattern, $content, $matches);


        if (empty($matches) || empty($matches[1])) {
            return $content;
        }

        $patterns = [];

        $replacements = [];


        foreach ($matches[1] as $index => $method) {


            $params = [];

            if (!empty($matches[2])) {
                $params = explode(',', $matches[2][$index]);
            }


            array_push($patterns, $this->getGlobalFunctionsPattern($method));

            $output = "<!--{$method}(" . implode(',', $params) . ")-->";

            if (is_callable($method)) {
                $output = call_user_func_array($method, $params);
            }


            if ($this->parse_attributes_only === true) {
                $output = "\"{$output}\"";
            }
            array_push($replacements, $output);

        }


        return preg_replace($patterns, $replacements, $content);

    }

    /**
     * global functions pattern
     * @param string $func
     * @return string
     */
    protected function getGlobalFunctionsPattern($func = '\w+')
    {

        if ($this->parse_attributes_only === false) {
            return "/\[($func):(.*?)\]/";
        }
        return "/\"\[($func):(.*?)\]\"/";
    }

    /**
     * parse tags
     * @param $content
     * @return mixed
     */
    public function parseTags($content)
    {

        return $this->parseInlineTags($this->parseContentTags($content));

    }

    /**
     * render inline tags
     * @param $content
     * @return mixed
     */
    public function parseInlineTags($content)
    {
        $pattern = $this->getTagsPattern();

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);


        if (empty($matches)) {
            return $content;
        }

        $patterns = [];

        $replacements = [];


        foreach ($matches as $index => $tag) {


            $tagObject = $this->getTag($tag[1]);


            if (!empty($tag[2])) {

                $tagObject->attributes = $this->extractAttributes($tag[2], array_keys($tagObject->attributes()));
            }


            //$output = "<!--[{$tag} ".implode('&', $params)."]-->";


            $output = $tagObject->parse();


            array_push($replacements, $output);
            array_push($patterns, $this->getTagsPattern($tagObject->name()));

        }

        return preg_replace($patterns, $replacements, $content);
    }

    /**
     * inline tags pattern
     * @param null $tags
     * @return string
     */
    protected function getTagsPattern($tags = null)
    {

        if (is_null($tags)) {
            $tags = implode('|', array_keys($this->tags));
        }

        $pattern = "/\[($tags)";
        //$pattern .= "(?![\\w-=:])";
        $pattern .= "(.*?)";
        $pattern .= "\]/";

        return $pattern;
    }

    /**
     * get tag from collection by name
     * @param $key
     * @return mixed
     */
    public function getTag($key)
    {
        return $this->tags[$key];
    }

    /**
     * extract attributes from tags
     * @param $raw_attribs
     * @param $allowed_attributes
     * @return array
     */
    protected function extractAttributes($raw_attribs, $allowed_attributes)
    {


        $attributes = [];
        $allowed_attributes = implode('|', $allowed_attributes);
        preg_match_all("/({$allowed_attributes})=['\"]([^'\"]*)/", $raw_attribs, $params, PREG_SET_ORDER);


        if (!empty($params)) {


            foreach ($params as $row) {
                $attributes[$row[1]] = $row[2];
            }

        }

        return $attributes;

    }

    /**
     * render tags
     * @param $content
     * @return mixed
     */
    public function parseContentTags($content)
    {
        $pattern = $this->getContentTagsPattern();

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);


        if (empty($matches)) {
            return $content;
        }

        $patterns = [];

        $replacements = [];


        foreach ($matches as $index => $tag) {


            $tagObject = $this->getTag($tag[1]);


            if (!empty($tag[2])) {

                $tagObject->attributes = $this->extractAttributes($tag[2], array_keys($tagObject->attributes()));
            }

            //$output = "<!--[{$tag} ".implode('&', $params)."]-->";


            $output = $tagObject->parse($tag[3]);


            array_push($replacements, $output);
            array_push($patterns, $this->getContentTagsPattern($tagObject->name()));

        }

        return preg_replace($patterns, $replacements, $content, 1);
    }

    /**
     * tags pattern
     * @param null $tags
     * @return string
     */
    protected function getContentTagsPattern($tags = null)
    {

        if (is_null($tags)) {
            $tags = implode('|', array_keys($this->tags));
        }

        $pattern = "/\[($tags)";
        //$pattern .= "(?![\\w-=:])";
        $pattern .= "(.*?)";
        $pattern .= "\]";
        $pattern .= "(.*?)";
        $pattern .= "\[\/($tags)";
        $pattern .= "\]/";

        return $pattern;

    }

}