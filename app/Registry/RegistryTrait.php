<?php

namespace SiteWideColoring\App\Registry;

trait RegistryTrait
{
    protected $_registry;

    public function setRegistry(RegistryInterface $registry)
    {
        $this->_registry = $registry;
    }

    protected function registry($name)
    {
        return $this->_registry->get($name);
    }
}