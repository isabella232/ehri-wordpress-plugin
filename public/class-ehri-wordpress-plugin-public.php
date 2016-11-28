<?php

class Ehri_Wordpress_Plugin_Public {
    private $plugin_name;
    private $version;
    private $twig;
    private $api_token;
    private $base_url;
    private $api_path;

    private $TEMPLATES = array(
        'Repository'      => 'institution.twig',
        'HistoricalAgent' => 'authority.twig',
        'DocumentaryUnit' => 'unit.twig',
        'VirtualUnit'     => 'virtual.twig'
    );

    const API_MIMETYPE = 'application/vnd.api+json';

    public function __construct( $plugin_name, $version, $twig ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->twig        = $twig;

        $this->api_token = get_option( 'ehri_api_access_token' );
        $this->base_url   = get_option( 'ehri_portal_base_url' );
        $this->api_path   = get_option( 'ehri_api_path' );
    }

    function fetch_shortcode( $atts ) {
        $id_atts = shortcode_atts( array( 'id' => '' ), $atts );

        $args = array(
            'headers' => array(
                'Accept' => self::API_MIMETYPE
            )
        );
        if ( ! is_null( $this->api_token ) ) {
            $args['headers']['Authorization'] = 'Bearer ' . $this->api_token;
        }

        $url      = $this->base_url . $this->api_path . $id_atts["id"];
        $response = wp_remote_request( $url, $args );
        $code     = wp_remote_retrieve_response_code( $response );
        $body     = wp_remote_retrieve_body( $response );
        if ( $code != 200 ) {
            error_log( 'Error retrieving API data [' . $code . ']: ' . $body );

            return '<pre>Error requesting EHRI API data: ' . $code . '</pre>';
        }

        $json = json_decode( $body, true );
        $type = $json['data']['type'];

        if ( ! array_key_exists( $type, $this->TEMPLATES ) ) {
            return '<pre>Unsupported type: ' . $type . '</pre>';
        }

        $data             = $json['data'];
        $data["baseUrl"]  = $this->base_url;

        // If there is 'included' data at the top level, move it
        // into the main data array...
        if (array_key_exists("included", $json)) {
            $data["included"] = $json["included"];
        }

        return $this->twig->render( $this->TEMPLATES[ $type ], $data );
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
