<?php

namespace SiteWideColoring\App\View;

use SiteWideColoring\App\Registry\RegistryAwareInterface;
use SiteWideColoring\App\Registry\RegistryTrait;

class Renderer implements RegistryAwareInterface
{
    use RegistryTrait;

    public function render($template, $variables = [])
    {
        ob_start();
        $this->output($template, $variables);

        return ob_get_clean();
    }

    public function output($template, $variables = [])
    {
        $config = $this->registry('config');

        extract($variables);
        include $config['pluginDir'] . '/templates/' . $template . '.phtml';
    }
}