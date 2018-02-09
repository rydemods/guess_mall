<?php
$back_chk=$_REQUEST["back2"];
if($back_chk==1){
	header("Location: {$_SERVER['HTTP_REFERER']}");
	exit;
}

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once dirname(__FILE__)."/../lib/product.class.php";
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다
include_once($Dir."lib/paging_ajax.php");

# 시중가 대비 할인가 % 2016-02-02 유동혁
function get_price_percent( $consumerprice, $sellprice ){
	$per = round( ( ( $consumerprice - $sellprice ) / $consumerprice ) * 100 );
	return $per;
}


// 쿠폰의 할인 / 적립 text를 반환
function CouponText( $sale_type ){

	$text_arr = array(
		'text'=>'',
		'won'=>''
	);

	switch( $sale_type ){
		case '1' :
			$text_arr['text'] = '적립';
			$text_arr['won'] = '%';
			break;
		case '2' :
			$text_arr['text'] = '할인';
			$text_arr['won'] = '%';
			break;
		case '3' :
			$text_arr['text'] = '적립';
			$text_arr['won'] = '원';
			break;
		case '4' :
			$text_arr['text'] = '할인';
			$text_arr['won'] = '원';
			break;
		default :
			break;
	} //switch

	return $text_arr;
}

// popup일 경우 (2016-03-01 김재수 추가)
$popup=$_REQUEST["popup"];

$mode=$_REQUEST["mode"];
$coupon_code=$_REQUEST["coupon_code"];
$code=$_REQUEST["code"];
$prod_cate_code = $code;
$productcode=$_REQUEST["productcode"];
$link_url   = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$instaimgpath = $Dir.DataDir."shopimages/instagram/";
$productimgpath = $Dir.DataDir."shopimages/product/";

##### VIP 상품일 경우 회원등급 체크
$sql_prd_vip = "SELECT vip_product, staff_product FROM tblproduct WHERE productcode = '{$productcode}' ";
list($prd_vip_type, $staff_product) = pmysql_fetch(pmysql_query($sql_prd_vip));

if($prd_vip_type && ($member_group_level < $vip_limit_level)){
	alert_go("해당상품은 VIP 전용 상품 입니다.","/");
}
##### //VIP 상품일 경우

if(!$_ShopInfo->getStaffType() && $staff_product){
	alert_go("해당상품은 STAFF 전용 상품 입니다.","/");
}

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

//$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
//$selfcodefont_end = "</font>"; //진열코드 폰트 끝
$_cdata="";
$_pdata="";

if( strlen($productcode) > 0 ) {

	//ERP 상품을 쇼핑몰에 업데이트한다.
	getUpErpProductUpdate($productcode);

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
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		if($row->c_maincate == 1){
			$mainCate = $row;
		}
		$cateProduct[] = $row;
	}

	if($cateProduct) {
		if($mainCate) $_cdata=$mainCate;
		else $_cdata=$cateProduct[0];
		if(count($cateProduct)==0 || !$cateProduct){
			$group_sql = "
				SELECT
				a.group_code
				FROM tblproductcode a
				,tblproductlink b
				WHERE a.code_a||a.code_b||a.code_c||a.code_d = b.c_category
				AND group_code != ''
				AND c_productcode = '{$productcode}'
				GROUP BY a.group_code
			";
			$gruop_res = pmysql_query($group_sql,get_db_conn());
			while($gruop_row = pmysql_fetch_object($gruop_res)){
				if($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
					Header("Location:/");
					exit;
				}else if(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
					alert_go('해당 분류의 접근 권한이 없습니다.',-1);
				}
			}
			alert_go('판매가 종료된 상품입니다.',"/");
		}

		//Wishlist 담기
		if($mode=="wishlist") {
			if(strlen($_ShopInfo->getMemid())==0) {	//비회원
				alert_go('로그인을 하셔야 본 서비스를 이용하실 수 있습니다.',$Dir.FrontDir."login.php?chUrl=".getUrl());
			}
			$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
			$result2=pmysql_query($sql,get_db_conn());
			$row2=pmysql_fetch_object($result2);
			$totcnt=$row2->totcnt;
			pmysql_free_result($result2);
			$maxcnt=20;
			if($totcnt>=$maxcnt) {
				$sql = "SELECT b.productcode ";
				$sql.= "FROM tblwishlist a, view_tblproduct b ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
				$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
				$sql.= "AND b.display='Y' ";
				$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
				$sql.= "GROUP BY b.productcode ";

				$result2=pmysql_query($sql,get_db_conn());
				$i=0;
				$wishprcode="";
				while($row2=pmysql_fetch_object($result2)) {
					$wishprcode.="'{$row2->productcode}',";
					$i++;
				}
				pmysql_free_result($result2);
				$totcnt=$i;
				$wishprcode=substr($wishprcode,0,-1);
				if(ord($wishprcode)) {
					$sql = "DELETE FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode NOT IN ({$wishprcode}) ";
					pmysql_query($sql,get_db_conn());
				}
			}
			if($totcnt<$maxcnt) {
				$sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode='{$productcode}' ";
				$result2=pmysql_query($sql,get_db_conn());
				$row2=pmysql_fetch_object($result2);
				$cnt=$row2->cnt;
				pmysql_free_result($result2);
				if($cnt>0) {
					alert_go('WishList에 이미 등록된 상품입니다.',-1);
				} else {
					$sql = "INSERT INTO tblwishlist (
					id			,
					productcode	,
					date		) VALUES (
					'".$_ShopInfo->getMemid()."',
					'{$productcode}',
					'".date("YmdHis")."')";
					pmysql_query($sql,get_db_conn());
					alert_go('WishList에 해당 상품을 등록하였습니다.',-1);
				}
			} else {
				alert_go("WishList에는 {$maxcnt}개 까지만 등록이 가능합니다.\\n\\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다.",-1);
			}
		}
	} else {
		alert_go('해당 분류가 존재하지 않습니다.',"/");
	}
	pmysql_free_result($result);

	$sql = "SELECT * ";
	$sql.= "FROM tblproduct ";
	$sql.= "WHERE productcode='{$productcode}' ";
	if(!$_ShopInfo->getId()) {
		$sql.= "AND display='Y' ";
	}
	$result=pmysql_query($sql,get_db_conn());

	if($row=pmysql_fetch_object($result)) {
		$_pdata=$row;
		$_pdata->brand += 0;
		$sql = "SELECT * FROM tblproductbrand ";
		$sql.= "WHERE bridx='{$_pdata->brand}' ";
		$bresult=pmysql_query($sql,get_db_conn());
		$brow=pmysql_fetch_object($bresult);
		$_pdata->brandcode = $_pdata->brand;
		$_pdata->brand = $brow->brandname;
		$_pdata->staff_rate = $brow->staff_rate;

		pmysql_free_result($result);

		if($_pdata->assembleuse=="Y") {
			$sql = "SELECT * FROM tblassembleproduct ";
			$sql.= "WHERE productcode='{$productcode}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=@pmysql_fetch_object($result)) {
				$_adata=$row;
				pmysql_free_result($result);
				$assemble_list_pridx = str_replace("","",$_adata->assemble_list);

				if(ord($assemble_list_pridx)) {
					$sql = "SELECT pridx,productcode,productname,sellprice,quantity,tinyimage FROM tblproduct ";
					$sql.= "WHERE pridx IN ('".str_replace(",","','",trim($assemble_list_pridx,','))."') ";
					$sql.= "AND assembleuse!='Y' ";
					$sql.= "AND display='Y' ";
					$result=pmysql_query($sql,get_db_conn());
					while($row=@pmysql_fetch_object($result)) {
						$_acdata[$row->pridx] = $row;
					}
					pmysql_free_result($result);
				}
			}
		}
	} else {
		alert_go('해당 상품 정보가 존재하지 않습니다.',-1);
	}

} else {
// 	alert_go('해당 상품 정보가 존재하지 않습니다.',"/");
}
# 상품상세 뷰 카운트 update 2016-01-26 유동혁
$vcnt_sql = "UPDATE tblproduct SET vcnt = vcnt + 1 WHERE productcode = '".$productcode."'";
pmysql_query( $vcnt_sql, get_db_conn() );

# 쿠폰 다운로드 최근 날짜 1장 노출

$couponDownLoadFlag = false;
$goods_sale_type = "";
$goods_sale_money = "";
$goods_amount_floor = "";
$goods_sale_max_money = "";
if($_data->coupon_ok=="Y") {
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

	$sql = "SELECT a.* FROM tblcouponinfo a ";
	$sql .= "LEFT JOIN tblcouponproduct c on a.coupon_code=c.coupon_code ";
	$sql .= "LEFT JOIN tblcouponcategory d on a.coupon_code=d.coupon_code ";
	if($_pdata->vender>0) {
		$sql .= "WHERE (a.vender='0' OR a.vender='{$_pdata->vender}') ";
	} else {
		$sql .= "WHERE a.vender='0' ";
	}
	$sql .= "AND a.display='Y' AND a.issue_type='Y' AND a.detail_auto='Y' AND a.coupon_type='1' ";
	$sql .= "AND a.date_start<='".date("YmdH")."' AND (a.date_end>'".date("YmdH")."' OR a.date_end='') ";
	$sql .= "AND ((a.use_con_type2='Y' AND a.productcode = 'ALL') OR ((a.use_con_type2='Y' AND a.productcode != 'ALL') AND (c.productcode = '".$productcode."' OR (d.categorycode IN ".$addCategoryQuery." AND a.use_con_type2 = 'Y')))) ";
	$sql .= "AND mod(sale_type::int , 2) = '0' ";
	$sql .= "AND (mini_price = 0 OR mini_price < '".$_pdata->sellprice."') ";
	$sql .= "ORDER BY date DESC ";
	$sql .= "LIMIT 1 OFFSET 0";

	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$goods_sale_type = $row->sale_type;
		$goods_sale_money = $row->sale_money;
		$goods_amount_floor = $row->amount_floor;
		$goods_sale_max_money = $row->sale_max_money;
		$goods_coupon_code = $row->coupon_code;

		$couponDownLoadFlag = true;
	}
	pmysql_free_result($result);
}

$ref=$_REQUEST["ref"];
if (ord($ref)==0) {
	$ref=strtolower(str_replace("http://","",$_SERVER["HTTP_REFERER"]));
	if(strpos($ref,"/") != false) $ref=substr($ref,0,strpos($ref,"/"));
}

if(ord($ref) && strlen($_ShopInfo->getRefurl())==0) {
	$sql2="SELECT * FROM tblpartner WHERE url LIKE '%{$ref}%' ";
	$result2 = pmysql_query($sql2,get_db_conn());
	if ($row2=pmysql_fetch_object($result2)) {
		pmysql_query("UPDATE tblpartner SET hit_cnt = hit_cnt+1 WHERE url = '{$row2->url}'",get_db_conn());
		$_ShopInfo->setRefurl($row2->id);
		$_ShopInfo->Save();
	}
	pmysql_free_result($result2);
}

/*
//if(strlen($productcode)==18) {
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
//}
*/
// 최근 본 상품 DB에 저장(hott 는 로긴기반이라 저장하기로 했음.) 2016-08-04 jhjeong
if(strlen($_ShopInfo->getMemid()) > 0) {

	$sql = "Select count(*) as cnt From tblproduct_recent Where productcode = '".$productcode."' and mem_id = '".$_ShopInfo->getMemid()."' ";
	list($cnt_recent) = pmysql_fetch($sql, get_db_conn());

	$current_date = date("YmdHis");
	if($cnt_recent == 0) {
		$sql = "Insert into tblproduct_recent 
                (productcode, mem_id, regdt) 
                Values 
                ('".$productcode."', '".$_ShopInfo->getMemid()."', '".$current_date."') 
                ";
		pmysql_query($sql, get_db_conn());
	} else {
		$sql = "Update tblproduct_recent Set regdt = '".$current_date."' Where productcode = '".$productcode."' and mem_id = '".$_ShopInfo->getMemid()."' ";
		pmysql_query($sql, get_db_conn());
	}

	// 30개 넘으면 삭제..
	$rno_arr = Get_Over_Recent_Product($_ShopInfo->getMemid(), 30);
	//exdebug($rno_arr);
	if(count($rno_arr) > 0) {

		$rno_in = array();
		foreach($rno_arr as $k => $v) {
			//exdebug($v->rno);
			$rno_in[] = $v->rno;
		}

		$sql = "Delete from tblproduct_recent Where rno in (".implode($rno_in, ",").") ";
		pmysql_query($sql, get_db_conn());
		//exdebug($sql);
	}
}

//상품단어 필터링
if(ord($_data->filter)) {
	$arr_filter=explode("#",$_data->filter);
	$detail_filter=$arr_filter[0];
	$filters=explode("=",$detail_filter);
	$filtercnt=count($filters)/2;

	for($i=0;$i<$filtercnt;$i++){
		$filterpattern[$i]="/".str_replace("\0","\\0",preg_quote($filters[$i*2]))."/";
		$filterreplace[$i]=$filters[$i*2+1];
		if(ord($filterreplace[$i])==0) $filterreplace[$i]="***";
	}

	$review_filter_array=explode("REVIEWROW",$arr_filter[1]);
	$review_filter=$review_filter_array[0];
}

//상품다중이미지 확인
$multi_img="N";
$sql2 ="SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
$result2=pmysql_query($sql2,get_db_conn());
if($row2=pmysql_fetch_object($result2)) {
	if($_data->multi_distype=="0") {
		$multi_img="I";
	} else if($_data->multi_distype=="1") {
		$multi_img="Y";
		$multi_imgs=array(&$row2->primg01,&$row2->primg02,&$row2->primg03,&$row2->primg04,&$row2->primg05,&$row2->primg06,&$row2->primg07,&$row2->primg08,&$row2->primg09,&$row2->primg10);
		$thumbcnt=0;
		for($j=0;$j<10;$j++) {
			if(ord($multi_imgs[$j])) {
				$thumbcnt++;
			}
		}
		$multi_height=430;
		$thumbtype=1;
		if($thumbcnt>5) {
			$multi_height=490;
			$thumbtype=2;
		}
	}
}
pmysql_free_result($result2);
//exdebug($multi_imgs);
//상품 상세정보 노출정보
if(ord($_data->exposed_list)==0) {
	$_data->exposed_list=",0,2,3,4,5,6,7,19,";
}
$arexcel = explode(",",substr($_data->exposed_list,1,-1));

$prcnt = count($arexcel);
$arproduct=array(&$prproduction,&$prmadein,&$prconsumerprice,&$prsellprice,&$prreserve,&$praddcode,&$prquantity,&$proption,&$prproductname,&$prdollarprice,&$prmodel,&$propendate,&$pruserspec0,&$pruserspec1,&$pruserspec2,&$pruserspec3,&$pruserspec4,&$prbrand,&$prselfcode,&$prpackage);
$ardollar=explode(",",$_data->ETCTYPE["DOLLAR"]);

if(ord($ardollar[1])==0 || $ardollar[1]<=0) $ardollar[1]=1;

if(preg_match("/^\[OPTG\d{4}\]$/",$_pdata->option1)){
	$optcode = substr($_pdata->option1,5,4);
	$_pdata->option1="";
	$_pdata->option_price="";
}

$miniq = 1;
if (ord($_pdata->etctype)) {
	$etctemp = explode("",$_pdata->etctype);
	for ($i=0;$i<count($etctemp);$i++) {
		if (strpos($etctemp[$i],"MINIQ=")===0)			$miniq=substr($etctemp[$i],6);
		if (strpos($etctemp[$i],"DELIINFONO=")===0)	$deliinfono=substr($etctemp[$i],11);
	}
}
//입점업체 정보 관련
if($_pdata->vender>0) {
	$sql = "SELECT a.vender, a.id, a.brand_name, a.deli_info, b.prdt_cnt ";
	$sql.= "FROM tblvenderstore a, tblvenderstorecount b ";
	$sql.= "WHERE a.vender='{$_pdata->vender}' AND a.vender=b.vender ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_vdata=pmysql_fetch_object($result)) {
		$_pdata->vender=0;
	}
	pmysql_free_result($result);
}

//배송/교환/환불정보 노출
$deli_info="";
if($deliinfono!="Y") {	//개별상품별 배송/교환/환불정보 노출일 경우
	$deli_info_data="";
	if($_pdata->vender>0) {	//입점업체 상품이면 입점업체 배송/교환/환불정보 누출
		$tempvdeli_info=explode("=", stripslashes($_vdata->deli_info));
		if ($_vdata->deli_info && $tempvdeli_info[0]=="Y") {
			$deli_info_data=$_vdata->deli_info;
			$aboutdeliinfofile=$Dir.DataDir."shopimages/vender/aboutdeliinfo_{$_vdata->vender}.gif";
		} else {
			$deli_info_data=$_data->deli_info;
			$aboutdeliinfofile=$Dir.DataDir."shopimages/etc/aboutdeliinfo.gif";
		}
	} else {
		$deli_info_data=$_data->deli_info;
		$aboutdeliinfofile=$Dir.DataDir."shopimages/etc/aboutdeliinfo.gif";
	}
	if(ord($deli_info_data)) {
		$tempdeli_info=explode("=", stripslashes($deli_info_data));
		if($tempdeli_info[0]=="Y") {
			if($tempdeli_info[1]=="TEXT") {			//텍스트형
				$allowedTags = "<h1><b><i><a><ul><li><pre><hr><blockquote><u><img><br><font>";

				if(ord($tempdeli_info[2]) || ord($tempdeli_info[3])) {
					if(ord($tempdeli_info[2])) {	//배송정보 텍스트
						$deli_info.= "	<dl class='delivery_info'><dd>".nl2br(strip_tags($tempdeli_info[2],$allowedTags))."</dd></dl>\n";
					}
					if(ord($tempdeli_info[3])) {	//교환/환불정보 텍스트
						$deli_info.= "	<dl class='delivery_info'><dd>".nl2br(strip_tags($tempdeli_info[3],$allowedTags))."</dd></dl>\n";
					}
				}
			} else if($tempdeli_info[1]=="IMAGE") {	//이미지형
				if(file_exists($aboutdeliinfofile)) {
					$deli_info = "<img src=\"{$aboutdeliinfofile}\" align=absmiddle border=0>\n";
				}
			} else if($tempdeli_info[1]=="HTML") {	//HTML로 입력
				if(ord($tempdeli_info[2])) {
					$deli_info = "{$tempdeli_info[2]}\n";
				}
			}
		}
	}
}

//리뷰관련 환경 설정
$reviewlist=$_data->ETCTYPE["REVIEWLIST"];
$reviewdate=$_data->ETCTYPE["REVIEWDATE"];
if(ord($reviewlist)==0) $reviewlist="N";

//상품QNA 게시판 존재여부 확인 및 설정정보 확인
$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
if(ord($prqnaboard)) {
	$sql = "SELECT * FROM tblboardadmin WHERE board='{$prqnaboard}' ";
	$result=pmysql_query($sql,get_db_conn());
	$qnasetup=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($qnasetup->use_hidden=="Y") $qnasetup=null;
}

//멀티 이미지 관련()2013-12-23 멀티 이미지 기능만 추가함. 확대보기 없음.

if($multi_img=="Y") {

	//$imagepath=$Dir.DataDir."shopimages/multi/";
	$imagepath=$Dir.DataDir."shopimages/product/";
	//$dispos=$row->multi_dispos;
	// 멀티이미지 설정
	$changetype=$_data->multi_changetype;
	$bgcolor=$_data->multi_bgcolor;

	$sql = "SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
	//exdebug($sql);
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$multi_imgs = array(
			&$row->primg01,
			&$row->primg02,
			&$row->primg03,
			&$row->primg04,
			&$row->primg05,
			&$row->primg06,
			&$row->primg07,
			&$row->primg08,
			&$row->primg09,
			&$row->primg10
		);

		$tmpsize=explode("",$row->size);
		$insize="";
		$updategbn="N";

		$y=0;
		for($i=0;$i<10;$i++) {
			if(ord($multi_imgs[$i])) {
				$yesimage[$y]=$multi_imgs[$i];
				if(ord($tmpsize[$i])==0) {
					if ( strpos("http://", $multi_imgs[$i]) === false ) {
						$size=getimagesize($Dir.DataDir."shopimages/product/".$multi_imgs[$i]);
					}
					$xsize[$y]=$size[0];
					$ysize[$y]=$size[1];
					$insize.="{$size[0]}X".$size[1];
					$updategbn="Y";
				} else {
					$insize.="".$tmpsize[$i];
					$tmp=explode("X",$tmpsize[$i]);
					$xsize[$y]=$tmp[0];
					$ysize[$y]=$tmp[1];
				}
				$y++;
			} else {
				$insize.="";
			}
		}

		$makesize=$maxsize;
		for($i=0;$i<$y;$i++){
			if($xsize[$i]>$makesize || $ysize[$i]>$makesize) {
				if($xsize[$i]>=$ysize[$i]) {
					$tempxsize=$makesize;
					$tempysize=($ysize[$i]*$makesize)/$xsize[$i];
				} else {
					$tempxsize=($xsize[$i]*$makesize)/$ysize[$i];
					$tempysize=$makesize;
				}
				$xsize[$i]=$tempxsize;
				$ysize[$i]=$tempysize;
			}
		}
		if($updategbn=="Y"){
			$sql = "UPDATE tblmultiimages SET size='".ltrim($insize,'')."' ";
			$sql.= "WHERE productcode='{$productcode}'";
			pmysql_query($sql,get_db_conn());
		}

		pmysql_free_result($result);
	}
}

# templet 상품 금액설정 위치 변경 2015 11 04 유동혁

$product = new PRODUCT();

$dc_data = $product->getProductDcRate($productcode);

$option1Arr;$option2Arr;

# 상품 이미지 path
$imagepath_product = $Dir.DataDir.'shopimages/product/';
$imagepath_multi = $Dir.DataDir.'shopimages/product/';
$imgPath = 'http://'.$_SERVER['HTTP_HOST'].'/data/shopimages/product/';
if(strpos($_pdata->maximage, "http://") === false) {
	$width= GetImageSize( $imagepath_product.$_pdata->maximage );
}

# 해당 유저에 맞는 상품 메뉴를 가져옴
$cateSql = "
	SELECT code_a||code_b||code_c||code_d AS prcode, code_name
	FROM tblproductcode 
	WHERE type = 'LMX'
	AND code_a = '".substr($_cdata->c_category, 0, 3)."'
	AND code_b = '".substr($_cdata->c_category, 3, 3)."'
	ORDER BY cate_sort ASC
";
$cateRes = pmysql_query( $cateSql, get_db_conn() );
while( $cateRow = pmysql_fetch_array( $cateRes ) ){
	$cateLoc[] = $cateRow;
}
pmysql_free_result( $cateRes );
//$thisCate = getCodeLoc3( $_cdata->c_category );
$thisCate = getDecoCodeLoc( $_pdata->productcode, $prod_cate_code );
//옵션정보를 가져온다
if( $_pdata->option_type == '0' ){
	$options = get_option( $_pdata->productcode );
} else if( $_pdata->option_type == '1' ){
	$options = get_alone_option( $_pdata->productcode );
} else {
	$options = array();
}
$optionNames = explode( '@#', $_pdata->option1 );
$option_depth = count( $optionNames );

$addOptionNames = explode( '@#', $_pdata->option2 );
$addOption_tf = explode( '@#', $_pdata->option2_tf );
$addOption_maxlen = explode( '@#', $_pdata->option2_maxlen );

#관련 태그
$r_sql =  " select productcode from tblproduct_related where productcode='{$productcode}' ";
list($chk_rproduct) = pmysql_fetch($r_sql);
if($chk_rproduct){//수동 등록된 연관상품을 노출시킵니다. 수동노출 연관상품의 개수가 10개가 안되면, 자동 노출상품을 더해서 10개를 출력합니다 ㅠㅠ
	//$related_sql = "WITH related AS ( SELECT c_productcode  FROM tblproductlink  WHERE c_category like '". substr($_cdata->c_category, 0, 9) ."%' ";
	//$related_sql.= " AND c_maincate = 1 GROUP BY c_productcode ) ";
	$related_sql.= "SELECT pr.productcode, pr.productname, pr.sellprice, ";
	$related_sql.= "pr.consumerprice, pr.buyprice, pr.brand, pr.maximage, ";
	$related_sql.= "pr.minimage, pr.tinyimage, pr.mdcomment, pr.review_cnt, ";
	$related_sql.= "pr.icon, pr.soldout, pr.quantity, pr.over_minimage, pr.relation_tag FROM tblproduct pr ";
	$related_sql.= "LEFT JOIN (select r_productcode,sort from tblproduct_related where productcode='{$productcode}' ) r ON pr.productcode = r.r_productcode ";
	$related_sql.= "LEFT JOIN (SELECT c_productcode  FROM tblproductlink  WHERE c_category like '". substr($_cdata->c_category, 0, 9) ."%' AND c_maincate = 1 GROUP BY c_productcode ) r2 ON pr.productcode = r2.c_productcode ";
	$related_sql.= "WHERE pr.productcode <> '{$productcode}' "; // 현재 자신은 제외
	$related_sql.= "AND pr.display = 'Y' ";

	// ================================================================
	// 승인대기중인 브랜드에 속한 상품은 리스트에서 제외처리
	// ================================================================
	$sub_sql = "SELECT b.bridx FROM tblvenderinfo a JOIN tblproductbrand b ON a.vender = b.vender WHERE a.delflag='N' AND a.disabled='1' ";
	$sub_result = pmysql_query($sub_sql);

	$arrNotAllowedBrandList = array();
	while ( $sub_row = pmysql_fetch_object($sub_result) ) {
		array_push($arrNotAllowedBrandList, $sub_row->bridx);
	}
	pmysql_free_result($sub_result);

	if ( count($arrNotAllowedBrandList) >= 1 ) {
		$related_sql .= "AND pr.brand not in ( " . implode(",", $arrNotAllowedBrandList) . " ) ";
	}
	$related_sql .= " order by r.sort asc limit 10 ";
	//exdebug($related_sql);
}else{
	//상품의 조회순 , 등록날짜로 10개
	/*
	$related_sql = "WITH related AS ( SELECT c_productcode  FROM tblproductlink  WHERE c_category like '". substr($_cdata->c_category, 0, 9) ."%' ";
	$related_sql.= " AND c_maincate = 1 GROUP BY c_productcode ) ";
	$related_sql.= "SELECT pr.productcode, pr.productname, pr.sellprice, ";
	$related_sql.= "pr.consumerprice, pr.buyprice, pr.brand, pr.maximage, ";
	$related_sql.= "pr.minimage, pr.tinyimage, pr.mdcomment, pr.review_cnt, ";
	$related_sql.= "pr.icon, pr.soldout, pr.quantity, pr.over_minimage FROM tblproduct pr ";
	$related_sql.= "JOIN related r ON pr.productcode = r.c_productcode ";
	$related_sql.= "WHERE pr.productcode <> '{$productcode}' "; // 현재 자신은 제외
	$related_sql.= "AND pr.display = 'Y' ";
	*/

	// ================================================================
	// 수정날짜 : 2016-08-12
	// 수정자 : daeyeob(김대엽)
	// 수정내용 : 추가 된 관련상품 Tag(relation_tag) 필드에서 관련상품을 가져온다.
	// ================================================================

	$prod_sql = "SELECT p.productcode, p.relation_tag FROM tblproduct p 
						WHERE p.productcode = '{$productcode}'";
	$result = pmysql_query($prod_sql);
	$row = pmysql_fetch_object($result);
	$relation_tag = $row->relation_tag;
	$arr_relation = explode(",", $relation_tag);

	foreach( $arr_relation as $key=> $val ){
		if($key == 0){
			$or =  "AND (pr.keyword like '%".$val."%'" ;
		}else{
			$or .=  " OR pr.keyword like '%".$val."%'" ;
		}
	}
	$or .= ")";

	$related_sql = "WITH related AS ( SELECT c_productcode  FROM tblproductlink  WHERE c_category like '". substr($_cdata->c_category, 0, 9) ."%' ";
	$related_sql.= " AND c_maincate = 1 GROUP BY c_productcode ) ";
	$related_sql.= "SELECT pr.productcode, pr.productname, pr.sellprice, ";
	$related_sql.= "pr.consumerprice, pr.buyprice, pr.brand, pr.maximage, ";
	$related_sql.= "pr.minimage, pr.tinyimage, pr.mdcomment, pr.review_cnt, ";
	$related_sql.= "pr.icon, pr.soldout, pr.quantity, pr.over_minimage, pr.relation_tag FROM tblproduct pr ";
//	 $related_sql.= "JOIN related r ON pr.productcode = r.c_productcode ";
	$related_sql.= "WHERE pr.productcode <> '{$productcode}' "; // 현재 상품은 제외
	$related_sql.= "AND pr.display = 'Y' ";

	// ================================================================
	// 승인대기중인 브랜드에 속한 상품은 리스트에서 제외처리
	// ================================================================
	$sub_sql = "SELECT b.bridx FROM tblvenderinfo a JOIN tblproductbrand b ON a.vender = b.vender WHERE a.delflag='N' AND a.disabled='1' ";
	$sub_result = pmysql_query($sub_sql);

	$arrNotAllowedBrandList = array();
	while ( $sub_row = pmysql_fetch_object($sub_result) ) {
		array_push($arrNotAllowedBrandList, $sub_row->bridx);
	}
	pmysql_free_result($sub_result);

	if ( count($arrNotAllowedBrandList) >= 1 ) {
		$related_sql .= "AND pr.brand not in ( " . implode(",", $arrNotAllowedBrandList) . " ) ";
	}
	$related_sql .= $or;

	$related_sql.= " OFFSET (random()) LIMIT 4 ";

}
$related_html = productlist_print( $related_sql, 'W_009' );

#상품 TAG  정보
$tag_sql = "SELECT p.productcode, p.keyword FROM tblproduct p";
$tag_sql .= " WHERE p.productcode = '{$productcode}' AND p.display = 'Y'";
$result = pmysql_query($tag_sql);
$tag_row = pmysql_fetch_object($result);
$tag = $tag_row->keyword;
$arrTag = explode(",", $tag);


#상품정보고시
// 2016 01 13 유동혁
$jungbo_option = explode( '||', $_pdata->sabangnet_prop_option );
$jungbo_val = explode( '||', $_pdata->sabangnet_prop_val );
//$jungbo_cnt = strlen( str_replace( '||', '',$_pdata->sabangnet_prop_val ) );
//정보고시 내용 없으면 노출안되도록 (앞에 3자리 코드 자르고 || 구분자로 배열변경) 2016-03-07
$jungbo_arr = explode("||",substr($_pdata->sabangnet_prop_val,'3'));
$jungbo_cnt=0;
//정보고시 내용이 빈값인지 체크 2016-03-07
foreach($jungbo_arr as $jk){
	if($jk) $jungbo_cnt++;
}
$jungbo_title = $jungbo_code[$jungbo_option[0]]['title'];

#상품의 메인 브랜드 정보
$brand_sql   = "SELECT bridx FROM tblbrandproduct WHERE productcode = '".$_pdata->productcode."' ORDER BY sort ASC LIMIT 1";
list($brand_code) = pmysql_fetch($brand_sql);

$brand_name = "";
if ( !empty($brand_code) ) {
	$brand_sql = " SELECT bridx, brandname, vender FROM tblproductbrand WHERE bridx = ";
	$brand_sql.= "( SELECT bridx FROM tblbrandproduct WHERE productcode = '".$_pdata->productcode."' ORDER BY sort ASC LIMIT 1 )";
	$brand_res = pmysql_query( $brand_sql, get_db_conn() );
	$brand_row = pmysql_fetch_object( $brand_res );
	$brand_code = $brand_row->bridx;
	//$brand_vender = $brand_row->vender;
	$brand_name = $brand_row->brandname;
	pmysql_free_result( $brand_res );
}

// ======================================================================================
// 브랜드 정보 조회
// ======================================================================================

$brand_desc = "";
if ( !empty($brand_code) ) {
	$sql  = "SELECT a.*, b.brandname ";
	$sql .= "FROM tblvenderinfo_add a LEFT JOIN tblproductbrand b ON a.vender = b.vender ";
	$sql .= "WHERE a.vender = '".$_pdata->vender."' ";
	$row  = pmysql_fetch_object(pmysql_query($sql));

	$brand_desc = $row->description;
}

// 롤링할 이미지
$arrRollingBannerImg = array();
for ( $i = 1; $i <= 10; $i++ ) {
	$varName = "b_img" . $i;

	if ( !empty($row->$varName) ) {
		array_push($arrRollingBannerImg, $row->$varName);
	}
}

// ======================================================================================
// 찜한 리스트(로그인한 상태인 경우)
// ======================================================================================
$arrBrandWishList = array();
$onBrandWishClass = "";
if (strlen($_ShopInfo->getMemid()) > 0) {
	$sql  = "SELECT a.bridx, b.brandname ";
	$sql .= "FROM tblbrandwishlist a LEFT JOIN tblproductbrand b ON a.bridx = b.bridx ";
	$sql .= "WHERE id = '" . $_ShopInfo->getMemid() . "' ";
	$sql .= "ORDER BY wish_idx desc ";

	$result = pmysql_query($sql);
	while ($row = pmysql_fetch_array($result)) {
		$arrBrandWishList[$row['bridx']] = $row['brandname'];

		// 내가 찜한 브랜드인 경우
		if ( $row['bridx'] == $brand_code ) {
			$onBrandWishClass = "on";
		}
	}
}

// ======================================================================================
// 관련 프로모션 정보
// ======================================================================================

$sql  = "SELECT idx, title, bridx_list ";
$sql .= "FROM tblpromo ";
$sql .= "WHERE display_type in ('A', 'P') and hidden = '1' AND current_date <= end_date ";
$sql .= "ORDER BY rdate desc ";

$result = pmysql_query($sql);

$bLoopBreak = false;
$limitCount = 2;
$arrPromotionIdx = array();
$arrPromotionTitle = array();
while ($row = pmysql_fetch_array($result)) {
	$promo_idx          = $row['idx'];
	$promo_title        = $row['title'];
	$promo_bridx_list   = $row['bridx_list'];

	$sub_sql  = "SELECT a.special_list ";
	$sub_sql .= "FROM tblspecialpromo a LEFT JOIN tblpromotion b ON a.special::integer = b.seq ";
	$sub_sql .= "WHERE b.promo_idx = '{$promo_idx}' ";

	$sub_result = pmysql_query($sub_sql);

	if ( pmysql_num_rows($sub_result) == 0 ) {
		// 상품이 등록되어 있지 않은 프로모션
		if ( strpos($promo_bridx_list, ",{$brand_code},") !== false ) {
			if ( count($arrPromotionIdx) < $limitCount && !in_array($promo_idx, $arrPromotionIdx) ) {
				array_push($arrPromotionIdx, $promo_idx);
				array_push($arrPromotionTitle, $promo_title);
			}
		}
	} else {
		if ( !empty($brand_code) ) {
			while ( $sub_row = pmysql_fetch_object($sub_result) ) {
				$special_list   = trim($sub_row->special_list, ",");
				$special_list   = str_replace(",", "','", $special_list);

				// 해당 브랜드에 속한 상품 리스트 조회
				$sub_sql2  = "SELECT count(*) ";
				$sub_sql2 .= "FROM tblbrandproduct ";
				$sub_sql2 .= "WHERE bridx = {$brand_code} AND productcode in ( '{$special_list}' ) ";
				$sub_sql2 .= "LIMIT 1 ";

				$sub_row2  = pmysql_fetch_object(pmysql_query($sub_sql2));

				if ( $sub_row2->count >= 1 ) {
					if ( count($arrPromotionIdx) < $limitCount && !in_array($promo_idx, $arrPromotionIdx) ) {
						array_push($arrPromotionIdx, $promo_idx);
						array_push($arrPromotionTitle, $promo_title);
					}
				}
			}
		}
		pmysql_free_result($sub_result);
	}
}

#위시리스트 정보
$wish_sql = "SELECT COUNT(*) AS cnt FROM tblwishlist WHERE productcode = '".$_pdata->productcode."' AND id = '".$_ShopInfo->getMemid()."'";
$wish_res = pmysql_query( $wish_sql, get_db_conn() );
$wish_row = pmysql_fetch_object( $wish_res );
//if( $wish_row->cnt > 0 ) $wishlist_class = 'on';
//else $wishlist_class = '';
pmysql_free_result( $wish_res );

# 최근 상품 프로모션
$promo_sql =" SELECT pm.idx, pm.title, pm.rdate, pmt.title AS subtitle FROM tblpromo pm ";
$promo_sql.=" JOIN tblpromotion pmt ON pmt.promo_idx = pm.idx ";
$promo_sql.=" JOIN tblspecialpromo sp ON sp.special::int = pmt.seq ";
$promo_sql.=" WHERE sp.special_list LIKE '%".$_pdata->productcode."%' ";
$promo_sql.=" AND   pm.hidden = '1' ";
$promo_sql.=" ORDER BY pm.rdate DESC LIMIT 2";
$promo_res = pmysql_query( $promo_sql, get_db_conn() );
$promo_link = array();

$promo_target	 = "";
if($popup == "ok") $promo_target	 = " target='_parent'";

while( $promo_row = pmysql_fetch_object( $promo_res ) ) {
	$promo_link[] = "<a href='../front/promotion_detail.php?idx=".$promo_row->idx."'".$promo_target.">&gt; ".$promo_row->title."</a>";
}
pmysql_free_result( $promo_res );

#리뷰 베너
$review_banner = get_banner( 94 );

#사용 가능한 쿠폰 정보
$dpc = DownPossibleCoupon( $_pdata->productcode );

if ($dpc) {
	foreach($dpc as $dpcKey => $dpcVal) {
		if ($dpcVal->sale_type == 2) { // % 할인
			$coupon_use['per']	= $dpcVal->sale_money;
			$coupon_use['price']	= round( ( (100 - $dpcVal->sale_money) / 100 ) * $_pdata->sellprice );
		} else if ($dpcVal->sale_type == 4) { // 금액 할인
			$coupon_use['per']	= round( ( ( $_pdata->sellprice - ($_pdata->sellprice - $dpcVal->sale_money) ) / $_pdata->sellprice ) * 100 );
			$coupon_use['price']	= $_pdata->sellprice - $dpcVal->sale_money;
		}
		$coupon_use['name']		= $dpcVal->coupon_name;
		$coupon_use['code']		= $dpcVal->coupon_code;
		$coupon_use['type']		= $dpcVal->coupon_type;
		$coupon_use['dn']			= $dpcVal->take_dn;
		$coupon_use['btn_yn']	= $dpcVal->detail_auto;
	}
}

//임직원가
$staff_use['per']	= $_pdata->staff_rate;
$staff_use['price']	= round( ( (100 - $_pdata->staff_rate) / 100 ) * $_pdata->consumerprice );

//카드혜택 베너
$card_banner = get_banner( '111' );
# 쿠폰혜택 레이어 url
$iFrameLayrUrl = $Dir.FrontDir.'coupon_layer.php?productcode='.$productcode;


// 전체 리뷰갯수 및 별점별 갯수를 가져온다.
$rc_sql = "select productcode, 
			marks1 as marks1_cnt, 
			marks2 as marks2_cnt, 
			marks3 as marks3_cnt, 
			marks4 as marks4_cnt, 
			marks5 as marks5_cnt, 
			marks_total_cnt,
			marks1*1+marks2*2+marks3*3+marks4*4+marks5*5 as marks_sum_cnt,
			TRUNC(5.00 * (marks1*1+marks2*2+marks3*3+marks4*4+marks5*5) / (marks_total_cnt * 5),1) as marks_ever_cnt,
			ROUND(100.00 * marks1 / marks_total_cnt,2) as marks1_per,
			ROUND(100.00 * marks2 / marks_total_cnt,2) as marks2_per,
			ROUND(100.00 * marks3 / marks_total_cnt,2) as marks3_per,
			ROUND(100.00 * marks4 / marks_total_cnt,2) as marks4_per,
			ROUND(100.00 * marks5 / marks_total_cnt,2) as marks5_per
			from (SELECT productcode,
			sum(case when marks=1 then 1 else 0 end) as marks1,
			sum(case when marks=2 then 1 else 0 end) as marks2,
			sum(case when marks=3 then 1 else 0 end) as marks3,
			sum(case when marks=4 then 1 else 0 end) as marks4,
			sum(case when marks=5 then 1 else 0 end) as marks5, 
			count(productcode) as marks_total_cnt
			FROM tblproductreview group by productcode) a WHERE productcode='{$productcode}' ";
$rc_result=pmysql_query($rc_sql,get_db_conn());
$rc_row=pmysql_fetch_object($rc_result);
$review_info['marks_total_cnt']	= $rc_row->marks_total_cnt?$rc_row->marks_total_cnt:'0';
$review_info['marks1_cnt']	= $rc_row->marks1_cnt?$rc_row->marks1_cnt:'0';
$review_info['marks2_cnt']	= $rc_row->marks2_cnt?$rc_row->marks2_cnt:'0';
$review_info['marks3_cnt']	= $rc_row->marks3_cnt?$rc_row->marks3_cnt:'0';
$review_info['marks4_cnt']	= $rc_row->marks4_cnt?$rc_row->marks4_cnt:'0';
$review_info['marks5_cnt']	= $rc_row->marks5_cnt?$rc_row->marks5_cnt:'0';
$review_info['marks1_per']	= $rc_row->marks1_per?$rc_row->marks1_per:'0';
$review_info['marks2_per']	= $rc_row->marks2_per?$rc_row->marks2_per:'0';
$review_info['marks3_per']	= $rc_row->marks3_per?$rc_row->marks3_per:'0';
$review_info['marks4_per']	= $rc_row->marks4_per?$rc_row->marks4_per:'0';
$review_info['marks5_per']	= $rc_row->marks5_per?$rc_row->marks5_per:'0';
$review_info['marks_ever_cnt']	= $rc_row->marks_ever_cnt?$rc_row->marks_ever_cnt:'0.0';
$review_info['marks_ever_width']	= $rc_row->marks_ever_cnt?substr($rc_row->marks_ever_cnt,0,1)*20:'0';
pmysql_free_result($rc_result);

#좋아요
$like_sql = "SELECT p.productcode, li.section,
						COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND p.productcode = tl.hott_code),0) AS hott_cnt
			FROM tblproduct p
			LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on p.productcode = li.hott_code";
$like_sql .= " WHERE p.productcode = '".$productcode."' AND p.display = 'Y'";
$result = pmysql_query( $like_sql, get_db_conn() );
$like_row = pmysql_fetch_object( $result );
$like_info = $like_row;

?>
<?php include ($Dir.MainDir.$_data->menu_type.".php") ?>
<script src='<?=$Dir?>js/content/jquery.bxslider.min.js' type="text/javascript" ></script>
<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script src="https://npmcdn.com/imagesloaded@4.1/imagesloaded.pkgd.js"></script>
<SCRIPT LANGUAGE="JavaScript">
	var memId = "<?=$_ShopInfo->getMemid()?>";

	<?php if($_pdata->vender>0){?>
	function custRegistMinishop() {
		if(document.custregminiform.memberlogin.value!="Y") {
			alert("로그인 후 이용이 가능합니다.");
			return;
		}
		owin=window.open("about:blank","miniregpop","width=100,height=100,scrollbars=no");
		owin.focus();
		document.custregminiform.target="miniregpop";
		document.custregminiform.action="minishop.regist.pop.php";
		document.custregminiform.submit();
	}
	<?php }?>

	function primage_view(img,type) {
		if (img.length==0) {
			alert("확대보기 이미지가 없습니다.");
			return;
		}
		var tmp = "height=350,width=450,toolbar=no,menubar=no,resizable=no,status=no";
		if(type=="1") {
			tmp+=",scrollbars=yes";
			sc="yes";
		} else {
			sc="";
		}
		url = "<?=$Dir.FrontDir?>primage_view.php?scroll="+sc+"&image="+img;

		window.open(url,"primage_view",tmp);
	}

	function check_login() {
		if(confirm("로그인이 필요한 서비스입니다. 로그인을 하시겠습니까?")) {
			document.location.href="<?=$Dir.FrontDir?>login.php?chUrl=<?=getUrl()?>";
		}
	}


	function GoPage(gbn,block,gotopage) {
		document.idxform.action=document.idxform.action+"?#"+gbn;
		if(gbn=="review") {
			document.idxform.block.value=block;
			document.idxform.gotopage.value=gotopage;
		} else if(gbn=="prqna") {
			document.idxform.qnablock.value=block;
			document.idxform.qnagotopage.value=gotopage;
		}
		document.idxform.submit();
	}

	/* ################ 태그관련 ################## */
	var IE = false ;
	if (window.navigator.appName.indexOf("Explorer") !=-1) {
		IE = true;
	}
	//tag 금칙 문자 (%, &, +, <, >, ?, /, \, ', ", =,  \n)
	var restrictedTagChars = /[\x25\x26\x2b\x3c\x3e\x3f\x2f\x5c\x27\x22\x3d\x2c\x20]|(\x5c\x6e)/g;
	function check_tagvalidate(aEvent, input) {
		var keynum;
		if(typeof aEvent=="undefined") aEvent=window.event;
		if(IE) {
			keynum = aEvent.keyCode;
		} else {
			keynum = aEvent.which;
		}
		//  %, &, +, -, ., /, <, >, ?, \n, \ |
		var ret = input.value;
		if(ret.match(restrictedTagChars) != null ) {
			ret = ret.replace(restrictedTagChars, "");
			input.value=ret;
		}
	}

	function tagCheck(productcode) {
		<?php if(strlen($_ShopInfo->getMemid())>0){?>
		var obj = $("#searchtagname");

		if(obj.val().length < 2 ){
			alert("태그를(2자 이상) 입력해 주세요!");
			obj.focus();
			return;
		}
		goProc("prtagreg",productcode);
		return;
		<?php }else{?>
		alert("로그인 후 작성해 주세요!");
		return;
		<?php }?>

	}

	function goProc(mode,productcode){
		var obj = document.all;
		if(mode=="prtagreg") {
			succFun=myFunction;
			var tag=obj.searchtagname.value;
			var path="<?=$Dir.FrontDir?>tag.xml.php?mode="+mode+"&productcode="+productcode+"&tagname="+tag;
			obj.searchtagname.value="처리중 입니다!";
		} else {
			succFun=prTaglist;
			var path="<?=$Dir.FrontDir?>tag.xml.php?mode="+mode+"&productcode="+productcode;
		}
		var myajax = new Ajax(path,
			{
				onComplete: function(text) {
					succFun(text,productcode);
				}
			}
		).request();
	}

	function myFunction(request,productcode){

		var msgtmp = request;
		var splitString = msgtmp.split("|");

		//다시 초기화
		var obj = document.all;
		obj.searchtagname.value="";
		if(splitString[0]=="OK") {
			var tag = splitString[2];
			if(splitString[1]=="0") {

			} else if(splitString[1]=="1") {
				goProc("prtagget",productcode);
			}
		} else if(splitString[0]=="NO") {
			alert(splitString[1]);
		}
	}

	function prTaglist(request) {

		var msgtmp = request;
		var splitString = msgtmp.split("|");
		if(splitString[0]=="OK") {
			$("#prtaglist").html(splitString[1]);
		} else if(splitString[0]=="NO") {
			alert(splitString[1]);
		}
	}

	function cardinfo_pop() {
		//$('.card_info').css('width','570px');
		$('.card_info').toggle();
	}

	function primg_preview(imagepath,img) {

		var attrVal = imagepath + img;
		if ( img.indexOf("http://") !== -1 ) {
			attrVal = img;
		}

		if($("img[name='primg']")!=null) {
			setcnt=0;
			$("img[name='primg']").attr("src", attrVal);
			$("img[name='primg']").attr("data-zoom-image", attrVal);
		}

	}

	// DECO용 조합형 옵션선택
	// 2016-02-03 유동혁
	function option_select( code, idx ){
		var productcode = $('#prcode').val();
		var option_code = code;
		var next_idx = parseInt( idx ) + 1;
		var option_count = $('.CLS_option_value').length;

		$('.CLS_option_value').eq( idx ).attr( 'data-option-code', code );
		if( jQuery.type( $('.CLS_option_value').eq( next_idx ) ) === "undefinded" ) return;

		$('.CLS_option_select').each( function( option_idx, option_obj ) {
			if( option_idx != idx && option_idx > idx ){
				$('.CLS_option_value').eq( option_idx ).attr( 'data-option-code', '' );
				if( option_code == '' ){
					$(this).find('option').remove();
					$(this).append('<option value=\'\' data-qty=\'\' data-code=\'\' >'+$(this).attr("title")+'</option>');
				}
			}
		});
		if( option_code != '' ){
			$.ajax({
				type : "POST",
				url : "ajax_option_select.php",
				data : { productcode : productcode, option_code : option_code, idx : next_idx },
				dataType : "json"
			}).done( function( data ){
				if( !jQuery.isEmptyObject( data ) ){

					var option_html = '';
					var constant_quantity = parseInt( $('#constant_quantity').val() );
					$.each( data , function( i, obj ){
						if( next_idx + 1 == option_count ){
							var soldout = '';
							var disabled_on = '';
							var price_text = '';
							if( obj.price != '' && obj.price > 0 ){
								price_text = ' ( + ' + comma( obj.price ) + ' 원 )';
							} else if( obj.price != '' && obj.price < 0 ) {
								price_text = ' ( - ' + comma( obj.price ) + ' 원 )';
							}
							if( obj.soldout == "1" && constant_quantity < 999999999 ) {
								soldout = '[품절]';
								disabled_on = ' disable';
							}
							option_html += '<option value=\''+option_code+chr(30)+obj.code+'\' data-code="' + option_code+chr(30)+obj.code + '" data-qty="' + obj.qty + '" ' + disabled_on + ' >' + soldout + obj.code + price_text + '</option>';
						} else {
							option_html += '<option value=\''+option_code+chr(30)+obj.code+'\' data-code="' + option_code+chr(30)+obj.code + '" data-qty="' + obj.qty + '" >' + obj.code + '</option>';
						}
					});

					$('.CLS_option_select').eq( next_idx ).append( option_html );
				}
			});
		}

	}
	// 상품 옵션별 검색 추가 2016-03-01 유동혁
	function option_type_search(){
		var option_type_search = 0; // 0 - 조합형 옵션, 1 - 독립형 옵션, 2 - 옵션없음

		if( $('.CLS_option_value').length > 0  ){
			option_type_search = 0;
		} else if( $('select[name="alone_option[]"]').length > 0 ){
			option_type_search = 1;
		} else {
			option_type_search = 2;
		}

		return option_type_search;
	}

	//상품의 옵션 상태를 확인한다
	// 2016-02-03 유동혁
	function option_case(){

		var option_case = false;

		if( $('.CLS_option_value').length > 0  ){
			option_case = $('.CLS_option_value').length - 1;
		}

		return option_case;
	}
	//상품의 옵션 quantity를 넘겨준다
	// 2016-02-03 유동혁
	function quantity_case(){

		var quantity = 0;
		var option_idx = option_case();
		var option_code = $('.CLS_option_value').eq( option_idx ).attr('data-option-code'); // 조합형

		if( option_idx === false ){
			quantity = $('#constant_quantity').val();
		} else {
			if( $('.CLS_option_value').eq( option_idx ).find('option:selected').val() != '' ){
				if( option_code ) {
					quantity = $('.CLS_option_value').eq( option_idx ).find('option:selected').attr('data-qty');
				}
			} else {
				quantity = false;
			}
		}

		return quantity;

	}

	//상품의 옵션 code를 넘겨준다
	// 2016-02-03 유동혁
	function select_option(){

		var option_code = '';
		var option_idx = option_case();
		var option_code = '';
		var option_arr = [];

		if( option_idx !== false ) {
			option_code = $('.CLS_option_value').eq( option_idx ).attr('data-option-code');
		} else if( $('select[name="alone_option[]"]').length > 0 ){
			$('select[name="alone_option[]"]').each( function(){
				option_arr.push( $(this).attr('data-option-code') );
			});
			option_code = option_arr.join( '@#' );
		}

		return option_code;
	}

	//수량 올리기
	$(document).on( 'click', '.btn-plus', function() {

		var select_quantity = quantity_case();
		var up_quantity = parseInt( $('#quantity').val() );
		var qty = 0;
		var constant_quantity = parseInt( $('#constant_quantity').val() );

		if( select_quantity ){
			qty = parseInt( select_quantity );
		} else {
			alert('옵션을 선택해 주시기 바랍니다.');
			return;
		}

		if( ( up_quantity + 1 ) > qty && constant_quantity < 999999999 ) {
			alert('재고가 부족합니다.');
			$('#quantity').val( qty );
			return;
		} else {
			$('#quantity').val( up_quantity + 1 );
		}

	} );
	//수량 내리기
	$(document).on( 'click', '.btn-minus', function() {

		var select_quantity = quantity_case();
		var up_quantity = parseInt( $('#quantity').val() );
		var qty = 0;

		if( select_quantity ){
			qty = parseInt( select_quantity );
		} else {
			alert('옵션을 선택해 주시기 바랍니다.');
			return;
		}

		if( ( up_quantity - 1 )  < 1 ) {
			alert('상품수량을 1개 이상 선택하셔야 합니다.');
			return;
		} else {
			$('#quantity').val( up_quantity - 1 );
		}

	} );

	//상품수량 직접입력
	$(document).on( 'keyup', '#quantity', function( event ) {

		var select_quantity = quantity_case();
		var up_quantity = parseInt( $('#quantity').val() );
		var qty = 0;0
		var constant_quantity = parseInt( $('#constant_quantity').val() );

		if( select_quantity ){
			qty = parseInt( select_quantity );
		} else {
			alert('옵션을 선택해 주시기 바랍니다.');
			$(this).val( 1 );
			return;
		}

		if( up_quantity < 1 ) {
			alert('상품수량을 1개 이상 선택하셔야 합니다.');
			$(this).val( 1 );
			return;
		} else if( up_quantity > qty && constant_quantity < 999999999 ) {
			alert('재고가 부족합니다.');
			$(this).val( qty );
			return;
		}

	});
	// 추가옵션 체크 2016-03-01 유동혁
	function addoption_check (){
		var err = true;

		if( $('input[name="addoption_tf[]"]').length > 0 ){
			$('input[name="addoption_tf[]"]').each( function( idx, obj ){
				if( $(this).val() == 'T' && $('input[name="addoption[]"]').eq(idx).val().length == 0 )  err = false;
			});
		}

		return err;
	}
	// 독립옵션 체크 2016-03-01 유동혁
	function alone_option_check(){
		var err = true;

		if( $('input[name="alone_option_tf[]"]').length > 0 ){
			$('input[name="alone_option_tf[]"]').each( function( idx, obj ){
				if( $(this).val() == 'T' && $('button[name="alone_option[]"]').eq(idx).attr('data-option-code').length == 0 ) {
					err = false;
				}
			});
		}

		return err;
	}

	$(document).on( 'keyup', 'input[name="addoption[]"]', function( event ) {
		var event_target = $(this).next().next().find('strong');
		event_target.html( $(this).val().length );
	});
	// 독립옵션 선택 2016-03-01 유동혁
	$(document).on( 'change', 'select[name="alone_option[]"]', function( event ) {
		var code             = $(this).find("option:selected").attr('data-code');
		var quantity         = $(this).find("option:selected").attr('data-qty');
		var constant_quantity = parseInt( $('#constant_quantity').val() );

		$(this).attr( 'data-option-code', '' );
		$(this).attr( 'data-option-qty', '' );

		if( quantity == 0 && code != '' && constant_quantity < 999999999 ) {

			alert('품절된 상품입니다.');
			return;
		}

		$(this).attr( 'data-option-code', code );
		$(this).attr( 'data-option-qty', quantity );

	});

	//숫자키 이외의 것을 막음
	$(document).on( 'keydown', '#quantity', function( event ) {
		if( !isNumKey( event ) ) event.preventDefault();
	});

	function order_check( memchk, staffchk ){
		var productcode = $('#prcode').val();
		// 여러개를 담아야 하므로 주석 처리
		// var quantity = $('#quantity').val();
		// var option_code = select_option();
		var option_type = option_type_search();
		var text_content = '';
		var text_subject = '';
		var alone_chk = true;
		var constant_quantity = parseInt( $('#constant_quantity').val() );

		var chkPassOptionStore = optionStoreSettingChk();
		if(!chkPassOptionStore){
			alert('옵션의 매장선택을 확인하세요.');
			return;
		}

		/*
		 if ( $("#quantity").val() < 1){
		 alert('주문 수량을 확인하세요.');
		 return;
		 }
		 */
		var totalQuantitySum = 0;
		$("input[name='add_quantity[]']").each( function(){
			totalQuantitySum += parseInt( $( this ).val() );
		});
		if($("input[name='add_quantity[]']").length < 1 && $("input[name='add_quantity[]']").length > totalQuantitySum){
			alert('주문 수량을 확인하세요.');
			return;
		}


		/*
		 if( !quantity_case() ){
		 alert('옵션을 선택해 주시기 바랍니다.');
		 return;
		 }
		 */
		if($("input[name='add_option[]']").length < 1){
			alert('옵션을 선택해 주시기 바랍니다.');
			return;
		}

		if( !addoption_check () ){
			alert('필수 옵션을 선택해 주시기 바랍니다.');
			return;
		} else {
			if( $('input[name="addoption[]"]').length > 0 ) {
				var tmp_content = [];
				var tmp_subject = [];
				$('input[name="addoption[]"]').each( function(i,v){
					tmp_subject.push( $(this).attr('data-option-code') );
					tmp_content.push( $.trim( $(this).val() ) );
				});
				text_subject = tmp_subject.join( '@#' );
				text_content = tmp_content.join( '@#' );
			}
		}

		if( !alone_option_check () ){
			alert('필수 옵션을 선택해 주시기 바랍니다.');
			return;
		} else {
			if( option_type == 1 ){
				$('select[name="alone_option[]"]').each( function( i, v ){
					if( $(this).attr('data-option-code') != '' && $(this).attr( 'data-option-qty' ) < totalQuantitySum && constant_quantity < 999999999 ){
						//if( $(this).attr('data-option-code') != '' && $(this).attr( 'data-option-qty' ) < quantity && constant_quantity < 999999999 ){
						alert('옵션의 수량이 부족합니다. (' + $(this).attr('data-option-qty') + ')');
						$(this).focus();
						alone_chk = false;
						return alone_chk;
					}
				});
			}
		}

		if( !alone_chk ) return;

		// 장바구니를 거쳐서 가는것을 ajax로 변경 2015 11 09 유동혁
		var dataBasketidx = [];
		var basketMsg = "";
		// 옵션 수량 만큼 배열을 돌려서 장바구니에 담음
		$("input[name='add_option[]']").each(function(){
			option_code = $(this).val();
			quantity = $(this).parent().parent().find("input[name='add_quantity[]']").val();

			var reservation_date = "";
			var post_code = "";
			var address1 = "";
			var address2 = "";

			var store_code = $(this).parent().parent().find("input[name='store_code[]']").val();

			if($(this).parent().parent().find("input[name='reservation_date[]']").length > 0){
				reservation_date = $(this).parent().parent().find("input[name='reservation_date[]']").val();
			}

			if($(this).parent().parent().find("input[name='post_code[]']").length > 0){
				post_code = $(this).parent().parent().find("input[name='post_code[]']").val();
			}

			if($(this).parent().parent().find("input[name='address1[]']").length > 0){
				address1 = $(this).parent().parent().find("input[name='address1[]']").val();
			}

			if($(this).parent().parent().find("input[name='address2[]']").length > 0){
				address2 = $(this).parent().parent().find("input[name='address2[]']").val();
			}

			$.ajax({
				method : 'POST',
				url : 'confirm_basket_proc.php',
				async: false,
				data: {
					productcode : productcode, option_code : option_code,
					quantity : quantity, option_type : option_type, mode : 'order',
					text_opt_subject : text_subject, text_opt_content : text_content,
					delivery_type : global_delivery_type, store_code : store_code, reservation_date : reservation_date,
					post_code : post_code, address1 : address1, address2 : address2
				},
				dataType : 'json'
			}).done( function( data ) {
				if( data.basketidx ){
					dataBasketidx.push( data.basketidx );
				} else {
					basketMsg = '장바구니 등록이 실패되었습니다.';
				}
			});
		})

		if( dataBasketidx.length > 0 ){
			var dataBasketidxStr = dataBasketidx.join( '|' );

			if( memchk == 0 ){
				<?if($popup == "ok") echo "parent.";?>location.href = "/front/login.php?chUrl=/front/order.php?basketidxs=" + dataBasketidxStr;
			} else {
				<?if($popup == "ok") echo "parent.";?>location.href = "/front/order.php?"+"basketidxs=" + dataBasketidxStr + "&staff_order=" + staffchk;
			}
		}else{
			alert(basketMsg);
		}
	}

	function basket_check(){
		var productcode = $('#prcode').val();
		// 여러개를 담아야 하므로 주석 처리
		// var quantity = $('#quantity').val();
		// var option_code = select_option();
		var option_type = option_type_search();
		var text_content = '';
		var text_subject = '';
		var alone_chk = true;
		var constant_quantity = parseInt( $('#constant_quantity').val() );


		var chkPassOptionStore = optionStoreSettingChk();
		if(!chkPassOptionStore){
			alert('옵션의 매장선택을 확인하세요.');
			return;
		}


		/*
		 if ( $("#quantity").val() < 1){
		 alert('주문 수량을 확인하세요.');
		 return;
		 }
		 */
		var totalQuantitySum = 0;
		$("input[name='add_quantity[]']").each( function(){
			totalQuantitySum += parseInt( $( this ).val() );
		});
		if($("input[name='add_quantity[]']").length < 1 && $("input[name='add_quantity[]']").length > totalQuantitySum){
			alert('주문 수량을 확인하세요.');
			return;
		}


		/*
		 if( !quantity_case() ){
		 alert('옵션을 선택해 주시기 바랍니다.');
		 return;
		 }
		 */
		if($("input[name='add_option[]']").length < 1){
			alert('옵션을 선택해 주시기 바랍니다.');
			return;
		}

		if( !addoption_check () ){
			alert('필수 옵션을 선택해 주시기 바랍니다.');
			return;
		} else {
			if( $('input[name="addoption[]"]').length > 0 ) {
				var tmp_content = [];
				var tmp_subject = [];
				$('input[name="addoption[]"]').each( function(i,v){
					tmp_subject.push( $(this).attr('data-option-code') );
					tmp_content.push( $.trim( $(this).val() ) );
				});
				text_subject = tmp_subject.join( '@#' );
				text_content = tmp_content.join( '@#' );
			}
		}

		if( !alone_option_check () ){
			alert('필수 옵션을 선택해 주시기 바랍니다.');
			return;
		} else {
			if( option_type == 1 ){
				$('select[name="alone_option[]"]').each( function( i, v ){
					if( $(this).attr('data-option-code') != '' && $(this).attr( 'data-option-qty' ) < totalQuantitySum && constant_quantity < 999999999 ){
						alert('옵션의 수량이 부족합니다. (' + $(this).attr('data-option-qty') + ')');
						$(this).focus();
						alone_chk = false;
						return alone_chk;
					}
				});
			}
		}

		if( !alone_chk ) return;

		// 장바구니를 거쳐서 가는것을 ajax로 변경 2015 11 09 유동혁
		var dataBasketFlag = false;
		var basketMsg = "";
		// 옵션 수량 만큼 배열을 돌려서 장바구니에 담음
		$("input[name='add_option[]']").each(function(){
			option_code = $(this).val();
			quantity = $(this).parent().parent().find("input[name='add_quantity[]']").val();

			var reservation_date = "";
			var post_code = "";
			var address1 = "";
			var address2 = "";

			var store_code = $(this).parent().parent().find("input[name='store_code[]']").val();

			if($(this).parent().parent().find("input[name='reservation_date[]']").length > 0){
				reservation_date = $(this).parent().parent().find("input[name='reservation_date[]']").val();
			}

			if($(this).parent().parent().find("input[name='post_code[]']").length > 0){
				post_code = $(this).parent().parent().find("input[name='post_code[]']").val();
			}

			if($(this).parent().parent().find("input[name='address1[]']").length > 0){
				address1 = $(this).parent().parent().find("input[name='address1[]']").val();
			}

			if($(this).parent().parent().find("input[name='address2[]']").length > 0){
				address2 = $(this).parent().parent().find("input[name='address2[]']").val();
			}

			$.ajax({
				method : 'POST',
				url : 'confirm_basket_proc.php',
				async: false,
				data: {
					productcode : productcode, option_code : option_code, quantity : quantity,
					option_type : option_type, mode : 'insert',
					text_opt_subject : text_subject, text_opt_content : text_content,
					delivery_type : global_delivery_type, store_code : store_code, reservation_date : reservation_date,
					post_code : post_code, address1 : address1, address2 : address2
				},
				dataType : 'json'
			}).done( function( data ) {
				if( data ){
					dataBasketFlag = true;
				} else {
					basketMsg = '장바구니 등록이 실패되었습니다.';
				}
			});
		})

		if( dataBasketFlag ){
			if ( confirm("장바구니에 추가되었습니다.\n장바구니로 이동하시겠습니까?") ) {
				location.href="../front/basket.php";
			}
		}else{
			alert(basketMsg);
		}
	}


	function wish_check(){
		var productcode = $('#prcode').val();
		var option_code = select_option();
		var option_type = 0; //데코앤이는 필수옵션만 존재함

		/*
		 if( !quantity_case() ){
		 alert('옵션을 선택해 주시기 바랍니다.');
		 return;
		 }
		 */

		// 장바구니를 거쳐서 가는것을 ajax로 변경 2015 11 09 유동혁
		$.ajax({
			method : 'POST',
			url : 'confirm_wishlist_proc.php',
			data: { productcode : productcode, option_code : option_code, option_type : option_type, mode : 'insert' }
			//dataType : 'json'
		}).done( function( data ) {
			if( data.length == 0 ){
				if ( confirm("위시리스트에 등록되었습니다.\n위시리스트로 이동하시겠습니까?") ) {
					location.href="../front/wishlist.php";
				}
			} else {
				alert( data );
			}
		});
	}

	function set_review_vote(val) {
		$("#review_vote").val(val);
	}

	function ajax_review_insert(){
		var productcode         = $("#productcode").val().trim();
		var ordercode           = $("#ordercode").val().trim();
		var productorder_idx    = $("#productorder_idx").val().trim();

		var review_title        = $("#review_title").val().trim();
		var review_content      = $("#review_content").val().trim();
		var review_vote         = $("#review_vote").val();

		var size = $(':radio[name="review_size"]:checked').val();
		var color = $(':radio[name="review_color"]:checked').val();
		var foot_width = $(':radio[name="review_foot_width"]:checked').val();
		var quality = $(':radio[name="review_quality"]:checked').val();

		if ( review_vote == "" ) {
			alert("별점을 선택해 주세요.");
		} else if ( review_title == "" ) {
			alert("제목을 입력해 주세요.");
			$("#review-title").val('').focus();
		} else if ( review_content == "" ) {
			alert("내용을 입력해 주세요.");
			$("#review-content").val('').focus();
		} else if ( chkReviewContentLength($("#review_content")[0]) === false ) {
			$("#review_content").focus();
			// } else if ( productcode == "" || ordercode == "" || productorder_idx == "" ) { // 상품코드만 있으면 될것 같은데~
		} else if ( productcode == "") {
			alert("주문상품을 선택해 주세요.");
		} else if ( size == "" ) {
			alert("사이즈를 선택해 주세요");
		} else if ( color == "" ) {
			alert("색상을 선택해 주세요");
		} else if ( foot_width == "" ) {
			alert("발볼 넓이를 선택해 주세요");
		} else if ( quality == "" ) {
			alert("품질/만족도를 선택해 주세요");
		} else {
			$("#size").val(size);
			$("#foot_width").val(foot_width);
			$("#color").val(color);
			$("#quality").val(quality);
			var fd = new FormData($("#reviewForm")[0]);

			$.ajax({
				url: "ajax_insert_review_v2.php",
				type: "POST",
				data: fd,
				async: false,
				cache: false,
				contentType: false,
				processData: false,
			}).success(function(data){
				console.log(data);
				if ( data == "SUCCESS" ) {
					alert("리뷰가 등록되었습니다.");
					location.reload();
				} else {
					var arrTmp = data.split("||");
					if ( arrTmp[0] == "FAIL" ) {
						alert(arrTmp[1]);
					} else {
						alert("리뷰가 등록이 실패하였습니다.");
					}
				}
			})	;
		}

	}

	// php chr() 대응
	function chr(code)
	{
		return String.fromCharCode(code);
	}

	//관련 포스팅
	function postingList(){
		var productcode = "<?=$productcode?>";
		var brand = $("#brand_name").val();
		$.ajax({
			type: "POST",
			url: "ajax_posting_list.php",
			data: "productcode="+productcode+"&brand="+brand,
			dataType:"HTML",
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		}).done(function(html){
			$(".posting-list").html(html);
			var posting_count = $("#posting_count").val();
			if(typeof posting_count == "undefined"){
				$(".btn-posting").html("<strong>관련 포스팅</strong> (0)");
			}else{
				$(".btn-posting").html("<strong>관련 포스팅</strong> ("+posting_count+")");
			}

		});
	}

	//색상 옵션 선택
	function colorSelect(productcode){

		$($(".CLS_colorProduct").find("li")).each(function(i, e) {
			var id = $(e).attr("id");
			var arrProductcode = id.split("_");
			$("#color_"+arrProductcode[1]).removeClass();

			if(productcode == arrProductcode[1]){
				$("#color_"+productcode).addClass("on");
			}
		});

		$('#prcode').val(productcode);

	}

	// 관련포스팅 상세정보
	function detailView(idx){
		$.ajax({
			type: "POST",
			url: "../front/ajax_instagram_detail.php",
			data: "idx="+idx,
			dataType:"JSON"
		}).done(function(data){
			console.log(data);
			reset();
			var arrTag = data[0]['hash_tags'].split(",");
			var arrRelation = data[0]['relation_product'].split(",");
			var arrProdName = data[0]['productname'].split(",");
			var arrBrandName = data[0]['brandname'].split(",");
			var arrProdImage = data[0]['brandimage'].split(",");
			var html =  "";
			if(data[0]['hash_tags'] != ""){
				$.each( arrTag, function( i, v ){
					$(".tag").text("#"+v);
				});
			}
			if(data[0]['relation_product'] != ""){
				$.each( arrRelation, function( i, v ){
					html += '<li>';
					html += '<a href="javascript:prod_detail(\''+v+'\');">';
					html += '<figure>';
					html += '<img src="<?=$productimgpath ?>'+arrProdImage[i]+'" alt="관심상품">';
					html += '<figcaption># '+arrBrandName[i]+'<br>'+arrProdName[i]+' ';
					html += '</figcaption>';
					html += '</figure>';
					html += '</a>';
					html += '</li>';
					$(".related-list").html(html);
				});
			}
			$("#content").text(strip_tags(data[0]['content']));
			$("#instagram_img").attr("src","<?=$instaimgpath ?>"+data[0]['img_file']+"");

			if(data[0]['section'] == null){
				$(".title").append('<button class="comp-like btn-like detail-like like_i'+idx+'"  onclick="detailSaveLike(\''+idx+'\',\'off\',\'instagram\',\''+memId+'\',\'\')" id="likedetail_'+idx+'" title="선택 안됨"><span class="like_icount_'+idx+'"><strong>좋아요</strong>'+data[0]['hott_cnt']+'</span></button>');
			}else{
				$(".title").append('<button class="comp-like btn-like detail-like like_i'+idx+' on " onclick="detailSaveLike(\''+idx+'\',\'on\',\'instagram\',\''+memId+'\',\'\')" id="likedetail_'+idx+'" title="선택됨"><span class="like_icount_'+idx+'"><strong>좋아요</strong>'+data[0]['hott_cnt']+'</span></button>');
			}

		});
	}

	// 옵션 z-index 설정
	var global_delivery_type = 0;
	var global_selection_store_idx = "";
	$(document).ready( function() {
		postingList();
		var option_cnt = $('div.select').length;
		var productcode = "<?=$productcode?>";

		//색상
		$($(".CLS_colorProduct").find("li")).each(function(i, e) {
			var id = $(e).attr("id");
			var arrProductcode = id.split("_");
			if(productcode == arrProductcode[1]){
				$("#color_"+productcode).addClass("on");
			}
		});

		if( option_cnt > 0 ){
			$('div.select').each( function( idx, obj ) {
				$(this).css( 'z-index', ( option_cnt * 5 ) - ( idx * 5 ) );
			});
		}

		$('.btn-qna-detail').click(function(){
			$('.pop-qna-detail').fadeIn();
		});

		$('.btn-review-detail').click(function(){
			$('.pop-review-detail').fadeIn();
		});

		$(document).on('click', '.btn-view-detail', function(){
			$('.CLS_instagram').fadeIn();
		});

		//sns 이벤트
		$('#facebook-link').click( snsLinkPop );
		$('#twitter-link').click( snsLinkPop );
		$('#band-link').click( snsLinkPop );

		$(".btn-close").click(function(){
			reset();
		});





		/////////////////////////////////////////////////////////
		//////////////////// 상품 옵션 추가 ///////////////////
		/////////////////////////////////////////////////////////
		$(".CLS_option_value").change(function(){
			var selectQty = 1;
			var optionQty = $(this).find(':selected').data("qty");
			var optionCode = $(this).find(':selected').data("code");

			// 추가된 객체에서 추가할 옵션이 있는지 체크한다.
			var optionCheck = true;
			$(".hero-info-option-table input[name='add_option[]']").each(function(){
				if($(this).val() == optionCode){
					optionCheck = false;
				}
			})

			var deliveyTypeCheck = true;
			if(global_delivery_type == 2 && $(".hero-info-option-table tr").length > 0){
				deliveyTypeCheck = false;
			}

			if(optionCheck){
				var innerHTML = "";
				innerHTML += "<tr style = 'display:none;'>";

				innerHTML += "	<td>";
				innerHTML += "		" + optionCode + " <input type = 'hidden' name = 'add_option[]' value = '" + optionCode + "'>";
				innerHTML += "	</td>";

				if(global_delivery_type == 2){
					// 당일 발송일시 수량 덧셈 뺄셈 제거
					innerHTML += "	<td>";
					innerHTML += "		<div class='qty'>";
					innerHTML += "			<input type='text' name='add_quantity[]' title='수량' value='" + selectQty + "' style='width:74px;' readonly>";
					innerHTML += "		</div>";
					innerHTML += "	</td>";
				}else{
					// 당일 발송이 아니면 수량 덧셈 뺄셈
					innerHTML += "	<td>";
					innerHTML += "		<div class='qty'>";
					innerHTML += "			<input type='text' name='add_quantity[]' title='수량' value='" + selectQty + "' style='width:74px;' readonly>";
					innerHTML += "			<button class='btn_add addOption-btn-plus' type='button'><span>수량 1 더하기</span></button>";
					innerHTML += "			<button class='btn_subtract addOption-btn-minus' type='button'><span>수량 1 빼기</span></button>";
					innerHTML += "		</div>";
					innerHTML += "	</td>";
				}

				if(global_delivery_type == 0){
					// 택배발송일 경우 제거 버튼만 노출
					innerHTML += "	<td colspan = '3'>";
					innerHTML += "		<div class='btn_option_delete'>";
					innerHTML += "			<a class='btn-type1 addOptionDelete' href='javascript:;'>제거</a>";
					innerHTML += "		</div>";
					innerHTML += "	</td>";
				}else{
					innerHTML += "	<td>";
					innerHTML += "		<div class='btn_option_delete_type'>";
					innerHTML += "			<a class='btn-type1 addOptionStore' data-delivery_type = '" + global_delivery_type + "' href='javascript:;'>매장선택</a>";
					innerHTML += "		</div>";
					innerHTML += "	</td>";

					innerHTML += "	<td>";
					innerHTML += "		<div class='store_selection_area'>";
					innerHTML += "			[미선택]";
					innerHTML += "		</div>";
					innerHTML += "	</td>";

					// 택배발송이 아닐 경우 매장 선택 버튼 노출
					innerHTML += "	<td>";
					innerHTML += "		<div class='btn_option_delete_type'>";
					innerHTML += "			<a class='btn-type1 addOptionDelete' href='javascript:;'>제거</a>";
					innerHTML += "		</div>";
					innerHTML += "	</td>";
				}

				innerHTML += "</tr>";


				if(deliveyTypeCheck){
					if(optionQty > 0){
						$(".hero-info-option-table").append(innerHTML);
						$(".hero-info-option-table tr:last").fadeIn('800');

						reSettingEqIndex();
					}else{
						alert("해당 옵션의 재고가 없습니다.");
					}
				}else{
					alert("당일 수령 상품은 한번에 하나만 주문 가능합니다.");
				}


			}else{
				alert("해당 옵션이 이미 추가되어 있습니다.");
			}
		})


		/////////////////////////////////////////////////////////
		///////////////// 옵션의 인덱스값 셋팅 ///////////////
		/////////////////////////////////////////////////////////
		function reSettingEqIndex(){
			var reIdx = 0;
			$(".hero-info-option-table tr").find(".addOptionStore").each(function(){
				$(this).attr("data-eqindex", reIdx);
				reIdx++;
			})
		}


		/////////////////////////////////////////////////////////
		///////////////// 추가 옵션 매장 선택 /////////////////
		/////////////////////////////////////////////////////////
		$(document).on('click', '.addOptionStore', function(){
			var optionAppendObj = $(this);

			$.ajax({
				type: "POST",
				url: "../front/ajax_get.reserve.date.php",
				data : { mode : 'dateFlag', delivery_type : global_delivery_type },
				dataType : 'html'
			}).done( function( flag ){
				//if((global_delivery_type == '2' && flag == '1') || (global_delivery_type == '1' && flag == '1')){
				if((global_delivery_type == '2' && flag == '1') || (global_delivery_type == '1')){

					document.frm_storesearch.sizechk.value		= "";
					document.frm_storesearch.area_code.value	= "";
					document.frm_storesearch.searchVal.value	= "";
					$(".CLS_set_quantity").val('')
					$("#stock_store_result").html("");
					$(".CLS_stock_store_list").hide();

					if($(optionAppendObj).parent().parent().parent().find("input[name='add_option[]']").length > 0){
						var optionCode = $(optionAppendObj).parent().parent().parent().find("input[name='add_option[]']").val();
						var optionQuantity = $(optionAppendObj).parent().parent().parent().find("input[name='add_quantity[]']").val();
						$(".CLS_set_quantity").val(optionQuantity);



						$('#size_'+optionCode).prop("disabled", false);
						$('#size_'+optionCode).next().css("background", "#000");

						$(".CLS_sizechk").not($('#size_'+optionCode)).prop("disabled", true);
						$(".CLS_sizechk").not($('#size_'+optionCode)).next().css("background", "#e2e2e2");

						$('#size_'+optionCode).prop("checked", true);
						storeSearchChkForm('size');
					}else{
						$(".CLS_set_quantity").val("");

						$('#size_0').prop("checked", true);
						initialize('','');
						storeSearchChkForm('size');
					}

					// 배송 방법 값
					if(global_delivery_type == '1'){
						$(".CLS_reservation_date").show();
						$(".CLS_write_address").hide();
					}else if(global_delivery_type == '2'){
						$(".CLS_reservation_date").hide();
						$(".CLS_write_address").show();
					}else{
						$(".CLS_reservation_date").hide();
						$(".CLS_write_address").hide();
					}

					$(".CLS_select_storecode").val('');
					$(".CLS_select_storename").val('');

					$("#post5").val('');
					$("#rpost1").val('');
					$("#rpost2").val('');
					$("#raddr1").val('');
					$("#raddr2").val('');
					$("#raddr2").focus();
					$("#post").val( '' );


					global_selection_store_idx = $(optionAppendObj).data('eqindex');

					$('.pop_stock_detail').fadeIn();
					return false;

				}else{
					// 배송타입 1은 매장 선택시 새로 불러오도록 되어 있음
					if(global_delivery_type == '2'){
						alert("주문 가능한 시간이 지났습니다.( 매일 15시 )");
						$(".hero-info-option-table").html('');
					}
				}
			});
		})

		/////////////////////////////////////////////////////////
		//////////////////// 배송 타입 선택 ///////////////////
		/////////////////////////////////////////////////////////
		$(document).on('click', '.CLS_delivery_type', function(){
			if(global_delivery_type != $(this).val()){
				$(".CLS_option_value").val('');
				$("#quantity").val('1');
				allDeleteOptionValues();
			}
			global_delivery_type = $(this).val();
		})

		/////////////////////////////////////////////////////////
		//////////////////// 상품 옵션 삭제 ///////////////////
		/////////////////////////////////////////////////////////
		$(document).on('click', '.addOptionDelete', function(){
			$(this).parent().parent().parent().remove();

			reSettingEqIndex();
		})

		////////////////////////////////////////////////////////
		///////////////// 상품 옵션  전체 삭제 ///////////////
		////////////////////////////////////////////////////////
		function allDeleteOptionValues(){
			$(".hero-info-option-table").html('');
		}


		////////////////////////////////////////////////////////
		///////////////// 상품 옵션 재고 수정 ////////////////
		////////////////////////////////////////////////////////
		function quantityChange(flag, obj){
			var quantityObj = $(obj).parent().find("input[name='add_quantity[]']");

			var currQty = $(quantityObj).val();

			var stockSize = $(obj).parent().parent().parent().find("input[name='add_option[]']").val();
			var stockStorecode = $(obj).parent().parent().parent().find("input[name='store_code[]']").val();
			var stockProdcd = $("input[name='stock_prodcd']").val();
			var stockColorcd = $("input[name='stock_colorcd']").val();
			var stockQuantity = currQty;

			if(flag == '+'){
				stockQuantity = parseInt(currQty, 10) + 1;

			}else if(flag == '-'){
				if(currQty > 1){
					stockQuantity = parseInt(currQty, 10) - 1;
				}
			}

			$.ajax({
				type: "POST",
				url: "../front/ajax_get.reserve.date.php",
				data : { mode : 'stockChk', quantity : stockQuantity, prodcode : stockProdcd, colorcode : stockColorcd, size : stockSize, storecode : stockStorecode, flag : flag },
				dataType : 'json'
			}).done( function( data ){
				if(data.flag){
					$(quantityObj).val( data.quan );
				}else{
					alert(data.str);
					$(quantityObj).val( data.quan );
				}
			});
		}
		$(document).on('click', '.addOption-btn-plus', function(){
			quantityChange("+", $(this));
		})

		$(document).on('click', '.addOption-btn-minus', function(){
			quantityChange("-", $(this));
		})


		/////////////////////////////////////////////////////////////////////////////////
		///////// 구매 / 장바구니 액션시 픽업, 수령일시 필수값 추가 체크 /////////
		/////////////////////////////////////////////////////////////////////////////////
		function chkOptionFlag(){
			var msg = "";
			if(global_delivery_type == '1'){
				if($(".required_reservation_date").val()){
					msg = "succ";
				}else{
					msg = "수령일을 선택하지 않으셨습니다.";
				}
			}else if(global_delivery_type == '2'){
				if($("select[name='get_address']").val()){
					msg = "succ";
				}else{
					msg = "수령 주소를 입력하지 않으셨습니다.";
				}
			}else{
				msg = "succ";
			}

			return msg;
		}



		////////////////////////////////////////////////////
		///////// 선택 완료 매장명 레이어 출력 /////////
		///////////////////////////////////////////////////
		$(document).on('mouseover', '.CLS_store_selection_done', function(){
			//$(this).prev().css("left", (parseInt($(".hero-info-option-table").offset().left, 10) + ($(this).prev().width() / 2)) + "px").show();
			$(this).prev().css("margin-left", "-"+ (parseInt($(this).prev().width(), 10)+25) + "px");
			$(this).prev().show();
		})
		$(document).on('mouseout', '.CLS_store_selection_done', function(){
			$(this).prev().hide();
		})

	});



	////////////////////////////////////////////////////////
	/////////////////////// 매장 선택 /////////////////////
	////////////////////////////////////////////////////////
	function storeSelectData(){
		var chkPassSelection = true;
		var msg = "매장을 선택하셨습니다.";
		var storeDataPut = "";
		if(chkPassSelection && !$(".CLS_select_storecode").val()){
			chkPassSelection = false;
			msg = "매장을 선택하지 않으셨습니다.";
		}else{
			chkPassSelection = true;
		}

		if(global_delivery_type == '1'){
			if(chkPassSelection && !$(".CLS_select_reservation_date").val()){
				chkPassSelection = false;
				msg = "픽업 방문일을 선택하지 않으셨습니다.";
			}else{
				storeDataPut = "픽업 방문일 : " + $(".CLS_select_reservation_date").val();
			}
		}else if(global_delivery_type == '2'){
			var postVal = $("input[name='post']").val();
			var raddr1Val = $("input[name='raddr1']").val();
			var raddr2Val = $("input[name='raddr2']").val();
			if(chkPassSelection && (!postVal || !raddr1Val || !raddr2Val)){
				chkPassSelection = false;
				msg = "당일수령 주소를 입력하지 않으셨습니다.";
			}else{
				storeDataPut = "우편번호 : " + postVal + "<br>주소 : " + raddr1Val + "  " + raddr2Val;
			}
		}

		var storeAddress = $(".CLS_select_storeaddr").val();
		if (global_delivery_type == '2' && storeAddress.indexOf('서울') == -1) {
			chkPassSelection = false;
			msg = "당일 수령 배송주소는 서울지역만 입력 가능합니다.";
		}

		$.ajax({
			type: "POST",
			url: "../front/ajax_get.reserve.date.php",
			data : { mode : 'dateFlag', delivery_type : global_delivery_type, sel_date : $(".CLS_select_reservation_date").val() },
			dataType : 'text'
		}).done( function( flag ){
			// 당일 수령의 경우 15시가 지나면 주문이 되지 않도록 하기 때문에 분기 처리 함
			if(chkPassSelection){
				if((global_delivery_type == '2' && flag == '1') || (global_delivery_type == '1' && flag == '1')){
					var settingOptionStoreHTML = "";
					settingOptionStoreHTML = "<div class = 'CLS_store_selection_done_layer'>매장명 : "+$(".CLS_select_storename").val()+"<br>"+storeDataPut+"</div><span class = 'CLS_store_selection_done' style = 'cursor:pointer;'>[완료]</span>";
					settingOptionStoreHTML += "<input type = 'hidden' name = 'store_code[]' value = '"+$(".CLS_select_storecode").val()+"'>";

					if(global_delivery_type == '1'){
						settingOptionStoreHTML += "<input type = 'hidden' name = 'reservation_date[]' value = '"+$(".required_reservation_date").val()+"'>";
					}else if(global_delivery_type == '2'){
						settingOptionStoreHTML += "<input type = 'hidden' name = 'post_code[]' value = '"+$("#post").val()+"'>";
						settingOptionStoreHTML += "<input type = 'hidden' name = 'address1[]' value = '"+$("#raddr1").val()+"'>";
						settingOptionStoreHTML += "<input type = 'hidden' name = 'address2[]' value = '"+$("#raddr2").val()+"'>";
					}

					$(".store_selection_area:eq("+global_selection_store_idx+")").html(settingOptionStoreHTML);


					alert("["+$(".CLS_select_storename").val()+"] " + msg);
					$('.pop_stock_detail').fadeOut();
					return false;
				}else{
					if(global_delivery_type == '1' && flag == '0'){
						alert("주문 가능한 시간이 지났습니다. 다음날로 선택 해 주세요.");
					}else if(global_delivery_type == '2' && flag == '0'){
						alert("주문 가능한 시간이 지났습니다.( 매일 15시 )");
						$(".hero-info-option-table").html('');
					}
				}
			}else{
				alert(msg);
			}

		});


	}




	//////////////////////////////////////////////////
	////// 장바구니 / 구매시 매장 선택 확인 //////
	//////////////////////////////////////////////////
	function optionStoreSettingChk(){
		var chkPassSelection = true;

		if(global_delivery_type == '1' || global_delivery_type == '2'){
			if(  ($("input[name='store_code[]']").length != $("input[name='add_quantity[]']").length)   ||   ($("input[name='store_code[]']").length != $("input[name='add_option[]']").length)  ){
				chkPassSelection = false;
			}
		}

		return chkPassSelection;
	}


	/////////////////////////////////////////
	////// 당일 수령 주소 입력 팝업 //////
	/////////////////////////////////////////
	function openDaumPostcode() {
		new daum.Postcode({
			oncomplete: function(data) {
				var address = data.address;
				if (address.indexOf('서울') != -1) {
					$("#post5").val(data.zonecode);
					$("#rpost1").val(data.postcode1);
					$("#rpost2").val(data.postcode2);
					$("#raddr1").val(data.address);

					$("#raddr2").val('');
					$("#raddr2").focus();
					$("#post").val( data.zonecode );
				} else {
					$("#post5").val('');
					$("#rpost1").val('');
					$("#rpost2").val('');
					$("#raddr1").val('');

					$("#raddr2").val('');
					$("#post").val( '' );
					alert("당일 수령 배송주소는 서울지역만 입력 가능합니다.");
				}
			}
		}).open();
	}


	// 쿠폰 다운로드
	$(document).on( 'click', '.CLS_coupon_download', function( event ) {
		var coupon = $(this).attr('data-coupon');
		var coupon_button = $(this);

		$.ajax({
			type: "POST",
			url: "../front/ajax_coupon_download_proc.php",
			data : { coupon : coupon },
			dataType : 'json'
		}).done( function( data ){
			alert(data.msg);
		});
	});

</SCRIPT>


<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td>
			<?php
			/*if (strlen($_cdata->detail_type)==6 && $_cdata->detail_type[5]=="U") {
                $sql = "SELECT leftmenu,body,code FROM tbldesignnewpage ";
                $sql.= "WHERE type='prdetail' AND (code='{$code}' OR code='ALL') AND leftmenu='Y' ";
                $sql.= "ORDER BY code ASC LIMIT 1 ";
                $result=pmysql_query($sql,get_db_conn());
                $row=pmysql_fetch_object($result);
                $_ndata=$row;
                pmysql_free_result($result);
                if($_ndata) {
                    $body=$_ndata->body;
                    $body=str_replace("[DIR]",$Dir,$body);
                    include($Dir.TempletDir."product/detail_U.php");

                } else {
                    include($Dir.TempletDir."product/detail_".substr($_cdata->detail_type,0,5).".php");
                }
            }else{
                if ( false ) {
                    include($Dir.TempletDir."product/detail_TEM001_optionback.php");
                } else {*/
			include($Dir.TempletDir."product/detail_{$_cdata->detail_type}_ykm.php");
			/*}

        }*/
			?>
		</td>
	</tr>
</table>

<form name=couponform method=get action="<?=$_SERVER['PHP_SELF']?>">
	<input type=hidden name=mode value="">
	<input type=hidden name=coupon_code value="">
	<input type=hidden name=productcode value="<?=$productcode?>">
	<?=($brandcode>0?"<input type=hidden name=brandcode value=\"{$brandcode}\">\n":"")?>
</form>
<form name=idxform method=get action="<?=$_SERVER['PHP_SELF']?>">
	<input type=hidden name=productcode value="<?=$productcode?>">
	<input type=hidden name=sort value="<?=$sort?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type=hidden name=qnablock value="<?=$qnablock?>">
	<input type=hidden name=qnagotopage value="<?=$qnagotopage?>">
	<?=($brandcode>0?"<input type=hidden name=brandcode value=\"{$brandcode}\">\n":"")?>
</form>
<form name=wishform method=post action="<?=$Dir.FrontDir?>confirm_wishlist.php" target="confirmwishlist">
	<input type=hidden name=productcode value="<?=$productcode?>">
</form>

<?php if($_pdata->vender>0){?>
	<form name=custregminiform method=post>
		<input type=hidden name=sellvidx value="<?=$_vdata->vender?>">
		<input type=hidden name=memberlogin value="<?=(strlen($_ShopInfo->getMemid())>0?"Y":"N")?>">
	</form>
<?php }?>
<!-- 카테고리 -->
<script>
	// 카테고리 셀렉트

	$(document).on( "click", ".cate_select .cate_select-options li", function( e ) {
		var listUrl = '../front/productlist.php?code='+$(this).attr('rel');
		location.href = listUrl;
	});
	$(document).ready( function() { // 옵션, 수량 초기화
		if( $('.CLS_option_value').length > 0  ){
			$('.CLS_option_value').each( function( idx, obj ){
				$(this).val("");
			});
		}
		$('#quantity').val('1');
	});
</script>

<!-- 옵션 스크립트 -->
<script>
</script>
<!-- //옵션 스크립트 -->



<div id="create_openwin" style="display:none"></div>
<?php
/*
<!-- WIDERPLANET  SCRIPT START 2016.3.23 -->
<div id="wp_tg_cts" style="display:none;"></div>
<script type="text/javascript">
var wptg_tagscript_vars = wptg_tagscript_vars || [];
wptg_tagscript_vars.push(
(function() {
	return {
		wp_hcuid:"",
		ti:"24558",
		ty:"Item",
		device:"web"
		,items:[{i:"<?=$_pdata->productcode?>",	t:"<?=$_pdata->productname?>"}]
		};
}));
</script>
<script type="text/javascript" async src="//cdn-aitg.widerplanet.com/js/wp_astg_4.0.js"></script>
<!-- // WIDERPLANET  SCRIPT END 2016.3.23 -->
*/
?>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
