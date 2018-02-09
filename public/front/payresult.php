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
	$temp1 = "INSERT INTO tblorderinfo ( ".$orStr." ) ( SELECT ".$orStr." FROM tblorderinfotemp WHERE ordercode='{$ordercode}' ) ";
	pmysql_query($temp1,get_db_conn());
	// echo "temp1 = ".pmysql_error()."<br>";
	//echo "temp1-result:".pmysql_error()."<br>";
	if (pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();
	// echo "pmysql_errno_temp1 = ".$pmysql_errno."<br>";
	//pmysql_query("INSERT INTO tblorderproduct SELECT * FROM tblorderproducttemp WHERE ordercode='{$ordercode}'",get_db_conn());
	$temp2 = "INSERT INTO tblorderproduct ( ".$prStr." ) ( SELECT ".$prStr." FROM tblorderproducttemp WHERE ordercode='{$ordercode}' ) ";
	pmysql_query($temp2,get_db_conn());
	// echo "temp2 = ".pmysql_error()."<br>";
	//echo "temp2-result:".pmysql_error()."<br>";

	//setSyncInfo($ordercode, $opidx, 'I');
	
	if (pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();
	// echo "pmysql_errno_temp2 = ".$pmysql_errno."<br>";
	pmysql_query("INSERT INTO tblorderoption SELECT * FROM tblorderoptiontemp WHERE ordercode='{$ordercode}'",get_db_conn());
	// echo "temp3 = ".pmysql_error()."<br>";
	if (pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();
	// echo "pmysql_errno_temp3 = ".$pmysql_errno."<br>";
	if($pmysql_errno) $okmail="YES";
	
// 	IF($_SERVER[REMOTE_ADDR]=='218.234.32.62'){
// 		echo "pmysql_errno = ".$pmysql_errno."<br>";
// 		echo "okmail = ".$okmail."<br>";
// 		exit();
// 	}
}

$sql="UPDATE tblorderinfo SET regdt='".date('YmdHis')."' WHERE ordercode='{$ordercode}'";
pmysql_query($sql,get_db_conn());


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
	$user_point=$row->point;
	$last_price=$row->price  + $row->deli_price - $row->dc_price - $row->reserve - $row->point;
	$pay_data=$row->pay_data;
	$delflag=$row->del_gbn;
	$sender_name=$row->sender_name;
	$sender_email=$row->sender_email;
	$sender_tel=$row->sender_tel;
	$staff_order = $row->staff_order;
	$cooper_order = $row->cooper_order;
    $oi_step2 = $row->oi_step2;
    $user_id = $row->id;
}
pmysql_free_result($result);

// 20170428 구글통계 관련 데이터추출
$sql2="SELECT * FROM tblorderproduct WHERE ordercode='{$ordercode}' ";
$result2=pmysql_query($sql2,get_db_conn());
if($row2=pmysql_fetch_object($result2)) {
	$order_product_code = $row2->productcode;
	$order_product_name = $row2->productname;
	$order_product_option = $row2->opt1_name."/".$row2->opt2_name;
	$order_product_count = $row2->quantity;
}
pmysql_free_result($result2);

list($pay_ok)=pmysql_fetch_array(pmysql_query("SELECT ok FROM tblpvirtuallog WHERE ordercode='".$ordercode."' "));

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
//$staff_reserve		= getErpStaffPoint($_ShopInfo->getStaffCardNo());			// 임직원 포인트
// 핫티 erp 부분이라 강제 변경
list($staff_reserve)=pmysql_fetch("select staff_reserve from tblmember where id='".$_ShopInfo->getMemid()."'");			// 임직원 포인트
if(!$staff_reserve) $staff_reserve="0";
list($staff_price)=pmysql_fetch("select sum(staff_price) from tblorderproduct where ordercode='".$ordercode."'");
list($cooper_price)=pmysql_fetch("select sum(cooper_price) from tblorderproduct where ordercode='".$ordercode."'");

//결제성공 처리
if (($paymethod=="B" || (strstr("VOQCPMY", $paymethod) && strcmp($pay_flag,"0000")==0)) && $okmail!="YES") {

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

	if (strstr("BG", $paymethod)) {

		//실행자 이름을 가져온다 (2016.10.07 - 김재수 추가)
		if ($_ShopInfo->getMemname()) {
			$reg_name	= $_ShopInfo->getMemname();
		} else {
			list($reg_name)=pmysql_fetch_array(pmysql_query("select sender_name from tblorderinfo WHERE ordercode='".trim($ordercode)."' "));
		}
		$exe_id		= $_ShopInfo->getMemid()."|".$reg_name."|user";	// 실행자 아이디|이름|타입

		//주문 상태 변경 (주문접수)
		orderStepUpdate($exe_id,  $ordercode, '0', '', '', '', '', 'Y' );
	}

	# alimtalk_position
	$alim = new ALIM_TALK();
	
	# SMS 발송
	if( strstr( "OQ", $paymethod ) && $last_price > 0 ){
		if($pay_ok == "M"){
			#가상계좌 미입금
			# 주문 접수 안내
			$mem_return_msg = sms_autosend( 'mem_order', $ordercode, '', '' );
			$admin_return_msg = sms_autosend( 'admin_order', $ordercode, '', '' );
			# 입금 계좌 안내
			$mem_return_msg2 = sms_autosend( 'mem_bank', $ordercode, '', '' );
			$admin_return_msg2 = sms_autosend( 'admin_bank', $ordercode, '', '' );
			
		}else if($pay_ok == "Y"){
			#가상계좌 입금 확인
			# 주문 완료 안내
			$mem_return_msg = sms_autosend( 'mem_bankok', $ordercode, '', '' );
			$admin_return_msg = sms_autosend( 'admin_bankok', $ordercode, '', '' );
			
		}else{
			# 주문 접수 안내
			$mem_return_msg = sms_autosend( 'mem_order', $ordercode, '', '' );
			$admin_return_msg = sms_autosend( 'admin_order', $ordercode, '', '' );
			# 입금 계좌 안내
			$mem_return_msg2 = sms_autosend( 'mem_bank', $ordercode, '', '' );
			$admin_return_msg2 = sms_autosend( 'admin_bank', $ordercode, '', '' );
		}

        // 알림톡 삽입 2017-05-04
		$alim->makeAlimTalkSearchData($ordercode, 'WEB01');
		
	} else if( strstr( "BCVPMGY", $paymethod ) ){
		# 주문 완료 안내
		$mem_return_msg = sms_autosend( 'mem_orderok', $ordercode, '', '' );
		$admin_return_msg = sms_autosend( 'admin_orderok', $ordercode, '', '' );
		
		$alim->makeAlimTalkSearchData($ordercode, 'WEB03');
		
		// 매장픽업시 업주 알림톡 delivery_type = 1 [WEB16]
		$alim_owner = new ALIM_TALK();
		$alim_owner->makeAlimTalkSearchData($ordercode, 'WEB16');
	}

	//ERP로 주문완료
	if( strstr( "CPMYVG", $paymethod ) ){

		sendErporder($ordercode);
		$f = fopen("../data/backup/send_erp_order_".date("Ymd").".txt","a+");
		fwrite($f,"########################################## START send_erp_order_".$ordercode." ".date("Y-m-d H:i:s")."\r\n");
		fwrite($f," sendErporder = ".$ordercode."\r\n");
		fwrite($f,"########################################## END send_erp_order_".$ordercode." ".date("Y-m-d H:i:s")."\r\n\r\n");
		fclose($f);
		chmod("../data/backup/send_erp_order_".date("Ymd").".txt",0777);
	}
}

//주문중 주문취소 데이터 처리
if ((strstr("OQCPMY", $paymethod) && strcmp($pay_flag,"0000")!=0 && ord($ordercode) && ord($pay_auth_no)==0 && $pay_flag_check=="N" && $deli_gbn=="C" ) || ($paymethod=="V" && ord($ordercode) && $deli_gbn=="C" && ord($bank_date)==0) && $delflag=='N') {

	if( strstr("CPM", $paymethod) ){
		# 수량복구
		order_recovery_quantity( $ordercode );
	}
	
	#적립금 복구
	if( strlen( $_ShopInfo->getMemid() ) > 0 && ($user_reserve > 0 || $user_point > 0 || $staff_order=="Y" || $cooper_order=="Y")){
		$point_sql="select * from tblorderproduct where ordercode='".$ordercode."'";
		$point_result=pmysql_query($point_sql);
		while($point_data=pmysql_fetch_object($point_result)){
			if($staff_order=="Y"){
				$point_change=$point_data->staff_price;
			}else if($cooper_order=="Y"){
				$point_change=$point_data->cooper_price;
			}else{
				$point_change=$point_data->use_point;
			}

			//insert_point( $_ShopInfo->getMemid(), (-1) * $user_reserve, "주문번호 {$ordercode} 결제시 사용", '@order', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid('') ); 
			$logText.= " 적립금 사용 : ".$point_change." \n";
			$logText.= " E포인트 사용 : ".$user_point." \n";
			insert_order_point( $ordercode, $_ShopInfo->getMemid(), $point_change, "주문번호 {$ordercode} 결제(1건)에 대한 사용복구", '@order_cancel', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid('').'|'.$ordercode.'_'.$point_data->idx, '', '', $point_data->use_epoint );
	//		"주문번호 {$ordercode} 결제(1건)에 대한 사용"
		}
	//		insert_order_point( $ordercode, $_ShopInfo->getMemid(), $point_change, "주문번호 {$ordercode} 결제시 사용복구", '@order', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid(''), '', '', $user_point );
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

    // 결제중 취소
    if( $oi_step2 != 54 ) {
        # 주문 상태 => 결제실패
        $oi2_sql = "UPDATE tblorderinfo SET oi_step2 = 54 WHERE ordercode ='".$ordercode."'";
        pmysql_query( $oi2_sql, get_db_conn() );
         # 주문상품 상태 => 결제실패
        $op_sql = "UPDATE tblorderproduct SET op_step = 54 WHERE ordercode ='".$ordercode."'";
        pmysql_query( $op_sql, get_db_conn() );
    }


} else if(((strstr("OQCPMY", $paymethod) && strcmp($pay_flag,"0000")==0 && ord($ordercode) && $deli_gbn!="C" && $deli_flag=="N") || $paymethod=="B" || ($paymethod=="V" && ord($ordercode) && $deli_gbn!="C" && $deli_flag=="N" && ord($bank_date))) && $delflag=='N'){

    # 주문수량 차감
	if( (strstr( "B", $paymethod ) && $last_price == 0) || (strstr( "G", $paymethod ) && $last_price > 0) ) {
		//주문수량 차감
		order_quantity( $ordercode );

		//실행자 이름을 가져온다 (2016.10.07 - 김재수 추가)
		if ($_ShopInfo->getMemname()) {
			$reg_name	= $_ShopInfo->getMemname();
		} else {
			list($reg_name)=pmysql_fetch_array(pmysql_query("select sender_name from tblorderinfo WHERE ordercode='".trim($ordercode)."' "));
		}
		$exe_id		= $_ShopInfo->getMemid()."|".$reg_name."|user";	// 실행자 아이디|이름|타입

		//주문 상태 변경 (결제완료)
		orderStepUpdate($exe_id,  $ordercode, 1 );
		if( (strstr( "B", $paymethod ) && $last_price == 0) || (strstr( "G", $paymethod ) && $staff_reserve >= $last_price) ) {
			$sql="UPDATE tblorderinfo SET bank_date = '".date('YmdHis')."' WHERE ordercode = '".$ordercode."'";
			pmysql_query($sql,get_db_conn());
		}

        // 마일리지 100% 결제일 경우, 벤더에게 mail / sms 발송하기 위해 추가..2016-06-08 jhjeong
        //SendVenderForm( $_data->shopname, $_ShopInfo->getShopurl(), $ordercode, $_data->design_mail, $_data->info_email, 1 );

		setSyncInfo($ordercode, $opidx, 'I');

		$Sync = new Sync();
		$arrayDatax=array('ordercode'=>$ordercode);
		$srtn=$Sync->OrderInsert($arrayDatax);

		//erp로 전송
		sendErporder($ordercode);
		$f = fopen("../data/backup/send_erp_order_".date("Ymd").".txt","a+");
		fwrite($f,"########################################## START send_erp_order_".$ordercode." ".date("Y-m-d H:i:s")."\r\n");
		fwrite($f," sendErporder = ".$ordercode."\r\n");
		fwrite($f,"########################################## END send_erp_order_".$ordercode." ".date("Y-m-d H:i:s")."\r\n\r\n");
		fclose($f);
		chmod("../data/backup/send_erp_order_".date("Ymd").".txt",0777);
	}

	if( strstr("VCPMY", $paymethod) ){ // 계좌이체, 신용카드, 매매보호(신용카드) , 휴대폰

		$Sync = new Sync();
		$arrayDatax=array('ordercode'=>$ordercode);
		$srtn=$Sync->OrderInsert($arrayDatax);

//		if ($staff_order!= "Y") {
			//주문완료시 본사발송이 아닐경우 입찰로 돌림
			//$pr_qry="select * from tblorderproduct where ordercode='".$ordercode."' AND (delivery_type = '0') AND op_step in ('1') and store_code!='".$sync_bon_code."'";
			//15ss 자동으로 싱크커머스로 이동안되게수정
			//$pr_qry="select * from tblorderproduct op left join tblproduct p on (op.productcode=p.productcode) where op.ordercode='".$ordercode."' AND (op.delivery_type = '0') AND op.op_step in ('1') and op.store_code!='".$sync_bon_code."' and concat(p.season_year,p.season)!='2015K'";
			//2017년 12월19일 신원고효서 주임 요청으로 2015년 상품 o2o 제외
			$pr_qry="select * from tblorderproduct op left join tblproduct p on (op.productcode=p.productcode) where op.ordercode='".$ordercode."' AND (op.delivery_type = '0') AND op.op_step in ('1') and op.store_code!='".$sync_bon_code."' and p.season_year!='2015'";

			$pr_result=pmysql_query($pr_qry);
			while($pr_data=pmysql_fetch_object($pr_result)){
				$sync_idx = $pr_data->idx;
				//변경전 erp로 전송
				sendErpChangeShop($ordercode, $sync_idx, '');

				$arrayDatax_p=array('ordercode'=>$ordercode,'sync_idx'=>'AND idx='.$sync_idx);
				$deli_type_sql = "UPDATE tblorderproduct SET store_code='',delivery_type='2' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";
				pmysql_query($deli_type_sql, get_db_conn());

				$srtn=$Sync->OrderInsert($arrayDatax_p);

				#싱크커머스 API호출
				if($srtn != 'fail') {
					#주문정보 update
					$in_deli_sql = "UPDATE tblorderproduct SET delivery_type='2' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";

					pmysql_query($in_deli_sql, get_db_conn());
					#배송준비중으로 변경
					$exe_id		= $reg_id."|".$reg_name."|user";	// 실행자 아이디|이름|타입
					orderProductStepUpdate($exe_id, $ordercode, $pr_data->idx, '2');

					//현재 주문의 상태값을 가져온다.
					list($old_step1, $old_step2) = pmysql_fetch_array(pmysql_query("select oi_step1, oi_step2 from tblorderinfo WHERE ordercode='" . trim($ordercode) . "'"));
					if (($old_step1 == '1' || $old_step1 == '2') && $old_step2 == '0') {
						//주문을 배송 준비중으로 변경한다.
						$sql2 = "UPDATE tblorderinfo SET oi_step1 = '2', oi_step2 = '0', deli_gbn='S' WHERE ordercode='" . $ordercode . "'";
						pmysql_query($sql2, get_db_conn());
					}

					$template = 'WEB14';
					$alim = new ALIM_TALK();
					$alim->makeAlimTalkSearchNewData($ordercode, $template , $pr_data->idx ,'');

				}else{
					$in_deli_sql = "UPDATE tblorderproduct SET  store_code='',delivery_type='0' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";
					pmysql_query($in_deli_sql, get_db_conn());
					
				}
			}
//		}

	}

    if( strstr( "BG", $paymethod ) ){
        #적립금 사용
        if( strlen( $_ShopInfo->getMemid() ) > 0 && ($user_reserve > 0 || $user_point > 0 || $staff_order=="Y" || $cooper_order=="Y")){
			$point_sql="select * from tblorderproduct where ordercode='".$ordercode."'";
			$point_result=pmysql_query($point_sql);
			while($point_data=pmysql_fetch_object($point_result)){
				if($staff_order=="Y"){
					$point_change=$point_data->staff_price;
				}else if($cooper_order=="Y"){
					$point_change=$point_data->cooper_price;
				}else{
					$point_change=$point_data->use_point;
				}

				//insert_point( $_ShopInfo->getMemid(), (-1) * $user_reserve, "주문번호 {$ordercode} 결제시 사용", '@order', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid('') ); 
				$logText.= " 적립금 사용 : ".$point_change." \n";
				$logText.= " E포인트 사용 : ".$user_point." \n";
				insert_order_point( $ordercode, $_ShopInfo->getMemid(), (-1) * $point_change, "주문번호 {$ordercode} 결제(1건)에 대한 사용", '@order', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid('').'|'.$ordercode.'_'.$point_data->idx, '', '', (-1) * $point_data->use_epoint );
		//		"주문번호 {$ordercode} 결제(1건)에 대한 사용"
			}

			
            //insert_point( $_ShopInfo->getMemid(), (-1) * $user_reserve, "주문번호 {$ordercode} 결제시 사용", '@order', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid('') );
            //insert_order_point( $ordercode, $_ShopInfo->getMemid(), (-1) * $point_change, "주문번호 {$ordercode} 결제시 사용", '@order', $_ShopInfo->getMemid(), $_ShopInfo->getMemid().'-'.uniqid(''), '', '', (-1) * $user_point );
        }
        #쿠폰사용
        $sql = "SELECT ci_no FROM tblcoupon_order WHERE ordercode='{$ordercode}' ";
        $result=pmysql_query($sql,get_db_conn());
        while($row=pmysql_fetch_object($result)){
            $ci_no = $row->ci_no;
            pmysql_query("UPDATE tblcouponissue SET used='Y' WHERE id='".$_ShopInfo->getMemid()."' AND ci_no='{$ci_no}'",get_db_conn());
        }

		$Sync = new Sync();
		$arrayDatax=array('ordercode'=>$ordercode);
		$srtn=$Sync->OrderInsert($arrayDatax);

//		if ($staff_order!= "Y") {
			//주문완료시 본사발송이 아닐경우 입찰로 돌림
			$pr_qry="select * from tblorderproduct op left join tblproduct p on (op.productcode=p.productcode) where op.ordercode='".$ordercode."' AND (op.delivery_type = '0') AND op.op_step in ('1') and op.store_code!='".$sync_bon_code."' and concat(p.season_year,p.season)!='2015K'";

			$pr_result=pmysql_query($pr_qry);
			while($pr_data=pmysql_fetch_object($pr_result)){
				$sync_idx = $pr_data->idx;
				//변경전 erp로 전송
//				sendErpChangeShop($ordercode, $sync_idx, ''); 2017-07-31
				sendErpChangeShop($ordercode, $sync_idx, '', '2');

				$arrayDatax_p=array('ordercode'=>$ordercode,'sync_idx'=>'AND idx='.$sync_idx);
				$deli_type_sql = "UPDATE tblorderproduct SET store_code='',delivery_type='2' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";
				pmysql_query($deli_type_sql, get_db_conn());
// 무통장(포인트결제)시 본사 발송이 아닌경우 위 테이블 삽입 해야 ERP 자료랑 맞음
				$sql = "INSERT INTO tblorderproduct_store_change(ordercode, idx, regdt) VALUES ('{$ordercode}','{$pr_data->idx}','".date('YmdHis')."')";
				pmysql_query($sql, get_db_conn());

				$srtn=$Sync->OrderInsert($arrayDatax_p);

				#싱크커머스 API호출
				if($srtn != 'fail') {
					#주문정보 update
					$in_deli_sql = "UPDATE tblorderproduct SET delivery_type='2' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";

					pmysql_query($in_deli_sql, get_db_conn());
					#배송준비중으로 변경
					$exe_id		= $reg_id."|".$reg_name."|user";	// 실행자 아이디|이름|타입
					orderProductStepUpdate($exe_id, $ordercode, $pr_data->idx, '2');

					//현재 주문의 상태값을 가져온다.
					list($old_step1, $old_step2) = pmysql_fetch_array(pmysql_query("select oi_step1, oi_step2 from tblorderinfo WHERE ordercode='" . trim($ordercode) . "'"));
					if (($old_step1 == '1' || $old_step1 == '2') && $old_step2 == '0') {
						//주문을 배송 준비중으로 변경한다.
						$sql2 = "UPDATE tblorderinfo SET oi_step1 = '2', oi_step2 = '0', deli_gbn='S' WHERE ordercode='" . $ordercode . "'";
						pmysql_query($sql2, get_db_conn());
					}

					$template = 'WEB14';
					$alim = new ALIM_TALK();
					$alim->makeAlimTalkSearchNewData($ordercode, $template , $pr_data->idx ,'');

				}else{
					$in_deli_sql = "UPDATE tblorderproduct SET  store_code='',delivery_type='0' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";
					pmysql_query($in_deli_sql, get_db_conn());
					
				}
			}
//		}
    }
	$sql="UPDATE tblorderinfo SET del_gbn='N' WHERE ordercode='{$ordercode}'";
	pmysql_query($sql,get_db_conn());

	if( strlen( $_ShopInfo->getMemid() ) > 0 && $last_price > 0 ){
		$memSql = "UPDATE tblmember SET sumprice = sumprice + ".$last_price."  WHERE id='".$_ShopInfo->getMemid()."'";
		pmysql_query($memSql,get_db_conn());
	}

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
<form name='form1' action="<?=$Dir?>m/order_end.php" method='get' target='_parent'>
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
<SCRIPT LANGUAGE="JavaScript">
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-99599198-1', 'auto');
	  ga('send', 'pageview');

	//alert('<?=$ordercode ?>');
	
	ga('require', 'ecommerce', 'ecommerce.js');
	ga('ecommerce:addTransaction', { 
	  'id': '<?=$ordercode ?>', 						// 시스템에서 생성된 주문번호. 필수. 
	  'affiliation': '<?=$paymethod ?>', 				// 제휴사이름. 선택사항. 
	  'revenue': '<?=$last_price ?>', 					// 구매총액. 필수. 
	  'shipping': '<?=$row->deli_price ?>', 			// 배송비. 선택사항. 
	  'tax': '0' 										// 세금. 선택사항.
	});
	ga('ecommerce:addItem', { 
	  'id': '<?=$ordercode ?>', 						// 시스템에서 생성된 주문번호. 필수. 
	  'name': '<?=$order_product_name ?>', 				// 제품명. 필수. 
	  'sku': '<?=$order_product_code ?>', 				// SKU 또는 제품고유번호. 선택사항. 
	  'category': '<?=$order_product_option ?>', 		// 제품 분류. 
	  'price': '<?=$last_price ?>', 					// 제품 단가. 
	  'quantity': '<?=$order_product_count ?>', 		// 제품 수량.
	  'currency': 'KRW'									// 국제환율
	});
	ga('ecommerce:send');

</SCRIPT>

<!-- 페이스북 마케팅 공통  -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1681170978578991'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1681170978578991&ev=PageView&noscript=1"/></noscript>

<!-- 구글 마케팅 공통 -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 852381434;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/852381434/?guid=ON&amp;script=0"/>
</div>
</noscript>

<?php
    if ( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) ) {
?>
<script type="text/javascript" src="/sinwon/m/static/js/buildV63.js"></script>
<SCRIPT LANGUAGE="JavaScript">
	csf('event','3','<?=$order_product_name ?>','<?=$last_price ?>');
</SCRIPT>
<!-- 구글 마케팅 구매완료 mobile -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 852381434;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "edWDCKvYmHEQ-p25lgM";
var google_conversion_value = "<?=$last_price ?>";
var google_conversion_currency = "KRW";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/852381434/?value=<?=$last_price ?>&amp;currency_code=KRW&amp;label=edWDCKvYmHEQ-p25lgM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<!-- 페이스북 마케팅 구매완료 -->
<script>
fbq('track', 'Purchase', {
value: <?=$last_price ?>,
currency: 'KRW'
});
</script>

<!-- 네이버 마케팅 구매완료 -->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script> 
<script type="text/javascript"> 
var _nasa={};
_nasa["cnv"] = wcs.cnv("1","<?=$last_price ?>"); // 전환가치 설정해야함. 설치매뉴얼 참고
</script> 

<!-- 네이버 마케팅 공통 -->
<script type="text/javascript"> 
if (!wcs_add) var wcs_add={};
wcs_add["wa"] = "s_4c8895fe304e";
if (!_nasa) var _nasa={};
wcs.inflow();
wcs_do(_nasa);
</script>

<form name='form1' action="<?=$Dir?>m/order_end.php" method='get' >
<input type='hidden' name='ordercode' value="<?=$ordercode?>">
</form>
<?php
    } else {
?>
<script type="text/javascript" src="/sinwon/web/static/js/buildV63.js"></script>
<SCRIPT LANGUAGE="JavaScript">
	csf('event','1','<?=$order_product_name ?>','<?=$last_price ?>');
</SCRIPT>
<!-- 구글 마케팅 구매완료 -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 852381434;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "edWDCKvYmHEQ-p25lgM";
var google_conversion_value = '<?=$last_price ?>';
var google_conversion_currency = "KRW";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/852381434/?value=<?=$last_price ?>&amp;currency_code=KRW&amp;label=edWDCKvYmHEQ-p25lgM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<!-- 페이스북 마케팅 구매완료 -->
<!-- 
value: Number(<?=$last_price ?>),
 -->
<script>
fbq('track', 'Purchase', {
value: <?=$last_price ?>,
currency: 'KRW'
});
</script>

<!-- 네이버 마케팅 구매완료 -->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script> 
<script type="text/javascript"> 
var _nasa={};
_nasa["cnv"] = wcs.cnv("1","<?=$last_price ?>"); // 전환가치 설정해야함. 설치매뉴얼 참고
</script> 

<!-- 네이버 마케팅 공통 -->
<script type="text/javascript"> 
if (!wcs_add) var wcs_add={};
wcs_add["wa"] = "s_4c8895fe304e";
if (!_nasa) var _nasa={};
wcs.inflow();
wcs_do(_nasa);
</script>

<form name='form1' action="<?=$Dir.FrontDir?>orderend.php" method='get' target='_parent'>
<input type='hidden' name='ordercode' value="<?=$ordercode?>">
</form>
<?php
    }
?>

<!-- 주문완료 -->
<script async=”true” src="https://cdn.megadata.co.kr/js/enliple_min2.js"></script> 
<script type="text/javascript">
   function mobConv(){
	  	var cn = new EN();
	  	cn.setData("uid",  "shinwonmall");
	  	cn.setData("ordcode",  "<?=$ordercode?>");
		cn.setData("pcode", "<?=$order_product_code ?>");  
		cn.setData("qty", "<?=$order_product_count ?>");
		cn.setData("price", "<?=$last_price ?>");
		cn.setData("pnm", encodeURIComponent(encodeURIComponent("<?=$order_product_name ?>"))); 
		cn.setSSL(true);
	 	cn.sendConv();
   }
</script>
<script async="true" src="https://cdn.megadata.co.kr/js/enliple_min2.js" onload="mobConv()"></script>

<!-- 공통 -->
<script type="text/javascript">
	function mobRf(){
  		var rf = new EN();
		rf.setSSL(true);
  		rf.sendRf();
	}
</script>
<script async="true" src="https://cdn.megadata.co.kr/js/enliple_min2.js" onload="mobRf()"></script>

</body>
</html>
<?
}
?>
