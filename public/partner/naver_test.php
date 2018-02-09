<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata2.php");

	header("Cache-Control: no-cache, must-revalidate");
	header("Content-Type: text/plain; charset=euc-kr");

	$url = "http://".$_SERVER['HTTP_HOST']."/".FrontDir;
	$imagepath="http://".$_SERVER['HTTP_HOST']."/".DataDir."shopimages/product/";

	$sql = "	SELECT 
						* 
					FROM 
						tblproduct a LEFT JOIN tblproductlink b on (a.productcode = b.c_productcode and c_maincate=1) 
					WHERE 
						(quantity is NULL OR quantity > 0) 
						AND a.display='Y'
						AND sabangnet_flag= 'N'
						AND vip_product=0 
						AND staff_product!='1'
					ORDER BY 
						regdate DESC
				";
	$result = pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		if(!is_file($Dir.DataDir."shopimages/product/".$row->maximage)) continue;
		if (ord($row->maximage)){
			$imgUrl = $imagepath.$row->maximage;
		} else {
			$imgUrl = "";
		}
		
		$cate_query="select * from tblproductlink where c_productcode='".$row->productcode."' and c_productcode!='' LIMIT 1 OFFSET 0";
		$cate_result=pmysql_query($cate_query);		
		while($cate_row=pmysql_fetch_array($cate_result)){			
			$cate_array[$i]["c_category"]=$cate_row[c_category];
			$cate_cut="";
			$catename="";
			$cate_cut[]=str_pad(substr($cate_row[c_category],0,3), 12, "0");
			if(substr($cate_row[c_category],3,3)!='000')$cate_cut[]=str_pad(substr($cate_row[c_category],0,6), 12, "0");
			if(substr($cate_row[c_category],6,3)!='000')$cate_cut[]=str_pad(substr($cate_row[c_category],0,9), 12, "0");
			if(substr($cate_row[c_category],9,3)!='000')$cate_cut[]=str_pad(substr($cate_row[c_category],0,12), 12, "0");
			
			foreach($cate_cut as $k){
				$catename_qry="select * from tblproductcode where code_a='".substr($k,0,3)."' and code_b='".substr($k,3,3)."' and code_c='".substr($k,6,3)."' and code_d='".substr($k,9,3)."'";
				$catename_result=pmysql_query($catename_qry);
				$catename_data=pmysql_fetch_array($catename_result);
				$catename[]=$catename_data[code_name];
			}
			$cate_array[$i]["c_codename"]=implode(" > ",$catename);
			$i++;
		}
		
		$sql_brand = "SELECT brandname FROM tblproductbrand WHERE bridx = '{$row->brand}' ";
		$result_brand = pmysql_query($sql_brand,get_db_conn());
		$_data_brand = pmysql_fetch_object($result_brand);
		pmysql_free_result($result_brand);




		# 쿠폰 다운로드 최근 날짜 1장 노출
		$couponDownLoadFlag = false;
		$goods_sale_type = "";
		$goods_sale_money = "";
		$goods_amount_floor = "";
		$goods_sale_max_money = "";
		if($_data->coupon_ok=="Y") {
			$goods_cate_sql = "SELECT * FROM tblproductlink WHERE c_productcode = '".$row->productcode."'";
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
			$sql_coupon = "SELECT a.* FROM tblcouponinfo a ";
			$sql_coupon .= "LEFT JOIN tblcouponproduct c on a.coupon_code=c.coupon_code ";
			$sql_coupon .= "LEFT JOIN tblcouponcategory d on a.coupon_code=d.coupon_code ";
			if($row->vender>0) {
				$sql_coupon .= "WHERE (a.vender='0' OR a.vender='{$row->vender}') ";
			} else {
				$sql_coupon .= "WHERE a.vender='0' ";
			}
			$sql_coupon .= "AND a.display='Y' AND a.issue_type='Y' AND a.detail_auto='Y' AND a.coupon_type='1' ";
			$sql_coupon .= "AND (a.date_end>'".date("YmdH")."' OR a.date_end='') ";
			$sql_coupon .= "AND ((a.use_con_type2='Y' AND a.productcode = 'ALL') OR ((a.use_con_type2='Y' AND a.productcode != 'ALL') AND (c.productcode = '".$row->productcode."' OR (d.categorycode IN ".$addCategoryQuery." AND a.use_con_type2 = 'Y')))) ";
			$sql_coupon .= "AND mod(sale_type::int , 2) = '0' ";
			$sql_coupon .= "ORDER BY date DESC ";
			$sql_coupon .= "LIMIT 1 OFFSET 0";
			$result_coupon=pmysql_query($sql_coupon,get_db_conn());
			while($row_coupon=pmysql_fetch_object($result_coupon)) {
				$goods_sale_type = $row_coupon->sale_type;
				$goods_sale_money = $row_coupon->sale_money;
				$goods_amount_floor = $row_coupon->amount_floor;
				$goods_sale_max_money = $row_coupon->sale_max_money;
				$goods_coupon_code = $row_coupon->coupon_code;

				$couponDownLoadFlag = true;
			}
			pmysql_free_result($result_coupon);
		}

		if($couponDownLoadFlag){
			$cou_data = couponDisPrice($row->productcode);
			
			if($goods_sale_type <= 2){
				$goods_dc_coupong = number_format($goods_sale_money)."%";
			}else{
				$goods_dc_coupong = number_format($goods_sale_money)."원";
			}
		}


		$vender_deliprice = 0;
		if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
			$deli_productprice = $row->deli_price;
		} else if($row->deli=="F" || $row->deli=="G") {
			$deli_productprice = 0;
		} else {
			$vender_delisumprice = $row->sellprice;
			$deli_init=true;
		}

		$vender_deliprice=$deli_productprice;


		if($_data->deli_basefee>0) {
			if($_data->deli_basefeetype=="Y") {
				$vender_delisumprice = $vender_sumprice;
			}

			if ($vender_delisumprice<$_data->deli_miniprice && $deli_init) {
				$vender_deliprice = $_data->deli_basefee;
			}
		} else if(strlen($_data->deli_limit)>0) {
			if($_data->deli_basefeetype=="Y") {
				$vender_delisumprice = $vender_sumprice;
			}

			if($deli_init) {
				$delilmitprice = setDeliLimit($vender_delisumprice,$_data->deli_limit);
				$vender_deliprice = $delilmitprice;
			}
		}

		$deli_price = $vender_deliprice;
		if(!$deli_price) $deli_price = 0;
		/*
		<!--<<<pcard>>>삼성3/현대6/국민12-->
		<!--<<<point>>><?=$row->reserve."\n"?>-->
		*/
		# 쿠폰할인가 임시해제 2015 07 01 유동혁
		// $row->sellprice - $cou_data['coumoney']) 쿠폰가격이 빠진 금액
		// if ($couponDownLoadFlag){ <<<pcpdn>>> echo "Y\n"; }
		# 상품 일괄 10% DC
		//$alDcPcice = $row->sellprice - ($row->sellprice * 0.1);
		
?>

<<<begin>>>
<<<mapid>>><?=$row->productcode."\n"?>
<<<pname>>><?=$row->productname."\n"?>
<<<price>>><?=$row->sellprice."\n"?>
<<<pgurl>>><?=$url?>productdetail.php?productcode=<?=$row->productcode."\n"?>
<<<igurl>>><?=$imgUrl."\n"?>
<? for ($i=1;$i<=strlen($cate_cut[0])/3;$i++){ ?>
<<<cate<?=$i?>>>><?=$catename[($i-1)]."\n"?>
<? } ?>
<? for ($i=1;$i<=strlen($cate_cut[0])/3;$i++){ ?>
<<<caid<?=$i?>>>><?=$cate_cut[($i-1)]."\n"?>
<? } ?>
<<<model>>><?=$v[goodscd]."\n"?>
<<<brand>>><?=$_data_brand->brandname."\n"?>
<<<maker>>><?=$row->production."\n"?>
<<<origi>>><?=$row->madein."\n"?>
<<<deliv>>><?=$deli_price."\n"?>

<<<pcard>>>
<<<point>>>
<<<ftend>>>
<?
	}
?>