<?

class ThemeSeo {
	function __construct() {

		$this->seo__title_showsin = get_option('seo__title_showsin');
		if( empty( $this->seo__title_showsin ) ) $this->seo__title_showsin = 'wordpress';

		$this->seo__site_name = get_option('seo__site_name');
		$this->LastWord = $this->seo__site_name;
	}
	public function Title(){
		if ( function_exists( 'kayan_seo_theme_seo_delegates_to_kayan' ) && kayan_seo_theme_seo_delegates_to_kayan() ) {
			return;
		}

		if( $this->seo__title_showsin == 'theme_seo' ){
			$title = '';
			if( is_page() ){
				global $post;
				if( $post->post_parent > 0 ) {
					$title .= get_post($post->post_parent)->post_title.' ';
				}

				$title .= $post->post_title;

			}else if( is_author() ){
				$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
				$title .= $curauth->display_name;

			}else if( is_category() ){

				$obj = get_queried_object();
				if( $obj->parent > 0 ) {
					$title .= get_term($obj->parent, 'category')->name.' ';
				}
				$title .= $obj->name;

			}else if( is_tax() ){
				$obj = get_queried_object();

				if( $obj->parent > 0 ) {
					$title .= get_term($obj->parent, $obj->taxonomy)->name.' ';
				}

				$title .= $obj->name;
			}else if( is_single() ){
				global $post;
				if( $post->post_parent > 0 ) {
					$title .= get_post($post->post_parent)->post_title.' ';
				}
				
				$title .= $post->post_title;

			}else if( is_home() ){
				$title .= get_option('home__title');
			}else{
				$title .= get_option('default__title');
			}

			echo "<title>{$title}{$this->LastWord}</title>";
		}else{
			if( is_home() ){
    			echo '<title>'.get_bloginfo('name').'</title>';
			}else{
    			echo '<title>';
    				wp_title();
    			echo'</title>';
			}
		}
	}
}