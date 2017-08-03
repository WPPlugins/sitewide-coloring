<?php

namespace SiteWideColoring\App\View;

use SiteWideColoring\App\Registry\RegistryAwareInterface;
use SiteWideColoring\App\Registry\RegistryTrait;

class Layout implements RegistryAwareInterface
{
    use RegistryTrait;

    public function initialize()
    {
        $config = $this->registry('config');

        add_action('wp_enqueue_scripts', function()
        use ($config) {
            if (isset($config['layout']['scripts'])) {
                $this->_registerScripts($config['layout']['scripts']);
            }
            if (isset($config['layout']['styles'])) {
                $this->_registerStyles($config['layout']['styles']);
            }
        });

        if (is_admin()) {
            add_action('admin_enqueue_scripts', function()
            use ($config) {
                if (isset($config['admin']['layout']['scripts'])) {
                    $this->_registerScripts($config['admin']['layout']['scripts']);
                }
                if (isset($config['admin']['layout']['styles'])) {
                    $this->_registerStyles($config['admin']['layout']['styles']);
                }
            });
        }
    }

    protected function _registerScripts($scripts)
    {
        $config = $this->registry('config');

        foreach ($scripts as $scriptId => $scriptUrl) {
            wp_register_script(
                $scriptId, $config['pluginUrl'] . $scriptUrl,
                [], false, true
            );

            wp_enqueue_script($scriptId);
        }
    }

    protected function _registerStyles($styles)
    {
        $config = $this->registry('config');

        foreach ($styles as $styleId => $styleUrl) {
            wp_register_style(
                $styleId, (strpos($styleUrl, '//') !== 0
                ? $config['pluginUrl'] : '')
                . $styleUrl
            );

            wp_enqueue_style($styleId);
        }
    }
}