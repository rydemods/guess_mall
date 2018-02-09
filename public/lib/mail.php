<?php
/********************************************************************* 
// 파 일 명		: mail.php
// 설     명		: 이메일 관련 함수
// 상세설명	: 가입, 탈퇴, 인증등의 메일함수 총괄
// 작 성 자		: hspark
// 수 정 자		: 2015.10.29 - 김재수
// 수 정 자		: 2016-05-18 - 유동혁
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

//탈퇴축하메일
function SendOutMail($shopname, $shopurl, $mail_type, $out_msg, $info_email, $email, $name) {
/* 
    $shopname = '신원몰';
	$sql = "SELECT * FROM tbldesignnewpage WHERE type='outmail' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("[SHOP]","[NAME]");
		$replace = array ($shopname,$name);
		$subject = str_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $shopname." 탈퇴 안내 메일입니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/outmail{$mail_type}.php")) {
			$buffer = file_get_contents(DirPath.TempletDir."mail/outmail{$mail_type}.php");
			$body=$buffer;
		}
	}
	pmysql_free_result($result);
	if(ord($body)) {
		$pattern = array ("[SHOP]","[NAME]","[MESSAGE]","[URL]");
		$replace = array ($shopname,$name,$out_msg,$shopurl);
		$body	 = str_replace($pattern,$replace,$body);
		if (ord($shopname)) $mailshopname = "=?utf-8?b?".base64_encode($shopname)."?=";		
		if (ord($subject)) $subject = "=?utf-8?b?".base64_encode($subject)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
//			sendmail($email, $subject, $body, $header);
		}
	} */
}

//가입축하메일
function SendJoinMail($shopname, $shopurl, $mail_type, $join_msg, $info_email, $email, $name, $id='') {

    $shopname = '신원몰';
	$sql = "SELECT * FROM tbldesignnewpage WHERE type='joinmail' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("[SHOP]","[NAME]");
		$replace = array ($shopname,$name);
		$subject = str_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $shopname." 가입 축하 메일입니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/joinmail{$mail_type}.php")) {
			//$buffer = file_get_contents(DirPath.TempletDir."mail/joinmail{$mail_type}.php");
			ob_start();
			include(DirPath.TempletDir."mail/joinmail{$mail_type}.php");
			$buffer = ob_get_contents();
			$body=$buffer;
			ob_end_clean();
		}
	}
	pmysql_free_result($result);
	$curdate = date("Y년 m월 d일");
	if(ord($body)) {
		$pattern_arr = array(
								"[SHOP]" => $shopname,
								"[NAME]" => $name,
								"[MESSAGE]" => $join_msg,
								"[URL]" => $shopurl,
								"[ID]" => $id,
								"[CURDATE]" => $curdate,
								"[EMAIL]" => $email
							);
		$pattern = array();
		unset($pattern);
		$replace = array();
		unset($replace);
		foreach($pattern_arr as $k=>$v){
			$pattern[]=$k;
			$replace[]=$v;
		}

		$body	 = str_replace($pattern,$replace,$body);
		if (ord($shopname)) $mailshopname = "=?utf-8?b?".base64_encode($shopname)."?=";		
		if (ord($subject)) $subject = "=?utf-8?b?".base64_encode($subject)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

//주문확인메일 sms_type = 0
function SendOrderMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg) {

	//$ordercode = "2017051721424162056A";
	
	$shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$oderproduct = array();
	// 주문정보
	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
	$result = pmysql_query( $sql, get_db_conn() );
	if( $row = pmysql_fetch_object( $result ) ) {
		$_ord  = $row;
		$email = $_ord->sender_email; // 받는이
		//$email = "dereklee1012@naver.com"; // 받는이
		if( $row->id[0] == 'X' ) $guest_type = "guest";
	} else {
		$ordercode = "";
	}
	pmysql_free_result( $result );

	$op_sql = "SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, p.color_code, p.prodcode,op.quantity, op.price, op.reserve, op.date, op.coupon_price, ";
	$op_sql.= "op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage ";
	$op_sql.= "FROM tblorderproduct op left join tblproduct p on op.productcode=p.productcode  ";
    $op_sql.= "WHERE op.ordercode = '".$ordercode."' ORDER BY op.vender ASC ";

	$op_res = pmysql_query( $op_sql, get_db_conn() );
	
	while( $op_row = pmysql_fetch_object( $op_res ) ){
		$orderproduct[$op_row->vender][] = $op_row;
	}
	pmysql_free_result( $op_res );
	
	//오늘 날짜
	$curdate = date( "Y-m-d H:i:s" );

    //주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	$paymemt_type = "";
    $arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->sender_name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[ORDERCODE]"     => $ordercode,				//주문번호
		"[ORDERTELL]"     => $_ord->sender_tel,			//보내는이 전화
		"[ORDEREMAIL]"    => $email,					 //메일
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]], // 결제형태
		"[RECEIVERNAME]"  => $_ord->receiver_name,  // 받는이 이름
		"[RECEIVERTELL1]"  => $_ord->receiver_tel1, //받는이 일반전화
		"[RECEIVERTELL2]"  => $_ord->receiver_tel2, //받는이 핸드폰
		"[RECEIVERADDR]"  => $_ord->receiver_addr,  //받는이 주소
		"[RECEIVERPOST5]" => $_ord->post5,			//우편번호 5자리
		"[ORDMSG]"        => $_ord->order_msg2,		// 배송 메세지
		"[PRICE]"         => $_ord->price,			// 상품금액
		"[PAYDATA]"       => $_ord->pay_data,		//입금정보
		"[ORDERDATE]"         => $orderdate,			// 주문일
		//"[PRO_CODE]"         => $_ord->price,			// 품명
		//"[PRO_COLOR]"         => $_ord->price,			// 색상
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/ordermail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/ordermail{$mail_type}.php");
		$buffer = ob_get_contents();
		ob_end_clean();
		$body = $buffer;

	}

    $subject = $shopname." 주문내역서 확인 메일입니다.";

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

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	}

    if( strlen( $_ord->bank_date ) == 14 ){
		//SendVenderForm( $shopname, $shopurl, $ordercode, $mail_type, $info_email );
    }

}

//상품발송완료메일
function SendDeliMail_back($shopname, $shopurl, $mail_type, $info_email, $ordercode, $deli_com, $deli_num, $delimailtype) {

   	$shopname = '신원몰';
 	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
   	/*
   	$sql = " SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, p.color_code, p.prodcode,op.quantity, op.price, op.reserve, op.date, op.coupon_price,
   				op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage
   					FROM tblorderproduct op left join tblproduct p on op.productcode=p.productcode
   						WHERE op.ordercode = '{$ordercode}'
   	";*/
	$result=pmysql_query($sql,get_db_conn());
	$_ord=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($_ord) {
		$email=$_ord->sender_email;
		$patterns = array(" ","_","-");
		$replace = "";
		$deli_num = str_replace($patterns,$replace,$deli_num);

		if(ord($deli_com) && ord($deli_num)) {
			$sql="SELECT * FROM tbldelicompany WHERE code='{$deli_com}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$deliurl=$row->deli_url;
				$delicom=$row->company_name;
				$transnum=$row->trans_num;
			}
			pmysql_free_result($result);
		}

		$sql = "SELECT * FROM tbldesignnewpage WHERE type='delimail' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			//개별디자인
			$pattern = array ("[SHOP]");
			$replace = array ($shopname);
			$subject = str_replace($pattern,$replace,$row->subject).($delimailtype=="Y"?" [송장이 변경되었습니다.]":"");
			$body	 = $row->body;
		} else {
			//템플릿
			$subject = $shopname." 발송 메일입니다.".($delimailtype=="Y"?" [송장이 변경되었습니다.]":"");
			$buffer="";
			if(file_exists(DirPath.TempletDir."mail/delimail{$mail_type}.php")) {
				$buffer = file_get_contents(DirPath.TempletDir."mail/delimail{$mail_type}.php");
				$body=$buffer;
			}
		}
		pmysql_free_result($result);
		if(ord($body)) {
			$orderdate = substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2);
			$delidate = date("Y/m/d");
			if(strpos($body,"[IFDELICHANGE]")!=0) {
				$ifdelichange=strpos($body,"[IFDELICHANGE]");
				$elsedelichange=strpos($body,"[ELSEDELICHANGE]");
				$enddelichange=strpos($body,"[ENDDELICHANGE]");

				$yesdelichange=substr($body,$ifdelichange+14,$elsedelichange-$ifdelichange-14);
				$nodelichange =substr($body,$elsedelichange+17,$enddelichange-$elsedelichange-17);

				if($delimailtype=="Y") {
					$changemsg=$yesdelichange;
				} else {
					$changemsg=$nodelichange;
				}
				$body=substr($body,0,$ifdelichange-1).$changemsg.substr($body,$enddelichange+15);
			}
			if(strpos($body,"[IFDELINUM]")!=0) {
				$ifdelinum=strpos($body,"[IFDELINUM]");
				$enddelinum=strpos($body,"[ENDDELINUM]");
				$yesdelinum=substr($body,$ifdelinum+11,$enddelinum-$ifdelinum-11);

				if(strpos($body,"[IFDELIURL]")!=0) { 
					$ifurl=strpos($yesdelinum,"[IFDELIURL]");
					$elseurl=strpos($yesdelinum,"[ELSEDELIURL]");
					$endurl=strpos($yesdelinum,"[ENDDELIURL]");

					$yesdeliurl=substr($yesdelinum,$ifurl+11,$elseurl-$ifurl-11);
					$nodeliurl =substr($yesdelinum,$elseurl+14,$endurl-$elseurl-14);

					if(ord($deli_com) && $deli_num>0) {
						if(ord($deliurl)) {
							if(ord($transnum)) {
								$artransnum=explode(",",$transnum);
								$trpatten=array("[1]","[2]","[3]","[4]");
								$trreplace=array(substr($deli_num,0,$artransnum[0]),substr($deli_num,$artransnum[0],$artransnum[1]),substr($deli_num,$artransnum[0]+$artransnum[1],$artransnum[2]),substr($deli_num,$artransnum[0]+$artransnum[1]+$artransnum[2],$artransnum[3]));
								$deliurl=str_replace($trpatten,$trreplace,$deliurl);
								$yesdeliurl=str_replace('[DELIVERYURL][DELIVERYNUM]','[DELIVERYURL]',$yesdeliurl);
							} else {
								$deliurl=$deliurl.$deli_num;
							}
							$delivery =  substr($yesdelinum,0,$ifurl-1).$yesdeliurl.substr($yesdelinum,$endurl+12);
						} else {
							$delivery =  substr($yesdelinum,0,$ifurl-1).$nodeliurl.substr($yesdelinum,$endurl+12);
						}
					}
				} else {
					$delivery=$yesdelinum;
				}
				$body=substr($body,0,$ifdelinum-1).$delivery.substr($body,$enddelinum+12);
			}
			$patten = array ("[SHOP]","[DELIVERYURL]","[DELIVERYNUM]","[DELIVERYCOMPANY]","[URL]","[DELIVERYDATE]","[ORDERDATE]");
			$replace = array ($shopname,$deliurl,$deli_num,$delicom,$shopurl,$delidate,$orderdate);
			$body = str_replace($patten,$replace,$body);

			if (ord($shopname)) $mailshopname = "=?utf-8?b?".base64_encode($shopname)."?=";
			if (ord($subject)) $subject = "=?utf-8?b?".base64_encode($subject)."?=";
			$header=getMailHeader($mailshopname,$info_email);
			if(ismail($email)) {
				sendmail($email, $subject, $body, $header);
			}
		}
	} 
}

//상품발송완료메일
function SendDeliMail( $shopname, $shopurl, $mail_type, $info_email, $ordercode, $deli_com, $deli_num, $delimailtype, $idx='' ) {

    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$oderproduct = array();

	// 주문정보
	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
	$result = pmysql_query( $sql, get_db_conn() );
	if( $row = pmysql_fetch_object( $result ) ) {
		$_ord  = $row;

		$deli_num = str_replace($patterns,$replace,$deli_num);

		if(ord($deli_com) && ord($deli_num)) {
			$sql="SELECT * FROM tbldelicompany WHERE code='{$deli_com}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$deliurl=$row->deli_url;
				$delicom=$row->company_name;
				$transnum=$row->trans_num;
			}
			pmysql_free_result($result);
		}

		$email = $_ord->sender_email; // 받는이
		if( $row->id[0] == 'X' ) $guest_type = "guest";
	} else {
		$ordercode = "";
	}
	pmysql_free_result( $result );
	$op_sql = "SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, op.quantity, op.price, op.reserve, op.date, op.coupon_price, ";
	$op_sql.= "op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage "; //text_opt_subject, text_opt_content
	$op_sql.= "FROM tblorderproduct op left join tblproduct p on op.productcode=p.productcode  WHERE op.ordercode = '".$ordercode."' ";
	if( $idx != '' ) $op_sql.= "AND op.idx IN ('".str_replace(",","','",trim($idx))."') ";
	$op_sql.= "ORDER BY op.vender ASC  ";
	$op_res = pmysql_query( $op_sql, get_db_conn() );
	while( $op_row = pmysql_fetch_object( $op_res ) ){
		$orderproduct[$op_row->vender][] = $op_row;
	}
	pmysql_free_result( $op_res );

	//오늘 날짜
	$curdate = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	$paymemt_type = "";
    $arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->sender_name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[ORDERCODE]"     => $ordercode,				//주문번호
		"[ORDERTELL]"     => $_ord->sender_tel,			//보내는이 전화
		"[ORDEREMAIL]"    => $email,					 //메일
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]], // 결제형태
		"[RECEIVERNAME]"  => $_ord->receiver_name,  // 받는이 이름
		"[RECEIVERTELL1]"  => $_ord->receiver_tel1, //받는이 일반전화
		"[RECEIVERTELL2]"  => $_ord->receiver_tel2, //받는이 핸드폰
		"[RECEIVERADDR]"  => $_ord->receiver_addr,  //받는이 주소
		"[RECEIVERPOST5]" => $_ord->post5,			//우편번호 5자리
		"[ORDMSG]"        => $_ord->order_msg2,		// 배송 메세지
		"[PRICE]"         => $_ord->price,			// 상품금액
		"[DELICOM]"         => $delicom,			// 배송지
		"[DELINUM]"         => $deli_num,			// 송장번호
		"[ORDERDATE]"         => $orderdate,			// 주문일

	);

	$buffer="";

	if(file_exists(DirPath.TempletDir."mail/delimail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/delimail{$mail_type}.php");
		
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
        $subject = $shopname." 상품발송 메일입니다.".($delimailtype=="Y"?" [송장이 변경되었습니다.]":"");
		$body = str_replace( $pattern, $replace, $body );
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}
	
	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	}
}

//아이디/패스워드안내메일
function SendPassMail($shopname, $shopurl, $mail_type, $info_email, $email, $name, $id, $passwd) {

	$shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$oderproduct = array();
	
	$sql = "SELECT * from tblmember WHERE id = '".$id."'";
	$result = pmysql_query( $sql, get_db_conn() );
	if( $row = pmysql_fetch_object( $result ) ) {
		$_ord  = $row;
		$email = $_ord->email; // 받는이
		if( $row->id[0] == 'X' ) $guest_type = "guest";
	} else {
		$ordercode = "";
	}
	
	pmysql_free_result( $result );
	
	$patten_arr = array(
			"[SHOP]"        => $shopname,			//샵 명칭
			"[NAME]"        => $_ord->name,			//주문자 이름
			"[ID]"       	=> $id,					//오늘날짜
			"[PASSWORD]"    => $passwd,				//메세지
	);
	
	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/passmailTEM_001.php")) {
		ob_start();
		include(DirPath.TempletDir."mail/passmailTEM_001.php");
		$buffer = ob_get_contents();
		ob_end_clean();
		$body = $buffer;
	}
	
	$subject = $shopname." 패스워드 안내메일입니다.";
	
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
		$header = getMailHeader( $mailshopname, "help@sw.co.kr" );
	}
	
	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	}
	
	if( strlen( $_ord->bank_date ) == 14 ){
		//SendVenderForm( $shopname, $shopurl, $ordercode, $mail_type, $info_email );
	}
}

function SendIdMail($shopname, $shopurl, $mail_type, $info_email, $email, $name, $id) {

    $shopname = '신원몰';
	$sql = "SELECT * FROM tbldesignnewpage WHERE type='passmail' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("[SHOP]","[NAME]");
		$replace = array ($shopname,$name);
		$subject = str_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $shopname." 아이디 안내메일입니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/passmail{$mail_type}.php")) {
			//$buffer = file_get_contents(DirPath.TempletDir."mail/passmail{$mail_type}.php");
			ob_start();
			include(DirPath.TempletDir."mail/passmail{$mail_type}.php");
			$buffer = ob_get_contents();
			$body=$buffer;
			ob_end_clean();
		}
	}
	pmysql_free_result($result);
	if(ord($body)) {
		$pattern_arr = array(
								"[SHOP]" => $shopname,
								"[NAME]" => $name,
								"[URL]" => $shopurl,
								"[ID]" => $id,
								"[PASSWORD]" => $passwd,
								"[CURDATE]" => $curdate,
								"[EMAIL]" => $email
							);
		$pattern = array();
		$replace = array();
		unset($pattern);
		unset($replace);
		foreach($pattern_arr as $k=>$v){
			$pattern[]=$k;
			$replace[]=$v;
		}
		$body	 = str_replace($pattern,$replace,$body);
		if (ord($shopname)) $mailshopname = "=?utf-8?b?".base64_encode($shopname)."?=";
		if (ord($subject)) $subject = "=?utf-8?b?".base64_encode($subject)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

// 등급변경 메일
function SendGradeMail($shopname, $shopurl, $mail_type, $info_email, $id, $name, $bf_group, $af_group, $email, $news_yn) {

    /* $shopname = '신원몰';

    //템플릿
    $subject = $shopname." 회원등급 변경안내 메일입니다.";
    $buffer="";
    if(file_exists(DirPath.TempletDir."mail/grade_".$af_group.$mail_type.".php")) {
        ob_start();
        include(DirPath.TempletDir."mail/grade_".$af_group.$mail_type.".php");
        $buffer = ob_get_contents();
        $body = $buffer;
        ob_end_clean();
    }

    // 등급별 정보
    $sql = "SELECT  group_code, group_name, group_level, group_ap_s, group_ap_e 
            FROM    tblmembergroup 
            ORDER BY group_code 
            ";
    $ret = pmysql_query($sql);
    $grade = array();
    while($row = pmysql_fetch_object($ret)) {
        $grade[$row->group_code] = $row;
    }
    pmysql_free_result( $ret );

	if(ord($body)) {
        $pattern_arr = array(
                "[SHOP]"            => $shopname,               // 샵 명칭
                "[PRE_GRADE]"       => strtoupper($grade[$bf_group]->group_name),  // 이전 등급
                "[MEMBER_NAME]"     => $name,                   // 이름 
                "[URL]"             => $shopurl,                // shop url
                "[CURDATE]"         => date("Y.m.d"),
            );

		$pattern = array();
		$replace = array();
		unset($pattern);
		unset($replace);
		foreach($pattern_arr as $k=>$v){
			$pattern[]=$k;
			$replace[]=$v;
		}
		$body	 = str_replace($pattern,$replace,$body);
		if (ord($shopname)) $mailshopname = "=?utf-8?b?".base64_encode($shopname)."?=";
		if (ord($subject)) $subject = "=?utf-8?b?".base64_encode($subject)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	} */
}

//입금확인메일 sms_type = 1
function SendBankMail($shopname, $shopurl, $mail_type, $info_email, $email, $ordercode) {

    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$oderproduct = array();

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
	$op_sql = "SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, op.quantity, op.price, op.reserve, op.date, op.coupon_price, ";
	$op_sql.= "op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage "; //text_opt_subject, text_opt_content
	$op_sql.= "FROM tblorderproduct op left join tblproduct p on op.productcode=p.productcode  WHERE op.ordercode = '".$ordercode."' ORDER BY op.vender ASC ";
	$op_res = pmysql_query( $op_sql, get_db_conn() );
	while( $op_row = pmysql_fetch_object( $op_res ) ){
		$orderproduct[$op_row->vender][] = $op_row;
	}
	pmysql_free_result( $op_res );

	//오늘 날짜
	$curdate = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	$paymemt_type = "";
    $arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->sender_name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[ORDERCODE]"     => $ordercode,				//주문번호
		"[ORDERTELL]"     => $_ord->sender_tel,			//보내는이 전화
		"[ORDEREMAIL]"    => $email,					 //메일
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]], // 결제형태
		"[RECEIVERNAME]"  => $_ord->receiver_name,  // 받는이 이름
		"[RECEIVERTELL1]"  => $_ord->receiver_tel1, //받는이 일반전화
		"[RECEIVERTELL2]"  => $_ord->receiver_tel2, //받는이 핸드폰
		"[RECEIVERADDR]"  => $_ord->receiver_addr,  //받는이 주소
		"[RECEIVERPOST5]" => $_ord->post5,			//우편번호 5자리
		"[ORDMSG]"        => $_ord->order_msg2,		// 배송 메세지
		"[PRICE]"         => $_ord->price,			// 상품금액
		"[ORDERDATE]"         => $orderdate,			// 주문일자
		"[PAYDATA]"       => $_ord->pay_data,		//입금정보

	);

	$buffer="";

	if(file_exists(DirPath.TempletDir."mail/bankmail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/bankmail{$mail_type}.php");
		
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
        $subject = $shopname." 입금 확인 메일입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	
	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	}

    if( strlen( $_ord->bank_date ) == 14 ){
		//SendVenderForm( $shopname, $shopurl, $ordercode, $mail_type, $info_email, 1 );
    }
}



//입금확인메일
function SendBankMail_back($shopname, $shopurl, $mail_type, $info_email, $email, $ordercode) {
// 	$sql = "SELECT * FROM tbldesignnewpage WHERE type='bankmail' ";
// 	$result=pmysql_query($sql,get_db_conn());
// 	if($row=pmysql_fetch_object($result)) {
// 		//개별디자인
// 		$pattern = array ("[SHOP]");
// 		$replace = array ($shopname);
// 		$subject = str_replace($pattern,$replace,$row->subject);
// 		$body	 = $row->body;
// 	} else {
// 		//템플릿
// 		$subject = $shopname." 입금 확인 메일입니다.";
// 		$buffer="";
// 		if(file_exists(DirPath.TempletDir."mail/bankmail{$mail_type}.php")) {
// 			//$buffer = file_get_contents(DirPath.TempletDir."mail/bankmail{$mail_type}.php");
// 			ob_start();
// 			include(DirPath.TempletDir."mail/bankmail{$mail_type}.php");
// 			$buffer = ob_get_contents();
// 			$body=$buffer;
// 			ob_end_clean();
// 		}
// 	}
// 	pmysql_free_result($result);
// 	if(ord($body)) {
// 		$orderdate = substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2);
// 		$bankdate = date("Y.m.d");
		
// 		$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
// 		$result=pmysql_query($sql,get_db_conn());
// 		if($row=pmysql_fetch_object($result)){
// 			$_ord = $row;
// 		}
// 		##### 결제 방법
// 		$paymemt_type = "";
// 		if(strstr("VCPM", $_ord->paymethod[0])) {
// 			$arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰");
// 			$paymemt_type = $arpm[$_ord->paymethod[0]];

// 			if ($_ord->pay_flag=="0000") {
// 				if(strstr("CP", $_ord->paymethod[0])) {
// 					$paymemt_type.="(승인번호 : {$_ord->pay_auth_no}) ";
// 				} else {
// 					$paymemt_type.="";
// 				}
// 			} else if(ord($_ord->pay_flag))
// 				$paymemt_type.="(거래결과 : <font color=red><b><u>{$_ord->pay_data}</u></b></font>)\n";
// 			else
// 				$paymemt_type.="(<font color=red>(지불실패)</font>)";

// 			if (strstr("CPM", $_ord->paymethod[0]) && $_data->card_payfee>0){
// 				//$paymemt_type.="<br>&nbsp\n".$arpm[$_ord->paymethod[0]]." 결제시 현금 할인가 적용이 안됩니다.";
// 			}

// 		} else if (strstr("BOQ", $_ord->paymethod[0])) {
// 			if(strstr("B", $_ord->paymethod[0])) $paymemt_type.="무통장 입금 - <font color=#0054A6>{$_ord->pay_data}</font>";
// 			else {
// 				if($_ord->pay_flag=="0000") $msg = "";
// 				if(strstr("O", $_ord->paymethod[0])) $paymemt_type.="가상계좌 : <font color=#0054A6>{$_ord->pay_data}</font> ".$msg;
// 				else if(strstr("Q", $_ord->paymethod[0])) $paymemt_type.="매매보호 - 가상계좌 : <font color=#0054A6>{$_ord->pay_data}</font> ".$msg;
// 			}
// 		}		
		

// 		$pattern_arr = array(
// 								"[SHOP]" => $shopname,
// 								"[URL]" => $shopurl,
// 								"[ORDERCODE]" => $ordercode,
// 								"[BANKDATE]" => $bankdate,
// 								"[ORDERDATE]" => $orderdate,
// 								"[NAME]" => $_ord->sender_name,
// 								"[PAYTYPE]" => $paymemt_type,
// 								"[PRICE]" => number_format($_ord->price),
// 								"[CURDATE]" => $curdate,
// 								"[EMAIL]" => $email
// 							);
// 		$pattern = array();
// 		$replace = array();
// 		unset($pattern);
// 		unset($replace);
// 		foreach($pattern_arr as $k=>$v){
// 			$pattern[]=$k;
// 			$replace[]=$v;
// 		}
// 		$body	 = str_replace($pattern,$replace,$body);
// 		if (ord($shopname)) $mailshopname = "=?utf-8?b?".base64_encode($shopname)."?=";
// 		if (ord($subject)) $subject = "=?utf-8?b?".base64_encode($subject)."?=";
// 		$header=getMailHeader($mailshopname,$info_email);
// 		if(ismail($email)) {
// 			sendmail($email, $subject, $body, $header);
// 		}
// 	}
}


//회원인증메일
function SendAuthMail($shopname, $shopurl, $mail_type, $info_email, $email, $id) {

//     $shopname = '신원몰';
// 	$sql = "SELECT * FROM tbldesignnewpage WHERE type='authmail' ";
// 	$result=pmysql_query($sql,get_db_conn());
// 	if($row=pmysql_fetch_object($result)) {
// 		//개별디자인
// 		$pattern = array ("[SHOP]","[ID]");
// 		$replace = array ($shopname,$id);
// 		$subject = str_replace($pattern,$replace,$row->subject);
// 		$body	 = $row->body;
// 	} else {
// 		//템플릿
// 		$subject = $shopname." 회원 인증 메일입니다.";
// 		$buffer="";
// 		if(file_exists(DirPath.TempletDir."mail/authmail{$mail_type}.php")) {
// 			$buffer = file_get_contents(DirPath.TempletDir."mail/authmail{$mail_type}.php");
// 			$body=$buffer;
// 		}
// 	}
// 	pmysql_free_result($result);
// 	if(ord($body)) {
// 		$okdate=date("Y/m/d");
// 		$pattern = array ("[SHOP]","[URL]","[OKDATE]","[ID]");
// 		$replace = array ($shopname,$shopurl,$okdate,$id);
// 		$body	 = str_replace($pattern,$replace,$body);
// 		if (ord($shopname)) $mailshopname = "=?utf-8?b?".base64_encode($shopname)."?=";
// 		if (ord($subject)) $subject = "=?utf-8?b?".base64_encode($subject)."?=";
// 		$header=getMailHeader($mailshopname,$info_email);
// 		if(ismail($email)) {
// 			sendmail($email, $subject, $body, $header);
// 		}
// 	}
}
// 개별/ 단체 메일
function sendMailForm($sender_name,$sender_email,$message,$upfile,&$bodytext,&$mailheaders) {
// 	$boundary = "--------" . uniqid("part");

//     $mailheaders  = "MIME-Version: 1.0\r\n";
//     $mailheaders .= "Content-Type: text/html; charset=UTF-8\r\n";
// 	$mailheaders .= "From: $sender_name <$sender_email>\r\n";
// 	//$mailheaders .= "X-Mailer:SendMail\r\n";
	

// 	if ($upfile && $upfile["size"]>0) {	// 첨부파일 있으면...
// 		$mailheaders .= "Content-Type: Multipart/mixed; boundary=\"$boundary\"";
// 		//$bodytext  = "This is a multi-part message in MIME format.\r\n";
// 		//$bodytext .= "\r\n--$boundary\r\n";
// 		//$bodytext .= "Content-Type: text/html; charset=utf-8\r\n";
// 		//$bodytext .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
// 		$bodytext .= $message . "\r\n\r\n";

// 		$filename = basename($upfile["name"]);
// 		$file = file_get_contents($upfile["tmp_name"]);

// 		if ($upfile["type"]=="") {
// 			$upfile["type"] = "application/octet-stream";
// 		}

// 		$bodytext .= "\r\n--$boundary\r\n";
// 		$bodytext .= "Content-Type: {$upfile['type']}; name=\"$filename\"\r\n";
// 		$bodytext .= "Content-Transfer-Encoding: base64\r\n";
// 		$bodytext .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
// 		$bodytext .= chunk_split(base64_encode($file))."\r\n";
// 		$bodytext .= "\r\n--{$boundary}--\r\n";
// 	} else {
// 		//$mailheaders .= "Content-Type: text/html;";
// 		$bodytext .= $message . "\r\n\r\n";
// 	}
}

//가입 인증 메일 - 회원가입에서 이메일 인증으로 회원 가입페이지로 오게 하는... (2015.10.29 - 김재수)
function SendJoinCertMail($shopname, $shopurl, $mail_type, $join_msg, $info_email, $email, $name='', $id='', $rfcode='') {

//     $shopname = '신원몰';
	
// 		//템플릿
// 		$subject = "[".$shopname."] 인증확인 메일입니다.";
// 		$buffer="";
// 		if(file_exists(DirPath.TempletDir."mail/joincertmail{$mail_type}.php")) {
// 			//$buffer = file_get_contents(DirPath.TempletDir."mail/joinmail{$mail_type}.php");
// 			ob_start();
// 			include(DirPath.TempletDir."mail/joincertmail{$mail_type}.php");
// 			$buffer = ob_get_contents();
// 			$body=$buffer;
// 			ob_end_clean();
// 		}

// 		if ($rfcode) $rfcode	= "?rfcode=".$rfcode;

// 	$curdate = date("Y.m.d");
// 	if(ord($body)) {
// 		$pattern_arr = array(
// 								"[SHOP]" => $shopname,
// 								"[NAME]" => $name,
// 								"[MESSAGE]" => $join_msg,
// 								"[URL]" => $shopurl,
// 								"[ID]" => $id,
// 								"[CURDATE]" => $curdate,
// 								"[EMAIL]" => $email,
// 								"[RFCODE]" => $rfcode
// 							);
// 		$pattern = array();
// 		unset($pattern);
// 		$replace = array();
// 		unset($replace);
// 		foreach($pattern_arr as $k=>$v){
// 			$pattern[]=$k;
// 			$replace[]=$v;
// 		}

// 		$body	 = str_replace($pattern,$replace,$body);
// 		if (ord($shopname)) $mailshopname = "=?utf-8?b?".base64_encode($shopname)."?=";
// 		if (ord($subject)) $subject = "=?utf-8?b?".base64_encode($subject)."?=";
// 		$header=getMailHeader($mailshopname,$info_email);
// 		if(ismail($email)) {
// 			sendmail($email, $subject, $body, $header);
// 		}
// 	}
}

//이메일로 패스워드 안내 메일 보내기(2015.11.03 - 김재수)
function SendPasswordMail($shopname, $shopurl, $mail_type, $info_email, $email, $name, $id, $passwd) {

//     $shopname = '신원몰';

// 	//템플릿
// 	$subject = $shopname." 패스워드 안내메일입니다.";
// 	$buffer="";
// 	if(file_exists(DirPath.TempletDir."mail/passwordmail_{$mail_type}.php")) {
// 		//$buffer = file_get_contents(DirPath.TempletDir."mail/passmail{$mail_type}.php");
// 		ob_start();
// 		include(DirPath.TempletDir."mail/passwordmail_{$mail_type}.php");
// 		$buffer = ob_get_contents();
// 		$body=$buffer;
// 		ob_end_clean();
// 	}

// 	if(ord($body)) {
// 		$pattern_arr = array(
// 								"[SHOP]" => $shopname,
// 								"[NAME]" => $name,
// 								"[URL]" => $shopurl,
// 								"[ID]" => $id,
// 								"[PASSWORD]" => $passwd,
// 								"[CURDATE]" => $curdate,
// 								"[EMAIL]" => $email
// 							);
// 		$pattern = array();
// 		$replace = array();
// 		unset($pattern);
// 		unset($replace);
// 		foreach($pattern_arr as $k=>$v){
// 			$pattern[]=$k;
// 			$replace[]=$v;
// 		}
// 		$body	 = str_replace($pattern,$replace,$body);
// 		if (ord($shopname)) $mailshopname = "=?utf-8?b?".base64_encode($shopname)."?=";
// 		if (ord($subject)) $subject = "=?utf-8?b?".base64_encode($subject)."?=";
// 		$header=getMailHeader($mailshopname,$info_email);
// 		if(ismail($email)) {
// 			sendmail($email, $subject, $body, $header);
// 		}
// 	}
}

//주문취소메일 sms_type = 2
function SendCancelMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg, $oc_no) {

    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$oderproduct = array();

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

/*
	$op_sql = "SELECT vender, ordercode, productcode, productname, opt1_name, opt2_name, quantity, price, reserve, date, ";
	$op_sql.= "selfcode, option_price, option_quantity, option_type, idx, basketidx ";
	$op_sql.= "FROM tblorderproduct WHERE ordercode = '".$ordercode."' ORDER BY vender ASC ";*/
	$op_sql = "SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, op.quantity, op.price, op.reserve, op.date, op.oc_no, op.coupon_price, p.color_code, p.prodcode,";
	$op_sql.= "op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage "; //text_opt_subject, text_opt_content
	$op_sql.= "FROM tblorderproduct op left join tblproduct p on op.productcode=p.productcode  WHERE op.ordercode = '".$ordercode."' and op.oc_no='".$oc_no."' ORDER BY op.vender ASC ";
	$op_res = pmysql_query( $op_sql, get_db_conn() );
	while( $op_row = pmysql_fetch_object( $op_res ) ){
		//$orderproduct[$op_row->vender][$op_row->basketidx][] = $op_row;
		$orderproduct[$op_row->vender][] = $op_row;
	}
	pmysql_free_result( $op_res );

	//오늘 날짜
	$curdate = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	$paymemt_type = "";
    $arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->sender_name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[ORDERCODE]"     => $ordercode,				//주문번호
		"[ORDERTELL]"     => $_ord->sender_tel,			//보내는이 전화
		"[ORDEREMAIL]"    => $email,					 //메일
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]], // 결제형태
		"[RECEIVERNAME]"  => $_ord->receiver_name,  // 받는이 이름
		"[RECEIVERTELL1]"  => $_ord->receiver_tel1, //받는이 일반전화
		"[RECEIVERTELL2]"  => $_ord->receiver_tel2, //받는이 핸드폰
		"[RECEIVERADDR]"  => $_ord->receiver_addr,  //받는이 주소
		"[RECEIVERPOST5]" => $_ord->post5,			//우편번호 5자리
		"[ORDMSG]"        => $_ord->order_msg2,		// 배송 메세지
		"[PRICE]"         => $_ord->price,			// 상품금액
		"[ORDERDATE]"         => $orderdate,			// 주문일자
		"[PAYDATA]"       => $_ord->pay_data,		//입금정보
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/ordercancelmail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/ordercancelmail{$mail_type}.php");
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
        $subject = $shopname." 주문취소 메일입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	}

	//SendVenderForm( $shopname, $shopurl, $ordercode, $mail_type, $info_email, 2, $oc_no );
}


//반품요청메일 sms_type = 3
function SendReturnMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg, $oc_no) {

    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$oderproduct = array();

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

	$op_sql = "SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, op.quantity, op.price, op.reserve, op.date, op.oc_no, op.coupon_price,";
	$op_sql.= "op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage, p.color_code, p.prodcode "; //text_opt_subject, text_opt_content
	$op_sql.= "FROM tblorderproduct op left join tblproduct p on op.productcode=p.productcode  WHERE op.ordercode = '".$ordercode."' and op.oc_no='".$oc_no."' ORDER BY op.vender ASC ";
	$op_res = pmysql_query( $op_sql, get_db_conn() );
	while( $op_row = pmysql_fetch_object( $op_res ) ){
		
		$orderproduct[$op_row->vender][] = $op_row;
	}
	pmysql_free_result( $op_res );

	
	
	//오늘 날짜
	$curdate = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	$paymemt_type = "";
    $arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->sender_name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[ORDERCODE]"     => $ordercode,				//주문번호
		"[ORDERTELL]"     => $_ord->sender_tel,			//보내는이 전화
		"[ORDEREMAIL]"    => $email,					 //메일
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]], // 결제형태
		"[RECEIVERNAME]"  => $_ord->receiver_name,  // 받는이 이름
		"[RECEIVERTELL1]"  => $_ord->receiver_tel1, //받는이 일반전화
		"[RECEIVERTELL2]"  => $_ord->receiver_tel2, //받는이 핸드폰
		"[RECEIVERADDR]"  => $_ord->receiver_addr,  //받는이 주소
		"[RECEIVERPOST5]" => $_ord->post5,			//우편번호 5자리
		"[ORDMSG]"        => $_ord->order_msg2,		// 배송 메세지
		"[PRICE]"         => $_ord->price,			// 상품금액
		"[ORDERDATE]"         => $orderdate,			// 주문일자
		"[PAYDATA]"       => $_ord->pay_data,		//입금정보
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/orderreturnmail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/orderreturnmail{$mail_type}.php");
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
        $subject = $shopname." 반품 요청 메일입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	}
 
    // 반품신청시점이 아닌 반품접수처리 시점에 벤더에게 보내게 수정..따라서 주석 처리..2016-06-14 jhjeong
	//SendVenderForm( $shopname, $shopurl, $ordercode, $mail_type, $info_email, 3, $oc_no );
}


//반품완료메일
function SendReturnokMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg, $oc_no) {
/* 
    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$oderproduct = array();

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

	$op_sql = "SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, op.quantity, op.price, op.reserve, op.date, op.oc_no, op.coupon_price, ";
	$op_sql.= "op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage "; //text_opt_subject, text_opt_content
	$op_sql.= "FROM tblorderproduct op left join tblproduct p on op.productcode=p.productcode  WHERE op.ordercode = '".$ordercode."' and op.oc_no='".$oc_no."' ORDER BY op.vender ASC ";
	$op_res = pmysql_query( $op_sql, get_db_conn() );
	while( $op_row = pmysql_fetch_object( $op_res ) ){
		
		$orderproduct[$op_row->vender][] = $op_row;
	}
	pmysql_free_result( $op_res );

	//반품 내용
	$return_qry="select * from tblorder_cancel where oc_no='".$oc_no."' and ordercode='".$ordercode."'";
	$return_result= pmysql_query( $return_qry, get_db_conn() );
	$return_row = pmysql_fetch_object( $return_result );

	$return_date= substr($return_row->regdt,0,4)."-".substr($return_row->regdt,4,2)."-".substr($return_row->regdt,6,2)." ".substr($return_row->regdt,8,2).":".substr($return_row->regdt,10,2).":".substr($return_row->regdt,12,2);

	
	//오늘 날짜
	$curdate = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	$paymemt_type = "";
    $arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->sender_name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[ORDERCODE]"     => $ordercode,				//주문번호
		"[ORDERTELL]"     => $_ord->sender_tel,			//보내는이 전화
		"[ORDEREMAIL]"    => $email,					 //메일
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]], // 결제형태
		"[RECEIVERNAME]"  => $_ord->receiver_name,  // 받는이 이름
		"[RECEIVERTELL1]"  => $_ord->receiver_tel1, //받는이 일반전화
		"[RECEIVERTELL2]"  => $_ord->receiver_tel2, //받는이 핸드폰
		"[RECEIVERADDR]"  => $_ord->receiver_addr,  //받는이 주소
		"[RECEIVERPOST5]" => $_ord->post5,			//우편번호 5자리
		"[ORDMSG]"        => $_ord->order_msg2,		// 배송 메세지
		"[PRICE]"         => $_ord->price,			// 상품금액
		"[ORDERDATE]"         => $orderdate,			// 주문일자
		"[CANCELMEMO]"         => $return_row->memo,			// 취소메모
		"[RETRUNDATE]"         => $return_date,			// 취소일자
		"[PAYDATA]"       => $_ord->pay_data,		//입금정보
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/orderreturnokmail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/orderreturnokmail{$mail_type}.php");
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
        $subject = $shopname." 반품 완료 메일입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	} */
}

//환불안내메일
function SendRefundMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg, $oc_no) {

    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$oderproduct = array();

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

	$op_sql = "SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, op.quantity, op.price, op.reserve, op.date, op.oc_no, op.coupon_price,p.color_code, p.prodcode,";
	$op_sql.= "op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage "; //text_opt_subject, text_opt_content
	$op_sql.= "FROM tblorderproduct op left join tblproduct p on op.productcode=p.productcode  WHERE op.ordercode = '".$ordercode."' and op.oc_no='".$oc_no."' ORDER BY op.vender ASC ";
	$op_res = pmysql_query( $op_sql, get_db_conn() );
	while( $op_row = pmysql_fetch_object( $op_res ) ){
		
		$orderproduct[$op_row->vender][] = $op_row;
	}
	pmysql_free_result( $op_res );

	//반품 내용
	$return_qry="select * from tblorder_cancel where oc_no='".$oc_no."' and ordercode='".$ordercode."'";
	$return_result= pmysql_query( $return_qry, get_db_conn() );
	$return_row = pmysql_fetch_object( $return_result );

	$return_date= substr($return_row->regdt,0,4)."-".substr($return_row->regdt,4,2)."-".substr($return_row->regdt,6,2)." ".substr($return_row->regdt,8,2).":".substr($return_row->regdt,10,2).":".substr($return_row->regdt,12,2);

    $arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");

	if($return_row->pgcancel=="Y"){
//		$refund_content="신용카드 ".number_format($return_row->rprice)."원";
		$refund_content=$arpm[$_ord->paymethod[0]]." ".number_format($return_row->rprice)."원";
	}else{
		$refund_content="계좌이체 ".number_format($return_row->rprice)."원";
	}
	
	//오늘 날짜
	$curdate = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	$paymemt_type = "";
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->sender_name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[ORDERCODE]"     => $ordercode,				//주문번호
		"[ORDERTELL]"     => $_ord->sender_tel,			//보내는이 전화
		"[ORDEREMAIL]"    => $email,					 //메일
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]], // 결제형태
		"[RECEIVERNAME]"  => $_ord->receiver_name,  // 받는이 이름
		"[RECEIVERTELL1]"  => $_ord->receiver_tel1, //받는이 일반전화
		"[RECEIVERTELL2]"  => $_ord->receiver_tel2, //받는이 핸드폰
		"[RECEIVERADDR]"  => $_ord->receiver_addr,  //받는이 주소
		"[RECEIVERPOST5]" => $_ord->post5,			//우편번호 5자리
		"[ORDMSG]"        => $_ord->order_msg2,		// 배송 메세지
		"[PRICE]"         => $_ord->price,			// 상품금액
		"[ORDERDATE]"         => $orderdate,			// 주문일자
		"[CANCELMEMO]"         => $return_row->memo,			// 취소메모
		"[RETRUNDATE]"         => $return_date,			// 취소일자
		"[REFUNDC]"         => $refund_content,		// 환불수단
		"[PAYDATA]"       => $_ord->pay_data,		//입금정보
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/orderrefundmail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/orderrefundmail{$mail_type}.php");
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
        $subject = $shopname." 환불 안내 메일입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	}
}

//교환요청메일 sms_type = 4
function SendRequestMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg, $oc_no) {

    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$oderproduct = array();

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

	$op_sql = "SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, op.quantity, op.price, op.reserve, op.date, op.oc_no, op.coupon_price,p.color_code, p.prodcode, ";
	$op_sql.= "op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage, op.opt1_change, op.opt2_change "; //text_opt_subject, text_opt_content
	$op_sql.= "FROM tblorderproduct op left join tblproduct p on op.productcode=p.productcode  WHERE op.ordercode = '".$ordercode."' and op.oc_no='".$oc_no."' ORDER BY op.vender ASC ";
	$op_res = pmysql_query( $op_sql, get_db_conn() );
	while( $op_row = pmysql_fetch_object( $op_res ) ){
		
		$orderproduct[$op_row->vender][] = $op_row;
	}
	pmysql_free_result( $op_res );

	//반품 내용
	$return_qry="select * from tblorder_cancel where oc_no='".$oc_no."' and ordercode='".$ordercode."'";
	$return_result= pmysql_query( $return_qry, get_db_conn() );
	$return_row = pmysql_fetch_object( $return_result );

	$return_date= substr($return_row->regdt,0,4)."-".substr($return_row->regdt,4,2)."-".substr($return_row->regdt,6,2)." ".substr($return_row->regdt,8,2).":".substr($return_row->regdt,10,2).":".substr($return_row->regdt,12,2);


	
	//오늘 날짜
	$curdate = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	$paymemt_type = "";
    $arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->sender_name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[ORDERCODE]"     => $ordercode,				//주문번호
		"[ORDERTELL]"     => $_ord->sender_tel,			//보내는이 전화
		"[ORDEREMAIL]"    => $email,					 //메일
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]], // 결제형태
		"[RECEIVERNAME]"  => $_ord->receiver_name,  // 받는이 이름
		"[RECEIVERTELL1]"  => $_ord->receiver_tel1, //받는이 일반전화
		"[RECEIVERTELL2]"  => $_ord->receiver_tel2, //받는이 핸드폰
		"[RECEIVERADDR]"  => $_ord->receiver_addr,  //받는이 주소
		"[RECEIVERPOST5]" => $_ord->post5,			//우편번호 5자리
		"[ORDMSG]"        => $_ord->order_msg2,		// 배송 메세지
		"[PRICE]"         => $_ord->price,			// 상품금액
		"[ORDERDATE]"         => $orderdate,			// 주문일자
		"[CANCELMEMO]"         => $return_row->memo,			// 취소메모
		"[RETRUNDATE]"         => $return_date,			// 취소일자
		"[REFUNDC]"         => $refund_content,		// 환불수단
		"[PAYDATA]"       => $_ord->pay_data,		//입금정보
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/orderrequestmail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/orderrequestmail{$mail_type}.php");
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
       	$subject = $shopname." 교환 요청 메일입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	}
 
    // 교환신청시점이 아닌 교환접수처리 시점에 벤더에게 보내게 수정..따라서 주석 처리..2016-06-14 jhjeong
	//SendVenderForm( $shopname, $shopurl, $ordercode, $mail_type, $info_email, 4, $oc_no );
}


//교환완료메일
function SendRequestokMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg, $oc_no) {
/* 
    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$oderproduct = array();

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

	$op_sql = "SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, op.quantity, op.price, op.reserve, op.date, op.oc_no, op.coupon_price, ";
	$op_sql.= "op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage, op.opt1_change, op.opt2_change "; //text_opt_subject, text_opt_content
	$op_sql.= "FROM tblorderproduct op left join tblproduct p on op.productcode=p.productcode  WHERE op.ordercode = '".$ordercode."' and op.oc_no='".$oc_no."' ORDER BY op.vender ASC ";
	$op_res = pmysql_query( $op_sql, get_db_conn() );
	while( $op_row = pmysql_fetch_object( $op_res ) ){
		
		$orderproduct[$op_row->vender][] = $op_row;
	}
	pmysql_free_result( $op_res );

	//반품 내용
	$return_qry="select * from tblorder_cancel where oc_no='".$oc_no."' and ordercode='".$ordercode."'";
	$return_result= pmysql_query( $return_qry, get_db_conn() );
	$return_row = pmysql_fetch_object( $return_result );

	$return_date= substr($return_row->regdt,0,4)."-".substr($return_row->regdt,4,2)."-".substr($return_row->regdt,6,2)." ".substr($return_row->regdt,8,2).":".substr($return_row->regdt,10,2).":".substr($return_row->regdt,12,2);


	
	//오늘 날짜
	$curdate = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	$paymemt_type = "";
    $arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->sender_name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[ORDERCODE]"     => $ordercode,				//주문번호
		"[ORDERTELL]"     => $_ord->sender_tel,			//보내는이 전화
		"[ORDEREMAIL]"    => $email,					 //메일
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]], // 결제형태
		"[RECEIVERNAME]"  => $_ord->receiver_name,  // 받는이 이름
		"[RECEIVERTELL1]"  => $_ord->receiver_tel1, //받는이 일반전화
		"[RECEIVERTELL2]"  => $_ord->receiver_tel2, //받는이 핸드폰
		"[RECEIVERADDR]"  => $_ord->receiver_addr,  //받는이 주소
		"[RECEIVERPOST5]" => $_ord->post5,			//우편번호 5자리
		"[ORDMSG]"        => $_ord->order_msg2,		// 배송 메세지
		"[PRICE]"         => $_ord->price,			// 상품금액
		"[ORDERDATE]"         => $orderdate,			// 주문일자
		"[CANCELMEMO]"         => $return_row->memo,			// 취소메모
		"[RETRUNDATE]"         => $return_date,			// 취소일자
		"[REFUNDC]"         => $refund_content,		// 환불수단
		"[PAYDATA]"       => $_ord->pay_data,		//입금정보
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/orderrequestokmail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/orderrequestokmail{$mail_type}.php");
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
        $subject = $shopname." 교환 완료 메일입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	} */
}


//상품Qna 메일
function SendQnaMail($shopname, $shopurl, $mail_type, $info_email, $board, $num) {
/* 
    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	
	
	$query="select b.name, b.title, b.content, b.writetime, bc.comment, p.productcode, p.productname, p.model, p.sellprice, p.reserve, p.reservetype, p.tinyimage, m.email 
    from 
	tblboard b 
	left join tblproduct p on b.pridx=p.pridx
	left join tblboardcomment bc on b.num=bc.parent
	left join tblmember m on m.id=b.mem_id
	where b.num='".$num."'
	and b.board='".$board."'";
	
	$result = pmysql_query( $query, get_db_conn() );
	if( $row = pmysql_fetch_object( $result ) ) {
		$_ord  = $row;
		$email = $_ord->email; // 받는이
	}
	pmysql_free_result( $result );

	//적립금
	$p_reserve=number_format( getReserveConversion( $_ord->reserve, $_ord->reservetype, $_ord->sellprice, "N") );
	
	//이미지
	$img_check=stripos($_ord->tinyimage, "ttp:");
	
	if(!empty($img_check)){
		$p_img=$_ord->tinyimage;
	}else{
		$p_img="http://".$shopurl."/data/shopimages/product/".$_ord->tinyimage;
	}

    $product_link = "http://".$shopurl."/front/productdetail.php?productcode=".$_ord->productcode;

	//작성일
	$strDate = date("Y.m.d",$_ord->writetime);
	
	//오늘 날짜
	$curdate = date( "Y.m.d" );
	$curdate2 = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[CURDATE2]"       => $curdate2,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[NAME]"           => $_ord->name,				//작성자
		"[TITLE]"           => $_ord->title,			//제목
		"[CONTENT]"           => nl2br(stripslashes($_ord->content)),		//내용
		"[COMMENT]"           => nl2br(stripslashes($_ord->comment)),		//관리자답변
		"[REGDT]"           => $strDate,		//작성일
		"[PRODUCTNAME]"           => $_ord->productname,		//상품명
		"[SELLPRICE]"           => number_format($_ord->sellprice),		//상품금액
		"[RESERVE]"           => $p_reserve,		//적립금
		"[PRODUCTIMG]"           => $p_img,		//이미지
		"[PRODUCTLINK]"           => $product_link,		//이미지
		"[PRODUCTBRAND]"           => $_ord->model,		//상품브랜드
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/qnamail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/qnamail{$mail_type}.php");
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
        $subject = $shopname." 상품 문의에 대한 답변입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	} */
}

//상품1:1 메일
function SendInquiryMail($shopname, $shopurl, $mail_type, $info_email, $idx) {

	$shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';

/* 
	$query="select b.name, b.title, b.content, b.writetime, bc.comment, p.productcode, p.productname, p.model, p.sellprice, p.reserve, p.reservetype, p.tinyimage, m.email
    from
	tblboard b
	left join tblproduct p on b.pridx=p.pridx
	left join tblboardcomment bc on b.num=bc.parent
	left join tblmember m on m.id=b.mem_id
	where b.num='".$num."'
	and b.board='".$board."'";
	 */
	$query="SELECT ID,NAME,EMAIL,SUBJECT,DATE,CONTENT,RE_DATE,RE_CONTENT,HEAD_TITLE,RE_ID,RE_SUBJECT,CHK_MAIL,CHK_SMS FROM tblpersonal WHERE IDX = '{$idx}'";

	$result = pmysql_query( $query, get_db_conn() );
	if( $row = pmysql_fetch_object( $result ) ) {
		$_ord  = $row;
		$email = $_ord->email; // 받는이
	}
	pmysql_free_result( $result );

	//적립금
	$p_reserve=number_format( getReserveConversion( $_ord->reserve, $_ord->reservetype, $_ord->sellprice, "N") );

	//이미지
	$img_check=stripos($_ord->tinyimage, "ttp:");

	if(!empty($img_check)){
		$p_img=$_ord->tinyimage;
	}else{
		$p_img="http://".$shopurl."/data/shopimages/product/".$_ord->tinyimage;
	}

	$product_link = "http://".$shopurl."/front/productdetail.php?productcode=".$_ord->productcode;

	//작성일
	//$strDate = date("Y.m.d",$_ord->date);
	//$strDate = substr($_ord->date, 3).".".substr($_ord->date, 4,5).".".substr($_ord->date, 6,7) ;
	$strDate = substr($_ord->date,0,4).".".substr($_ord->date,4,2).".".substr($_ord->date,6,2);

	//오늘 날짜
	$curdate = date( "Y.m.d" );
	$curdate2 = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);


	$patten_arr = array(
			"[SHOP]"          => $shopname,					//샵 명칭
			"[CURDATE]"       => $curdate,					//오늘날짜
			"[CURDATE2]"       => $curdate2,					//오늘날짜
			"[MESSAGE]"       => $thankmsg,					//메세지
			"[URL]"           => $shopurl,					//shop url
			"[NAME]"           => $_ord->name,				//작성자
			"[TITLE]"           => $_ord->subject,			//제목
			"[CONTENT]"           => nl2br(stripslashes($_ord->content)),		//내용
			"[COMMENT]"           => nl2br(stripslashes($_ord->re_content)),		//관리자답변
			"[RETITLE]"           => nl2br(stripslashes($_ord->re_subject)),		//답변제목
			"[REGDT]"           => $strDate,		//작성일
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/inquirymail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/inquirymail{$mail_type}.php");
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
		$subject = $shopname." 1:1 문의에 대한 답변입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}
	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	}
}

//가상계좌안내
function SendAccountMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg) {
/* 
    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$bank_ex='';
	$bank_name='';
	$bank_num='';
	$oderproduct = array();

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
	
	//입금액
	$bank_price=($_ord->price+$_ord->deli_price)-($_ord->dc_price+$_ord->reserve);

	//입금은행
	#무통장입금
	if($_ord->paymethod=="B"){
		$bank_ex=explode(":",$_ord->pay_data);
		$bank_name=$bank_ex[0];
		$bank_num=$bank_ex[1].":".$bank_ex[2];
	
	#가상계좌
	}else if($_ord->paymethod=="O"){
		$bank_ex=explode(" ",$_ord->pay_data);
		$bank_name=$bank_ex[0];
		$bank_num=$bank_ex[1]." ".$bank_ex[2];
	
	}
	$bank_ex=explode(":",$_ord->pay_data);
		
	//오늘 날짜
	$curdate = date( "Y-m-d H:i:s" );

	//주문 날짜
	$orderdate = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);

	$paymemt_type = "";
    $arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");
	
	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->sender_name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[ORDERCODE]"     => $ordercode,				//주문번호
		"[ORDERTELL]"     => $_ord->sender_tel,			//보내는이 전화
		"[ORDEREMAIL]"    => $email,					 //메일
		"[PAYTYPE]"       => $arpm[$_ord->paymethod[0]], // 결제형태
		"[RECEIVERNAME]"  => $_ord->receiver_name,  // 받는이 이름
		"[RECEIVERTELL1]"  => $_ord->receiver_tel1, //받는이 일반전화
		"[RECEIVERTELL2]"  => $_ord->receiver_tel2, //받는이 핸드폰
		"[RECEIVERADDR]"  => $_ord->receiver_addr,  //받는이 주소
		"[RECEIVERPOST5]" => $_ord->post5,			//우편번호 5자리
		"[ORDMSG]"        => $_ord->order_msg2,		// 배송 메세지
		"[PRICE]"         => $_ord->price,			// 상품금액
		"[ORDERDATE]"         => $orderdate,			// 주문일자
		"[CANCELMEMO]"         => $return_row->memo,			// 취소메모
		"[RETRUNDATE]"         => $return_date,			// 취소일자
		"[BANKPRICE]"         => number_format($bank_price),			// 입금액
		"[BANKNAME]"         => $bank_name,			// 은행명
		"[BANKNUM]"         => $bank_num,			// 계좌번호
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/orderaccountmail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/orderaccountmail{$mail_type}.php");
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
        $subject = $shopname." 가상 계좌 안내 메일입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	} */
}

//마일리지 소멸 안내
function SendMilMail($shopname, $shopurl, $mail_type, $info_email, $m_id, $out_mil) {
/* 
    $shopname = '신원몰';
	$subject = '';
	$header  = '';
	$body    = '';
	$email   = '';
	$bank_ex='';
	$bank_name='';
	$bank_num='';
	$oderproduct = array();

	// 주문정보
	$sql = "SELECT * FROM tblmember WHERE id='".$m_id."' ";
	$result = pmysql_query( $sql, get_db_conn() );
	if( $row = pmysql_fetch_object( $result ) ) {
		$_ord  = $row;
		$email = $_ord->email; // 받는이
		if( $row->id[0] == 'X' ) $guest_type = "guest";
	}
	pmysql_free_result( $result );

	//오늘 날짜
	$curdate = date( "Y.m.d" );
	$curdate2 = date( "Y-m-d H:i:s" );
	$curdate3 = date( "m" )."월 ".date( "d" )."일";
	$curdate4 = date( "Y" )."년 ".date( "m" )."월 ".date( "d" )."일";

	
	
	//해당달 마지막날
	$enddate=date("Y.m.t",mktime(0,0,0,date('m'),1,date('Y'))); 


	$patten_arr = array(
		"[SHOP]"          => $shopname,					//샵 명칭
		"[NAME]"          => $_ord->name,		//주문자 이름
		"[CURDATE]"       => $curdate,					//오늘날짜
		"[CURDATE2]"       => $curdate2,					//오늘날짜
		"[CURDATE3]"       => $curdate3,					//오늘날짜
		"[CURDATE4]"       => $curdate4,					//오늘날짜
		"[MESSAGE]"       => $thankmsg,					//메세지
		"[URL]"           => $shopurl,					//shop url
		"[RESERVE]"           => number_format($_ord->reserve),					//보유마일리지
		"[OUTRESERVE]"           => number_format($out_mil),					//소멸예정 마일리지
		"[ENDDATE]"           => $enddate,					//마지막달
		
	);

	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/miloutmail{$mail_type}.php")) {

		ob_start();
		include(DirPath.TempletDir."mail/miloutmail{$mail_type}.php");
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
        $subject = $shopname." 마일리지 소멸 안내 메일입니다.";
		if ( ord( $shopname ) ) {
			$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
		}
		if ( ord( $subject ) ) {
			$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
		}
		$header = getMailHeader( $mailshopname, $info_email );
	}

	if( ismail( $email ) ) {
		sendmail( $email, $subject, $body, $header );
	} */
}


# 주문정보를 벤더에게 발송 2016-03-14 유동혁
/**
* 함수명 :SendVenderForm
* 주문정보를 벤더에게 발송
* parameter :
*   - String $shopname   : 상점명
*   - String $ordercode  : 주문코드
*   - String $mail_type  : 쇼핌몰 type
*   - String $info_email : 이메일 정보
*   - Int    $sms_type   : SMS 발송 Type ( 0 - 주문확인, 1 - 입금확인, 2 - 주문취소, 3 - 반품요청, 4 - 교환요청 )
*   - Int    $oc_no      : 주문 취소 번호
*/
function SendVenderForm( $shopname, $shopurl, $ordercode, $mail_type, $info_email, $sms_type = 0, $oc_no = 0 ){

//     $shopname = '신원몰';
//     if($shopname == "") {
//         $sql = "select shopname, replace(replace(shopurl, 'http://', ''), 'https://', '') as shopurl, design_mail, info_email from tblshopinfo limit 1";
//         list($shopname, $shopurl, $mail_type, $info_email) = pmysql_fetch($sql);
//     }

//     error_log("\r\n\r\n\r\n"."===================================================================================================",3,"/tmp/log_mail_hott_".date("Ymd"));
//     error_log("\r\n".date("Y-m-d H:i:s ")." ".$ordercode,3,"/tmp/log_mail_hott_".date("Ymd"));
//     error_log("\r\n"."shoopname = ".$shopname,3,"/tmp/log_mail_hott_".date("Ymd"));
//     error_log("\r\n"."shopurl = ".$shopurl,3,"/tmp/log_mail_hott_".date("Ymd"));
//     error_log("\r\n"."mail_type = ".$mail_type,3,"/tmp/log_mail_hott_".date("Ymd"));
//     error_log("\r\n"."info_email = ".$info_email,3,"/tmp/log_mail_hott_".date("Ymd"));
//     error_log("\r\n"."sms_type = ".$sms_type,3,"/tmp/log_mail_hott_".date("Ymd"));
//     error_log("\r\n"."oc_no = ".$oc_no,3,"/tmp/log_mail_hott_".date("Ymd"));
//     error_log("\r\n"."=============================================================",3,"/tmp/log_mail_hott_".date("Ymd"));
//     /*
//     error_log("\r\n"."=============================================================",3,"/tmp/log_sms_deco_".date("Ymd"));
//     error_log("\r\n".date("Y-m-d H:i:s ")." ".$ordercode,3,"/tmp/log_sms_deco_".date("Ymd"));
//     error_log("\r\n"."sms_type = ".$sms_type,3,"/tmp/log_sms_deco_".date("Ymd"));
//     error_log("\r\n"."oc_no = ".$oc_no,3,"/tmp/log_sms_deco_".date("Ymd"));
//     error_log("\r\n"."=============================================================",3,"/tmp/log_sms_deco_".date("Ymd"));
//     */
// 	 # 벤더 발송용  SMS
// 	$sms_sql     = "SELECT id, authkey, admin_tel, return_tel FROM tblsmsinfo ";
// 	$sms_result  = pmysql_query( $sms_sql, get_db_conn() );
// 	$sms_row     = pmysql_fetch_object( $sms_result );
// 	$sms_id      = $sms_row->id;
// 	$sms_authkey = $sms_row->authkey;
// 	$fromtel     = $sms_row->return_tel;
// 	$sms_date    = date("Y-m-d H:i:s");
// 	$mail_dir    = "";

// 	# 주문 정보
// 	$sql = "SELECT sender_name, sender_email, sender_tel, paymethod, receiver_name, receiver_tel1, ";
// 	$sql.= "receiver_tel2, receiver_addr, post5, order_msg2, price, pay_data ";
// 	$sql.= "FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
// 	$result = pmysql_query( $sql, get_db_conn() );
// 	if( $row = pmysql_fetch_object( $result ) ) {
// 		$_ord  = $row;
// 		if( $row->id[0] == 'X' ) $guest_type = "guest";
// 	} else {
// 		$ordercode = "";
// 	}
// 	pmysql_free_result( $result );

// 	# 상품별 벤더 정보
// 	$vender_sql = "SELECT op.vender, op.ordercode, vi.p_name, vi.p_mobile, vi.p_email, vi.com_name   ";
// 	$vender_sql.= "FROM ( SELECT vender, ordercode FROM tblorderproduct WHERE ordercode = '".$ordercode."' ";
// 	if( $oc_no > 0 ) $vender_sql.= "AND oc_no = '".$oc_no."' ";
// 	$vender_sql.= "GROUP BY vender, ordercode ) op ";
// 	$vender_sql.= "LEFT JOIN tblvenderinfo vi ON ( op.vender = vi.vender ) ";
// 	$vender_sql.= "ORDER BY op.ordercode, op.vender ";
// 	$vender_res = pmysql_query( $vender_sql, get_db_conn() );

// 	while( $vender_row = pmysql_fetch_array( $vender_res ) ){

// 		# 상품정보 array
// 		$orderproduct = array();
// 		# 상품정보를 구한다
// 		$op_sql = "SELECT op.vender, op.ordercode, op.productcode, op.productname, op.opt1_name, op.opt2_name, op.quantity, op.price, op.reserve, op.date, ";
// 		$op_sql.= "op.selfcode, op.option_price, op.option_quantity, op.option_type, op.idx, op.basketidx, op.text_opt_subject, op.text_opt_content, p.tinyimage, op.oc_no ";
// 		$op_sql.= "FROM tblorderproduct op LEFT JOIN tblproduct p on op.productcode = p.productcode ";
// 		$op_sql.= "WHERE op.ordercode = '".$ordercode."' AND op.vender ='".$vender_row['vender']."' ";
// 		if( $oc_no > 0 ) $op_sql.= "AND op.oc_no = '".$oc_no."' ";
// 		$op_res = pmysql_query( $op_sql, get_db_conn() );
// 		while( $op_row = pmysql_fetch_object( $op_res ) ){
// 			$orderproduct[$op_row->vender][] = $op_row;
// 		}
// 		pmysql_free_result( $op_res );
		
// 		#메일 내용을 불러온다
// 		$return_memo    = '';
// 		$return_date    = '';
// 		$refund_content = '';
// 		$orderdate      = substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)." ".substr($ordercode,8,2).":".substr($ordercode,10,2).":".substr($ordercode,12,2);
// 		$paymemt_type   = "";
// 		$arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌", "Y"=>"PAYCO");

// 		switch( $sms_type ){
// 			case 0 : // 주문확인
// 				$sms_msg     = " [ ".$shopname." ] [".$sms_date."]에 주문코드 [".$ordercode."]주문이 들어왔습니다.";
// 				$sms_etcmsg  = "주문 메세지 전송";
// 				$subject = $shopname." 주문내역서 확인 메일입니다.";
// 				$mail_dir = 'v_ordermail';
// 				$buffer="";
// 				break;
// 			case 1 : // 입금확인
// 				$sms_msg     = " [ ".$shopname." ] [".$sms_date."]에 주문코드 [".$ordercode."]입금이 완료 되었습니다.";
// 				$sms_etcmsg  = "입금 메세지 전송";
// 				$subject = $shopname." 입금 확인 메일입니다.";
// 				$mail_dir = 'v_bankmail';
// 				$buffer="";
// 				break;
// 			case 2 : // 주문취소
// 				$sms_msg     = " [ ".$shopname." ] [".$sms_date."]에 주문코드 [".$ordercode."]주문취소가 들어왔습니다.";
// 				$sms_etcmsg  = "주문취소 메세지 전송";
// 				$subject = $shopname." 주문취소 메일입니다.";
// 				$mail_dir = 'v_ordercancelmail';
// 				$buffer="";
// 				break;
// 			case 3 : // 반품요청
// 				$sms_msg     = " [ ".$shopname." ] [".$sms_date."]에 주문코드 [".$ordercode."]반품요청이 들어왔습니다.";
// 				$sms_etcmsg  = "반품요청 메세지 전송";
// 				$subject = $shopname." 반품요청 메일입니다.";
// 				$mail_dir = 'v_orderreturnmail';
// 				$buffer="";
// 				break;
// 			case 4 : // 교환요청
// 				$sms_msg     = " [ ".$shopname." ] [".$sms_date."]에 주문코드 [".$ordercode."]교환요청이 들어왔습니다.";
// 				$sms_etcmsg  = " 교환요청 메세지 전송";
// 				$subject = $shopname." 교환요청 메일입니다.";
// 				$mail_dir = 'v_orderrequestmail';
// 				$buffer="";

// 				//반품 내용
// 				$return_qry    = "select * from tblorder_cancel where oc_no='".$oc_no."' and ordercode='".$ordercode."'";
// 				$return_result = pmysql_query( $return_qry, get_db_conn() );
// 				$return_row    = pmysql_fetch_object( $return_result );
// 				$return_date   = substr($return_row->regdt,0,4)."-".substr($return_row->regdt,4,2)."-".substr($return_row->regdt,6,2)." ".substr($return_row->regdt,8,2).":".substr($return_row->regdt,10,2).":".substr($return_row->regdt,12,2);
// 				$return_memo   = $return_row->memo;
// 				if($return_row->pgcancel=="Y"){
// 					$refund_content = $arpm[$_ord->paymethod[0]]." ".number_format($return_row->rprice)."원";
// 				}else{
// 					$refund_content = "계좌이체 ".number_format($return_row->rprice)."원";
// 				}
// 				pmysql_free_result( $return_result );
// 				break;
// 		}

// 		$patten_arr = array(
// 			"[SHOP]"           => $shopname,					//샵 명칭
// 			"[NAME]"           => $_ord->sender_name,			//주문자 이름
// 			"[V_NAME]"         => $vender_row['com_name'],		//벤더명
// 			"[CURDATE]"        => $sms_date,					//오늘날짜
// 			"[MESSAGE]"        => $thankmsg,					//메세지
// 			"[URL]"            => $shopurl,						//shop url
// 			"[ORDERCODE]"      => $ordercode,					//주문번호
// 			"[ORDERTELL]"      => $_ord->sender_tel,			//보내는이 전화
// 			"[ORDEREMAIL]"     => $_ord->sender_email,			//메일
// 			"[PAYTYPE]"        => $arpm[$_ord->paymethod[0]],	// 결제형태
// 			"[RECEIVERNAME]"   => $_ord->receiver_name,			// 받는이 이름
// 			"[RECEIVERTELL1]"  => $_ord->receiver_tel1,			//받는이 일반전화
// 			"[RECEIVERTELL2]"  => $_ord->receiver_tel2,			//받는이 핸드폰
// 			"[RECEIVERADDR]"   => $_ord->receiver_addr,			//받는이 주소
// 			"[RECEIVERPOST5]"  => $_ord->post5,					//우편번호 5자리
// 			"[ORDMSG]"         => $_ord->order_msg2,			// 배송 메세지
// 			"[PRICE]"          => $_ord->price,					// 상품금액
// 			"[PAYDATA]"        => $_ord->pay_data,				//입금정보
// 			"[ORDERDATE]"      => $orderdate,					// 주문일자
// 			"[CANCELMEMO]"     => $return_memo,					// 취소메모
// 			"[RETRUNDATE]"     => $return_date,					// 취소일자
// 			"[REFUNDC]"        => $refund_content,				// 환불수단
// 		);

// 		if(file_exists(DirPath.TempletDir."mail/".$mail_dir.$mail_type.".php")) {
// 			ob_start();
// 			include(DirPath.TempletDir."mail/".$mail_dir.$mail_type.".php");
// 			$buffer = ob_get_contents();
// 			ob_end_clean();
// 			$body = $buffer;
// 		}

// 		if( ord( $body ) ) {
// 			unset( $pattern );
// 			unset( $replace );
// 			foreach( $patten_arr as $k=>$v ){
// 				$pattern[] = $k;
// 				$replace[] = $v;
// 			}
// 			$body = str_replace( $pattern, $replace, $body );
// 			if ( ord( $shopname ) ) {
// 				$mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
// 			}
// 			if ( ord( $subject ) ) {
// 				$subject = "=?utf-8?b?".base64_encode( $subject )."?=";
// 			}
// 			$header = getMailHeader( $mailshopname, $info_email );
// 		}

// 		if( ismail( $vender_row['p_email'] ) ){
// 			$vendermail = $vender_row['p_email'];
// 			//$vendermail = "donghyeok@commercelab.co.kr";
//             //if($vender_row['vender'] == 195) $vendermail = "jhjeong@commercelab.co.kr";
//             //elseif($vender_row['vender'] == 250) $vendermail = "ikazeus@naver.com";
//             //else $vendermail = "ikazeus@gmail.com";

//             // 테섭이라 막아놓음.
// 			//sendmail( $vendermail, $subject, $body, $header );

//             error_log("\r\n"."=============================================================",3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n".date("Y-m-d H:i:s ")." ".$ordercode,3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n"."vender = ".$vender_row['vender'],3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n"."vendermail = ".$vendermail,3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n"."subject = ".$subject,3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n"."=============================================================",3,"/tmp/log_mail_hott_".date("Ymd"));
// 		}

// 		#smsID, sms인증키, 받는사람핸드폰, 받는사람명, 보내는사람(회신전화번호), 발송일, 메세지, etc메세지(예:개별 메세지 전송)
// 		if( strlen( $vender_row['p_mobile'] ) > 0 ){
// 			$totellist = $vender_row['p_mobile'];
// 			//$totellist = "010-9249-3375";
// 			//SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $sms_date, $sms_msg, $sms_etcmsg);

//             error_log("\r\n"."=============================================================",3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n".date("Y-m-d H:i:s ")." ".$ordercode,3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n"."vender = ".$vender_row['vender'],3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n"."totellist = ".$totellist,3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n"."sms_msg = ".$sms_msg,3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n"."sms_etcmsg = ".$sms_etcmsg,3,"/tmp/log_mail_hott_".date("Ymd"));
//             error_log("\r\n"."==================================================================================================="."\r\n",3,"/tmp/log_mail_hott_".date("Ymd"));
// 		}

// 	}
// 	pmysql_free_result( $vender_res );

}
