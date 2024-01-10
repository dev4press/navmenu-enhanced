<?php

namespace Dev4Press\Plugin\NavMenuEnhanced\Menu;

use Dev4Press\Plugin\NavMenuEnhanced\Basic\Helper;
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
						$item->url = wp_login_url( Helper::current_url() );
						break;
					case 'navxtra-logout-back':
						$item->url = wp_logout_url( Helper::current_url() );
						break;
					case 'navxtra-register':
						$item->url = wp_registration_url();
						break;
					case 'navxtra-bbpress-profile':
						if ( is_user_logged_in() ) {
							if ( Helper::is_bbpress_active() ) {
								$item->url = bbp_get_user_profile_url( bbp_get_current_user_id() );
							} else {
								$item->url = site_url();
							}
						} else {
							$item->url = wp_login_url( Helper::current_url() );
							$item->xfn = 'noindex nofollow';
						}
				}
			}
		}

		return $items;
	}

	public function register_metaboxes() {
		add_meta_box( 'navmenu-items-more', __( 'Additional Items', 'navmenu-enhanced' ), array(
			$this,
			'add_items_more',
		), 'nav-menus', 'side' );
	}

	public function add_items_more() {
		$list  = array();
		$items = array(
			array(
				'group' => 'access',
				'name'  => 'navxtra-login',
				'title' => __( 'Login', 'navmenu-enhanced' ),
				'xfn'   => 'noindex nofollow',
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-login-back',
				'title' => __( 'Login with Redirect Back', 'navmenu-enhanced' ),
				'xfn'   => 'noindex nofollow',
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-logout',
				'title' => __( 'Logout', 'navmenu-enhanced' ),
				'xfn'   => 'noindex nofollow',
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-logout-back',
				'title' => __( 'Logout with Redirect Back', 'navmenu-enhanced' ),
				'xfn'   => 'noindex nofollow',
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-register',
				'title' => __( 'Register', 'navmenu-enhanced' ),
				'xfn'   => 'noindex nofollow',
			),
		);

		if ( Helper::is_bbpress_active() ) {
			$items[] = array(
				'group' => 'bbpress',
				'name'  => 'navxtra-bbpress-profile',
				'title' => __( 'bbPress Profile', 'navmenu-enhanced' ),
				'xfn'   => '',
			);
		}

		foreach ( $items as $el ) {
			$item = new stdClass();

			$item->classes   = array();
			$item->type      = $el['name'];
			$item->object_id = $el['name'];
			$item->title     = $el['title'];
			$item->object    = 'navmenu-extra';

			$item->menu_item_parent = null;
			$item->url              = null;
			$item->xfn              = $el['xfn'];
			$item->db_id            = null;
			$item->target           = null;
			$item->attr_title       = null;

			$list[ $el['group'] ][ $el['name'] ] = $item;
		}

		$walker = new Walker_Nav_Menu_Checklist( array() );

		?>

        <div id="navmenu-extras" class="posttypediv">
            <ul class="taxonomy-tabs add-menu-item-tabs" id="taxonomy-category-tabs">
                <li class="tabs">
                    <a href="#tabs-panel-navmenu-enhanced-access" class="nav-tab-link" data-type="tabs-panel-navmenu-enhanced-access"><?php _e( 'Account', 'navmenu-enhanced' ); ?></a>
                </li>
				<?php if ( ! empty( $list['bbpress'] ) ) { ?>
                    <li>
                        <a href="#tabs-panel-navmenu-enhanced-bbpress" class="nav-tab-link" data-type="tabs-panel-navmenu-enhanced-bbpress"><?php _e( 'bbPress', 'navmenu-enhanced' ); ?></a>
                    </li>
				<?php } ?>
            </ul>
            <div id="tabs-panel-navmenu-enhanced-access" class="tabs-panel tabs-panel-active">
                <ul id="navmenu-extras-checklist-access" class="categorychecklist form-no-clear">
					<?php

					echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $list['access'] ), 0, (object) array( 'walker' => $walker ) );

					?>
                </ul>
            </div>
			<?php if ( ! empty( $list['bbpress'] ) ) { ?>
                <div id="tabs-panel-navmenu-enhanced-bbpress" class="tabs-panel tabs-panel-inactive">
                    <ul id="navmenu-extras-checklist-bbpress" class="categorychecklist form-no-clear">
						<?php

						echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $list['bbpress'] ), 0, (object) array( 'walker' => $walker ) );

						?>
                    </ul>
                </div>
			<?php } ?>
        </div>
        <p class="button-controls wp-clearfix">
            <span class="add-to-menu">
		        <input type="submit" class="button submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'navmenu-enhanced' ); ?>" name="add-navmenu-extras-menu-item" id="submit-navmenu-extras"/>
		        <span class="spinner"></span>
		    </span>
        </p>

		<?php
	}
}
