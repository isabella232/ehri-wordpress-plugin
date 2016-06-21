<?php

class Ehri_Wordpress_Plugin_i18n {


	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ehri-wordpress-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
