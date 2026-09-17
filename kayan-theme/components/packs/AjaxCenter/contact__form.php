<?
header("Content-Type: application/json");
ob_start();
$_POST = YC_stripslashes_deep( $_POST );
$json = array();
#
if( isset( $_POST['user__name'] ) ) {

	$AdminMail = get_bloginfo('admin_email');


	if( isset( $_POST['servies__category'] ) ){
		$category = get_term_by( 'id',$_POST['servies__category'],'category' );
		$message_title = "قام {$_POST['user__name']} بتقديم طلب للحصول على الخدمة {$category->name}";
	}else{
		$message_title = "قام {$_POST['user__name']} بإرسال طلب للتواصل مع إعمار الريا ض";
	}

	echo "<div>";
		echo "<ul>";
			echo ( ( isset( $_POST['user__name'] ) ) ) ? "<li><strong>اسم العميل :</strong><span>{$_POST['user__name']}</span></li>" : '';
			echo ( ( isset( $_POST['user_mail'] ) ) ) ? "<li><strong>البريد الالكتروني :</strong><span>{$_POST['user_mail']}</span></li>" : '';
			echo ( ( isset( $_POST['phone__number'] ) ) ) ? "<li><strong>رقم الهاتف :</strong><span>{$_POST['phone__number']}</span></li>" : '';
			if( isset( $_POST['servies__category'] ) ){
				
				echo "<li><strong>الخدمة :</strong><span>{$category->name}</span></li>";
			}
			echo ( ( isset( $_POST['description'] ) ) ) ? "<li><strong>ملاحظات الطلب :</strong><span>{$_POST['description']}</span></li>" : '';
		echo "</ul>";
	echo "</div>";
	$mail__output = ob_get_clean();


	$headers = ['Content-Type: text/html; charset=UTF-8'];
	wp_mail( $AdminMail,$message_title, $mail__output ,$headers );	
	#
	$json['alert_output'] = array('type'=>'','alert'=>'تم إرسال رسالتك بنجاح وسيتم التواصل معك قريبا');

}else{
	$json['alert_output'] = 'لم يتم العثور على نموذج ';
}
echo json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);