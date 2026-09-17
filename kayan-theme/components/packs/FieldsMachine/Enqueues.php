<?php # YOURCOLOR CUSTOM FIELDS MACHINE.
class YC__CFM_Enqueues {
	function __construct($arguments=array()) {
		$this->YC__CFM = new YC__CFM;
		// 
		$this->UI__URL = $this->YC__CFM->YC__CFM_URL.'/UI/';
		$this->JS__URL = $this->YC__CFM->YC__CFM_URL.'UI/js/';
		$this->Style__URL = $this->YC__CFM->YC__CFM_URL.'UI/css/';

	}

	public function YC__CFM_AdminFooter(){


		# echo '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>';
		echo '<script type="text/javascript" src="'.$this->JS__URL.'bootstrap.bundle.min.js"></script>';

		# colorpicker
			echo '<script type="text/javascript" src="'.$this->JS__URL.'bootstrap-colorpicker.min.js"></script>';

		# codemirror
			echo '<script type="text/javascript" src="'.$this->JS__URL.'codemirror.js"></script>';

		# datepicker	
			echo '<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>';

		# jquery-ui
			echo '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';

		# owl carousel
			echo '<script src="'.$this->JS__URL.'owl.carousel.min.js"></script>';

		# INSERT PIN JQUERY .	
			do_action('YC__CFM__pin_jquery');

		# YC__CFM__before_main_js
			do_action('YC__CFM__before_main_js');	

		# YC__CFM__after_main_js
			do_action('YC__CFM__after_main_js');	

		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();

		}
	 	wp_register_script('mediaelement', plugins_url('wp-mediaelement.min.js', __FILE__), array('jquery'), '4.8.2', true);
		wp_enqueue_script('mediaelement');

		wp_print_media_templates();

		# CUSTOM JS
			echo '<script src="'.$this->JS__URL.'kayan-gradient-builder.js?'.rand().'" type="text/javascript"></script>';
			echo '<script src="'.$this->JS__URL.'kayan-global-shadows.js?'.rand().'" type="text/javascript"></script>';
			echo '<script src="'.$this->JS__URL.'kayan-homepage-sections-order.js?'.rand().'" type="text/javascript"></script>';
			echo '<script src="'.$this->UI__URL.'Custom-Setup.js?'.rand().'" type="text/javascript"></script>';

	}

	public function YC__CFM_Admin_Enqueue(){
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'codemirror.css" />';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'richtext.min.css" />';
		echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">';
		# KAYAN hotfix: keep admin pseudo icons on Font Awesome Free.
		echo '<style id="kayan-admin-fa-free-hotfix">';
			echo '.fa:not(.fa-brands):not(.fab),.fas,.fa-solid,.fa-regular,.far,i[class^="fa-"]:not(.fa-brands):not(.fab),i[class*=" fa-"]:not(.fa-brands):not(.fab),span[class^="fa-"]:not(.fa-brands):not(.fab),span[class*=" fa-"]:not(.fa-brands):not(.fab){font-family:"Font Awesome 6 Free" !important;font-weight:900 !important;}';
			echo '.fa:not(.fa-brands):not(.fab)::before,.fas::before,.fa-solid::before,.fa-regular::before,.far::before,i[class^="fa-"]:not(.fa-brands):not(.fab)::before,i[class*=" fa-"]:not(.fa-brands):not(.fab)::before,span[class^="fa-"]:not(.fa-brands):not(.fab)::before,span[class*=" fa-"]:not(.fa-brands):not(.fab)::before{font-family:"Font Awesome 6 Free" !important;font-weight:900 !important;}';
			echo '.fa-brands,.fab,.fa-brands::before,.fab::before{font-family:"Font Awesome 6 Brands" !important;font-weight:400 !important;}';
			echo '[class*="fa-"]:not(.fa-brands):not(.fab)::before,[class*="fa-"]:not(.fa-brands):not(.fab)::after{font-family:"Font Awesome 6 Free" !important;font-weight:900 !important;}';
		echo '</style>';

		echo '<link href="'.$this->Style__URL.'bootstrap-colorpicker.css" rel="stylesheet">';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'kayan-gradient-builder.css?'.rand().'" />';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'kayan-global-shadows.css?'.rand().'" />';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'kayan-homepage-sections-order.css?'.rand().'" />';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'kayan-seo-dashboard.css?'.rand().'" />';

		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->UI__URL.'Custom-Style.css?'.rand().'" />';
		echo '<link href="'.$this->Style__URL.'flatpickr.min.css" rel="stylesheet">';
	}

	public function Setup(){
		add_action( 'admin_enqueue_scripts', array($this, 'YC__CFM_Admin_Enqueue') );
		add_action('admin_footer', array($this, 'YC__CFM_AdminFooter'));

	}
	
}
(new YC__CFM_Enqueues)->Setup();