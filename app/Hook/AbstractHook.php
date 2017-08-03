<?php

namespace SiteWideColoring\App\Hook;

use SiteWideColoring\App\Registry\RegistryAwareInterface;
use SiteWideColoring\App\Registry\RegistryTrait;

abstract class AbstractHook implements RegistryAwareInterface
{
    use RegistryTrait;
}