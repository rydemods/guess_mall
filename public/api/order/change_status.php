<?php
include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$resultTotArr = array();
$resultArr = array();

$code = 0;
$message = "success";
$exe_id		= "||api";	// 실행자 아이디|이름|타입

$arrOrder           = $_POST["orders"];
$json_arrOrder=json_encode($arrOrder);
$Encrypt = new Sync();
$Encrypt->log( "[Synccommerce_jsonPostData] {$json_arrOrder}\r\n" );

BeginTrans();
$bSuccess = true;

if ( empty($arrOrder) || count($arrOrder) == 0 ) {
    $code       = 1;
    $message    = "주문정보가 없습니다.";
} else {

    try {

        for ( $i = 0; $i < count($arrOrder); $i++ ) {
            $ordercode              = $arrOrder[$i]["ordercode"];
            $op_idx                 = $arrOrder[$i]["op_idx"];
            $shipping_com_code      = $arrOrder[$i]["shipping_com_code"];
            $invoice_number         = $arrOrder[$i]["invoice_number"];
            $store_code         = $arrOrder[$i]["store_code"];
            $status                 = $arrOrder[$i]["status"];                      // 0 : 배송준비중, 1 : 배송중, 2 : 배송완료, 3 : 주문취소, 4 : 반품완료
            $cancel_msg             = $arrOrder[$i]["cancel_msg"];                  // 취소 사유

            if ( empty($ordercode) ) {
                $code       = 1;
                $message    = "주문코드가 없습니다.";
            } elseif ( empty($op_idx) ) {
                $code       = 1;
                $message    = "주문 내 품목인덱스가 없습니다.";
            } else if ( $status == "" ) {
                $code       = 1;
                $message    = "주문상태코드가 없습니다.";
            } else {
                $arrOrderField = array(
                    "delivery_type",
                    "store_code",
                    "quantity",
                    "date"
                );

                $sql  = "SELECT ";
                $sql .= "   a.delivery_type, a.store_code, a.quantity, a.date ";
                $sql .= "FROM ";
                $sql .= "   tblorderproduct a left join tblproduct b on a.productcode = b.productcode ";
                $sql .= "WHERE ";
                $sql .= "   a.ordercode = '{$ordercode}' AND a.idx = {$op_idx} ";
                $result = pmysql_query($sql);
                $op_cnt = 0;

                $arrOrderData = array();
                while ( $row = pmysql_fetch_array($result) ) {
                    foreach ( $arrOrderField as $fieldName ) {
                        $arrOrderData[$fieldName] = $row[$fieldName];
                    }
                    $op_cnt++;
                }
                pmysql_free_result($result);

                if ( $op_cnt == 0 ) {
                    $code       = 1;
                    $message    = "해당 주문내역이 없습니다.";
                } else {
                    if ( $status == 0 ) {
                        // 배송준비중

                        orderProductStepUpdate($exe_id, $ordercode, $op_idx, '2');

                        //현재 주문의 상태값을 가져온다.
                        list($old_step1, $old_step2)=pmysql_fetch_array(pmysql_query("select oi_step1, oi_step2 from tblorderinfo WHERE ordercode='".trim($ordercode)."'"));
                        if ($old_step1 == '1' && $old_step2 == '0') {
                            //주문을 배송 준비중으로 변경한다.
                            $sql2 = "UPDATE tblorderinfo SET oi_step1 = '2', oi_step2 = '0', deli_gbn='S' WHERE ordercode='".$ordercode."'";
                            pmysql_query($sql2,get_db_conn());
                            // 상점코드를 싱크에서 넘겨준다
                            // 입찰기능이 생기면 store_code를 update 시키는 부분을 제거해야 한다 2016-10-13 유동혁
                            //$sql3 = "UPDATE tblorderproduct SET store_code = '".$store_code."' WHERE idx = '".$op_idx."' ";
                            //pmysql_query($sql3,get_db_conn());
                        }

                    } elseif ( $status == 1 ) {
                        // 배송중
                        // 배송업체코드와 송장번호는 O2O의 경우 없을 수도 있음(단, 매장발송인 경우는 있어야 함)

                        $delimailok = "Y";	                //배송완료에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
                        $deli_com   = $shipping_com_code;
                        $deli_num   = $invoice_number;
                        $idx        = $op_idx;
                        $type       = "delivery";
                        

                        $sql  = "SELECT company_name FROM tbldelicompany WHERE code = '{$deli_com}'";
                        list($deli_name)  = pmysql_fetch_array(pmysql_query($sql));

                        $sql = "select  a.ordercode, b.deli_gbn, a.paymethod, a.sender_tel, a.sender_name, 
                                        b.price, b.quantity, b.coupon_price, b.use_point, b.deli_price, b.store_code,
                                        (b.price*b.quantity)-b.coupon_price-b.use_point-b.use_epoint+b.deli_price as act_price	
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
                                                    $resultTotArr["result"]    = $resultArr;
                                                    $resultTotArr["code"]      = 1;
                                                    $resultTotArr["message"]   = $errmsg;
                                                    echo json_encode($resultTotArr);

                                                    exit;
                                                }
                                            } else {
                                                $tempdata=explode("|",$delivery_data);
                                                if(ord($tempdata[1])) $errmsg=$tempdata[1];
                                                if(ord($errmsg)) {
                                                    //echo "<script> alert('{$errmsg}');</script>";
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
                                                    $resultTotArr["result"]    = $resultArr;
                                                    $resultTotArr["code"]      = 1;
                                                    $resultTotArr["message"]   = $errmsg;
                                                    echo json_encode($resultTotArr);

                                                    exit;
                                                }
                                            } else {
                                                $tempdata=explode("|",$delivery_data);
                                                if(ord($tempdata[1])) $errmsg=$tempdata[1];
                                                if(ord($errmsg)) {
                                                    //echo "<script> alert('{$errmsg}');</script>";
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
                                                    $resultTotArr["result"]    = $resultArr;
                                                    $resultTotArr["code"]      = 1;
                                                    $resultTotArr["message"]   = $errmsg;
                                                    echo json_encode($resultTotArr);

                                                    exit;
                                                }
                                            } else {
                                                $tempdata=explode("|",$delivery_data);
                                                if(ord($tempdata[1])) $errmsg=$tempdata[1];
                                                if(ord($errmsg)) {
                                                    //echo "<script> alert('{$errmsg}');</script>";
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
                                                    $resultTotArr["result"]    = $resultArr;
                                                    $resultTotArr["code"]      = 1;
                                                    $resultTotArr["message"]   = $errmsg;
                                                    echo json_encode($resultTotArr);

                                                    exit;
                                                }
                                            } else {
                                                $tempdata=explode("|",$delivery_data);
                                                if(ord($tempdata[1])) $errmsg=$tempdata[1];
                                                if(ord($errmsg)) {
                                                    //echo "<script> alert('{$errmsg}');</script>";
                                                }
                                            }
                                        } else if($pg_type=="G") {	//NICE
                                            //$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);
                                            $query="sitecd={$pgid_info["ID"]}&sitekey=".urlencode($pgid_info["KEY"])."&ordercode={$ordercode}&deli_num={$deli_num}&deli_name=".urlencode($deli_name);

                                            $delivery_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$_ord->paymethod[1]."/delivery.php",$query);

                                            $delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
                                            if (substr($delivery_data,0,2)!="OK") {
                                                $tempdata=explode("|",$delivery_data);
                                                $errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
                                                if(ord($tempdata[1])) $errmsg=$tempdata[1];
                                                if(ord($errmsg)) {
                                                    //$resultTotArr["result"]    = $resultArr;
                                                    //$resultTotArr["code"]      = 1;
                                                    //$resultTotArr["message"]   = $errmsg;
                                                    $code       = 1;
                                                    $message    = $errmsg;
                                                }
                                            } else {
                                                $tempdata=explode("|",$delivery_data);
                                                if(ord($tempdata[1])) $errmsg=$tempdata[1];
                                                if(ord($errmsg)) {
                                                    //echo "<script> alert('{$errmsg}');</script>";
                                                }
                                            }
                                        }
                                    }
                                }

                                if($code == "1") {
                                    throw new Exception($message, $code);
                                }

                                $deliQry = "";
                                if( strlen( $deli_num ) > 0 && strlen( $deli_com ) > 0 ){
                                    $deliQry = ", deli_com = '".$deli_com."', deli_num = '".$deli_num."' ";
                                }

                                $sql = "UPDATE tblorderproduct SET deli_gbn='Y', deli_date='".date("YmdHis")."' ".$deliQry;
                                $sql.= "WHERE ordercode='{$ordercode}' ";
                                $sql.= "AND idx = {$idx} ";
                                $sql.= "AND op_step < 40 ";

								
								$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/sync_status_'.date("Ym").'/';

								$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
								$outText.= " ordercode     : ".$ordercode."\n";
								$outText.= " product_sql     : ".$sql."\n";
						
								

                                if(pmysql_query($sql,get_db_conn())) {

                                    // 신규상태 변경 추가 - (2016.04.15 - 김재수 추가)
                                    orderProductStepUpdate($exe_id, $ordercode, $idx, '3'); // 배송중
									
                                    $sql = "UPDATE  tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
                                    $sql.= "WHERE   ordercode='{$ordercode}' ";
                                    pmysql_query($sql,get_db_conn());
									$outText.= " info_sql     : ".$sql."\n";
                                    // 신규상태 변경 추가
                                    orderStepUpdate($exe_id, $ordercode, '3', '0' ); // 배송중
									$outText.= " step_op     : ok\n";
									//erp전송
									$dstore_code=$_ord->store_code;
									$dstoreArr_code[$i]=$_ord->store_code; // 20170818  ERP 전송을 위해 배열 and 아래 ERP 전송 왜 주석 처리한지 모르겠음
									//sendErpDeliveryInfo($ordercode, $idx, $deli_com, $deli_num, $_ord->store_code);
									$outText.= " erp     : ok\n";
                                    // 상점코드를 싱크에서 넘겨준다
                                    // 입찰기능이 생기면 store_code를 update 시키는 부분을 제거해야 한다 2016-10-13 유동혁
                                    //$sql3 = "UPDATE tblorderproduct SET store_code = '".$store_code."' WHERE idx = '".$idx."' ";
                                    //pmysql_query($sql3,get_db_conn());
                                }

								$outText.= "\n";
								$outText.= '========================='.date("Y-m-d H:i:s")."=============================\n";
								if(!is_dir($textDir)){
									mkdir($textDir, 0700);
									chmod($textDir, 0777);
								}
								$upQrt_f = fopen($textDir.'sync_step3_'.date("Ymd").'.txt','a');
								fwrite($upQrt_f, $outText );
								fclose($upQrt_f);
								chmod($textDir."sync_step3_".date("Ymd").".txt",0777);


                                $isupdate=true;

                                if($delimailok=="Y") {	//배송완료 메일을 발송할 경우
                                    $delimailtype="N";
                                    //SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype, $idx);
                                    SendDeliMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, $ordercode, $deli_com, $deli_num, $delimailtype, $idx);
                                    //$log_text = $_data->shopname." / ".$shopurl." / ".$_data->design_mail." / ".$_data->info_email." / ".$ordercode." / ".$deli_com." / ".$deli_num." / ".$delimailtype." / ".$idx;
                                    //IFLog($log_text, "deli_check");

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
                                    }
                                }
                            } elseif(!strstr("NXS",$_ord->deli_gbn)) {
                                
                                $code       = 1;
                                $message    = "이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.";
                                
                                ;   // do nothing
                            }
                        }
                    } elseif ( $status == 2 ) {
                        // 배송완료

                        $sql = "SELECT reserve,deli_date FROM tblorderproduct WHERE idx = {$op_idx} ";
                        list($reserve,$deli_date) = pmysql_fetch_array(pmysql_query($sql));

                        $qryErr = 0;
                        $textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/deli_finish_logs_'.date("Ym").'/';
                        $outText = '========================='.date("Y-m-d H:i:s")."=============================\n";

                        $idxs ="";
                        $idxs_cnt	= 0;

                        $idx=$deli_com=$deli_num="";

                        $idx = $op_idx;
                        $deli_reserve = $reserve;

                        if(strlen($idx) > 0) {
                            if ($deli_reserve !='') {
                                if(!$deli_date){
                                    $deli_query=",deli_date='".date("YmdHis")."' ";
                                }
                                $sql = "UPDATE tblorderproduct SET deli_gbn='F',order_conf = '1',order_conf_date='".date('YmdHis')."' ".$deli_query;
                                $sql.= "WHERE ordercode='{$ordercode}' AND idx='{$idx}' ";
                                //$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
                                $sql.= "AND op_step < 40 ";
                                //echo $sql."<br>";
                                pmysql_query($sql,get_db_conn());
                                if( !pmysql_error() ){

                                    // 신규상태 변경 추가 - (2016.02.18 - 김재수 추가)
                                    orderProductStepUpdate($exe_id, $ordercode, $idx, '4', '', '', '', '', '', '', 'S'); // 배송완료

                                    #배송정보 변경 LOG
                                    $outText.= " 주문 번호     : ".$ordercode."\n";
                                    $outText.= " 상품 IDX     : ".$idx."\n";
                                    $outText.= " 송장 번호     : ".$deli_num."\n";
                                    $outText.= " 배송회사 코드 : ".$deli_com."\n";
                                    $outText.= " 배송상태 변경 : 배송완료\n";
                                    if ($idxs_cnt > 0) $idxs .= "|";
                                    $idxs		.= $idx;
                                    $idxs_cnt++;
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

                        if( $qryErr == 0 ){
                            //주문중 배송완료, 취소완료상태가 아닌경우
                            list($op_idx_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $idxs)."') AND (op_step != '4' AND op_step != '44')"));
                            //echo $op_idx_cnt;
                            if ($op_idx_cnt == 0) {
                                $sql = "UPDATE tblorderinfo SET deli_gbn = 'F',order_conf = '1',order_conf_date='".date('YmdHis')."' ";
                                $sql.= "WHERE ordercode='{$ordercode}' ";
                                //echo $sql."<br>";
                                pmysql_query($sql,get_db_conn());
                            }
							//배송완료시 erp로 전송
							//sendErpOrderEndInfo($ordercode, $idx);
                        }
                        //exit;

                        

                        $code = 0;
                        $message = "success";
						$outText.= "massage :" .$message."\n";
						$outText.= "\n";
						$outText.= '========================='.date("Y-m-d H:i:s")."=============================\n";
                        if(!is_dir($textDir)){
                            mkdir($textDir, 0700);
                            chmod($textDir, 0777);
                        }
                        $upQrt_f = fopen($textDir.'deli_finish_'.date("Ymd").'.txt','a');
                        fwrite($upQrt_f, $outText );
                        fclose($upQrt_f);
                        chmod($textDir."deli_finish_".date("Ymd").".txt",0777);

                    } elseif ( $status == 3 ) {
                        list($oi_step1,$oi_step2,$paymethod,$del_gbn)=pmysql_fetch_array(pmysql_query("select oi_step1,oi_step2,paymethod,del_gbn from tblorderinfo WHERE ordercode='".trim($ordercode)."'"));
                        if($oi_step1 == '3' || $oi_step1 == '4'){
                            $c_re_type='B';
                        }else{
                            $c_re_type='';
                        }

                        $updates=orderCancel($exe_id, $ordercode, $op_idx, $oi_step1, $oi_step2, $oi_step1, $paymethod[0], '10', 'Synccommerce', '6', '111', '1', $c_re_type);
                        $deliupdate = " deli_gbn='E' ";
                        $up_deli_gbn="E";

                        list($op_dg_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_dg_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $op_idx)."') AND deli_gbn != '{$up_deli_gbn}'"));

                        if($del_gbn=="Y") $okdel="R";
                        else $okdel="A";

                        $sql = "UPDATE tblorderinfo SET {$deliupdate} ";
                        $sql.= "WHERE ordercode='{$ordercode}' ";
                        pmysql_query($sql,get_db_conn());


                        $sql = "UPDATE tblorderproduct SET deli_gbn='{$up_deli_gbn}' ";
                        $sql.= "WHERE ordercode='{$ordercode}' AND idx IN ('".str_replace("|", "','", $op_idx)."') ";
                        //$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
                        pmysql_query($sql,get_db_conn());

                        $isupdate=true;
                        
                        // 싱크커머스 취소 시 알림톡
                        $alim = new ALIM_TALK();
                        $alim->makeAlimTalkSearchData($ordercode, 'WEB11', $op_idx, $oc_no);

                    } elseif ( $status == 4 ) {
                        // 반품완료
                        $sql  = "INSERT INTO tblorder_cancel ";
                        $sql .= "( ordercode, idx, status, reg_date, source ) VALUES ";
                        $sql .= "( '{$ordercode}', '{$op_idx}', '2', now(), '1' ) ";

                        $result = pmysql_query($sql, get_db_conn());
                        if($err=pmysql_error()) {
                            $code       = 1;
                            $message    = "반품완료가 실패했습니다.";
                        }
                    } elseif( $status == 6 ){
                        //선택매장정보(매장발송)
						/*
                        $sql = "UPDATE tblorderproduct SET store_code='{$store_code}'";
                        $sql.= "WHERE ordercode='{$ordercode}' ";
                        $sql.= "AND idx = {$op_idx} ";
                        $rtn=pmysql_query($sql,get_db_conn());
*/
                        // 매장변경정보 ERP로 전송..2016-12-20
                        sendErpChangeShop($ordercode, $op_idx, $store_code);

                    } elseif( $status == 7 ){ // 재고부족 추가 2016-10-13 유동혁
                        // 재고부족
                        // 입찰기능이 생기면 store_code를 update 시키는 부분을 제거해야 한다 2016-10-13 유동혁
                        $sql = "
                            UPDATE
                                tblorderproduct 
                            SET
                                store_code = '".$store_code."',
                                store_stock_yn = 'N'
                            WHERE
                                ordercode = '".$ordercode."'
                            AND
                                idx = '".$op_idx."'
                        ";
                        $rtn = pmysql_query( $sql, get_db_conn() );
                        if( $err = pmysql_error() ) {
                            $code       = 1;
                            $message    = "재고없음 처리가 실패했습니다.";
                        }
                    } elseif( $status == 8 ){ // 송장번호 변경
                        $deli_com   = $shipping_com_code;
                        $deli_num   = $invoice_number;
                        $idx        = $op_idx;
						if($deli_com && $deli_num){
							$sql="select * from tblorderproduct where ordercode='".$ordercode."' and idx='".$idx."' and op_step='3'";
							$result=pmysql_query($sql);
							$rownum=pmysql_num_rows($result);
							if($rownum){
								pmysql_query("update tblorderproduct set deli_com = '".$deli_com."', deli_num = '".$deli_num."' where ordercode='".$ordercode."' and idx='".$idx."' and op_step='3'");
							}else{
								$code       = 1;
								$message    = "주문이없거나 배송중상태가아닙니다.";
							}
						}else{
							$code       = 1;
	                        $message    = "송장코드와 번호가 존재하지않습니다.";
						}
					} elseif( $status == 9 ){ // 매장->본사 발송 수정

						$idx        = $op_idx;

						$sql="select * from tblorderproduct where ordercode='".$ordercode."' and idx='".$idx."' ";
						$result=pmysql_query($sql);
						$rownum=pmysql_num_rows($result);

						if($rownum){

							$sql = "SELECT a.id,a.sender_name,b.op_step,b.store_code FROM tblorderinfo a join tblorderproduct b on a.ordercode = b.ordercode WHERE a.ordercode='{$ordercode}'";
							$result=pmysql_query($sql,get_db_conn());
							if($row=pmysql_fetch_object($result)) {
								$reg_id=$row->id;
								$reg_name=$row->sender_name;
								$op_step=$row->op_step;
								$old_store_code=$row->store_code;
							}
							pmysql_free_result($result);

							$n_date = date('YmdHis');
							
							/* 주문건이 2개 일경우도 있어서 제외
							$sql = "UPDATE tblorderinfo SET deli_date='' WHERE ordercode='{$ordercode}'"; // oi_step 1로 변경
							pmysql_query($sql,get_db_conn());
							if( $err = pmysql_error() ) {
								$message    = "본사창고 변환 처리가 실패했습니다.";
								$resultTotArr["result"]    = $resultArr;
								$resultTotArr["code"]      = 1;
								$resultTotArr["message"]   = $message;
								echo json_encode($resultTotArr);

								exit;
							}
							*/
							$sql = "UPDATE tblorderproduct SET deli_gbn='N', deli_com='', deli_num='', deli_date='', op_step=1, delivery_type='0', store_code='A1801B' WHERE ordercode='".$ordercode."' and idx='".$idx."'"; // 해당 주문 본사로 변경
							pmysql_query( $sql, get_db_conn() );
							if( $err = pmysql_error() ) {
								$message    = "본사창고 변환 처리가 실패했습니다1.0.";
								$resultTotArr["result"]    = $resultArr;
								$resultTotArr["code"]      = 1;
								$resultTotArr["message"]   = $message;
								echo json_encode($resultTotArr);

								exit;
							}

							// 							$sql = "insert into tblorder_log (
							$sql = "insert into tblorderproduct_log (
											ordercode,
											idx,
											step_prev,
											step_next,
											memo,regdt,
											reg_id,
											reg_name,
											reg_type
											) 
											values (
											'".$ordercode."',
											'".$idx."',
											'".$op_step."', 
											'1',
											'매장발송->본사발송',
											'".$n_date."',
											'master',
											'수동변경',
											'api'
										)";
							$result = pmysql_query($sql, get_db_conn());
							if($err=pmysql_error()) {
								$message    = "본사창고 변환 처리가 실패했습니다1.1.";
								$resultTotArr["result"]    = $resultArr;
								$resultTotArr["code"]      = 1;
								$resultTotArr["message"]   = $message;
								echo json_encode($resultTotArr);

								exit;
							}

							$sql = "insert into tblorderproduct_store_code (ordercode,idx,store_code,old_store_code,regdt) values ('".$ordercode."','".$idx."','A1801B','".$old_store_code."','".$n_date."')";
							$result = pmysql_query($sql, get_db_conn());
							if( $err = pmysql_error() ) {
								$message    = "본사창고 변환 처리가 실패했습니다.1.2";
								$resultTotArr["result"]    = $resultArr;
								$resultTotArr["code"]      = 1;
								$resultTotArr["message"]   = $message;
								echo json_encode($resultTotArr);

								exit;
							}
							
							$sql = "insert into tblorderproduct_store_change (ordercode,idx,regdt) values ('".$ordercode."','".$idx."','".$n_date."')";
							$result = pmysql_query($sql, get_db_conn());
							if( $err = pmysql_error() ) {
								$message    = "본사창고 변환 처리가 실패했습니다.1.3";
								$resultTotArr["result"]    = $resultArr;
								$resultTotArr["code"]      = 1;
								$resultTotArr["message"]   = $message;
								echo json_encode($resultTotArr);

								exit;
							}

							$sql = "insert into tblorder_status_log (
							ordercode,
							idx,
							status_code,
							step_code,
							memo,
							reg_id,
							reg_name,
							reg_type,
							regdt,
							proc_type
							) values (
							'".$ordercode."',
							'".$idx."',
							'order',
							'1',
							'본사배송',
							'master',
							'수동변경',
							'api',
							'".$n_date."',
							'CS'
							)";
							$result = pmysql_query($sql, get_db_conn());

							if($err=pmysql_error()) {
								$message    = "본사창고 변환 처리가 실패했습니다.5";
								$resultTotArr["result"]    = $resultArr;
								$resultTotArr["code"]      = 1;
								$resultTotArr["message"]   = $message;
								echo json_encode($resultTotArr);

								exit;
							}

						}else{
								$code       = 1;
								$message    = "주문이없거나 배송중상태가아닙니다.";
						}
                    } else {
                        $code       = 1;
                        $message    = "주문상태코드가 유효하지 않습니다.";
                    }
                }
            }

            if ( $code == 1 ) { break; }
        }

    } catch (Exception $e) {
        $bSuccess = false;
        RollbackTrans();
    }
}

// 2017-08-09  14시25분 수정
$orderErpcheck =  getErpcheckinfo($ordercode,$op_idx);

if (!$orderErpcheck) {
		$code       = 1;
		$message    = "주문 생성중입니다. 10초뒤 다시 시도해주세요.";
		$m_str = $ordercode."-".$op_idx;
		api_log($m_str);
}

if ( $bSuccess && $code == 0 ) {
    CommitTrans();
} else {
    $bSuccess = false;
    RollbackTrans();
}

$resultTotArr["result"]    = $resultArr;
$resultTotArr["code"]      = $code;
$resultTotArr["message"]   = $message;

echo json_encode($resultTotArr);
//완료가되면 erp로 통신
if($message=="success"){
	if ( $status == 1 ) {
		// 픽업주문시 다량 발생인데 1건만 찍히는 오류 처리 20170818 14:00
		if(count($arrOrder) > 1){
	        for ( $j = 0; $j < count($arrOrder); $j++ ) {
				$ordercode		=	$arrOrder[$j]["ordercode"];
				$idx					=	$arrOrder[$j]["op_idx"];
				$deli_com			=	$arrOrder[$j]["shipping_com_code"];
				$deli_num			=	$arrOrder[$j]["invoice_number"];
				$dstore_code	=	$dstoreArr_code[$j];
				sendErpDeliveryInfo($ordercode, $idx, $deli_com, $deli_num, $dstore_code);
			}
		}else{
			sendErpDeliveryInfo($ordercode, $idx, $deli_com, $deli_num, $dstore_code);
		}
//		sendErpDeliveryInfo($ordercode, $idx, $deli_com, $deli_num, $dstore_code);
	} elseif ( $status == 2 ) {
		sendErpOrderEndInfo($ordercode, $idx);
	} elseif ( $status == 9 ){
		//ERP 전송
		sendErporderShopReChange($ordercode, $idx, '0');
	}
}

?>
