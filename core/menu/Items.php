<?php

namespace Dev4Press\Plugin\NavMenuEnhanced\Menu;

use stdClass;
use Walker_Nav_Menu_Checklist;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Items {
	public function __construct() {
	}

	public static function instance() : Items {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Items();
			$instance->run();
		}

		return $instance;
	}

	private function run() {
		add_action( 'admin_init', array( $this, 'register_metaboxes' ) );
		add_filter( 'wp_get_nav_menu_items', array( $this, 'items_processing' ), 10, 3 );
	}

	public function items_processing( $items, $menu, $args ) {
		foreach ( $items as &$item ) {
			if ( $item->object === 'navmenu-extra' ) {
				switch ( $item->type ) {
					case 'navxtra-login':
						$item->url = wp_login_url();
						break;
					case 'navxtra-logout':
						$item->url = wp_logout_url();
						break;
					case 'navxtra-login-back':
						$item->url = wp_login_url( $this->current_url_request() );
						break;
					case 'navxtra-logout-back':
						$item->url = wp_logout_url( $this->current_url_request() );
						break;
					case 'navxtra-register':
						$item->url = wp_registration_url();
						break;
				}
			}
		}

		return $items;
	}

	public function register_metaboxes() {
		add_meta_box( 'navmenu-items-more', __( "Additional Items", "gd-bbpress-toolbox" ), array(
			$this,
			'add_items_more'
		), 'nav-menus', 'side' );
	}

	public function add_items_more() {
		$list  = array();
		$items = array(
			array(
				'group' => 'access',
				'name'  => 'navxtra-login',
				'title' => __( "Login", "gd-bbpress-toolbox" )
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-login-back',
				'title' => __( "Login with Redirect Back", "gd-bbpress-toolbox" )
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-logout',
				'title' => __( "Logout", "gd-bbpress-toolbox" )
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-logout-back',
				'title' => __( "Logout with Redirect Back", "gd-bbpress-toolbox" )
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-register',
				'title' => __( "Register", "gd-bbpress-toolbox" )
			)
		);

		foreach ( $items as $el ) {
			$item = new stdClass();

			$item->classes   = array();
			$item->type      = $el['name'];
			$item->object_id = $el['name'];
			$item->title     = $el['title'];
			$item->object    = 'navmenu-extra';

			$item->menu_item_parent = null;
			$item->url              = null;
			$item->xfn              = null;
			$item->db_id            = null;
			$item->target           = null;
			$item->attr_title       = null;

			$list[ $el['group'] ][ $el['name'] ] = $item;
		}

		$walker = new Walker_Nav_Menu_Checklist( array() );

		?>

        <div id="navmenu-extras" class="posttypediv">
            <ul class="taxonomy-tabs add-menu-item-tabs" id="taxonomy-category-tabs">
                <li class="tabs"><?php _e( "Extra Pages", "gd-bbpress-toolbox" ); ?></li>
            </ul>
            <div id="tabs-panel-navmenu-extras" class="tabs-panel tabs-panel-active">
                <h4><?php _e( "Account Access", "gd-bbpress-toolbox" ); ?></h4>
                <ul id="navmenu-extras-checklist-access" class="categorychecklist form-no-clear">
					<?php

					echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $list['access'] ), 0, (object) array( 'walker' => $walker ) );

					?>
                </ul>
            </div>
        </div>
        <p class="button-controls">
		    <span class="add-to-menu">
		        <input type="submit" class="button-secondary submit-add-to-menu" value="<?php esc_attr_e( "Add to Menu", "gd-bbpress-toolbox" ); ?>" name="add-navmenu-extras-menu-item" id="submit-navmenu-extras"/>
		        <span class="spinner"></span>
		    </span>
        </p>

		<?php
	}

	private function current_url_request() : string {
		$path_info = $_SERVER['PATH_INFO'] ?? '';
		list( $path_info ) = explode( '?', $path_info );
		$path_info = str_replace( '%', '%25', $path_info );

		$request         = explode( '?', $_SERVER['REQUEST_URI'] );
		$req_uri         = $request[0];
		$req_query       = $request[1] ?? false;
		$home_path       = parse_url( home_url(), PHP_URL_PATH );
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

		return home_url( $url_request );
	}
}