<?php 
Header("Pragma: no-cache");
/*
NICE 공통 통보 처리 (RESULT=OK, RESULT=NO)
*/
if($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']) exit;

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


$ip=$_SERVER['REMOTE_ADDR'];

$ordercode=$_POST["ordercode"];
$tx_cd=$_POST["tx_cd"];
$price=$_POST["price"];
$ok=$_POST["ok"];

$exe_id		= "||pg";	// 실행자 아이디|이름|타입

# 메일 발송을 위한 shopurl 2016-03-22 유동혁
if(ord(RootPath)) {
	$hostscript=$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER["HTTP_HOST"].'/';
}
# shop 정보 2016-03-22 유동혁
$shopinfo_sql = "SELECT shopname, design_mail, info_email FROM tblshopinfo ";
$shopinfo_res = pmysql_query( $shopinfo_sql, get_db_conn() );
$shopinfo_row = pmysql_fetch_object( $shopinfo_res );
pmysql_free_result( $shopinfo_res );

//log_txt_tmp($ordercode);
//log_txt_tmp($tx_cd);
//log_txt_tmp($price);
//log_txt_tmp($ok);
$paymethod="";
$istaxsave=false;
$date=date("YmdHis");
$f = fopen("./log/nice_common_return_".date("Ymd").".txt","a+");
fwrite($f,"########################################## START Common_Return_".$ordercode." ".date("Y-m-d H:i:s")."\r\n");
fwrite($f," ordercode = ".$ordercode."\r\n");
fwrite($f," tx_cd     = ".$tx_cd."\r\n");
fwrite($f," price     = ".$price."\r\n");
fwrite($f," ok        = ".$ok."\r\n");
fwrite($f," date      = ".$date."\r\n");
fwrite($f," REMOTE_ADDR = ".$_SERVER[REMOTE_ADDR]."\r\n");
fwrite($f,"########################################## END Common_Return_".$ordercode." ".date("Y-m-d H:i:s")."\r\n\r\n");
fclose($f);
chmod("./log/nice_common_return_".date("Ymd").".txt",0777);
if($tx_cd=="4110" && ord($ordercode) && ord($price) && ord($ok)) {	//가상계좌 입금통보
	######### paymethod=>"O|Q", pay_flag=>"0000", pay_admin_proc=>"N", (deli_gbn=>"N" OR (deli_gbn=>"D" AND ord(deli_date)==0))  ######
	//$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, deli_date FROM tblorderinfo ";
    $sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, deli_date, price, deli_price, dc_price, reserve FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
    //log_txt_tmp($sql);
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
        // nice 에서 넘어온 금액은 실결제 금액이므로, tblorderinfo.price 하고는 값이 다르다. 2015-11-19 by jhjeong
        //$compare_amt = $row->dc_price + $row->reserve - $row->deli_price;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);

	if($ok=="Y" && strstr("OQ", $paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET bank_date='{$date}', deli_gbn='N' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";

		if(pmysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='N' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			//$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			pmysql_query($sql,get_db_conn());

			//주문 상태 변경
			orderStepUpdate($exe_id,  $ordercode, 1 );

			//상품 수량 차감
			order_quantity( $ordercode );

			//입금 확인 메일과 SMS을 보낸다 ( 벤더에도 발송되기에 메일이 없어도 함수를 호출한다 )
			SendBankMail( $shopinfo_row->shopname, $shopurl, $shopinfo_row->design_mail, $shopinfo_row->info_email, $row->sender_email, $ordercode );

			# SMS ( 입금 확인 안내 )
			$mem_return_msg = sms_autosend( 'mem_bankok', $ordercode, '', '' );
			$admin_return_msg = sms_autosend( 'admin_bankok', $ordercode, '', '' );

		}
	} else if($ok=="C" && strstr("OQ", $paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET bank_date=NULL ";
		//$sql.= "WHERE ordercode='{$ordercode}' AND price='{$price}' ";
        //$sql.= "WHERE ordercode='{$ordercode}' AND price={$price + $compare_amt} ";
        $sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
	} else {
		echo "RESULT=NO"; exit;
	}

	if(!pmysql_error()) {
		echo "RESULT=OK";
		$istaxsave=true;
	} else {
		echo "RESULT=NO";
		if(ord(AdminMail)) {
			@mail(AdminMail,"NICE 가상계좌 입금처리 안됨","$sql<br>".pmysql_error());
		}
	}

	if($istaxsave) {
		$sql = "SELECT tax_type FROM tblshopinfo ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$tax_type=$row->tax_type;
		pmysql_free_result($result);

		if($tax_type=="Y") {
			if(strstr("OQ", $paymethod[0])) {
				$sql = "SELECT bank_date, pay_admin_proc FROM tblorderinfo ";
				$sql.= "WHERE ordercode='{$ordercode}' ";				
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				pmysql_free_result($result);
				if(strlen($row->bank_date)>=12 && $row->pay_admin_proc=="Y") {
					$sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='{$ordercode}' AND type='N' ";
					$result=pmysql_query($sql,get_db_conn());
					$row=pmysql_fetch_object($result);
					pmysql_free_result($result);
					if($row->cnt>0) {
						$flag="Y";
						include($Dir."lib/taxsave.inc.php");
					}
				} else {
					//현금영수증 취소처리를 해야될까?????
				}
			}
		}
	}
} else {
	echo "RESULT=NO";
}
?>