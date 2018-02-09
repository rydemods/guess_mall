<?php // hspark
$Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$ord_chg=$_POST['ord_chg'];
$ordcode=$_GET['ordcode'];
$ordcode=substr($ordcode,0,-1);
$ordcode_chk=str_replace(",","','",$ordcode);
$ordcode_arr=explode(",",$ordcode);
$ordcode_cnt=count($ordcode_arr);

//송장번호 체크
$chk_qry="select count(*) as cnt from tblorderproduct where ordercode in('".$ordcode_chk."') and deli_num is null
AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%')";
$chk_res=pmysql_query($chk_qry);
$chk_row=pmysql_fetch_array($chk_res);
pmysql_free_result($chk_res);

if($ordcode_cnt>0){

	for($k=0;$k<$ordcode_cnt;$k++){
		
		$ordercode=$ordcode_arr[$k];

		if(!$ordercode) continue;
		
		$sql="SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}'";
		$result=pmysql_query($sql,get_db_conn());
		$_ord=pmysql_fetch_object($result);
		pmysql_free_result($result);
		

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

	
		if($ord_chg=='readybank'){ //미입금처리

			pmysql_query("UPDATE tblorderinfo SET bank_date='', deli_gbn='N' WHERE ordercode='{$ordercode}' ",get_db_conn());
			pmysql_query("UPDATE tblorderproduct SET deli_gbn='N' WHERE ordercode='{$ordercode}' ",get_db_conn());

		}else if($ord_chg=='bank'){ //무통장입금

			pmysql_query("UPDATE tblorderinfo SET bank_date='".date("YmdHis")."', deli_gbn='N' WHERE ordercode='{$ordercode}' ",get_db_conn());
			pmysql_query("UPDATE tblorderproduct SET deli_gbn='N' WHERE ordercode='{$ordercode}' ",get_db_conn());
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
				$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_mobile, "", $fromtel, $date, $msg_mem_bankok, $etcmsg);
			}
			pmysql_free_result($result);

		}else if($ord_chg=='readydeli'){//발송준비

			$sql = "UPDATE tblorderinfo SET deli_gbn='S' WHERE ordercode='{$ordercode}' ";
			if(pmysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='S' WHERE ordercode='{$ordercode}' ";
				$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
				$sql.= "AND deli_gbn='N' ";
				//echo $sql; 
				pmysql_query($sql,get_db_conn());
			}

		}else if($ord_chg=='delivery'){//배송완료
			
			if($chk_row['cnt']==0){	
				$delimailok='Y';

				$rev_qry = "SELECT sum(reserve*quantity) as sumr FROM tblorderproduct WHERE ordercode='{$_ord->ordercode}' ";	
				$rev_res = pmysql_query($rev_qry);
				$rev_row = pmysql_fetch_array($rev_res);
				pmysql_free_result($rev_res);

				$in_reserve=$rev_row['sumr'];

				$deli_data_qry="select a.deli_num, a.deli_com ,b.company_name from tblorderproduct a 
				left join tbldelicompany b on a.deli_com=b.code  where a.ordercode='{$_ord->ordercode}' limit 1";
				$deli_data_res=pmysql_query($deli_data_qry);
				$deli_data_row = pmysql_fetch_array($deli_data_res);
				pmysql_free_result($deli_data_res);


				if(strstr("NXS",$_ord->deli_gbn)) {
					$deli_com=$deli_data_row["deli_com"];
					$deli_num=$deli_data_row["deli_num"];
					$deli_name=$deli_data_row["company_name"];
					
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

						if(ord($_ord->sender_mobile)) {
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

								/*
								echo $rowsms->mem_delinum;
								echo "<br>";
								echo ord($deli_name);	
								echo "<br>";
								echo ord($deli_num);
								echo "<br>";
								echo $rowsms->mem_delivery;
								echo "end";
								*/


								if($rowsms->mem_delinum=="Y" && ord($deli_name) && ord($deli_num)) {	//송장안내메세지
									$etcmsg="송장안내메세지(회원)";
									$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_mobile, "", $fromtel, $date, $msg_mem_delinum, $etcmsg);
								}
								if($rowsms->mem_delivery=="Y") {	//상품발송메세지
									$etcmsg="상품발송메세지(회원)";
									$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_mobile, "", $fromtel, $date, $msg_mem_delivery, $etcmsg);
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

				} elseif(!strstr("NXS",$_ord->deli_gbn)) {
					//echo "<script>alert(\"이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.\");</script>";
					msg('이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.',-1);
				}

				
			}else{
				msg('송장번호가 입력되지 않은 주문이 있습니다. 송장번호를 모두 입력해 주세요.',-1);
			}//if cnt	
		}
	}//for

	msg("변경되었습니다.",-1);

}else{
	msg("변경하실 주문을 선택해주세요.",-1);
}

?>
