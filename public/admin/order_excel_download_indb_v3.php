<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");

	function doubleQuote($str) {
		return str_replace('"', '""', $str);
	}

	function option_slice2( $content, $option_type = '0' ){
		$tmp_content = '';
		if( $option_type == '0' ) {
			$tmp_content = explode( chr(30), $content );
		} else {
			$tmp_content = explode( '@#', $content );
		}
		
		return $tmp_content;
	}

	@set_time_limit(1000);

	$mode			= $_POST["mode"];  
	$item_type		= $_POST["item_type"];  
	$excel_sql		= $_POST["excel_sql"];  
	$est				= $_POST["est"];    
	$oc_step	= $_POST["oc_step"];
	$redeliverytype=$_POST["redeliverytype"];
	$connect_ip	= $_SERVER['REMOTE_ADDR'];
	$curdate		= date("YmdHis");	

	$pattern				= array("\r\n","\"",",",";");
	$replacement		= array(" ","",".","");

	//벤더, 브랜드 리스트 검색
	$sql = "SELECT vendercnt FROM tblshopcount ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$vendercnt=$row->vendercnt;
	pmysql_free_result($result);

	if($vendercnt>0){
		$venderlist=array();
		$sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
				FROM    tblvenderinfo a 
				JOIN    tblproductbrand b on a.vender = b.vender 
				ORDER BY lower(b.brandname) ASC
				";
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$venderlist[$row->vender]=$row;
		}
		pmysql_free_result($result);
	}	


	$result = pmysql_query($excel_sql,get_db_conn());

	if ($item_type	== 'order_all') $log_title = "전체주문조회(주문별)";
	if ($item_type	== 'order_all_0') $log_title = "단계별주문조회(주문별) - 주문접수";
	if ($item_type	== 'order_all_1') $log_title = "단계별주문조회(주문별) - 결제완료";
	if ($item_type	== 'order_all_2') $log_title = "단계별주문조회(주문별) - 배송준비중";
	if ($item_type	== 'order_all_3') $log_title = "단계별주문조회(주문별) - 배송중";
	if ($item_type	== 'order_all_4') $log_title = "단계별주문조회(주문별) - 배송완료";

	if ($item_type	== 'order_product_all') $log_title = "전체주문조회(상품별)";
	if ($item_type	== 'order_product_all_0') $log_title = "단계별주문조회(상품별) - 주문접수";
	if ($item_type	== 'order_product_all_1') $log_title = "단계별주문조회(상품별) - 결제완료";
	if ($item_type	== 'order_product_all_2') $log_title = "단계별주문조회(상품별) - 배송준비중";
	if ($item_type	== 'order_product_all_3') $log_title = "단계별주문조회(상품별) - 배송중";
	if ($item_type	== 'order_product_all_4') $log_title = "단계별주문조회(상품별) - 배송완료";

	if ($item_type	== 'order_misu') $log_title = "입금대기 리스트";
	if ($item_type	== 'order_delivery') $log_title = "배송준비중 리스트";
	if ($item_type	== 'order_cancel') $log_title = "주문취소리스트";
	if ($item_type	== 'order_cancel_0') $log_title = "입금전 취소 관리";
	if ($item_type	== 'order_cancel_41_2') $log_title = "취소관리(결제취소) - 취소접수(환불접수)";
	if ($item_type	== 'order_cancel_44_2') $log_title = "취소관리(결제취소) - 환불완료";
	if ($item_type	== 'order_cancel_34_40') $log_title = "반품관리 - 반품신청";
	if ($item_type	== 'order_cancel_34_41') $log_title = "반품관리 - 반품접수";
	if ($item_type	== 'order_cancel_41_34') $log_title = "반품관리 - 환불접수";
	if ($item_type	== 'order_cancel_44_34') $log_title = "반품관리 - 환불완료";
	if ($item_type	== 'order_cancel_34_change_40') $log_title = "교환관리 - 교환신청";
	if ($item_type	== 'order_cancel_34_change_41') $log_title = "교환관리 - 교환접수";
	if ($item_type	== 'order_cancel_44_342') $log_title = "교환관리 - 교환완료";
	if ($item_type	== 'order_cancel_41_1234') $log_title = "환불관리 - 환불접수";
	if ($item_type	== 'order_cancel_44_1234') $log_title = "환불관리 - 환불완료";

	if ($item_type	== 'cs_order_all') $log_title = "CS 통합 리스트(주문별)";	
	if ($item_type	== 'cs_order_cancel_41_2') $log_title = "CS 취소";
	if ($item_type	== 'cs_order_cancel_34_change_41') $log_title = "CS 교환";
	if ($item_type	== 'cs_order_cancel_34_41') $log_title = "CS 반품";

	$log_content		= "## ".$log_title." 엑셀 다운로드 ## - 다운로드 ".$_ShopInfo->getId()." - 시간 : ".$curdate;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$file_ext	= $item_type=='order_delivery'?"csv":"xls";
	$down_filename	= $item_type."_".$curdate.".".$file_ext;

	header("Content-type: application/vnd.ms-excel");
	Header("Content-Disposition: attachment; filename={$down_filename}"); 
	Header("Pragma: no-cache"); 
	Header("Expires: 0");
	Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	Header("Content-Description: PHP4 Generated Data");

	$xls_data	= "";
	$csv_data	= "";

	$xls_data	.= "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"></head><body>";

	$fields = parse_ini_file("./order_excel_download_conf.ini", true);	

	$xls_data	.= "<table border=1><tr>";
	$i	= 0;
	foreach ( $est as $key => $val ) {
		if ( $fields[$val] !='' && $fields[$val]['down'] == 'Y' ) {
			$xls_data	.= "<td>";
			$xls_data	.= $fields[$val]['text'];
			$xls_data	.= "</td>";

			if($i!=0) $csv_data	.= ",";
			$csv_data	.= iconv('UTF-8', 'EUC-KR', $fields[$val]['text']);
			$i++;
		}
	}
	$xls_data	.= "</tr>";
	$csv_data	.= "\n";

	$vnum				= 0;

	while ($row=pmysql_fetch_object($result)) {

		// 번호
		$vnum++;

		// 고유번호
		$idx					= $row->idx;
		
		// 일자
		$date					= substr($row->ordercode, 0, 12);
		$date					= substr($date,0,4)."/".substr($date,4,2)."/".substr($date,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
		$regdt					= substr($row->ordercode, 0, 12);
		$regdt					= substr($regdt,0,4)."-".substr($regdt,4,2)."-".substr($regdt,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";	

		if($row->store_regdt!=null || $row->store_regdt !=''){
			$store_regdt			= substr($row->store_regdt, 0, 12);
			$store_regdt			= substr($store_regdt,0,4)."-".substr($store_regdt,4,2)."-".substr($store_regdt,6,2)." (".substr($row->store_regdt,8,2).":".substr($row->store_regdt,10,2).")";	
		}else{
			$store_regdt			='';
		}

		if($row->toca_dt !=null || $row->toca_dt !=''){
			$toca_dt			= substr($row->toca_dt, 0, 12);
			$toca_dt			= substr($toca_dt,0,4)."-".substr($toca_dt,4,2)."-".substr($toca_dt,6,2)." (".substr($row->toca_dt,8,2).":".substr($row->toca_dt,10,2).")";	
		}else{
			$toca_dt			='';
		}

		if($row->toc_dt !=null || $row->toc_dt !=''){
			$toc_dt			= substr($row->toc_dt, 0, 12);
			$toc_dt			= substr($toc_dt,0,4)."-".substr($toc_dt,4,2)."-".substr($toc_dt,6,2)." (".substr($row->toc_dt,8,2).":".substr($row->toc_dt,10,2).")";	
		}else{
			$toc_dt			='';
		}

		$ordercode			= $row->ordercode;						// 주문번호	
		$sender_name	= $row->sender_name;					// 주문자
		
		// 주문ID
		if(substr($row->ordercode,20)=="X") {	// 비회원
			//$idnum	= "(비회원)".substr($row->id,1,6);
			$idnum	= "(비회원)".$row->sender_name;
		} else {												// 회원
			$idnum	= $row->id;
		}
		
		$sender_tel		= replace_tel(check_num($row->sender_tel));	// 주문자핸드폰	
		$sender_email	= $row->sender_email;									// 이메일
		$sender_tel2		= $row->sender_tel2;										// 주문자전화번호	
		$sender_post		= $row->home_post;										// 주문자우편번호
		$sender_addr		= str_replace("↑=↑"," ",$row->home_addr);		// 주문자주소
		$brand				= $venderlist[$row->vender]->brandname;		// 브랜드

		// 상품명
		if($row->prod_cnt > 1) $product = $row->productname." 외 ".($row->prod_cnt-1)."건";
		else $product = $row->productname;
		$product				= str_replace("<BR>"," ",$product);
		$product				= str_replace("<br>"," ",$product);

		$productcode			= $row->productcode;									// 상품코드
		$prodcode				= $row->prodcode;										// ERP 상품코드
		$colorcode				= $row->colorcode;										// ERP 색상코드
		$self_goods_code	= $row->prodcode.$row->colorcode.$row->opt2_name;							// 자체상품코드
		$tag_style_no			= $row->tag_style_no;
		$online_goods_code = $row->tag_style_no.$row->colorcode.$row->opt2_name;
		
		// 매장코드
		//$store_code = $arrChainCode[$row->delivery_type];
		$store_code = "";

		if($row->store_code){
			$storeData = getStoreData($row->store_code);
			$store_code .= ($row->delivery_type==0)?'A1801B':$row->store_code;
		} 
		
		// 재고 
		$status = $op_step[$row->op_step];
		if($row->redelivery_type == "G" && $row->op_step == "41") $status = "교환접수";
		if($row->redelivery_type == "G" && $row->op_step == "44") $status = "교환완료";
		//$deli_gbn				= "asd".$status;
		$sumqty = '';
		if($row->delivery_type==0 || $row->delivery_type==3){
			//list($prodcode)=pmysql_fetch("select prodcode from tblproduct where productcode='".$row->productcode."'");
			$rtn=getErpPriceNStock($prodcode, $row->colorcode, $row->opt2_name, $sync_bon_code);
			
			if(!$rtn[sumqty]){
				$rtn[sumqty]=0;
			}
			
			if($row->delivery_type==0 ){
				//$sumqty = "본사발송<br>(재고:".$rtn[sumqty]."|".$row->redelivery_type."|".$row->op_step."|".$status."|".implode(",", $est)."|".$est['deli_gbn'].")";
				if($status == "취소완료"){
					$sumqty = $rtn[sumqty];
				} else {
					$sumqty =$rtn[sumqty];
				}
				//$sumqty = "재고 : ".$rtn[sumqty]."|".$status."|".$deli_gbn;
			} else if($row->delivery_type==3){
// 				$sumqty = "매장발송";
				//$sumqty = "재고 : 없음";
				$sumqty = '-';
			}
		}
			
		$A1801_qty	= 0;
		$A1770_qty	= 0;
		$A1771_qty	= 0;
		$online_qty = array();
		$first_online_code	="";
		if($row->delivery_type==0) {
			$res = getErpProdOnlineShopStock($prodcode, $row->colorcode, $row->opt2_name);
			if ($res['p_err_code'] == '0') {
				if ($res['p_data']) {
					$f	= 0;
					foreach($res['p_data'] as $key => $val) {
						if ($f==0) $first_online_code	= $key;
						$online_qty[$key]	= $val;
						$f++;
					}
				}
			}
		}
			
		$A1801_qty	= $online_qty['A1801']?$online_qty['A1801']:0;
		$A1770_qty	= $online_qty['A1770']?$online_qty['A1770']:0;
		$A1771_qty	= $online_qty['A1771']?$online_qty['A1771']:0;
		$first_online_code	= $first_online_code?$first_online_code:"-";

		$sizecode	= $row->opt2_name?$row->opt2_name:"";
		
		// 옵션
		$option					= "";
		$text_opt_subject	= $row->text_opt_subject;
		$text_opt_content	= $row->text_opt_content;
		$tmp_opt1				= explode("@#", $row->opt1_name);
		$tmp_opt2				= option_slice2( $row->opt2_name, '0' );

		$options = array();
		if($row->opt1_name) {
			for($i=0; $i < count($tmp_opt1); $i++) {
				$options[$idx] .= $options[$idx]?" / ".$tmp_opt1[$i]." : ".$tmp_opt2[$i]:$tmp_opt1[$i]." : ".$tmp_opt2[$i];
			}
		} else {
			$options[$idx] = "-";
		}

		$add_opt = '';
		if($text_opt_content) {
			$tmp_subject = option_slice2(  $text_opt_subject, '1' );
			$tmp_content = option_slice2(  $text_opt_content, '1' );
			for($i=0; $i < count($tmp_subject); $i++) {
				$add_opt .= $add_opt?" / ".$tmp_subject[$i]." : ".$tmp_content[$i]:$tmp_subject[$i]." : ".$tmp_content[$i];
			}
		}
		$option = $options[$idx].($add_opt?" / ".$add_opt:$add_opt);																																								// 옵션
		
		if ($item_type == 'order_cancel_34_change_40' || $item_type == 'order_cancel_34_change_41' || $item_type == 'cs_order_cancel_34_change_41') {

			$change_prodcode				= $row->prodcode;							// 교환 ERP 상품코드
			$change_colorcode				= $row->colorcode;							// 교환 ERP 색상코드
			$change_self_goods_code	= $row->self_goods_code_change;	// 교환 자체상품코드

			if($row->tor_dt!=null || $row->tor_dt !=''){
				$tor_dt			= substr($row->tor_dt, 0, 12);
				$tor_dt			= substr($tor_dt,0,4)."-".substr($tor_dt,4,2)."-".substr($tor_dt,6,2)." (".substr($row->tor_dt,8,2).":".substr($row->tor_dt,10,2).")";	
			}else{
				$tor_dt			='';
			}
			
			
			// 교환옵션
			$change_option					= "";
			$change_text_opt_subject	= $row->text_opt_subject_change;
			$change_text_opt_content	= $row->text_opt_content_change;
			$change_tmp_opt1				= explode("@#", $row->opt1_change);
			$change_tmp_opt2				= option_slice2( $row->opt2_change, '0' );

			$change_options = array();
			if($row->opt1_change) {
				for($i=0; $i < count($change_tmp_opt1); $i++) {
					$change_options[$idx] .= $change_options[$idx]?" / ".$change_tmp_opt1[$i]." : ".$change_tmp_opt2[$i]:$change_tmp_opt1[$i]." : ".$change_tmp_opt2[$i];
				}
			} else {
				$change_options[$idx] = "-";
			}

			$change_add_opt = '';
			if($change_text_opt_content) {
				$change_tmp_subject = option_slice2(  $change_text_opt_subject, '1' );
				$change_tmp_content = option_slice2(  $change_text_opt_content, '1' );
				for($i=0; $i < count($change_tmp_subject); $i++) {
					$change_add_opt .= $change_add_opt?" / ".$change_tmp_subject[$i]." : ".$change_tmp_content[$i]:$change_tmp_subject[$i]." : ".$change_tmp_content[$i];
				}
			}
			$change_option = $change_options[$idx].($change_add_opt?" / ".$change_add_opt:$change_add_opt);																														// 교환옵션
			
			// 교환옵션 가격 및 교환상품결제가
			$change_option_price=$change_totprice=0;
					
			$opt_price_chn	= 0;

			if ($row->option_price_text_change) {
				$optc_arr	= explode("||", $row->option_price_text_change);
				for($i-0;$i < count($optc_arr);$i++) {
					$opt_price_chn = $opt_price_chn + $optc_arr[$i];
				}

				$change_option_price	= $opt_price_chn * $row->option_quantity;																													// 교환 옵션가
				$change_totprice			= (($row->price + $opt_price_chn)*$row->option_quantity)-$row->coupon_price-$row->use_point-$row->use_epoint+$row->deli_price;		// 교환상품결제가
			}
			if(!$change_totprice) $change_totprice			= ($row->price*$row->option_quantity)-$row->coupon_price-$row->use_point-$row->use_epoint+$row->deli_price;		// 교환상품결제가
			
		}

		// 결제방법
		if(strstr("Y", $row->paymethod[0])) {	//PAYCO
			$paymethod	= "PAYCO";
		} elseif(strstr("B", $row->paymethod[0])) {	//무통장
			$paymethod	= "무통장";
			if (strlen($row->bank_date)==9 && $row->bank_date[8]=="X") $pay="환불";
			elseif (ord($row->bank_date)) $pay="입금완료";
			else $pay="미입금";
		} elseif(strstr("V", $row->paymethod[0])) {	//계좌이체
			$paymethod	= "실시간계좌이체";
			if (strcmp($row->pay_flag,"0000")!=0) $pay="결제실패";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="환불";
			elseif ($row->pay_flag=="0000") $pay="결제완료";
		} elseif(strstr("M", $row->paymethod[0])) {	//핸드폰
			$paymethod	= "핸드폰결제";
			if (strcmp($row->pay_flag,"0000")!=0) $pay="결제실패";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="취소완료";
			elseif ($row->pay_flag=="0000") $pay="결제완료";
		} elseif(strstr("OQ", $row->paymethod[0])) {	//가상계좌
			if(strstr("O", $row->paymethod[0])) $paymethod	= "가상계좌";
			elseif(strstr("Q", $row->paymethod[0])) $paymethod	= "가상계좌(매매보호)";
			if (strcmp($row->pay_flag,"0000")!=0) $pay="주문실패";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="환불";
			elseif ($row->pay_flag=="0000" && ord($row->bank_date)==0) $pay="미입금";
			elseif ($row->pay_flag=="0000" && ord($row->bank_date)) $pay="입금완료";
		} else {
			if(strstr("C", $row->paymethod[0])) $paymethod	= "신용카드";
			elseif(strstr("P", $row->paymethod[0])) $paymethod	= "신용카드(매매보호)";
			if (strcmp($row->pay_flag,"0000")!=0) $pay="카드실패";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") $pay="카드승인";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") $pay="결제완료";
			elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="취소완료";
		}

		//$paymethod			= $paymethod."[{$pay}]";	
		$bank_date					= substr($row->bank_date, 0, 8);																												// 입금일

		$price						= $row->price;																																		// 금액
		$sell_price					= $row->sell_price;																																	// 판매가
		$quantity					= $row->quantity;																																	// 수량
		if($mode =='orderinfo') {
			$coupon_price		= $row->dc_price;																																	// 쿠폰할인
			$usereserve			= $row->reserve;																																	// 사용포인트
			$usepoint			= $row->point;																																	// 사용E포인트
			$totprice					= $row->price-$row->dc_price-$row->reserve-$row->point+$row->deli_price;																// 실결제금액
			$deli_gbn				= $o_step[$row->oi_step1][$row->oi_step2];																								// 처리단계
		} else if($mode =='orderproduct' || $mode =='ordercancel') {
			$coupon_price		= $row->coupon_price;																															// 쿠폰할인
			$usereserve			= $row->use_point;																																	// 사용포인트
			$usepoint			= $row->use_epoint;																																	// 사용E포인트
			$totprice					= ($row->price*$row->option_quantity)-$row->coupon_price-$row->use_point-$row->use_epoint+$row->deli_price;					// 실결제금액
			$deli_gbn				= $op_step[$row->op_step];																													// 처리단계
			$deli_gbn2				= $o_step[$row->oi_step1][$row->op_step];																								// 처리단계
		}
		$deli_price					= $row->deli_price;																																	// 배송비
		$receiver_name			= $row->receiver_name;																															// 수령자
		$receiver_tel1				= $row->receiver_tel1;																																// 전화번호
		$receiver_tel2				= $row->receiver_tel2;																																// 비상전화
		$receiver_addr			= explode("주소 : ",str_replace("↑=↑"," ",str_replace("\n","",str_replace("\r\n","",$row->receiver_addr))));		// 우편번호+주소
		$post2						= str_replace("우편번호 : ","",$receiver_addr[0]);																							// 우편번호

		$addr							= $receiver_addr[1];																																// 주소
		$is_mobile					= $arr_mobile[$row->is_mobile];																												// 유입경로
		//$staff_sale_price			= (($row->ori_price-$row->price)*$row->option_quantity);																												// 임직원 할인
		$staff_sale_price			= $row->staff_price;																												// 임직원 할인
		$cooper_sale_price			= $row->cooper_price;																												// 협력사 할인
		$ord_msg2					= $row->order_msg2;																																// 비고

		$rg_date						= substr($row->reg_dt,0,4)."/".substr($row->reg_dt,4,2)."/".substr($row->reg_dt,6,2)." (".substr($row->reg_dt,8,2).":".substr($row->reg_dt,10,2).")";	// 환불접수일
		$rg_date2					= substr($row->regdt,0,4)."/".substr($row->regdt,4,2)."/".substr($row->regdt,6,2)." (".substr($row->regdt,8,2).":".substr($row->regdt,10,2).")";			// 반품요청일
		$rg_date3					= substr($row->regdt,0,4)."/".substr($row->regdt,4,2)."/".substr($row->regdt,6,2)." (".substr($row->regdt,8,2).":".substr($row->regdt,10,2).")";			// 교환요청일
		$fin_date					= substr($row->fin_dt,0,4)."/".substr($row->fin_dt,4,2)."/".substr($row->fin_dt,6,2)." (".substr($row->fin_dt,8,2).":".substr($row->fin_dt,10,2).")";		// 환불완료일
		if ($item_type	== 'order_delivery'){
			$deli_company="CJ대한통운택배";
		}
		if($row->delivery_type=="1"){
			$delivery_type="매장픽업";
		}else if($row->delivery_type=="2"){
			$delivery_type="매장발송";
		}else if($row->delivery_type=="3"){
			$delivery_type="당일수령";
		}else{
			$delivery_type="일반택배";
		}

		if ($mode =='ordercancel') {
			list($main_order_pridx)=pmysql_fetch("SELECT idx from tblorderproduct WHERE oc_no='{$row->oc_no}' GROUP BY idx, productcode, vender, productname ORDER BY idx, productcode, vender, productname");	
			list($main_sum_totprice)=pmysql_fetch("SELECT SUM( ((price + option_price) * option_quantity) - coupon_price - use_point - use_epoint + deli_price ) AS sum_totprice from tblorderproduct WHERE oc_no='{$row->oc_no}' GROUP BY oc_no");	
			list($oc_count)=pmysql_fetch("SELECT count(*) as oc_count from tblorderproduct WHERE oc_no='{$row->oc_no}' ");			
			list($tot_count)=pmysql_fetch("SELECT count(*) as tot_count from tblorderproduct WHERE ordercode = '{$row->ordercode}' ");	
			$occode				= $oc_code[$row->code];																														// 사유
			$option_price			= $row->option_price*$row->option_quantity;																								// 옵션가격
			$can_ord_quantity	= "'".$oc_count."/".$tot_count."'";																												// 취소수량/주문수량
			$ref_bankcode		= $oc_bankcode[$row->bankcode];																											// 환불은행
			$ref_bankaccount	= $row->bankaccount;																																// 환불계좌
			$ref_bankuser			= $row->bankuser;																																	// 예금주
			$ref_tot_price			= ($row->price*$row->option_quantity)-$row->coupon_price-$row->use_point-$row->use_epoint+$row->deli_price;					// 환불예정금액(상품결제단가)	

			
			if ($row->rfee	 > 0) {
				if($oc_count > 1) {
					if ($row->idx == $main_order_pridx) {
						$ocp_sql="SELECT idx, ((price + option_price) * option_quantity) - coupon_price - use_point - use_epoint + deli_price AS sum_totprice from tblorderproduct WHERE oc_no='{$row->oc_no}'";
						//echo $ocp_sql;
						$ocp_result = pmysql_query($ocp_sql,get_db_conn());
						$sum_ocp_fee	= $main_sum_totprice;
						while($ocp_row=pmysql_fetch_object($ocp_result)) {
							$ocp_fee[$ocp_row->idx] = $row->rfee * (round(($ocp_row->sum_totprice/$main_sum_totprice)*100)*0.01);
							$ocp[$ocp_row->idx] = $ocp_row->sum_totprice - $ocp_fee[$ocp_row->idx];
							if ($ocp_row->sum_totprice <  $ocp[$ocp_row->idx]) $ocp[$ocp_row->idx] = $ocp_row->sum_totprice;
							$sum_ocp_fee = $sum_ocp_fee - $ocp[$ocp_row->idx];
							if ($sum_ocp_fee != 0) {
								$ocp[$main_order_pridx] + $sum_ocp_fee;
							}
						}
						pmysql_free_result($ocp_result);
					} else {
						$ref_rfee					= $ocp_fee[$row->idx];																												// 환불 수수료	
					}
				} else {
					$ref_rfee					= $row->rfee;																																	// 환불 수수료
					$ocp[$row->idx] = (($row->price*$row->option_quantity)-$row->coupon_price-$row->use_point-$row->use_epoint+$row->deli_price) - $row->rfee;
				}
			} else {
				$ref_rfee					= $row->rfee;																																		// 환불 수수료
				$ocp[$row->idx] = ($row->price*$row->option_quantity)-$row->coupon_price-$row->use_point-$row->use_epoint+$row->deli_price;
			}

			if ($row->rprice > 0) {
				$ref_rprice			= $ocp[$row->idx];																																	// 최종 환불금액 (실결제금액 - 환불수수료)
			} else {
				$ref_rprice			= $ocp[$row->idx];																																	// 최종 환불금액 (실결제금액 - 환불수수료)
			}

			if ($row->paymethod[0] == 'C') {		// 카드결제일 경우
				if($oc_count == $tot_count) {			// 전체취소시
					if ($row->pgcancel == 'N') {
						$pg_cancel	= "-";																																					// 취소처리
					} else if ($row->pgcancel == 'Y') {
						$pg_cancel	= "전체취소 완료";																																// 취소처리
					}
				} else {										// 부분취소시
					if ($row->pgcancel == 'N') {
						$pg_cancel	= "-";																																					// 취소처리
					} else if ($row->pgcancel == 'Y') {
						$pg_cancel	= "부분취소 완료";																																// 취소처리
					}
				}
			} else { // 그외
				$pg_cancel	= "카드결제건이 아닙니다.";																															// 취소처리
			}

			$rdesc			= $row->rdesc;																																				// 메모
		}

		$redelivery_type = $row->redelivery_type;
		$oc_step = $row->oc_step;
		$hold_oc_step = $row->hold_oc_step;

		$status_txt	= "";
		if ($redelivery_type == 'N') $status_def = "<font color='blue'>취소";
		if ($redelivery_type == 'Y') $status_def = "<font color='blue'>반품";
		if ($redelivery_type == 'G') $status_def = "<font color='blue'>교환";

		if ($oc_step == '0') $status_txt = $status_def."신청";
		if ($oc_step == '1') $status_txt = $status_def."접수";
		if ($oc_step == '2') $status_txt = "제품도착";
		if ($oc_step == '3') $status_txt = $status_def."승인";
		if ($oc_step == '4') $status_txt = $status_def."완료";
		if ($oc_step == '5') {
			$status_txt = $status_def."보류";
			if ($hold_oc_step == '0') $status_txt .= "<br><span style='font-size:11px'>(".$status_def."신청)</span>";
			if ($hold_oc_step == '1') $status_txt .= "<br><span style='font-size:11px'>(".$status_def."접수)</span>";
			if ($hold_oc_step == '2') $status_txt .= "<br><span style='font-size:11px'>(제품도착)</span>";
			if ($hold_oc_step == '3') $status_txt .= "<br><span style='font-size:11px'>(".$status_def."승인)</span>";
		}
		if ($oc_step == '6') $status_txt = $status_def."철회";

		$status_txt .="</font><br/>";

		if($redeliverytype=="G"){
			$redeliveryname="교환";
			$redeliverypop="rechange";
		}else if($redeliverytype=="Y"){
			$redeliveryname="반품";
			$redeliverypop="regoods";
		}

		$oc_reg_type="-";
		if ($row->reg_type =='admin') {
			$oc_reg_type="CS".$redeliveryname;
		} else if ($row->reg_type =='user') {
			$oc_reg_type="고객".$redeliveryname;
		} else if ($row->reg_type =='api') {
			$oc_reg_type="API".$redeliveryname;
		} else if ($row->reg_type =='pg') {
			$oc_reg_type="PG".$redeliveryname;
		}
		if($ord_status){
			$status_txt .="(".$oc_reg_type.")<br/>".$ord_status;
		}else{
			$status_txt .="(".$oc_reg_type.")";
		}
		if($row->accept_status == 'Y'){
			$cancel_type=$status_txt."(<br/><font color='red'>접수</font>)";
		}else{
			$cancel_type=$status_txt;
		}

		$xls_data	.= "<tr>";
		$i	= 0;
		foreach ( $est as $key => $val ) {
			if ( $fields[$val] !='' && $fields[$val]['down'] == 'Y' ) {				
				$xls_data	.= "<td";
				if($val=='productcode'||$val=='self_goods_code'||$val=='prodcode'||$val=='colorcode'||$val=='change_self_goods_code'||$val=='change_prodcode'||$val=='change_colorcode'||$val=='post2') $xls_data	.= " style=mso-number-format:'\@'";
				$xls_data	.= ">";
				$xls_data	.= doubleQuote(${$val});
				$xls_data	.= "</td>";

				if($i!=0) $csv_data	.= ",";
				if($val=='productcode'||$val=='self_goods_code'||$val=='prodcode'||$val=='colorcode'||$val=='change_self_goods_code'||$val=='change_prodcode'||$val=='change_colorcode'||$val=='post2') $csv_data .= "=";
				$csv_data	.= '"' . doubleQuote(iconv('UTF-8', 'EUC-KR', ${$val})) . '"';
				$i++;
			}
		}
		$xls_data	.= "</tr>";
		$csv_data	.= "\n";
	}

	pmysql_free_result($result);

	$xls_data	.= "</table>";
	$xls_data	.= "</body></html>";

	if ($file_ext == 'xls') echo $xls_data;
	if ($file_ext == 'csv') echo $csv_data;
?>