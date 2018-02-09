<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

$ordercode=$_POST["ordercode"];
$type=$_POST["type"];
$mode=$_POST["mode"];
$hidedisplay=$_POST["hidedisplay"];

$order_msg=$_POST["order_msg"];

$rescode=$_POST["rescode"];
$pay_admin_proc=$_POST["pay_admin_proc"];

if($type=="sort") {
	$sort=$_POST["sort"];
}

if($ordercode==NULL) {
	alert_go('잘못된 접근입니다.','c');
}

$sql="SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}'";
$result=pmysql_query($sql,get_db_conn());
$_ord=pmysql_fetch_object($result);
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
	$isupdate=true;

	if(ord($_ord->sender_email)) {
		SendBankMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $_ord->sender_email, $ordercode);
	}

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

//배송준비 세팅
} elseif($type=="readydeli" && ord($ordercode)) {
	if($_ord->deli_gbn=="N") {
		$sql = "UPDATE tblorderinfo SET deli_gbn='S' WHERE ordercode='{$ordercode}' ";
		if(pmysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='S' WHERE ordercode='{$ordercode}' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			$sql.= "AND deli_gbn='N' ";
			pmysql_query($sql,get_db_conn());
		}
	}
	echo "<script>if(opener) {opener.history.go(0);} window.close(); </script>";
	exit;

//배송완료 세팅
} elseif($type=="delivery" && ord($ordercode)) {
	$delimailok=$_POST["delimailtype"];	//배송완료에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
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

		$sql = "UPDATE tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		if(pmysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			$sql.= "AND deli_gbn!='Y' ";
			pmysql_query($sql,get_db_conn());
		}
		$isupdate=true;

		if($delimailok=="Y") {	//배송완료 메일을 발송할 경우
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

		if($in_reserve>0) {
			$sql = "SELECT reserve FROM tblmember WHERE id='{$_ord->id}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$reservemoney=$in_reserve + $row->reserve;
				$sql = "UPDATE tblmember SET reserve = {$reservemoney} ";
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
				'{$in_reserve}', 
				'Y', 
				'물품 구입건에 대한 적립금 지급', 
				'{$ordercode}={$_ord->price}', 
				'".date("YmdHis")."')";
				pmysql_query($sql,get_db_conn());
				$in_reserve=0;
			}
			pmysql_free_result($result);
		}
		echo "<script>if(opener) {opener.history.go(0);} window.close(); </script>";
		exit;
	} elseif(!strstr("NXS",$_ord->deli_gbn)) {
		echo "<script>alert(\"이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.\");</script>";
	}

#반송처리
} elseif($type=="redelivery" && ord($ordercode)) {
	$sql = "UPDATE tblorderinfo SET deli_gbn='R' WHERE ordercode='{$ordercode}' ";
	if(pmysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='R' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
		pmysql_query($sql,get_db_conn());
	}
	echo "<script>if(opener) {opener.history.go(0);} window.close(); </script>";
	exit;

//배송지 주소 업데이트
} elseif($type=="addressupdate" && ord($ordercode)) {
	$post1=$_POST["post1"];
	$post2=$_POST["post2"];
	$address1=$_POST["address1"];
	$receiver_addr="우편번호 : {$post1}-{$post2}\\n주소 : ".$address1;
	$sql = "UPDATE tblorderinfo SET receiver_addr='{$receiver_addr}' ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	pmysql_query($sql,get_db_conn());
	$isupdate=true;

//송장정보 업데이트
} elseif($type=="deliupdate" && ord($ordercode)) {
	$delimailtype=$_POST["delimailtype"];	//송장정보 업데이트에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
	$deli_com=$_POST["deli_com"];
	$deli_num=$_POST["deli_num"];

	$patterns = array(" ","_","-");
	$replace = array("","","");
	$deli_num = str_replace($patterns,$replace,$deli_num);

	/********
	에스크로 서버에 송장정보 전달 - 에스크로 결제일 경우에만.....
	********/
	if(strstr("QP", $_ord->paymethod[0])) {
		//KCP|올더게이트는 송장정보 업데이트 모듈이 없음. (추후 다른 PG사 추가시 작업.....)
	}

	$sql = "UPDATE tblorderproduct SET deli_num='{$deli_num}', deli_com='{$deli_com}' ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	pmysql_query($sql,get_db_conn());

	if(ord($_ord->sender_email) && $delimailtype=="Y") {
		SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype);
	}
	if(ord($_ord->sender_tel) && $delimailtype=="Y") {
		if(ord($deli_com) && ord($deli_name)==0) {
			$sql = "SELECT company_name FROM tbldelicompany WHERE code='{$deli_com}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$deli_name=$row->company_name;
			}
			pmysql_free_result($result);
		}
		if(ord($deli_name)==0) {
			$deli_name="자가배송";
		}
		$sql="SELECT * FROM tblsmsinfo WHERE mem_delinum='Y' ";
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

			$fromtel=$rowsms->return_tel;
			$date=0;
			$etcmsg="송장안내메세지(회원)";
			$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_delinum, $etcmsg);
		}
		pmysql_free_result($result);
	}

//KCP/올더게이트/이니시스 - 상품배송 후 취소요청이 있을 경우 우선 정산보류 상태로 돌린다. (반송 완료 후 취소처리 가능)
} elseif($type=="okhold" && ord($ordercode) && ($pg_type=="A" || $pg_type=="C" || $pg_type=="D")) {
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
		}
		$isupdate=true;
	}

//수량복구, 주문취소
} elseif(($type=="recancel" || $type=="recoveryquan" || $type=="recoverycan") && ord($ordercode)) {
	if($_ord->pay_admin_proc!="C" && $_ord->pay_flag=="0000" && strstr("C", $_ord->paymethod[0])) { // 일반 카드 주문은 주문취소전에 카드를 취소해야함
		alert_go('먼저 카드취소를 하셔야 합니다.',-1);
	} elseif($_ord->pay_admin_proc!="C" && $_ord->pay_flag=="0000" && strstr("M", $_ord->paymethod[0])) { // 핸드폰 주문은 주문취소전에 핸드폰 결제를 취소해야함
		alert_go('먼저 핸드폰결제 취소를 하셔야 합니다.',-1);
	}
	
	/************* 에스크로 결제 환불(가상계좌) 또는 취소(신용카드) ***************/
	if(strstr("QP", $_ord->paymethod[0])) {
		//Q(가상계좌 매매보호)일 경우엔 우선 환불대기 후 환불되면 자동 취소처리된다.

		if($pg_type=="A") {			#KCP
			$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode=".$ordercode;
		} elseif($pg_type=="B") {	#LG데이콤
			$query="mid={$pgid_info["ID"]}&mertkey={$pgid_info["KEY"]}&ordercode=".$ordercode;
		} elseif($pg_type=="C") {  #올더게이트
			$query="storeid={$pgid_info["ID"]}&ordercode=".$ordercode;
		} elseif($pg_type=="D") {  #이니시스
			$query="sitecd={$pgid_info["EID"]}&ordercode={$ordercode}&curgetid=".$_ShopInfo->getId();
		}
		
		$cancel_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/escrow_cancel.php",$query);

		$cancel_data=substr($cancel_data,strpos($cancel_data,"RESULT=")+7);
		if (substr($cancel_data,0,2)!="OK") {
			$tempdata=explode("|",$cancel_data);
			$errmsg="취소처리가 정상 완료 되지 못 했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
			if(ord($tempdata[1])) $errmsg=$tempdata[1];
			alert_go($errmsg,-1);
		} else {
			$tempdata=explode("|",$cancel_data);
			if(ord($tempdata[1])) $errmsg=$tempdata[1];
			if(ord($errmsg)) {
				echo "<script> alert('{$errmsg}');</script>";
			}
		}
	}

	if($_ord->del_gbn!="R"){ //R일 경우 수량이 복구된 경우
		if($type=="recoveryquan"){ //수량복구 실행
			$sql = "SELECT a.productcode,a.productname,a.opt1_name,a.opt2_name,a.quantity, ";
			$sql.= "b.option_quantity,b.option1,b.option2,a.package_idx,a.assemble_idx,a.assemble_info FROM tblorderproduct a, tblproduct b ";
			$sql.= "WHERE a.productcode=b.productcode AND a.ordercode='{$ordercode}' ";
			$result=pmysql_query($sql,get_db_conn());
			$message="";
			while ($row=pmysql_fetch_object($result)) {
				$tmpoptq="";
				if(ord($artmpoptq[$row->productcode]))
					$optq=$artmpoptq[$row->productcode];
				else
					$optq=$row->option_quantity;

				if(strlen($optq)>51 && substr($row->opt1_name,0,5)!="[OPTG"){
					$tmpoptname1=explode(" : ",$row->opt1_name);
					$tmpoptname2=explode(" : ",$row->opt2_name);
					$tmpoption1=explode(",",$row->option1);
					$tmpoption2=explode(",",$row->option2);
					$cnt=1;
					$maxoptq = count($tmpoption1);
					while ($tmpoption1[$cnt]!=$tmpoptname1[1] && $cnt<$maxoptq) {
						$cnt++;
					}
					$opt_no1=$cnt;
					$cnt=1;
					$maxoptq2 = count($tmpoption2);
					while ($tmpoption2[$cnt]!=$tmpoptname2[1] && $cnt<$maxoptq2) {
						$cnt++;
					}
					$opt_no2=$cnt;
					$optioncnt = explode(",",substr($optq,1));
					if($optioncnt[($opt_no2-1)*10+($opt_no1-1)]!="") $optioncnt[($opt_no2-1)*10+($opt_no1-1)]+=$row->quantity;
					for($j=0;$j<5;$j++){
						for($i=0;$i<10;$i++){
							$tmpoptq.=",".$optioncnt[$j*10+$i];
						}
					}
					if(ord($tmpoptq) && $tmpoptq.","!=$optq){
						$artmpoptq[$row->productcode]=$tmpoptq;
						$tmpoptq=",option_quantity='{$tmpoptq},'";
					}else{
						$tmpoptq="";
						$message .="[{$row->productname} - ".$row->opt1_name.$row->opt2_name."]\\n";
					}
				}
				$sql = "UPDATE tblproduct SET quantity=quantity+".$row->quantity.$tmpoptq." 
				WHERE productcode='{$row->productcode}'";
				pmysql_query($sql,get_db_conn());

				if(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info)))) {
					$assemble_infoall_exp = explode("=",$row->assemble_info);

					if($row->package_idx>0 && ord(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))) {
						$package_info_exp = explode(":",$assemble_infoall_exp[0]);
						if(ord($package_info_exp[0])) {
							$package_productcode_exp = explode("",$package_info_exp[0]);
							for($k=0; $k<count($package_productcode_exp); $k++) {
								$sql2 = "UPDATE tblproduct SET 
								quantity		= quantity+{$row->quantity} 
								WHERE productcode='{$package_productcode_exp[$k]}' ";
								pmysql_query($sql2,get_db_conn());
							}
						}
					}
					
					if($row->assemble_idx>0 && ord(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))) {
						$assemble_info_exp = explode(":",$assemble_infoall_exp[1]);
						if(ord($assemble_info_exp[0])) {
							$assemble_productcode_exp = explode("",$assemble_info_exp[0]);
							for($k=0; $k<count($assemble_productcode_exp); $k++) {
								$sql2 = "UPDATE tblproduct SET 
								quantity		= quantity+{$row->quantity} 
								WHERE productcode='{$assemble_productcode_exp[$k]}' ";
								pmysql_query($sql2,get_db_conn());
							}
						}
					}
				}
			}
			pmysql_free_result($result);

			if(ord($message)==0) $message="수량복구가 성공적으로 되었습니다. 확인바랍니다.";
			else $message="옵션별 수량복구가 아래의 원인으로 실패하였습니다.\\n\\n{$message}\\n위의 상품의 경우 현재 옵션값이 주문시의 옵션 값과 다릅니다.\\n개별 상품에서 직접 확인후 옵션별 수량 조정하세요\\n기본 수량은 복구된 상태입니다. 옵션별 수량만 조정하세요.";
			$canmess="<script>alert('{$message}');</script>";

			$log_content = "## 상품수량 복구 ## - 주문번호 : ".$ordercode;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		} else {
			$canmess="<script>alert('해당 주문을 취소하였습니다.');</script>";

			$log_content = "## 주문취소 ## - 주문번호 : ".$ordercode;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		}
	}

	if(strstr("BOQ", $_ord->paymethod[0]) && $tax_type=="Y") {	//현금영수증 자동 발행
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
	} elseif (strstr("P", $_ord->paymethod[0])) {
		$deliupdate = " deli_gbn='C', pay_admin_proc='C' ";
		$up_deli_gbn="C";
	} else {
		$deliupdate = " deli_gbn='C' ";
		$up_deli_gbn="C";
	}

	if($type=="recoveryquan") {
		if($_ord->del_gbn=="Y") $okdel="R";
		else $okdel="A";
	} elseif($type=="recoverycan") $okdel=$_ord->del_gbn;

	$sql = "UPDATE tblorderinfo SET {$deliupdate}, del_gbn='{$okdel}' ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	if(pmysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='{$up_deli_gbn}' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
		pmysql_query($sql,get_db_conn());
	}
	$isupdate=true;
	echo $canmess;

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

		$sql = "UPDATE tblmember SET reserve=reserve+{$_ord->reserve} ";
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
		'주문 취소건에 대한 적립금 환원', 
		'{$ordercode}={$_ord->price}', 
		'".date("YmdHis")."')";
		pmysql_query($sql,get_db_conn());

		$log_content="## 회원 적립금 환원 ## - 주문번호 : {$ordercode} - 적립금 ".$_ord->reserve;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

//주문취소로 인한 회원에게 지급했던 적립금 취소처리
} elseif($type=="recoveryrecan" && ord($ordercode)) {
	$canreserve=$_POST["canreserve"];
	if(strstr("YDH",$_ord->deli_gbn) && strlen($_ord->deli_date)==14 && $canreserve>0 && ord($_ord->id)) {
		$sql = "UPDATE tblmember SET reserve=reserve-{$canreserve} ";
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
		'주문 취소건에 대한 적립금 지급취소', 
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
		'주문 취소건에 대한 적립금 지급취소', 
		'1', 
		'-$canreserve', 
		'".date("Ymd")."')";
		pmysql_query($sql,get_db_conn());

		$log_content="## 회원 적립금 지급취소 ## - 주문번호 : {$ordercode} - 적립금 -".$canreserve;
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
	$enuriprice=(int)$_POST["enuriprice"];
	
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
	} elseif($productcode==6) { // 에누리금액
		$sql = "UPDATE tblorderinfo SET enuri_price='{$enuriprice}' ";
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

} elseif($type=="deligbnup" && ord($ordercode) && ord($_POST["prcodes"]) && strstr("NSY",$_POST["deli_gbn"]) && strstr("NXS",$_ord->deli_gbn)) {
	$prcodes=$_POST["prcodes"];
	$deli_gbn=$_POST["deli_gbn"];

	$prcodes=rtrim($prcodes,',');
	$prlist=str_replace(',','\',\'',$prcodes);
	$sql = "UPDATE tblorderproduct SET deli_gbn='{$deli_gbn}', ";
	if($deli_gbn=="Y") $sql.= "deli_date='".date("YmdHis")."' ";
	else $sql.= "deli_date=NULL ";
	$sql.= "WHERE ordercode='{$ordercode}' AND productcode IN ('{$prlist}') ";
	$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	if(pmysql_query($sql,get_db_conn())) {
		if($_ord->deli_gbn!=$deli_gbn) {
			$rescode=getDeligbn_detail($ordercode,$deli_gbn);
			if(ord($rescode)) {
				$isupdate=true;
			}
		}
	}

} elseif($type=="deliinfoup" && ord($ordercode) && ord($_POST["prcodes"])) {
	$prcodes=$_POST["prcodes"];

	$deliinfo=rtrim($prcodes,',');
	$ardeli=explode("|",$deliinfo);
	for($i=0;$i<count($ardeli);$i++) {
		$prcode=$deli_com=$deli_num="";
		$prinfo=explode(",",$ardeli[$i]);
		for($j=0;$j<count($prinfo);$j++) {
			if (substr($prinfo[$j],0,7)=="PRCODE=") $prcode=substr($prinfo[$j],7);
			elseif (substr($prinfo[$j],0,9)=="DELI_COM=") $deli_com=substr($prinfo[$j],9);
			elseif (substr($prinfo[$j],0,9)=="DELI_NUM=") $deli_num=substr($prinfo[$j],9);
		}
		if(strlen($prcode)==18) {
			$sql = "UPDATE tblorderproduct SET deli_com='{$deli_com}', deli_num='{$deli_num}' ";
			$sql.= "WHERE ordercode='{$ordercode}' AND productcode='{$prcode}' ";
			$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
			pmysql_query($sql,get_db_conn());
		}
	}
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
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo 
	ORDER BY id ASC ";
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
<SCRIPT LANGUAGE="javascript">
<!--
//document.onkeydown = CheckKeyPress;
//document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
	   event.keyCode = 0;
	   return false;
	 }
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 30;
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
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
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

function ChangeUpdateMode(mode){
	document.form1.mode.value=mode;
	document.form1.type.value="";
	document.form1.submit();
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

function RestoreOrder(temp,delivery,reserve) {
	if(temp=="quan") mess="수량복구";
	else mess="주문취소";

	if(document.form2.recoveryquan.value=="Y") {
		alert(mess+"는 한 주문서에 대해서 한번만 가능합니다.");
		return;
	}
<?php
	if ($_ord->deli_gbn!="C" && strstr("CP",$_ord->paymethod[0]) && substr($_ord->ordercode,0,12)>$curdate) {
		echo "	alert(\"카드주문의 경우 주문시점에서 30분 경과 후 \"+mess+\"가 가능합니다.\\n\\n고객이 카드정보 입력중 일 수 있습니다.\");";
	} elseif($_ord->pay_admin_proc!="C" && $_ord->pay_flag=="0000" && strstr("C", $_ord->paymethod[0])) {
		echo "	alert(\"카드주문 취소는 먼저 카드취소 후 진행해 주세요.\");";
	} elseif($_ord->pay_admin_proc!="C" && $_ord->pay_flag=="0000" && strstr("M", $_ord->paymethod[0])) {
		echo "	alert(\"휴대폰주문 취소는 먼저 휴대폰결제 취소 후 진행해 주세요.\");";
	} else {	
?>
	if(delivery!="C" && reserve>0 && confirm("회원적립금을 먼저 취소하셔야 합니다.")) {
		RestoreReserve();
	} else if(temp=="quan" && confirm("상품수량이 자동으로 복귀되며,\n\n해당주문은 주문취소됩니다.<?=(strstr("PQ",$_ord->paymethod[0])?"\\n\\n매매보호 결제의 경우는 자동으로 결제도 취소가 됩니다.":"")?>")) {
		document.form2.type.value="recoveryquan";
		document.form2.recoveryquan.value="Y";
		document.form2.submit();
	} else if(temp=="can" && confirm("상품수량은 복구되지 않습니다.\n\n해당 주문을 취소하시겠습니까?<?=(strstr("PQ",$_ord->paymethod[0])?"\\n\\n매매보호 결제의 경우는 자동으로 결제도 취소가 됩니다.":"")?>")) {
		document.form2.type.value="recoverycan";
		document.form2.recoveryquan.value="Y";
		document.form2.submit();
	}
<?php	} ?>
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
//카드/핸드폰 취소
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
		if(temp=="Y" && !confirm("입금확인이 안된 주문서입니다. 배송을 완료하시겠습니까?")) return;
		else if(temp=="S" && !confirm("발송준비 지시를 하시겠습니까?")) return;
		else if(temp=="N" && !confirm("배송을 완료하시겠습니까?")) return;
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
}
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

<?php if($pg_type=="A" || $pg_type=="C" || $pg_type=="D"){?>
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

function AddressUpdate(){
	if(confirm("배송지를 해당 주소로 수정하시겠습니까?")) {
		document.form2.type.value="addressupdate";
		document.form2.submit();
	}
}

function DeliSearch(deli_url){
	window.open(deli_url,"배송추적","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");
}

function DeliNumUpdate() {
	if(!countdelinum) {
		if(!confirm("모든 상품의 배송정보를 수정하시겠습니까?")) {
			document.form2.delimailtype.value="N";
			return;
		}

		if(confirm("모든 상품의 배송정보 변경내역을 메일/SMS로 발송하시겠습니까?"))
			document.form2.delimailtype.value="Y";
		else 
			document.form2.delimailtype.value="N";

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

function changeDeliinfo() {
	if(typeof(document.form1.chkprcode.length)=="number") {
		document.form1.prcodes.value="";
		cnt=document.form1.chkprcode.length;
		for(i=1;i<cnt;i++){
			if(document.form1.chkprcode[i].checked) {
				document.form1.prcodes.value+="PRCODE="+document.form1.chkprcode[i].value+",DELI_COM="+document.form1.chkdeli_com[i].value+",DELI_NUM="+document.form1.chkdeli_num[i].value+"|";
			}
		}
	} else {
		alert("배송 상품이 존재하지 않습니다.");
		return;
	}
	if(document.form1.prcodes.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		return;
	}
	if(confirm("선택된 상품의 배송업체/송장번호를 수정(등록)합니다.\n\n정말로 적용하시겠습니까?")) {
		document.form1.type.value="deliinfoup";
		document.form1.submit();
	}
}

<?php if(strstr("NXS",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C" && $prescd=="Y") {?>
function changeDeli(obj) {
	if(typeof(document.form1.chkprcode.length)=="number") {
		document.form1.prcodes.value="";
		cnt=document.form1.chkprcode.length;
		for(i=1;i<cnt;i++){
			if(document.form1.chkprcode[i].checked) {
				document.form1.prcodes.value+=document.form1.chkprcode[i].value+",";
			}
		}
	} else {
		alert("배송 상품이 존재하지 않습니다.");
		return;
	}
	deli_gbn=obj.value;
	document.form1.prcodes.value="";
	for(i=1;i<document.form1.chkprcode.length;i++) {
		if(document.form1.chkprcode[i].checked) {
			document.form1.prcodes.value+=document.form1.chkprcode[i].value+",";
		}
	}
	if(document.form1.prcodes.value.length==0) {
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
			document.form1.prcodes.value="";
			obj.selectedIndex=0;
		}
	} else {
		document.form1.prcodes.value="";
		obj.selectedIndex=0;
	}
}
<?php }?>
function trans_view(id,approve,send_dt,send_no){
	
	window.open('http://allthegate.com/customer/receiptLast3.jsp?sRetailer_id='+id+'&approve='+approve+'&send_no='+send_no+'&send_dt='+send_dt, 'COLOR_WINDOW', 'resizable=yes scrollbars=yes width=420 height=800');
	
}
//-->
</SCRIPT>
</head>
<!--body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();"-->
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="PagePrint();return false;" style="overflow-x:hidden;overflow-y:auto;" onLoad="PageResize();">

<table border=0 cellpadding=0 cellspacing=0 width=770 style="table-layout:fixed;" id=table_body>
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
			$createbutton.="<tr><td align=right style='padding-right:40px' height=15><img src='images/ordtl_arrow1.gif' align=absmiddle></td></tr>";
		}
		$createbutton.="<tr><td>";

		//신용카드 정상 결제일 경우
		if(strstr("C",$_ord->paymethod[0]) && $_ord->pay_flag=="0000") {
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

			$trans_url=pmysql_fetch_object(pmysql_query("select pay_data from tbl_card_estimate where ordercode='{$ordercode}'"));
			if($trans_url->pay_data){
				$pay_data_arr=explode("|",$trans_url->pay_data);
				$pay_data_arr8=substr($pay_data_arr[8],0,8);
				$createbutton.="<a href=\"javascript:trans_view('".$pay_data_arr[0]."','".$pay_data_arr[3]."','".$pay_data_arr8."','".$pay_data_arr[13]."');\"><img src=\"images/ordtl_btntax_card.gif\" align=absmiddle border=0></a>&nbsp;";
			}

		//핸드폰 결제일 경우
		} elseif (strstr("M",$_ord->paymethod[0]) && $_ord->pay_flag=="0000") {

			if($_ord->pay_admin_proc=="N" && strcmp($_ord->pay_flag,"0000")==0) {
				$createbutton.="<a href=\"javascript:card_ask('card_cancel','hp')\"><img src=\"images/ordtl_btnpaycancel.gif\" align=absmiddle border=0></a>\n";		//결제취소
				$createbutton.="&nbsp;&nbsp;&nbsp;<font color=#C0C0C0>|</font>&nbsp;";
			}

		//실시간계좌이체/일반가상계좌 환불안내
		} elseif (strstr("VO",$_ord->paymethod[0]) && $_ord->pay_flag=="0000" && $_ord->pay_admin_proc!="C") {
			$createbutton.="<a href=\"javascript:alert('실시간계좌이체 및 가상계좌 결제건은 시스템적인 자동 환불처리가 불가하오니\\n\\n주문취소처리 후 적립금으로 지급 또는 무통장 환불처리를 하시기 바랍니다.')\"><img src=\"images/ordtl_refundinfo.gif\" border=0 align=absmiddle></a>\n";
			$createbutton.="&nbsp;<font color=#C0C0C0>|</font>&nbsp;";
		}

		//신용카드 결제가 승인되었거나 또는 (무통장입금이고, 주문취소가 아니고, 입금이 완료되었고) 또는 (실시간계좌이체이고 정상적으로 결제된건이라면,,,)
		if($_ord->pay_admin_proc=="Y" || (strstr("B",$_ord->paymethod[0]) && $_ord->deli_gbn!="C" && strlen($_ord->bank_date)==14) || (strstr("VO",$_ord->paymethod[0]) && $_ord->pay_flag=="0000" && $_ord->deli_gbn!="C")) {
			$createbutton.="<a href=\"javascript:printtax()\"><img src=\"images/ordtl_btntax.gif\" align=absmiddle border=0></a>\n";	//영수증 발급
		}
		if(strstr("BOQ",$_ord->paymethod[0]) && $tax_type!="N" && $_ord->deli_gbn!="C") {
			$createbutton.="<a href=\"javascript:get_taxsave()\"><img src=\"images/ordtl_btntaxsave.gif\" align=absmiddle border=0></a>\n";	//현금영수증 요청
			//$createbutton.="<a href=\"javascript:get_taxsave2()\">현금영수증New</a>\n";	//현금영수증 요청
		}
		if(strstr("NXS",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C") {
			//(미처리/배송요청/배송완료) 되고 카드결제 취소건이 아니면,,,,,
			$createbutton.="<a href=\"javascript:printaddress()\"><img src=\"images/ordtl_btnprint.gif\" align=absmiddle border=0></a>\n";	//운송장 출력
			$createbutton.="<font color=#C0C0C0>|</font>&nbsp;";
			if(strstr("B",$_ord->paymethod[0]) && strlen($_ord->bank_date)!=14) {
				//무통장 입금 결제건이고, 입금이 안된 경우
				$createbutton.="<a href=\"javascript:banksend()\"><img src=\"images/ordtl_btnbankok.gif\" align=absmiddle border=0></a><img src='images/ordtl_arrow2.gif' align=absmiddle>";	//입금완료

				//배송준비 단계가 아니면,,,,,
				if($_ord->deli_gbn!="S") $createbutton.="<a href=\"javascript:delisend('S')\"><img src=\"images/ordtl_btndeliready.gif\" align=absmiddle border=0></a><img src='images/ordtl_arrow2.gif' align=absmiddle>";	//발송준비
				$createbutton.="<a href=\"javascript:delisend('Y')\"><img src=\"images/ordtl_btndeliok.gif\" align=absmiddle border=0></a>\n";	//배송완료
			} elseif(!strstr("OQ",$_ord->paymethod[0]) || strlen($_ord->bank_date)>=12) {	//가상계좌 입금건에 대해서 입금이 된 경우

				//배송준비 단계가 아니면,,,,,
				if($_ord->deli_gbn!="S") $createbutton.="<a href=\"javascript:delisend('S')\"><img src=\"images/ordtl_btndeliready.gif\" align=absmiddle border=0></a>\n";	//발송준비
				
				$createbutton.="<div id=deliescrow style=\"position:absolute; z-index:100; display:none;\">\n";
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
				$createbutton.="		<td class=\"td_con1\"><select name=escrow_deli_com style=\"width:90;height:18;font-size:8pt\">\n";
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
				$createbutton.="		<td class=\"td_con1\"><input type=text name=escrow_deli_num value=\"\" size=13 maxlength=20 style=\"height:19;font-size:8pt\"></td>\n";
				$createbutton.="	</tr>\n";
				$createbutton.="	<tr bgcolor=#FFFFFF>\n";
				$createbutton.="		<td height=40 colspan=\"2\" align=\"center\"><a href=\"javascript:delisend('N')\"><img src=\"images/btn_ok3.gif\" border=\"0\"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:escrow_deliclose();\"><img src=\"images/ordtl_close.gif\" border=\"0\"></a></td>\n";
				$createbutton.="	</tr>\n";
				$createbutton.="	</table>\n";
				$createbutton.="	</td>\n";
				$createbutton.="</tr>\n";
				$createbutton.="</form>\n";
				$createbutton.="</table>\n";
				$createbutton.="</div>\n";
				
				$createbutton.="<a href=\"javascript:delisend('N')\"><img src=\"images/ordtl_btndeliok.gif\" align=absmiddle border=0></a>\n";	//배송완료
			}
		} elseif($_ord->deli_gbn=="Y") {	//배송완료된 건에 대해서,,,,,,
			$createbutton.="<a href=\"javascript:printaddress()\"><img src=\"images/ordtl_btnprint.gif\" align=absmiddle border=0></a>\n";	//운송장 출력
			$createbutton.="<font color=#C0C0C0>|</font>&nbsp;";
			if(!strstr("QP",$_ord->paymethod[0])) $createbutton.="<a href=\"javascript:delicancel()\"><img src=\"images/ordtl_btndelino.gif\" align=absmiddle border=0></a>..\n";	//반송처리
		}
		//무통장 입금완료건에 대해서 주문취소이고 환불이 안된 경우
		if(strstr("B",$_ord->paymethod[0]) && $_ord->deli_gbn=="C" && strlen($_ord->bank_date)>=12) {
			$createbutton.="<a href=\"javascript:bankcancel()\"><img src=\"images/ordtl_btnrefund.gif\" align=absmiddle border=0></a>\n";	//환불처리
		} elseif(strstr("O",$_ord->paymethod[0]) && $_ord->deli_gbn=="C" && $_ord->pay_admin_proc!="C") {
			$createbutton.="<a href=\"javascript:virtualcancel()\"><img src=\"images/ordtl_btnrefund.gif\" align=absmiddle border=0></a>\n";	//환불처리
		} elseif(strstr("V",$_ord->paymethod[0]) && $_ord->deli_gbn=="C" && $_ord->pay_admin_proc!="C") {
			$createbutton.="<a href=\"javascript:transcancel()\"><img src=\"images/ordtl_btnrefund.gif\" align=absmiddle border=0></a>\n";	//환불처리
		}
		//매매보호 가상계좌/신용카드건에 대해서 (주문취소/환불대기) 상태가 아니면,,,,,
		if(strstr("QP",$_ord->paymethod[0]) && !strstr("CE",$_ord->deli_gbn) && $_ord->price>0 && !strstr("YC",$_ord->escrow_result)) {
			if(strstr("YD",$_ord->deli_gbn) && strlen($_ord->deli_date)==14) {	//배송완료된 에스크로 결제건은 "정산보류" 버튼 활성화
				if($pg_type=="A" || $pg_type=="C" || $pg_type=="D") {
					$createbutton.="<a href=\"javascript:okhold()\"><img src=\"images/ordtl_btnescrowhold.gif\" align=absmiddle border=0></a>\n";	//정산보류
				}
			} else {
				$createbutton.="<a href=\"javascript:okcancel('".$_ord->paymethod[0]."','{$_ord->bank_date}')\"><img src=\"images/ordtl_btnescrowcancel.gif\" align=absmiddle border=0></a>\n";	//취소처리
			}
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
<tr class="page_screen">
	<td>
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<col width=190></col>
	<col width=></col>
	<col width=100></col>

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
	<tr>
		<td style="padding-left:3pt">
		정렬 : <select name=sort style="width:90;height:18;font-size:8pt;" onchange="Sort(this.value);">
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
		<td align=center>
<?php
		echo "	<table border=0 cellpadding=0 cellspacing=0>\n";
		echo "	<tr>\n";
		echo "		<td style=\"padding-bottom:2\">배송정보 일괄 등록 : \n";
		echo "		<select name=deli_com style=\"width:90;height:18;font-size:8pt\">\n";
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
		echo "		<input type=text name=deli_num value=\"\" size=10 maxlength=20 style=\"height:19;font-size:8pt\" onkeyup=\"strnumkeyup(this)\">\n";
		echo "<input type=button value='등록' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;width:30\" onclick=\"DeliNumUpdate()\">\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";

?>
		</td>
		<?php if($_ord->paymethod=="B" && $mode!="update"){?>
		<td align=right style="padding-right:2pt">
		<a href="javascript:ChangeUpdateMode('update')"><img src="images/ordtl_btnpriceup.gif" align=absmiddle border=0></a>
		</td>
		<?php }?>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td align=center style="padding-left:3">
	<!-- 주문내역 시작 -->
	<table border=1 cellpadding=0 cellspacing=0 width=100% bordercolorlight=#E2B892 bordercolordark=#ffffff style="table-layout:fixed">
	<col width=25></col>
	<?php if($vendercnt>0){?>
	<col width=55></col>
	<?php }?>
	<col width=></col>
	<col width=200></col>
	<col width=28></col>
	<col width=55></col>
	<col width=65></col>
	<?=($_ord->paymethod=="B" && $mode=="update"?"<col width=30></col>\n":"")?>
	<tr bgcolor=#efefef>
		<td align=center class="page_print">No</td><td align=center class="page_screen"><input type=checkbox name=allcheck onclick="CheckAll()"></td>
		<?php if($vendercnt>0){?>
		<td align=center>입점업체</td>
		<?php }?>
		<td align=center>상품명</td>
		<td align=center>선택사항</td>
		<td align=center>수량</td>
		<td align=center>적립금</td>
		<td align=center>가격</td>
		<?=($_ord->paymethod=="B" && $mode=="update"?"<td>&nbsp;</td>\n":"")?>
	</tr>

	<input type=hidden name=chkprcode>
	<input type=hidden name=chkdeli_com>
	<input type=hidden name=chkdeli_num>
	<input type=hidden name=prcodes>

<?php
	$colspan=6;
	if($vendercnt>0) $colspan++;

	$sql = "SELECT p.option1, p.optcode, op.* FROM tblorderproduct op left join tblproduct p on op.productcode = p.productcode WHERE op.ordercode='{$_ord->ordercode}' ";
	if(ord($sort)) $sql.="ORDER BY productcode asc, {$sort}";
	$result=pmysql_query($sql,get_db_conn());
	$sumquantity=0;
	$totalprice=0;
	$in_reserve=0;
	$cnt=0;
	$taxsaveprname="";
	while($row=pmysql_fetch_object($result)) {
		$isGift=false;
		if(preg_match("/^COU\d{8}X$/",$row->productcode)) {				#쿠폰
			//if($row->price!=0 && $row->price!=NULL) {
				$etcdata[]=$row;
				continue;
			//}
		} elseif(preg_match("/^9999999999\d(X|R)$/",$row->productcode)) {
			$etcdata[]=$row;
			continue;
		} else {															#진짜상품
			$prdata[]=$row;
		}
		if(substr($_ord->ordercode,-1)=="X" || substr($row->productcode,-4)=="GIFT"){
			$isGift=true;
		}

		$taxsaveprname.=$row->productname.",";
		$cnt++;
		$sumprice=$row->quantity*$row->price;
		$reserve=$row->quantity*$row->reserve;
		$tempopt1=$row->opt1_name;
		if($row->productcode=="99999999999R") $norecan="Y";
		if ($row->productcode!="99999999999X" && substr($row->productcode,0,3)!="COU" && $row->productcode!="99999999999R") {
			$sumquantity+=$row->quantity;
		}
		$in_reserve+=$reserve;
		$totalprice+=$sumprice;

		$optvalue="";

		$option1 =  explode(",",$row->option1);
		$optcode = explode(",",$row->optcode);
		$opt1name = explode(":",$row->opt1_name);
		$opt1name = $opt1name[count($opt1name)-1];
		$opt1idx = array_search(trim($opt1name),$option1)-1;
		$product_barcode = $optcode[$opt1idx];

//		print_r($option1);
//		print_r($optcode);
//		echo $optcode = explode(",",$row->optcode);
//		echo $optcode = explode(",",$row->optcode);
//		echo $row->opt1_name;

		if(preg_match("/^\[OPTG\d{3}\]$/",$row->opt1_name)) {
			$optioncode=$row->opt1_name;
			$row->opt1_name="";
			$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='{$_ord->ordercode}' AND productcode='{$row->productcode}' ";
			$sql.= "AND opt_idx='{$optioncode}' ";
			$result2=pmysql_query($sql,get_db_conn());
			if($row2=pmysql_fetch_object($result2)) {
				$optvalue=$row2->opt_name;
			}
			pmysql_free_result($result2);
		}

		$assemblestr = "";
		$packagestr = "";
		if(($_ord->paymethod!="B" || $mode!="update") && ord(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info))))) {
			$assemble_infoall_exp = explode("=",$row->assemble_info);

			if($row->package_idx>0 && ord(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))) {
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
			
			if($row->assemble_idx>0 && ord(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))) {
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

		echo "<tr bgcolor=#FFFFFF>\n";
		echo "	<td align=center class=\"page_print\" style=\"font-size:8pt\"><font color=#878787>{$cnt}</td>\n";

		if($isGift){
			echo "	<td align=center class=\"page_screen\" style=\"font-size:8pt\">-</td>\n";
		}else{
			echo "	<td align=center class=\"page_screen\" style=\"font-size:8pt\"><input type=checkbox name=chkprcode value=\"{$row->productcode}\"></td>\n";
		}

		if($vendercnt>0) {
			if($row->vender>0) {
				echo "	<td align=center style=\"font-size:8pt\"><a href=\"javascript:viewVenderInfo({$row->vender})\"><B>{$venderlist[$row->vender]->id}</B></a></td>\n";
			} else {
				echo "	<td align=center>&nbsp;</td>\n";
			}
		}

		
		//상품 이미지
		list($product_img)=pmysql_fetch("select tinyimage from tblproduct where productcode='".$row->productcode."'");

		if(file_exists($Dir.DataDir."shopimages/product/{$product_img}")) $file=$product_img;
		elseif(file_exists($Dir.DataDir."shopimages/product/{$product_img}")) $file=$product_img;
		else $file="NO";
		
		if($file!="NO") {
			echo "	<td style=\"font-size:8pt;padding:7,7,5,5;line-height:10pt\">\n";
			echo "	".(strlen($row->selfcode)?"진열코드 : {$row->selfcode}<br>":"")."<span style=\"line-height:10pt\" onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage{$cnt}');\">{$row->productname} ({$product_barcode})";
			echo "	<span class=\"page_screen\"><a href=\"javascript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','YES')\"><img src=images/ordtl_icnnewwin.gif align=absmiddle border=0></a></span>";
			echo "</span>\n";
			echo "	<div id=primage{$cnt} style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
			echo "	<table border=0 cellspacing=1 cellpadding=0 bgcolor=#000000 width=170>\n";
			echo "	<tr bgcolor=#FFFFFF>\n";
			echo "		<td align=center width=100% height=150><img name=bigimgs src=\"".$Dir.DataDir."shopimages/product/{$file}\"></td>\n";
			echo "	</tr>\n";
			echo "	<tr bgcolor=#FFFFFF>\n";
			echo "		<td height=54 bgcolor=#f5f5f5><table border=0><tr><td style=\"line-height:12pt\">예전 주문서,삭제/이동 상품은 이미지가 일치하지 않을수 있으니 <font color=red>주의하여 배송</font>바랍니다.</td></tr></table></td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</div>\n";
			if(ord($row->addcode)) echo "<br><font color=blue><b>[</b>특수표시 : {$row->addcode}<b>]</b></font>";
			if(ord($optvalue)) echo "<br><font color=red><b>[</b>옵션사항 : {$optvalue}<b>]</b></font>";
			echo $packagestr;
			echo $assemblestr;
			echo "	</td>\n";
		} else {
			echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">";
			echo (strlen($row->selfcode)?"진열코드 : {$row->selfcode}<br>":"").$row->productname;
			if(ord($row->addcode)) echo "<br><font color=blue><b>[</b>특수표시 : {$row->addcode}<b>]</b></font>";
			if(ord($optvalue)) echo "<br><font color=red><b>[</b>옵션사항 : {$optvalue}<b>]</b></font>";
			echo $packagestr;
			echo $assemblestr;
			echo "	</td>\n";
		}

		echo "	<td style=\"font-size:8pt;padding:2,5;line-height:11pt\">";
		echo (ord($row->opt1_name)?$row->opt1_name."<br>":"&nbsp;");
		if(ord($row->opt2_name)) echo $row->opt2_name;
		echo "	</td>\n";
		if ($row->productcode=="99999999999X" || substr($row->productcode,0,3)=="COU" || $row->productcode=="99999999999R") { // 현금결제면 수량표시안함
			echo "	<td><input type=hidden name=arquantity value=\"1\">&nbsp;</td>\n";
		} else {
			echo "	<td align=center style=\"font-size:8pt\" ".($_ord->paymethod!="B" || $mode!="update"?($row->quantity>1?" bgcolor=#FDE9D5><font color=#000000><b>":">").$row->quantity:"><input type=text style='text-align:right;font-size:8pt;width:100%' name=arquantity value=\"{$row->quantity}\" >")."</td>\n";
		}
		echo "	<td align=right style=\"font-size:8pt\">".(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && substr($row->productcode,-4)!="GIFT"?($_ord->paymethod!="B" || $mode!="update"?number_format($reserve)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=arreserve value=\"{$reserve}\">"):"<input type=hidden name=arreserve>&nbsp;")."</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".(substr($row->productcode,-4)!="GIFT"?$_ord->paymethod!="B" || $mode!="update"?number_format($sumprice)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=arprice value=\"{$sumprice}\">":"&nbsp;<input type=hidden name=arprice>")."</td>\n";

		if($_ord->paymethod=="B" && $mode=="update") {
			echo "<td align=center><a href=\"javascript:OrderUpdate('1',{$cnt},'{$row->vender}','{$row->productcode}','{$tempopt1}','{$row->opt2_name}')\"><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='상품수정'></a><br><img width=0 height=2 border=0><br><a href=\"javascript:OrderDelete('1','{$row->vender}','{$row->productcode}','{$tempopt1}','{$row->opt2_name}')\"><img src='images/ordtl_minidel.gif' align=absmiddle border=0 alt='상품삭제'></a></td>\n";
		}
		echo "</tr>\n";
		$totalsum+=$sumprice;
		/*
		if(($_ord->paymethod!="B" || $mode!="update") && !$isGift) {
			echo "<tr bgcolor=#FFFFFF style=\"font-size:8pt\">\n";
			echo "	<td align=right style=\"padding:2,5\" colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1)).">\n";
			echo "	<table border=0 cellpadding=0 cellspacing=0>\n";
			echo "	<tr>\n";
			echo "		<td style=\"padding-left:20\" style=\"font-size:8pt;letter-spacing:-0.5pt;\">배송업체 : \n";
			echo "		<span class=\"page_screen\">\n";
			echo "		<select name=chkdeli_com style=\"width:100;height:18;font-size:8pt\">\n";
			echo "		<option value=\"\">없음</option>\n";
			$deli_url="";
			$trans_num="";
			$company_name="";
			for($yy=0;$yy<count($delicomlist);$yy++) {
				if($pg_type=="B" && strstr("QP", $_ord->paymethod[0])) {
					if(ord($delicomlist[$yy]->dacom_code)) {
						echo "		<option value=\"{$delicomlist[$yy]->code}\"";
						if($row->deli_com>0 && $row->deli_com==$delicomlist[$yy]->code) {
							echo " selected";
							$deli_url=$delicomlist[$yy]->deli_url;
							$trans_num=$delicomlist[$yy]->trans_num;
							$company_name=$delicomlist[$yy]->company_name;
						}
						echo ">{$delicomlist[$yy]->company_name}</option>\n";
					}
				} else {
					echo "		<option value=\"{$delicomlist[$yy]->code}\"";
					if($row->deli_com>0 && $row->deli_com==$delicomlist[$yy]->code) {
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
			echo "		<td style=\"font-size:8pt;letter-spacing:-0.5pt;padding-left:5px;padding-right:5px;\">송장번호 : \n";
			echo "		<span class=\"page_screen\">\n";
			echo "		<input type=text name=chkdeli_num value=\"{$row->deli_num}\" size=10 maxlength=20 style=\"height:18;font-size:8pt\" onkeyup=\"strnumkeyup(this)\" class=\"input\"><img width=2 height=0>";
			if(ord($row->deli_num) && ord($deli_url)) {
				if(ord($trans_num)) {
					$arrtransnum=explode(",",$trans_num);
					$pattern=array("[1]","[2]","[3]","[4]");
					$replace=array(substr($row->deli_num,0,$arrtransnum[0]),substr($row->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
					$deli_url=str_replace($pattern,$replace,$deli_url);
				} else {
					$deli_url.=$row->deli_num;
				}
				echo "<input type=button value='추적' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;width:30\" onclick=\"DeliSearch('{$deli_url}')\">";
			} else {
				echo "<input type=button value='추적' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;width:30\">";
			}
			echo "		</span>\n";
			echo "		<span class=\"page_print\">".(ord($row->deli_num)?$row->deli_num:"없음")."</span>\n";
			echo "		</td>\n";
			echo "		<td style=\"font-size:8pt;letter-spacing:-0.5pt;\">\n";
			echo "	배송상태 : <B>";
			switch($row->deli_gbn) {
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
			if($row->deli_gbn=="D" && strlen($row->deli_date)==14) echo " (배송)";
			echo "	</B>";
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "</tr>\n";
		}*/


		/*
			라인별로 존재하는 배송 정보를 한줄로 처리
		*/
		if(($_ord->paymethod!="B" || $mode!="update") && !$isGift) {
			$deliveryData = "<table>";
			$deliveryData .= "<tr bgcolor=#FFFFFF style=\"font-size:8pt\">\n";
			$deliveryData .= "	<td align=right style=\"padding:2,5\" colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1)).">\n";
			$deliveryData .= "	<table border=0 cellpadding=0 cellspacing=0>\n";
			$deliveryData .= "	<tr>\n";
			$deliveryData .= "		<td style=\"padding-left:20\" style=\"font-size:8pt;letter-spacing:-0.5pt;\">배송업체 : \n";
			$deliveryData .= "		<span class=\"page_screen\">\n";
			$deliveryData .= "		<select name=chkdeli_com style=\"width:100;height:18;font-size:8pt\">\n";
			$deliveryData .= "		<option value=\"\">없음</option>\n";
			$deli_url="";
			$trans_num="";
			$company_name="";
			for($yy=0;$yy<count($delicomlist);$yy++) {
				if($pg_type=="B" && strstr("QP", $_ord->paymethod[0])) {
					if(ord($delicomlist[$yy]->dacom_code)) {
						$deliveryData .= "		<option value=\"{$delicomlist[$yy]->code}\"";
						if($row->deli_com>0 && $row->deli_com==$delicomlist[$yy]->code) {
							$deliveryData .= " selected";
							$deli_url=$delicomlist[$yy]->deli_url;
							$trans_num=$delicomlist[$yy]->trans_num;
							$company_name=$delicomlist[$yy]->company_name;
						}
						$deliveryData .= ">{$delicomlist[$yy]->company_name}</option>\n";
					}
				} else {
					$deliveryData .= "		<option value=\"{$delicomlist[$yy]->code}\"";
					if($row->deli_com>0 && $row->deli_com==$delicomlist[$yy]->code) {
						$deliveryData .= " selected";
						$deli_url=$delicomlist[$yy]->deli_url;
						$trans_num=$delicomlist[$yy]->trans_num;
						$company_name=$delicomlist[$yy]->company_name;
					}
					$deliveryData .= ">{$delicomlist[$yy]->company_name}</option>\n";
				}
			}
			$deliveryData .= "		</select>\n";
			$deliveryData .= "		</span>\n";
			$deliveryData .= "		<span class=\"page_print\">".(ord($company_name)?$company_name:"없음")."</span>\n";
			$deliveryData .= "		</td>\n";
			$deliveryData .= "		<td style=\"font-size:8pt;letter-spacing:-0.5pt;padding-left:5px;padding-right:5px;\">송장번호 : \n";
			$deliveryData .= "		<span class=\"page_screen\">\n";
			$deliveryData .= "		<input type=text name=chkdeli_num value=\"{$row->deli_num}\" size=10 maxlength=20 style=\"height:18;font-size:8pt\" onkeyup=\"strnumkeyup(this)\" class=\"input\"><img width=2 height=0>";
			if(ord($row->deli_num) && ord($deli_url)) {
				if(ord($trans_num)) {
					$arrtransnum=explode(",",$trans_num);
					$pattern=array("[1]","[2]","[3]","[4]");
					$replace=array(substr($row->deli_num,0,$arrtransnum[0]),substr($row->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
					$deli_url=str_replace($pattern,$replace,$deli_url);
				} else {
					$deli_url.=$row->deli_num;
				}
				$deliveryData .= "<input type=button value='추적' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;width:30\" onclick=\"DeliSearch('{$deli_url}')\">";
			} else {
				$deliveryData .= "<input type=button value='추적' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;width:30\">";
			}
			$deliveryData .= "		</span>\n";
			$deliveryData .= "		<span class=\"page_print\">".(ord($row->deli_num)?$row->deli_num:"없음")."</span>\n";
			$deliveryData .= "		</td>\n";
			$deliveryData .= "		<td style=\"font-size:8pt;letter-spacing:-0.5pt;\">\n";
			$deliveryData .= "	배송상태 : <B>";
			switch($row->deli_gbn) {
				case 'S': $deliveryData .= "발송준비";  break;
				case 'X': $deliveryData .= "배송요청";  break;
				case 'Y': $deliveryData .= "배송";  break;
				case 'D': $deliveryData .= "<font color=blue>취소요청</font>";  break;
				case 'N': $deliveryData .= "미처리";  break;
				case 'E': $deliveryData .= "<font color=red>환불대기</font>";  break;
				case 'C': $deliveryData .= "<font color=red>주문취소</font>";  break;
				case 'R': $deliveryData .= "반송";  break;
				case 'H': $deliveryData .= "배송(<font color=red>정산보류</font>)";  break;
			}
			if($row->deli_gbn=="D" && strlen($row->deli_date)==14) $deliveryData .= " (배송)";
			$deliveryData .= "	</B>";
			$deliveryData .= "		</td>\n";
			$deliveryData .= "	</tr>\n";
			$deliveryData .= "	</table>\n";
			$deliveryData .= "	</td>\n";
			$deliveryData .= "</tr>\n";
			$deliveryData .= "</table>";
		}









	}
	pmysql_free_result($result);
	
	echo "<tr class=\"page_screen\">\n";
	echo "<td colspan=\"2\" style=\"font-size:8pt;padding:5,27\"><B>상품 총 합계</B> </td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td align=right style=\"font-size:8pt;padding-right:4px;\">";
	echo number_format($totalsum);	
	echo "</td>";
	echo "</tr>";
	
	if($deliveryData){
		echo "<tr class=\"page_screen\">\n";
		echo "	<td style=\"padding:5,0,5,0\" align = 'right' colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1)).">\n";
		echo $deliveryData;
		echo "	</td>";
		echo "</tr>\n";
	}

	if($mode!="update") {
		echo "<tr class=\"page_screen\">\n";
		echo "	<td style=\"padding:5,0,5,0\" colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1)).">\n";
		echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
		echo "	<tr>\n";
		echo "		<td style=\"padding-left:2\">\n";
		if(strstr("NXS",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C" && $prescd=="Y") {
			echo "	<img src=images/arrow_ltr.gif border=0 align=absmiddle>\n";
			echo "	&nbsp; <B>선택한 상품에 대해서</B>\n";
			echo "	<select name=deli_gbn onchange=\"changeDeli(this)\">\n";
			echo "	<option value=\"\">처리상태 선택</option>\n";
			echo "	<option value=\"N\">미처리</option>\n";
			echo "	<option value=\"S\">발송준비</option>\n";
			echo "	<option value=\"Y\">배송완료</option>\n";
			echo "	</select>\n";
		}
		echo "		&nbsp;\n";
		echo "		<A HREF=\"javascript:changeDeliinfo()\"><img src=images/btn_deliinfomodify.gif border=0 align=absmiddle></A>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>";
		echo "</tr>\n";
	}

	echo "<tr height=30 bgcolor=#efefef>\n";
	echo "	<td align=center colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1))."><B>추가비용/할인/적립내역</B></td>";
	echo "</tr>\n";

	if(count($etcdata)>0) {
		for($j=0;$j<count($etcdata);$j++) {
			$cnt++;
			$sumprice=$etcdata[$j]->price;
			$reserve=$etcdata[$j]->reserve;
			$in_reserve+=$reserve;
			$totalprice+=$sumprice;
			echo "<tr>\n";
			echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				if($etcdata[$j]->vender>0) {
					echo "	<td align=center style=\"font-size:8pt\"><a href=\"javascript:viewVenderInfo({$etcdata[$j]->vender})\"><B>{$venderlist[$etcdata[$j]->vender]->id}</B></a></td>\n";
				} else {
					echo "	<td align=center>&nbsp;</td>\n";
				}
			}
			echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">{$etcdata[$j]->productname} <span class=\"page_screen\">";
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

			if($_ord->paymethod=="B" && $mode=="update") {
				echo "<td align=center style=\"font-size:8pt\"><a href=\"javascript:OrderUpdate('1',{$cnt},'{$etcdata[$j]->vender}','{$etcdata[$j]->productcode}','','')\"><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='상품수정'></a><br><img width=0 height=2 border=0><br><a href=\"javascript:OrderDelete('1','{$etcdata[$j]->vender}','{$etcdata[$j]->productcode}','','')\"><img src='images/ordtl_minidel.gif' align=absmiddle border=0 alt='상품삭제'></a></td>\n";
			}
			echo "</tr>\n";
		}
	}

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

		if($_ord->tot_price_dc){
			echo "<tr bgcolor=#FFFFE6>\n";
			echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				echo "	<td>&nbsp;</td>\n";
			}
			echo "	<td style=\"font-size:8pt;padding:2,5\">총 구매금액대별 할인</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:8pt\"></td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($_ord->tot_price_dc>0?($_ord->paymethod!="B" || $mode!="update"?"-".number_format($_ord->tot_price_dc)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=salemoney value=\"-{$salemoney}\">"):"&nbsp;")."</td>\n";
			if($_ord->paymethod=="B" && $mode=="update") {
				echo "	<td align=center><a href=javascript:OrderUpdate('2','','','','','')><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='수정'></a><br><img width=0 height=2 border=0><br><a href=javascript:OrderDelete('2','','','','')><img src='images/ordtl_minidel.gif' align=absmiddle border=0 alt='삭제'></a></td>\n";
			}
			echo "</tr>\n";
		}


		if($dc_price<>0) {
			//$salereserve=$_ord->mem_reserve;
			$salemoney=-$dc_price;
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
			if($_ord->paymethod=="B" && $mode=="update") {
				echo "	<td align=center><a href=javascript:OrderUpdate('2','','','','','')><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='수정'></a><br><img width=0 height=2 border=0><br><a href=javascript:OrderDelete('2','','','','')><img src='images/ordtl_minidel.gif' align=absmiddle border=0 alt='삭제'></a></td>\n";
			}
			echo "</tr>\n";
			$in_reserve+=$salereserve;
		}
		if($_ord->reserve>0) {
			echo "<tr bgcolor=#F5F5F5>\n";
			echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				echo "	<td>&nbsp;</td>\n";
			}
			echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#0054A6>적립금사용액</font></td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($_ord->paymethod!="B" || $mode!="update"?"- ".number_format($_ord->reserve)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=usereserve value=\"{$_ord->reserve}\">")."</td>\n";
			if($_ord->paymethod=="B" && $mode=="update") {
				echo "	<td align=center><a href=javascript:OrderUpdate('3','','','','','')><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='수정'></a><br><img width=0 height=2 border=0><br><a href=javascript:OrderDelete('3','','','','')><img src='images/ordtl_minidel.gif' align=absmiddle border=0 alt='삭제'></a></td>\n";
			}
			echo "</tr>\n";
		}
		$totalprice=$totalprice-$salemoney-$_ord->reserve;
		if($_shopdata->card_payfee>0 && strstr("CPM",$_ord->paymethod[0]) && $_ord->price<>$totalprice) {
			echo "<tr bgcolor=#F5F5F5>\n";
			echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				echo "	<td>&nbsp;</td>\n";
			}
			echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#F26622>카드수수료</font></td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".number_format($_ord->price-$totalprice)."&nbsp;</td>\n";
			echo "</tr>\n";
		}
		$temp = substr($_ord->ordercode,0,4)."/".substr($_ord->ordercode,4,2)."/".substr($_ord->ordercode,6,2)." ".substr($_ord->ordercode,8,2).":".substr($_ord->ordercode,10,2).":".substr($_ord->ordercode,12,2);
		$message=explode("[MEMO]",$_ord->order_msg);
		$message[0]=str_replace("\"","&quot;",$message[0]);
		$message[0]=str_replace("\"","",$message[0]);

		$message[0]=str_replace("\r\n","<br>\n&nbsp;&nbsp;",$message[0]);
		/*
		$mes1 = explode("<br>",$message[0]);
		$mescnt = count($mes1);
		$message[0]="";
		for($i=0;$i<$mescnt;$i++) {
			//$message[0].=messageview2($mes1[$i],80)."<br>";
		}
		*/
		
	
		echo "<tr bgcolor=#F5F5F5>\n";
		echo "	<td>&nbsp;</td>\n";
		if($vendercnt>0) {
			echo "	<td>&nbsp;</td>\n";
		}
		echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#0054A6>에누리금액</font></td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".($mode!="update"?"- ".number_format($_ord->enuri_price)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=enuriprice value=\"{$_ord->enuri_price}\">")."</td>\n";
		if($mode=="update") {
			echo "	<td align=center><a href=javascript:OrderUpdate('6','','','','','')><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='수정'></a><br><img width=0 height=2 border=0></td>\n";
		}
		echo "</tr>\n";

		
		
		echo "<tr ";
		if($_ord->reserve==0) echo " bgcolor=#F5F5F5";
		echo ">\n";
		echo "	<td colspan=".($colspan-4)." style=\"font-size:8pt;padding:5,27\"><B>총 합계</B> </td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=center style=\"font-size:8pt\">{$sumquantity}</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X"?number_format($in_reserve)."&nbsp;":"&nbsp")."</td>\n";
		echo "	<td align=right style=\"font-size:8pt\"> ".($_ord->paymethod!="B" || $mode!="update"?number_format($_ord->price)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=sumprice value=\"{$_ord->price}\">")."</td>\n";
		if($_ord->paymethod=="B" && $mode=="update") { 
			echo "	<td align=center><a href=javascript:OrderUpdate('5','','','','','')><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='총금액수정'></a></td>";
		}

		echo "</form>\n";

		echo "</tr>\n";

		$candate = date("Ymd",strtotime('-15 day'));
		if((!strstr("RA", $_ord->del_gbn) && (!strstr("QP",$_ord->paymethod[0]) || $_ord->price==0))
		|| (!strstr("RA", $_ord->del_gbn) && strstr("QP",$_ord->paymethod[0]) && ($_ord->deli_gbn=="C" || substr($_ord->ordercode,0,8)<$candate) && $_ord->deli_gbn!="Y")) {
			echo "<tr bgcolor=#FFFFFF height=24 class=\"page_screen\">\n";
			echo "	<td align=right colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1))."><B>미입금 및 주문취소에 따른 :</B> <a href=\"javascript:RestoreOrder('quan','{$_ord->deli_gbn}','{$_ord->reserve}')\"><img src=\"images/ordtl_restorequan.gif\" border=0 align=absmiddle></a>";
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
		}

        echo "<tr>\n";
		echo "	<td colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1))." bgcolor=#fafafa align=center>\n";
		echo "	<table border=0 cellpadding=0 cellspacing=0 width=96%>\n";
		echo "	<col width=90 style=\"padding-left:3\"></col>\n";
		echo "	<col width=></col>\n";
		echo "	<tr><td colspan=2 height=5></td></tr>\n";
		echo "	<form name=form2 method=post action=\"{$_SERVER['PHP_SELF']}\">\n";
		echo "	<input type=hidden name=type>\n";
		echo "	<input type=hidden name=ordercode value=\"{$_ord->ordercode}\">\n";
		echo "	<input type=hidden name=id value=\"".urlencode($_ord->id)."\">\n";
		echo "	<tr>\n";
		echo "		<td>주문 일자</td>\n";
		echo "		<td>: ".$temp;
		if(($_ord->del_gbn=="Y" || $_ord->del_gbn=="R") && !strstr("Y",$_ord->deli_gbn)) {
			echo " &nbsp;&nbsp;&nbsp;<font color=blue>[주문자가 내용삭제 버튼을 누른 주문서]</font>";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>주문자</td>\n";
		echo "		<td>: ".$_ord->sender_name;
		if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			echo "({$_ord->id}) ";
			if(ord($group_name)) echo " [ 그룹명 : {$group_name} ] ";
			if($hidedisplay!="Y") {
				echo "<a href=\"javascript:MemberMemo('{$_ord->id}')\"><img src='images/ordtl_icnmemo.gif' align=absmiddle border=0 alt='메모 입력/수정하기'></a> ";
				if(ord(trim($usermemo))) {
					echo "<div id=\"membermemo_layer\" style=\"position:absolute; z-index:20; width:300;\"><table border=0 cellspacing=0 cellpadding=1 bgcolor=#7F7F65><tr><td style=\"padding:3\"><font color=#ffffff>{$usermemo} <a href=\"javascript:HideMemo()\"><img src=\"images/x.gif\" align=absmiddle border=0 alt=\"숨기기\"></a>&nbsp;</td></tr></table></div>";
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
			echo "		<td>주문자IP</td>\n";
			echo "		<td>: {$ip}</td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td>연락처</td>\n";
		echo "		<td>: <img src=\"images/ordtl_icntel.gif\" align=absmiddle>전화 : {$_ord->sender_tel}";
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
				$sql.= "WHERE id='{$_ord->id}' AND deli_gbn='Y'";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$ordercnt=$row->cnt;
					$ordersum=$row->money;
				}
				pmysql_free_result($result);
				echo "	<tr>\n";
				echo "		<td bgcolor=#7F7F65><font color=#ffffff>누적 주문</font></td>\n";
				echo "		<td bgcolor=#7F7F65 style=\"color:#ffffff\">: ";
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
		echo "	<tr>\n";
		echo "		<td>받는분</td>\n";
//		echo "		<td>: {$_ord->receiver_name}</td>\n";
		echo "		<td>&nbsp;&nbsp;<input type='text' size='10' name='receiver_name' value='{$_ord->receiver_name}'>&nbsp;<input type=button value='입 력' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:40\" onclick=\"MemoUpdate()\"></td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td valign=top style=\"padding-top:5px\">받는 주소</td>\n";
		echo "		<td>: \n";
		echo "		<span class=\"page_screen\">\n";
		$address = str_replace("\n"," ",trim($_ord->receiver_addr));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+7);
		}
		$post = str_replace("우편번호 : ","",$post);
		$arpost = explode("-",$post);
		echo "		우편번호 : <input name=post1 size=3 value=\"{$arpost[0]}\" onclick=\"this.blur();f_addr_search('form2','post','address1',2);\">-<input name=post2 size=3 value=\"{$arpost[1]}\" onclick=\"this.blur();f_addr_search('form2','post','address1',2);\">\n";
		echo "		<input type=button value='우편번호검색' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:80\" onclick=\"f_addr_search('form2','post','address1',2);\"><br>\n";
		echo "		&nbsp;&nbsp;주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소: <input type=text name=address1 size=50 value=\"{$address}\"> <input type=button value='주소수정' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:60\" onclick=\"AddressUpdate()\">\n";
		echo "		</span>\n";
		echo "		<span class=\"page_print\">\n";
		echo "		우편번호 : {$arpost[0]}-{$arpost[1]}<br>\n";
		echo "		&nbsp;&nbsp;주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소: {$address}\n";
		echo "		</span>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>연락처</td>\n";
		echo "		<td>: <img src=\"images/ordtl_icntel.gif\" align=absmiddle>전 &nbsp; 화: <input type='text' name='receiver_tel1' size='12' value='{$_ord->receiver_tel1}'> , <input type='text' name='receiver_tel2' size='10' value='{$_ord->receiver_tel2}'>&nbsp;<input type=button value='입 력' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:40\" onclick=\"MemoUpdate()\"></td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>결제 방법</td>\n";
		echo "		<td>: ";

		$pgdate = date("YmdHi",strtotime('-2 hour'));
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드",/*"P"=>"신용카드(매매보호)",*/"M"=>"핸드폰");

		if($_ord->pay_data=="신용카드결제 - 카드작성중" && substr($_ord->ordercode,0,12)<=$pgdate) $_ord->pay_data=$arpm[$_ord->paymethod[0]]." 에러";

		if (strstr("BOQ",$_ord->paymethod[0])) {	//무통장, 가상계좌, 가상계좌 에스크로
			if($_ord->paymethod=="B") echo "<font color=#FF5D00>무통장입금</font>\n";
			elseif($_ord->paymethod[0]=="O") echo "<font color=#FF5D00>가상계좌</font>\n";
			else echo "매매보호 - 가상계좌";

			if(!strstr("CD",$_ord->deli_gbn) || $_ord->paymethod=="B"){
				if($_ord->paymethod=="B"){
					$arrpayinfo=explode("=",$_data->bank_account);
					$arrpayinfo=explode(",",$arrpayinfo[0]);
					echo "<select name='pay_data'>";
					foreach($arrpayinfo as $v){
					$selected = $v==$_ord->pay_data?"selected":"";
					echo "	<option value='{$v}' {$selected}>{$v}</option>";
					}
					echo "</select>&nbsp;<input type=button value='입 력' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:40\" onclick=\"MemoUpdate()\">";
				}else echo "【 {$_ord->pay_data} 】";
			}else echo "【 계좌 취소 】";

			if (strlen($_ord->bank_date)>=12) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td><FONT COLOR=red><B>입금확인</B></FONT></td>\n";
				echo "	<td>: <B><font color=red>".substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")</font></B>";
			} elseif(strlen($_ord->bank_date)==9) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td><FONT COLOR=red><B>입금확인</B></FONT></td>\n";
				echo "	<td>: <B><font color=red>환불</font></B>";
			}
		} elseif($_ord->paymethod[0]=="M") {	//핸드폰 결제
			echo "핸드폰 결제【 ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>결제취소 완료</font> 】";
				else echo "<font color=red>결제가 성공적으로 이루어졌습니다.</font>";
			}
			else echo "결제가 실패되었습니다.";
			echo " 】";
		} elseif($_ord->paymethod[0]=="P") {	//매매보호 신용카드
			echo "매매보호 - 신용카드";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>카드결제 취소완료</font> 】";
				elseif($_ord->pay_admin_proc=="Y") echo "【 카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no} 】";
			}
			else echo "【 {$_ord->pay_data} 】";
		} elseif ($_ord->paymethod[0]=="C") {	//일반신용카드
			echo "<font color=#FF5D00>신용카드</font>\n";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>카드결제 취소완료</font> 】";
				elseif($_ord->pay_admin_proc=="Y") echo "【 카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no} 】";
			}
			else echo "【 {$_ord->pay_data} 】";
		} elseif ($_ord->paymethod[0]=="V") {
			echo "실시간 계좌이체 : ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=005000> [환불]</font> 】";
				else echo "<font color=red>{$_ord->pay_data}</font>";
			}
			else echo "결제가 실패되었습니다.";
		}

		if(strstr("QP",$_ord->paymethod[0]) && strstr("Y",$_ord->escrow_result) && $_ord->deli_gbn!="C") echo " - <font color=red><b>[구매확인]</b></font>";
		elseif(strstr("QP",$_ord->paymethod[0]) && strstr("C",$_ord->escrow_result) && $_ord->deli_gbn=="C") echo " - <font color=red><b>[구매취소]</b></font>";
		echo "		</td>\n";
		echo "	</tr>\n";
		$ardelivery=array("Y"=>"발송중","N"=>"미발송","C"=>"주문취소","X"=>"배송요청","S"=>"발송준비","D"=>"취소요청","E"=>"환불대기","H"=>"배송(정산보류)");
		echo "	<tr>\n";
		echo "		<td>영수증</td>\n";
		echo "		<td>: ";
		if($_ord->receipt_yn=="Y"  && $_ord->paymethod=="B"){
			echo "신청";
		}else if($_ord->receipt_yn=="N"  && $_ord->paymethod=="B"){
			echo "미신청";
		}else{
			echo "-";
		}
		echo "</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>발송 여부</td>\n";
		echo "		<td>: <font color=#A00000>{$ardelivery[$_ord->deli_gbn]}</font>";
		if(strlen($_ord->deli_date)==14) {
			echo " - 발송셋팅일 : ".substr($_ord->deli_date,0,4)."/".substr($_ord->deli_date,4,2)."/".substr($_ord->deli_date,6,2)." (".substr($_ord->deli_date,8,2).":".substr($_ord->deli_date,10,2).")";
		}

		// KCP 매매보호 가상계좌이면서 취소요청의 경우 환불계좌 미리 등록
		if(strstr("Q",$_ord->paymethod[0]) && strlen($_ord->bank_date)==14 && ($pg_type!="C" && $pg_type!="D")) {
			if(strlen($_ord->deli_date)!=14) {
				echo " <a href='javascript:escrow_bank_account()'><font color=red><U>[환불계좌수기입력]</U></font></a>";
			}
		}

		echo "		</td>\n";
		echo "	</tr>\n";
		if($in_reserve>0 && $_ord->deli_gbn=="N" && strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X"){
			echo "	<tr>\n";
			echo "		<td>&nbsp;</td>\n";
			echo "		<td><font color=#0000FF>&nbsp;&nbsp;* 배송완료 버튼을 누르면 회원에게 적립금</font> <font color=#A00000>".number_format($in_reserve)."원</font><font color=#0000FF>이 적립됩니다.</font></td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr height=22>\n";
		echo "		<td valign=top style=\"padding-top:5\">주문요청사항</td>\n";
		echo "		<td>&nbsp;&nbsp;<textarea name=memo0 cols=76 rows=3 style=\"font-size:9pt\">{$message[0]}</textarea>&nbsp;<input type=button value='입 력' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:40\" onclick=\"MemoUpdate()\"></td>\n";
//		echo "		<td>:{$message[0]}</td>\n";
		echo "	</tr>\n";

		//for($j=0;$j<count($prdata);$j++) {
			$j=0;
			if(ord($prdata[$j]->order_prmsg)) {
				echo "	<tr height=22 class=\"page_screen\">\n";
				echo "		<td valign=middle>주문메세지</td>\n";
				echo "		<td style=\"padding-left:7\">";
				//echo "	<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> {$prdata[$j]->productname}<BR>\n";
				echo "<textarea style=\"width:95%;height:38;overflow-x:hidden;overflow-y:auto;\" readonly>{$prdata[$j]->order_prmsg}</textarea>\n";
				echo "		</td>\n";
				echo "	</tr>\n";
				echo "<tr><td colspan=2 height=5></td></tr>\n";

				echo "	<tr height=22 class=\"page_print\">\n";
				echo "		<td valign=middle>주문메세지</td>\n";
				echo "		<td style=\"padding-left:7\">";
				echo "		<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> {$prdata[$j]->productname}<BR>\n";
				echo "		{$prdata[$j]->order_prmsg}";
				echo "		</td>\n";
				echo "	</tr>\n";
				echo "<tr><td colspan=2 height=3></td></tr>\n";
			}
		//}

		echo "	<tr height=58 class=\"page_screen\">\n";
		echo "		<td valign=top style=\"padding-top:8\">주문관련 메모</td>\n";
		echo "		<td style=\"padding-top:3\">\n";
		echo "		<font style=\"line-height:20px\">&nbsp;&nbsp;<textarea name=memo1 cols=76 rows=3 style=\"font-size:9pt\">{$message[1]}</textarea>&nbsp;<input type=button value='입 력' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:40\" onclick=\"MemoUpdate()\"><br>	&nbsp;&nbsp;<font color=#0000FF>*쇼핑몰 운영자만 확인할수 있는 주문관련 메모를 남길 수 있습니다.</font>";
		echo "		</td>\n";
		echo "	</tr>\n";
		if(ord($message[1])) {
			echo "	<tr height=58 class=\"page_print\">\n";
			echo "		<td valign=top style=\"padding-top:8\">주문관련 메모</td>\n";
			echo "		<td style=\"padding-top:3\">: \n";
			echo "		{$message[1]}\n";
			echo "		</td>\n";
			echo "	</tr>\n";
		}
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
		echo "	</td>\n";
		echo "</tr>\n";

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
		}
	}
?>
	</table>
	<!-- 주문내역 끝 -->
	</td>
</tr>

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

<tr><td height=10></td></tr>

</table>
<?=$onload?>
</body>
</html>
