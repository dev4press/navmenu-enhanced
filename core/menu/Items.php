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
		add_filter( 'customize_nav_menu_available_item_types', array( $this, 'customizer_types' ) );
		add_filter( 'customize_nav_menu_available_items', array( $this, 'customizer_items' ), 10, 4 );
	}

	private function menu_items( $customizer = false ) : array {
		$list  = array();
		$items = array(
			array(
				'group' => 'access',
				'name'  => 'navxtra-login',
				'title' => __( 'Login', 'navmenu-enhanced' ),
				'xfn'   => 'noindex nofollow',
				'url'   => wp_login_url(),
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-login-back',
				'title' => __( 'Login with Redirect Back', 'navmenu-enhanced' ),
				'xfn'   => 'noindex nofollow',
				'url'   => wp_login_url(),
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-logout',
				'title' => __( 'Logout', 'navmenu-enhanced' ),
				'xfn'   => 'noindex nofollow',
				'url'   => wp_logout_url(),
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-logout-back',
				'title' => __( 'Logout with Redirect Back', 'navmenu-enhanced' ),
				'xfn'   => 'noindex nofollow',
				'url'   => wp_logout_url(),
			),
			array(
				'group' => 'access',
				'name'  => 'navxtra-register',
				'title' => __( 'Register', 'navmenu-enhanced' ),
				'xfn'   => 'noindex nofollow',
				'url'   => wp_registration_url(),
			),
		);

		if ( Helper::is_bbpress_active() ) {
			$items[] = array(
				'group' => 'bbpress',
				'name'  => 'navxtra-bbpress-profile',
				'title' => __( 'bbPress Profile', 'navmenu-enhanced' ),
				'url'   => site_url(),
			);
		}

		if ( $customizer ) {
			foreach ( $items as $el ) {
				$item = array(
					'id'         => 'navmenu-extra-' . $el['name'],
					'type'       => $el['name'],
					'title'      => $el['title'],
					'type_label' => __( 'Dynamic' ),
					'object'     => 'navmenu-extra',
					'object_id'  => $el['name'],
					'url'        => $el['url'] ?? site_url(),
					'xfn'        => $el['xfn'] ?? '',
				);

				$list[] = $item;
			}
		} else {
			foreach ( $items as $el ) {
				$item = array(
					'id'               => 'navmenu-extra-' . $el['name'],
					'classes'          => array(),
					'type'             => $el['name'],
					'type_label'       => __( 'Dynamic' ),
					'object_id'        => $el['name'],
					'title'            => $el['title'],
					'object'           => 'navmenu-extra',
					'menu_item_parent' => null,
					'url'              => null,
					'xfn'              => $el['xfn'] ?? '',
					'db_id'            => null,
					'target'           => null,
					'attr_title'       => null,
				);

				$list[ $el['group'] ][ $el['name'] ] = (object) $item;
			}
		}

		return $list;
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
		$list = $this->menu_items();

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

	public function customizer_types( $item_types ) {
		$item_types[] = array(
			'title'      => __( 'Additional Items', 'navmenu-enhanced' ),
			'type_label' => __( 'Additional Items', 'navmenu-enhanced' ),
			'type'       => 'navmenu-enhanced',
			'object'     => 'navmenu-enhanced',
		);

		return $item_types;
	}

	public function customizer_items( $items, $object_type, $object_name, $page ) {
		if ( $object_type == 'navmenu-enhanced' ) {
			if ( $page > 1 ) {
				return $items;
			}

			return $this->menu_items( true );
		}

		return $items;
	}
}
