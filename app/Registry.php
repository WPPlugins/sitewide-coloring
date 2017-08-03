<?php

namespace SiteWideColoring\App;

use SiteWideColoring\App\Registry\RegistryInterface;
use SiteWideColoring\App\Registry\RegistryAwareInterface;

class Registry implements RegistryInterface
{
    protected $_instances = [];
    protected $_definitions = [];

    public function __construct(array $config)
    {
        $this->_definitions = $config;
    }

    public function set($name, $definition)
    {
        $this->_definitions[$name] = $definition;
    }

    public function get($name)
    {
        if (!isset($this->_instances[$name])) {
            $this->_instances[$name] = $this->_instantiate($name);
        }

        return $this->_instances[$name];
    }

    protected function _instantiate($name)
    {
        $definition = $this->_definitions[$name];

        if (is_callable($definition)) {
            $instance = call_user_func_array($definition, [$this]);
        } elseif (is_string($definition) && class_exists($definition)) {
            $instance = new $definition;
        } else {
            $instance = $definition;
        }

        if ($instance instanceof RegistryAwareInterface) {
            $instance->setRegistry($this);
        }

        return $instance;
    }
}