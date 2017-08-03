<?php
/*
Plugin Name: Sitewide Coloring
Description: Color your website with banners just in one click.
Version: 2.0
Author: Mobilunity
License: GPLv2 or later
*/

require_once __DIR__ . '/app/Loader/Autoloader.php';

use SiteWideColoring\App\Loader\Autoloader;
use SiteWideColoring\App\Registry;

final class SiteWideColoring
{
    protected static $_loader = null;
    protected static $_initialized = false;
    protected static $_registry = null;

    public static function initialize()
    {
        $config = require __DIR__ . '/etc/config.php';
        $config['registry'] = array_merge($config['registry'], [
            'request' => 'SiteWideColoring\App\Http\Request',
            'layout' => 'SiteWideColoring\App\View\Layout',
            'renderer' => 'SiteWideColoring\App\View\Renderer',
            'hooks' => 'SiteWideColoring\App\Hook\Manager',
            'db' => function () {
                global $wpdb;

                return $wpdb;
            }
        ]);

        self::$_loader = new Autoloader($config['autoload']);
        self::$_registry = new Registry($config['registry']);
        self::$_registry->set('loader', self::$_loader);
        self::$_registry->set('config', $config);
        self::$_registry->get('hooks')->initialize();
        self::$_registry->get('layout')->initialize();
        self::$_initialized = true;
    }

    public static function get($name)
    {
        return self::$_registry->get($name);
    }
}

SiteWideColoring::initialize();