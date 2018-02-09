<?php
/*********************************************************************
// 파 일 명		: product_code_product.php
// 설     명		: 상품 관리
// 상세설명	: 상품을 관리하는 리스트입니다.
// 작 성 자		: hspark
// 수 정 자		: 2015.11.26 - 김재수
//
//
*********************************************************************/
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

//exdebug($_POST);
//exdebug($_GET);

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}

if($_REQUEST['chk_detail'] && $_REQUEST['serial_value']){
	$arrSerialValue = explode("&", $_REQUEST['serial_value']);
	foreach($arrSerialValue as $serialVal){
		$serialValue = explode("=", $serialVal);
		$_REQUEST[$serialValue[0]] = str_replace('+', ' ', $serialValue[1]);
	}
}

#########################################################
$category_data=$_REQUEST["category_data"];
if($category_data){
	$arrCategoryData = explode("|", $_REQUEST['category_data']);

	$_REQUEST["code_a"] = $arrCategoryData[0];
	$_REQUEST["code_b"] = $arrCategoryData[1];
	$_REQUEST["code_c"] = $arrCategoryData[2];
	$_REQUEST["code_d"] = $arrCategoryData[3];
}
$copy_type          = $_REQUEST["copy_type"];
$mode               = $_REQUEST["mode"];
$s_keyword          = pg_escape_string(trim($_REQUEST["s_keyword"]));
$s_brand_keyword    = trim($_REQUEST["s_brand_keyword"]);   // 브랜드 검색
$s_check            = $_REQUEST["s_check"];
$display_type       = $_REQUEST["display_type"];  // 선택 진열, 미진열 값.
$display_yn         = $_REQUEST["display_yn"];
$vip                = $_REQUEST["vip"];
$staff              = $_REQUEST["staff"];
$vperiod            = $_REQUEST["vperiod"];
$code_a             = $_REQUEST["code_a"];
$code_b             = $_REQUEST["code_b"];
$code_c             = $_REQUEST["code_c"];
$code_d             = $_REQUEST["code_d"];
$search_end         = $_REQUEST["search_end"];
$search_start       = $_REQUEST["search_start"];
$sellprice_min      = $_REQUEST["sellprice_min"];
$sellprice_max      = $_REQUEST["sellprice_max"];
$sel_vender         = $_REQUEST["sel_vender"];
$code_type          = $_REQUEST["code_type"];
$code_area          = $_REQUEST["code_area"];
$hotdealyn          = $_REQUEST["hotdealyn"];
$dcpriceyn          = $_REQUEST["dcpriceyn"];
$sel_season         = $_REQUEST["sel_season"];
$orderby         = $_REQUEST["orderby"];

if($code_area){
	$s_keyword="";
}
$listnum=(int)$_REQUEST["listnum"];
if(!$listnum){
	//$listnum = (int)$_REQUEST["listnum_select"];
	$listnum = 20;
}
$gotopage = $_REQUEST["gotopage"];

$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

$likecodeExchange = $code_a."|".$code_b."|".$code_c."|".$code_d;

$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));

if($display_yn==""){
	$display_yn = "all";
}
if($vip==""){
	$vip = "all";
}
if($s_check==""){
	$s_check = "all";
}
if($staff==""){
	$staff = "all";
}
$checked["display_yn"][$display_yn] = "checked";
$checked["vip"][$vip] = "checked";
$checked["staff"][$staff] = "checked";
$checked["s_check"][$s_check] = "checked";
$checked["hotdealyn"][$hotdealyn] = "checked";
$checked["dcpriceyn"][$dcpriceyn] = "checked";
//$checked["check_vperiod"][$vperiod] = "checked";

$imagepath=$Dir.DataDir."shopimages/product/";
if($mode=="delete"){
	$prcode=$_REQUEST["prcode"];

	$sql = "SELECT vender,display,brand,pridx,assembleuse,assembleproduct FROM tblproduct WHERE productcode = '{$prcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	$vender=(int)$row->vender;
	$vdisp=$row->display;
	$brand=$row->brand;
	$vpridx=$row->pridx;
	$vassembleuse=$row->assembleuse;
	$vassembleproduct=$row->assembleproduct;

	#태그관련 지우기
	$sql = "DELETE FROM tbltagproduct WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#리뷰 지우기
	$sql = "DELETE FROM tblproductreview WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#위시리스트 지우기
	$sql = "DELETE FROM tblwishlist WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#관련상품 지우기
	$sql = "DELETE FROM tblcollection WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#테마상품 지우기
	$sql = "DELETE FROM tblproducttheme WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#메인상품 진열리스트 삭제
	//$sql = "DELETE FROM tblmainlist WHERE pridx = '".$vpridx."' ";
	//pmysql_query($sql,get_db_conn());

	#메인 메뉴 상품 진열리스트 삭제
	//$sql = "DELETE FROM tblmainmenulist WHERE pridx = '".$vpridx."' ";
	//pmysql_query($sql,get_db_conn());

	#카테고리별 상품 리스트 삭제
	//$sql = "DELETE FROM tblrecommendlist WHERE pridx = '".$vpridx."' ";
	//pmysql_query($sql,get_db_conn());

	#상품삭제
	$sql = "DELETE FROM tblproduct WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#카테고리 삭제
	$sql = "DELETE FROM tblproductlink WHERE c_productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#옵션 삭제
	$sql = "DELETE FROM tblproduct_option WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#상품접근권한 지우기
	$sql = "DELETE FROM tblproductgroupcode WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#상품별 노출 브랜드 삭제(2016.01.22 - 김재수)
	$sql = "DELETE FROM tblbrandproduct WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#튜토리얼 데이터 삭제
	pmysql_query("DELETE  FROM tblproduct_tutorial WHERE prcode = '".$prcode."'", get_db_conn());
/*	2015 04 06 디지아톰은 assembleproduct 사용안함
	if($vassembleuse=="Y") {
		$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
		$sql.= "WHERE productcode = '{$prcode}' ";
		$result = pmysql_query($sql,get_db_conn());
		if($row = @pmysql_fetch_object($result)) {
			$sql = "DELETE FROM tblassembleproduct WHERE productcode = '{$prcode}' ";
			pmysql_query($sql,get_db_conn());

			if(ord(str_replace("","",$row->assemble_pridx))) {
				$sql = "UPDATE tblproduct SET ";
				$sql.= "assembleproduct = REPLACE(assembleproduct,',{$prcode}','') ";
				$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
				$sql.= "AND assembleuse != 'Y' ";
				pmysql_query($sql,get_db_conn());
			}
		}
		pmysql_free_result($result);
	} else {
		if(ord($vassembleproduct)) {
			$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
			$sql.= "WHERE productcode IN ('".str_replace(",","','",$vassembleproduct)."') ";
			$result = pmysql_query($sql,get_db_conn());
			while($row = @pmysql_fetch_object($result)) {
				$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
				$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
				$sql.= "AND display ='Y' ";
				$sql.= "AND assembleuse!='Y' ";
				$result2 = pmysql_query($sql,get_db_conn());
				if($row2 = @pmysql_fetch_object($result2)) {
					$sql = "UPDATE tblproduct SET sellprice='{$row2->sumprice}' ";
					$sql.= "WHERE productcode = '{$row->productcode}' ";
					$sql.= "AND assembleuse='Y' ";
					pmysql_query($sql,get_db_conn());
				}
				pmysql_free_result($result2);
			}
		}

		$sql = "UPDATE tblassembleproduct SET ";
		$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$vpridx}',''), ";
		$sql.= "assemble_list=REPLACE(assemble_list,',{$vpridx}','') ";
		pmysql_query($sql,get_db_conn());
	}
*/
	if($vender>0) {
		//미니샵 테마코드에 등록된 상품 삭제
		//setVenderThemeDeleteNor($prcode, $vender);
		setVenderCountUpdateMin($vender, $vdisp);

		$tmpcode_a=substr($prcode,0,3);
		$sql = "SELECT COUNT(*) as cnt FROM tblproduct ";
		$sql.= "WHERE productcode LIKE '{$tmpcode_a}%' AND vender='{$vender}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$prcnt=$row->cnt;
		pmysql_free_result($result);

		/*if($prcnt==0) {
			setVenderDesignDeleteNor($tmpcode_a, $vender);
			$imagename=$Dir.DataDir."shopimages/vender/{$vender}_CODE10_{$tmpcode_a}.gif";
			@unlink($imagename);
		}*/
	}

	$log_content = "## 상품삭제 ## - 상품코드 $prcode - 상품명 : ".urldecode($productname)." $display_yn";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$delshopimage=$Dir.DataDir."shopimages/product/{$prcode}*";
	proc_matchfiledel($delshopimage);

	delProductMultiImg("prdelete","",$prcode);

	################## 회원별 판매 금액 delete ######################
	$memprice_sql = "DELETE FROM tblmembergroup_price WHERE productcode = '{$prcode}'";
	pmysql_query($memprice_sql,get_db_conn());

	# 수량에 따른 회원별 가격 변경 삭제
	pmysql_query("DELETE  FROM tblmembergroup_sale WHERE productcode = '".$prcode."'", get_db_conn());

	$onload="<script>window.onload=function(){ alert(\"상품 삭제가 완료되었습니다.\");}</script>";
	$prcode="";

}


if($mode=="all_delete"){	//체크된 상품 지우기
	if($_REQUEST["selectChk"]){
		$selectChk_tmp = substr($_REQUEST["selectChk"],0,strlen($_REQUEST["selectChk"])-1);
		$selectChk = explode(",",$selectChk_tmp);
		$chkPrcode = "'";
		for($i=0;$i<count($selectChk);$i++){
			$chkPrcode.= $selectChk[$i]."','";
		}
		$chkPrcode .= "'";
	}
	//exdebug($chkPrcode);

	$sql = "SELECT productcode,vender,display,brand,pridx,assembleuse,assembleproduct FROM tblproduct WHERE productcode IN ({$chkPrcode}) ";
	//exdebug($sql);
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		$vender[$row->productcode]=(int)$row->vender;
		$vdisp[$row->productcode]=$row->display;
		$vpridx[$row->productcode] = $row->pridx;
	}
	pmysql_free_result($result);
	//$brand=$row->brand;
	//$vpridx=$row->pridx;
	//$vassembleuse=$row->assembleuse;
	//$vassembleproduct=$row->assembleproduct;

	$chkPridx = "'";
	for($i=0;$i<count($vpridx);$i++){
		$chkPridx.= $vpridx[$i].",";
	}
	$chkPridx .= "'";

	#태그관련 지우기
	$sql = "DELETE FROM tbltagproduct WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	#리뷰 지우기
	$sql = "DELETE FROM tblproductreview WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	#위시리스트 지우기
	$sql = "DELETE FROM tblwishlist WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	#관련상품 지우기
	$sql = "DELETE FROM tblcollection WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	#테마상품 지우기
	$sql = "DELETE FROM tblproducttheme WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());
/*
	#메인상품 진열리스트 삭제
	$sql = "DELETE FROM tblmainlist WHERE pridx IN (".$chkPridx.") ";
	pmysql_query($sql,get_db_conn());

	#메인 메뉴 상품 진열리스트 삭제
	$sql = "DELETE FROM tblmainmenulist WHERE pridx IN (".$chkPridx.") ";
	pmysql_query($sql,get_db_conn());

	#카테고리별 상품 리스트 삭제
	$sql = "DELETE FROM tblrecommendlist WHERE pridx IN (".$chkPridx.") ";
	pmysql_query($sql,get_db_conn());
*/
	#상품삭제
	$sql = "DELETE FROM tblproduct WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	#카테고리 삭제
	$sql = "DELETE FROM tblproductlink WHERE c_productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	#옵션 삭제
	$sql = "DELETE FROM tblproduct_option WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	#상품접근권한 지우기
	$sql = "DELETE FROM tblproductgroupcode WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	#상품별 노출 브랜드 삭제(2016.01.22 - 김재수)
	$sql = "DELETE FROM tblbrandproduct WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	#튜토리얼 데이터 삭제
	pmysql_query("DELETE  FROM tblproduct_tutorial WHERE prcode IN ({$chkPrcode}) ", get_db_conn());
/*				2014 12 19 XNELLS는 assembleproduct 사용안함
	if($vassembleuse=="Y") {
		$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
		$sql.= "WHERE productcode IN ({$chkPrcode}) ";
		$result = pmysql_query($sql,get_db_conn());
		if($row = @pmysql_fetch_object($result)) {
			$sql = "DELETE FROM tblassembleproduct WHERE productcode IN ({$chkPrcode}) ";
			pmysql_query($sql,get_db_conn());

			if(ord(str_replace("","",$row->assemble_pridx))) {
				$sql = "UPDATE tblproduct SET ";
				$sql.= "assembleproduct = REPLACE(assembleproduct,',{$prcode}','') ";
				$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
				$sql.= "AND assembleuse != 'Y' ";
				pmysql_query($sql,get_db_conn());
			}
		}
		pmysql_free_result($result);
	} else {
		if(ord($vassembleproduct)) {
			$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
			$sql.= "WHERE productcode IN ('".str_replace(",","','",$vassembleproduct)."') ";
			$result = pmysql_query($sql,get_db_conn());
			while($row = @pmysql_fetch_object($result)) {
				$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
				$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
				$sql.= "AND display ='Y' ";
				$sql.= "AND assembleuse!='Y' ";
				$result2 = pmysql_query($sql,get_db_conn());
				if($row2 = @pmysql_fetch_object($result2)) {
					$sql = "UPDATE tblproduct SET sellprice='{$row2->sumprice}' ";
					$sql.= "WHERE productcode = '{$row->productcode}' ";
					$sql.= "AND assembleuse='Y' ";
					pmysql_query($sql,get_db_conn());
				}
				pmysql_free_result($result2);
			}
		}

		$sql = "UPDATE tblassembleproduct SET ";
		$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$vpridx}',''), ";
		$sql.= "assemble_list=REPLACE(assemble_list,',{$vpridx}','') ";
		pmysql_query($sql,get_db_conn());
	}
*/

	if($vender) {
		foreach($selectChk as $prV){
			//미니샵 테마코드에 등록된 상품 삭제
			//setVenderThemeDeleteNor($prV, $vender[$prV]);
			setVenderCountUpdateMin($vender[$prV], $vdisp[$prV]);

			$tmpcode_a=substr($prV,0,3);
			$sql = "SELECT COUNT(*) as cnt FROM tblproduct ";
			$sql.= "WHERE productcode LIKE '{$tmpcode_a}%' AND vender='{$vender[$prV]}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$prcnt=$row->cnt;
			pmysql_free_result($result);
/*
			if($prcnt==0) {
				setVenderDesignDeleteNor($tmpcode_a, $vender[$prV]);
				$imagename=$Dir.DataDir."shopimages/vender/{$vender[$prV]}_CODE10_{$tmpcode_a}.gif";
				@unlink($imagename);
			}
*/
		}
	}

	foreach($selectChk as $sltV){
		$log_content = "## 상품삭제 ## - 상품코드 $sltV - 상품명 : ".urldecode($productname)." $display_yn";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		$delshopimage=$Dir.DataDir."shopimages/product/{$sltV}*";
		proc_matchfiledel($delshopimage);

		delProductMultiImg("prdelete","",$sltV);
	}

################## 회원별 판매 금액 delete ######################

	$onload="<script>window.onload=function(){ alert(\"상품 삭제가 완료되었습니다.\");}</script>";
	$prcode="";
	$mode="";

}

if($mode=="all_category_mach"){
	if($_REQUEST["selectChk"]){
		$selectChk_tmp = substr($_REQUEST["selectChk"],0,strlen($_REQUEST["selectChk"])-1);
		$selectChk = explode(",",$selectChk_tmp);
		$temp_i = -1;
		for($i=0;$i<count($selectChk);$i++){
			$temp1 = "SELECT no,c_category , chk FROM tblproductlink  WHERE c_productcode = '".$selectChk[$i]."'";
			$temp1_result=pmysql_query($temp1,get_db_conn());
			$temp1_row=pmysql_fetch_object($temp1_result);
			pmysql_free_result($temp1_result);
			
			$temp_c_category = $temp1_row->c_category;
			$temp_chk = $temp1_row->chk;
			$temp_no = $temp1_row->no;
			
			$temp_set = "";
			$temp_and = "";
			
			if($temp_chk == 2){
				$temp2 = "SELECT standard_code, matching_code FROM tblproductcode_match where gubun='1' AND matching_code = '".$temp_c_category."'";
				$temp2_result=pmysql_query($temp2,get_db_conn());
				$temp2_row=pmysql_fetch_object($temp2_result);
				pmysql_free_result($temp2_row);
				
				$standard_code = $temp2_row->standard_code;
				$matching_code = $temp2_row->matching_code;
				
				$temp_set = $standard_code;
				$temp_and = $matching_code;
				
				$chk = "1";
			} else {
				$temp2 = "SELECT standard_code, matching_code FROM tblproductcode_match where gubun='1' AND standard_code = '".$temp_c_category."'";
				$temp2_result=pmysql_query($temp2,get_db_conn());
				$temp2_row=pmysql_fetch_object($temp2_result);
				pmysql_free_result($temp2_row);
				
				$standard_code = $temp2_row->standard_code;
				$matching_code = $temp2_row->matching_code;
				
				$temp_set = $matching_code;
				$temp_and = $standard_code;
				
				$chk = "2";
				
			}
			
			$temp3 = "update tblproductlink set c_category = '".$temp_set."',chk = '".$chk."' where c_productcode = '".$selectChk[$i]."' and c_category='".$temp_and."'";
			pmysql_query($temp3,get_db_conn());
			
// 			echo $temp3."<br>";
// 			echo "?????????<br>";
			
		}
// 		exit();
		
		$onload="<script>window.onload=function(){ alert(\"매칭변경 완료 됬습니다.\");}</script>";
	}
}

if($mode=="modify"){
	$modcode = $_REQUEST["modcode"];
	$modsellprice = $_REQUEST["modsellprice"];
	$modquantity = $_REQUEST["modquantity"];
	$moddisplay = $_REQUEST["moddisplay"];
	$mc = explode(",", $modcode);
	$ms = explode(",", $modsellprice);
	$mq = explode(",", $modquantity);
	$md = explode(",", $moddisplay);
	$temp_quantity = "";
	for($aa=0;count($mc)>$aa;$aa++){
		$temp_quantity = $mq[$aa];
		if($mq[$aa] == '품절'){
			list($temp_quantity)=pmysql_fetch("SELECT quantity FROM tblproduct WHERE productcode='".$mc[$aa]."'");
			if(!$temp_quantity) $temp_quantity = 'NULL';
		}
		$usql = "UPDATE tblproduct ";
		$usql.= "SET sellprice = {$ms[$aa]} ";
		$usql.= ", quantity = ".$temp_quantity." ";
		$usql.= ", display = '".$md[$aa]."' ";
		$usql.= "WHERE productcode = '".$mc[$aa]."' ";
		pmysql_query($usql,get_db_conn());
	}
}

else if($mode=="copy"){
	$prcode=$_REQUEST["prcode"];
	if ( strlen( $prcode ) > 0 ) {
		$sql = "SELECT * FROM tblproduct WHERE productcode = '{$prcode}'";
		$result = pmysql_query($sql,get_db_conn());
		if ( $row = pmysql_fetch_object( $result ) ) {
            $copy_vender = $row->vender;
            #해당 상품의 카테고리
            $cate_sql = "SELECT pl.c_category , pl.c_date_1, pl.c_date_2, pl.c_date_3, pl.c_date_4, pl.c_date, pc.code_a||pc.code_b||pc.code_c||pc.code_d AS catecode
                    FROM tblproductlink pl
                    JOIN tblproductcode pc ON pl.c_category = pc.code_a||pc.code_b||pc.code_c||pc.code_d
                    WHERE pl.c_productcode = '{$prcode}'
                    AND pl.c_maincate = '1'
                    LIMIT 1
            ";
            $cate_res  = pmysql_query( $cate_sql, get_db_conn() );
            $cate_row  = pmysql_fetch_object( $cate_res );
            $main_cate = $cate_row;
            pmysql_free_result( $cate_res );

			$copycode = $main_cate->catecode;

            #카테고리 코드 + 6자리 숫자코드로 새 코드 발급
			$sql = "SELECT productcode FROM tblproduct WHERE productcode LIKE '{$copycode}%' ";
			$sql.= "ORDER BY productcode DESC LIMIT 1 ";
			$result = pmysql_query( $sql, get_db_conn() );
			if ( $rows = pmysql_fetch_object( $result ) ) {
				$newproductcode = substr( $rows->productcode, 12 ) + 1;
				$newproductcode = substr( "000000".$newproductcode, strlen( $newproductcode ) );
			} else {
				$newproductcode = "000001";
			}
			pmysql_free_result($result);

			$path = $Dir.DataDir."shopimages/product/";

			if ( ord( $row->maximage ) ) {
				$ext = strtolower( pathinfo( $row->maximage, PATHINFO_EXTENSION ) );
				$maximage = $copycode.$newproductcode.'/'.$copycode.$newproductcode."_thum1_500X500.".$ext;
				if ( file_exists( $path.$row->maximage ) ) {
                    if( !is_dir( $path.$copycode.$newproductcode ) ){
                        mkdir( $path.$copycode.$newproductcode, 0700 );
                        chmod( $path.$copycode.$newproductcode, 0777 );
                    }
					copy( $path.$row->maximage, $path.$maximage );
				}
			} else {
                $maximage = "";
            }

			if ( ord( $row->minimage ) ) {
				$ext = strtolower( pathinfo( $row->minimage, PATHINFO_EXTENSION ) );
				$minimage = $copycode.$newproductcode.'/'.$copycode.$newproductcode."_thum2_500X500.".$ext;
				if ( file_exists( $path.$row->minimage ) ) {
					copy( $path.$row->minimage, $path.$minimage );
				}
			} else {
                $minimage = "";
            }

			if ( ord( $row->tinyimage ) ) {
				$ext = strtolower( pathinfo( $row->tinyimage, PATHINFO_EXTENSION ) );
				$tinyimage = $copycode.$newproductcode.'/'.$copycode.$newproductcode."_thum3_500X500.".$ext;
				if ( file_exists( $path.$row->tinyimage ) ) {
					copy( $path.$row->tinyimage, $path.$tinyimage );
				}
			} else {
                $tinyimage="";
            }

            if (ord($row->over_minimage)) {
				$ext = strtolower( pathinfo( $row->over_minimage, PATHINFO_EXTENSION ) );
				$over_minimage = $copycode.$newproductcode.'/'.$copycode.$newproductcode."_thum4_500X500.".$ext;
				if ( file_exists( $path.$row->over_minimage ) ) {
					copy( $path.$row->over_minimage, $path.$over_minimage );
				}
			} else {
                $over_minimage="";
            }

            $quantity = $row->quantity;
            # 브랜드 추가 필요
			if( ord( $row->brand ) == 0 ) $row->brand = 'NULL';

			$productname     = pmysql_escape_string( $row->productname );
			$production      = pmysql_escape_string( $row->production );
			$madein          = pmysql_escape_string( $row->madein );
			$model           = pmysql_escape_string( $row->model );
			$tempkeyword     = pmysql_escape_string( $row->keyword );
			$addcode         = pmysql_escape_string( $row->addcode );
			$userspec        = pmysql_escape_string( $row->userspec );
			$option1         = pmysql_escape_string( $row->option1 );
			$option2         = pmysql_escape_string( $row->option2 );
			$content         = pmysql_escape_string( $row->content );
			$selfcode        = pmysql_escape_string( $row->selfcode );
			$assembleproduct = pmysql_escape_string( $row->assembleproduct );

			$sql = "INSERT INTO tblproduct(
			productcode	,
			productname	,
			assembleuse	,
			assembleproduct	,
			sellprice	,
			consumerprice	,
			buyprice	,
			reserve		,
			reservetype	,
			production	,
			madein		,
			model		,
			brand		,
			opendate	,
			selfcode	,
			bisinesscode	,
			quantity	,
			group_check	,
			keyword		,
			addcode		,
			userspec	,
			maximage	,
			minimage	,
			tinyimage	,
			option1		,
			option2		,
			sabangnet_flag,
			etctype		,
			deli		,
			package_num	,
			display		,
			date		,
			vender		,
			regdate		,
			modifydate	,
			content,
			sabangnet_prop_val,
            icon,
            dicker ,
            min_quantity,
            max_quantity,
            setquota,
            prkeyword,
            deli_qty,
            deli_select,
            mdcommentcolor,
            option2_tf,
            option2_maxlen,
            option_type,
            soldout,
            option1_tf,
            deliinfono,
            deli_price,
            over_minimage
			) VALUES (
			'".$copycode.$newproductcode."',
			'{$productname}',
			'{$row->assembleuse}',
			'{$row->assembleproduct}',
			{$row->sellprice},
			{$row->consumerprice},
			{$row->buyprice},
			'{$row->reserve}',
			'{$row->reservetype}',
			'{$production}',
			'{$madein}',
			'{$model}',
			{$row->brand},
			'{$row->opendate}',
			'{$row->selfcode}',
			'{$row->bisinesscode}',
			{$quantity},
			'{$row->group_check}',
			'{$tempkeyword}',
			'{$addcode}',
			'{$userspec}',
			'{$maximage}',
			'{$minimage}',
			'{$tinyimage}',
			'{$option1}',
			'{$option2}',
			'{$copy_type}',
			'{$row->etctype}',
			'{$row->deli}',
			'".(int)$row->package_num."',
			'N',
			'".(($newtime=="Y")?date("YmdHis"):$row->date)."',
			'{$row->vender}',
			now(),
			now(),
			'{$content}',
			'{$row->sabangnet_prop_val}',
            '{$row->icon}',
            '{$row->dicker }',
            '{$row->min_quantity}',
            '{$row->max_quantity}',
            '{$row->setquota}',
            '{$row->prkeyword}',
            '{$row->deli_qty}',
            '{$row->deli_select}',
            '{$row->mdcommentcolor}',
            '{$row->option2_tf}',
            '{$row->option2_maxlen}',
            '{$row->option_type}',
            '{$row->soldout}',
            '{$row->option1_tf}',
            '{$row->deliinfono}',
            '{$row->deli_price}',
            '{$over_minimage}'

            ) RETURNING pridx";
			$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));

			$fromproductcodes.= "|".$prcode;
			$copyproductcodes.= "|".$copycode.$newproductcode;

			$copy_cate_insert_sql = "INSERT INTO tblproductlink
    			(c_productcode, c_category, c_maincate, c_date, c_date_1, c_date_2, c_date_3, c_date_4 )
				VALUES
				('".$copycode.$newproductcode."', '".$main_cate->c_category."', '1', '".$main_cate->c_date."', '".$main_cate->c_date_1."', '".$main_cate->c_date_2."', '".$main_cate->c_date_3."', '".$main_cate->c_date_4."'  )";
			pmysql_query($copy_cate_insert_sql,get_db_conn());

			$log_content = "## 상품복사입력 ## - 상품코드 {$prcode} => ".$copycode.$newproductcode." - 상품명 : ".$productname;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		}
        # 멀티이미지는 가져오지 못한다
		delProductMultiImg($mode,substr($fromproductcodes,1),substr($copyproductcodes,1));

		#옵션복사
		$optionSelectSql = " SELECT option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use, option_tf ";
		$optionSelectSql .= "FROM tblproduct_option WHERE productcode = '".$prcode."' ";
        $optionSelectSql .= "ORDER BY option_num ASC ";
		$optionSelectRes = pmysql_query( $optionSelectSql, get_db_conn() );
		while( $optionSelect = pmysql_fetch_object( $optionSelectRes ) ){
			$optionCopySql = "INSERT INTO tblproduct_option ( option_code, productcode, option_price, option_quantity, ";
			$optionCopySql.= "option_quantity_noti, option_type, option_use, option_tf ) ";
			$optionCopySql.= "VALUES ( '".$optionSelect->option_code."','".$copycode.$newproductcode."', '".$optionSelect->option_price."', '".$optionSelect->option_quantity."', ";
			$optionCopySql.= "'".$optionSelect->option_quantity_noti."', '".$optionSelect->option_type."', '".$optionSelect->option_use."', '".$optionSelect->option_tf."' ) ";
			pmysql_query( $optionCopySql, get_db_conn() );
		}
		pmysql_free_result( $optionSelectRes );


        # 브랜드 처리
        if ( $copy_vender ) {
            list( $bridx ) = pmysql_fetch("SELECT bridx FROM tblproductbrand WHERE vender='{$copy_vender}'");
            if ( $bridx > 0 ) {
                @pmysql_query("UPDATE tblproduct SET brand = '{$bridx}' WHERE productcode = '".$copycode.$newproductcode."'",get_db_conn());
                $bpSql = "INSERT INTO tblbrandproduct(productcode, bridx, sort) VALUES ('".$copycode.$newproductcode."','".$bridx."','1')";
                pmysql_query($bpSql,get_db_conn());
            }
        }

		$onload="<script>window.onload=function(){ alert(\"상품 복사가 완료되었습니다.\");}</script>";
		$prcode="";
	}
}

if($mode == "change_display") {
    //echo "change";
    if($_REQUEST["selectChk"]){
		//$selectChk_tmp = substr($_REQUEST["selectChk"],0,strlen($_REQUEST["selectChk"])-1);
        $selectChk_tmp = substr($_REQUEST["selectChk"], 0, -1);
		$selectChk = explode(",",$selectChk_tmp);
		$chkPrcode = "'";
		for($i=0;$i<count($selectChk);$i++){
			$chkPrcode.= $selectChk[$i]."','";
		}
		$chkPrcode .= "'";
	}
	//exdebug($chkPrcode);

	#진열여부 UPDATE
	$sql = "Update tblproduct Set display = '".$display_type."' WHERE productcode IN ({$chkPrcode})";
    //exdebug($sql);
	pmysql_query($sql,get_db_conn());

	foreach($selectChk as $sltV){
		$log_content = "## 상품진열상태변경 ## - 상품코드 $sltV - 상태 : ".$display_type;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

	$onload="<script>window.onload=function(){ alert(\"진열상태 변경이 완료되었습니다.\");}</script>";
	$display_type="";
	$mode="";

}

// 네이버 지식쇼핑 진열유무
if($mode == "change_naver_display") {
    if($_REQUEST["selectChk"]){
        $selectChk_tmp = substr($_REQUEST["selectChk"], 0, -1);
		$selectChk = explode(",",$selectChk_tmp);
		$chkPrcode = "'";
		for($i=0;$i<count($selectChk);$i++){
			$chkPrcode.= $selectChk[$i]."','";
		}
		$chkPrcode .= "'";
	}

	#진열여부 UPDATE
	$sql = "Update tblproduct Set naver_display = '".$display_type."' WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	foreach($selectChk as $sltV){
		$log_content = "## 지식쇼핑 상품진열상태변경 ## - 상품코드 $sltV - 상태 : ".$display_type;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

	$onload="<script>window.onload=function(){ alert(\"지식쇼핑 진열상태 변경이 완료되었습니다.\");}</script>";
	$display_type="";
	$mode="";

}

// 다음 쇼핑하우 진열유무
if($mode == "change_daum_display") {
	if($_REQUEST["selectChk"]){
		$selectChk_tmp = substr($_REQUEST["selectChk"], 0, -1);
		$selectChk = explode(",",$selectChk_tmp);
		$chkPrcode = "'";
		for($i=0;$i<count($selectChk);$i++){
			$chkPrcode.= $selectChk[$i]."','";
		}
		$chkPrcode .= "'";
	}

	#진열여부 UPDATE
	$sql = "Update tblproduct Set daum_display = '".$display_type."' WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	foreach($selectChk as $sltV){
		$log_content = "## 쇼핑하우 상품진열상태변경 ## - 상품코드 $sltV - 상태 : ".$display_type;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

	$onload="<script>window.onload=function(){ alert(\"쇼핑하우 진열상태 변경이 완료되었습니다.\");}</script>";
	$display_type="";
	$mode="";

}

// o2o 배송 진열유무
if($mode == "change_o2o_yn") {
	if($_REQUEST["selectChk"]){
		$selectChk_tmp = substr($_REQUEST["selectChk"], 0, -1);
		$selectChk = explode(",",$selectChk_tmp);
		$chkPrcode = "'";
		for($i=0;$i<count($selectChk);$i++){
			$chkPrcode.= $selectChk[$i]."','";
		}
		$chkPrcode .= "'";
	}

	#진열여부 UPDATE
	$sql = "Update tblproduct Set o2o_yn = '".$display_type."' WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	foreach($selectChk as $sltV){
		$log_content = "## o2o 배송 상품진열상태변경 ## - 상품코드 $sltV - 상태 : ".$display_type;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

	$onload="<script>window.onload=function(){ alert(\"O2O배송 진열상태 변경이 완료되었습니다.\");}</script>";
	$display_type="";
	$mode="";

}

// 진열상품 시간변경
if($mode == "change_product_time") {
	
	$curdate = date("YmdHis");
	
	if($_REQUEST["selectChk"]){
		$selectChk_tmp = substr($_REQUEST["selectChk"], 0, -1);
		$selectChk = explode(",",$selectChk_tmp);
		$chkPrcode = "'";
		for($i=0;$i<count($selectChk);$i++){
			$chkPrcode.= $selectChk[$i]."','";
		}
		$chkPrcode .= "'";
	}

	#진열여부 UPDATE
	$sql = "Update tblproduct Set date = '{$curdate}' , regdate		= now(), modifydate		= now() WHERE productcode IN ({$chkPrcode})";
	pmysql_query($sql,get_db_conn());

	foreach($selectChk as $sltV){
		$log_content = "## 지식쇼핑 상품진열상태변경 ## - 상품코드 $sltV - 상태 : ".$display_type;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

	$onload="<script>window.onload=function(){ alert(\"상품 진열시간 변경이 완료되었습니다.\");}</script>";
	$display_type="";
	$mode="";

}

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	//$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
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
//print_r($venderlist);

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
}

function ProductInfo(prcode,popuptype,category_data) {
	code=prcode.substring(0,12);
	popup=popuptype;
	document.form_register.code.value=code;
	document.form_register.prcode.value=prcode;
	document.form_register.popup.value=popup;
	document.form_register.category_data.value=category_data;
	if (popup=="YES") {
		/*document.form_register.action="product_register.add.php"; 팝업 ㅍ페이지 통일 */
		document.form_register.action="product_register.set.php";
		document.form_register.target="register";
		window.open("about:blank","register","width=1500,height=700,scrollbars=yes,status=no");
	}
	 else {
		document.form_register.action="product_register.set.php";
		document.form_register.target="";
	}

	// 폼데이터를 시리얼라이즈 하여 셋팅
	var editserialize = $("form[name='form_register']").serialize();
	editserialize = decodeURIComponent(editserialize.replace(/%2F/g, " "));
	document.form_register.serial_value.value = editserialize;

	document.form_register.submit();
}

function ProductDel(prcode){
	if(confirm("선택하신 상품을 정말로 삭제하시겠습니까?")){
		document.form1.mode.value="delete";
		document.form1.prcode.value=prcode;
		document.form1.submit();
	}
}

function Productcopy(prcode,copy_type){
	if(confirm("선택하신 상품을 동일하게 한개 더 생성하시겠습니까?")){
		document.form1.mode.value="copy";
		document.form1.prcode.value=prcode;
		document.form1.copy_type.value=copy_type;
		document.form1.submit();
	}
}

function registeradd(){
	document.form_register.code.value='';
	document.form_register.prcode.value='';
	document.form_register.popup.value="NO";
	//document.form_register.code.value="004002000000";
	document.form_register.action="product_register.set.php";
	document.form_register.target="";
	document.form_register.submit();
}

function listnumSet(listnum){
	document.form1.listnum.value=listnum.value;
	document.form1.submit();
}

$(document).ready(function(){
	$('.check-all').click(function(){
		$('.code_check').prop('checked', this.checked);
	});

	$('.th_sellprice').keyup(function(){
		var nocomma = document.form1.th_sellprice.value.replace(/,/gi,'');
		var b = '';
		var i = 0;
		for (var k=(nocomma.length-1);k>=0; k--){
			var a = nocomma.charAt(k);
			if (k == 0 && a == 0) {
			document.form1.th_sellprice.value = '';
			return;
			}else {
				if (i != 0 && i % 3 == 0) {
				b = a + "," + b ;
				}else {
				b = a + b;
				}
			i++;
			}
		}
		document.form1.th_sellprice.value = b;
		return;
	});

	$('.edit').click(function(){
		if (confirm("선택한 항목을 수정하시겠습니까?") == true){
			var modfrm = document.form_modify;
			var edfrm = document.form1;

			for (var i=0; i<edfrm.code_check.length; i++) {
				if (edfrm.code_check[i].checked) {
					modfrm.modcode.value = edfrm.product_code[i].value+","+modfrm.modcode.value;

					if(edfrm.th_sellprice.value!=""){
						var sellprice = edfrm.th_sellprice.value;
						sellprice = sellprice.replace(/,/gi, '');
						modfrm.modsellprice.value = sellprice+","+modfrm.modsellprice.value;
					}else{
						var sellprice = edfrm.sellprice[i].value;
						sellprice = sellprice.replace(/,/gi, '');
						modfrm.modsellprice.value = sellprice+","+modfrm.modsellprice.value;
					}

					if(edfrm.th_quantity.value!=""){
						modfrm.modquantity.value = edfrm.th_quantity.value+","+modfrm.modquantity.value;
					}else{
						modfrm.modquantity.value = edfrm.quantity[i].value+","+modfrm.modquantity.value;
					}

					if(edfrm.th_display.value!=""){
						modfrm.moddisplay.value = edfrm.th_display.value+","+modfrm.moddisplay.value;
					}else{
						modfrm.moddisplay.value = edfrm.display_select[i].value+","+modfrm.moddisplay.value;
					}
				}
			}
			modfrm.mode.value = "modify";
			modfrm.modcode.value=modfrm.modcode.value.slice(0, -1);
			modfrm.modsellprice.value=modfrm.modsellprice.value.slice(0, -1);
			modfrm.modquantity.value=modfrm.modquantity.value.slice(0, -1);
			modfrm.moddisplay.value=modfrm.moddisplay.value.slice(0, -1);
			modfrm.submit();
		}else{
			return;
		}

	});
});

function allDelete(){
	if(confirm("선택하신 상품을 정말로 삭제하시겠습니까?")){
		var selectCheck = "";
		if($("input[name=code_check]:checked").length<1){
			alert("삭제하실 상품을 선택해 주세요.");
            return;
		}
		$("input[name=code_check]:checked").each(function(){
			selectCheck += $(this).val()+",";
		});
		$("#selectChk").val(selectCheck);
		document.form1.mode.value="all_delete";
		$("#frm1").submit();
	}
}

// 카테고리 매칭 일괄 변경
function allCategoryMach(){
	if(confirm("선택하신 상품을 정말로 매칭변경하시겠습니까?")){
		var selectCheck = "";
		if($("input[name=code_check]:checked").length<1){
			alert("삭제하실 상품을 선택해 주세요.");
            return;
		}
		$("input[name=code_check]:checked").each(function(){
			selectCheck += $(this).val()+",";
		});
		$("#selectChk").val(selectCheck);
		document.form1.mode.value="all_category_mach";
		$("#frm1").submit();
	}
}

function Chk_Display(view){

    document.form1.display_type.value = view;

    if(view == "N") {
        if(confirm("선택하신 상품을 미진열로 변경하시겠습니까?")){
            var selectCheck = "";
            if($("input[name=code_check]:checked").length<1){
                alert("변경하실 상품을 선택해 주세요.");
                return;
            }
            $("input[name=code_check]:checked").each(function(){
                selectCheck += $(this).val()+",";
            });
            $("#selectChk").val(selectCheck);
            document.form1.mode.value="change_display";
            $("#frm1").submit();
        }
    } else if(view == "Y") {
        if(confirm("선택하신 상품을 진열로 변경하시겠습니까?")){
            var selectCheck = "";
            if($("input[name=code_check]:checked").length<1){
                alert("변경하실 상품을 선택해 주세요.");
                return;
            }
            $("input[name=code_check]:checked").each(function(){
                selectCheck += $(this).val()+",";
            });
            $("#selectChk").val(selectCheck);
            document.form1.mode.value="change_display";
            $("#frm1").submit();
        }
    }
}

function Chk_Naver_Display(view){

    document.form1.display_type.value = view;

    if(view == "N") {
        if(confirm("선택하신 상품을 지식쇼핑 미진열로 변경하시겠습니까?")){
            var selectCheck = "";
            if($("input[name=code_check]:checked").length<1){
                alert("변경하실 상품을 선택해 주세요.");
                return;
            }
            $("input[name=code_check]:checked").each(function(){
                selectCheck += $(this).val()+",";
            });
            $("#selectChk").val(selectCheck);
            document.form1.mode.value="change_naver_display";
            $("#frm1").submit();
        }
    } else if(view == "Y") {
        if(confirm("선택하신 상품을 지식쇼핑 진열로 변경하시겠습니까?")){
            var selectCheck = "";
            if($("input[name=code_check]:checked").length<1){
                alert("변경하실 상품을 선택해 주세요.");
                return;
            }
            $("input[name=code_check]:checked").each(function(){
                selectCheck += $(this).val()+",";
            });
            $("#selectChk").val(selectCheck);
            document.form1.mode.value="change_naver_display";
            $("#frm1").submit();
        }
    }
}

function Chk_Daum_Display(view){

    document.form1.display_type.value = view;

    if(view == "N") {
        if(confirm("선택하신 상품을 쇼핑하우 미진열로 변경하시겠습니까?")){
            var selectCheck = "";
            if($("input[name=code_check]:checked").length<1){
                alert("변경하실 상품을 선택해 주세요.");
                return;
            }
            $("input[name=code_check]:checked").each(function(){
                selectCheck += $(this).val()+",";
            });
            $("#selectChk").val(selectCheck);
            document.form1.mode.value="change_daum_display";
            $("#frm1").submit();
        }
    } else if(view == "Y") {
        if(confirm("선택하신 상품을 쇼핑하우 진열로 변경하시겠습니까?")){
            var selectCheck = "";
            if($("input[name=code_check]:checked").length<1){
                alert("변경하실 상품을 선택해 주세요.");
                return;
            }
            $("input[name=code_check]:checked").each(function(){
                selectCheck += $(this).val()+",";
            });
            $("#selectChk").val(selectCheck);
            document.form1.mode.value="change_daum_display";
            $("#frm1").submit();
        }
    }
}

function Chk_O2O_Yn(view){

    document.form1.display_type.value = view;

    if(view == "N") {
        if(confirm("선택하신 상품을 O2O배송 미진열로 변경하시겠습니까?")){
            var selectCheck = "";
            if($("input[name=code_check]:checked").length<1){
                alert("변경하실 상품을 선택해 주세요.");
                return;
            }
            $("input[name=code_check]:checked").each(function(){
                selectCheck += $(this).val()+",";
            });
            $("#selectChk").val(selectCheck);
            document.form1.mode.value="change_o2o_yn";
            $("#frm1").submit();
        }
    } else if(view == "Y") {
        if(confirm("선택하신 상품을  O2O배송 진열로 변경하시겠습니까?")){
            var selectCheck = "";
            if($("input[name=code_check]:checked").length<1){
                alert("변경하실 상품을 선택해 주세요.");
                return;
            }
            $("input[name=code_check]:checked").each(function(){
                selectCheck += $(this).val()+",";
            });
            $("#selectChk").val(selectCheck);
            document.form1.mode.value="change_o2o_yn";
            $("#frm1").submit();
        }
    }
}

function Update_Product_Time(view){

    document.form1.display_type.value = view;

    if(view == "N") {
        if(confirm("선택하신 상품을 진열시간 삭제하시 겟습니까?")){
            var selectCheck = "";
            if($("input[name=code_check]:checked").length<1){
                alert("변경하실 상품을 선택해 주세요.");
                return;
            }
            $("input[name=code_check]:checked").each(function(){
                selectCheck += $(this).val()+",";
            });
            $("#selectChk").val(selectCheck);
            document.form1.mode.value="change_product_time";
            $("#frm1").submit();
        }
    } else if(view == "Y") {
        if(confirm("선택하신 상품을 진열시간 변경을 하시겠습니까?")){
            var selectCheck = "";
            if($("input[name=code_check]:checked").length<1){
                alert("변경하실 상품을 선택해 주세요.");
                return;
            }
            $("input[name=code_check]:checked").each(function(){
                selectCheck += $(this).val()+",";
            });
            $("#selectChk").val(selectCheck);
            document.form1.mode.value="change_product_time";
            $("#frm1").submit();
        }
    }
}

function reg_review(prcode){
	window.open('product_review_reg_form.php?productcode='+prcode ,'_blank',"width=400,height=700scrollbars=yes");
}

function ProductExcel() {
	document.downexcelform.productcodes.value= '';
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function ProductCheckExcel() {
	var selectCheck = "";
	if($("input[name=code_check]:checked").length<1){
		alert("선택하신 상품이 없습니다.");
		return;
	}
	$("input[name=code_check]:checked").each(function(){
		if(selectCheck!='') selectCheck +=",";
		selectCheck += $(this).val();
	});
	document.downexcelform.productcodes.value= selectCheck;
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function ProductCheckUpdate(type){
	var alert_text	= '';
	if(type=='name') {
		alert_text	= '상품명을';
	} else if(type=='img') {
		alert_text	= '이미지를';
	}

	if($("input[name=code_check]:checked").length<1){
		alert("업데이트하실 상품을 선택해 주세요.");
		return;
	}

	if(confirm("선택하신 상품의 "+alert_text+" 업데이트 하시겠습니까?")){
		var selectCheck = "";
		$("input[name=code_check]:checked").each(function(){
			if(selectCheck!='') selectCheck +=",";
			selectCheck += $(this).val();
		});
		
		$.ajax({ 
			type: "POST", 
			url: "product_update_img.php", 
			data: "type="+type+"&productcodelist=" + selectCheck,
			dataType:"json", 
			success: function(data) {
					alert(data.msg);
				if (data.code == 0) {
					window.location.reload();
					return;
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다."); 
				return;
			}
		}); 
	}
}

</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span>상품관리 리스트</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_product.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<form name=form1 id="frm1" action="<?=$_SERVER['PHP_SELF']?>" method=POST>
			<input type=hidden name=mode>
			<input type=hidden name=prcode>
			<input type=hidden name=copy_type>
            <input type=hidden name=display_type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=listnum value="<?=$listnum?>">
			<input type="hidden" name="selectChk" id="selectChk"/>
			<input type="hidden" name="orderby" id="orderby" value="<?=$orderby?>"/>
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td>
				<div class="title_depth3">상품관리</div>

				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20">
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>상품검색</span></th>
							<td>
								<select name="code_type" style="width:80px;height:32px;vertical-align:middle;">
							<?php 
								if($code_type=="name" || $code_type == ""){
									echo "<option value=\"name\" selected>상품명</option>\n";
									echo "<option value=\"code\" >상품코드</option>\n";
									echo "<option value=\"erpcode\" >ERP코드</option>\n";
								} else if ($code_type=="code"){
									echo "<option value=\"name\" >상품명</option>\n";
									echo "<option value=\"code\" selected>상품코드</option>\n";
									echo "<option value=\"erpcode\" >ERP코드</option>\n";
								} else if ($code_type=="erpcode"){
									echo "<option value=\"name\" >상품명</option>\n";
									echo "<option value=\"code\" >상품코드</option>\n";
									echo "<option value=\"erpcode\" selected>ERP코드</option>\n";
								}
							?>
								<!-- 
								<option value="name" <?if($code_type=="name")echo"selected";?>>상품명</option>
								<option value="code" <?if($code_type=="code")echo"selefcted";?>>상품코드</option>
								<option value="erpcode" <?if($code_type=="erpcode")echo"selected";?>>ERP코드</option>
								 -->
								</select> 
								<textarea rows="2" cols="10" class="w200" name="s_keyword" id="s_keyword" style="resize:none;vertical-align:middle;"><?=$s_keyword?></textarea>
								<!-- 
								<input class="w200" type="text" name="s_keyword" id="s_keyword" value="<?=$s_keyword?>"> <!-- style="line-height: 50px;" -->
								<!-- 
								&nbsp; <input type="checkbox" value="Y" name="hotdealyn" <?=$checked["hotdealyn"]["Y"]?>> 핫딜상품만검색
								 -->
								 
								&nbsp; <input type="checkbox" value="Y" name="dcpriceyn" <?=$checked["dcpriceyn"]["Y"]?>> 할인상품만검색
								</td>
							<!-- <th style="text-align:center; width:250;" rowspan="2">빠른조회<br/>
							<select name="code_type"><option value="1"  <?if($code_type=="1"){ echo "selected";}?>>상품코드</option>
														<option value="2"  <?if($code_type=="2"){ echo "selected";}?>>ERP코드</option></select></th> -->
						</tr>
						<tr>
							<th><span>카테고리 검색</span></th>
							<td>
				<?php
								$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
								$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
								$i=0;
								$ii=0;
								$iii=0;
								$iiii=0;
								$strcodelist = "";
								$strcodelist.= "<script>\n";
								$result = pmysql_query($sql,get_db_conn());
								$selcode_name="";

								while($row=pmysql_fetch_object($result)) {
									$strcodelist.= "var clist=new CodeList();\n";
									$strcodelist.= "clist.code_a='{$row->code_a}';\n";
									$strcodelist.= "clist.code_b='{$row->code_b}';\n";
									$strcodelist.= "clist.code_c='{$row->code_c}';\n";
									$strcodelist.= "clist.code_d='{$row->code_d}';\n";
									$strcodelist.= "clist.type='{$row->type}';\n";
									$strcodelist.= "clist.code_name='{$row->code_name}';\n";
									if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
										$strcodelist.= "lista[{$i}]=clist;\n";
										$i++;
									}
									if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
										if ($row->code_c=="000" && $row->code_d=="000") {
											$strcodelist.= "listb[{$ii}]=clist;\n";
											$ii++;
										} else if ($row->code_d=="000") {
											$strcodelist.= "listc[{$iii}]=clist;\n";
											$iii++;
										} else if ($row->code_d!="000") {
											$strcodelist.= "listd[{$iiii}]=clist;\n";
											$iiii++;
										}
									}
									$strcodelist.= "clist=null;\n\n";
								}
								pmysql_free_result($result);
								$strcodelist.= "CodeInit();\n";
								$strcodelist.= "</script>\n";

								echo $strcodelist;


								echo "<select name=code_a style=\"width:170px;\" onchange=\"SearchChangeCate(this,1)\">\n";
								echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_b style=\"width:170px;\" onchange=\"SearchChangeCate(this,2)\">\n";
								echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_c style=\"width:170px;\" onchange=\"SearchChangeCate(this,3)\">\n";
								echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_d style=\"width:170px;\">\n";
								echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
				?>
							</td>
						</tr>
						<tr>
							<th><span>등록일</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
							<!-- <td rowspan="4"><textarea style="width:230; height:100;" name="code_area"><?=$code_area?></textarea> </td> -->
						</tr>
						<tr>
							<th><span>상품금액별 검색</span></th>
							<td><input class="input_bd_st01" type="text" name="sellprice_min" value="<?=$sellprice_min?>"/> 원 ~ <input class="input_bd_st01" type="text" name="sellprice_max" value="<?=$sellprice_max?>"/> 원</td>
						</tr>
						<tr>
							<th><span>품절 유무</span></th>
							<td>
								<input type="radio" name="s_check" value="all" <?=$checked["s_check"]['all']?>/>전체 
								<input type="radio" name="s_check" value="1" <?=$checked["s_check"]['1']?>/>미품절 
								<input type="radio" name="s_check" value="2" <?=$checked["s_check"]['2']?>/>품절
							</td>
						</tr>
						<tr>
							<th><span>진열 유무</span></th>
							<td>
                                <input type="radio" name="display_yn" value="all" <?=$checked["display_yn"]['all']?>/>전체
                                <input type="radio" name="display_yn" value="Y" <?=$checked["display_yn"]['Y']?>/> 진열&nbsp;&nbsp;
                                <input type="radio" name="display_yn" value="N" <?=$checked["display_yn"]['N']?>/> 미진열&nbsp;&nbsp;
                                <input type="radio" name="display_yn" value="R" <?=$checked["display_yn"]['R']?>/> 가등록
                            </td>
						</tr>
                        <!-- <TR>
                            <th><span>벤더검색</span></th>
                            <td><select name=sel_vender class="select">
                                <option value="">==== 전체 ====</option>
        <?php
                                $sql = "select vender, com_name from tblvenderinfo group by vender, com_name order by com_name asc";
                                $result = pmysql_query($sql,get_db_conn());
                                while($row = pmysql_fetch_object($result)){
                                    echo "<option value=\"{$row->vender}\"";
                                    if($sel_vender==$row->vender) echo " selected";
                                    echo ">{$row->com_name}</option>\n";
                                }
        ?>
                                </select>
                            </td>
                        </TR> -->
                        <TR>
                            <th><span>브랜드검색</span></th>
                            <td><select name=sel_vender class="select" onChange="javascript:resetBrandSearchWord(this);">
                                <option value="">==== 전체 ====</option>
        <?php
                        foreach($venderlist as $key => $val) {
                            echo "<option value=\"{$val->bridx}\"";
                            if($sel_vender==$val->bridx) echo " selected";
                            echo ">{$val->brandname}</option>\n";
                        }
        ?>
                                </select>
                                <input class="w200" type="text" id="s_brand_keyword" name="s_brand_keyword" value="<?=$s_brand_keyword?>" <?php if($sel_vender) echo "disabled";?>>
                            </td>
                        </TR>
                        <!-- 20170410 시즌검색  -->
                        <TR>
                            <th><span>시즌 검색</span></th>
                            <td><select name=sel_season class="select">
                                <option value="">==== 전체 ====</option>
		<?php
								// 20170410 시즌검색 추가
                                $sql = "SELECT SEASON_YEAR,SEASON,SEASON_KOR_NAME,season_eng_name FROM tblproductseason ORDER BY NO DESC";
                                $result = pmysql_query($sql,get_db_conn());
                                while($row = pmysql_fetch_object($result)){
                                    echo "<option value=\"{$row->season_year},{$row->season}\"";
                                    if($sel_season=="{$row->season_year},{$row->season}") echo " selected";
                                    echo ">{$row->season_eng_name}</option>\n";
                                }
        ?>
                                </select>
                            </td>
                        </TR>

					</table>
					<!-- 
					 <p class="ta_r"><a href="#;"><img src="img/btn/btn_search01.gif" alt="검색" onclick="javascript:SearchFrom();"/></a>&nbsp;<a href="javascript:ProductExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></p>
					 -->
					<p class="ta_r"><a href="#;"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a>&nbsp;<a href="#;" onclick="ProductExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></p>
				</div>

				<div class="table_style02">
					<table width=100% cellpadding=0 cellspacing=0 border=0>
						<colgroup>
							<col width="50" />
							<col width="50" />
							<col width="80" />
							<col width="" />
							<col width="80" />
                            <col width="80" />
							<col width="80" />
							<col width="80" />
							<col width="80" />
							<col width="80" />
							<col width="60" />
							<col width="60" />
							<col width="60" />
							<col width="60" />
							<col width="60" />
							<col width="60" />
						</colgroup>
						<div class="btn_right">
							<select name="listnum_select" onchange="javascript:listnumSet(this)">
                                <option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
                                <option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
                                <option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
                                <option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
                                <option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
                                <option value="200" <?if($listnum==200)echo "selected";?>>200개씩 보기</option>
                                <option value="300" <?if($listnum==300)echo "selected";?>>300개씩 보기</option>
                                <option value="400" <?if($listnum==400)echo "selected";?>>400개씩 보기</option>
                                <option value="500" <?if($listnum==500)echo "selected";?>>500개씩 보기</option>
                                <option value="100000" <?if($listnum==100000)echo "selected";?>>전체</option>
							</select>
						</div>
						<tr>
							<th><input type="checkbox" name="check-all" class="check-all"></th>
							<th>No</th>
							<th>이미지</th>
							<th width="300">상품명</th>
							<!--  
							<th>쇼핑몰재고</th>
							-->
							<th>품절여부</th>
                            <th><a href="#;" onclick="javascritp:orderby('date');" class="link_wh">등록일</a></th>
							<th>브랜드</th>
							<th>구매가</th>
							<th><a href="#;" onclick="javascritp:orderby('consumerprice');" class="link_wh">정가<br/></a></th>
							<th><a href="#;" onclick="javascritp:orderby('sellprice');" class="link_wh">판매가</a></th>
							<th><a href="#;" onclick="javascritp:orderby('quantity');" class="link_wh">재고</a></th>
							<th>진열유무</th>
							<th>지식쇼핑</th>
							<th>쇼핑하우</th>
							<th>O2O배송</th>
							<th>복사</th>
							<th>비고</th>
						</tr>
		<?php
						$page_numberic_type=1;

						$qry_form	= 'tblproduct';

						if($likecode) $qry= "AND b.c_category LIKE '{$likecode}%' ";

						if($s_keyword) {
							
							// 20170411 엑셀 복수검색 기능
							$s_keyword = trim($s_keyword);
							$temp_keyword = explode("\r\n", $s_keyword);
							$cnt = count($temp_keyword);
							
							$search_arr = array();
							for($i = 0 ; $i < $cnt ; $i++){
								array_push($search_arr, "'%".$temp_keyword[$i]."%'");
							}
							
							if($code_type=="name"){
								$qry.= "AND productname LIKE any ( array[".implode(",", $search_arr)."] ) ";
							} else if ($code_type=="code") {
								$qry.= "AND productcode LIKE any ( array[".implode(",", $search_arr)."] ) ";
							} else if($code_type=="erpcode") {
								$qry.= "AND prodcode || colorcode LIKE any ( array[".implode(",", $search_arr)."] ) ";
							}
							
							// 기존
// 							if($code_type=="name") $qry.= "AND lower( productname ) LIKE lower( '%{$s_keyword}%' ) ";
// 							else if($code_type=="code") $qry.= "AND lower( productcode ) LIKE lower( '%{$s_keyword}%' ) ";
//                          else if($code_type=="erpcode") $qry.= "AND lower( prodcode||colorcode ) LIKE lower( '%{$s_keyword}%' ) ";

							if($code_type=="self_goods_code") {
								$qry_form	= "( SELECT pd.* FROM (
								SELECT
								prd.productcode
								FROM tblproduct prd LEFT JOIN tblproduct_option opt ON prd.productcode=opt.productcode where lower(prd.self_goods_code) LIKE lower('%{$s_keyword}%') OR lower(opt.self_goods_code) LIKE lower('%{$s_keyword}%') group by prd.productcode ) as prdc LEFT JOIN tblproduct pd ON prdc.productcode=pd.productcode ) ";
							}

						}

						if($s_check==1)	{
                            //$qry.="AND quantity > 0 ";
                            $qry.="AND (a.quantity > 0 and a.soldout = 'N') ";
                        } else if($s_check==2){
							//$qry.=" AND quantity <= 0 ";
                            // 무제한 아니고, 품절 체크 아닌거, 또는 재고 0보다 작은거 ==> 품절로 검색 처리.
                            $qry.="AND ( (a.quantity < 999999999 and a.soldout = 'Y') OR (a.quantity <= 0) ) ";
						} 
						if($display_yn=="Y")	$qry.="AND a.display='Y' ";
						elseif($display_yn=="N")	$qry.="AND a.display='N' ";
                        elseif($display_yn=="R")	$qry.="AND a.display='R' ";

						if($hotdealyn=="Y") $qry.="AND a.hotdealyn='Y' ";
						if($dcpriceyn=="Y") $qry.="AND a.consumerprice > a.sellprice ";

						if($search_start && $search_end) $qry.="AND to_char(modifydate,'YYYYMMDD') between replace('{$search_start}','-','') AND replace('{$search_end}','-','') ";
						//if(!isnull($sellprice_min) && !isnull($sellprice_max)) $qry.="AND sellprice between '{$sellprice_min}' and '{$sellprice_max}' ";
                        //if($sellprice_min) $qry.="AND sellprice >= '{$sellprice_min}' ";
                        //if($sellprice_max) $qry.="AND sellprice <= '{$sellprice_max}' ";
                        if(!isnull($sellprice_min) > 0) $qry.="AND sellprice >= '{$sellprice_min}' ";
                        if(!isnull($sellprice_max) > 0) $qry.="AND sellprice <= '{$sellprice_max}' ";

                        if ($sel_season){
                       		$temp = explode (",", $sel_season);
                       		$season_year = $temp[0];
                       		$season = $temp[1];
                       		$qry.="AND a.season_year = '{$season_year}' AND season = '{$season}'";
                        }
                        
                        if ( $sel_vender ) {
                            $qry.="AND a.brand = '{$sel_vender}' ";
                        } elseif ( $s_brand_keyword ) {
                            $arrBrandIdx = array();

                            $tmp_search_keyword = strtolower($s_brand_keyword);
                            $subsql  = "SELECT bridx FROM tblproductbrand WHERE lower(brandname) like '%{$tmp_search_keyword}%' OR lower(brandname2) like '%{$tmp_search_keyword}%' ";
                            $subresult = pmysql_query($subsql);
                            while ( $subrow = pmysql_fetch_object($subresult) ) {
                                if ( $subrow->bridx != "" ) {
                                    array_push($arrBrandIdx, $subrow->bridx);
                                }
                            }
                            pmysql_free_result($subresult);

                            if ( count($arrBrandIdx) > 0 ) {
                                $qry.="AND a.brand in ( " . implode(",", $arrBrandIdx) . " ) ";
                            }
                        }

                        ## jhjeong 2015-06-11, 2016-08-22 중복카테고리도 검색할 수 있게..
						$sql = "Select *
                                From
                                (
                                    select distinct on (productcode,regdate, pridx) *
                                    from
                                    (
                                        SELECT	productcode,productname,sellprice,consumerprice,
                                                buyprice,quantity,reserve,reservetype,addcode,
                                                display,a.vender,c.brandname, minimage, date, modifydate, a.regdate, pridx, a.start_no, a.soldout,
                                                prodcode, colorcode, a.naver_display,
                                                a.daum_display, a,o2o_yn
                                        FROM	{$qry_form} a
                                        left join tblproductlink b on (a.productcode=b.c_productcode)
                                        left join tblproductbrand c on (a.brand = c.bridx)
                                        WHERE 1=1
                                        ".$qry."
                                    ) v
                                ) a
								";
						
                        ## jhjeong 2015-06-11
						/*
						# 유동혁 2016-02-02
						$sql = "
							SELECT	productcode,productname,sellprice,consumerprice,
									buyprice,quantity,reserve,reservetype,addcode,
									display,a.vender,c.brandname, minimage, date, modifydate, a.regdate, pridx,
                                    a.soldout
							FROM	{$qry_form} a
							left join tblproductlink b on (a.productcode=b.c_productcode AND b.c_maincate=1 )
							left join tblproductbrand c on (a.brand = c.bridx)
							WHERE 1=1
							".$qry."
						";
						*/
						
						//echo $sql;
						//exit();
						
						$sql0 = "SELECT COUNT(*) as t_count FROM (".$sql.") a  WHERE 1=1 ";

						// 엑셀용 쿼리를 저장한다.(2016.06.23 - 김재수 추가)
						$excel_sql = "
							SELECT a.*, b.*
							FROM	{$qry_form} a
							left join tblproductlink b on (a.productcode=b.c_productcode AND b.c_maincate=1 )
							left join tblproductbrand c on (a.brand = c.bridx)
							WHERE 1=1
							".$qry."
						";

						if(!$listnum){
							$listnum = 20;
						}
                        //echo "sql = ".$sql0."<br>";
						//echo $sql0;
						//exit();
						$paging = new newPaging($sql0,10,$listnum);
						$t_count = $paging->t_count;
						$gotopage = $paging->gotopage;
						

						// 2014 12 30 기준
						//2015 03 26 ORDER BY 수정 일괄등록이 있어서 등록시간이 중복되는게 있음
						//$sql.= "ORDER BY a.regdate DESC, a.pridx ASC ";
						//$sql.= " ORDER BY a.start_no asc, a.modifydate desc ";
                        //$sql.= " ORDER BY a.pridx desc ";
                        
						if($orderby == 'no asc'){
							$sql.=" ORDER BY a.start_no asc ";
							$excel_sql_orderby =" ORDER BY a.start_no asc ";
						} else if ($orderby == 'no desc') {
							$sql.=" ORDER BY a.start_no desc ";
							$excel_sql_orderby =" ORDER BY a.start_no desc ";
						} else if ($orderby == 'date asc') {
							$sql.=" ORDER BY a.regdate asc ";
							$excel_sql_orderby =" ORDER BY a.regdate asc ";
						} else if ($orderby == 'date desc') {
							$sql.=" ORDER BY a.regdate desc ";
							$excel_sql_orderby =" ORDER BY a.regdate desc ";
						} else if ($orderby == 'consumerprice asc') {
							$sql.=" ORDER BY consumerprice asc ";
							$excel_sql_orderby =" ORDER BY consumerprice asc ";
						} else if ($orderby == 'consumerprice desc') {
							$sql.=" ORDER BY consumerprice desc ";
							$excel_sql_orderby =" ORDER BY consumerprice desc";
						} else if ($orderby == 'sellprice asc') {
							$sql.=" ORDER BY sellprice asc ";
							$excel_sql_orderby =" ORDER BY sellprice asc ";
						} else if ($orderby == 'sellprice desc') {
							$sql.=" ORDER BY sellprice desc ";
							$excel_sql_orderby =" ORDER BY sellprice desc";
						} else if ($orderby == 'quantity asc') {
							$sql.=" ORDER BY quantity asc ";
							$excel_sql_orderby =" ORDER BY quantity asc ";
						} else if ($orderby == 'quantity desc') {
							$sql.=" ORDER BY quantity desc ";
							$excel_sql_orderby =" ORDER BY quantity desc";
						} else {
							$sql.=" ORDER BY a.start_no asc, a.modifydate desc ";
							$excel_sql_orderby =" ORDER BY a.start_no asc, a.modifydate desc "; // 엑셀용 쿼리를 저장한다.(2016.06.23 - 김재수 추가)
						}
						
//                         $sql.=" ORDER BY a.start_no asc, a.modifydate desc ";
//                         $excel_sql_orderby =" ORDER BY a.start_no asc, a.modifydate desc "; // 엑셀용 쿼리를 저장한다.(2016.06.23 - 김재수 추가)
                        
						$sql = $paging->getSql($sql);
							if($_SERVER[REMOTE_ADDR]=="218.234.32.103"){
 		exdebug($sql);
	}
// 						exdebug($sql);
// 						exit();
                        //echo "t_count = ".$t_count."<br>";
                        //echo "list_num = ".$setup['list_num']."<br>";
                        //echo "gotopage = ".$gotopage."<br>";
						$result = pmysql_query($sql,get_db_conn());
						$cnt=0;
						while($row=pmysql_fetch_object($result)) {
						$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
                        $min_img = getProductImage($imagepath, $row->minimage );

						?>

						<tr>
						<td><input type="checkbox" name="code_check"  value="<?=$row->productcode?>" class="code_check"></td>
						<td><?=$number?></td>

						<!--이미지-->
						<td>
                            <input type="hidden" name="product_code" value="<?=$row->productcode?>">
                            <input type="hidden" name="sabangnet_prop_val" value="<?=$row->sabangnet_prop_val?>">
                            <?	if (ord($row->minimage) && file_exists($imagepath.$row->minimage)){ ?>
                            <a href="/front/productdetail.php?productcode=<?=$row->productcode?>" target="_blank">
                            <img src="<?=$imagepath.$row->minimage."?v".date("His")?>" style="width:70px" border=1></a>
                            <?} else if(ord($row->minimage) && file_exists($Dir.$row->minimage)) { ?>
                            <a href="/front/productdetail.php?productcode=<?=$row->productcode?>" target="_blank">
                            <img src="<?=$Dir.$row->minimage."?v".date("His")?>" style="width:70px" border=1></a>
                            <?} else if($min_img) { ?>
                            <a href="/front/productdetail.php?productcode=<?=$row->productcode?>" target="_blank">
                            <img src="<?=$min_img."?v".date("His")?>" style="width:70px" border=1></a>
                            <?} else { ?>
                            <img src=images/space01.gif>
                            <?} ?>
						</td>
						<!--상품명-->
						<td height="50"><p class="ta_l" style="text-align:center">
                            <a href="javascript:ProductInfo('<?=$row->productcode?>','YES', '<?=$likecodeExchange?>');"> <!--YES를 NO로 바꾸면 팝업안됨-->
                            <?=$row->productname.($row->selfcode?"-".$row->selfcode:"")?><br><?=$row->productcode?> / <?=$row->prodcode?>-<?=$row->colorcode?></a></p>
                        </td>
						<!--품절여부-->
						<td><?=$row->soldout?></td>
						<!--등록일-->
						<td><?=substr($row->modifydate,0,10)?></td>
<?php
	/*그룹별 가격
	$group_price_sql = "SELECT b.group_name,a.sellprice,a.consumerprice
						FROM tblmembergroup_price a
						JOIN tblmembergroup b ON a.group_code=b.group_code
						WHERE a.productcode = '{$row->productcode}'
						ORDER BY b.group_level
						";
	$group_price_res = pmysql_query($group_price_sql,get_db_conn());
	while($group_price_row = pmysql_fetch_object($group_price_res)){
		$group_price[] = $group_price_row;
	}
	pmysql_free_result($group_price_res);
    */
?>

                        <!--벤더-->
						<!-- <td><?=$row->com_name?></td> -->
<?
                        echo "	<TD><B>".(ord($venderlist[$row->vender]->vender)?"<span onclick=\"viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->brandname}</span>":"-")."</B></td>\n";
?>
						<!--구매가-->
						<td><img src="images/won_icon.gif" border="0" style="margin-right:2px;"><?=number_format($row->buyprice)?></td>

						<!--소비자가(정가)-->
						<td style="text-align:right; padding-right:10px"><img src="images/won_icon.gif" border="0" style="margin-right:2px;">
						<span class="font_orange"><?=/*number_format(exchageRate($row->sellprice))*/number_format($row->consumerprice)?></span><br>

<!--<?php
	/*foreach($group_price as $v){
		echo substr($v->group_name,0,8).".. : ".number_format($v->consumerprice)."<br>";
	}*/
?>-->

						</td>
						<!--판매가-->
						<td style="text-align:right; padding-right:10px">
						<img src="images/won_icon.gif" border="0" style="margin-right:2px;">
						<span class="font_orange"><?=number_format($row->sellprice)?></span><br>
<!--<?php
	/*
	foreach($group_price as $v){
		echo number_format($v->sellprice)."<br>";
	}*/
?>-->
						</td>
						<!--마진율-->
						<!--<td><br>
<?php
	/*
	foreach($group_price as $v){
						echo number_format(100-($row->buyprice/$v->sellprice*100))."%"."<br>";
	}
		unset($group_price);
	*/
?>-

						</td>-->
						<TD>
						<?#if ($row->quantity=="0")
							#{ echo "품절";
						#}else
                        if($row->quantity=="999999999") {
							echo "무제한";
						}else echo $row->quantity;
						?>
						</td>
						<!--진열유무-->
						<td>
							<?=$row->display?>
						</td>
						<!--지식쇼핑 진열유무-->
						<td>
							<?=$row->naver_display?>
						</td>
						<!--쇼핑하우 진열유무-->
						<td>
							<?=$row->daum_display?>
						</td>
						<!--O2O 배송진열유무-->
						<td>
							<?=$row->o2o_yn?>
						</td>
						<!--복사-->
						<td>
							<a href="javascript:Productcopy('<?=$row->productcode?>','H')"><img src="img/btn/btn_cate_copy.gif" alt="제휴몰복사" /></a>
							<!-- <a href="javascript:Productcopy('<?=$row->productcode?>','N')"><img src="img/btn/btn_cate_copy2.gif" alt="자사몰복사" /></a> -->
						</td>
						<!--수정-->
						<!--삭제-->
						<td>
							<a href="javascript:ProductInfo('<?=$row->productcode?>','NO', '<?=$likecodeExchange?>');"><img src="img/btn/btn_cate_modify.gif" alt="수정" /></a>
							<a href="javascript:ProductDel('<?=$row->productcode?>')"><img src="img/btn/btn_cate_del01.gif" alt="삭제" /></a><br>
							<input onclick="reg_review('<?=$row->productcode?>');" type="button" value="리뷰등록" class="btn_type1">
						</td>
						</tr>
						<?
						$cnt++;
						}
						if ($cnt==0) {
							$colspan='16';
							$page_numberic_type="";
							echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
						}?>
				</table>
			</div>

			<!--하단 버튼-->
            <table cellSpacing=0 cellPadding=0 width="100%" border=0 style='padding-bottom:10px;'>
                <tr>
                    <td>
                        <a href="javascript:Chk_Display('Y')"><span class="btn-point h-small">상품진열ON</span></a>
                        <a href="javascript:Chk_Display('N')"><span class="btn-basic h-small">상품진열OFF</span></a>
                        <a href="javascript:Chk_Naver_Display('Y')"><span class="btn-point h-small">지식쇼핑 진열ON</span></a>
                        <a href="javascript:Chk_Naver_Display('N')"><span class="btn-basic h-small">지식쇼핑 진열OFF</span></a>
                        <a href="javascript:Chk_Daum_Display('Y');"><span class="btn-point h-small">쇼핑하우ON</span></a>
                        <a href="javascript:Chk_Daum_Display('N');"><span class="btn-basic h-small">쇼핑하우OFF</span></a>
                        <a href="javascript:Chk_O2O_Yn('Y');"><span class="btn-point h-small">O2O 배송ON</span></a>
                        <a href="javascript:Chk_O2O_Yn('N');"><span class="btn-basic h-small">O2O 배송OFF</span></a>
                    </td>
                    <td width=500 align=right>
                        <a href="javascript:ProductCheckUpdate('name');"><span class="btn-point blk h-small">상품명 업데이트</span></a>
                        <a href="javascript:ProductCheckUpdate('img');"><span class="btn-point blk h-small">이미지 업데이트</span></a>
						<a href="javascript:allCategoryMach();"><span class="btn-point blk h-small">카테고리 매칭변경</span></a>
						<a href="javascript:Update_Product_Time('Y');"><span class="btn-point blk h-small">등록일 변경</span></a>
                    </td>
                </tr>
            </table>
            <table cellSpacing=0 cellPadding=0 width="100%" border=0>
                <tr>
                    <td width=450>
						<a href="javascript:ProductCheckExcel();"><span class="btn-point blk">선택 엑셀 다운로드</span></a>
                    </td>
					<td>
					<!--페이징-->
					<div id="page_navi01" style="height:'40px'">
						<div class="page_navi">
						<?if($page_numberic_type){?>
							<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
						<?}?>
						</div>
					</div>
					</td>
                    <td width=450 align=right>
                        <a href="javascript:registeradd();"><span class="btn-point">상품등록</span></a>
                        <a href="javascript:allDelete();"><span class="btn-basic">삭제하기</span></a>
                    </td>
                </tr>
			</table>


        	<table height="20"><tr><td> </td></tr></table>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<ul class="help_list">
						<li>상품에 대한 기간할인, 그룹관리, O2O배송여부, 가격수정이 필요한 경우 [상품 일괄관리 > 상품 일괄 간편수정]에서 일괄적으로 수정하실 수 있습니다. </li>
					</ul>
				</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</form>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<form name=form_register action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
<input type=hidden name=category_data>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=s_keyword value="<?=$s_keyword?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=display_yn value="<?=$display_yn?>">
<input type=hidden name=vip value="<?=$vip?>">
<input type=hidden name=staff value="<?=$staff?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=sellprice_min value="<?=$sellprice_min?>">
<input type=hidden name=sellprice_max value="<?=$sellprice_max?>">
<input type=hidden name=code_type value="<?=$code_type?>">
<input type=hidden name=code_area value="<?=$code_area?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=serial_value value="">
<input type=hidden name=action_page value="product_code_product.php">
</form>

<form name=form_modify action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=modcode>
<input type=hidden name=modsellprice>
<input type=hidden name=modquantity>
<input type=hidden name=moddisplay>

<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
<input type=hidden name=category_data>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=s_keyword value="<?=$s_keyword?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=display_yn value="<?=$display_yn?>">
<input type=hidden name=vip value="<?=$vip?>">
<input type=hidden name=staff value="<?=$staff?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=sellprice_min value="<?=$sellprice_min?>">
<input type=hidden name=sellprice_max value="<?=$sellprice_max?>">
<input type=hidden name=code_type value="<?=$code_type?>">
<input type=hidden name=code_area value="<?=$code_area?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=mode2 value="N">
</form>

<form name=downexcelform action="product_excel_sel_popup.php" method="post">
	<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
	<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
	<input type=hidden name="productcodes"/>
</form>

<?php if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?php }?>

<script>
    function resetBrandSearchWord(obj) {
        if ( $(obj).val() == "" ) {
            $("#s_brand_keyword").attr("disabled", false).val("").focus();
        } else {
            $("#s_brand_keyword").attr("disabled", true);
        }
    }
    function orderby (obj){
		if($('#orderby').val() ==  obj+' desc'){
			$('#orderby').val(obj+' asc');
		} else {
			$('#orderby').val(obj+' desc');
		}

	    document.form1.submit();
    }
</script>
<?php
include("copyright.php");
?>
<?=$onload?>
