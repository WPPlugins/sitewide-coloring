<?php

namespace SiteWideColoring\App\Hook;

use SiteWideColoring\App\Registry\RegistryAwareInterface;
use SiteWideColoring\App\Registry\RegistryTrait;
use SiteWideColoring\App\Utility\ArrayUtil;

class Manager implements RegistryAwareInterface
{
    use RegistryTrait;

    public function initialize()
    {
        $config = $this->registry('config');
        $sections = ['actions', 'hooks', 'filters'];

        foreach ($sections as $section) {
            $items = [];

            if (isset($config[$section])) {
                $items = array_merge_recursive($items, $config[$section]);
            }

            if (is_admin() && isset($config['admin'][$section])) {
                $items = array_merge_recursive($items, $config['admin'][$section]);
            }

            foreach ($items as $name => $hooks) {
                if (ArrayUtil::isAssociative($hooks)) {
                    $hooks = [$hooks];
                }

                foreach ($hooks as $hook) {
                    $priority = null;
                    $callback = isset($hook['function'])
                        ? $hook['function'] : [
                            $this->registry($hook['hook']),
                            $hook['action']
                        ];

                    if (isset($hook['priority'])) {
                        $priority = (int) $hook['priority'];
                    }

                    switch ($section) {
                        case 'actions':
                            add_action($name, $callback, $priority);
                            break;
                        case 'filters':
                            add_filter($name, $callback, $priority);
                            break;
                        case 'shortcodes':
                            add_shortcode($name, $callback, $priority);
                            break;
                    }
                }

            }
        }
    }
}