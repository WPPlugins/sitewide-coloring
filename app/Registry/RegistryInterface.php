<?php

namespace SiteWideColoring\App\Registry;

interface RegistryInterface
{
    public function set($name, $definition);

    public function get($name);
}
