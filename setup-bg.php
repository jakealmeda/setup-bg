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

		$args = array();
		
		// PULL POST ID FROM THE URL
		$post_id = url_to_postid( $_SERVER['REQUEST_URI'] , '_wpg_def_keyword', true ); 

		$repeater_name = 'images';

		// loop through each repeater's row
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

					$fields_bg = array( 'bg_template', 'bg_size', 'bg_css' );
					foreach( $fields_bg as $v ) {
						$args[ $v ] = $bg_attributes_group[ $v ];
					}

					// display fields based on the hook	| pass the variable to the function
					add_action( $bg_hooks, function() use ( $args ) {

						$this->setup_bg_display_hooks( $args );

					});

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

			$replace_array = array(
					'{@image}'	=> wp_get_attachment_image( $args[ 'bg_image' ] , $size, '', array( 'class' => $args[ 'bg_css' ] ) ),
				);

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