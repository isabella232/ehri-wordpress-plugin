<?php

class Ehri_Wordpress_Plugin_Admin {

    private $plugin_name;
    private $version;
    private $twig;

    public function __construct( $plugin_name, $version, $twig ) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->twig        = $twig;
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
        add_options_page( 'EHRI Wordpress Plugin Options', 'EHRI Wordpress Plugin',
            'manage_options', 'ehri-wordpress-plugin', array( $this, 'ehri_wordpress_plugin_options' ) );
    }

    public function register_options() {
        register_setting( 'ehri-wordpress-plugin', 'ehri_api_base_url', array( $this, 'sanitize_api_base_url' ) );
        register_setting( 'ehri-wordpress-plugin', 'ehri_api_access_token' );
        add_settings_section( 'section-one', 'Shortcode', array(
            $this,
            'section_one_callback'
        ), 'ehri-wordpress-plugin' );
        add_settings_field( 'ehri_api_base_url', 'API Base URL', array(
            $this,
            'api_base_url_callback'
        ), 'ehri-wordpress-plugin', 'section-one' );
        add_settings_field( 'ehri_api_access_token', 'Access Token', array(
            $this,
            'api_access_token_callback'
        ), 'ehri-wordpress-plugin', 'section-one' );
    }

    public function sanitize_api_base_url( $url ) {
        return filter_var( $url, FILTER_VALIDATE_URL );
    }

    public function section_one_callback() {
        echo 'EHRI shortcode plugin settings';
    }

    public function api_base_url_callback() {
        $setting = esc_attr( get_option( 'ehri_api_base_url', 'https://portal.ehri-project.eu/api/v1/' ) );
        echo "<input type='text' name='ehri_api_base_url' value='$setting' />";
    }

    public function api_access_token_callback() {
        $setting = esc_attr( get_option( 'ehri_api_access_token' ) );
        echo "<input type='text' name='ehri_api_access_token' value='$setting' />";
    }

    function ehri_wordpress_plugin_options() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        ?>
        <div class="wrap">
            <h2>EHRI Plugin Options</h2>

            <form action="options.php" method="POST">
                <?php settings_fields( 'ehri-wordpress-plugin' ); ?>
                <?php do_settings_sections( 'ehri-wordpress-plugin' ); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
