<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/coupon.class.php");


if($_REQUEST['mode']=='paper'){
	$papercode	= $_REQUEST['papercode'];

	//exdebug($_REQUEST);

	$memid	= $_ShopInfo->getMemid();
	$msg = "0";
	//exdebug($memid);

	#쿠폰 설정 
	$_CouponInfo = new CouponInfo( '7' ); 
	//exdebug($_CouponInfo);

	#쿠폰 확인 
	$return_code = $_CouponInfo->search_paper_coupon( $papercode, $memid );	
	//exdebug($return_code );
	
	if ($return_code == '1') {	// 정상 쿠폰 발급인 경우
		#insert 설정 
		$_CouponInfo->set_couponissue( $memid ); 
		$returnArr = $_CouponInfo->insert_couponissue(); 
		if ($returnArr[0] == 0) {
			#정상 발급
			$msg = "1";
		} else {
			#발급시 오류
			$msg = "0";
		}
	} else {							// 쿠폰 확인시 오류		
		/* 3 : 페이퍼 쿠폰 없음							*/
		/* 2 : 페이퍼 쿠폰 발급 받음					*/
		/* 4 : 페이퍼 쿠폰 발급 후 사용				*/
		/* 5 : 페이퍼 쿠폰 발급 후 사용안함		*/
		$msg = $return_code;
	}



	/*list($coupon_code, $used)=pmysql_fetch("SELECT coupon_code, used FROM tblcouponpaper WHERE papercode='".$_POST['papercode']."'");
	
	if($coupon_code && $used == 'N'){
		# 페이퍼 발급
		$sql = "SELECT * FROM tblcouponinfo WHERE coupon_type='7' AND (date_end>'".date("YmdH")."' OR date_end='') AND coupon_code = '".$coupon_code."' ORDER BY date DESC ";
		$res = pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($res)) {
			$date=date("YmdHis");
			if($row->date_start>0) {
				$date_start=$row->date_start;
				$date_end=$row->date_end;
			} else {
				$date_start = substr($date,0,10);
				$date_end = date("Ymd23",strtotime("+".abs($row->date_start)." day"));
			}

			list($chkId, $chkUsed)=pmysql_fetch("SELECT id, used FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND coupon_code = '".$coupon_code."'");
			if(!$chkId){
				$sqlGive = "	INSERT INTO tblcouponissue 
										(coupon_code, id, date_start, date_end, date) 
									VALUES 
										('".$row->coupon_code."', '".$_ShopInfo->getMemid()."', '{$date_start}', '{$date_end}', '{$date}')";
				pmysql_query($sqlGive,get_db_conn());

				$sqlUsed = "UPDATE tblcouponpaper SET used = 'Y' WHERE papercode='".$_POST['papercode']."'";
				pmysql_query($sqlUsed, get_db_conn());

				$sqlUsed2 = "UPDATE tblcouponinfo SET issue_no = issue_no+1 WHERE coupon_code = '".$row->coupon_code."'";
				pmysql_query($sqlUsed2, get_db_conn());
				#정상 발급
				$msg = "1";
			}else if($chkUsed == 'Y'){
				$sqlGive = "UPDATE tblcouponissue SET used = 'N', date_start = '{$date_start}', date_end = '{$date_end}', date = '{$date}' WHERE coupon_code = '".$row->coupon_code."'";
				pmysql_query($sqlGive,get_db_conn());

				$sqlUsed = "UPDATE tblcouponpaper SET used = 'Y' WHERE papercode='".$_POST['papercode']."'";
				pmysql_query($sqlUsed, get_db_conn());
				#사용 쿠폰 날짜 갱신
				$msg = "4";
			}else if($chkUsed == 'N'){
				#사용하지 않은 쿠폰 존재
				$msg = "5";
			}
		}
	}else if($coupon_code && $used == 'Y'){
		$msg = "2";
	}else if(!$coupon_code){
		$msg = "3";
	}*/
}

echo $msg;
?>