<?php
include_once('./outline/header_m.php');
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다


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

$productcode=$_REQUEST["productcode"];
$sort=$_REQUEST["sort"];
$brandcode=$_REQUEST["brandcode"]+0;
$_cdata="";
$_pdata="";


if( strlen($productcode) > 0 ) {

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
    $sql.= "AND display='Y' ";

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
    alert_go('해당 상품 정보가 존재하지 않습니다.',"/");
}
# 상품상세 뷰 카운트 update 2016-01-26 유동혁
$vcnt_sql = "UPDATE tblproduct SET vcnt = vcnt + 1 WHERE productcode = '".$productcode."'";
pmysql_query( $vcnt_sql, get_db_conn() );


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

//멀티 이미지 관련()2013-12-23 멀티 이미지 기능만 추가함. 확대보기 없음.

if($multi_img=="Y") {

	$imagepath=$Dir.DataDir."shopimages/multi/";
	//$dispos=$row->multi_dispos;
	// 멀티이미지 설정
	$changetype=$_data->multi_changetype;
	$bgcolor=$_data->multi_bgcolor;

	$sql = "SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
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
                        $size=getimagesize($Dir.DataDir."shopimages/multi/".$multi_imgs[$i]);
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


# 상품 이미지 path
$imagepath_product = $Dir.DataDir.'shopimages/product/';
$imagepath_multi = $Dir.DataDir.'shopimages/multi/';
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
$thisCate = getDecoCodeLoc( $_pdata->productcode );
$optionNames = explode( '@#', $_pdata->option1 );
$option_depth = count( $optionNames );

$addOptionNames = explode( '@#', $_pdata->option2 );
$addOption_tf = explode( '@#', $_pdata->option2_tf );
$addOption_maxlen = explode( '@#', $_pdata->option2_maxlen );

#연관상품
//상품의 조회순 , 등록날짜로 10개
$related_sql = "WITH related AS ( SELECT c_productcode  FROM tblproductlink  WHERE c_category like '". substr($_cdata->c_category, 0, 9) ."%' ";
$related_sql.= " AND c_maincate = 1 GROUP BY c_productcode ) ";
$related_sql.= "SELECT pr.productcode, pr.productname, pr.sellprice, ";
$related_sql.= "pr.consumerprice, pr.buyprice, pr.brand, pr.maximage, ";
$related_sql.= "pr.minimage, pr.tinyimage, pr.mdcomment, pr.review_cnt, ";
$related_sql.= "pr.icon, pr.soldout, pr.quantity, pr.over_minimage FROM tblproduct pr ";
$related_sql.= "JOIN related r ON pr.productcode = r.c_productcode ";
$related_sql.= "WHERE pr.productcode <> '{$productcode}' "; // 현재 자신은 제외
$related_sql.= "AND pr.display = 'Y' ";
$related_sql.= "ORDER BY pr.vcnt DESC, date DESC LIMIT 6 ";

$related_html = productlist_print( $related_sql, 'W_016' );

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
        if ( $row['bridx'] == $bridx ) {
            $onBrandWishClass = "on";
        }
    }
}

// ======================================================================================
// 관련 프로모션 정보
// ======================================================================================

// 기획전 중에서 현재 진행중인것들을 조회
$sql  = "SELECT a.special_list, c.idx, c.title ";
$sql .= "FROM tblspecialpromo a ";
$sql .= "   LEFT JOIN tblpromotion b ON a.special::integer = b.seq ";
$sql .= "   LEFT JOIN tblpromo c ON b.promo_idx = c.idx ";
$sql .= "WHERE c.display_type in ('A', 'P') and current_date <= c.end_date ";
$sql .= "ORDER BY c.rdate desc ";

$result = pmysql_query($sql);

$bLoopBreak = false;
$limitCount = 2;           
$arrPromotionIdx = array();
$arrPromotionTitle = array();
while ($row = pmysql_fetch_array($result)) {
    $special_list   = str_replace(",", "','", $row['special_list']);
    $promo_idx      = $row['idx'];
    $promo_title    = $row['title'];

    // 해당 브랜드에 속한 상품 리스트 조회

    if ( !empty($brand_code) ) {
        $sub_sql  = "SELECT count(*) ";
        $sub_sql .= "FROM tblbrandproduct "; 
        $sub_sql .= "WHERE bridx = {$brand_code} AND productcode in ( '{$special_list}' ) ";
        $sub_sql .= "LIMIT 1 ";

        $sub_row  = pmysql_fetch_object(pmysql_query($sub_sql));

        if ( $sub_row->count >= 1 ) { 
            if ( !in_array($promo_idx, $arrPromotionIdx) ) {
                array_push($arrPromotionIdx, $promo_idx);
                array_push($arrPromotionTitle, $promo_title);
            }

            if (count($arrPromotionIdx) >= $limitCount) { break; }
        }
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

#회원 쿠폰정보
$member_coupon = MemberCoupon( 1, 'M' );
#사용 가능한 쿠폰 정보
$possible_coupon = PossibleCoupon( $_pdata->productcode );

//쿠폰 레이어팝업 내용
function CouponLayer( $member_coupon, $possible_coupon ){
	$member_layerHtml = array();
	$possible_layerHtml = array();
	$layerHtml = array();
	$mem_layerText = '';
	$possible_layerText = '';
	$tmpPossibelCoupon = $possible_coupon;
	$coupons = array();
    
	foreach( $member_coupon as $mcKey=>$mcVal ){
        if( !in_array( $mcVal->coupon_code, $coupons ) ){
            $coupons[] = $mcVal->coupon_code;
            $pricetype_text = CouponText( $mcVal->sale_type );
            $mem_layerText = "<tr name='TR_memcoupon' data-code='".$mcVal->coupon_code."' >";
            $mem_layerText.= "	<td>".$mcVal->coupon_name."</td>";
            $mem_layerText.= "	<td>".$mcVal->sale_money.' '.$pricetype_text['won']."</td>";
            $mem_layerText.= "	<td>";
            $mem_layerText.= "		".toDate( $mcVal->date_start, '-' )."<br>";
            $mem_layerText.= "		~ ".toDate( $mcVal->date_end, '-' );
            $mem_layerText.= "		</td>";
            $mem_layerText.= "</tr>";
            $member_layerHtml[] = $mem_layerText;
        }
	}

	foreach( $possible_coupon as $pcKey=>$pcVal ){
		$pricetype_text = CouponText( $pcVal->sale_type );
		$possible_layerText = "<tr>";
		$possible_layerText.= "	<td>".$pcVal->coupon_name."</td>";
		$possible_layerText.= "	<td>".$pcVal->sale_money.' '.$pricetype_text['won']."</td>";
		$possible_layerText.= "	<td>";
		$possible_layerText.= "	<button type='button' class='btn-dib-function CLS_coupon_download' data-coupon='".$pcVal->coupon_code."' >";
		$possible_layerText.= "		<span>쿠폰받기</span>";
		$possible_layerText.= "	</button>";
		$possible_layerText.= "		</td>";
		$possible_layerText.= "</tr>";
		$possible_layerHtml[] = $possible_layerText;
	}

	$layerHtml[] = $member_layerHtml;
	$layerHtml[] = $possible_layerHtml;

	return $layerHtml;

}
//쿠폰 레이어팝업 내용
$coupon_layer = CouponLayer( $member_coupon, $possible_coupon );

//카드혜택 베너
$card_banner = get_banner( '111' );

// 상품 썸네일 옆에 작은 이미지들을 배열에 저장해서 한번에 그려준다.
$arrMiniThumbList = array();

# 상품 큰 이미지
if( is_file( $imagepath_product.$_pdata->maximage ) || strpos($_pdata->maximage, "http://") !== false ) {
    $tmp_imgCont = getProductImage($imagepath_product, $_pdata->maximage);
    array_push($arrMiniThumbList, $tmp_imgCont);
}

if ( $multi_img=="Y" && $yesimage[0] ) {

    $arrMultiImg = array(); // 상품 상세 설명이 없는 경우 노출하기 위해 배열에 저장
    foreach( $yesimage as $mImgKey=>$mImgVal ){
        $multiImg = getProductImage($imagepath_multi, $mImgVal);
        array_push($arrMultiImg, $multiImg);

        $tmp_imgCont = $multiImg;
        array_push($arrMiniThumbList, $tmp_imgCont);
    }
}

?>


<main id="content">
    <div class="sub-title">
        <h2>상품정보</h2>
        <a class="btn-prev" href="#"><img src="<?=$Dir?>/m/static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
    </div>
    
    <div class="goods-detail-breadcrumb">
        <ol>
            <li>WOMEN</li>
            <li>WOMENWEAR</li>
            <li>OUTER</li>
        </ol>
    </div>
    
    <!-- 상단 이미지 -->
    <div class="js-goods-detail-img">
        <div class="js-carousel-list">
            <ul>
<?php
    for( $i=0; $i < count( $arrMiniThumbList ); $i++ ){
?>
                <li class="js-carousel-content"><a href="javascript:;"><img src="<?=$arrMiniThumbList[$i]?>" alt=""></a></li>
<?php
    }
?>
            </ul>
        </div>
        <div class="page <? if( count( $arrMiniThumbList ) < 2 ){ echo 'hide'; }?>">
            <ul>
<?php
    for( $i=0; $i < count( $arrMiniThumbList ); $i++ ){
?>
                <li class="js-carousel-page"><a href="javascript:;"><span class="ir-blind"><?=( $i + 1 )?></span></a></li>
<?php
    }
?>
            </ul>
        </div>
        <button class="js-carousel-arrow" data-direction="prev" type="button"><img src="<?=$Dir?>/m/static/img/btn/btn_slider_arrow_prev.png" alt="이전"></button>
        <button class="js-carousel-arrow" data-direction="next" type="button"><img src="<?=$Dir?>/m/static/img/btn/btn_slider_arrow_next.png" alt="다음"></button>
    </div>
    <!-- // 상단 이미지 -->
    
    <!-- 상단 정보 -->
    <div class="goods-detail-info">
        <ul class="tag-list">
<?php
    if( $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ){
        echo "<li><img src='".$Dir."images/common/icon_soldout.gif' ></li>";
    }
?>
            <?=get_viewIcon( $_pdata->icon )?>
            <!-- <li><span class="tag-def tag-special">SPECIAL</span></li>
            <li><span class="tag-def tag-new">NEW</span></li>
            <li><span class="tag-def tag-sale">SALE</span></li> -->
        </ul>
        <h3>
            <span class="brand"><?=$brand_name?></span>
            <span class="promotion-ment" style="color:#<?=$_pdata->mdcommentcolor?> !important"><?=$_pdata->mdcomment?></span><br>
            <?=$_pdata->productname?>
        </h3>
        <div class="goods-detail-info-price">
            <section>
                <h4>정상가</h4>
<?php
if( $_pdata->consumerprice > 0 && $_pdata->consumerprice > $_pdata->sellprice ){
?>
                <del><?=number_format( $_pdata->consumerprice )?></del>
<?php
}
?>
            </section>
            <section>
                <h4>판매가</h4>
                <strong><?=number_format( $_pdata->sellprice )?></strong>
            </section>
<?php
if( $_pdata->consumerprice > 0 && $_pdata->consumerprice > $_pdata->sellprice ){
?>
            <span class="discount"><?=get_price_percent( $_pdata->consumerprice, $_pdata->sellprice )?>%</span>
<?php
}
?>
        </div>
        <div class="goods-detail-info-benefit">
            <section>
                <h4>카드혜택</h4>
                <a class="btn-card" href="#">무이자 카드보기</a>
            </section>
            <section>
                <h4>쿠폰혜택</h4>
                <a class="btn-coupon" href="#">사용가능 쿠폰</a>
            </section>
        </div>
        <div class="goods-detail-info-option">
<?php
        exdebug( $options );
		if( strlen( $_pdata->option1 ) > 0 || strlen( $_pdata->option2 ) > 0 ){ // 옵션정보 확인
            if( strlen( $_pdata->option1 ) > 0 ){
                $opt1_subject = option_slice( $_pdata->option1, '1' );
                //$opt1_content = option_slice( $_pdata->option1, $_pdata->option_type );
                $opt_tf       = option_slice( $_pdata->option1_tf, '1' );
                $select_option_code = array();
                $option_depth = count( $opt1_subject ); // 옵션 길이
                foreach( $opt1_subject as $subjectKey=>$subjectVal ){
?>
                            <section name='opt' >
                                <h4><?=$subjectVal?></h4>
                                <div class="select-def">
                                    <select name='opt_value' 
                                        data-type='<?=$_pdata->option_type?>' 
                                        data-prcode='<?=$_pdata->productcode?>'  
                                        data-depth='<?=($subjectKey + 1)?>' 
                                        data-qty='<?=$_pdata->quantity?>'
                                        data-tf='<?=$opt_tf[$subjectKey]?>'
                                    >
                                        <option value='' > 선택 </option>
<?php
                    if( ( $subjectKey == 0 && $_pdata->option_type == '0' ) || $_pdata->option_type == '1' ){
                        //옵션정보를 가져온다
                        if( $_pdata->option_type == '0' ){
                            $options = get_option( $_pdata->productcode );
                        } else if( $_pdata->option_type == '1' ){
                            $options = mobile_get_alone_option( $_pdata->productcode, $subjectVal );
                        } else {
                            $options = array();
                        }
                        foreach( $options as $contentKey=>$contentVal ) { //옵션내용
                            $option_qty = $contentVal['qty']; // 수량
                            $option_text = ''; // 품절 text
                            $priceText = ''; // 가격
                            $option_desabled = false;
                            $alone_opt = array();
                            
                            if( $_pdata->option_type == '0' && $subjectKey == 0 ) {
                                $select_code = $contentVal['code']; //조합형 옵션 코드형태 + 1depth 일때
                            } else if( $_pdata->option_type == '1' ) {
                                //$select_code = $contentVal['option_code']; // 독립형 옵션일때
                                //$alone_opt = explode( chr( 30 ), $opt1_content[$subjectKey] );
                            } else {
                                $select_code = '';
                            }

                            //상품가격 text 처리 ( 조합형일 경우 마지막 depth의 옵션만 적용, 독립형일경우 전부다 적용 )
                            if( 
                                ( 
                                  ( $_pdata->option_type == '0' && $subjectKey + 1 == $option_depth ) || 
                                  ( $_pdata->option_type == '1' )
                                ) && $contentVal['price'] > 0 
                            ) {
                                $priceText = ' ( + '.number_format($contentVal['price']).' 원 )';
                            } else if(
                                ( 
                                  ( $_pdata->option_type == '0' && $subjectKey + 1 == $option_depth ) || 
                                  ( $_pdata->option_type == '1' )
                                ) && $contentVal['price'] < 0 
                            ) {
                                $priceText = ' ( - '.number_format($contentVal['price']).' 원 )';
                            } // 상품가격 if

                            //품절 text 처리
                            if( 
                                ( $option_qty !== null && $option_qty <= 0 ) && 
                                $_pdata->option_type == '0' && 
                                $_pdata->quantity < 999999999
                            ){
                                $option_text = '[품절]&nbsp;';
                                $option_desabled = true;
                            } //품절 id
?>
                                        <option value="<?=$select_code?>" 
                                            <? if( $contentVal['code'] == $opt1_content[$subjectKey] && $_pdata->option_type == '0' ){ echo ' selected '; } ?> 
                                            <?// if( $contentVal['code'] == $alone_opt[1] && $_pdata->option_type == '1' ){ echo ' selected '; } ?> 
                                            <? if( $option_desabled ) { echo ' disabled '; } ?>
                                            <? if( $_pdata->option_type == '0' && $subjectKey + 1 == $option_depth ) { echo 'data-qty="'.$option_qty.'"'; } ?>
                                        >
                                            <?=$option_text.$contentVal['code'].$priceText?>
                                        </option>
<?php
                        } // get_option if
                    }
?>
                                    </select>
                                </div>
                            </section>

<?php
                } // opt_subject foreach
            } // opt1_name if
            
            if( strlen( $_pdata->option2 ) > 0 ){ // 텍스트 옵션
                $text_opt_subject = option_slice( $_pdata->option2, '1' );
                //$text_opt_content = option_slice( $_pdata->text_opt_content, '1' );
                $text_opt_tf      = option_slice( $_pdata->option2_tf, '1' ); 
                $test_opt_maxln   = option_slice( $_pdata->option2_maxlen, '1' );
                foreach( $text_opt_subject as $textOptKey=>$textOptVal ){
                    $text_opt_tf_msg = '';
                    if( $text_opt_tf[$textOptKey] == 'T' ) $text_opt_tf_msg = '(필수)';

?>
                            <section name='text-opt'>
                                <h4><?=$textOptVal.' '.$text_opt_tf_msg?></h4>
                                <div class="select-def">
                                    <input type='text' name='text_opt_value' value='<?=$text_opt_content[$textOptKey]?>' maxlength='<?=$test_opt_maxln[$textOptKey]?>' data-tf="<?=$text_opt_tf[$textOptKey]?>" >
                                    <span class="byte">(<strong><?=strlen($text_opt_content[$textOptKey])?></strong>/<?=$test_opt_maxln[$textOptKey]?>)</span>
                                </div>
                            </section>
<?php
                } // text_opt_subject foreach
            } // text_opt_subject if
?>
<?php
        }// option if
        if( $_pdata->quantity <= 0 || $_pdata->soldout != 'Y' ){  // 상품 구매 가능
?>
            <section>
                <input type="hidden" name='quantity' id='quantity' value="0">
            </section>
        </div>
        <div class="goods-detail-info-btn">
            <!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
            <button class="btn-wishlist on" type="button" title="담겨짐" onClick="javascript:alert('품절된 상품입니다.');" ><span>찜</span></button>
            <button class="btn-share" type="button" ><span>공유</span></button>
        </div>
        <!-- // 상단 정보 -->
    
        <!-- 구매버튼 -->
        <div class="goods-detail-buy">
            <a class="btn-buy" href="javascript:alert('품절된 상품입니다.');">SOLD OUT</a>
            <a class="btn-shoppingbag" href="javascript:alert('품절된 상품입니다.');">
                <img src="<?=$Dir?>/m/static/img/btn/goods_detail_shoppingbag.png" alt="">SHOPPING BAG
            </a>
            <div class="box"><a class="btn-brandshop" href="#"><span><?=$brand_name?><br><strong>브랜드 샵 가기</strong></span></a></div>
        </div>
        <!-- // 구매버튼 -->
<?
        } else {  // 상품 품절
?>
                <section>
                    <h4>QUANTITY</h4>
                    <div class="qty">
                        <button class="btn-qty-subtract" type="button"><span>수량 1빼기</span></button>
                        <input type="text" value="1" title="수량">
                        <button class="btn-qty-add" type="button"><span>수량 1더하기</span></button>
                    </div>
                </section>
            </div>
            <div class="goods-detail-info-btn">
                <!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
                <button class="btn-wishlist on" type="button" title="담겨짐"><span>찜</span></button>
                <button class="btn-share" type="button"><span>공유</span></button>
            </div>
        </div>
        <!-- // 상단 정보 -->
        
        <!-- 구매버튼 -->
        <div class="goods-detail-buy">
            <a class="btn-buy" href="#">BUY NOW</a>
            <a class="btn-shoppingbag" href="#"><img src="<?=$Dir?>/m/static/img/btn/goods_detail_shoppingbag.png" alt="">SHOPPING BAG</a>
            <div class="box"><a class="btn-brandshop" href="#"><span><?=$brand_name?><br><strong>브랜드 샵 가기</strong></span></a></div>
        </div>
        <!-- // 구매버튼 -->
<?php
    }
?>
    <!-- 상품내용 -->
    <div class="js-goods-detail-content" id="js-goods-detail-content">
        <div class="content-tab">
            <div class="js-menu-list">
                <div class="js-tab-line"></div>
                <ul>
                    <li class="js-tab-menu on"><a href="#"><span>상품정보</span></a></li>
                    <li class="js-tab-menu"><a href="#"><span>상품리뷰</span></a></li>
                    <li class="js-tab-menu"><a href="#"><span>배송정보</span></a></li>
                </ul>
            </div>
        </div>
        
        <!-- 상품정보 -->
        <div class="js-tab-content goods-detail-content-info">
            
<?php
    // ================================================================================
    // PRODUCT INFO // 모바일용으로 변경해야함
    // ================================================================================
	$_pdata_content = stripslashes($_pdata->content);

	// 상품상세의 내용중 이미지의 스타일일 제거한다. (2016-03-31 김재수 추가)-------------------------------
	preg_match_all("/<IMG[^>]*style=[\"']?([^>\"']+)[\"']?[^>]*>/i",$_pdata_content,$_pdata_content_img);
	if ($_pdata_content_img) {
		foreach($_pdata_content_img[0] as $con_img_arr => $con_img) {
			$tem_con_img=$con_img;
			$tem_con_img=preg_replace("/ zzstyle=([^\"\']+) /"," ",$tem_con_img); 
			$tem_con_img=preg_replace("/ style=(\"|\')?([^\"\']+)(\"|\')?/","",$tem_con_img);
			$_pdata_content = str_replace($con_img, $tem_con_img, $_pdata_content);
		}
	}
	// ---------------------------------------------------------------------------------------------------
	if( strlen($detail_filter) > 0 ) {
		$_pdata_content = preg_replace($filterpattern,$filterreplace,$_pdata_content);
	}

    // <br>태그 제거
    $arrList = array("/<br\/>/", "/<br>/");
	$_pdata_content_tmp = trim(preg_replace($arrList, "", $_pdata_content));

    if ( empty($_pdata_content_tmp) ) {
        echo "<ul class=\"detail-thumb\">";
        foreach ( $arrMultiImg as $key => $val ) {
            echo "<li><img src=\"{$val}\" alt=\"\"></li>";
        }
        echo "</ul>";
    } else {
        if ( strpos($_pdata_content,"table>")!=false || strpos($_pdata_content,"TABLE>")!=false)
            echo "<pre>".$_pdata_content."</pre>";
        else if(strpos($_pdata_content,"</")!=false)
            echo nl2br($_pdata_content);
        else if(strpos($_pdata_content,"img")!=false || strpos($_pdata_content,"IMG")!=false)
            echo nl2br($_pdata_content);
        else
            echo str_replace(" ","&nbsp;",nl2br($_pdata_content));
    }
?>
            <!-- 추가예정 -->
            <!-- <table class="info-size">
                <caption>SIZE<span class="unit">단위(cm)</span></caption>
                <colgroup>
                    <col style="width:19.35%">
                    <col style="width:16.13%">
                    <col style="width:16.13%">
                    <col style="width:16.13%">
                    <col style="width:16.13%">
                    <col style="width:16.13%">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="row">사이즈</th>
                        <th scope="col">90</th>
                        <th scope="col">95</th>
                        <th scope="col">100</th>
                        <th scope="col">105</th>
                        <th scope="col">110</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">가슴둘레</th>
                        <td>103</td>
                        <td>108</td>
                        <td>113</td>
                        <td>118</td>
                        <td>120</td>
                    </tr>
                    <tr>
                        <th scope="row">목둘레</th>
                        <td>56.5</td>
                        <td>58</td>
                        <td>59.5</td>
                        <td>61</td>
                        <td>62</td>
                    </tr>
                    <tr>
                        <th scope="row">밑단둘레</th>
                        <td>77</td>
                        <td>82</td>
                        <td>100</td>
                        <td>105</td>
                        <td>110</td>
                    </tr>
                    <tr>
                        <th scope="row">상의길이</th>
                        <td>60</td>
                        <td>62</td>
                        <td>63</td>
                        <td>86</td>
                        <td>95</td>
                    </tr>
                    <tr>
                        <th scope="row">소매길이</th>
                        <td>60</td>
                        <td>62</td>
                        <td>63</td>
                        <td>86</td>
                        <td>95</td>
                    </tr>
                    <tr>
                        <th scope="row">어깨너비</th>
                        <td>77</td>
                        <td>82</td>
                        <td>100</td>
                        <td>105</td>
                        <td>110</td>
                    </tr>
                    <tr>
                        <th scope="row">총길이</th>
                        <td>77</td>
                        <td>82</td>
                        <td>100</td>
                        <td>105</td>
                        <td>110</td>
                    </tr>
                </tbody>
            </table>
            <ul class="info-size-note">
                <li>위 사이즈는 해당 브랜드의 표준상품 사이즈이며, 단위는 cm 입니다.</li>
                <li>사이즈를 재는 위치나 방법에 따라 약간의 오차가 있을수있습니다.</li>
                <li>위 사항들은 교환 및 반품, 환불의 사유가 될수 없으며, 고객의 단순변심으로 분류됩니다.</li>
            </ul> -->
            
            <table class="info-info">
                <caption>INFO</caption>
                <colgroup>
                    <col style="width:19.35%">
                    <col style="width:auto">
                </colgroup>
                <tbody>
                    
                    <tr>
                        <th scope="row">소재</th>
                        <td>
                            <?=$jungbo_val[1]?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">제조년월</th>
                        <td><?=$jungbo_val[2]?></td>
                    </tr>
                    <tr>
                        <th scope="col">제조사<br>원산지</th>
                        <td><?=$jungbo_val[3]?></td>
                    </tr>
                    <tr>
                        <th scope="col">품질보증<br>기간</th>
                        <td><?=$jungbo_val[4]?></td>
                    </tr>
                    <tr>
                        <th scope="col">A/S문의</th>
                        <td><?=$jungbo_val[5]?></td>
                    </tr>
                    <tr>
                        <th scope="row">세탁방법</th>
                        <td>
                            <?=$jungbo_val[6]?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">주의사항</th>
                        <td>
                            <?=$jungbo_val[7]?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="btnwrap">
                <div class="box">
                    <a class="btn-def" href="#js-goods-detail-content" onClick="$('.js-tab-menu').eq(1).trigger('click');scroll_anchor($(this).attr('href'));return false;">상품리뷰</a>
                    <a class="btn-def" href="product_qna.php?productcode=<?=$productcode?>&pridx=<?=$_pdata->pridx?>">상품문의</a>
                </div>
            </div>
        </div>
        <!-- // 상품정보 -->
<?php
// 리뷰 작성 가능 리스트 조회
$sql  = "SELECT tblResult.ordercode, tblResult.idx ";
$sql .= "FROM ";
$sql .= "   ( ";
$sql .= "       SELECT a.*, b.regdt  ";
$sql .= "       FROM tblorderproduct a LEFT JOIN tblorderinfo b ON a.ordercode = b.ordercode ";
$sql .= "       WHERE a.productcode = '" . $productcode . "' AND b.id = '" . $_ShopInfo->getMemid()  . "' and ( (b.oi_step1 = 3 AND b.oi_step2 = 0) OR (b.oi_step1 = 4 AND b.oi_step2 = 0) ) ";
$sql .= "       ORDER BY a.idx DESC ";
$sql .= "   ) AS tblResult LEFT ";
$sql .= "   OUTER JOIN tblproductreview tpr ON tblResult.productcode = tpr.productcode and tblResult.ordercode = tpr.ordercode and tblResult.idx = tpr.productorder_idx ";
$sql .= "WHERE tpr.productcode is null ";
$sql .= "ORDER BY tblResult.idx asc ";
$sql .= "LIMIT 1 ";

$result = pmysql_query($sql);
list($review_ordercode, $review_order_idx) = pmysql_fetch($sql);
pmysql_free_result($result);

$qry = "WHERE a.productcode='{$productcode}' ";
$sql = "SELECT COUNT(*) as t_count, SUM(a.marks) as totmarks FROM tblproductreview a ";
$sql.= $qry;
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$t_count_review = (int)$row->t_count;
$totmarks = (int)$row->totmarks;
$marks=@ceil($totmarks/$t_count_review);
pmysql_free_result($result);
$paging = new New_Templet_mobile_paging($t_count_review,10,4,'GoPageAjax');
$gotopage = $paging->gotopage;

# 리뷰 리스트를 불러온다
//$reviewlist = 'Y';
$sql  = "SELECT a.*, b.productname FROM tblproductreview a LEFT JOIN tblproduct b ON a.productcode = b.productcode ";
$sql .= "{$qry} ORDER BY a.date DESC, a.num DESC ";

$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
$j=0;
$reviewList = array();
while($row=pmysql_fetch_object($result)) {
	
	$reviewComment = array();

	$reviewList[$j]['idx'] = $row->num;
	$reviewList[$j]['num'] = $row->num;
	$reviewList[$j]['number'] = ($t_count_review-($setup['list_num'] * ($gotopage-1))-$j);
	$reviewList[$j]['id'] = $row->id;
	$reviewList[$j]['name'] = $row->name;
	$reviewList[$j]['subject'] = $row->subject;
	$reviewList[$j]['productcode'] = $row->productcode;
	$reviewList[$j]['productname'] = $row->productname;
	$reviewList[$j]['ordercode'] = $row->ordercode;
	$reviewList[$j]['productorder_idx'] = $row->productorder_idx;
	$reviewList[$j]['marks'] = $row->marks;
	$reviewList[$j]['hit'] = $row->hit;
	$reviewList[$j]['type'] = $row->type;

    // 별표시하기
    $reviewList[$j]['marks_sp'] = '';
    for ( $i = 0; $i < $row->marks; $i++ ) {
        $reviewList[$j]['marks_sp'] .= '★';
    }
	
	$reviewList[$j]['best_type'] = $row->best_type;

	$reviewList[$j]['upfile'] = $row->upfile;       // 첨부파일1
	$reviewList[$j]['upfile2'] = $row->upfile2;     // 첨부파일2
	$reviewList[$j]['upfile3'] = $row->upfile3;     // 첨부파일3
	$reviewList[$j]['upfile4'] = $row->upfile4;     // 첨부파일4

	$reviewList[$j]['up_rfile'] = $row->up_rfile;   // 첨부파일1(실제 업로드한 파일명)
	$reviewList[$j]['up_rfile2'] = $row->up_rfile2; // 첨부파일2(실제 업로드한 파일명)
	$reviewList[$j]['up_rfile3'] = $row->up_rfile3; // 첨부파일3(실제 업로드한 파일명)
	$reviewList[$j]['up_rfile4'] = $row->up_rfile4; // 첨부파일4(실제 업로드한 파일명)

	//exdebug($reviewList);
	$reviewList[$j]['date'] = substr($row->date,0,4).".".substr($row->date,4,2).".".substr($row->date,6,2);
	$reviewList[$j]['date'].= '&nbsp;'.substr($row->date,8,2).":".substr($row->date,10,2).":".substr($row->date,12,2);
	$reviewList[$j]['content'] = explode("=",$row->content);

	# 코멘트 가져오기
	$comment_sql  = "SELECT no, id, name, content, regdt, pnum ";
    $comment_sql .= "FROM tblproductreview_comment ";
    $comment_sql .= "WHERE pnum = '".$row->num."' ";
    $comment_sql .= "ORDER BY no desc ";

	$comment_res = pmysql_query( $comment_sql, get_db_conn() );
	while( $comment_row = pmysql_fetch_object( $comment_res ) ){
		$reviewComment[] = $comment_row;
	}
	pmysql_free_result( $comment_res );
	$reviewList[$j]['comment'] = $reviewComment;
	$j++;
}
pmysql_free_result($result);

//exdebug( $_SERVER );

?>
<script type="text/javascript">
    var listnum_comment = "<?=$listnum_comment?>";

    function goLogin() {
        <?php $url = $Dir.FrontDir."login.php?chUrl="; ?>
        if ( confirm("로그인이 필요합니다.") ) {
            location.href = "<?=$url?>" + encodeURIComponent('<?=$_SERVER['REQUEST_URI']?>');
        }
    }

    function delete_review_comment(obj) {
        var review_comment_num = $(obj).attr("ids");
        var review_num = $(obj).attr("ids2");

        if ( review_comment_num != "" ) {
            if ( confirm("댓글을 삭제하시겠습니까?") ) {
                $.ajax({
                    type        : "GET", 
                    url         : "../front/ajax_delete_review_comment.php", 
                    data        : { review_comment_num : review_comment_num }
                }).done(function ( result ) {
                    if ( result == "SUCCESS" ) {
                        alert("댓글이 삭제되었습니다.");

                        $(obj).parent().parent().parent().remove();
                    } else {
                        alert("댓글이 삭제가 실패했습니다.");
                    }
                });
            }
        }
    }

	// 리뷰에 댓글달기
	function review_comment_write(obj) {
		var frm = $(obj).parent();            // form
		var obj_comment = $(frm).find("input[name=review_comment]");      // textarea
		var pnum = $(frm).find("input[name=pnum]").val();      // pnum
		var mem_id = $(frm).find("input[name=mem_id]").val();    
		var now_date = $(frm).find("input[name=now_date]").val();  
		var inElement = frm.parent().parent().find('.list-con');

		var review_comment = $(obj_comment).val().trim();

		if ( review_comment == "" ) {
			alert("댓글을 입력해 주세요.");
			$(obj_comment).val("").focus();
			return false;
		}

		var fd = new FormData($(frm)[0]);  
		
		$.ajax({
			url: "../front/ajax_insert_review_comment.php",
			type: "POST",
			data: fd, 
			async: false,
			cache: false,
			contentType: false,
			processData: false,
		}).success(function(data){
				data_arr	= data.split("|");
			if ( data_arr[0] === "SUCCESS" ) {
				alert("댓글이 등록되었습니다.");
				$(obj_comment).val("");
				inElement.removeClass("hide");
				inElement.prepend( '<div class="list-comment"><ul><li class="data"><span class="id">'+mem_id+'</span><span class="date">('+now_date+')</span></li><li>'+review_comment+' <a class="btn-delete" href="javascript:;" onClick="javascript:delete_review_comment(this);" ids="'+data_arr[1]+'" ids2="'+pnum+'"><img src="../static/img/btn/close.png" alt="닫기"></a></li></ul></div>');
			} else {
				alert("댓글 등록이 실패하였습니다.");
			}
		}).error(function () {
			alert("다시 시도해 주세요.");
		});
	}

	//리뷰 paging ajax
	function GoPageAjax(block,gotopage) {
		gBlock = block;
		gGotopage = gotopage;
		$.ajax({
			type: "GET",
			url: "../m/ajax_get_review_list.php",
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			data: "productcode="+$("input[name='productcode']").val()+"&block="+block+"&gotopage="+gotopage
		}).done(function ( data ) {
			$(".review-list").html(data);
			$(".js-review-accordion").accordion();
		});
	}
	
	//리뷰 수정
    function send_review_write_page(
        productcode, 
        ordercode, 
        productorder_idx, 
        review_num) {

        if ( review_num == undefined ) {
            review_num = 0;
        }

        var frm = document.reviewForm;

        frm.productcode.value = productcode;
        frm.ordercode.value = ordercode;
        frm.productorder_idx.value = productorder_idx;
        frm.review_num.value = review_num;
        frm.mode.value = "modify";
		frm.submit();
    }

	// 리뷰삭제
    function delete_review(review_num) {
        if ( confirm("삭제하시겠습니까?") ) {
            $.ajax({
                type        : "GET", 
                url         : "../front/ajax_delete_review.php", 
                contentType : "application/x-www-form-urlencoded; charset=UTF-8",
                data        : { review_num : review_num }
            }).done(function ( data ) {
                if ( data === "SUCCESS" ) {
                    alert("리뷰가 삭제되었습니다.");
                    location.reload();
                }
            });
        }
    }

</script>        
        <!-- 상품리뷰 -->
        <div class="js-tab-content goods-detail-content-review">
<?php
            if( count( $review_banner ) > 0 ) { //리뷰베너
?>
            <a class="bth-review-banner" <?if ( !empty($review_banner[94][1]['banner_link']) ) {?> href="<?=$review_banner[94][1]['banner_link']?>" target="<?=$review_banner[94][1]['banner_target']?>"<?} else {?>href="javascript:;"<?}?>><img src="<?=$review_banner[94][1]['banner_img_m']?>" alt=""></a>
<?
            }
?>
            <div class="review-list">
                <h5>고객님이 작성해 주신 상품 상품평 (<strong><?=$t_count_review?></strong>)</h5>
                <ul class="js-review-accordion">
<?php
	if( count( $reviewList ) > 0 ) {
		foreach( $reviewList as $rKey=>$rVal ) { 
?>
                    <li>
                        <dl>
                            <dt class="js-accordion-menu">
                                <button type="button" title="펼쳐보기">
                                    <span class="list-score" title=""><?=$rVal['marks_sp']?></span>
                                    <span class="box">
                                        <span class="list-id"><?=setIDEncryp($rVal['id'])?></span>
                                        <span class="list-date"><?= $rVal['date'] ?></span>
                                    </span>
                                    <span class="list-title"><?=$rVal['subject']?><? if( $rVal['type'] == "1" ) { ?><img class="ico-photo" src="<?=$Dir?>/m/static/img/icon/ico_review_photo.png" alt="사진첨부"><? } ?></span>
                                </button>
                            </dt>
                            <dd class="js-accordion-content">
                                <p class="list-content"><?=nl2br($rVal['content'][0])?>
							
							<?
							if ( $_ShopInfo->getMemid() == $rVal['id'] ) {
								echo '
									<div class="btn-place">
										<button class="btn-dib-line " type="button" onclick="javascript:send_review_write_page(
											\'' . $rVal['productcode'] . '\', 
											\'' . $rVal['ordercode'] . '\', 
											\'' . $rVal['productorder_idx'] . '\', 
											\'' . $rVal['num'] . '\');"><span>[수정]</span></button>
										<button class="btn-delete" type="button" onclick="javascript:delete_review(\'' . $rVal['num'] . '\');"><span>[삭제]</span></button>
									</div>';

							}
							?></p>
							<?if ( !empty($rVal['upfile']) || !empty($rVal['upfile2']) || !empty($rVal['upfile3']) || !empty($rVal['upfile4']) ) {?>
                                <ul class="img-list">
							<?
							if ( !empty($rVal['upfile']) ) echo "<li><img src='" . $Dir.DataDir."shopimages/review/" . $rVal['upfile'] . "' /></li>";
							if ( !empty($rVal['upfile2']) ) echo "<li><img src='" . $Dir.DataDir."shopimages/review/" . $rVal['upfile2'] . "' /></li>";
							if ( !empty($rVal['upfile3']) ) echo "<li><img src='" . $Dir.DataDir."shopimages/review/" . $rVal['upfile3'] . "' /></li>";
							if ( !empty($rVal['upfile4']) ) echo "<li><img src='" . $Dir.DataDir."shopimages/review/" . $rVal['upfile4'] . "' /></li>";
							?>
                                </ul>
							<?}?>
                                <div class="list-comment">
                                <form onsubmit="return false;">
                                <input type="hidden" name="pnum" value="<?=$rVal['idx']?>">
                                <input type="hidden" name="mem_id" value="<?=$_ShopInfo->getMemid()?>">
                                <input type="hidden" name="now_date" value="<?=date("Y.m.d")?>">
                                <input type="hidden" name="return" value="OK">
                                    <input type="text" name="review_comment">          
                                    <?php if(strlen($_ShopInfo->getMemid())==0) { ?>
                                    <button class="btn-def" type="button" onClick="javascript:goLogin();"><span>OK</span></button>
                                    <?php } else { ?>
                                    <button class="btn-def" type="button" onClick="javascript:review_comment_write(this);"><span>OK</span></button>
                                    <?php } ?>
                                </form>
								</div>
<?php
			if( count( $rVal['comment'] ) == 0 ){
				$class_add	= " hide";
			} // comment if
?>
                                <div class="list-con<?=$class_add?>" id="reply_comment_<?=$rVal['idx']?>">
<?
				foreach( $rVal['comment'] as $commentKey=>$commentVal ){

                    echo '<div class="list-comment"><ul>
                        <li class="data"><span class="id">' . $commentVal->id . '</span><span class="date">(' . substr($commentVal->regdt,0,4).".".substr($commentVal->regdt,4,2).".".substr($commentVal->regdt,6,2) . ')</span></li>
                        <li>' . $commentVal->content;

                    if ( $commentVal->id == $_ShopInfo->getMemid() ) {
                        echo ' <a class="btn-delete" href="javascript:;" onClick="javascript:delete_review_comment(this);" ids="' . $commentVal->no . '" ids2="' . $commentVal->pnum . '"><img src="../static/img/btn/close.png" alt="닫기"></a>';
                    }
                    echo '</li></ul></div>';

				} // comment foreach 
?>
                                </div>
                            </dd>
                        </dl>
                    </li>

<?php
		} // reviewList foreach
	} else {
?>
<?php
	} // reviewList else
?>
                </ul>
                <div class="paginate">
                    <div class="box">
                        <?=$paging->a_prev_page.' '.$paging->print_page.' '.$paging->a_next_page?>
                    </div>
                </div>
                <div class="btnwrap">
                    <div class="box">
<?php
	if((strlen($_ShopInfo->getMemid())==0) ) { //&& $_data->review_memtype=="Y"
?>
		<a class="btn-def" onclick='javascript:location.href="<?=$Dir.MDir."login.php?chUrl=".$_SERVER["REQUEST_URI"]?>";' >리뷰 글쓰기</a>
<?php
	} else if( ( (!empty($review_ordercode) && !empty($review_order_idx)) || $_ShopInfo->getStaffType() == 1) && strlen($_ShopInfo->getMemid()) > 0 ){ // && $_data->review_memtype=="Y"
?>
		<a class="btn-def" onClick="javascript:document.reviewForm.mode.value='write';document.reviewForm.submit();">리뷰 글쓰기</a>
<?php
	} else{
?>
		<a class="btn-def" onclick="javascript:alert('상품을 주문하신후에 후기 등록이 가능합니다. 마이페이지->주문상세내역에서 확인해주세요.');">리뷰 글쓰기</a>
<?php
	}
?>
                    </div>
                </div>
            </div>
		<form name=reviewForm method="POST" action="mypage_review_write.php">
		<input type="hidden" name="productcode" id="productcode" value="<?=$productcode?>" />
		<input type="hidden" name="ordercode" id="ordercode" value="<?=$review_ordercode?>" />
		<input type="hidden" name="productorder_idx" id="productorder_idx" value="<?=$review_order_idx?>" />
		<input type="hidden" name="review_num" id="review_num" value="0" />
		<input type="hidden" name="mode" id="mode" value="" />
		</form>
        </div>
        <!-- // 상품리뷰 -->
        
        <!-- 배송정보 -->
        <div class="js-tab-content goods-detail-content-shipping">
            <section>
                <h5>배송안내</h5>
                <dl>
                    <dt>배송업체</dt>
                    <dd>현대택배 (<a href="tel:1588-2121">1588-2121</a>)</dd>
                </dl>
                <dl>
                    <dt>배송비</dt>
                    <dd>
                        무료배송
                        단 50,000원 이상 구매 시 무료배송이며, 50,000원 미만 시 2.500원의 배송비가 지불됩니다.<br>
                        또한 이벤트 상품 중 배송비 적용 및 상품페이지에 단품구매 시 배송비 책정 상품의 경우 배송비가 적용될 수 있습니다.<br>
                        (타 쇼핑몰과 달리 도서, 도서산간지역도 추가 배송비가 없습니다)<br>
                        배송비는 한번에 결제하신 동일 주문번호, 동일 배송지 기준으로 부과됩니다 반품시에는 배송비가 환불되지 않습니다.
                    </dd>
                </dl>
                <dl>
                    <dt>배송기간</dt>
                    <dd>
                        평일 오전 9시 이전 입금 확인분에 한해 당일 출고를 원칙으로 합니다. 입금 확인 후 2~3일 이내 배송( 일, 공휴일 제외), 도서 산간지역은 7일 이내 배송됩니다.<br>
                        단, 물류 사정에 따라 다소 차이가 날 수 있습니다.
                    </dd>
                </dl>
            </section>
            <section>
                <h5>반품/교환 안내</h5>
                <dl>
                    <dt>반품/교환</dt>
                    <dd>
                        택배비<br>
                        반품배송비 : 고객님의 변심으로 인한 반품의 배송비는 고객님 부담입니다. 단, 상품불량 및 오배송 등의 이유로 반품하실 경우 반품 배송비는 무료입니다.<br>
                        고객변심으로 인한 반품/교환 시 왕복 또는 편도 배송비는 고객님의 부담입니다.<br>
                        (사이즈교환 포함이며, 최초 주문시 무료배송 받으신 경우 왕복 택배비가 부과됩니다.)<br>
                        맞교환은 불가하며 교환 반품 상품이 물류센터에 도착하여 확인 후 교환 배송상품이 배송됩니다.<br>
                        외환은행 12342-13-1234  예금주 (주)데코앤이<br>
                        (교환 및 환불 전용 계좌 입니다)<br>
                        <br>
                        신청방법<br>
                        1. 홈페이지 로그인 후 마이페이지 -&#62; 취소/교환/반품신청 선택 후 상세 주문내역에서 반품/교환 버튼을 선택<br>
                        2. 상품이 반송 완료되면 요청하신 상품으로 반품절차나 교환배송을 해드립니다.<br>
                        3. 교환은 동일 상품의 색상, 사이즈 교환만 가능하며, 다른 상품으로 교환을 원하시면 반품처리 하시고 신규 주문해주셔야 합니다.
                    </dd>
                </dl>
                <dl>
                    <dt>상품반송처</dt>
                    <dd>
                        현대택배 : 반송 수거 신청해드립니다.<br>
                        반송처 주소 : (138-130) 서울특별시 송파구 오금동 23-1 데코앤이 빌딩<br>
                        CASH 온라인 몰에서 현대택배로 상품을 수거 신청해드리니, 타 택배는 이용이 불가한 점 양해 부탁드립니다.
                    </dd>
                </dl>
            </section>
            <section>
                <h5>반품/교환 신청 기준</h5>
                <dl>
                    <dt>반품/교환</dt>
                    <dd>
                        주문상품 수령 후 사용및 착용하지 않으신 경우에 한해서,수령일로부터 7일 이내에 반품이 가능합니다.<br>
                        한번이라도 착용하여 새 제품과 다른 경우는 교환/환불이 불가합니다.<br>
                        제품에 붙어있는 택을 뜯었거나 상품의 본 박스(예:신발)에 낙서나 테이핑을 한 경우는 교환/환불이 불가합니다.
                    </dd>
                </dl>
            </section>
            <section>
                <h5>A/S</h5>
                <dl>
                    <dt>품질 보증기간</dt>
                    <dd>
                        구매일로부터 1년간<br>
                        A/S문의<br>
                        <a href="tel:02-2145-1400">02-2145-1400</a>
                    </dd>
                </dl>
            </section>
            <div class="btnwrap">
                <div class="box">
                    <a class="btn-def" href="#js-goods-detail-content" onClick="$('.js-tab-menu').eq(1).trigger('click');scroll_anchor($(this).attr('href'));return false;">상품리뷰</a>
                    <a class="btn-def" href="product_qna.php?productcode=<?=$productcode?>&pridx=<?=$_pdata->pridx?>">상품문의</a>
                </div>
            </div>
        </div>
        <!-- // 배송정보 -->
    </div>
    <!-- // 상품내용 -->
    
    <!-- 관련상품 -->
    <div class="js-goods-detail-related">
        <h4>RELATED PRODUCT</h4>
        <div class="page">
            <ul>
<?php
for( $i=0; $i<count( $related_html ); $i++ ){
?>
                <li class="js-carousel-page"><a href="javascript:;"><span class="ir-blind"><?=( $i + 1 )?></span></a></li>
<?php
}
?>
            </ul>
        </div>
        <div class="goods-detail-related-inner">
            <ul class="js-carousel-list">

<?php	
foreach( $related_html as $key=>$related ){
    echo $related;
} // related foreach
?>

            </ul>
        </div>
    </div>
    <!-- // 관련상품 -->
</main>

<?php
include_once('./outline/footer_m.php')
?>
