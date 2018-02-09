<?
	#header('Content-Type: application/json');
	header("Content-Type: text/plain");
	header("Content-Type: text/html; charset=euc-kr");

	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	
	$productcode = $_GET['productcode'];
	$price = $_GET['price'];	
	$arrCouponGoods = explode("||", $couponGoods);
	$goods_cate_sql = "SELECT * FROM tblproductlink WHERE c_productcode = '".$productcode."'";
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
											a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, a.bank_only, a.productcode,
											a.mini_price,a.use_con_type1,a.use_con_type2,a.use_point,a.vender, b.date_start, b.date_end, a.sale_max_money, a.amount_floor, a.coupon_use_type
										FROM 
											tblcouponinfo a 
											JOIN tblcouponissue b on a.coupon_code=b.coupon_code 
											LEFT JOIN tblcouponproduct c on b.coupon_code=c.coupon_code
											LEFT JOIN tblcouponcategory d on b.coupon_code=d.coupon_code
										WHERE 
											b.id='".$_ShopInfo->getMemid()."' 
											AND b.date_start<='".date("YmdH")."' 
											AND (b.date_end>='".date("YmdH")."' OR b.date_end='') 
											AND b.used='N' 
											AND ((a.use_con_type2='Y' AND a.productcode = 'ALL') OR ((a.use_con_type2='Y' AND a.productcode != 'ALL') AND (c.productcode = '".$productcode."' OR (d.categorycode IN ".$addCategoryQuery." AND a.use_con_type2 = 'Y'))))
											AND mod(a.sale_type::int , 2) = '0' 
										ORDER BY 
											coupon_use_type 
										ASC";
	$resultGoodsCou = pmysql_query($sqlGoodsCou,get_db_conn());
	$aryCouponList = array();
	$cntCoupon = 0;
	while($rowGoodsCou=pmysql_fetch_array($resultGoodsCou)) {
		if($rowGoodsCou['sale_type'] <= 2){
			$couponDcPrice = ($price*$rowGoodsCou['sale_money'])*0.01;
			$couponDcPrice = ($couponDcPrice / pow(10, $rowGoodsCou['amount_floor'])) * pow(10, $rowGoodsCou['amount_floor']);
		}else{
			$couponDcPrice = $rowGoodsCou['sale_money'];
		}
		if($rowGoodsCou['sale_max_money'] && $rowGoodsCou['sale_max_money'] < $couponDcPrice){
			$couponDcPrice = $rowGoodsCou['sale_max_money'];
		}

		$rowGoodsCou['dc_price_str'] = number_format($couponDcPrice)."원 할인";
		$rowGoodsCou['dc_price'] = $couponDcPrice;







		$aryCouponList[$rowGoodsCou['coupon_use_type']][$rowGoodsCou['coupon_code']] = $rowGoodsCou;
		$cntCoupon++;
	}

	$layer1 = $layer2 = $layer3 = "";
	foreach($aryCouponList[1] as $k => $v){
		$layer1 .= "<li><a href='javascript:;'>".iconv('EUC-KR', 'UTF-8', $v['coupon_name'])." [".iconv('EUC-KR', 'UTF-8', $v['dc_price_str'])."] <img src='../img/button/pop_cal_coupon_use.gif' alt='' class = 'CLS_btn_coupon_basket' price = '".$v['dc_price']."'/></a></li>";
	}
	foreach($aryCouponList[2] as $k => $v){
		$layer2 .= "<li><a href='javascript:;'>".iconv('EUC-KR', 'UTF-8', $v['coupon_name'])." [".iconv('EUC-KR', 'UTF-8', $v['dc_price_str'])."] <img src='../img/button/pop_cal_coupon_use.gif' alt='' class = 'CLS_btn_coupon_goods' price = '".$v['dc_price']."'/></a></li>";
	}
	foreach($aryCouponList[3] as $k => $v){
		$layer3 .= "<li><a href='javascript:;'>".iconv('EUC-KR', 'UTF-8', $v['coupon_name'])." [".iconv('EUC-KR', 'UTF-8', $v['dc_price_str'])."] <img src='../img/button/pop_cal_coupon_use.gif' alt='' class = 'CLS_btn_coupon_etc' price = '".$v['dc_price']."'/></a></li>";
	}
	/*
	
			ID_view_basket_coupon_layer : 쿠폰 조회로 인한 결과가 출력 되는 레이어 [장바구니]
			ID_view_etc_coupon_layer : 쿠폰 조회로 인한 결과가 출력 되는 레이어 [무적]
			ID_view_goods_coupon_layer : 쿠폰 조회로 인한 결과가 출력 되는 레이어 [상품]
	*/

	echo json_encode(array('layer1' => $layer1, 'layer2'=> $layer2, 'layer3'=> $layer3, 'tot'=> $cntCoupon));
?>