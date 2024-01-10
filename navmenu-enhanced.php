<?php

/**
 * Plugin Name:       NavMenu Enhanced
 * Plugin URI:        https://github.com/dev4press/navmenu-enhanced/
 * Description:       Add new menu items to the WordPress Navigation Menus interface for login, logout, registration, and more (with or without a current URL redirect).
 * Author:            Milan Petrovic
 * Author URI:        https://www.dev4press.com/
 * Text Domain:       navmenu-enhanced
 * Version:           1.1
 * Requires at least: 5.8
 * Tested up to:      6.4
 * Requires PHP:      7.4
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 *
 * == Copyright ==
 * Copyright 2008 - 2024 Milan Petrovic (email: support@dev4press.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 */

use Dev4Press\Plugin\NavMenuEnhanced\Basic\Plugin;

const NAVMENUENHANCED_VERSION = '1.1';

$navmenuenhanced_dirname_basic = dirname( __FILE__ ) . '/';
$navmenuenhanced_urlname_basic = plugins_url( '/', __FILE__ );

define( 'NAVMENUENHANCED_PATH', $navmenuenhanced_dirname_basic );
define( 'NAVMENUENHANCED_URL', $navmenuenhanced_urlname_basic );

require_once( NAVMENUENHANCED_PATH . 'core/autoload.php' );

Plugin::instance();
