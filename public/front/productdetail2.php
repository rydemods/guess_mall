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
//exdebug($aaa = exchageRate(10));
$mode=$_REQUEST["mode"];
$coupon_code=$_REQUEST["coupon_code"];
//exdebug($_ShopInfo->getMemid());
$code=$_REQUEST["code"];
$productcode=$_REQUEST["productcode"];
//exdebug($productcode);

##### VIP 상품일 경우 회원등급 체크
$sql_prd_vip = "SELECT vip_product, staff_product FROM tblproduct WHERE productcode = '{$productcode}' ";
list($prd_vip_type, $staff_product) = pmysql_fetch(pmysql_query($sql_prd_vip));

if($prd_vip_type && ($member_group_level < $vip_limit_level)){
	alert_go("해당상품은 VIP 전용 상품 입니다.","{$Dir}main/main.php");
}
##### //VIP 상품일 경우

if(!$_ShopInfo->getStaffType() && $staff_product){
	alert_go("해당상품은 STAFF 전용 상품 입니다.","{$Dir}main/main.php");
}
//exdebug($_ShopInfo->getStaffType());
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

$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝
$_cdata="";
$_pdata="";
if(strlen($productcode)==18) {
	//$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	//$result=pmysql_query($sql,get_db_conn());

	$sql = "
		SELECT
		a.*,b.c_maincate,b.c_category
		FROM tblproductcode a
		,tblproductlink b
		WHERE a.code_a||a.code_b||a.code_c||a.code_d = b.c_category
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

	//if($row=pmysql_fetch_object($result)) {
		//$_cdata=$row;
		if($cateProduct) {
		if($mainCate) $_cdata=$mainCate;
		else $_cdata=$cateProduct[0];

		// if($row->group_code=="NO") {	//숨김 분류
		// 	alert_go('판매가 종료된 상품입니다.',$Dir.MainDir."main.php");
		// } else if($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
		// 	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
		// 	exit;
		// } else if(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
		// 	alert_go('해당 분류의 접근 권한이 없습니다.',-1);
		// }
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
					Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
					exit;
				}else if(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
					alert_go('해당 분류의 접근 권한이 없습니다.',-1);
				}
			}
			alert_go('판매가 종료된 상품입니다.',$Dir.MainDir."main.php");
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
		alert_go('해당 분류가 존재하지 않습니다.',$Dir.MainDir."main.php");
	}
	pmysql_free_result($result);

	$sql = "SELECT a.* ";
	$sql.= "FROM view_tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode='{$productcode}' AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$_pdata=$row;
		#### 오늘의 특가, 타임세일에 의한 가격 $salePrice
	    $salePrice = getSpeDcPrice($productcode);
	    if($salePrice > 0){
	    	$_pdata->sellprice = $salePrice;
	    }
	    ##### //오늘의 특가, 타임세일에 의한 가격

		$_pdata->brand += 0;
		$sql = "SELECT * FROM tblproductbrand ";
		$sql.= "WHERE bridx='{$_pdata->brand}' ";
		$bresult=pmysql_query($sql,get_db_conn());
		$brow=pmysql_fetch_object($bresult);
		$_pdata->brandcode = $_pdata->brand;
		$_pdata->brand = $brow->brandname;

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
					$sql = "SELECT pridx,productcode,productname,sellprice,quantity,tinyimage FROM view_tblproduct ";
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
	alert_go('해당 상품 정보가 존재하지 않습니다.',$Dir.MainDir."main.php");
}

/*if($mode=="coupon" && strlen($coupon_code)==8 && strlen($productcode)==18) {	//쿠폰 발급
	if(strlen($_ShopInfo->getMemid())==0) {	//비회원
		alert_go('로그인 후 쿠폰 다운로드가 가능합니다.',$Dir.FrontDir."login.php?chUrl=".getUrl());
	} else {
		$sql = "SELECT * FROM tblcouponinfo ";
		if($_pdata->vender>0) {
			$sql.= "WHERE (vender='0' OR vender='{$_pdata->vender}') ";
		} else {
			$sql.= "WHERE vender='0' ";
		}
		$sql.= "AND coupon_code='{$coupon_code}' ";
		$sql.= "AND display='Y' AND issue_type='Y' AND detail_auto='Y' ";
		$sql.= "AND (date_end>'".date("YmdH")."' OR date_end='') ";
		$sql.= "AND ((use_con_type2='Y' AND productcode IN ('ALL','".substr($code,0,3)."000000000','".substr($code,0,6)."000000','".substr($code,0,9)."000','{$code}','{$productcode}')) OR (use_con_type2='N' AND productcode NOT IN ('".substr($code,0,3)."000000000','".substr($code,0,6)."000000','".substr($code,0,9)."000','{$code}','{$productcode}'))) ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->issue_tot_no>0 && $row->issue_tot_no<$row->issue_no+1) {
				$onload="<script>alert(\"모든 쿠폰이 발급되었습니다.\");</script>";
			} else {
				$date=date("YmdHis");
				if($row->date_start>0) {
					$date_start=$row->date_start;
					$date_end=$row->date_end;
				} else {
					$date_start = substr($date,0,10);
					$date_end = date("Ymd23",strtotime("+".abs($row->date_start)." day"));
				}
				$sql = "INSERT INTO tblcouponissue (
				coupon_code	,
				id			,
				date_start	,
				date_end	,
				date		) VALUES (
				'{$coupon_code}',
				'".$_ShopInfo->getMemid()."',
				'{$date_start}',
				'{$date_end}',
				'{$date}' )";
				pmysql_query($sql,get_db_conn());
				if(!pmysql_errno()) {
					$sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 ";
					$sql.= "WHERE coupon_code = '{$coupon_code}'";
					pmysql_query($sql,get_db_conn());

					$onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
				} else {
					if($row->repeat_id=="Y") {	//동일인 재발급이 가능하다면,,,,
						$sql = "UPDATE tblcouponissue SET ";
						if($row->date_start<=0) {
							$sql.= "date_start	= '{$date_start}', ";
							$sql.= "date_end	= '{$date_end}', ";
						}
						$sql.= "used		= 'N' ";
						$sql.= "WHERE coupon_code='{$coupon_code}' ";
						$sql.= "AND id='".$_ShopInfo->getMemid()."' ";
						pmysql_query($sql,get_db_conn());
						$onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
					} else {
						$onload="<script>alert(\"이미 쿠폰을 발급받으셨습니다.\\n\\n해당 쿠폰은 재발급이 불가능합니다.\");</script>";
					}
				}
			}
		} else {
			$onload="<script>alert(\"해당 쿠폰은 사용 가능한 쿠폰이 아닙니다.\");</script>";
		}
		pmysql_free_result($result);
	}
}*/
//exdebug($_data->coupon_ok);
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

if(strlen($productcode)==18) {
	$viewproduct=$_COOKIE["ViewProduct"];
	if(ord($viewproduct)==0 || strpos($viewproduct,",{$productcode},")===FALSE) {
		if(ord($viewproduct)==0) {
			$viewproduct=",{$productcode},";
		} else {
			$viewproduct=",".$productcode.$viewproduct;
		}
	} else {
		$viewproduct=str_replace(",{$productcode}","",$viewproduct);
		$viewproduct=",".$productcode.$viewproduct;
	}
	$viewproduct=substr($viewproduct,0,571);
	setcookie("ViewProduct",$viewproduct,0,"/".RootPath);
}


//상품 상세 공통 이벤트 관리
if(strlen($_cdata->detail_type)==5) {	//개별디자인이 아닐 경우
	$sql = "SELECT * FROM tbldesignnewpage WHERE type='detailimg' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$row->body=str_replace("[DIR]",$Dir,$row->body);
		$design_type=$row->code;
		$detailimg_eventloc=$row->leftmenu;
		$detailimg_body="<table border=0 cellpadding=0 cellspacing=0>\n";
		if($design_type=="1") {	//이미지 타입
			$detailimg_body.="<tr><td align=center><img src=\"".$Dir.DataDir."shopimages/etc/{$row->filename}\" border=0></td></tr>\n";
		} else if($design_type=="2") {	//html 타입
			$detailimg_body.="<tr><td align=center>{$row->body}</td></tr>\n";
		}
		$detailimg_body.="</table>\n";
	}
	pmysql_free_result($result);
}

//추천관련상품
/* coll_loc => 0:사용안함, 1:상세화면 상단 위치, 2:상세화면 하단 위치, 3:상세화면 오른쪽 위치 */

if($_data->coll_loc>0) {
	$sql = "SELECT collection_list FROM tblcollection ";
	$sql.= "WHERE (productcode='".substr($code,0,3)."000000000' ";
	$sql.= "OR productcode='".substr($code,0,6)."000000' OR productcode='".substr($code,0,9)."000' ";
	$sql.= "OR productcode='".substr($code,0,12)."' OR productcode='{$productcode}') ";
	$sql.= "ORDER BY productcode DESC LIMIT 1 ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$collection_list=$row->collection_list;
	pmysql_free_result($result);

	if(ord($collection_list)) {

		$codes = explode(',',$collection_list);
		$collection=str_replace(",","','",$collection_list);
		$sql = "SELECT a.productcode,a.productname,a.sellprice,a.maximage,a.tinyimage,a.etctype,a.reserve,a.reservetype,a.consumerprice,a.option_price,a.tag,a.quantity,a.selfcode
		FROM view_tblproduct AS a
		LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode
		WHERE a.productcode IN ('{$collection}')
		AND a.display='Y' AND a.productcode!='{$productcode}'
		AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."')
		ORDER BY FIELD(a.productcode,'{$collection}') LIMIT ".$_data->coll_num;
		$result=pmysql_query($sql,get_db_conn());
		$collcnt=$_data->coll_num;

		$collection_body="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" ";
		if($_data->coll_loc=="3") {
			$collection_body.="width=\"100%\" style=\"table-layout:fixed\">\n";
			$collection_body.="<tr>\n";
			$collection_body.="	<td style=\"padding:5;border:#dddddd solid 1\">\n";
			$collection_body.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
		} else {
			$collection_body.="width=100%>";
			$collection_body.="<tr>\n";
			$collection_body.="	<td width=100% style=\"padding:5\">\n";
			$collection_body.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$collection_body.="	<tr>\n";
		}
		$tag_detail_count=2;
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			if($_data->coll_loc=='1'){
				$coll_loc_type1[]=	$row;
			}else{

				if($_data->coll_loc=="3") {
					if($i>0) {
						$collection_body.="<tr><td height=\"3\"></td></tr>\n";
						$collection_body.="<tr>\n";
						$collection_body.="	<td align=\"center\">";
						$collection_body.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\" style=\"table-layout:fixed\"><tr><td height=\"1\" bgcolor=\"#dddddd\"></td></tr></table>\n";
						$collection_body.="	</td>\n";
						$collection_body.="</tr>\n";
						$collection_body.="<tr><td height=\"5\"></td></tr>\n";
					} else {
						$collection_body.="<tr><td height=\"3\"></td></tr>\n";
					}
					$collection_body.="<tr>\n";
					$collection_body.="	<td align=center valign=\"top\">\n";
					$collection_body.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\" id=\"R{$row->productcode}\" onmouseover=\"quickfun_show(this,'R{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'R{$row->productcode}','none')\">\n";
					$collection_body.="<col width=75></col><col width=1></col><col></col>\n";
				} else {
					if($i>0) $collection_body.="<td width=\"5\" nowrap></td>\n";
					$collection_body.="	<td width=\"".ceil(100/$collcnt)."%\" valign=\"top\">";
					$collection_body.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\" id=\"R{$row->productcode}\" onmouseover=\"quickfun_show(this,'R{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'R{$row->productcode}','none')\">\n";
				}

				$collection_body.="	<tr>\n";
				$collection_body.="		<td align=\"center\" valign=middle>\n";
				$collection_body.= "	<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode={$row->productcode}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$collection_body.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($width[0]>$width[1]) $collection_body.="width=70";
					else $collection_body.="height=70";
				} else {
					$collection_body.= "<img src=\"{$Dir}images/no_img.gif\" width=\"70\" border=\"0\" align=\"center\"";
				}
				$collection_body.= "		></A></td>";
				//$collection_body.="		\n";

				if($_data->coll_loc!="3") {
					$collection_body.="	</tr>\n";
					$collection_body.="	<tr><td height=\"5\"></td></tr>\n";
					$collection_body.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','R','{$row->productcode}','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
					$collection_body.="	<tr>";
				} else {
					$collection_body.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','R','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				}

				$collection_body.="		<td ".($_data->coll_loc!="3"?"align=\"center\"":"")." valign=middle style=\"word-break:break-all;\">";
				$collection_body.="		<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode={$row->productcode}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";

				if($row->consumerprice!=0) {
					if($_data->coll_loc!="3") {
						$collection_body.="		</td>\n";
						$collection_body.="	</tr>\n";
						$collection_body.="	<tr>\n";
						$collection_body.="		<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\">";
					} else {
						$collection_body.="		<BR>";
					}

					$collection_body.= "<img src=\"{$Dir}images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원";
				}

				if($_data->coll_loc!="3") {
					$collection_body.="		</td>\n";
					$collection_body.="	</tr>\n";
					$collection_body.="	<tr>\n";
					$collection_body.="		<td align=\"center\">";
				} else {
					$collection_body.="		<BR>";
				}
				$collection_body.="		<FONT class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$collection_body.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$collection_body.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $collection_body.="(기본가)";
				} else {
					$collection_body.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (ord($row->option_price)==0) $collection_body.= number_format($row->sellprice)."원";
					else $collection_body.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $collection_body.= soldout();

				if($row->reserve!=0) {
					if($_data->coll_loc!="3") {
						$collection_body.="		</font></td>\n";
						$collection_body.="	</tr>\n";
						$collection_body.="	<tr>\n";
						$collection_body.="		<td align=\"center\" style=\"word-break:break-all;\" class=\"prreserve\">";
					} else {
						$collection_body.="		<BR>";
					}
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$collection_body.= "<img src=\"{$Dir}images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원";
				}

				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$tag_detail_count;$ii++) {
					$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
					if(ord($taglist[$ii])) {
						if($jj==0) {
							if($_data->coll_loc!="3") {
								$collection_body.="		</font></td>\n";
								$collection_body.="	</tr>\n";
								$collection_body.="	<tr>\n";
								$collection_body.="		<td align=\"center\" style=\"word-break:break-all;\">";
							} else {
								$collection_body.="		<BR>";
							}
							$collection_body.= "<img src=\"{$Dir}images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='{$taglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$taglist[$ii]}</font></a>";
						}
						else {
							$collection_body.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='{$taglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$taglist[$ii]}</font></a>";
						}
						$jj++;
					}
				}


				$collection_body.="		</font></td>\n";


				$collection_body.="	</tr>\n";
				$collection_body.="	</table>\n";
				$collection_body.="	</td>\n";
				if($_data->coll_loc=="3") {
					$collection_body.="</tr>\n";
				}

				$i++;
			}
		}
		pmysql_free_result($result);
		if($_data->coll_loc!="3") {
			if($i!=$collcnt) {
				for($j=$i;$j<$collcnt;$j++) {
					$collection_body.="<td width=\"".ceil(100/$collcnt)."%\" align=\"center\"></td>";
				}
			}
			$collection_body.="	</tr>\n";
		}
		$collection_body.="	</table>\n";
		$collection_body.="	</td>\n";
		$collection_body.="</tr>\n";
		$collection_body.="</table>\n";
	}
}

//쿠폰을 사용할 경우
if($_data->coupon_ok=="Y") {
	$sql = "SELECT * FROM tblcouponinfo ";
	if($_pdata->vender>0) {
		$sql.= "WHERE (vender='0' OR vender='{$_pdata->vender}') ";
	} else {
		$sql.= "WHERE vender='0' ";
	}
	$sql.= "AND display='Y' AND issue_type='Y' AND detail_auto='Y' ";
	$sql.= "AND (date_end>'".date("YmdH")."' OR date_end='') ";
	$sql.= "AND ((use_con_type2='Y' AND productcode IN ('ALL','".substr($code,0,3)."000000000','".substr($code,0,6)."000000','".substr($code,0,9)."000','{$code}','{$productcode}')) OR (use_con_type2='N' AND productcode NOT IN ('".substr($code,0,3)."000000000','".substr($code,0,6)."000000','".substr($code,0,9)."000','{$code}','{$productcode}'))) ";
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		if($row->date_start>0) {
			$date2 = substr($row->date_start,4,2)."/".substr($row->date_start,6,2)." ~ ".substr($row->date_end,4,2)."/".substr($row->date_end,6,2);
		} else {
			$date2 = date("m/d")." ~ ".date("m/d",strtotime("+".abs($row->date_start)." day"));
		}

		if($i==0) {
			$coupon_body="<table border=0 cellpadding=0 cellspacing=0>\n";
			$couponbody1=$coupon_body;
			$couponbody2=$coupon_body;
		}
		$tmpcouponbody="<tr><td height=\"16\"><font style=\"font-size:8pt;\">* {$row->description}</font></td></tr>\n";
		$coupon_body.=$tmpcouponbody;
		$couponbody1.=$tmpcouponbody;
		$tmpcouponbody="";
		$tmpcouponbody.="<tr><td>";
		if(file_exists($Dir.DataDir."shopimages/etc/COUPON{$row->coupon_code}.gif")) {
			$tmpcouponbody.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"352\" style=\"table-layout:fixed;cursor:hand;\">\n";
			$tmpcouponbody.="<tr>\n";
			$tmpcouponbody.="	<td onclick=\"issue_coupon('{$row->coupon_code}')\"><a href=\"javascript:issue_coupon('{$row->coupon_code}')\"><img src=\"".$Dir.DataDir."shopimages/etc/COUPON{$row->coupon_code}.gif\" border=0></a></td>\n";
			$tmpcouponbody.="</tr>\n";
			$tmpcouponbody.="<tr><td align=\"right\"><A HREF=\"javascript:issue_coupon('{$row->coupon_code}')\"><IMG SRC=\"{$Dir}images/common/coupon_download.gif\" border=\"0\"></A></td></tr>\n";
			$tmpcouponbody.="</table>\n";
		} else {
			$tmpcouponbody.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"352\" style=\"table-layout:fixed;\">\n";
			$tmpcouponbody.="<col width=\"5\"></col>\n";
			$tmpcouponbody.="<col width=></col>\n";
			$tmpcouponbody.="<col width=\"5\"></col>\n";
			$tmpcouponbody.="<tr style=\"cursor:hand;\" onclick=\"issue_coupon('{$row->coupon_code}')\">\n";
			$tmpcouponbody.="	<td colspan=\"3\"><IMG SRC=\"{$Dir}images/common/coupon_table01.gif\" border=\"0\"></td>\n";
			$tmpcouponbody.="</tr>\n";
			$tmpcouponbody.="<tr style=\"cursor:hand;\" onclick=\"issue_coupon('{$row->coupon_code}')\">\n";
			$tmpcouponbody.="	<td background=\"{$Dir}images/common/coupon_table02.gif\"><IMG SRC=\"{$Dir}images/common/coupon_table02.gif\" border=\"0\"></td>\n";
			$tmpcouponbody.="	<td width=\"100%\" style=\"padding:3pt;\" background=\"{$Dir}images/common/coupon_bg.gif\">\n";
			$tmpcouponbody.="	<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\n";
			$tmpcouponbody.="	<tr>\n";
			$tmpcouponbody.="		<td style=\"padding-bottom:4pt;\"><IMG SRC=\"{$Dir}images/common/coupon_title{$row->sale_type}.gif\" border\"0\"></td>\n";
			$tmpcouponbody.="	</tr>\n";
			$tmpcouponbody.="	<tr>\n";
			$tmpcouponbody.="		<td>\n";
			$tmpcouponbody.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$tmpcouponbody.="		<tr>\n";
			$tmpcouponbody.="			<td><font color=\"#585858\" style=\"font-size:11px;letter-spacing:-0.5pt;\">유효기간 : {$date2}</font>\n";
			if($row->bank_only=="Y") $tmpcouponbody.=" <font color=\"0000FF\">(현금결제만 가능)</font>";
			$tmpcouponbody.="			</td>\n";
			$tmpcouponbody.="		</tr>\n";
			$tmpcouponbody.="		</table>\n";
			$tmpcouponbody.="		</td>\n";
			$tmpcouponbody.="	</tr>\n";
			$tmpcouponbody.="	<tr>\n";
			$tmpcouponbody.="		<td>\n";
			$tmpcouponbody.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$tmpcouponbody.="		<tr>\n";
			$tmpcouponbody.="			<td width=\"100%\" align=\"right\"><font color=#FF5000 style=\"font-family:sans-serif;font-size:48px;line-height:45px\"><b><font color=\"#FF6600\" face=\"강산체\">".number_format($row->sale_money)."</font></b></td>\n";
			$tmpcouponbody.="			<td><IMG SRC=\"{$Dir}images/common/coupon_text{$row->sale_type}.gif\" border=\"0\"></td>\n";
			$tmpcouponbody.="		</tr>\n";
			$tmpcouponbody.="		</table>\n";
			$tmpcouponbody.="		</td>\n";
			$tmpcouponbody.="	</tr>\n";
			$tmpcouponbody.="	</table>\n";
			$tmpcouponbody.="	</td>\n";
			$tmpcouponbody.="	<td background=\"{$Dir}images/common/coupon_table04.gif\"><IMG SRC=\"{$Dir}images/common/coupon_table04.gif\" border=\"0\"></td>\n";
			$tmpcouponbody.="</tr>\n";
			$tmpcouponbody.="<tr style=\"cursor:hand;\" onclick=\"issue_coupon('{$row->coupon_code}')\">\n";
			$tmpcouponbody.="	<td colspan=\"3\"><IMG SRC=\"{$Dir}images/common/coupon_table03.gif\" border=\"0\"></td>\n";
			$tmpcouponbody.="</tr>\n";
			$tmpcouponbody.="<tr><td align=\"right\" colspan=\"3\"><A HREF=\"javascript:issue_coupon('{$row->coupon_code}')\"><IMG SRC=\"{$Dir}images/common/coupon_download.gif\" border=\"0\"></A></td></tr>\n";
			$tmpcouponbody.="</table>\n";
		}
		$tmpcouponbody.="</td></tr>\n";
		$coupon_body.=$tmpcouponbody;
		$couponbody1.=$tmpcouponbody;
		$couponbody2.=$tmpcouponbody;
		$tmpcouponbody="<tr><td height=\"10\"></td></tr>\n";
		$coupon_body.=$tmpcouponbody;
		$couponbody1.=$tmpcouponbody;
		$couponbody2.=$tmpcouponbody;
		$i++;
	}
	pmysql_free_result($result);
	if($i>0) {
		$coupon_body.="</table>\n";
		$couponbody1.="</table>\n";
		$couponbody2.="</table>\n";
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
		$deli_info_data=$_vdata->deli_info;
		$aboutdeliinfofile=$Dir.DataDir."shopimages/vender/aboutdeliinfo_{$_vdata->vender}.gif";
	} else {
		$deli_info_data=$_data->deli_info;
		$aboutdeliinfofile=$Dir.DataDir."shopimages/etc/aboutdeliinfo.gif";
	}
	if(ord($deli_info_data)) {
		$tempdeli_info=explode("=",$deli_info_data);
		if($tempdeli_info[0]=="Y") {
			if($tempdeli_info[1]=="TEXT") {			//텍스트형
				$allowedTags = "<h1><b><i><a><ul><li><pre><hr><blockquote><u><img><br><font>";

				if(ord($tempdeli_info[2]) || ord($tempdeli_info[3])) {
					$deli_info = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
					$deli_info.= "<tr>\n";
					$deli_info.= "	<td style=\"padding:10,15,10,15\">\n";
					$deli_info.= "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
					if(ord($tempdeli_info[2])) {	//배송정보 텍스트
						$deli_info.= "	<tr>\n";
						$deli_info.= "		<td><img src=\"{$Dir}images/common/detaildeliinfo_img1.gif\" border=0></td>\n";
						$deli_info.= "	</tr>\n";
						$deli_info.= "	<tr>\n";
						$deli_info.= "		<td style=\"line-height:14pt;padding-left:10\">\n";
						$deli_info.= "		".nl2br(strip_tags($tempdeli_info[2],$allowedTags))."\n";
						$deli_info.= "		</td>\n";
						$deli_info.= "	</tr>\n";
						$deli_info.= "	<tr><td height=15></td></tr>\n";
					}
					if(ord($tempdeli_info[3])) {	//교환/환불정보 텍스트
						$deli_info.= "	<tr>\n";
						$deli_info.= "		<td><img src=\"{$Dir}images/common/detaildeliinfo_img2.gif\" border=0></td>\n";
						$deli_info.= "	</tr>\n";
						$deli_info.= "	<tr>\n";
						$deli_info.= "		<td style=\"line-height:14pt;padding-left:10\">\n";
						$deli_info.= "		".nl2br(strip_tags($tempdeli_info[3],$allowedTags))."\n";
						$deli_info.= "		</td>\n";
						$deli_info.= "	</tr>\n";
						$deli_info.= "	<tr><td height=15></td></tr>\n";
					}
					$deli_info.= "	</table>\n";
					$deli_info.= "	</td>\n";
					$deli_info.= "</tr>\n";
					$deli_info.= "</table>\n";
				}
			} else if($tempdeli_info[1]=="IMAGE") {	//이미지형
				if(file_exists($aboutdeliinfofile)) {
					$deli_info = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
					$deli_info.= "<tr>\n";
					$deli_info.= "	<td align=center><img src=\"{$aboutdeliinfofile}\" align=absmiddle border=0></td>\n";
					$deli_info.= "</tr>\n";
					$deli_info.= "</table>\n";
				}
			} else if($tempdeli_info[1]=="HTML") {	//HTML로 입력
				if(ord($tempdeli_info[2])) {
					$deli_info = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
					$deli_info.= "<tr><td>{$tempdeli_info[2]}</td></tr>\n";
					$deli_info.= "</table>\n";
				}
			}
		}
	}
}

//리뷰관련 환경 설정
$reviewlist=$_data->ETCTYPE["REVIEWLIST"];
$reviewdate=$_data->ETCTYPE["REVIEWDATE"];
if(ord($reviewlist)==0) $reviewlist="N";

//리뷰등록
if($mode=="review_write") {
	function ReviewFilter($filter,$memo,&$findFilter) {
		//$use_filter = split(",",$filter);
		$use_filter = explode(",",$filter);
		$isFilter = false;
		for($i=0;$i<count($use_filter);$i++) {
			if (preg_match("/{$use_filter[$i]}/i",$memo)) {
				$findFilter = $use_filter[$i];
				$isFilter = true;
				break;
			}
		}
		return $isFilter;
	}
	
	$rname=$_POST["rname"];
	$rcontent=$_POST["rcontent"];
	$rsubject = $_POST["rsubject"];
	$rmarks=$_POST["rmarks"];

	###기존에 리뷰 작성시 첨부파일 추가 안되서 새로 작성함 20150527 원재###
	$imagepath2=$Dir.DataDir."shopimages/board/reviewbbs/";
	$userfile = $_FILES["rfile"];
	$upfile=$userfile["name"];

	if ($userfile['tmp_name']) {
		$ext = strtolower(pathinfo($userfile["name"], PATHINFO_EXTENSION));

		if(in_array($ext,array('gif','jpg','jpeg','bmp'))) {
			$uploadFile = time().".".$ext;
			move_uploaded_file($userfile["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/data/shopimages/board/reviewbbs/".$userfile ["name"]);
			chmod($imagepath2.$uploadFile,0664);
		} else {
			alert_go("gif와  jpg타입의 이미지만 업로드 가능합니다.", "{$_SERVER['HTTP_REFERER']}");
			$uploadFile = "";
		}

			if ($userfile["size"] > 838860){
		alert_go('해당 파일은 800K를 초과합니다.', "{$_SERVER['HTTP_REFERER']}");

		//$addFileColumn = ", upfile";
		//$addFileData = ", '".$uploadFile."'";

		}


	}

	if((strlen($_ShopInfo->getMemid())==0) && $_data->review_memtype=="Y") {
		alert_go('로그인을 하셔야 사용후기 등록이 가능합니다.',$Dir.FrontDir."login.php?chUrl=".getUrl());
	}
	if(ord($review_filter)) {	//사용후기 내용 필터링
		if(ReviewFilter($review_filter,$rcontent,$findFilter)) {
			alert_go("사용하실 수 없는 단어를 입력하셨습니다.({$findFilter})\\n\\n다시 입력하시기 바랍니다.",-1);
		}
	}



	$sql = "INSERT INTO tblproductreview (
	upfile,
	productcode	,
	id			,
	name		,
	marks		,
	date		,
	subject		,
	content		) VALUES (
	'{$upfile}',
	'{$productcode}',
	'".$_ShopInfo->getMemid()."',
	'{$rname}',
	'{$rmarks}',
	'".date("YmdHis")."',
	'".$rsubject."',
	'{$rcontent}')";
	pmysql_query($sql,get_db_conn());

	if($_data->review_type=="A") $msg="관리자 인증후 등록됩니다.";
	else $msg="등록되었습니다.";
	$rqry="productcode=".$productcode;
	if(ord($code)) $rqry.="&code=".$code;
	if(ord($sort)) $rqry.="&sort=".$sort;
	if(ord($brandcode)) $rqry.="&brandcode=".$brandcode;
	alert_go($msg,"{$_SERVER["PHP_SELF"]}?{$rqry}");
}
//리뷰등록 끝

//이전/다음 상품 관련
$qry = "WHERE 1=1 ";
if(strstr($_cdata->type,"T")) {	//가상분류
	$sql = "SELECT productcode FROM tblproducttheme WHERE code LIKE '{$likecode}%' ";
	$result=pmysql_query($sql,get_db_conn());
	$t_prcode="";
	while($row=pmysql_fetch_object($result)) {
		$t_prcode.=$row->productcode.",";
		$i++;
	}
	pmysql_free_result($result);
	$t_prcode=rtrim($t_prcode,',');
	$t_prcode=str_replace(',','\',\'',$t_prcode);
	$qry.= "AND a.productcode IN ('{$t_prcode}') ";

	$add_query="&code=".$code;
} else {	//일반분류
	$qry.= "AND a.productcode LIKE '{$likecode}%' ";
}
$qry.= "AND a.display='Y' ";

$tmp_sort=explode("_",$sort);
if($brandcode>0) {
	$qry.="AND a.brand='{$brandcode}' ";
	$add_query.="&brandcode=".$brandcode;
	$brand_link = "brandcode={$brandcode}&";

	$sql ="SELECT SUBSTR(a.productcode, 1, 3) AS code FROM view_tblproduct AS a ";
	$sql.="LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.="WHERE a.display='Y' AND a.brand='{$brandcode}' ";
	$sql.="AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$sql.="GROUP BY code ";
	$result=pmysql_query($sql,get_db_conn());
	$brand_qry = "";
	$leftcode = array();
	while($row=pmysql_fetch_object($result)) {
		$leftcode[] = $row->code;
	}
	if(count($leftcode)>0) {
		$brand_qry = "AND code_a IN ('".implode("','",$leftcode)."') ";
	}

	if($tmp_sort[0]=="reserve") {
		$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
	}
	$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
	$sql.= "a.tinyimage, a.date, a.etctype, a.option_price ";
	$sql.= $addsortsql;
	$sql.= "FROM view_tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production {$tmp_sort[1]} ";
	else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname {$tmp_sort[1]} ";
	else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice {$tmp_sort[1]} ";
	else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort {$tmp_sort[1]} ";
	else $sql.= "ORDER BY a.productname ";
} else {
	if($tmp_sort[0]=="reserve") {
		$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
	}
	$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
	if($_cdata->sort=="date2") $sql.="CASE WHEN a.quantity<=0 THEN '11111111111111' ELSE a.date END as date, ";
	$sql.= "a.tinyimage, a.etctype, a.option_price ";
	$sql.= $addsortsql;
	$sql.= "FROM view_tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production {$tmp_sort[1]} ";
	else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname {$tmp_sort[1]} ";
	else if($tmp_sort[0]=="sellprice") $sql.= "ORDER BY a.sellprice {$tmp_sort[1]} ";
	else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort {$tmp_sort[1]} ";
	else {
		if(ord($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
			$sql.= "ORDER BY date DESC ";
		} else if($_cdata->sort=="productname") {
			$sql.= "ORDER BY a.productname ";
		} else if($_cdata->sort=="production") {
			$sql.= "ORDER BY a.production ";
		} else if($_cdata->sort=="price") {
			$sql.= "ORDER BY a.sellprice ";
		}
	}
}
$result=pmysql_query($sql,get_db_conn());
$arr_productcode=array();
$isprcode=false;
while($row=pmysql_fetch_object($result)) {
	if($productcode==$row->productcode) {
		$isprcode=true;
	} else {
		if($isprcode==false) {
			$arr_productcode["prev"]=$row->productcode;
		} else {
			$arr_productcode["next"]=$row->productcode;
			break;
		}
	}
}
pmysql_free_result($result);


//현재위치
$codenavi=($brandcode>0?getBCodeLoc($brandcode,$code):getCodeLoc2($code));

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

	$imagepath=$Dir.DataDir."shopimages/multi/";
	//$dispos=$row->multi_dispos;
	//$changetype=$_data->multi_changetype;
	$changetype='1'; //임시로 멀티이미지 타입을 변경 2015 05 04
	//exdebug($_data);
	$bgcolor=$_data->multi_bgcolor;

	$sql = "SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$multi_imgs=array(&$row->primg01,&$row->primg02,&$row->primg03,&$row->primg04,&$row->primg05,&$row->primg06,&$row->primg07,&$row->primg08,&$row->primg09,&$row->primg10);

		$tmpsize=explode("",$row->size);
		$insize="";
		$updategbn="N";

		$y=0;
		for($i=0;$i<10;$i++) {
			if(ord($multi_imgs[$i])) {
				$yesimage[$y]=$multi_imgs[$i];
				if(ord($tmpsize[$i])==0) {
					$size=getimagesize($Dir.DataDir."shopimages/multi/".$multi_imgs[$i]);
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

		//$maxnumsize=($maxsize/60);
		//if($y>=5 && $maxnumsize<=5) $y=5;
		//else if($maxnumsize<$y) $y=$maxnumsize;

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


/*--------------------------------- 2014-07-08 05:15
상품 상세 FACEBOOK 공유  Start
------------------------------------*/
$faceboolMallUrl = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$msg = html_entity_decode(preg_replace('/\n/','', "'msg_facebook'=>'[{shopnm}] {goodsnm}',"));
$msg = preg_replace('/{shopnm}/i', $_data->shopname, $msg);
$msg = preg_replace('/{goodsnm}/i', $_pdata->productname, $msg);
$msg = preg_replace('/{goodsurl}/i', $faceboolMallUrl, $msg);
$encodedMsg = urlencode($msg);
$facebookurl = 'http://www.facebook.com/sharer.php?u='.urlencode($faceboolMallUrl.'&time='.time());

/*--------------------------------- 2014-12-20
상품 상세 twitter 공유  Start
------------------------------------*/

$twitterMsg = "[".$_data->shopname."] ".$_pdata->productname;
$twitterUrl = 'https://twitter.com/intent/tweet?url='.$faceboolMallUrl.'&text='.urlencode(iconv("EUC-KR","UTF-8",$twitterMsg));


### 수량 변경에 따른 가격변경
/*
$s_sql = "
	SELECT * FROM tblmembergroup_sale
	WHERE productcode='".$productcode."' AND group_code='".$_ShopInfo->memgroup."'
";
$s_res = pmysql_query($s_sql,get_db_conn());
while($s_row = pmysql_fetch_array($s_res)){
	$s_price[] = $s_row;
}
$s_cnt = pmysql_num_rows($s_res);
pmysql_free_result($s_res);
*/
### 관련 인기상품

$popular_product_sql = "
	SELECT productcode,productname,sellprice,maximage,vcnt
	FROM view_tblproduct
	WHERE productcode IN (SELECT c_productcode FROM tblproductlink WHERE c_category='{$code}')
	AND staff_product != '1'
	ORDER BY vcnt DESC
	OFFSET 0 LIMIT 4
";
$popular_product_res = pmysql_query($popular_product_sql, get_db_conn());
while($popular_product_row = pmysql_fetch_array($popular_product_res)){
	$popular_product[] = $popular_product_row;
}
pmysql_free_result($popular_product_res);

?>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--


/*
//오른쪽 마우스 버튼 방지
function right(e) {
   if(navigator.appName == 'Netscape' && (e.which == 3 || e.which == 2)) //브라우저가 Netscape 이고 누른 버튼이 마우스 의 오른쪽 버튼이라면
       return false; // false(즉 잘못된 호출로 인식) 시킵니다
   else if (navigator.appName == 'Microsoft Internet Explorer' && (event.button == 2 || event.button == 3))//브라우저가 Microsoft Internet Explorer 이고 누른 버튼이 마우스 의 오른쪽 버튼이라면 아래를 실행합니다.
   {
    alert("오른쪽 마우스 버튼은 사용 못합니다!"); //알림 메세지를 보여줍니다.
    return false; // false(즉 잘못된 호출로 인식) 시킵니다
     }
     return true; //나머지 버튼 즉 오른쪽 버튼이 아니라면 true 즉 정상적인 호출로 인식 시켜줍니다.
   }
document.onmousedown = right; //DOCUMENT 상에서 마우스가 눌러지면 right 라는 함수를 호출합니다.
if(document.layers) //layer 가 존제한다면 다음을 실행합니다.
  window.captureEvents(Event.MOUSEDOWN); //captureEvents() : 지정한 유형의 이벤트를 감지하도록 설정합니다. 즉 MOUSEDOWN 이벤트를 감지합니다
//window.onmousedown = right; //마우스가 눌러지면 right 라는 함수를 호출합니다.
//제한을 걸려면 body태그에  oncontextmenu="return false" 를추가해야 함
*/

function ClipCopy(url) {
	/*
	var tmp;
	tmp = window.clipboardData.setData('Text', url);
	if(tmp) {
		alert('주소가 복사되었습니다.');
	}
	*/
	var IE=(document.all)?true:false;
	if (IE) {
		if(confirm("현제 페이지의 클립보드에 복사하시겠습니까?"))
			window.clipboardData.setData("Text", url);
	} else {
		temp = prompt("현제 페이지의 주소입니다. Ctrl+C를 눌러 클립보드로 복사하세요", url);
	}
}

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

function change_quantity(gbn) {
	tmp=document.form1.quantity.value;
	if(gbn=="up") {
		tmp++;
	} else if(gbn=="dn") {
		if(tmp>1) tmp--;
	}
	var cons_qu = $("#constant_quantity").val();
	if (cons_qu != "" && cons_qu != "0"){
		if (cons_qu<tmp){
			alert('재고량이 부족 합니다.');
			return;
		}
	} else if(cons_qu == "0") {
		alert('품절 입니다.');
		return;
	}
	<?php  if($_pdata->assembleuse=="Y") { ?>
		if(getQuantityCheck(tmp)) {
			if(document.form1.assemblequantity) {
				document.form1.assemblequantity.value=tmp;
			}
			document.form1.quantity.value=tmp;
			setTotalPrice(tmp);
		} else {
			alert('구성상품 중 '+tmp+'보다 재고량이 부족한 상품있어서 변경을 불가합니다.');
			return;
		}
	<?php  } else { ?>
		//var tmp_price = $("#ID_goodsprice").val();
		var tmp_price = $("#ID_sellprice").val();

		document.form1.quantity.value=tmp;
		tmp_price = tmp_price == newOptionPrice(tmp_price,tmp) ? tmp_price : newOptionPrice(tmp_price,tmp);
		tmp_price = Number(tmp_price)*Number(tmp);
		setDeliPrice(tmp_price,tmp);
		setTotalPrice();
		$("#ID_goodsprice").val(tmp_price);
		$("#result_total_price").html("<span class='price_d'><strong>"+jsSetComa(tmp_price)+"원</strong></span>");


	<?php  } ?>
	//적립금
	<?php if($_pdata->reserve>0){ ?>
		var tmp_reserve = $("#ID_reserv").val();
		$("#ID_displyReserv").html(comma(tmp*tmp_reserve)+" point");
	<?php } ?>
}

function change_quantityOpt(gbn) {
	tmp=document.form1.quantity.value;
	if(gbn=="up") {
		tmp++;
	} else if(gbn=="dn") {
		if(tmp>1) tmp--;
	}
	var cons_qu = $("#constant_quantity").val();
	if (cons_qu != "" && cons_qu != "0"){
		if (cons_qu<tmp){
			alert('재고량이 부족 합니다.');
			return false;
		}
	} else if(cons_qu == "0") {
		alert('품절 입니다.');
		return false;
	}
	<?php  if($_pdata->assembleuse=="Y") { ?>
		if(getQuantityCheck(tmp)) {
			if(document.form1.assemblequantity) {
				document.form1.assemblequantity.value=tmp;
			}
			document.form1.quantity.value=tmp;
			setTotalPrice(tmp);
			return true;
		} else {
			alert('구성상품 중 '+tmp+'보다 재고량이 부족한 상품있어서 변경을 불가합니다.');
			return false;
		}
	<?php  } else { ?>
		//var tmp_price = $("#ID_goodsprice").val();
		//tmp_price = Number(tmp_price)*Number(tmp);
		//setDeliPrice(tmp_price,tmp);
		//$("#result_total_price").html(jsSetComa(tmp_price));
		document.form1.quantity.value=tmp;
		return true;
	<?php  } ?>
	//적립금
	<?php if($_pdata->reserve>0){ ?>
		var tmp_reserve = $("#ID_reserv").val();
		$("#ID_displyReserv").html(comma(tmp*tmp_reserve)+" point");
	<?php } ?>
}


function quantityKeyUp(num){
	 tmp=document.form1.quantity.value;

	 var cons_qu = $("#constant_quantity").val();
	if (cons_qu != "" && cons_qu != "0"){
		if (cons_qu<tmp){
			alert('재고량이 부족 합니다.');
			return;
		}
	} else if(cons_qu == "0") {
		alert('품절 입니다.');
		return;
	}
	<?php  if($_pdata->assembleuse=="Y") { ?>
		if(getQuantityCheck(tmp)) {
			if(document.form1.assemblequantity) {
				document.form1.assemblequantity.value=tmp;
			}
			document.form1.quantity.value=tmp; alert(tmp
			setTotalPrice(tmp);
		} else {
			alert('구성상품 중 '+tmp+'보다 재고량이 부족한 상품있어서 변경을 불가합니다.');
			return;
		}
	<?php  } else { ?>
		var tmp_price = $("#ID_goodsprice").val();
		tmp_price = Number(tmp_price)*Number(tmp);
		setDeliPrice(tmp_price,tmp);
		$("#result_total_price").html(jsSetComa(tmp_price));
		document.form1.quantity.value=tmp;
	<?php  } ?>

}


function check_login() {
	if(confirm("로그인이 필요한 서비스입니다. 로그인을 하시겠습니까?")) {
		document.location.href="<?=$Dir.FrontDir?>login.php?chUrl=<?=getUrl()?>";
	}
}

function review_write() {

}


<?php /*if($_data->coupon_ok=="Y") {?>
function issue_coupon(coupon_code){
	document.couponform.mode.value="coupon";
	document.couponform.coupon_code.value=coupon_code;
	document.couponform.submit();
}
<?php }*/?>

<?php if($_data->coupon_ok=="Y") {?>
function issue_coupon(coupon_code){
	/*if($_ShopInfo->getMemid()){
		alert("ok");
	}*/
	$.ajax({
		type: "POST", 
		url: "../front/prcoupon_proc.php", 
		data: "mode=coupon&productcode="+$("input[name='productcode']").val()+"&coupon_code="+coupon_code,
		dataType: "json"
	}).done(function ( msg ) {
		alert(msg.msg);
		if(msg.msgType == 0){
			location.href="../front/login.php";
		}
	});
}
<?php }?>


function CheckForm(gbn,temp2) {
	var itemCount = 0;
	/*if(gbn!="wishlist") {*/
	document.form1.optionArr.value = "";
	document.form1.quantityArr.value = "";
	document.form1.priceArr.value = "";
	if (typeof($("#quantity").val()) == "undefined" || $("#quantity").val() == null && typeof($("#option2").val())){
		$(".opt_list li").each(function(){
			var id = $(this).attr("id");
			var ex_id = id.split("-");
			document.form1.optionArr.value = document.form1.optionArr.value == "" ? ex_id[1] : document.form1.optionArr.value+"||"+ex_id[1];
			document.form1.quantityArr.value = document.form1.quantityArr.value == "" ? $("#quantityea-"+ex_id[1]).val(): document.form1.quantityArr.value+"||"+$("#quantityea-"+ex_id[1]).val();
			document.form1.priceArr.value = document.form1.priceArr.value == "" ? $("#itemPrice-"+ex_id[1]).attr("alt"): document.form1.priceArr.value+"||"+$("#itemPrice-"+ex_id[1]).attr("alt");
			itemCount++;
		});

		if (itemCount < 1){
			document.form1.optionArr.value = "";
			document.form1.quantityArr.value = "";
			document.form1.priceArr.value = "";
			alert('주문을 추가 하셔야 합니다.');
			$("#option1").focus();
			return;
		}
	} else {
		if ($("#quantity").val() < 1){
			alert('주문 수량을 확인하세요.');
		}
		$(".opt_list li").each(function(){
			var id = $(this).attr("id");
			var ex_id = id.split("-");
			document.form1.optionArr.value = document.form1.optionArr.value == "" ? ex_id[1] : document.form1.optionArr.value+"||"+ex_id[1];
			document.form1.quantityArr.value = document.form1.quantityArr.value == "" ? $("#quantityea-"+ex_id[1]).val(): document.form1.quantityArr.value+"||"+$("#quantityea-"+ex_id[1]).val();
			document.form1.priceArr.value = document.form1.priceArr.value == "" ? $("#itemPrice-"+ex_id[1]).attr("alt"): document.form1.priceArr.value+"||"+$("#itemPrice-"+ex_id[1]).attr("alt");
			itemCount++;
		});
		
		/*	jhjeong 2015-06-26
			detail_TEM001.php에서 quantity 의 기본수량을 1로 세팅(536 라인)하였기 때문에 위의 quantity 체크는 별 의미가 없어짐.
			옵션이 없는 상품은 수량만 체크하기 때문에 option1 이 있는 경우에만 체크하게 하였음.
		*/
		if(itemCount<1 && typeof(document.form1.option1)!="undefined"){
			//alert(itemCount);
			//alert(typeof($("#option1").val()));
			alert('주문 수량을 확인하세요..');
			return;
		}
	}
		/*
		if(document.form1.quantity.value.length==0 || document.form1.quantity.value==0) {
			alert("주문수량을 입력하세요.");
			document.form1.quantity.focus();
			return;
		}
		if(!IsNumeric(document.form1.quantity.value)) {
			alert("주문수량은 숫자만 입력하세요.");
			document.form1.quantity.focus();
			return;
		}
		if(miniq>1 && document.form1.quantity.value<=1) {
			alert("해당 상품의 구매수량은 "+miniq+"개 이상 주문이 가능합니다.");
			document.form1.quantity.focus();
			return;
		}*/
	/*}*/

	if(gbn=="ordernow") {

		if(typeof(document.form1.option1)!="undefined" && document.form1.option1.selectedIndex<2  && itemCount<1) {
			if (document.form1.option1.value == ""){
				alert('해당 상품의 옵션을 선택하세요.');
				document.form1.option1.focus();
				return;
			}

		}

		if(typeof(document.form1.option2)!="undefined" && document.form1.option2.selectedIndex<2  && itemCount<1) {
		alert('해당 상품의 옵션을 선택하세요..');
		document.form1.option2.focus();
		return;
		}

		if(typeof(document.form1.option1)!="undefined" && document.form1.option1.selectedIndex>=2 && itemCount<1) {
			temp2=document.form1.option1.selectedIndex-1;
			if(typeof(document.form1.option2)=="undefined") temp3=1;
			else temp3=document.form1.option2.selectedIndex-1;
			if(num[(temp3-1)*10+(temp2-1)]==0) {
			alert('해당 상품의 옵션은 품절되었습니다. 다른 옵션을 선택하세요');
			document.form1.option1.focus();
			return;
			}
		}

		if(typeof(document.form1.package_type)!="undefined" && typeof(document.form1.packagenum)!="undefined" && document.form1.package_type.value=="Y" && document.form1.packagenum.selectedIndex<2) {
		alert('해당 상품의 패키지를 선택하세요.');
		document.form1.packagenum.focus();
		return;
		}
	}

	if(temp2!="") {
		document.form1.opts.value="";
		try {
			for(i=0;i<temp2;i++) {
				if(document.form1.optselect[i].value==1 && document.form1.mulopt[i].selectedIndex==0) {
					alert('필수선택 항목입니다. 옵션을 반드시 선택하세요');
					document.form1.mulopt[i].focus();
					return;
				}
				document.form1.opts.value+=document.form1.mulopt[i].selectedIndex+",";
			}
		} catch (e) {}
	}

	if(typeof(document.form1.option1)!="undefined" && document.form1.option1.selectedIndex<2  && itemCount<1) {
		if (document.form1.option1.value == ""){
			alert('해당 상품의 옵션을 선택하세요.');
			document.form1.option1.focus();
			return;
		}

	}

	if(typeof(document.form1.option2)!="undefined" && document.form1.option2.selectedIndex<2  && itemCount<1) {
		alert('해당 상품의 옵션을 선택하세요..');
		document.form1.option2.focus();
		return;
	}

	if(typeof(document.form1.option1)!="undefined" && document.form1.option1.selectedIndex>=2 && itemCount<1) {
		temp2=document.form1.option1.selectedIndex-1;
		if(typeof(document.form1.option2)=="undefined") temp3=1;
		else temp3=document.form1.option2.selectedIndex-1;
		if(num[(temp3-1)*10+(temp2-1)]==0) {
			alert('해당 상품의 옵션은 품절되었습니다. 다른 옵션을 선택하세요');
			document.form1.option1.focus();
			return;
		}
	}

	if(typeof(document.form1.package_type)!="undefined" && typeof(document.form1.packagenum)!="undefined" && document.form1.package_type.value=="Y" && document.form1.packagenum.selectedIndex<2) {
		alert('해당 상품의 패키지를 선택하세요.');
		document.form1.packagenum.focus();
		return;
	}
	
	if(gbn=="ordernow") {
		document.form1.ordertype.value="ordernow";
		//////////////////
			document.form1.action="../front/basket.php";
			document.form1.submit();
		////////////////
	}
	
	if(gbn!="wishlist") {
		<?php  if($_pdata->assembleuse=="Y") { ?> // 무시해도 됨
		if(typeof(document.form1.assemble_type)=="undefined") {
			alert('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.');
			return;
		} else {
			if(document.form1.assemble_type.value.length>0) {
				arracassembletype = document.form1.assemble_type.value.split("|");
				document.form1.assemble_list.value="";

				for(var i=1; i<=arracassembletype.length; i++) {
					if(arracassembletype[i]=="Y") {
						if(document.getElementById("acassemble"+i).options.length<2) {
							alert('필수 구성상품의 상품이 없어서 구매가 불가능합니다.');
							document.getElementById("acassemble"+i).focus();
							return;
						} else if(document.getElementById("acassemble"+i).value.length==0) {
							alert('필수 구성상품을 선택해 주세요.');
							document.getElementById("acassemble"+i).focus();
							return;
						}
					}

					if(document.getElementById("acassemble"+i)) {
						if(document.getElementById("acassemble"+i).value.length>0) {
							arracassemblelist = document.getElementById("acassemble"+i).value.split("|");
							document.form1.assemble_list.value += "|"+arracassemblelist[0];
						} else {
							document.form1.assemble_list.value += "|";
						}
					}
				}
			} else {
				alert('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.');
				return;
			}
		}
		<?php  } ?>
		if (gbn != "ordernow"){
			//optionArr quantityArr priceArr
			//return;
			document.form1.action="/front/confirm_basket.php";
			document.form1.target="confirmbasketlist";
			//alert(document.form1.quantity.value);
			window.open("about:blank","confirmbasketlist","width=401,height=309,scrollbars=no,resizable=no, status=no,");
			document.form1.submit();
		} else {
			document.form1.submit();
		}
	} else {
		//document.wishform.opts.value=document.form1.opts.value;
		//if(typeof(document.form1.option1)!="undefined") document.wishform.option1.value=document.form1.option1.value;
		//if(typeof(document.form1.option2)!="undefined") document.wishform.option2.value=document.form1.option2.value;
		document.wishform.optionArr.value=document.form1.optionArr.value;
		document.wishform.priceArr.value=document.form1.priceArr.value;
		document.wishform.quantityArr.value=document.form1.quantityArr.value;
		/////////////////////////// bi
		window.open("about:blank","confirmwishlist","width=401,height=309,scrollbars=no");
		document.wishform.submit();
	}
}


function review_write() {
	if(typeof(document.all["reviewwrite"])=="object") {
		if(document.all["reviewwrite"].style.display=="none") {
			document.all["reviewwrite"].style.display="";
		} else {
			document.all["reviewwrite"].style.display="none";
		}
	}
}
/*
function CheckReview() {
	if(document.reviewform.rname.value.length==0) {
		alert("작성자 이름을 입력하세요.");
		document.reviewform.rname.focus();
		return;
	}
	if(document.reviewform.rcontent.value.length==0) {
		alert("사용후기 내용을 입력하세요.");
		document.reviewform.rcontent.focus();
		return;
	}
	document.reviewform.mode.value="review_write";
	document.reviewform.submit();
}
*/


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

<?php  if($_pdata->assembleuse=="Y") { ?>
var currentSelectIndex = "";
function setCurrentSelect(thisSelectIndex) {
	currentSelectIndex = thisSelectIndex;
}

function setAssenbleChange(thisObj,idxValue) {
	if(thisObj.value.length>0) {
		thisValueSplit = thisObj.value.split('|');
		if(thisValueSplit[1].length>0) {
			if(Number(thisValueSplit[1])==0) {
				alert('현재 상품은 품절 상품입니다.');
			} else {
				if(Number(document.form1.quantity.value)>0) {
					if(Number(thisValueSplit[1]) < Number(document.form1.quantity.value)) {
						alert('구성 상품의 재고량이 부족합니다.');
					} else {
						setTotalPrice(document.form1.quantity.value);
						if(thisValueSplit.length>3 && thisValueSplit[4].length>0 && document.getElementById("acimage"+idxValue)) {
							document.getElementById("acimage"+idxValue).src="<?=$Dir.DataDir."shopimages/product/"?>"+thisValueSplit[4];
						} else {
							document.getElementById("acimage"+idxValue).src="<?=$Dir."images/acimage.gif"?>";
						}
						return;
					}
				} else {
					alert('본 상품 수량을 입력해 주세요.');
				}
			}
		} else {
			setTotalPrice(document.form1.quantity.value);
			if(thisValueSplit.length>3 && thisValueSplit[4].length>0 && document.getElementById("acimage"+idxValue)) {
				document.getElementById("acimage"+idxValue).src="<?=$Dir.DataDir."shopimages/product/"?>"+thisValueSplit[4];
			} else {
				document.getElementById("acimage"+idxValue).src="<?=$Dir."images/acimage.gif"?>";
			}
			return;
		}

		thisObj.options[currentSelectIndex].selected = true;
	} else {
		setTotalPrice(document.form1.quantity.value);
		document.getElementById("acimage"+idxValue).src="<?=$Dir."images/acimage.gif"?>";
		return;
	}
}

function getQuantityCheck(tmp) {
	var i=true;
	var j=1;
	while(i) {
		if(document.getElementById("acassemble"+j)) {
			if(document.getElementById("acassemble"+j).value) {
				arracassemble = document.getElementById("acassemble"+j).value.split("|");
				if(arracassemble[1].length>0 && Number(tmp) > Number(arracassemble[1])) {
					return false;
				}
			}
		} else {
			i=false;
		}
		j++;
	}
	return true;
}

function assemble_proinfo(idxValue) { // 조립상품 개별 상품 정보보기
	if(document.getElementById("acassemble"+idxValue)) {
		if(document.getElementById("acassemble"+idxValue).value.length>0) {
			thisValueSplit = document.getElementById("acassemble"+idxValue).value.split('|');
			if(thisValueSplit[0].length>0) {
				product_info_pop("assemble_proinfo.php?op=<?=$productcode?>&np="+thisValueSplit[0],"assemble_proinfo_"+thisValueSplit[0],700,700,"yes");
			} else {
				alert("해당 상품정보가 존재하지 않습니다.");
			}
		}
	}
}

function product_info_pop(url,win_name,w,h,use_scroll) {
	var x = (screen.width - w) / 2;
	var y = (screen.height - h) / 2;
	if (use_scroll==null) use_scroll = "no";
	var use_option = "";
	use_option = use_option + "toolbar=no, channelmode=no, location=no, directories=no, resizable=no, menubar=no";
	use_option = use_option + ", scrollbars=" + use_scroll + ", left=" + x + ", top=" + y + ", width=" + w + ", height=" + h;

	var win = window.open(url,win_name,use_option);
	return win;
}
<?php  } ?>


//-->
</SCRIPT>


<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?php include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td>
<?php
/*
	if(strlen($_cdata->detail_type)==5) {
		include($Dir.TempletDir."product/detail_{$_cdata->detail_type}.php");
	} else if (strlen($_cdata->detail_type)==6 && $_cdata->detail_type[5]=="U") {
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
	}
*/
	if (strlen($_cdata->detail_type)==6 && $_cdata->detail_type[5]=="U") {
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
			//include($Dir.TempletDir."product/detail_TEM001_test.php");
			include($Dir.TempletDir."product/detail_TEM001_optionback.php");
		} else {
			include($Dir.TempletDir."product/detail_{$_cdata->detail_type}_test.php");
		}

	}
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
<input type=hidden name=optionArr>
<input type=hidden name=priceArr>
<input type=hidden name=quantityArr>
</form>

<?php if($_pdata->vender>0){?>
<form name=custregminiform method=post>
<input type=hidden name=sellvidx value="<?=$_vdata->vender?>">
<input type=hidden name=memberlogin value="<?=(strlen($_ShopInfo->getMemid())>0?"Y":"N")?>">
</form>
<?php }?>

<div id="create_openwin" style="display:none"></div>

<script type="text/javascript">
	_TRK_PI = "PDV";
	_TRK_PN = "<?=$_pdata->productname?>";          //상품명
	_TRK_MF = "<?=$_pdata->production?>";          //브랜드명
</script>

<!-- WIDERPLANET  SCRIPT START 2015.6.30 -->
<div id="wp_tg_cts" style="display:none;"></div>
<script type="text/javascript">
var wptg_tagscript_vars = wptg_tagscript_vars || [];
wptg_tagscript_vars.push(
(function() {
	return {
		wp_hcuid:"",  	/*Cross device targeting을 원하는 광고주는 로그인한 사용자의 Unique ID (ex. 로그인 ID, 고객넘버 등)를 암호화하여 대입.
				 *주의: 로그인 하지 않은 사용자는 어떠한 값도 대입하지 않습니다.*/
		ti:"22251",
		ty:"Item",
		device:"web"
		,items:[{i:"<?=$_pdata->productcode?>",	t:"<?=$_pdata->productname?>"}] /* i:상품 식별번호 (Feed로 제공되는 상품코드와 일치하여야 합니다.) t:상품명 */
		};
}));
</script>
<script type="text/javascript" async src="//astg.widerplanet.com/js/wp_astg_4.0.js"></script>
<!-- // WIDERPLANET  SCRIPT END 2015.6.30 -->

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
