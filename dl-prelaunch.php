<?php

use DLPL\Prelaunch\Menu;
use DLPL\Prelaunch\Editor;
use DLPL\Prelaunch\Entity;
use DLPL\Prelaunch\Utils\Debug;
use DLPL\Prelaunch\Utils\Favicon;
use DLPL\Prelaunch\Utils\H;
use DLPL\Prelaunch\Utils\H1;
use DLPL\Prelaunch\Utils\HashLinks;
use DLPL\Prelaunch\Utils\Links;
use DLPL\Prelaunch\Utils\MetaDescription;
use DLPL\Prelaunch\Utils\MetaTitle;
use DLPL\Prelaunch\Utils\Noindex;
use DLPL\Prelaunch\Utils\Robots;
use DLPL\Prelaunch\Utils\Sitemap;

/**
 * Plugin Name: Prelaunch
 * Description: Pre-checks for the launching of a website
 * Version: 1.1.1
 * Author: Daniel Lucia
 * Author URI: https://daniellucia.es
 * License: GPL2
 * Text Domain: dl-prelaunch
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

define('DL_PRELAUNCH_FILE', plugin_basename(__FILE__));
define('DL_PRELAUNCH_FULL_PATH_FILE', plugin_dir_path(__FILE__) . basename(__FILE__));

$entity = new Entity();
register_activation_hook(__FILE__, [$entity, 'activate']);
register_deactivation_hook(__FILE__, [$entity, 'deactivate']);

add_action('plugins_loaded', function () {

    load_plugin_textdomain(
        'dl-prelaunch',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );

    new Menu();
    new Editor();

    //Utils
    new H();
    new MetaTitle();
    new MetaDescription();
    new Links();
    new HashLinks();
    new Noindex();
    new Favicon();
    new Robots();
    new Sitemap();
    //new Debug();
});
