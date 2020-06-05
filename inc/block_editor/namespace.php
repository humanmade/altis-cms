<?php
/**
 * Altis CMS Block Editor Additions.
 *
 * @package altis/cms
 */

namespace Altis\CMS\Block_Editor;

/**
 * Set up block editor modifications.
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register_block_categories' );
	add_action( 'admin_menu', __NAMESPACE__ . '\\admin_menu', 9 );
	add_filter( 'register_post_type_args', __NAMESPACE__ . '\\show_wp_block_in_menu', 10, 2 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\set_default_editor_preferences' );
}

/**
 * Create the block categories taxonomy.
 */
function register_block_categories() {
	register_taxonomy( 'wp_block_category', 'wp_block', [
		'label' => __( 'Block Categories', 'altis' ),
		'labels' => [
			'name'                       => _x( 'Block Categories', 'taxonomy general name', 'altis' ),
			'singular_name'              => _x( 'Block Category', 'taxonomy singular name', 'altis' ),
			'search_items'               => __( 'Search Block Categories', 'altis' ),
			'popular_items'              => __( 'Popular Block Categories', 'altis' ),
			'all_items'                  => __( 'All Block Categories', 'altis' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Block Category', 'altis' ),
			'update_item'                => __( 'Update Block Category', 'altis' ),
			'add_new_item'               => __( 'Add New Block Category', 'altis' ),
			'new_item_name'              => __( 'New Block Category Name', 'altis' ),
			'separate_items_with_commas' => __( 'Separate block categories with commas', 'altis' ),
			'add_or_remove_items'        => __( 'Add or remove block categories', 'altis' ),
			'choose_from_most_used'      => __( 'Choose from the most used block categories', 'altis' ),
			'not_found'                  => __( 'No block categories found.', 'altis' ),
			'menu_name'                  => __( 'Categories', 'altis' ),
		],
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_in_rest' => true,
		'hierarchical' => true,
		'show_admin_column' => true,
		'rewrite' => false,
	] );
}

/**
 * Update the wp_block post type to display in the admin menu.
 *
 * @param array $args The post type creation args.
 * @param string $post_type The post type name.
 * @return array
 */
function show_wp_block_in_menu( array $args, string $post_type ) {
	if ( $post_type !== 'wp_block' ) {
		return $args;
	}

	if ( function_exists( 'wp_get_current_user' ) && ! current_user_can( 'edit_posts' ) ) {
		return $args;
	}

	$args['show_in_menu'] = true;
	$args['menu_position'] = 24;
	$args['menu_icon'] = 'dashicons-screenoptions';

	return $args;
}

/**
 * Add wp_block to main menu global var.
 *
 * Replicates wp-admin/menu.php line 103-163 without built in post type special cases.
 */
function admin_menu() {
	global $menu, $submenu, $_wp_last_object_menu;

	$ptype = 'wp_block';

	$ptype_obj = get_post_type_object( $ptype );

	// Check if it should be a submenu.
	if ( $ptype_obj->show_in_menu !== true ) {
		return;
	}

	$ptype_menu_position = is_int( $ptype_obj->menu_position ) ? $ptype_obj->menu_position : ++$_wp_last_object_menu; // If we're to use $_wp_last_object_menu, increment it first.
	$ptype_for_id = sanitize_html_class( $ptype );

	$menu_icon = 'dashicons-admin-post';
	if ( is_string( $ptype_obj->menu_icon ) ) {
		// Special handling for data:image/svg+xml and Dashicons.
		if ( 0 === strpos( $ptype_obj->menu_icon, 'data:image/svg+xml;base64,' ) || 0 === strpos( $ptype_obj->menu_icon, 'dashicons-' ) ) {
			$menu_icon = $ptype_obj->menu_icon;
		} else {
			$menu_icon = esc_url( $ptype_obj->menu_icon );
		}
	}

	$menu_class = 'menu-top menu-icon-' . $ptype_for_id;

	$ptype_file = "edit.php?post_type=$ptype";
	$post_new_file = "post-new.php?post_type=$ptype";
	$edit_tags_file = "edit-tags.php?taxonomy=%s&amp;post_type=$ptype";

	$ptype_menu_id = 'menu-posts-' . $ptype_for_id;

	/*
		* If $ptype_menu_position is already populated or will be populated
		* by a hard-coded value below, increment the position.
		*/
	$core_menu_positions = [ 59, 60, 65, 70, 75, 80, 85, 99 ];
	while ( isset( $menu[ $ptype_menu_position ] ) || in_array( $ptype_menu_position, $core_menu_positions, true ) ) {
		$ptype_menu_position++;
	}

	$menu[ $ptype_menu_position ] = [ esc_attr( $ptype_obj->labels->menu_name ), $ptype_obj->cap->edit_posts, $ptype_file, '', $menu_class, $ptype_menu_id, $menu_icon ];
	$submenu[ $ptype_file ][5] = [ $ptype_obj->labels->all_items, $ptype_obj->cap->edit_posts, $ptype_file ];
	$submenu[ $ptype_file ][10] = [ $ptype_obj->labels->add_new, $ptype_obj->cap->create_posts, $post_new_file ];

	$i = 15;
	foreach ( get_taxonomies( [], 'objects' ) as $tax ) {
		if ( ! $tax->show_ui || ! $tax->show_in_menu || ! in_array( $ptype, (array) $tax->object_type, true ) ) {
			continue;
		}

		$submenu[ $ptype_file ][ $i++ ] = [ esc_attr( $tax->labels->menu_name ), $tax->cap->manage_terms, sprintf( $edit_tags_file, $tax->name ) ];
	}
}

/**
 * Queue scripts for setting default block editor preferences in WP 5.4.
 *
 * Disables default fullscreen mode and welcome guide.
 */
function set_default_editor_preferences() {
	global $wp_scripts;

	wp_register_script(
		'altis-default-editor-settings',
		plugin_dir_url( dirname( __FILE__, 2 ) ) . 'assets/editor-settings.js',
		[],
		'2020-06-04-1',
		false
	);
	wp_localize_script(
		'altis-default-editor-settings',
		'altisDefaultEditorSettings',
		[
			'uid' => get_current_user_id(),
		]
	);

	// Add default settings as a dependency of wp-data.
	$wp_scripts->registered['wp-data']->deps[] = 'altis-default-editor-settings';
}
