<?php

namespace SiteWideColoring\App\Service;

use SiteWideColoring\App\Registry\RegistryAwareInterface;
use SiteWideColoring\App\Registry\RegistryTrait;

abstract class AbstractService implements RegistryAwareInterface
{
    use RegistryTrait;
}