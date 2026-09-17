<?
# GET URL OPTIONS .
	if( $button_context['button_mode'] == 'manual' ){

		if( !empty( $button_context[ $button_context['button_mode'] ] ) && isset( $button_context[ $button_context['button_mode'] ]['button__URL'] ) && !empty( $button_context[ $button_context['button_mode'] ]['button__URL'] ) ){
			$PermaLink = $button_context[ $button_context['button_mode'] ]['button__URL'];
		}
		$button_Text = 'عرض المزيد';

	}else if( $button_context['button_mode'] == 'watshapp' ){

		$wa_num = '';
		if( !empty( $button_context[ $button_context['button_mode'] ] ) && isset( $button_context[ $button_context['button_mode'] ]['watshapp'] ) && !empty( $button_context[ $button_context['button_mode'] ]['watshapp'] ) ){
			$wa_num = $button_context[ $button_context['button_mode'] ]['watshapp'];
		}else if( !empty( get_option('whatsapp_number') ) ){
			$wa_num = get_option('whatsapp_number');
		}
		if( $wa_num !== '' ) {
			$PermaLink = function_exists( 'kayan_wa_build_url' ) ? kayan_wa_build_url( $wa_num ) : "https://wa.me/{$wa_num}";
		}
		$target = ' target="_blank"';
		$button_Text = $button_context[ $button_context['button_mode'] ]['watshapp'];
		if( empty($button_context[ $button_context['button_mode'] ]['watshapp']) ){
			$button_Text = get_option('phonenumber');
		}
	}else if( $button_context['button_mode'] == 'phonenumber' ){

		if ( function_exists( 'kayan_ui_show_call_button' ) && ! kayan_ui_show_call_button() ) {
			return;
		}
		if( !empty( $button_context[ $button_context['button_mode'] ] ) && isset( $button_context[ $button_context['button_mode'] ]['phonenumber'] ) && !empty( $button_context[ $button_context['button_mode'] ]['phonenumber'] ) ){
			$PermaLink = "tel:{$button_context[ $button_context['button_mode'] ]['phonenumber']}";
		}else if( !empty( get_option('phonenumber') ) ){
			$phonenumber = get_option('phonenumber');
			$PermaLink = "tel:{$phonenumber}";
		}
		$target = ' target="_blank"';
		$button_Text = $button_context[ $button_context['button_mode'] ]['phonenumber'];
		if( empty( $button_Text = $button_context[ $button_context['button_mode'] ]['phonenumber'] ) ){
			$button_Text = get_option('phonenumber');
		}

	}else if( $button_context['button_mode'] == 'page' ){

		if( !empty( $button_context[ $button_context['button_mode'] ] ) && isset( $button_context[ $button_context['button_mode'] ]['button_page'] ) && !empty( $button_context[ $button_context['button_mode'] ]['button_page'] ) ){

			$PermaLink = get_the_permalink( $button_context[ $button_context['button_mode'] ]['button_page'] );

			# GET BUTTON POST TITLE
			$button_Text = get_the_title( $button_context[ $button_context['button_mode'] ]['button_page'] );
		}
		if( empty( $button_context[ $button_context['button_mode'] ]['button_page'] ) ){
			$button_Text = get_the_title( $button_context[ $button_context['button_mode'] ]['button_page'] );
		}

	}

# GET BUTTON TITLE
	if( !empty( $button_context[ $button_context['button_mode'] ] ) && isset( $button_context[ $button_context['button_mode'] ]['button_Text'] ) && !empty( $button_context[ $button_context['button_mode'] ]['button_Text'] ) ) $button_Text = $button_context[ $button_context['button_mode'] ]['button_Text'];
# GET BUTTON ICON 
	if( !empty( $button_context[ $button_context['button_mode'] ] ) && isset( $button_context[ $button_context['button_mode'] ]['button_Icon'] ) && !empty( $button_context[ $button_context['button_mode'] ]['button_Icon'] ) ) $button_Icon = $button_context[ $button_context['button_mode'] ]['button_Icon'];

if( !isset( $PermaLink ) || isset( $PermaLink ) && empty( $PermaLink ) ){
	$contact__us__page = get_option('contact__us__page');
	if( !empty( $contact__us__page ) ){
		$PermaLink = get_the_permalink( $contact__us__page );
	}else{
		$PermaLink = home_url();
	}
}
$button_Text = ( ( isset( $button_Text ) ) ) ? $button_Text : '';
if( $button_context['button_mode'] == 'manual' ){

	echo '<div'.( ( isset( $class ) ) ? ' class="'.$class.'"' : '' ).( ( isset( $attributes ) ) ? ' '.$attributes : '' ).'>';
		echo '<a href="'.$PermaLink.'" title="'.$button_Text.'"'.( ( isset( $href_class ) ) ? ' class="'.$href_class.'"' : '' ).( ( isset( $target ) ) ? $target : '' ).'>';
			echo ( ( isset( $button_Text ) ) ) ? '<span>'.$button_Text.'</span>' : '';
			echo ( ( isset( $button_Icon ) ) ) ? $button_Icon : '';
		echo '</a>';
	echo '</div>';

}else {

	echo '<div'.( ( isset( $class ) ) ? ' class="'.$class.'"' : '' ).( ( isset( $attributes ) ) ? ' '.$attributes : '' ).'>';
		echo '<a href="'.$PermaLink.'" title="'.$button_Text.'"'.( ( isset( $href_class ) ) ? ' class="'.$href_class.'"' : '' ).( ( isset( $target ) ) ? $target : '' ).'>';
			echo ( ( isset( $button_Text ) ) ) ? '<span>'.$button_Text.'</span>' : '';
			echo ( ( isset( $button_Icon ) ) ) ? $button_Icon : '';
		echo '</a>';
	echo '</div>';
}