<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

//$ordercode=$_POST["ordercode"];
$ordercode=$_REQUEST["ordercode"];

$type=$_POST["type"];
$mode=$_POST["mode"];
$hidedisplay=$_POST["hidedisplay"];

$order_msg=$_POST["order_msg"];

$rescode=$_POST["rescode"];
$pay_admin_proc=$_POST["pay_admin_proc"];

$clameno=$_POST["clameno"];
$op_idx=$_POST["op_idx"];

if($type=="sort") {
	$sort=$_POST["sort"];
}

if($ordercode==NULL) {
	alert_go('잘못된 접근입니다.','c');
}

// 취소시 추가 값들 정리 S (2016.02.12 - 김재수 추가) -----------------------------
$c_re_type		= $_POST["re_type"];
$c_oi_step1		= $_POST["oi_step1"];
$c_oi_step2		= $_POST["oi_step2"];
$c_op_step		= $_POST["op_step"];
$c_paymethod	= $_POST["paymethod"];
$c_idxs			= $_POST["idxs"];
$c_code			= $_POST["sel_code"];
$c_memo			= $_POST["memo"];
$c_bankcode		= $_POST["bankcode"];
$c_bankaccount	= $_POST["bankaccount"];
$c_bankuser		= $_POST["bankuser"];
$c_sel_option1	= $_POST["sel_option1"];
$c_sel_option2	= $_POST["sel_option2"];

$c_sel_option_price_text	= $_POST["sel_option_price_text"];
$c_sel_text_opt_subject	    = $_POST["sel_text_opt_subject"];
$c_sel_text_opt_content	    = $_POST["sel_text_opt_content"];

$c_pgcancel_type			= $_POST["pgcancel_type"];
$c_pgcancel_res_code	= $_POST["pgcancel_res_code"];
$c_pgcancel_res_msg	    = $_POST["pgcancel_res_msg"];


// 취소시 추가 값들 정리 E (2016.02.12 - 김재수 추가) -----------------------------

$sql="SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}'";
$result=pmysql_query($sql,get_db_conn());
$_ord=pmysql_fetch_object($result);
//exdebug($_ord);
pmysql_free_result($result);
if(!$_ord) {
	alert_go("해당 주문내역이 존재하지 않습니다.",'c');
}
$isupdate=false;

$pgid_info="";
$pg_type="";
switch ($_ord->paymethod[0]) {
	case "B":
		break;
	case "V":
		$pgid_info=GetEscrowType($_shopdata->trans_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "O":
		$pgid_info=GetEscrowType($_shopdata->virtual_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "Q":
		$pgid_info=GetEscrowType($_shopdata->escrow_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "C":
		$pgid_info=GetEscrowType($_shopdata->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "P":
		$pgid_info=GetEscrowType($_shopdata->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "M":
		$pgid_info=GetEscrowType($_shopdata->mobile_id);
		$pg_type=$pgid_info["PG"];
		break;
}
$pg_type=trim($pg_type);

$tax_type=$_shopdata->tax_type;
## 쿠폰 복구 1회
#

//카드승인/취소,  핸드폰결제 취소 처리 (결제서버에서 호출)
if (ord($rescode) && ord($ordercode)) {
	$confirm_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/pgconfirm.php","ordercode=".$ordercode);
	if (strlen($rescode)==1 && $rescode==$confirm_data) {
		$sql = "UPDATE tblorderinfo SET pay_admin_proc='{$rescode}' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		$isupdate=true;
	}
	if (strlen($rescode)==1 && $rescode==$confirm_data && $rescode=="C" && $_ord->paymethod[0]=="P") {
		$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
		$sql.= "WHERE ordercode='{$ordercode}' AND SUBSTR(paymethod,1,1)='P'";
		if(pmysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			pmysql_query($sql,get_db_conn());
		}
		$isupdate=true;
	}
}

//무통장입금확인
if($type=="bank" && ord($ordercode)) {
	if($_ord->paymethod=="B" && $tax_type=="Y") {
		$sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='{$ordercode}' AND type='N' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		if($row->cnt>0) {
			$flag="Y";
			include($Dir."lib/taxsave.inc.php");
		}
	}

	pmysql_query("UPDATE tblorderinfo SET bank_date='".date("YmdHis")."' WHERE ordercode='{$ordercode}' ",get_db_conn());

	//주문수량 차감
	order_quantity( $ordercode );

	// 신규상태 변경 추가 - (2016.02.12 - 김재수 추가)
	orderStepUpdate($exe_id, $ordercode, '1'); // 결제완료

	$isupdate=true;

	//입금 확인 메일과 SMS을 보낸다. - 테스트 중이라 일단 막음 꼭 풀어야함 - 김재수
	if(ord($_ord->sender_email)) {
		SendBankMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $_ord->sender_email, $ordercode);
	}

	# SMS ( 입금 확인 안내 )
	$mem_return_msg = sms_autosend( 'mem_bankok', $ordercode, '', '' );
	$admin_return_msg = sms_autosend( 'admin_bankok', $ordercode, '', '' );
	/*
	$sql="SELECT * FROM tblsmsinfo WHERE mem_bankok='Y' ";
	$result=pmysql_query($sql,get_db_conn());
	if($rowsms=pmysql_fetch_object($result)) {
		$sms_id=$rowsms->id;
		$sms_authkey=$rowsms->authkey;

		$bankprice=$_ord->price;
		$bankname=$_ord->sender_name;
		$msg_mem_bankok=$rowsms->msg_mem_bankok;
		if(ord($msg_mem_bankok)==0) $msg_mem_bankok="[".strip_tags($_shopdata->shopname)."] [DATE]의 주문이 입금확인 되었습니다. 빨리 발송해 드리겠습니다.";
		$patten=array("[DATE]","[NAME]","[PRICE]");
		$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$bankname,$bankprice);

		$msg_mem_bankok=str_replace($patten,$replace,$msg_mem_bankok);
		$msg_mem_bankok=addslashes($msg_mem_bankok);

		$fromtel=$rowsms->return_tel;
		$date=0;
		$etcmsg="입금확인메세지(회원)";
		$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_bankok, $etcmsg);
	}
	pmysql_free_result($result);
	*/
	echo "<script>";
	echo "	alert('입금 완료로 처리되었습니다.');";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;

//무통장입금 취소(환불처리)
} elseif($type=="bankcancel" && ord($ordercode)) {
	if($_ord->deli_gbn=="C" && $_ord->paymethod=="B") {
		$sql = "UPDATE tblorderinfo SET bank_date='".substr($_ord->bank_date,0,8)."X' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		$isupdate=true;
	}

//일반 가상계좌 취소(환불처리)
} elseif($type=="virtualcancel" && ord($ordercode)) {
	if($_ord->deli_gbn=="C" && strstr("O", $_ord->paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET pay_admin_proc='C', bank_date='".substr($_ord->bank_date,0,8)."X' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		$isupdate=true;
	}

//실시간계좌이체 취소(환불처리)
} elseif($type=="transcancel" && ord($ordercode)) {
	if($_ord->deli_gbn=="C" && strstr("V", $_ord->paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET pay_admin_proc='C', bank_date='".substr($_ord->bank_date,0,8)."X' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		$isupdate=true;
	}

//상품별 배송준비 세팅
} elseif($type=="readyOneDeli" && ord($ordercode)) {

	//현재 주문의 상태값을 가져온다.
	list($old_step, $op_deli_gbn)=pmysql_fetch_array(pmysql_query("select op_step, deli_gbn from tblorderproduct WHERE ordercode='{$ordercode}' AND idx='{$op_idx}' "));
	if ($old_step == '1' && $op_deli_gbn == 'N') {

		//각각의 주문상품을 배송 준비중으로 변경한다.
		$sql = "UPDATE tblorderproduct SET deli_gbn='S' ";
		$sql.= "WHERE ordercode='".$ordercode."' AND op_step = '1' AND deli_gbn='N' ";
		$sql.= "AND idx='".$op_idx."' ";

		if(pmysql_query($sql,get_db_conn())) {
			// 신규상태 변경
			orderProductStepUpdate($exe_id, $ordercode, $op_idx, '2'); // 배송 준비중

			//현재 주문의 상태값을 가져온다.
			list($old_step1, $old_step2)=pmysql_fetch_array(pmysql_query("select oi_step1, oi_step2 from tblorderinfo WHERE ordercode='".trim($ordercode)."'"));
			if ($old_step1 == '1' && $old_step2 == '0') {
				//주문을 배송 준비중으로 변경한다.
				$sql2 = "UPDATE tblorderinfo SET deli_gbn='S' WHERE ordercode='".$ordercode."' AND oi_step1 = '1' AND oi_step2 = '0' AND deli_gbn='N' ";
				pmysql_query($sql2,get_db_conn());

				// 신규상태 변경
				orderStepUpdate($exe_id, $ordercode, '2', '0'); // 배송 준비중
			}
		}
	}

	echo "<script>";
	echo "	alert('배송 준비로 처리되었습니다.');";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;

//배송준비 세팅
} elseif($type=="readydeli" && ord($ordercode)) {
	//$readydeli_sql	= "select count(*) as cnt,sum(case when length(store_code)=0 then 1 else 0 end) delivery_cnt  from tblorderproduct where ordercode='{$ordercode}'"; //2016-10-07 libe90 O2O주문은 배송처리 안되게 작업

	$readydeli_sql	= "select count(*) as cnt,sum(case when delivery_type='0' then 1 else 0 end) delivery_cnt  from tblorderproduct where ordercode='{$ordercode}'"; //2016-10-11 ssuya O2O주문이 아니더라고 store_code가 들어감으로 변경

	list($bcnt,$delivery_cnt)=pmysql_fetch_array(pmysql_query($readydeli_sql));

	if ($_ord->deli_gbn == "N" && $bcnt == $delivery_cnt) {
		$sql = "UPDATE tblorderinfo SET deli_gbn='S' WHERE ordercode='{$ordercode}' ";
		if (pmysql_query($sql, get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='S' WHERE ordercode='{$ordercode}' ";
			//$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			$sql .= "AND op_step < 40 ";
			//$sql .= "AND deli_gbn='N' and length(store_code)=0 ";	//2016-10-07 libe90 O2O주문은 배송처리 안되게 작업
			$sql .= "AND deli_gbn='N' and delivery_type='0' ";	//2016-10-11 ssuya O2O주문이 아니더라고 store_code가 들어감으로 변경
			pmysql_query($sql, get_db_conn());
		}

		// 신규상태 변경 추가 - (2016.02.12 - 김재수 추가)
		orderStepUpdate($exe_id, $ordercode, '2'); // 배송준비
	}

	echo "<script>";
	echo "	alert('배송 준비로 처리되었습니다.');";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;

//배송준비 세팅->배송중->배송완료 세팅 (사용안함 2016.02.18 - 김재수 막음)
/*} elseif($type=="delivery" && ord($ordercode)) {
	$delimailok=$_POST["delimailtype"];	//배송중에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
	$in_reserve=$_POST["in_reserve"];

	if(strstr("NXS",$_ord->deli_gbn)) {
		$deli_com=$_POST["deli_com"];
		$deli_num=$_POST["deli_num"];
		$deli_name=$_POST["deli_name"];

		$patterns = array(" ","_","-");
		$replace = array("","","");
		$deli_num = str_replace($patterns,$replace,$deli_num);

		###에스크로 서버에 배송정보 전달 - 에스크로 결제일 경우에만.....

		if(ord($deli_name)==0) {
			$deli_name="자가배송";
		}
		if(strstr("QP", $_ord->paymethod[0])) {

			if($pg_type=="A") {	//KCP
				$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

				$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

				$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
				if (substr($delivery_data,0,2)!="OK") {
					$tempdata=explode("|",$delivery_data);
					$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
					if(ord($tempdata[1])) $errmsg=$tempdata[1];
					alert_go($errmsg,-1);
				} else {
					$tempdata=explode("|",$delivery_data);
					if(ord($tempdata[1])) $errmsg=$tempdata[1];
					if(ord($errmsg)) {
						echo "<script> alert('{$errmsg}');</script>";
					}
				}
			} elseif($pg_type=="B") {	//LG데이콤
				$delicom_code="";
				if(ord($deli_com)) {
					$sql = "SELECT dacom_code FROM tbldelicompany WHERE code='{$deli_com}' ";
					$result=pmysql_query($sql,get_db_conn());
					if($row=pmysql_fetch_object($result)) {
						$delicom_code=$row->dacom_code;
					}
					pmysql_free_result($result);
				}
				$query="mid={$pgid_info["ID"]}&mertkey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&delicom_code=".$delicom_code;

				$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

				$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
				if (substr($delivery_data,0,2)!="OK") {
					$tempdata=explode("|",$delivery_data);
					$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
					if(ord($tempdata[1])) $errmsg=$tempdata[1];
					alert_go($errmsg,-1);
				} else {
					$tempdata=explode("|",$delivery_data);
					if(ord($tempdata[1])) $errmsg=$tempdata[1];
					if(ord($errmsg)) {
						echo "<script> alert('{$errmsg}');</script>";
					}
				}
			} elseif($pg_type=="C") {	//올더게이트
				$query="storeid={$pgid_info["ID"]}&ordercode=".$ordercode;

				$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

				$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
				if (substr($delivery_data,0,2)!="OK") {
					$tempdata=explode("|",$delivery_data);
					$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
					if(ord($tempdata[1])) $errmsg=$tempdata[1];
					alert_go($errmsg,-1);
				} else {
					$tempdata=explode("|",$delivery_data);
					if(ord($tempdata[1])) $errmsg=$tempdata[1];
					if(ord($errmsg)) {
						echo "<script> alert('{$errmsg}');</script>";
					}
				}
			} elseif($pg_type=="D") {	//INICIS
				$delicom_code="";
				if(ord($deli_com)) {
					$sql = "SELECT inicis_code FROM tbldelicompany WHERE code='{$deli_com}' ";
					$result=pmysql_query($sql,get_db_conn());
					if($row=pmysql_fetch_object($result)) {
						$delicom_code=$row->inicis_code;
					}
					pmysql_free_result($result);
				}
				$query="sitecd={$pgid_info["EID"]}&ordercode={$ordercode}&deli_num={$deli_num}&delicom_code={$delicom_code}&deli_name=".urlencode($deli_name);

				$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

				$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
				if (substr($delivery_data,0,2)!="OK") {
					$tempdata=explode("|",$delivery_data);
					$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
					if(ord($tempdata[1])) $errmsg=$tempdata[1];
					alert_go($errmsg,-1);
				} else {
					$tempdata=explode("|",$delivery_data);
					if(ord($tempdata[1])) $errmsg=$tempdata[1];
					if(ord($errmsg)) {
						echo "<script> alert('{$errmsg}');</script>";
					}
				}
			}
		}
		$deliQry = "";
		if( strlen( $deli_num ) > 0 && strlen( $deli_com ) > 0 ){
			$deliQry = ", deli_com = '".$deli_com."', deli_num = '".$deli_num."' ";
		}

		$sql = "UPDATE tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		if(pmysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='Y', deli_date='".date("YmdHis")."' ".$deliQry;
			$sql.= "WHERE ordercode='{$ordercode}' ";
			//$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			$sql.= "AND op_step < 41 ";
			$sql.= "AND deli_gbn!='Y' ";
			pmysql_query($sql,get_db_conn());

			// 신규상태 변경 추가 - (2016.02.12 - 김재수 추가)
			orderStepUpdate($exe_id, $ordercode, '3'); // 배송중
		}
		$isupdate=true;

		if($delimailok=="Y") {	//배송중 메일을 발송할 경우
			$delimailtype="N";
			SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype);

			if(ord($_ord->sender_tel)) {
				$sql ="SELECT * FROM tblsmsinfo WHERE (mem_delivery='Y' OR mem_delinum='Y') ";
				$result=pmysql_query($sql,get_db_conn());
				if($rowsms=pmysql_fetch_object($result)) {
					$sms_id=$rowsms->id;
					$sms_authkey=$rowsms->authkey;

					$deliprice=$_ord->price;
					$deliname=$_ord->sender_name;

					$msg_mem_delinum=$rowsms->msg_mem_delinum;
					if(ord($msg_mem_delinum)==0) {
						$msg_mem_delinum="[".strip_tags($shopname)."] [DELICOM] 송장번호 : [DELINUM] 금일 발송처리 되었습니다.";
					}
					$patten=array("[DATE]","[DELICOM]","[DELINUM]","[NAME]","[PRICE]");
					$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$deli_name,$deli_num,$deliname,$deliprice);
					$msg_mem_delinum=str_replace($patten,$replace,$msg_mem_delinum);
					$msg_mem_delinum=addslashes($msg_mem_delinum);

					$msg_mem_delivery=$rowsms->msg_mem_delivery;
					if(ord($msg_mem_delivery)==0) {
						$msg_mem_delivery="[".strip_tags($shopname)."]에서 [DATE]에 주문한 상품을 발송해 드렸습니다. 감사합니다.";
					}
					$patten=array("[DATE]","[NAME]","[PRICE]");
					$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$deliname,$deliprice);
					$msg_mem_delivery=str_replace($patten,$replace,$msg_mem_delivery);
					$msg_mem_delivery=addslashes($msg_mem_delivery);

					$fromtel=$rowsms->return_tel;
					$date=0;
					if($rowsms->mem_delinum=="Y" && ord($deli_name) && ord($deli_num)) {	//송장안내메세지
						$etcmsg="송장안내메세지(회원)";
						$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_delinum, $etcmsg);
					}
					if($rowsms->mem_delivery=="Y") {	//상품발송메세지
						$etcmsg="상품발송메세지(회원)";
						$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_delivery, $etcmsg);
					}
				}
				pmysql_free_result($result);
			}
		}
		//echo "<script>if(opener) {opener.history.go(0);} window.close(); </script>";
		echo "<script>if(opener) {opener.location.reload();} window.close(); </script>";
		exit;
	} elseif(!strstr("NXS",$_ord->deli_gbn)) {
		echo "<script>alert(\"이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.\");</script>";
	}
*/

#반송처리 - 처리방식 변경으로 막음 (2016.02.11 - 김재수 막음)
/*} elseif($type=="redelivery" && ord($ordercode)) {
	$sql = "UPDATE tblorderinfo SET deli_gbn='R' WHERE ordercode='{$ordercode}' ";
	if(pmysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='R' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
		pmysql_query($sql,get_db_conn());
	}
	//echo "<script>if(opener) {opener.history.go(0);} window.close(); </script>";
	echo "<script>if(opener) {opener.location.reload();} window.close(); </script>";
	exit;*/
//취소하기 - 반품/교환 신청
} elseif(($type=="redelivery" || $type=="redelivery_all") && ord($c_re_type)) {

	//exdebug($_POST);
	//exit;

	$in_date		= date("YmdHis");
	if ($c_re_type == 'B') {			// 반품
		$alert_text	="선택상품 취소가 요청되었습니다.";
		$c_redelivery_type	= "Y";
	} else if ($c_re_type == 'C') {	// 교환
		$alert_text	="선택상품 교환이 요청되었습니다.";
		$c_redelivery_type	= "G";
	}

	// 기존에 사용하던 반품신청 필드를 반품또는 교환으로 업데이트 한다.
	$sql = "UPDATE tblorderproduct SET redelivery_type='{$c_redelivery_type}', redelivery_date='{$in_date}' WHERE idx IN ('".str_replace("|", "','", $c_idxs)."') ";
	pmysql_query($sql,get_db_conn());

	$c_opt1_changes	= implode( '|', $c_sel_option1);
	$c_opt2_changes	= implode( '|', $c_sel_option2);

	//echo $c_opt1_changes."<br>".$c_opt2_changes;
	//exit;

	$c_opt2_pt_changes		= implode( '|!@#|', $c_sel_option_price_text);
	$c_opt_text_s_changes	= implode( '|!@#|', $c_sel_text_opt_subject);
	$c_opt_text_c_changes	= implode( '|!@#|', $c_sel_text_opt_content);

	$fail_cnt	= 0;

	/*if (strstr("Q", $c_paymethod) && ($c_oi_step1 == 2 || $c_oi_step1 == 3) && $c_redelivery_type == "Y") {
		// 상품별 반품이 아니거나 반품접수, 환불접수 상태가 아닌게 있는지 체크한다.
		list($op_rt_cnt)=pmysql_fetch_array(pmysql_query("select count(redelivery_type) as op_rt_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND (redelivery_type != '{$c_redelivery_type}' or (redelivery_type = '{$c_redelivery_type}' AND op_step NOT IN ('41', '42'))) "));
		if ($op_rt_cnt == 0) { // 전체가 반품접수, 환불접수 상태일 경우
			//KCP에 정산보류 전달.

			$pgid_info=GetEscrowType($_shopdata->escrow_id);
			$pg_type=$pgid_info["PG"];
			$pg_type=trim($pg_type);

			// 정산보류로 보낸다.
			$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode=".$ordercode;
			$hold_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/hold.php",$query);
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

					$hold_sql = "UPDATE tblorderinfo SET ";
					$hold_sql.= "deli_gbn		= 'H' WHERE ordercode='{$ordercode}' AND deli_gbn != 'C' ";
					if(pmysql_query($hold_sql,get_db_conn())) {
						$hold_sql2 = "UPDATE tblorderproduct SET deli_gbn='H' ";
						$hold_sql2.= "WHERE ordercode='{$ordercode}' AND deli_gbn != 'C' ";
						pmysql_query($hold_sql2,get_db_conn());
					}
				}
			}
		}
	}*/

	if ($fail_cnt == 0) {
		// 반품및 교환 신청을 한다.
		orderCancel($exe_id, $ordercode, $c_idxs, $c_oi_step1, $c_oi_step2, $c_op_step, $c_paymethod, $c_code, $c_memo, $c_bankcode, $c_bankaccount, $c_bankuser, $c_bankusertel, $c_re_type, $c_opt1_changes, $c_opt2_changes, $c_opt2_pt_changes, $c_opt_text_s_changes, $c_opt_text_c_changes );
		//exit;
		// 상품별 반품또는 교환신청이 아닌 상태가 있는지 체크한다.
		list($op_rt_cnt)=pmysql_fetch_array(pmysql_query("select count(redelivery_type) as op_rt_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND redelivery_type != '{$c_redelivery_type}'"));
		if ($op_rt_cnt == 0) { // 전체가 반품또는 교환신청일 경우
			$sql = "UPDATE tblorderinfo SET redelivery_type='{$c_redelivery_type}', redelivery_date='{$in_date}' WHERE ordercode='".trim($ordercode)."' ";
			pmysql_query($sql,get_db_conn());
		}
	}

	echo "<script>";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	alert('{$alert_text}');";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
//배송지 주소 업데이트
} elseif($type=="addressupdate" && ord($ordercode)) {
	$zonecode=$_POST["zonecode"];
	$post1=$_POST["post1"];
	$post2=$_POST["post2"];
	$address1=$_POST["address1"];
	$receiver_addr="우편번호 : {$post1}-{$post2}\\n주소 : ".$address1;
	$sql = "UPDATE tblorderinfo SET receiver_addr='{$receiver_addr}', post5 = '{$zonecode}'";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	pmysql_query($sql,get_db_conn());
	$isupdate=true;

//선택 상품별 송장번호 업데이트
} elseif($type=="deliinfoup" && ord($ordercode) && ord($_POST["deli_idxs"])) {
	//$delimailtype=$_POST["delimailtype"];	//송장정보 업데이트에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
	$delimailok=$_POST["delimailtype"];	//배송중에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
	$idxs=$_POST["deli_idxs"];
	$qryErr = 0;
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/deliinfoup_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$deliinfo=rtrim($idxs,',');
	$ardeli=explode("|",$deliinfo);
	$tmp_arr_deli = array();
	$arr_deli_idxs = array();
	for($i=0;$i<count($ardeli);$i++) {
		$idx=$deli_com=$deli_num="";
		$prinfo=explode(",",$ardeli[$i]);
		for($j=0;$j<count($prinfo);$j++) {
			if (substr($prinfo[$j],0,5)=="IDXS=") $idx=trim(substr($prinfo[$j],5));
			elseif (substr($prinfo[$j],0,9)=="DELI_COM=") $deli_com=trim(substr($prinfo[$j],9));
			elseif (substr($prinfo[$j],0,9)=="DELI_NUM=") $deli_num=trim(substr($prinfo[$j],9));
		}
		if(strlen($idx) > 0) {
			$deliQry = "";
			if ($deli_com != '' && $deli_num !='') {
				/********
				에스크로 서버에 송장정보 전달 - 에스크로 결제일 경우에만.....
				********/
				//배송한 상품의 수를 체크한다.
				list($op_deli_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='{$ordercode}' AND deli_gbn = 'Y' "));
				list($deli_name)=pmysql_fetch_array(pmysql_query("SELECT company_name FROM tbldelicompany WHERE code='{$deli_com}' "));

				if ($op_deli_cnt==0) { // 처음 배송된 상품일 경우
					if(ord($deli_name)==0) {
						$deli_name="자가배송";
						$deli_num="0000";
					}
					if(strstr("QP", $_ord->paymethod[0])) {

						if($pg_type=="A") {	//KCP
							$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

							$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

							$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
							if (substr($delivery_data,0,2)!="OK") {
								$tempdata=explode("|",$delivery_data);
								$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
								if(ord($tempdata[1])) $errmsg=$tempdata[1];
								//alert_go($errmsg,-1);
								echo "<script>alert('{$errmsg}');window.location.href = 'order_detail.php?ordercode={$ordercode}' </script>";
							} else {
								$tempdata=explode("|",$delivery_data);
								if(ord($tempdata[1])) $errmsg=$tempdata[1];
								if(ord($errmsg)) {
									echo "<script> alert('{$errmsg}');</script>";
								}
							}
						} else if($pg_type=="G") {	//NICE
							$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

							$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

							$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
							if (substr($delivery_data,0,2)!="OK") {
								$tempdata=explode("|",$delivery_data);
								$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
								if(ord($tempdata[1])) $errmsg=$tempdata[1];
								//alert_go($errmsg,-1);
								echo "<script>alert('{$errmsg}');window.location.href = 'order_detail.php?ordercode={$ordercode}' </script>";
							} else {
								$tempdata=explode("|",$delivery_data);
								if(ord($tempdata[1])) $errmsg=$tempdata[1];
								if(ord($errmsg)) {
									echo "<script> alert('{$errmsg}');</script>";
								}
							}
						}
					}
				}
				$deliQry = ", deli_com = '".$deli_com."', deli_num = '".$deli_num."' ";
				$sql = "UPDATE tblorderproduct SET deli_com='{$deli_com}', deli_num='{$deli_num}', deli_gbn = 'Y', deli_date='".date("YmdHis")."' ";
				$sql.= "WHERE ordercode='{$ordercode}' AND idx='{$idx}' ";
				//$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				$sql.= "AND op_step < 40 ";
				//echo $sql;
				pmysql_query($sql,get_db_conn());
				if( !pmysql_error() ){

					// 신규상태 변경 추가 - (2016.02.12 - 김재수 추가)
					orderProductStepUpdate($exe_id, $ordercode, $idx, '3'); // 배송중

					#배송정보 변경 LOG
					$outText.= " 주문 번호     : ".$ordercode."\n";
					$outText.= " 상품 IDX     : ".$idx."\n";
					$outText.= " 송장 번호     : ".$deli_num."\n";
					$outText.= " 배송회사 코드 : ".$deli_com."\n";
					$outText.= " 배송상태 변경 : 발송중( deli_gbn = Y )\n";

					if($delimailok=="Y") {	//배송완료 메일을 발송할 경우
						$delimailtype="N";
						$tmp_arr_deli_idx = array_search( $deliQry, $tmp_arr_deli );
						if( $tmp_arr_deli_idx === false && $deliQry != '' ) {
							$tmp_arr_deli[] = $deliQry;
							$arr_deli_idxs[] = array( 'ordercode'=>$ordercode, 'idxs'=>$idx, 'deli_com'=>$deli_com, 'deli_num'=>$deli_num );
						} else if( $deliQry != '' ) {
							$arr_deli_idxs[$tmp_arr_deli_idx]['idxs'] = $arr_deli_idxs[$tmp_arr_deli_idx]['idxs'].','.$idx;
						}
					}
				} else {
					$outText.= " 주문 번호     : ".$ordercode."\n";
					$outText.= " 상품 IDX     : ".$idx."\n";
					$outText.= " 송장 번호     : ".$deli_num."\n";
					$outText.= " 배송회사 코드 : ".$deli_com."\n";
					$outText.= " 배송상태 변경 : ERR \n";
					$qryErr++;
				}
			} else {
				$outText.= " 주문 번호     : ".$ordercode."\n";
				$outText.= " 상품 IDX     : ".$idx."\n";
				$outText.= " 송장 번호     : ".$deli_num."\n";
				$outText.= " 배송회사 코드 : ".$deli_com."\n";
				$outText.= " 배송상태 변경 : ERR \n";
				$qryErr++;
			}
		}
	}
	if( $qryErr == 0 ){
		$sql = "UPDATE tblorderinfo SET deli_gbn = 'Y', deli_date='".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		//echo $sql;
		pmysql_query($sql,get_db_conn());

		// 신규상태 변경 추가
		orderStepUpdate($exe_id, $ordercode, '3', '0' ); // 배송중

		// 배송 mail 및 문자발송
		if( count( $arr_deli_idxs ) > 0 ){
			foreach( $arr_deli_idxs as $k=>$v ){
				SendDeliMail( $_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $v['ordercode'], $v['deli_com'], $v['deli_num'], 'N', $v['idxs'] );
				$op_cnt_sql = "SELECT COUNT( * ) AS cnt FROM tblorderproduct WHERE ordercode ='".$v['ordercode']."'";
				$op_cnt_res = pmysql_query( $op_cnt_sql, get_db_conn() );
				$op_cnt_row = pmysql_fetch_object( $op_cnt_res );
				pmysql_free_result( $op_cnt_res );
				$op_idx_cnt = count( explode( ',', $v['idxs'] ) );
				if( $op_cnt_row->cnt == 1 || $op_idx_cnt == $op_cnt_row->cnt ){
					$mem_return_msg = sms_autosend( 'mem_delivery', $v['ordercode'], $v['idxs'], '' );
					$admin_return_msg = sms_autosend( 'admin_delivery', $v['ordercode'], $v['idxs'], '' );
				} else if( $op_cnt_row->cnt > 1 ) {
					$mem_return_msg = sms_autosend( 'mem_delinum', $v['ordercode'], $v['idxs'], '' );
					$admin_return_msg = sms_autosend( 'admin_delinum', $v['ordercode'], $v['idxs'], '' );
				}
			}
		}
	}

	$outText.= "\n";
	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'deliinfoup_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."deliinfoup_".date("Ymd").".txt",0777);

	echo "<script>";
	//echo "	alert('발송처리 완료 되었습니다.');";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;

//송장정보 업데이트
} elseif($type=="deliupdate" && ord($ordercode)) {
	$delimailok=$_POST["delimailtype"];	//배송중에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
	$deli_com=$_POST["deli_com"];
	$deli_num=$_POST["deli_num"];
	$qryErr = 0;
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/deliupdate_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$patterns = array(" ","_","-");
	$replace = array("","","");
	$deli_num = str_replace($patterns,$replace,$deli_num);

	/********
	에스크로 서버에 송장정보 전달 - 에스크로 결제일 경우에만.....
	********/

	list($deli_name)=pmysql_fetch_array(pmysql_query("SELECT company_name FROM tbldelicompany WHERE code='{$deli_com}' "));

	if(ord($deli_name)==0) {
		$deli_name="자가배송";
		$deli_num="0000";
	}
	if(strstr("QP", $_ord->paymethod[0])) {

		if($pg_type=="A") {	//KCP
			$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

			$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

			$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
			if (substr($delivery_data,0,2)!="OK") {
				$tempdata=explode("|",$delivery_data);
				$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				//alert_go($errmsg,-1);
				echo "<script>alert('{$errmsg}');window.location.href = 'order_detail.php?ordercode={$ordercode}' </script>";
			} else {
				$tempdata=explode("|",$delivery_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					echo "<script> alert('{$errmsg}');</script>";
				}
			}
		} else if($pg_type=="G") {	//NICE
			$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

			$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

			$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
			if (substr($delivery_data,0,2)!="OK") {
				$tempdata=explode("|",$delivery_data);
				$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				//alert_go($errmsg,-1);
				echo "<script>alert('{$errmsg}');window.location.href = 'order_detail.php?ordercode={$ordercode}' </script>";
			} else {
				$tempdata=explode("|",$delivery_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					echo "<script> alert('{$errmsg}');</script>";
				}
			}
		}
	}

	$sql = "UPDATE tblorderproduct SET deli_num='{$deli_num}', deli_com='{$deli_com}', deli_gbn ='Y', deli_date='".date("YmdHis")."'  ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	//$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	$sql.= "AND op_step < 40 AND (op_step='3' OR op_step='4')";
	pmysql_query($sql,get_db_conn());

	if( !pmysql_error() ){
		$sql = "UPDATE tblorderinfo SET deli_gbn ='Y', deli_date='".date("YmdHis")."'  ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());

		// 신규상태 변경 추가
		orderStepUpdate($exe_id, $ordercode, '3' ); // 배송중

		#배송정보 변경 LOG
		$outText.= " 주문 번호     : ".$ordercode."\n";
		$outText.= " 송장 번호     : ".$deli_num."\n";
		$outText.= " 배송회사 코드 : ".$deli_com."\n";
		$outText.= " 배송상태 변경 : 발송중( deli_gbn = Y )\n";
	} else {

		#배송정보 변경 LOG
		$outText.= " 주문 번호     : ".$ordercode."\n";
		$outText.= " 송장 번호     : ".$deli_num."\n";
		$outText.= " 배송회사 코드 : ".$deli_com."\n";
		$outText.= " 배송상태 변경 : ERR \n";
		$qryErr++;
	}
	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$outText.= "\n";
	$upQrt_f = fopen($textDir.'deliupdate_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."deliupdate_".date("Ymd").".txt",0777);

	if( $qryErr == 0 ){
		if($delimailok=="Y") {	//배송완료 메일을 발송할 경우
			$delimailtype="N";
			SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype);

			if(ord($_ord->sender_tel)) {
				# SMS ( 완전배송 안내메세지 )
				$mem_return_msg = sms_autosend( 'mem_delivery', $ordercode, '', '' );
				$admin_return_msg = sms_autosend( 'admin_delivery', $ordercode, '', '' );
				/*
				$sql ="SELECT * FROM tblsmsinfo WHERE (mem_delivery='Y' OR mem_delinum='Y') ";
				$result=pmysql_query($sql,get_db_conn());
				if($rowsms=pmysql_fetch_object($result)) {
					$sms_id=$rowsms->id;
					$sms_authkey=$rowsms->authkey;

					$deliprice=$_ord->price;
					$deliname=$_ord->sender_name;

					$msg_mem_delinum=$rowsms->msg_mem_delinum;
					if(ord($msg_mem_delinum)==0) {
						$msg_mem_delinum="[".strip_tags($shopname)."] [DELICOM] 송장번호 : [DELINUM] 금일 발송처리 되었습니다.";
					}
					$patten=array("[DATE]","[DELICOM]","[DELINUM]","[NAME]","[PRICE]");
					$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$deli_name,$deli_num,$deliname,$deliprice);
					$msg_mem_delinum=str_replace($patten,$replace,$msg_mem_delinum);
					$msg_mem_delinum=addslashes($msg_mem_delinum);

					$msg_mem_delivery=$rowsms->msg_mem_delivery;
					if(ord($msg_mem_delivery)==0) {
						$msg_mem_delivery="[".strip_tags($shopname)."]에서 [DATE]에 주문한 상품을 발송해 드렸습니다. 감사합니다.";
					}
					$patten=array("[DATE]","[NAME]","[PRICE]");
					$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$deliname,$deliprice);
					$msg_mem_delivery=str_replace($patten,$replace,$msg_mem_delivery);
					$msg_mem_delivery=addslashes($msg_mem_delivery);

					$fromtel=$rowsms->return_tel;
					$date=0;
					if($rowsms->mem_delinum=="Y" && ord($deli_name) && ord($deli_num)) {	//송장안내메세지
						$etcmsg="송장안내메세지(회원)";
						$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_delinum, $etcmsg);
					}
					if($rowsms->mem_delivery=="Y") {	//상품발송메세지
						$etcmsg="상품발송메세지(회원)";
						$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_delivery, $etcmsg);
					}
				}
				pmysql_free_result($result);
				*/
			}
		}
	}
	echo "<script>";
	echo "	if(opener) {opener.location.reload();} ";
	//echo "	alert('발송처리 완료 되었습니다.');";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;

//KCP/올더게이트/이니시스/NICEPAY - 상품배송 후 취소요청이 있을 경우 우선 정산보류 상태로 돌린다. (반송 완료 후 취소처리 가능)
} elseif($type=="okhold" && ord($ordercode) && ($pg_type=="A" || $pg_type=="C" || $pg_type=="D" || $pg_type=="G")) {
	if(strstr("YD", $_ord->deli_gbn) && strlen($_ord->deli_date)==14) {
		if($pg_type=="A") {
			$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode=".$ordercode;
			$hold_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/hold.php",$query);
			$hold_data=substr($hold_data,strpos($hold_data,"RESULT=")+7);
			if (substr($hold_data,0,2)!="OK") {
				$tempdata=explode("|",$hold_data);
				$errmsg="정산보류 정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				alert_go($errmsg,-1);
			} else {
				$tempdata=explode("|",$hold_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					echo "<script> alert('{$errmsg}');</script>";
				}
			}

			$sql = "UPDATE tblorderinfo SET ";
			$sql.= "deli_gbn		= 'H' WHERE ordercode='{$ordercode}' ";
			if(pmysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
				$sql.= "WHERE ordercode='{$ordercode}' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				pmysql_query($sql,get_db_conn());
			}
		} elseif($pg_type=="C") {
			$query="sitecd={$pgid_info["ID"]}&ordercode=".$ordercode;
			$hold_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/hold.php",$query);
			$hold_data=substr($hold_data,strpos($hold_data,"RESULT=")+7);
			if (substr($hold_data,0,2)!="OK") {
				$tempdata=explode("|",$hold_data);
				$errmsg="정산보류 처리가 정상 완료하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				alert_go($errmsg,-1);
			} else {
				$tempdata=explode("|",$hold_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					echo "<script> alert('{$errmsg}');</script>";
				}
			}

			$sql = "UPDATE tblorderinfo SET ";
			$sql.= "deli_gbn		= 'H' WHERE ordercode='{$ordercode}' ";
			if(pmysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
				$sql.= "WHERE ordercode='{$ordercode}' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				pmysql_query($sql,get_db_conn());
			}
		} elseif($pg_type=="D") {
			$query="sitecd={$pgid_info["EID"]}&ordercode={$ordercode}&curgetid=".$_ShopInfo->getId();
			$hold_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/hold.php",$query);
			$hold_data=substr($hold_data,strpos($hold_data,"RESULT=")+7);
			if (substr($hold_data,0,2)!="OK") {
				$tempdata=explode("|",$hold_data);
				$errmsg="정산보류 처리가 정상 완료하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				alert_go($errmsg,-1);
			} else {
				$tempdata=explode("|",$hold_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					echo "<script> alert('{$errmsg}');</script>";
				}
			}

			$sql = "UPDATE tblorderinfo SET ";
			$sql.= "deli_gbn		= 'H' WHERE ordercode='{$ordercode}' ";
			if(pmysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
				$sql.= "WHERE ordercode='{$ordercode}' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				pmysql_query($sql,get_db_conn());
			}
		} elseif($pg_type=="G") {
			$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode=".$ordercode;
			$hold_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/hold.php",$query);
			$hold_data=substr($hold_data,strpos($hold_data,"RESULT=")+7);
			if (substr($hold_data,0,2)!="OK") {
				$tempdata=explode("|",$hold_data);
				$errmsg="정산보류 정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				alert_go($errmsg,-1);
			} else {
				$tempdata=explode("|",$hold_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					echo "<script> alert('{$errmsg}');</script>";
				}
			}

			$sql = "UPDATE tblorderinfo SET ";
			$sql.= "deli_gbn		= 'H' WHERE ordercode='{$ordercode}' ";
			if(pmysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
				$sql.= "WHERE ordercode='{$ordercode}' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				pmysql_query($sql,get_db_conn());
			}
		}
		$isupdate=true;
	}
//수량복구, 주문취소
} elseif(($type=="recancel" || $type=="recoveryquan" || $type=="recoveryquan_all" || $type=="recoverycan_all") && ord($ordercode)) {
	// recancel - 매매보호, recoveryquan - 취소시 수량복구함(선택상품), recoveryquan_all - 취소시 수량복구함(전체상품),  recoverycan_all - 0 - 취소시 수량복구안함(전체상품)

	$canmess	= "";

	if($c_idxs){ //  상품 IDX가 있을경우
		$idxs = $c_idxs;
	} else {
		$sql = "SELECT * FROM tblorderproduct WHERE ordercode='{$ordercode}'";
		$result=pmysql_query($sql,get_db_conn());

		while ($row=pmysql_fetch_object($result)) {
			$idx[]	= $row->idx;
		}
		pmysql_free_result($result);
		$idxs = implode("|",$idx);
	}

	/*if (strstr("Q", $_ord->paymethod[0])) {
		if ($_ord->oi_step1 == 0) {
			//KCP에 정산보류 전달.

			$pgid_info=GetEscrowType($_shopdata->escrow_id);
			$pg_type=$pgid_info["PG"];
			$pg_type=trim($pg_type);

			// 발급계좌 해지로 보낸다.
			$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode=".$ordercode;
			$cancel_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/escrow_cancel.php",$query);
			$cancel_data=substr($cancel_data,strpos($cancel_data,"RESULT=")+7);
			if (substr($cancel_data,0,2)!="OK") {
				$tempdata=explode("|",$cancel_data);
				$errmsg="정산보류 정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					$canmess = $errmsg;
				}
			} else {
				$tempdata=explode("|",$cancel_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					//$alert_text = $errmsg;
				}
			}
		}
	}*/

	if ($canmess == '') {

		//신규상태 변경 또는 반품 신청(2016.02.15 김재수 추가)
		orderCancel($exe_id, $ordercode, $idxs, $_ord->oi_step1, $_ord->oi_step2, $c_op_step, $_ord->paymethod[0], $c_code, $c_memo, $c_bankcode, $c_bankaccount, $c_bankuser, $c_bankusertel, $c_re_type, '', '', '', '', '', $c_pgcancel_type, $c_pgcancel_res_code, $c_pgcancel_res_msg);

		//exit;

		if(strstr("BOQ", $_ord->paymethod[0]) && $tax_type=="Y") {	//현금영수증 자동 발행 - 주문취소
			$sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='{$ordercode}' AND type='Y' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);
			if($row->cnt>0) {
				$flag="C";
				include($Dir."lib/taxsave.inc.php");
			}
		}


		if (strstr("Q", $_ord->paymethod[0]) && strlen($_ord->bank_date)==14 && ($pg_type!="C" && $pg_type!="D")) {
			$deliupdate =" deli_gbn='E' ";	//환불대기
			$up_deli_gbn="E";
		} elseif (strstr("CP", $_ord->paymethod[0])) {
			$deliupdate = " deli_gbn='C' ";
			if ($type=="recoveryquan_all") {
				$deliupdate .= ", pay_admin_proc='C' ";
			}
			$up_deli_gbn="C";
		} else {
			if ($_ord->oi_step1 == '0') {
				$deliupdate = " deli_gbn='C' ";
				$up_deli_gbn="C";
			} else {
				$deliupdate = " deli_gbn='E' ";
				$up_deli_gbn="E";
			}
		}

		list($op_dg_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_dg_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $idxs)."') AND deli_gbn != '{$up_deli_gbn}'"));

		if ($op_dg_cnt == 0) {
			if($type=="recoveryquan" ||$type=="recoveryquan_all") {
				if($_ord->del_gbn=="Y") $okdel="R";
				else $okdel="A";
			} elseif($type=="recoverycan_all") {
				$okdel=$_ord->del_gbn;
			}

			$sql = "UPDATE tblorderinfo SET {$deliupdate} ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			pmysql_query($sql,get_db_conn());
		}



		$sql = "UPDATE tblorderproduct SET deli_gbn='{$up_deli_gbn}' ";
		$sql.= "WHERE ordercode='{$ordercode}' AND idx IN ('".str_replace("|", "','", $idxs)."') ";
		//$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
		pmysql_query($sql,get_db_conn());

		if($type=="recoveryquan" || $type=="recoveryquan_all"){ //수량복구 실행
			//if (strstr("C", $_ord->paymethod[0])) {
			//	$canmess="alert('해당 주문을 취소하였으며, 수량을 복구하였습니다.');";
			//} else {


				if ($_ord->oi_step1 > 0 && ((strstr("CV", $_ord->paymethod[0]) && $c_pgcancel_type == '1') || strstr("G", $_ord->paymethod[0]))) { // 카드, 계좌이체 또는 임직원 포인트일 경우

					//취소건에 대해 불러온다.
					$oc_sql = "SELECT idx, oc_no from tblorderproduct WHERE ordercode='{$c_ordercode}' AND idx IN ('".str_replace("|", "','", $idxs)."') order by oc_no";
					$oc_res = pmysql_query($oc_sql,get_db_conn());
					$oc_no_arr	= array();
					while($oc_row = pmysql_fetch_array($oc_res)){
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
						$c_idxs					= $oc_val['idx'];
						$rfee						= "";

						//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
						/*list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND oc_no != '".$oc_no."' AND deli_gbn != 'C' AND op_step != '44'"));
						if ($op_deli_gbn_cnt == 0) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
							//PG취소가 완료된 수수료를 가져온다.
							list($op_rfee_amt)=pmysql_fetch_array(pmysql_query("select SUM(rfee) as op_rfee_amt from tblorder_cancel WHERE ordercode='".trim($ordercode)."' AND pgcancel = 'Y' GROUP BY ordercode"));
							if (($op_rfee_amt + $rfee) == 0) { // 수수료가 없으면 PG결제상태를 취소로 변경한다.
								$sql = "UPDATE tblorderinfo SET pay_admin_proc='C' ";
								$sql.= "WHERE ordercode='".trim($ordercode)."' ";
								pmysql_query($sql,get_db_conn());
							}
						}				*/

						//환불 금액을 가져온다. - 상품당 금액 가져오는 부분
						list($sum_price)=pmysql_fetch_array(pmysql_query("select SUM( ((price + option_price) * option_quantity) - coupon_price - use_point + deli_price ) AS sum_price from tblorderproduct  WHERE ordercode='{$ordercode}' and oc_no='{$oc_no}' group by ordercode"));

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

						orderCancelFin($exe_id, $ordercode, $c_idxs, $oc_no, '', '', '', '', $rfee, '' );

						//ERP로 환불완료데이터를 보낸다.
						sendErporderCancel($ordercode, $oc_no, $c_idxs);

						$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
						$sql.= "WHERE ordercode='".trim($ordercode)."' ";
						$sql.= "AND idx IN ('".str_replace("|", "','", $c_idxs)."') ";
						//echo $sql;

						if(pmysql_query($sql,get_db_conn())) {
							//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
							list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."'AND idx NOT IN ('".str_replace("|", "','", $c_idxs)."') AND deli_gbn != 'C' AND op_step != '44'"));
							if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
								//PG취소가 완료된 수수료를 가져온다.
								list($op_rfee_amt)=pmysql_fetch_array(pmysql_query("select SUM(rfee) as op_rfee_amt from tblorder_cancel WHERE ordercode='".trim($ordercode)."' AND pgcancel = 'Y' GROUP BY ordercode"));
								$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
								if (($op_rfee_amt + $rfee) == 0) { // 수수료가 없으면 PG결제상태를 취소로 변경한다.
									$sql .= ", pay_admin_proc='C' ";
								}
								$sql .= "WHERE ordercode='".trim($ordercode)."' ";
								//echo $sql;
								pmysql_query($sql,get_db_conn());
							}
						}
						sms_autosend( 'mem_refund', $ordercode, $oc_no, '' );
						sms_autosend( 'admin_refund', $ordercode, $oc_no, '' );
					}

					if($c_idxs){
						$canmess="alert('선택상품 취소가 완료되었습니다.');";
					} else {
						$canmess="alert('해당 주문상품 취소가 완료되었습니다.');";
					}
				} else {
					if($c_idxs){
						$canmess="alert('선택상품 취소가 요청되었습니다.');";
					} else {
						$canmess="alert('해당 주문상품 취소가 요청되었습니다.');";
					}
				}
			//}

			$log_content = "## 상품수량 복구 + 주문취소 ## - 주문번호 : ".$ordercode." / - 상품 IDX : ".$idxs;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		} else {
			$canmess="alert('해당 주문을 취소하였습니다.');";

			$log_content = "## 주문취소 ## - 주문번호 : ".$ordercode." / - 상품 IDX : ".$idxs;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		}

		$isupdate=true;
	}

	echo "<script>";
	echo $canmess;
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;

//회원이 사용한 적립금 복구처리 후 주문서 취소처리
} elseif($type=="recoveryres" && ord($ordercode)) {
	if($_ord->deli_gbn!="C" && $_ord->reserve>0 && ord($_ord->id)) {
		if($_ord->deli_gbn!="C" && ord($_ord->bank_date) && strstr("Q", $_ord->paymethod[0])) {
			//가상계좌 에스크로일 경우
			alert_go('가상계좌 에스크로 입금건은 취소 후 수기로 적립금을 처리하셔야 합니다.',-1);
		}

		$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		if(pmysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
			pmysql_query($sql,get_db_conn());
		}
		$isupdate=true;

		//포인트를 수정한다.(2015.11.25 - 김재수 추가)
		$mem_auth_type	= getAuthType($_ord->id);
		if ($mem_auth_type != 'sns') { // 정회원일 경우에만 지급
			if ($_ord->reserve != 0) insert_point_act($_ord->id, $_ord->reserve, "주문 $ordercode 취소건에 대한 포인트 환원", '','', date('YmdHis'), $return_point_term);
		}

		/*$sql = "UPDATE tblmember SET reserve=reserve+{$_ord->reserve} ";
		$sql.= "WHERE id='{$_ord->id}' ";
		pmysql_query($sql,get_db_conn());

		$sql = "INSERT INTO tblreserve(
		id		,
		reserve		,
		reserve_yn	,
		content		,
		orderdata	,
		date) VALUES (
		'{$_ord->id}',
		'{$_ord->reserve}',
		'Y',
		'주문 취소건에 대한 포인트 환원',
		'{$ordercode}={$_ord->price}',
		'".date("YmdHis")."')";
		pmysql_query($sql,get_db_conn());*/

		$log_content="## 회원 포인트 환원 ## - 주문번호 : {$ordercode} - 포인트 ".$_ord->reserve;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

//주문취소로 인한 회원에게 지급했던 포인트 취소처리
} elseif($type=="recoveryrecan" && ord($ordercode)) {
	$canreserve=$_POST["canreserve"];
	if(strstr("YDH",$_ord->deli_gbn) && strlen($_ord->deli_date)==14 && $canreserve>0 && ord($_ord->id)) {

		//포인트를 수정한다.(2015.11.25 - 김재수 추가)
		$mem_auth_type	= getAuthType($_ord->id);
		if ($mem_auth_type != 'sns') { // 정회원일 경우에만 지급
			if ($canreserve != 0) insert_point_act($_ord->id, $canreserve*-1, "주문 $ordercode 취소건에 대한 포인트 지급취소", '','', date('YmdHis'), $return_point_term);
		}

		/*$sql = "UPDATE tblmember SET reserve=reserve-{$canreserve} ";
		$sql.= "WHERE id='{$_ord->id}' ";
		pmysql_query($sql,get_db_conn());

		$sql = "INSERT INTO tblreserve(
		id		,
		reserve		,
		reserve_yn	,
		content		,
		orderdata	,
		date) VALUES (
		'{$_ord->id}',
		'-$canreserve',
		'Y',
		'주문 취소건에 대한 포인트 지급취소',
		'{$ordercode}={$_ord->price}',
		'".date("YmdHis")."')";
		pmysql_query($sql,get_db_conn());

		$sql = "INSERT INTO tblorderproduct(
		ordercode	,
		tempkey		,
		productcode	,
		productname	,
		quantity	,
		reserve		,
		date) VALUES (
		'{$ordercode}',
		'{$_ord->tempkey}',
		'99999999999R',
		'주문 취소건에 대한 포인트 지급취소',
		'1',
		'-$canreserve',
		'".date("Ymd")."')";
		pmysql_query($sql,get_db_conn());*/

		$log_content="## 회원 포인트 지급취소 ## - 주문번호 : {$ordercode} - 포인트 -".$canreserve;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

//메모 업데이트
} elseif($type=="memoupdate" && ord($ordercode)) {
//	$memo0=$_POST["memo0"];
//	$memo1=$_POST["memo1"];
//	$memo2=$_POST["memo2"];

	$receiver_name = $_POST["receiver_name"];
	$receiver_tel1 = $_POST["receiver_tel1"];
	$receiver_tel2 = $_POST["receiver_tel2"];
	$memo[0] = $_POST["memo0"];
	$memo[1] = $_POST["memo1"];
	$memo[2] = $_POST["memo2"];

	$pay_data = $_POST["pay_data"];
	$pay_data_query = $pay_data?",pay_data='{$pay_data}'":"";
	$order_msg=implode("[MEMO]",$memo);
//	$order_msg.="[MEMO]".$memo1;
//	if(strlen(trim($memo2))!=0) $order_msg.="[MEMO]".$memo2;
	pmysql_query("UPDATE tblorderinfo SET order_msg='{$order_msg}', receiver_name='{$receiver_name}', receiver_tel1='{$receiver_tel1}', receiver_tel2='{$receiver_tel2}' {$pay_data_query} WHERE ordercode='{$ordercode}'",get_db_conn());
	$isupdate=true;

//무통장입금 결제시 수량/적립금/가격 정보 변경
} elseif($type=="orderupdate" && ord($ordercode)) {
	$curdate = date("YmdHis");
	$vender=(int)$_POST["vender"];
	$productcode=$_POST["productcode"];
	$opt1_name=$_POST["opt1_name"];
	$opt2_name=$_POST["opt2_name"];
	$reserve=(int)$_POST["reserve"];
	$price=(int)$_POST["price"];
	$quantity=(int)$_POST["quantity"];
	$salereserve=(int)$_POST["salereserve"];
	$salemoney=(int)$_POST["salemoney"];
	$usereserve=(int)$_POST["usereserve"];
	$deli_price=(int)$_POST["deli_price"];
	$sumprice=(int)$_POST["sumprice"];

	if(strlen($productcode)==12 || strlen($productcode)==18) {
		if(strlen($productcode)==18 || substr($productcode,-4)=="GIFT") {
			if ($quantity<1) {
				echo "<script> alert ('수량은 1보다 큰 숫자로 입력해 주셔야 합니다.');history.back();</script>\n";
				exit;
			}
			$setprice=ceil($price/$quantity);
			$setreserve=ceil($reserve/$quantity);
		} else {
			$setprice=$price;
			$quantity=1;
		}
		$sql = "UPDATE tblorderproduct SET ";
		$sql.= "quantity	= '{$quantity}', ";
		$sql.= "price		= '{$setprice}', ";
		$sql.= "reserve		= '{$setreserve}' ";
		$sql.= "WHERE vender='{$vender}' ";
		$sql.= "AND ordercode='{$ordercode}' AND productcode='{$productcode}' ";
		$sql.= "AND opt1_name='{$opt1_name}' AND opt2_name='{$opt2_name}' ";

	} elseif($productcode==2) { //그룹회원 할인/적립
		if($salereserve>0) $tempdc_price=$salereserve;
		else $tempdc_price=$salemoney;
		$sql = "UPDATE tblorderinfo SET dc_price='{$tempdc_price}' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
	} elseif($productcode==3) { //적립금 사용액
		$sql = "UPDATE tblorderinfo SET reserve='{$usereserve}' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
	} elseif($productcode==4) { //배송료
		$sql = "UPDATE tblorderinfo SET deli_price='{$deli_price}' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
	} elseif($productcode==5) { //전체금액
		$sql = "UPDATE tblorderinfo SET price='{$sumprice}' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
	}
	if(pmysql_query($sql,get_db_conn())) {
		if($productcode=="99999999990X") {	//배송료일 경우
			$sql = "SELECT SUM(price) as in_deli_price FROM tblorderproduct ";
			$sql.= "WHERE ordercode='{$ordercode}' AND productcode='99999999990X' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);

			$in_deli_price=$row->in_deli_price;
			$sql = "UPDATE tblorderinfo SET deli_price='{$in_deli_price}' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			pmysql_query($sql,get_db_conn());
		}
	}
	$isupdate=true;

	$log_content = "## 주문상품 변경 ## - 주문번호 : {$ordercode} - 상품코드 : {$productcode} - 수량 : {$quantity} - 가격 : {$setprice} - 적립금 : ".$reserve;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

//무통장입금 결제시 상품/정보 삭제
} elseif($type=="orderdelete" && ord($ordercode)) {
	$curdate = date("YmdHis");
	$vender=(int)$_POST["vender"];
	$productcode=$_POST["productcode"];
	$opt1_name=$_POST["opt1_name"];
	$opt2_name=$_POST["opt2_name"];

	if(strlen($productcode)==12 || strlen($productcode)==18) {
		$sql = "DELETE FROM tblorderproduct WHERE vender='{$vender}' ";
		$sql.= "AND ordercode='{$ordercode}' AND productcode='{$productcode}' ";
		$sql.= "AND opt1_name='{$opt1_name}' AND opt2_name='{$opt2_name}' ";
	} elseif($productcode==2) { //그룹회원 할인/적립
		$sql = "UPDATE tblorderinfo SET dc_price=0 WHERE ordercode='{$ordercode}' ";
	} elseif($productcode==3) { //적립금 사용액
		$sql = "UPDATE tblorderinfo SET reserve=0 WHERE ordercode='{$ordercode}' ";
	} elseif($productcode==4) { //배송료
		$sql = "UPDATE tblorderinfo SET deli_price=0 WHERE ordercode='{$ordercode}' ";
	}
	if(pmysql_query($sql,get_db_conn())) {
		if($productcode=="99999999990X") {	//배송료일 경우
			$sql = "SELECT SUM(price) as in_deli_price FROM tblorderproduct ";
			$sql.= "WHERE ordercode='{$ordercode}' AND productcode='99999999990X' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);

			$in_deli_price=$row->in_deli_price;
			$sql = "UPDATE tblorderinfo SET deli_price='{$in_deli_price}' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			pmysql_query($sql,get_db_conn());
		}
	}
	$isupdate=true;

	$log_content = "## 주문상품 삭제 ## - 주문번호 : {$ordercode} - 상품코드 : ".$productcode;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

//선택 상품별 처리상태 변경 - 사용안함 (2016.02.12 - 김재수 막음)
/*} elseif($type=="deligbnup" && ord($ordercode) && ord($_POST["idxs"]) && strstr("NSY",$_POST["deli_gbn"]) && strstr("NXS",$_ord->deli_gbn)) {
	$idxs=$_POST["idxs"];
	$deli_gbn=$_POST["deli_gbn"];
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/deligbnup_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";

	$idxs=rtrim($idxs,',');
	$idx=str_replace(',','\',\'',$idxs);
	$sql = "UPDATE tblorderproduct SET deli_gbn='{$deli_gbn}', ";
	if($deli_gbn=="Y") $sql.= "deli_date='".date("YmdHis")."' ";
	else $sql.= "deli_date=NULL ";
	$sql.= "WHERE ordercode='{$ordercode}' AND idx IN ('{$idx}') ";
	$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	if(pmysql_query($sql,get_db_conn())) {
		if($_ord->deli_gbn!=$deli_gbn) {
			$rescode=getDeligbn_detail($ordercode,$deli_gbn);
			if(ord($rescode)) {
				$isupdate=true;
			}
		}
	}
	$sql = "UPDATE tblorderinfo SET deli_gbn = 'S' ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	pmysql_query($sql,get_db_conn());
	$outText.= " 주문 번호     : ".$ordercode."\n";
	$outText.= " 상품 idx     : ".$idxs."\n";
	$outText.= " 배송상태 변경 : 발송준비( deli_gbn = S )\n";
	$outText.= "\n";
	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'deligbnup_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."deligbnup_".date("Ymd").".txt",0777);
	echo "<script>";
	echo "	if(opener) {opener.location.reload();} ";
	//echo "	alert('발송처리 완료 되었습니다.');";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;*/

}else if($type == "clameupdate" && ord($ordercode)){

	list($sabang_flag)=pmysql_fetch("SELECT sabang_flag FROM tblorderclame WHERE no = '".$clameno."'");
	if($sabang_flag == 'N'){
		pmysql_query("UPDATE tblorderclame SET sabang_flag = 'Y' WHERE no = '".$clameno."'", get_db_conn());
	}else{
		pmysql_query("UPDATE tblorderclame SET sabang_flag = 'N' WHERE no = '".$clameno."'", get_db_conn());
	}

} elseif($type=="deli_finish" && ord($ordercode) && ord($_POST["deli_idxs"])){//배송완료_구매확정 (원재) 추가 // (2016.02.18 - 김재수 수정)
	//exdebug($_POST);
	//exit;
	$deli_idxs=$_POST["deli_idxs"];
	$qryErr = 0;
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/deli_finish_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$deliinfo=rtrim($deli_idxs,',');
	$ardeli=explode("|",$deliinfo);
	$idxs ="";
	$idxs_cnt	= 0;
	for($i=0;$i<count($ardeli);$i++) {
		$idx=$deli_com=$deli_num="";
		$prinfo=explode(",",$ardeli[$i]);
		for($j=0;$j<count($prinfo);$j++) {
			if (substr($prinfo[$j],0,5)=="IDXS=") $idx=trim(substr($prinfo[$j],5));
			elseif (substr($prinfo[$j],0,13)=="DELI_RESERVE=") $deli_reserve=trim(substr($prinfo[$j],13));
		}

		if(strlen($idx) > 0) {
			if ($deli_reserve !='') {
				$sql = "UPDATE tblorderproduct SET receive_ok = '1' ,deli_gbn='F' ";
				$sql.= "WHERE ordercode='{$ordercode}' AND idx='{$idx}' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				$sql.= "AND op_step < 40 AND order_conf = '0' ";
				//echo $sql."<br>";
				pmysql_query($sql,get_db_conn());
				if( !pmysql_error() ){

					// 신규상태 변경 추가 - (2016.02.18 - 김재수 추가)
					orderProductStepUpdate($exe_id, $ordercode, $idx, '4'); // 배송완료

					#배송정보 변경 LOG
					$outText.= " 주문 번호     : ".$ordercode."\n";
					$outText.= " 상품 IDX     : ".$idx."\n";
					$outText.= " 송장 번호     : ".$deli_num."\n";
					$outText.= " 배송회사 코드 : ".$deli_com."\n";
					$outText.= " 배송상태 변경 : 배송완료\n";
					if ($idxs_cnt > 0) $idxs .= "|";
					$idxs		.= $idx;
					$idxs_cnt++;

					list($op_up_check_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='{$ordercode}' AND idx='{$idx}' AND op_step = '4' AND order_conf = '0' "));
						//echo "select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='{$ordercode}' AND idx='{$idx}' AND op_step = '4' AND order_conf = '0'";
					if ($op_up_check_cnt > 0) {
						$sql = "UPDATE tblorderproduct SET order_conf = '1', order_conf_date='".date('YmdHis')."' ";
						$sql.= "WHERE ordercode='{$ordercode}' AND idx='{$idx}' ";
						$sql.= "AND op_step = '4' AND order_conf = '0' ";
						//echo $sql."<br>";
						pmysql_query($sql,get_db_conn());
						if( !pmysql_error() ){
							list($deli_reserve)=pmysql_fetch_array(pmysql_query("select reserve from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx IN ('".str_replace("|", "','", $idx)."') "));

							//적립 예정 포인트을 지급한다.
							$mem_auth_type	= getAuthType($_ord->id);
							if ($mem_auth_type != 'sns') { // 정회원일 경우에만 지급
								if ($deli_reserve != 0) insert_point_act($_ord->id, $deli_reserve, "주문 ".$ordercode." 배송완료(".count($idx)."건)에 의한 포인트 지급", '', '', date('YmdHis'), $return_point_term);
							}

							#배송정보 변경 LOG
							$outText.= " 주문 번호     : ".$ordercode."\n";
							$outText.= " 상품 IDX     : ".$idx."\n";
							$outText.= " 구매확정     : 완료\n";
							$outText.= " 구매확정일 : ".date('YmdHis')."\n";
							if ($idxs_cnt > 0) $idxs .= "|";
							$idxs		.= $idx;
							$idxs_cnt++;
						} else {
							$outText.= " 주문 번호     : ".$ordercode."\n";
							$outText.= " 상품 IDX     : ".$idx."\n";
							$outText.= " 구매확정     : ERR\n";
							$outText.= " 구매확정일 : -\n";
							$qryErr++;
						}
					} else {
						$outText.= " 주문 번호     : ".$ordercode."\n";
						$outText.= " 상품 IDX     : ".$idx."\n";
						$outText.= " 구매확정     : 이미완료됨\n";
						$outText.= " 구매확정일 : -\n";
					}
				} else {
					$outText.= " 주문 번호     : ".$ordercode."\n";
					$outText.= " 상품 IDX     : ".$idx."\n";
					$outText.= " 송장 번호     : ".$deli_num."\n";
					$outText.= " 배송회사 코드 : ".$deli_com."\n";
					$outText.= " 배송상태 변경 : ERR \n";
					$qryErr++;
				}
			} else {
				$outText.= " 주문 번호     : ".$ordercode."\n";
				$outText.= " 상품 IDX     : ".$idx."\n";
				$outText.= " 송장 번호     : ".$deli_num."\n";
				$outText.= " 배송회사 코드 : ".$deli_com."\n";
				$outText.= " 배송상태 변경 : ERR \n";
				$qryErr++;
			}
		}
	}
	if( $qryErr == 0 ){
		$add_sql	= "";
		if ($idxs) $add_sql	= " AND idx NOT IN ('".str_replace("|", "','", $idxs)."') ";
		//주문중 배송완료, 취소완료상태가 아닌경우
		list($op_idx_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' {$add_sql} AND ((op_step != '4' OR (op_step = '4' AND order_conf !='1')) AND op_step != '44' "));
		//echo $op_idx_cnt;
		if ($op_idx_cnt == 0) {
			$sql = "UPDATE tblorderinfo SET receive_ok = '1', deli_gbn = 'F', order_conf = '1', order_conf_date='".date('YmdHis')."' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			//echo $sql."<br>";
			pmysql_query($sql,get_db_conn());
		}
	}
	//exit;

	$outText.= "\n";
	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'deli_finish_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."deli_finish_".date("Ymd").".txt",0777);

	echo "<script>";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;

} elseif($type=="order_finish" && ord($ordercode) && ord($_POST["deli_idxs"])){//구매확정 추가 (2016.02.18 - 김재수 수정)
	//exdebug($_POST);
	//exit;
	$deli_idxs=$_POST["deli_idxs"];
	$qryErr = 0;
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/order_finish_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$deliinfo=rtrim($deli_idxs,',');
	$ardeli=explode("|",$deliinfo);
	$idxs ="";
	$idxs_cnt	= 0;
	for($i=0;$i<count($ardeli);$i++) {
		$idx=trim($ardeli[$i]);
		if(strlen($idx) > 0) {
			list($op_up_check_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='{$ordercode}' AND idx='{$idx}' AND op_step = '4' AND order_conf = '0' "));
				//echo "select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='{$ordercode}' AND idx='{$idx}' AND op_step = '4' AND order_conf = '0'";
			if ($op_up_check_cnt > 0) {
				$sql = "UPDATE tblorderproduct SET order_conf = '1', order_conf_date='".date('YmdHis')."' ";
				$sql.= "WHERE ordercode='{$ordercode}' AND idx='{$idx}' ";
				$sql.= "AND op_step = '4' AND order_conf = '0' ";
				//echo $sql."<br>";
				pmysql_query($sql,get_db_conn());
				if( !pmysql_error() ){
					list($deli_reserve)=pmysql_fetch_array(pmysql_query("select reserve from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx IN ('".str_replace("|", "','", $idx)."') "));

					//적립 예정 포인트을 지급한다.
					$mem_auth_type	= getAuthType($_ord->id);
					if ($mem_auth_type != 'sns') { // 정회원일 경우에만 지급
						if ($deli_reserve != 0) insert_point_act($_ord->id, $deli_reserve, "주문 ".$ordercode." 배송완료(".count($idx)."건)에 의한 포인트 지급", '', '', date('YmdHis'), $return_point_term);
					}

					#배송정보 변경 LOG
					$outText.= " 주문 번호     : ".$ordercode."\n";
					$outText.= " 상품 IDX     : ".$idx."\n";
					$outText.= " 구매확정     : 완료\n";
					$outText.= " 구매확정일 : ".date('YmdHis')."\n";
					if ($idxs_cnt > 0) $idxs .= "|";
					$idxs		.= $idx;
					$idxs_cnt++;
				} else {
					$outText.= " 주문 번호     : ".$ordercode."\n";
					$outText.= " 상품 IDX     : ".$idx."\n";
					$outText.= " 구매확정     : ERR\n";
					$outText.= " 구매확정일 : -\n";
					$qryErr++;
				}
			} else {
				$outText.= " 주문 번호     : ".$ordercode."\n";
				$outText.= " 상품 IDX     : ".$idx."\n";
				$outText.= " 구매확정     : 이미완료됨\n";
				$outText.= " 구매확정일 : -\n";
			}
		}
	}
	//exdebug($ardeli);
	//exit;
	if( $qryErr == 0 ){
		$add_sql	= "";
		if ($idxs) $add_sql	= " AND idx NOT IN ('".str_replace("|", "','", $idxs)."') ";
		//주문중 배송완료, 취소완료상태가 아닌경우
		list($op_idx_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' {$add_sql} AND ((op_step != '4' OR (op_step = '4' AND order_conf !='1')) AND op_step != '44') "));

		if ($op_idx_cnt == 0) {
			$sql = "UPDATE tblorderinfo SET order_conf = '1', order_conf_date='".date('YmdHis')."' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			//echo $sql."<br>";
			//exit;
			pmysql_query($sql,get_db_conn());
		}
	}

	$outText.= "\n";
	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'order_finish_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."order_finish_".date("Ymd").".txt",0777);

	echo "<script>";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;

//주문취소접수 복원 (2016.02.18 - 김재수 추가)
} elseif($type=="restore_cancel" && ord($ordercode) && ord($_POST["oc_no"])){
	$oc_no = $_POST["oc_no"];

	orderCancelRestore($exe_id, $ordercode, $oc_no);

	echo "<script>";
	echo "	alert('복원이 완료 되었습니다.');";
	echo "	if(opener) {opener.location.reload();} ";
	echo "	window.location.href = 'order_detail.php?ordercode={$ordercode}' ";
	echo "</script>";
	exit;
}

//debug($type."/".ord($ordercode)."/".ord($_POST["prcodes"])."/".strstr("NSY",$_POST["deli_gbn"])."/".strstr("NXS",$_ord->deli_gbn));

if($isupdate) {
	$sql="SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}'";
	$result=pmysql_query($sql,get_db_conn());
	$_ord=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(!$_ord) {
		echo "<script>alert(\"해당 주문내역이 존재하지 않습니다.\");window.close();</script>";
		exit;
	}
}

$prescd="N";
if(strstr("B", $_ord->paymethod[0])) {	//무통장
	if (ord($_ord->bank_date)) $prescd="Y";
} elseif(strstr("V", $_ord->paymethod[0])) {	//계좌이체
	if ($_ord->pay_flag=="0000") $prescd="Y";
} elseif(strstr("M", $_ord->paymethod[0])) {	//핸드폰
	if ($_ord->pay_flag=="0000") $prescd="Y";
} elseif(strstr("OQ", $_ord->paymethod[0])) {	//가상계좌
	if ($_ord->pay_flag=="0000" && ord($_ord->bank_date)) $prescd="Y";
} else {
	if ($_ord->pay_flag=="0000" && $_ord->pay_admin_proc=="Y") $prescd="Y";
}

$sql = "SELECT * FROM tblsmsinfo ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$smsok=true;
}
pmysql_free_result($result);

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	//$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo
	//ORDER BY id ASC ";

    $sql = "SELECT  a.vender,a.id,a.com_name, a.delflag, b.brandname 
            FROM    tblvenderinfo a 
            JOIN    tblproductbrand b on a.vender = b.vender 
            ORDER BY b.brandname
            ";

	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

$delicomlist=array();
$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$delicomlist[]=$row;
}
pmysql_free_result($result);

$curdate = date("YmdHi",strtotime('-30 min'));
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>주문상세내역 보기</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<STYLE TYPE="text/css">
<!--
body { font-size: 9pt}
td { font-size: 9pt; line-height: 15pt}
tr { font-size: 9pt}
.break {page-break-before: always;}
-->
</STYLE>
<script src="../js/jquery-1.10.1.min.js" type="text/javascript"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<SCRIPT LANGUAGE="javascript">
<!--
//document.onkeydown = CheckKeyPress;
//document.onkeyup = CheckKeyPress;
function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
			document.getElementById('zonecode').value = data.zonecode;
			document.getElementById('post1').value = data.postcode1;
			document.getElementById('post2').value = data.postcode2;
			document.getElementById('address1').value = data.address;
			document.getElementById('address1').focus();
			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
			//document.getElementById('addr').value = addr;


		}
	}).open();
}
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
	   event.keyCode = 0;
	   return false;
	 }
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 40;
	//var oHeight = document.all.table_body.clientHeight + 55;
	var oHeight=650;

	window.resizeTo(oWidth,oHeight);
}

var countdeli=countdelinum=countdecan=countbank=countbacan=countvican=counttrcan=countokcan=countokhold=0;

function PagePrint(){
	if(confirm("주문상세내역을 프린트 하시겠습니까?")) {
		print();
	}
}

function Sort(key){
	document.form2.sort.value=key;
	document.form2.type.value="sort";
	document.form2.submit();
}

function ProductInfo(code,prcode,popup) {
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.set.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=1500,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}
function ProductDetail(prcode) {
	window.open("/front/productdetail.php?productcode="+prcode,"productView","");
}
function ProductMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.primage"+cnt);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.visibility = "visible";
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	Obj = document.getElementById(Obj);
	Obj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}


function OrderUpdate(num,cnt,vender,productcode,opt1_name,opt2_name){
	if(confirm("해당 상품이나 내역을 수정하시겠습니까?\n해당 주문의 수량,금액 변경시 적립금, 할인금액, 총 합계도 관리자가 세팅해주셔야 합니다.")) {
		if(num==1) {
			document.form1.vender.value=vender;
			document.form1.productcode.value=productcode;
			document.form1.opt1_name.value=opt1_name;
			document.form1.opt2_name.value=opt2_name;
			document.form1.quantity.value=document.form1.arquantity[cnt].value;
			document.form1.reserve.value=document.form1.arreserve[cnt].value;
			document.form1.price.value=document.form1.arprice[cnt].value;
		} else {
			document.form1.productcode.value=num;
		}

		document.form1.mode.value="update";
		document.form1.type.value="orderupdate";
		document.form1.submit();
	}
}

function OrderDelete(num,vender,productcode,opt1_name,opt2_name){
	if(confirm("해당 상품이나 내역을 삭제하시겠습니까?")) {
		if(num==1) {
			document.form1.vender.value=vender;
			document.form1.productcode.value=productcode;
			document.form1.opt1_name.value=opt1_name;
			document.form1.opt2_name.value=opt2_name;
		} else {
			document.form1.productcode.value=num;
		}

		document.form1.mode.value="update";
		document.form1.type.value="orderdelete";
		document.form1.submit();
	}
}
//chr(30)처리를 위한 함수
 function chr(code)
{
    return String.fromCharCode(code);
}

//주문상품 취소하기 폼 오픈 및 정보 전달. (2016.02.03 - 김재수 추가)
function RestoreOrderCancel_chk(oi_step1, oi_step2, paymethod, re_type, exe_type) {
	//초기화 한다.
	$("#ocf").hide();
	$(".ocf_title").html("");
	$(".ocf_bank").show();
	document.orderCancelForm.type.value= exe_type;
	document.orderCancelForm.re_type.value= re_type;
	document.orderCancelForm.idxs.value="";
	document.orderCancelForm.sel_code.value="";
	document.orderCancelForm.memo.value="";
	document.orderCancelForm.bankcode.value="";
	document.orderCancelForm.bankaccount.value="";
	document.orderCancelForm.bankuser.value="";
	document.orderCancelForm.each_price.value="";
	document.orderCancelForm.pc_type.value="";
	document.orderCancelForm.op_step.value="";
	$(".cancelform_productlist").find("tr").remove();

	if (re_type =='C') { // 교환일 경우
		$(".ocf_title").html("선택상품 교환하기");
	} else {
		$(".ocf_title").html("선택상품 취소하기");
	}

	//if (re_type =='C' || document.orderCancelForm.paymethod.value == 'C') { // 바로취소(카드), 교환일 경우
    if (re_type =='C' || !(document.orderCancelForm.paymethod.value == 'B' || document.orderCancelForm.paymethod.value == 'O' || document.orderCancelForm.paymethod.value == 'Q' || document.orderCancelForm.paymethod.value == 'V')) { // 취소(카드,계좌이체), 교환일 경우
		$(".ocf_bank").hide();
	} else {
		$(".ocf_bank").show();
	}

	var t_cnt		= 0;
	var c_cnt	= 0;
	var each_price = 0;
	var op_step_cnt = 0;
	if(typeof(document.form1.chkprcode.length)=="number") {
		cnt=document.form1.chkprcode.length;
		$k = 0;
		for(i=1;i<cnt;i++){

			if(document.form1.chkprcode[i].checked) {
				var app_val	='Y';
			} else {
				if (exe_type == 'recoverycan_all' || exe_type == 'recoveryquan_all' || exe_type == 'redelivery_all') {
					var app_val	='Y';
				} else {
					var app_val	='N';
				}
			}
			if (app_val == 'Y')
			{
				if ($k == 0) {
					document.orderCancelForm.idxs.value = document.form1.idxs[i].value;
					document.orderCancelForm.op_step.value = document.form1.op_step[i].value;
				} else {
					document.orderCancelForm.idxs.value+= "|"+document.form1.idxs[i].value;
					if (document.orderCancelForm.op_step.value != document.form1.op_step[i].value)
					{
						op_step_cnt++;
					}
				}
				appHtml  = "<tr>\n";
				appHtml += "	<td align='center' bgcolor='#EFEFEF'>상품명</td>\n";
				appHtml += "	<td align='left'>"+document.form1.ord_productname[i].value+"</td>\n";
				appHtml += "	<td align='center' bgcolor='#EFEFEF'>옵션</td>\n";
				if (re_type =='C' && document.orderCancelForm.op_step.value != '2') { // 교환일 경우
					appHtml += "	<td align='left'><span class='sel_optionname'>"+document.form1.ord_optionname[i].value+"</span>";
					if (document.form1.ord_optionname[i].value != '')
					{
						appHtml += " <input type='button' value='변경' class='btn_blue' style='padding:2px 5px 1px' onclick=\"javascript:optionChange('"+document.orderCancelForm.ordercode.value+"','"+document.form1.idxs[i].value+"','"+document.form1.chkprcode[i].value+"','"+$k+"');\">";
					}
					appHtml += "<input type='hidden' name='sel_option1[]' value=\""+document.form1.ord_option1[i].value+"\"><input type='hidden' name='sel_option2[]' value=\""+document.form1.ord_option2[i].value+"\"><input type='hidden' name='sel_option_price_text[]' value=\""+document.form1.ord_option_price_text[i].value+"\">";
					appHtml += "<input type='hidden' name='sel_text_opt_subject[]' value=\""+document.form1.ord_text_opt_subject[i].value+"\"><input type='hidden' name='sel_text_opt_content[]' value=\""+document.form1.ord_text_opt_content[i].value+"\"></td>\n";
				} else {
					appHtml += "	<td align='left'>"+document.form1.ord_optionname[i].value+"</td>\n";
				}
				appHtml += "</tr>\n";
				$(".cancelform_productlist").append(appHtml);
				each_price	= each_price + parseInt(document.form1.each_price[i].value);

				$k++;
				c_cnt++;
			}
			t_cnt++;
		}
	} else {
		alert("상품이 존재하지 않습니다.");
		return;
	}

	if(op_step_cnt > 0) {
		alert("선택하신 상품의 주문상태가 다릅니다.\n같은 주문상태만 처리 가능합니다.");
		return;
	}

	if (document.orderCancelForm.oi_step1.value == '3' && document.orderCancelForm.op_step.value == '2') {
		if(!(document.orderCancelForm.paymethod.value == 'B' || document.orderCancelForm.paymethod.value == 'O'|| document.orderCancelForm.paymethod.value == 'Q'|| document.orderCancelForm.paymethod.value == 'V')) {
		    $(".ocf_bank").hide();
        }
		if (exe_type == 'redelivery') {
			exe_type = 'recoveryquan';
			document.orderCancelForm.type.value= exe_type;
			document.orderCancelForm.re_type.value= "";
			$(".ocf_title").html("선택상품 취소하기");
		}
	}

	if(document.orderCancelForm.idxs.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		return;
	} else {
		if (c_cnt == t_cnt)
		{
			document.orderCancelForm.pc_type.value = "ALL";
			if (exe_type == 'recoverycan' || exe_type == 'recoveryquan' || exe_type == 'redelivery') {
				document.orderCancelForm.type.value= exe_type+"_all";
			}
		} else {
			document.orderCancelForm.pc_type.value = "PART";
			document.orderCancelForm.each_price.value=each_price;
		}


		$("#ocf").show();
		if (exe_type == 'recoveryquan_all') {
			var position=$('#ocf').offset(); // 위치값
			$('html,body').animate({scrollTop:position.top},1); // 이동
		}
	}
}

//주문상품 옵션변경 팝업 (2016.02.05 - 김재수 추가)
function optionChange(ordercode, idx, productcode, place) {
	window.open("product_optionchange_pop.php?ordercode="+ordercode+"&idx="+idx+"&productcode="+productcode+"&place="+place,"optionpop","width=500,height=210,scrollbars=no");
}

//주문상품 취소하기 폼 SUBMIT (2016.02.04 - 김재수 추가)
function submitOrderCancel() {
	var confirm_t		= "취소";
	var oi_step1		= document.orderCancelForm.oi_step1.value;
	var op_step		= document.orderCancelForm.op_step.value;
	var paymethod	= document.orderCancelForm.paymethod.value;
	var oldordno	= document.orderCancelForm.oldordno.value;

	if(document.orderCancelForm.sel_code.value=='') {
		alert("사유를 선택해 주세요.");
		return;
	}

	if (document.orderCancelForm.re_type.value == 'C') { // 교환이 아닐경우
		confirm_t	="교환";
	} else {
		if (oi_step1 != '0')
		{
			//if (document.orderCancelForm.paymethod.value != 'C' ) // 반품시 결제방식이 카드가 아닌경우
            if (document.orderCancelForm.paymethod.value == 'B' || document.orderCancelForm.paymethod.value == 'O' || document.orderCancelForm.paymethod.value == 'Q' || document.orderCancelForm.paymethod.value == 'V' ) // 반품시 결제방식이 카드, 계좌이체가 아닌경우
			{
				if(document.orderCancelForm.bankcode.value=='') {
					alert("환불받으실 은행을 선택해 주세요.");
					return;
				}

				if(document.orderCancelForm.bankaccount.value=='') {
					alert("환불받으실 계좌번호를 입력해 주세요.");
					document.orderCancelForm.bankaccount.focus();
					return;
				}

				if(document.orderCancelForm.bankuser.value=='') {
					alert("환불받으실 예금주를 입력해 주세요.");
					document.orderCancelForm.bankuser.focus();
					return;
				}
			}
		}
	}

	if(confirm("선택상품을 "+confirm_t+" 하시겠습니까?")){
		//document.orderCancelForm.type.value= "recoveryquan";
			$(".sbmit_btn").html("처리중 입니다. 잠시만 기다려 주세요.");
			if (document.orderCancelForm.re_type.value == '' && (document.orderCancelForm.paymethod.value =='C' || document.orderCancelForm.paymethod.value =='V')) { // 카드, 계좌이체 결제일 경우에는 취소로 보낸이후에 취소로직으로 이동한다.
			<?php if($pg_type=="G"){?>
			var sitecd = '<?=$pgid_info["ID"]?>';
			var sitekey = '<?=$pgid_info["KEY"]?>';
			var sitepw = "<?=$pgid_info['PW']?>";
			$.post("<?=$Dir?>paygate/<?=$pg_type?>/cancel.ajax.php",{sitecd:sitecd, sitekey:sitekey, sitepw:sitepw, ordercode:ordercode, pc_type:pc_type,mod_mny:each_price},function(data){
				if(data.res_code !='N'){
					document.orderCancelForm.pgcancel_type.value			= data.type;
					document.orderCancelForm.pgcancel_res_code.value	= data.res_code;
					document.orderCancelForm.pgcancel_res_msg.value		= data.res_msg;
					document.orderCancelForm.submit();
				} else {
					alert(data.msg);
					document.orderCancelForm.pgcancel_type.value			= "";
					document.orderCancelForm.pgcancel_res_code.value	= "";
					document.orderCancelForm.pgcancel_res_msg.value		= "";
				}
			},"json");
			<?}?>
		} else {
			document.orderCancelForm.submit();
		}
	}
}

//주문취소시 - 사용안함
function RestoreOrder(temp,delivery,reserve) {
	var mess="주문취소";

	if(document.form2.recoveryquan.value=="Y") {
		alert(mess+"는 한 주문서에 대해서 한번만 가능합니다.");
		return;
	}

<?php
	/*if ($_ord->deli_gbn!="C" && strstr("CP",$_ord->paymethod[0]) && substr($_ord->ordercode,0,12)>$curdate) {
		echo "	alert(\"카드주문의 경우 주문시점에서 30분 경과 후 \"+mess+\"가 가능합니다.\\n\\n고객이 카드정보 입력중 일 수 있습니다.\");";
	} elseif($_ord->pay_admin_proc!="C" && $_ord->pay_flag=="0000" && strstr("C", $_ord->paymethod[0])) {
		echo "	alert(\"카드주문 취소는 먼저 카드취소 후 진행해 주세요.\");";
	} elseif($_ord->pay_admin_proc!="C" && $_ord->pay_flag=="0000" && strstr("M", $_ord->paymethod[0])) {
		echo "	alert(\"휴대폰주문 취소는 먼저 휴대폰결제 취소 후 진행해 주세요.\");";
	} else {	*/
?>
	/*if(delivery!="C" && reserve>0 && confirm("회원적립금을 먼저 취소하셔야 합니다.")) {
		RestoreReserve();
	} else*/
		if(temp=="quan" && confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까??<?=(strstr("PQ",$_ord->paymethod[0])?"\\n\\n매매보호 결제의 경우는 자동으로 결제도 취소가 됩니다.":"")?>")) {

			<?php if($pg_type=="A"){?>
				document.kcpform.action="<?=$Dir?>paygate/A/cancel.php";
				document.kcpform.submit();
				sitecd = document.kcpform.sitecd.value;
				sitekey = document.kcpform.sitekey.value;
				ordcode = document.kcpform.ordercode.value;
				$.post("../paygate/A/cancel.ajax.php",{mode:"pay_cancel_ok", sitecd:sitecd, sitekey:sitekey, ordercode:ordcode, pc_type:"ALL"},function(data){
					if(data.type == 1){
						document.form2.type.value="recoveryquan_all";
						document.form2.recoveryquan.value="Y";
						document.form2.submit();
					} else {
						alert(data.msg);
					}
				},"json");
			<?php }elseif($pg_type=="B"){?>
			<?php }elseif($pg_type=="C"){?>
			<?php } elseif($pg_type=="D"){?>
			<?php }?>

		} else if(temp=="can" && confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까??<?=(strstr("PQ",$_ord->paymethod[0])?"\\n\\n매매보호 결제의 경우는 자동으로 결제도 취소가 됩니다.":"")?>")) {
			document.form2.type.value="recoverycan_all";
			document.form2.recoveryquan.value="Y";
			document.form2.submit();
		}
<?php
	//}
?>
}

function RestoreReserve(){
    if(document.form2.recoveryrese.value=="Y"){
        alert("적립금 복구는 한 주문서에 대해서 한번만 가능합니다.");
        return;
    }
<?php
	if ($_ord->deli_gbn!="C" && strstr("CP",$_ord->paymethod[0]) && substr($_ord->ordercode,0,12)>$curdate) {
         echo "	alert(\"카드주문의 경우 주문시점에서 30분 경과 후 적립금 복구가 가능합니다.\\n\\n고객이 카드정보 입력중 일 수 있습니다.\");";
	} else {
?>
	if(confirm("회원 적립금이 자동으로 복구되며,\n\n해당주문은 주문최소됩니다.")){
		document.form2.type.value="recoveryres";
		document.form2.recoveryrese.value="Y";
		document.form2.submit();

	}
<?php	} ?>
}

function RestoreReserveCancel(temp) {
	if(document.form2.recoveryrecan.value=="Y") {
		alert("적립금 취소는 한 주문서에 대해서 한번만 가능합니다.");
		return;
	}
	if(confirm("상품 배송으로 인한 지급 적립금이 자동으로 취소됩니다.\n\n적립금 취소후 주문서는 반드시 취소상태로 변경하셔야 합니다.")) {
		document.form2.type.value="recoveryrecan";
		document.form2.canreserve.value=temp;
		document.form2.recoveryrecan.value="Y";
		document.form2.submit();
	}
}

function SendSMS(tel1,tel2,tel3) {
	number=tel1+"|"+tel2+"|"+tel3;
	document.smsform.number.value=number;
	window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
	document.smsform.submit();
}

function SendMail(mail) {
	try {
		opener.parent.topframe.ChangeMenuImg(3);
		opener.document.mailform.rmail.value=mail;
		opener.document.mailform.submit();
	} catch(e) {}
}

function HideMemo() {
	try {
		membermemo_layer.style.visibility="hidden";
	} catch (e) {}
}

function MemberMemo(id) {
	window.open("about:blank","memopop","width=350,height=350,scrollbars=no");
	document.formmemo.target="memopop";
	document.formmemo.id.value=id;
	document.formmemo.action="member_memopop.php";
	document.formmemo.submit();
	document.formmemo.target="";
	document.formmemo.action="<?=$_SERVER['PHP_SELF']?>";
}

function HideDisplay() {
	document.formhide.submit();
}

/**************************************************************************************************/
//영수증 발행
function printtax(){
	document.taxprintform.submit();
}
//현금영수증 요청
function get_taxsave() {
	window.open("about:blank","taxsavepop","width=275,height=220,scrollbars=no");
	document.taxsaveform.submit();
}
//현금영수증 요청 New
function get_taxsave2() {
	window.open("about:blank","taxsavepop","width=500,height=500,scrollbars=no");
	document.taxsaveform.action = "<?=$Dir.FrontDir?>taxsave2.php";
	document.taxsaveform.submit();
}

//운송장 출력
function printaddress(){
	alert("서비스 준비중입니다.");
}
//반송처리
function delicancel(){
	if(!countdecan){
		if(!confirm("반송처리 하시겠습니까?")) return;
		countdecan++;
		document.form2.type.value="redelivery";
		document.form2.submit();
	}
}
//카드/핸드폰 취소 버튼 막음 (2016.02.16 - 김재수 막음)
function card_ask(temp,caltype){
	//card_ask - 매입요청
	//card_cancel - 카드취소
	if(temp=="card_ask") {			//매입요청
		if(confirm("신용카드 매입요청을 하시겠습니까?")) {
<?php if($pg_type=="A"){?>
			document.kcpform.action="<?=$Dir?>paygate/A/edi.php";
			document.kcpform.submit();
<?php }elseif($pg_type=="B"){?>

<?php }elseif($pg_type=="C"){?>

<?php }elseif($pg_type=="D"){?>

<?php }?>
		}
	} else if(temp=="card_cancel") {//취소요청
<?php if($pg_type=="A"){?>
		if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
			document.kcpform.action="<?=$Dir?>paygate/A/cancel.php";
			document.kcpform.submit();
		}
<?php }elseif($pg_type=="B"){?>
		if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
			document.dacomform.action="<?=$Dir?>paygate/B/cancel.php";
			document.dacomform.submit();
		}
<?php }elseif($pg_type=="C"){?>
		if(caltype == "hp") {
			if(confirm("\n┏━━━━━━━━━━━━━━  【 주      의      사      항 】  ━━━━━━━━━━━━━━━━┓    \n┃                                                                                                                                    ┃    \n┃                                                                                                                                    ┃    \n┃       １. 휴대폰 결제 취소 처리는 쇼핑몰 DB에만 반영되며 올더게이트에 전달되지 않습니다.       ┃    \n┃                                                                                                                                    ┃    \n┃       ２. 올더게이트 휴대폰 결제 취소는 해당 ＰＧ사의 관리자페이지에서 처리 해 주세요.           ┃    \n┃                                                                                                                                    ┃    \n┃                                                                                                                                    ┃    \n┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛    \n\n                               결제취소처리는 쇼핑몰 DB에만 반영됩니다. 정말 하시겠습니까?")) {
				document.allthegateform.action="<?=$Dir?>paygate/C/cancel.php";
				document.allthegateform.submit();
			}
		} else {
			if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
				document.allthegateform.action="<?=$Dir?>paygate/C/cancel.php";
				document.allthegateform.submit();
			}
		}
<?php } elseif($pg_type=="D"){?>
		if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
			document.inicisform.action="<?=$Dir?>paygate/D/cancel.php";
			document.inicisform.submit();
		}
<?php }?>
	}
}

//발송준비, 배송완료 처리
function delisend(temp){
	if(!countdeli){
		if(temp=="S" && !confirm("발송준비 지시를 하시겠습니까?")) return;
		if(temp=="S") document.form2.type.value="readydeli";
		countdeli++;
		document.form2.submit();
	}
}

//상품별 발송준비 처리
function deliOneSend(idx){
	if(!countdeli){
		if(!confirm("발송준비 지시를 하시겠습니까?")) return;
		document.form2.type.value="readyOneDeli";
		document.form2.op_idx.value=idx;
		countdeli++;
		document.form2.submit();
	}
}
// 배송완료
function deliFin(deli_type) {
	if(typeof(document.form1.chkprcode.length)=="number") {
		document.form1.deli_idxs.value="";
		cnt=document.form1.chkprcode.length;
		var sel_cnt	= 0;
		for(i=1;i<cnt;i++){
			if(document.form1.chkprcode[i].checked) {
				var app_val	= "Y";
			} else {
				if (deli_type == 'ALL') {
					var app_val	= "Y";
				} else {
					var app_val	= "N";
				}
			}

			if (app_val == 'Y') {
				if (document.form1.op_step[i].value == '3')
				{
					if (sel_cnt > 0)
					{
						document.form1.deli_idxs.value+= "|";
					}
					document.form1.deli_idxs.value+="IDXS="+document.form1.idxs[i].value+","+"DELI_RESERVE="+document.form1.op_reserve[i].value;
					sel_cnt++;
				}
			}
		}
	} else {
		alert("배송 상품이 존재하지 않습니다.");
		return;
	}
	if(document.form1.deli_idxs.value.length==0) {
		alert("배송중인 상품이 없습니다.");
		return;
	}
	//alert(document.form1.deli_idxs.value);
	//return;

	if(confirm("배송중인 상품에만 적용됩니다.\n배송완료 처리를 하시겠습니까?")) {
		document.form1.type.value="deli_finish";
		document.form1.submit();
	}

}
// 구매확정
function orderFin(deli_type) {
	if(typeof(document.form1.chkprcode.length)=="number") {
		document.form1.deli_idxs.value="";
		cnt=document.form1.chkprcode.length;
		var sel_cnt	= 0;
		for(i=1;i<cnt;i++){
			if(document.form1.chkprcode[i].checked) {
				var app_val	= "Y";
			} else {
				if (deli_type == 'ALL') {
					var app_val	= "Y";
				} else {
					var app_val	= "N";
				}
			}

			if (app_val == 'Y') {
				if (document.form1.op_step[i].value == '4')
				{
					if (sel_cnt > 0)
					{
						document.form1.deli_idxs.value+= "|";
					}
					document.form1.deli_idxs.value+=document.form1.idxs[i].value;
					sel_cnt++;
				}
			}
		}
	} else {
		alert("배송 상품이 존재하지 않습니다.");
		return;
	}
	if(document.form1.deli_idxs.value.length==0) {
		alert("배송완료된 상품이 없습니다.");
		return;
	}
	//alert(document.form1.deli_idxs.value);
	//return;

	if(confirm("배송완료된 상품중 구매확정 되지않은 상품에만 적용됩니다.\n구매확정 처리를 하시겠습니까?")) {
		document.form1.type.value="order_finish";
		document.form1.submit();
	}

}
/*function delisend(temp){
	if(!countdeli){
		if(temp=="Y" && !confirm("입금확인이 안된 주문서입니다. 배송을 완료하시겠습니까?")) return;
		else if(temp=="S" && !confirm("발송준비 지시를 하시겠습니까?")) return;
		else if(temp=="N" && !confirm("배송을 완료하시겠습니까?")) return;

		if(temp == 'F'){
			if( confirm("배송완료 처리를 하시겠습니까?") ) {
				document.form2.type.value="deli_finish";
				document.form2.submit();
			}
		}

		if(temp=="S") document.form2.type.value="readydeli";
		else {

			tmpdelicom=document.escrow_form1.escrow_deli_com.value;
			tmpdelinum=document.escrow_form1.escrow_deli_num.value;
			tmpdeliname=document.escrow_form1.escrow_deli_com.options[document.escrow_form1.escrow_deli_com.selectedIndex].text;

			if(document.getElementById("deliescrow")) {
				if(document.getElementById("deliescrow").style.display == "none") {
					document.getElementById("deliescrow").style.display="";
					return;
				}
				if(document.escrow_form1.escrowcaltype.value=="Y" && (!tmpdelicom || !tmpdelinum || !tmpdeliname)) {
					alert("에스크로 배송완료 처리는 송장정보를 입력해야만 처리가 가능합니다.");
					return;
				}
			}

			if(confirm("\n1. 대표 송장정보 미입력할 경우 배송메일에서 송장정보가 출력되지 않습니다.\n"+"2. 대표 송장정보 미입력할 경우 송장번호 안내 SMS 는 발송되지 않습니다.\n\n          배송완료된 정보를 메일/SMS로 발송하시겠습니까?\n\n\n   * 배송업체 : "+tmpdeliname+"\n\n   * 송장번호 : "+tmpdelinum+"")) {
				document.form2.delimailtype.value="Y";
			} else {
				document.form2.delimailtype.value="N";
			}
			if(!confirm("정말 배송을 완료하시겠습니까?")) return;

			document.form2.deli_com.value=tmpdelicom;
			document.form2.deli_num.value=tmpdelinum;
			document.form2.deli_name.value=tmpdeliname;
			document.form2.type.value="delivery";
		}
		countdeli++;
		document.form2.submit();
	}
}*/
function escrow_deliclose()
{
	if(document.getElementById("deliescrow")) {
		if(document.getElementById("deliescrow").style.display == "") {
			document.escrow_form1.escrow_deli_com.selectedIndex=0;
			document.escrow_form1.escrow_deli_num.value="";
			document.getElementById("deliescrow").style.display = "none";
		}
	}
}

//무통장입금 완료처리
function banksend(){
	if(!countbank){
		if(!confirm("입금확인을 셋팅하시겠습니까?")) return;
		countbank++;
		document.form2.type.value="bank";
		document.form2.submit();
	}
}
//무통장 환불처리
function bankcancel(){
	if(!countbacan){
		if(!confirm("입금취소하시겠습니까?")) return;
		countbacan++;
		document.form2.type.value="bankcancel";
		document.form2.submit();
	}
}
//일반 가상계좌 환불처리
function virtualcancel() {
	if(!countvican){
		if(!confirm("가상계좌 입금건에 대해서 적립금 지급 또는 무통장으로 환불을 하셨습니까?")) return;
		countvican++;
		document.form2.type.value="virtualcancel";
		document.form2.submit();
	}
}
//실시간계좌이체 환불처리
function transcancel() {
	if(!counttrcan){
		if(!confirm("실시간계좌이체 결제건에 대해서 적립금 지급 또는 무통장으로 환불을 하셨습니까?")) return;
		counttrcan++;
		document.form2.type.value="transcancel";
		document.form2.submit();
	}
}

<?php if($pg_type=="A" || $pg_type=="C" || $pg_type=="D" || $pg_type=="G"){?>
//매매보호 정산보류
function okhold() {
	if(!countokhold) {
		if(!confirm("이미 배송된 매매보호 결제건에 대해서 정산보류 처리를 하시겠습니까?\n\n정산보류 처리 후 상품이 반송완료되면 최종 취소처리가 가능합니다.")) return;
		countokhold++;
		document.form2.type.value="okhold";
		document.form2.submit();
	}
}
<?php }?>

//매매보호 취소처리
function okcancel(temp,date) {
	if(!countokcan) {
		if(temp=="Q") {
			if(date.length>0) {
				<?php if($pg_type=="A"){?>
				if(!confirm("매매보호 주문에 대해서 취소처리 하시겠습니까?\n\n환불대기 상태후 금액이 환불되면 자동 주문 취소됩니다.")) return;
				<?php }elseif($pg_type=="B"){?>
					<?php if(strlen($_ord->deli_date)==14){?>
					alert("에스크로 환불처리는 LG데이콤 상관점관리에서 하시기 바랍니다.\n\n환불완료 후 쇼핑몰에 자동 반영됩니다."); return;
					<?php }?>
				<?php }elseif($pg_type=="C"){?>

				<?php }elseif($pg_type=="D"){?>

				<?php }?>
			} else {
				<?php if($pg_type=="A"){?>
				if(!confirm("매매보호 주문에 대해서 취소처리 하시겠습니까?\n\n입금전이므로 발급계좌는 소멸됩니다.")) return;
				<?php }elseif($pg_type=="B"){?>
				//if(!confirm("매매보호 주문에 대해서 취소처리 하시겠습니까?")) return;
				<?php }elseif($pg_type=="C"){?>

				<?php }elseif($pg_type=="D"){?>

				<?php }?>
			}
		}
		if(!confirm("매매보호 주문에 대해서 취소처리 하시겠습니까?")) return;
		countokcan++;
		document.form2.type.value="recancel";
		document.form2.submit();
	}
}
/**************************************************************************************************/


function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
}

function AddressUpdate(flag){
	var msg = "배송지를 해당 주소로 수정하시겠습니까?";
	if(!flag) msg += "\r주의 : 해당주문은 당일 배송이 포함되어 있습니다.";
	if(confirm(msg)) {
		document.form2.type.value="addressupdate";
		document.form2.submit();
	}
}

function DeliSearch(deli_url){
	//window.open(deli_url,"배송추적",'_blank ',"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");
	//새탭으로 변경
	opener.window.open(deli_url,"배송추적");
}

function changeDeliinfo() {
	if(typeof(document.form1.chkprcode.length)=="number") {
		document.form1.deli_idxs.value="";
		cnt=document.form1.chkprcode.length;
		for(i=1;i<cnt;i++){
			if(document.form1.chkprcode[i].checked) {
				if (document.form1.op_step[i].value == '2' || document.form1.op_step[i].value == '3') {
					document.form1.deli_idxs.value+="IDXS="+document.form1.idxs[i].value+",DELI_COM="+document.form1.chkdeli_com[i].value+",DELI_NUM="+document.form1.chkdeli_num[i].value+"|";
				}
			}
		}
	} else {
		alert("배송 상품이 존재하지 않습니다.");
		return;
	}

	if(document.form1.deli_idxs.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		return;
	}

	if(!confirm("선택된 상품의 배송정보를 수정하시겠습니까?")) {
		document.form1.delimailtype.value="N";
		return;
	}

	if(confirm("선택된 상품의 배송정보 변경내역을 메일/SMS로 발송하시겠습니까?"))
		document.form1.delimailtype.value="Y";
	else
		document.form1.delimailtype.value="N";
		document.form1.type.value="deliinfoup";
		document.form1.submit();

}

function DeliNumUpdate() {
	if(!countdelinum) {
		//요청에 의한 confirm창 삭제 2015 11 26 유동혁 // 송장 번호 업데이트시 필요함 다시 살림 (2016.02.12 - 김재수 풀었음)
		var deil_cnt	= 0;
		if(typeof(document.form1.chkprcode.length)=="number") {
			cnt=document.form1.chkprcode.length;
			for(i=1;i<cnt;i++){
				if (document.form1.op_step[i].value == '2' || document.form1.op_step[i].value == '3') {
					deil_cnt++;
				}
			}
		} else {
			alert("배송 상품이 존재하지 않습니다.");
			return;
		}
		if (deil_cnt == 0) {
			alert("배송준비중인 상품이 없습니다.");
			return;
		}

		if(!confirm("모든 상품의 배송정보를 수정하시겠습니까?")) {
			document.form2.delimailtype.value="N";
			return;
		}

		if(confirm("모든 상품의 배송정보 변경내역을 메일/SMS로 발송하시겠습니까?"))
			document.form2.delimailtype.value="Y";
		else
			document.form2.delimailtype.value="N";

		//document.form2.delimailtype.value="Y";

		document.form2.deli_com.value=document.form1.deli_com.value;
		document.form2.deli_num.value=document.form1.deli_num.value;
		document.form2.type.value="deliupdate";
		document.form2.submit();
	}
}

function MemoUpdate(){
	if(confirm("주문정보를 등록/수정하시겠습니까?")) {
		document.form2.type.value="memoupdate";
		document.form2.submit();
	}
}
function ClameUpdate(no){
	if(confirm("클레임 정보를 수정하시겠습니까?")) {
		document.form2.type.value="clameupdate";
		document.form2.clameno.value=no;
		document.form2.submit();
	}
}



<?php if(strstr("Q",$_ord->paymethod[0]) && strlen($_ord->bank_date)==14) { ?>
function escrow_bank_account() {
	alert("환불계좌 등록 후 매매보호 취소처리를 하시면\n\n등록된 환불계좌로 환불처리됩니다.");
	window.open("about:blank","baccountpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizeble=no,copyhistory=no,width=100,height=100");
	document.vform.submit();
}
<?php } ?>


function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

function EtcMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.etcdtl"+cnt);
	obj._tid = setTimeout("EtcView(WinObj)",200);
}
function EtcView(WinObj) {
	WinObj.style.visibility = "visible";
}
function EtcMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.etcdtl"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function CheckAll(){
	chkval=document.form1.allcheck.checked;
	if(typeof(document.form1.chkprcode.length)=="number") {
		cnt=document.form1.chkprcode.length;
		for(i=1;i<cnt;i++){
			document.form1.chkprcode[i].checked=chkval;
		}
	}
}

<?php if(strstr("NXS",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C" && $prescd=="Y") {?>
function changeDeli(obj) {
	if(typeof(document.form1.chkprcode.length)=="number") {
		document.form1.idxs.value="";
		cnt=document.form1.chkprcode.length;
		for(i=1;i<cnt;i++){
			if(document.form1.chkprcode[i].checked) {
				document.form1.idxs.value+=document.form1.chkprcode[i].value+",";
			}
		}
	} else {
		alert("배송 상품이 존재하지 않습니다.");
		return;
	}
	deli_gbn=obj.value;
	document.form1.idxs.value="";
	for(i=1;i<document.form1.chkprcode.length;i++) {
		if(document.form1.chkprcode[i].checked) {
			document.form1.idxs.value+=document.form1.chkprcode[i].value+",";
		}
	}
	if(document.form1.idxs.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		obj.selectedIndex=0;
		return;
	}
	if(deli_gbn.length>0) {
		delistr="";
		if(deli_gbn=="N") delistr="[미처리]";
		else if(deli_gbn=="S") delistr="[발송준비]";
		else if(deli_gbn=="Y") delistr="[배송완료]";
		if(confirm("선택된 상품의 처리상태를 "+delistr+" 상태로 변경하시겠습니까?")) {
			document.form1.type.value="deligbnup";
			document.form1.submit();
		} else {
			document.form1.idxs.value="";
			obj.selectedIndex=0;
		}
	} else {
		document.form1.idxs.value="";
		obj.selectedIndex=0;
	}
}
<?php }?>
function trans_view(id,approve,send_dt,send_no){

	window.open('http://allthegate.com/customer/receiptLast3.jsp?sRetailer_id='+id+'&approve='+approve+'&send_no='+send_no+'&send_dt='+send_dt, 'COLOR_WINDOW', 'resizable=yes scrollbars=yes width=420 height=800');

}


function noEvent() {
    if (event.keyCode == 116) {
        event.keyCode= 2;
        return false;
    }
    else if(event.ctrlKey && (event.keyCode==78 || event.keyCode == 82))
    {
        return false;
    }
}
document.onkeydown = noEvent;

function modReserve(reserve_pre){
	if(confirm("적립금 사용을 수정하시겠습니까? 총 주문금액은 변경되지 않습니다.")==true){
	var mod_reserve = document.getElementById("mod_reserve").value;
	if(reserve_pre<0){
		alert("0이하는 설정할 수 없습니다.");
		return;
	}
	if(mod_reserve > parseInt(reserve_pre)){
		alert("사용액보다 큰 금액은 설정할 수 없습니다.");
		return;
	}
	var tot_reserve = reserve_pre - mod_reserve;

	document.mForm.id.value = document.form2.id.value;
	document.mForm.ordercode.value = document.form2.ordercode.value;
	document.mForm.modreserve.value = mod_reserve;
	document.mForm.tot_reserve.value = tot_reserve;
	mForm.submit();
	}
}
function submitMsgCancel(){
	document.mFormMsgCancel.type.value="recoverypartcanmsg";
	document.mFormMsgCancel.ordercode.value = document.form2.ordercode.value;
	document.mFormMsgCancel.msg_part_cancel.value = document.mFormMsgPartCancel.msg_part_cancel.value
	document.mFormMsgCancel.submit();
}

function modRefundCancel(){
	if(confirm("환불금 사용을 수정하시겠습니까?")==true){
		var mod_refund = document.getElementById("mod_pay_cancel").value;

		document.mFormMsgCancel.type.value="recoverypartcanrefund";
		document.mFormMsgCancel.ordercode.value = document.form2.ordercode.value;
		document.mFormMsgCancel.mod_refund.value = mod_refund;
		document.mFormMsgCancel.submit();
	}
}


/*function backPartCancel(orcode, prcode){
	if(confirm("해당 상품의 부분취소 내용을 복원 하시겠습니까?")){
		var mod_refund = document.getElementById("mod_pay_cancel").value;

		document.restoreForm.ordercode.value = orcode;
		document.restoreForm.productcode.value = prcode;
		document.restoreForm.submit();
	}
}*/

//주문취소접수 복원
function ordercancel_restore(ordercode, oc_no) {
	if(confirm("해당 취소접수를 복원하시겠습니까?")) {
		document.restoreForm.ordercode.value = ordercode;
		document.restoreForm.oc_no.value = oc_no;
		document.restoreForm.submit();
	}
}

function order_admin_memo(mode, ordercode, om_no) {
		window.open("about:blank","ordermemo_pop","width=200,height=200,scrollbars=yes");
		document.form_memo.target="ordermemo_pop";
		document.form_memo.mode.value=mode;
		document.form_memo.ordercode.value=ordercode;
		document.form_memo.om_no.value=om_no;
		document.form_memo.action="order_memo_reg.php";
		document.form_memo.submit();
}

function pop_receiptCardView(ordercode) {
	var receiptWin = "/front/mypage_receipt.pop.php?orderid="+ordercode+"&mode=02";
	window.open(receiptWin , "receipt_pop" , "width=360, height=647");
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail_"+ordercode,"scrollbars=yes,width=700,height=600,resizable=yes");
	document.detailform.target = "orderdetail_"+ordercode;
	document.detailform.submit();
}

function CharacterCheck( character ){
	var temp_character = '';
	var character_type = false;
	for( var i = 0; i < character.length; i++ ){
		if( $.isNumeric( character[i] ) ){
			temp_character += character[i];
		} else {
			character_type = true;
		}
	}

	if( character_type ) alert( '숫자만 입력이 가능합니다.' );

	return temp_character;
}

$(document).on( 'keyup', 'input[name="chkdeli_num"], input[name="deli_num"]', function( event ){
	 $(this).val( CharacterCheck(  $(this).val() ) );
});

function CrmView(id) {
	document.crmview.id.value = id;
	window.open("about:blank","crm_view","scrollbars=yes,width=100,height=100,resizable=yes");
    document.crmview.target="crm_view";
	document.crmview.submit();
}

//-->
</SCRIPT>
</head>
<!--body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();"-->
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="PagePrint();return false;" style="overflow-x:hidden;overflow-y:auto;" onLoad="PageResize();">

<table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed;" id=table_body>
<tr class="page_screen">
	<td width=100% align=center>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<col width=5></col>
	<col width=></col>
	<col width=50></col>
	<tr><td colspan=3 height=10></td></tr>
	<tr>
		<td></td>
		<td>
<?php
		$createbutton="<table border=0 cellpadding=0 cellspacing=0>";
		//미처리 또는 배송요청건이고, 카드취소가 아니고, 무통장 입금건이고, 입금이 안됐다면,,,,,
		if(strstr("NX",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C" && strstr("B",$_ord->paymethod[0]) && strlen($_ord->bank_date)!=14) {
			//$createbutton.="<tr><td align=right style='padding-right:40px' height=15><img src='images/ordtl_arrow1.gif' align=absmiddle></td></tr>"; - (2016.02.15 김재수 막음)
		}
		$createbutton.="<tr><td>";


		#주문 전체 취소 버튼 노출 (2016.02.015  - 김재수)
		#주문 전체 취소 버튼 노출 S--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------#

		//매매보호 가상계좌/신용카드건에 대해서 (주문취소/환불대기) 상태가 아니면,,,,,
		//if(strstr("QP",$_ord->paymethod[0]) && !strstr("CE",$_ord->deli_gbn) && $_ord->price>0 && !strstr("YC",$_ord->escrow_result)) {
			//if(strstr("YD",$_ord->deli_gbn) && strlen($_ord->deli_date)==14) {	//배송완료된 에스크로 결제건은 "정산보류" 버튼 활성화
				//if($pg_type=="A" || $pg_type=="C" || $pg_type=="D") {
					//$createbutton.="<a href=\"javascript:okhold()\"><img src=\"images/ordtl_btnescrowhold.gif\" align=absmiddle border=0></a>\n";	//정산보류
				//}
			//} else {
				//$createbutton.="<a href=\"javascript:okcancel('".$_ord->paymethod[0]."','{$_ord->bank_date}')\"><img src=\"images/ordtl_btnescrowcancel.gif\" align=absmiddle border=0></a>\n";	//취소처리
			//}
		//} else {
			$candate = date("Ymd",strtotime('-15 day'));
			if((!strstr("RA", $_ord->del_gbn) && (!strstr("P",$_ord->paymethod[0]) || $_ord->price==0) && (!strstr("C", $_ord->deli_gbn)))
			|| (!strstr("RA", $_ord->del_gbn) && strstr("P",$_ord->paymethod[0]) && ($_ord->deli_gbn=="C" || substr($_ord->ordercode,0,8)<$candate) && $_ord->deli_gbn!="Y")) {

				//카드실패체크 2014-02-21 카드 실패의 경우 버튼감춤
				$cc_count=pmysql_num_rows(pmysql_query("SELECT * FROM tblorderinfo a 
				WHERE a.deli_gbn='N' 
				AND (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') 
				AND a.pay_flag='N' AND a.pay_admin_proc='N') and a.ordercode='".$_ord->ordercode."'"));

				if(!$cc_count){
					$ro_type='quan';
					if ($_ord->oi_step1 == '0') {
						//$createbutton.="<a href=\"javascript:RestoreOrder('can','{$_ord->deli_gbn}','{$_ord->reserve}')\"><img src=\"images/ordtl_btnescrowcancel.gif\" border=0 align=absmiddle></a>\n";
						$createbutton.="<a href=\"javascript:RestoreOrderCancel_chk('".$_ord->oi_step1."','".$_ord->oi_step2."','".$_ord->paymethod[0]."','{$re_type}','recoverycan_all')\"><img src=\"images/ordtl_btnescrowcancel.gif\" border=0 align=absmiddle></a>\n";
					} else {
						/*if(strstr("CP",$_ord->paymethod[0]) && $_ord->oi_step1 < 3) {
							$createbutton.="<a href=\"javascript:RestoreOrder('quan','{$_ord->deli_gbn}','{$_ord->reserve}')\"><img src=\"images/ordtl_btnescrowcancel.gif\" border=0 align=absmiddle></a>\n";
						} else {*/
							if ($_ord->oi_step1 < 3) {
								$re_type=''; // 취소환불만
								$ex_type	= "recoveryquan_all";
							} else {
								$re_type='B'; // 배송중, 배송완료일 경우 취소(반품+환불)
								$ex_type	= "redelivery_all";
							}
							//상품의 상태값이 취소요청, 완료가 있는지 체크
							list($op_step_cnt)=pmysql_fetch_array(pmysql_query("select count(op_step) as op_step_cnt from (select op_step from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND op_step >= 40 group by op_step) as foo"));
							if ($op_step_cnt ==0) {		//모두 취소상태가 아닌경우
								$createbutton.="<a href=\"javascript:RestoreOrderCancel_chk('".$_ord->oi_step1."','".$_ord->oi_step2."','".$_ord->paymethod[0]."','{$re_type}','{$ex_type}')\"><img src=\"images/ordtl_btnescrowcancel.gif\" border=0 align=absmiddle></a>\n";
							}
						//}
					}
				}
			}
		//}

		#주문 전체 취소 버튼 노출 E--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------#


		//신용카드 정상 결제일 경우
		if(strstr("C",$_ord->paymethod[0]) && $_ord->pay_flag=="0000") {
			//신용카드 전체 취소 버튼 막음 (2016.02.16 - 김재수 막음)
			/*if($_ord->deli_gbn != 'C') {// 취소상태가 아닌경우
				if($_ord->pay_admin_proc=="N" && strcmp($_ord->pay_flag,"0000")==0) {	//매입요청이 안된 경우
					$createbutton.="<a href=\"javascript:card_ask('card_ask','card')\"><img src=\"images/ordtl_btncardok.gif\" align=absmiddle border=0></a>\n";	//카드결제요청
					$createbutton.="<a href=\"javascript:card_ask('card_cancel','card')\"><img src=\"images/ordtl_btncardcancel.gif\" align=absmiddle border=0></a>\n";	//카드취소
					$createbutton.="&nbsp;&nbsp;&nbsp;<font color=#C0C0C0>|</font>&nbsp;";
				} elseif($_ord->pay_admin_proc=="Y") {	//매입요청된 경우
					if(!strstr("YC",$_ord->escrow_result)) {
						$createbutton.="<a href=\"javascript:card_ask('card_cancel','card')\"><img src=\"images/ordtl_btncardcancel.gif\" align=absmiddle border=0></a>\n";	//카드취소
						$createbutton.="&nbsp;&nbsp;&nbsp;<font color=#C0C0C0>|</font>&nbsp;";
					}
				}
			}*/


			// 신용카드 결제시 영수증을 확인할수 있는 부분인데 사용 안함 (2016.02.16 - 김재수 막음) 필요할것 같은데

			/*$trans_url=pmysql_fetch_object(pmysql_query("select pay_data from tbl_card_estimate where ordercode='{$ordercode}'"));
			if($trans_url->pay_data){
				$pay_data_arr=explode("|",$trans_url->pay_data);
				$pay_data_arr8=substr($pay_data_arr[8],0,8);
				$createbutton.="<a href=\"javascript:trans_view('".$pay_data_arr[0]."','".$pay_data_arr[3]."','".$pay_data_arr8."','".$pay_data_arr[13]."');\"><img src=\"images/ordtl_btntax_card.gif\" align=absmiddle border=0></a>&nbsp;";
			}*/

		//핸드폰 결제일 경우
		} elseif (strstr("M",$_ord->paymethod[0]) && $_ord->pay_flag=="0000") {

			//핸드폰 전체 취소 버튼 막음 (2016.02.16 - 김재수 막음)
			/*if($_ord->pay_admin_proc=="N" && strcmp($_ord->pay_flag,"0000")==0) {
				$createbutton.="<a href=\"javascript:card_ask('card_cancel','hp')\"><img src=\"images/ordtl_btnpaycancel.gif\" align=absmiddle border=0></a>\n";		//결제취소
				$createbutton.="&nbsp;&nbsp;&nbsp;<font color=#C0C0C0>|</font>&nbsp;";
			}*/

		//실시간계좌이체/일반가상계좌 환불안내
		} elseif (strstr("VO",$_ord->paymethod[0]) && $_ord->pay_flag=="0000" && $_ord->pay_admin_proc!="C") {
			$createbutton.="<a href=\"javascript:alert('실시간계좌이체 및 가상계좌 결제건은 시스템적인 자동 환불처리가 불가하오니\\n\\n주문취소처리 후 적립금으로 지급 또는 무통장 환불처리를 하시기 바랍니다.')\"><img src=\"images/ordtl_refundinfo.gif\" border=0 align=absmiddle></a>\n";
			$createbutton.="&nbsp;<font color=#C0C0C0>|</font>&nbsp;";
		}

		//신용카드 결제가 승인되었거나 또는 (무통장입금이고, 주문취소가 아니고, 입금이 완료되었고) 또는 (실시간계좌이체이고 정상적으로 결제된건이라면,,,)
		if($_ord->pay_admin_proc=="Y" || (strstr("B",$_ord->paymethod[0]) && $_ord->deli_gbn!="C" && strlen($_ord->bank_date)==14) || (strstr("VO",$_ord->paymethod[0]) && $_ord->pay_flag=="0000" && $_ord->deli_gbn!="C")) {
			$createbutton.="<a href=\"javascript:printtax()\"><img src=\"images/ordtl_btntax.gif\" align=absmiddle border=0></a>\n";	//영수증 발급
		}
		if(strstr("BOQ",$_ord->paymethod[0]) && $tax_type!="N" && $_ord->deli_gbn!="C" && $_ord->oi_step1 > 0) {
			$createbutton.="<a href=\"javascript:get_taxsave()\"><img src=\"images/ordtl_btntaxsave.gif\" align=absmiddle border=0></a>\n";	//현금영수증 요청
			//$createbutton.="<a href=\"javascript:get_taxsave2()\">현금영수증New</a>\n";	//현금영수증 요청
		}
		if(strstr("NXS",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C") {


			//(미처리/배송요청/배송준비) 되고 카드결제 취소건이 아니면,,,,,
			//$createbutton.="<a href=\"javascript:printaddress()\"><img src=\"images/ordtl_btnprint.gif\" align=absmiddle border=0></a>\n";	//운송장 출력
			if ($createbutton !='<table border=0 cellpadding=0 cellspacing=0><tr><td>') {
				$createbutton.="<font color=#C0C0C0>|</font>&nbsp;";
			}
			if(strstr("B",$_ord->paymethod[0]) && strlen($_ord->bank_date)!=14) {

				//무통장 입금 결제건이고, 입금이 안된 경우
				$createbutton.="<a href=\"javascript:banksend()\"><img src=\"images/ordtl_btnbankok.gif\" align=absmiddle border=0></a><!-- img src='images/ordtl_arrow2.gif' align=absmiddle -->";	//입금완료

				//배송준비 단계가 아니면,,,,,
				//if($_ord->deli_gbn!="S") $createbutton.="<a href=\"javascript:delisend('S')\"><img src=\"images/ordtl_btndeliready.gif\" align=absmiddle border=0></a><img src='images/ordtl_arrow2.gif' align=absmiddle>";	//발송준비

				//$createbutton.="<a><img src=\"images/won_ordtl_btn.jpg\" align=absmiddle border=0></a>\n";//배송중 이란아이콘이 없었음.원재

				//$createbutton.="<a href=\"javascript:delisend('Y')\"><img src=\"images/ordtl_btndeliok.gif\" align=absmiddle border=0></a>\n";	//배송완료

				//$createbutton.="<a><img src=\"images/gordtl_btn.jpg\" align=absmiddle border=0></a>\n";	//배송완료->발송완료 - 처리방식 변경으로 막음 (2016.02.15 - 김재수 막음)
				//현재 주문접수 화면에서 배송완료 버튼 누르면 기능 제데로 안됨. 뿐만 아니라 논리적으로도 맞지가 않음.발송준비 주문건에 대해서 배송완료 버튼을 누르게 하는것이 맞을듯. 일단 버튼 동작 안하도록 막아놓음

				//$createbutton.="<a><img src=\"images/ordtl_btndeliok.gif\" align=absmiddle border=0></a>\n";	//배송완료

			} elseif(!strstr("OQ",$_ord->paymethod[0]) || strlen($_ord->bank_date)>=12) {	//가상계좌, 가상계좌(매매보호) 입금건이 아닌건에 대해서 입금이 된 경우
				//배송준비 단계가 아니면,,,,,
				if($_ord->deli_gbn!="S") $createbutton.="<a href=\"javascript:delisend('S')\"><img src=\"images/ordtl_btndeliready.gif\" align=absmiddle border=0></a>\n";	//발송준비

				// 배송 레이어 - 이건 왜 쓰는지 모르겠음 (2016.02.18 - 김재수 막음)
				/*$createbutton.="<div id=deliescrow style=\"position:absolute; z-index:100; display:none;\">\n";
				$createbutton.="<table border=0 cellspacing=1 cellpadding=0 bgcolor=#0099CC width=250>\n";
				$createbutton.="<tr>\n";
				$createbutton.="	<td style=\"padding:10px;\">\n";
				$createbutton.="	<table border=0 cellspacing=1 cellpadding=0 bgcolor=#B9B9B9 width=250>\n";
				$createbutton.="	<form name=escrow_form1>\n";
				if(strstr("QP", $_ord->paymethod[0])) {
					$createbutton.="	<input type=hidden name=\"escrowcaltype\" value=\"Y\">\n";
				} else {
					$createbutton.="	<input type=hidden name=\"escrowcaltype\" value=\"N\">\n";
				}
				$createbutton.="	<tr bgcolor=#FFFFFF>\n";
				$createbutton.="		<td class=\"table_cell\" colspan=\"2\" align=\"center\">대 표 송 장 정 보</td>\n";
				$createbutton.="	</tr>\n";
				$createbutton.="	<tr bgcolor=#FFFFFF>\n";
				$createbutton.="		<td class=\"table_cell\"><img src=\"images/icon_point5.gif\" width=\"8\" height=\"11\" border=\"0\">배송업체</td>\n";
				$createbutton.="		<td class=\"td_con1\"><select name=escrow_deli_com style=\"width:90;font-size:9pt\">\n";
				$createbutton.="		<option value=\"\">없음</option>\n";

				for($yy=0;$yy<count($delicomlist);$yy++) {
					if($pg_type=="B" && strstr("QP", $_ord->paymethod[0])) {
						if(ord($delicomlist[$yy]->dacom_code)) {
							$createbutton.="		<option value=\"{$delicomlist[$yy]->code}\">{$delicomlist[$yy]->company_name}</option>\n";
						}
					} else {
						$createbutton.="		<option value=\"{$delicomlist[$yy]->code}\">{$delicomlist[$yy]->company_name}</option>\n";
					}
				}
				$createbutton.="		</select>\n";
				$createbutton.="		</td>\n";
				$createbutton.="	</tr>\n";
				$createbutton.="	<tr bgcolor=#FFFFFF>\n";
				$createbutton.="		<td class=\"table_cell\"><img src=\"images/icon_point5.gif\" width=\"8\" height=\"11\" border=\"0\">송장번호</td>\n";
				$createbutton.="		<td class=\"td_con1\"><input type=text name=escrow_deli_num value=\"\" size=13 maxlength=20 style=\"font-size:9pt\"></td>\n";
				$createbutton.="	</tr>\n";
				$createbutton.="	<tr bgcolor=#FFFFFF>\n";
				$createbutton.="		<td height=40 colspan=\"2\" align=\"center\"><a href=\"javascript:delisend('N')\"><img src=\"images/btn_ok3.gif\" border=\"0\"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:escrow_deliclose();\"><img src=\"images/ordtl_close.gif\" border=\"0\"></a></td>\n";
				$createbutton.="	</tr>\n";
				$createbutton.="	</table>\n";
				$createbutton.="	</td>\n";
				$createbutton.="</tr>\n";
				$createbutton.="</form>\n";
				$createbutton.="</table>\n";
				$createbutton.="</div>\n";*/

				//$createbutton.="<a href=\"javascript:delisend('N')\"><img src=\"images/won2_ordtl_btn.jpg\" align=absmiddle border=0></a>\n";	//발송중 으로 상태값 변경

				if($_ord->deli_gbn=="S"){
					//$createbutton.="<a href=javascript:delisend('N')><img src=\"images/gordtl_btn.jpg\" align=absmiddle border=0></a>\n";//발송완료 버튼(발송준비 됬을때) - 처리방식 변경으로 막음 (2016.02.15 - 김재수 막음)
				}else{
					if($_ord->deli_gbn=="Y") {	//배송중건에 대해서,,,,,,
						//if ($createbutton !='<table border=0 cellpadding=0 cellspacing=0><tr><td>') {
						//	$createbutton.="<font color=#C0C0C0>|</font>&nbsp;";
						//}
						//$createbutton.="<a href=\"javascript:deliFin('ALL')\"><img src=\"images/ordtl_btndeliok.gif\" align=absmiddle border=0></a>\n";//배송완료 버튼 (2016.02.18 - 김재수 추가)
					}
					//$createbutton.="<a><img src=\"images/gordtl_btn.jpg\" align=absmiddle border=0></a>\n";//발송완료 버튼 (발송준비 안 되었을때) - 처리방식 변경으로 막음 (2016.02.15 - 김재수 막음)
				}
				//$createbutton.="<a href=\"javascript:delisend('N')\"><img src=\"images/ordtl_btndeliok.gif\" align=absmiddle border=0></a>\n";

				//$createbutton.="<a><img src=\"images/ordtl_btndeliok.gif\" align=absmiddle border=0></a>\n";
				//배송완료버튼. 발송준비가 완료 된 상태에서만 배송완료진행하게 함
			}
		} elseif($_ord->deli_gbn=="Y") {	//배송중건에 대해서,,,,,,
			//$createbutton.="<a href=\"javascript:printaddress()\"><img src=\"images/ordtl_btnprint.gif\" align=absmiddle border=0></a>\n";	//운송장 출력
			//if ($createbutton !='<table border=0 cellpadding=0 cellspacing=0><tr><td>') {
			//	$createbutton.="<font color=#C0C0C0>|</font>&nbsp;";
			//}
			//if( $_ord->receive_ok == '0'){
				//$createbutton.="<a href=\"javascript:deliFin('ALL')\"><img src=\"images/ordtl_btndeliok.gif\" align=absmiddle border=0></a>\n";//배송완료 버튼 추가(원재)
			//}
			//if(!strstr("QP",$_ord->paymethod[0])) $createbutton.="<a href=\"javascript:delicancel()\"><img src=\"images/ordtl_btndelino.gif\" align=absmiddle border=0></a>\n";	//반송처리 - 반송처리는 상품선택으로 변경되어 버튼노출 안함(2016.02.11 - 김재수 막음)
		}elseif($_ord->deli_gbn=="F"){//새롭게 추가된 배송완료 시 반송처리 버튼 추가
			//if(!strstr("QP",$_ord->paymethod[0])) $createbutton.="<a href=\"javascript:delicancel()\"><img src=\"images/ordtl_btndelino.gif\" align=absmiddle border=0></a>\n"; - 반송처리는 상품선택으로 변경되어 버튼노출 안함(2016.02.11 - 김재수 막음)
		}
		//무통장 입금완료건에 대해서 주문취소이고 환불이 안된 경우
		if(strstr("B",$_ord->paymethod[0]) && $_ord->deli_gbn=="C" && strlen($_ord->bank_date)>=12) { // 무통장
			$createbutton.="<a href=\"javascript:bankcancel()\"><img src=\"images/ordtl_btnrefund.gif\" align=absmiddle border=0></a>\n";	//환불처리
		} elseif(strstr("O",$_ord->paymethod[0]) && $_ord->deli_gbn=="C" && $_ord->pay_admin_proc!="C") { // 가상계좌
			$createbutton.="<a href=\"javascript:virtualcancel()\"><img src=\"images/ordtl_btnrefund.gif\" align=absmiddle border=0></a>\n";	//환불처리
		} elseif(strstr("V",$_ord->paymethod[0]) && $_ord->deli_gbn=="C" && $_ord->pay_admin_proc!="C") { // 실시간 계좌이체
			$createbutton.="<a href=\"javascript:transcancel()\"><img src=\"images/ordtl_btnrefund.gif\" align=absmiddle border=0></a>\n";	//환불처리
		}

		$createbutton.="</td></tr>\n";
		$createbutton.="<tr><td height=10></td></tr>\n";
		$createbutton.="</table>\n";

		echo $createbutton;
?>
		</td>

		<td align=right style="padding-right:2pt">
		<!--table border=0 cellspacing=0 cellpadding=0 width=100%>
		<tr><td align=right style="padding-top:7"><img src="images/ordtl_close.gif" border=0 style="cursor:hand" onclick="window.close()"></td></tr>
		</table-->
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<div class='area_set'>
<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
<col width=190></col>
<col width=></col>

<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=type>
<input type=hidden name=mode>
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
<input type=hidden name=vender>
<input type=hidden name=productcode>
<input type=hidden name=opt1_name>
<input type=hidden name=opt2_name>
<input type=hidden name=quantity>
<input type=hidden name=reserve>
<input type=hidden name=price>
<input type=hidden name=arquantity>
<input type=hidden name=arreserve>
<input type=hidden name=arprice>
<input type=hidden name=deli_idxs>
<tr>
	<td style="padding-left:3pt">
	정렬 : <select name=sort style="width:90;font-size:9pt;" onchange="Sort(this.value);">
<?php
if($_shopdata->ETCTYPE["SELFCODEVIEW"]=="Y" || $_shopdata->ETCTYPE["SELFCODEVIEW"]=="Z" || $_shopdata->ETCTYPE["SELFCODEVIEW"]=="M" || $_shopdata->ETCTYPE["SELFCODEVIEW"]=="N") {
	if(!isset($sort)) {
		$sort="selfcode";
	}

	echo "<option value=\"selfcode\" ".($sort=="selfcode"?"selected":"").">진열코드</option>\n";
}
?>
	<option value="" <?=(ord($sort)==0?"selected":"")?>>장바구니</option>
	<option value="productname" <?=($sort=="productname"?"selected":"")?>>제품명</option>
	<option value="price desc" <?=($sort=="price desc"?"selected":"")?>>가격</option>
	</select>
	</td>
	<td align=right>
	</td>
</tr>
</table>
</div>
<div class='area_set'>
	<!-- 주문내역 시작 -->
	<table border=0 cellpadding=0 cellspacing=0 width=100% class='table_style0'>
	<col width=25></col>
	<?php if($vendercnt>0){?>
	<col width=90></col>
	<?php }?>
	<col width=></col>
	<col width=60></col>
	<col width=60></col>
	<col width=60></col>
	<col width=75></col>
	<col width=75></col>
	<col width=75></col>
	<col width=70></col>
	<tr bgcolor=#efefef>
		<td align=center class="page_print">No</td><td align=center class="page_screen"><!--input type=checkbox name=allcheck onclick="CheckAll()"--></td>
		<?php if($vendercnt>0){?>
		<td align=center>브랜드</td>
		<?php }?>
		<td align=center>상품명</td>
		<td align=center>가격</td>
		<td align=center>수량</td>
		<td align=center>옵션</td>
		<td align=center>쿠폰</td>
		<td align=center>사용적립금</td>
		<td align=center>개별배송비</td>
		<td align=center>소계</td>
	</tr>

	<input type=hidden name=chkprcode>
	<input type=hidden name='ord_productname'>
	<input type=hidden name='ord_optionname'>
	<input type=hidden name='ord_option1'>
	<input type=hidden name='ord_option2'>
	<input type=hidden name='ord_option_price_text'>
	<input type=hidden name='ord_text_opt_subject'>
	<input type=hidden name='ord_text_opt_content'>
	<input type=hidden name=chkdeli_com>
	<input type=hidden name=chkdeli_num>
	<input type=hidden name=prcodes>
	<input type=hidden name=idxs>
	<input type=hidden name=delimailtype value="N">
	<input type=hidden name=each_price>
	<input type=hidden name=op_step>
	<input type=hidden name=op_reserve>

<?php
	$colspan=9;
	if($vendercnt>0) $colspan++;

	$taxsaveprname=""; // 세금계산서 상품명
	$sumquantity=0; // 상품 총 합계
	$totalprice=0;
	$totalInitprice=0;
	$in_reserve=0;

	$tot_def_price		= 0;
	$tot_opt_price		= 0;
	$tot_cou_price	= 0;
	$tot_deil_price		= 0;
	$order_conf_not_cnt	= 0; // 구매확정 안된 상품수
	$cnt=0;
	$prCodeList = array(); // 상품코드 정보
	$groupOpSql = "SELECT a.idx, a.productcode, a.vender, a.productname, a.addcode, a.selfcode, a.deli_com, a.deli_num, a.price, ";
	$groupOpSql.= "( a.price * a.option_quantity ) AS sum_def_price, ";
	$groupOpSql.= "( a.option_price * a.option_quantity ) AS sum_opt_price,  ";
	$groupOpSql.= "( ( a.price + a.option_price ) * a.option_quantity ) AS sum_price, ";
	$groupOpSql.= "a.quantity, a.opt1_name, a.opt2_name, a.option_quantity, a.option_price, a.option_price_text, ";
	$groupOpSql.= "a.coupon_price, (a.reserve * a.option_quantity) AS sum_reserve, a.use_point, a.receive_ok, ";
	$groupOpSql.= "a.redelivery_type, a.redelivery_date, a.redelivery_reason, ";
	$groupOpSql.= "a.deli_gbn,a.deli_price, a.receive_ok, a.op_step, a.oc_no, a.opt1_change, a.opt2_change, a.text_opt_subject, ";
    $groupOpSql.= "a.text_opt_content, a.option_price_text, a.text_opt_subject_change, a.text_opt_content_change, ";
	$groupOpSql.= "option_price_text_change, b.oi_step1, b.oi_step2, a.order_conf, a.delivery_type, a.store_code, a.reservation_date ";
	$groupOpSql.= "FROM tblorderproduct a ";
    $groupOpSql.= "join tblorderinfo b on a.ordercode = b.ordercode ";
    $groupOpSql.= "WHERE a.ordercode ='".$_ord->ordercode."' ";
    $groupOpSql.= "order by a.vender, a.idx";
    //echo "sql = ".$groupOpSql."<br>";
	$groupOpRes = pmysql_query( $groupOpSql, get_db_conn() );
	$chkDeliveryType = true;
	while( $groupOpRow = pmysql_fetch_object( $groupOpRes ) ){
		if($groupOpRow->delivery_type == '2'){
			# 당일 배송이 있는지 체크
			# 당일 배송이 있을 경우 배송지 정보수정을 막기 위해.
			$chkDeliveryType = false;
		}

		$storeData = getStoreData($groupOpRow->store_code);

		$isGift=false;
		# 할인코드
		if(preg_match("/^9999999999\d(X|R)$/",$groupOpRow->productcode)) {
			$etcdata[]=$row;
			continue;
		# 상품
		} else {
			$taxsaveprname.=$groupOpRow->productname.",";
			$sumprice = $groupOpRow->sum_price;			// 상품당 총금액
			$reserve = $groupOpRow->sum_reserve;			// 적립금
			$sumquantity += $groupOpRow->quantity;			// 수량 - (로직 변경되면 option_quantity로 사용)

			$in_reserve += $reserve;
			$totalprice += $sumprice;
			$totalInitprice += $sumprice;

			$sum_def_price	= $groupOpRow->sum_def_price;
			$sum_opt_price	= $groupOpRow->sum_opt_price;
			$coupon_price	= $groupOpRow->coupon_price; // 쿠폰금액
			$use_reserve		= $groupOpRow->use_point; // 사용 적립금
			$deil_price			= $groupOpRow->deli_price; // 개별 배송비

			$tot_def_price		+= $sum_def_price;
			$tot_opt_price		+= $sum_opt_price;
			$tot_cou_price	+= $coupon_price;
			$tot_deil_price		+= $deil_price;
			$in_use_reserve += $use_reserve;

			$each_price		= $sumprice - $coupon_price - $use_reserve + $deil_price;


			$prdata[]=$groupOpRow;
			$prCodeList[$groupOpRow->productcode] = $groupOpRow->productname;
		}
		# 기프트 ( 현제 사용 안함 )
		if(substr($groupOpRow->productcode,-4)=="GIFT"){
			$isGift=true;
		}

		if($groupOpRow->productcode=="99999999999R") $norecan="Y";

		$cnt++;

		# 묶음상품 관련
		$assemblestr = "";
		$packagestr = "";
		if(($_ord->paymethod!="B" || $mode!="update") && ord(str_replace("","",str_replace(":","",str_replace("=","",$groupOpRow->assemble_info))))) {
			$assemble_infoall_exp = explode("=",$groupOpRow->assemble_info);

			if($groupOpRow->package_idx>0 && ord(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))) {
				$package_info_exp = explode(":", $assemble_infoall_exp[0]);

				$package_productcode_exp = explode("", $package_info_exp[0]);
				$package_productname_exp = explode("", $package_info_exp[1]);
				$package_sellprice = $package_info_exp[2];
				$package_packagename = $package_info_exp[3];

				if(count($package_info_exp)>2 && ord($package_packagename)) {
					$packagestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
					$packagestr.="	<tr>\n";
					$packagestr.="		<td colspan=\"2\" style=\"word-break:break-all;font-size:8pt;\"><font color=green><b>[</b>패키지선택 : {$package_packagename}<b>]</b></font></td>\n";
					$packagestr.="	</tr>\n";
					if(ord(str_replace("","",$package_info_exp[1]))) {
						$packagestr.="	<tr>\n";
						$packagestr.="		<td width=\"30\" valign=\"top\" nowrap><font color=\"#008000\" style=\"line-height:10px;\">│<br>└▶</font></td>\n";
						$packagestr.="		<td width=\"100%\" bgcolor=\"#DDDDDD\">\n";
						$packagestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\">\n";
						$packagestr.="		<col width=\"\"></col>\n";
						$packagestr.="		<col width=\"55\"></col>\n";
						for($k=0; $k<count($package_productname_exp); $k++) {
							if($k==0) {
								$packagestr.="		<tr bgcolor=\"#FFFFFF\">\n";
								$packagestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">{$package_productname_exp[$k]}&nbsp;<span class=\"page_screen\"><a href=\"javascript:ProductInfo('".substr($package_productcode_exp[$k],0,12)."','{$package_productcode_exp[$k]}','YES')\"><img src=images/ordtl_icnnewwin.gif align=absmiddle border=0 vspace=\"1\"></a></span></td>\n";
								$packagestr.="				<td rowspan=\"".count($package_productname_exp)."\" align=\"right\" style=\"padding-left:4px;padding-right:4px;font-size:8pt;\">".number_format((int)$package_sellprice)."</td>\n";
								$packagestr.="		</tr>\n";
							} else {
								$packagestr.="		<tr bgcolor=\"#FFFFFF\">\n";
								$packagestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">{$package_productname_exp[$k]}&nbsp;<span class=\"page_screen\"><a href=\"javascript:ProductInfo('".substr($package_productcode_exp[$k],0,12)."','{$package_productcode_exp[$k]}','YES')\"><img src=images/ordtl_icnnewwin.gif align=absmiddle border=0 vspace=\"1\"></a></span></td>\n";
								$packagestr.="		</tr>\n";
							}
						}

						$packagestr.="		</table>\n";
						$packagestr.="		</td>\n";
						$packagestr.="	</tr>\n";
					}
					$packagestr.="	</table>\n";
				}
				@pmysql_free_result($alproresult);
			}

			if($groupOpRow->assemble_idx>0 && ord(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))) {
				$assemblestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
				$assemblestr.="	<tr height=\"2\"><td></td></tr>\n";
				$assemblestr.="	<tr>\n";
				$assemblestr.="		<td width=\"30\" valign=\"top\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">│<br>└▶</font></td>\n";
				$assemblestr.="		<td width=\"100%\" bgcolor=\"#DDDDDD\">\n";
				$assemblestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\">\n";

				$assemble_info_exp = explode(":", $assemble_infoall_exp[1]);

				if(count($assemble_info_exp)>2) {
					$assemble_productcode_exp = explode("", $assemble_info_exp[0]);
					$assemble_productname_exp = explode("", $assemble_info_exp[1]);
					$assemble_sellprice_exp = explode("", $assemble_info_exp[2]);

					for($k=0; $k<count($assemble_productname_exp); $k++) {
						$assemblestr.="		<col width=\"\"></col>\n";
						$assemblestr.="		<col width=\"55\"></col>\n";
						$assemblestr.="		<tr bgcolor=\"#FFFFFF\">\n";
						$assemblestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">{$assemble_productname_exp[$k]}&nbsp;<span class=\"page_screen\"><a href=\"javascript:ProductInfo('".substr($assemble_productcode_exp[$k],0,12)."','{$assemble_productcode_exp[$k]}','YES')\"><img src=images/ordtl_icnnewwin.gif align=absmiddle border=0></a></span></td>\n";
						$assemblestr.="				<td align=\"right\" style=\"padding-left:4px;padding-right:4px;font-size:8pt;\">".number_format((int)$assemble_sellprice_exp[$k])."</td>\n";
						$assemblestr.="		</tr>\n";
					}
				}
				@pmysql_free_result($alproresult);
				$assemblestr.="		</table>\n";
				$assemblestr.="		</td>\n";
				$assemblestr.="	</tr>\n";
				$assemblestr.="	</table>\n";
			}
		}

		if ($groupOpRow->redelivery_type == 'Y' || $groupOpRow->redelivery_type == 'G'){ // 반품 및 교환 신청을 경우
			$pro_redelivery['productname']	= $groupOpRow->productname;
			$pro_redelivery['redelivery_type']	= $groupOpRow->redelivery_type;
			$pro_redelivery['redelivery_date']	= $groupOpRow->redelivery_date;
			$pro_redelivery['redelivery_reason']	= $groupOpRow->redelivery_reason;
			$redeliveryinfo[]	= $pro_redelivery;
		}

		// 주문취소번호가 있을경우 (2016.02.11 -  김재수 추가)
		if ($groupOpRow->oc_no != ''){
			$pro_cancel['productname']	= $groupOpRow->productname;
			$pro_cancel['opt1_name']		= $groupOpRow->opt1_name;
			$pro_cancel['opt2_name']		= $groupOpRow->opt2_name;
			$pro_cancel['option_price_text']		= $groupOpRow->option_price_text;
			$pro_cancel['option_quantity']		= $groupOpRow->option_quantity;
			$pro_cancel['text_opt_subject']		= $groupOpRow->text_opt_subject;
			$pro_cancel['text_opt_content']		= $groupOpRow->text_opt_content;
			$pro_cancel['opt1_change']		= $groupOpRow->opt1_change;
			$pro_cancel['opt2_change']		= $groupOpRow->opt2_change;
			$pro_cancel['option_price_text_change']		= $groupOpRow->option_price_text_change;
			$pro_cancel['text_opt_subject_change']		= $groupOpRow->text_opt_subject_change;
			$pro_cancel['text_opt_content_change']		= $groupOpRow->text_opt_content_change;
			$pro_cancel['redelivery_type']		= $groupOpRow->redelivery_type;
			$pro_cancelinfo[$groupOpRow->oc_no][]	= $pro_cancel;
		}

		//구매 확정이 안된 배송완료된 상품이 있으면 카운트를 더한다.
		if ($groupOpRow->op_step == '4' && $groupOpRow->order_conf == '0') $order_conf_not_cnt++;
		//exdebug($groupOpRow);

		if($_ord->oi_step2 >= 40 || $groupOpRow->op_step >= 40){
?>
		<tr bgcolor=#FFD8D8>
<?
		} else {
?>
		<tr bgcolor=#FFFFFF>
<?
		}
?>
			<td align=center class="page_print" style="font-size:8pt"><font color=#878787><?=$cnt?></td>
			<td align=center class="page_screen" style="font-size:8pt">
<?php
		if(!$isGift){
?>
				<input type=checkbox name=chkprcode value="<?=$groupOpRow->productcode?>" <?if($groupOpRow->op_step >= 40) echo " disabled=true";?>>
				<input type=hidden name=idxs value="<?=$groupOpRow->idx?>">
				<input type=hidden name=op_step value="<?=$groupOpRow->op_step?>">
				<input type=hidden name=op_reserve value="<?=$groupOpRow->sum_reserve?>">
<?php
		} else {
?>
				-
<?php
		} // if $isGift
?>
			</td>
				<td align=center style="font-size:8pt">
<?php

		if($vendercnt>0) {
			if($groupOpRow->vender>0) {
?>
					<a href="javascript:viewVenderInfo(<?=$groupOpRow->vender?>)"><B><?=$venderlist[$groupOpRow->vender]->brandname?></B></a>
<?php
			} else {
?>
					&nbsp;
<?php
			}
?>
				</td>
<?php
		} // if $vendercnt

		#상품 이미지
		list($product_img)=pmysql_fetch("SELECT tinyimage FROM tblproduct WHERE productcode='".$groupOpRow->productcode."'");
		$file = getProductImage($Dir.DataDir.'shopimages/product/',$product_img);

		if($file != '') {
			$file=$file;
		} else {
			$file="NO";
		}

		if($file!="NO") {
?>
			<td style="font-size:8pt; padding:7,7,5,5; line-height:10pt;">
				<input type='hidden' name='ord_productname' value="<?=$groupOpRow->productname?>">
				<?=( strlen($groupOpRow->selfcode) ? "진열코드 : ".$groupOpRow->selfcode."<br>" : "" )?>
				<span style="line-height:10pt" onMouseOver='ProductMouseOver(<?=$cnt?>)' onMouseOut="ProductMouseOut('primage<?=$cnt?>');">
				<?=$groupOpRow->productname?> <!--(<?=$product_barcode?>)-->
				<span class="page_screen">
					<!--a href="javascript:ProductInfo('<?=substr($groupOpRow->productcode,0,12)?>','<?=$groupOpRow->productcode?>','YES')"><img src=images/ordtl_icnnewwin.gif align=absmiddle border=0></a --><input type="button" value="새창" class="btn_orange" style="font-size:8pt;margin-top:2px;padding:1px 2px 0px;border-radius: 4px;font-family: '돋움';" onclick="javascript:ProductInfo('<?=substr($groupOpRow->productcode,0,12)?>','<?=$groupOpRow->productcode?>','YES')"> <input type="button" value="상품" class="btn_green" style="font-size:8pt;margin-top:2px;padding:1px 2px 0px;border-radius: 4px;font-family: '돋움';" onclick="javascript:ProductDetail('<?=$groupOpRow->productcode?>')">
				</span>
				</span>
				<?php
						# 상품 옵션 정보 저장 및 출력

						$opt_name	= "";
						if( strlen( trim( $groupOpRow->opt1_name ) ) > 0 ) {
							$opt1_name_arr	= explode("@#", $groupOpRow->opt1_name);
							$opt2_name_arr	= explode(chr(30), $groupOpRow->opt2_name);
							$s_cnt	= 0;
							for($s=0;$s < sizeof($opt1_name_arr);$s++) {
								if ($opt2_name_arr[$s]) {
									if ($s_cnt > 0) $opt_name	.= " / ";
									$opt_name	.= $opt1_name_arr[$s].' : '.$opt2_name_arr[$s];
									$s_cnt++;
								}
							}
							//echo "<br>".$opt_name;
						}

						if( strlen( trim( $groupOpRow->text_opt_subject ) ) > 0 ) {
							$text_opt_subject_arr	= explode("@#", $groupOpRow->text_opt_subject);
							$text_opt_content_arr	= explode("@#", $groupOpRow->text_opt_content);

							for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
								if ($text_opt_content_arr[$s]) {
									if ($opt_name != '') $opt_name	.= " / ";
									$opt_name	.= $text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s];
								}
							}
						}

						if ($opt_name) echo "<br>".$opt_name;


						$tmpOpPrice = $groupOpRow->option_price * $groupOpRow->option_quantity;

				?>

				<?if($storeData['name'] && $groupOpRow->delivery_type != '2'){	//2016-10-07 libe90 매장발송 정보표시?>
					<div style = 'color:blue;'>[<?=$arrDeliveryType[$groupOpRow->delivery_type]?>] <?=$storeData['name']?></div>
					<?if($groupOpRow->delivery_type == '2'){?>
						<div style = 'color:blue;'>예약일 : <?=$groupOpRow->reservation_date?></div>
					<?}?>
				<?}else if($groupOpRow->delivery_type == '2'){?>
					<div style = 'color:blue;'>[<?=$arrDeliveryType['2']?>] <?=$storeData['name']?></div>
					<?
						if ($_ord->receiver_addr) {
							$_ord_receiver_addr	= $_ord->receiver_addr;
							$_ord_receiver_addr	= str_replace("우편번호 :","[",$_ord_receiver_addr);
							$_ord_receiver_addr	= str_replace("주소 :","]",$_ord_receiver_addr);
						}
					?>
					<div style = 'color:blue;'>주소 : <?=$_ord_receiver_addr?></div>
				<?}?>



				<input type='hidden' name='ord_optionname' value='<?=$opt_name?>'>
				<input type='hidden' name='ord_option1' value='<?=$groupOpRow->opt1_name?>'>
				<input type='hidden' name='ord_option2' value='<?=$groupOpRow->opt2_name?>'>
				<input type='hidden' name='ord_option_price_text' value='<?=$groupOpRow->option_price_text?>'>
				<input type='hidden' name='ord_text_opt_subject' value='<?=$groupOpRow->text_opt_subject?>'>
				<input type='hidden' name='ord_text_opt_content' value='<?=$groupOpRow->text_opt_content?>'>
				<div id=primage<?=$cnt?> style="position:absolute; z-index:100; visibility:hidden;">
					<table border=0 cellspacing=1 cellpadding=0 bgcolor=#000000 width=170>
					<tr bgcolor=#FFFFFF>
						<td align=center width=100% height=150><img name=bigimgs src="<?=$file?>" width=100%></td>
					</tr>
					<tr bgcolor=#FFFFFF>
						<td height=54 bgcolor=#f5f5f5>
							<table border=0>
							<tr>
								<td style="line-height:12pt">
									예전 주문서,삭제/이동 상품은 이미지가 일치하지 않을수 있으니 <font color=red>주의하여 배송</font>바랍니다.
								</td>
							</tr>
							</table>
						</td>
					</tr>
					</table>
				</div>
<?php
			if(ord($groupOpRow->addcode)) {
?>
				<br><font color=#0074BA><b>[</b>특수표시 : <?=$groupOpRow->addcode?><b>]</b></font>
<?php
			}
?>
				<?=$packagestr?>
				<?=$assemblestr?>
			</td>
<?php
		} else {
?>
			<td style="font-size:8pt; padding:2,5; line-height:10pt;">
				<?=( strlen($groupOpRow->selfcode) ? "진열코드 : ".$groupOpRow->selfcode."<br>" : "" )?>
<?php
			if(ord($groupOpRow->addcode)) {
?>
				<br><font color=#0074BA><b>[</b>특수표시 : <?=$groupOpRow->addcode?><b>]</b></font>
<?php
			}
?>
				<?=$packagestr?>
				<?=$assemblestr?>
			</td>
<?php
		}
?>
			<td align=right style="font-size:8pt">
<?php
		# 상품 합산가
		if( substr($groupOpRow->productcode,-4)!="GIFT" ){
			if( $_ord->paymethod!="B" || $mode!="update" ){
?>
				<?=number_format( $groupOpRow->price)."&nbsp;"?>
<?php
			} else {
?>
				<input type=text style='font-size:8pt; text-align:right; width:100%' name=arprice value="<?=$groupOpRow->price?>">
<?php
			}
		} else {
?>
				&nbsp;<input type=hidden name=arprice>
<?php
		}
?>
			</td>

<?
		# 추가정보
		if ($groupOpRow->productcode=="99999999999X" || $groupOpRow->productcode=="99999999999R") { // 현금결제면 수량표시안함
?>
			<td><input type=hidden name=arquantity value="1">&nbsp;</td>
<?php
		} else {
?>
			<td align=center style="font-size:8pt">
				<?=$groupOpRow->quantity?>
			</td>
<?php
		}
?>
			<td align=right style="font-size:8pt; padding:2,5; line-height:11pt;"><?=number_format($tmpOpPrice)?></td>
<?php


?>
			<td align=right style="font-size:8pt">
				<?=number_format( $groupOpRow->coupon_price )."&nbsp;"?>
			</td>
<?php
		//echo "<td align=center><a href=\"javascript:OrderUpdate('1',{$cnt},'{$row->vender}','{$row->productcode}','{$tempopt1}','{$row->opt2_name}')\"><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='상품수정'></a><br><img width=0 height=2 border=0><br><a href=\"javascript:OrderDelete('1','{$row->vender}','{$row->productcode}','{$tempopt1}','{$row->opt2_name}')\"><img src='images/ordtl_minidel.gif' align=absmiddle border=0 alt='상품삭제'></a></td>\n";
		# 특수버튼
?>
			<td align=right style="font-size:8pt">
				<?=number_format($use_reserve)."&nbsp;"?>
			</td>
			<td align=right style="font-size:8pt">
			<?=number_format($deil_price)."&nbsp;"?>
			</td>
			<td align=right style="font-size:8pt">
			<?=number_format($each_price)."&nbsp;"?>
			<input type="hidden" name="each_price" value="<?=$each_price?>">
			</td>

		</tr>
<?php
		#배송버튼
		if(($_ord->paymethod!="B" || $mode!="update") && !$isGift) {
			$reordercode_text	= "";
			if ($groupOpRow->op_step == '44' && $groupOpRow->redelivery_type == 'G') {
				list($reordercode)=pmysql_fetch_array(pmysql_query("select oi.ordercode as reordercode from tblorderproduct op left join tblorderinfo oi on op.ordercode=oi.ordercode WHERE oi.oldordno='".$_ord->ordercode."' and op.productcode='".$groupOpRow->productcode."'"));
				if ($reordercode) $reordercode_text	= "<a href=\"JavaScript:OrderDetailView('{$reordercode}')\"><font color=#0074BA>[ 재주문코드는 {$reordercode} 입니다. ]</font></a>&nbsp;&nbsp;&nbsp;";
			}
?>
			<tr bgcolor=#FFFFFF style="font-size:8pt">
				<td align=right style="padding:0px" colspan="<?=($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1))?>" >
					<table border=0 cellpadding=0 cellspacing=0>
					<colgroup>
					<col width="">
					<col width="195">
					<col width="220">
					</colgroup>
					<tr>
						<td style="font-size:8pt;letter-spacing:-0.5pt;"><?=$reordercode_text?>배송업체 :
							<span class="page_screen">
								<select name=chkdeli_com style="width:100;  font-size:9pt">
									<option value="">없음</option>
<?php

			$deli_url="";
			$trans_num="";
			$company_name="";
			for($yy=0;$yy<count($delicomlist);$yy++) {
				if($pg_type=="B" && strstr("QP", $_ord->paymethod[0])) {
					if(ord($delicomlist[$yy]->dacom_code)) {
						echo "		<option value=\"{$delicomlist[$yy]->code}\"";
						if($groupOpRow->deli_com>0 && $groupOpRow->deli_com==$delicomlist[$yy]->code) {
							echo " selected";
							$deli_url=$delicomlist[$yy]->deli_url;
							$trans_num=$delicomlist[$yy]->trans_num;
							$company_name=$delicomlist[$yy]->company_name;
						}
						echo ">{$delicomlist[$yy]->company_name}</option>\n";
					}
				} else {
					echo "		<option value=\"{$delicomlist[$yy]->code}\"";
					if($groupOpRow->deli_com>0 && $groupOpRow->deli_com==$delicomlist[$yy]->code) {
						echo " selected";
						$deli_url=$delicomlist[$yy]->deli_url;
						$trans_num=$delicomlist[$yy]->trans_num;
						$company_name=$delicomlist[$yy]->company_name;
					}
					echo ">{$delicomlist[$yy]->company_name}</option>\n";
				}
			}
			echo "		</select>\n";
			echo "		</span>\n";
			echo "		<span class=\"page_print\">".(ord($company_name)?$company_name:"없음")."</span>\n";
			echo "		</td>\n";
			echo "		<td align='right' style=\"font-size:8pt;letter-spacing:-0.5pt;\">송장번호 : \n";
			echo "		<span class=\"page_screen\">\n";
			echo "		<input type=text name=chkdeli_num value=\"{$groupOpRow->deli_num}\" size=10 maxlength=20 style=\"font-size:9pt\"  class=\"input\"><img width=2 height=0>"; // onkeyup=\"strnumkeyup(this)\"
			if(ord($groupOpRow->deli_num) && ord($deli_url)) {
				if(ord($trans_num)) {
					$arrtransnum=explode(",",$trans_num);
					$pattern=array("[1]","[2]","[3]","[4]");
					$replace=array(substr($groupOpRow->deli_num,0,$arrtransnum[0]),substr($groupOpRow->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($groupOpRow->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($groupOpRow->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
					$deli_url=str_replace($pattern,$replace,$deli_url);
				} else {
					$deli_url.=$groupOpRow->deli_num;
				}
				echo "<input type=button value='추적' class='btn_blue' style=\"padding:2px 5px 1px\" onclick=\"DeliSearch('{$deli_url}')\">";
			} else {
				echo "<input type=button value='추적' class='btn_blue' style=\"padding:2px 5px 1px\">";
			}
			echo "		</span>\n";
			echo "		<span class=\"page_print\">".(ord($groupOpRow->deli_num)?$groupOpRow->deli_num:"없음")."</span>\n";
			echo "		</td>\n";
			echo "		<td style=\"font-size:8pt;letter-spacing:-0.5pt;\">\n";
			//$orderstate = GetOrderState($groupOpRow->deli_gbn, $_ord->paymethod, $_ord->bank_date, $_ord->pay_flag, $_ord->pay_admin_proc, $groupOpRow->receive_ok, $groupOpRow->order_conf );
			echo "	주문상태 : <B>";
			/*
			switch($groupOpRow->deli_gbn) {
				case 'S': echo "발송준비";  break;
				case 'X': echo "배송요청";  break;
				case 'Y': echo "배송";  break;
				case 'D': echo "<font color=blue>취소요청</font>";  break;
				case 'N': echo "미처리";  break;
				case 'E': echo "<font color=red>환불대기</font>";  break;
				case 'C': echo "<font color=red>주문취소</font>";  break;
				case 'R': echo "반송";  break;
				case 'H': echo "배송(<font color=red>정산보류</font>)";  break;
			}
			if($groupOpRow->deli_gbn=="D" && strlen($groupOpRow->deli_date)==14) echo " (배송)";
			*/
           // if($groupOpRow->op_step >= 40) $status = $o_step[$groupOpRow->oi_step1][$groupOpRow->op_step];
           // else $status = $op_step[$groupOpRow->op_step];
           // if($groupOpRow->redelivery_type == "G" && $groupOpRow->op_step == "40") $status = "교환신청";
           // if($groupOpRow->redelivery_type == "G" && $groupOpRow->op_step == "41") $status = "교환접수";
          //  if($groupOpRow->redelivery_type == "G" && $groupOpRow->op_step == "44") $status = "교환완료";

			//echo		$op_step[$groupOpRow->op_step];
			//echo		$status;
			if ($groupOpRow->deli_gbn == 'Y') {
				$_ord_oi_step1	= '3';
			} else if ($groupOpRow->deli_gbn == 'F') {
				$_ord_oi_step1	= '4';
			} else {
				$_ord_oi_step1	= $_ord->oi_step1;
			}
			echo GetStatusOrder("p", $_ord_oi_step1, $_ord->oi_step2, $groupOpRow->op_step, $groupOpRow->redelivery_type);
			if ($groupOpRow->order_conf == '1') echo ' (구매확정)';
			echo "	</B>";
			//if ($groupOpRow->op_step == "1" && $groupOpRow->store_code=='') {	//2016-10-07 libe90 O2O주문은 해당버튼 안나오게 처리
			if ($groupOpRow->op_step == "1" && $groupOpRow->delivery_type=='0') {	//2016-10-11 ssuya O2O주문이아니더라도 store_code가 들어감으로 변경
				echo "&nbsp;&nbsp;<input type=\"button\" style=\"padding:2px 5px 2px\" onclick=\"javascript:deliOneSend('{$groupOpRow->idx}')\" value=\"발송준비\">";
			}
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "</tr>\n";
		}
		if($_ord->deli_type == 2){
				echo "<tr height=30 bgcolor=#efefef>\n
		<td align=center colspan=6><B>해당 주문은 고객 [직접수령] 입니다!!</B></td>
	</tr>\n";
			}
	}
	pmysql_free_result( $groupOpRes);

	if($deliveryData){
		echo "<tr class=\"page_screen\">\n";
		echo "	<td style=\"padding:5,0,5,0\" align = 'right' colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1)).">\n";
		echo $deliveryData;
		echo "	</td>";
		echo "</tr>\n";
	}



		#주문 상품 관련 취소/환불/반품에 따른 요청 버튼 노출 (2016.02.03  - 김재수)
		#주문 상품 관련 취소/환불/반품 요청 S--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------#

		//결제완료 이후부터 노출
		if ($_ord->oi_step1 > 0) {
			echo "<tr class=\"page_screen\">\n";
			if ($_ord->oi_step1 > 1) { // 배송준비부터 노출
				echo "	<td colspan=".($colspan-5).">\n";
			} else {
				echo "	<td colspan=".($colspan).">\n";
			}

			//선택 상품별 발송 준비 및 배송중 처리 - 사용안함 (2016.02.12 - 김재수 막음)
			/*if(strstr("NXS",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C" && $prescd=="Y") {
				echo "	<img src=images/arrow_ltr.gif border=0 align=absmiddle>\n";
				echo "	&nbsp; <B>선택한 상품에 대해서</B>\n";
				echo "	<select name=deli_gbn onchange=\"changeDeli(this)\">\n";
				echo "	<option value=\"\">처리상태 선택</option>\n";
				//echo "	<option value=\"N\">미처리</option>\n"; - 미처리 막음 (2016.02.12 - 김재수 막음)
				echo "	<option value=\"S\">발송준비</option>\n";
				echo "	<option value=\"Y\">배송중</option>\n";
				echo "	</select>\n";
			}
			echo "		&nbsp;\n";*/

			echo "&nbsp;&nbsp;<img src=images/arrow_ltr.gif border=0 align=absmiddle height=7> <B>선택한 상품에 대해서</B>\n";

			if ($_ord->oi_step2 < 40 && $_ord->oi_step1 > 0) { // 정상적인 경우
				if ($_ord->oi_step1 < 3) {
					$re_type=''; // 취소환불만
					$ex_type	= "recoveryquan";
				} else {
					$re_type='B'; // 배송중, 배송완료일 경우 취소(반품+환불)
					$ex_type	= "redelivery";
				}
				$cancel_btn.="<input type='button' onClick=\"javascript:RestoreOrderCancel_chk('".$_ord->oi_step1."','".$_ord->oi_step2."','".$_ord->paymethod[0]."','{$re_type}','{$ex_type}')\" value='취소하기'>";

				if ($_ord->oi_step1 > 2) { // 배송중, 배송완료일 경우 교환 노출
					$re_type2='C'; // 교환
					$cancel_btn.="&nbsp;<input type='button' onClick=\"javascript:RestoreOrderCancel_chk('".$_ord->oi_step1."','".$_ord->oi_step2."','".$_ord->paymethod[0]."','{$re_type2}','redelivery')\" value='교환하기'>";
				}
			}

			echo "		{$cancel_btn}\n";
			if ($_ord->oi_step1 > 1 && $_ord->oi_step1 < 4) echo "		<input type='button' onClick=\"javascript:changeDeliinfo()\" value='배송정보 수정'>\n"; // 배송준비부터 노출
			if ($_ord->oi_step1 == 3) echo "		<input type='button' onClick=\"javascript:deliFin('PART')\" value='구매확정'>\n"; // 배송중일때 노출
			//if ($order_conf_not_cnt > 0) echo "		<input type='button' onClick=\"javascript:orderFin('PART')\" value='구매확정'>\n"; // 구매확정하지 않은 상품이 있을경우 노출
			echo "</td>";

			if ($_ord->oi_step1 > 1 && $_ord->oi_step1 < 4) { // 배송준비부터 노출
			echo "	<td colspan=5 align=right>";
				echo "		배송정보 일괄등록 : \n";
				echo "		<select name=deli_com style=\"width:90;font-size:9pt\">\n";
				echo "		<option value=\"\">없음</option>\n";

				for($yy=0;$yy<count($delicomlist);$yy++) {
					if($pg_type=="B" && strstr("QP", $_ord->paymethod[0])) {
						if(ord($delicomlist[$yy]->dacom_code)) {
							echo "		<option value=\"{$delicomlist[$yy]->code}\">{$delicomlist[$yy]->company_name}</option>\n";
						}
					} else {
						echo "		<option value=\"{$delicomlist[$yy]->code}\">{$delicomlist[$yy]->company_name}</option>\n";
					}
				}
				echo "		</select>\n";
				echo "		<input type=text name=deli_num value=\"\" size=10 maxlength=20 style=\"font-size:9pt\" >\n"; // onkeyup=\"strnumkeyup(this)\"
				echo "<input type=button value='등록' onclick=\"DeliNumUpdate()\">\n";
				echo "		\n";
				echo "</td>";
			}
			echo "</tr>\n";
		}
		echo "</form>\n";
		#주문 상품 관련 취소/환불/반품 요청 E--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------#
?>
</table>
</div>
<!-- 주문 취소 입력폼 시작 (2016.02.03 - 김재수 추가) -->
<div class='area_set' style="display:none;" id='ocf'>
<form name=orderCancelForm id='orderCancelForm' action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type="hidden" name="type" value="redelivery">
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
<input type="hidden" name="oi_step1" value="<?=$_ord->oi_step1?>">
<input type="hidden" name="oi_step2" value="<?=$_ord->oi_step2?>">
<input type="hidden" name="op_step" value="">
<input type="hidden" name="paymethod" value="<?=$_ord->paymethod[0]?>">
<input type="hidden" name="idxs" value="">
<input type="hidden" name="re_type" value="">
<input type="hidden" name="each_price" value="">
<input type="hidden" name="pc_type" value="">
<input type="hidden" name="pgcancel_type" value="">
<input type="hidden" name="pgcancel_res_code" value="">
<input type="hidden" name="pgcancel_res_msg" value="">
<input type="hidden" name="oldordno" value="<?=$_ord->oldordno?>">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class='table_style0'>
	<colgroup><col width="90"><col width=""></colgroup>
	<tbody>
	<tr>
		<td align="left" colspan=2 height=30 bgcolor='#4b4b4b' class='ts0_title ocf_title'></td>
	</tr>
	<tr>
		<td align="center" bgcolor='#EFEFEF'><b>상품정보</b></td>
		<td align='left' class='ts0_list'>
		<table border="0" cellpadding="5" cellspacing="0" width="100%" class='cancelform_productlist'>
			<colgroup><col width="60"><col width=""><col width="50"><col width="340"></colgroup>
			<tbody>
			<tr>
				<td align="center" bgcolor='#EFEFEF'>상품명</td>
				<td align="left"></td>
				<td align="center" bgcolor='#EFEFEF'>옵션</td>
				<td align="left"></td>
			</tr>
			</tbody>
		</table>
		</td>
	</tr>
	<tr>
		<td align="center" bgcolor='#EFEFEF'><b>사유</b></td>
		<td align='left'><select name=sel_code class="select">
		<option value="">======== 전체 ========</option>
<?php
		foreach($oc_code as $key => $val) {
			echo "<option value=\"{$key}\">{$val}</option>\n";
		}
?>
		</select></td>
	</tr>
	<tr>
		<td align="center" bgcolor='#EFEFEF'><b>상세사유</b></td>
		<td align='left'><textarea name="memo" class = 'CLS_msg_part_cancel' style = 'width:100%;height:70px;'></textarea>
		</td>
	</tr>
	<tr class='ocf_bank'>
		<td align="center" bgcolor='#EFEFEF'><b>환불계좌정보</b></td>
		<td align='left'>
		<select name=bankcode class="select">
		<option value="">==== 은행선택 ====</option>
<?php
		foreach($oc_bankcode as $key => $val) {
			echo "<option value=\"{$key}\"";
			if($row->bankcode==$key) echo " selected";
			echo ">{$val}</option>\n";
		}
?>
		</select>
		<input class="select" type="text" name="bankaccount" style='border: 1px solid #cbcbcb;width:220px;' value=''>&nbsp;&nbsp;&nbsp;
		예금주 <input class="select" type="text" name="bankuser" style='border: 1px solid #cbcbcb;width:100px;' value=''></textarea>
		</td>
	</tr>
	<tr>
		<td align="center" colspan=2 height=30 style="padding:5px;" bgcolor='#EFEFEF' class='sbmit_btn'>
			<a href="javascript:;" onClick = 'submitOrderCancel()'><img src='images/btn_ok3.gif' border=0></a>
		</td>
	</tr>
	</tbody>
</table>
</form>
</div>
<!-- 주문 취소 입력폼 끝 (2016.02.03 - 김재수 추가) -->
<?php
		# 쿠폰
		$couponSql = "SELECT op.productname, op.opt1_name, op.opt2_name, op.text_opt_subject, op.text_opt_content, ci.coupon_name, co.dc_price, ci.coupon_type FROM tblcouponinfo ci ";
		$couponSql.= "JOIN tblcoupon_order co ON co.coupon_code = ci.coupon_code ";
		$couponSql.= "left join tblorderproduct op ON op.idx = co.op_idx ";
		$couponSql.= "WHERE co.ordercode = '".$ordercode."' order by op.vender, op.idx";

		//echo $couponSql;

		$couponRes =  pmysql_query( $couponSql, get_db_conn() );
		$couponTotal	= pmysql_num_rows($couponRes);
		if ($couponTotal > 0) {
?>
<div class='area_set'>
	<div class='area_set_title'>
	<span class='ast_title'> 쿠폰사용정보</span><span class='ast_subtitle'>(상품별 쿠폰 사용내역을 볼 수 있습니다)</span>
	</div>
	<table border=0 cellpadding=0 cellspacing=0 width=100% class='table_style0'>
	<col width=300></col>
	<col width=></col>
	<col width=65></col>
	<tr>
		<td align=center bgcolor="#EFEFEF"><b>쿠폰명<b></td>
		<td align=center bgcolor="#EFEFEF"><b>상품명<b></td>
		<td align=center bgcolor="#EFEFEF"><b>할인액<b></td>
	</tr>

<?
		while( $couponRow = pmysql_fetch_object( $couponRes ) ) {
		# 상품 옵션 정보 저장 및 출력


		$cp_opt_name	= "";
		if( strlen( trim( $couponRow->opt1_name ) ) > 0 ) {
			$cp_opt1_name_arr	= explode("@#", $couponRow->opt1_name);
			$cp_opt2_name_arr	= explode(chr(30), $couponRow->opt2_name);
			$s_cnt	= 0;
			for($s=0;$s < sizeof($cp_opt1_name_arr);$s++) {
				if ($cp_opt2_name_arr[$s]) {
					if ($s_cnt > 0) $cp_opt_name	.= " / ";
					$cp_opt_name	.= $cp_opt1_name_arr[$s].' : '.$cp_opt2_name_arr[$s];
					$s_cnt++;
				}
			}
			//$cp_opt_name = "<br>".$cp_opt_name;
		}

		if( strlen( trim( $couponRow->text_opt_subject ) ) > 0 ) {
			$cp_text_opt_subject_arr	= explode("@#", $couponRow->text_opt_subject);
			$cp_text_opt_content_arr	= explode("@#", $couponRow->text_opt_content);

			for($s=0;$s < sizeof($cp_text_opt_subject_arr);$s++) {
				if ($cp_text_opt_content_arr[$s]) {
					if ($cp_opt_name != '') $cp_opt_name	.= " / ";
					$cp_opt_name	.= $cp_text_opt_subject_arr[$s].' : '.$cp_text_opt_content_arr[$s];
				}
			}
		}

		if ($cp_opt_name) $cp_opt_name = "<br>".$cp_opt_name;

		/*$cp_opt_name	= "";
		if( strlen( trim( $couponRow->opt1_name ) ) > 0 ) {
			if( strlen( $couponRow->opt1_name ) > 0 ) {
				$cp_opt_name	.= $couponRow->opt1_name;
				if( strlen( $couponRow->opt2_name) > 0 ) {
					$cp_opt_name	.= ' / '.$couponRow->opt2_name;
				}
			}
			$cp_opt_name = "<br>옵션 : ".$cp_opt_name;
		}*/
?>
	<tr>
		<td align=left style="font-size:8pt;"><?=$couponRow->coupon_name?></td>
		<td align=left style="font-size:8pt;"><?=$couponRow->productname.$cp_opt_name?></td>
		<td align=right style="font-size:8pt;"><?if ($couponRow->coupon_type == '9') { echo "-";} else {echo number_format( $couponRow->dc_price )."원";}?></td>
	</tr>
<?php
		}
		pmysql_free_result( $couponRes );
?>
	</table>
</div>
<?
		}
?>
<div class='area_set'>
	<div class='area_set_title'>
	<span class='ast_title'> 결제금액정보</span><span class='ast_subtitle'>(총 결제 금액을 볼 수 있습니다)</span>
	</div>
	<!-- 주문내역 시작 -->
	<table border=0 cellpadding=0 cellspacing=0 width=100% class='table_style0'>
	<col width=250></col>
	<col width=250></col>
	<col width=250></col>
	<col width=></col>
	<tr>
		<td align=center bgcolor="#EFEFEF"><b>주문금액<b></td>
		<td align=center bgcolor="#EFEFEF"><b>할인액<b></td>
		<td align=center bgcolor="#EFEFEF"><b>배송비<b></td>
		<td align=center bgcolor="#EFEFEF"><b>결제금액<b></td>
	</tr>
<?


	# 총합계 출력 2014-04-18 06:00
	/*
	echo "<tr>\n";
		echo "	<td colspan=".($colspan-4)." style=\"font-size:8pt;padding:5,27\"><B>총 합계</B> </td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=center style=\"font-size:8pt\">&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".number_format($totalInitprice)."&nbsp;</td>\n";
	echo "</tr>\n";
	*/

	// 할인 상품관련 (2016.02.05 - 김재수 막음)
	/*if(count($etcdata)>0) {
		for($j=0;$j<count($etcdata);$j++) {
			$cnt++;
			$sumprice=$etcdata[$j]->price;
			$reserve=$etcdata[$j]->reserve;
			$in_reserve+=$reserve;
			$totalprice+=$sumprice;
			echo "<tr>\n";
			echo "	<td>...&nbsp;</td>\n";
			if($vendercnt>0) {
				if($etcdata[$j]->vender>0) {
					echo "	<td align=center style=\"font-size:8pt\"><a href=\"javascript:viewVenderInfo({$etcdata[$j]->vender})\"><B>{$venderlist[$etcdata[$j]->vender]->id}</B></a></td>\n";
				} else {
					echo "	<td align=center>&nbsp;</td>\n";
				}
			}

			echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">{$etcdata[$j]->productname} <span class=\"page_screen\">";

			//exdebug($etcdata[$j]->productname);
			//echo "  <A style=\"cursor:hand\" onMouseOver='EtcMouseOver($cnt)' onMouseOut=\"EtcMouseOut($cnt);\"><img src=images/btn_more02.gif border=0 align=absmiddle></A>";
			echo "	<div id=etcdtl{$cnt} style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
			echo "	<table border=0 cellpadding=0 cellspacing=0 width=300 bgcolor=#A47917>\n";
			echo "	<tr><td align=center style=\"color:#FFFFFF;padding:5\"><B>###### 해당 상품명 ######</B></td></tr>\n";
			echo "	<tr><td style=\"font-size:8pt;color:#FFFFFF;padding:10;padding-top:0;line-height:11pt\">{$etcdata[$j]->order_prmsg}</td></tr>\n";
			echo "	</table>\n";
			echo "	</div>\n";
			echo "	</span>\n";
			echo "	</td>\n";
			echo "	<td>&nbsp;</td>\n";
			if ($etcdata[$j]->productcode=="99999999999X" || $etcdata[$j]->productcode=="99999999990X" || $etcdata[$j]->productcode=="99999999997X" || substr($etcdata[$j]->productcode,0,3)=="COU" || $etcdata[$j]->productcode=="99999999999R") { // 현금결제면 수량표시안함
				echo "	<td><input type=hidden name=arquantity value=\"1\">&nbsp;</td>\n";
			} else {
				echo "	<td align=center".($_ord->paymethod!="B" || $mode!="update"?($etcdata[$j]->quantity>1?" bgcolor=#FDE9D5 style=\"font-size:8pt\"><font color=#000000><b>":">").$etcdata[$j]->quantity:"><input type=text style='font-size:8pt;text-align:right' name=arquantity value=\"{$etcdata[$j]->quantity}\" style=\"width:100%\">")."</td>\n";
			}
			echo "	<td align=right style=\"font-size:8pt\">";
			if($etcdata[$j]->vender>0) {
				if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && $etcdata[$j]->productcode!="99999999990X" && $etcdata[$j]->productcode!="99999999997X") {
					if($_ord->paymethod!="B" || $mode!="update") {
						echo ($reserve>0?number_format($reserve):"")."&nbsp;";
					} else {
						echo "<input type=hidden name=arreserve>&nbsp;";
					}
				} else {
					echo "<input type=hidden name=arreserve>&nbsp;";
				}
			} else {
				if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && $etcdata[$j]->productcode!="99999999990X" && $etcdata[$j]->productcode!="99999999997X") {
					if($_ord->paymethod!="B" || $mode!="update") {
						echo ($reserve>0?number_format($reserve):"")."&nbsp;";
					} else {
						echo "<input type=text style='font-size:8pt;text-align:right;width:100%' name=arreserve value=\"{$reserve}\">";
					}
				} else {
					echo "<input type=hidden name=arreserve>&nbsp;";
				}
			}
			echo "	</td>\n";

			echo "	<td align=right style=\"font-size:8pt\">".(substr($etcdata[$j]->productcode,-4)!="GIFT"?$_ord->paymethod!="B" || $mode!="update"?number_format($sumprice)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=arprice value=\"{$sumprice}\">":"&nbsp;<input type=hidden name=arprice>")."</td>\n";

			echo "</tr>\n";

		}
	}*/


	if($_ord) {
		// 회원일경우 회원정보(회원메모)를 가져온다.
		if (strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			$sql = "SELECT memo,group_code FROM tblmember WHERE id='{$_ord->id}' ";
			$result=pmysql_query($sql,get_db_conn());
			if ($row=pmysql_fetch_object($result)) {
				$usermemo=$row->memo;
				$group_code=$row->group_code;
			}
			pmysql_free_result($result);

			if(ord($group_code)) {
				$sql = "SELECT group_name FROM tblmembergroup WHERE group_code='{$group_code}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$group_name = $row->group_name;
				}
				pmysql_free_result($result);
			}
		}

		$dc_price=(int)$_ord->dc_price;
		$salemoney=0;
		$salereserve=0;

		// 그룹회원 할인 (안쓰는것 같은데...)
		if($_ord->tot_price_dc){
			/*echo "<tr bgcolor=#FFFFE6>\n";
			echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				echo "	<td>&nbsp;</td>\n";
			}
			echo "	<td style=\"font-size:8pt;padding:2,5\" colspan=6>총 구매금액대별 할인</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($_ord->tot_price_dc>0?($_ord->paymethod!="B" || $mode!="update"?"-".number_format($_ord->tot_price_dc)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=salemoney value=\"-{$salemoney}\">"):"&nbsp;")."</td>\n";
			echo "</tr>\n";*/
		}

		// 그룹회원 할인
		if($dc_price<>0) {
			//$salereserve=$_ord->mem_reserve;
			$salemoney=-$dc_price;
			/*
			if (strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
				$sql = "SELECT b.group_name FROM tblmember a, tblmembergroup b ";
				$sql.= "WHERE a.id='{$_ord->id}' AND b.group_code=a.group_code AND SUBSTR(b.group_code,1,1)!='M'";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$group_name=$row->group_name;
				}
				pmysql_free_result($result);
			}
			echo "<tr bgcolor=#FFFFE6>\n";
			echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				echo "	<td>&nbsp;</td>\n";
			}
			echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=red>그룹회원 할인 : {$group_name}</font></td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($salereserve>0?($_ord->paymethod!="B" || $mode!="update"?number_format($salereserve)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=salereserve value=\"{$salereserve}\">"):"&nbsp;")."</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($salemoney>0?($_ord->paymethod!="B" || $mode!="update"?"-".number_format($salemoney)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=salemoney value=\"-{$salemoney}\">"):"&nbsp;")."</td>\n";
			echo "</tr>\n";
			*/
			//$in_reserve+=$salereserve;
		}

		// 적립금 사용액
		/*if($_ord->reserve>0) {
			echo "<tr bgcolor=#F5F5F5>\n";
			echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				echo "	<td>&nbsp;</td>\n";
			}
			echo "	<td style=\"font-size:8pt;padding:2,5\" colspan=6><font color=#0054A6>적립금사용액</font></td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($_ord->paymethod!="B" || $mode!="update"?"- ".number_format($_ord->reserve)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=usereserve value=\"{$_ord->reserve}\">")."</td>\n";

			echo "</tr>\n";
		}*/

		//$totalprice=$totalprice-$salemoney-$_ord->reserve;

		// 카드 수수료라는데 안쓰는것 같음 (2016.02.05 - 김재수 막음)
		/*if($_shopdata->card_payfee>0 && strstr("CPM",$_ord->paymethod[0]) && $_ord->price<>$totalprice) {
			echo "<tr bgcolor=#F5F5F5>\n";
			echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				echo "	<td>&nbsp;</td>\n";
			}
			echo "	<td style=\"font-size:8pt;padding:2,5\" colspan=6><font color=#F26622>카드수수료</font></td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".number_format($_ord->price-$totalprice)."&nbsp;</td>\n";
			echo "</tr>\n";
		}*/



		$temp = substr($_ord->ordercode,0,4)."/".substr($_ord->ordercode,4,2)."/".substr($_ord->ordercode,6,2)." ".substr($_ord->ordercode,8,2).":".substr($_ord->ordercode,10,2).":".substr($_ord->ordercode,12,2);
		$message=explode("[MEMO]",$_ord->order_msg);
		$message[0]=str_replace("\"","&quot;",$message[0]);
		$message[0]=str_replace("\"","",$message[0]);

		$message[0]=str_replace("\r\n","<br>\n&nbsp;&nbsp;",$message[0]);


		echo "<tr>\n";
		echo "	<td align=right>".number_format($totalprice)."원</td>\n";
		echo "	<td align=right>- ".number_format($tot_cou_price + $in_use_reserve)."원</td>\n";
		echo "	<td align=right>".number_format($tot_deil_price)."원</td>\n";
		echo "	<td align=right style=\"font-size:8pt;color:#0074BA;\"><b>".number_format($_ord->price + $_ord->deli_price -  $_ord->dc_price - $_ord->reserve )."원</b></td>\n";

		echo "</tr>\n";

		// 전체 취소, 부분취소 예전 번전 막음 (2016.02.12 - 김재수 막음)
		/*$candate = date("Ymd",strtotime('-15 day'));
		//echo $_ord->del_gbn."/".$_ord->paymethod[0]."/".$_ord->price;
		if((!strstr("RA", $_ord->del_gbn) && (!strstr("QP",$_ord->paymethod[0]) || $_ord->price==0))
		|| (!strstr("RA", $_ord->del_gbn) && strstr("QP",$_ord->paymethod[0]) && ($_ord->deli_gbn=="C" || substr($_ord->ordercode,0,8)<$candate) && $_ord->deli_gbn!="Y")) {

			//카드실패체크 2014-02-21 카드 실패의 경우 버튼감춤
			$cc_count=pmysql_num_rows(pmysql_query("SELECT * FROM tblorderinfo a
			WHERE a.deli_gbn='N'
			AND (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V')
			AND a.pay_flag='N' AND a.pay_admin_proc='N') and a.ordercode='".$_ord->ordercode."'"));

			if(!$cc_count){

				echo "<tr bgcolor=#FFFFFF height=24 class=\"page_screen\" style='display:none;'>\n";
				echo "	<td align=right colspan=".$colspan."><B>미입금 및 주문취소에 따른 :</B> <!--a href=\"javascript:RestoreOrder('quan','{$_ord->deli_gbn}','{$_ord->reserve}')\"><img src=\"images/ordtl_restorequan.gif\" border=0 align=absmiddle></a-->";
				if($_ord->deli_gbn!="C") {
					echo " <a href=\"javascript:RestoreOrder('can','{$_ord->deli_gbn}','{$_ord->reserve}')\"><img src=\"images/ordtl_restorecan.gif\" border=0 align=absmiddle></a>";
				}
				if($_ord->deli_gbn!="C" && $_ord->reserve>0) {
					echo " <a href=\"javascript:RestoreReserve()\"><img src=\"images/ordtl_restoreres.gif\" border=0 align=absmiddle></a>";
				}
				if($norecan!="Y" && $_ord->deli_gbn!="C" && strstr("YDH",$_ord->deli_gbn) && strlen($_ord->deli_date)==14 && $in_reserve>0 && strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
					echo " <a href=\"javascript:RestoreReserveCancel('{$in_reserve}')\"><img src=\"images/ordtl_restorerescan.gif\" border=0 align=absmiddle></a>";
				}
				echo "&nbsp;</td>\n";
				echo "</tr>\n";

				//140828 적립금 설정 기능 추가//
				if($_ord->reserve>0) {
				echo "<tr bgcolor=#FFFFFF height=24 class=\"page_screen\">\n";
				echo "	<td align=right colspan=\"".$colspan."\"><B>적립금 사용액 수정 :</B> ";
				echo	 "<input type=text id=mod_reserve name=mod_reserve value=\"".$_ord->reserve."\" style=\"width:60px;\" > <input type='button' onClick=\"javascript:modReserve('{$_ord->reserve}')\" value='수정'>&nbsp;&nbsp;<font color=red style='font-size:9pt'>(총 주문금액은 변경되지 않습니다)</font></td>\n";
				echo "</tr>\n";

				}
			}
		}*/

		if($in_reserve>0 && strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			echo "	<tr>\n";
			echo "		<td colspan=4 align=right><font color=#0000FF>적립될 예상금액은</font> <font color=#A00000><b>".number_format($in_reserve)."원</b></font><font color=#0000FF>입니다.</font></td>\n";
			echo "	</tr>\n";
		}

		echo "</table>\n";
?>
</div>

<?
$oc_sql		= "SELECT oc.*, ocr.productinfo FROM tblorder_cancel oc left join tblorder_cancel_restore ocr on oc.rs_no=ocr.rs_no where oc.ordercode='".$_ord->ordercode."' order by oc.oc_no desc";
$oc_result	= pmysql_query($oc_sql,get_db_conn());
$oc_total	= pmysql_num_rows($oc_result);
if ($oc_total > 0) {
?>
<div class='area_set'>
	<div class='area_set_title'>
	<span class='ast_title'> 취소내역리스트</span><span class='ast_subtitle'>(주문취소를 요청한 내역을 볼 수 있습니다)</span>
	</div>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class='table_style0'>
	<colgroup><col width="60"><col width="60"><col width=""><col width="130"></colgroup>
	<tbody>
<?
	$refundinfo	="";
	$pro_arr = "";
	while($oc_row=pmysql_fetch_object($oc_result)) {
		$refundinfo_row	="";
		$regdate = substr($oc_row->regdt,0,4)."-".substr($oc_row->regdt,4,2)."-".substr($oc_row->regdt,6,2)." ".substr($oc_row->regdt,8,2).":".substr($oc_row->regdt,10,2).":".substr($oc_row->regdt,12,2);
		list($step_prev, $step_next, $memo)=pmysql_fetch_array(pmysql_query("select step_prev, step_next, memo from tblorder_cancel_log where oc_no='".$oc_row->oc_no."' order by ocl_no desc limit 1"));
		//var_dump($pro_cancelinfo);

		//echo count($pro_cancelinfo[$oc_row->oc_no]);
		if ($oc_row->restore == 'N') {
			$pro_arr	= $pro_cancelinfo[$oc_row->oc_no];
		} else if ($oc_row->restore == 'Y') {
			$rows_proinfo	= explode("|!@#|",$oc_row->productinfo);
			for($k=0;$k < count($rows_proinfo); $k++) {
				$pro_row	= explode("!@#",$rows_proinfo[$k]);
				$pro_arr[$k]['idx']									= $pro_row[0];
				$pro_arr[$k]['productname']					= $pro_row[1];
				$pro_arr[$k]['option_quantity']					= $pro_row[2];
				$pro_arr[$k]['opt1_name']						= $pro_row[3];
				$pro_arr[$k]['opt2_name']						= $pro_row[4];
				$pro_arr[$k]['option_price_text']				= $pro_row[5];
				$pro_arr[$k]['text_opt_subject']				= $pro_row[6];
				$pro_arr[$k]['text_opt_content']				= $pro_row[7];
				$pro_arr[$k]['opt1_change']					= $pro_row[8];
				$pro_arr[$k]['opt2_change']					= $pro_row[9];
				$pro_arr[$k]['option_price_text_change']	= $pro_row[10];
				$pro_arr[$k]['text_opt_subject_change']	= $pro_row[11];
				$pro_arr[$k]['text_opt_content_change']	= $pro_row[12];
				$pro_arr[$k]['redelivery_type']					= $pro_row[13];
			}
		}

		$re_type_text	="";
		$re_type_text2	="";
		$re_pro_text	="";
		$re_pro_text2	="";
		 foreach($pro_arr as $key => $val) {
			if ($val['redelivery_type'] == 'Y') {
				if (!$re_type_text) $re_type_text	= "반품";

			} else if($val['redelivery_type'] == 'G') {
				if (!$re_type_text) $re_type_text	= "교환";
			} else {
				 if ($_ord->oi_step1 == 0) {
					if (!$re_type_text) $re_type_text	= "취소";
				 } else {
					if (!$re_type_text) $re_type_text	= "환불";
				 }
			}
			if ($oc_row->restore == 'Y') if (!$re_type_text2) $re_type_text2	= "<br>(복원)";
			# 상품 옵션 출력

			$oc_opt_name	= "";
			if( strlen( trim( $val['opt1_name'] ) ) > 0 ) {

				$oc_opt1_name_arr	= explode("@#", $val['opt1_name']);
				$oc_opt2_name_arr	= explode(chr(30), $val['opt2_name']);
				$s_cnt	= 0;
				for($s=0;$s < sizeof($oc_opt1_name_arr);$s++) {
					if ($oc_opt2_name_arr[$s]) {
						if ($s_cnt > 0) $oc_opt_name	.= " / ";
						$oc_opt_name	.= $oc_opt1_name_arr[$s].' : '.$oc_opt2_name_arr[$s];
						$s_cnt++;
					}
				}
			}

			if( strlen( trim( $val['text_opt_subject'] ) ) > 0 ) {
				$oc_text_opt_subject_arr	= explode("@#", $val['text_opt_subject']);
				$oc_text_opt_content_arr	= explode("@#", $val['text_opt_content']);

				for($s=0;$s < sizeof($oc_text_opt_subject_arr);$s++) {
					if ($oc_text_opt_content_arr[$s]) {
						if ($oc_opt_name != '') $oc_opt_name	.= " / ";
						$oc_opt_name	.= $oc_text_opt_subject_arr[$s].' : '.$oc_text_opt_content_arr[$s];
					}
				}
			}

			if($val['redelivery_type'] == 'G') {
				$oc_opt_name	.= " >> ";
				$oc_opt_name	.= "<font color='#0074BA'>";

				if( strlen( $val['opt1_change'] ) > 0 ) {
					$oc_optc1_name_arr	= explode("@#", $val['opt1_change']);
					$oc_optc2_name_arr	= explode(chr(30), $val['opt2_change']);
					for($ss=0;$ss < sizeof($oc_optc1_name_arr);$ss++) {
						if ($ss > 0) $oc_opt_name	.= " / ";
						$oc_opt_name	.= $oc_optc1_name_arr[$ss].' : '.$oc_optc2_name_arr[$ss];
					}
				}

				if( strlen( trim( $val['text_opt_subject_change'] ) ) > 0 ) {
					$oc_text_opt_subject_arr	= explode("@#", $val['text_opt_subject_change']);
					$oc_text_opt_content_arr	= explode("@#", $val['text_opt_content_change']);

					for($s=0;$s < sizeof($oc_text_opt_subject_arr);$s++) {
						if ($oc_text_opt_content_arr[$s]) {
							if ($oc_opt_name != '') $oc_opt_name	.= " / ";
							$oc_opt_name	.= $oc_text_opt_subject_arr[$s].' : '.$oc_text_opt_content_arr[$s];
						}
					}
				}

				$oc_opt_name	.= "</font>";
			}
			if ($oc_opt_name) $oc_opt_name = "&nbsp;&nbsp;&nbsp;(".$oc_opt_name.")";

			 $re_pro_text .= "- ".$val['productname']."&nbsp;".$val['option_quantity']."개".$oc_opt_name."&nbsp;&nbsp;&nbsp;<font color='#EA0095'>".$memo."</font><br>";
			 $re_pro_text2 .= "- ".$val['productname']."&nbsp;".$val['option_quantity']."개".$oc_opt_name."<br>";
		 }
		// 환불 리스트를 위해 배열로 묶어둔다(퀴리 다시 안날리려고 함)
		if ($oc_row->rfindt) { // 환불처리가 완료된 경우
			$refundinfo_row['rfee']	= $oc_row->rfee;
			$refundinfo_row['rprice']	= $oc_row->rprice;
			$refundinfo_row['rfindt']	= substr($oc_row->rfindt,0,4)."-".substr($oc_row->rfindt,4,2)."-".substr($oc_row->rfindt,6,2)." ".substr($oc_row->rfindt,8,2).":".substr($oc_row->rfindt,10,2).":".substr($oc_row->rfindt,12,2);
			$refundinfo_row['bankcode']	= $oc_row->bankcode;
			$refundinfo_row['bankaccount']	= $oc_row->bankaccount;
			$refundinfo_row['bankuser']	= $oc_row->bankuser;
			$refundinfo_row['product_text']	= $re_pro_text2;
			$refundinfo[]	= $refundinfo_row;
		}

        $rowspan = 3;
        if($oc_row->bankaccount != "") $rowspan++;
        if($oc_row->bankusertel != "") $rowspan++;
?>
	<tr>
		<td align="center" style="font-size:9pt" rowspan=<?=$rowspan?>><font color="#EA0095"><?=$re_type_text.$re_type_text2?></font><?if (($step_next == '41' && !strstr("Q",$_ord->paymethod[0])) || $step_next == '40') {?><input type="button" value="복원" class="btn_blue" style="padding:2px 5px 1px;margin-top:5px;" onClick="javascript:ordercancel_restore('<?=$_ord->ordercode?>','<?=$oc_row->oc_no?>');"><?}?></td>
		<td align='center' style="font-size:8pt" bgcolor='#EFEFEF'><b>사유</b></td>
		<td align='left' style="font-size:8pt"><?if($oc_row->code) { echo $oc_code[$oc_row->code]; } else { echo "-"; }?></td>
		<td align='center' style="font-size:8pt"><?=$regdate?></td>
	</tr>
	<tr>
		<td align='center' style="font-size:8pt" bgcolor='#EFEFEF'><b>상세사유</b></td>
		<td align='left' colspan=2 style="font-size:8pt"><?if ($oc_row->memo) { echo nl2br($oc_row->memo); } else { echo "-"; }?></td>
	</tr>
	<tr>
		<td align='center' style="font-size:8pt" bgcolor='#EFEFEF'><b>상품</b></td>
		<td align='left' colspan=2 style="font-size:8pt"><?=$re_pro_text?></td>
	</tr>
<?
        if($oc_row->bankaccount != "") {
?>
	<tr>
		<td align='center' style="font-size:8pt" bgcolor='#EFEFEF'><b>환불계좌</b></td>
		<td align='left' colspan=2 style="font-size:8pt"><?=$oc_bankcode[$oc_row->bankcode]." ".$oc_row->bankaccount." ".$oc_row->bankuser?></td>
	</tr>
<?
        }
?>
<?
        if($oc_row->bankusertel != "") {
?>
	<tr>
		<td align='center' style="font-size:8pt" bgcolor='#EFEFEF'><b>연락처</b></td>
		<td align='left' colspan=2 style="font-size:8pt"><?=$oc_row->bankusertel?></td>
	</tr>
<?
        }
?>
<?
	}
	pmysql_free_result($oc_result);
?>
	</tbody>
	</table>
</div>
<?
}
//var_dump(trim($refundinfo));
if (sizeof($refundinfo) > 0 && $refundinfo != '' && $_ord->oi_step1 > 0) {
?>
<div class='area_set'>
	<div class='area_set_title'>
	<span class='ast_title'> 환불내역정보</span><span class='ast_subtitle'>(아래는 이미 환불완료된 내역입니다)</span>
	</div>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class='table_style0'>
	<colgroup><col width="60"><col width="60"><col width="120"><col width="60"><col width="120"><col width="60"><col width=""><col width="60"><col width="130"></colgroup>
	<tbody>
<?
	$refund_cnt	= 1;
	foreach($refundinfo as $key => $val) {
?>
	<tr>
		<td align="center" style="font-size:8pt" rowspan=2><?=$refund_cnt?></td>
		<td align='center' style="font-size:8pt" bgcolor='#EFEFEF'><b>수수료</b></td>
		<td align='right' style="font-size:8pt"><?=number_format($val['rfee'])?>원</td>
		<td align='center' style="font-size:8pt" bgcolor='#EFEFEF'><b>환불금액</b></td>
		<td align='right' style="font-size:8pt"><b><font color="red"><?=number_format($val['rprice'])?>원</font></b></td>
		<td align='center' style="font-size:8pt" bgcolor='#EFEFEF'><b>환불계좌</b></td>
		<td align='left' style="font-size:8pt"><?=$oc_bankcode[$val['bankcode']]." ".$val['bankaccount']." ".$val['bankuser']?></td>
		<td align='center' style="font-size:8pt" bgcolor='#EFEFEF'><b>처리일</b></td>
		<td align='center' style="font-size:8pt"><?=$val['rfindt']?></td>
	</tr>
	<tr>
		<td align='center' style="font-size:8pt" bgcolor='#EFEFEF'><b>상품</b></td>
		<td align='left' colspan=7 style="font-size:8pt"><?=$val['product_text']?></td>
	</tr>
<?
		$refund_cnt++;
	}
?>
	</tbody>
	</table>
</div>
<?
}
?>

<div class='area_set'>
	<div class='area_set_title'>
	<span class='ast_title'>주문자 정보</span><span class='ast_subtitle'>(상품 주문자의 정보를 볼 수 있습니다.)</span>
	</div>
<?

		echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% class='table_style0'>\n";
		echo "	<form name=form2 method=post action=\"{$_SERVER['PHP_SELF']}\">\n";
		echo "	<input type=hidden name=type>\n";
		echo "	<input type=hidden name=ordercode value=\"{$_ord->ordercode}\">\n";
		echo "	<input type=hidden name=id value=\"".urlencode($_ord->id)."\">\n";
		echo "	<input type=hidden name=clameno>\n";
		echo "	<input type=hidden name=op_idx>\n";
		echo "	<col width=120 style=\"padding-left:3\"></col>\n";
		echo "	<col width=></col>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor='#EFEFEF'><B>주문 일자</B></td>\n";
		echo "		<td>".$temp;
		if(($_ord->del_gbn=="Y" || $_ord->del_gbn=="R") && !strstr("Y",$_ord->deli_gbn)) {
			echo " &nbsp;&nbsp;&nbsp;<font color=#0074BA>[주문자가 내용삭제 버튼을 누른 주문서]</font>";
		}
		echo "		</td>\n";
		echo "	</tr>\n";

		echo "		<td bgcolor='#EFEFEF'><B>주문 코드</B></td>\n";
		echo "		<td>".$ordercode;
		if($_ord->oldordno) {
			echo " &nbsp;&nbsp;&nbsp;<a href=\"JavaScript:OrderDetailView('{$_ord->oldordno}')\"><font color=#0074BA>[주문코드 ".$_ord->oldordno."의 재주문건 입니다.]</font></a>";
		}
		echo "		</td>\n";
		echo "	</tr>\n";

		echo "	<tr>\n";
		echo "		<td bgcolor='#EFEFEF'><B>주문자</B></td>\n";
		echo "		<td style='position:relative'>".$_ord->sender_name;
		if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			echo "({$_ord->id}) ";
			if(ord($group_name)) echo " [ 그룹명 : {$group_name} ] ";
			if($hidedisplay!="Y") {
				echo "<a href=\"javascript:MemberMemo('{$_ord->id}')\"><img src='images/ordtl_icnmemo.gif' align=absmiddle border=0 alt='메모 입력/수정하기'></a> ";
				echo "<a href=\"javascript:CrmView('{$_ord->id}')\"><img src='images/ordtl_icncrm.png' align=absmiddle border=0 alt='CRM'></a> ";
				if(ord(trim($usermemo))) {
					echo "<div id=\"membermemo_layer\" style=\"position:absolute; z-index:20;top:3px; left:300px;width:300;\"><table border=0 cellspacing=0 cellpadding=1 bgcolor=#7F7F65><tr><td style=\"padding:3\"><font color=#ffffff>{$usermemo} <a href=\"javascript:HideMemo()\"><img src=\"images/x.gif\" align=absmiddle border=0 alt=\"숨기기\"></a>&nbsp;</td></tr></table></div>";
				}
			}
		} else {
			echo "(비회원주문)";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		if (ord($_ord->ip)) {
			$ip = $_ord->ip;
			echo "	<tr>\n";
			echo "		<td bgcolor='#EFEFEF'><B>주문자IP</B></td>\n";
			echo "		<td>{$ip}</td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td bgcolor='#EFEFEF'><B>연락처</B></td>\n";
		echo "		<td><img src=\"images/ordtl_icntel.gif\" align=absmiddle>전화 : {$_ord->sender_tel}";
		if($smsok) {
			echo "<span class=\"page_screen\">&nbsp;<a href=\"javascript:SendSMS('{$_ord->sender_tel}','{$_ord->receiver_tel1}','{$_ord->receiver_tel1}')\"><img src=\"images/ordtl_icnsms.gif\" border=0 align=absmiddle alt='sms보내기'></a></span>";
		}
		echo "<img src=\"images/ordtl_icnemail.gif\" align=absmiddle>이메일 : <a href=\"javascript:SendMail('{$_ord->sender_email}')\"><font color=#AA0000>{$_ord->sender_email}</font></a>";
		echo "		</td>\n";
		echo "	</tr>\n";

		// 누적주문표시여부
		if ($hidedisplay!="Y") {
			if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
				$sql = "SELECT COUNT(*) as cnt, SUM(price) as money FROM tblorderinfo ";
				$sql.= "WHERE id='{$_ord->id}' AND oi_step1='4' AND oi_step2='0' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$ordercnt=$row->cnt;
					$ordersum=$row->money;
				}
				pmysql_free_result($result);
				echo "	<tr>\n";
				echo "		<td bgcolor=#7F7F65><font color=#ffffff>누적 주문</font></td>\n";
				echo "		<td bgcolor=#7F7F65 style=\"color:#ffffff\">";
				if($ordercnt!=0) {
					echo "주문횟수 {$ordercnt}건, 총주문금액 ".number_format($ordersum)." (배송완료 기준) ";
				} else {
					echo "첫구매 고객입니다.";
				}
				echo "		&nbsp;&nbsp;<A HREF=\"javascript:HideDisplay()\"><img src=\"images/x.gif\" align=absmiddle border=0 alt=\"숨기기\"></A>";
				echo "		</td>\n";
				echo "	</tr>\n";
			}
		}
?>
</table>
</div>
<div class='area_set'>
	<div class='area_set_title'>
	<span class='ast_title'>수령자 정보</span><span class='ast_subtitle'>(상품 수령자의 정보를 볼 수 있습니다.)</span>
	</div>
<?

		echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% class='table_style0'>\n";
		echo "	<col width=120 style=\"padding-left:3\"></col>\n";
		echo "	<col width=></col>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor='#EFEFEF'><B>받는분</B></td>\n";
//		echo "		<td>{$_ord->receiver_name}</td>\n";
		echo "		<td><input type='text' size='10' name='receiver_name' value='{$_ord->receiver_name}'>&nbsp;<!--input type=button value='입 력' onclick=\"MemoUpdate()\"--></td>\n";	//2016-10-07 libe90 배송지정보변경 제한
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td valign=top bgcolor='#EFEFEF'><B>받는 주소</B></td>\n";
		echo "		<td>\n";
		echo "		<span class=\"page_screen\">\n";
		$address = str_replace("\n"," ",trim($_ord->receiver_addr));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$post = str_replace("우편번호 : ","",$post);
		$arpost = explode("-",$post);
		$zonecode	= $post;
		echo "		<input name=zonecode size=5 value=\"{$zonecode}\" id='zonecode' readonly='' onclick=\"this.blur();openDaumPostcode();\"><input type=hidden name=post1 size=3 value=\"{$arpost[0]}\" id='post1'><input type=hidden name=post2 size=3 value=\"{$arpost[1]}\" id='post2'>\n";
		echo "		<!--input type=button value='우편번호검색' onclick=\"openDaumPostcode();\"-->\n";	//2016-10-07 libe90 배송지정보변경 제한
		if(!$chkDeliveryType){
			echo "		<b style = 'color:blue;'>해당 주문은 당일배송이 포함된 주문입니다. 주소 변경이 주의 하세요.</b>\n";
		}
		echo "		<br><input type=text name=address1 id='address1' size=108 value=\"{$address}\" style='margin-top:5px'> <!--input type=button value='주소수정' onclick=\"AddressUpdate('".$chkDeliveryType."')\"-->\n";	//2016-10-07 libe90 배송지정보변경 제한
		echo "		</span>\n";
		echo "		<span class=\"page_print\">\n";
		echo "		우편번호 : {$arpost[0]}-{$arpost[1]}<br>\n";
		echo "		&nbsp;&nbsp;주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소: {$address}\n";
		echo "		</span>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor='#EFEFEF'><B>연락처</B></td>\n";
		echo "		<td><img src=\"images/ordtl_icntel.gif\" align=absmiddle>전 &nbsp; 화: <input type='text' name='receiver_tel1' size='12' value='{$_ord->receiver_tel1}'> , <input type='text' name='receiver_tel2' size='14' value='{$_ord->receiver_tel2}'>&nbsp;<!--input type=button value='입 력' onclick=\"MemoUpdate()\"--></td>\n";	//2016-10-07 libe90 배송지정보변경 제한
		echo "	</tr>\n";
?>
</table>
</div>
<div class='area_set'>
	<div class='area_set_title'>
	<span class='ast_title'>결제 정보</span><span class='ast_subtitle'>(주문에 대한 결제정보를 볼 수 있습니다.)</span>
	</div>
<?

		echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% class='table_style0'>\n";
		echo "	<col width=120 style=\"padding-left:3\"></col>\n";
		echo "	<col width=></col>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor='#EFEFEF'><B>결제 방법</B></td>\n";
		echo "		<td>";

		$pgdate = date("YmdHi",strtotime('-2 hour'));
		#$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드",/*"P"=>"신용카드(매매보호)",*/"M"=>"핸드폰");

		if($_ord->pay_data=="신용카드결제 - 카드작성중" && substr($_ord->ordercode,0,12)<=$pgdate) $_ord->pay_data=$arpm[$_ord->paymethod[0]]." 에러";

		if (strstr("BOQ",$_ord->paymethod[0])) {	//무통장, 가상계좌, 가상계좌 에스크로
			if($_ord->paymethod=="B"){
				echo "<font color=#FF5D00>무통장입금</font>\n";
			}else if($_ord->paymethod[0]=="O"){
				if($_ord->sabangnet_idx){
					echo "<font color=#FF5D00>사방넷 연동 주문</font>\n";
				}else{
					echo "<font color=#FF5D00>가상계좌</font>\n";
				}
			}else{
				echo "매매보호 - 가상계좌";
			}

			if(!strstr("CD",$_ord->deli_gbn) || $_ord->paymethod=="B"){
				if($_ord->paymethod=="B"){
					$arrpayinfo=explode("=",$_data->bank_account);
					$arrpayinfo=explode(",",$arrpayinfo[0]);
					echo "<select name='pay_data'>";
					foreach($arrpayinfo as $v){
						$selected = $v==$_ord->pay_data?"selected":"";
						echo "	<option value='{$v}' {$selected}>{$v}</option>";
					}
					echo "</select>&nbsp;<input type=button value='입 력' onclick=\"MemoUpdate()\">";
				}else{
					if($_ord->sabangnet_idx){
						echo "【 ".$arraySabangnetShopCode[$_ord->sabangnet_mall_id]." 】";
					}else{
						echo "【 {$_ord->pay_data} 】";
					}
				}
			}else{
				echo "【 계좌 취소 】";
			}

			if($_ord->sabangnet_idx){
				$cashInCheckStr = "제휴몰주문";
			}else{
				$cashInCheckStr = "입금확인";
			}

			if (strlen($_ord->bank_date)>=12) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td bgcolor='#EFEFEF'><FONT COLOR=red><B>".$cashInCheckStr."</B></FONT></td>\n";
				echo "	<td><B><font color=red>".substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")</font></B>";
			} elseif(strlen($_ord->bank_date)==9) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td bgcolor='#EFEFEF'><FONT COLOR=red><B>".$cashInCheckStr."</B></FONT></td>\n";
				echo "	<td><B><font color=red>환불</font></B>";
			}
		} elseif($_ord->paymethod[0]=="M") {	//핸드폰 결제
			echo "핸드폰 결제【 ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C" && $_ord->oi_step2 >= 44) echo "<font color=red>결제취소 완료</font>";
				else echo "<font color=red>결제가 성공적으로 이루어졌습니다.</font>";
			}
			else echo "결제가 실패되었습니다.";
			echo " 】";
		} elseif($_ord->paymethod[0]=="P") {	//매매보호 신용카드
			echo "매매보호 - 신용카드";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") {
					if($_ord->oi_step2 >= 44) echo "【 <font color=red>카드결제 취소완료</font> 】";
					else echo "【 카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no} 】";
				} else if($_ord->pay_admin_proc=="Y") {
					echo "【 카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no} 】";
				}
			}
			else echo "【 {$_ord->pay_data} 】";
		} elseif ($_ord->paymethod[0]=="C") {	//일반신용카드
			echo "<font color=#FF5D00>신용카드</font>\n";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") {
					if($_ord->oi_step2 >= 44) echo "【 <font color=red>카드결제 취소완료</font> 】";
					else echo "【 카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no} 】";
				} else if($_ord->pay_admin_proc=="Y") {
					echo "【 카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no} 】";
				}
				echo " <input type=\"button\" value=\"신용카드 매출전표\" class=\"btn_blue\" style=\"padding:2px 5px 1px;\" onClick=\"javascript:pop_receiptCardView('".$_ord->ordercode."');\">";
			}
			else echo "【 {$_ord->pay_data} 】";
		} elseif ($_ord->paymethod[0]=="V") {
			echo "실시간 계좌이체 : ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C" && $_ord->oi_step2 >= 44) echo "【 <font color=005000> [환불]</font> 】";
				else echo "<font color=red>{$_ord->pay_data}</font>";
			}
			else echo "결제가 실패되었습니다.";
		} elseif($_ord->paymethod[0]=="Y") {	//PAYCO 결제
			echo "PAYCO 결제【 ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C" && $_ord->oi_step2 >= 44) echo "<font color=red>결제취소 완료</font>";
				else echo "<font color=red>결제가 성공적으로 이루어졌습니다.</font>";
			}
			else echo "결제가 실패되었습니다.";
			echo " 】";
		}

		if(strstr("QP",$_ord->paymethod[0]) && strstr("Y",$_ord->escrow_result) && $_ord->deli_gbn!="C") echo " - <font color=red><b>[구매확인]</b></font>";
		elseif(strstr("QP",$_ord->paymethod[0]) && strstr("C",$_ord->escrow_result) && $_ord->deli_gbn=="C") echo " - <font color=red><b>[구매취소]</b></font>";
		echo "		</td>\n";
		echo "	</tr>\n";
?>
</table>
</div>
<div class='area_set'>
	<div class='area_set_title'>
	<span class='ast_title'>주문상태정보</span><span class='ast_subtitle'>(주문상태에 대한 정보를 확인할 수 있습니다.)</span>
	</div>
<?
        //$ord_status = $o_step[$_ord->oi_step1][$_ord->oi_step2];
       // if($_ord->redelivery_type == "G" && $_ord->oi_step2 == "40") $ord_status = "교환신청";
       // if($_ord->redelivery_type == "G" && $_ord->oi_step2 == "41") $ord_status = "교환접수";
        //if($_ord->redelivery_type == "G" && $_ord->oi_step2 == "44") $ord_status = "교환완료";

		echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% class='table_style0'>\n";
		echo "	<col width=120 style=\"padding-left:3\"></col>\n";
		echo "	<col width=></col>\n";
		//$ardelivery=array("Y"=>"배송중","N"=>"미발송","C"=>"주문취소","X"=>"배송요청","S"=>"발송준비","D"=>"취소요청","E"=>"환불대기","H"=>"배송(정산보류)");
		//$orderstate_info = GetOrderState($_ord->deli_gbn, $_ord->paymethod, $_ord->bank_date, $_ord->pay_flag, //$_ord->pay_admin_proc, $_ord->receive_ok, $_ord->order_conf );

		$ord_status =  GetStatusOrder("o", $_ord->oi_step1, $_ord->oi_step2, "", $_ord->redelivery_type);
		if ($_ord->order_conf == '1') $ord_status .= ' (구매확정)';

		echo "	<tr>\n";
		echo "		<td bgcolor='#EFEFEF'><B>주문 상태</B></td>\n";
		//echo "		<td><font color=#A00000>{$o_step[$_ord->oi_step1][$_ord->oi_step2]}</font>";
        echo "		<td><font color=#A00000>{$ord_status}</font>";

		// KCP 매매보호 가상계좌이면서 취소요청의 경우 환불계좌 미리 등록
		if(strstr("Q",$_ord->paymethod[0]) && strlen($_ord->bank_date)==14 && ($pg_type!="C" && $pg_type!="D")) {
			if(strlen($_ord->deli_date)!=14) {
				//echo " <a href='javascript:escrow_bank_account()'><font color=red><U>[환불계좌수기입력]</U></font></a>";
			}
		}

		echo "		</td>\n";
		echo "	</tr>\n";
?>
</table>
</div>
<div class='area_set'>
	<div class='area_set_title'>
	<span class='ast_title'>요청사항/주문메모</span><span class='ast_subtitle'>(요청사항 및 주문에 대한 메모가 가능합니다.)</span>
	</div>
<?

		echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% class='table_style0'>\n";
		echo "	<col width=120></col>\n";
		echo "	<col width=></col>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor='#EFEFEF'><B>요청사항</B></td>\n";
		echo "		<td>".($_ord->order_msg2)."</td>\n";
		echo "	</tr>\n";

		if($_ord->sabangnet_idx){
			$sqlClame = "SELECT * FROM tblorderclame WHERE sabang_order_id = '".$_ord->sabangnet_order_id."'";
			$resultClame=pmysql_query($sqlClame, get_db_conn());
			echo "	<tr height=58 class=\"page_screen\">\n";
			echo "		<td valign=top style=\"padding-top:8\">클레임 내역</td>\n";
			echo "		<td style=\"padding-top:3\">\n";
			if($rowClame=pmysql_fetch_object($resultClame)) {
				if($rowClame->sabang_flag == 'Y'){
					$strClame = "클레임 처리 취소";
				}else{
					$strClame = "클레임 처리 확인";
				}

				echo "			<div style = 'color:#FF5D00;'>[ ".$arraySabangnetShopCode[$_ord->sabangnet_mall_id]." ] <a href = 'javascript:ClameUpdate(\"".$rowClame->no."\");'>[".$strClame."]</a></div>\n";
				echo "			<div>".nl2br($rowClame->sabang_clame_contents)."</div>\n";
			}
			echo "		</td>\n";
			echo "	</tr>\n";
		}else{
			echo "	<tr height=22>\n";
			echo "		<td bgcolor='#EFEFEF'><B>주문요청사항</B></td>\n";
			echo "		<td ><textarea name=memo0 cols=110 rows=3 style=\"font-size:9pt;vertical-align: middle;\">{$message[0]}</textarea>&nbsp;<input type=button style='padding:14px 14px 16px 14px' value='입 력' onclick=\"MemoUpdate()\"></td>\n";

			echo "	</tr>\n";
			$j=0;
			if(ord($prdata[$j]->order_prmsg)) {
				echo "	<tr height=22 class=\"page_screen\">\n";
				echo "		<td valign=middle>주문메세지</td>\n";
				echo "		<td style=\"padding-left:7\">";
				//echo "	<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> {$prdata[$j]->productname}<BR>\n";
				echo "<textarea style=\"width:95%;height:38;overflow-x:hidden;overflow-y:auto;\" readonly>{$prdata[$j]->order_prmsg}</textarea>\n";
				echo "		</td>\n";
				echo "	</tr>\n";

				echo "	<tr height=22 class=\"page_print\">\n";
				echo "		<td valign=middle>주문메세지</td>\n";
				echo "		<td style=\"padding-left:7\">";
				echo "		<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> {$prdata[$j]->productname}<BR>\n";
				echo "		{$prdata[$j]->order_prmsg}";
				echo "		</td>\n";
				echo "	</tr>\n";
			}

			/*echo "	<tr height=58 class=\"page_screen\">\n";
			echo "		<td bgcolor='#EFEFEF'><B>주문관련 메모</B></td>\n";
			echo "		<td valign='top'>\n";
			echo "		<font style=\"line-height:20px\"><textarea name=memo1 cols=110 rows=3 style=\"font-size:9pt;vertical-align: middle;\">{$message[1]}</textarea>&nbsp;<input type=button style='padding:14px 14px 16px 14px' value='입 력' onclick=\"MemoUpdate()\"><br>	&nbsp;&nbsp;<font color=#0074BA style=\"font-size:8pt;\">*쇼핑몰 운영자만 확인할수 있는 주문관련 메모를 남길 수 있습니다.</font>";
			echo "		</td>\n";
			echo "	</tr>\n";
			if(ord($message[1])) {
				echo "	<tr height=58 class=\"page_print\">\n";
				echo "		<td>주문관련 메모</td>\n";
				echo "		<td>\n";
				echo "		{$message[1]}\n";
				echo "		</td>\n";
				echo "	</tr>\n";
			}*/

			echo "	<tr height=58 class=\"page_screen\">\n";
			echo "		<td bgcolor='#EFEFEF'><B>주문 메모</B>&nbsp;&nbsp;&nbsp;<input type=\"button\" value=\"등록\" class=\"btn_blue\" style=\"padding:2px 5px 1px;margin-top:5px;\" onClick=\"javascript:order_admin_memo('insert', '".$_ord->ordercode."','');\"></td>\n";
			echo "		<td valign='top'>\n";
			echo "		<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$sqlMemo = "SELECT * FROM tblorder_memo WHERE ordercode = '".$_ord->ordercode."' order by om_no desc";
			$resultMemo=pmysql_query($sqlMemo, get_db_conn());

			while($rowMemo=pmysql_fetch_object($resultMemo)) {
				echo "		<tr>\n";
				echo "			<td style='font-size:11px;border-bottom:1px dotted #e3e3e3'>\n";
				echo "				<b>".$rowMemo->memo_id."</b> [".$rowMemo->regdt."]\n";
				if ($_ShopInfo->id == $rowMemo->memo_id && $rowMemo->memo_id_type == 'A') {
					echo "				<input type=\"button\" value=\"수정\" class=\"btn_blue\" style=\"padding:2px 3px 1px;margin-top:5px;font-size:11px;\" onClick=\"javascript:order_admin_memo('update', '".$rowMemo->ordercode."','".$rowMemo->om_no."');\">";
					echo "				<input type=\"button\" value=\"삭제\" class=\"btn_blue\" style=\"padding:2px 3px 1px;margin-top:5px;font-size:11px;\" onClick=\"javascript:order_admin_memo('del_exe', '".$rowMemo->ordercode."','".$rowMemo->om_no."');\">";
				}
				echo "			</td>\n";
				echo "		</tr>\n";
				echo "		<tr>\n";
				echo "			<td>\n";
				echo nl2br($rowMemo->memo);
				echo "			</td>\n";
				echo "		</tr>\n";
			}
			echo "		</table>\n";
			echo "		</td>\n";
			echo "	</tr>\n";
			/*
			echo "	<tr height=42 class=\"page_screen\">\n";
			echo "		<td valign=top style=\"padding-top:8\">고객알리미</td>\n";
			echo "		<td style=\"padding-top:3\">\n";
			echo "		<font style=\"line-height:20px\">&nbsp;&nbsp;<input type=text name=memo2 size=66 maxlength=100 value=\"{$message[2]}\">&nbsp;<input type=button value='입 력' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:40\" onclick=\"MemoUpdate()\"><br> &nbsp;&nbsp;<font color=#0000FF>*입력을 하시면, 고객 주문조회 화면을 통해 고객에게 알려드립니다.</font>";
			echo "		</td>\n";
			echo "	</tr>\n";
			if(ord($message[2])) {
				echo "	<tr height=58 class=\"page_print\">\n";
				echo "		<td valign=top style=\"padding-top:8\">고객알리미</td>\n";
				echo "		<td style=\"padding-top:3\">: \n";
				echo "		{$message[2]}\n";
				echo "		</td>\n";
				echo "	</tr>\n";
			}
			*/
		}
		echo "	<input type=hidden name=paymethod value=\"{$_ord->paymethod}\">\n";
		echo "	<input type=hidden name=in_reserve value=\"{$in_reserve}\">\n";
		echo "	<input type=hidden name=sender_email value=\"{$_ord->sender_email}\">\n";
		echo "	<input type=hidden name=sender_tel value=\"{$_ord->sender_tel}\">\n";
		echo "	<input type=hidden name=order_msg value=\"{$message[0]}\">\n";
		echo "	<input type=hidden name=sort>\n";
		echo "	<input type=hidden name=recoveryquan value=\"N\">\n";
		echo "	<input type=hidden name=recoveryrese value=\"N\">\n";
		echo "	<input type=hidden name=canreserve>\n";
		echo "	<input type=hidden name=recoveryrecan value=\"N\">\n";
		echo "	<input type=hidden name=deli_name>\n";
		echo "	<input type=hidden name=deli_com>\n";
		echo "	<input type=hidden name=deli_num>\n";
		echo "	<input type=hidden name=delimailtype value=\"N\">\n";
		echo "	</form>\n";
		echo "	</table>\n";
		if($pg_type=="A") {	//KCP
			echo "<form name=kcpform method=post action=\"{$Dir}paygate/A/cancel.php\">\n";
			echo "<input type=hidden name=sitecd value=\"{$pgid_info["ID"]}\">\n";
			echo "<input type=hidden name=sitekey value=\"{$pgid_info["KEY"]}\">\n";
			echo "<input type=hidden name=ordercode value=\"{$_ord->ordercode}\">\n";
			echo "<input type=hidden name=paymethod value=\"".$_ord->paymethod[0]."\">\n";
			echo "<input type=hidden name=return_host value=\"".urlencode($_SERVER['HTTP_HOST'])."\">\n";
			echo "<input type=hidden name=return_script value=\"".str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl()).AdminDir."order_detail.php"."\">\n";
			echo "<input type=hidden name=return_data value=\"ordercode={$ordercode}\">\n";
			echo "<input type=hidden name=return_type value=\"form\">\n";
			echo "</form>\n";
		} elseif($pg_type=="B") {	//LG데이콤
			echo "<form name=dacomform method=post action=\"{$Dir}paygate/B/cancel.php\">\n";
			echo "<input type=hidden name=mid value=\"{$pgid_info["ID"]}\">\n";
			echo "<input type=hidden name=mertkey value=\"{$pgid_info["KEY"]}\">\n";
			echo "<input type=hidden name=ordercode value=\"{$_ord->ordercode}\">\n";
			echo "<input type=hidden name=paymethod value=\"".$_ord->paymethod[0]."\">\n";
			echo "<input type=hidden name=return_host value=\"".urlencode($_SERVER['HTTP_HOST'])."\">\n";
			echo "<input type=hidden name=return_script value=\"".str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl()).AdminDir."order_detail.php"."\">\n";
			echo "<input type=hidden name=return_data value=\"ordercode={$ordercode}\">\n";
			echo "<input type=hidden name=return_type value=\"form\">\n";
			echo "</form>\n";
		} elseif($pg_type=="C") {	//올더게이트
			echo "<form name=allthegateform method=post action=\"{$Dir}paygate/C/cancel.php\">\n";
			echo "<input type=hidden name=\"storeid\" value=\"{$pgid_info["ID"]}\">\n";
			echo "<input type=hidden name=\"ordercode\" value=\"{$_ord->ordercode}\">\n";
			echo "<input type=hidden name=\"paymethod\" value=\"".$_ord->paymethod[0]."\">\n";
			echo "<input type=hidden name=\"return_host\" value=\"".urlencode($_SERVER['HTTP_HOST'])."\">\n";
			echo "<input type=hidden name=\"return_script\" value=\"".str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl()).AdminDir."order_detail.php"."\">\n";
			echo "<input type=hidden name=\"return_data\" value=\"ordercode={$ordercode}\">\n";
			echo "<input type=hidden name=\"return_type\" value=\"form\">\n";
			echo "</form>\n";
		}elseif($pg_type=="D") {	//이니시스
			echo "<form name=inicisform method=post action=\"{$Dir}paygate/D/cancel.php\">\n";
			echo "<input type=hidden name=sitecd value=\"{$pgid_info["ID"]}\">\n";
			echo "<input type=hidden name=ordercode value=\"{$_ord->ordercode}\">\n";
			echo "<input type=hidden name=paymethod value=\"".$_ord->paymethod[0]."\">\n";
			echo "<input type=hidden name=return_host value=\"".urlencode($_SERVER['HTTP_HOST'])."\">\n";
			echo "<input type=hidden name=return_script value=\"".str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl()).AdminDir."order_detail.php"."\">\n";
			echo "<input type=hidden name=return_data value=\"ordercode={$ordercode}\">\n";
			echo "<input type=hidden name=return_type value=\"form\">\n";
			echo "</form>\n";
		}elseif($pg_type=="G") {	//나이스
			echo "<form name=niceform method=post action=\"{$Dir}paygate/G/cancel.php\">\n";
			echo "<input type=hidden name=sitecd value=\"{$pgid_info["ID"]}\">\n";
			echo "<input type=hidden name=ordercode value=\"{$_ord->ordercode}\">\n";
			echo "<input type=hidden name=paymethod value=\"".$_ord->paymethod[0]."\">\n";
			echo "<input type=hidden name=return_host value=\"".urlencode($_SERVER['HTTP_HOST'])."\">\n";
			echo "<input type=hidden name=return_script value=\"".str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl()).AdminDir."order_detail.php"."\">\n";
			echo "<input type=hidden name=return_data value=\"ordercode={$ordercode}\">\n";
			echo "<input type=hidden name=return_type value=\"form\">\n";
			echo "</form>\n";
		}
	}
?>
</div>
<!-- 주문내역 끝 -->

<form name=form_reg action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>

<form name=smsform action="sendsms.php" method=post target="sendsmspop">
<input type=hidden name=number>
</form>

<form name=formmemo method=post>
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
<input type=hidden name=id>
</form>

<form name=formhide action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
<input type=hidden name=hidedisplay value="Y">
</form>

<form name=taxsaveform method=post action="<?=$Dir.FrontDir?>taxsave.php" target=taxsavepop>
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
<input type=hidden name=productname value="<?=urlencode(titleCut(30,htmlspecialchars(strip_tags($taxsaveprname),ENT_QUOTES)))?>">
</form>

<form name=taxprintform method=post action="taxprint.php">
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
</form>

<form name=vform action="<?=$Dir?>paygate/set_bank_account.php" method=post target="baccountpop">
<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>

<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>

<form name=mForm action="order_detail_forModReserve.php" method=post>
<input type=hidden name=id>
<input type=hidden name=ordercode>
<input type=hidden name=modreserve>
<input type=hidden name=tot_reserve>
</form>

<form name=mFormMsgCancel action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=type>
<input type=hidden name=ordercode>
<textarea name = 'msg_part_cancel' style = 'display:none;'></textarea>
<input type=hidden name=mod_refund>
</form>

<form name='restoreForm' action="<?=$_SERVER['PHP_SELF']?>" method='post'>
<input type='hidden' name='type' value = 'restore_cancel'>
<input type='hidden' name='ordercode'>
<input type='hidden' name='oc_no'>
</form>

<form name='form_memo' action="<?=$_SERVER['PHP_SELF']?>" method='post'>
<input type='hidden' name='mode'>
<input type='hidden' name='ordercode'>
<input type='hidden' name='om_no'>
</form>

<form name=detailform method="post" action="order_detail.php">
<input type=hidden name=ordercode>
</form>

<form name=crmview method="post" action="crm_view.php">
<input type=hidden name=id>
</form>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
</body>
</html>
