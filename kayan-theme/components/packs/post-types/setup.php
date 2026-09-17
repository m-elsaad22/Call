<?
function PostTypes() {
	global $ThemeTree;
	#
    $ThemeTree->AddPType('الأسئله', 'سؤال', 'ة', 'faq', true, false, array('title', "editor"), 8);

    $ThemeTree->AddPType('خطط الأسعار', 'الاسعار', 'ة', 'price', true, array("slug"=>get_option('plans_url')) , array('title', "editor"), 8);
    $ThemeTree->AddPType('سابقة الاعمال', 'سابقة الاعمال', 'ة', 'works', true, array("slug"=>'works') , array('title', "editor", "thumbnail"), 8);
    #
}
add_action('Initialize', 'PostTypes', 10, 3);