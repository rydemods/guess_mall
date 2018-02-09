<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

# 문자열 요소에 숫자만 골라 리턴해준다
function character_chack ( $character ) {

    $tmp_character = '';
    $character_type = false;
    $returnArr = array();

    for( $i = 0; $i < strlen( $character ); $i++ ){
        if( is_numeric( $character[$i] ) ){
            $tmp_character .= $character[$i];
        } else {
            $character_type = true;
        }
    }

    //$returnArr = array( $character_type, $tmp_character, 'type'=>$character_type, 'string'=>$tmp_character );

    return $tmp_character;
}

$_shopdata=new ShopData($_ShopInfo);
$_shopdata=$_shopdata->shopdata;
$_ShopInfo->getPgdata();
$_shopdata->escrow_id	= $_data->escrow_id;
$_shopdata->trans_id		= $_data->trans_id;
$_shopdata->virtual_id		= $_data->virtual_id;
$_shopdata->card_id		= $_data->card_id;
$_shopdata->mobile_id	= $_data->mobile_id;

if (!$shopurl) $shopurl = $_shopdata->shopurl;

$type       = "delivery";
$delimailok = $_POST["delimailtype"]?$_POST["delimailtype"]:"Y";	//배송완료에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)

$mode				= $_POST["mode"];
$arr_idx				= explode(",", $_POST['idxs']);
$arr_ordercode	= explode(",", $_POST['ordercodes']);
$arr_deli_com		= explode(",", $_POST['delicoms']);
$arr_deli_num		= explode(",", $_POST['delinums']);

$exe_id		= $_VenderInfo->getId()."||vender";	// 실행자 아이디|이름|타입

if ($mode == 'step3change') { // 배송중으로 변경
	
	//배송회사 정보를 가져온다.
	$delicomlist=array();
	$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$deli_code	= trim($row->code);
		$delicomlist[$deli_code]=$row->company_name;
	}
	pmysql_free_result($result);



	$arrResultMsg = array();
	$tmp_arr_deli = array();
	$arr_deli_idxs = array();

	for ( $i = 0; $i < count($arr_idx); $i++ ) {

		$deli_com		= $arr_deli_com[$i];
		$deli_num		= $arr_deli_num[$i];
		$deli_name	= $delicomlist[$arr_deli_com[$i]];
		$idx				= $arr_idx[$i];
		$ordercode		= $arr_ordercode[$i];

		$deli_num		= character_chack(  $deli_num ); // 문자열을 제거 후에 숫자만 넣어준다

		$arrResult = array("주문번호 : ".$ordercode." / 배송업체 : ".$deli_name." / 송장번호 : ".$deli_num);

		//exdebug($delimailok);

		//$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}'";
		$sql = "select  a.ordercode, b.deli_gbn, a.paymethod, a.sender_tel, a.sender_name, 
						b.price, b.quantity, b.coupon_price, b.use_point, b.deli_price,
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

				$fail_cnt	= 0;

				$patterns = array(" ","_","-");
				$replace = array("","","");
				$deli_num = str_replace($patterns,$replace,$deli_num);

				###에스크로 서버에 배송정보 전달 - 에스크로 결제일 경우에만.....

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
								if(ord($errmsg)) {
									array_push($arrResult, " / PG처리 : ".$errmsg);
									array_push($arrResultMsg, $arrResult);
									$fail_cnt++;
								}
							} else {
								$tempdata=explode("|",$delivery_data);
								if(ord($tempdata[1])) $errmsg=$tempdata[1];
								if(ord($errmsg)) {
									array_push($arrResult, " / PG처리 : ".$errmsg);
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
								if(ord($errmsg)) {
									array_push($arrResult, " / PG처리 : ".$errmsg);
									array_push($arrResultMsg, $arrResult);
									$fail_cnt++;
								}
							} else {
								$tempdata=explode("|",$delivery_data);
								if(ord($tempdata[1])) $errmsg=$tempdata[1];
								if(ord($errmsg)) {
		//                            echo "<script> alert('{$errmsg}');</script>";
									array_push($arrResult, " / PG처리 : ".$errmsg);
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
								if(ord($errmsg)) {
									array_push($arrResult, " / PG처리 : ".$errmsg);
									array_push($arrResultMsg, $arrResult);
									$fail_cnt++;
								}
							} else {
								$tempdata=explode("|",$delivery_data);
								if(ord($tempdata[1])) $errmsg=$tempdata[1];
								if(ord($errmsg)) {
		//                            echo "<script> alert('{$errmsg}');</script>";
									array_push($arrResult, " / PG처리 : ".$errmsg);
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
								if(ord($errmsg)) {
									array_push($arrResult, " / PG처리 : ".$errmsg);
									array_push($arrResultMsg, $arrResult);
									$fail_cnt++;
								}
							} else {
								$tempdata=explode("|",$delivery_data);
								if(ord($tempdata[1])) $errmsg=$tempdata[1];
								if(ord($errmsg)) {
		//                            echo "<script> alert('{$errmsg}');</script>";
									array_push($arrResult, " / PG처리 : ".$errmsg);
									array_push($arrResultMsg, $arrResult);
								}
							}
						}
					}
				}

				if ($fail_cnt == 0) {
					$deliQry = "";
					if( strlen( $deli_num ) > 0 && strlen( $deli_com ) > 0 ){
						$deliQry = ", deli_com = '".$deli_com."', deli_num = '".$deli_num."' ";
					}
                    
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
                    }

					$isupdate=true;

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

					array_push($arrResult, " / 처리 : 성공");
					array_push($arrResultMsg, $arrResult);
				}
			} elseif(!strstr("NXS",$_ord->deli_gbn)) {
				array_push($arrResult, " / 처리 : 실패");
				array_push($arrResultMsg, $arrResult);
			}
		}		
	}

	$canmess="배송중으로 처리되었습니다.";

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

	foreach ( $arrResultMsg as $arrData ) {
		$canmess.="\\n";
		foreach ( $arrData as $data ) {
			$canmess.="{$data}";
		}
	}
	echo "<html></head><body onload=\"alert('".$canmess."');parent.location.reload();\"></body></html>";exit;
}
?>