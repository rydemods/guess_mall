<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");


if($_POST[mode]=="order_transe"){

	if(is_array($_POST[order_idx])){
		foreach($_POST[order_idx] as $order_idx){
			$ordercode=$_POST["ordercode"][$order_idx];
			$delimailok='N';	//배송완료에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
			$deli_com=$_POST["deli_com"][$order_idx];
			$deli_name=$_POST["deli_name"][$order_idx];
			$deli_num=$_POST["deli_num"][$order_idx];

			$sql = "SELECT SUM(reserve*quantity) as in_reserve FROM tblorderproduct ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$in_reserve = $row->in_reserve;
			}


			$errmsg="";
			$pgid_info="";
			$pg_type="";

			$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($_ord=pmysql_fetch_object($result)) {
				if(strstr("NSX", $_ord->deli_gbn)) {
					if(strstr("BOQ", $_ord->paymethod[0]) && strlen($_ord->bank_date)<12) {
						$errmsg="입금확인이 않된 주문서입니다. 다시 확인하시기 바랍니다.";
					} else if(strstr("CP", $_ord->paymethod[0]) && $_ord->pay_admin_proc!="Y") {
						$errmsg="결제가 실패한 주문서입니다. 다시 확인하시기 바랍니다.";
					} else if(strstr("M", $_ord->paymethod[0]) && $_ord->pay_admin_proc!="N" && $_ord->pay_flag!="0000") {
						$errmsg="결제가 실패한 주문서입니다. 다시 확인하시기 바랍니다.";
					} else if(strstr("V", $_ord->paymethod[0]) && $_ord->pay_admin_proc!="N" && $_ord->pay_flag!="0000") {
						$errmsg="결제가 실패한 주문서입니다. 다시 확인하시기 바랍니다.";
					}
				} else if($_ord->deli_gbn=="Y") {
					$errmsg="이미 배송완료된 주문서입니다.";
				} else {
					$errmsg="이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.";
				}
			} else {
				$errmsg="해당 주문코드가 존재하지 않습니다.";
			}

			pmysql_free_result($result);

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

			if(ord($errmsg)==0) {
				$patterns = array(" ","_","-");
				$replace = array("","","");
				$deli_num = str_replace($patterns,$replace,$deli_num);
				
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
					} else if($pg_type=="B") {	//LG데이콤
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
					} else if($pg_type=="C") {	//올더게이트
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
					} else if($pg_type=="D") {	//INICIS
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
					$sql = "UPDATE tblorderproduct SET ";
					$sql.= "deli_gbn	= 'Y', ";
					$sql.= "deli_date	= '".date("YmdHis")."', ";
					$sql.= "deli_com	= '{$deli_com}', ";
					$sql.= "deli_num	= '{$deli_num}' ";
					$sql.= "WHERE ordercode='{$ordercode}' ";
					$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
					pmysql_query($sql,get_db_conn());
				}

				if($delimailok=="Y") {	//배송완료 메일을 발송할 경우
					$delimailtype="N";
					SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->mail_type, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype);

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
							if($rowsms->mem_delinum=="Y") {	//송장안내메세지
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
				
				$dc_price=(int)$_ord->dc_price;
				if($dc_price<>0) {
					if($dc_price>0) $in_reserve+=$dc_price;
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
				$success++;
			}else{
				echo "주문번호 $ordercode 는 ".$errmsg."<br>";
			}
				
			
		}
	}
	if($success){
	echo number_format($success)." 건의 주문이 배송완료 처리되었습니다";
	echo "<br>";
	}
	echo "<a href='$_POST[returnUrl]'>돌아가기</a>";
//	alert_go(number_format($success)." 건의 주문이 배송완료 처리되었습니다", $_POST[returnUrl]);

	exit;
}

if($_GET["type"]=="init" && ord($_GET["ordercode"])) {
	$sql = "SELECT SUM(reserve*quantity) as in_reserve FROM tblorderproduct ";
	$sql.= "WHERE ordercode='{$_GET['ordercode']}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$in_reserve = $row->in_reserve;
	} ?>
	<html>
	<head>
	<SCRIPT LANGUAGE="javascript">
	<!--
	function delivery() {
		if(confirm("배송완료된 정보를 메일/SMS로 발송하시겠습니까?")) {
			document.form1.delimailtype.value="Y";
		} else {
			document.form1.delimailtype.value="Y";
		}
		document.form1.submit();
	}
	//-->
	</SCRIPT>
	</head>
	<body bgcolor=#FFFFFF topmargin=1 leftmargin=0 marginwidth=0 marginheight=0>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
	<input type=hidden name=type value="delivery">
	<input type=hidden name=ordercode value="<?=$_GET["ordercode"]?>">
	<input type=hidden name=delimailtype value="N">
	<input type=hidden name=deli_com value="<?=$_GET["deli_com"]?>">
	<input type=hidden name=deli_name value="<?=$_GET["deli_name"]?>">
	<input type=hidden name=deli_num value="<?=$_GET["deli_num"]?>">
	<input type=hidden name=in_reserve value="<?=$in_reserve?>">
	<tr>
		<td align=center><a href='javascript:delivery();'><img src=images/order_csvdelivery_b3.gif align=absmiddle border=0></a></td>
	</tr>
	</form>
	</table>
	</body>
	</html>
<?php exit;
}

$type=$_POST["type"];
$ordercode=$_POST["ordercode"];
$delimailok=$_POST["delimailtype"];	//배송완료에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
$deli_com=$_POST["deli_com"];
$deli_name=$_POST["deli_name"];
$deli_num=$_POST["deli_num"];
$in_reserve=$_POST["in_reserve"];

//배송완료 세팅
if($type=="delivery" && ord($ordercode)) {
	$errmsg="";
	$pgid_info="";
	$pg_type="";

	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($_ord=pmysql_fetch_object($result)) {
		if(strstr("NSX", $_ord->deli_gbn)) {
			if(strstr("BOQ", $_ord->paymethod[0]) && strlen($_ord->bank_date)<12) {
				$errmsg="입금확인이 않된 주문서입니다. 다시 확인하시기 바랍니다.";
			} else if(strstr("CP", $_ord->paymethod[0]) && $_ord->pay_admin_proc!="Y") {
				$errmsg="결제가 실패한 주문서입니다. 다시 확인하시기 바랍니다.";
			} else if(strstr("M", $_ord->paymethod[0]) && $_ord->pay_admin_proc!="N" && $_ord->pay_flag!="0000") {
				$errmsg="결제가 실패한 주문서입니다. 다시 확인하시기 바랍니다.";
			} else if(strstr("V", $_ord->paymethod[0]) && $_ord->pay_admin_proc!="N" && $_ord->pay_flag!="0000") {
				$errmsg="결제가 실패한 주문서입니다. 다시 확인하시기 바랍니다.";
			}
		} else if($_ord->deli_gbn=="Y") {
			$errmsg="이미 배송완료된 주문서입니다.";
		} else {
			$errmsg="이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.";
		}
	} else {
		$errmsg="해당 주문코드가 존재하지 않습니다.";
	}
	pmysql_free_result($result);

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

	if(ord($errmsg)==0) {
		$patterns = array(" ","_","-");
		$replace = array("","","");
		$deli_num = str_replace($patterns,$replace,$deli_num);
		
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
			} else if($pg_type=="B") {	//LG데이콤
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
			} else if($pg_type=="C") {	//올더게이트
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
			} else if($pg_type=="D") {	//INICIS
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
			$sql = "UPDATE tblorderproduct SET ";
			$sql.= "deli_gbn	= 'Y', ";
			$sql.= "deli_date	= '".date("YmdHis")."', ";
			$sql.= "deli_com	= '{$deli_com}', ";
			$sql.= "deli_num	= '{$deli_num}' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			pmysql_query($sql,get_db_conn());
		}

		if($delimailok=="Y") {	//배송완료 메일을 발송할 경우
			$delimailtype="N";
			SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->mail_type, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype);

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
					if($rowsms->mem_delinum=="Y") {	//송장안내메세지
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
		
		$dc_price=(int)$_ord->dc_price;
		if($dc_price<>0) {
			if($dc_price>0) $in_reserve+=$dc_price;
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
		echo "<html>\n";
		echo "<head></head>\n";
		echo "<body bgcolor=#FFFFFF topmargin=1 leftmargin=0 marginwidth=0 marginheight=0>\n";
		echo "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center><A HREF=\"javascript:parent.OrderDetailView('{$ordercode}')\"><img src=\"images/order_csvdelivery_b1.gif\" border=0></A></td></tr></table>\n";
		echo "</body>\n";
		echo "</html>\n";
		exit;
	} else {
		echo "<html>\n";
		echo "<head></head>\n";
		echo "<script>\n";
		echo "function orderdetail(ordercode) {\n";
		if($_ord) {
			echo "	parent.OrderDetailView(ordercode);\n";
		} else {
			echo "	alert('{$errmsg}');\n";
		}
		echo "}\n";
		echo "</script>\n";
		echo "<body bgcolor=#FFFFFF topmargin=1 leftmargin=0 marginwidth=0 marginheight=0 onload=\"alert('{$errmsg}')\">\n";
		echo "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center><A HREF=\"javascript:orderdetail('{$ordercode}')\"><img src=\"images/order_csvdelivery_b2.gif\" border=0></A></td></tr></table>\n";
		echo "</body>\n";
		echo "</html>\n";
		exit;
	}
}
