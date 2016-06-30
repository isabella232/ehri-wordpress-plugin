<?php

class Ehri_Wordpress_Plugin_Public {
    private $plugin_name;
    private $version;
    private $twig;
    private $api_token;
    private $api_url;

    private $TEMPLATES = array(
        'Repository' => 'institution.twig.html'
    );

    const API_MIMETYPE = 'application/vnd.api+json';

    public function __construct( $plugin_name, $version, $twig ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->twig        = $twig;

        $this->api_token = get_option( 'ehri_api_access_token' );
        $this->api_url   = get_option( 'ehri_api_base_url' );
    }

    function fetch_shortcode( $atts ) {
        $id_atts = shortcode_atts( array( 'id' => '' ), $atts );

        $args = array(
            'headers' => array(
                'Accept'        => self::API_MIMETYPE
            )
        );
        if (!is_null($this->api_token)) {
            $args['headers']['Authorization'] = 'Bearer ' . $this->api_token;
        }

        $url      = $this->api_url . $id_atts["id"];
        $response = wp_remote_request( $url, $args );
        $code     = wp_remote_retrieve_response_code( $response );
        $body     = wp_remote_retrieve_body( $response );
        if ( $code != 200 ) {
            error_log( 'Error retrieving API data [' . $code . ']: ' . $body );

            return '<pre>Error requesting EHRI API data: ' . $code . '</pre>';
        }

        $json = json_decode( $body, true );
        $type = $json['data']['type'];

        return $this->twig->render( $this->TEMPLATES[ $type ], $json['data'] );
    }


    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ehri-wordpress-plugin-public.css',
            array(), $this->version, 'all' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ehri-wordpress-plugin-public.js',
            array( 'jquery' ), $this->version, false );
    }
}
