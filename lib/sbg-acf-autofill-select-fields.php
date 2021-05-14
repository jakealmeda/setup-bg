<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//include_once( 'sfm-genesis-hooks-list.php' );

$ss_bg_acf = new SetupBG_FillACFSelectFields();
class SetupBG_FillACFSelectFields {

	/**
	 * Auto fill Select options for Genesis Hooks
	 *
	 */
	public function sbg_autofill_hookimages( $field ) {

		$hookers = new SetupBG_GenesisHooksList();

		$field['choices'] = array();

		//Loop through whatever data you are using, and assign a key/value
		if( is_array( $hookers->genesis_hooks ) ) {

		    foreach( $hookers->genesis_hooks as $value ) {
		        
		        $field['choices'][$value] = $value;
		    }

		    return $field;

		}

	}


	/**
	 * Auto fill Select options for Views
	 *
	 */
	public function acf_sbg_view_choices( $field ) {
	    
	    $z = new SetupBG();

	    $file_extn = 'html';

	    // get all files found in VIEWS folder
	    $view_dir = $z->setup_sbg_dir_path().'views/';

	    $data_from_dir = $this->sbg_autofill_view_files( $view_dir, $file_extn );

	    $field['choices'] = array();

	    //Loop through whatever data you are using, and assign a key/value
	    if( is_array( $data_from_dir ) ) {

	        foreach( $data_from_dir as $field_key => $field_value ) {
	            $field['choices'][$field_key] = $field_value;
	        }

	        return $field;

	    }
	    
	}


	public function sbg_autofill_view_files( $directory, $file_extn ) {

		$out = array();

		// get all files inside the directory but remove unnecessary directories
		$ss_plug_dir = array_diff( scandir( $directory ), array( '..', '.' ) );

		foreach( $ss_plug_dir as $filename ) {

			if( pathinfo( $filename, PATHINFO_EXTENSION ) == $file_extn ) {
				$out[ $filename ] = pathinfo( $filename, PATHINFO_FILENAME );
			}

		}

        // Return an array of files (without the directory)
        return $out;

    }


    public function acf_sbg_size_choices( $field ) {

		$field['choices'] = array();

		//Loop through whatever data you are using, and assign a key/value
		if( is_array( get_intermediate_image_sizes() ) ) {

			foreach( get_intermediate_image_sizes() as $value ) {

				$field['choices'][$value] = $value;

			}

			return $field;

		}

    }


	// CONSTRUCT
	public function __construct() {

		// AUTO FILL SELECT FOR HOOKS (ACF)
		add_filter( 'acf/load_field/name=bg_hooks', array( $this, 'sbg_autofill_hookimages' ) );

		// AUTO FILL SELECT FOR TEMPLATES (ACF)
		add_filter( 'acf/load_field/name=bg_template', array( $this, 'acf_sbg_view_choices' ) );

		// AUTO FILL SELECT FOR TEMPLATES (ACF)
		add_filter( 'acf/load_field/name=bg_size', array( $this, 'acf_sbg_size_choices' ) );

	}
	
}