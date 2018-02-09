<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	
	$localXmlDir = "http://".$_SERVER[SERVER_NAME]."/linkerXml";

	function orderInfo($v){
		debug("사방넷 주문번호 : ".(string)$v->idx);
		debug("쇼핑몰 주문번호 : ".(string)$v->order_id);
		debug("쇼핑몰명 : ".(string)$v->mall_id);
		debug("쇼핑몰 아이디 : ".(string)$v->mall_user_id);
		debug("배송상태 : ".(string)$v->order_status);
		debug("주문자 아이디 : ".(string)$v->user_id);
		debug("주문자명 : ".mb_convert_encoding((string)$v->user_name, 'EUC-KR', 'UTF-8'));
		debug("주문자 전화번호 : ".(string)$v->user_tel);
		debug("주문자 핸드폰번호 : ".(string)$v->user_cel);
		debug("주문자 이메일 : ".(string)$v->user_email);
		debug("수취인전화번호 : ".(string)$v->receive_tel);
		debug("수취인핸드폰번호 : ".(string)$v->receive_cel);
		debug("수취인이멜주소 : ".(string)$v->receive_email);
		debug("배송매세지 : ".mb_convert_encoding((string)$v->delv_msg, 'EUC-KR', 'UTF-8'));
		debug("수취인명 : ".mb_convert_encoding((string)$v->receive_name, 'EUC-KR', 'UTF-8')     );
		debug("수취 우편번호 : ".(string)$v->receive_zipcode);
		debug("수취주소 : ".mb_convert_encoding((string)$v->receive_addr, 'EUC-KR', 'UTF-8') );
		debug("주문총금액 : ".(string)$v->total_cost);
		debug("주문총결제금액 : ".(string)$v->pay_cost);
		debug("주문일자 : ".(string)$v->order_date);
		debug("매입처명 : ".(string)$v->partner_id);
		debug("물류처명 : ".(string)$v->dpartner_id);
		debug("쇼핑몰상품코드 : ".(string)$v->mall_product_id);
		debug("사방넷상품코드 : ".(string)$v->product_id);
		debug("사방넷단품코드 : ".(string)$v->sku_id);
		debug("확정데이터 상품명 : ".mb_convert_encoding((string)$v->p_product_name, 'EUC-KR', 'UTF-8') );
		debug("확정데이터옵션명 : ".mb_convert_encoding((string)$v->sku, 'EUC-KR', 'UTF-8') );
		debug("쇼핑몰주문상품명 : ".mb_convert_encoding((string)$v->product_name, 'EUC-KR', 'UTF-8') );
		debug("판매단가 : ".(string)$v->sale_cost);
		debug("쇼핑몰납품단가 : ".(string)$v->mall_won_cost);
		debug("매입단가 : ".(string)$v->won_cost);
		debug("쇼핑몰주문옵션명 : ".mb_convert_encoding((string)$v->sku_value, 'EUC-KR', 'UTF-8'));
		debug("주문수량 : ".(string)$v->sale_cnt);
		debug("쇼핑몰 주문 배송정보 : ".mb_convert_encoding((string)$v->delivery_method_str, 'EUC-KR', 'UTF-8'));
		debug("주문 배송비 : ".(string)$v->delv_cost);
		debug("고객사 상품코드 : ".(string)$v->compayny_goods_cd);
		debug("옵션별칭 : ".(string)$v->sku_alias);
		debug("묶음 갯수 : ".(string)$v->box_ea);
		debug("매출정산확인여부 : ".(string)$v->jung_chk_yn);
		debug("주문순번 : ".(string)$v->mall_order_seq);
		debug("부주문번호 : ".(string)$v->mall_order_id);
		debug("수정된 수집옵션명 : ".(string)$v->etc_field3);
		debug("주문구분 : ".(string)$v->order_gubun);
		debug("확정EA : ".(string)$v->p_ea);
		debug("주문 수집일시 : ".(string)$v->reg_date);
		debug("자사몰필드1 : ".(string)$v->order_etc_1);
		debug("자사몰필드2 : ".(string)$v->order_etc_2);
		debug("자사몰필드3 : ".(string)$v->order_etc_3);
		debug("자사몰필드4 : ".(string)$v->order_etc_4);
		debug("자사몰필드5 : ".(string)$v->order_etc_5);
		debug("자사몰필드6 : ".(string)$v->order_etc_6);
		debug("자사몰필드7 : ".(string)$v->order_etc_7);
		debug("세트분리주문구분 : ".(string)$v->ord_field2);
		debug("원주문번호 : ".(string)$v->copy_idx);
		debug(" =========================================================================================================== ");
		debug(" =========================================================================================================== ");
	}


	/*	
	ALTER TABLE tblproduct ADD shoplinker_flag character(1) NOT NULL DEFAULT 'N'::bpchar;
	COMMENT ON COLUMN tblproduct.shoplinker_flag IS '샵링커 연동 상품 유무';
	ALTER TABLE tblproduct ADD shoplinker_product_id character varying(20) NOT NULL DEFAULT ''::character varying;
	COMMENT ON COLUMN tblproduct.shoplinker_product_id IS '샵링커 연동 상품 번호';


	ALTER TABLE tblorderinfo ADD shoplinker_order_id character varying(20) NOT NULL DEFAULT ''::character varying;
	COMMENT ON COLUMN tblorderinfo.shoplinker_order_id IS '샵링커 주문번호';
	ALTER TABLE tblorderinfo ADD shoplinker_mall_order_id character varying(40) NOT NULL DEFAULT ''::character varying;
	COMMENT ON COLUMN tblorderinfo.shoplinker_mall_order_id IS '샵링커 제휴몰 주문 번호';
	ALTER TABLE tblorderinfo ADD shoplinker_mall_name character varying(40) NOT NULL DEFAULT ''::character varying;
	COMMENT ON COLUMN tblorderinfo.shoplinker_mall_name IS '샵링커 제휴몰 이름';

	ALTER TABLE tblorderinfo ADD shoplinker_send_flag character(1) NOT NULL DEFAULT 'N'::bpchar;
	COMMENT ON COLUMN tblorderinfo.shoplinker_send_flag IS '샵링커에 송장 전송 유무';
	ALTER TABLE tblorderinfo ADD shoplinker_market_send_flag character(1) NOT NULL DEFAULT 'N'::bpchar;
	COMMENT ON COLUMN tblorderinfo.shoplinker_market_send_flag IS '오픈마켓에 송장 전송 유무';
	*/





	switch ($_POST["mode"]){
		case "linkerProductRegister":
			# 상품 코드를 serialize하여 XML생성 php 파일에 전송. unserialize 하여 사용

			$resultMsg = "";
			$resultCount = $resultFailCount = $resultSuccCount = 0;

			$arrayProductCode = $_POST['linker_val'];
			$serialized_ArrayProductCode = serialize($arrayProductCode);

			$arrProductCode = unserialize(str_replace("\\", "", $serialized_ArrayProductCode));
			foreach($arrProductCode as $codeKey => $codeVal){
				$linkerCallURL = "http://apiweb.shoplinker.co.kr/ShoplinkerApi/Product/xmlInsert.php";
				$iteminfoURL = urlencode($localXmlDir."/shoplinkerReg.php?pd_code=".$codeVal);
				$insertGoodsData = simplexml_load_file($linkerCallURL."?iteminfo_url=".$iteminfoURL);
				#debug($localXmlDir."/shoplinkerReg.php?pd_code=002002001000000001");
				if($insertGoodsData->result == "true"){
					pmysql_query("UPDATE tblproduct SET shoplinker_flag = 'Y', shoplinker_product_id = '".$insertGoodsData->product_id."' WHERE productcode = '".$codeVal."'", get_db_conn());
					$resultMsg .= "<tr><td style = 'text-align:left;padding-left:10px;'>".mb_convert_encoding((string)$insertGoodsData->message, 'EUC-KR', 'UTF-8')." : ".$codeVal." ( ".$insertGoodsData->product_id." )</td></tr>";
					$resultSuccCount++;
				}else{
					pmysql_query("UPDATE tblproduct SET shoplinker_flag = 'N' WHERE productcode = '".$codeVal."'", get_db_conn());
					$resultMsg .= "<tr><td style = 'text-align:left;padding-left:10px;'>상품연동 실패 : ".$codeVal."</td></tr>";
					$resultMsg .= "<tr><td style = 'text-align:left;padding-left:10px;'>&nbsp;&nbsp;- 실패 이유 : &nbsp;".mb_convert_encoding((string)$insertGoodsData->message, 'EUC-KR', 'UTF-8')."</td></tr>";					
					$resultFailCount++;
				}
				$resultCount++;
			}
			echo "<table width = '100%' align = 'center'>";
			echo "	<tr>";
			echo "		<td align = 'center'>";
			echo				mb_convert_encoding("총 ".number_format($resultCount)."건 (성공 : ".number_format($resultSuccCount)."건, 실패 : ".number_format($resultFailCount)."건)", 'UTF-8', 'EUC-KR');
			echo "		</td>";
			echo "	</tr>";
			echo		mb_convert_encoding($resultMsg, 'UTF-8', 'EUC-KR');
			echo "</table>";
			#echo mb_convert_encoding("<div>총 ".number_format($resultCount)."건 (성공 : ".number_format($resultSuccCount)."건, 실패 : ".number_format($resultFailCount)."건)</div>".$resultMsg, 'UTF-8', 'EUC-KR');
			exit;
		break;











		case "linkerProductModify":
			# 상품 코드를 serialize하여 XML생성 php 파일에 전송. unserialize 하여 사용.

			$resultMsg = "";
			$resultCount = $resultFailCount = $resultSuccCount = 0;

			$arrayProductCode = $_POST['linker_val'];
			$serialized_ArrayProductCode = serialize($arrayProductCode);

			$arrProductCode = unserialize(str_replace("\\", "", $serialized_ArrayProductCode));

			foreach($arrProductCode as $codeKey => $codeVal){
				$linkerCallURL = "http://apiweb.shoplinker.co.kr/ShoplinkerApi/Product/Shopmall_soldout.html";
				$iteminfoURL = urlencode($localXmlDir."/shoplinkerModify.php?pd_code=".$codeVal);
				$insertGoodsData = simplexml_load_file($linkerCallURL."?iteminfo_url=".$iteminfoURL);
				if($insertGoodsData->ResultMessage->result == "true"){
					pmysql_query("UPDATE tblproduct SET shoplinker_flag = 'Y', shoplinker_product_id = '".$insertGoodsData->ResultMessage->product_id."' WHERE productcode = '".$codeVal."'", get_db_conn());
					$resultMsg .= "<tr><td style = 'text-align:left;padding-left:10px;'>".mb_convert_encoding((string)$insertGoodsData->ResultMessage->message, 'EUC-KR', 'UTF-8')." : ".$codeVal." ( ".$insertGoodsData->ResultMessage->product_id." )</td></tr>";
					$resultSuccCount++;
				}else{
					pmysql_query("UPDATE tblproduct SET shoplinker_flag = 'R' WHERE productcode = '".$codeVal."'", get_db_conn());
					$resultMsg .= "<tr><td style = 'text-align:left;padding-left:10px;'>상품연동 실패 : ".$codeVal."</td></tr>";
					$resultMsg .= "<tr><td style = 'text-align:left;padding-left:10px;'>&nbsp;&nbsp;- 실패 이유 : &nbsp;".mb_convert_encoding((string)$insertGoodsData->ResultMessage->message, 'EUC-KR', 'UTF-8')."</td></tr>";					
					$resultFailCount++;
				}
				$resultCount++;
			}
			echo "<table width = '100%' align = 'center'>";
			echo "	<tr>";
			echo "		<td align = 'center'>";
			echo				mb_convert_encoding("총 ".number_format($resultCount)."건 (성공 : ".number_format($resultSuccCount)."건, 실패 : ".number_format($resultFailCount)."건)", 'UTF-8', 'EUC-KR');
			echo "		</td>";
			echo "	</tr>";
			echo		mb_convert_encoding($resultMsg, 'UTF-8', 'EUC-KR');
			echo "</table>";
			exit;
		break;














		case "linkerProductSoldout":

			$resultMsg = "";
			$resultCount = $resultFailCount = $resultSuccCount = 0;

			$arrayProductCode = $_POST['linker_val'];
			$serialized_ArrayProductCode = serialize($arrayProductCode);

			$arrProductCode = unserialize(str_replace("\\", "", $serialized_ArrayProductCode));
			foreach($arrProductCode as $codeKey => $codeVal){
				$linkerCallURL = "http://apiweb.shoplinker.co.kr/ShoplinkerApi/Product/Shopmall_soldout.html";
				$iteminfoURL = urlencode($localXmlDir."/shoplinkerModify.php?solodout=1&pd_code=".$codeVal);
				$insertGoodsData = simplexml_load_file($linkerCallURL."?iteminfo_url=".$iteminfoURL);
				#debug($localXmlDir."/shoplinkerReg.php?pd_code=002002001000000001");
				if($insertGoodsData->ResultMessage->result == "true"){
					pmysql_query("UPDATE tblproduct SET shoplinker_flag = 'Y', shoplinker_product_id = '".$insertGoodsData->ResultMessage->product_id."' WHERE productcode = '".$codeVal."'", get_db_conn());
					$resultMsg .= "<tr><td style = 'text-align:left;padding-left:10px;'>".mb_convert_encoding((string)$insertGoodsData->ResultMessage->message, 'EUC-KR', 'UTF-8')." : ".$codeVal." ( ".$insertGoodsData->ResultMessage->product_id." )</td></tr>";
					$resultSuccCount++;
				}else{
					pmysql_query("UPDATE tblproduct SET shoplinker_flag = 'R' WHERE productcode = '".$codeVal."'", get_db_conn());
					$resultMsg .= "<tr><td style = 'text-align:left;padding-left:10px;'>상품연동 실패 : ".$codeVal."</td></tr>";
					$resultMsg .= "<tr><td style = 'text-align:left;padding-left:10px;'>&nbsp;&nbsp;- 실패 이유 : &nbsp;".mb_convert_encoding((string)$insertGoodsData->ResultMessage->message, 'EUC-KR', 'UTF-8')."</td></tr>";					
					$resultFailCount++;
				}
				$resultCount++;
			}
			echo "<table width = '100%' align = 'center'>";
			echo "	<tr>";
			echo "		<td align = 'center'>";
			echo				mb_convert_encoding("총 ".number_format($resultCount)."건 (성공 : ".number_format($resultSuccCount)."건, 실패 : ".number_format($resultFailCount)."건)", 'UTF-8', 'EUC-KR');
			echo "		</td>";
			echo "	</tr>";
			echo		mb_convert_encoding($resultMsg, 'UTF-8', 'EUC-KR');
			echo "</table>";
			#echo mb_convert_encoding("<div>총 ".number_format($resultCount)."건 (성공 : ".number_format($resultSuccCount)."건, 실패 : ".number_format($resultFailCount)."건)</div>".$resultMsg, 'UTF-8', 'EUC-KR');
			exit;

		break;












		case "linkerOrderGet":
			$sdate = str_replace("-", "", $_POST['search_start']);
			$edate = str_replace("-", "", $_POST['search_end']);
			
			$linkerCallURL = "http://apiweb.shoplinker.co.kr/ShoplinkerApi/Order/orderlist.php";
			$iteminfoURL = urlencode($localXmlDir."/shoplinkerGetOrder.php?st_date=".$sdate."&ed_date=".$edate);
			$insertGoodsData = simplexml_load_file($linkerCallURL."?iteminfo_url=".$iteminfoURL);


			$arrOrderDatas = array();
			$orderGetCount = 0;
			$orderModifyCount = 0;
			$orderFailCount = 0;
			$orderGetInvoiceCount = 0;
			$returnData = "";

			if((string)$insertGoodsData->ResultMessage->result == 'true'){
				foreach($insertGoodsData->order as $k => $v){
					$arrReturnFlag = array();
					list($ordercode)=pmysql_fetch("SELECT ordercode FROM tblorderinfo WHERE shoplinker_order_id = '".(string)$v->shoplinker_order_id."'");
					if($ordercode){
						continue;
					}



					$ordercode = unique_id();
					$ordercode_front = substr((string)$v->orderdate, 0, 12);
					$ordercode_end = substr($ordercode, 12);
					$ordercode = $ordercode_front.$ordercode_end;

					$mall_nameEnc = mb_convert_encoding((string)$v->mall_name, 'EUC-KR', 'UTF-8');

					$tempkey = md5(uniqid(rand(),1));
					$address = "우편번호 : ".(string)$v->receive_zipcode."  주소 : ".mb_convert_encoding((string)$v->receive_addr, 'EUC-KR', 'UTF-8');
					$loc = substr(mb_convert_encoding((string)$v->receive_addr, 'EUC-KR', 'UTF-8'), 0, 4);

					list($sabangnet_set_flag)=pmysql_fetch("SELECT count(no) FROM tblproduct_sabangnet WHERE productcode = '".(string)$v->compayny_goods_cd."'");
					if($sabangnet_set_flag > 0){
						$data_set_flag = "Y";
					}else{
						$data_set_flag = "N";
					}


					$addInvoiceCol = $addInvoiceVal = "";
					/*
					if((string)$v->invoice_no){
						$addInvoiceCol = ", deli_num, deli_date, deli_com, deli_gbn";
						$addInvoiceVal = ", '".(string)$v->invoice_no."', '".date("YmdHis")."', '6', 'Y'";
					}
					*/

					$sql = "
									INSERT INTO tblorderinfo
										(
											tot_price_dc, ordercode, tempkey, id, price, 
											deli_price, paymethod, pay_data, sender_name, sender_email, 
											sender_tel, receiver_name, receiver_tel1, receiver_tel2, receiver_addr, 
											order_msg, ip, del_gbn, partner_id, loc, 
											bank_sender, receipt_yn, order_msg2, shoplinker_order_id, shoplinker_mall_order_id, 
											shoplinker_mall_name, pay_flag, bank_date ".$addInvoiceCol."
										) 
									VALUES 
										(
											'0', '".$ordercode."', '".$tempkey."', '', '".(string)$v->order_price."', 
											'".(string)$v->baesong_bi."', 'O', '', '".mb_convert_encoding((string)$v->order_name, 'EUC-KR', 'UTF-8')."', '".(string)$v->order_email."', 
											'".(string)$v->order_tel."', '".mb_convert_encoding((string)$v->receive, 'EUC-KR', 'UTF-8')."', '".(string)$v->receive_tel."', '".(string)$v->receive_cel."', '".$address."', 
											'[".$mall_nameEnc."]에서 생성된 주문\n".mb_convert_encoding((string)$v->delivery_msg, 'EUC-KR', 'UTF-8')."', '".$_SERVER['REMOTE_ADDR']."', 'N', '".$mall_nameEnc."', '".$loc."', 
											'', 'Y', '', '".(string)$v->shoplinker_order_id."', '".(string)$v->mall_order_id."', 
											'".$mall_nameEnc."', '0000', '".(string)$v->order_date."' ".$addInvoiceVal."
										)";
					#debug($sql);
					pmysql_query($sql, get_db_conn());
					if(pmysql_errno()!=1062) $pmysql_errno += pmysql_errno();

					$resultPd = pmysql_query("SELECT * FROM tblproduct WHERE productcode = '".(string)$v->partner_product_id."' ",get_db_conn());
					$_data = pmysql_fetch_object($resultPd);


					$skuEnc = mb_convert_encoding((string)$v->sku, 'EUC-KR', 'UTF-8');
					if($skuEnc){					
						if($mall_nameEnc == "(주)인터파크"){
							$arrSku1 = explode("-", $skuEnc);
							$arrSku2 = explode(" / ", $arrSku1[0]);
							$arrSku_value = explode("/", $arrSku2[1]);
						}else if($mall_nameEnc == "11번가"){
							$arrSku1 = explode("-", $skuEnc);
							$arrSku2 = explode(":", $arrSku1[0]);
							$arrSku_value = explode("/", $arrSku2[1]);
						}else{
							$arrSku1 = explode("-", $skuEnc);
							$arrSku2 = explode("/", $arrSku1[0]);
							$arrSku_value = explode(":", $arrSku2[0]);
						}

						#$arrSku_value = explode(":", $skuEnc);
					}else{
						$arrSku_value = "";
					}
					$tempOptName1 = $_data->option1;
					$tempOptName2 = $_data->option2;

					if($arrSku_value[0] && $arrSku_value[1]){
						$tok = explode(",", $tempOptName1);
						$option1 = $tok[0]." : ".$arrSku_value[0];
						$option1 = str_replace("'","", $option1);

						$tok2 = explode(",", $tempOptName2);
						$option2 = $tok2[0]." : ".$arrSku_value[1];
						$option2 = str_replace("'","", $option2);
					}else if($arrSku_value[0] && !$arrSku_value[1]){
						$tok = explode(",", $tempOptName1);
						$option1 = $tok[0]." : ".$arrSku_value[0];
						$option1 = str_replace("'","", $option1);

						$option2 = "";
					}else{
						$option1 = "";
						$option2 = "";
					}

					$priceProduct = (string)$v->order_price/(string)$v->quantity;
					if(!$priceProduct) $priceProduct = (string)$v->sale_price;


					$sql = "
									INSERT INTO tblorderproduct
										(
											vender, ordercode, tempkey, productcode, productname, 
											opt1_name, opt2_name, package_idx, assemble_idx, addcode, 
											quantity, price, reserve, date, selfcode, 
											productbisiness, order_prmsg, assemble_info, deli_num, deli_date
										) 
									VALUES
										(
											'0', '".$ordercode."', '".$tempkey."', '".(string)$v->partner_product_id."', '".mb_convert_encoding((string)$v->product_name, 'EUC-KR', 'UTF-8')."',
											'".$option1."', '".$option2."', '0', '0', '',
											'".(string)$v->quantity."', '".$priceProduct."', '0', '".substr((string)$v->orderdate, 0, 8)."', '', 
											'','','','', ''
										)";

					#debug($sql);
					pmysql_query($sql,get_db_conn());
					if(pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();
					if($pmysql_errno) $okmail="YES";

					$tempSabangQuantity = array();
					
					#orderInfo($v);
					if($okmail != "YES"){
						if(strlen($_data->option1)>0 && strlen($_data->option2)>0) {
							$arrOption1 = explode(",", $_data->option1);
							$arrOption2 = explode(",", $_data->option2);
							$arrQuantity = explode(",", $_data->option_quantity);

							for($j=0;$j<5;$j++){
								for($i=0;$i<10;$i++){
									if($arrOption1[$i] == $arrSku_value[0] && $arrOption2[$j] == $arrSku_value[1] && $arrQuantity[$j*10+$i] ){
										$tempSabangQuantity[] = $arrQuantity[$j*10+$i] - 1;
									}else{
										$tempSabangQuantity[] = $arrQuantity[$j*10+$i];
									}
								}
							}
							$newQuantity = implode(",", $tempSabangQuantity);
						}else if(strlen($_data->option1)>0 && !strlen($_data->option2)>0) {
							$arrOption1 = explode(",", $_data->option1);
							$arrOption2 = explode(",", $_data->option2);
							$arrQuantity = explode(",", $_data->option_quantity);
							for($j=0;$j<5;$j++){
								for($i=0;$i<10;$i++){
									if($arrOption1[$i] == $arrSku_value[0] && $arrQuantity[$j*10+$i]){
										$tempSabangQuantity[] = $arrQuantity[$j*10+$i] - 1;
									}else{
										$tempSabangQuantity[] = $arrQuantity[$j*10+$i];
									}
								}
							}
							$newQuantity = implode(",", $tempSabangQuantity);
						}else{
							$_dataoption_quantity = ",10";
							$arrQuantity = explode(",", $_data->option_quantity);
							$tempSabangQuantity[] = $arrQuantity[1] - (string)$v->quantity;
							$newQuantity = ",".implode(",", $tempSabangQuantity);
						}

						$sqlQuantity = "UPDATE tblproduct SET option_quantity = '".$newQuantity."' WHERE productcode='".(string)$v->partner_product_id."'";						
						#debug($sqlQuantity);
						pmysql_query($sqlQuantity,get_db_conn());

						if($arrReturnFlag[0] == '1'){
							$orderModifyCount++;
							$returnData .= "[".$mall_nameEnc."] 에서 주문번호 : [".(string)$v->mall_order_id."] 주문 옵션 수정 성공<br>";
						}else{
							$orderGetCount++;
							$returnData .= "[".$mall_nameEnc."] 에서 주문번호 : [".(string)$v->mall_order_id."] 주문 수집 성공<br>";
						}
					}else{
						$orderFailCount++;
						$returnData .= "[".$mall_nameEnc."] 에서 주문번호 : [".(string)$v->mall_order_id."] 주문 수집 실패<br>";
					}
					#debug(" =========================================================================================================== ");
					#debug(" =========================================================================================================== ");
				}
				$resultMsg = "주문 ".$orderGetCount."건 수집(송장입력 ".$orderGetInvoiceCount."건, 옵션 수정 ".$orderModifyCount."건, 실패 ".$orderFailCount."건)<br>";
				$resultMsg .= $returnData;
				#echo $resultMsg;
				echo mb_convert_encoding($resultMsg, 'UTF-8', 'EUC-KR');
			}else{
				echo (string)$insertGoodsData->ResultMessage->message;
			}
			exit;
		break;





		case "linkerClameGet":
			$sdate = str_replace("-", "", $_POST['search_start']);
			$edate = str_replace("-", "", $_POST['search_end']);
			#tblorderclame
			
			$linkerCallURL = "http://apiweb.shoplinker.co.kr/ShoplinkerApi/Clame/Clame_Xml.php";
			$iteminfoURL = urlencode($localXmlDir."/shoplinkerGetClame.php?st_date=".$sdate."&ed_date=".$edate);
			$insertGoodsData = simplexml_load_file($linkerCallURL."?iteminfo_url=".$iteminfoURL);


			$clameGetCount = 0;
			$returnData = "";

			$clameType = array("001"=>"취소관련", "002"=>"교환관련", "003"=>"반품관련");


			foreach($insertGoodsData->Clame as $k => $v){
				if((string)$v->shoplinker_order_id && is_numeric((string)$v->quantity)){
					list($shoplinker_order_id) = $db->fetch("
																					SELECT 
																						shoplinker_order_id 
																					FROM 
																						tblorderlinkerclame 
																					WHERE 
																						shoplinker_mall_order_id = '".(string)$v->mall_order_id."' 
																						AND shoplinker_clame_date = '".(string)$v->clame_date."'
																		");
					if(!$shoplinker_order_id){

						$resultPd = pmysql_query("SELECT * FROM tblproduct WHERE shoplinker_product_id = '".(string)$v->shoplinker_product_id."' ",get_db_conn());
						$_data = pmysql_fetch_object($resultPd);

						$optArray = array();
						
						$skuEnc = mb_convert_encoding((string)$v->sku, 'EUC-KR', 'UTF-8');
						$clameMemo = mb_convert_encoding(addslashes((string)$v->clame_memo), 'EUC-KR', 'UTF-8');
						$clameStatus = mb_convert_encoding((string)$v->clame_status, 'EUC-KR', 'UTF-8');
						$mall_nameEnc =  mb_convert_encoding((string)$v->mall_name, 'EUC-KR', 'UTF-8');


						if($skuEnc){					
							if($mall_nameEnc == "(주)인터파크"){
								$arrSku1 = explode("-", $skuEnc);
								$arrSku2 = explode(" / ", $arrSku1[0]);
								$arrSku_value = explode("/", $arrSku2[1]);
							}else if($mall_nameEnc == "11번가"){
								$arrSku1 = explode("-", $skuEnc);
								$arrSku2 = explode(":", $arrSku1[0]);
								$arrSku_value = explode("/", $arrSku2[1]);
							}else{
								$arrSku1 = explode("-", $skuEnc);
								$arrSku2 = explode("/", $arrSku1[0]);
								$arrSku_value = explode(":", $arrSku2[0]);
							}
						}else{
							$arrSku_value = "";
						}
						$tempOptName1 = $_data->option1;
						$tempOptName2 = $_data->option2;

						if($arrSku_value[0] && $arrSku_value[1]){
							$tok = explode(",", $tempOptName1);
							$option1 = $tok[0]." : ".$arrSku_value[0];
							$option1 = str_replace("'","", $option1);

							$tok2 = explode(",", $tempOptName2);
							$option2 = $tok2[0]." : ".$arrSku_value[1];
							$option2 = str_replace("'","", $option2);
						}else if($arrSku_value[0] && !$arrSku_value[1]){
							$tok = explode(",", $tempOptName1);
							$option1 = $tok[0]." : ".$arrSku_value[0];
							$option1 = str_replace("'","", $option1);

							$option2 = "";
						}else{
							$option1 = "";
							$option2 = "";
						}

						$db->query("INSERT INTO 
											tblorderlinkerclame 
											(
												shoplinker_order_id, shoplinker_mall_order_id, shoplinker_mall_name, 
												shoplinker_order_product_id, shoplinker_product_id, shoplinker_product_name, 
												shoplinker_quantity, shoplinker_order_price, shoplinker_opt1, 
												shoplinker_opt2, shoplinker_clame_status, shoplinker_clame_date,
												shoplinker_proc_type, shoplinker_memo
											)
											VALUES
											(
												'".(string)$v->shoplinker_order_id."', '".(string)$v->mall_order_id."', '".(string)$v->mall_name."', 
												'".(string)$v->order_product_id."', '".(string)$v->shoplinker_product_id."', '".(string)$v->product_name."', 
												'".(string)$v->quantity."', '".(string)$v->order_price."', '".$option1."', 
												'".$option2."', '".$clameStatus."', 	'".(string)$v->clame_date."', 
												'".$clameType[(string)$v->clame_type]."', '".$clameMemo."'
											)");
						$returnData .= "[".(string)$v->mall_order_id."] 주문에 대한 클레임 수집.<br>";
						$clameGetCount++;
					}
				}
			}
			if($clameGetCount > 0){
				$resultMsg = "클레임 ".number_format($clameGetCount)."건 수집<br><br>";
				$resultMsg .= $returnData;
			}else{
				$resultMsg .= "수집할 클레임이 존재하지 않습니다.";
			}
			echo mb_convert_encoding($resultMsg, 'UTF-8', 'EUC-KR');

			/*
			foreach($insertGoodsData->DATA as $k => $v){
				$compayny_goods_cd = mb_convert_encoding ((string)$v->compayny_goods_cd, 'EUC-KR', 'UTF-8');
				$idx = mb_convert_encoding ((string)$v->idx, 'EUC-KR', 'UTF-8');
				$order_id = mb_convert_encoding ((string)$v->order_id, 'EUC-KR', 'UTF-8');
				$mall_id = mb_convert_encoding ((string)$v->mall_id, 'EUC-KR', 'UTF-8');
				$mall_user_id = mb_convert_encoding ((string)$v->mall_user_id, 'EUC-KR', 'UTF-8');
				$clame_status_gubun = mb_convert_encoding ((string)$v->clame_status_gubun, 'EUC-KR', 'UTF-8');
				$clame_content = mb_convert_encoding ((string)$v->clame_content, 'EUC-KR', 'UTF-8');

				$resultCheck = pmysql_query("SELECT sabang_idx FROM tblorderclame WHERE sabang_idx = '".$idx."' ",get_db_conn());
				$_DataCheck = pmysql_fetch_object($resultCheck);
				if($_DataCheck->sabang_idx || !$compayny_goods_cd){
					continue;
				}

				$sql = "
								INSERT INTO tblorderclame
									(
										sabang_company_goods_cd, sabang_idx, sabang_order_id, sabang_mall_id, 
										sabang_mall_user_id, sabang_clame_status_gubun, sabang_clame_contents
									) 
								VALUES
									(
										'".$compayny_goods_cd."', '".$idx."', '".$order_id."', '".$mall_id."', 
										'".$mall_user_id."', '".$clame_status_gubun."', '".$clame_content."'
									)";
				pmysql_query($sql,get_db_conn());
				$clameGetCount++;
				$returnData .= "[".$order_id."] 주문에 대한 클레임 수집.<br>";
			}
			$resultMsg = "클레임 ".$clameGetCount."건 수집<br>";
			$resultMsg .= $returnData;
			echo mb_convert_encoding($resultMsg, 'UTF-8', 'EUC-KR');
			*/
			exit;
		break;
		case "sabangGetCode":
			$sabangCallURL = "http://r.sabangnet.co.kr/RTL_API/xml_goods_prop_code_info.html";
			$iteminfoURL = $localXmlDir."/".$_POST["mode"].".xml";
			$insertGoodsData = file_get_contents($sabangCallURL."?xml_url=".$iteminfoURL);
			$logData[] = $insertGoodsData;
			debug($insertGoodsData);
			exit;
		break;
	}
?>
