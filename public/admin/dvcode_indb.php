<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

//exdebug($_POST);
//exit;

$delimailok = $_POST["delimailtype"]?$_POST["delimailtype"]:"Y";	//배송완료에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
$deli_com   = $_POST["delicom"];
$deli_num   = $_POST["dvcode"];
$deli_name  = $_POST["deliname"];
$idx        = $_POST["idx"];
$ordercode  = $_POST["ordercode"];
$type       = $_POST["mode"];
if($type == "updatedvcode") $type = "delivery";

$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

//exdebug($delimailok);

//$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}'";
$sql = "select  a.ordercode, b.deli_gbn, a.paymethod, a.sender_tel, a.sender_name, 
                b.price, b.quantity, b.coupon_price, b.use_point, b.deli_price,
                (b.price*b.quantity)-b.coupon_price-b.use_point+b.deli_price as act_price, b.store_code,
				a.pg_ordercode
        from    tblorderinfo a 
        join    tblorderproduct b on a.ordercode = b.ordercode 
        where   a.ordercode = '{$ordercode}' 
        and     b.idx = ".$idx."
        ";
$result = pmysql_query($sql,get_db_conn());
$_ord = pmysql_fetch_object($result);
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


if($type=="delivery" && ord($ordercode)) {

	if(strstr("NXS",$_ord->deli_gbn)) {

		$patterns = array(" ","_","-");
		$replace = array("","","");
		$deli_num = str_replace($patterns,$replace,$deli_num);

		###에스크로 서버에 배송정보 전달 - 에스크로 결제일 경우에만.....

		//배송한 상품의 수를 체크한다.
		list($op_deli_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='{$ordercode}' AND deli_gbn = 'Y' "));
		list($deli_name)=pmysql_fetch_array(pmysql_query("SELECT company_name FROM tbldelicompany WHERE code='{$deli_com}' "));
		list($pro_count)=pmysql_fetch_array(pmysql_query("select count(idx) from tblorderproduct WHERE ordercode='{$ordercode}' AND idx != '".$idx."' "));

		if ($op_deli_cnt==0) { // 처음 배송된 상품일 경우
			if(ord($deli_name)==0) {
				$deli_name="자가배송";
				$deli_num="0000";
			}
		}

if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
//	exdebug($_ord->pg_ordercode);
//	exdebug ($_ord->paymethod[0]);
//	exdebug($query);
//	exdebug("paygate/".$_ord->paymethod[1]."/delivery.php");
//	exdebug($_ShopInfo);
//	exit;
}

		if(strstr("QP", $_ord->paymethod[0]) && $op_deli_cnt==$pro_count && $ordercode == $_ord->pg_ordercode) {

			if($pg_type=="A") {	//KCP
				$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

				$delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

				$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);

				if (substr($delivery_data,0,2)!="OK") {
					$tempdata=explode("|",$delivery_data);
					$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
					if(ord($tempdata[1])) $errmsg=$tempdata[1];
					if(ord($errmsg)) {
						echo "<script> alert('{$errmsg}');</script>";
						exit;
					}
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
					if(ord($errmsg)) {
						echo "<script> alert('{$errmsg}');</script>";
						exit;
					}
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
					if(ord($errmsg)) {
						echo "<script> alert('{$errmsg}');</script>";
						exit;
					}
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
					if(ord($errmsg)) {
						echo "<script> alert('{$errmsg}');</script>";
						exit;
					}
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
		
		/*$sql = "UPDATE tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		if(pmysql_query($sql,get_db_conn())) {
			$sql = "UPDATE  tblorderproduct SET deli_gbn='Y', deli_date='".date("YmdHis")."' ".$deliQry;
			$sql.= "WHERE   ordercode='{$ordercode}' ";
			$sql.= "AND     idx = {$idx} ";
			$sql.= "AND deli_gbn!='Y' ";
			pmysql_query($sql,get_db_conn());
            //exdebug($sql);
		}
        // 상태변경 호출
        orderProductStepUpdate($exe_id, $ordercode, $idx, 3);*/
		
		$sql = "UPDATE tblorderproduct SET deli_gbn='Y', deli_date='".date("YmdHis")."' ".$deliQry;
		$sql.= "WHERE ordercode='{$ordercode}' ";
		$sql.= "AND idx = {$idx} ";
		$sql.= "AND op_step < 40 ";
		if(pmysql_query($sql,get_db_conn())) {

			// 신규상태 변경 추가 - (2016.04.15 - 김재수 추가)
			orderProductStepUpdate($exe_id, $ordercode, $idx, '3'); // 배송중

			$sql = "UPDATE  tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
			$sql.= "WHERE   ordercode='{$ordercode}' ";
			pmysql_query($sql,get_db_conn());
							
			// 신규상태 변경 추가
			orderStepUpdate($exe_id, $ordercode, '3', '0' ); // 배송중
			
			//송장번호 erp전송			
			sendErpDeliveryInfo($ordercode, $idx, $deli_com, $deli_num, $_ord->store_code);
		}

		$isupdate=true;

		if($delimailok=="Y") {	//배송완료 메일을 발송할 경우
			$delimailtype="N";
            //exdebug($_shopdata->shopname);
            //exdebug($shopurl);
            //exdebug($_shopdata->design_mail);
            //exdebug($_shopdata->info_email);

			SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype, $idx);

			if(ord($_ord->sender_tel)) {
				# SMS ( 부분배송 안내메세지 )
				$op_cnt = pmysql_fetch_array( pmysql_query( "SELECT COUNT( * ) as cnt FROM tblorderproduct WHERE ordercode = '".$ordercode."'", get_db_conn() ) );
				if( $op_cnt[0] > 1 ){
					$mem_return_msg = sms_autosend( 'mem_delinum', $ordercode, $idx, '' );
					$admin_return_msg = sms_autosend( 'admin_delinum', $ordercode, $idx, '' );
				} else if( $op_cnt[0] == 1 ){
					$mem_return_msg = sms_autosend( 'mem_delivery', $ordercode, '', '' );
					$admin_return_msg = sms_autosend( 'admin_delivery', $ordercode, '', '' );
				}
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

		echo "<script>parent.location.reload(); </script>";
		exit;
	} elseif(!strstr("NXS",$_ord->deli_gbn)) {
		echo "<script>alert(\"이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.\");</script>";
	}
}
?>