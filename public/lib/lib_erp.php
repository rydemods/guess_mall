<?
/*******************************************
1. 핫티 ERP 관련 함수 모음
********************************************/
/*** 나중에 주석처리 하자 S ***/
//$Dir="../";
//include_once($Dir."lib/init.php");
//include_once($Dir."lib/lib_test.php");

//$prodcd = "BJP22600";
//$season_year = "2014";
//$colorcd = "WH";
//$sizecd = "55";
//$shopcd = "A1801B";
//$staffno = "2015000100085";

//$sizestock = getErpProdStock($prodcd, $colorcd);                        // 상품 사이즈별 재고 구하기(실시간)
//$shopstock = getErpProdShopStock($prodcd, $colorcd, $sizecd);           // 상품 해당 사이즈의 매장별  재고 구하기(실시간)
//$sizesumstock = getErpProdSizeStock($prodcd, $colorcd, $sizecd);        // 상품 해당 사이즈의 총재고 구하기(실시간)
//$tagprice = getErpTagPrice($prodcd, $colorcd);                          // 해당 상품 tagprice 구하기.(실시간)
//$polprice = getErpPolicyPrice($prodcd, $colorcd);                       // 해당 상품 정책가 구하기.(실시간)
//$pricenstock = getErpPriceNStock($prodcd, $colorcd, $sizecd, $shopcd);
//$prodshopstock_type	= getErpProdShopStock_Type($prodcd, $colorcd, $sizecd);
//$staffpoint = getErpStaffPoint($staffno);                               // 임직원 잔여 마일리지 구하기

//exdebug($sizestock);
//exdebug($shopstock);
//exdebug("size_sum_stock = ".$sizesumstock);
////exdebug("tagprice = ".$tagprice);
//exdebug("polprice = ".$polprice);
//exdebug($pricenstock);
//exdebug($prodshopstock_type);
//exdebug("staffpoint = ".$staffpoint);*/
/*** 나중에 주석처리 하자 E ***/

// erp db 접속 후 사용할 계정명 정의..
$erp_account = "SWSIS";   // 개발 DB

$erp_deli_com_list=array();
$erp_deli_com_sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$erp_deli_com_result=pmysql_query($erp_deli_com_sql,get_db_conn());
while($erp_deli_com_row=pmysql_fetch_object($erp_deli_com_result)) {
	$erp_deli_com_list[trim($erp_deli_com_row->code)]=$erp_deli_com_row;	
}
pmysql_free_result($erp_deli_com_result);

function GetErpDBConn() {
    // 접속정보 변경시 반드시 실서버 주문정보 전송되는 부분등 체크해서 주석으로 막을지 체크해야됨.
    $conn = oci_connect("swonline", "commercelab", "125.128.119.220/SWERP", "US7ASCII");   // 개발 실DB

    return $conn;
}

function GetErpDBClose($conn) {
    oci_close($conn);
}

// ERP 상품 prodcd, colorcd 로 사이즈 정보 구하기
function getErpProdSizeOpt($prodcd, $colorcd, $season_year, $season, $db_conn='') {

	$requestParams = array(
		  'aStyleNo' => array($prodcd),
		  'aColorCode' => array($colorcd),
		  'aSeasonYear' => array($season_year)
	);

	$client = new SoapClient('http://swerp.sw.co.kr/shinwonmall/service.asmx?wsdl');
	$response = $client->service_GetSizeCodes($requestParams);
	$xml = simplexml_load_string($response->service_GetSizeCodesResult->any);

	$size_opt = array();

	foreach($xml->NewDataSet->SIZECODES as $list) 
	{
		   $size_opt[SIZE][]         = trim($list->SIZE_CODE);
	}

	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
		//exdebug($size_opt);
		//exit;
	}

    return $size_opt;

}

/**
 *  1. 2016년 10월 17일 오픈시에는 강남과 홍대점의 재고만 사용하기로 협의함.김태형과장 요청..2016-10-11
 *  2. 전체적으로 재고 관련 부분은 강남점(008810)의 재고만 사용하게 수정함.
 *  3. 2016-10-26 홍대점 (008560) 추가.
 *  4. 2016-11-08 광주 충장로점 (000570) 추가
 **/
// 상품 사이즈별 재고 구하기(실시간)
function getErpProdStock($prodcd, $colorcd, $type='delivery') {
		
	list($season_year) = pmysql_fetch(" SELECT season_year FROM tblproduct WHERE prodcode = '{$prodcd}' AND colorcode = '{$colorcd}' ");

	if($season_year){

		if(!$sizecd){
			$sizecd = "%";;
		}

		$requestParams = array(
			  'aStyleNo' => array($prodcd),
			  'aSeasonYear' => array($season_year),
			  'aColorCode' => array($colorcd),
			  'aSizeCode' => array($sizecd)
		);

		$client = new SoapClient('http://swerp.sw.co.kr/shinwonmall/service.asmx?wsdl');
		$response = $client->service_GetStyleStockQTy($requestParams);
		$xml = simplexml_load_string($response->service_GetStyleStockQTyResult->any);

		$check_season = substr($prodcd,1,1);
		$size_stock = array();
		foreach($xml->NewDataSet->STOCK as $list) 
		{

			$res_size_stock[size][]   = trim($list->SIZECD);
			//20170908 신원 권정수대리 요청으로 2017년 추동 상품 안전재고 -1 설정
			//20171128 신원 고효서 주임 요청으로 해제
			//20171219 신원 권정수대리 요청으로 -10 복구 
			if($check_season == 'R' && $season_year == '2017' && $list->SUMQTY > 1){
				$res_size_stock[sumqty][] = trim($list->SUMQTY - 1);
			}else if($list->SUMQTY > 10){
				$res_size_stock[sumqty][] = trim($list->SUMQTY - 10);
			}else{
				$res_size_stock[sumqty][] = 0;
			}
		}

	}
	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
		//exdebug($res_size_stock);
		//exit;
	}
    return $res_size_stock;

}


// 상품 해당 사이즈의 매장별  재고 구하기(실시간)
function getErpProdShopStock($prodcd, $colorcd, $sizecd, $type='delivery') {
	
	list($season_year) = pmysql_fetch(" SELECT season_year FROM tblproduct WHERE prodcode = '{$prodcd}' AND colorcode = '{$colorcd}' ");

	if ($season_year) {

		$boolean = 'TRUE';

		$requestParams = array(
			'aStyleNo' => $prodcd,
			'aSeasonYear' => $season_year,
			'aColorCode' => $colorcd,
			'aSizeCode' => $sizecd,
			'aZeroInclude' => $boolean
		);

		$client = new SoapClient('http://swerp.sw.co.kr/shinwonmall/service.asmx?wsdl');
		$response = $client->service_PartStyleColorSizeStockQTy($requestParams);
		$xml = simplexml_load_string($response->service_PartStyleColorSizeStockQTyResult->any);
		$shop_stock = array();

		foreach($xml->NewDataSet->STOCK as $list) 
		{
			if ($list->PART_CODE =='A1801B') {
				$list->PART_NAME = "온라인매장";
				$shop_stock[sumqty] =	trim($list->STOCK_QTY);
			}

			if ($list->PART_NAME) {
				$shop_stock[shopnm][]	=	trim($list->PART_NAME);
				$shop_stock[shopcd][]	=	trim($list->PART_CODE);
				$shop_stock[availqty][]       = (($prodcd=='BMC21890' && $sizecd=='77') || ($list->STOCK_QTY < 0))?0:trim($list->STOCK_QTY);
			}

		}
	}

	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
		//exdebug($shop_stock);
		//exit;
	}

	return $shop_stock;

}

// 상품 해당 사이즈의 매장별  재고 구하기(실시간)
function getErpProdShopStock_Part($prodcd, $colorcd, $sizecd, $shopCd) {
	
	list($season_year) = pmysql_fetch(" SELECT season_year FROM tblproduct WHERE prodcode = '{$prodcd}' AND colorcode = '{$colorcd}' ");

	if ($season_year) {

		$boolean = 'TRUE';

		$requestParams = array(
			'aStyleNo' => $prodcd,
			'aSeasonYear' => $season_year,
			'aColorCode' => $colorcd,
			'aSizeCode' => $sizecd,
			'aZeroInclude' => $boolean
		);

		$client = new SoapClient('http://swerp.sw.co.kr/shinwonmall/service.asmx?wsdl');
		$response = $client->service_PartStyleColorSizeStockQTy($requestParams);
		$xml = simplexml_load_string($response->service_PartStyleColorSizeStockQTyResult->any);
		$shop_stock = array();

		foreach($xml->NewDataSet->STOCK as $list) 
		{
			if($shopCd == $list->PART_CODE){
				$size_stock[sumqty]	=	(($prodcd=='BMC21890' && $sizecd=='77') || ($list->STOCK_QTY < 0))?0:trim($list->STOCK_QTY);
			}
		}
	}

	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
		//exdebug($shop_stock);
		//exit;
	}

	return $size_stock;

}

// 상품 해당 사이즈의 매장별  재고 구하기(실시간)
function getErpProdShopStock_New($prodcd, $colorcd, $sizecd, $type='delivery') {
		
	list($season_year) = pmysql_fetch(" SELECT season_year FROM tblproduct WHERE prodcode = '{$prodcd}' AND colorcode = '{$colorcd}' ");

	if ($season_year == '') {
		/*erp재고 가져오는부분 임시로 강제 수량 설정 실적용시 삭제 2017-02-22*/
		$shop_stock="";
		$shop_stock[shopnm][]         = "신림포도몰직영(BB)";
		$shop_stock[shopcd][]         = "D6306B";
		$shop_stock[availqty][]       = 20;
	} else{
		$store = array();
		$store_arr = array();
		$store = getShopCodeWhere($type);
		foreach($store as $key => $val) {
			$part_div	= substr($val, 0, 1);
			$part_no		= substr($val, 1, 4);
                	$store_arr[] = "('{$part_div}','{$part_no}')";
        	}
		$store_arr = array_unique($store_arr);
        	$where = "(PART_DIV,PART_NO) IN (".implode(",", $store_arr).")";
            	$subsql = "AND ".$where." ";

		$conn = GetErpDBConn();

		// 재고 수량이 0 보다 작을 경우 0으로 변경쿼리 추가 (2016.11.01 - 김재수)
		$sql = "SELECT S.PART_DIV, S.PART_NO, S.BRAND, S.STYLE_NO, S.COLOR_CODE, S.SIZE_CODE, CASE WHEN S.STOCK_QTY < 0 THEN 0 ELSE S.STOCK_QTY END AVAILQTY, CUST_NAME AS SHOPNM
	FROM (SELECT MAX(A.BRAND) BRAND,A.PART_DIV , A.PART_NO , 
		   STYLE_NO , 
		   A.COLOR_CODE, 
		   A.SIZE_CODE,
		   MAX(B.CUST_NAME) AS CUST_NAME,
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
					AND TRIM(SIZE_CODE) = '".$sizecd."'            --옵션		   
				   ".$subsql."
	  ) A,
		   VI_PART_INFO1 B
	   WHERE A.PART_DIV = B.PART_DIV
	   AND A.PART_NO  = B.PART_NO
	   AND (B.REALPART_GB = '1' AND B.PART_DIV IN ('D','G','K')
		OR  (B.PART_DIV = 'A' AND B.PART_NO = '1801'))
		GROUP BY  
		A.PART_DIV , 
		A.PART_NO ,
		A.STYLE_NO, 
		A.COLOR_CODE, 
		A.SIZE_CODE
		) S
		--LEFT JOIN TA_OM006 T ON S.PART_DIV=T.PART_DIV AND S.PART_NO=T.PART_NO AND S.BRAND=T.BRAND
		WHERE 1=1
		 ORDER BY (CASE 	WHEN S.PART_DIV = 'A' AND S.PART_NO = '1801' THEN 1                          		
							ELSE 2 END) ASC,
							(CASE WHEN S.STOCK_QTY < 0 THEN 0 ELSE S.STOCK_QTY END) DESC
				";
		$smt_stock = oci_parse($conn, $sql);
		oci_execute($smt_stock);
		//exdebug($sql);

		//$size_sum	= getErpProdSizeStock($prodcd, $colorcd, $sizecd, $type);

		$shop_stock = array();
		while($data = oci_fetch_array($smt_stock, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

			foreach($data as $k => $v)
			{
				$data[$k] = utf8encode($v);
			}

			if (/*$data[SHOPNM] =='' && */$data[PART_DIV]=='A' && $data[PART_NO] = '1801') {
				$data[SHOPNM] = "온라인매장";
				$data[BRAND]	= "B";
			}
			if ($data[SHOPNM]) {
				$shop_stock[shopnm][]         = $data[SHOPNM];
				$shop_stock[shopcd][]         = $data[PART_DIV].$data[PART_NO].$data[BRAND];
				$shop_stock[availqty][]       = (($prodcd=='BMC21890' && $sizecd=='77') || ($data[AVAILQTY] < 0))?0:$data[AVAILQTY];
			}
		}
		oci_free_statement($smt_stock);
		GetErpDBClose($conn);
	}

    return $shop_stock;
}

// 상품 해당 사이즈의 총재고 구하기(실시간)
function getErpProdSizeStock($prodcd, $colorcd, $sizecd, $type='delivery') {
		
	list($season_year) = pmysql_fetch(" SELECT season_year FROM tblproduct WHERE prodcode = '{$prodcd}' AND colorcode = '{$colorcd}' ");

	if($season_year){

		$requestParams = array(
			  'aStyleNo' => array($prodcd),
			  'aSeasonYear' => array($season_year),
			  'aColorCode' => array($colorcd),
			  'aSizeCode' => array($sizecd)
		);

		$client = new SoapClient('http://swerp.sw.co.kr/shinwonmall/service.asmx?wsdl');
		$response = $client->service_GetStyleStockQTy($requestParams);
		$xml = simplexml_load_string($response->service_GetStyleStockQTyResult->any);

		$check_season = substr($prodcd,1,1);
		foreach($xml->NewDataSet->STOCK as $list) 
		{
			//20170908 신원 권정수대리 요청으로 2017년 추동 상품 안전재고 -1 설정
			//20171128 신원 고효서 주임 요청으로 해제
			//20171219 신원 권정수대리 요청으로 -10 복구 
			if($check_season == 'R' && $season_year == '2017'){
				$data['SUMQTY']	= $list->SUMQTY - 1;
			}else{
				$data['SUMQTY']	= $list->SUMQTY - 10;
			}

			$size_sum = (($prodcd=='BMC21890' && $sizecd=='77') || ($data['SUMQTY'] < 0))?0:$data['SUMQTY'];

		}
	}
	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
		//exdebug($size_sum);
		//exit;
	}

    return $size_sum;

}

// 해당 상품 정책가 구하기.(실시간) - 쇼핑몰 db의 판매가로 가져온다.
function getErpPolicyPrice($prodcd, $colorcd) {
		
	list($season_year) = pmysql_fetch(" SELECT season_year FROM tblproduct WHERE prodcode = '{$prodcd}' AND colorcode = '{$colorcd}' ");

    list($polprice)=pmysql_fetch_array(pmysql_query("SELECT sellprice FROM tblproduct WHERE prodcode='".$prodcd."' AND colorcode ='".$colorcd."' AND season_year='".$season_year."' "));

    return $polprice;
}

// 해당 상품 tagprice 구하기.(실시간) - 쇼핑몰 db의 소비자가로 가져온다.
function getErpTagPrice($prodcd, $colorcd) {
		
	list($season_year) = pmysql_fetch(" SELECT season_year FROM tblproduct WHERE prodcode = '{$prodcd}' AND colorcode = '{$colorcd}' ");

    list($tagprice)=pmysql_fetch_array(pmysql_query("SELECT consumerprice FROM tblproduct WHERE prodcode='".$prodcd."' AND colorcode ='".$colorcd."' AND season_year='".$season_year."' "));

    return $tagprice;
}

// 해당 상품의 택가, 정책가, 사이즈의 총재고 구하기(실시간)
function getErpPriceNStock($prodcd, $colorcd, $sizecd='', $shopcd='', $type='delivery') {
			
	list($season_year) = pmysql_fetch(" SELECT season_year FROM tblproduct WHERE prodcode = '{$prodcd}' AND colorcode = '{$colorcd}' ");

	if ($season_year == '') {
		/*erp재고 가져오는부분 임시로 강제 수량 설정 실적용시 삭제 2017-02-22*/
		$shop_stock="";
		$size_stock[size]       = $sizecd;
		$size_stock[sumqty]     = 20;
		$size_stock[polprice]   = "";
		$size_stock[tagprice]   = "";
	} else{

		if($sizecd) $sizecd = $sizecd;
		else $sizecd = "%";

		$requestParams = array(
			  'aStyleNo' => array($prodcd),
			  'aSeasonYear' => array($season_year),
			  'aColorCode' => array($colorcd),
			  'aSizeCode' => array($sizecd)
		);

		$client = new SoapClient('http://swerp.sw.co.kr/shinwonmall/service.asmx?wsdl');
		$response = $client->service_GetStyleStockQTy($requestParams);
		$xml = simplexml_load_string($response->service_GetStyleStockQTyResult->any);

		$check_season = substr($prodcd,1,1);
		$size_stock = array();
		// 정책가
		list($polprice)=pmysql_fetch_array(pmysql_query("SELECT sellprice FROM tblproduct WHERE prodcode='".$prodcd."' AND colorcode ='".$colorcd."' AND season_year='".$season_year."' "));

		// 소비자가
		list($tagprice)=pmysql_fetch_array(pmysql_query("SELECT consumerprice FROM tblproduct WHERE prodcode='".$prodcd."' AND colorcode ='".$colorcd."' AND season_year='".$season_year."' "));
		
		foreach($xml->NewDataSet->STOCK as $list) 
		{

			if (!$shopcd) {
				//20170908 신원 권정수대리 요청으로 2017년 추동 상품 안전재고 -1 설정
				if($check_season == 'R' && $season_year == '2017'){
					$data['SUMQTY']	= $list->SUMQTY - 1;
				}else{
					$data['SUMQTY']	= $list->SUMQTY - 10;
				}
			}else{
				$data['SUMQTY']	= $list->SUMQTY - 1;
			}

			$size_stock[size]		=	trim($list->SIZECD);
			$size_stock[sumqty]	=	(($prodcd=='BMC21890' && trim($data[SIZECD])=='77') || ($data['SUMQTY'] < 0))?0:trim($data['SUMQTY']);
			$size_stock[polprice]   = $polprice;
			$size_stock[tagprice]   = $tagprice;
		}
	}

	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
		//exdebug($size_stock);
		//exit;
	}

	return $size_stock;

}

// ERP 창고별 재고 가져오기
function getErpProdOnlineShopStock($prodcd, $colorcd, $sizecd) {
			
	list($season_year) = pmysql_fetch(" SELECT season_year FROM tblproduct WHERE prodcode = '{$prodcd}' AND colorcode = '{$colorcd}' ");
	
	$conn = GetErpDBConn();

	$data[p_location]	= "";
	$data[p_err_code]	= -9999;
	$data[p_err_text]	= "";
	$p_crypt_key			= "Twins";
	
	$sql = "
			BEGIN 
				PA_ONLINE_MALL.SP_GET_STOCK_LOCATION_PROCESS(
				   P_STYLE_NO => :P_STYLE_NO,
				   P_SEASON_YEAR => :P_SEASON_YEAR,
				   P_COLOR_CODE => :P_COLOR_CODE,
				   P_SIZE_CODE => :P_SIZE_CODE,
				   P_LOCATION => :P_LOCATION,
				   P_ERR_CODE => :P_ERR_CODE,
				   P_ERR_TEXT => :P_ERR_TEXT
				);
			END;
		";
	$smt_erp = oci_parse($conn, $sql);

	//exdebug($sql);
	//입력값
	oci_bind_by_name($smt_erp, ':P_STYLE_NO', $prodcd);
	oci_bind_by_name($smt_erp, ':P_SEASON_YEAR', $season_year);
	oci_bind_by_name($smt_erp, ':P_COLOR_CODE', $colorcd);
	oci_bind_by_name($smt_erp, ':P_SIZE_CODE', $sizecd);

	//출력값
	oci_bind_by_name($smt_erp, ':P_LOCATION', $data[p_location],1000);
	oci_bind_by_name($smt_erp, ':P_ERR_CODE', $data[p_err_code],32);
	oci_bind_by_name($smt_erp, ':P_ERR_TEXT', $data[p_err_text],300);

	$stid   = oci_execute($smt_erp);
	foreach($data as $k => $v)
	{
		$data[$k] =  trim($v)==''?'':trim(utf8encode($v));
	}
	$c_arr	= explode("^", $data[p_location]);
	
	$res_data	= array();
	$res_shopinfo	= array();
	foreach($c_arr as $key) {
		if (trim($key)!='') {
			$v_arr	= explode(":", trim($key));
			$res_shopinfo[$v_arr[0]]	= ($prodcd=='BMC21890' && $sizecd=='77')?0:$v_arr[1];		
		}
	}

	$res_data[p_err_code]	= $data[p_err_code];
	$res_data[p_err_text]		= $data[p_err_text];
	$res_data[p_data]			= $data[p_err_code]=='0'?$res_shopinfo:"";	

	oci_free_statement($smt_erp);
	GetErpDBClose($conn);

	return $res_data;
}

// 쇼핑몰 전체 EPR 브랜드코드별 쇼핑몰 브랜드코드, 벤더코드 구하기
function getAllBrand() {
	$brand_vender = array();
	
	$sql = "SELECT* FROM tblproductbrand WHERE brandcd !='' ";
	$result = pmysql_query($sql,get_db_conn());
	while($rows = pmysql_fetch_object($result)) {
		if ($rows->brandcd) {
			$brand_vender[$rows->brandcd][BRAND]	= $rows->bridx;
			$brand_vender[$rows->brandcd][VENDER]	= $rows->vender;
		}
	}
	return $brand_vender;
}

// 브랜드 그룹별 할인율 구하기
function getDiscountRate() {
	$discountrate = array();
	
	$sql = "SELECT* FROM tblproductdiscount ";
	$result = pmysql_query($sql,get_db_conn());
	while($rows = pmysql_fetch_object($result)) {
		$discountrate[$rows->brandcd][$rows->season_year][$rows->season][$rows->item_gubun]	= $rows->discount;
	}
	return $discountrate;
}

// 임직원 잔여 마일리지 구하기(실시간) - 필요없음
//function getErpStaffPoint($staffno) {
function getErpStaffPoint($id) {

   /* global $erp_account;

    $conn = GetErpDBConn();

    $sql = "SELECT * FROM ".$erp_account.".IF_ONLINE_STAFFCARDLIMIT_V a WHERE a.EMPNO = '".$empno."'";
    $smt_stock = oci_parse($conn, $sql);
    oci_execute($smt_stock);
    //exdebug($sql);

    $data = oci_fetch_assoc($smt_stock);
	$remainamt = $data['REMAINAMT'];

    oci_free_statement($smt_stock);
    GetErpDBClose($conn);*/

    list($staff_reserve) = pmysql_fetch("Select staff_reserve From tblmember Where id = '".$id."'");

    return $staff_reserve;
}

function getMemberNo($id) {

    list($mem_seq) = pmysql_fetch("Select mem_seq From tblmember Where id = '".$id."'");

    return $mem_seq;
}

// 관리자에서 등록하는 값이 사번 empno 값이라고 함.2016-10-25 - 필요없음
function getStaffCardNo($empno) {

    /*global $erp_account;

    $conn = GetErpDBConn();

    $sql = "SELECT * FROM ".$erp_account.".IF_ONLINE_STAFFCARDLIMIT_V a WHERE a.EMPNO = '".$empno."'";
    $smt_stock = oci_parse($conn, $sql);
    oci_execute($smt_stock);
    //exdebug($sql);

    $data = oci_fetch_assoc($smt_stock);
	$staffcardno = $data['STAFFCARDNO'];

    oci_free_statement($smt_stock);
    GetErpDBClose($conn);*/

    return $staffcardno;
}

// 임직원 EmpNo 구하기(실시간)..관리자에서 등록하는 값이 사번 empno 값이라고 함.2016-10-25
function getErpStaffEmpNo($id) {

    list($erp_emp_id) = pmysql_fetch("Select erp_emp_id From tblmember Where id = '".$id."'");

    return trim($erp_emp_id);
}

function getPaymethod($ordercode) {

    list($paymethod) = pmysql_fetch("Select paymethod From tblorderinfo Where ordercode = '".$ordercode."'");

    //$arrPay = array("V"=>"abank","O"=>"vbank","Q"=>"vbank(escrow)","C"=>"card","M"=>"mobile","Y"=>"payco");
    $arrPay = array("V"=>"abank","O"=>"vbank","Q"=>"vbank","C"=>"card","M"=>"mobile","Y"=>"payco","B"=>"cash");

    return $arrPay[$paymethod[0]];
}

// TA_OM011.PAY_GB
function getErpPaygb($val) {

    $arrDetail = array(
					'abank' => '11',  //계좌이체
					'vbank' => '12',  //가상계좌
					'card'  => '20',  //신용카드
					'mobile' => '21',   //모바일
					'cash' => '22',   //무통장(0원결제)
					'payco' => '30'   //PAYCO
                    );

    return $arrDetail[$val];
}

// TA_OM010.ORDER_STEP
function getErpOrderStep($val) {
	

	$arrStep		= array(
					1	=> "P",				// 결제완료
					2	=> "R",				// 배송준비중
					3	=> "D",				// 배송중
					4	=> "Y",				// 배송완료
					44	=> "C",				// 취소완료
					);

    return $arrStep[$val];
}

function setSyncInfo($ordercode, $opidx, $order_type) {
    if($order_type=='I'){
        $ord_sql="select * from tblorderproduct where ordercode='{$ordercode}'";
        $ord_result = pmysql_query($ord_sql, get_db_conn());
        while($row = pmysql_fetch_object( $ord_result )){
            $sql = "INSERT INTO tblsync_check ( ";
            $sql.= "ordercode, order_type, idx, reg_dt ";
            $sql.= " ) VALUES ( ";
            $sql.= " '".$ordercode."', '".$order_type."', '".$row->idx."', '".date('YmdHis')."' ";
            $sql.= " ) RETURNING idx ";
            $result = pmysql_query( $sql, get_db_conn() );
        }
    }else{
        #취소시 입력
    }


    return $result;
}

//쿠폰정보 가져오기
function getCouponInfo($ordercode, $opidx) {

    $sql = "Select  a.coupon_code, b.coupon_name From tblcoupon_order a Join tblcouponinfo b on a.coupon_code = b.coupon_code Where a.ordercode = '".$ordercode."' And op_idx = ".$opidx." ";
    list($coupon_code, $coupon_name) = pmysql_fetch($sql);

    return $coupon_code."^".$coupon_name;
}

//O2O 수수료율 가져오기
function getFeeRate($store_code, $n_date, $o2o_gb) {
    $sql = "Select fee_rate From tblstore_o2o_fees Where store_code = '".$store_code."' AND apply_fdate <='".$n_date."' AND apply_tdate >='".$n_date."' and o2o_gb='".$o2o_gb."' ";
	//exdebug($sql);
    list($fee_rate) = pmysql_fetch($sql);

	$fee_rate	= $fee_rate?$fee_rate:0;

    return $fee_rate;
}

// ERP order_app 용 정보 구하기.
function getPaymethodInfo($ordercode) {

    $app_arr = array();

    $paymethod = getPaymethod($ordercode);
    if($paymethod == "abank") $table = "tblptranslog";
    elseif($paymethod == "vbank") $table = "tblpvirtuallog";
    elseif($paymethod == "card") $table = "tblpcardlog";
    elseif($paymethod == "mobile") $table = "tblpmobilelog";
    elseif($paymethod == "payco") $table = "tblppaycolog";
    elseif($paymethod == "cash") $table = "tblorderinfo";

    $sql = "Select * From ".$table." Where ordercode = '".$ordercode."'";
   // exdebug($sql);
    $ret = pmysql_query($sql);
    if($row = pmysql_fetch_array($ret)){

        $app_arr[detailgb]      = getErpPaygb($paymethod);
        $app_arr[appamt]        = $row[price];
        if($paymethod == "abank") {
            $app_arr[months]        = 0;
            $app_arr[validityterm]  = "";
            $app_arr[approvalno]    = $row[trans_code];
            $app_arr[cardno]        = "";
            $app_arr[cardcompanycd] = "";
            $app_arr[cardcompanynm] = "";
            $app_arr[bankcd] = $row[bank_code];
            $app_arr[banknm] = $row[bank_name];
            $app_arr[bankaccount] = "";
            $app_arr[escrow] = "";
        } elseif($paymethod == "vbank") {
            $app_arr[months]        = "";
            $app_arr[validityterm]  = "";
            $app_arr[approvalno]    = "";
            $app_arr[cardno]        = "";
            $app_arr[cardcompanycd] = "";
            $app_arr[cardcompanynm] ="";
            $app_arr[bankcd] = $row[bank_code];
            $tmp = explode(" ", $row[pay_data]);
            $app_arr[banknm] = trim($tmp[0]);
            $app_arr[bankaccount] = $row[account];
            $app_arr[escrow] = ($row[paymethod]=='Q')?"Y":"";
        } elseif($paymethod == "card") {
            $app_arr[months]        = "";
            $app_arr[validityterm]  = "";
            $tmp = explode(":", $row[pay_data]);
            $app_arr[approvalno]    = trim($tmp[1]);
            $app_arr[cardno]        = "";
            $app_arr[cardcompanycd] = $row[cardcode];
            $app_arr[cardcompanynm] = $row[cardname];
            $app_arr[bankcd] = "";
            $app_arr[banknm] = "";
            $app_arr[bankaccount] = "";
            $app_arr[escrow] = "";
        } elseif($paymethod == "mobile") {
            $app_arr[months]        = 0;
            $app_arr[validityterm]  = "";
            $app_arr[approvalno]    = trim(str_replace("승인번호 : ",$row[pay_data]));
            $app_arr[cardno]        = "";
            $app_arr[cardcompanycd] = ""; 
            $app_arr[cardcompanynm] = "";
            $app_arr[bankcd] = "";
            $app_arr[banknm] = "";
            $app_arr[bankaccount] = "";
            $app_arr[escrow] = "";
        } elseif($paymethod == "payco") {
            $app_arr[months]        = "";
            $app_arr[validityterm]  = "";
            $app_arr[approvalno]    = $row[pay_data];
            $app_arr[cardno]        = "";
            $app_arr[cardcompanycd] = $row[cardcode];
            $app_arr[cardcompanynm] = $row[cardnamSe];
            $app_arr[bankcd] = "";
            $app_arr[banknm] = "";
            $app_arr[bankaccount] = "";
            $app_arr[escrow] = "";
        }
        $app_arr[appdt]				= substr($row[okdate], 0, 8);
        $app_arr[apptime]			= substr($row[okdate], -6);
		
		if($paymethod == "cash") {
			$app_arr[appamt]        = $row[price]-$row[dc_price]-$row[point]-$row[reserve]+$row[deli_price];
            $app_arr[months]        = "";
            $app_arr[validityterm]  = "";
            $app_arr[approvalno]    = "";
            $app_arr[cardno]        = "";
            $app_arr[cardcompanycd] = "";
            $app_arr[cardcompanynm] = "";
            $app_arr[bankcd] = "";
            $app_arr[banknm] = "";
            $app_arr[bankaccount] = "";
            $app_arr[escrow] = "";
			$app_arr[appdt]				= substr($row[bank_date], 0, 8);
			$app_arr[apptime]			= substr($row[bank_date], -6);
        }
    }

    return $app_arr;
}

// 일반주문 전송
function sendErporder($ordercode) {

    $conn = GetErpDBConn();

    sendErpOrderInfo($ordercode, $conn);
    sendErpOrderinfoApp($ordercode, $conn);
    sendErpOrderDeliInfo($ordercode, $conn);

    GetErpDBClose($conn);
}

// ERP 에 일반주문정보 전송
function sendErpOrderInfo($ordercode, $conn) {

    //global $conn;
    global $erp_account;

    $sql = "Select 	a.id, a.ordercode, b.idx, a.oi_step1, a.oi_step2, b.op_step, a.regdt, a.bank_date, 
                    a.sender_name, a.sender_tel2, sender_tel, a.paymethod, 
                    a.receiver_name, a.receiver_addr, a.receiver_tel1, a.receiver_tel2, a.order_msg2, 
                    a.oldordno, b.opt2_name, b.option_quantity, b.price, ((b.price+b.option_price)*b.option_quantity) as sum_price, 
                    b.deli_price, b.coupon_price, c.prodcode, c.colorcode, c.season_year, a.staff_order, b.delivery_type, b.reservation_date, b.store_code, b.pr_code, b.cooper_order, b.use_point, b.use_epoint, b.reserve,
					b.staff_price, b.cooper_price, a.pg_ordercode, c.brandcd
            From	tblorderinfo a 
            Join	tblorderproduct b on a.ordercode = b.ordercode 
            Join	tblproduct c on b.productcode = c.productcode 
            Join	tblsync_check d on d.idx = b.idx and d.order_type='I' and d.erp_product_yn='N'
            Where	a.ordercode = '".$ordercode."' 
            Order by b.idx asc 
            ";
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }
        $order_no				= $ordercode;						// 주문번호
        $order_detail_no		= $data[idx];						// 주문상세번호
        $style_order_no		= $data[pr_code];				// 품목별 주문번호
		$order_date			= substr($ordercode,0,8);		// 주문날짜

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";							
			$emp_no				= "";
			$member_id			= "";
		} else {			
            list($erp_shopmem_id, $erp_emp_id, $company_code) = pmysql_fetch("Select erp_shopmem_id, erp_emp_id, company_code From tblmember Where id = '".$data[id]."'");
			$member_id			= $erp_shopmem_id;
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
				$emp_no			= $erp_emp_id;
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
				$emp_no			= "";
				list($group_name)=pmysql_fetch("select group_name from tblcompanygroup where group_code='".$company_code."'");// 임직원 포인트
				$cooper_nm = $group_name;
			} else {
				$order_type		= "A";
				$emp_no			= "";
			}
		}

        $style_no			= $data[prodcode];					// 품번
        $season_year		= $data[season_year];			// 시즌년도
        $color_code		= $data[colorcode];				// 색상
		$size_code		= str_pad($data[opt2_name],"3"," ",STR_PAD_LEFT);				// 사이즈

        $shopcd			= $data[delivery_type]=='0'?'A1801B':$data[store_code];
		$part_div			= substr($shopcd,0,1);				// 유통망
		$part_no				= substr($shopcd,1,4);				// 매장코드
		$brand				= substr($shopcd,5,1);				// 브랜드
		
        $o2o_gb = $data[delivery_type];										// O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)

        /*if($data[delivery_type] == "0") {
			$o2o_gb = ($part_div=='A'&&$part_no='1801')?"0":"2";       // 본사/매장발송
        } elseif($data[delivery_type] == "1") {
			$o2o_gb = "1";																// 매장픽업
        } elseif($data[delivery_type] == "2") {
			$o2o_gb = "3";																// 당일수령
		}*/

		if ($part_div=='A'&&$part_no=='1801') {
			 $o2o_gb			= "0";										// O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)
			$part_div			= "O";				// 유통망
			$part_no				= "1111";				// 매장코드
			$brand				= $data[brandcd];				// 브랜드
		}

		//$order_step		= getErpOrderStep($data[op_step]);			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)
		$order_step		= "P";			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)

		$sender_name	= $data[sender_name];			// 보낸이

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        $r_address = "(".$zonecode.") ".$address;
		$sender_addr			= $r_address;							// 보낸이주소
		$data[sender_tel2]	= $data[sender_tel2]=='02－－'?'':$data[sender_tel2];
		$sender_tel_no		= str_replace('-', '', $data[sender_tel2]);				// 보낸이전화번호
		$sender_cell_no		= str_replace('-', '', $data[sender_tel]);				// 보낸이휴대폰번호
		$sender_memo		= $data[order_msg2];				// 보낸이 메모
		$rcver_name			= $data[receiver_name];			// 받는이
		$rcver_addr			= $r_address;							// 받는이주소
		$data[receiver_tel1]	= $data[receiver_tel1]=='02－－'?'':$data[receiver_tel1];
		$rcver_tel_no			= str_replace('-', '', $data[receiver_tel1]);				// 받는이전화번호
		$rcver_cell_no		= str_replace('-', '', $data[receiver_tel2]);				// 받는이휴대폰번호

		$order_qty				= $data[option_quantity];			// 주문수량
		$order_price			= $data[price];						// 주문단가
		$order_amt				= $data[sum_price];				// 주문금액
		$rsale_amt				= $data[sum_price] - ($data[use_point] + $data[use_epoint] + $data[coupon_price]);				// 실결제금액
		$supply_amt			= round($order_amt / 1.1);		// VAT제외금액
		$vat_amt				= round($supply_amt * 0.1);		// VAT금액
		
        $coupontemp     = getCouponInfo($order_no, $order_detail_no);
        if($coupontemp) {
            $couponinfo		= explode("^", $coupontemp);
            $coupon_no		= $couponinfo[0];				// 쿠폰번호
            $coupon_name	= str_replace(" 쿠폰","",$couponinfo[1]);				// 쿠폰명
        } else {
            $coupon_no		= "";									// 쿠폰번호
            $coupon_name	= "";									// 쿠폰명
        }
        $coupon_amt			= $data[coupon_price];		// 쿠폰금액

		$use_point					= $data[use_point];			// 사용포인트
		$occur_point				= $data[reserve];			// 적립포인트
		$use_epoint				= $data[use_epoint];		// 사용E포인트
		$emp_sale_amt			= $data[staff_price];		// 임직원할인금액
		$cooper_sale_amt		= $data[cooper_price];	// 협력업체할인금액
		$order_payment_no		= $data[pg_ordercode];	// 결제번호

		// 임시 추가 20170906
		if($order_type == "A" && $emp_sale_amt > 0) {
				$emp_no			= "333333";
		}

        $pay_date    = $data[bank_date]?substr($data[bank_date],0,8):"";	// 결제일


		$sale_confm_date		= ($o2o_gb=='0')?substr($data[bank_date],0,8):"";		// 매출확정일


        /*$remark         = getPaymethod($ordercode);
        if($data[paymethod][0] == "O") $remark .= " : ".$data[bank_date];
        $deliveryamt    = $data[deli_price];*/
        //exdebug($deli_price);

        // 2016-12-23 동일상품 복수개 주문시 쿠폰 금액 계산식이 erp 에서 문제가 된다고 함. 쿠폰 금액 제외식으로 수정 처리..
        //$couponamt      = $data[coupon_price] / $req_qty;
        //$deposityn      = "Y";

        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						SALE_CONFM_DATE,
						SEND_DATE
                    )
                    values 
                    (
                        '".$order_no."',
                        '".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
                        '".$style_order_no."',
                        '".$order_date."',
                        '".$order_type."',
                        '".$style_no."',
                        '".$season_year."',
                        '".$color_code."',
                        '".$size_code."',
                        '".$part_div."',
                        '".$part_no."',
                        '".$brand."',
                        '".$o2o_gb."',
                        '".$order_step."',
                        '".euckrencode($sender_name)."',
                        '".euckrencode($sender_addr)."',
                        '".$sender_tel_no."',
                        '".$sender_cell_no."',
                        '".euckrencode($sender_memo)."',
                        '".euckrencode($rcver_name)."',
                        '".euckrencode($rcver_addr)."',
                        '".$rcver_tel_no."',
                        '".$rcver_cell_no."',
                        '".(is_numeric($order_qty) ? $order_qty : 0)."',
                        '".(is_numeric($order_price) ? $order_price : 0)."',
                        '".(is_numeric($order_amt) ? $order_amt : 0)."',
                        '".(is_numeric($supply_amt) ? $supply_amt : 0)."',
                        '".(is_numeric($vat_amt) ? $vat_amt : 0)."',
                        '".$coupon_no."',
                        '".euckrencode($coupon_name)."',
                        '".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
                        '".$member_id."',
                        '".$emp_no."',
                        '".euckrencode($cooper_nm)."',
                        '".(is_numeric($use_point) ? $use_point : 0)."',
                        '".(is_numeric($occur_point) ? $occur_point : 0)."',
                        '".(is_numeric($use_epoint) ? $use_epoint : 0)."',
                        '".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
                        '".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
                        '".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
                        '".$order_payment_no."',
                        '".$pay_date."',
                        '".$sale_confm_date."',
                        SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);

        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
			//exdebug($error);
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }else{
            $usql = "update tblsync_check set erp_product_yn = 'Y' where ordercode = '".$ordercode."' and idx={$data[idx]} and order_type='I'";
            pmysql_query($usql);
        }
        
    }
}

// ERP 에 일반 결제정보 전송
function sendErpOrderinfoApp($ordercode, $conn) {

    //global $conn;
    global $erp_account;

    //결제수단 정보 가져오자.
    //결제수단에 따른 참조 테이블 정의하자.
    $apparr = getPaymethodInfo($ordercode);
    
    $sql = "Select pg_ordercode, staff_order, cooper_order From tblorderinfo Where ordercode = '".$ordercode."' ";
    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());

    if($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }

		$order_payment_no		= $data[pg_ordercode];	// 결제번호

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";		
		} else {			
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
			} else {
				$order_type		= "A";
			}
		}
		
		$order_date				= substr($ordercode,0,8);			// 주문날짜
		$pay_gb						= $apparr[detailgb];				// 결제구분
		$aprvl_amt					= $apparr[appamt];					// 승인금액
		$aprvl_no					= $apparr[approvalno];			// 승인번호
		$issuer_name				= $apparr[cardcompanynm];	// 카드사명
		$aprvl_date					= $apparr[appdt];					// 승인일자
		$aprvl_time					= $apparr[apptime];				// 승인시간
		$bank_name				= $apparr[banknm];				// 은행명
		$account_no				= $apparr[bankaccount];			// 계좌번호
		$escro_yn					= $apparr[escrow]?$apparr[escrow]:"N";					// 에스크로여부
		$bigo							= "";										// 비고

        $erp_sql2 = "insert into ".$erp_account.".TA_OM011 
                    (
						ORDER_PAYMENT_NO,
						ORDER_PAYMENT_SEQ,
						ORDER_TYPE,
						ORDER_DATE,
						PAY_GB,
						APRVL_AMT,
						APRVL_NO,
						ISSUER_NAME,
						APRVL_DATE,
						APRVL_TIME,
						BANK_NAME,
						ACCOUNT_NO,
						ESCRO_YN,
						BIGO,
						SEND_DATE
                    )
                    values 
                    (
                        '" . $order_payment_no. "',
						NVL((SELECT MAX(ORDER_PAYMENT_SEQ) FROM TA_OM011 WHERE ORDER_PAYMENT_NO = '".$order_payment_no."' ),0) + 1,
                        '" . $order_type. "',
                        '" . $order_date. "',
                        '" . $pay_gb. "',
                        '" . (is_numeric($aprvl_amt) ? $aprvl_amt : 0) . "',
                        '" . $aprvl_no. "',
                        '" . euckrencode($issuer_name). "',
                        '" . $aprvl_date. "',
                        '" . $aprvl_time. "',
                        '" . euckrencode($bank_name). "',
                        '" . $account_no. "',
                        '" . $escro_yn. "',
                        '" . $bigo. "',
						SYSDATE
                    )";
        //exdebug($erp_sql2);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql2 : ".$erp_sql2."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);
        
        $smt_erp = oci_parse($conn, $erp_sql2);
        $stid = oci_execute($smt_erp);
        if (!$stid) {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n" . date("Y-m-d H:i:s ") . realpath($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME']) . $error['message'] . $bt[0]['line'], 3, "/tmp/error_log_sw_erp");
            error_log($erp_sql2 . "\r\n", 3, "/tmp/error_log_sw_erp");
        } else {
            $usql = "update tblsync_check set erp_info_yn = 'Y' where ordercode = '" . $ordercode . "' and order_type='I'";
            //pmysql_query($usql);
        }
        
    }
}

// ERP 에 일반주문 배송비정보 전송
function sendErpOrderDeliInfo($ordercode, $conn) {

    //global $conn;
    global $erp_account;

    $sql = "Select 	max(a.id) as id, a.ordercode as ordercode, max(a.deli_price) as deli_price, max(a.pg_ordercode) as pg_ordercode 
            From	tblorderinfo a 
            Join	tblorderproduct b on a.ordercode = b.ordercode 
            Join	tblproduct c on b.productcode = c.productcode 
            Where	a.ordercode = '".$ordercode."' 
            group by a.ordercode
            ";
    $result = pmysql_query($sql, get_db_conn());

	if($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }

        $order_no				= $ordercode;						// 주문번호

		$delevery_fee				= $data[deli_price];			// 결제번호
		$order_payment_no		= $data[pg_ordercode];	// 결제번호

        $erp_sql = "insert into ".$erp_account.".TA_OM018 
                    (
						ORDER_PAYMEMT_NO,
						ORDER_PAYMENT_SEQ,
						DELEVERY_FEE,
						EXT_DELEVERY_FEE,
						SEND_DATE
                    )
                    values 
                    (
                        '".$order_payment_no."',
						NVL((SELECT MAX(ORDER_PAYMENT_SEQ) FROM TA_OM018 WHERE ORDER_PAYMEMT_NO = '".$order_payment_no."' ),0) + 1,
                        '".$delevery_fee."',
                        '0',
                        SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);

        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }
	}
}

/** 
 *  매장 정보를 변경한다
 *  싱크커머스에서 배송지 정보 변경되면, api/change_status.php에서 호출함.
**/ 
function sendErpChangeShop($ordercode, $idx, $shopcd, $deli_type='') {

	list($old_store_code, $old_reg_dt) = pmysql_fetch("Select old_store_code, regdt From tblorderproduct_store_code Where ordercode = '".$ordercode."' and idx = '".$idx."' order by no desc limit 1");

	 list($old_shopcd, $delivery_type) = pmysql_fetch("Select store_code, delivery_type From tblorderproduct Where ordercode = '".$ordercode."' and idx = ".$idx."");

	if($old_store_code == '') {
		$old_shopcd	= 'A1801B';
		$shopcd		= ($shopcd!='A1801B'&&$shopcd!='')?$shopcd:'A1801B';
		if ($deli_type=='') {
			list($sscnt) = pmysql_fetch("select count(*) from tblorderproduct_store_change where ordercode = '".$ordercode."' and idx = '".$idx."' ");
			if ($sscnt == '1' && $old_reg_dt == '') {
				$old_shopcd	= '';
			}
		}
	} else {
		if($old_shopcd=='' && $old_store_code=='A1801B') {
			$old_shopcd	= 'A1801B';
		}
	}

	$regdt	= date("YmdHis");

	//매장 변경내역 저장.
	$sql = "INSERT INTO tblorderproduct_store_code(ordercode, idx, store_code, old_store_code, regdt) VALUES ('{$ordercode}','{$idx}','{$shopcd}','{$old_shopcd}','{$regdt}')";
	//exdebug($sql);
	//exit;
	$rtn=pmysql_query($sql,get_db_conn());

	$shopcd	= ($shopcd!='A1801B')?$shopcd:'';

	//선택매장정보(매장발송)
	$sql = "UPDATE tblorderproduct SET store_code='{$shopcd}' ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$sql.= "AND idx = {$idx} ";
	$rtn=pmysql_query($sql,get_db_conn());
	
	//매장변경 ERP 전송
	if($delivery_type!='0' || $deli_type=='2') sendErporderShopChange($ordercode, $idx, $deli_type);
}
/** 
 *  창고 발송으로 변경한다
 *  싱크커머스에서 배송지 정보 변경되면, api/change_status.php에서 호출함.
**/ 
function sendErpChangedeli($ordercode, $idx, $shopcd, $deli_type='') {

/*
	list($old_store_code, $old_reg_dt) = pmysql_fetch("Select old_store_code, regdt From tblorderproduct_store_code Where ordercode = '".$ordercode."' and idx = '".$idx."' order by no desc limit 1");

	 list($old_shopcd, $delivery_type) = pmysql_fetch("Select store_code, delivery_type From tblorderproduct Where ordercode = '".$ordercode."' and idx = ".$idx."");

	if($old_store_code == '') {
		$old_shopcd	= 'A1801B';
		$shopcd		= ($shopcd!='A1801B'&&$shopcd!='')?$shopcd:'A1801B';
		if ($deli_type=='') {
			list($sscnt) = pmysql_fetch("select count(*) from tblorderproduct_store_change where ordercode = '".$ordercode."' and idx = '".$idx."' ");
			if ($sscnt == '1' && $old_reg_dt == '') {
				$old_shopcd	= '';
			}
		}
	} else {
		if($old_shopcd=='' && $old_store_code=='A1801B') {
			$old_shopcd	= 'A1801B';
		}
	}

	$regdt	= date("YmdHis");

	//매장 변경내역 저장.
	$sql = "INSERT INTO tblorderproduct_store_code(ordercode, idx, store_code, old_store_code, regdt) VALUES ('{$ordercode}','{$idx}','{$shopcd}','{$old_shopcd}','{$regdt}')";
	//exdebug($sql);
	//exit;
	$rtn=pmysql_query($sql,get_db_conn());

	$shopcd	= ($shopcd!='A1801B')?$shopcd:'';

	//선택매장정보(매장발송)
	$sql = "UPDATE tblorderproduct SET store_code='{$shopcd}' ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$sql.= "AND idx = {$idx} ";
	$rtn=pmysql_query($sql,get_db_conn());
	
	//매장변경 ERP 전송
	if($delivery_type!='0' || $deli_type=='2') sendErporderShopChange($ordercode, $idx, $deli_type);
*/

}


// 일반주문 매장변경시 전송
function sendErporderShopChange($ordercode, $idxs, $deli_type) {

    $conn = GetErpDBConn();

    sendErpOrderShopChangeInfo($ordercode, $idxs, $deli_type, $conn);

    GetErpDBClose($conn);
}

// ERP 에 일반주문정보 매장변경시 전송
function sendErpOrderShopChangeInfo($ordercode, $idxs, $deli_type, $conn) {

    //global $conn;
    global $erp_account;

    $sql = "Select 	a.id, a.ordercode, b.idx, a.oi_step1, a.oi_step2, b.op_step, a.regdt, a.bank_date, 
                    a.sender_name, a.sender_tel2, sender_tel, a.paymethod, 
                    a.receiver_name, a.receiver_addr, a.receiver_tel1, a.receiver_tel2, a.order_msg2, 
                    a.oldordno, b.opt2_name, b.option_quantity, b.price, ((b.price+b.option_price)*b.option_quantity) as sum_price, 
                    b.deli_price, b.coupon_price, c.prodcode, c.colorcode, c.season_year, a.staff_order, b.delivery_type, b.reservation_date, b.store_code, b.pr_code, b.cooper_order, b.use_point, b.use_epoint, b.reserve,
					b.staff_price, b.cooper_price, a.pg_ordercode, c.brandcd, b.pg_idx
            From	tblorderinfo a 
            Join	tblorderproduct b on a.ordercode = b.ordercode 
            Join	tblproduct c on b.productcode = c.productcode 
            Where	a.ordercode = '".$ordercode."' 
			and	    b.idx in ('".str_replace("|", "','", $idxs)."')
            Order by b.idx asc 
            ";
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }
        $order_no				= $ordercode;						// 주문번호
        $order_detail_no		= $data[idx];						// 주문상세번호
        $style_order_no		= $data[pr_code];				// 품목별 주문번호
		$order_date			= substr($ordercode,0,8);		// 주문날짜

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";							
			$emp_no				= "";
			$member_id			= "";
		} else {			
            list($erp_shopmem_id, $erp_emp_id) = list($erp_shopmem_id, $erp_emp_id, $company_code) = pmysql_fetch("Select erp_shopmem_id, erp_emp_id, company_code From tblmember Where id = '".$data[id]."'");
			$member_id			= $erp_shopmem_id;
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
				$emp_no			= $erp_emp_id;
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
				$emp_no			= "";
				list($group_name)=pmysql_fetch("select group_name from tblcompanygroup where group_code='".$company_code."'");// 임직원 포인트
				$cooper_nm = $group_name;
			} else {
				$order_type		= "A";
				$emp_no			= "";
			}
		}

        $style_no			= $data[prodcode];					// 품번
        $season_year		= $data[season_year];			// 시즌년도
        $color_code		= $data[colorcode];				// 색상
		$size_code		= str_pad($data[opt2_name],"3"," ",STR_PAD_LEFT);				// 사이즈		

		if ($deli_type == '2') {
			$data[delivery_type] = '0';
		}
		
		list($old_store_code, $cancel_date, $scnt) = pmysql_fetch("select a.old_store_code, a.regdt, (select count(*) from tblorderproduct_store_code where ordercode=a.ordercode and idx =a.idx) cnt
from tblorderproduct_store_code a where a.ordercode='".$ordercode."' and a.idx = '".$data[idx]."' order by a.no desc limit 1");
		if ($data[delivery_type] == '2' && $old_store_code == 'A1801B' && $scnt> 1) $old_store_code = '';

		$old_shopcd			= $old_store_code;
		$old_part_div			= substr($old_shopcd,0,1);				// 변경전 유통망
		$old_part_no			= substr($old_shopcd,1,4);				// 변경전 매장코드
		$old_brand				= substr($old_shopcd,5,1);				// 변경전 브랜드
		
		$old_o2o_gb = $data[delivery_type];										// 변경전 O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)
		/*if($data[delivery_type] == "0") {
			$old_o2o_gb = ($old_part_div=='A'&&$old_part_no='1801')?"0":"2";       // 본사/매장발송
		} elseif($data[delivery_type] == "1") {
			$old_o2o_gb = "1";																// 매장픽업
		} elseif($data[delivery_type] == "2") {
			$old_o2o_gb = "3";																// 당일수령
		}*/

		if ($old_part_div=='A'&&$old_part_no=='1801') {
			$old_o2o_gb			= "0";				// O2O구분
			$old_part_div			= "O";				// 유통망
			$old_part_no			= "1111";				// 매장코드
			$old_brand				= $data[brandcd];				// 브랜드
		}

		$shopcd			= $data[delivery_type]=='0'?'A1801B':$data[store_code];
		$part_div			= substr($shopcd,0,1);				// 유통망
		$part_no				= substr($shopcd,1,4);				// 매장코드
		$brand				= substr($shopcd,5,1);				// 브랜드

		if ($part_div=='A'&&$part_no=='1801') {
			$part_div		= "";		// 유통망
			$part_no			= "";		// 매장코드
			$brand			= "";		// 브랜드
		}
		
		$o2o_gb  = '2';										// O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)
		/*if($data[delivery_type] == "0") {
			$o2o_gb = ($part_div=='A'&&$part_no='1801')?"0":"2";       // 본사/매장발송
		} elseif($data[delivery_type] == "1") {
			$o2o_gb = "1";																// 매장픽업
		} elseif($data[delivery_type] == "2") {
			$o2o_gb = "3";																// 당일수령
		}*/

		$order_step		= 'R';			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)

		$sender_name	= $data[sender_name];			// 보낸이

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        $r_address = "(".$zonecode.") ".$address;
		$sender_addr			= $r_address;							// 보낸이주소
		$data[sender_tel2]	= $data[sender_tel2]=='02－－'?'':$data[sender_tel2];
		$sender_tel_no		= str_replace('-', '', $data[sender_tel2]);				// 보낸이전화번호
		$sender_cell_no		= str_replace('-', '', $data[sender_tel]);				// 보낸이휴대폰번호
		$sender_memo		= $data[order_msg2];				// 보낸이 메모
		$rcver_name			= $data[receiver_name];			// 받는이
		$rcver_addr			= $r_address;							// 받는이주소
		$data[receiver_tel1]	= $data[receiver_tel1]=='02－－'?'':$data[receiver_tel1];
		$rcver_tel_no			= str_replace('-', '', $data[receiver_tel1]);				// 받는이전화번호
		$rcver_cell_no		= str_replace('-', '', $data[receiver_tel2]);				// 받는이휴대폰번호

		$old_order_qty		= $data[option_quantity] * -1;	// 주문수량
		$order_qty				= $data[option_quantity];			// 주문수량
		$order_price			= $data[price];						// 주문단가
		$order_amt				= $data[sum_price];				// 주문금액
		$rsale_amt				= $data[sum_price] - ($data[use_point] + $data[use_epoint] + $data[coupon_price]);				// 실결제금액
		$supply_amt			= round($order_amt / 1.1);		// VAT제외금액
		$vat_amt				= round($supply_amt * 0.1);		// VAT금액
		if ($data[pg_idx]) {
			$ct_order_no			= $data[pg_ordercode];
			$ct_order_detail_no	= $data[pg_idx];
		} else {
			$ct_order_no			= $order_no;
			$ct_order_detail_no	= $order_detail_no;
		}
        $coupontemp     = getCouponInfo($ct_order_no, $ct_order_detail_no);
        if($coupontemp) {
            $couponinfo		= explode("^", $coupontemp);
            $coupon_no		= $couponinfo[0];				// 쿠폰번호
            $coupon_name	= str_replace(" 쿠폰","",$couponinfo[1]);				// 쿠폰명
        } else {
            $coupon_no		= "";									// 쿠폰번호
            $coupon_name	= "";									// 쿠폰명
        }
        $coupon_amt			= $data[coupon_price];		// 쿠폰금액

		$use_point					= $data[use_point];			// 사용포인트
		$occur_point				= $data[reserve];			// 적립포인트
		$use_epoint				= $data[use_epoint];		// 사용E포인트
		$emp_sale_amt			= $data[staff_price];		// 임직원할인금액
		$cooper_sale_amt		= $data[cooper_price];	// 협력업체할인금액
		$order_payment_no		= $data[pg_ordercode];	// 결제번호

		// 임시 추가 20170906
		if($order_type == "A" && $emp_sale_amt > 0) {
				$emp_no			= "333333";
		}

        $pay_date    = $data[bank_date]?substr($data[bank_date],0,8):"";	// 결제일


		$old_sale_confm_date	= ($old_o2o_gb=='0')?substr($data[bank_date],0,8):"";	// 이전매출확정일
		$sale_confm_date		= ($o2o_gb=='0')?substr($data[bank_date],0,8):"";		// 매출확정일
		$cancel_date				= substr($cancel_date,0,8);		// 취소일


        /*$remark         = getPaymethod($ordercode);
        if($data[paymethod][0] == "O") $remark .= " : ".$data[bank_date];
        $deliveryamt    = $data[deli_price];*/
        //exdebug($deli_price);

        // 2016-12-23 동일상품 복수개 주문시 쿠폰 금액 계산식이 erp 에서 문제가 된다고 함. 쿠폰 금액 제외식으로 수정 처리..
        //$couponamt      = $data[coupon_price] / $req_qty;
        //$deposityn      = "Y";
	
		// 매장변경전 주문정보
        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						SALE_CONFM_DATE,
						CANCEL_DATE,
						SEND_DATE
                    )
                    values 
                    (
                        '".$order_no."',
                        '".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
                        '".$style_order_no."',
                        '".$order_date."',
                        '".$order_type."',
                        '".$style_no."',
                        '".$season_year."',
                        '".$color_code."',
                        '".$size_code."',
                        '".$old_part_div."',
                        '".$old_part_no."',
                        '".$old_brand."',
                        '".$old_o2o_gb."',
                        '".$order_step."',
                        '".euckrencode($sender_name)."',
                        '".euckrencode($sender_addr)."',
                        '".$sender_tel_no."',
                        '".$sender_cell_no."',
                        '".euckrencode($sender_memo)."',
                        '".euckrencode($rcver_name)."',
                        '".euckrencode($rcver_addr)."',
                        '".$rcver_tel_no."',
                        '".$rcver_cell_no."',
                        '".(is_numeric($old_order_qty) ? $old_order_qty : 0)."',
                        '".(is_numeric($order_price) ? $order_price : 0)."',
                        '".(is_numeric($order_amt) ? $order_amt : 0)."',
                        '".(is_numeric($supply_amt) ? $supply_amt : 0)."',
                        '".(is_numeric($vat_amt) ? $vat_amt : 0)."',
                        '".$coupon_no."',
                        '".euckrencode($coupon_name)."',
                        '".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
                        '".$member_id."',
                        '".$emp_no."',
                        '".euckrencode($cooper_nm)."',
                        '".(is_numeric($use_point) ? $use_point : 0)."',
                        '".(is_numeric($occur_point) ? $occur_point : 0)."',
                        '".(is_numeric($use_epoint) ? $use_epoint : 0)."',
                        '".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
                        '".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
                        '".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
                        '".$order_payment_no."',
                        '".$pay_date."',
                        '".$old_sale_confm_date."',
                        '".$cancel_date."',
                        SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);

        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }

	
		// 매장변경후 주문정보
        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						SALE_CONFM_DATE,
						SEND_DATE
                    )
                    values 
                    (
                        '".$order_no."',
                        '".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
                        '".$style_order_no."',
                        '".$order_date."',
                        '".$order_type."',
                        '".$style_no."',
                        '".$season_year."',
                        '".$color_code."',
                        '".$size_code."',
                        '".$part_div."',
                        '".$part_no."',
                        '".$brand."',
                        '".$o2o_gb."',
                        '".$order_step."',
                        '".euckrencode($sender_name)."',
                        '".euckrencode($sender_addr)."',
                        '".$sender_tel_no."',
                        '".$sender_cell_no."',
                        '".euckrencode($sender_memo)."',
                        '".euckrencode($rcver_name)."',
                        '".euckrencode($rcver_addr)."',
                        '".$rcver_tel_no."',
                        '".$rcver_cell_no."',
                        '".(is_numeric($order_qty) ? $order_qty : 0)."',
                        '".(is_numeric($order_price) ? $order_price : 0)."',
                        '".(is_numeric($order_amt) ? $order_amt : 0)."',
                        '".(is_numeric($supply_amt) ? $supply_amt : 0)."',
                        '".(is_numeric($vat_amt) ? $vat_amt : 0)."',
                        '".$coupon_no."',
                        '".euckrencode($coupon_name)."',
                        '".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
                        '".$member_id."',
                        '".$emp_no."',
                        '".euckrencode($cooper_nm)."',
                        '".(is_numeric($use_point) ? $use_point : 0)."',
                        '".(is_numeric($occur_point) ? $occur_point : 0)."',
                        '".(is_numeric($use_epoint) ? $use_epoint : 0)."',
                        '".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
                        '".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
                        '".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
                        '".$order_payment_no."',
                        '".$pay_date."',
                        '".$sale_confm_date."',
                        SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);

        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }
        
    }
}


// 매장주문 일반주문 변경시 전송
function sendErporderShopReChange($ordercode, $idxs, $deli_type) {

    $conn = GetErpDBConn();

    sendErpOrderShopReChangeInfo($ordercode, $idxs, $deli_type, $conn);

    GetErpDBClose($conn);
}

// ERP 에 매장주문 일반주문 변경시 전송
function sendErpOrderShopReChangeInfo($ordercode, $idxs, $deli_type, $conn) {

    //global $conn;
    global $erp_account;

    $sql = "Select 	a.id, a.ordercode, b.idx, a.oi_step1, a.oi_step2, b.op_step, a.regdt, a.bank_date, 
                    a.sender_name, a.sender_tel2, sender_tel, a.paymethod, 
                    a.receiver_name, a.receiver_addr, a.receiver_tel1, a.receiver_tel2, a.order_msg2, 
                    a.oldordno, b.opt2_name, b.option_quantity, b.price, ((b.price+b.option_price)*b.option_quantity) as sum_price, 
                    b.deli_price, b.coupon_price, c.prodcode, c.colorcode, c.season_year, a.staff_order, b.delivery_type, b.reservation_date, b.store_code, b.pr_code, b.cooper_order, b.use_point, b.use_epoint, b.reserve,
					b.staff_price, b.cooper_price, a.pg_ordercode, c.brandcd, b.pg_idx
            From	tblorderinfo a 
            Join	tblorderproduct b on a.ordercode = b.ordercode 
            Join	tblproduct c on b.productcode = c.productcode 
            Where	a.ordercode = '".$ordercode."' 
			and	    b.idx in ('".str_replace("|", "','", $idxs)."')
            Order by b.idx asc 
            ";
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }
        $order_no				= $ordercode;						// 주문번호
        $order_detail_no		= $data[idx];						// 주문상세번호
        $style_order_no		= $data[pr_code];				// 품목별 주문번호
		$order_date			= substr($ordercode,0,8);		// 주문날짜

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";							
			$emp_no				= "";
			$member_id			= "";
		} else {			
            list($erp_shopmem_id, $erp_emp_id) = list($erp_shopmem_id, $erp_emp_id, $company_code) = pmysql_fetch("Select erp_shopmem_id, erp_emp_id, company_code From tblmember Where id = '".$data[id]."'");
			$member_id			= $erp_shopmem_id;
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
				$emp_no			= $erp_emp_id;
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
				$emp_no			= "";
				list($group_name)=pmysql_fetch("select group_name from tblcompanygroup where group_code='".$company_code."'");// 임직원 포인트
				$cooper_nm = $group_name;
			} else {
				$order_type		= "A";
				$emp_no			= "";
			}
		}

        $style_no			= $data[prodcode];					// 품번
        $season_year		= $data[season_year];			// 시즌년도
        $color_code		= $data[colorcode];				// 색상
		$size_code		= str_pad($data[opt2_name],"3"," ",STR_PAD_LEFT);				// 사이즈		

		$old_order_step		= 'R';		// 경우의 수 없음 고정
		$old_o2o_gb				= "2";			// 변경전 O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)
		$old_part_div			= "";			// 변경전 유통망
		$old_part_no			= "";			// 변경전 매장코드
		$old_brand				= "";			// 변경전 브랜드

		$shopcd			= $data[delivery_type]=='0'?'A1801B':$data[store_code];
		$part_div			= substr($shopcd,0,1);				// 유통망
		$part_no			= substr($shopcd,1,4);				// 매장코드
		$brand				= substr($shopcd,5,1);				// 브랜드

		$order_step		= 'P';						// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)
		$o2o_gb			= '0';							// O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)
		$part_div			= "O";						// 유통망
		$part_no			= "1111";					// 매장코드
		$brand				= $data[brandcd];	// 브랜드

		$sender_name	= $data[sender_name];			// 보낸이

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        $r_address = "(".$zonecode.") ".$address;
		$sender_addr			= $r_address;							// 보낸이주소
		$data[sender_tel2]	= $data[sender_tel2]=='02－－'?'':$data[sender_tel2];
		$sender_tel_no		= str_replace('-', '', $data[sender_tel2]);				// 보낸이전화번호
		$sender_cell_no		= str_replace('-', '', $data[sender_tel]);				// 보낸이휴대폰번호
		$sender_memo		= $data[order_msg2];				// 보낸이 메모
		$rcver_name			= $data[receiver_name];			// 받는이
		$rcver_addr			= $r_address;							// 받는이주소
		$data[receiver_tel1]	= $data[receiver_tel1]=='02－－'?'':$data[receiver_tel1];
		$rcver_tel_no			= str_replace('-', '', $data[receiver_tel1]);				// 받는이전화번호
		$rcver_cell_no		= str_replace('-', '', $data[receiver_tel2]);				// 받는이휴대폰번호

		$old_order_qty		= $data[option_quantity] * -1;	// 주문수량
		$order_qty				= $data[option_quantity];			// 주문수량
		$order_price			= $data[price];						// 주문단가
		$order_amt				= $data[sum_price];				// 주문금액
		$rsale_amt				= $data[sum_price] - ($data[use_point] + $data[use_epoint] + $data[coupon_price]);				// 실결제금액
		$supply_amt			= round($order_amt / 1.1);		// VAT제외금액
		$vat_amt				= round($supply_amt * 0.1);		// VAT금액
		if ($data[pg_idx]) {
			$ct_order_no			= $data[pg_ordercode];
			$ct_order_detail_no	= $data[pg_idx];
		} else {
			$ct_order_no			= $order_no;
			$ct_order_detail_no	= $order_detail_no;
		}
        $coupontemp     = getCouponInfo($ct_order_no, $ct_order_detail_no);
        if($coupontemp) {
            $couponinfo		= explode("^", $coupontemp);
            $coupon_no		= $couponinfo[0];				// 쿠폰번호
            $coupon_name	= str_replace(" 쿠폰","",$couponinfo[1]);				// 쿠폰명
        } else {
            $coupon_no		= "";									// 쿠폰번호
            $coupon_name	= "";									// 쿠폰명
        }
        $coupon_amt			= $data[coupon_price];		// 쿠폰금액

		$use_point					= $data[use_point];			// 사용포인트
		$occur_point				= $data[reserve];			// 적립포인트
		$use_epoint				= $data[use_epoint];		// 사용E포인트
		$emp_sale_amt			= $data[staff_price];		// 임직원할인금액
		$cooper_sale_amt		= $data[cooper_price];	// 협력업체할인금액
		$order_payment_no		= $data[pg_ordercode];	// 결제번호

        $pay_date    = $data[bank_date]?substr($data[bank_date],0,8):"";	// 결제일

		$old_sale_confm_date	= "";					// 이전매출확정일
		$sale_confm_date	= date("Ymd");		// 매출확정일
		$cancel_date			= date("Ymd");		// 이전 매장주문 취소일

	
		// 매장변경전 주문정보
        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						SALE_CONFM_DATE,
						CANCEL_DATE,
						SEND_DATE
                    )
                    values 
                    (
                        '".$order_no."',
                        '".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
                        '".$style_order_no."',
                        '".$order_date."',
                        '".$order_type."',
                        '".$style_no."',
                        '".$season_year."',
                        '".$color_code."',
                        '".$size_code."',
                        '".$old_part_div."',
                        '".$old_part_no."',
                        '".$old_brand."',
                        '".$old_o2o_gb."',
                        '".$old_order_step."',
                        '".euckrencode($sender_name)."',
                        '".euckrencode($sender_addr)."',
                        '".$sender_tel_no."',
                        '".$sender_cell_no."',
                        '".euckrencode($sender_memo)."',
                        '".euckrencode($rcver_name)."',
                        '".euckrencode($rcver_addr)."',
                        '".$rcver_tel_no."',
                        '".$rcver_cell_no."',
                        '".(is_numeric($old_order_qty) ? $old_order_qty : 0)."',
                        '".(is_numeric($order_price) ? $order_price : 0)."',
                        '".(is_numeric($order_amt) ? $order_amt : 0)."',
                        '".(is_numeric($supply_amt) ? $supply_amt : 0)."',
                        '".(is_numeric($vat_amt) ? $vat_amt : 0)."',
                        '".$coupon_no."',
                        '".euckrencode($coupon_name)."',
                        '".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
                        '".$member_id."',
                        '".$emp_no."',
                        '".euckrencode($cooper_nm)."',
                        '".(is_numeric($use_point) ? $use_point : 0)."',
                        '".(is_numeric($occur_point) ? $occur_point : 0)."',
                        '".(is_numeric($use_epoint) ? $use_epoint : 0)."',
                        '".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
                        '".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
                        '".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
                        '".$order_payment_no."',
                        '".$pay_date."',
                        '".$old_sale_confm_date."',
                        '".$cancel_date."',
                        SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);

        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }

	
		// 매장변경후 주문정보
        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						SALE_CONFM_DATE,
						SEND_DATE
                    )
                    values 
                    (
                        '".$order_no."',
                        '".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
                        '".$style_order_no."',
                        '".$order_date."',
                        '".$order_type."',
                        '".$style_no."',
                        '".$season_year."',
                        '".$color_code."',
                        '".$size_code."',
                        '".$part_div."',
                        '".$part_no."',
                        '".$brand."',
                        '".$o2o_gb."',
                        '".$order_step."',
                        '".euckrencode($sender_name)."',
                        '".euckrencode($sender_addr)."',
                        '".$sender_tel_no."',
                        '".$sender_cell_no."',
                        '".euckrencode($sender_memo)."',
                        '".euckrencode($rcver_name)."',
                        '".euckrencode($rcver_addr)."',
                        '".$rcver_tel_no."',
                        '".$rcver_cell_no."',
                        '".(is_numeric($order_qty) ? $order_qty : 0)."',
                        '".(is_numeric($order_price) ? $order_price : 0)."',
                        '".(is_numeric($order_amt) ? $order_amt : 0)."',
                        '".(is_numeric($supply_amt) ? $supply_amt : 0)."',
                        '".(is_numeric($vat_amt) ? $vat_amt : 0)."',
                        '".$coupon_no."',
                        '".euckrencode($coupon_name)."',
                        '".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
                        '".$member_id."',
                        '".$emp_no."',
                        '".euckrencode($cooper_nm)."',
                        '".(is_numeric($use_point) ? $use_point : 0)."',
                        '".(is_numeric($occur_point) ? $occur_point : 0)."',
                        '".(is_numeric($use_epoint) ? $use_epoint : 0)."',
                        '".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
                        '".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
                        '".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
                        '".$order_payment_no."',
                        '".$pay_date."',
                        '".$sale_confm_date."',
                        SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);

        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }
        
    }
}

// 취소전송
function sendErporderCancel($ordercode, $oc_no, $idxs) {

    $conn = GetErpDBConn();

    sendErpOrderInfoCancel($ordercode, $oc_no, $idxs, $conn);
    sendErpOrderinfoAppCancel($ordercode, $oc_no, $idxs, $conn);

    GetErpDBClose($conn);
}

// ERP 에 취소주문(환불)정보 전송
function sendErpOrderInfoCancel($ordercode, $oc_no, $idxs, $conn) {

    //global $conn;
    global $erp_account;

    $sql = "Select	b.id, b.ordercode, a.idx, b.oi_step1, b.oi_step2, a.op_step, d.rfindt as regdt, b.bank_date, 
                    b.sender_name, b.sender_tel2, b.sender_tel, b.paymethod, 
                    b.receiver_name, b.receiver_addr, b.receiver_tel1, b.receiver_tel2, b.order_msg2, 
                    b.oldordno, a.opt2_name, a.option_quantity, a.price, ((a.price+a.option_price)*a.option_quantity) as sum_price, 
                    a.deli_price, a.coupon_price, c.prodcode, c.colorcode, c.season_year, b.staff_order, a.delivery_type, a.reservation_date, a.store_code, a.pr_code, a.cooper_order, a.use_point, a.use_epoint, a.reserve,
					a.staff_price, a.cooper_price, b.pg_ordercode, c.brandcd, a.pg_idx, a.deli_date
            From	tblorderproduct  a 
            Join	tblorderinfo b on a.ordercode = b.ordercode 
            Join	tblproduct c on a.productcode = c.productcode 
            join 	tblorder_cancel d on a.oc_no = d.oc_no 
            Where	a.ordercode = '".$ordercode."'  
            and 	d.oc_no = ".$oc_no."  
            and	    a.idx in ('".str_replace("|", "','", $idxs)."') 
            Order by a.idx asc 
            ";

    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }
        $order_no				= $ordercode;						// 주문번호
        $order_detail_no		= $data[idx];						// 주문상세번호
        $style_order_no		= $data[pr_code];				// 품목별 주문번호
		$order_date			= substr($ordercode,0,8);		// 주문날짜

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";							
			$emp_no				= "";
			$member_id			= "";
		} else {			
            list($erp_shopmem_id, $erp_emp_id, $company_code) = pmysql_fetch("Select erp_shopmem_id, erp_emp_id, company_code From tblmember Where id = '".$data[id]."'");
			$member_id			= $erp_shopmem_id;
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
				$emp_no			= $erp_emp_id;
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
				$emp_no			= "";
				list($group_name)=pmysql_fetch("select group_name from tblcompanygroup where group_code='".$company_code."'");// 임직원 포인트
				$cooper_nm = $group_name;
			} else {
				$order_type		= "A";
				$emp_no			= "";
			}
		}

        $style_no			= $data[prodcode];					// 품번
        $season_year		= $data[season_year];			// 시즌년도
        $color_code		= $data[colorcode];				// 색상
		$size_code		= str_pad($data[opt2_name],"3"," ",STR_PAD_LEFT);				// 사이즈

        $shopcd			= $data[delivery_type]=='0'?'A1801B':$data[store_code];
		$part_div			= substr($shopcd,0,1);				// 유통망
		$part_no				= substr($shopcd,1,4);				// 매장코드
		$brand				= substr($shopcd,5,1);				// 브랜드
		
        $o2o_gb = $data[delivery_type];										// O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)
        /*if($data[delivery_type] == "0") {
			$o2o_gb = ($part_div=='A'&&$part_no='1801')?"0":"2";       // 본사/매장발송
        } elseif($data[delivery_type] == "1") {
			$o2o_gb = "1";																// 매장픽업
        } elseif($data[delivery_type] == "2") {
			$o2o_gb = "3";																// 당일수령
		}*/

		if($o2o_gb == '2') { // 매장발송
			list($old_store_code, $store, $scnt) = pmysql_fetch("select a.old_store_code, a.store_code, (select count(*) from tblorderproduct_store_code where ordercode=a.ordercode and idx =a.idx) cnt
from tblorderproduct_store_code a where a.ordercode='".$ordercode."' and a.idx = '".$data[idx]."' order by a.no desc limit 1");
			
			if($old_store_code=='A1801B' && $store=='A1801B' && $scnt =='1') { // 매장입찰일때 결제완료단계일 경우
				// ERP 에서 등급정보 가져오기
				$bef_sql = "Select  O2O_GB
							From (Select  O2O_GB
							From TA_OM010 
							Where  1=1
							AND ORDER_NO = '{$ordercode}' 
							AND ORDER_DETAIL_NO = '".$data[idx]."' 
							AND ORDER_STEP != 'C' 
							ORDER BY O2O_GB DESC, ORDER_SEQ DESC 
							) WHERE ROWNUM = 1
				";
				$smt_bef = oci_parse($conn, $bef_sql);
				oci_execute($smt_bef);
				//exdebug($bef_sql);

				$bef_o2o_gb = '';
				while($bef_data = oci_fetch_array($smt_bef, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {
					$bef_o2o_gb = $bef_data[O2O_GB];
				}
				oci_free_statement($smt_bef);

				if ($bef_o2o_gb == '0') {
					$o2o_gb				= '0';
					$part_div			= "O";				// 유통망
					$part_no				= "1111";				// 매장코드
					$brand				= $data[brandcd];				// 브랜드
				}
			}
		}

		if ($part_div=='A'&&$part_no=='1801') {
			$part_div			= "O";				// 유통망
			$part_no				= "1111";				// 매장코드
			$brand				= $data[brandcd];				// 브랜드
		} else {
			if ($data[deli_date] == "" &&  $o2o_gb != '0') {
				$part_div			= "";				// 유통망
				$part_no				= "";				// 매장코드
				$brand				= "";				// 브랜드
			}
		}

		$order_step		= getErpOrderStep($data[op_step]);			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)

		$sender_name	= $data[sender_name];			// 보낸이

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        $r_address = "(".$zonecode.") ".$address;
		$sender_addr			= $r_address;							// 보낸이주소
		$data[sender_tel2]	= $data[sender_tel2]=='02－－'?'':$data[sender_tel2];
		$sender_tel_no		= str_replace('-', '', $data[sender_tel2]);				// 보낸이전화번호
		$sender_cell_no		= str_replace('-', '', $data[sender_tel]);				// 보낸이휴대폰번호
		$sender_memo		= $data[order_msg2];				// 보낸이 메모
		$rcver_name			= $data[receiver_name];			// 받는이
		$rcver_addr			= $r_address;							// 받는이주소
		$data[receiver_tel1]	= $data[receiver_tel1]=='02－－'?'':$data[receiver_tel1];
		$rcver_tel_no			= str_replace('-', '', $data[receiver_tel1]);				// 받는이전화번호
		$rcver_cell_no		= str_replace('-', '', $data[receiver_tel2]);				// 받는이휴대폰번호

		$order_qty				= $data[option_quantity] * -1;	// 주문수량
		$order_price			= $data[price];						// 주문단가
		$order_amt				= $data[sum_price];				// 주문금액
		$rsale_amt				= $data[sum_price] - ($data[use_point] + $data[use_epoint] + $data[coupon_price]);				// 실결제금액
		$supply_amt			= round($order_amt / 1.1);		// VAT제외금액
		$vat_amt				= round($supply_amt * 0.1);		// VAT금액
		
        if ($data[pg_idx]) {
			$ct_order_no			= $data[pg_ordercode];
			$ct_order_detail_no	= $data[pg_idx];
		} else {
			$ct_order_no			= $order_no;
			$ct_order_detail_no	= $order_detail_no;
		}
        $coupontemp     = getCouponInfo($ct_order_no, $ct_order_detail_no);
        if($coupontemp) {
            $couponinfo		= explode("^", $coupontemp);
            $coupon_no		= $couponinfo[0];				// 쿠폰번호
            $coupon_name	= str_replace(" 쿠폰","",$couponinfo[1]);				// 쿠폰명
        } else {
            $coupon_no		= "";									// 쿠폰번호
            $coupon_name	= "";									// 쿠폰명
        }
        $coupon_amt			= $data[coupon_price];		// 쿠폰금액

		$use_point					= $data[use_point];			// 사용포인트
		$occur_point				= $data[reserve];			// 적립포인트
		$use_epoint				= $data[use_epoint];		// 사용E포인트
		$emp_sale_amt			= $data[staff_price];		// 임직원할인금액
		$cooper_sale_amt		= $data[cooper_price];	// 협력업체할인금액
		$order_payment_no		= $data[pg_ordercode];	// 결제번호

		// 임시 추가 20170906
		if($order_type == "A" && $emp_sale_amt > 0) {
				$emp_no			= "333333";
		}

        $pay_date		= $data[bank_date]?substr($data[bank_date],0,8):"";	// 결제일
        $cancel_date	= substr($data[regdt],0,8);	// 취소일


		$sale_confm_date		= ($o2o_gb=='0' || $data[deli_date] != "")?substr($data[bank_date],0,8):"";		// 매출확정일
		$fee_rate		= "";
		if ($shopcd != '' && $shopcd != 'A1801B') {
			$fee_rate		= ($o2o_gb=='0')?"":getFeeRate($shopcd, $cancel_date, $o2o_gb);		// O2O수수료율
		}


        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						SALE_CONFM_DATE,
						FEE_RATE,
						CANCEL_DATE,
						SEND_DATE
                    )
                    values 
                    (
                        '".$order_no."',
                        '".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
                        '".$style_order_no."',
                        '".$order_date."',
                        '".$order_type."',
                        '".$style_no."',
                        '".$season_year."',
                        '".$color_code."',
                        '".$size_code."',
                        '".$part_div."',
                        '".$part_no."',
                        '".$brand."',
                        '".$o2o_gb."',
                        '".$order_step."',
                        '".euckrencode($sender_name)."',
                        '".euckrencode($sender_addr)."',
                        '".$sender_tel_no."',
                        '".$sender_cell_no."',
                        '".euckrencode($sender_memo)."',
                        '".euckrencode($rcver_name)."',
                        '".euckrencode($rcver_addr)."',
                        '".$rcver_tel_no."',
                        '".$rcver_cell_no."',
                        '".(is_numeric($order_qty) ? $order_qty : 0)."',
                        '".(is_numeric($order_price) ? $order_price : 0)."',
                        '".(is_numeric($order_amt) ? $order_amt : 0)."',
                        '".(is_numeric($supply_amt) ? $supply_amt : 0)."',
                        '".(is_numeric($vat_amt) ? $vat_amt : 0)."',
                        '".$coupon_no."',
                        '".euckrencode($coupon_name)."',
                        '".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
                        '".$member_id."',
                        '".$emp_no."',
                        '".euckrencode($cooper_nm)."',
                        '".(is_numeric($use_point) ? $use_point : 0)."',
                        '".(is_numeric($occur_point) ? $occur_point : 0)."',
                        '".(is_numeric($use_epoint) ? $use_epoint : 0)."',
                        '".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
                        '".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
                        '".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
                        '".$order_payment_no."',
                        '".$pay_date."',
                        '".$sale_confm_date."',
						'".$fee_rate."',
                        '".$cancel_date."',
                        SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);
        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }
        
    }
}

// ERP 에 취소(환불) 결제정보 전송
function sendErpOrderinfoAppCancel($ordercode, $oc_no, $idxs, $conn) {

    //global $conn;
    global $erp_account;

    //결제수단 정보 가져오자.
    //결제수단에 따른 참조 테이블 정의하자.
    $apparr = getPaymethodInfo($ordercode);
    //exdebug($apparr);

    $sql = "Select	b.ordercode, a.idx, d.rfindt as regdt, b.staff_order,b.cooper_order,
                    ((a.price+a.option_price)*a.option_quantity-a.coupon_price+a.deli_price-a.use_point-a.use_epoint) as sum_price,
					b.pg_ordercode
            From	tblorderproduct  a 
            Join	tblorderinfo b on a.ordercode = b.ordercode 
            Join	tblproduct c on a.productcode = c.productcode 
            join 	tblorder_cancel d on a.oc_no = d.oc_no 
            Where	a.ordercode = '".$ordercode."'  
            and 	d.oc_no = ".$oc_no."  
            and	    a.idx in ('".str_replace("|", "','", $idxs)."') 
            Order by a.idx asc 
            ";
    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    $detailseq = 0;
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }

		$order_payment_no		= $data[pg_ordercode];	// 결제번호

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";		
		} else {			
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
			} else {
				$order_type		= "A";
			}
		}
		
		$order_date				= substr($ordercode,0,8);			// 주문날짜
		$pay_gb						= $apparr[detailgb];				// 결제구분
		$aprvl_amt					= $data[sum_price] * -1;			// 취소금액
		$aprvl_no					= $apparr[approvalno];			// 승인번호
		$issuer_name				= $apparr[cardcompanynm];	// 카드사명
		$aprvl_date					= substr($data[regdt], 0, 8);		// 승인일자
		$aprvl_time					= substr($data[regdt], -6);		// 승인시간
		$bank_name				= $apparr[banknm];				// 은행명
		$account_no				= $apparr[bankaccount];			// 계좌번호
		$escro_yn					= $apparr[escrow]?$apparr[escrow]:"N";					// 에스크로여부
		$bigo							= "";										// 비고

        $erp_sql2 = "insert into ".$erp_account.".TA_OM011 
                    (
						ORDER_PAYMENT_NO,
						ORDER_PAYMENT_SEQ,
						ORDER_TYPE,
						ORDER_DATE,
						PAY_GB,
						APRVL_AMT,
						APRVL_NO,
						ISSUER_NAME,
						APRVL_DATE,
						APRVL_TIME,
						BANK_NAME,
						ACCOUNT_NO,
						ESCRO_YN,
						BIGO,
						SEND_DATE
                    )
                    values 
                    (
                        '" . $order_payment_no. "',
						NVL((SELECT MAX(ORDER_PAYMENT_SEQ) FROM TA_OM011 WHERE ORDER_PAYMENT_NO = '".$order_payment_no."' ),0) + 1,
                        '" . $order_type. "',
                        '" . $order_date. "',
                        '" . $pay_gb. "',
                        '" . (is_numeric($aprvl_amt) ? $aprvl_amt : 0) . "',
                        '" . $aprvl_no. "',
                        '" . euckrencode($issuer_name). "',
                        '" . $aprvl_date. "',
                        '" . $aprvl_time. "',
                        '" . euckrencode($bank_name). "',
                        '" . $account_no. "',
                        '" . $escro_yn. "',
                        '" . $bigo. "',
						SYSDATE
                    )";
        //exdebug($erp_sql2);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql2 : ".$erp_sql2."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);
        
        $smt_erp = oci_parse($conn,$erp_sql2);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql2."\r\n",3,"/tmp/error_log_sw_erp");
        }
        
    }
}

// 반품 완료 -> 환불완료 시 전송
function sendErporderReturn($ordercode, $oc_no, $idxs) {

    $conn = GetErpDBConn();

    sendErpOrderInfoReturn($ordercode, $oc_no, $idxs, $conn);
    sendErpOrderinfoAppReturn($ordercode, $oc_no, $idxs, $conn);

    GetErpDBClose($conn);
}

// ERP 에 반품주문(환불)정보 전송
function sendErpOrderInfoReturn($ordercode, $oc_no, $idxs, $conn) {

    //global $conn;
    global $erp_account, $erp_deli_com_list;

    $sql = "Select	b.id, b.ordercode, a.idx, b.oi_step1, b.oi_step2, a.op_step, d.cfindt as regdt, b.bank_date, 
                    b.sender_name, b.sender_tel2, b.sender_tel, b.paymethod, 
                    b.receiver_name, b.receiver_addr, b.receiver_tel1, b.receiver_tel2, b.order_msg2, 
                    b.oldordno, a.opt2_name, a.option_quantity, a.price, ((a.price+a.option_price)*a.option_quantity) as sum_price, 
                    a.deli_price, a.coupon_price, c.prodcode, c.colorcode, c.season_year, b.staff_order, a.delivery_type, a.reservation_date, a.store_code, a.pr_code, a.cooper_order, a.use_point, a.use_epoint, a.reserve,
					a.staff_price, a.cooper_price, b.pg_ordercode, a.deli_com, a.deli_num, a.deli_date, a.order_conf_date, c.brandcd, a.pg_idx
            From	tblorderproduct  a 
            Join	tblorderinfo b on a.ordercode = b.ordercode 
            Join	tblproduct c on a.productcode = c.productcode 
            join 	tblorder_cancel d on a.oc_no = d.oc_no 
            Where	a.ordercode = '".$ordercode."'  
            and 	d.oc_no = ".$oc_no."  
            and	    a.idx in ('".str_replace("|", "','", $idxs)."') 
            Order by a.idx asc 
            ";

    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }
        $order_no				= $ordercode;						// 주문번호
        $order_detail_no		= $data[idx];						// 주문상세번호
        $style_order_no		= $data[pr_code];				// 품목별 주문번호
		$order_date			= substr($ordercode,0,8);		// 주문날짜

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";							
			$emp_no				= "";
			$member_id			= "";
		} else {			
            list($erp_shopmem_id, $erp_emp_id, $company_code) = pmysql_fetch("Select erp_shopmem_id, erp_emp_id, company_code From tblmember Where id = '".$data[id]."'");
			$member_id			= $erp_shopmem_id;
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
				$emp_no			= $erp_emp_id;
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
				$emp_no			= "";
				list($group_name)=pmysql_fetch("select group_name from tblcompanygroup where group_code='".$company_code."'");// 임직원 포인트
				$cooper_nm = $group_name;
			} else {
				$order_type		= "A";
				$emp_no			= "";
			}
		}

        $style_no			= $data[prodcode];					// 품번
        $season_year		= $data[season_year];			// 시즌년도
        $color_code		= $data[colorcode];				// 색상
		$size_code		= str_pad($data[opt2_name],"3"," ",STR_PAD_LEFT);				// 사이즈

        $shopcd			= $data[delivery_type]=='0'?'A1801B':$data[store_code];
		$part_div			= substr($shopcd,0,1);				// 유통망
		$part_no				= substr($shopcd,1,4);				// 매장코드
		$brand				= substr($shopcd,5,1);				// 브랜드
		
        $o2o_gb = $data[delivery_type];										// O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)
        /*if($data[delivery_type] == "0") {
			$o2o_gb = ($part_div=='A'&&$part_no='1801')?"0":"2";       // 본사/매장발송
        } elseif($data[delivery_type] == "1") {
			$o2o_gb = "1";																// 매장픽업
        } elseif($data[delivery_type] == "2") {
			$o2o_gb = "3";																// 당일수령
		}*/

		if ($part_div=='A'&&$part_no=='1801') {
			$part_div			= "O";				// 유통망
			$part_no				= "1111";				// 매장코드
			$brand				= $data[brandcd];				// 브랜드
		}

		$order_step		= getErpOrderStep($data[op_step]);			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)

		$sender_name	= $data[sender_name];			// 보낸이

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        $r_address = "(".$zonecode.") ".$address;
		$sender_addr			= $r_address;							// 보낸이주소
		$data[sender_tel2]	= $data[sender_tel2]=='02－－'?'':$data[sender_tel2];
		$sender_tel_no		= str_replace('-', '', $data[sender_tel2]);				// 보낸이전화번호
		$sender_cell_no		= str_replace('-', '', $data[sender_tel]);				// 보낸이휴대폰번호
		$sender_memo		= $data[order_msg2];				// 보낸이 메모
		$rcver_name			= $data[receiver_name];			// 받는이
		$rcver_addr			= $r_address;							// 받는이주소
		$data[receiver_tel1]	= $data[receiver_tel1]=='02－－'?'':$data[receiver_tel1];
		$rcver_tel_no			= str_replace('-', '', $data[receiver_tel1]);				// 받는이전화번호
		$rcver_cell_no		= str_replace('-', '', $data[receiver_tel2]);				// 받는이휴대폰번호

		 list($old_ordercode, $old_idx, $old_pr_code) = pmysql_fetch("Select old_ordercode, old_idx, old_pr_code From tblorder_cancel_reorder Where ordercode = '".$ordercode."' and idx = '".$data[idx]."' ");
		
		$linked_order_no				= $old_ordercode;			// 연계주문번호
		$linked_order_detail_no	= $old_idx;						// 연계주문상세번호
		$linked_erp_order_no		= $old_pr_code;				// ERP연계 품목별 주문번호

		$order_qty				= $data[option_quantity] * -1;	// 주문수량
		$order_price			= $data[price];						// 주문단가
		$order_amt				= $data[sum_price];				// 주문금액
		$rsale_amt				= $data[sum_price] - ($data[use_point] + $data[use_epoint] + $data[coupon_price]);				// 실결제금액
		$supply_amt			= round($order_amt / 1.1);		// VAT제외금액
		$vat_amt				= round($supply_amt * 0.1);		// VAT금액
		
        if ($data[pg_idx]) {
			$ct_order_no			= $data[pg_ordercode];
			$ct_order_detail_no	= $data[pg_idx];
		} else {
			$ct_order_no			= $order_no;
			$ct_order_detail_no	= $order_detail_no;
		}
        $coupontemp     = getCouponInfo($ct_order_no, $ct_order_detail_no);
        if($coupontemp) {
            $couponinfo		= explode("^", $coupontemp);
            $coupon_no		= $couponinfo[0];				// 쿠폰번호
            $coupon_name	= str_replace(" 쿠폰","",$couponinfo[1]);				// 쿠폰명
        } else {
            $coupon_no		= "";									// 쿠폰번호
            $coupon_name	= "";									// 쿠폰명
        }
        $coupon_amt			= $data[coupon_price];		// 쿠폰금액

		$use_point					= $data[use_point];			// 사용포인트
		$occur_point				= $data[reserve];			// 적립포인트
		$use_epoint				= $data[use_epoint];		// 사용E포인트
		$emp_sale_amt			= $data[staff_price];		// 임직원할인금액
		$cooper_sale_amt		= $data[cooper_price];	// 협력업체할인금액

		// 임시 추가 20170906
		if($order_type == "A" && $emp_sale_amt > 0) {
				$emp_no			= "333333";
		}
		
		$delivery_cust_code	=  $erp_deli_com_list[trim($data[deli_com])]->company_name;			// 배송업체
		$invoice_no				=  $data[deli_num];			// 송장번호

		$order_payment_no		= $data[pg_ordercode];	// 결제번호

        $pay_date			= $data[bank_date]?substr($data[bank_date],0,8):"";	// 결제일

		$deli_date			= substr($data[deli_date],0,8);											// 배송일
		$deli_end_date	= substr($data[order_conf_date],0,8);								// 배송완료일

		$sale_confm_date		= ($o2o_gb=='0')?substr($data[bank_date],0,8):substr($data[deli_date],0,8);		// 매출확정일
		$fee_rate		= ($o2o_gb=='0')?"":getFeeRate($shopcd, substr($data[deli_date],0,8), $o2o_gb);		// O2O수수료율
        $cancel_date	= substr($data[regdt],0,8);													// 취소일





        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						LINKED_ORDER_NO,
						LINKED_ORDER_DETAIL_NO,
						LINKED_ERP_ORDER_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						DELIVERY_CUST_CODE,
						INVOICE_NO,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						DELI_DATE,
						DELI_END_DATE,
						SALE_CONFM_DATE,
						FEE_RATE,
						CANCEL_DATE,
						SEND_DATE
                    )
                    values 
                    (
						'".$order_no."',
						'".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
						'".$style_order_no."',
						'".$order_date."',
						'".$order_type."',
						'".$style_no."',
						'".$season_year."',
						'".$color_code."',
						'".$size_code."',
						'".$part_div."',
						'".$part_no."',
						'".$brand."',
						'".$o2o_gb."',
						'".$order_step."',
						'".euckrencode($sender_name)."',
						'".euckrencode($sender_addr)."',
						'".$sender_tel_no."',
						'".$sender_cell_no."',
						'".euckrencode($sender_memo)."',
						'".euckrencode($rcver_name)."',
						'".euckrencode($rcver_addr)."',
						'".$rcver_tel_no."',
						'".$rcver_cell_no."',
						'".$linked_order_no."',
						'".$linked_order_detail_no."',
						'".$linked_erp_order_no."',
						'".(is_numeric($order_qty) ? $order_qty : 0)."',
						'".(is_numeric($order_price) ? $order_price : 0)."',
						'".(is_numeric($order_amt) ? $order_amt : 0)."',
						'".(is_numeric($supply_amt) ? $supply_amt : 0)."',
						'".(is_numeric($vat_amt) ? $vat_amt : 0)."',
						'".$coupon_no."',
						'".euckrencode($coupon_name)."',
						'".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
						'".$member_id."',
						'".$emp_no."',
						'".euckrencode($cooper_nm)."',
						'".(is_numeric($use_point) ? $use_point : 0)."',
						'".(is_numeric($occur_point) ? $occur_point : 0)."',
						'".(is_numeric($use_epoint) ? $use_epoint : 0)."',
						'".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
						'".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
						'".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
						'".euckrencode($delivery_cust_code)."',
						'".$invoice_no."',
						'".$order_payment_no."',
						'".$pay_date."',
						'".$deli_date."',
						'".$deli_end_date."',
						'".$sale_confm_date."',
						'".$fee_rate."',
						'".$cancel_date."',
						SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);
        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }
        
    }
}

// ERP 에 반품(환불) 결제정보 전송 20170921 확인 수정요함
function sendErpOrderinfoAppReturn($ordercode, $oc_no, $idxs, $conn) {

    //global $conn;
    global $erp_account;

    $sql = "Select	b.ordercode, a.idx, d.rfindt as regdt,  
                    ((a.price+a.option_price)*a.option_quantity-a.coupon_price+a.deli_price-a.use_point-a.use_epoint) as sum_price,
					b.pg_ordercode
            From	tblorderproduct  a 
            Join	tblorderinfo b on a.ordercode = b.ordercode 
            Join	tblproduct c on a.productcode = c.productcode 
            join 	tblorder_cancel d on a.oc_no = d.oc_no 
            Where	a.ordercode = '".$ordercode."'  
            and 	d.oc_no = ".$oc_no."  
            and	    a.idx in ('".str_replace("|", "','", $idxs)."') 
            Order by a.idx asc 
            ";
    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    $detailseq = 0;
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }



		$order_payment_no		= $data[pg_ordercode];	// 결제번호
		
		//결제수단 정보 가져오자.
		//결제수단에 따른 참조 테이블 정의하자.
		$apparr = getPaymethodInfo($order_payment_no);
		//exdebug($apparr);

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";		
		} else {			
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
			} else {
				$order_type		= "A";
			}
		}
		
		$order_date				= substr($ordercode,0,8);			// 주문날짜
		$pay_gb						= $apparr[detailgb];				// 결제구분
		$aprvl_amt					= $data[sum_price] * -1;			// 취소금액
		$aprvl_no					= $apparr[approvalno];			// 승인번호
		$issuer_name				= $apparr[cardcompanynm];	// 카드사명
		$aprvl_date					= substr($data[regdt], 0, 8);		// 승인일자
		$aprvl_time					= substr($data[regdt], -6);		// 승인시간
		$bank_name				= $apparr[banknm];				// 은행명
		$account_no				= $apparr[bankaccount];			// 계좌번호
		$escro_yn					= $apparr[escrow]?$apparr[escrow]:"N";					// 에스크로여부
		$bigo							= "";										// 비고

        $erp_sql2 = "insert into ".$erp_account.".TA_OM011 
                    (
						ORDER_PAYMENT_NO,
						ORDER_PAYMENT_SEQ,
						ORDER_TYPE,
						ORDER_DATE,
						PAY_GB,
						APRVL_AMT,
						APRVL_NO,
						ISSUER_NAME,
						APRVL_DATE,
						APRVL_TIME,
						BANK_NAME,
						ACCOUNT_NO,
						ESCRO_YN,
						BIGO,
						SEND_DATE
                    )
                    values 
                    (
						'" . $order_payment_no. "',
						NVL((SELECT MAX(ORDER_PAYMENT_SEQ) FROM TA_OM011 WHERE ORDER_PAYMENT_NO = '".$order_payment_no."' ),0) + 1,
						'" . $order_type. "',
						'" . $order_date. "',
						'" . $pay_gb. "',
						'" . (is_numeric($aprvl_amt) ? $aprvl_amt : 0) . "',
						'" . $aprvl_no. "',
						'" . euckrencode($issuer_name). "',
						'" . $aprvl_date. "',
						'" . $aprvl_time. "',
						'" . euckrencode($bank_name). "',
						'" . $account_no. "',
						'" . $escro_yn. "',
						'" . $bigo. "',
						SYSDATE
                    )";
        //exdebug($erp_sql2);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql2 : ".$erp_sql2."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);
        
        $smt_erp = oci_parse($conn,$erp_sql2);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql2."\r\n",3,"/tmp/error_log_sw_erp");
        }
        
    }
}

// 교환 완료 -> 교환완료 재주문 생성 시 전송
function sendErporderChange($reordercode) {

    $conn = GetErpDBConn();
	
    sendErpOrderInfoChange($reordercode, $conn);

    GetErpDBClose($conn);
}

// 교환 시 반품완료 전송
function sendErporderChangeReturn($ordercode, $oc_no, $idxs) {

    $conn = GetErpDBConn();

    sendErpOrderInfoReturn($ordercode, $oc_no, $idxs, $conn);

    GetErpDBClose($conn);
}

// ERP 에 재주문정보 전송
function sendErpOrderInfoChange($ordercode, $conn) {

    //global $conn;
    global $erp_account, $erp_deli_com_list;

    $sql = "Select 	a.id, a.ordercode, b.idx, a.oi_step1, a.oi_step2, b.op_step, a.regdt, a.bank_date, 
                    a.sender_name, a.sender_tel2, sender_tel, a.paymethod, 
                    a.receiver_name, a.receiver_addr, a.receiver_tel1, a.receiver_tel2, a.order_msg2, 
                    a.oldordno, b.opt2_name, b.option_quantity, b.price, ((b.price+b.option_price)*b.option_quantity) as sum_price, 
                    b.deli_price, b.coupon_price, c.prodcode, c.colorcode, a.staff_order, a.cooper_order,b.delivery_type, b.reservation_date, b.store_code, b.pr_code, c.season_year, b.use_point, b.use_epoint, b.reserve,
					b.staff_price, b.cooper_price, a.pg_ordercode, b.deli_com, b.deli_num, b.deli_date, b.order_conf_date, c.brandcd, b.pg_idx
            From	tblorderinfo a 
            Join	tblorderproduct b on a.ordercode = b.ordercode 
            Join	tblproduct c on b.productcode = c.productcode ";
    $sql .= "
            Where	a.ordercode = '".$ordercode."' 
            Order by b.idx asc 
            ";
    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
   //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }
        $order_no				= $ordercode;						// 주문번호
        $order_detail_no		= $data[idx];						// 주문상세번호
        $style_order_no		= $data[pr_code];				// 품목별 주문번호
		$order_date			= substr($ordercode,0,8);		// 주문날짜

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";							
			$emp_no				= "";
			$member_id			= "";
		} else {			
            list($erp_shopmem_id, $erp_emp_id, $company_code) = pmysql_fetch("Select erp_shopmem_id, erp_emp_id, company_code From tblmember Where id = '".$data[id]."'");
			$member_id			= $erp_shopmem_id;
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
				$emp_no			= $erp_emp_id;
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
				$emp_no			= "";
				list($group_name)=pmysql_fetch("select group_name from tblcompanygroup where group_code='".$company_code."'");// 임직원 포인트
				$cooper_nm = $group_name;
			} else {
				$order_type		= "A";
				$emp_no			= "";
			}
		}

        $style_no			= $data[prodcode];					// 품번
        $season_year		= $data[season_year];			// 시즌년도
        $color_code		= $data[colorcode];				// 색상
		$size_code		= str_pad($data[opt2_name],"3"," ",STR_PAD_LEFT);				// 사이즈

        $shopcd			= $data[delivery_type]=='0'?'A1801B':$data[store_code];
		$part_div			= substr($shopcd,0,1);				// 유통망
		$part_no				= substr($shopcd,1,4);				// 매장코드
		$brand				= substr($shopcd,5,1);				// 브랜드
		
        $o2o_gb = $data[delivery_type];										// O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)
        /*if($data[delivery_type] == "0") {
			$o2o_gb = ($part_div=='A'&&$part_no='1801')?"0":"2";       // 본사/매장발송
        } elseif($data[delivery_type] == "1") {
			$o2o_gb = "1";																// 매장픽업
        } elseif($data[delivery_type] == "2") {
			$o2o_gb = "3";																// 당일수령
		}*/

		if ($part_div=='A'&&$part_no=='1801') {
			$part_div			= "O";				// 유통망
			$part_no				= "1111";				// 매장코드
			$brand				= $data[brandcd];				// 브랜드
		}

		//$order_step		= getErpOrderStep($data[op_step]);			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)
		$order_step		= "G";			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)

		$sender_name	= $data[sender_name];			// 보낸이

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        $r_address = "(".$zonecode.") ".$address;
		$sender_addr			= $r_address;							// 보낸이주소
		$data[sender_tel2]	= $data[sender_tel2]=='02－－'?'':$data[sender_tel2];
		$sender_tel_no		= str_replace('-', '', $data[sender_tel2]);				// 보낸이전화번호
		$sender_cell_no		= str_replace('-', '', $data[sender_tel]);				// 보낸이휴대폰번호
		$sender_memo		= $data[order_msg2];				// 보낸이 메모
		$rcver_name			= $data[receiver_name];			// 받는이
		$rcver_addr			= $r_address;							// 받는이주소
		$data[receiver_tel1]	= $data[receiver_tel1]=='02－－'?'':$data[receiver_tel1];
		$rcver_tel_no			= str_replace('-', '', $data[receiver_tel1]);				// 받는이전화번호
		$rcver_cell_no		= str_replace('-', '', $data[receiver_tel2]);				// 받는이휴대폰번호

		 list($old_ordercode, $old_idx, $old_pr_code, $old_oc_no) = pmysql_fetch("Select old_ordercode, old_idx, old_pr_code, oc_no From tblorder_cancel_reorder Where ordercode = '".$ordercode."' and idx = '".$data[idx]."' ");
		 list($rechange_date) = pmysql_fetch("Select cfindt From tblorder_cancel Where oc_no = '".$old_oc_no."' ");
		
		if ($old_ordercode) {
			// 반품취소를 ERP에 본낸다
			sendErporderChangeReturn($old_ordercode, $old_oc_no, $old_idx);
		}
		
		$linked_order_no				= $old_ordercode;			// 연계주문번호
		$linked_order_detail_no	= $old_idx;						// 연계주문상세번호
		$linked_erp_order_no		= $old_pr_code;				// ERP연계 품목별 주문번호

		$order_qty				= $data[option_quantity];			// 주문수량
		$order_price			= $data[price];						// 주문단가
		$order_amt				= $data[sum_price];				// 주문금액
		$rsale_amt				= $data[sum_price] - ($data[use_point] + $data[use_epoint] + $data[coupon_price]);				// 실결제금액
		$supply_amt			= round($order_amt / 1.1);		// VAT제외금액
		$vat_amt				= round($supply_amt * 0.1);		// VAT금액
		
        $coupontemp     = getCouponInfo($data[pg_ordercode], $data[pg_idx]);
        if($coupontemp) {
            $couponinfo		= explode("^", $coupontemp);
            $coupon_no		= $couponinfo[0];				// 쿠폰번호
            $coupon_name	= str_replace(" 쿠폰","",$couponinfo[1]);				// 쿠폰명
        } else {
            $coupon_no		= "";									// 쿠폰번호
            $coupon_name	= "";									// 쿠폰명
        }
        $coupon_amt			= $data[coupon_price];		// 쿠폰금액

		$use_point					= $data[use_point];			// 사용포인트
		$occur_point				= $data[reserve];			// 적립포인트
		$use_epoint				= $data[use_epoint];		// 사용E포인트
		$emp_sale_amt			= $data[staff_price];		// 임직원할인금액
		$cooper_sale_amt		= $data[cooper_price];	// 협력업체할인금액

		// 임시 추가 20170906
		if($order_type == "A" && $emp_sale_amt > 0) {
				$emp_no			= "333333";
		}
		
		$delivery_cust_code	=  $erp_deli_com_list[trim($data[deli_com])]->company_name;			// 배송업체
		$invoice_no				=  $data[deli_num];			// 송장번호

		$order_payment_no		= $data[pg_ordercode];	// 결제번호

        $pay_date			= $data[bank_date]?substr($data[bank_date],0,8):"";	// 결제일

		$deli_date			= substr($data[deli_date],0,8);											// 배송일
		$deli_end_date	= substr($data[order_conf_date],0,8);								// 배송완료일

		$sale_confm_date		= ($o2o_gb=='0')?substr($data[bank_date],0,8):substr($data[deli_date],0,8);		// 매출확정일
		$fee_rate		= ($o2o_gb=='0')?"":getFeeRate($shopcd, substr($data[deli_date],0,8), $o2o_gb);		// O2O수수료율
        $cancel_date		= '';													// 취소일
        $rechange_date	= substr($rechange_date,0,8);				// 교환일





        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						LINKED_ORDER_NO,
						LINKED_ORDER_DETAIL_NO,
						LINKED_ERP_ORDER_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						DELIVERY_CUST_CODE,
						INVOICE_NO,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						DELI_DATE,
						DELI_END_DATE,
						SALE_CONFM_DATE,
						FEE_RATE,
						CANCEL_DATE,
						RECHANGE_DATE,
						SEND_DATE
                    )
                    values 
                    (
						'".$order_no."',
						'".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
						'".$style_order_no."',
						'".$order_date."',
						'".$order_type."',
						'".$style_no."',
						'".$season_year."',
						'".$color_code."',
						'".$size_code."',
						'".$part_div."',
						'".$part_no."',
						'".$brand."',
						'".$o2o_gb."',
						'".$order_step."',
						'".euckrencode($sender_name)."',
						'".euckrencode($sender_addr)."',
						'".$sender_tel_no."',
						'".$sender_cell_no."',
						'".euckrencode($sender_memo)."',
						'".euckrencode($rcver_name)."',
						'".euckrencode($rcver_addr)."',
						'".$rcver_tel_no."',
						'".$rcver_cell_no."',
						'".$linked_order_no."',
						'".$linked_order_detail_no."',
						'".$linked_erp_order_no."',
						'".(is_numeric($order_qty) ? $order_qty : 0)."',
						'".(is_numeric($order_price) ? $order_price : 0)."',
						'".(is_numeric($order_amt) ? $order_amt : 0)."',
						'".(is_numeric($supply_amt) ? $supply_amt : 0)."',
						'".(is_numeric($vat_amt) ? $vat_amt : 0)."',
						'".$coupon_no."',
						'".euckrencode($coupon_name)."',
						'".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
						'".$member_id."',
						'".$emp_no."',
						'".euckrencode($cooper_nm)."',
						'".(is_numeric($use_point) ? $use_point : 0)."',
						'".(is_numeric($occur_point) ? $occur_point : 0)."',
						'".(is_numeric($use_epoint) ? $use_epoint : 0)."',
						'".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
						'".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
						'".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
						'".euckrencode($delivery_cust_code)."',
						'".$invoice_no."',
						'".$order_payment_no."',
						'".$pay_date."',
						'".$deli_date."',
						'".$deli_end_date."',
						'".$sale_confm_date."',
						'".$fee_rate."',
						'".$cancel_date."',
						'".$rechange_date."',
						SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);
        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }
        
    }
}


/**
 *  주문유형에 따른 상품 해당 사이즈의 매장별  재고 구하기(실시간)
 *  type : delivery, pickup, day_delivery
 *  ORDER BY MIN(a.AVAILQTY) DESC, a.SHOPCD DESC 로 수정..(재고 같을때 강남점 008810을 우선으로 하기 위해서)
 *  order by 수정..물류창고 1순위, 없으면 강남점 1순위를 위해..
 **/
function getErpProdShopStock_Type($prodcd, $colorcd, $sizecd, $type='delivery') {	
		
	list($season_year) = pmysql_fetch(" SELECT season_year FROM tblproduct WHERE prodcode = '{$prodcd}' AND colorcode = '{$colorcd}' ");

	if ($season_year == '') {
		/*erp재고 가져오는부분 임시로 강제 수량 설정 실적용시 삭제 2017-02-22*/
		$shop_stock="";
		$shop_stock[shopnm][]         = "신림포도몰직영(BB)";
		$shop_stock[shopcd][]         = "D6306B";
		$shop_stock[availqty][]       = 20;
	} else{
		$store = array();
		$store_arr = array();
		$store = getShopCodeWhere($type);
		foreach($store as $key => $val) {
			$part_div	= substr($val, 0, 1);
			$part_no		= substr($val, 1, 4);
                	$store_arr[] = "'{$part_no}'";
        	}
		$store_arr = array_unique($store_arr);
        	$where = "PART_NO IN (".implode(",", $store_arr).")";
            	$subsql = "AND ".$where." ";

		$conn = GetErpDBConn();

		// 재고 수량이 0 보다 작을 경우 0으로 변경쿼리 추가 (2016.11.01 - 김재수)
		$sql = "SELECT * 
					FROM 
					(
					  SELECT S.PART_DIV, S.PART_NO, S.BRAND, S.STYLE_NO, S.COLOR_CODE, S.SIZE_CODE, CASE WHEN S.AVAILQTY < 0 THEN 0 ELSE S.AVAILQTY END AVAILQTY, CUST_NAME AS SHOPNM
					FROM (SELECT MAX(A.BRAND) BRAND,A.PART_DIV , A.PART_NO , 
						   STYLE_NO , 
						   A.COLOR_CODE, 
						   A.SIZE_CODE,
						   MAX(B.CUST_NAME) AS CUST_NAME,
						   SUM(NVL(CASE WHEN A.PART_DIV = 'A' THEN A.IN_QTY ELSE A.OUT_QTY END, 0)
										   - NVL(CASE WHEN A.PART_DIV = 'A' THEN A.OUT_QTY ELSE A.SALE_QTY END, 0)
										   - NVL(A.ETC_OUT_QTY, 0)
										   - NVL(CASE WHEN A.PART_DIV = 'A' THEN NVL(A.OUT_RETURN_QTY, 0) * (-1) ELSE A.OUT_RETURN_QTY END, 0))
										   AS AVAILQTY
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
								   AND TRIM(SIZE_CODE) = '".$sizecd."'            --옵션
								   ".$subsql."
					  ) A,
						   VI_PART_INFO1 B
					   WHERE A.PART_DIV = B.PART_DIV
					   AND A.PART_NO  = B.PART_NO
					   AND (B.REALPART_GB = '1' AND B.PART_DIV IN ('D','G','K')
						OR  (B.PART_DIV = 'A' AND B.PART_NO = '1801'))
						GROUP BY  
						A.PART_DIV , 
						A.PART_NO ,
						A.STYLE_NO, 
						A.COLOR_CODE, 
						A.SIZE_CODE
						) S
						--LEFT JOIN TA_OM006 T ON S.PART_DIV=T.PART_DIV AND S.PART_NO=T.PART_NO AND S.BRAND=T.BRAND
						WHERE 1=1
						 ORDER BY (CASE WHEN S.AVAILQTY < 0 THEN 0 ELSE S.AVAILQTY END) DESC,
						 (CASE 	WHEN S.PART_DIV = 'A' AND S.PART_NO = '1801' THEN 1                          		
											ELSE 2 END) ASC
						) Z 		
						WHERE ROWNUM = 1
				";
		$smt_stock = oci_parse($conn, $sql);
		oci_execute($smt_stock);
		//exdebug($sql);
		
		$size_sum	= getErpProdSizeStock($prodcd, $colorcd, $sizecd, $type);

		$shop_stock = array();
		while($data = oci_fetch_array($smt_stock, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

			foreach($data as $k => $v)
			{
				$data[$k] = utf8encode($v);
			}

			if (/*$data[SHOPNM] =='' && */$data[PART_DIV]=='A' && $data[PART_NO] = '1801') {
				$data[SHOPNM] = "온라인매장";
				$data[BRAND]	= "B";
			}
			if ($data[SHOPNM]) {
				$shop_stock[shopnm]         = $data[SHOPNM];
				$shop_stock[shopcd]         = $data[PART_DIV].$data[PART_NO].$data[BRAND];
				$shop_stock[availqty]       = (($prodcd=='BMC21890' && $sizecd=='77') || ($size_sum < 0))?0:$data[AVAILQTY];
			}
		}
		oci_free_statement($smt_stock);
		GetErpDBClose($conn);
	}

    return $shop_stock;
}

// ERP 배송정보 전송
function sendErpDeliveryInfo($ordercode, $idxs, $deli_cd, $deli_num, $shopcd='') {
    $conn = GetErpDBConn();

	sendErpDeliveryInfoApp($ordercode, $idxs, $deli_cd, $deli_num, $shopcd, $conn);

    GetErpDBClose($conn);
}

// ERP에 배송정보 전송
function sendErpDeliveryInfoApp($ordercode, $idxs, $deli_cd, $deli_num, $deli_shopcd, $conn) {

    //global $conn;
    global $erp_account, $erp_deli_com_list;

    $sql = "Select	b.id, b.ordercode, a.idx, b.oi_step1, b.oi_step2, a.op_step, b.regdt, b.bank_date, 
                    b.sender_name, b.sender_tel2, b.sender_tel, b.paymethod, 
                    b.receiver_name, b.receiver_addr, b.receiver_tel1, b.receiver_tel2, b.order_msg2, 
                    b.oldordno, a.opt2_name, a.option_quantity, a.price, ((a.price+a.option_price)*a.option_quantity) as sum_price, 
                    a.deli_price, a.coupon_price, c.prodcode, c.colorcode, c.season_year, b.staff_order, a.delivery_type, a.reservation_date, a.store_code, a.pr_code, a.cooper_order, a.use_point, a.use_epoint, a.reserve,
					a.staff_price, a.cooper_price, b.pg_ordercode, a.deli_com, a.deli_num, a.deli_date, a.order_conf_date, c.brandcd, a.pg_idx
            From	tblorderproduct  a 
            Join	tblorderinfo b on a.ordercode = b.ordercode 
            Join	tblproduct c on a.productcode = c.productcode 
            Where	a.ordercode = '".$ordercode."'  
            and	    a.idx in ('".str_replace("|", "','", $idxs)."') 
            Order by a.idx asc 
            ";

    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }
        $order_no				= $ordercode;						// 주문번호
        $order_detail_no		= $data[idx];						// 주문상세번호
        $style_order_no		= $data[pr_code];				// 품목별 주문번호
		$order_date			= substr($ordercode,0,8);		// 주문날짜

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";							
			$emp_no				= "";
			$member_id			= "";
		} else {			
            list($erp_shopmem_id, $erp_emp_id, $company_code) = pmysql_fetch("Select erp_shopmem_id, erp_emp_id, company_code From tblmember Where id = '".$data[id]."'");
			$member_id			= $erp_shopmem_id;
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
				$emp_no			= $erp_emp_id;
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
				$emp_no			= "";
				list($group_name)=pmysql_fetch("select group_name from tblcompanygroup where group_code='".$company_code."'");// 임직원 포인트
				$cooper_nm = $group_name;
			} else {
				$order_type		= "A";
				$emp_no			= "";
			}
		}

        $style_no			= $data[prodcode];					// 품번
        $season_year		= $data[season_year];			// 시즌년도
        $color_code		= $data[colorcode];				// 색상
		$size_code		= str_pad($data[opt2_name],"3"," ",STR_PAD_LEFT);				// 사이즈
		
		$data[store_code]	= $data[delivery_type]=='0'?'A1801B':$data[store_code];

        $shopcd			= $deli_shopcd?$deli_shopcd:$data[store_code];
		$part_div			= substr($shopcd,0,1);				// 유통망
		$part_no				= substr($shopcd,1,4);				// 매장코드
		$brand				= substr($shopcd,5,1);				// 브랜드
		
        $o2o_gb = $data[delivery_type];										// O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)
        /*if($data[delivery_type] == "0") {
			$o2o_gb = ($part_div=='A'&&$part_no='1801')?"0":"2";       // 본사/매장발송
        } elseif($data[delivery_type] == "1") {
			$o2o_gb = "1";																// 매장픽업
        } elseif($data[delivery_type] == "2") {
			$o2o_gb = "3";																// 당일수령
		}*/

		if ($part_div=='A'&&$part_no=='1801') {
			$part_div			= "O";				// 유통망
			$part_no				= "1111";				// 매장코드
			$brand				= $data[brandcd];				// 브랜드
		}

		$order_step		= getErpOrderStep($data[op_step]);			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)

		$sender_name	= $data[sender_name];			// 보낸이

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        $r_address = "(".$zonecode.") ".$address;
		$sender_addr			= $r_address;							// 보낸이주소
		$data[sender_tel2]	= $data[sender_tel2]=='02－－'?'':$data[sender_tel2];
		$sender_tel_no		= str_replace('-', '', $data[sender_tel2]);				// 보낸이전화번호
		$sender_cell_no		= str_replace('-', '', $data[sender_tel]);				// 보낸이휴대폰번호
		$sender_memo		= $data[order_msg2];				// 보낸이 메모
		$rcver_name			= $data[receiver_name];			// 받는이
		$rcver_addr			= $r_address;							// 받는이주소
		$data[receiver_tel1]	= $data[receiver_tel1]=='02－－'?'':$data[receiver_tel1];
		$rcver_tel_no			= str_replace('-', '', $data[receiver_tel1]);				// 받는이전화번호
		$rcver_cell_no		= str_replace('-', '', $data[receiver_tel2]);				// 받는이휴대폰번호

		 list($old_ordercode, $old_idx, $old_pr_code) = pmysql_fetch("Select old_ordercode, old_idx, old_pr_code From tblorder_cancel_reorder Where ordercode = '".$ordercode."' and idx = '".$data[idx]."' ");
		
		$linked_order_no				= $old_ordercode;			// 연계주문번호
		$linked_order_detail_no	= $old_idx;						// 연계주문상세번호
		$linked_erp_order_no		= $old_pr_code;				// ERP연계 품목별 주문번호

		$order_qty				= $data[option_quantity];			// 주문수량
		$order_price			= $data[price];						// 주문단가
		$order_amt				= $data[sum_price];				// 주문금액
		$rsale_amt				= $data[sum_price] - ($data[use_point] + $data[use_epoint] + $data[coupon_price]);				// 실결제금액
		$supply_amt			= round($order_amt / 1.1);		// VAT제외금액
		$vat_amt				= round($supply_amt * 0.1);		// VAT금액
		
        if ($data[pg_idx]) {
			$ct_order_no			= $data[pg_ordercode];
			$ct_order_detail_no	= $data[pg_idx];
		} else {
			$ct_order_no			= $order_no;
			$ct_order_detail_no	= $order_detail_no;
		}
        $coupontemp     = getCouponInfo($ct_order_no, $ct_order_detail_no);
        if($coupontemp) {
            $couponinfo		= explode("^", $coupontemp);
            $coupon_no		= $couponinfo[0];				// 쿠폰번호
            $coupon_name	= str_replace(" 쿠폰","",$couponinfo[1]);				// 쿠폰명
        } else {
            $coupon_no		= "";									// 쿠폰번호
            $coupon_name	= "";									// 쿠폰명
        }
        $coupon_amt			= $data[coupon_price];		// 쿠폰금액

		$use_point					= $data[use_point];			// 사용포인트
		$occur_point				= $data[reserve];			// 적립포인트
		$use_epoint				= $data[use_epoint];		// 사용E포인트
		$emp_sale_amt			= $data[staff_price];		// 임직원할인금액
		$cooper_sale_amt		= $data[cooper_price];	// 협력업체할인금액

		// 임시 추가 20170906
		if($order_type == "A" && $emp_sale_amt > 0) {
				$emp_no			= "333333";
		}

		$delivery_cust_code	=  $deli_cd?$erp_deli_com_list[trim($deli_cd)]->company_name:$erp_deli_com_list[trim($data[deli_com])]->company_name;			// 배송업체
		$invoice_no				=  $deli_num?$deli_num:$data[deli_num];			// 송장번호

		$order_payment_no		= $data[pg_ordercode];	// 결제번호

        $pay_date			= $data[bank_date]?substr($data[bank_date],0,8):"";	// 결제일

		$deli_date			= substr($data[deli_date],0,8);											// 배송일
		$deli_end_date	= substr($data[order_conf_date],0,8);								// 배송완료일

		$sale_confm_date		= ($o2o_gb=='0')?substr($data[bank_date],0,8):substr($data[deli_date],0,8);		// 매출확정일
		$fee_rate		= ($o2o_gb=='0')?"":getFeeRate($shopcd, substr($data[deli_date],0,8), $o2o_gb);		// O2O수수료율





        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						LINKED_ORDER_NO,
						LINKED_ORDER_DETAIL_NO,
						LINKED_ERP_ORDER_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						DELIVERY_CUST_CODE,
						INVOICE_NO,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						DELI_DATE,
						DELI_END_DATE,
						SALE_CONFM_DATE,
						FEE_RATE,
						SEND_DATE
                    )
                    values 
                    (
						'".$order_no."',
						'".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
						'".$style_order_no."',
						'".$order_date."',
						'".$order_type."',
						'".$style_no."',
						'".$season_year."',
						'".$color_code."',
						'".$size_code."',
						'".$part_div."',
						'".$part_no."',
						'".$brand."',
						'".$o2o_gb."',
						'".$order_step."',
						'".euckrencode($sender_name)."',
						'".euckrencode($sender_addr)."',
						'".$sender_tel_no."',
						'".$sender_cell_no."',
						'".euckrencode($sender_memo)."',
						'".euckrencode($rcver_name)."',
						'".euckrencode($rcver_addr)."',
						'".$rcver_tel_no."',
						'".$rcver_cell_no."',
						'".$linked_order_no."',
						'".$linked_order_detail_no."',
						'".$linked_erp_order_no."',
						'".(is_numeric($order_qty) ? $order_qty : 0)."',
						'".(is_numeric($order_price) ? $order_price : 0)."',
						'".(is_numeric($order_amt) ? $order_amt : 0)."',
						'".(is_numeric($supply_amt) ? $supply_amt : 0)."',
						'".(is_numeric($vat_amt) ? $vat_amt : 0)."',
						'".$coupon_no."',
						'".euckrencode($coupon_name)."',
						'".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
						'".$member_id."',
						'".$emp_no."',
						'".euckrencode($cooper_nm)."',
						'".(is_numeric($use_point) ? $use_point : 0)."',
						'".(is_numeric($occur_point) ? $occur_point : 0)."',
						'".(is_numeric($use_epoint) ? $use_epoint : 0)."',
						'".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
						'".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
						'".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
						'".euckrencode($delivery_cust_code)."',
						'".$invoice_no."',
						'".$order_payment_no."',
						'".$pay_date."',
						'".$deli_date."',
						'".$deli_end_date."',
						'".$sale_confm_date."',
						'".$fee_rate."',
						SYSDATE
                    )";
        //exdebug($erp_sql);
		if($o2o_gb == "2"  && $part_no==""){

			error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']),3,"/tmp/error_log_sw_erp_2d");
			error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp_2d");

		}else{
			//**********************************************************************************
			//이부분에 로그파일 경로를 수정해주세요.
			$logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
			//로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
			//**********************************************************************************
			fwrite( $logfile,"************************************************\r\n");
			fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
			fwrite( $logfile,"************************************************\r\n");
			fclose( $logfile );
			chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);
			
			$smt_erp = oci_parse($conn,$erp_sql);
			$stid   = oci_execute($smt_erp);
			if(!$stid)
			{
				$error = oci_error();
				//exdebug($error);
				$bt = debug_backtrace();
				error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
				error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
			}
		}
        
    }
}

// ERP 배송완료/구매확정 전송
function sendErpOrderEndInfo($ordercode, $idxs) {
    $conn = GetErpDBConn();

	sendErpOrderEndInfoApp($ordercode, $idxs, $conn);

    GetErpDBClose($conn);
}

// ERP에 배송완료/구매확정 전송
function sendErpOrderEndInfoApp($ordercode, $idxs, $conn) {

    //global $conn;
    global $erp_account, $erp_deli_com_list;

    $sql = "Select	b.id, b.ordercode, a.idx, b.oi_step1, b.oi_step2, a.op_step, b.regdt, b.bank_date, 
                    b.sender_name, b.sender_tel2, b.sender_tel, b.paymethod, 
                    b.receiver_name, b.receiver_addr, b.receiver_tel1, b.receiver_tel2, b.order_msg2, 
                    b.oldordno, a.opt2_name, a.option_quantity, a.price, ((a.price+a.option_price)*a.option_quantity) as sum_price, 
                    a.deli_price, a.coupon_price, c.prodcode, c.colorcode, c.season_year, b.staff_order, a.delivery_type, a.reservation_date, a.store_code, a.pr_code, a.cooper_order, a.use_point, a.use_epoint, a.reserve,
					a.staff_price, a.cooper_price, b.pg_ordercode, a.deli_com, a.deli_num, a.deli_date, a.order_conf_date, c.brandcd, a.pg_idx
            From	tblorderproduct  a 
            Join	tblorderinfo b on a.ordercode = b.ordercode 
            Join	tblproduct c on a.productcode = c.productcode 
            Where	a.ordercode = '".$ordercode."'  
            and	    a.idx in ('".str_replace("|", "','", $idxs)."') 
            Order by a.idx asc 
            ";

    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }
        $order_no				= $ordercode;						// 주문번호
        $order_detail_no		= $data[idx];						// 주문상세번호
        $style_order_no		= $data[pr_code];				// 품목별 주문번호
		$order_date			= substr($ordercode,0,8);		// 주문날짜

		// 주문유형 (A:일반회원, X:비회원, E:임직원, C:협력업체)
		if(substr(trim($ordercode), -1) == "X") {
			$order_type			= "X";							
			$emp_no				= "";
			$member_id			= "";
		} else {			
            list($erp_shopmem_id, $erp_emp_id, $company_code) = pmysql_fetch("Select erp_shopmem_id, erp_emp_id, company_code  From tblmember Where id = '".$data[id]."'");
			$member_id			= $erp_shopmem_id;
			if($data[staff_order] == "Y") {
				$order_type        = "E";	
				$emp_no			= $erp_emp_id;
			} else if($data[cooper_order] == "Y") {
				$order_type        = "C";
				$emp_no			= "";
				list($group_name)=pmysql_fetch("select group_name from tblcompanygroup where group_code='".$company_code."'");// 임직원 포인트
				$cooper_nm = $group_name;
			} else {
				$order_type		= "A";
				$emp_no			= "";
			}
		}

        $style_no			= $data[prodcode];					// 품번
        $season_year		= $data[season_year];			// 시즌년도
        $color_code		= $data[colorcode];				// 색상
		$size_code		= str_pad($data[opt2_name],"3"," ",STR_PAD_LEFT);				// 사이즈

        $shopcd			= $data[delivery_type]=='0'?'A1801B':$data[store_code];
		$part_div			= substr($shopcd,0,1);				// 유통망
		$part_no				= substr($shopcd,1,4);				// 매장코드
		$brand				= substr($shopcd,5,1);				// 브랜드
		
        $o2o_gb = $data[delivery_type];										// O2O구분 (0 : 본사발송, 1 : 매장픽업, 2 : 매장발송, 3 : 당일수령)
        /*if($data[delivery_type] == "0") {
			$o2o_gb = ($part_div=='A'&&$part_no='1801')?"0":"2";       // 본사/매장발송
        } elseif($data[delivery_type] == "1") {
			$o2o_gb = "1";																// 매장픽업
        } elseif($data[delivery_type] == "2") {
			$o2o_gb = "3";																// 당일수령
		}*/

		if ($part_div=='A'&&$part_no=='1801') {
			$part_div			= "O";				// 유통망
			$part_no				= "1111";				// 매장코드
			$brand				= $data[brandcd];				// 브랜드
		}

		$order_step		= getErpOrderStep($data[op_step]);			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)
		$order_step_y		= "Y";			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)
		$order_step_e		= "E";			// 주문구분 (P:결제완료, R:배송준비중, D:배송중, Y:배송완료, C:취소완료, G:교환완료, E:구매확정)

		$sender_name	= $data[sender_name];			// 보낸이

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        $r_address = "(".$zonecode.") ".$address;
		$sender_addr			= $r_address;							// 보낸이주소
		$data[sender_tel2]	= $data[sender_tel2]=='02－－'?'':$data[sender_tel2];
		$sender_tel_no		= str_replace('-', '', $data[sender_tel2]);				// 보낸이전화번호
		$sender_cell_no		= str_replace('-', '', $data[sender_tel]);				// 보낸이휴대폰번호
		$sender_memo		= $data[order_msg2];				// 보낸이 메모
		$rcver_name			= $data[receiver_name];			// 받는이
		$rcver_addr			= $r_address;							// 받는이주소
		$data[receiver_tel1]	= $data[receiver_tel1]=='02－－'?'':$data[receiver_tel1];
		$rcver_tel_no			= str_replace('-', '', $data[receiver_tel1]);				// 받는이전화번호
		$rcver_cell_no		= str_replace('-', '', $data[receiver_tel2]);				// 받는이휴대폰번호

		 list($old_ordercode, $old_idx, $old_pr_code) = pmysql_fetch("Select old_ordercode, old_idx, old_pr_code From tblorder_cancel_reorder Where ordercode = '".$ordercode."' and idx = '".$data[idx]."' ");
		
		$linked_order_no				= $old_ordercode;			// 연계주문번호
		$linked_order_detail_no	= $old_idx;						// 연계주문상세번호
		$linked_erp_order_no		= $old_pr_code;				// ERP연계 품목별 주문번호

		$order_qty				= $data[option_quantity];			// 주문수량
		$order_price			= $data[price];						// 주문단가
		$order_amt				= $data[sum_price];				// 주문금액
		$rsale_amt				= $data[sum_price] - ($data[use_point] + $data[use_epoint] + $data[coupon_price]);				// 실결제금액
		$supply_amt			= round($order_amt / 1.1);		// VAT제외금액
		$vat_amt				= round($supply_amt * 0.1);		// VAT금액
		
        if ($data[pg_idx]) {
			$ct_order_no			= $data[pg_ordercode];
			$ct_order_detail_no	= $data[pg_idx];
		} else {
			$ct_order_no			= $order_no;
			$ct_order_detail_no	= $order_detail_no;
		}
        $coupontemp     = getCouponInfo($ct_order_no, $ct_order_detail_no);
        if($coupontemp) {
            $couponinfo		= explode("^", $coupontemp);
            $coupon_no		= $couponinfo[0];				// 쿠폰번호
            $coupon_name	= str_replace(" 쿠폰","",$couponinfo[1]);				// 쿠폰명
        } else {
            $coupon_no		= "";									// 쿠폰번호
            $coupon_name	= "";									// 쿠폰명
        }
        $coupon_amt			= $data[coupon_price];		// 쿠폰금액

		$use_point					= $data[use_point];			// 사용포인트
		$occur_point				= $data[reserve];			// 적립포인트
		$use_epoint				= $data[use_epoint];		// 사용E포인트
		$emp_sale_amt			= $data[staff_price];		// 임직원할인금액
		$cooper_sale_amt		= $data[cooper_price];	// 협력업체할인금액

		// 임시 추가 20170906
		if($order_type == "A" && $emp_sale_amt > 0) {
				$emp_no			= "333333";
		}

		$delivery_cust_code	= $erp_deli_com_list[trim($data[deli_com])]->company_name;			// 배송업체
		$invoice_no				= $data[deli_num];			// 송장번호

		$order_payment_no		= $data[pg_ordercode];	// 결제번호

        $pay_date			= $data[bank_date]?substr($data[bank_date],0,8):"";	// 결제일

		$deli_date				= substr($data[deli_date],0,8);											// 배송일
		$deli_end_date		= substr($data[order_conf_date],0,8);								// 배송완료일
		$buy_confm_date	= substr($data[order_conf_date],0,8);								// 구매확정일

		$sale_confm_date		= ($o2o_gb=='0')?substr($data[bank_date],0,8):substr($data[deli_date],0,8);		// 매출확정일
		$fee_rate		= ($o2o_gb=='0')?"":getFeeRate($shopcd, substr($data[deli_date],0,8), $o2o_gb);		// O2O수수료율
        $cancel_date	= '';													// 취소일




		// 배송완료
        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						LINKED_ORDER_NO,
						LINKED_ORDER_DETAIL_NO,
						LINKED_ERP_ORDER_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						DELIVERY_CUST_CODE,
						INVOICE_NO,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						DELI_DATE,
						DELI_END_DATE,
						SALE_CONFM_DATE,
						FEE_RATE,
						CANCEL_DATE,
						SEND_DATE
                    )
                    values 
                    (
						'".$order_no."',
						'".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
						'".$style_order_no."',
						'".$order_date."',
						'".$order_type."',
						'".$style_no."',
						'".$season_year."',
						'".$color_code."',
						'".$size_code."',
						'".$part_div."',
						'".$part_no."',
						'".$brand."',
						'".$o2o_gb."',
						'".$order_step_y."',
						'".euckrencode($sender_name)."',
						'".euckrencode($sender_addr)."',
						'".$sender_tel_no."',
						'".$sender_cell_no."',
						'".euckrencode($sender_memo)."',
						'".euckrencode($rcver_name)."',
						'".euckrencode($rcver_addr)."',
						'".$rcver_tel_no."',
						'".$rcver_cell_no."',
						'".$linked_order_no."',
						'".$linked_order_detail_no."',
						'".$linked_erp_order_no."',
						'".(is_numeric($order_qty) ? $order_qty : 0)."',
						'".(is_numeric($order_price) ? $order_price : 0)."',
						'".(is_numeric($order_amt) ? $order_amt : 0)."',
						'".(is_numeric($supply_amt) ? $supply_amt : 0)."',
						'".(is_numeric($vat_amt) ? $vat_amt : 0)."',
						'".$coupon_no."',
						'".euckrencode($coupon_name)."',
						'".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
						'".$member_id."',
						'".$emp_no."',
						'".euckrencode($cooper_nm)."',
						'".(is_numeric($use_point) ? $use_point : 0)."',
						'".(is_numeric($occur_point) ? $occur_point : 0)."',
						'".(is_numeric($use_epoint) ? $use_epoint : 0)."',
						'".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
						'".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
						'".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
						'".euckrencode($delivery_cust_code)."',
						'".$invoice_no."',
						'".$order_payment_no."',
						'".$pay_date."',
						'".$deli_date."',
						'".$deli_end_date."',
						'".$sale_confm_date."',
						'".$fee_rate."',
						'".$cancel_date."',
						SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);
        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }


		// 구매확정
        $erp_sql = "insert into ".$erp_account.".TA_OM010 
                    (
						ORDER_NO,
						ORDER_DETAIL_NO,
						ORDER_SEQ,
						STYLE_ORDER_NO,
						ORDER_DATE,
						ORDER_TYPE,
						STYLE_NO,
						SEASON_YEAR,
						COLOR_CODE,
						SIZE_CODE,
						PART_DIV,
						PART_NO,
						BRAND,
						O2O_GB,
						ORDER_STEP,
						SENDER_NAME,
						SENDER_ADDR,
						SENDER_TEL_NO,
						SENDER_CELL_NO,
						SENDER_MEMO,
						RCVER_NAME,
						RCVER_ADDR,
						RCVER_TEL_NO,
						RCVER_CELL_NO,
						LINKED_ORDER_NO,
						LINKED_ORDER_DETAIL_NO,
						LINKED_ERP_ORDER_NO,
						ORDER_QTY,
						ORDER_PRICE,
						ORDER_AMT,
						SUPPLY_AMT,
						VAT_AMT,
						COUPON_NO,
						COUPON_NAME,
						COUPON_AMT,
						MEMBER_ID,
						EMP_NO,
						COOPER_NM,
						USE_POINT,
						OCCUR_POINT,
						USE_EPOINT,
						EMP_SALE_AMT,
						COOPER_SALE_AMT,
						RSALE_AMT,
						DELIVERY_CUST_CODE,
						INVOICE_NO,
						BUY_CONFM_DATE,
						ORDER_PAYMENT_NO,
						PAY_DATE,
						DELI_DATE,
						DELI_END_DATE,
						SALE_CONFM_DATE,
						FEE_RATE,
						CANCEL_DATE,
						SEND_DATE
                    )
                    values 
                    (
						'".$order_no."',
						'".$order_detail_no."',
						NVL((SELECT MAX(ORDER_SEQ) FROM TA_OM010 WHERE ORDER_NO = '".$order_no."' AND ORDER_DETAIL_NO = '".$order_detail_no."' ),0) + 1,
						'".$style_order_no."',
						'".$order_date."',
						'".$order_type."',
						'".$style_no."',
						'".$season_year."',
						'".$color_code."',
						'".$size_code."',
						'".$part_div."',
						'".$part_no."',
						'".$brand."',
						'".$o2o_gb."',
						'".$order_step_e."',
						'".euckrencode($sender_name)."',
						'".euckrencode($sender_addr)."',
						'".$sender_tel_no."',
						'".$sender_cell_no."',
						'".euckrencode($sender_memo)."',
						'".euckrencode($rcver_name)."',
						'".euckrencode($rcver_addr)."',
						'".$rcver_tel_no."',
						'".$rcver_cell_no."',
						'".$linked_order_no."',
						'".$linked_order_detail_no."',
						'".$linked_erp_order_no."',
						'".(is_numeric($order_qty) ? $order_qty : 0)."',
						'".(is_numeric($order_price) ? $order_price : 0)."',
						'".(is_numeric($order_amt) ? $order_amt : 0)."',
						'".(is_numeric($supply_amt) ? $supply_amt : 0)."',
						'".(is_numeric($vat_amt) ? $vat_amt : 0)."',
						'".$coupon_no."',
						'".euckrencode($coupon_name)."',
						'".(is_numeric($coupon_amt) ? $coupon_amt : 0)."',
						'".$member_id."',
						'".$emp_no."',
						'".euckrencode($cooper_nm)."',
						'".(is_numeric($use_point) ? $use_point : 0)."',
						'".(is_numeric($occur_point) ? $occur_point : 0)."',
						'".(is_numeric($use_epoint) ? $use_epoint : 0)."',
						'".(is_numeric($emp_sale_amt) ? $emp_sale_amt : 0)."',
						'".(is_numeric($cooper_sale_amt) ? $cooper_sale_amt : 0)."',
						'".(is_numeric($rsale_amt) ? $rsale_amt : 0)."',
						'".euckrencode($delivery_cust_code)."',
						'".$invoice_no."',
						'".$buy_confm_date."',
						'".$order_payment_no."',
						'".$pay_date."',
						'".$deli_date."',
						'".$deli_end_date."',
						'".$sale_confm_date."',
						'".$fee_rate."',
						'".$cancel_date."',
						SYSDATE
                    )";
        //exdebug($erp_sql);
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_orderinfo_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_orderinfo_".date("Ymd").".txt",0777);
        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }
        
    }
}


function getShopCodeWhere($type) {

    $subsql = "";
    if($type == "delivery") $subsql = "AND  delivery_yn = 'Y' ";
    else if($type == "pickup") $subsql = "AND  pickup_yn = 'Y' ";
    else if($type == "day_delivery") $subsql = "AND  day_delivery_yn = 'Y' ";

    $sql = "Select store_code From tblstore Where 1=1 ".$subsql." Order By sort asc, sno desc";
    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){
        $store[] = $data[store_code];
    }
    //exdebug($store);

    return $store;
}

// ERP 해당 이전회원정보 가져오기
function getErpOldMeberinfo($name, $mobile){
	
	$conn = GetErpDBConn();

	$mobile	= str_replace("-", "", $mobile);

	if(strlen($mobile)==11){
		$mobile1 = substr($mobile,0,3);
		$mobile2 = substr($mobile,3,4);
		$mobile3 = substr($mobile,7,4);
	}else if(strlen($mobile)==10){
		$mobile1 = substr($mobile,0,3);
		$mobile2 = substr($mobile,3,3);
		$mobile3 = substr($mobile,6,4);
	}

	$data[p_data]			= "";
	$data[p_err_code]	= 0;
	$data[p_err_text]		= "";
	$p_crypt_key	= "Twins";
	
	$sql = "
				BEGIN 
					PA_ONLINE_MALL.SP_GET_OLD_CUSTOMER(P_CUST_NAME => :P_CUST_NAME,
					   P_CELL_PHONE_NO1 => :P_CELL_PHONE_NO1,
					   P_CELL_PHONE_NO2 => :P_CELL_PHONE_NO2,
					   P_CELL_PHONE_NO3 => :P_CELL_PHONE_NO3,
					   P_CRYPT_KEY => :P_CRYPT_KEY,
					   P_DATA => :P_DATA,
					   P_ERR_CODE => :P_ERR_CODE,
					   P_ERR_TEXT => :P_ERR_TEXT
					);
				END;
			";
        $smt_erp = oci_parse($conn, $sql);

        //입력값
		oci_bind_by_name($smt_erp, ':P_CUST_NAME', $name);
		oci_bind_by_name($smt_erp, ':P_CELL_PHONE_NO1', $mobile1);
		oci_bind_by_name($smt_erp, ':P_CELL_PHONE_NO2', $mobile2);
		oci_bind_by_name($smt_erp, ':P_CELL_PHONE_NO3', $mobile3);
		oci_bind_by_name($smt_erp, ':P_CRYPT_KEY', $p_crypt_key);

		$_param[':P_CUST_NAME']			= utf8encode($name);
		$_param[':P_CELL_PHONE_NO1']	= utf8encode($mobile1);
		$_param[':P_CELL_PHONE_NO2']	= utf8encode($mobile2);
		$_param[':P_CELL_PHONE_NO3']	= utf8encode($mobile3);
		$_param[':P_CRYPT_KEY']				= utf8encode($p_crypt_key);

		///erp_member_send_log("getErpOldMeberinfo_request", $_param);

		//출력값
		oci_bind_by_name($smt_erp, ':P_DATA', $data[p_data],1000);
		oci_bind_by_name($smt_erp, ':P_ERR_CODE', $data[p_err_code],32);
		oci_bind_by_name($smt_erp, ':P_ERR_TEXT', $data[p_err_text],300);
        //oci_bind_by_name($smt_erp, ':P_ESHOP_ID', $id);
		
		$res_param[p_err_code]	= $data[p_err_code];
		$res_param[p_err_text]	= $data[p_err_text];

        $stid   = oci_execute($smt_erp);
		foreach($data as $k => $v)
		{
			$data[$k] =  trim($v)==''?'':trim(utf8encode($v));
		}

		$c_arr	= array('member_id', 'cust_name', 'birthday', 'birth_gb', 'cell_phone_no1', 'cell_phone_no2', 'cell_phone_no3', 'sex_gb', 'job_cd', 'home_zip_old_new', 'home_zip_no', 'home_addr1', 'home_addr2', 'sms_yn', 'kakao_yn', 'email1', 'email2', 'home_tel_no1', 'home_tel_no2', 'home_tel_no3', 'eshop_id');
		$v_arr	= explode("^", $data[p_data]);
		
		$res_data	= array();
		$res_meminfo	= array();
		$i=0;
		foreach($c_arr as $key) {
			$res_meminfo[$key]	= trim($v_arr[$i])==''?'':$v_arr[$i];	
			$res_param[$key]	= trim($v_arr[$i])==''?'':$v_arr[$i];	
			$i++;
		}

		$res_data[p_err_code]	= $data[p_err_code];
		$res_data[p_err_text]		= $data[p_err_text];
		$res_data[p_data]			= $data[p_err_code]=='0'?$res_meminfo:"";

		///erp_member_send_log("getErpOldMeberinfo_response", $res_param);
		

        oci_free_statement($smt_erp);
        GetErpDBClose($conn);

		return $res_data;
}


// ERP 해당 회원정보 가져오기
function getErpMeberinfo($name, $mobile){
	
	$conn = GetErpDBConn();

	$mobile	= str_replace("-", "", $mobile);

	if(strlen($mobile)==11){
		$mobile1 = substr($mobile,0,3);
		$mobile2 = substr($mobile,3,4);
		$mobile3 = substr($mobile,7,4);
	}else if(strlen($mobile)==10){
		$mobile1 = substr($mobile,0,3);
		$mobile2 = substr($mobile,3,3);
		$mobile3 = substr($mobile,6,4);
	}

	$data[p_data]			= "";
	$data[p_err_code]	= 0;
	$data[p_err_text]		= "";
	$p_crypt_key	= "Twins";
	
	$sql = "
				BEGIN 
					PA_ONLINE_MALL.SP_CHECK_CUSTOMER(P_CUST_NAME => :P_CUST_NAME,
					   P_CELL_PHONE_NO1 => :P_CELL_PHONE_NO1,
					   P_CELL_PHONE_NO2 => :P_CELL_PHONE_NO2,
					   P_CELL_PHONE_NO3 => :P_CELL_PHONE_NO3,
					   P_CRYPT_KEY => :P_CRYPT_KEY,
					   P_DATA => :P_DATA,
					   P_ERR_CODE => :P_ERR_CODE,
					   P_ERR_TEXT => :P_ERR_TEXT
					);
				END;
			";
        $smt_erp = oci_parse($conn, $sql);

        //입력값
		oci_bind_by_name($smt_erp, ':P_CUST_NAME', $name);
		oci_bind_by_name($smt_erp, ':P_CELL_PHONE_NO1', $mobile1);
		oci_bind_by_name($smt_erp, ':P_CELL_PHONE_NO2', $mobile2);
		oci_bind_by_name($smt_erp, ':P_CELL_PHONE_NO3', $mobile3);
		oci_bind_by_name($smt_erp, ':P_CRYPT_KEY', $p_crypt_key);

		$_param[':P_CUST_NAME']			= utf8encode($name);
		$_param[':P_CELL_PHONE_NO1']	= utf8encode($mobile1);
		$_param[':P_CELL_PHONE_NO2']	= utf8encode($mobile2);
		$_param[':P_CELL_PHONE_NO3']	= utf8encode($mobile3);
		$_param[':P_CRYPT_KEY']				= utf8encode($p_crypt_key);

		erp_member_send_log("getErpMeberinfo_request", $_param);

		//출력값
		oci_bind_by_name($smt_erp, ':P_DATA', $data[p_data],1000);
		oci_bind_by_name($smt_erp, ':P_ERR_CODE', $data[p_err_code],32);
		oci_bind_by_name($smt_erp, ':P_ERR_TEXT', $data[p_err_text],300);
        //oci_bind_by_name($smt_erp, ':P_ESHOP_ID', $id);
		
		$res_param[p_err_code]	= $data[p_err_code];
		$res_param[p_err_text]	= $data[p_err_text];

        $stid   = oci_execute($smt_erp);
		foreach($data as $k => $v)
		{
			$data[$k] =  trim($v)==''?'':trim(utf8encode($v));
		}

		$c_arr	= array('member_id', 'cust_name', 'birthday', 'birth_gb', 'cell_phone_no1', 'cell_phone_no2', 'cell_phone_no3', 'sex_gb', 'job_cd', 'home_zip_old_new', 'home_zip_no', 'home_addr1', 'home_addr2', 'sms_yn', 'kakao_yn', 'email1', 'email2', 'home_tel_no1', 'home_tel_no2', 'home_tel_no3', 'eshop_id');
		$v_arr	= explode("^", $data[p_data]);
		
		$res_data	= array();
		$res_meminfo	= array();
		$i=0;
		foreach($c_arr as $key) {
			$res_meminfo[$key]	= trim($v_arr[$i])==''?'':$v_arr[$i];	
			$res_param[$key]	= trim($v_arr[$i])==''?'':$v_arr[$i];	
			$i++;
		}

		$res_data[p_err_code]	= $data[p_err_code];
		$res_data[p_err_text]		= $data[p_err_text];
		$res_data[p_data]			= $data[p_err_code]=='0'?$res_meminfo:"";

		erp_member_send_log("getErpMeberinfo_response", $res_param);
		

        oci_free_statement($smt_erp);
        GetErpDBClose($conn);

		return $res_data;
}

// ERP 해당 임직원정보 가져오기
function getErpEmpMeberinfo($name, $emp_id) {

    $conn = GetErpDBConn();

    $sql = "SELECT EMP_ID, NM_KOR
			FROM  VI_INSA
			WHERE NM_KOR = '".$name."'        
			AND EMP_ID = '".$emp_id."'
            ";
    $smt_emp = oci_parse($conn, $sql);
    oci_execute($smt_emp);
    //exdebug($sql);

    $emp_info = array();
    while($data = oci_fetch_array($smt_emp, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

        $emp_info[emp_id]		= $data[EMP_ID];
        $emp_info[emp_name] = $data[NM_KOR];
    }
	foreach($emp_info as $k => $v)
	{
		$emp_info[$k] =  trim($v)==''?'':trim(utf8encode($v));
	}

	$res_data[p_err_code]	= $emp_info[emp_id]?"0":"-1";
	$res_data[p_err_text]		= $emp_info[emp_id]?"사원정보가 있습니다.":"사원정보가 없습니다.";
	$res_data[p_data]			= $emp_info[emp_id]?$emp_info:"";

    oci_free_statement($smt_emp);
    GetErpDBClose($conn);

    return $res_data;
}

// ERP 해당 임직원 복지할인 제한금액 설정하기
function getErpStaffLimitPoint($year) {
	global $erp_account;

	list($yy, $limit_amt) = pmysql_fetch(" SELECT yy, limit_amt FROM tblpoint_staff_limit WHERE yy = '{$year}' ");

	if ($yy) {
		$conn = GetErpDBConn();

        $erp_sql = "MERGE INTO ".$erp_account.".TA_OM083 USING DUAL ON (YY = '".$yy."')
		WHEN MATCHED THEN
		UPDATE SET LIMIT_AMT = '".$limit_amt."', INPUT_DATE = SYSDATE
		WHEN NOT MATCHED THEN
		INSERT (
			YY,
			LIMIT_AMT,
			INPUT_DATE
		) VALUES (
			'".$yy."',
			'".$limit_amt."',
			SYSDATE
		)";
        //exdebug($erp_sql);
		//exit;
        //**********************************************************************************
        //이부분에 로그파일 경로를 수정해주세요.
        $logfile = fopen("/tmp/test_erp_stafflimitpoint_".date("Ymd").".txt","a+");
        //로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
        //**********************************************************************************
        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
        fwrite( $logfile,"************************************************\r\n");
        fclose( $logfile );
        chmod("/tmp/test_erp_stafflimitpoint_".date("Ymd").".txt",0777);
        
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");
        }

		oci_free_statement($smt_emp);
		GetErpDBClose($conn);
	}
}

// ERP 해당 통합포인트 가져오기
function getErpMeberPoint($id, $oci_conn='') {

    if (!$oci_conn) 
		$conn = GetErpDBConn();
	else
		$conn = $oci_conn;

	$data[p_data]			= "";
	$data[p_err_code]	= 0;
	$data[p_err_text]	= "";
	
	$sql = "
			BEGIN 
				PA_ONLINE_MALL.SP_GET_CUSTOMER_POINT(P_ESHOP_ID => :P_ESHOP_ID,
				   P_DATA => :P_DATA,
				   P_ERR_CODE => :P_ERR_CODE,
				   P_ERR_TEXT => :P_ERR_TEXT
				);
			END;
		";
	$smt_erp = oci_parse($conn, $sql);

	//exdebug($sql);
	//입력값
	oci_bind_by_name($smt_erp, ':P_ESHOP_ID', $id);
	$_param[':P_DATA']	= utf8encode($id);

	erp_member_send_log("getErpMeberPoint_request", $_param);

	//출력값
	oci_bind_by_name($smt_erp, ':P_DATA', $data[p_data],1000);
	oci_bind_by_name($smt_erp, ':P_ERR_CODE', $data[p_err_code],32);
	oci_bind_by_name($smt_erp, ':P_ERR_TEXT', $data[p_err_text],300);

	$stid   = oci_execute($smt_erp);
	foreach($data as $k => $v)
	{
		$data[$k] =  trim($v)==''?'':trim(utf8encode($v));
	}
	
	$res_param[p_data]		= $data[p_data];
	$res_param[p_err_code]	= $data[p_err_code];
	$res_param[p_err_text]	= $data[p_err_text];

	$res_data[p_err_code]	= $data[p_err_code];
	$res_data[p_err_text]		= $data[p_err_text];
	$res_data[p_data]			= $data[p_data];

	erp_member_send_log("getErpMeberPoint_response", $res_data);

	$mem_reserve		= $res_data[p_err_code]==0?$res_data[p_data]:'0';

	$sql2= " update tblmember set reserve='".$mem_reserve."' where id = '".$id."' "; 
	pmysql_query($sql2); 
	

	oci_free_statement($smt_erp);
	 if (!$oci_conn) GetErpDBClose($conn);

	return $res_data;
}

// ERP 해당 회원등급 가져오기
function getErpMeberGrade($mem_id, $erp_sm_id='', $oci_conn='') {

    if (!$oci_conn) 
		$conn = GetErpDBConn();
	else
		$conn = $oci_conn;
	
	if (!$erp_sm_id) {
		$mem_sql = "SELECT erp_shopmem_id 
				FROM tblmember
				WHERE id = '".$mem_id."' 
				";
		//exdebug($mem_sql);
		list($erp_sm_id) = pmysql_fetch($mem_sql);
	}

	$data[p_data]			= "";
	$data[p_err_code]	= -9999;
	$data[p_err_text]	= "";	

	// ERP 에서 등급정보 가져오기
	$sql = "Select  MEMBER_ID, 
			CASE WHEN LEVEL_ID='D' AND RNK='1' THEN 'B3' ELSE LEVEL_ID||RNK END AS GROUP_CODE 
			From  TA_SP092 
			Where  1=1
			AND BYYMM = TO_CHAR(SYSDATE,'YYYYMM')
			AND LEVEL_GB = 'N'
			AND BRAND = 'E'
			AND MEMBER_ID = '{$erp_sm_id}'
	";
    $smt_emp = oci_parse($conn, $sql);
    oci_execute($smt_emp);
    //exdebug($sql);

    $emp_info = array();
    while($data = oci_fetch_array($smt_emp, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

        $emp_info[member_id]	= $data[MEMBER_ID];
        $emp_info[group_code]	= $data[GROUP_CODE];
    }
	foreach($emp_info as $k => $v)
	{
		$emp_info[$k] =  trim($v)==''?'':trim(utf8encode($v));
	}

	$res_data[p_err_code]	= $emp_info[member_id]?"0":"-1";
	$res_data[p_err_text]		= $emp_info[member_id]?"등급정보가 있습니다.":"등급정보가 없습니다.";
	$res_data[p_data]			= $emp_info[member_id]?$emp_info:"";

    oci_free_statement($smt_emp);
    if (!$oci_conn) GetErpDBClose($conn);

    return $res_data;
}

// ERP 해당 통합회원정보 가져오기(쇼핑몰 아이디로)
function getErpShopMeberinfo($id){
	
	$conn = GetErpDBConn();

	$data[p_data]			= "";
	$data[p_err_code]	= -9999;
	$data[p_err_text]	= "";
	$p_crypt_key			= "Twins";
	
	$sql = "
			BEGIN 
				PA_ONLINE_MALL.SP_GET_CUSTOMER_INFO(P_ESHOP_ID => :P_ESHOP_ID,
				   P_CRYPT_KEY => :P_CRYPT_KEY,
				   P_DATA => :P_DATA,
				   P_ERR_CODE => :P_ERR_CODE,
				   P_ERR_TEXT => :P_ERR_TEXT
				);
			END;
		";
	$smt_erp = oci_parse($conn, $sql);

	//exdebug($sql);
	//입력값
	oci_bind_by_name($smt_erp, ':P_ESHOP_ID', $id);
	oci_bind_by_name($smt_erp, ':P_CRYPT_KEY', $p_crypt_key);
	$_param[':P_ESHOP_ID']	= utf8encode($id);
	$_param[':P_CRYPT_KEY']	= utf8encode($p_crypt_key);

	erp_member_send_log("getErpShopMeberinfo_request", $_param);

	//출력값
	oci_bind_by_name($smt_erp, ':P_DATA', $data[p_data],1000);
	oci_bind_by_name($smt_erp, ':P_ERR_CODE', $data[p_err_code],32);
	oci_bind_by_name($smt_erp, ':P_ERR_TEXT', $data[p_err_text],300);

	$stid   = oci_execute($smt_erp);
	foreach($data as $k => $v)
	{
		$data[$k] =  trim($v)==''?'':trim(utf8encode($v));
	}

	$c_arr	= array('member_id', 'cust_name', 'birthday', 'birth_gb', 'cell_phone_no1', 'cell_phone_no2', 'cell_phone_no3', 'sex_gb', 'job_cd', 'home_zip_old_new', 'home_zip_no', 'home_addr1', 'home_addr2', 'sms_yn', 'kakao_yn', 'email1', 'email2', 'home_tel_no1', 'home_tel_no2', 'home_tel_no3', 'eshop_id');
	$v_arr	= explode("^", $data[p_data]);
	
	$res_param[p_err_code]	= $data[p_err_code];
	$res_param[p_err_text]	= $data[p_err_text];
	
	$res_data	= array();
	$res_meminfo	= array();
	$i=0;
	foreach($c_arr as $key) {
		$res_meminfo[$key]	= trim($v_arr[$i])==''?'':$v_arr[$i];	
		$res_param[$key]	= trim($v_arr[$i])==''?'':$v_arr[$i];	
		$i++;
	}

	$res_data[p_err_code]	= $data[p_err_code];
	$res_data[p_err_text]		= $data[p_err_text];
	$res_data[p_data]			= $data[p_err_code]=='0'?$res_meminfo:"";

	erp_member_send_log("getErpShopMeberinfo_response", $res_param);
	

	oci_free_statement($smt_erp);
	GetErpDBClose($conn);

	return $res_data;
}

// 정회원정보 가입/수정/탈퇴시 ERP로 전송..2016-12-14
function sendErpMemberInfo($mem_id, $type="join") {
	global $erp_group_code;

    $sql = "Select  id, 
			name, 
			email, 
			mobile, 
			news_yn, 
			kko_yn, 
			gender, 
			job_code,
			birth, 
			lunar, 
			home_post, 
			home_addr, 
			home_tel, 
			auth_type,
			dupinfo,
			conninfo,
			group_code, 
			erp_mem_id, 
			date, 
			trandate, 
			member_out, 
			staff_yn, 
			staffcardno, 
			memo, 
			mem_seq,
			erp_mem_id
            FROM    tblmember 
            WHERE   id = '".$mem_id."' 
            And     auth_type != 'sns' 
            ";
    list($id, $name, $email, $mobile, $news_yn, $kko_yn, $gender, $job_code, $birth, $lunar, $home_post, $home_addr, $home_tel, $auth_type, $dupinfo, $conninfo, $group_code, $erp_mem_id, $date, $trandate, $member_out, $staff_yn, $staffcardno, $memo, $mem_seq, $erp_mem_id) = pmysql_fetch($sql);

	if ($type=="out") $id=$mem_id;
    if($id) {
        if($type=="out") {
            $name = $email = $mobile = $news_yn = $gender = $birth = $lunar = $home_post = $home_addr = $home_tel = $group_code = $date = $trandate = $staff_yn = $staffcardno = $memo = $erp_mem_id = "";
        } else {
            if($home_addr) {
                $temp = explode("↑=↑",$home_addr);
                $home_addr1 = $temp[0];
                $home_addr2 = $temp[1];
            } else {
                $home_addr1 = $home_addr2 = "";
            }
        }

       // member_send_log($type, $id, $name, $email, $mobile, $news_yn, $kko_yn, $gender, $job_code, $birth, $lunar, $home_post, $home_addr, $home_tel, $auth_type, $dupinfo, $group_code, $erp_mem_id, $date, $trandate, $member_out, $staff_yn, $staffcardno, $memo, $mem_seq);

		if ($type=="join" || $type=="modify") {
			$email_arr		= explode("@", $email);
			$email1			= $email_arr[0];
			$email2			= $email_arr[1];
			$mobile_arr	= explode("-", $mobile);
			$mobile1		= $mobile_arr[0];
			$mobile2		= $mobile_arr[1];
			$mobile3		= $mobile_arr[2];
			$home_tel_arr	= explode("-", $home_tel);
			$home_tel1		= $home_tel_arr[0];
			$home_tel2		= $home_tel_arr[1];
			$home_tel3		= $home_tel_arr[2];
			if ($home_tel1!='' && $home_tel2!='' && $home_tel3!='') {
			} else {
				$home_tel1 = $home_tel2 = $home_tel3 = "";	
			}
			$gender			= $gender=='1'?'M':'F';
			$lunar			= $lunar=='0'?'2':'1';

			$home_zip_old_new	= strlen($home_post)>5?"N":"Y";

			if($news_yn=="Y") {
				$email_yn	="Y";
				$sms_yn	="Y";
			} else if($news_yn=="M") {
				$email_yn	="Y";
				$sms_yn	="N";
			} else if($news_yn=="S") {
				$email_yn	="N";
				$sms_yn	="Y";
			} else if($news_yn=="N") {
				$email_yn	="N";
				$sms_yn	="N";
			}

			if ($auth_type=='ipin') {
				$id_chk		="Y";
				$hp_chk		="N";
			} else if ($auth_type=='mobile') {
				$id_chk		="N";
				$hp_chk		="Y";
			}
		}

		$member_yn	= $erp_mem_id?"Y":"N";

		$data[p_member_id]			= "";
		$data[p_level_id]	= "";
		$data[p_err_code]	= -9999;
		$data[p_err_text]	= "";

		$p_part_div				= "O";
		$p_part_no				= "1111";
		$p_crypt_key			= "Twins";
		
		//exdebug($id."/".$p_part_div."/".$p_part_no."/".$name."/".$birth."/".$lunar."/".$mobile1."/".$mobile2."/".$mobile3."/".$gender."/".$job_code."/".$home_zip_old_new."/".$home_post."/".$home_addr1."/".$home_addr2."/".$sms_yn."/".$kko_yn."/".$email_yn."/".$email1."/".$email2."/".$home_tel1."/".$home_tel2."/".$home_tel3."/".$member_yn);
		//exit;

        $conn = GetErpDBConn();

        if ($type=="join") {
			$sql = "
						BEGIN 
							PA_ONLINE_MALL.SP_NEW_CUSTOMER_RESISTER (  
								:P_ESHOP_ID,
								:P_PART_DIV,
								:P_PART_NO,
								:P_CUST_NAME,
								:P_BIRTHDAY,
								:P_BIRTH_GB,
								:P_CELL_PHONE_NO1,
								:P_CELL_PHONE_NO2 ,
								:P_CELL_PHONE_NO3,
								:P_SEX_GB,
								:P_JOB_CD,
								:P_HOME_ZIP_OLD_NEW,
								:P_HOME_ZIP_NO,
								:P_HOME_ADDR1,
								:P_HOME_ADDR2,
								:P_SMS_YN,
								:P_KKO_YN,
								:P_EMAIL_YN,
								:P_EMAIL1,
								:P_EMAIL2,
								:P_TEL_NO1,
								:P_TEL_NO2,
								:P_TEL_NO3,
								:P_HP_CHK,
								:P_ID_CHK,
								:P_CRYPT_KEY,
								:P_OLDMEMBER_YN,
								:P_OLDMEMBER_ID,
								:P_CI_CODE,
								:P_MEMBER_ID,
								:P_LEVEL_ID,
								:P_ERR_CODE,
								:P_ERR_TEXT
							); 
						END;
					";
		} else  if ($type=="modify") {
			$sql = "
						BEGIN 
							PA_ONLINE_MALL.SP_UPDATE_CUSTOMER (
								:P_ESHOP_ID,
								:P_CUST_NAME,
								:P_BIRTHDAY,
								:P_BIRTH_GB,
								:P_CELL_PHONE_NO1,
								:P_CELL_PHONE_NO2 ,
								:P_CELL_PHONE_NO3,
								:P_SEX_GB,
								:P_JOB_CD,
								:P_HOME_ZIP_OLD_NEW,
								:P_HOME_ZIP_NO,
								:P_HOME_ADDR1,
								:P_HOME_ADDR2,
								:P_SMS_YN,
								:P_KKO_YN,
								:P_EMAIL_YN,
								:P_EMAIL1,
								:P_EMAIL2,
								:P_TEL_NO1,
								:P_TEL_NO2,
								:P_TEL_NO3, 
								:P_CRYPT_KEY,
								:P_ERR_CODE,
								:P_ERR_TEXT
							); 
						END;
					";
		} else  if ($type=="out") {
			$sql = "
						BEGIN 
							PA_ONLINE_MALL.SP_DELETE_CUSTOMER (  
								:P_ESHOP_ID, 
								:P_ERR_CODE,
								:P_ERR_TEXT
							); 
						END;
					";
		}
		
		//exdebug($sql);

		$smt_erp = oci_parse($conn, $sql);
		$_param	= array();

		//입력값
		oci_bind_by_name($smt_erp, ':P_ESHOP_ID', $id);
		$_param[':P_ESHOP_ID']	= $id;
		if ($type=="join") {
			oci_bind_by_name($smt_erp, ':P_PART_DIV', $p_part_div);
			oci_bind_by_name($smt_erp, ':P_PART_NO', $p_part_no);

			$_param[':P_PART_DIV']	= $p_part_div;
			$_param[':P_PART_NO']	= $p_part_no;
		}
		if ($type=="join" || $type=="modify") {
			oci_bind_by_name($smt_erp, ':P_CUST_NAME', euckrencode($name));
			oci_bind_by_name($smt_erp, ':P_BIRTHDAY', $birth);
			oci_bind_by_name($smt_erp, ':P_BIRTH_GB', $lunar);
			oci_bind_by_name($smt_erp, ':P_CELL_PHONE_NO1', $mobile1);
			oci_bind_by_name($smt_erp, ':P_CELL_PHONE_NO2', $mobile2);
			oci_bind_by_name($smt_erp, ':P_CELL_PHONE_NO3', $mobile3);
			oci_bind_by_name($smt_erp, ':P_SEX_GB', $gender);
			oci_bind_by_name($smt_erp, ':P_JOB_CD', $job_code);
			oci_bind_by_name($smt_erp, ':P_HOME_ZIP_OLD_NEW', $home_zip_old_new);
			oci_bind_by_name($smt_erp, ':P_HOME_ZIP_NO', $home_post);
			oci_bind_by_name($smt_erp, ':P_HOME_ADDR1', euckrencode($home_addr1));
			oci_bind_by_name($smt_erp, ':P_HOME_ADDR2', euckrencode($home_addr2));
			oci_bind_by_name($smt_erp, ':P_SMS_YN', $sms_yn);
			oci_bind_by_name($smt_erp, ':P_KKO_YN', $kko_yn);
			oci_bind_by_name($smt_erp, ':P_EMAIL_YN', $email_yn);
			oci_bind_by_name($smt_erp, ':P_EMAIL1', $email1);
			oci_bind_by_name($smt_erp, ':P_EMAIL2', $email2);
			oci_bind_by_name($smt_erp, ':P_TEL_NO1', $home_tel1);
			oci_bind_by_name($smt_erp, ':P_TEL_NO2', $home_tel2);
			oci_bind_by_name($smt_erp, ':P_TEL_NO3', $home_tel3);

			$_param[':P_CUST_NAME']	= $name;
			$_param[':P_BIRTHDAY']	= $birth;
			$_param[':P_BIRTH_GB']	= $lunar;
			$_param[':P_CELL_PHONE_NO1']	= $mobile1;
			$_param[':P_CELL_PHONE_NO2']	= $mobile2;
			$_param[':P_CELL_PHONE_NO3']	= $mobile3;
			$_param[':P_SEX_GB']	= $gender;
			$_param[':P_JOB_CD']	= $job_code;
			$_param[':P_HOME_ZIP_OLD_NEW']	= $home_zip_old_new;
			$_param[':P_HOME_ZIP_NO']	= $home_post;
			$_param[':P_HOME_ADDR1']	= $home_addr1;
			$_param[':P_HOME_ADDR2']	= $home_addr2;
			$_param[':P_SMS_YN']	= $sms_yn;
			$_param[':P_KKO_YN']	= $kko_yn;
			$_param[':P_EMAIL_YN']	= $email_yn;
			$_param[':P_EMAIL1']	= $email1;
			$_param[':P_EMAIL2']	= $email2;
			$_param[':P_TEL_NO1']	= $home_tel1;
			$_param[':P_TEL_NO2']	= $home_tel2;
			$_param[':P_TEL_NO3']	= $home_tel3;
		}

		if ($type=="join") {
			oci_bind_by_name($smt_erp, ':P_HP_CHK', $hp_chk);
			oci_bind_by_name($smt_erp, ':P_ID_CHK', $id_chk);
			oci_bind_by_name($smt_erp, ':P_OLDMEMBER_YN', $member_yn);
			oci_bind_by_name($smt_erp, ':P_OLDMEMBER_ID', $erp_mem_id);
			oci_bind_by_name($smt_erp, ':P_CI_CODE', $conninfo);
			$_param[':P_HP_CHK']	= $id_chk;
			$_param[':P_ID_CHK']	= $id_chk;
			$_param[':P_OLDMEMBER_YN']	= $member_yn;
			$_param[':P_OLDMEMBER_ID']	= $erp_mem_id;
			$_param[':P_CI_CODE']	= $conninfo;
		}
		if ($type=="join" || $type=="modify") {
			oci_bind_by_name($smt_erp, ':P_CRYPT_KEY', $p_crypt_key);
			$_param[':P_CRYPT_KEY']	= $p_crypt_key;
		}

		//exdebug($_param);

		erp_member_send_log("sendErpMemberInfo_".$type."_request", $_param);

		//출력값
		if ($type=="join") {
			oci_bind_by_name($smt_erp, ':P_MEMBER_ID', $data[p_member_id],30);
			oci_bind_by_name($smt_erp, ':P_LEVEL_ID', $data[p_level_id],30);
		}
		oci_bind_by_name($smt_erp, ':P_ERR_CODE', $data[p_err_code],32);
		oci_bind_by_name($smt_erp, ':P_ERR_TEXT', $data[p_err_text],300);

		$stid   = oci_execute($smt_erp);
		foreach($data as $k => $v)
		{
			$data[$k] = trim($v)==''?'':trim(utf8encode($v));
		}

		erp_member_send_log("sendErpMemberInfo_".$type."_response", $data);
		
		//exdebug($smt_erp);
		//exdebug($data);
		if ($type=="join") {
			/*$group_name	= $erp_group_code[$data[p_level_id]];
			list($group_code) = pmysql_fetch(" SELECT group_code FROM tblmembergroup WHERE group_name = '{$group_name}' ");
			$group_code	= "0001";
			$sql2= " update tblmember set group_code='".$group_code."', erp_shopmem_id = '".$data[p_member_id]."' where id = '".$id."' "; */
			$sql2= " update tblmember set erp_shopmem_id = '".$data[p_member_id]."' where id = '".$id."' "; 
			pmysql_query($sql2); 
		}
		
		if(!$stid) 
		{ 
			$error = oci_error();
			exdebug($error);
			$bt = debug_backtrace();
			error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
			error_log($sql."\r\n",3,"/tmp/error_log_sw_erp");
		}

		oci_free_statement($smt_erp);
		GetErpDBClose($conn);
    }
}

function sendErpMemberGradeChange($mem_id, $group_code, $oci_conn='') {
	global $erp_group_code;

    if (!$oci_conn) 
		$conn = GetErpDBConn();
	else
		$conn = $oci_conn;

	$data[p_data]			= "";
	$data[p_err_code]	= -9999;
	$data[p_err_text]	= "";

    $mem_sql = "SELECT group_name
            FROM   tblmembergroup
            WHERE   group_code = '".$group_code."' 
            ";
	//exdebug($mem_sql);
	$id	= $mem_id;
    list($group_name) = pmysql_fetch($mem_sql);
	//exdebug($group_name);

	$p_grade	= array_search($group_name, $erp_group_code);
	//exdebug($p_grade);
	//exit;

    if($id) {
		$sql = "
					BEGIN 
						PA_ONLINE_MALL.SP_UPDATE_CUSTOMER_GRADE (  
							:P_ESHOP_ID, 
							:P_GRADE, 
							:P_ERR_CODE,
							:P_ERR_TEXT
						); 
					END;
				";
		
		//exdebug($sql);

		$smt_erp = oci_parse($conn, $sql);
		$_param	= array();

		//입력값
		oci_bind_by_name($smt_erp, ':P_ESHOP_ID', $id);
		oci_bind_by_name($smt_erp, ':P_GRADE', $p_grade);

		$_param[':P_ESHOP_ID']	= $id;
		$_param[':P_GRADE']	= $p_grade;

		//exdebug($_param);
		//exit;

		erp_member_send_log("sendErpMemberGradeChange_request", $_param);

		//출력값
		oci_bind_by_name($smt_erp, ':P_ERR_CODE', $data[p_err_code],32);
		oci_bind_by_name($smt_erp, ':P_ERR_TEXT', $data[p_err_text],300);

		$stid   = oci_execute($smt_erp);
		foreach($data as $k => $v)
		{
			$data[$k] = trim($v)==''?'':trim(utf8encode($v));
		}

		erp_member_send_log("sendErpMemberGradeChange_response", $data);
		
		//exdebug($smt_erp);
		//exdebug($data);
		
		if(!$stid) 
		{ 
			$error = oci_error();
			$bt = debug_backtrace();
			error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
			error_log($sql."\r\n",3,"/tmp/error_log_sw_erp");
		}

		oci_free_statement($smt_erp);
		if (!$oci_conn) GetErpDBClose($conn);
    }

}

function member_send_log($type, $id, $name, $email, $mobile, $news_yn, $kko_yn, $gender, $job_code, $birth, $lunar, $home_post, $home_addr, $home_tel, $auth_type, $dupinfo, $group_code, $erp_mem_id, $date, $trandate, $member_out, $staff_yn, $staffcardno, $memo, $mem_seq) {
    ###############################
    $textDir =  DirPath.DataDir.'backup/send_memberinfo_logs_'.date("Ym").'/';  // 텍스트로그 위치
    $outText = '';  //로그내용
    //echo "dir = ".$textDir."\r\n";

    # 로그작성
        $outText  = "=========================".date("Y-m-d H:i:s")."=============================".PHP_EOL;
        $outText .= "type         : ".$type.PHP_EOL;
        $outText .= "id         : ".$id.PHP_EOL;
        $outText .= "name       : ".$name.PHP_EOL;
        $outText .= "email      : ".$email.PHP_EOL;
        $outText .= "mobile     : ".$mobile.PHP_EOL;
        $outText .= "news_yn    : ".$news_yn.PHP_EOL;
        $outText .= "kko_yn    : ".$kko_yn.PHP_EOL;
        $outText .= "gender     : ".$gender.PHP_EOL;
        $outText .= "job_code     : ".$job_code.PHP_EOL;
        $outText .= "birth      : ".$birth.PHP_EOL;
        $outText .= "lunar      : ".$lunar.PHP_EOL;
        $outText .= "home_post  : ".$home_post.PHP_EOL;
        $outText .= "home_addr1 : ".$home_addr1.PHP_EOL;
        $outText .= "home_addr2 : ".$home_addr2.PHP_EOL;
        $outText .= "home_tel   : ".$home_tel.PHP_EOL;
        $outText .= "auth_type   : ".$auth_type.PHP_EOL;
        $outText .= "dupinfo   : ".$dupinfo.PHP_EOL;
        $outText .= "group_code : ".$group_code.PHP_EOL;
        $outText .= "erp_mem_id : ".$erp_mem_id.PHP_EOL;
        $outText .= "date       : ".$date.PHP_EOL;
        $outText .= "trandate   : ".$trandate.PHP_EOL;
        $outText .= "member_out : ".$member_out.PHP_EOL;
        $outText .= "staff_yn   : ".$staff_yn.PHP_EOL;
        $outText .= "staffcardno: ".$staffcardno.PHP_EOL;
        $outText .= "memo       : ".$memo.PHP_EOL;
        $outText .= "mem_seq    : ".$mem_seq.PHP_EOL;
        $outText .= PHP_EOL;

        if( !is_dir( $textDir ) ){
            mkdir( $textDir, 0700 );
            chmod( $textDir, 0777 );
        }
        $upQrt_f = fopen($textDir.'memberinfo_'.date("Ymd").'.txt','a');
        fwrite( $upQrt_f, $outText );
        fclose( $upQrt_f );
        chmod( $textDir."memberinfo_".date("Ymd").".txt",0777 );
    ###############################*/
}

function erp_member_send_log($type, $data) {
    ###############################
    $textDir =  DirPath.DataDir.'backup/erp_send_memberinfo_logs_'.date("Ym").'/';  // 텍스트로그 위치
    $outText = '';  //로그내용
    //echo "dir = ".$textDir."\r\n";

    # 로그작성
        $outText  = "=========================".date("Y-m-d H:i:s")."=============================".PHP_EOL;
		$outText .= "type : ".$type.PHP_EOL;
		foreach($data as $k => $v) { 
			$outText .= $k." : ".$v.PHP_EOL;
		}
        $outText .= PHP_EOL;

        if( !is_dir( $textDir ) ){
            mkdir( $textDir, 0700 );
            chmod( $textDir, 0777 );
        }
        $upQrt_f = fopen($textDir.'erp_memberinfo_'.date("Ymd").'.txt','a');
        fwrite( $upQrt_f, $outText );
        fclose( $upQrt_f );
        chmod( $textDir."erp_memberinfo_".date("Ymd").".txt",0777 );
    ###############################*/
}



/*
# ERP 활동포인트 전송
# mem_id : 회원 아이디
# point : 포인트값
# point_type : EPR 활동 포인트 타입
# point_name : EPR 활동 포인트 명
# regdt : 등록일
# order_no : 주문번호
# order_idx : 주문상품 idx
# reason : 특이사항
*/
function sendErpPointAct($mem_id, $point, $point_type, $point_name, $regdt, $order_no=null, $order_idx=null, $reason=null) {

    global $erp_account;

    $conn = GetErpDBConn();

	$type			= "actpoint";
	$date			= $regdt?$regdt:date("Ymd");

	$res_status		="";
	$res_message	="";

	$send_data	= array (
		"date" => $date, 
		"mem_id" => $mem_id, 
		"point" => $point, 
		"point_type" => $point_type, 
		"point_name" => $point_name, 
		"order_no" => $order_no, 
		"order_idx" => $order_idx, 
		"reason" => $reason
	);

    totalpoint_send_log($type, $send_data);

	$erp_sql = "insert into ".$erp_account.".TA_OM007
				(
					ESHOP_ID,
					ESHOP_SEQ,
					ORDER_NO,
					ORDER_DETAIL_NO,
					POINT_NAME,
					ADJUSTMENT_POINT,
					ADJUSTMENT_REASON,
					ADJUSTMENT_DATE,
					SEND_DATE
				)
				values 
				(
					'".$mem_id."',
					NVL((SELECT MAX(ESHOP_SEQ) FROM TA_OM007 WHERE ESHOP_ID = '".$mem_id."'  ),0) + 1,
					'".$order_no."',
					'".$order_idx."',
					'".euckrencode($point_name)."',
					'".$point."',
					'".euckrencode($reason)."',
					'".$date."',
					SYSDATE
				)";
	//exdebug($erp_sql);
	//**********************************************************************************
	//이부분에 로그파일 경로를 수정해주세요.
	$logfile = fopen("/tmp/test_erp_".$type."_".date("Ymd").".txt","a+");
	//로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
	//**********************************************************************************
	fwrite( $logfile,"************************************************\r\n");
	fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
	fwrite( $logfile,"************************************************\r\n");
	fclose( $logfile );
	chmod("/tmp/test_erp_".$type."_".date("Ymd").".txt",0777);
	
	$smt_erp = oci_parse($conn,$erp_sql);
	$stid   = oci_execute($smt_erp);
	if(!$stid)
	{
		$error = oci_error();
		$bt = debug_backtrace();
		error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
		error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");

		$res_status		= "N";
		$res_message	= "실패";
	} else {
		$res_status		= "Y";
		$res_message	= "성공";
	}
    GetErpDBClose($conn);

	$returnData	= array(
		'res_status' => $res_status,
		'res_message' => $res_message
	);

	return  (object)$returnData;
}

/*
# ERP 임직원포인트 전송
# mem_id : 회원 아이디
# erp_mem_id : 회원 아이디
# point : 포인트값
# point_type : EPR 활동 포인트 타입
# point_name : EPR 활동 포인트 명
# regdt : 등록일
# order_no : 주문번호
# order_idx : 주문상품 idx
# reason : 특이사항
*/
function sendErpPointStaff($mem_id, $erp_mem_id, $point, $point_type, $point_name, $regdt, $order_no=null, $order_idx=null, $reason=null) {

    global $erp_account;

    $conn = GetErpDBConn();

	$type			= "staffpoint";
	$date			= $regdt?$regdt:date("Ymd");

	$res_status		="";
	$res_message	="";

	$send_data	= array (
		"date" => $date, 
		"mem_id" => $mem_id, 
		"erp_mem_id" => $erp_mem_id, 
		"point" => $point, 
		"point_type" => $point_type, 
		"point_name" => $point_name, 
		"order_no" => $order_no, 
		"order_idx" => $order_idx, 
		"reason" => $reason
	);

    totalpoint_send_log($type, $send_data);

	$erp_sql = "insert into ".$erp_account.".TA_OM008
				(
					ESHOP_ID,
					ESHOP_SEQ,
					ORDER_NO,
					ORDER_DETAIL_NO,
					MEMBEER_ID,
					POINT_NAME,
					ADJUSTMENT_POINT,
					ADJUSTMENT_REASON,
					ADJUSTMENT_DATE,
					SEND_DATE
				)
				values 
				(
					'".$mem_id."',
					NVL((SELECT MAX(ESHOP_SEQ) FROM TA_OM008 WHERE ESHOP_ID = '".$mem_id."'  ),0) + 1,
					'".$order_no."',
					'".$order_idx."',
					'".$erp_mem_id."',
					'".euckrencode($point_name)."',
					'".$point."',
					'".euckrencode($reason)."',
					'".$date."',
					SYSDATE
				)";
	//exdebug($erp_sql);
	//**********************************************************************************
	//이부분에 로그파일 경로를 수정해주세요.
	$logfile = fopen("/tmp/test_erp_".$type."_".date("Ymd").".txt","a+");
	//로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
	//**********************************************************************************
	fwrite( $logfile,"************************************************\r\n");
	fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
	fwrite( $logfile,"************************************************\r\n");
	fclose( $logfile );
	chmod("/tmp/test_erp_".$type."_".date("Ymd").".txt",0777);
	
	$smt_erp = oci_parse($conn,$erp_sql);
	$stid   = oci_execute($smt_erp);
	if(!$stid)
	{
		$error = oci_error();
		$bt = debug_backtrace();
		error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
		error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");

		$res_status		= "N";
		$res_message	= "실패";
	} else {
		$res_status		= "Y";
		$res_message	= "성공";
	}
    GetErpDBClose($conn);

	$returnData	= array(
		'res_status' => $res_status,
		'res_message' => $res_message
	);

	return  (object)$returnData;
}

/*
# ERP 협력업체포인트 전송
# mem_id : 회원 아이디
# point : 포인트값
# point_type : EPR 활동 포인트 타입
# point_name : EPR 활동 포인트 명
# regdt : 등록일
# order_no : 주문번호
# order_idx : 주문상품 idx
# reason : 특이사항
*/
function sendErpPointCooper($mem_id, $point, $point_type, $point_name, $regdt, $order_no=null, $order_idx=null, $reason=null) {

    global $erp_account;

    $conn = GetErpDBConn();

	$type			= "cooperpoint";
	$date			= $regdt?$regdt:date("Ymd");

	$res_status		="";
	$res_message	="";

	$send_data	= array (
		"date" => $date, 
		"mem_id" => $mem_id, 
		"point" => $point, 
		"point_type" => $point_type, 
		"point_name" => $point_name, 
		"order_no" => $order_no, 
		"order_idx" => $order_idx, 
		"reason" => $reason
	);

    totalpoint_send_log($type, $send_data);

	$erp_sql = "insert into ".$erp_account.".TA_OM009
				(
					ESHOP_ID,
					ESHOP_SEQ,
					ORDER_NO,
					ORDER_DETAIL_NO,
					POINT_NAME,
					ADJUSTMENT_POINT,
					ADJUSTMENT_REASON,
					ADJUSTMENT_DATE,
					SEND_DATE
				)
				values 
				(
					'".$mem_id."',
					NVL((SELECT MAX(ESHOP_SEQ) FROM TA_OM009 WHERE ESHOP_ID = '".$mem_id."'  ),0) + 1,
					'".$order_no."',
					'".$order_idx."',
					'".euckrencode($point_name)."',
					'".$point."',
					'".euckrencode($reason)."',
					'".$date."',
					SYSDATE
				)";
	//exdebug($erp_sql);
	//**********************************************************************************
	//이부분에 로그파일 경로를 수정해주세요.
	$logfile = fopen("/tmp/test_erp_".$type."_".date("Ymd").".txt","a+");
	//로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
	//**********************************************************************************
	fwrite( $logfile,"************************************************\r\n");
	fwrite( $logfile,"erp_sql : ".$erp_sql."\r\n");
	fwrite( $logfile,"************************************************\r\n");
	fclose( $logfile );
	chmod("/tmp/test_erp_".$type."_".date("Ymd").".txt",0777);
	
	$smt_erp = oci_parse($conn,$erp_sql);
	$stid   = oci_execute($smt_erp);
	if(!$stid)
	{
		$error = oci_error();
		$bt = debug_backtrace();
		error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error['message'].$bt[0]['line'],3,"/tmp/error_log_sw_erp");
		error_log($erp_sql."\r\n",3,"/tmp/error_log_sw_erp");

		$res_status		= "N";
		$res_message	= "실패";
	} else {
		$res_status		= "Y";
		$res_message	= "성공";
	}
    GetErpDBClose($conn);

	$returnData	= array(
		'res_status' => $res_status,
		'res_message' => $res_message
	);

	return  (object)$returnData;
}

/*
# ERP 온오프라인 통합포인트 가져오기
# mem_id : 회원 아이디
*/
function getErpOnOffPoint($mem_id) {
    
	$conn = GetErpDBConn();

	$type	= "point_onoff";

	$sql = "SELECT ESHOP_ID,
				OCCUR_DATE,
				OCCUR_SEQ,
				OCCUR_TYPE,
				OCCUR_POINT,
				ORDER_NO,
				ORDER_DETAIL_NO,
				--TAG_STYLE_NO,
				COLOR_CODE,
				SIZE_CODE,
				SALE_QTY,
				PROD_NAME
				FROM TA_OM012 
				WHERE 1=1
				AND ESHOP_ID = '".$mem_id."'
				AND RCV_DATE is NULL
				ORDER BY OCCUR_DATE, OCCUR_SEQ
			";
	$smt = oci_parse($conn, $sql);
	oci_execute($smt);

	$cnt = 0;
	while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

		foreach($data as $k => $v)
		{
			$data[$k] = utf8encode($v);
		}

		$mem_id			= $data[ESHOP_ID];
		$regdt				= $data[OCCUR_DATE]."000000";
		$body				= $data[OCCUR_TYPE];
		$point				= $data[OCCUR_POINT];
		$expire_date		= 99999999;

		$rel_job  = date("YmdHis");
		$rel_job .= "|".$data[OCCUR_SEQ];
		$rel_job .= "|".$data[ORDER_NO];
		$rel_job .= "|".$data[ORDER_DETAIL_NO];
		$rel_job .= "|".$data[TAG_STYLE_NO];
		$rel_job .= "|".$data[COLOR_CODE];
		$rel_job .= "|".$data[SIZE_CODE];
		$rel_job .= "|".$data[SALE_QTY];
		$rel_job .= "|".$data[PROD_NAME];
		
		$sql1 = "INSERT INTO tblpoint ( ";
		$sql1.= "mem_id, regdt, body, point, expire_date, rel_job ";
		$sql1.= " ) VALUES ( ";
		$sql1.= " '".$mem_id."', '".$regdt."', '".$body."', '".$point."', '".$expire_date."', '".$rel_job."' ";
		$sql1.= " ) ";

		//**********************************************************************************
		//이부분에 로그파일 경로를 수정해주세요.
		$logfile = fopen("/tmp/test_get_ins_".$type."_".date("Ymd").".txt","a+");
		//로그는 문제발생시 오류 추적의 중요데이터 이므로 반드시 적용해주시기 바랍니다.
		//**********************************************************************************
		fwrite( $logfile,"************************************************\r\n");
		fwrite( $logfile,"erp_sql : ".$sql1."\r\n");
		fwrite( $logfile,"************************************************\r\n");
		fclose( $logfile );
		chmod("/tmp/test_get_ins_".$type."_".date("Ymd").".txt",0777);

		$result = pmysql_query( $sql1, get_db_conn() );

		// 수신일 업데이트
		$sql2 = "update TA_OM012 set RCV_DATE=SYSDATE where RCV_DATE is NULL and ESHOP_ID = '".$data[ESHOP_ID]."' AND OCCUR_DATE = '".$data[OCCUR_DATE]."' AND OCCUR_SEQ = '".$data[OCCUR_SEQ]."'  ";
		$smt_rec = oci_parse($conn, $sql2);
		oci_execute($smt_rec);

		$cnt++;
	}

	oci_free_statement($smt);
	GetErpDBClose($conn);
}


function totalpoint_send_log($type, $data) {
    ###############################
    $textDir =  DirPath.DataDir.'backup/send_'.$type.'_logs_'.date("Ym").'/';  // 텍스트로그 위치
    $outText = '';  //로그내용
    //echo "dir = ".$textDir."\r\n";

    # 로그작성
        $outText  = "=========================".date("Y-m-d H:i:s")."=============================".PHP_EOL;
		foreach($data as $k => $v) { 
			$outText .= $k." : ".$v.PHP_EOL;
		}
        $outText .= PHP_EOL;

        if( !is_dir( $textDir ) ){
            mkdir( $textDir, 0700 );
            chmod( $textDir, 0777 );
        }
        $upQrt_f = fopen($textDir.$type.'_'.date("Ymd").'.txt','a');
        fwrite( $upQrt_f, $outText );
        fclose( $upQrt_f );
        chmod( $textDir.$type."_".date("Ymd").".txt",0777 );
    ###############################
}

function euckrencode($str){
	return iconv('utf-8','euc-kr',$str);
}

?>
