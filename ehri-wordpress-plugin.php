<?php

/**
 * @wordpress-plugin
 * Plugin Name:       EHRI Wordpress Plugin
 * Plugin URI:        http://github.com/EHRI/ehri-wordpress-plugin
 * Description:       A simple plugin to embed data from the EHRI portal in a Wordpress post.
 * Version:           0.0.1
 * Author:            Mike Bryant
 * Author URI:        http://github.com/mikesname
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ehri-wordpress-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

function activate_ehri_wordpress_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-ehri-wordpress-plugin-activator.php';
    Ehri_Wordpress_Plugin_Activator::activate();
}

function deactivate_ehri_wordpress_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-ehri-wordpress-plugin-deactivator.php';
    Ehri_Wordpress_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ehri_wordpress_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_ehri_wordpress_plugin' );

require plugin_dir_path( __FILE__ ) . 'includes/class-ehri-wordpress-plugin.php';
function run_ehri_wordpress_plugin() {
    $plugin = new Ehri_Wordpress_Plugin();
    $plugin->run();

}

run_ehri_wordpress_plugin();


