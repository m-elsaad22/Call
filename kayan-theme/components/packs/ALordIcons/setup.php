<?php
if ( ! function_exists( 'kayan_ui_fa_icon_from_lord' ) ) {
	function kayan_ui_fa_icon_from_lord( $json ) {
		$map = array(
			'puvaffet'  => 'fa-cloud-arrow-up',
			'jvucoldz'  => 'fa-layer-group',
			'tdrtiskw'  => 'fa-circle-xmark',
			'zczmziog'  => 'fa-circle-check',
			'slkvcfos'  => 'fa-box-open',
			'xfftqrxe'  => 'fa-file-lines',
			'gzvfczvr'  => 'fa-gear',
			'wloilxuq'  => 'fa-star',
		);
		return isset( $map[ $json ] ) ? $map[ $json ] : 'fa-icons';
	}
}

function LoardIcons( $json = '', $w = '50px', $h = '50px', $options = array( 'primary' => '#ffffff', 'secondary' => '#ffffff', 'stroke' => '70', 'trigger' => 'loop', 'delay' => '3000' ) ) {
	if ( $json === '' ) {
		return false;
	}
	$icon = kayan_ui_fa_icon_from_lord( $json );
	$color = isset( $options['primary'] ) ? $options['primary'] : '#1269eb';
	return '<i class="fa-solid ' . esc_attr( $icon ) . ' kayan-fa-lord-replacement" style="width:' . esc_attr( $w ) . ';height:' . esc_attr( $h ) . ';font-size:calc(' . esc_attr( $w ) . ' * 0.45);color:' . esc_attr( $color ) . ';display:inline-flex;align-items:center;justify-content:center;"></i>';
}
