<?php

class Ehri_Wordpress_Plugin_Admin {

    private $plugin_name;

    private $version;

    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

    }

    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ehri-wordpress-plugin-admin.css',
            array(), $this->version, 'all' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ehri-wordpress-plugin-admin.js',
            array( 'jquery' ), $this->version, false );
    }

    public function plugin_menu() {
        add_submenu_page( 'tools.php', 'EHRI Wordpress Plugin Options', 'EHRI Wordpress Plugin',
            'manage_options', 'my-unique-identifier', array($this, 'ehri_wordpress_plugin_options') );
    }

    function ehri_wordpress_plugin_options() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        echo '<div class="wrap">';
        echo '<p>Here is where the form would go if I actually had options.</p>';
        echo '</div>';
    }
}
