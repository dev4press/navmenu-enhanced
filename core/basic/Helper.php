<?php

namespace Dev4Press\Plugin\NavMenuEnhanced\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helper {
	public static function is_bbpress_active( $min_version = '2.6' ) : bool {
		if ( self::is_plugin_active( 'bbpress/bbpress.php' ) && function_exists( 'bbp_get_version' ) ) {
			return version_compare( bbp_get_version(), $min_version, '>=' );
		} else {
			return false;
		}
	}

	public static function is_plugin_active( $plugin ) : bool {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || self::is_plugin_active_for_network( $plugin );
	}

	public static function is_plugin_active_for_network( $plugin ) : bool {
		if ( ! is_multisite() ) {
			return false;
		}

		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}

		return false;
	}

	public static function current_url() : string {
		$path_info = $_SERVER['PATH_INFO'] ?? ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput,WordPress.Security.NonceVerification
		list( $path_info ) = explode( '?', $path_info );
		$path_info = str_replace( '%', '%25', $path_info );

		$request         = explode( '?', $_SERVER['REQUEST_URI'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput,WordPress.Security.NonceVerification
		$req_uri         = $request[0];
		$req_query       = $request[1] ?? false;
		$home_path       = wp_parse_url( home_url(), PHP_URL_PATH );
		$home_path       = $home_path ? trim( $home_path, '/' ) : '';
		$home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

		$req_uri = str_replace( $path_info, '', $req_uri );
		$req_uri = ltrim( $req_uri, '/' );
		$req_uri = preg_replace( $home_path_regex, '', $req_uri );
		$req_uri = ltrim( $req_uri, '/' );

		$url_request = $req_uri;

		if ( $req_query !== false ) {
			$url_request .= '?' . $req_query;
		}

		return home_url($url_request);
	}
}
