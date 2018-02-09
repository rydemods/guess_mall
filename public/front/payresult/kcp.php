<?php 
/*
KCP 공통 통보 처리 (RESULT=OK, RESULT=NO)
*/

#취소통보를 받으면 바로 정산보류를 다시 쏴준다.
			
if($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']) exit;

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$ip=$_SERVER['REMOTE_ADDR'];

$ordercode=$_POST["ordercode"];
$tx_cd=$_POST["tx_cd"];
$price=$_POST["price"];
$ok=$_POST["ok"];

$exe_id		= "||pg";	// 실행자 아이디|이름|타입

#kcp은행코드=>환불계좌 은행 array (2016-04-21 - 유동혁)
$re_bankcode = array(
	"39"=>1,
	"34"=>2,
	"04"=>3,
	"03"=>4,
	"11"=>5,
	"31"=>6,
	"32"=>8,
	"02"=>9,
	"45"=>11,
	"07"=>12,
	"48"=>13,
	"88"=>14,
	"05"=>15,
	"20"=>16,
	"71"=>17,
	"37"=>18,
	"35"=>19,
	"81"=>20,
	"27"=>21,
	"54"=>22,
	"23"=>23
);

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
$f = fopen("./log/kcp_common_return_".date("Ymd").".txt","a+");
fwrite($f,"########################################## START Common_Return_".$ordercode." ".date("Y-m-d H:i:s")."\r\n");
fwrite($f," ordercode = ".$ordercode."\r\n");
fwrite($f," tx_cd     = ".$tx_cd."\r\n");
fwrite($f," price     = ".$price."\r\n");
fwrite($f," ok        = ".$ok."\r\n");
fwrite($f," date      = ".$date."\r\n");
fwrite($f," REMOTE_ADDR = ".$_SERVER[REMOTE_ADDR]."\r\n");
fwrite($f,"########################################## END Common_Return_".$ordercode." ".date("Y-m-d H:i:s")."\r\n\r\n");
fclose($f);
chmod("./log/kcp_common_return_".date("Ymd").".txt",0777);

if($tx_cd=="TX00" && ord($ordercode) && ord($price) && ord($ok)) {	//가상계좌 입금통보
	######### paymethod=>"O|Q", pay_flag=>"0000", pay_admin_proc=>"N", (deli_gbn=>"N" OR (deli_gbn=>"D" AND ord(deli_date)==0))  ######
	//$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, deli_date FROM tblorderinfo ";
    $sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, deli_date, price, deli_price, dc_price, reserve, bank_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
    //log_txt_tmp($sql);
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
        // kcp 에서 넘어온 금액은 실결제 금액이므로, tblorderinfo.price 하고는 값이 다르다. 2015-11-19 by jhjeong
        //$compare_amt = $row->dc_price + $row->reserve - $row->deli_price;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);

	//중복 입금확인처리 방지 
	if($row->bank_date){
		echo "RESULT=OK"; exit;
	}

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

			$Sync = new Sync();
			$arrayDatax=array('ordercode'=>$ordercode);

			$srtn=$Sync->OrderInsert($arrayDatax);

			
			//ERP로 입금완료
			sendErporder($ordercode);

			
			//입금 확인 메일과 SMS을 보낸다 ( 벤더에도 발송되기에 메일이 없어도 함수를 호출한다 )
			SendBankMail( $shopinfo_row->shopname, $shopurl, $shopinfo_row->design_mail, $shopinfo_row->info_email, $row->sender_email, $ordercode );

			# SMS ( 입금 확인 안내 )
			$mem_return_msg = sms_autosend( 'mem_bankok', $ordercode, '', '' );
			$admin_return_msg = sms_autosend( 'admin_bankok', $ordercode, '', '' );


			//주문완료시 본사발송이 아닐경우 입찰로 돌림
			$pr_qry="select op.*, p.prodcode, p.colorcode from tblorderproduct op left join tblproduct p on(op.productcode=p.productcode) where op.ordercode='".$ordercode."' AND (op.delivery_type = '0') AND op.op_step in ('1')";
			$pr_result=pmysql_query($pr_qry);
			while($pr_data=pmysql_fetch_object($pr_result)){
				$maket_stock=getErpPriceNStock($pr_data->prodcode, $pr_data->colorcode, $pr_data->opt2_name, $sync_bon_code);

				if($maket_stock[sumqty]>0){
					pmysql_query("update tblorderproduct set store_code='".$sync_bon_code."' where idx='".$pr_data->idx."'");
				}else{

					$sync_idx = $pr_data->idx;
					$arrayDatax_p=array('ordercode'=>$ordercode,'sync_idx'=>'AND idx='.$sync_idx);
					$deli_type_sql = "UPDATE tblorderproduct SET store_code='',delivery_type='2' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";
					pmysql_query($deli_type_sql, get_db_conn());

					$sql = "INSERT INTO tblorderproduct_store_change(ordercode, idx, regdt) VALUES ('{$ordercode}','{$sync_idx}','".date('YmdHis')."')";
					pmysql_query($sql, get_db_conn());

					$srtn=$Sync->OrderInsert($arrayDatax_p);

					#싱크커머스 API호출
					if($srtn != 'fail') {

						//변경전 erp로 전송
						sendErpChangeShop($ordercode, $sync_idx, '', '2');

						#주문정보 update
						$in_deli_sql = "UPDATE tblorderproduct SET delivery_type='2' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";

						pmysql_query($in_deli_sql, get_db_conn());
						#배송준비중으로 변경
						$exe_id		= $reg_id."|".$reg_name."|pg";	// 실행자 아이디|이름|타입
						orderProductStepUpdate($exe_id, $ordercode, $pr_data->idx, '2');

						//현재 주문의 상태값을 가져온다.
						list($old_step1, $old_step2) = pmysql_fetch_array(pmysql_query("select oi_step1, oi_step2 from tblorderinfo WHERE ordercode='" . trim($ordercode) . "'"));
						if (($old_step1 == '1' || $old_step1 == '2') && $old_step2 == '0') {
							//주문을 배송 준비중으로 변경한다.
							$sql2 = "UPDATE tblorderinfo SET oi_step1 = '2', oi_step2 = '0', deli_gbn='S' WHERE ordercode='" . $ordercode . "'";
							pmysql_query($sql2, get_db_conn());
						}
						
					}else{
						$in_deli_sql = "UPDATE tblorderproduct SET  store_code='',delivery_type='0' WHERE ordercode='{$ordercode}' and idx={$pr_data->idx} ";
						pmysql_query($in_deli_sql, get_db_conn());
						
					}
				}
			}
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
			@mail(AdminMail,"KCP 가상계좌 입금처리 안됨","$sql<br>".pmysql_error());
		}
	}
/*
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
	*/
} else if($tx_cd=="TX01" && ord($ordercode)) {	//가상계좌 환불통보
	######### paymethod=>"Q", pay_flag=>"0000", pay_admin_proc=>"N", deli_gbn=>"E"  ######
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, bank_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$bank_date=$row->bank_date;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);

	if(strstr("Q", $paymethod[0])) {
		$pg_sql = "SELECT refund_bank_code, refund_account, refund_name ";
		$pg_sql.= "FROM tblpvirtuallog WHERE ordercode = '".$ordercode."' ";
		$pg_res = pmysql_query( $pg_sql, get_db_conn() );
		$pg_row = pmysql_fetch_object( $pg_res );

		$bankcode	 = $pg_row->refund_bank_code;
		$bankaccount = $pg_row->refund_account;
		$bankuser	 = $pg_row->refund_name;

		$sql = "UPDATE tblorderinfo ";
		$sql.= "SET pay_admin_proc='C', deli_gbn='C' "; // , bank_date='".substr($bank_date,0,8)."X'
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			$oc_no_arr = array();
			$oc_no_str = '';
			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			pmysql_query( $sql, get_db_conn() );
			// 환불 완료 상태로 변경해준다
			$cancel_sql = "SELECT op.ordercode, op.oc_no , array_to_string( array_agg( op.idx ), '|' ) AS idxs ";
			$cancel_sql.= "FROM tblorderproduct op ";
			$cancel_sql.= "JOIN tblorder_cancel oc ON ( op.ordercode = oc.ordercode AND op.oc_no = oc.oc_no ) ";
			$cancel_sql.= "WHERE ordercode = '".$ordercode."' AND  op.op_step != 44 AND oc.restore != 'Y' ";
			$cancel_sql.= "GROUP BY op.ordercode, op.oc_no ";
			$cancel_res = pmysql_query( $cancel_sql, get_db_conn() );
			while( $cancel_row = pmysql_fetch_object( $cancel_res ) ){
				orderCancelFin($exe_id,  $ordercode, $cancel_row->idxs, $cancel_row->oc_no, 'Y', $re_bankcode[$bankcode], $bankaccount, $bankuser, 0, 'Y' );
				$oc_no_arr[] = $cancel_row->oc_no;
			}
			pmysql_free_result( $cancel_res );
			// tblorder_cancel의 pgcancel 상태를 Y 로 바꾼다
			if( count( $oc_no_arr ) > 0 ) {
				$oc_no_str = implode( ',', $oc_no_arr );
				$oc_sql = "UPDATE tblorder_cancel SET pgcancel = 'Y' WHERE oc_no IN (".$oc_no_str.") ";
				pmysql_query( $oc_sql, get_db_conn() );
			}

			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"KCP 가상계좌 환불완료처리 안됨","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO";
	}
} else if($tx_cd=="TX02" && ord($ordercode) && ord($ok)) {//구매확인/취소
	
	//echo "RESULT=OK";
	
	#### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"!C", deli_gbn=>"Y", escrow_result=>"N" #####
	$sql = "SELECT paymethod,pay_flag,pay_admin_proc,deli_gbn,deli_date,escrow_result, oi_step1, oi_step2 FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
		$escrow_result=$row->escrow_result;
		$oi_step1 = $row->oi_step1;
		$oi_step2 = $row->oi_step2;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);
	
	//구매확인은 솔루션 기간으로 한다.2017-04-07
	if($ok=="Y" && strstr("QP", $paymethod[0])) {			//구매확인
		echo "RESULT=OK";
		/*
		$sql = "UPDATE tblorderinfo SET escrow_result='Y' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
            //주문 상태 변경
			$state_sql = "SELECT oc_no FROM tblorderproduct WHERE ordercode = '".$ordercode."' ";
			$state_sql.= "AND oc_no > 0 AND (op_step = 41 OR op_step = 40) ";
			$state_res = pmysql_query( $state_sql, get_db_conn() );
			while( $state_row = pmysql_fetch_object( $state_res ) ){
				orderCancelRestore($exe_id,  $ordercode, $state_row->oc_no );
			}
			pmysql_free_result( $state_res );
			orderStepUpdate($exe_id,  $ordercode, 4 );
			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"KCP 에스크로 구매확인처리 안됨","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}*/
	} else if($ok=="C" && strstr("QP", $paymethod[0])) {	//구매취소
		$sql = "UPDATE tblorderinfo SET ";
		$sql.= "deli_gbn		= 'H' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			$pr_idx = array();
			$str_pridx = '';
			$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
			$sql.= "WHERE ordercode='{$ordercode}' RETURNING idx ";
			$cancel_op_res = pmysql_query($sql,get_db_conn());
			while( $cancel_op_row = pmysql_fetch_object( $cancel_op_res ) ){
				$pr_idx[] = $cancel_op_row->idx;
			}
			$str_pridx = implode( '|', $pr_idx );
			orderCancel($exe_id,  $ordercode, $str_pridx, $oi_step1, $oi_step2, $oi_step1, $paymethod[0], 10, '구매자 KCP 직접 취소' );
			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"KCP 에스크로 구매취소처리 안됨","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($tx_cd=="TX03" && ord($ordercode)) {//배송시작 통보
	######### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"N|Y", deli_gbn=>"N|S|X"  ######
	$deli_num=$_POST["deli_num"];
	$deli_name=$_POST["deli_name"];
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, deli_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);
	if(strstr("QP", $paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET deli_gbn='Y', deli_date='{$date}' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='Y' ";
			$sql.= "WHERE ordercode='{$ordercode}' AND op_code < 40 ";
			//$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			pmysql_query($sql,get_db_conn());
			orderStepUpdate($exe_id,  $ordercode, 3 );
			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"KCP 에스크로 배송시작처리 안됨","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($tx_cd=="TX04" && ord($ordercode)) {//정산보류 통보
	### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"N|Y", (deli_gbn=>"Y" OR (deli_gbn=>"D" AND strlen(deli_date)==14)), escrow_result=>"N"  ###
	$sql = "SELECT paymethod,pay_flag,pay_admin_proc,deli_gbn,deli_date,escrow_result, oi_step1, oi_step2 FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
		$escrow_result=$row->escrow_result;
		$oi_step1 = $row->oi_step1;
		$oi_step2 = $row->oi_step2;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);
	if(strstr("QP", $paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET deli_gbn='H' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			$pr_idx = array();
			$str_pridx = '';
			$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
			$sql.= "WHERE ordercode='{$ordercode}' RETURNING idx ";
			//$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			$cancel_op_res = pmysql_query($sql,get_db_conn());
			while( $cancel_op_row = pmysql_fetch_object( $cancel_op_res ) ){
				$pr_idx[] = $cancel_op_row->idx;
			}
			$str_pridx = implode( '|', $pr_idx );
			orderCancel($exe_id,  $ordercode, $str_pridx, $oi_step1, $oi_step2, $oi_step1, $paymethod[0].'K', 10, 'KCP 정산보류 통보' );

			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"KCP 에스크로 정산보류처리 안됨","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($tx_cd=="TX05" && ord($ordercode)) {//즉시취소 통보
	######### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"N|Y", (deli_gbn=>"N|S|X" OR (deli_gbn=>"D" AND ord(deli_date)==0))  ######
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, deli_date, oi_step1, oi_step2 FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod      = $row->paymethod;
		$pay_flag       = $row->pay_flag;
		$pay_admin_proc = $row->pay_admin_proc;
		$deli_gbn       = $row->deli_gbn;
		$deli_date      = $row->deli_date;
		$oi_step1       = $row->oi_step1;
		$oi_step2       = $row->oi_step2;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);
	if(strstr("QP", $paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET ";
		if(strstr("P", $paymethod[0])) {	//신용카드는 정상 즉시취소처리
			$sql.= "pay_admin_proc	= 'C', ";
			$sql.= "deli_gbn		= 'C', ";
			$temp_deli_gbn="C";
		} else {	//가상계좌는 환불대기상태로
			$sql.= "deli_gbn		= 'E' ";
			$temp_deli_gbn="E";
		}
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			$pr_idx = array();
			$str_pridx = '';

			$sql = "UPDATE tblorderproduct SET deli_gbn='{$temp_deli_gbn}' ";
			$sql.= "WHERE ordercode='{$ordercode}' RETURNING idx ";
			$cancel_op_res = pmysql_query( $sql, get_db_conn() );
			while( $cancel_op_row = pmysql_fetch_object( $cancel_op_res ) ){
				$pr_idx[] = $cancel_op_row->idx;
			}
			$str_pridx = implode( '|', $pr_idx );
			orderCancel($exe_id,  $ordercode, $str_pridx, $oi_step1, $oi_step2, $oi_step1, $paymethod[0], 10, 'KCP 즉시취소처리 통보' );

			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"KCP 에스크로 즉시취소처리 안됨","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($tx_cd=="TX06" && ord($ordercode)) {//취소 통보 (정산보류 처리된 건)
	######### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"N|Y", deli_gbn=>"H"  ######
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, oi_step1, oi_step2 FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result = pmysql_query( $sql, get_db_conn() );
	if( $row = pmysql_fetch_object( $result ) ) {
		$paymethod      = $row->paymethod;
		$pay_flag       = $row->pay_flag;
		$pay_admin_proc = $row->pay_admin_proc;
		$deli_gbn       = $row->deli_gbn;
		$oi_step1       = $row->oi_step1;
		$oi_step2       = $row->oi_step2;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);
	if(strstr("QP", $paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET ";
		if(strstr("P", $paymethod[0])) {	//신용카드는 정상 취소처리
			$sql.= "pay_admin_proc	= 'C', ";
			$sql.= "escrow_result	= 'C', ";
			$sql.= "deli_gbn		= 'C', ";
			$temp_deli_gbn="C";
		} else {	//가상계좌는 환불대기상태로
			$sql.= "escrow_result	= 'C', ";
			$sql.= "deli_gbn		= 'E' ";
			$temp_deli_gbn="E";
		}
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			$pr_idx = array();
			$str_pridx = '';

			$sql = "UPDATE tblorderproduct SET deli_gbn='{$temp_deli_gbn}' ";
			$sql.= "WHERE ordercode='{$ordercode}' RETURNING idx ";
			$cancel_op_res = pmysql_query( $sql, get_db_conn() );
			while( $cancel_op_row = pmysql_fetch_object( $cancel_op_res ) ){
				$pr_idx[] = $cancel_op_row->idx;
			}
			$str_pridx = implode( '|', $pr_idx );
			orderCancel($exe_id,  $ordercode, $str_pridx, $oi_step1, $oi_step2, $oi_step1, $paymethod[0], 10, 'KCP 취소 통보', '', '', '', '', 'B' );
				
			$redeli_sql = "UPDATE tblorderinfo SET redelivery_type='Y', redelivery_date='".date("YmdHis")."' WHERE ordercode='".trim($ordercode)."' ";
			pmysql_query($redeli_sql,get_db_conn());
			$redelip_sql = "UPDATE tblorderproduct SET ";
			$redelip_sql.= "redelivery_type='Y' , redelivery_date='".date("YmdHis")."' ";
			$redelip_sql.= "WHERE ordercode='".trim($ordercode)."' ";
			pmysql_query($redelip_sql,get_db_conn());
			
			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"KCP 에스크로 취소처리 안됨","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($tx_cd=="TX07" && ord($ordercode)) {//발급계좌 해지 통보
	######### paymethod=>"Q", pay_flag=>"0000", pay_admin_proc=>"N", (deli_gbn=>"N" OR (deli_gbn=>"D" AND ord(deli_date)==0))  ######
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, deli_date, oi_step1, oi_step2 FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod      = $row->paymethod;
		$pay_flag       = $row->pay_flag;
		$pay_admin_proc = $row->pay_admin_proc;
		$deli_gbn       = $row->deli_gbn;
		$deli_date      = $row->deli_date;
		$oi_step1       = $row->oi_step1;
		$oi_step2       = $row->oi_step2;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);
	if(strstr("QP", $paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			$pr_idx = array();
			$str_pridx = '';

			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='{$ordercode}' RETURNING idx ";
			$cancel_op_res = pmysql_query($sql,get_db_conn());
			while( $cancel_op_row = pmysql_fetch_object( $cancel_op_res ) ){
				$pr_idx[] = $cancel_op_row->idx;
			}
			$str_pridx = implode( '|', $pr_idx );
			orderCancel($exe_id,  $ordercode, $str_pridx, $oi_step1, $oi_step2, $oi_step1, $paymethod[0], 10, 'KCP 발급계좌 해지 통보' );

			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"KCP 에스크로 발급계좌해지처리 안됨","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else {
	echo "RESULT=NO";
}
?>
