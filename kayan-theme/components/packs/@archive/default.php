<?

$obj = get_queried_object();

$Styles = array();
$UniqId = uniqid();

$CategoryContent = $obj->description;
$CategoryContent = str_replace('<br/>', PHP_EOL, $CategoryContent);
$CategoryContent = str_replace('&nbsp;', ' ', $CategoryContent);
$CategoryContent = strip_tags($CategoryContent);
$MoreClass 		= '';
if( strlen($CategoryContent) > 500 ) {
	$CategoryContent = mb_substr($CategoryContent, 0, 500, 'utf-8').'... <a href="javascript:void(0);" data-button="readmore-objects" data-object-type="taxonomeis" data-object-name="'.$obj->taxonomy.'" data-object-id="'.$obj->term_id.'" class="readmore--category-item -BTN--hoverable">قراءة المزيد</a>';
}else{
	$CategoryContent = $CategoryContent;
}

$videoID = get_term_meta( $obj->term_id,'videoID',true );
$archive__posts_per_page = (INT) get_option('posts_per_page');
if( $archive__posts_per_page == 0 ) $archive__posts_per_page = 20;

$Styles['kayan-home']  = 'kayan-home.css';
$Styles['kayan-inner'] = 'kayan-inner.css';
$Styles['shortcodes']  = 'shortcodes.css';

$category_full_text = $obj->description;
$category_full_text = str_replace( '<br/>', PHP_EOL, $category_full_text );
$category_full_text = str_replace( '&nbsp;', ' ', $category_full_text );
$category_full_text = trim( strip_tags( $category_full_text ) );

$category_hero_text = $category_full_text;
if ( empty( $category_hero_text ) ) {
	$category_hero_text = 'جميع مقالات ' . $obj->name;
	$category_full_text = $category_hero_text;
} elseif ( mb_strlen( $category_full_text, 'UTF-8' ) > 160 ) {
	$category_hero_text = mb_substr( $category_full_text, 0, 160, 'UTF-8' ) . '…';
}

$this->Part('header',array('Styles'=>$Styles));

$tax_meta = '';
if ( ! empty( $videoID ) ) {
	$tax_meta = '<div><button type="button" class="btn btn-ghost" data-yt-open="' . esc_attr( $videoID ) . '"><i class="fas fa-play"></i> فيديو</button></div>';
}
if ( function_exists( 'kayan_kit_render_phero' ) ) {
	kayan_kit_render_phero(
		array(
			'title'     => $obj->name,
			'subtitle'  => $category_hero_text,
			'meta_html' => $tax_meta,
		)
	);
}

echo '<div class="kayan-inner-archive-shell kayan-tax-archive">';
kayan_kit_open_section();
echo '<div class="blog-grid kayan-inner-archive-grid -archivePage-Posts-Grid">';
					$this->Part(
				        'Posts',
				        array(
				            'object__type'=>'posts', # posts or taxonomy or users
				            'object__name'=>'post', # post_type or taxonomy name.
				            'part_object__name'=>'post',
				            'part__name'=>'Post-box',
				            'ObjectTerms'=>array($obj),
				            'ScrollLoader'=>true,
				            'per'=>8,
				            'show__empty_part'=>'object--empty',
				            'data__empty_part'=>array(
				                '__empty_icon'=>'<i class="fa-solid fa-ban"></i>',
				                '__empty_title'=>'لن يتم العثور على المقالات اخري',
				                '__empty_description'=>'<a href="'.home_url().'">الرئيسية </a>',
				                '__Ajax_empty_title'=>'لقد شاهدت جميع الالمقالات',
				                '__Ajax_empty_description'=>'تم عرض جميع المقالات قسم <strong></strong><a href="'.home_url().'">الرئيسية </a>',

				            ),
				        )
				    );
echo '</div>';

if ( mb_strlen( $category_full_text, 'UTF-8' ) > 160 ) {
	echo '<div class="kayan-inner-readmore is-collapsed article-body prose" id="kayan-readmore-' . esc_attr( $UniqId ) . '" data-readmore style="margin-top:40px">';
	echo '<div class="kayan-inner-readmore__text" data-readmore-text>' . esc_html( $category_full_text ) . '</div>';
	echo '<button type="button" class="btn btn-soft kayan-inner-readmore__btn" data-readmore-btn>قراءة المزيد</button>';
	echo '</div>';
}

kayan_kit_close_section();

if ( ! empty( $videoID ) ) {
	echo '<div class="kayan-yt-modal" id="kayan-yt-modal-' . esc_attr( $UniqId ) . '" hidden aria-hidden="true">';
	echo '<div class="kayan-yt-modal__backdrop" data-yt-close tabindex="-1"></div>';
	echo '<div class="kayan-yt-modal__panel" role="dialog" aria-modal="true" aria-label="' . esc_attr( 'فيديو ' . $obj->name ) . '">';
	echo '<button type="button" class="kayan-yt-modal__close" data-yt-close aria-label="' . esc_attr( 'إغلاق' ) . '">&times;</button>';
	echo '<div class="kayan-yt-modal__frame-wrap">';
	echo '<iframe title="' . esc_attr( $obj->name ) . '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen data-yt-iframe></iframe>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
}

echo '</div>';

if ( ! empty( $videoID ) || mb_strlen( $category_full_text, 'UTF-8' ) > 160 ) {
	?>
<script>
(function () {
	var modalId = <?php echo wp_json_encode( 'kayan-yt-modal-' . $UniqId ); ?>;
	var modal = document.getElementById(modalId);
	var iframe = modal ? modal.querySelector('[data-yt-iframe]') : null;

	function openModal(videoId) {
		if (!modal || !iframe || !videoId) return;
		iframe.src = 'https://www.youtube.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1&rel=0';
		modal.hidden = false;
		modal.setAttribute('aria-hidden', 'false');
		document.body.classList.add('kayan-yt-modal-open');
	}

	function closeModal() {
		if (!modal || !iframe) return;
		iframe.src = '';
		modal.hidden = true;
		modal.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('kayan-yt-modal-open');
	}

	document.querySelectorAll('[data-yt-open]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			openModal(btn.getAttribute('data-yt-open'));
		});
	});

	if (modal) {
		modal.querySelectorAll('[data-yt-close]').forEach(function (el) {
			el.addEventListener('click', closeModal);
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !modal.hidden) closeModal();
		});
	}

	document.querySelectorAll('[data-readmore]').forEach(function (wrap) {
		var btn = wrap.querySelector('[data-readmore-btn]');
		if (!btn) return;
		btn.addEventListener('click', function () {
			var expanded = wrap.classList.toggle('is-expanded');
			wrap.classList.toggle('is-collapsed', !expanded);
			btn.textContent = expanded ? 'عرض أقل' : 'قراءة المزيد';
		});
	});
})();
</script>
	<?php
}

$this->Part('footer',array('Styles'=>$Styles));
