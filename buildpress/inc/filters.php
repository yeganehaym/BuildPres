<?php
/**
 * Filters for BuildPress WP theme
 *
 * @package BuildPress
 */



/**
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
if ( ! function_exists( 'buildpress_wp_title' ) && ! function_exists( '_wp_render_title_tag' ) ) {
	function buildpress_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() ) {
			return $title;
		}

		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title = "$title $sep $site_description";
		}

		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 ) {
			$title = "$title $sep " . sprintf( __( 'Page %s', 'buildpress_wp'), max( $paged, $page ) );
		}

		return $title;
	}
	add_filter( 'wp_title', 'buildpress_wp_title', 10, 2 );
}



/**
 * Add shortcodes in widgets
 */
add_filter( 'widget_text', 'do_shortcode' );



if ( ! function_exists( 'add_disabled_editor_buttons' ) ) {
	function add_disabled_editor_buttons($buttons) {
		/**
		 * Add a core button that's disabled by default
		 */
		$buttons[] = 'hr';

		return $buttons;
	}
	add_filter('mce_buttons', 'add_disabled_editor_buttons');
}



/**
 * Custom tag font size
 */
if ( ! function_exists( 'set_tag_cloud_sizes' ) ) {
	function set_tag_cloud_sizes($args) {
		$args['smallest'] = 8;
		$args['largest']  = 12;
		return $args;
	}
	add_filter( 'widget_tag_cloud_args', 'set_tag_cloud_sizes' );
}



/**
 * Custom text after excerpt
 */
if ( ! function_exists( 'buildpress_excerpt_more' ) ) {
	function buildpress_excerpt_more( $more ) {
		return _x( ' &hellip;', 'custom read more text after the post excerpts' , 'buildpress_wp');
	}
	add_filter( 'excerpt_more', 'buildpress_excerpt_more' );
}



/**
 * Add Formats Dropdown Menu To TinyMCE
 */
if ( ! function_exists( 'buildpress_style_select' ) ) {
	function buildpress_style_select( $buttons ) {
		array_push( $buttons, 'styleselect' );
		return $buttons;
	}
}
add_filter( 'mce_buttons', 'buildpress_style_select' );



/**
 * Add new styles to the TinyMCE "formats" menu dropdown
 */
if ( ! function_exists( 'buildpress_styles_dropdown' ) ) {
	function buildpress_styles_dropdown( $settings ) {

		$items = array();
		for ($i=1; $i <= 6; $i++) {
			$items[] = array(
				'title'   => _x( 'Heading', 'backend', 'buildpress_wp' ) . " {$i}",
				'block'   => "h{$i}",
				'classes' => 'alternative-heading'
			);
		}

		// Create array of new styles
		$new_styles = array(
			array(
				'title' => _x( 'ProteusThemes', 'backend','buildpress_wp' ),
				'items' => $items
			),
		);

		// Merge old & new styles
		$settings['style_formats_merge'] = true;

		// Add new styles
		$settings['style_formats'] = json_encode( $new_styles );

		// Return New Settings
		return $settings;

	}
}
add_filter( 'tiny_mce_before_init', 'buildpress_styles_dropdown' );



/**
 * Filter the text in the footer
 */
foreach ( array( 'buildpress/footer_left_txt', 'buildpress/footer_right_txt' ) as $buildpress_filter ) {
	add_filter( $buildpress_filter, 'wptexturize' );
	add_filter( $buildpress_filter, 'convert_chars' );
	add_filter( $buildpress_filter, 'capital_P_dangit' );
}



/**
 * Return Google fonts and sizes
 *
 * @see https://github.com/grappler/wp-standard-handles/blob/master/functions.php
 * @return array Google fonts and sizes.
 */
if ( ! function_exists( 'buildpress_additional_fonts' ) ) {
	function buildpress_additional_fonts( $fonts ) {

		/* translators: If there are characters in your language that are not supported by Noto Serif, translate this to 'off'. Do not translate into your own language. */
		if ( 'off' !== _x( 'on', 'Noto Serif font: on or off', 'buildpress_wp' ) ) {
			$fonts['Source Sans Pro'] = array(
				'400' => '400',
				'700' => '700',
			);
			$fonts['Montserrat'] = array(
				'700' => '700',
			);
		}

		return $fonts;
	}
	add_filter( 'pre_google_web_fonts', 'buildpress_additional_fonts' );
}



/**
 * Add subsets from customizer, if needed.
 *
 * @return array
 */
if ( ! function_exists( 'buildpress_subsets_google_web_fonts' ) ) {
	function buildpress_subsets_google_web_fonts( $subsets ) {
		$additional_subset = get_theme_mod( 'charset_setting', 'latin' );

		array_push( $subsets, $additional_subset );

		return $subsets;
	}
	add_filter( 'subsets_google_web_fonts', 'buildpress_subsets_google_web_fonts' );
}