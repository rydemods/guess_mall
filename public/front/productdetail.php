<?php

/*-------------------------------
 * Global변수
 *-----------------------------*/
$Dir="../";
$_cdata="";
//$_pdata="";


/*-------------------------------
 * 공통영역
 *-----------------------------*/
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once dirname(__FILE__)."/../lib/product.class.php";
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다
include_once($Dir."lib/paging_ajax.php");
include_once($Dir."lib/coupon.class.php");


/*-------------------------------
 * REQUEST 
 *-----------------------------*/
$popup=$_REQUEST["popup"]; //popup일 경우 (2016-03-01 김재수 추가)
$mode=$_REQUEST["mode"];
$coupon_code=$_REQUEST["coupon_code"];
$code=$_REQUEST["code"];
$prod_cate_code = $code;
$productcode=$_REQUEST["productcode"];
$link_url   = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$instaimgpath = $Dir.DataDir."shopimages/instagram/";
$productimgpath = $Dir.DataDir."shopimages/product/";



if(ord($code)==0) {
	$code=substr($productcode,0,12);
}
list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
if(strlen($code_a)!=3) $code_a="000";
if(strlen($code_b)!=3) $code_b="000";
if(strlen($code_c)!=3) $code_c="000";
if(strlen($code_d)!=3) $code_d="000";
$likecode=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

$sort=$_REQUEST["sort"];
$brandcode=$_REQUEST["brandcode"]+0;


/*-------------------------------
 * 장바구니 tempkey 
 *-----------------------------*/

if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {	
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
} 

/*-------------------------------
 * VIP 상품일 경우 회원등급 체크
 *-----------------------------*/
$sql_prd_vip = "SELECT vip_product, staff_product FROM tblproduct WHERE productcode = '{$productcode}' ";
list($prd_vip_type, $staff_product) = pmysql_fetch(pmysql_query($sql_prd_vip));

if($prd_vip_type && ($member_group_level < $vip_limit_level)){
	alert_go("해당상품은 VIP 전용 상품 입니다.","/");
}


if(!$_ShopInfo->getStaffType() && $staff_product){
	alert_go("해당상품은 STAFF 전용 상품 입니다.","/");
}



/*-------------------------------
 * 상품정보 조회 
 *-----------------------------*/
if( strlen($productcode) > 0 ) {
	
	$_pdata = getProductInfo($productcode);

/* 
	//ERP 상품을 쇼핑몰에 업데이트한다.
	$sql = " 	select productcode from tblproduct where prodcode in (
			select prodcode from tblproduct where productcode ='{$productcode}' )";
	//exdebug($sql);
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		
		getUpErpProductUpdate($row->productcode);
		
	}
*/	
shell_exec("wget -O /dev/null 'http://{$_SERVER['HTTP_HOST']}/front/product_update.php?productcode={$productcode}' > /dev/null 2>/dev/null &");	

	$sql = "
		SELECT
		a.*,b.c_maincate,b.c_category
		FROM tblproductcode a
		,tblproductlink b
		WHERE a.code_a||a.code_b||a.code_c||a.code_d = b.c_category
		AND c_maincate = 1
		AND group_code = ''
		AND c_productcode = '{$productcode}'
	";
	//exdebug($sql);
	$result = pmysql_query($sql,get_db_conn());
	
	while($row = pmysql_fetch_object($result)){
		if($row->c_maincate == 1){
			$mainCate = $row;
		}
		$cateProduct[] = $row;
	}
	//var_dump($cateProduct);

	if($cateProduct) {
			
		if($mainCate) $_cdata = $mainCate;	
		else $_cdata = $cateProduct[0];
		//var_dump($_cdata);
		
		
	} else {
		alert_go('해당 분류가 존재하지 않습니다.',"/");
	}
		
	pmysql_free_result($result);


	
} else {
// 	alert_go('해당 상품 정보가 존재하지 않습니다.',"/");
}


/*-------------------------------
 * 장바구니 담기변수설정
 *-----------------------------*/
$vdate = date("YmdHis");


/*-------------------------------
 * 사용유무 확인중...... 
 *-----------------------------*/
// 리뷰 작성 가능 리스트 조회
/*
$sql  = "SELECT tblResult.ordercode, tblResult.idx ";
$sql .= "FROM ";
$sql .= "   ( ";
$sql .= "       SELECT a.*, b.regdt  ";
$sql .= "       FROM tblorderproduct a LEFT JOIN tblorderinfo b ON a.ordercode = b.ordercode ";
// 구매확정 이후 작성 가능하게 수정...2016-10-10
//$sql .= "       WHERE a.productcode = '" . $productcode . "' AND b.id = '" . $_ShopInfo->getMemid()  . "' and ( (b.oi_step1 = 3 AND b.oi_step2 = 0) OR (b.oi_step1 = 4 AND b.oi_step2 = 0) ) ";
$sql .= "       WHERE a.productcode = '" . $productcode . "' AND b.id = '" . $_ShopInfo->getMemid()  . "' and ( a.op_step = 4 and a.order_conf = '1' ) ";
$sql .= "       ORDER BY a.idx DESC ";
$sql .= "   ) AS tblResult LEFT ";
$sql .= "   OUTER JOIN tblproductreview tpr ON tblResult.productcode = tpr.productcode and tblResult.ordercode = tpr.ordercode and tblResult.idx = tpr.productorder_idx ";
#해당 상품의 리뷰가 있으면 작성 불가
$sql .= "WHERE tpr.productcode is null ";
$sql .= "ORDER BY tblResult.idx asc ";
$sql .= "LIMIT 1 ";
// exdebug($sql);
$result = pmysql_query($sql);
list($review_ordercode, $review_order_idx) = pmysql_fetch($sql);
pmysql_free_result($result);
*/
/*-------------------------------
 * 좋아요like
 *-----------------------------*/
 /*
$like_sql = "SELECT p.productcode, li.section,
						COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND p.prodcode = tl.hott_code),0) AS hott_cnt
			FROM tblproduct p
			LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on p.prodcode = li.hott_code";
$like_sql .= " WHERE p.productcode = '".$productcode."' AND p.display = 'Y'";
$result = pmysql_query( $like_sql, get_db_conn() );
$like_row = pmysql_fetch_object( $result );
$like_info = $like_row;
*/
/*-------------------------------
 * 상품 간략정보 
 *-----------------------------*/
 /*
if($_cdata->detail_type  == "TEM001"){

	$_pdata_prcontent = stripslashes($_pdata->pr_content);
	if( strlen($detail_filter) > 0 ) {
		$_pdata_prcontent = preg_replace($filterpattern,$filterreplace,$_pdata_prcontent);
	}

}
  * /
/*-------------------------------
 * 멀티이미지
 *-----------------------------*/
 /*
if($_cdata->detail_type  == "TEM001"){
	
	$urlpath = $Dir.DataDir."shopimages/product/";
	$product_multi_imgs[] = array();
	
	if(strlen($productcode)>0){
		
		$sql = "SELECT * FROM tblmultiimages ";
		$sql.= "WHERE productcode = '{$productcode}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)){
			$mulimg_name = array ("01"=>&$row->primg01,"02"=>&$row->primg02,"03"=>&$row->primg03,"04"=>&$row->primg04,"05"=>&$row->primg05,"06"=>&$row->primg06,"07"=>&$row->primg07,"08"=>&$row->primg08,"09"=>&$row->primg09,"10"=>&$row->primg10);
		}
		foreach($mulimg_name as $img){
			$product_multi_imgs[] = $img;
		}
	}
	
}
*/


/*-------------------------------
 * 상품상세 뷰 카운트
 *-----------------------------*/
$vcnt_sql = "UPDATE tblproduct SET vcnt = vcnt + 1 WHERE productcode = '".$productcode."'";
pmysql_query( $vcnt_sql, get_db_conn() );

/*-------------------------------
 * 쿠키
 *-----------------------------*/
$current_date = date("YmdHis");
	$viewproduct=$_COOKIE["ViewProduct"];

    // 쿠키값 : 상품코드 + "||" + 현재시각(YYYYMMDDHHMMSS)
    $cookieVal = "{$productcode}||{$current_date}";

	if(ord($viewproduct)==0 || strpos($viewproduct,",{$cookieVal},")===FALSE) {
		if(ord($viewproduct)==0) {
			$viewproduct=",{$cookieVal},";
		} else {
			$viewproduct=",".$cookieVal.$viewproduct;
		}
	} else {
		$viewproduct=str_replace(",{$cookieVal}","",$viewproduct);
		$viewproduct=",".$cookieVal.$viewproduct;
	}
	$viewproduct=substr($viewproduct,0,571);

	setcookie("ViewProduct",$viewproduct,time()+60*60*24*3,"/".RootPath);	// 쿠키를 3일동안만 저장 추가 (2015.11.10 - 김재수)


/*-------------------------------
 * TEMPLATE
 *-----------------------------*/
include ($Dir.MainDir.$_data->menu_type.".php"); 
include($Dir.TempletDir."product/detail_{$_cdata->detail_type}.php");
include ($Dir."lib/bottom.php");
?>
