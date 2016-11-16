<?php
/*
 Plugin Name: BEA - Model factory
 Version: 1.0.0
 Version Boilerplate: 3.0.0
 Plugin URI: http://www.beapi.fr
 Description: Add a model factory feature
 Author: BE API Technical team
 Author URI: http://www.beapi.fr
 Domain Path: languages
 Text Domain: bea-model-factory
 Network: true

 ----

 Copyright 2015 BE API Technical team (human@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'BEA_MODEL_FACTORY_VERSION', '1.0.0' );
define( 'BEA_MODEL_FACTORY_MIN_PHP_VERSION', '5.4' );

// Plugin URL and PATH
define( 'BEA_MODEL_FACTORY_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_MODEL_FACTORY_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Check the PHP version defined on the plugin constant
 */
require_once( BEA_MODEL_FACTORY_DIR . 'compat.php' );
add_action( 'admin_init', array( 'BEA\Model_Factory\Compatibility', 'admin_init' ) );

/**
 * Autoload all the things \o/
 */
require_once BEA_MODEL_FACTORY_DIR . 'autoload.php';

add_action( 'plugins_loaded', 'init_bea_model_factory_plugin' );
/**
 * Init the plugin
 */
function init_bea_model_factory_plugin() {
	\BEA\Model_Factory\Main::get_instance();
}
