<?php

class Ehri_Wordpress_Plugin {

    protected $loader;
    protected $plugin_name;
    protected $version;
    private $twig;

    public function __construct() {

        $this->plugin_name = 'ehri-wordpress-plugin';
        $this->version     = '0.0.1';

        $this->load_dependencies();
        $this->twig = $this->initialize_twig();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ehri-wordpress-plugin-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ehri-wordpress-plugin-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ehri-wordpress-plugin-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ehri-wordpress-plugin-public.php';

        /**
         * Load Twig
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

        $this->loader = new Ehri_Wordpress_Plugin_Loader();
    }

    private function initialize_twig() {
        $loader = new Twig_Loader_Filesystem( array(
            plugin_dir_path( dirname( __FILE__ ) ) . 'public/templates',
            plugin_dir_path( dirname( __FILE__ ) ) . 'admin/templates'
        ) );

        $twig = new Twig_Environment( $loader, array(
            'debug' => WP_DEBUG,
            'cache' => false //WP_DEBUG ? false : plugin_dir_path( dirname(__FILE__) ) . 'cache',
        ) );
        $twig->addExtension(new Twig_Extensions_Extension_Text());
        $twig->addExtension(new Twig_Extensions_Extension_Date());

        return $twig;
    }

    private function set_locale() {
        $plugin_i18n = new Ehri_Wordpress_Plugin_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    private function define_admin_hooks() {
        $plugin_admin = new Ehri_Wordpress_Plugin_Admin(
            $this->get_plugin_name(), $this->get_version(), $this->get_twig() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'plugin_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_options' );
    }

    private function define_public_hooks() {

        $plugin_public = new Ehri_Wordpress_Plugin_Public(
            $this->get_plugin_name(), $this->get_version(), $this->get_twig() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        add_shortcode( 'ehri-item-data', array( $plugin_public, 'fetch_shortcode' ) );
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

    public function get_twig() {
        return $this->twig;
    }
}
