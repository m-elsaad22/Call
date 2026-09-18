<?php
$Styles = array();
$YC__WidgetsMachine = new YC__WidgetsMachine;
$hide__sidebar__single = get_option( 'hide__sidebar__pages' );
$widgets_pages__meta = array();
if ( empty( $hide__sidebar__single ) ) {
	$widgets_pages__meta = is_array( get_option( 'widgets_pages__meta' ) ) ? get_option( 'widgets_pages__meta' ) : array();
	if ( ! empty( $widgets_pages__meta ) ) {
		$Styles = array_merge( $Styles, $YC__WidgetsMachine->widgets__Enqueues( $widgets_pages__meta ) );
	}
}

$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

ob_start();
the_content();
$body = ob_get_clean();

echo '<section class="sec"><div class="wrap article-layout">';
echo '<div class="article-body prose">' . $body . '</div>';
echo '<div>';
kayan_kit_side_cta();
if ( empty( $hide__sidebar__single ) && ! empty( $widgets_pages__meta ) ) {
	$YC__WidgetsMachine->widgets___UI(
		array(
			'Widgets_data'            => $widgets_pages__meta,
			'WidgetID'                => 'widgets_pages__meta',
			'Parent__section__class'  => 'side-w',
			'Single__section__class'  => 'side-w',
			'section_InnerRow_class'  => '',
		)
	);
}
echo '</div></div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
