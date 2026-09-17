<?
class AjaxCenter {
	function __construct() {
		$this->ThemeStatic = new ThemeStatic;
	}
	public function QueryEndpoint() {
		add_rewrite_endpoint( 'AjaxCenter', EP_ROOT );
	}
	public function AjaxCenterPage() {
		if($AjaxCenter = get_query_var('AjaxCenter')){
			$Action = explode('/', $AjaxCenter)[0];
			$Action = basename( sanitize_file_name( $Action ) );
			$allowed = array(
				'forms__services',
				'fields-loadmore',
				'contact__form',
				'TabsActions',
				'ReadMoreObject',
				'RateAjax',
				'PopoverActions',
				'More-Ajax-objects',
				'MenusInitialize',
				'CommentContent',
				'AddComment',
			);
			if ( ! in_array( $Action, $allowed, true ) ) {
				status_header( 404 );
				die();
			}
			if( strpos( $AjaxCenter , $Action.'/') !== FALSE ){
				$Params = explode($Action.'/', $AjaxCenter)[1];
			}else{
				$Params = '';
			}
			$AjaxCenterPath = get_template_directory().'/components/packs/AjaxCenter/';
			$file = $AjaxCenterPath . $Action . '.php';
			if ( ! file_exists( $file ) ) {
				status_header( 404 );
				die();
			}
			$AjaxCenterURL = get_template_directory_uri().'/components/packs/AjaxCenter/';
	    	require( $file );
	    	die();
	    }
	}
	public function Setup() {
		add_action( 'init', array( $this, 'QueryEndpoint' ) );
		add_action( 'BeforeHeader', array( $this, 'AjaxCenterPage' ) );
	}
}
(new AjaxCenter)->Setup();