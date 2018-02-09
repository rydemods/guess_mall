<?
// =========================================================================================
// FileName         : dvcode_all_indb.php
// Desc             : csv파일 업로드해서 일괄 송장정보 업데이트
// By               : moondding2
// Last Updated     : 2016.03.13
// =========================================================================================

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

$uploaddir = $Dir . "uploads/";
$uploadfile = $uploaddir . basename($_FILES['csv_file']['name']);

$type       = $_POST["mode"];
$delimailok = $_POST["delimailtype"]?$_POST["delimailtype"]:"Y";	//배송완료에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)

$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $uploadfile)) {

    $sql = "select code, lower(company_name) as company_name from tbldelicompany";
    $result = pmysql_query($sql);
 
    $arrDeliCompany = array();   
    while ( $row = pmysql_fetch_object($result) ) {
        $arrDeliCompany[$row->company_name] = $row->code;
    }
    pmysql_free_result($result);

    // 필드 0 : idx
    // 필드 1 : order code
    // 필드 2 : 입금일
    // 필드 3 : 상품명
    // 필드 4 : 상품코드
    // 필드 5 : 창고품목코드
    // 필드 6 : 품번
    // 필드 7 : 색상
    // 필드 8 : 사이즈
    // 필드 9 : 결제수단
    // 필드 10 : 판매가
    // 필드 11 : 수량
    // 필드 12 : 수령자
    // 필드 13 : 우편번호
    // 필드 14 : 주소
    // 필드 15 : 전화번호
    // 필드 16 : 비상전화
    // 필드 17 : 비고
    // 필드 18 : 주문자
    // 필드 19 : 주문자ID
    // 필드 20 : 주문자우편번호
    // 필드 21 : 주문자주소
    // 필드 22 : 주문자전화번호
    // 필드 23 : 주문자핸드폰
    // 필드 24 : 배송업체명
    // 필드 25 : 송장번호
    // 필드 26 : O2O구분
    // 필드 27 : 매장정보
    // 필드 28 : 우선창고코드
    // 필드 29 : 재고
    // 필드 30 : A1801
    // 필드 31 : A1770
    // 필드 32 : A1771

    $handle = fopen($uploadfile,  "r"); 

    $fieldCount = 33;

    $arrResultMsg = array();
    $tmp_arr_deli = array();
    $arr_deli_idxs = array();
    $rowCount = 0; 
    while (($data = fgetcsv($handle, 135000, ",")) !== FALSE) {
        if ( $rowCount == 0 ) {
            // 첫번째 라인은 pass
            $rowCount++;
            continue;
        }

        //echo "cnt = ".count($data)."<br>";
 
        if ( $data && count($data) == $fieldCount || true ) {
            $idx            = trim($data[0]);
            $ordercode      = trim($data[1]);
            $productcode    = trim($data[4]);
            $option         = iconv("euc-kr", "utf-8", trim($data[8]));
            /*$temp = explode("/", $option);
            $temp = array_notnull($temp);

            $opt1_name = $opt2_name = "";
            foreach($temp as $k => $v) {

                $opt1_tmp = explode(":", $v);

                $opt1_name .= trim($opt1_tmp[0])."@#";
                $opt2_name .= trim($opt1_tmp[1])."@#";
            }*/
            $opt1_name = 'SIZE';
            $opt2_name = $option;

            $self_goods_code= trim($data[5]);
            //$deli_name      = iconv("euc-kr", "utf-8", strtolower(trim($data[22])));
            $deli_name      = iconv("euc-kr", "utf-8", trim($data[24]));
            $deli_num       = trim($data[25]);

            // 택배업체 코드번호 조회
            /*
            $deli_com   = "";
            if ( isset($arrDeliCompany[$deli_name]) ) { 
                $deli_com = $arrDeliCompany[$deli_name];
            }
            */
            list($deli_com) = pmysql_fetch("select code from tbldelicompany where lower(company_name) = lower('".$deli_name."')");

            /*
            // order_idx 값 구하기
            $subsql  = "SELECT idx FROM tblorderproduct ";
            $subsql .= "WHERE ordercode = '{$ordercode}' AND productcode = '{$productcode}' ";
            $subsql .= "AND opt1_name = '{$opt1_name}' AND opt2_name = '{$new_opt2_name}' ";
            list($idx) = pmysql_fetch($subsql);
            */

            //$arrResult = array($ordercode, $productcode, $opt1_name, $opt2_name, $deli_name, $deli_num);
            $arrResult = $data;

            if($type == "updatedvcode") $type = "delivery";

            //exdebug($delimailok);

            //$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}'";
            $sql = "select  a.ordercode, b.deli_gbn, a.paymethod, a.sender_tel, a.sender_name, 
                            b.price, b.quantity, b.coupon_price, b.use_point, b.deli_price,
                            (b.price*b.quantity)-b.coupon_price-b.use_point+b.deli_price as act_price, b.store_code, b.delivery_type	
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
				if($_ord->delivery_type == '0' && $_ord->store_code == 'A1801B') { // 일반배송만 처리
					if(strstr("NXS",$_ord->deli_gbn)) {

						$patterns = array(" ","_","-");
						$replace = array("","","");
						$deli_num = str_replace($patterns,$replace,$deli_num);
						$deli_num = sprintf("%.0f", $deli_num); // 지수형태일 경우 숫자로 변환.

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
	//                                    echo "<script> alert('{$errmsg}');</script>";
										array_push($arrResult, iconv('UTF-8', 'EUC-KR', $errmsg));
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
	//                                    echo "<script> alert('{$errmsg}');</script>";
										array_push($arrResult, iconv('UTF-8', 'EUC-KR', $errmsg));
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
	//                                    echo "<script> alert('{$errmsg}');</script>";
										array_push($arrResult, iconv('UTF-8', 'EUC-KR', $errmsg));
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
	//                                    echo "<script> alert('{$errmsg}');</script>";
										array_push($arrResult, iconv('UTF-8', 'EUC-KR', $errmsg));
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
							//$sql.= "AND deli_gbn!='Y' ";
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

	//                    echo "<script>parent.location.reload(); </script>";
						//array_push($arrResult, "성공");
						array_push($arrResult, iconv('UTF-8', 'EUC-KR', '성공'));
						array_push($arrResultMsg, $arrResult);
					} elseif(!strstr("NXS",$_ord->deli_gbn)) {
						$errmsg = "이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.";
	//                    echo "<script>alert(\"이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.\");</script>";
						array_push($arrResult, iconv('UTF-8', 'EUC-KR', $errmsg));
						array_push($arrResultMsg, $arrResult);
					}
				} else {
					$errmsg = "송장등록 불가능한 물품입니다. 다시 확인하시기 바랍니다.";
					array_push($arrResult, iconv('UTF-8', 'EUC-KR', $errmsg));
					array_push($arrResultMsg, $arrResult);
				}
            }

        }

        $rowCount++;
    } 
    fclose($handle); 

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
        Header("Content-Disposition: attachment; filename=dvcode_all_result_".date("Ymd").".xls");
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
        <th>고유번호</th>
        <th>주문코드</th>
        <th>입금일</th>
        <th>상품명</th>
        <th>상품코드</th>
        <th>창고품목코드</th>
        <th>품번</th>
        <th>색상</th>
        <th>사이즈</th>
        <th>결제수단</th>
        <th>판매가</th>
        <th>수량</th>
        <th>수령자</th>
        <th>우편번호</th>
        <th>주소</th>
        <th>전화번호</th>
        <th>비상전화</th>
        <th>비고</th>
        <th>주문자</th>
        <th>주문자ID</th>
        <th>주문자우편번호</th>
        <th>주문자주소</th>
        <th>주문자전화번호</th>
        <th>주문자핸드폰</th>
        <th>배송업체명</th>
        <th>송장번호</th>
        <th>O2O구분</th>
        <th>매장번호</th>
        <th>우선창고코드</th>
        <th>재고</th>
        <th>A1801</th>
        <th>A1770</th>
        <th>A1771</th>
        <th>결과</th>
    </tr>
<?php 
        foreach ( $arrResultMsg as $arrData ) {
            echo "<tr>";
            foreach ( $arrData as $data ) {
                echo "<td>".iconv('EUC-KR', 'UTF-8', "'".$data."'")."</td>";
                //echo "<td>{$data}</td>";
            }
            echo "</tr>";
        }
?>
</table>
</body>
</html>

<?php
    }
}
?>
