<?php

namespace SiteWideColoring\App\Loader;

class Autoloader
{
    protected $_namespaces;

    public function __construct(array $config)
    {
        $this->_namespaces = $config['namespaces'];

        spl_autoload_register([$this, '_autoload']);
    }

    protected function _autoload($className)
    {
        foreach ($this->_namespaces as $namespace => $path) {
            if (strpos($className, $namespace) === 0) {
                require_once $path . '/'
                    . str_replace('\\', '/'
                        , str_replace($namespace . '\\', '', $className))
                    . '.php';
                break;
            }
        }
    }
}