<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	
	$localXmlDir = "http://".$_SERVER[SERVER_NAME]."/sabangXml";

	function checkOrderOption($v, $ordercode){
		$resultPd = pmysql_query("SELECT * FROM tblproduct WHERE productcode = '".(string)$v->compayny_goods_cd."' ",get_db_conn());
		$_data = pmysql_fetch_object($resultPd);

		if((string)$v->p_sku_value){
			$arrP_sku_value = explode(":", mb_convert_encoding((string)$v->p_sku_value, 'EUC-KR', 'UTF-8'));
		}else{
			$arrP_sku_value = "";
		}
		$tempOptName1 = $_data->option1;
		$tempOptName2 = $_data->option2;

		if($arrP_sku_value[0] && $arrP_sku_value[1]){
			$tok = explode(",", $tempOptName1);
			$option1 = $tok[0]." : ".$arrP_sku_value[0];
			$option1 = str_replace("'","", $option1);

			$tok2 = explode(",", $tempOptName2);
			$option2 = $tok2[0]." : ".$arrP_sku_value[1];
			$option2 = str_replace("'","", $option2);
		}else if($arrP_sku_value[0] && !$arrP_sku_value[1]){
			$tok = explode(",", $tempOptName1);
			$option1 = $tok[0]." : ".$arrP_sku_value[0];
			$option1 = str_replace("'","", $option1);

			$option2 = "";
		}else{
			$option1 = "";
			$option2 = "";
		}

		list($tempProductcode, $tempOption1, $tempOption2)=pmysql_fetch("SELECT productcode, opt1_name, opt2_name FROM tblorderproduct WHERE ordercode = '".$ordercode."'");
		if($option1 != $tempOption1 || $option2 != $tempOption2 || (string)$v->compayny_goods_cd != $tempProductcode){	
			return "1<<>>".$option1."<<>>".$option2."<<>>".$ordercode;
		}else{
			return "2<<>><<>><<>>";
		}
	}

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
		debug("확정데이터옵션명 : ".mb_convert_encoding((string)$v->p_sku_value, 'EUC-KR', 'UTF-8') );
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
	$arraySabangnetShopCode = array("shop0001" => "옥션", 
															"shop0002" => "지마켓", 
															"shop0003" => "11번가", 
															 "shop0004 " => " 인터파크", 
															"shop0005" => "CJOshopping", 
															"shop0007" => "GS shop", 
															"shop0006" => "현대홈쇼핑", 
															"shop0008" => "롯데홈쇼핑", 
															"shop0009" => "농수산홈쇼핑", 
															"shop0010" => "롯데닷컴", 
															"shop0011" => "신세계몰", 
															"shop0012" => "d&shop", 
															"shop0013" => "AKmall", 
															"shop0014" => "QOOK쇼핑", 
															"shop0015" => "NH쇼핑", 
															"shop0016" => "패션플러스", 
															"shop0017" => "오가게", 
															"shop0018" => "iSTYLE24", 
															"shop0020" => "하프클럽", 
															"shop0021" => "이지웰", 
															"shop0022" => "이마트", 
															"shop0023" => "1300K", 
															"shop0024" => "WIZWID", 
															"shop0025" => "MakeShop", 
															"shop0026" => "cafe24", 
															"shop0027" => "PLAYER", 
															"shop0028" => "도서11번가", 
															"shop0029" => "YES24", 
															"shop0030" => "고도몰", 
															"shop0031" => "2001-OUTLET", 
															"shop0032" => "와와닷컴", 
															"shop0033" => "상록몰", 
															"shop0034" => "오셀러", 
															"shop0035" => "FoodMart", 
															"shop0036" => "동원몰", 
															"shop0037" => "패션밀", 
															"shop0038" => "팔도오일장", 
															"shop0039" => "Homeplus", 
															"shop0040" => "세일코리아", 
															"shop0041" => "네이버 체크아웃", 
															"shop0042" => "텐바이텐", 
															"shop0043" => "두바이", 
															"shop0044" => "체크아이몰", 
															"shop0045" => "홀리스퀘어", 
															"shop0046" => "NH마켓", 
															"shop9999" => "자사운영쇼핑몰");
	*/





	switch ($_POST["mode"]){
		case "sabangProductRegister":
			# 상품 코드를 serialize하여 XML생성 php 파일에 전송. unserialize 하여 사용.
			$resultMsg = "";
			$resultCount = 0;

			$arrayProductCode = $_POST['sabang_val'];
			$serialized_ArrayProductCode = serialize($arrayProductCode);

			$sabangCallURL = "http://r.sabangnet.co.kr/RTL_API/xml_goods_info.html";
			$iteminfoURL = urlencode($localXmlDir."/".$_POST["mode"].".php?pd_code=".$serialized_ArrayProductCode);
			$insertGoodsData = file_get_contents($sabangCallURL."?xml_url=".$iteminfoURL);
			$arrResultData = explode("br", $insertGoodsData);
			$arrResultData = explode("<br>", $insertGoodsData);
			$arrResultData = array_slice($arrResultData, 1);

			foreach($arrResultData as $v){
				if(!$v) continue;
				$resultCount++;
				$msg = strip_tags(substr($v, 4));
				$arrMsg = explode(" : ", $msg);
				$replaceMsg = str_replace("[", "", $arrMsg[1]);
				$replaceMsg = str_replace("]", "", $replaceMsg);
				$pdCodeArr = explode(" ", $replaceMsg);
				#$arrMsg[0] 성공 / 수정 성공 / 누락 등등 리턴 결과값 메세지
				#$pdCodeArr[0] 사방넷 상품 코드
				#$pdCodeArr[1] 넥솔브 상품 코드

				if(strstr($arrMsg[0], '성공')){
					pmysql_query("UPDATE tblproduct SET sabangnet_flag = 'Y' WHERE productcode = '".$pdCodeArr[1]."'", get_db_conn());
					$resultMsg .= "<div>[".number_format($resultCount)."] 등록(수정) 성공 : ".$pdCodeArr[1]."</div>";
				}else{
					pmysql_query("UPDATE tblproduct SET sabangnet_flag = 'N' WHERE productcode = '".$pdCodeArr[1]."'", get_db_conn());
					$resultMsg .= "<div>[".number_format($resultCount)."] 등록(수정) 실패 : ".$pdCodeArr[1]."</div>";
				}
			}
			echo mb_convert_encoding($insertGoodsData, 'UTF-8', 'EUC-KR');
			#$strString = mb_convert_encoding("<div>총 ".number_format($resultCount )."건</div>".$resultMsg, 'UTF-8', 'EUC-KR');
			#echo $strString;
			exit;
		break;











		# 셋트 상품용 상품 등록
		case "sabangProductSetRegister":
			# 상품 코드를 serialize하여 XML생성 php 파일에 전송. unserialize 하여 사용.
			$resultMsg = "";
			$resultCount = 0;
			$resultArray = array();

			$arrayProductCode = $_POST['sabang_val'];
			$serialized_ArrayProductCode = serialize($arrayProductCode);

			$sabangCallURL = "http://r.sabangnet.co.kr/RTL_API/xml_goods_info.html";
			$iteminfoURL = urlencode($localXmlDir."/sabangProductRegister.php?pd_code=".$serialized_ArrayProductCode);
			$insertGoodsData = file_get_contents($sabangCallURL."?xml_url=".$iteminfoURL);
			$arrResultData = explode("br", $insertGoodsData);
			$arrResultData = explode("<br>", $insertGoodsData);
			$arrResultData = array_slice($arrResultData, 1);

			foreach($arrResultData as $v){
				if(!$v) continue;
				$resultCount++;
				$msg = strip_tags(substr($v, 4));
				$arrMsg = explode(" : ", $msg);
				$replaceMsg = str_replace("[", "", $arrMsg[1]);
				$replaceMsg = str_replace("]", "", $replaceMsg);
				$pdCodeArr = explode(" ", $replaceMsg);
				#$arrMsg[0] 성공 / 수정 성공 / 누락 등등 리턴 결과값 메세지
				#$pdCodeArr[0] 사방넷 상품 코드
				#$pdCodeArr[1] 넥솔브 상품 코드

				if(strstr($arrMsg[0], '성공')){
					pmysql_query("UPDATE tblproduct SET sabangnet_flag = 'Y' WHERE productcode = '".$pdCodeArr[1]."'", get_db_conn());
					$resultArray[$pdCodeArr[1]] = true;
					$resultMsg .= "<div>[".number_format($resultCount)."] <font color='blue'>등록(수정) 성공 : ".$pdCodeArr[1]."</font></div>";
				}else{
					pmysql_query("UPDATE tblproduct SET sabangnet_flag = 'N' WHERE productcode = '".$pdCodeArr[1]."'", get_db_conn());
					$resultArray[$pdCodeArr[1]] = false;
					$resultMsg .= "<div>[".number_format($resultCount)."] <font color='blue'>등록(수정) 실패 : ".$pdCodeArr[1]."</font></div>";
				}
			}
			#$strString = mb_convert_encoding("상품등록<br>", 'UTF-8', 'EUC-KR').mb_convert_encoding($insertGoodsData, 'UTF-8', 'EUC-KR');


			/*
			$sabangCallURL = "http://r.sabangnet.co.kr/RTL_API/xml_goods_info2.html";
			$iteminfoURL = urlencode($localXmlDir."/sabangProductModify.php?pd_code=".$serialized_ArrayProductCode);
			$insertGoodsData = file_get_contents($sabangCallURL."?xml_url=".$iteminfoURL);
			$arrResultData = explode("br", $insertGoodsData);
			$arrResultData = explode("<br>", $insertGoodsData);
			$arrResultData = array_slice($arrResultData, 1);
			foreach($arrResultData as $v){
				if(!$v) continue;
				$resultCount++;
				$msg = strip_tags(substr($v, 4));
				$arrMsg = explode(" : ", $msg);
				$replaceMsg = str_replace("[", "", $arrMsg[1]);
				$replaceMsg = str_replace("]", "", $replaceMsg);
				$pdCodeArr = explode(" ", $replaceMsg);
				#$arrMsg[0] 성공 / 수정 성공 / 누락 등등 리턴 결과값 메세지
				#$pdCodeArr[0] 사방넷 상품 코드
				#$pdCodeArr[1] 넥솔브 상품 코드

				if(strstr($arrMsg[0], '성공')){
					if($resultArray[$pdCodeArr[1]]){
						$resultMsg .= "<div>[".number_format($resultCount)."] <font color='blue'>등록 성공 / 수정 성공 : ".$pdCodeArr[1]."</font></div>";
					}else{
						$resultMsg .= "<div>[".number_format($resultCount)."] <font color='blue'>등록 성공 / 수정 실패 : ".$pdCodeArr[1]."</font></div>";
					}
				}else{
					if($resultArray[$pdCodeArr[1]]){
						$resultMsg .= "<div>[".number_format($resultCount)."] <font color='blue'>등록 실패 / 수정 성공 : ".$pdCodeArr[1]."</font></div>";
					}else{
						$resultMsg .= "<div>[".number_format($resultCount)."] <font color='blue'>등록 실패 / 수정 실패 : ".$pdCodeArr[1]."</font></div>";
					}
				}
			}
			*/


			$strString = mb_convert_encoding("<div>총건수 : ".number_format($resultCount )."</div>".$resultMsg, 'UTF-8', 'EUC-KR');
			echo $strString;
			exit;
		break;















		case "sabangProductModify":
			# 상품 코드를 serialize하여 XML생성 php 파일에 전송. unserialize 하여 사용.
			$arrayProductCode = $_POST['sabang_val'];
			$serialized_ArrayProductCode = serialize($arrayProductCode);

			$sabangCallURL = "http://r.sabangnet.co.kr/RTL_API/xml_goods_info2.html";
			$iteminfoURL = urlencode($localXmlDir."/".$_POST["mode"].".php?pd_code=".$serialized_ArrayProductCode);
			$insertGoodsData = file_get_contents($sabangCallURL."?xml_url=".$iteminfoURL);
			$arrResultData = explode("br", $insertGoodsData);
			$arrResultData = explode("<br>", $insertGoodsData);
			$arrResultData = array_slice($arrResultData, 1);
			foreach($arrResultData as $v){
				if(!$v) continue;
				$msg = strip_tags(substr($v, 4));
				$arrMsg = explode(" : ", $msg);
				$replaceMsg = str_replace("[", "", $arrMsg[1]);
				$replaceMsg = str_replace("]", "", $replaceMsg);
				$pdCodeArr = explode(" ", $replaceMsg);
				#$arrMsg[0] 성공 / 수정 성공 / 누락 등등 리턴 결과값 메세지
				#$pdCodeArr[0] 사방넷 상품 코드
				#$pdCodeArr[1] 넥솔브 상품 코드

				if(strstr($arrMsg[0], '성공')){
					pmysql_query("UPDATE tblproduct SET sabangnet_flag = 'Y' WHERE productcode = '".$pdCodeArr[1]."'", get_db_conn());
					#debug("성공////".$pdCodeArr[0]."////".$pdCodeArr[1]);
				}else{
					pmysql_query("UPDATE tblproduct SET sabangnet_flag = 'R' WHERE productcode = '".$pdCodeArr[1]."'", get_db_conn());
					#debug("실패////".$pdCodeArr[0]."////".$pdCodeArr[1]);
				}
			}
			echo mb_convert_encoding($insertGoodsData, 'UTF-8', 'EUC-KR');
			exit;
		break;














		case "sabangProductSoldout":
			# 상품 코드를 serialize하여 XML생성 php 파일에 전송. unserialize 하여 사용.
			$arrayProductCode = $_POST['sabang_val'];
			$serialized_ArrayProductCode = serialize($arrayProductCode);

			$sabangCallURL = "http://r.sabangnet.co.kr/RTL_API/xml_goods_info2.html";
			$iteminfoURL = urlencode($localXmlDir."/sabangProductModify.php?soldout=1&pd_code=".$serialized_ArrayProductCode);
			$insertGoodsData = file_get_contents($sabangCallURL."?xml_url=".$iteminfoURL);
			$arrResultData = explode("br", $insertGoodsData);
			$arrResultData = explode("<br>", $insertGoodsData);
			$arrResultData = array_slice($arrResultData, 1);
			foreach($arrResultData as $v){
				if(!$v) continue;
				$msg = strip_tags(substr($v, 4));
				$arrMsg = explode(" : ", $msg);
				$replaceMsg = str_replace("[", "", $arrMsg[1]);
				$replaceMsg = str_replace("]", "", $replaceMsg);
				$pdCodeArr = explode(" ", $replaceMsg);
				#$arrMsg[0] 성공 / 수정 성공 / 누락 등등 리턴 결과값 메세지
				#$pdCodeArr[0] 사방넷 상품 코드
				#$pdCodeArr[1] 넥솔브 상품 코드

				if(strstr($arrMsg[0], '성공')){
					pmysql_query("UPDATE tblproduct SET sabangnet_flag = 'Y' WHERE productcode = '".$pdCodeArr[1]."'", get_db_conn());
					#debug("성공////".$pdCodeArr[0]."////".$pdCodeArr[1]);
				}else{
					pmysql_query("UPDATE tblproduct SET sabangnet_flag = 'R' WHERE productcode = '".$pdCodeArr[1]."'", get_db_conn());
					#debug("실패////".$pdCodeArr[0]."////".$pdCodeArr[1]);
				}
			}
			echo mb_convert_encoding($insertGoodsData, 'UTF-8', 'EUC-KR');
			exit;
		break;












		case "sabangOrderGet":
			$sdate = $_POST['search_start'];
			$edate = $_POST['search_end'];
			$sabangCallURL = "https://r.sabangnet.co.kr/RTL_API/xml_order_info.html";
			$iteminfoURL = urlencode($localXmlDir."/".$_POST["mode"].".php?sdate=".$sdate."&edate=".$edate);
			$insertGoodsData = simplexml_load_file($sabangCallURL."?xml_url=".$iteminfoURL);
			$arrOrderDatas = array();
			$orderGetCount = 0;
			$orderModifyCount = 0;
			$orderFailCount = 0;
			$orderGetInvoiceCount = 0;
			$returnData = "";

			/*
			foreach($insertGoodsData->DATA as $k => $v){
				if((string)$v->order_id != '114276440') continue;
				orderInfo($v);
			}
			exit;
			*/
			foreach($insertGoodsData->DATA as $k => $v){
				$arrReturnFlag = array();
				list($_DataCheckSabangnetIdx, $ordercode)=pmysql_fetch("SELECT count(sabangnet_idx), MAX(ordercode) FROM tblorderinfo WHERE sabangnet_idx = '".(string)$v->idx."'");
				if($_DataCheckSabangnetIdx && $ordercode){
					if((string)$v->invoice_no){
						list($_DataOrderCode)=pmysql_fetch("SELECT ordercode FROM tblorderinfo WHERE sabangnet_idx = '".(string)$v->idx."'");
						$sql = "UPDATE tblorderproduct SET deli_num = '".(string)$v->invoice_no."', deli_date = '".date("YmdHis")."', deli_com = '6', deli_gbn = 'Y' WHERE ordercode = '".$_DataOrderCode."'";
						pmysql_query($sql,get_db_conn());
						$sql = "UPDATE tblorderinfo SET deli_gbn = 'Y', deli_date = '".date("YmdHis")."' WHERE ordercode = '".$_DataOrderCode."'";
						pmysql_query($sql,get_db_conn());
						$returnData .= "[".$arraySabangnetShopCode[(string)$v->mall_id]."] 에서 주문번호 : [".(string)$v->order_id."] 주문 송장 입력 성공<br>";
						$orderGetInvoiceCount++;
					}
					#orderInfo($v);
					$returnFlag = checkOrderOption($v, $ordercode);
					$arrReturnFlag = explode("<<>>", $returnFlag);
					if($arrReturnFlag[0] == '2'){
						continue;
					}
				}




				if($arrReturnFlag[0] == '1'){
					list($sabangnet_set_flag)=pmysql_fetch("SELECT count(no) FROM tblproduct_sabangnet WHERE productcode = '".(string)$v->compayny_goods_cd."'");
					if($sabangnet_set_flag > 0){
						$data_set_flag = "Y";
					}else{
						$data_set_flag = "N";
					}

					$resultPd = pmysql_query("SELECT * FROM tblproduct WHERE productcode = '".(string)$v->compayny_goods_cd."' ",get_db_conn());
					$_data = pmysql_fetch_object($resultPd);

					if((string)$v->p_sku_value){
						$arrP_sku_value = explode(":", mb_convert_encoding((string)$v->p_sku_value, 'EUC-KR', 'UTF-8'));
					}else{
						$arrP_sku_value = "";
					}
					$tempOptName1 = $_data->option1;
					$tempOptName2 = $_data->option2;

					if($arrP_sku_value[0] && $arrP_sku_value[1]){
						$tok = explode(",", $tempOptName1);
						$option1 = $tok[0]." : ".$arrP_sku_value[0];
						$option1 = str_replace("'","", $option1);

						$tok2 = explode(",", $tempOptName2);
						$option2 = $tok2[0]." : ".$arrP_sku_value[1];
						$option2 = str_replace("'","", $option2);
					}else if($arrP_sku_value[0] && !$arrP_sku_value[1]){
						$tok = explode(",", $tempOptName1);
						$option1 = $tok[0]." : ".$arrP_sku_value[0];
						$option1 = str_replace("'","", $option1);

						$option2 = "";
					}else{
						$option1 = "";
						$option2 = "";
					}

					$sql = "UPDATE tblorderproduct SET productcode = '".(string)$v->compayny_goods_cd."', productname = '".mb_convert_encoding((string)$v->p_product_name, 'EUC-KR', 'UTF-8')."', opt1_name = '".$arrReturnFlag[1]."', opt2_name = '".$arrReturnFlag[2]."' WHERE ordercode = '".$arrReturnFlag[3]."'";
					#debug($sql);
					pmysql_query($sql,get_db_conn());
					if(pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();
					if($pmysql_errno) $okmail="YES";
				}else{
					$ordercode = unique_id();
					$ordercode_front = substr((string)$v->reg_date, 0, 12);
					$ordercode_end = substr($ordercode, 12);
					$ordercode = $ordercode_front.$ordercode_end;

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
					if((string)$v->invoice_no){
						//deli_num = '".(string)$v->invoice_no."', deli_date = '".date("YmdHis")."', deli_com = '6', deli_gbn = 'Y'
						$addInvoiceCol = ", deli_num, deli_date, deli_com, deli_gbn";
						$addInvoiceVal = ", '".(string)$v->invoice_no."', '".date("YmdHis")."', '6', 'Y'";
					}

					$sql = "
									INSERT INTO tblorderinfo
										(
											tot_price_dc, ordercode, tempkey, id, price, 
											deli_price, paymethod, pay_data, sender_name, sender_email, 
											sender_tel, receiver_name, receiver_tel1, receiver_tel2, receiver_addr, 
											order_msg, ip, del_gbn, partner_id, loc, 
											bank_sender, receipt_yn, order_msg2, sabangnet_idx, sabangnet_order_id, 
											sabangnet_mall_id, sabangnet_mall_user_id, pay_flag, bank_date, sabangnet_set_flag ".$addInvoiceCol."
										) 
									VALUES 
										(
											'0', '".$ordercode."', '".$tempkey."', '', '".(string)$v->pay_cost."', 
											'".(string)$v->delv_cost."', 'O', '', '".mb_convert_encoding((string)$v->user_name, 'EUC-KR', 'UTF-8')."', '".(string)$v->user_email."', 
											'".(string)$v->user_tel."', '".mb_convert_encoding((string)$v->receive_name, 'EUC-KR', 'UTF-8')."', '".(string)$v->receive_tel."', '".(string)$v->receive_cel."', '".$address."', 
											'[".$arraySabangnetShopCode[(string)$v->mall_id]."]에서 생성된 주문\n".mb_convert_encoding((string)$v->delv_msg, 'EUC-KR', 'UTF-8')."', '".$_SERVER['REMOTE_ADDR']."', 'N', '".$arraySabangnetShopCode[(string)$v->mall_id]."', '".$loc."', 
											'', 'Y', '', '".(string)$v->idx."', '".(string)$v->order_id."', 
											'".(string)$v->mall_id."', '".(string)$v->mall_user_id."', '0000', '".(string)$v->order_date."', '".$data_set_flag."' ".$addInvoiceVal."
										)";
					#debug($sql);
					pmysql_query($sql, get_db_conn());
					if(pmysql_errno()!=1062) $pmysql_errno += pmysql_errno();

					$resultPd = pmysql_query("SELECT * FROM tblproduct WHERE productcode = '".(string)$v->compayny_goods_cd."' ",get_db_conn());
					$_data = pmysql_fetch_object($resultPd);

					if((string)$v->p_sku_value){
						$arrP_sku_value = explode(":", mb_convert_encoding((string)$v->p_sku_value, 'EUC-KR', 'UTF-8'));
					}else{
						$arrP_sku_value = "";
					}
					$tempOptName1 = $_data->option1;
					$tempOptName2 = $_data->option2;

					if($arrP_sku_value[0] && $arrP_sku_value[1]){
						$tok = explode(",", $tempOptName1);
						$option1 = $tok[0]." : ".$arrP_sku_value[0];
						$option1 = str_replace("'","", $option1);

						$tok2 = explode(",", $tempOptName2);
						$option2 = $tok2[0]." : ".$arrP_sku_value[1];
						$option2 = str_replace("'","", $option2);
					}else if($arrP_sku_value[0] && !$arrP_sku_value[1]){
						$tok = explode(",", $tempOptName1);
						$option1 = $tok[0]." : ".$arrP_sku_value[0];
						$option1 = str_replace("'","", $option1);

						$option2 = "";
					}else{
						$option1 = "";
						$option2 = "";
					}

					$priceProduct = (string)$v->pay_cost/(string)$v->sale_cnt;
					if(!$priceProduct) $priceProduct = (string)$v->sale_cost;


					if((string)$v->invoice_no){
						$sql = "
										INSERT INTO tblorderproduct
											(
												vender, ordercode, tempkey, productcode, productname, 
												opt1_name, opt2_name, package_idx, assemble_idx, addcode, 
												quantity, price, reserve, date, selfcode, 
												productbisiness, order_prmsg, assemble_info, deli_num, deli_date,
												deli_com, sabangnet_mall_won_cost
											) 
										VALUES
											(
												'0', '".$ordercode."', '".$tempkey."', '".(string)$v->compayny_goods_cd."', '".mb_convert_encoding((string)$v->p_product_name, 'EUC-KR', 'UTF-8')."',
												'".$option1."', '".$option2."', '0', '0', '',
												'".(string)$v->sale_cnt."', '".$priceProduct."', '0', '".substr((string)$v->order_date, 0, 8)."', '', 
												'','','','".(string)$v->invoice_no."', '".date("YmdHis")."', 
												'6', '".(string)$v->mall_won_cost."'
											)";
					}else{
						$sql = "
										INSERT INTO tblorderproduct
											(
												vender, ordercode, tempkey, productcode, productname, 
												opt1_name, opt2_name, package_idx, assemble_idx, addcode, 
												quantity, price, reserve, date, selfcode, 
												productbisiness, order_prmsg, assemble_info, deli_num, deli_date,
												sabangnet_mall_won_cost
											) 
										VALUES
											(
												'0', '".$ordercode."', '".$tempkey."', '".(string)$v->compayny_goods_cd."', '".mb_convert_encoding((string)$v->p_product_name, 'EUC-KR', 'UTF-8')."',
												'".$option1."', '".$option2."', '0', '0', '',
												'".(string)$v->sale_cnt."', '".$priceProduct."', '0', '".substr((string)$v->order_date, 0, 8)."', '', 
												'','','','', '',
												'".(string)$v->mall_won_cost."'
											)";
					}
					#debug($sql);
					pmysql_query($sql,get_db_conn());
					if(pmysql_errno()!=1062) $pmysql_errno+=pmysql_errno();
					if($pmysql_errno) $okmail="YES";
				}


				$tempSabangQuantity = array();
				
				#orderInfo($v);
				if($okmail != "YES"){
					if(	$data_set_flag == 'Y' ){
						$sqlQuantity = "UPDATE tblproduct_sabangnet SET quantity = quantity - ".(string)$v->sale_cnt." WHERE productcode='".(string)$v->compayny_goods_cd."' AND option1 = '".$arrP_sku_value[0]."' AND option2 = '".$arrP_sku_value[1]."'";
						#debug($sqlQuantity);
						pmysql_query($sqlQuantity,get_db_conn());

						if($arrReturnFlag[0] == '1'){
							$orderModifyCount++;
							$returnData .= "[".$arraySabangnetShopCode[(string)$v->mall_id]."] 에서 주문번호 : [".(string)$v->order_id."] 주문 옵션 수정 성공<br>";
						}else{
							$orderGetCount++;
							$returnData .= "[".$arraySabangnetShopCode[(string)$v->mall_id]."] 에서 주문번호 : [".(string)$v->order_id."] 주문 수집 성공<br>";
						}
					}else{
						if(strlen($_data->option1)>0 && strlen($_data->option2)>0) {
							$arrOption1 = explode(",", $_data->option1);
							$arrOption2 = explode(",", $_data->option2);
							$arrQuantity = explode(",", $_data->sabangnet_option_quantity);

							for($j=0;$j<5;$j++){
								for($i=0;$i<10;$i++){
									if($arrOption1[$i] == $arrP_sku_value[0] && $arrOption2[$j] == $arrP_sku_value[1] && $arrQuantity[$j*10+$i] ){
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
							$arrQuantity = explode(",", $_data->sabangnet_option_quantity);
							for($j=0;$j<5;$j++){
								for($i=0;$i<10;$i++){
									if($arrOption1[$i] == $arrP_sku_value[0] && $arrQuantity[$j*10+$i]){
										$tempSabangQuantity[] = $arrQuantity[$j*10+$i] - 1;
									}else{
										$tempSabangQuantity[] = $arrQuantity[$j*10+$i];
									}
								}
							}
							$newQuantity = implode(",", $tempSabangQuantity);
						}else{
							$_datasabangnet_option_quantity = ",10";
							$arrQuantity = explode(",", $_data->sabangnet_option_quantity);
							$tempSabangQuantity[] = $arrQuantity[1] - (string)$v->sale_cnt;
							$newQuantity = ",".implode(",", $tempSabangQuantity);
						}

						$sqlQuantity = "UPDATE tblproduct SET sabangnet_option_quantity = '".$newQuantity."' WHERE productcode='".(string)$v->compayny_goods_cd."'";						
						#debug($sqlQuantity);
						pmysql_query($sqlQuantity,get_db_conn());

						if($arrReturnFlag[0] == '1'){
							$orderModifyCount++;
							$returnData .= "[".$arraySabangnetShopCode[(string)$v->mall_id]."] 에서 주문번호 : [".(string)$v->order_id."] 주문 옵션 수정 성공<br>";
						}else{
							$orderGetCount++;
							$returnData .= "[".$arraySabangnetShopCode[(string)$v->mall_id]."] 에서 주문번호 : [".(string)$v->order_id."] 주문 수집 성공<br>";
						}
					}
				}else{
					$orderFailCount++;
					$returnData .= "[".$arraySabangnetShopCode[(string)$v->mall_id]."] 에서 주문번호 : [".(string)$v->order_id."] 주문 수집 실패<br>";
				}
				#debug(" =========================================================================================================== ");
				#debug(" =========================================================================================================== ");
			}
			$resultMsg = "주문 ".$orderGetCount."건 수집(송장입력 ".$orderGetInvoiceCount."건, 옵션 수정 ".$orderModifyCount."건, 실패 ".$orderFailCount."건)<br>";
			$resultMsg .= $returnData;
			#echo $resultMsg;
			echo mb_convert_encoding($resultMsg, 'UTF-8', 'EUC-KR');
			exit;
		break;





		case "sabangClameGet":
			$sdate = $_POST['search_start'];
			$edate = $_POST['search_end'];
			$sabangCallURL = "https://r.sabangnet.co.kr/RTL_API/xml_clm_info.html";
			$iteminfoURL = urlencode($localXmlDir."/".$_POST["mode"].".php?sdate=".$sdate."&edate=".$edate);
			$insertGoodsData = simplexml_load_file($sabangCallURL."?xml_url=".$iteminfoURL);

			$clameGetCount = 0;
			$clameNoGetCount = 0;
			$returnData = "";
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
