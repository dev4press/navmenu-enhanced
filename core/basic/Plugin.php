<?php

namespace Dev4Press\Plugin\NavMenuEnhanced\Basic;

use Dev4Press\Plugin\NavMenuEnhanced\Menu\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {
	public function __construct() {
	}

	public static function instance() : Plugin {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
			$instance->run();
		}

		return $instance;
	}

	public function run() {
		Items::instance();
	}
}
