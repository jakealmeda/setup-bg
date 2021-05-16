<?php
/**
 * Plugin Name: Setup BG
 * Description: Use repeater images as background.
 * Version: 1.0
 * Author: Jake Almeda
 * Author URI: http://smarterwebpackages.com/
 * Network: true
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/*
Wishlist:

1. Insert styles inline or CSS selectors on existing container (like body,
site-container, site-header, inner)

2. Use ACF templates to dictate the layout structure of what we're inserting.
Does this mean we can modify the header just from ACF template alone?
*/
$setup_bg = new SetupBG();
include_once( 'lib/sbg-genesis-hooks-list.php' );
include_once( 'lib/sbg-acf-autofill-select-fields.php' );

class SetupBG {

	/**
	 * Main function | Loop through repeater entries
	 */
	public function setup_bg() {

		// exit if not post or page entry
//		if( ! is_single() )
//			return TRUE;

		// PULL POST ID FROM THE URL
		$post_id = url_to_postid( $_SERVER['REQUEST_URI'] , '_wpg_def_keyword', true ); 

		$args = array();
		$repeater_name = 'bg_background';
		
		if( have_rows( $repeater_name, $post_id ) ):
			
			while( have_rows( $repeater_name, $post_id ) ): the_row();

				$bg_image_group = get_sub_field( 'bg_image_group' );
				$bg_attributes_group = get_sub_field( 'bg_attributes_group' );

				$bg_hooks = $bg_attributes_group[ 'bg_hooks' ];
				if( $bg_hooks == 'N/A' ) :

					// NOT USING HOOKS

					// USE SHORTCODE?
					// CALL BY FUNCTION?

				else :

					// DISPLAY USING HOOKS

					$args = array (
						'pid' 			=> $post_id,
						'bg_image' 		=> $bg_image_group[ 'bg_image' ],
					);

					$fields_bg = array( 'bg_template', 'bg_size', 'bg_css', 'bg_inline_style' );
					foreach( $fields_bg as $v ) {
						$args[ $v ] = $bg_attributes_group[ $v ];
					}

					// display fields based on the hook	| pass the variable to the function
					add_action( $bg_hooks, function() use ( $args ) {

						$this->setup_bg_display_hooks( $args );

					}, $bg_attributes_group[ 'bg_priority' ] );

				endif;

			endwhile;
		
		endif;

	}


	public function setup_bg_display_hooks( $args ) {
		
		// Set default size
		if( empty( $args[ 'bg_size' ] ) ) {
			$size = 'large';
		} else {
			$size = $args[ 'bg_size' ];
		}

		// Get the image
		if( !empty( $args[ 'bg_image' ] ) ) {

			if( empty( $args[ 'bg_inline_style' ] ) ) :

				$replace_array = array(
						'{@image}'	=> wp_get_attachment_image( $args[ 'bg_image' ] , $size, FALSE, array( 'class' => $args[ 'bg_css' ] ) ),
					);

			else:

				//echo wp_get_attachment_image_url( $args[ 'bg_image' ], $size );
				$p = wp_get_attachment_image_src( $args[ 'bg_image' ], $size, FALSE, '' );
//				echo '<img src="'.$p[ 0 ].'" width="'.$p[ 1 ].'" height="'.$p[ 2 ].'" style="'.$args[ 'bg_inline_style' ].'" class="'.$args[ 'bg_css' ].'" />';
				$replace_array = array(
						'{@image}'	=> '<img src="'.$p[ 0 ].'" width="'.$p[ 1 ].'" height="'.$p[ 2 ].'" style="'.$args[ 'bg_inline_style' ].'" class="'.$args[ 'bg_css' ].'" />',
					);

			endif;

			// OUTPUT
			echo strtr( $this->sbg_get_html_templates( $args[ 'bg_template' ] ), $replace_array );

		}

	}


	/**
	 * This plug-in's directory
	 */
	public function setup_sbg_dir_path() {

		return plugin_dir_path( __FILE__ );

	}


	/**
	 * Get VIEW template (INCLUDE)
	 *
	 */
	function sbg_get_html_templates( $layout ) {

		$layout_file = $this->setup_sbg_dir_path().'views/'.$layout;

		return file_get_contents( $layout_file );

	}


	/**
	 * Handle the display
	 */
	public function __construct() {

		// Enqueue scripts
		if ( !is_admin() ) {

//			add_action( 'wp_enqueue_scripts', array( $this, 'setup_sfmenux_enqueue_scripts' ), 20 );

			add_action( 'init', array( $this, 'setup_bg' ) );

		}

	}

}