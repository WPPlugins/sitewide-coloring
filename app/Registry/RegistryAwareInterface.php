<?php

namespace SiteWideColoring\App\Registry;

interface RegistryAwareInterface
{
    public function setRegistry(RegistryInterface $registry);
}
