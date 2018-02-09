<?
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/product.class.php");
# 상품정보를 세팅해주는 부분의 php 따로 정리
# 2015 12 21 유동혁
include_once("order_check.php");


############################################# 여기까지 작업완료 ############################################

/* 상품 개별 쿠폰 내용 저장 */
if(count($coupon_code_goods) > 0 && $sumprice > 0){
	$couponData = array();
	foreach($coupon_code_goods as $couponGoods){
		$arrCouponGoods = explode("||", $couponGoods);
		$goods_cate_sql = "SELECT * FROM tblproductlink WHERE c_productcode = '".$arrCouponGoods[1]."'";
		$goods_cate_result = pmysql_query($goods_cate_sql,get_db_conn());
		$categorycode = array();
		while($goods_cate_row=pmysql_fetch_object($goods_cate_result)) {
			list($cate_a, $cate_b, $cate_c, $cate_d) = sscanf($goods_cate_row->c_category,'%3s%3s%3s%3s');
			$categorycode[] = $cate_a;
			$categorycode[] = $cate_a.$cate_b;
			$categorycode[] = $cate_a.$cate_b.$cate_c;
			$categorycode[] = $cate_a.$cate_b.$cate_c.$cate_d;
		}
		if(count($categorycode) > 0){											
			$addCategoryQuery = "('".implode("', '", $categorycode)."')";
		}else{
			$addCategoryQuery = "('')";
		}

		$date = date("YmdH");
		$sqlGoodsCou = "SELECT 
							a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, 
							a.bank_only, a.productcode,	a.mini_price, a.use_con_type1, 
							a.use_con_type2, a.use_point, a.vender, a.sale_max_money,
							a.amount_floor,	a.coupon_type, a.use_card, 
							b.date_start, b.date_end
						FROM 
							tblcouponinfo a 
							JOIN tblcouponissue b on a.coupon_code=b.coupon_code 
							LEFT JOIN tblcouponproduct c on b.coupon_code=c.coupon_code
							LEFT JOIN tblcouponcategory d on b.coupon_code=d.coupon_code
						WHERE 
							b.id='".$_ShopInfo->getMemid()."' 
							AND b.date_start<='".date("YmdH")."' 
							AND (b.date_end>='".date("YmdH")."' OR b.date_end='') 
							AND a.coupon_code='".$arrCouponGoods[0]."'
							AND b.used='N' 
							AND a.coupon_use_type = '2' 
							AND (c.productcode = '".$arrCouponGoods[1]."' OR (d.categorycode IN ".$addCategoryQuery." AND a.use_con_type2 = 'Y'))
						ORDER BY 
							coupon_use_type 
						ASC";
		$resultGoodsCou = pmysql_query($sqlGoodsCou,get_db_conn());
		if($rowGoodsCou=pmysql_fetch_object($resultGoodsCou)) {
			$goods_coupon_code = $rowGoodsCou->coupon_code;
			$goods_coupon_name = $rowGoodsCou->coupon_name;
			$goods_use_con_type2 = $rowGoodsCou->use_con_type2;
			$goods_sale_type = $rowGoodsCou->sale_type;
			$goods_use_con_type1 = $rowGoodsCou->use_con_type1;
			$goods_sale_money = $rowGoodsCou->sale_money;
			$goods_mini_price = $rowGoodsCou->mini_price;
			$goods_vender = $rowGoodsCou->vender;
			$goods_bank_only = $rowGoodsCou->bank_only;
			$goods_amount_floor = $rowGoodsCou->amount_floor;
			$goods_delivery_type = $rowGoodsCou->delivery_type;
			$goods_delivery_type = $rowGoodsCou->delivery_type;
			$goods_sale_max_money = $rowGoodsCou->sale_max_money;
			$goods_use_card = $rowGoodsCou->use_card;
			if($goods_sale_type <= 2){
				$couponDcPrice = ($aryRealPrice[$arrCouponGoods[1]]*$goods_sale_money)*0.01;
				$couponDcPrice = floor( $couponDcPrice / pow(10, $goods_amount_floor) ) * pow(10, $goods_amount_floor);
			}else{
				$couponDcPrice = $goods_sale_money;
			}
			if($goods_sale_max_money && $goods_sale_max_money < $couponDcPrice){
				$couponDcPrice = $goods_sale_max_money;
			}
			
			$couponmsg = $aryProductName[$arrCouponGoods[1]];

			# 쿠폰 정보 추가 2015 11 18 유동혁
			# 쿠폰은 상품 1당 1만 사용 가능
			$couponData[$arrCouponGoods[1]] = array(
				'ordercode'=>$ordercode,
				'couponcode'=>$rowGoodsCou->coupon_code,
				'productcode'=>$arrCouponGoods[1],
				'dc_price'=>$couponDcPrice,
				'use_card'=>$goods_use_card
			);
			if( strlen( $goods_use_card ) > 0 ){
				$tmp_use_card[] = $goods_use_card;
			}
		}
		pmysql_free_result($resultGoodsCou);
	}
}

$arrProductDeli = array(); //tblorderproduct에 들어가는 배송료
if(count($prDeliPrcie)>0) {
	while(list($key,$val)=each($prDeliPrcie)) {
		$onDeli = array(); // 상품별 배송비가 존재하는 상품array
		$offDeli = array(); // 상품별 배송비가 없는 상품array
		$venDeliPrice = 0; // 밴더별 전체 배송비
		$onDeliCnt = 0;//상품별 배송비 count

		if($val>0) {
			//벤더별 배송비용을 확인한다 재가공 한다
			foreach( $val as $deliKey=>$deliVal ){
				if( $deliKey!= 'deli' && $deliVal > 0 ){
					$onDeli[] = $deliKey;
				} else if ( $deliKey!= 'deli' && $deliVal == 0 ) {
					$offDeli[] = $deliKey;
				} else {
					$venDeliPrice = $deliVal;
				}
			}
			//밴더가 정한 배송비 정책에 걸리는 상품 중 첫번째 상품에 배송비를 넣어준다
			if( $venDeliPrice > 0 ){
				$prDeliPrcie[$key][$offDeli[0]] += $venDeliPrice;
				unset($prDeliPrcie[$key]['deli']);
			}
			//상품별 배송비로 재가공 한다
			foreach( $prDeliPrcie[$key] as $deliKey=>$deliVal ){
				$arrProductDeli[$deliKey] = $deliVal;
			}
			
			//개별 배송비를 배송비 테이블에 넣는다
			$onDeliCnt = count( $onDeli );
			if( $onDeliCnt > 0 ){
				for( $dKey = 0; $dKey < $onDeliCnt; $dKey++ ){
					$sql = "INSERT INTO tblorder_delivery ( vender, ordercode, product, deli_price, date ) ";
					$sql.= "VALUES ( '".$key."', '".$ordercode."', '".$onDeli[$dKey]."', '".$val[$onDeli[$dKey]]."', '".date("YmdHis")."' ) ";
					pmysql_query( $sql, get_db_conn() );
				}
			}
			// 전체 배송비를 묶어서 테이블에 넣는다
			if( count( $offDeli ) > 0 && $venDeliPrice > 0 ){
				$offDeliStr = implode( ',' , $offDeli );
				$sql = "INSERT INTO tblorder_delivery ( vender, ordercode, product, deli_price, date ) ";
				$sql.= "VALUES ( '".$key."', '".$ordercode."', '".$offDeliStr."', '".$venDeliPrice."', '".date("YmdHis")."' ) ";
				pmysql_query( $sql, get_db_conn() );
			}
		}
	}
}

//업체별 배송료 tblorderproducttemp 테이블에 insert
/*
if(count($arr_deliprice)>0) {
	while(list($key,$val)=each($arr_deliprice)) {
		if($val>0) {
			$deli_type_tag = $deli_type=="1"?"(착불)":"(선불)";
			$sql = "INSERT INTO tblorderproducttemp (vender, ordercode, tempkey, productcode, productname, quantity, price, reserve, date, order_prmsg) VALUES ('{$key}','{$ordercode}','{$oldtempkey}','99999999990X','배송료-{$deli_type_tag} ({$arr_delisubj[$key]})','1','{$val}','0','".date("Ymd")."','{$arr_delimsg[$key]}')";
			pmysql_query($sql,get_db_conn());
			backup_save_sql($sql);
		}
	}
}
*/


//주문 장바구니에 상품 복사
for($orderi=0;$orderi<$count;$orderi++) {
	$optTotalPrice = 0;
	$oprDcPrice = 0;
	$prDeliPrice = 0;

	//if($reserve_useadd!="N" && $usereserve>=$reserve_useadd && $usereserve!=0) $realreserve[$orderi]=0;
	if($reserve_useadd!="N" && $usereserve>=$reserve_useadd && $usereserve!=0 || !$_ShopInfo->getMemid()) $realreserve[$orderi]=0;
	else if($reserve_useadd=="U" && $usereserve!=0) {
		$reservepercent = 100 * ($sumprice-$usereserve) / $sumprice;
		$realreserve[$orderi]=round($realreserve[$orderi]*($reservepercent/100),-1);
	}

		
	//옵션 상태 추가
	// 2015 11 10 유동혁
	// 옵션 수량이 존재 안할경우 처리
	if( is_null($optQuantity[$orderi]) || $optQuantity[$orderi] == '' ) $optQuantity[$orderi] = 0;
	if( is_null($optType[$orderi]) || $optType[$orderi] == '' ) $optType[$orderi] = 0;

	//쿠폰 정보 추가
	if( count( $couponData[$productcode[$orderi]] ) > 0 ){
		$oprDcPrice = $couponData[$productcode[$orderi]]['dc_price'];
		$dc_price += $oprDcPrice;
		$orderCouponSql = "INSERT INTO tblcoupon_order ( ordercode, coupon_code, productcode, date ) ";
		$orderCouponSql.= "VALUES ( '".$ordercode."', '".$couponData[$productcode[$orderi]]['couponcode']."', '".$productcode[$orderi]."', '".date("YmdHis")."' ) ";
		pmysql_query( $orderCouponSql, get_db_conn() );
		unset( $couponData[$productcode[$orderi]] );
	}

	//배송 정보 추가
	
	if( !is_null( $arrProductDeli[$productcode[$orderi]] ) && $optType[$orderi] == '0' ){
		$prDeliPrice = $arrProductDeli[$productcode[$orderi]];
	}

	# 상품 수량의 기준은 option_quantity로 한다
	# 옵션이 존재하지 않는 상품도 option_quantity에 넣는다

	$sql = "INSERT INTO tblorderproducttemp ( ";
	$sql.= "vender, ordercode, tempkey, productcode, ";
	$sql.= "productname, opt1_name, opt2_name, package_idx, ";
	$sql.= "assemble_idx, addcode, quantity, price, ";
	$sql.= "reserve, date, selfcode, productbisiness, ";
	$sql.= "order_prmsg, assemble_info, option_price, ";
	$sql.= "option_quantity, option_type, coupon_price, deli_price ";
	$sql.= " ) VALUES ( ";
	$sql.= " '{$vender[$orderi]}', '{$ordercode}', '".$_ShopInfo->getTempkey()."', '{$productcode[$orderi]}', ";
	$sql.= " '{$productname[$orderi]}', '{$option1[$orderi]}', '{$option2[$orderi]}', '{$package_idx[$orderi]}', ";
	$sql.= " '{$assemble_idx[$orderi]}', '{$addcode[$orderi]}', '{$quantity[$orderi]}', '{$realprice[$orderi]}', ";
	$sql.= " '{$realreserve[$orderi]}', '{$date[$orderi]}', '{$selfcode[$orderi]}', '{$companyviewval[$bisinesscode[$orderi]]}', ";
	$sql.= " '{$ordermessage[$orderi]}', '{$package_info[$orderi]}={$assemble_info[$orderi]}', '".$optPrice[$orderi]."', ";
	$sql.= " '".$optQuantity[$orderi]."', '".$optType[$orderi]."', '".$oprDcPrice."', '".$prDeliPrice."' ";
	$sql.= " )";
	pmysql_query($sql,get_db_conn());
	backup_save_sql($sql);

	if (pmysql_errno()) {
		backup_save_sql($sql);
		sendmail(AdminMail,"[긴급!] INSERT ERROR",$_SERVER['HTTP_HOST']."<br>$sql - ".pmysql_error(),"Content-Type: text/plain\r\n");
	}
}

delete_cache_file("main");

$oldtempkey=$_ShopInfo->getTempkey();
$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
$_ShopInfo->setGifttempkey($oldtempkey);
$_ShopInfo->setOldtempkey($oldtempkey);
$_ShopInfo->setOkpayment("");
$_ShopInfo->Save();

if ($paymethod=="B") $pay_data = $pay_data1;
else if (strstr("CP", $paymethod)) $pay_data = $pay_data2;
else if ($paymethod=="V") $pay_data = "실시간 계좌이체 결제중";
if($_data->ETCTYPE["VATUSE"]=="Y") { 
	$sumpricevat = return_vat($sumprice);
}

//구매금액대별 추가할인
$tot_price_dc=0;
$tot_dc_per=getTotalPriceDc($bf_sumprice);
if($tot_dc_per)$tot_price_dc=round($sumprice*$tot_dc_per/100,-1,PHP_ROUND_HALF_DOWN);

//상품구매가격(할인율 적용)에서 금액할인가 차감
$sumprice = $sumprice - $tot_price_dc;

// 배송가격은 합산하지 않는다
// 2015 11 19 유동혁
//if($deli_type=="0" && $deli_price>0) $sumprice+=$deli_price;

if($_data->ETCTYPE["VATUSE"]=="Y") { 
	if($sumpricevat>0) {
		$sumprice+=$sumpricevat;
		$sql = "INSERT INTO tblorderproducttemp (ordercode, tempkey, productcode, productname, quantity, price, reserve, date) VALUES ('{$ordercode}','{$oldtempkey}','99999999997X','부가세 VAT 10% 부과','1','{$sumpricevat}','0','".date("Ymd")."')";
		pmysql_query($sql,get_db_conn());
		backup_save_sql($sql);
	}
}
if (strstr("CPM", $paymethod) && $_data->card_payfee>0) {  // 카드결제시 추가 수수료 적용
	$tempprice = ((int) ($sumprice * ($_data->card_payfee/100) /100)) * 100;
	$sumprice+=$tempprice;
	$sql = "INSERT INTO tblorderproducttemp (ordercode, tempkey, productcode, productname, quantity, price, reserve, date) VALUES ('{$ordercode}','{$oldtempkey}','99999999998X','카드결제시 금액에서 {$_data->card_payfee}% 수수료 부과','1','{$tempprice}','0','".date("Ymd")."')";
	pmysql_query($sql,get_db_conn());
	backup_save_sql($sql);
} else if (strstr("BVOQ",$paymethod) && $_data->card_payfee<0 && $sumprice>$usereserve) {
	// 현금결제시 할인율 적용 & 적립금액만으로 결제시제외
	if($paymethod=="Q" && $escrow_info["esbank"]=="Y") {
		;
	} else {
		if($_data->card_payfee<-50){
			$_data->card_payfee+=50;
			$saletype="Y";
		}
		$_data->card_payfee=abs($_data->card_payfee);
		$dctemp = floor(($sumprice-$deli_price)/100*$_data->card_payfee/100)*100;
		if($saletype=="Y" && ord($_ShopInfo->getMemid())>0) {
			$sql = "INSERT INTO tblorderproducttemp (ordercode, tempkey, productcode, productname, quantity, price, reserve, date) VALUES ('{$ordercode}','{$oldtempkey}','99999999999X','현금결제시 결제금액에서 {$_data->card_payfee}% 추가 적립','1','0','{$dctemp}','".date("Ymd")."')";
			pmysql_query($sql,get_db_conn());
			backup_save_sql($sql);
		} else if($saletype!="Y") {
			$sumprice = $sumprice - $dctemp;
			$sql = "INSERT INTO tblorderproducttemp (ordercode, tempkey, productcode, productname, quantity, price, reserve, date) VALUES ('{$ordercode}','{$oldtempkey}','99999999999X','현금결제시 결제금액에서 {$_data->card_payfee}% 추가 할인','1',".-$dctemp.",'0','".date("Ymd")."')";
			pmysql_query($sql,get_db_conn());
			backup_save_sql($sql);
		}
	}
}

if($dc_price=='')$dc_price=0;
if($mem_reserve=='')$mem_reserve=0;
# 적립금은 따로 넣는다
# 2015 11 19 유동혁
//$last_price = $sumprice - $usereserve;
$last_price = $sumprice;

if ($paymethod=="Q" && $escrow_info["percent"]>0) {  // 에스크로 결제시 추가 수수료 적용
	$templast_price = ((int) ($last_price * ($escrow_info["percent"]/100) /10)) * 10;
	if($templast_price<300) $templast_price=300;
	$last_price+=$templast_price;
	$sql = "INSERT INTO tblorderproducttemp (ordercode, tempkey, productcode, productname, quantity, price, reserve, date) VALUES ('{$ordercode}','{$oldtempkey}','99999999998X','에스크로 결제시 금액에서 {$escrow_info['percent']}% 수수료 부과','1','{$templast_price}','0','".date("Ymd")."')";
	pmysql_query($sql,get_db_conn());
	backup_save_sql($sql);
}

$deli_price=$deli_type=="1"?0:$deli_price;

if(ord($_ShopInfo->getMemid())==0) {

	$sql = "INSERT INTO tblorderinfotemp (tot_price_dc, ordercode, tempkey, id, price, deli_price, paymethod, ";
	$sql.= "pay_data, sender_name, sender_email, sender_tel, receiver_name, receiver_tel1, receiver_tel2, ";
	$sql.= "receiver_addr, order_msg, ip, del_gbn, partner_id, loc, bank_sender, receipt_yn, order_msg2, deli_type, ";
	$sql.= "overseas_code, is_mobile ) VALUES ( ";
	$sql.= "'{$tot_price_dc}', '{$ordercode}', '{$oldtempkey}', '{$id}', '{$last_price}', ";
	$sql.= "'{$deli_price}', '{$pmethod}', '{$pay_data}', '{$sender_name}', '{$sender_email}', ";
	$sql.= "'{$sender_tel}', '{$receiver_name}', '{$receiver_tel1}', '{$receiver_tel2}', ";
	$sql.= "'{$receiver_addr}', '{$order_msg}', '{$ip}', 'N', '".$_ShopInfo->getRefurl()."', '{$loc}', '{$bank_sender}', '{$receipt_yn}', '".$ordermessage[0]."', '".$deli_type."', '{$overseas_code}', '1' )";
	pmysql_query($sql,get_db_conn());
	backup_save_sql($sql);
	if (pmysql_errno()) {
		backup_save_sql($order_msg);
		sendmail(AdminMail,"[긴급!] INSERT ERROR",$_SERVER['HTTP_HOST']."<br>$sql - ".pmysql_error(),"Content-Type: text/plain\r\n");
	}
} else {
	if($sumprice<=$usereserve) {
		$remain_reserve = $user_reserve - $sumprice;
		$usereserve = $sumprice;
	} else {
		$remain_reserve=$user_reserve-$usereserve;
	}
	if ($last_price<0) $last_price=0;

	$sql = "INSERT INTO tblorderinfotemp (tot_price_dc, ordercode, tempkey, id, price, deli_price, dc_price, mem_reserve, ";
	$sql.= "reserve, paymethod, pay_data, ";
	if($last_price==0) {
		$pay_data="총 구매금액 ".number_format($usereserve)."원을 적립금으로 구매";
		$sql.= "bank_date, ";
		if(strstr("OQ", $paymethod)) $sql.= "pay_flag, ";	//가상계좌만,,,
	}
	$sql.= "sender_name, sender_email, sender_tel, receiver_name, receiver_tel1, receiver_tel2, ";
	$sql.= "receiver_addr, order_msg, ip, del_gbn, partner_id, loc, bank_sender, receipt_yn, order_msg2, deli_type, ";
	$sql.= "overseas_code, is_mobile ) VALUES ( ";
	$sql.= "'{$tot_price_dc}', '{$ordercode}', '{$oldtempkey}', '".$_ShopInfo->getMemid()."', ";
	$sql.= "'{$last_price}', '{$deli_price}', '{$dc_price}', '{$mem_reserve}', '{$usereserve}', '{$pmethod}', ";
	$sql.= "'{$pay_data}', ";
	if($last_price==0) {
		$sql.= "'".date("YmdHis")."', ";
		if(strstr("OQ", $paymethod)) $sql.= "'0000', ";	//가상계좌만,,,
	}
	$sql.= "'{$sender_name}', '{$sender_email}', ";
	$sql.= "'{$sender_tel}', '{$receiver_name}', '{$receiver_tel1}', '{$receiver_tel2}', ";
	$sql.= "'{$receiver_addr}', '{$order_msg}', '{$ip}', 'N', '".$_ShopInfo->getRefurl()."', '{$loc}', '{$bank_sender}', '{$receipt_yn}', '".$ordermessage[0]."', '".$deli_type."', '{$overseas_code}', '1' )";
	pmysql_query($sql,get_db_conn());
	backup_save_sql($sql);
	if (pmysql_errno()) {
		backup_save_sql($order_msg);
		sendmail(AdminMail,"[긴급!] INSERT ERROR",$_SERVER['HTTP_HOST']."<br>$sql - ".pmysql_error(),"Content-Type: text/plain\r\n");
	}
}

// 남은 적립금은 다시 넣어 주거나 없앤다.
/*	2014-02-21 결제처리부분에서 빼줌
	if(ord($_ShopInfo->getMemid())>0 && $reserve_type=="Y" && $_data->reserve_maxuse>=0) {
		$sql = "UPDATE tblmember SET reserve=$remain_reserve";
		if($usereserve>=3000){
			$sql.=" , reserve_chk='1' ";
		}
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		pmysql_query($sql,get_db_conn());
	}
*/

//사은품
if($gift_sel){
	$productcode_gift=sprintf("%'98d",0)."GIFT";
	$gift_sql = "SELECT * FROM tblgiftinfo WHERE gift_regdate='".$gift_sel."'";

	$gift_res=pmysql_query($gift_sql,get_db_conn());
	if($gift_row=pmysql_fetch_object($gift_res)) {
		
		$sql_ins = "INSERT INTO tblorderproduct (
		ordercode	,
		tempkey		,
		productcode	,
		productname	,
		quantity	,
		price		,
		date		) VALUES (
		'{$ordercode}', 
		'".$_ShopInfo->getGifttempkey()."', 
		'".$productcode_gift."', 
		'사은품 - ".addslashes($gift_row->gift_name)."', 
		'1', 
		'0', 
		'".date("Ymd")."')";

		pmysql_query($sql_ins,get_db_conn());
		
		if (ord($gift_row->gift_quantity) && $gift_row->gift_quantity>0) {
			pmysql_query("UPDATE tblgiftinfo SET gift_quantity=(gift_quantity-1) WHERE gift_regdate='".$gift_sel."'",get_db_conn());
		}
		
	}
	pmysql_free_result($gift_res);

}


# 주문 금액 최종 ( ordersend에서는 가격을 나누어서 넣는다 )
// 2015 11 19 유동혁
$last_price = $sumprice + $deli_price - $usereserve - $dc_price;

#카드쿠폰 추가
if( count( $tmp_use_card ) == 1 ){
	$use_card = $tmp_use_card[0];
	$used_card_yn = 'Y';
} else if( count( $tmp_use_card ) > 1 ){
	$use_card = implode( ':', $tmp_use_card );
	$used_card_yn = 'Y';
} else {
	$used_card_yn = 'N';
}


if($paymethod!="B") {
	########### 결제시스템 연결 시작 ##########
	//include("paylist_kcp.php");
	//exit;

	$mobile_agent = '/(iPod|iPhone|iPad|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/';
	if(preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])) {
		echo "<script>
			if(parent.document.getElementsByName('good_mny')[0].value == '".$last_price."'){
				parent.kcp_AJAX();
			}else{
				alert('결제금액이 올바르지 않습니다.');
				//parent.location.replace('basket.php');
			}
			</script>";
	} else {
		echo "<script>
				alert('모바일 기기 접속이 아닙니다.');
				parent.location.replace('basket.php');
			</script>";
	}


	########### 결제시스템 연결 끝   ##########
	exit;
}

########### 최종 마무리 ###########
include("payresult.php");
########### 최종 마무리 끝 ########
