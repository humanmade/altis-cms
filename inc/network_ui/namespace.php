<?php

namespace Altis\CMS\Network_UI;

use Altis;
use WP_Site;

/**
 * Bootstrap.
 */
function bootstrap() {
	add_filter( 'wpmu_blogs_columns', __NAMESPACE__ . '\\override_columns' );
	add_filter( 'manage_sites_custom_column', __NAMESPACE__ . '\\handle_column', 10, 2 );
	add_filter( 'manage_sites_action_links', __NAMESPACE__ . '\\change_row_actions', 0, 2 );
}

/**
 * Get configuration for the network UI.
 *
 * @return array
 */
function get_config() : array {
	return Altis\get_config()['modules']['cms']['network-ui'];
}

/**
 * Override the columns in the site list.
 *
 * @param array $columns Columns provided by WordPress.
 * @return array Overridden columns.
 */
function override_columns( array $columns ) : array {
	$before = array_slice( $columns, 0, 1 );
	$extra = [
		'altis_name' => 'Name',
	];
	$after = array_slice( $columns, 2 );
	return array_merge( $before, $extra, $after );
}

/**
 * Handle rendering a column.
 *
 * Handles any custom columns we have.
 *
 * @param string $column Column ID to render.
 * @param string|int $id Site ID being rendered.
 */
function handle_column( string $column, $id ) : void {
	if ( $column === 'altis_name' ) {
		render_name_column( $id );
	}
}

/**
 * Change row actions.
 *
 * WordPress convention is that the first action in the row actions matches the
 * action of the title, so we need to move Dashboard to the first position.
 *
 * Also hides unwanted actions per config.
 *
 * @param array $actions Actions to render.
 * @param string|int $id Site ID being rendered.
 * @return array Overridden actions.
 */
function change_row_actions( array $actions, $id ) {
	$prefix = [
		'backend' => $actions['backend'],
	];
	unset( $actions['backend'] );
	$actions = array_merge( $prefix, $actions );

	$config = get_config();
	$site = get_site( $id );

	// Hide "Spam", unless the site is marked as spam (i.e. action is "Not Spam")
	if ( $config['disable-spam'] && ! $site->spam ) {
		unset( $actions['spam'] );
	}
	return $actions;
}

/**
 * Render the Name column.
 *
 * @param string|int $id Site ID being rendered.
 * @return void Outputs directly.
 */
function render_name_column( $id ) : void {
	global $mode;
	$blog = get_site( $id );

	$shorturl = untrailingslashit( $blog->domain . $blog->path );
	if ( $blog->path !== '/' ) {
		$shorturl = trailingslashit( $shorturl );
	}

	// Gather information.
	switch_to_blog( $blog->id );
	$name = get_option( 'blogname' );
	if ( $mode !== 'list' ) {
		$desc = get_option( 'blogdescription' );
	}
	restore_current_blog();

	printf(
		'<strong class="altis-site-name"><a href="%s" class="edit" title="%s">%s</a></strong>',
		esc_url( get_admin_url( $id ) ),
		esc_html__( 'Open site dashboard', 'altis' ),
		esc_html( $name )
	);
	printf(
		' &mdash; <span class="altis-site-shorturl">%s</span>',
		esc_html( $shorturl )
	);

	render_site_states( $blog );

	if ( 'list' !== $mode ) {
		printf(
			'<p><em>%s</em></p>',
			esc_html( $desc )
		);
	}
}

/**
 * Render the states of a site in the table.
 *
 * @param WP_Site $site Site being rendered.
 * @return void Outputs directly.
 */
function render_site_states( WP_Site $site ) : void {
	$site_states = [];
	$wp_list_table = _get_list_table( 'WP_MS_Sites_List_Table' );

	if ( is_main_site( $site->id ) ) {
		$site_states['main'] = __( 'Primary site', 'altis' );
	}
	if ( ! $site->public ) {
		$site_states['private'] = __( 'Private', 'altis' );
	}

	reset( $wp_list_table->status_list );

	$site_status = isset( $_REQUEST['status'] ) ? wp_unslash( trim( $_REQUEST['status'] ) ) : '';
	foreach ( $wp_list_table->status_list as $status => $col ) {
		if ( ( 1 === intval( $site->{$status} ) ) && ( $site_status !== $status ) ) {
			$site_states[ $col[0] ] = $col[1];
		}
	}

	/**
	 * Filter the default site display states for items in the Sites list table.
	 *
	 * @since 5.3.0
	 *
	 * @param array $site_states An array of site states. Default 'Main',
	 *                           'Archived', 'Mature', 'Spam', 'Deleted'.
	 * @param WP_Site $site The current site object.
	 */
	$site_states = apply_filters( 'display_site_states', $site_states, $site );

	if ( ! empty( $site_states ) ) {
		$state_count = count( $site_states );
		$i           = 0;
		echo ' <em>(';
		foreach ( $site_states as $state ) {
			++$i;
			printf(
				'<span class="post-state">%s%s</span>',
				esc_html( $state ),
				$i === $state_count ? '' : ', '
			);
		}
		echo ')</em>';
	}
}
