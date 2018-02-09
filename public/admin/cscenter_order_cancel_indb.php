<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");
include("access.php");

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

$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입


if($_POST['mode']=='receive_cancel'){//주문취소 - 주문접수상태일 경우

	$c_ordercode			= $_POST['ordercode'];
	$c_idxs					= $_POST['idxs'];
	$c_paymethod		= $_POST['paymethod'];

	list($chk_order, $c_oi_step1, $c_oi_step2) = pmysql_fetch("SELECT ordercode, oi_step1, oi_step2, paymethod FROM tblorderinfo WHERE deli_gbn='N' AND (SUBSTR(paymethod,1,1) IN ('B','O','Q') AND (bank_date IS NULL OR bank_date='')) AND ordercode='".$c_ordercode."' ");
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
	$c_re_type					= $_POST['re_type'];
	$c_ordercode				= $_POST['ordercode'];
	$c_idx						= $_POST['idx'];
	$c_idxs						= $_POST['idxs'];
	$c_sel_code				= $_POST['sel_code'];
	$c_sel_sub_code		= $_POST['sel_sub_code'];
	$c_paymethod			= $_POST['paymethod'];
	$c_rechange_type		= $_POST['rechange_type'];
	$c_return_store_code	= $_POST['return_store_code'];
	$c_memo					= pmysql_escape_string(trim($_POST['memo']));
	$c_admin_memo		= pmysql_escape_string(trim($_POST['admin_memo']));
	$c_bankcode				= $_POST['bankcode'];
	$c_bankaccount			= $_POST['bankaccount'];
	$c_bankuser				= $_POST['bankuser'];
	$c_bankusertel			= $_POST['bankusertel'];
	$c_opt1_changes		= $_POST['opt1_changes'];
	$c_opt2_changes		= $_POST['opt2_changes'];
	$c_opt2_pt_changes		= $_POST['opt2_pt_changes'];
	$c_opt_text_s_changes	= $_POST['opt_text_s_changes'];
	$c_opt_text_c_changes	= $_POST['opt_text_c_changes'];
	$c_pgcancel_type			= $_POST['pgcancel_type'];
	$c_pgcancel_res_code	= $_POST['pgcancel_res_code'];
	$c_pgcancel_res_msg		= $_POST['pgcancel_res_msg'];

	$c_receipt_name				= $_POST['receipt_name'];
	$c_receipt_tel					= $_POST['receipt_tel'];
	$c_receipt_mobile			= $_POST['receipt_mobile'];
	$c_receipt_addr				= $_POST['receipt_addr'];
	$c_receipt_post5				= $_POST['receipt_post5'];

	$c_return_deli_price			= $_POST['return_deli_price']?$_POST['return_deli_price']:"0";
	$c_return_deli_receipt		= pmysql_escape_string(trim($_POST['return_deli_receipt']));
	$c_return_deli_type			= $_POST['return_deli_type'];
	$c_return_deli_memo		= pmysql_escape_string(trim($_POST['return_deli_memo']));
	$c_cs_memo					= str_replace("'", "''", $_POST['cs_memo']);
	if ($c_cs_memo =='<br>') $c_cs_memo					="";
	//exdebug($c_cs_memo);
	//exit;

	//경로
	$filepath = $Dir.DataDir."shopimages/cscenter/";
	//파일
	$asfile = new FILE($filepath);
	$up_asfile = "";
	if ($c_cs_memo !='') {
		$up_asfile = $asfile->upFiles();
	}

	//exdebug($up_asfile);

	//exit;

	$in_date		= date("YmdHis");
	if ($c_re_type == '') {			// 취소
		$alert_text	="취소가 접수되었습니다.";
		$redelivery_type	= "N";
		$req_step_code	= "refund_0";
		$req_step_name	= "취소신청";
	} else if ($c_re_type == 'B') {			// 반품
		$alert_text	="반품이 접수되었습니다.";
		$redelivery_type	= "Y";
		$req_step_code	= "regoods_0";
		$req_step_name	= "반품신청";
	} else if ($c_re_type == 'C') {	// 교환
		$alert_text	="교환이 접수되었습니다.";
		$redelivery_type	= "G";
		$req_step_code	= "rechange_0";
		$req_step_name	= "교환신청";
	}

	$msg = "";
	$msgType = "0";
	$return_no = "";
	//결제완료가 된 주문인지 체크 및 상품들을 가져온다.
	$deliveryCheck_sql = "SELECT a.ordercode, a.oi_step1, a.oi_step2, b.idx, b.redelivery_type, b.op_step FROM tblorderinfo a, tblorderproduct b WHERE a.ordercode=b.ordercode ";
	//$deliveryCheck_sql.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND (a.bank_date IS NOT NULL OR a.bank_date!='')) ";
	//$deliveryCheck_sql.= "OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V','K') AND a.pay_flag='0000')) ";
	if ($c_re_type == '') $deliveryCheck_sql.= "AND (b.op_step ='1' OR b.op_step ='2') ";
	if ($c_re_type == 'B' || $c_re_type == 'C') $deliveryCheck_sql.= "AND (b.op_step ='3' OR b.op_step ='4') ";
	$deliveryCheck_sql.= "AND a.ordercode='".trim($c_ordercode)."' ";
	if ($c_idx && !$c_idxs) $deliveryCheck_sql.= "AND b.idx='".trim($c_idx)."' ";
	if ($c_idxs) $deliveryCheck_sql.= "AND b.idx IN ('".str_replace("|", "','", trim($c_idxs))."') ";
	$deliveryCheck_sql.= "AND b.op_step < 40 {$add_qry} ";
	$deliveryCheck_res = pmysql_query($deliveryCheck_sql,get_db_conn());
	$deliveryCheck_total	= pmysql_num_rows($deliveryCheck_res);


	if ($deliveryCheck_total > 0) {
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
						
					/*if (strstr("Q", $c_paymethod) && ($c_oi_step1 == 2 || $c_oi_step1 == 3) && $redelivery_type == "Y") {			
						// 상품별 반품이 아니거나 반품접수, 환불접수 상태가 아닌게 있는지 체크한다.
						list($op_rt_cnt)=pmysql_fetch_array(pmysql_query("select count(redelivery_type) as op_rt_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND idx != '".$deliveryCheck['idx']."' AND (redelivery_type != '{$redelivery_type}' or (redelivery_type = '{$redelivery_type}' AND op_step NOT IN ('41', '42'))) "));
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
					}*/

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
							$return_oc_no	= orderCancel($exe_id, $c_ordercode, $deliveryCheck['idx'], $c_oi_step1, $c_oi_step2, $c_op_step, $c_paymethod, $c_sel_code, $c_memo, $c_bankcode, $c_bankaccount, $c_bankuser, $c_bankusertel, $c_re_type, $c_opt1_changes, $c_opt2_changes, $c_opt2_pt_changes, $c_opt_text_s_changes, $c_opt_text_c_changes, '', '', '', $c_sel_sub_code, $c_admin_memo, $c_receipt_name, $c_receipt_tel, $c_receipt_mobile, $c_receipt_addr, $c_receipt_post5, $c_rechange_type, $c_return_store_code, $c_return_deli_price, $c_return_deli_receipt, $c_return_deli_type, $c_return_deli_memo );
				
							// 상품별 반품또는 교환신청이 아닌 상태가 있는지 체크한다.
							list($op_rt_cnt)=pmysql_fetch_array(pmysql_query("select count(redelivery_type) as op_rt_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND redelivery_type != '{$redelivery_type}'"));
							if ($op_rt_cnt == 0) { // 전체가 반품또는 교환신청일 경우
								$sql = "UPDATE tblorderinfo SET redelivery_type='{$redelivery_type}', redelivery_date='{$in_date}' WHERE ordercode='".trim($c_ordercode)."' ";
								pmysql_query($sql,get_db_conn());
							}

							if ($c_cs_memo !='' && $return_oc_no !='') {
								//cs메모
								$memo_sql="insert into tblcscentermemo (receipt_no, cs_memo, step_code, step_name, admin_id, admin_name, regdt, route_type) values ('".$return_oc_no."', '".$c_cs_memo."', '".$req_step_code."', '".$req_step_name."', '".$_ShopInfo->getId()."', '".$_ShopInfo->getName()."', '".date("YmdHis")."', 'csadmin') RETURNING no";
								$memo_idx = pmysql_fetch_object(pmysql_query( $memo_sql, get_db_conn() ));
								
								//파일첨부
								if($up_asfile["file"][0]["v_file"]){
									foreach($up_asfile as $fa=>$fav){
										foreach($fav as $na=>$nav){
											$file_sql="insert into tblcscenterfile (receipt_no, memo_no, filename, filename_ori, filewrite_id, regdt, route_type) values ('".$return_oc_no."', '".$memo_idx->no."', '".$nav[v_file]."', '".$nav[r_file]."', '".$_ShopInfo->getId()."', '".date("YmdHis")."', 'csadmin')";
											pmysql_query($file_sql);
										}
									}
								}
							}

							$msg = $alert_text;
							$msgType = "1";
							$return_no = $return_oc_no;
						}
					}
				}
			}
		}
	}else{
		$msg = "조건에 맞지 않는 주문입니다. 관리자에게 문의해주세요.";
	}

	if ($c_re_type == '' && $msg == "") {			// 취소
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
				// 취소 신청을 한다.
				$return_oc_no	= orderCancel($exe_id, $c_ordercode, $c_idxs, $c_oi_step1, $c_oi_step2, $c_op_step, $c_paymethod, $c_sel_code, $c_memo, $c_bankcode, $c_bankaccount, $c_bankuser, $c_bankusertel, $c_re_type, '', '', '', '', '', $c_pgcancel_type, $c_pgcancel_res_code, $c_pgcancel_res_msg, $c_sel_sub_code, $c_admin_memo, $c_receipt_name, $c_receipt_tel, $c_receipt_mobile, $c_receipt_addr, $c_receipt_post5, $c_return_deli_price, $c_return_deli_receipt, $c_return_deli_type, $c_return_deli_memo );

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
					if ($c_oi_step1 > 0 && ((strstr("CV", $c_paymethod) && $c_pgcancel_type == '1') || strstr("G", $c_paymethod))) { // 카드, 계좌이체 또는 임직원 포인트일 경우 
						
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

						foreach($oc_no_arr as $oc_no => $oc_val) {
							// PG상태값 업데이트 (카드, 계좌이체) 및 취소처리
							//exdebug($_POST);
							$idxs						= $oc_val['idx'];
							$rfee						= "";
								
							//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
							list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND oc_no != '".$oc_no."' AND deli_gbn != 'C' AND op_step != '44'"));			
							if ($op_deli_gbn_cnt == 0) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
								//PG취소가 완료된 수수료를 가져온다.
								list($op_rfee_amt)=pmysql_fetch_array(pmysql_query("select SUM(rfee) as op_rfee_amt from tblorder_cancel WHERE ordercode='".trim($c_ordercode)."' AND pgcancel = 'Y' GROUP BY ordercode"));
								if ($op_rfee_amt == 0) { // 수수료가 없으면 PG결제상태를 취소로 변경한다.
									$sql = "UPDATE tblorderinfo SET pay_admin_proc='C' ";
									$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
									pmysql_query($sql,get_db_conn());
									//exdebug($sql);
								}
							}

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

							orderCancelFin($exe_id, $c_ordercode, $idxs, $oc_no, '', '', '', '', $rfee, '' );
				
							//ERP로 환불완료데이터를 보낸다.
							sendErporderCancel($c_ordercode, $oc_no, $idxs);
							
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

							$return_oc_no	= $oc_no;
							$req_step_code	= "refund_1";
							$req_step_name	= "취소완료";

							if ($c_cs_memo !='' && $return_oc_no !='') {
								//cs메모
								$memo_sql="insert into tblcscentermemo (receipt_no, cs_memo, step_code, step_name, admin_id, admin_name, regdt, route_type) values ('".$return_oc_no."', '".$c_cs_memo."', '".$req_step_code."', '".$req_step_name."', '".$_ShopInfo->getId()."', '".$_ShopInfo->getName()."', '".date("YmdHis")."', 'csadmin') RETURNING no";
								$memo_idx = pmysql_fetch_object(pmysql_query( $memo_sql, get_db_conn() ));
								
								//파일첨부
								if($up_asfile["file"][0]["v_file"]){
									foreach($up_asfile as $fa=>$fav){
										foreach($fav as $na=>$nav){
											$file_sql="insert into tblcscenterfile (receipt_no, memo_no, filename, filename_ori, filewrite_id, regdt, route_type) values ('".$return_oc_no."', '".$memo_idx->no."', '".$nav[v_file]."', '".$nav[r_file]."', '".$_ShopInfo->getId()."', '".date("YmdHis")."', 'csadmin')";
											pmysql_query($file_sql);
										}
									}
								}
							}

							sms_autosend( 'mem_refund', $c_ordercode, $oc_no, '' );
							sms_autosend( 'admin_refund', $c_ordercode, $oc_no, '' );
						}

						$alert_text	= "해당 주문을 취소 하였습니다.";
					} else {

						if ($c_cs_memo !='' && $return_oc_no !='') {
							//cs메모
							$memo_sql="insert into tblcscentermemo (receipt_no, cs_memo, step_code, step_name, admin_id, admin_name, regdt, route_type) values ('".$return_oc_no."', '".$c_cs_memo."', '".$req_step_code."', '".$req_step_name."', '".$_ShopInfo->getId()."', '".$_ShopInfo->getName()."', '".date("YmdHis")."', 'csadmin') RETURNING no";
							$memo_idx = pmysql_fetch_object(pmysql_query( $memo_sql, get_db_conn() ));
							
							//파일첨부
							if($up_asfile["file"][0]["v_file"]){
								foreach($up_asfile as $fa=>$fav){
									foreach($fav as $na=>$nav){
										$file_sql="insert into tblcscenterfile (receipt_no, memo_no, filename, filename_ori, filewrite_id, regdt, route_type) values ('".$return_oc_no."', '".$memo_idx->no."', '".$nav[v_file]."', '".$nav[r_file]."', '".$_ShopInfo->getId()."', '".date("YmdHis")."', 'csadmin')";
										pmysql_query($file_sql);
									}
								}
							}
						}

						$alert_text	= "해당 주문을 취소요청 하였습니다.";
					}
				}
				//$alert_text	= $c_ordercode."\n".$c_idxs."\n".$c_oi_step1."\n".$c_oi_step2."\n".$c_op_step."\n".$c_paymethod."\n".$c_sel_code."\n".$c_memo."\n".$c_bankcode."\n".$c_bankaccount."\n".$c_bankuser."\n".$c_bankusertel."\n".$c_re_type;
				$alert_type	= '1';
				$return_no = $return_oc_no;
			}
			$msg			= $alert_text;
			$msgType = $alert_type;
		} else {			
			$msg = $row_cnt.".".count($idx_arr)."조건에 맞지 않는 주문입니다. 관리자에게 문의해주세요.";
			$msgType = "0";
		}
	}

	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg,"oc_no"=>$return_no);
	$msg = json_encode($tmpMsgArray);
	
}else if($_POST['mode']=='oc_step_process'){
	$c_re_type					= $_POST['re_type'];
	$c_oc_no					= $_POST['oc_no'];
	$c_n_oc_step				= $_POST['n_oc_step'];
	$c_oc_step					= $_POST['oc_step'];
	$c_ordercode				= $_POST['ordercode'];
	$c_idx						= $_POST['idx'];
	$c_idxs						= $_POST['idxs'];
	$c_sel_code				= $_POST['sel_code'];
	$c_sel_sub_code		= $_POST['sel_sub_code'];
	$c_paymethod			= $_POST['paymethod'];
	$c_rechange_type		= $_POST['rechange_type'];
	$c_return_store_code	= $_POST['return_store_code'];
	$c_memo					= pmysql_escape_string(trim($_POST['memo']));
	$c_admin_memo		= pmysql_escape_string(trim($_POST['admin_memo']));
	$c_bankcode				= $_POST['bankcode'];
	$c_bankaccount			= $_POST['bankaccount'];
	$c_bankuser				= $_POST['bankuser'];
	$c_bankusertel			= $_POST['bankusertel'];
	$c_opt1_changes		= $_POST['opt1_changes'];
	$c_opt2_changes		= $_POST['opt2_changes'];
	$c_opt2_pt_changes		= $_POST['opt2_pt_changes'];
	$c_opt_text_s_changes	= $_POST['opt_text_s_changes'];
	$c_opt_text_c_changes	= $_POST['opt_text_c_changes'];
	$c_pgcancel_type			= $_POST['pgcancel_type'];
	$c_pgcancel_res_code	= $_POST['pgcancel_res_code'];
	$c_pgcancel_res_msg		= $_POST['pgcancel_res_msg'];

	$c_return_deli_price			= $_POST['return_deli_price']?$_POST['return_deli_price']:"0";
	$c_return_deli_receipt		= pmysql_escape_string(trim($_POST['return_deli_receipt']));
	$c_return_deli_type			= $_POST['return_deli_type'];
	$c_return_deli_memo		= pmysql_escape_string(trim($_POST['return_deli_memo']));

	$c_return_store_deli_com	= $_POST['return_store_deli_com'];
	$c_return_store_deli_num	= $_POST['return_store_deli_num'];

	$c_cs_memo					= str_replace("'", "''", $_POST['cs_memo']);
	if ($c_cs_memo =='<br>') $c_cs_memo					="";
	//exdebug($c_cs_memo);
	//exit;

	//경로
	$filepath = $Dir.DataDir."shopimages/cscenter/";
	//파일
	$asfile = new FILE($filepath);
	$up_asfile = "";
	if ($c_cs_memo !='') {
		$up_asfile = $asfile->upFiles();
	}

	//exdebug($up_asfile);

	//exit;

	$in_date		= date("YmdHis");
	if ($c_re_type == '') {			// 취소
		$msg	="취소가 완료되었습니다.";
		$req_step_code	= "refund_".$c_oc_step;
		$req_step_name	= "취소신청";
	} else if ($c_re_type == 'B' || $c_re_type == 'C') {			// 반품// 교환
		$c_re_type_name	= $c_re_type=='B'?"반품":"교환";
		$c_re_type_code		= $c_re_type=='B'?"regoods":"rechange";
		
		$req_step_code	= $c_re_type_code."_".$c_oc_step;
		if ($c_oc_step == '1') {
			$msg			= $c_re_type_name."이 접수되었습니다.";
			$req_step_name	= $c_re_type_name."접수";
		} else if ($c_oc_step == '4') {
			$msg			= $c_re_type_name."이 완료되었습니다.";
			$req_step_name	= $c_re_type_name."완료";
		}
	}

	$msg = "";
	$msgType = "0";
	
	list($now_oc_step)=pmysql_fetch_array(pmysql_query("select oc_step from tblorder_cancel WHERE oc_no='{$c_oc_no}' "));

	if ($c_n_oc_step != $now_oc_step) {
		$msg = "이미 진행상태가 변경되었습니다.";
	}
	
	if ($msg == "") {
		if ($c_re_type == '') {			// 취소
			if ($c_n_oc_step != $c_oc_step && $c_oc_step == '4') { // 완료
				orderCancelFin($exe_id, $c_ordercode, $c_idxs, $c_oc_no, '', $c_bankcode, $c_bankaccount, $c_bankuser, '', '' );

				// 결제 취소 환불
				sendErporderCancel($c_ordercode, $c_oc_no, $c_idxs);

				$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
				$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
				$sql.= "AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
				//echo $sql;

				if(pmysql_query($sql,get_db_conn())) {
					//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
					list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND deli_gbn != 'C' AND op_step != '44'"));
					if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
						$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
						$sql .= ", pay_admin_proc='C' ";
						$sql .= "WHERE ordercode='".trim($c_ordercode)."' ";
						//echo $sql;
						pmysql_query($sql,get_db_conn());
					}
				}
				sms_autosend( 'mem_refund', $c_ordercode, $c_oc_no, '' );
				sms_autosend( 'admin_refund', $c_ordercode, $c_oc_no, '' );
				$msg	="취소 되었습니다.";
				$msgType	= '1';
			} else {
				$msg	="저장 되었습니다.";
				$msgType	= '1';
			}
		} else if ($c_re_type == 'B') {			// 반품
			if ($c_n_oc_step != $c_oc_step) {
				if ($c_oc_step == '1') { //접수
					orderCancelAccept($exe_id, $c_ordercode, $c_idxs, $c_oc_no );

					sms_autosend( 'mem_cancel', $c_ordercode, $c_oc_no, '' );
					sms_autosend( 'admin_cancel', $c_ordercode, $c_oc_no, '' );
					$msg	="반품접수 되었습니다.";
				} else if ($c_oc_step == '2') { //제품도착
					orderCancelGetPickup($exe_id, $c_ordercode, $c_idxs, $c_oc_no );
					$msg	="제품도착 되었습니다.";
				} else if ($c_oc_step == '3') { //승인
					orderCancelPickupFin($exe_id, $c_ordercode, $c_idxs, $c_oc_no );

					$sql = "UPDATE tblorderproduct SET deli_gbn='E' ";
					$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
					$sql.= "AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
					//echo $sql;

					if(pmysql_query($sql,get_db_conn())) {
						// 반송완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
						list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND deli_gbn != 'E' AND op_step != '44'"));
						if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 반송상태일 경우
							$sql = "UPDATE tblorderinfo SET deli_gbn='E' WHERE ordercode='".trim($c_ordercode)."' ";
							//echo $sql;
							pmysql_query($sql,get_db_conn());
						}
					}
					$msg	="반품승인 되었습니다.";
				} else if ($c_oc_step == '4') { //완료
					if (((strstr("CV", $c_paymethod) && $c_pgcancel_type == '1') || strstr("G", $c_paymethod))) { // 카드, 계좌이체 또는 임직원 포인트일 경우 
						
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

						foreach($oc_no_arr as $oc_no => $oc_val) {
							// PG상태값 업데이트 (카드, 계좌이체) 및 취소처리
							//exdebug($_POST);
							$idxs						= $oc_val['idx'];
							$rfee						= "";
								
							//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
							list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."' AND oc_no != '".$oc_no."' AND deli_gbn != 'C' AND op_step != '44'"));			
							if ($op_deli_gbn_cnt == 0) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
								//PG취소가 완료된 수수료를 가져온다.
								list($op_rfee_amt)=pmysql_fetch_array(pmysql_query("select SUM(rfee) as op_rfee_amt from tblorder_cancel WHERE ordercode='".trim($c_ordercode)."' AND pgcancel = 'Y' GROUP BY ordercode"));
								if ($op_rfee_amt == 0) { // 수수료가 없으면 PG결제상태를 취소로 변경한다.
									$sql = "UPDATE tblorderinfo SET pay_admin_proc='C' ";
									$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
									pmysql_query($sql,get_db_conn());
									//exdebug($sql);
								}
							}

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

							orderCancelFin($exe_id, $c_ordercode, $idxs, $oc_no, '', '', '', '', $rfee, '' );
				
							//ERP로 환불완료데이터를 보낸다.
							sendErporderReturn($c_ordercode, $oc_no, $idxs);
							
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
						
					} else {

						orderCancelFin($exe_id, $c_ordercode, $c_idxs, $c_oc_no, '', $c_bankcode, $c_bankaccount, $c_bankuser, '', '' );

						// 반품 환불
						sendErporderReturn($c_ordercode, $c_oc_no, $c_idxs);

						$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
						$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
						$sql.= "AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
						//echo $sql;

						if(pmysql_query($sql,get_db_conn())) {
							//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
							list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND deli_gbn != 'C' AND op_step != '44'"));
							if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
								$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
								$sql .= ", pay_admin_proc='C' ";
								$sql .= "WHERE ordercode='".trim($c_ordercode)."' ";
								//echo $sql;
								pmysql_query($sql,get_db_conn());
							}
						}
						sms_autosend( 'mem_refund', $c_ordercode, $c_oc_no, '' );
						sms_autosend( 'admin_refund', $c_ordercode, $c_oc_no, '' );			
					}
					$msg	="반품완료 되었습니다.";

				} else if ($c_oc_step == '5') { //보류
					orderCancelHold($exe_id, $c_ordercode, $c_idxs, $c_oc_no );
					$msg	="반품보류 되었습니다.";

				} else if ($c_oc_step == '6') { //철회
					orderCancelRestore($exe_id, $c_ordercode, $c_oc_no );		
					$msg	="반품철회 되었습니다.";	

				} else if ($c_oc_step == '7') { //교환요청
					orderCancelChange($exe_id, $c_ordercode, $c_idxs, $c_oc_no, 'G' );		
					$msg	="교환요청 되었습니다.";	
				}
				$msgType	= '1';
			} else {
				$msg	="저장 되었습니다.";
				$msgType	= '1';
			}
		
		} else if ($c_re_type == 'C') {			// 교환

			if ($c_oc_step != '6' && $c_oc_step != '7') { // 철회, 반품요청이 아닐경우
				$sql = "UPDATE tblorderproduct SET ";

				if ($opt1_change) { // 교환상품이 있을경우 자체코드를 불러온다
					list($self_goods_code_change)=pmysql_fetch_array(pmysql_query("SELECT b.self_goods_code FROM tblorderproduct a LEFT JOIN tblproduct_option b ON a.productcode=b.productcode AND b.option_code='{$opt2_change}' where  a.ordercode='".trim($c_ordercode)."' AND a.idx='{$c_idxs}' "));
					$sql	.= " self_goods_code_change='{$self_goods_code_change}' ";
				} else {
					$sql	.= " self_goods_code_change=self_goods_code ";
				}

				if ($c_opt1_changes) $sql	.= ", opt1_change='{$c_opt1_changes}' ";
				if ($c_opt2_changes) $sql	.= ", opt2_change='{$c_opt2_changes}' ";

				if ($c_opt2_pt_changes) $sql	.= ", option_price_text_change='{$c_opt2_pt_changes}' ";
				if ($c_opt_text_s_changes) $sql	.= ", text_opt_subject_change='{$c_opt_text_s_changes}' ";
				if ($c_opt_text_c_changes) $sql	.= ", text_opt_content_change='{$c_opt_text_c_changes}' ";
				$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
				$sql.= "AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
				 //echo $sql;
				pmysql_query($sql,get_db_conn());				

				// 옵션 변경시 로그 (2016.12.07 - 김재수 추가)
				// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
				$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/cancel_logs_'.date("Ym").'/';

				$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
				$outText.= " ordercode     : ".$c_ordercode."\n";
				$outText.= " idxs     : ".$c_idxs."\n";
				$outText.= " c_oc_step     : ".$c_oc_step."\n";
				$outText.= " c_n_oc_step     : ".$c_n_oc_step."\n";
				$outText.= " c_opt1_changes     : ".$c_opt1_changes."\n";
				$outText.= " c_opt2_changes     : ".$c_opt2_changes."\n";
				$outText.= " c_opt2_pt_changes     : ".$c_opt2_pt_changes."\n";
				$outText.= " c_opt_text_s_changes     : ".$c_opt_text_s_changes."\n";
				$outText.= " c_opt_text_c_changes     : ".$c_opt_text_c_changes."\n";
				$outText.= " c_oc_no     : ".$c_oc_no."\n";
				$outText.= " exe_id     : ".$exe_id."\n";
				$outText.= "\n";
				if(!is_dir($textDir)){
					mkdir($textDir, 0700);
					chmod($textDir, 0777);
				}
				$upQrt_f = fopen($textDir.'cancel_ordercancel_option_change_'.date("Ymd").'.txt','a');
				fwrite($upQrt_f, $outText );
				fclose($upQrt_f);
				chmod($textDir."cancel_ordercancel_option_change_".date("Ymd").".txt",0777);
				// 로그를 남긴다.-E--------------------------------------------------------------------------------------//
			}	

			if ($c_n_oc_step != $c_oc_step) {
				if ($c_oc_step == '1') { //접수
					orderCancelAccept($exe_id, $c_ordercode, $c_idxs, $c_oc_no );

					sms_autosend( 'mem_cancel', $c_ordercode, $c_oc_no, '' );
					sms_autosend( 'admin_cancel', $c_ordercode, $c_oc_no, '' );
					$msg	="교환접수 되었습니다.";
				} else if ($c_oc_step == '2') { //제품도착
					orderCancelGetPickup($exe_id, $c_ordercode, $c_idxs, $c_oc_no );
					$msg	="제품도착 되었습니다.";
				} else if ($c_oc_step == '3') { //승인
					orderCancelReorderPickupFin($exe_id, $c_ordercode, $c_idxs, $c_oc_no );
					$msg	="교환승인 되었습니다.";
				} else if ($c_oc_step == '4') { //완료
					orderCancelReorderFin($exe_id, $c_ordercode, $c_idxs, $c_oc_no);

					$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
					$sql.= "WHERE ordercode='".trim($c_ordercode)."' ";
					$sql.= "AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
					//echo $sql;

					if(pmysql_query($sql,get_db_conn())) {
						//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
						list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($c_ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND deli_gbn != 'C' AND op_step != '44'"));
						if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
							$sql = "UPDATE tblorderinfo SET deli_gbn='C' WHERE ordercode='".trim($c_ordercode)."' ";
							//echo $sql;
							pmysql_query($sql,get_db_conn());
						}
					}
					sms_autosend( 'mem_refund', $c_ordercode, $c_oc_no, '' );
					sms_autosend( 'admin_refund', $c_ordercode, $c_oc_no, '' );		
					$msg	="교환완료 되었습니다.";

				} else if ($c_oc_step == '5') { //보류
					orderCancelHold($exe_id, $c_ordercode, $c_idxs, $c_oc_no );		
					$msg	="교환보류 되었습니다.";	

				} else if ($c_oc_step == '6') { //철회
					orderCancelRestore($exe_id, $c_ordercode, $c_oc_no );		
					$msg	="교환철회 되었습니다.";

				} else if ($c_oc_step == '7') { //반품요청
					orderCancelChange($exe_id, $c_ordercode, $c_idxs, $c_oc_no, 'Y' );		
					$msg	="반품요청 되었습니다.";	
				}
				$msgType	= '1';
			} else {
				$msg	="저장 되었습니다.";
				$msgType	= '1';
			}	
		}
	}

	if ($msgType == "1") {

		$sql   = " UPDATE tblorder_cancel SET ";
		if ($c_oc_step == '7') { // 반품/교환 전환요청			
			if ($c_re_type == 'C') {					// 교환 -> 반품
				$c_rechange_type	='0';

				if ($c_sel_code=='11') $c_sel_code	= 4;
				if ($c_sel_code=='12') $c_sel_code	= 5;
				if ($c_sel_code=='13') $c_sel_code	= 6;
				if ($c_sel_code=='14') $c_sel_code	= 10;

			} else if ($c_re_type == 'B') {			// 반품 -> 교환
				$c_rechange_type	='1';

				if ($c_sel_code=='4') $c_sel_code	= 11;
				if ($c_sel_code=='5') $c_sel_code	= 12;
				if ($c_sel_code=='6') $c_sel_code	= 13;
				if ($c_sel_code=='7') {
					$c_sel_code			= 14;
					$c_sel_sub_code	= '0';
				}
				if ($c_sel_code=='8') $c_sel_code	= 14;
				if ($c_sel_code=='9') $c_sel_code	= 14;
				if ($c_sel_code=='10') $c_sel_code	= 14;
			}	
		}

		if ($c_sel_code) $sql  .= " code = '{$c_sel_code}' ";
		if ($c_sel_sub_code) $sql  .= " , sub_code = '{$c_sel_sub_code}' ";
		if ($c_rechange_type) $sql  .= " , rechange_type='{$c_rechange_type}' ";

		if ($c_memo) $sql  .= " , memo = '{$c_memo}' ";
		if ($c_admin_memo) $sql  .= " , admin_memo='{$c_admin_memo}' ";
		if ($c_bankaccount) $sql  .= " , bankcode='{$c_bankcode}', bankaccount='{$c_bankaccount}', bankuser='{$c_bankuser}' ";
		if ($c_pgcancel_res_code) $sql  .= " , pgcancel_res_code='{$c_pgcancel_res_code}' ";
		if ($c_pgcancel_res_msg) $sql  .= " , pgcancel_res_msg='{$c_pgcancel_res_msg}' ";
		if ($c_return_store_code) $sql  .= " , return_store_code='{$c_return_store_code}' ";
		if ($c_return_deli_receipt) $sql  .= " , return_deli_receipt='{$c_return_deli_receipt}' ";
		if ($c_return_deli_type) $sql  .= " , return_deli_type='{$c_return_deli_type}' ";
		if ($c_return_store_deli_com) $sql  .= " , return_store_deli_com='{$c_return_store_deli_com}' ";
		if ($c_return_store_deli_num) $sql  .= " , return_store_deli_num='{$c_return_store_deli_num}' ";
		if ($c_return_deli_memo) $sql  .= " , return_deli_memo='{$c_return_deli_memo}' ";

		$sql  .= " , return_deli_price='{$c_return_deli_price}' ";
		$sql  .= " WHERE oc_no='".trim($c_oc_no)."' ";
		 //echo $sql;
		pmysql_query($sql,get_db_conn());

		if ($c_cs_memo !='') {
			//cs메모
			$memo_sql="insert into tblcscentermemo (receipt_no, cs_memo, step_code, step_name, admin_id, admin_name, regdt, route_type) values ('".$c_oc_no."', '".$c_cs_memo."', '".$req_step_code."', '".$req_step_name."', '".$_ShopInfo->getId()."', '".$_ShopInfo->getName()."', '".date("YmdHis")."', 'csadmin') RETURNING no";
			$memo_idx = pmysql_fetch_object(pmysql_query( $memo_sql, get_db_conn() ));
			
			//파일첨부
			if($up_asfile["file"][0]["v_file"]){
				foreach($up_asfile as $fa=>$fav){
					foreach($fav as $na=>$nav){
						$file_sql="insert into tblcscenterfile (receipt_no, memo_no, filename, filename_ori, filewrite_id, regdt, route_type) values ('".$c_oc_no."', '".$memo_idx->no."', '".$nav[v_file]."', '".$nav[r_file]."', '".$_ShopInfo->getId()."', '".date("YmdHis")."', 'csadmin')";
						pmysql_query($file_sql);
					}
				}
			}
		}
	}

	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg);
	$msg = json_encode($tmpMsgArray);
	
} else if($_POST['mode']=="log_save"){//처리이력 수정
	$receipt_no=$_POST["receipt_no"];
	$logno=explode(',',$_POST["logno"]);
	$logtime_h=explode(',',$_POST["logtime_h"]);
	$logtime_i=explode(',',$_POST["logtime_i"]);
	$logtime_s=explode(',',$_POST["logtime_s"]);
	$logday=explode(',',$_POST["logday"]);
	for($i=0;$i<count($logno);$i++){
		$regdt=str_replace("-","",$logday[$i]).$logtime_h[$i].$logtime_i[$i].$logtime_s[$i];
		pmysql_query("update tblorder_status_log set moddt='".$regdt."' where osl_no='".$logno[$i]."' ");
		//exdebug("update tblorder_status_log set moddt='".$regdt."' where osl_no='".$logno[$i]."'\n");
		
	}
	$msg			= "처리이력이 저장되었습니다.";
	$msgType	= '1';
	$html			= '';

	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg,"html"=>$html);
	$msg = json_encode($tmpMsgArray);

}else if($_POST['mode']=="consult_can_save"){// 상담가능한연락처 수정
	$receipt_no	= $_POST["receipt_no"];
	$cc_name		= $_POST["cc_name"];
	$cc_tel			= $_POST["cc_tel"];
	$cc_mobile	= $_POST["cc_mobile"];

	pmysql_query("
		UPDATE tblorder_cancel SET 
		consult_can_name='".$cc_name."', 
		consult_can_tel='".$cc_tel."', 
		consult_can_mobile='".$cc_mobile."' 
		WHERE oc_no='".$receipt_no."'
	");

	$msg			= "상담 가능한 연락처가 수정되었습니다.";
	$msgType	= '1';
	$html			= '';

	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg,"html"=>$html);
	$msg = json_encode($tmpMsgArray);

}else if($_POST['mode']=="receipt_save"){// 수령지 변경
	$receipt_no		= $_POST["receipt_no"];
	$receipt_name	= $_POST["receipt_name"];
	$receipt_tel			= $_POST["receipt_tel"];
	$receipt_mobile	= $_POST["receipt_mobile"];
	$receipt_addr		= $_POST["receipt_addr"];
	$receipt_post5	= $_POST["receipt_post5"];

	pmysql_query("
		UPDATE tblorder_cancel SET 
		receipt_name='".$receipt_name."', 
		receipt_tel='".$receipt_tel."', 
		receipt_mobile='".$receipt_mobile."', 
		receipt_addr='".$receipt_addr."', 
		receipt_post5='".$receipt_post5."' 
		WHERE oc_no='".$receipt_no."'
	");

	$msg			= "수령지가 수정되었습니다.";
	$msgType	= '1';
	$html			= '<p>[받는 분] '.$receipt_name.' / '.$receipt_tel.' / '.$receipt_mobile.'</p>
						<p>'.$receipt_addr.'</p>';

	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg,"html"=>$html);
	$msg = json_encode($tmpMsgArray);
	
}else if($_POST['mode']=="memo_del" || $_POST['mode']=="memo_img_del"){

	$receipt_no	= $_POST["receipt_no"];

	//경로
	$filepath = $Dir.DataDir."shopimages/cscenter/";
	//파일
	$noticefile = new FILE($filepath);
	
	if($_POST['mode']=="memo_del"){
		$memo_no=$_POST["memo_no"];
		$memo_del_sql="select filename from tblcscenterfile where receipt_no='".$receipt_no."' and memo_no='".$memo_no."' and route_type='csadmin'";
		$memo_del_result=pmysql_query($memo_del_sql);
		while($memo_del_data=pmysql_fetch_array($memo_del_result)){
			$noticefile->removeFile( $memo_del_data["filename"] );	
		}

		pmysql_query("delete from tblcscenterfile  where receipt_no='".$receipt_no."' and memo_no='".$memo_no."' and route_type='csadmin'");
		pmysql_query("delete from tblcscentermemo  where receipt_no='".$receipt_no."' and no='".$memo_no."' and route_type='csadmin'");

		
	}else if($_POST['mode']=="memo_img_del"){
		$img_no=$_POST["img_no"];
		list($img_name)=pmysql_fetch("select filename from tblcscenterfile where no='".$img_no."' and route_type='onlineas'");
		$noticefile->removeFile( $img_name );	
		pmysql_query("delete from tblcscenterfile  where no='".$img_no."' and route_type='csadmin'");
		
	}
	
	#메모 정보 쿼리
	$memo_sql="select * from tblcscentermemo where receipt_no='".$receipt_no."' and route_type='csadmin' order by regdt";
	$memo_result=pmysql_query($memo_sql);
	while($memo_data=pmysql_fetch_array($memo_result)){
		$memo_while[$memo_data["no"]]=$memo_data;

		$file_sql="select * from tblcscenterfile where receipt_no='".$receipt_no."' and memo_no='".$memo_data["no"]."' and route_type='csadmin' order by no";
		$file_result=pmysql_query($file_sql);
		while($file_data=pmysql_fetch_array($file_result)){
			$memo_while[$memo_data["no"]]["filename"][$file_data["no"]]=$file_data["filename"];
		}
	}
	$html="";
	if($memo_while){
		foreach($memo_while as $mw=>$mwv){
			#접수일
			$memo_date=substr($mwv['regdt'],'0','4').'-'.substr($mwv['regdt'],'4','2').'-'.substr($mwv['regdt'],'6','2').' '.substr($mwv['regdt'],'8','2').':'.substr($mwv['regdt'],'10','2').':'.substr($mwv['regdt'],'12','2');

			$html.="<h4><strong>".$mwv["admin_name"]."(".$mwv["admin_id"].")</strong> ".$memo_date;
			if($mwv["admin_id"]==$_ShopInfo->id){
				$html.="&nbsp;<div class='btn-wrap1'><span><a href=\"javascript:oc_ajax_proc('memo_del', '".$mw."')\" class=\"btn-type1\" style=\"width:50px;\">삭제</a></span></div>";
			}
			$html.="</h4>";
			
			$html.="<div class=\"cont\">";
			if($mwv["filename"]){
				foreach($mwv["filename"] as $mwf=>$mwfv){
					$html.="<img src='".$filepath.$mwfv."'>";
					
					if($mwv["admin_id"]==$_ShopInfo->id){
						$html.="&nbsp;<div class=\"btn-wrap1\"><span><a href=\"javascript:oc_ajax_proc('memo_img_del', '".$mwf."')\" class=\"btn-type1\" style=\"width:50px;\">삭제</a></span></div>";
					}
					$html.="<br><br>";
				}
			}
			$html.=$mwv["cs_memo"];
			$html.="</div>";
			
		}
	}

	$msg			= "삭제 되었습니다.";
	$msgType	= '1';

	$tmpMsgArray = array("type"=>$msgType,"msg"=>$msg,"html"=>$html);
	$msg = json_encode($tmpMsgArray);
}

echo $msg;
?>
