<?php
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';
if(ord($ordercode)==0) $ordercode=$_REQUEST["ordercode"];
//$deli_gbn=$_REQUEST["deli_gbn"];
$paycode = $_REQUEST["paycode"];
if( strlen( $mobile_path ) == 0 ) $mobile_path = $_REQUEST['mobile_path'];
// $orInsertArr, $prInsertArr => lib에 존재함
if(ord($ordercode)) {
	# select table의 컬럼을 지정해준다
	$orStr = implode( ',', $orInsertArr );
	$prStr = implode( ',', $prInsertArr );
	//pmysql_query("INSERT INTO tblorderinfo SELECT * FROM tblorderinfotemp WHERE ordercode='{$ordercode}'",get_db_conn());
	pmysql_query("INSERT INTO tblorderinfo ( ".$orStr." ) ( SELECT ".$orStr." FROM tblorderinfotemp WHERE ordercode='{$ordercode}' ) ",get_db_conn());
	if (pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();
	//pmysql_query("INSERT INTO tblorderproduct SELECT * FROM tblorderproducttemp WHERE ordercode='{$ordercode}'",get_db_conn());
	pmysql_query("INSERT INTO tblorderproduct ( ".$prStr." ) ( SELECT ".$prStr." FROM tblorderproducttemp WHERE ordercode='{$ordercode}' ) ",get_db_conn());
	if (pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();
	pmysql_query("INSERT INTO tblorderoption SELECT * FROM tblorderoptiontemp WHERE ordercode='{$ordercode}'",get_db_conn());
	if (pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();
	if($pmysql_errno) $okmail="YES";
}

$sql="UPDATE tblorderinfo SET regdt='".date('YmdHis')."' WHERE ordercode='{$ordercode}'";
pmysql_query($sql,get_db_conn());

/*
if($okmail!="YES"){
	# 주문수량 차감
	if( $paymethod != 'B' ){

		# 주문수량 차감
		order_quantity( $ordercode );

		#적립금 사용
		insert_point($_ShopInfo->getMemid(), (-1) * $user_reserve, "주문번호 {$ordercode} 결제"); 

		#쿠폰사용
		$sql = "SELECT coupon_code FROM tblcoupon_order WHERE ordercode='{$ordercode}' ";
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)){
			$coupon_code = $row->coupon_code;
			pmysql_query("UPDATE tblcouponissue SET used='Y' WHERE id='".$_ShopInfo->getMemid()."' AND coupon_code='{$coupon_code}'",get_db_conn());
		}
	}
}
*/

$sql="SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod[0];
	$pay_flag=$row->pay_flag;
	$pay_flag_check=$row->pay_flag;
	$pay_auth_no=$row->pay_auth_no;
	$bank_date=$row->bank_date;
	//$deli_flag=$row->deli_gbn;
    $deli_gbn=$row->deli_gbn; // deli_flag > deli_gbn;
	$user_reserve=$row->reserve;
	$last_price=$row->price  + $row->deli_price - $row->dc_price - $row->reserve;
	$pay_data=$row->pay_data;
	$delflag=$row->del_gbn;
	$sender_name=$row->sender_name;
	$sender_email=$row->sender_email;
	$sender_tel=$row->sender_tel;
	$staff_order = $row->staff_order;
    $oi_step2 = $row->oi_step2;
}
pmysql_free_result($result);

// ================================================================
// 페이코 결제완료시 생성한 파일 삭제
// ================================================================
$logFile = $Dir.DataDir."backup/payco/" . $ordercode . ".txt";
if ( file_exists($logFile) ) {
    // 로그 파일 삭제
    unlink($logFile);
}

if (strstr("VOQCPMY", $paymethod) && $deli_gbn=="C") {
	$pay_data = "결제 중 주문취소";
}

if(strstr("VOQCPMY", $paymethod) && $last_price>0) {
	if(strlen($_ShopInfo->getOkpayment())==0) {
		$_ShopInfo->setOkpayment("result");
		$_ShopInfo->Save();
		$_ShopInfo->setOkpayment("");
	}
}

//카드실패시 장바구니 복구
if (strstr("VOQCPMY", $paymethod) && $pay_flag!="0000") {
	//pmysql_query("UPDATE tblbasket SET tempkey='".$_ShopInfo->getTempkey()."' WHERE tempkey='".$_ShopInfo->getGifttempkey()."'",get_db_conn());
} else {
	//pmysql_query("DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getGifttempkey()."'",get_db_conn());
	pmysql_query( "DELETE FROM tblbasket WHERE basketidx IN ( SELECT basketidx FROM tblorderproduct WHERE ordercode = '".$ordercode."' ) ", get_db_conn() );
}

//새로고침 방지
if (!strstr("BG",$paymethod) && $_ShopInfo->getOkpayment()=="result") {
//	if(!isdev()){
		echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";
		exit;
//	}
}

// 임직원 포인트을 ERP에서 가져온다.
$staff_reserve		= getErpStaffPoint($_ShopInfo->getStaffCardNo());			// 임직원 포인트

//결제성공 처리
if (($paymethod=="B" || ($paymethod=="G" && $staff_reserve >= $last_price) || (strstr("VOQCPMY", $paymethod) && strcmp($pay_flag,"0000")==0)) && $okmail!="YES") {
	$thankmsg="<hr size=1 width=100%>\n";
	if (ord($_data->orderend_msg)) {
		$orderend_msg=nl2br($orderend_msg);
		$thankmsg.="<table cellpadding=0 cellspacing=0 border=0 width=100%>\n";
		$thankmsg.="<tr><td align=center>\n";
		$thankmsg.=str_replace("\"","",$orderend_msg);
		$thankmsg.="</td></tr>\n";
		$thankmsg.="</table>\n";
	} else {
		$thankmsg.="<br><h3>구매해주셔서 감사합니다!</h3><br>";
	}	
	/*
	if($chk_product_staff && $_ShopInfo->getMemid() && $staff_price_total > 0){
		$sql_timesale_add = "update tblmember set staff_limit = staff_limit - '".$staff_price_total."' where id = '".$_ShopInfo->getMemid()."'";
		pmysql_query($sql_timesale_add);
	}
	*/
	if (ord($sender_email)) $oksendmail="Y"; //메일이 있으면 주문메일 발송
	if (ord($_data->info_email)) $okadminmail="Y"; //쇼핑몰 메일이 있으면 해당 주문내역서를 발송

	//관리자/입점업체/고객 주문완료 메일 발송
	SendOrderMail($_data->shopname, $_ShopInfo->getShopurl(), $_data->design_mail, $_data->info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg);
	//$arpay=array("B"=>"현금","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

	# SMS 발송
	if( strstr( "BOQ", $paymethod ) && $last_price > 0 ){
		# 주문 접수 안내
		$mem_return_msg = sms_autosend( 'mem_order', $ordercode, '', '' );
		$admin_return_msg = sms_autosend( 'admin_order', $ordercode, '', '' );
		# 입금 계좌 안내
		$mem_return_msg2 = sms_autosend( 'mem_bank', $ordercode, '', '' );
		$admin_return_msg2 = sms_autosend( 'admin_bank', $ordercode, '', '' );
	} else if( strstr( "CVPMG", $paymethod ) ){
		# 주문 완료 안내
		$mem_return_msg = sms_autosend( 'mem_orderok', $ordercode, '', '' );
		$admin_return_msg = sms_autosend( 'admin_orderok', $ordercode, '', '' );
	}

	//주문완료시 회원/관리자/입금내역을 sms로 발송함
	/*
	$sqlsms = "SELECT * FROM tblsmsinfo WHERE (mem_order='Y' OR admin_order='Y' ";
	if($paymethod=="B") $sqlsms.="OR mem_bank='Y' ";
	$sqlsms.=")";
	$resultsms= pmysql_query($sqlsms,get_db_conn());
	if($rowsms=pmysql_fetch_object($resultsms)){
		if(ord($ordercode)) {
			$sms_id=$rowsms->id;
			$sms_authkey=$rowsms->authkey;

			$admin_order=$rowsms->admin_order;
			$mem_order=$rowsms->mem_order;
			$mem_bank=$rowsms->mem_bank;
			$totellist=$rowsms->admin_tel;
			if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
			if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
			if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
			$fromtel=$rowsms->return_tel;

			$msg_mem_order=$rowsms->msg_mem_order;
			$msg_mem_bank=$rowsms->msg_mem_bank;
			if(ord($msg_mem_bank)==0) $msg_mem_bank="[NAME]님! [PRICE]원 [ACCOUNT] 입금바랍니다. [{$_data->shopname}]";
			$patten1       = array( "[NAME]", "[PRODUCT]" );
			$replace1      = array( $sender_name, substr( $smsproductname, 1 ) );
			$msg_mem_order = str_replace( $patten1, $replace1, $msg_mem_order );
			$msg_mem_order = AddSlashes( $msg_mem_order );
			$smsmsg        = $sender_name."님이 ".substr($smsproductname,1)."를 {$arpay[$paymethod]} 구입하셨습니다.";
			$patten2       = array( "[NAME]", "[PRICE]", "[ACCOUNT]" );
			$replace2      = array( $sender_name, number_format($last_price), $pay_data );
			$msg_mem_bank  = str_replace( $patten2, $replace2, $msg_mem_bank );
			pmysql_free_result($resultsms);
			$etcmsg = "상품주문 안내메세지(회원)";
			$date   = "0";
			if( $mem_order == "Y" ) {
				$temp = SendSMS($sms_id, $sms_authkey, $sender_tel, "", $fromtel, $date, $msg_mem_order, $etcmsg );
			}
			$etcmsg = "무통장입금 안내메세지(회원)";
			if( strstr( "BOQ", $paymethod ) && $mem_bank == "Y" ) {
				$temp = SendSMS( $sms_id, $sms_authkey, $sender_tel, "", $fromtel, $date, $msg_mem_bank, $etcmsg );
			}
			$etcmsg="상품주문 안내메세지(관리자)";
			if( $admin_order=="Y" && $rowsms->sleep_time1 != $rowsms->sleep_time2 ){
				$date = "0";
				$time = date("Hi");
				if($rowsms->sleep_time2<"12" && $time<=sprintf("%02d59",$rowsms->sleep_time2)) $time+=2400;
				if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

				if($time<sprintf("%02d00",$rowsms->sleep_time1) || $time>=sprintf("%02d59",$rowsms->sleep_time2)){
					if($time<sprintf("%02d00",$rowsms->sleep_time1)) $day = 0;
					else $day=1;
					
					$date = date("Y-m-d",strtotime("+{$day} day")).sprintf(" %02d:00:00",$rowsms->sleep_time1);
				}
			}

			if($admin_order=="Y") {
				$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $smsmsg, $etcmsg);
			}
		}
	}

	if( strstr("VC", $paymethod) && strcmp($pay_flag,"0000")==0  ){
			
		$sql="SELECT * FROM tblsmsinfo WHERE mem_bankok='Y' ";
		$result=pmysql_query($sql,get_db_conn());
		if($rowsms=pmysql_fetch_object($result)) {
			$sms_id=$rowsms->id;
			$sms_authkey=$rowsms->authkey;


			$msg_mem_bankok=$rowsms->msg_mem_bankok;
			if(ord($msg_mem_bankok)==0) $msg_mem_bankok="[".strip_tags($_shopdata->shopname)."] [NAME]님의 주문이 입금확인 되었습니다. 2~3일이내 배송됩니다.^^";
			$patten=array("[DATE]","[NAME]","[PRICE]");
			$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$sender_name,$bankprice);

			$msg_mem_bankok=str_replace($patten,$replace,$msg_mem_bankok);
			$msg_mem_bankok=addslashes($msg_mem_bankok);

			$fromtel=$rowsms->return_tel;
			$date=0;
			$etcmsg="입금확인메세지(회원)";
			$temp=SendSMS($sms_id, $sms_authkey, $sender_tel, "", $fromtel, $date, $msg_mem_bankok, $etcmsg);
		}
		pmysql_free_result($result);

	}
	*/
}


//주문중 주문취소 데이터 처리
if ((strstr("OQCPMY", $paymethod) && strcmp($pay_flag,"0000")!=0 && ord($ordercode) && ord($pay_auth_no)==0 && $pay_flag_check=="N" && $deli_gbn=="C" ) || ($paymethod=="V" && ord($ordercode) && $deli_gbn=="C" && ord($bank_date)==0) && $delflag=='N' || ($paymethod=="G" && $staff_reserve < $last_price)) {
	if ($paymethod!="G") {
	// && $deli_flag=="N" 
		if( strstr("CPM", $paymethod) ){
			# 수량복구
			order_recovery_quantity( $ordercode );
		}
		/*
		$sql = "UPDATE tblorderinfo SET ";
		$sql.= "pay_data		= '고객이 결제창에서 주문취소를 하였습니다.', ";
		$sql.= "deli_gbn	= 'C', ";
		$sql.= "del_gbn		= 'R' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		$sql.= "AND paymethod='{$paymethod}' AND pay_flag='N' ";
		if(pmysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			pmysql_query($sql,get_db_conn());
		}
		*/
		
		#적립금 복구
		if( strlen( $_ShopInfo->getMemid() ) > 0 && $user_reserve > 0 ){
			insert_order_point( $ordercode, $_ShopInfo->getMemid(), $user_reserve, "주문번호 {$ordercode} 결제시 사용복구", '@order', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid('') );
		}
		#쿠폰 복구
		$couponArr = array(); // 복구 대상
		$sql = "
			SELECT
				ci.coupon_code, ci.id, ci.date_start, ci.date_end, to_char( now(), 'YYYYMMDDHH24MISS' )::character varying(14) AS date
			  FROM
			  ( 
				SELECT coupon_code, id, date_start, date_end, ci_no 
				FROM tblcouponissue 
				WHERE used = 'Y'
				AND id = '".$_ShopInfo->getMemid()."'
			  ) AS ci
			  JOIN
			  (
				SELECT ci_no FROM tblcoupon_order 
				WHERE ordercode = '".$ordercode."'
				GROUP BY ci_no
			  ) AS co ON ( ci.ci_no = co.ci_no )
		";
		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ){
			// 복구 대상
			$couponArr[] = "( '".$row->coupon_code."', '".$row->id."', '".$row->date_start."', '".$row->date_end."', '".$row->date."' )";
		}
		if( count( $couponArr ) > 0 ){
			// 쿠폰 복구
			$cu_sql = "
				INSERT INTO tblcouponissue
				( coupon_code, id, date_start, date_end, date )
				".implode( ',', $couponArr )."
			";
			$result = pmysql_query( $cu_sql, get_db_conn() );
		}
	} else {
		// 임직원 결제시 포인트가 적을경우
		$sql = "UPDATE tblorderinfo SET pay_flag='9999', ";
		$sql.= "pay_data='임직원 포인트 부족', deli_gbn='C' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());

		$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
	}

    // 결제중 취소
    if( $oi_step2 != 54 ) {
        # 주문 상태 => 결제실패
        $oi2_sql = "UPDATE tblorderinfo SET oi_step2 = 54 WHERE ordercode ='".$ordercode."'";
        pmysql_query( $oi2_sql, get_db_conn() );
         # 주문상품 상태 => 결제실패
        $op_sql = "UPDATE tblorderproduct SET op_step = 54 WHERE ordercode ='".$ordercode."'";
        pmysql_query( $op_sql, get_db_conn() );
    }
    

} else if(((strstr("OQCPMY", $paymethod) && strcmp($pay_flag,"0000")==0 && ord($ordercode) && $deli_gbn!="C" && $deli_flag=="N") || $paymethod=="B" || ($paymethod=="G" && $staff_reserve >= $last_price) || ($paymethod=="V" && ord($ordercode) && $deli_gbn!="C" && $deli_flag=="N" && ord($bank_date))) && $delflag=='N'){
    
    # 주문 check 테이블 비우기
   // if( strlen( $paycode ) > 0 ){
   //     pmysql_query( "DELETE FROM tblorder_check WHERE paycode = '".$paycode."' ", get_db_conn() );
   // }
    # 주문수량 차감
	if( (strstr( "B", $paymethod ) && $last_price == 0) || (strstr( "G", $paymethod ) && $last_price > 0) ) {
		//주문수량 차감
		order_quantity( $ordercode );
		//주문 상태 변경
		orderStepUpdate($exe_id,  $ordercode, 1 );
		if( strstr( "B", $paymethod ) && $last_price == 0 ) {
			$sql="UPDATE tblorderinfo SET bank_date = '".date('YmdHis')."' WHERE ordercode = '".$ordercode."'";
			pmysql_query($sql,get_db_conn());
		}

        // 마일리지 100% 결제일 경우, 벤더에게 mail / sms 발송하기 위해 추가..2016-06-08 jhjeong
        //SendVenderForm( $_data->shopname, $_ShopInfo->getShopurl(), $ordercode, $_data->design_mail, $_data->info_email, 1 );
	}
    if( strstr( "BG", $paymethod ) ){
        #적립금 사용
        if( strlen( $_ShopInfo->getMemid() ) > 0 && $user_reserve > 0 ){
            //insert_point( $_ShopInfo->getMemid(), (-1) * $user_reserve, "주문번호 {$ordercode} 결제시 사용", '@order', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid('') ); 
            insert_order_point( $ordercode, $_ShopInfo->getMemid(), (-1) * $user_reserve, "주문번호 {$ordercode} 결제시 사용", '@order', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid('') );
        }
        #쿠폰사용
        $sql = "SELECT ci_no FROM tblcoupon_order WHERE ordercode='{$ordercode}' ";
        $result=pmysql_query($sql,get_db_conn());
        while($row=pmysql_fetch_object($result)){
            $ci_no = $row->ci_no;
            pmysql_query("UPDATE tblcouponissue SET used='Y' WHERE id='".$_ShopInfo->getMemid()."' AND ci_no='{$ci_no}'",get_db_conn());
        }
    }
	$sql="UPDATE tblorderinfo SET del_gbn='N' WHERE ordercode='{$ordercode}'";
	pmysql_query($sql,get_db_conn());

	if( strlen( $_ShopInfo->getMemid() ) > 0 && $last_price > 0 ){
		$memSql = "UPDATE tblmember SET sumprice = sumprice + ".$last_price."  WHERE id='".$_ShopInfo->getMemid()."'";
		pmysql_query($memSql,get_db_conn());
	}

	//ERP로 결제완료데이터를 보낸다.
	if( strstr( "CPMYVG", $paymethod ) ){
		sendErporder($ordercode);
	}


	//주문 상품 품절시 관리자에서 sms 통보
	/*
	$sqlsms="SELECT * FROM tblsmsinfo WHERE admin_soldout='Y' ";
	$resultsms= pmysql_query($sqlsms,get_db_conn());
	if($rowsms=pmysql_fetch_object($resultsms)) {
		$sms_id=$rowsms->id;
		$sms_authkey=$rowsms->authkey;

		$totellist=$rowsms->admin_tel;
		if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
		if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
		if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
		$fromtel=$rowsms->return_tel;
		pmysql_free_result($resultsms);
		$etcmsg="상품품절 알림 메세지(관리자)";
		$date="0";
		//관리자가 sms를 원하지 않는 시간 체크하여 그외시간에 보내도록 한다.
		if($rowsms->sleep_time1!=$rowsms->sleep_time2){
			$date="0";
			$time = date("Hi");
			if($rowsms->sleep_time2<"12" && $time<=sprintf("%02d59",$rowsms->sleep_time2)) $time+=2400;
			if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

			if($time<sprintf("%02d00",$rowsms->sleep_time1) || $time>=sprintf("%02d59",$rowsms->sleep_time2)){
				if($time<sprintf("%02d00",$rowsms->sleep_time1)) $day = 0;
				else $day=1;
				
				$date = date("Y-m-d",strtotime("+{$day} day")).sprintf(" %02d:00:00",$rowsms->sleep_time1);
			}			
		}
		$sql = "SELECT a.productname FROM tblproduct a, tblorderproduct b ";
		$sql.= "WHERE b.ordercode='{$ordercode}' ";
		$sql.= "AND a.productcode=b.productcode ";
		$sql.= "AND (a.quantity<=0 AND a.quantity is NOT NULL) ";
		$result = pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$smsmsg="[".addslashes($row->productname)."]이 {$sender_name}님 주문에 의해서 품절되었습니다.";
			$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $smsmsg, $etcmsg);
		}
		pmysql_free_result($result);
	}
	*/
}

if($_GET['pg_status'] == 'fail'){
?>
<html>
<head>
<title>결제실패</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=UTF-8">
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onload="document.form1.submit()">
<?php
    if ( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) ) {
?>
<form name='form1' action="<?=$Dir?>m/order_end.php" method='POST' target='_parent'>
<input type='hidden' name='ordercode' value="<?=$ordercode?>">
</form>
<?php
    } else {
?>
<form name='form1' action="<?=$Dir.FrontDir?>orderend.php" method='get' target='_parent'>
<input type='hidden' name='ordercode' value="<?=$ordercode?>">
</form>
<?php
    }
?>
<input type='hidden' name='ordercode' value="<?=$ordercode?>">
</body>
</html>
<?
} else {
?>
<html>
<head>
<title>결제</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=UTF-8">
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onload="document.form1.submit()">
<?php
    if ( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) ) {
?>
<form name='form1' action="<?=$Dir?>m/order_end.php" method='POST' >
<input type='hidden' name='ordercode' value="<?=$ordercode?>">
</form>
<?php
    } else {
?>
<form name='form1' action="<?=$Dir.FrontDir?>orderend.php" method='get' target='_parent'>
<input type='hidden' name='ordercode' value="<?=$ordercode?>">
</form>
<?php
    }
?>
</body>
</html>
<?
}
?>
