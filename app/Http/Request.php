<?php

namespace SiteWideColoring\App\Http;

class Request
{
    protected $_params = [];

    public function __construct()
    {
        $this->_params = array_merge(
            $this->_params,
            stripslashes_deep($_REQUEST)
        );
    }

    public function get($param, $default = null)
    {
        if ($this->hasParam($param)) {
            return $this->_params[$param];
        }

        return $default;
    }

    public function hasParam($param)
    {
        return isset($this->_params[$param]);
    }

    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function getUri($addParams = [])
    {
        $uri = $_SERVER['REQUEST_URI'];

        if (count($addParams)) {
            $uri .= ($_SERVER['QUERY_STRING'] ? '&' : '?');
            $uri .= http_build_query($addParams);
        }

        return $uri;
    }

    public function getOriginalUri()
    {
        return trim(
            str_replace(
                'do=submit', '',
                $this->getUri()
            ),
            '&?'
        );
    }

    public function hasBeenSubmitted()
    {
        $uri = $this->getUri();
        $referer = $_SERVER['HTTP_REFERER'];

        return (strpos($referer, $uri) !== false)
            && (strpos($referer, 'do=submit') !== false);
    }
}