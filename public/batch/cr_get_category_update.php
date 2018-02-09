#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : 
# Desc              : 
# Desc2             : 
# Last Updated      : 
# By                : 
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_get_erp_product.sh 
#######################################################################################
exit;
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다

//@set_time_limit(0);

echo "START = ".date("Y-m-d H:i:s")."\r\n";
$cnt = 1;
$sql = "select * from tblproduct where season_year='2016' and season='P' order by pridx asc";
$result=pmysql_query($sql);
while($data=pmysql_fetch_object($result)){
	echo $cnt."\r\n";
	//echo $data->productcode." \n";
	$sub_sql = "select * from tblproductlink where c_productcode='".$data->productcode."'";
	//echo $sub_sql;
	$sub_result=pmysql_query($sub_sql);
	if($sub_data=pmysql_fetch_object($sub_result)){
			if($sub_data->c_category){
				//echo $sub_data->c_category." \n";
			}else{
				$s_category =  substr($data->productcode, 0, 12);
				//echo $sub_data->c_category." \n";
				list($matching_code) = pmysql_fetch("select matching_code from tblproductcode_match where  standard_code = '".$s_category."'");
				$upqry="update tblproductlink set c_category = '".$matching_code."' where c_productcode='".$data->productcode."'";
				pmysql_query($upqry,get_db_conn());
				echo $upqry." \n";
				//exit;
			}
	}
    $cnt++;
/*
	if($cnt == 3){
		echo "END = ".date("Y-m-d H:i:s")."\r\n";
		exit;
	}
*/
}
echo "END = ".date("Y-m-d H:i:s")."\r\n";
exit;
$brand_vender	= getAllBrand();			// 쇼핑몰 전체 EPR 브랜드코드별 쇼핑몰 브랜드코드, 벤더코드
$discountrate		= getDiscountRate();	// 브랜드 그룹별 할인율
//exdebug($brand_vender);
//exit;

$productcode = "";
$brand = "";
$vender = "";
$self_goods_code = "";
$sizeopt = array();
$sizestock = array();
$sizeprice = array();
$sizearr = "";
while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {
	echo $cnt."\r\n";

    foreach($data as $k => $v)
    {
        $data[$k] = utf8encode($v);
    }

    $product_ins = getProducCode($data[PRODCD], $data[COLORCD], $data[SEASON_YEAR], $data[SEASON], $data[BRANDCD], $data[PRODCATECODE]);    // 상품코드 및 카테고리
	$productcode					= $product_ins["productcode"];
	$category						= $product_ins["catecode"];
	//echo "Select productcode From tblproduct Where prodcode = '".$data[PRODCD]."' And colorcode = '".$data[COLORCD]."' And season_year = '".$data[SEASON_YEAR]."' And season = '".$data[SEASON]."'";
	//exit;
list($chk_productcode) = pmysql_fetch("Select productcode From tblproduct Where prodcode = '".$data[PRODCD]."' And colorcode = '".$data[COLORCD]."' And season_year = '".$data[SEASON_YEAR]."' And season = '".$data[SEASON]."'");
	if($chk_productcode){
		$now_brandcd				= $data[BRANDCD];
		$data[BRANDCD]			= $now_brandcd=='Q'?'P':$data[BRANDCD];
		$data[BRANDCDNM]		= $now_brandcd=='Q'?'SIEG':$data[BRANDCDNM];
		$data[PRODNM]				= $now_brandcd=='Q'?str_replace("[지이크]","[".$data[BRANDCDNMH]."]",$data[PRODNM]):$data[PRODNM];
		$brand		= $brand_vender[$data[BRANDCD]][BRAND]; // 브랜드
		$vender		= $brand_vender[$data[BRANDCD]][VENDER];// 벤더

		$sizeopt = getProdSizeOpt($data[PRODCD], $data[COLORCD], $data[SEASON_YEAR], $data[SEASON]);       // 사이즈별 옵션정보(사이즈)
		$sizestock = getProdStock($data[PRODCD], $data[COLORCD], '', $data[SEASON_YEAR]);       // 사이즈별 재고정보(사이즈, 매장재고합계)
		$ratesellprice = getProdeSellPrice($brand, $data[SEASON_YEAR], $data[SEASON], $data[TAGPRICE], $data[PRODCATE]);       // 할인율 및 정책가격정보(정책가(=판매가))
		$sellprice	= $ratesellprice[PRICE];
		$dcrate		= $ratesellprice[DCRATE];
		$mixrate	= getProdMixRate($data[PRODCD], $data[COLORCD], $data[SEASON_YEAR]); // 혼용율
		$mixrate	= implode(", ", $mixrate);
		//exdebug($mixrate);
		//exdebug($sizeopt);
		//exdebug($sizestock);
		//exdebug($ratesellprice);
		//exdebug($sellprice);
		//exdebug($dcrate);
		//exit;

		$sizearr = implode("@#", $sizeopt[SIZE]);
		//exdebug($sizearr);

		if ($data[PRODNM] == '-') {
			list($prodnm) = pmysql_fetch("Select productname From tblproduct Where substr(prodcode, 1, 7) = '".substr($data[PRODCD], 0, 7)."' AND productname != '-' ");
			print_r("Select productname From tblproduct Where substr(prodcd, 1, 7) = '".substr($data[PRODCD], 0, 7)."'");
			//exit;
			if ($prodnm) {
				$data[PRODNM]	= $prodnm;
			}
		}

		$deli_year = substr($data[DEL_DATE],0,4);
		$deli_mon = substr($data[DEL_DATE],4,2);
		$deli_date = $deli_year."년".$deli_mon."월";
		$prod_nation =	$data[PROD_NATION];

		echo "prodcd = ".$data[PRODCD]." / colorcd = ".$data[COLORCD]." / prodnm = ".$data[PRODNM]." / category = ".$category." / productcode = ".$productcode." / deli_date = ".$deli_date." / prod_nation = ".$data[PROD_NATION]." / sizearr = ".$sizearr."\r\n";

		$self_goods_code = $data[PRODCD].$data[COLORCD];
		//$display = "R"; // R 가등록 상태 추가..

		list($color_code) = pmysql_fetch("Select maincolor_name From tblproduct_color_erp Where colorcode = '".$data[COLORCD]."'");
		
		$sabangnet_prop			= getJungboInfo("001", $mixrate, $data[COLORCD], $sizeopt[SIZE],$deli_date,$prod_nation);
		$sabangnet_prop_option	= $sabangnet_prop['option'];
		$sabangnet_prop_val		= $sabangnet_prop['val'];


		/*$sql = "
				WITH upsert as (
					update  tblproduct 
					set 	brand = '".$brand."',
							vender = '".$vender."',
							productname = '".$data[PRODNM]."',consumerprice = (case when erp_price_yn = 'Y' then $data[TAGPRICE] else consumerprice end), 
							sellprice = (case when erp_price_yn = 'Y' then $sellprice else sellprice end), 
							sellprice_dc_rate = (case when erp_price_yn = 'Y' then $dcrate else sellprice_dc_rate end), 
							mixrate = '$mixrate', 
							option1 = 'SIZE', 
							color_code = '$color_code', 
							option_type = '0', 
							option1_tf = 'T' , 
							sizecd = '".$sizearr."'
					where	productcode = '".$productcode."' 
					RETURNING * 
				)*/
		$sql = "
				WITH upsert as (
					update  tblproduct 
					set 	
							productname = '".$data[PRODNM]."',
							mixrate = '$mixrate', 
							sabangnet_prop_option = '$sabangnet_prop_option', 
							sabangnet_prop_val = '$sabangnet_prop_val', 
							color_code = '$color_code', 
							sizecd = '".$sizearr."'
					where	productcode = '".$productcode."' 
					RETURNING * 
				)
				insert into tblproduct 
				(
				productcode, 
				brand, 
				vender, 
				productname, 
				consumerprice, 
				production, 
				model, 
				option1, 
				color_code, 
				date, 
				regdate, 
				modifydate, 
				option_type, 
				option1_tf, 
				self_goods_code, 
				prodcode, 
				colorcode, 
				sizecd, 
				brandcd, 
				brandcdnm, 
				tag_style_no, 
				season_year, 
				season, 
				sabangnet_prop_option,
				sabangnet_prop_val,
				mixrate
				)
				Select  
				'$productcode', 
				'$brand', 
				'$vender', 
				'$data[PRODNM]', 
				$data[TAGPRICE], 
				'$data[BRANDCDNM]', 
				'$data[BRANDCDNM]', 
				'SIZE', 
				'$color_code', 
				'".date("YmdHis")."', 
				now(), 
				now(), 
				'0', 
				'T', 
				'$self_goods_code', 
				'$data[PRODCD]', 
				'$data[COLORCD]', 
				'$sizearr', 
				'$data[BRANDCD]', 
				'$data[BRANDCDNM]', 
				'$data[TAG_STYLE_NO]', 
				'$data[SEASON_YEAR]', 
				'$data[SEASON]', 
				'$sabangnet_prop_option',
				'$sabangnet_prop_val',
				'$mixrate'
				WHERE NOT EXISTS ( select * from upsert ) 
				";
		$ret = pmysql_query($sql, get_db_conn());
		print_r($sql);
		//exdebug($sql);
		if($err=pmysql_error()) echo $err."\r\n";

		//상품옵션이 안들어간 상품이 있다고 해서 로그추가함. (2016.11.04 - 김재수 추가)
		echo "sizestock_size = ".implode(" / ", $sizestock[SIZE])."\r\n";
		echo "sizestock_sumqty = ".implode(" / ", $sizestock[SUMQTY])."\r\n";

		//카테고리 등록
		//DbInsertProductCategory($productcode, $category);


		// 상품 옵션 생성
		//DbInsertProductOption($productcode, $sizeopt[SIZE], $sizestock[SIZE], $sizestock[SUMQTY], $data[PRODCD], $data[COLORCD]);

		// 실서버에는 주석풀자..
		$sql = "update TA_OM001 set RCV_DATE=SYSDATE where RCV_DATE is NULL and STYLE_NO = '".$data[PRODCD]."' and COLOR_CODE = '".$data[COLORCD]."' AND SEASON_YEAR = '".$data[SEASON_YEAR]."' AND SEASON = '".$data[SEASON]."'  ";
		$smt_rec = oci_parse($conn, $sql);
		oci_execute($smt_rec);

		// 실서버에는 주석풀자..
		$sql = "update TA_OM002 set RCV_DATE=SYSDATE where RCV_DATE is NULL and STYLE_NO = '".$data[PRODCD]."' and COLOR_CODE = '".$data[COLORCD]."' AND SEASON_YEAR = '".$data[SEASON_YEAR]."'  ";
		$smt_rec = oci_parse($conn, $sql);
		oci_execute($smt_rec);

}
    $cnt++;
    if( ($cnt%1000) == 0) {
		echo "cnt = ".$cnt."\r\n";
		sleep(10);
	}
	/*if ($cnt == 5) {
		oci_free_statement($smt);
		oci_close($conn);

		pmysql_free_result($ret);
		exit;
	}*/
	if($cnt  == 7000){
		exit;
	}
}

oci_free_statement($smt);
oci_close($conn);

pmysql_free_result($ret);

echo "END = ".date("Y-m-d H:i:s")."\r\n";


// 쇼핑몰 전용 상품 코드 구하기
function getProducCode($prodcd, $colorcd, $season_year, $season, $brandcd, $prodcate) {
	$prodcode_info	=	 array();
    list($productcode) = pmysql_fetch("Select productcode From tblproduct Where prodcode = '".$prodcd."' And colorcode = '".$colorcd."' And season_year = '".$season_year."' And season = '".$season."'");

    list($catecode) = pmysql_fetch("Select catecode From tblproduct_cate_erp Where brandcds LIKE '%".$brandcd."%' And bokjongs LIKE '%".$prodcate."%' ");

	if ($catecode =='') $catecode	= "001001001001";

    if($productcode == "") {
        $sql = "SELECT MAX(productcode) as maxproductcode FROM tblproduct WHERE productcode LIKE '".$catecode."%' LIMIT 1";
        $result = pmysql_query($sql,get_db_conn());
        if ($rows = pmysql_fetch_object($result)) {
            if (strlen($rows->maxproductcode)==18) {
                $productcode = ((int)$rows->maxproductcode)+1;
                $productcode = str_pad($productcode, 18, '0', STR_PAD_LEFT);
            } else if($rows->maxproductcode==NULL){
                $productcode = $catecode."000001";
            } 
            pmysql_free_result($result);
        } else {
            $productcode = $catecode."000001";
        }
    }
	$prodcode_info['productcode']	= $productcode;
	$prodcode_info['catecode']	= $catecode;
    return $prodcode_info;
}

//정보고시 구하기
function getJungboInfo($jungbo_cd, $mixrate, $colorcd, $size, $deli_date, $prod_nation) {
	global $jungbo_code;
	
	$incode = $jungbo_code[$jungbo_cd];

	$jungboinfo_val_arr	= array();
	$jungboinfo_val_arr[0]	= $mixrate;
	$jungboinfo_val_arr[1]	= $colorcd;
	$jungboinfo_val_arr[2]	= implode("/", $size);
	$jungboinfo_val_arr[3]	= $incode['comment'][3];
	$jungboinfo_val_arr[4]	= $prod_nation;
	$jungboinfo_val_arr[5]	= "본 상품은 반드시 드라이크리닝 해주시기 바랍니다.(세탁케어라벨참조)";
	$jungboinfo_val_arr[6]	= $deli_date;
	$jungboinfo_val_arr[7]	= $incode['comment'][7];
	$jungboinfo_val_arr[8]	= $incode['comment'][8];

	$jungboinfo_option	= $jungbo_cd;
	$jungboinfo_val		= $jungbo_cd;
	foreach( $incode['option'] as $inKey=>$inVal ){
		$jungboinfo_option .= "||".$inVal;
		$jungboinfo_val .= "||".$jungboinfo_val_arr[$inKey];
	}
	$jungboinfo				= array();
	$jungboinfo['option']	= $jungboinfo_option;
	$jungboinfo['val']		= $jungboinfo_val;

	return $jungboinfo;
}

// ERP 상품 prodcd, colorcd 로 사이즈 정보 구하기 & 상품옵션 생성하기
function getProdSizeOpt($prodcd, $colorcd, $season_year, $season) {

    global $conn;
    global $erp_account;

    $sql = "SELECT  SIZE_CODE AS SIZECD
			FROM    TA_OM001
			WHERE   STYLE_NO = '$prodcd' 
			AND     COLOR_CODE = '$colorcd' 
			AND     SEASON_YEAR = '$season_year' 
			AND     SEASON = '$season' 
			GROUP BY SIZE_CODE 
			ORDER BY SIZE_CODE
            ";
    $smt_opt = oci_parse($conn, $sql);
    oci_execute($smt_opt);
    //echo $sql."\r\n";

    $size_opt = array();
    //$ssizeopt = array();
    while($data = oci_fetch_array($smt_opt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

        $size_opt[SIZE][]         = trim($data[SIZECD]);
    }
    oci_free_statement($smt_opt);

    return $size_opt;
}

// 상품 사이즈별 재고 합계 구하기
function getProdStock($prodcd, $colorcd, $type='delivery', $season_year) {

    global $conn;

	$store = array();
	$store_arr = array();
	$store = getShopCodeWhere($type);
	foreach($store as $key => $val) {
		$part_div	= substr($val, 0, 1);
		$part_no		= substr($val, 1, 4);
		$store_arr[]	= "(PART_DIV = '".$part_div."' AND PART_NO = '".$part_no."')";
	}
	$where = implode(" OR ", $store_arr);
	$subsql = "AND (".$where.") ";

	// 재고 수량이 0 보다 작을 경우 0으로 변경쿼리 추가 (2016.11.01 - 김재수)
    $sql = "SELECT SIZECD, 
	CASE WHEN STOCK_QTY < 0 THEN 0 ELSE STOCK_QTY END SUMQTY
	FROM (SELECT  A.SIZE_CODE AS SIZECD,
		   SUM(NVL(CASE WHEN A.PART_DIV = 'A' THEN A.IN_QTY ELSE A.OUT_QTY END, 0)
						   - NVL(CASE WHEN A.PART_DIV = 'A' THEN A.OUT_QTY ELSE A.SALE_QTY END, 0)
						   - NVL(A.ETC_OUT_QTY, 0)
						   - NVL(CASE WHEN A.PART_DIV = 'A' THEN NVL(A.OUT_RETURN_QTY, 0) * (-1) ELSE A.OUT_RETURN_QTY END, 0))
						   AS STOCK_QTY
	  FROM (SELECT BRAND,
				   PART_DIV,
				   PART_NO,
				   PART_TYPE,
				   STYLE_NO,
				   COLOR_CODE,
				   SEASON,
				   SEASON_YEAR,
				   SIZE_CODE,
				   SALE_QTY,
				   IN_QTY,
				   OUT_QTY,
				   OUT_RETURN_QTY,
				   ETC_OUT_QTY
			  FROM VI_STOCK
			 WHERE STYLE_NO = '".$prodcd."'        -- 품번 8자리 필수 
			   AND SEASON_YEAR = '".$season_year."'          -- 시즌년도 필수 
			   AND COLOR_CODE = '".$colorcd."'            --옵션 			   	   
			   ".$subsql."
			) A,
		   VI_PART_INFO1 B
	 WHERE 1=1
	   AND A.PART_DIV = B.PART_DIV
	   AND A.PART_NO  = B.PART_NO
	   AND (B.REALPART_GB = '1' AND B.PART_DIV IN ('D','G','K')
		OR  (B.PART_DIV = 'A' AND B.PART_NO = '1801'))
		GROUP BY  A.SIZE_CODE
		ORDER BY A.SIZE_CODE
	 ) S
            ";
    $smt_stock = oci_parse($conn, $sql);
    oci_execute($smt_stock);
    //print_r($sql);

    $size_stock = array();
    while($data = oci_fetch_array($smt_stock, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

        $size_stock[SIZE][]         =  trim($data[SIZECD]);
        $size_stock[SUMQTY][]      =  trim($data[SUMQTY]);
    }
    oci_free_statement($smt_stock);

    return $size_stock;
}


 // 할인율 및 정책가격정보(정책가(=판매가)) 구하기
function getProdeSellPrice($brand, $season_year, $season, $tagprice, $prodcate) {

    global $conn;
    global $discountrate;
	
	$sellprice	= array();
	$item_gubun	= $prodcate!='A'?'1':$prodcate;
	$dcrate			= $discountrate[$brand][$season_year][$season][$item_gubun];
	$dcrate			= (!$dcrate && $prodcate=='A')?$discountrate[$brand][$season_year][$season]['1']:$discountrate[$brand][$season_year][$season][$item_gubun];
	$dcrate			= $dcrate?$dcrate:'0';

	$sellprice[PRICE]		= $tagprice * ((100-$dcrate)/100);
	$sellprice[DCRATE]	= $dcrate;

    return $sellprice;
}

// 혼용율 가져오기
function getProdMixRate($prodcd, $colorcd, $season_year) {

    global $conn;

    $sql = "SELECT COLORATION_GB,
				   MATR_NAME,
				   MIXRATE
			  FROM TA_OM002
			 WHERE STYLE_NO = '".$prodcd."'        -- 품번 8자리 필수 
			   AND SEASON_YEAR = '".$season_year."'          -- 시즌년도 필수 
			   AND COLOR_CODE = '".$colorcd."'            --옵션 
			   AND MIXRATE IS NOT NULL
            ";
    $smt_mr = oci_parse($conn, $sql);
    oci_execute($smt_mr);
    print_r($sql);

    $mix_rate = array();
    while($data = oci_fetch_array($smt_mr, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

		foreach($data as $k => $v)
		{
			$data[$k] = utf8encode($v);
		}

        $mix_rate[]         =  "[".trim($data[COLORATION_GB])."] ".trim($data[MATR_NAME])." ".$data[MIXRATE]."%";
    }
    oci_free_statement($smt_mr);

    return $mix_rate;
}

function DbInsertProductOption($productcode, $sizearr, $stocksizearr, $stockarr, $prodcd, $colorcd) {

    //exdebug($sizearr);
    //exdebug($stocksizearr);
    //exdebug($stockarr);
    //exdebug($pricesizearr);
    //exdebug($pricearr);
    
    // 재고 0으로 초기화 후 있는 사이즈의 재고만 업데이트 및 인서트하자..2016-10-11
    $sql = "update tblproduct_option set option_quantity = 0 where productcode = '".$productcode."'";
    pmysql_query($sql);

    $upOptQty = 0;
    $option_code = "";
    $self_goods_code = "";
    $barcode = "";
    $internalcode = "";
    $option_quantity = 0;
    for($i=0; $i < count($sizearr); $i++) { 
    //for($i=0; $i < count($stocksizearr); $i++) {

        $option_code        = $sizearr[$i];
        //$option_code        = $stocksizearr[$i];
        $self_goods_code    = $prodcd.$colorcd.$option_code;
        $option_quantity    = 0;
		for($q=0; $q < count($stocksizearr); $q++) {
			if ($option_code == $stocksizearr[$q])  $option_quantity = $stockarr[$q];
		}
        //$option_price       = ($option_code == $pricesizearr[$i])?$tagprice - $pricearr[$i]:0;
        $option_price       = 0;
        
        $optInsertSql = "
                WITH upsert_opt AS (
                    Update  tblproduct_option 
                    Set     option_price = ".$option_price.", 
                            option_quantity = ".$option_quantity.", 
                            self_goods_code = '".$self_goods_code."'
                    Where   productcode = '".$productcode."' 
                    And     option_code = '".$option_code."' 
                    RETURNING * 
                ) 
                INSERT INTO tblproduct_option 
                ( option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use, option_tf, self_goods_code ) 
                Select  '".$option_code."', '".$productcode."', ".$option_price.", ".$option_quantity.", 0, 0, 1, 'T', '".$self_goods_code."' 
                WHERE NOT EXISTS ( select * from upsert_opt ) 
                ";
        print_r($optInsertSql);
        pmysql_query($optInsertSql, get_db_conn());
        if($err=pmysql_error()) echo $err."\r\n";

        $upOptQty += $option_quantity;
    }

    $sql = "UPDATE tblproduct SET quantity = ".$upOptQty." WHERE productcode = '".$productcode."'";
    print_r($sql);
    pmysql_query($sql, get_db_conn());
    if($err=pmysql_error()) echo $err."\r\n";

}

function DbInsertProductCategory($productcode, $category) {    

	$date1=date("Ym");
	$date=date("dHis");
	$c_date	= $date1.$date;
	$sql = "
            WITH upsert_cate as (
                update  tblproductlink 
                set 	
                        c_category = '".$category."',
                        c_date = '$c_date', 
                        c_date_1 = '$c_date', 
                        c_date_2 = '$c_date', 
                        c_date_3 = '$c_date', 
                        c_date_4 = '$c_date'
                where	c_productcode = '".$productcode."'
				and c_maincate ='1' 
                RETURNING * 
            )	
			INSERT INTO tblproductlink
			(c_productcode, c_category, c_maincate, c_date, c_date_1, c_date_2, c_date_3, c_date_4 ) 
			Select  '".$productcode."', '".$category."', '1', '".$c_date."', '".$c_date."', '".$c_date."', '".$c_date."', '".$c_date."'
			WHERE NOT EXISTS ( select * from upsert_cate ) ";
	//exdebug($sql);
	print_r($sql);
	pmysql_query($sql,get_db_conn());
	if($err=pmysql_error()) echo $err."\r\n";

}

?>
