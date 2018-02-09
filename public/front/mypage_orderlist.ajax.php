<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if (!$_shopdata) {
	$_shopdata=new ShopData($_ShopInfo);
	$_shopdata=$_shopdata->shopdata;
	$_ShopInfo->getPgdata();
	$_shopdata->escrow_id	= $_data->escrow_id;
	$_shopdata->trans_id		= $_data->trans_id;
	$_shopdata->virtual_id		= $_data->virtual_id;
	$_shopdata->card_id		= $_data->card_id;
	$_shopdata->mobile_id	= $_data->mobile_id;
}

if($_POST['mode']=='deli_ok'){	// 배송완료
	$ordercode		= $_POST['ordercode'];
	$idx				= $_POST['idx'];

	//실행자 이름을 가져온다 (2016.10.07 - 김재수 추가)
	if ($_ShopInfo->getMemname()) {
		$reg_name	= $_ShopInfo->getMemname();
	} else {
		list($reg_name)=pmysql_fetch_array(pmysql_query("select sender_name from tblorderinfo WHERE ordercode='".trim($ordercode)."' "));
	}
	$exe_id		= $_ShopInfo->getMemid()."|".$reg_name."|user";	// 실행자 아이디|이름|타입

	$sql = "UPDATE tblorderproduct SET receive_ok = '1' ,deli_gbn='F' ";
	$sql.= "WHERE ordercode='{$ordercode}' AND idx='{$idx}' ";
	$sql.= "AND op_step < 40 ";

	pmysql_query($sql,get_db_conn());
	$ErpCancelDate="";
	if( !pmysql_error() ){

		// 신규상태 변경 추가 - (2016.02.18 - 김재수 추가)
		orderProductStepUpdate($exe_id, $ordercode, $idx, '4'); // 배송완료

		//주문중 배송완료, 취소완료상태가 아닌경우
		list($op_idx_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $idx)."') AND (op_step != '4' AND op_step != '44')"));

		if ($op_idx_cnt == 0) {
			$sql = "UPDATE tblorderinfo SET receive_ok = '1', deli_gbn = 'F' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			pmysql_query($sql,get_db_conn());
		}
	
		list($deli_reserve)=pmysql_fetch_array(pmysql_query("select reserve from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx IN ('".str_replace("|", "','", $idx)."') "));

		$sql = "UPDATE tblorderproduct SET order_conf = '1', order_conf_date='".date('YmdHis')."' ";
		$sql.= "WHERE ordercode='{$ordercode}' AND idx='{$idx}' ";
		$sql.= "AND op_step < 40 ";

		pmysql_query($sql,get_db_conn());
		if( !pmysql_error() ){
						
			//적립 예정 적립금을 지급한다.
			if ($deli_reserve != 0) insert_point($_ShopInfo->getMemid(), $deli_reserve, "주문 ".$ordercode." 배송완료(".count($idx)."건)에 의한 포인트 지급", '@order_end','','', $return_point_term);

			//주문중 배송완료, 취소완료상태가 아닌경우
			list($op_idx_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $idx)."') AND ((op_step != '4' OR (op_step = '4' AND order_conf !='1')) AND op_step != '44') "));

			if ($op_idx_cnt == 0) {
				$sql = "UPDATE tblorderinfo SET order_conf = '1', order_conf_date='".date('YmdHis')."' ";
				$sql.= "WHERE ordercode='{$ordercode}' ";
				pmysql_query($sql,get_db_conn());
			}

			//ERP통신오류로 맨하단에서 처리함
			//$ErpCancelDate["deli_ok"][$idx]["ordercode"]=$ordercode;
			//배송완료시 erp로 전송
			sendErpOrderEndInfo($ordercode, $idx);

			$msg	= "구매확정 되었습니다.";
			$msgType = "1";
		} else {
			$msg = "구매확정 실패. 관리자에게 문의해주세요.";
			$msgType = "0";
		}
	} else {
		$msg = "구매확정 실패. 관리자에게 문의해주세요.";
		$msgType = "0";
	}

	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg);
	$msg = json_encode($tmpMsgArray);

/*}else if($_POST['mode']=='ord_conf'){	// 구매확정
	$ordercode		= $_POST['ordercode'];
	$idx				= $_POST['idx'];
	
	list($deli_reserve)=pmysql_fetch_array(pmysql_query("select reserve from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx IN ('".str_replace("|", "','", $idx)."') "));

	$sql = "UPDATE tblorderproduct SET order_conf = '1', order_conf_date='".date('YmdHis')."' ";
	$sql.= "WHERE ordercode='{$ordercode}' AND idx='{$idx}' ";
	$sql.= "AND op_step < 40 ";

	pmysql_query($sql,get_db_conn());
	if( !pmysql_error() ){
					
		//적립 예정 적립금을 지급한다.
		if ($deli_reserve != 0) insert_point_act($_ShopInfo->getMemid(), $deli_reserve, "주문 ".$ordercode." 배송완료(".count($idx)."건)에 의한 적립금 지급", '','','', $return_point_term);

		//주문중 배송완료, 취소완료상태가 아닌경우
		list($op_idx_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $idx)."') AND ((op_step != '4' OR (op_step = '4' AND order_conf !='1')) AND op_step != '44') "));

		if ($op_idx_cnt == 0) {
			$sql = "UPDATE tblorderinfo SET order_conf = '1', order_conf_date='".date('YmdHis')."' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			pmysql_query($sql,get_db_conn());
		}
		$msg	= "구매확정 되었습니다.";
		$msgType = "1";
	} else {
		$msg = "구매확정 실패. 관리자에게 문의해주세요.";
		$msgType = "0";
	}

	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg);
	$msg = json_encode($tmpMsgArray);*/

}else if($_POST['mode']=='cancel_check'){//주문취소 상태체크시

	$c_ordercode			= $_POST['ordercode'];
	$c_idx					= $_POST['idx'];
	$c_idxs					= $_POST['idxs'];

	$cancelCheck_sql = "select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' ";	
	if ($c_idx && !$c_idxs) $cancelCheck_sql.= "AND idx='".trim($c_idx)."' ";
	if ($c_idxs) $cancelCheck_sql.= "AND idx IN ('".str_replace("|", "','", trim($c_idxs))."') ";
	$cancelCheck_sql.= "AND op_step IN ('40','41','42','43','44') ";

	list($op_idx_cnt)=pmysql_fetch_array(pmysql_query($cancelCheck_sql));
	if ($op_idx_cnt == 0) {
		$msgType	= '1';
		$msg			= '취소 가능한 주문건입니다.';
	} else {
		$msgType	= '0';
		$msg			= '이미 취소진행 또는 완료된 주문건입니다.';
	}
	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg);
	$msg = json_encode($tmpMsgArray);

}else if($_POST['mode']=='receive_cancel'){//주문취소

	$c_ordercode			= $_POST['ordercode'];
	$c_idxs					= $_POST['idxs'];
	$c_paymethod		= $_POST['paymethod'];

	//실행자 이름을 가져온다 (2016.10.07 - 김재수 추가)
	if ($_ShopInfo->getMemname()) {
		$reg_name	= $_ShopInfo->getMemname();
	} else {
		list($reg_name)=pmysql_fetch_array(pmysql_query("select sender_name from tblorderinfo WHERE ordercode='".trim($c_ordercode)."' "));
	}
	$exe_id		= $_ShopInfo->getMemid()."|".$reg_name."|user";	// 실행자 아이디|이름|타입

	if(strlen($c_ordercode)==20 && substr($c_ordercode,-1)!="X") {
		$add_qry	= " AND id = '".$_ShopInfo->getMemid()."' ";
	}

	list($chk_order, $c_oi_step1, $c_oi_step2) = pmysql_fetch("SELECT ordercode, oi_step1, oi_step2, paymethod FROM tblorderinfo WHERE deli_gbn='N' AND (SUBSTR(paymethod,1,1) IN ('B','O','Q') AND (bank_date IS NULL OR bank_date='')) AND ordercode='".$_POST['ordercode']."' {$add_qry} ");
	if($chk_order){		

		$fail_cnt	= 0;
			
		if (strstr("Q", $c_paymethod) && $c_oi_step1 == 0) {	
			//KCP에 정산보류 전달.

			$pgid_info=GetEscrowType($_shopdata->escrow_id);
			$pg_type=$pgid_info["PG"];				
			$pg_type=trim($pg_type);

			// 발급계좌 해지로 보낸다.
			$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode=".$c_ordercode;
			$cancel_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$pg_type."/escrow_cancel.php",$query);
			$cancel_data=substr($cancel_data,strpos($cancel_data,"RESULT=")+7);
			if (substr($cancel_data,0,2)!="OK") {
				$tempdata=explode("|",$cancel_data);
				$errmsg="정산보류 정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					$alert_text = $errmsg;
					$msg = $alert_text;
					$fail_cnt++;
				}
			} else {
				$tempdata=explode("|",$cancel_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					//$alert_text = $errmsg;
				}
			}
		}

		if ($fail_cnt == 0) {
			// 주문취소를 한다.
			orderCancel($exe_id, $c_ordercode, $c_idxs, $c_oi_step1, $c_oi_step2, $c_oi_step1, $c_paymethod );

			$sqlCancel = "UPDATE tblorderinfo SET deli_gbn = 'C' WHERE ordercode = '".$chk_order."'";		
			if(pmysql_query($sqlCancel,get_db_conn())){			
				//상품들도 모두 주문취소로 변경한다.
				$sql = "UPDATE tblorderproduct SET deli_gbn = 'C' WHERE ordercode= '".$chk_order."'";		
				pmysql_query($sql,get_db_conn());

				$msg	= "해당 주문을 취소 하였습니다.";
				$msgType = "1";
			}else{
				$msg = "취소 실패. 관리자에게 문의해주세요.";
				$msgType = "0";
			}
		}else{
			$msg = "취소 실패. 관리자에게 문의해주세요.";
			$msgType = "0";
		}
	}else{
			$msg = $alert_text;
			$msgType = "0";
	}

	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg);
	$msg = json_encode($tmpMsgArray);

}else if($_POST['mode']=='redelivery'){ // 취소, 반품,교환 접수
	$c_re_type				= $_POST['re_type'];
	$c_ordercode			= $_POST['ordercode'];
	$c_idx					= $_POST['idx'];
	$c_idxs					= $_POST['idxs'];
	$c_sel_code			= $_POST['sel_code'];
	$c_sel_sub_code		= $_POST['sel_sub_code'];
	$c_paymethod		= $_POST['paymethod'];
	$c_memo				= pmysql_escape_string(trim($_POST['memo']));
	$c_bankcode			= $_POST['bankcode'];
	$c_bankaccount		= $_POST['bankaccount'];
	$c_bankuser			= $_POST['bankuser'];
	$c_bankusertel		= $_POST['bankusertel'];
	$c_productcode		= $_POST['productcode'];
	$c_opt1_changes	= $_POST['opt1_changes'];
	$c_opt2_changes	= $_POST['opt2_changes'];
	$c_opt2_pt_changes		= $_POST['opt2_pt_changes'];
	$c_opt_text_s_changes	= $_POST['opt_text_s_changes'];
	$c_opt_text_c_changes	= $_POST['opt_text_c_changes'];
	$c_pgcancel_type			= $_POST['pgcancel_type'];
	$c_pgcancel_res_code	= $_POST['pgcancel_res_code'];
	$c_pgcancel_res_msg		= $_POST['pgcancel_res_msg'];
	$c_pgcancel_res_msg		= $_POST['pgcancel_res_msg'];
	$c_receipt_name          =  $_POST['receipt_name'];
	$c_receipt_tel               = $_POST['receipt_tel'];
	$c_receipt_mobile         = $_POST['receipt_mobile'];
	$c_receipt_addr            = $_POST['receipt_addr'];
	$c_return_deli_price		= $_POST['return_deli_price'];
	$c_return_deli_receipt		= $_POST['return_deli_receipt'];
	$c_return_deli_type		= $_POST['return_deli_type'];
	$c_return_deli_memo		= $_POST['return_deli_memo'];
	//exdebug($_POST);
	//exit;
	
	// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
	$textLogDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/redelivery_logs_'.date("Ym").'/';
	$outText = '시작 ========================='.date("Y-m-d H:i:s")."=============================\n";

	
	//실행자 이름을 가져온다 (2016.10.07 - 김재수 추가)
	if ($_ShopInfo->getMemname()) {
		$reg_name	= $_ShopInfo->getMemname();
	} else {
		list($reg_name)=pmysql_fetch_array(pmysql_query("select sender_name from tblorderinfo WHERE ordercode='".trim($c_ordercode)."' "));
	}
	$exe_id		= $_ShopInfo->getMemid()."|".$reg_name."|user";	// 실행자 아이디|이름|타입

	if(strlen($c_ordercode)==20 && substr($c_ordercode,-1)!="X") {
		$add_qry	= " AND id = '".$_ShopInfo->getMemid()."' ";
	}

	$in_date		= date("YmdHis");
	if ($c_re_type == '') {			// 취소
		$alert_text	="취소가 접수되었습니다.";
		$redelivery_type	= "N";
	} else if ($c_re_type == 'B') {			// 반품
		$alert_text	="반품이 접수되었습니다.";
		$redelivery_type	= "Y";
	} else if ($c_re_type == 'C') {	// 교환
		$alert_text	="교환이 접수되었습니다.";
		$redelivery_type	= "G";
	}

	$msg = "";
	$msgType = "0";
	//결제완료가 된 주문인지 체크 및 상품들을 가져온다.
	$deliveryCheck_sql = "SELECT a.ordercode, a.oi_step1, a.oi_step2, b.idx, b.redelivery_type, b.op_step FROM tblorderinfo a, tblorderproduct b WHERE a.ordercode=b.ordercode ";
	//$deliveryCheck_sql.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND (a.bank_date IS NOT NULL OR a.bank_date!='')) ";
	//$deliveryCheck_sql.= "OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V','K') AND a.pay_flag='0000')) ";
	if ($c_re_type == '') $deliveryCheck_sql.= "AND (b.op_step ='1' OR b.op_step ='2') ";
	if ($c_re_type == 'B' || $c_re_type == 'C') $deliveryCheck_sql.= "AND (b.op_step ='3' OR b.op_step ='4') ";
	$deliveryCheck_sql.= "AND a.ordercode='".trim($c_ordercode)."' ";
	if ($c_idx) $deliveryCheck_sql.= "AND b.idx='".trim($c_idx)."' ";
	if ($c_idxs) $deliveryCheck_sql.= "AND b.idx IN ('".str_replace("|", "','", trim($c_idxs))."') ";
	$deliveryCheck_sql.= "AND b.op_step < 40 {$add_qry} ";
	$deliveryCheck_res = pmysql_query($deliveryCheck_sql,get_db_conn());
	$deliveryCheck_total	= pmysql_num_rows($deliveryCheck_res);

	
	if ($deliveryCheck_total > 0) {
		$outText = '1번구간(접수) 체크 ========================='.date("Y-m-d H:i:s")."=============================\n";
		$row_cnt	= 0;
		while($deliveryCheck = pmysql_fetch_array($deliveryCheck_res)){
			if($deliveryCheck['redelivery_type'] == "Y"){
				$msg = "반품접수가 되어있는 주문입니다.";
			}else if($deliveryCheck['redelivery_type'] == "G"){
				$msg = "교환접수가 되어있는 주문입니다.";
			}else{
				if ($c_re_type == '') {			// 취소
					$row_cnt++;
					$c_oi_step1	= $deliveryCheck['oi_step1'];
					$c_oi_step2	= $deliveryCheck['oi_step2'];
					$c_op_step		= $deliveryCheck['op_step'];
				} else { // 반품, 교환
					$c_oi_step1	= $deliveryCheck['oi_step1'];
					$c_oi_step2	= $deliveryCheck['oi_step2'];
					$c_op_step		= $deliveryCheck['op_step'];

					$fail_cnt	= 0;
						
					if (strstr("Q", $c_paymethod) && $c_oi_step1 == 3 && $redelivery_type == "Y") {			
						// 상품별 반품이 아니거나 반품접수, 환불접수 상태가 아닌게 있는지 체크한다.
						list($op_rt_cnt)=pmysql_fetch_array(pmysql_query("select count(redelivery_type) as op_rt_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND idx != '".$deliveryCheck['idx']."' AND (redelivery_type != '{$redelivery_type}' or (redelivery_type = '{$redelivery_type}' AND op_step NOT IN ('40', '41', '42'))) "));
						if ($op_rt_cnt == 0) { // 전체가 반품접수, 환불접수 상태일 경우
							//KCP에 정산보류 전달.

							$pgid_info=GetEscrowType($_shopdata->escrow_id);
							$pg_type=$pgid_info["PG"];				
							$pg_type=trim($pg_type);
		
							// 정산보류로 보낸다.
							$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode=".$c_ordercode;
							$hold_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$pg_type."/hold.php",$query);
							$tmpMsgArray = array("type"=>$msgType,"msg"=>$hold_data);
							$hold_data=substr($hold_data,strpos($hold_data,"RESULT=")+7);
							if (substr($hold_data,0,2)!="OK") {
								$tempdata=explode("|",$hold_data);
								$errmsg="정산보류 정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
								if(ord($tempdata[1])) $errmsg=$tempdata[1];
								if(ord($errmsg)) {
									$alert_text = $errmsg;
									$msg = $errmsg;
									$fail_cnt++;
								}
							} else {
								$tempdata=explode("|",$hold_data);
								if(ord($tempdata[1])) $errmsg=$tempdata[1];
								if(ord($errmsg)) {
									//$alert_text = $errmsg;	
								}
							}
							
							if ($fail_cnt == 0) {
								$hold_sql = "UPDATE tblorderinfo SET ";
								$hold_sql.= "deli_gbn		= 'H' WHERE ordercode='{$c_ordercode}' AND deli_gbn != 'C' ";
								if(pmysql_query($hold_sql,get_db_conn())) {
									$hold_sql2 = "UPDATE tblorderproduct SET deli_gbn='H' AND deli_gbn != 'C' ";
									$hold_sql2.= "WHERE ordercode='{$c_ordercode}' ";
									pmysql_query($hold_sql2,get_db_conn());
								}		
							}

						}
					}

					if ($fail_cnt == 0) {

						// 기존에 사용하던 반품신청 필드를 반품또는 교환으로 업데이트 한다.
						$redliveryUpdate_sql = "UPDATE tblorderproduct SET ";
						$redliveryUpdate_sql.= "redelivery_type='{$redelivery_type}' , redelivery_date='".date("YmdHis")."' ";
						$redliveryUpdate_sql.= "WHERE ordercode='".$deliveryCheck['ordercode']."' ";
						$redliveryUpdate_sql.= "AND idx='".$deliveryCheck['idx']."' ";

						pmysql_query($redliveryUpdate_sql,get_db_conn());
						if(pmysql_error()){
							$msg = "접수 실패. 관리자에게 문의해주세요.";
						}else{
							
							// 반품및 교환 신청을 한다.
							orderCancel($exe_id, $c_ordercode, $deliveryCheck['idx'], $c_oi_step1, $c_oi_step2, $c_op_step, $c_paymethod, $c_sel_code, $c_memo, $c_bankcode, $c_bankaccount, $c_bankuser, $c_bankusertel, $c_re_type, $c_opt1_changes, $c_opt2_changes, $c_opt2_pt_changes, $c_opt_text_s_changes, $c_opt_text_c_changes, '', '', '', $c_sel_sub_code, $c_admin_memo, $c_receipt_name, $c_receipt_tel, $c_receipt_mobile, $c_receipt_addr, $c_receipt_post5, $c_rechange_type, $c_return_store_code, $c_return_deli_price, $c_return_deli_receipt, $c_return_deli_type, $c_return_deli_memo );
				
							// 상품별 반품또는 교환신청이 아닌 상태가 있는지 체크한다.
							list($op_rt_cnt)=pmysql_fetch_array(pmysql_query("select count(redelivery_type) as op_rt_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND redelivery_type != '{$redelivery_type}'"));
							if ($op_rt_cnt == 0) { // 전체가 반품또는 교환신청일 경우
								$sql = "UPDATE tblorderinfo SET redelivery_type='{$redelivery_type}', redelivery_date='{$in_date}' WHERE ordercode='".trim($c_ordercode)."' ";
								pmysql_query($sql,get_db_conn());
							}

							$msg = $alert_text;
							$msgType = "1";
						}
					}
				}
			}
		}
	}else{
		$msg = "조건에 맞지 않는 주문입니다. 관리자에게 문의해주세요.";
	}

	if ($c_re_type == '' && $msg == "") {			// 취소
		$outText.= '2번구간(취소) 체크 ========================='.date("Y-m-d H:i:s")."=============================\n";
		$idx_arr	= explode("|", $c_idxs);
		if ($row_cnt == count($idx_arr)) {
			$alert_text	= "";
			$alert_type = '0';
			list($op_idx_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."')"));
			/*
			if ($op_idx_cnt==0) {			
				if ($alert_text == '') {
					#### 에스크로 결제 환불(가상계좌) 또는 취소(신용카드) 
					if(strstr("QP", $c_paymethod)) {
						//Q(가상계좌 매매보호)일 경우엔 우선 환불대기 후 환불되면 자동 취소처리된다.

						if($pg_type=="A") {			#KCP
							$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode=".$c_ordercode;
						} elseif($pg_type=="B") {	#LG데이콤
							$query="mid={$pgid_info["ID"]}&mertkey={$pgid_info["KEY"]}&ordercode=".$c_ordercode;
						} elseif($pg_type=="C") {  #올더게이트
							$query="storeid={$pgid_info["ID"]}&ordercode=".$c_ordercode;
						} elseif($pg_type=="D") {  #이니시스
							$query="sitecd={$pgid_info["EID"]}&ordercode={$ordercode}&curgetid=".$_ShopInfo->getId();
						}
						
						$cancel_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/A/escrow_cancel.php",$query);

						$cancel_data=substr($cancel_data,strpos($cancel_data,"RESULT=")+7);
						if (substr($cancel_data,0,2)!="OK") {
							$tempdata=explode("|",$cancel_data);
							$errmsg="취소처리가 정상 완료 되지 못 했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
							if(ord($tempdata[1])) $errmsg=$tempdata[1];
							$alert_text = $errmsg;
						} else {
							$tempdata=explode("|",$cancel_data);
							if(ord($tempdata[1])) $errmsg=$tempdata[1];
							if(ord($errmsg)) {
								$alert_text = $errmsg;
							}
						}
					}
				}
			}
			*/
			if ($alert_text == '') {
				$outText.= '3번구간(취소신청) 체크 ========================='.date("Y-m-d H:i:s")."=============================\n";
				// 취소 신청을 한다.
				orderCancel($exe_id, $c_ordercode, $c_idxs, $c_oi_step1, $c_oi_step2, $c_op_step, $c_paymethod, $c_sel_code, $c_memo, $c_bankcode, $c_bankaccount, $c_bankuser, $c_bankusertel, $c_re_type, '', '', '', '', '', $c_pgcancel_type, $c_pgcancel_res_code, $c_pgcancel_res_msg, $c_sel_sub_code, $c_admin_memo, $c_receipt_name, $c_receipt_tel, $c_receipt_mobile, $c_receipt_addr, $c_receipt_post5, $c_return_deli_price, $c_return_deli_receipt, $c_return_deli_type, $c_return_deli_memo );

				if (strstr("Q", $c_paymethod) && $c_oi_step1 > 0) {
					$deliupdate =" deli_gbn='E' ";	//환불대기
					$up_deli_gbn="E";
				} elseif (strstr("CP", $c_paymethod)) {
					$deliupdate = " deli_gbn='E' ";
					if ($c_oi_step1 > 0) {
						$deliupdate .= ", pay_admin_proc='E' ";
					}
					$up_deli_gbn="E";
				} else {
					if ($c_oi_step1 == '0') {
						$deliupdate = " deli_gbn='C' ";
						$up_deli_gbn="C";
					} else {
						$deliupdate = " deli_gbn='E' ";
						$up_deli_gbn="E";
					}
				}
				$outText.= '3-1번구간(cacnel입력후) 체크 ========================='.date("Y-m-d H:i:s")."=============================\n";
				list($op_dg_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_dg_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND deli_gbn != '{$up_deli_gbn}'"));
				
				if ($op_dg_cnt == 0) {
					$sql = "UPDATE tblorderinfo SET {$deliupdate} ";
					$sql.= "WHERE ordercode='{$c_ordercode}' ";
					pmysql_query($sql,get_db_conn());
				}

				$sql = "UPDATE tblorderproduct SET deli_gbn='{$up_deli_gbn}' ";
				$sql.= "WHERE ordercode='{$c_ordercode}' AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
				pmysql_query($sql,get_db_conn());
				
				if ($up_deli_gbn == 'C') {
					$alert_text	= "해당 주문을 취소 하였습니다.";
				} else {
					if ($c_oi_step1 > 0 && ((strstr("CVM", $c_paymethod) && $c_pgcancel_type == '1') || strstr("G", $c_paymethod))) { // 카드, 계좌이체, 모바일결제 또는 임직원 포인트일 경우 
						$outText.= '3-2번구간(카드취소) 체크 ========================='.date("Y-m-d H:i:s")."=============================\n";
						//취소건에 대해 불러온다.
						$oc_sql = "SELECT idx, oc_no from tblorderproduct WHERE ordercode='{$c_ordercode}' AND idx IN ('".str_replace("|", "','", $c_idxs)."') order by oc_no";
						$oc_res = pmysql_query($oc_sql,get_db_conn());
						$oc_no_arr	= array();
						while($oc_row = pmysql_fetch_object($oc_res)){	
							if ($oc_no_arr[$oc_row->oc_no]) {
								$oc_no_arr[$oc_row->oc_no]['idx']	= $oc_no_arr[$oc_row->oc_no]['idx']."|".$oc_row->idx;
							} else {
								$oc_no_arr[$oc_row->oc_no]['idx']	= $oc_row->idx;
							}
						}
						pmysql_free_result($oc_res);
						$ErpCancelDate="";
						foreach($oc_no_arr as $oc_no => $oc_val) {
							// PG상태값 업데이트 (카드, 계좌이체) 및 취소처리
							//exdebug($_POST);
							$idxs						= $oc_val['idx'];
							$rfee						= "";
								
							//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
							/*list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND oc_no != '".$oc_no."' AND deli_gbn != 'C' AND op_step != '44'"));			
							if ($op_deli_gbn_cnt == 0) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
								//PG취소가 완료된 수수료를 가져온다.
								list($op_rfee_amt)=pmysql_fetch_array(pmysql_query("select SUM(rfee) as op_rfee_amt from tblorder_cancel WHERE ordercode='".trim($c_ordercode)."' AND pgcancel = 'Y' GROUP BY ordercode"));
								if (($op_rfee_amt + $rfee) == 0) { // 수수료가 없으면 PG결제상태를 취소로 변경한다.
									$sql = "UPDATE tblorderinfo SET pay_admin_proc='C' ";
									$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
									pmysql_query($sql,get_db_conn());
									//exdebug($sql);
								}
							}				*/			

							//환불 금액을 가져온다. - 상품당 금액 가져오는 부분
							list($sum_price)=pmysql_fetch_array(pmysql_query("select SUM( ((price + option_price) * option_quantity) - coupon_price - use_point + deli_price ) AS sum_price from tblorderproduct  WHERE ordercode='{$c_ordercode}' and oc_no='{$oc_no}' group by ordercode"));

							// 최종 환불금액을 계산한다. (실결제금액 - 환불수수료)
							if ($rfee > 0) {
								$rprice	= $sum_price - $rfee;
							} else {
								$rprice	= $sum_price;
							}

							// 취소테이블의 취소상태를 완료로 변경한다.
							$sql   = " UPDATE tblorder_cancel SET pgcancel='Y', rprice = '{$rprice}' ";
							if ($rfee) $sql  .= " , rfee='{$rfee}' ";
							$sql.= "WHERE oc_no='".$oc_no."' ";
							pmysql_query($sql,get_db_conn());
							//exdebug($sql);
							$outText.= '3-2-1번구간(주문 취소/환불) 시작 체크 ========================='.date("Y-m-d H:i:s")."=============================\n";
							orderCancelFin($exe_id, $c_ordercode, $idxs, $oc_no, '', '', '', '', $rfee, '' );
							$outText.= '3-2-1번구간(주문 취소/환불) 완료 체크 ========================='.date("Y-m-d H:i:s")."=============================\n";
							//ERP 통신오류로 맨하단에서 처리함
							//$ErpCancelDate["redelivery"][$idxs]["ordercode"]=$c_ordercode;
							//$ErpCancelDate["redelivery"][$idxs]["oc_no"]=$oc_no;
							//ERP로 환불완료데이터를 보낸다.
							sendErporderCancel($c_ordercode, $oc_no, $idxs);
							$outText.= '3-2-2번구간(erp종료) 완료 체크 ========================='.date("Y-m-d H:i:s")."=============================\n";
							$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
							$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
							$sql.= "AND idx IN ('".str_replace("|", "','", $idxs)."') ";
							//exdebug($sql);

							if(pmysql_query($sql,get_db_conn())) {
								//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
								list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $idxs)."') AND deli_gbn != 'C' AND op_step != '44'"));
								if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
									//PG취소가 완료된 수수료를 가져온다.
									list($op_rfee_amt)=pmysql_fetch_array(pmysql_query("select SUM(rfee) as op_rfee_amt from tblorder_cancel WHERE ordercode='".trim($c_ordercode)."' AND pgcancel = 'Y' GROUP BY ordercode"));
									$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
									if (($op_rfee_amt + $rfee) == 0) { // 수수료가 없으면 PG결제상태를 취소로 변경한다.
										$sql .= ", pay_admin_proc='C' ";
									}
									$sql .= "WHERE ordercode='".trim($c_ordercode)."' ";
									//exdebug($sql);
									pmysql_query($sql,get_db_conn());
								}
							}
							sms_autosend( 'mem_refund', $c_ordercode, $oc_no, '' );
							sms_autosend( 'admin_refund', $c_ordercode, $oc_no, '' );
						}

						$alert_text	= "해당 주문을 취소 하였습니다.";
					} else {
						$alert_text	= "해당 주문을 취소요청 하였습니다.";
					}
				}
				//$alert_text	= $c_ordercode."\n".$c_idxs."\n".$c_oi_step1."\n".$c_oi_step2."\n".$c_op_step."\n".$c_paymethod."\n".$c_sel_code."\n".$c_memo."\n".$c_bankcode."\n".$c_bankaccount."\n".$c_bankuser."\n".$c_bankusertel."\n".$c_re_type;
				$alert_type	= '1';
			}
			$msg = $alert_text;
			$msgType = $alert_type;
		} else {			
			$msg = $row_cnt.".".count($idx_arr)."조건에 맞지 않는 주문입니다. 관리자에게 문의해주세요.";
			$msgType = "0";
		}
	}
	$outText.= '종료 체크 ========================='.date("Y-m-d H:i:s")."=============================\n";
	if(!is_dir($textLogDir)){
		mkdir($textLogDir, 0700);
		chmod($textLogDir, 0777);
	}
	$upQrt_f = fopen($textLogDir.'redelivery_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textLogDir."redelivery_".date("Ymd").".txt",0777);


	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg);
	$msg = json_encode($tmpMsgArray);
	
}else if($_POST['mode']=='receiver_change'){	// 배송지 변경
	$dn_ins					= $_POST['dn_ins'];
	$ordercode				= $_POST['ordercode'];
	$destination_sel		= $_POST['destination_sel'];
	$destination_name	= trim($_POST['destination_name']);
	$get_name				= trim($_POST['get_name']);
	$mobile					= trim($_POST['mobile']);
	$postcode				= $_POST['postcode'];
	$postcode_new		= trim($_POST['postcode_new']);
	$addr1					= trim($_POST['addr1']);
	$addr2					= trim($_POST['addr2']);
	$base_chk				= $_POST['base_chk'];

	$receiver_tel2			= addMobile($mobile);

	$receiver_addr		= "우편번호 : {$postcode_new}
	주소 : {$addr1}  {$addr2}";
	if($dn_ins == "Y"){
		list($dn_cnt)=pmysql_fetch_array(pmysql_query("SELECT count(*) FROM tbldestination WHERE mem_id = '".$_ShopInfo->getMemid()."' AND destination_name='{$destination_name}' "));
	} else {
		$dn_cnt = 0;
	}

	if ($dn_cnt == 0) {
		$sql = "UPDATE tblorderinfo SET receiver_name = '{$get_name}', receiver_tel2='{$receiver_tel2}', receiver_addr='{$receiver_addr}' WHERE ordercode='{$ordercode}' ";

		pmysql_query($sql,get_db_conn());
		if( !pmysql_error() ){		
			if($dn_ins == "Y"){
				#배송지 관리에 등록
				#새로 등록될 배송지가 기본 배송지 일 경우
				if($base_chk == "Y"){
					#기존 기본 배송지로 등록되어 있는 데이터를 N으로 업데이트
					$sql = "UPDATE tbldestination SET  base_chk = 'N' WHERE mem_id = '".$_ShopInfo->getMemid()."'";
					pmysql_query( $sql, get_db_conn());
				}

				$today = date("Y-m-d");
				
				$sql = "INSERT INTO tbldestination (
							mem_id,
							destination_name,
							get_name,
							mobile,
							postcode,
							postcode_new,
							addr1,
							addr2,
							base_chk,
							reg_date
							)values(
							'{$_ShopInfo->getMemid()}',
							'{$destination_name}',
							'{$get_name}',
							'{$mobile}',
							'{$postcode}',
							'{$postcode_new}',
							'{$addr1}',
							'{$addr2}',
							'{$base_chk}',
							'{$today}'
						)";
				pmysql_query($sql,get_db_conn());					
			}
			$msg	= "배송지가 변경되었습니다.";
			$msgType = "1";
		} else {
			$msg = "배송지 변경 실패. 관리자에게 문의해주세요.";
			$msgType = "0";
		}
	} else {
		$msg = "같은 배송지 명이 존재합니다.";
		$msgType = "2";
	}

	

	// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
	$textLogDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/receiver_change_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$outText.= " ordercode     : ".$ordercode."\n";
	$outText.= " destination_sel     : ".$destination_sel."\n";
	$outText.= " destination_name     : ".$destination_name."\n";
	$outText.= " get_name     : ".$get_name."\n";
	$outText.= " mobile     : ".$mobile."\n";
	$outText.= " postcode     : ".$postcode."\n";
	$outText.= " postcode_new     : ".$postcode_new."\n";
	$outText.= " addr1     : ".$addr1."\n";
	$outText.= " addr2     : ".$addr2."\n";
	$outText.= " dn_ins     : ".$dn_ins."\n";
	$outText.= " base_chk     : ".$base_chk."\n";
	$outText.= " msg     : ".$msg."\n";
	$outText.= " msgType     : ".$msgType."\n";
	$outText.= "\n";
	if(!is_dir($textLogDir)){
		mkdir($textLogDir, 0700);
		chmod($textLogDir, 0777);
	}
	$upQrt_f = fopen($textLogDir.'receiver_change_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textLogDir."receiver_change_".date("Ymd").".txt",0777);
	// 로그를 남긴다.-E--------------------------------------------------------------------------------------//


	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg);
	$msg = json_encode($tmpMsgArray);

}else if($_POST['mode']=='ord_req_cancel'){//교환/반품 신청 철회

	$c_ordercode    = $_POST['ordercode'];
	$c_idxs         = $_POST['idxs'];
	$c_oc_no        = $_POST['oc_no'];

	//실행자 이름을 가져온다 (2016.10.07 - 김재수 추가)
	if ($_ShopInfo->getMemname()) {
		$reg_name	= $_ShopInfo->getMemname();
	} else {
		list($reg_name)=pmysql_fetch_array(pmysql_query("select sender_name from tblorderinfo WHERE ordercode='".trim($c_ordercode)."' "));
	}
	$exe_id		= $_ShopInfo->getMemid()."|".$reg_name."|user";	// 실행자 아이디|이름|타입
	
    // 신청철회를 한다.
    orderCancelRestore($exe_id, $c_ordercode, $c_oc_no );

    $msg	="신청철회 되었습니다.";
    $msgType = "1";


	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg);
	$msg = json_encode($tmpMsgArray);
}

echo $msg;

//erp로 데이터전송 //배송완료
/*
if(count($ErpCancelDate["deli_ok"])){
	foreach($ErpCancelDate["deli_ok"] as $idx=>$data){
		//ERP로 환불완료데이터를 보낸다.
		//sendErpOrderEndInfo($data["ordercode"], $idx);
	}
}

//erp로 데이터전송 //취소완료
if(count($ErpCancelDate["redelivery"])){
	foreach($ErpCancelDate["redelivery"] as $idx=>$data){
		//ERP로 환불완료데이터를 보낸다.
//		sendErporderCancel($data["ordercode"], $data["oc_no"], $idx);
	}
}*/
?>
