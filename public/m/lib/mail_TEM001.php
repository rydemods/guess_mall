<?php
/********************************************************************* 
// 파 일 명		: mail.php
// 설     명		: 이메일 관련 함수
// 상세설명	: 가입, 탈퇴, 인증등의 메일함수 총괄
// 작 성 자		: hspark
// 수 정 자		: 2015.10.29 - 김재수
// 수 정 자		: 2016-03-04 - 유동혁
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보를 설정한다.
#---------------------------------------------------------------
Header("Content-type: text/html; charset=utf-8");

if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

//탈퇴메일
function SendOutMail($shopname, $shopurl, $mail_type, $out_msg, $info_email, $email, $name) {
	
}

//가입메일
function SendJoinMail($shopname, $shopurl, $mail_type, $join_msg, $info_email, $email, $name, $id='') {
	
}

//주문확인메일
function SendOrderMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg) {

	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';

	// 주문정보
	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
	$result = pmysql_query( $sql, get_db_conn() );
	if( $row = pmysql_fetch_object( $result ) ) {
		$_ord  = $row;
		$email = $_ord->sender_email; // 받는이
		if( $row->id[0] == 'X' ) $guest_type = "guest";
	} else {
		$ordercode = "";
	}
	pmysql_free_result( $result );

	//오늘 날짜
	$curdate = date( "Y/m/d (H:i)" );

	$paymemt_type = "";
	$arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌");
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,
		"[NAME]"          => $_ord->sender_name,
		"[CURDATE]"       => $curdate,
		"[MESSAGE]"       => $thankmsg,
		"[URL]"           => $shopurl,
		"[ORDERCODE]"     => $ordercode,
		"[ORDERTELL]"     => $_ord->sender_tel,
		"[ORDEREMAIL]"    => $email,
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]],
		"[RECEIVERNAME]"  => $_ord->receiver_name,
		"[RECEIVERTELL]"  => $_ord->receiver_tel2,
		"[RECEIVERADDR]"  => $_ord->receiver_addr,
		"[RECEIVERPOST5]" => $_ord->post5,
		"[ORDMSG]"        => $_ord->order_msg
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/ordermail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/ordermail{$mail_type}.php");
		$buffer = ob_get_contents();
		ob_end_clean();
		$body = $buffer;

	}

	if( ord( $body ) ) {

		unset( $pattern );
		unset( $replace );
		foreach( $patten_arr as $k=>$v ){
			$pattern[] = $k;
			$replace[] = $v;
		}
		$body = str_replace( $pattern, $replace, $body );
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	$subject = $shopname." 주문내역서 확인 메일입니다.";

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	}
}

//상품발송메일
function SendDeliMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $deli_com, $deli_num, $delimailtype) {
	
}

//아이디/패스워드안내메일
function SendPassMail($shopname, $shopurl, $mail_type, $info_email, $email, $name, $id, $passwd) {
	
}

function SendIdMail($shopname, $shopurl, $mail_type, $info_email, $email, $name, $id) {
	
}

//입금확인메일
function SendBankMail($shopname, $shopurl, $mail_type, $info_email, $email, $ordercode) {
	
}

//회원인증메일
function SendAuthMail($shopname, $shopurl, $mail_type, $info_email, $email, $id) {
	
}

function sendMailForm($sender_name,$sender_email,$message,$upfile,&$bodytext,&$mailheaders) {
	
}

//가입 인증 메일 - 회원가입에서 이메일 인증으로 회원 가입페이지로 오게 하는... (2015.10.29 - 김재수)
function SendJoinCertMail($shopname, $shopurl, $mail_type, $join_msg, $info_email, $email, $name='', $id='', $rfcode='') {
	
}

//이메일로 패스워드 안내 메일 보내기(2015.11.03 - 김재수)
function SendPasswordMail($shopname, $shopurl, $mail_type, $info_email, $email, $name, $id, $passwd) {

}