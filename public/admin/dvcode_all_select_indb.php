<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

//exdebug($_POST);
//exit;
$type       = "delivery";
$delimailok = $_POST["delimailtype"]?$_POST["delimailtype"]:"Y";	//배송완료에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)

$arr_deli_com   = $_POST["delicoms"];
$arr_deli_num   = $_POST["dvcodes"];
$arr_deli_name  = $_POST["delinames"];
$arr_idx        = $_POST["idxs"];
$arr_ordercode  = $_POST["ordercodes"];

$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

/*
exdebug($_POST);
exdebug($arr_deli_com);
exdebug($arr_deli_num);
exdebug($arr_deli_name);
exdebug($arr_idx);
exdebug($arr_ordercode);
*/

$arrResultMsg = array();
$tmp_arr_deli = array();
$arr_deli_idxs = array();
for ( $i = 0; $i < count($arr_idx); $i++ ) {

    $deli_com   = $arr_deli_com[$i];
    $deli_num   = $arr_deli_num[$i];
    $deli_name  = $arr_deli_name[$i];
    $idx        = $arr_idx[$i];
    $ordercode  = $arr_ordercode[$i];

    $arrResult = array($ordercode, $idx, $deli_name, $deli_num);

    //exdebug($delimailok);

    //$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}'";
    $sql = "select  a.ordercode, b.deli_gbn, a.paymethod, a.sender_tel, a.sender_name, 
                    b.price, b.quantity, b.coupon_price, b.use_point, b.deli_price, b.store_code,
                    (b.price*b.quantity)-b.coupon_price-b.use_point+b.deli_price as act_price	
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

			if(strstr("QP", $_ord->paymethod[0]) && $op_deli_cnt==$pro_count) {

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
//                            echo "<script> alert('{$errmsg}');</script>";
							array_push($arrResult, $errmsg);
							array_push($arrResultMsg, $arrResult);
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
//                            echo "<script> alert('{$errmsg}');</script>";
							array_push($arrResult, $errmsg);
							array_push($arrResultMsg, $arrResult);
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
//                            echo "<script> alert('{$errmsg}');</script>";
							array_push($arrResult, $errmsg);
							array_push($arrResultMsg, $arrResult);
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
//                            echo "<script> alert('{$errmsg}');</script>";
							array_push($arrResult, $errmsg);
							array_push($arrResultMsg, $arrResult);
						}
					}
				} elseif($pg_type=="G") {	//NICE
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
//                            echo "<script> alert('{$errmsg}');</script>";
							array_push($arrResult, $errmsg);
							array_push($arrResultMsg, $arrResult);
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
                // 배송 메일 및 문자발송 세팅 ( 배송정보를 기준으로 묶는다.
                $tmp_arr_deli_idx = array_search( $deliQry, $tmp_arr_deli );
                if( $tmp_arr_deli_idx === false && $deliQry != '' ) {
                    $tmp_arr_deli[] = $deliQry;
                    $arr_deli_idxs[] = array( 'ordercode'=>$ordercode, 'idxs'=>$idx, 'deli_com'=>$deli_com, 'deli_num'=>$deli_num );
                } else if( $deliQry != '' ) {
                    $arr_deli_idxs[$tmp_arr_deli_idx]['idxs'] = $arr_deli_idxs[$tmp_arr_deli_idx]['idxs'].','.$idx;
                }

            }

//            echo "<script>parent.location.reload(); </script>";
            array_push($arrResult, "성공");
            array_push($arrResultMsg, $arrResult);
        } elseif(!strstr("NXS",$_ord->deli_gbn)) {
//            echo "<script>alert(\"이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.\");</script>";
            array_push($arrResult, $errmsg);
            array_push($arrResultMsg, $arrResult);
        }
    }
}
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

    if ( count($arrResultMsg) > 0 ) {
        Header("Content-type: application/vnd.ms-excel");
        Header("Content-Disposition: attachment; filename=result_".date("Ymd").".xls");
        Header("Pragma: no-cache");
        Header("Expires: 0");
        Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        Header("Content-Description: PHP4 Generated Data");
?>

<html>
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head> 
<body>  

<table border="1">
    <tr align="center">
        <th>주문코드</th>
        <th>주문인덱스</th>
        <th>배송업체명</th>
        <th>송장번호</th>
        <th>결과</th>
    </tr>
<?php 
        foreach ( $arrResultMsg as $arrData ) {
            echo "<tr>";
            foreach ( $arrData as $data ) {
                echo "<td>{$data}</td>";
            }
            echo "</tr>";
        }
?>
</table>
</body>
</html>

<?php
    }
?>
