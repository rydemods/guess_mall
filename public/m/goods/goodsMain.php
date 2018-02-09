<?php
include_once('../outline/header.php');

$cate_code  = $_GET['code'];
$likestr    = $_REQUEST["likestr"];
$sort       = $_REQUEST["sort"];

$listnum=(int)$_REQUEST["listnum"];
if($listnum<=0) $listnum=6;

list($code_a,$code_b,$code_c,$code_d) = sscanf($cate_code,'%3s%3s%3s%3s');
if(strlen($code_a)!=3) $code_a="000";
if(strlen($code_b)!=3) $code_b="000";
if(strlen($code_c)!=3) $code_c="000";
if(strlen($code_d)!=3) $code_d="000";

$code=$code_a.$code_b.$code_c.$code_d;

$likecode=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

$_cdata="";
$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' order by cate_sort";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {

    $listname = $row->code_name;
    //접근가능권한그룹 체크
    if($row->group_code=="NO") {

        echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
    }
    if(strlen($_ShopInfo->getMemid())==0) {
        if(ord($row->group_code)) {
            echo "<html></head><body onload=\"location.href='".$Dir.FrontDir."login.php?chUrl=".getUrl()."'\"></body></html>";exit;
        }
    } else {
        if($row->group_code!="ALL" && ord($row->group_code) && $row->group_code!=$_ShopInfo->getMemgroup()) {
            alert_go('해당 카테고리 접근권한이 없습니다.',$Dir.MainDir."main.php");
        }
    }
    $_cdata=$row;
} else {
    echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
}
pmysql_free_result($result);

// ===================================================================
// 대카테고리 상단 배너 롤링
// ===================================================================
$sql  = "SELECT * FROM tblmainbannerimg ";
$sql .= "WHERE banner_no = 90 and banner_hidden = 1 and banner_category like '{$cate_code}%' ";
$sql .= "ORDER BY banner_number desc ";
$result = pmysql_query($sql);

$cnt = 0;

$banner_rolling_html = '';
$banner_rolling_page_html = '';
while ($row = pmysql_fetch_array($result)) {
    if ( empty($row['banner_img_m']) ) { continue; }

    $linkUrl = $row['banner_link'];
    if ( empty($linkUrl) ) {
        $linkUrl = "javascript:;";
    }

    $banner_rolling_html .= '
        <li class="js-goods-hero-content">
            <a href="' . $linkUrl . '" target="' . $row['banner_target'] . '"><img src="/data/shopimages/mainbanner/' . $row['banner_img_m'] . '" alt="ALWAYS NEW TRADITION BYLORDY NEW BRAND"></a>
        </li>';

    $banner_rolling_page_html .= '<li class="js-goods-hero-page"><a href="#"><span class="ir-blind">1</span></a></li>';

    $cnt++;
}
pmysql_free_result($result);

$top_banner_html = '';
if ( $cnt > 0 ) {
    $top_banner_html = '
            <div class="js-goods-hero">
                <div class="js-goods-hero-list">
                    <ul>';

    $top_banner_html .= $banner_rolling_html;

    $top_banner_html .= '
                    </ul>
                </div>
                <div class="page">
                    <ul>';

    $top_banner_html .= $banner_rolling_page_html;

    $top_banner_html .= '
                    </ul>
                </div>';

    if ( $cnt >= 2 ) {
        $top_banner_html .= '
                <button class="js-goods-hero-arrow" data-direction="prev" type="button"><img src="../static/img/btn/btn_slider_arrow_prev.png" alt="이전"></button>
                <button class="js-goods-hero-arrow" data-direction="next" type="button"><img src="../static/img/btn/btn_slider_arrow_next.png" alt="다음"></button>';
    }

    $top_banner_html .= '</div>';
}

// ===================================================================
// 대카테고리 이름 조회
// ===================================================================
$sql  = "select * from tblproductcode where code_a = '{$cate_code}' and code_b = '000' limit 1";
$result = pmysql_query($sql);
$row = pmysql_fetch_object($result);
$cate_name = $row->code_name;

// ===================================================================
// 브랜드 리스트
// ===================================================================
$sql  = "select * from tblproductbrand where productcode_a = '{$cate_code}' and display_yn = 1 order by bridx asc ";
$result = pmysql_query($sql);

$idx = 0;
$countPerList = 10;

$brand_list_html = '';
while ( $row = pmysql_fetch_array($result) ) {
    $brand_list_html .= '<option val=\"' . $row['bridx'] . '\">' . $row['brandname'] . '</option>';
}

// ===================================================================
// 상품 리스트
// ===================================================================

$qry = "WHERE link.c_category LIKE '".$likecode."%' ";

//$qry.="AND (  a.mall_type = 0 OR a.mall_type = '".$_ShopInfo->getAffiliateType()."' ) "; // 해당 몰관련 상품만 보여줌 (2015.11.10 - 김재수)

$qry.="AND a.display='Y' ";

//아이템별 검색
$item_cate = $_REQUEST['item_cate'];
if($item_cate){
	$qry.="AND a.itemcate={$item_cate} ";
}
//브랜드별 검색
$brand = $_REQUEST['bridx'];
if($brand){
	$sql_brand = "SELECT c_productcode FROM tblproductlink ";
	$sql_brand.= "WHERE c_category like '{$brand}%'";
	$qry.="AND a.productcode in ({$sql_brand}) ";
	}
//검색어
if($likestr){
	$qry.="AND a.productname LIKE '%{$likestr}%' ";
}


	$sql = "SELECT DISTINCT(a.productcode) AS dis, * FROM tblproduct AS a ";
	$sql.= "JOIN tblproductlink link on(a.productcode=link.c_productcode AND c_maincate=1) ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";
	//exdebug($likeBrand);
	if($likeCate){
		$sql.=$likeCate;
	} else {
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') AND staff_product != '1' ";
	}
	if($likeBrand){
		$sql.=$likeBrand;
	}
	if(strlen($not_qry)>0) {
		$sql.= $not_qry." ";
	}
	//$listnum
	//exdebug($sql);

	$paging = new New_Templet_mobile_paging($sql,3,$listnum,'GoPage_Mobile',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	//번호, 사진, 상품명, 제조사, 가격
	$tmp_sort=explode("_",$sort);
	if($tmp_sort[0]=="reserve") {
		$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
	}

	$sql = "SELECT a.productcode, a.productname, a.overseas_type, a.buyprice, a.keyword, a.mdcomment, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, a.mdcomment, a.review_cnt, ";
	if($_cdata->sort=="date2") $sql.="CASE WHEN a.quantity<=0 THEN '11111111111111' ELSE a.date END as date, ";
	$sql.= "a.maximage, a.minimage,a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
	$sql.= $addsortsql;

	$sql.= "FROM (select *, case when (consumerprice - sellprice) <= 0 then 0 else (consumerprice - sellprice) end as saleprice from tblproduct) AS a  ";
	$sql.= "JOIN tblproductlink link on(a.productcode=link.c_productcode AND c_maincate=1) ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";

	if($likeCate){
		//exdebug($likeCate);
		$sql.=$likeCate;
	}
	if($likeBrand){
		//exdebug($likeBrand);
		$sql.=$likeBrand;
	}
	if(strlen($not_qry)>0) {
		$sql.= $not_qry." ";
	}

	if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="rcnt") $sql.= "ORDER BY a.review_cnt ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="saleprice") $sql.= "ORDER BY a.saleprice ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
    elseif($tmp_sort[0]=="opendate") $sql.= "ORDER BY a.regdate DESC, pridx ASC ";
	elseif($tmp_sort[0]=="dcprice") $sql.= "ORDER BY case when consumerprice>0 then  100 - cast((cast(sellprice as float)/cast(consumerprice as float))*100 as integer) else 0 end desc ";
	elseif($tmp_sort[0]=="order" ){
		$bestsql="select COALESCE(sum(cnt),0) sumcnt, a.productcode from tblproduct a left join tblcounterproduct b on (a.productcode=b.productcode) where a.productcode like '".$likecode."%' group by a.productcode order by sumcnt desc";
		$bestresult=pmysql_query($bestsql);

		$count=0;
		$lk=1;
		$casewhen="";
		while($bestrow=pmysql_fetch_object($bestresult)){
			$productcode[$count]=$bestrow->productcode;
			$casewhen[]=" '".$bestrow->productcode."' then ".$lk;
			$count++;
			$lk++;
		}
		$sql.= "ORDER BY a.start_no asc, modifydate desc";
	}else if($tmp_sort[0]=="best"){
		$sql.= "ORDER BY a.start_no desc ";
		if(count($lk_casewhen)>0) $sql.= " ,case a.productcode when ".implode(" when ",$lk_casewhen)." end ";
	}else {
		if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
			if(strstr($_cdata->type,"T") && strlen($t_prcode)>0) {
				$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
			} else {
				$sql.= "ORDER BY opendate DESC ";
				//$sql.= "ORDER BY a.start_no desc,case a.productcode when ".implode(" when ",$lk_casewhen)." end ";
			}
		} elseif($_cdata->sort=="productname") {
			$sql.= "ORDER BY a.start_no desc,a.productname ";
		} elseif($_cdata->sort=="production") {
			$sql.= "ORDER BY a.start_no desc,a.production ";
		} elseif($_cdata->sort=="price") {
			$sql.= "ORDER BY a.start_no desc,a.sellprice ";
		}
	}

	$sql = $paging->getSql($sql);

    $list_array = productlist_print( $sql, $type = 'W_015' );

    // =============================================================================================================================
    // 테스트 버젼
    // =============================================================================================================================

if ( false ) {
    $sql  = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.brand, a.maximage, a.minimage, a.tinyimage, ";
    $sql .= "a.mdcomment, a.review_cnt, a.icon, ";
    $sql .= "(a.consumerprice - a.sellprice) as diffprice ";
    $sql .= "FROM tblproduct a ";
    $sql .= "WHERE a.productname <> '' ";
//    $sql .= "LIMIT 200 ";

	$paging = new New_Templet_mobile_paging($sql,3,$listnum,'GoPage_Mobile',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

    $sql = $paging->getSql($sql);

    $list_array = productlist_print( $sql, $type = 'W_015' );
}

?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2><?=$cate_name?></h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<!-- 히어로 배너 -->
			<!--div class="js-goods-hero">
				<div class="js-goods-hero-list">
					<ul>
						<li class="js-goods-hero-content"><a href="#"><img src="../static/img/test/@goods_main_hero1.jpg" alt="ALWAYS NEW TRADITION BYLORDY NEW BRAND"></a></li>
						<li class="js-goods-hero-content"><a href="#"><img src="../static/img/test/@goods_main_hero2.jpg" alt="ALWAYS NEW TRADITION BYLORDY NEW BRAND"></a></li>
					</ul>
				</div>
				<div class="page">
					<ul>
						<li class="js-goods-hero-page"><a href="#"><span class="ir-blind">1</span></a></li>
						<li class="js-goods-hero-page"><a href="#"><span class="ir-blind">2</span></a></li>
					</ul>
				</div>
				<button class="js-goods-hero-arrow" data-direction="prev" type="button"><img src="../static/img/btn/btn_slider_arrow_prev.png" alt="이전"></button>
				<button class="js-goods-hero-arrow" data-direction="next" type="button"><img src="../static/img/btn/btn_slider_arrow_next.png" alt="다음"></button>
			</div-->
            <?=$top_banner_html?>
			<!-- // 히어로 배너 -->

			<!-- 정렬 -->
			<div class="goods-range">
				<div class="container">
					<div class="select-def">
						<select>
							<option value="a1">WOMEN</option>
						</select>
					</div>
					<div class="select-def">
						<select>
							<option value="b1">WOMENWEAR</option>
						</select>
					</div>
					<div class="select-def">
						<select>
							<option value="c1">COAT&#38;JACKET</option>
						</select>
					</div>
				</div>
				<div class="container">
					<div class="select-def">
						<select onchange="javascript:changeBrand_Mobile(this);">
							<option value="">ALL BRAND</option>
                            <?=$brand_list_html?>
						</select>
					</div>
					<div class="box">
                        <?=getSearchOptForMobile($sort)?>
					</div>
				</div>
			</div>
			<!-- // 정렬 -->

			<!-- 상품 리스트 -->
			<div class="goods-list">
				<div class="container">
					<p class="note">총 <?=number_format($t_count)?>개의 상품이 진열되어 있습니다.</p>
					<div class="list-type">
						<button class="js-goods-type on" data-type="double"><img src="../static/img/btn/btn_goods_list_type_double.png" alt="2열로 보기"></button>
						<button class="js-goods-type" data-type="single"><img src="../static/img/btn/btn_goods_list_type_single.png" alt="1열로 보기"></button>
					</div>
				</div>

				<!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
                <?=$list_array[0]?>


                <div class="paginate">
                    <div class="box">
                    <?php
                        if( $paging->pagecount > 1 ){
                            echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;
                        }
                    ?>
                    </div>
                </div>


<!--
				<div class="paginate">
					<div class="box">
						<a class="btn-page-first" href="#"><span class="ir-blind">처음</span></a>
						<a class="btn-page-prev" href="#"><span class="ir-blind">이전</span></a>
						<ul>
							<li class="on" title="선택됨"><a href="#">1</a></li>
							<li><a href="#">2</a></li>
							<li><a href="#">3</a></li>
						</ul>
						<a class="btn-page-next" href="#"><span class="ir-blind">다음</span></a>
						<a class="btn-page-last" href="#"><span class="ir-blind">마지막</span></a>
					</div>
				</div>
-->

			</div>
			<!-- // 상품 리스트 -->

            <form name="form_m" method=get action="<?=$_SERVER['PHP_SELF']?>">
                <input type=hidden name=listnum value="<?=$listnum?>">
                <input type=hidden name=sort value="<?=$sort?>">
                <input type=hidden name=block value="<?=$block?>">
                <input type=hidden name=gotopage value="<?=$gotopage?>">
                <input type=hidden name=bridx value="<?=$bridx?>">
                <input type=hidden name=search_word value="<?=$search_word?>">
                <input type=hidden name=code value="<?=$cate_code?>">
            </form>
		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>
