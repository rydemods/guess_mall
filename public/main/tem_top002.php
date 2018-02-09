<?php
/********************************************************************* 
// 파 일 명		: tem_top001.php 
// 설     명		: 상단 템플릿
// 상세설명	: 상단 ( 대메뉴, 검색, 로그인, 회원가입) 템플릿
// 작 성 자		: hspark
// 수 정 자		: 2015.11.02 - 김재수
// 
// 
*********************************************************************/ 

include_once($Dir."lib/basket.class.php");  // 장바구니 내용을 구하기 위해서

$mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';

// 모바일인지 pc인지 체크
/*
if(preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && !$_GET[pc]) {
    //if($_SERVER["REMOTE_ADDR"] == '218.234.32.102'){ // 모바일일경우 이동하는 경로 재설정(2015.12.24 - 김재수)
        
    
    $m_referrer_tmp			= parse_url($_SERVER['HTTP_REFERER']);
    $m_referrer_url			= $m_referrer_tmp['host'];

        if ((strpos($_SERVER["REQUEST_URI"],'/front/') !== false || strpos($_SERVER["REQUEST_URI"],'/board/') !== false) && $m_referrer_url != $_SERVER['HTTP_HOST']) { // 서브페이지로 올 경우에만 적용하고 아닐경우는 index.php 에서 경로 재설정을 한다.
            //게시판일 경우
            if ($_GET['board']) {
                $mainurl= str_replace('/board/','/m/',$_SERVER["REQUEST_URI"]);
                if ($_GET['pagetype'] == 'view') { // 상세보기 일 경우
                    if ($_GET['board'] == 'event') { // 이벤트 상세 보기일 경우
                        $mainurl= "/m/event_view.php";
                    } else {
                        $mainurl= "/m/board_view.php";
                    }					
                    $mainurl .= "?board=".$_GET['board']."&boardnum=".$_GET['num'];
                }
            } else {
                $mainurl= str_replace('/front/','/m/',$_SERVER["REQUEST_URI"]);
                $mainurl= str_replace('csfaq.php','customer_faq.php',$mainurl); // FAQ 경로 재설정
            }
            //echo $mainurl;
            Header("Location: ".$mainurl);
            exit;
        }
    //}
}
*/

//1:1 문의를 위한 회원 데이터를 가져온다.
$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
    $hptemp	= explode('-',$row->mobile);
    $c_hp0		= $hptemp[0];
    $c_hp1		= $hptemp[1];
    $c_hp2		= $hptemp[2];
    $c_email	= $row->email;
}
pmysql_free_result($result);

#상품 페이스북 공유
$facebook_share = '';
if( $_GET['productcode'] ){
    $facebook_share = FacebookShare( $_GET['productcode'] );
    $twitter_share = TwitterShare( $_GET['productcode'] );
}

// =====================================================================================================================================
// 장바구니
// =====================================================================================================================================
$Basket = new Basket();
$arrProdCode = array();
foreach( $Basket->basket as $bkVal ){
    array_push($arrProdCode, $bkVal->productcode);
}
$basket_products_html = MakeHeaderPreviewList('basket', count($arrProdCode), get_product_list($arrProdCode), '/front/basket.php');

// =====================================================================================================================================
// 위시리스트
// =====================================================================================================================================
$arrProdCode = array();
if ( $_ShopInfo->getMemid() != "" ) {
    $sql  = "SELECT productcode FROM tblwishlist WHERE id = '" . $_ShopInfo->getMemid() . "' ORDER BY wish_idx desc ";
    $result = pmysql_query($sql);

    while ( $row = pmysql_fetch_array($result) ) {
        array_push($arrProdCode, $row['productcode']); 
    }
}
$wishlist_products_html = MakeHeaderPreviewList('wish', count($arrProdCode), get_product_list($arrProdCode), '/front/wishlist.php');

// =====================================================================================================================================
// 최근 본 상품
// =====================================================================================================================================
$today_product = today_product();
$recent_view_products_html = MakeHeaderPreviewList('view', count($today_product), $today_product, '/front/lately_view.php');

// =====================================================================================================================================
// 검색어 리스트
// =====================================================================================================================================
$arrSearchKeyword = explode( ",", $_data->search_info['keyword'] );

// =====================================================================================================================================
// My Keyword
// =====================================================================================================================================
$arrMyKeyword = array();

if ( $_ShopInfo->getMemid() != "" ) {
    $sql  = "SELECT * FROM tblmykeyword ";
    $sql .= "WHERE id = '" . $_ShopInfo->getMemid() . "' ";
    $sql .= "ORDER BY regdate desc ";
    $sql .= "LIMIT 9";

    $result = pmysql_query($sql);
    while ( $row = pmysql_fetch_array($result) ) {
        array_push($arrMyKeyword, $row['keyword']);
    }
}

?>

<!DOCTYPE html>
<html lang="ko">
<head>
<title><?=$_data->shoptitle?></title>
<meta charset="UTF-8">
<!-- (D) w3c validator에서 오류 메시지가 떠서 chrome=1 주석 처리합니다. 필요하시면 http://qnacode.com/85/ie%EC%97%90%EC%84%9C-%EC%9E%90%EA%BE%B8-%ED%98%B8%ED%99%98%EC%84%B1-%EB%B3%B4%EA%B8%B0-%EB%AA%A8%EB%93%9C%EB%A1%9C-%EB%93%A4%EC%96%B4%EA%B0%80%EC%84%9C-%ED%99%94%EB%A9%B4%EC%9D%B4-%EC%9D%B4%EC%83%81%ED%95%B4%EC%A7%91%EB%8B%88%EB%8B%A4 참고하셔서 서버단에서 설정하시는게 좋겠습니다. -->
<!-- meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" -->

<meta name="Generator" content="">
<meta name="Author" content="">
<meta name="Keywords" content="<?=$_data->shopkeyword?>">
<meta name="Description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<!-- 페이스북 쉐어 2016-02-11 유동혁 -->
<?=$facebook_share?>
<!-- 트위터 쉐어 2016-02-11 유동혁 -->
<?=$twitter_share?>
<script type="text/javascript" src="../static/js/jquery-1.12.0.min.js" ></script>
<script type="text/javascript" src="../static/js/select_type01.js" ></script>
<script type="text/javascript" src="../static/js/deco_ui.js" ></script>
<script type="text/javascript" src="../static/js/deco_dev.js" ></script>
<script type="text/javascript" src="../static/js/TweenMax-1.18.2.min.js" ></script>

<script type="text/javascript" src="../static/js/jquery.bxslider.min.js" ></script>
<script type="text/javascript" src="../static/js/jquery.nanoscroller.js" ></script>
<script type="text/javascript" src="../static/js/jquery.nanoscroller.min.js" ></script>
<script type="text/javascript" src="../static/js/jquery.mCustomScrollbar.concat-3.1.3.min.js" ></script>
<script type="text/javascript" src="../static/js/ion.rangeSlider.min.js" ></script>
<!--[if gteIE 10]>
<script type="text/javascript" src="../static/js/jquery.gray.min.js"></script>
<![endif]-->

<link rel="stylesheet" href="../static/css/ion.rangeSlider.css">
<link rel="stylesheet" href="../static/css/ion.rangeSlider.skinHTML5.css">
<link rel="stylesheet" href="../static/css/jquery.mCustomScrollbar-3.1.3.css">
<link rel="stylesheet" href="../static/css/common.css">
<link rel="stylesheet" href="../static/css/component.css">
<link rel="stylesheet" href="../static/css/content.css">

<script src="../lib/lib.js.php" type="text/javascript"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<?php include_once($Dir.LibDir."analyticstracking.php") ?>
<script language="JavaScript">
<!--
	function inquiryCheckForm() {
		var f	= document.forminquiry;
		if(f.up_email.value.length>0) {
			if(!IsMailCheck(f.up_email.value)) {
				alert("이메일 입력이 잘못되었습니다.");
				f.up_email.focus();
				return;
			}
		} else {
			alert("이메일을 입력하세요.");
			f.up_email.focus();
			return;
		}
		if(f.up_subject.value.length==0) {
			alert("문의제목을 입력하세요.");
			f.up_subject.focus();
			return;
		}
		if(f.up_content.value.length==0) {
			alert("문의내용을 입력하세요.");
			f.up_content.focus();
			return;
		}

		var postData = $("#forminquiry").serializeArray();
		var formURL = $("#forminquiry").attr("action");
		$.ajax(
		{
			url : formURL,
			type: "POST",
			data : postData,
			dataType:"json", 
			success: function(data) {
				alert(data.msg);
				location.href	= "../front/mypage_personal.php";
			},
			error: function(result) {
				alert("에러가 발생하였습니다."); 
			}
		});

	}
	function inquiryResetForm() {
		var f	= document.forminquiry;
		f.up_subject.value	= "";
		f.up_content.value	= "";
		f.hp0.value	= "";
		f.hp1.value	= "";
		f.hp2.value	= "";
		f.chk_mail.checked	= "";
		f.chk_sms.checked	= "";
		$(".pop_info").hide();
	}
	function menuLoginCheck(url, view) {
		<?if(strlen($_ShopInfo->getMemid())==0){ ?>
		alert("로그인 후 이용해 주십시오.");
		<?} else {?>
		if (view == 'on')
		{
			location.href = url;
		} else {
			alert("서비스 준비중입니다.");
		}
		<?}?>
	}
	function proSearchChk() {
        if ( $("#search").val().trim() === "" ) {
			alert("검색어를 입력해주세요.");
            $("#search").val("").focus();
			return false;
		}

		document.formForSearch.submit();
	}

    $(document).ready(function() {

        function getCurrentClassNameAndLastPage(obj) {
            var arrResult = new Array();
            var topObj = $(obj).parent().parent().parent().parent().parent();

            arrResult[0] = $(topObj).attr('class');
            arrResult[1] = $(topObj).attr("ids");

            return arrResult;
        }

        // 상단 BASKET, WISH, VIEW 레이어 하단 페이징 클릭시 페이지 번호 변경
        $(".bx-prev").on("click", function() {
            var arrResult = getCurrentClassNameAndLastPage(this);
            var className = arrResult[0];
            var lastPage = arrResult[1];

            if ( lastPage == 2 ) {
                var obj = $("#page-" + className);
                if ( obj ) {
                    $(obj).html('<strong>1</strong> / ' + lastPage);
                }
            }
        });


        $(".bx-next").on("click", function() {
            var arrResult = getCurrentClassNameAndLastPage(this);
            var className = arrResult[0];
            var lastPage = arrResult[1];
            
            if ( lastPage == 2 ) {
                var obj = $("#page-" + className);
                if ( obj ) {
                    $(obj).html('<strong>2</strong> / '+ lastPage + '');
                }
            }
        });

        // 검색기록삭제 버튼 클릭
        $("#del_mykeyword").on("click", function() {
            $.ajax({
                url: "ajax_delete_mykeyword.php",
                type: "GET",
            }).success(function(data){
                if ( data === "SUCCESS" ) {
                    //alert("삭제되었습니다.");
                    $("#my-keyword li").remove();
                } else {
                    alert("삭제가 실패했습니다.");
                }
            }).error(function () {
                alert("다시 시도해 주세요.");
            });
        });

    });


//-->
</script>
</head>

<body>
<!-- (D) 왜 필요한지 모르겠어서 주석처리합니다. 
	<a name="top"></a>
-->
<div id="header" class="<?if($popup == 'ok') echo "hide";?>">







<!-- <div id="wrap"> -->
  <div class="header-wrap">
    <div class="top-divide">
        <? if ( $_SERVER['REQUEST_URI'] == "/" ) { // 메인페이지에서만 나오게 ?>
		<ul class="scroll-move hide">
			<li><button class="up section1" type="button"></button></li>
			<li><button class="down section1" type="button"></button></li>
			<li><button class="top" type="button"></button></li>
		</ul>
        <? } ?>
      <h1 class="header-logo"><a href="/"><img src="../static/img/common/h1_logo.png" alt="<?=$_data->shoptitle?>"></a></h1>
      <div class="nav-top-menu-wrap">
        <ul class="nav-top-menu">
          <li><a href="javascript:addBookmark();" title="즐겨찾기"><img src="../static/img/btn/btn_nav_fav.png" alt="즐겨찾기"></a></li>
<?if(strlen($_ShopInfo->getMemid())==0){?>
		  <li><a href="<?=$Dir.FrontDir?>login.php?chUrl=<?=$_SERVER[REQUEST_URI]?>">LOGIN</a></li>
		  <li><a href="<?=$Dir.FrontDir?>member_agree.php">JOIN US</a></li>
<?}else{?>
		  <li><a href="javascript:logout();">LOGOUT</a></li>
<?}?>
		  <li><a href="<?=$Dir.FrontDir?>mypage.php">MY PAGE</a></li>
		  <li><a href="/board/board.php?board=notice">CS CENTER</a></li>
        </ul>
        
		<ul class="see-icon">

            <!-- 장바구니 내역 -->
            <?=$basket_products_html?>
            <!-- 장바구니 내역 End -->

            <!-- 위시리스트 -->
            <?=$wishlist_products_html?>
            <!-- 위시리스트 End -->

            <!-- 최근 본 상품 -->
            <?=$recent_view_products_html?>
            <!-- 최근 본 상품 End -->

			<li class="search">
				<a href="javascript:;"><span class="count">&nbsp;</span><p>SEARCH</p></a>
				<div class="list_wrap searchLayer">
				
				<div class="gnb-search-box" data-ui="GnbSch">       
				  <form name=formForSearch action="../front/productsearch.php" method=get onsubmit="proSearchChk();return false;">
					<fieldset>
					  <legend>상품검색어 입력</legend>
					  <input name="search" id="search"  type="text" title="검색어 입력자리" placeholder="검색어를 입력해주세요." >
					  <button type="submit"><span>SEARCH</span></button>
					</fieldset>
					<div class="hot-keyword">
                        <?php for ( $i = 0; $i < 4; $i++ ) { // 인기키워드 중 상위 4개 ?>
                        <a href="/front/productsearch.php?search=<?=urlencode($arrSearchKeyword[$i])?>&thr=sw"><?=$arrSearchKeyword[$i]?></a>
                        <?php } ?>
					</div>
					<div class="gnb-search-preview">
					  <div class="hot-box">
						<h2>hot keyword</h2>
						<ol>
                            <?php for ( $i = 0; $i < count($arrSearchKeyword); $i++ ) { ?>
                            <li><a href="/front/productsearch.php?search=<?=urlencode($arrSearchKeyword[$i])?>&thr=sw"><span><?=$i+1?>.</span><?=$arrSearchKeyword[$i]?></a></li>
                            <?php } ?>
						</ol>
					  </div>
					  <div class="my-box">
						<h2>my keyword</h2>
						<ul id="my-keyword">
                            <?php for ( $i = 0; $i < count($arrMyKeyword); $i++ ) { ?>
                            <li><a href="/front/productsearch.php?search=<?=urlencode($arrMyKeyword[$i])?>&thr=sw"><?=$arrMyKeyword[$i]?></a></li>
                            <?php } ?>
						</ul>

                        <?php if ( $_ShopInfo->getMemid() != "" ) { ?>
						<footer>
						  <button type="button" id="del_mykeyword">검색기록삭제</button>
						</footer>
                        <?php } ?>
					  </div>
					</div>

                    <input type="hidden" name="thr" value="sw" />
				  </form>
				</div>
				
				</div>
			</li>
		</ul>
      </div>
      <!-- //.nav-top-menu-wrap --> 
      
    </div>
    <!-- //.top-divide -->


<?php
    // =====================================================================================================
    // 카테고리별 레이어 구성하기
    // =====================================================================================================
?>

    
    <div class="menu-divide">
	  <div class="fake-layer"></div>
      <div class="cate-menu-wrap">
        <ul>
<?
		// 좌측 메뉴를 불러온다.
		$nav_sql	= "select * from tblmainmenu where menu_type ='1' and menu_display = '1' order by menu_sort asc";			
		$nav_res = pmysql_query($nav_sql,get_db_conn());
		while($nav_row = pmysql_fetch_object($nav_res)){
			$nav_title	= $nav_row->menu_title;
			if(strpos($nav_row->menu_url, "javascript") !== false) {
				$nav_url		= $nav_row->menu_url;
			} else {
				$nav_url		= $Dir.$nav_row->menu_url;
			}
?>
          <li><a href="<?=$nav_url?>"><h2><?=$nav_title?></h2></a></li>
<?
		}
		pmysql_free_result($nav_res);

        // ================================================================================================================================
        // 1차 카테고리
        // ================================================================================================================================

		$cateListA_sql = "
		SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx
		FROM tblproductcode
		WHERE code_b = '000'
		AND group_code !='NO' AND display_list is NULL
		ORDER BY code_a,code_b,code_c,code_d ASC , cate_sort ASC";

		$cateListA_res = pmysql_query($cateListA_sql,get_db_conn());

		### 상단 메뉴 2/3차 카테고리 가져오기 끝

		while($cateListA_row = pmysql_fetch_object($cateListA_res)){
?>
          <li><a href="<?=$Dir.FrontDir."productlist.php?code=".$cateListA_row->code_a?>"><h2><?=$cateListA_row->code_name?></h2></a>
            <div class="container_wrap">
              <div class="container_box">

<?php
    // ================================================================================================================================
    // 1차 카테고리에 등록한 상품 리스트 구하기 (최대 3개)
    // ================================================================================================================================
    $banner_no = 110;

    $sub_sql  = "SELECT banner_title FROM tblmainbannerimg ";
    $sub_sql .= "WHERE banner_no = {$banner_no} AND banner_hidden = 1 AND banner_category like '" . $cateListA_row->code_a . "%' ";
    $sub_sql .= "ORDER BY no desc limit 1 ";
    $sub_result = pmysql_query($sub_sql);
    $sub_row = pmysql_fetch_object($sub_result);
    $gnb_banner_title = $sub_row->banner_title;
    pmysql_free_result($sub_result);

    $sub_sql  = "SELECT productcode  ";
    $sub_sql .= "FROM tblmainbannerimg_product ";
    $sub_sql .= "WHERE tblmainbannerimg_no = ";
    $sub_sql .= "(SELECT no FROM tblmainbannerimg WHERE banner_no = {$banner_no} AND banner_hidden = 1 AND banner_category like '" . $cateListA_row->code_a . "%' ";
    $sub_sql .= "ORDER BY no desc limit 1) ";
    $sub_sql .= "ORDER BY no asc LIMIT 3";
    $sub_result = pmysql_query($sub_sql);

    $arrProdCode = array();
    while ($sub_row = pmysql_fetch_object($sub_result)) {
        array_push($arrProdCode, $sub_row->productcode);
    }
    pmysql_free_result($sub_result);

    $whereProdCode = implode("','", $arrProdCode);

    $sub_sql  = "SELECT a.*, (SELECT brandname FROM tblproductbrand WHERE bridx = a.brand) as brandname ";
    $sub_sql .= "FROM tblproduct a, tblproductlink b ";
    $sub_sql .= "WHERE a.productcode = b.c_productcode and a.display = 'Y' and b.c_maincate = 1 and a.productcode in ('{$whereProdCode}') ";
    $sub_sql .= "and a.regdate is not null ";
    $sub_sql .= "ORDER BY FIELD(a.productcode, '$whereProdCode') ";
    $sub_sql .= "LIMIT 3";

    $sub_result = pmysql_query($sub_sql);

    $new_arrival_html = '';
    while ( $sub_row = pmysql_fetch_array($sub_result) ) {
//        $new_arrival_html .= '<li><a href="/front/productdetail.php?productcode=' . $prod_code . '"><span class="img"><img src="/data/shopimages/product/' . $prod_thumb . '" alt="' . $prod_name . '" width="230" height="230"></span><span class="nm">' . $prod_name . '</span></a></li>';

        $prodImg = getProductImage($Dir."/data/shopimages/product/", $sub_row['minimage']);

        $new_arrival_html .= '<li><a href="/front/productdetail.php?productcode=' . $sub_row['productcode'] . '"><span class="img"><img src="' . $prodImg . '" alt="' . $prod_name . '" width="230" height="230"></span>
                    <div class="info-list">
                        <p class="goods-brand">' . $sub_row['brandname'] . '</p>
                        <p class="goods-nm">' . $sub_row['productname'] . '</p>
                        <p class="price">';

        if ( $sub_row['consumerprice'] != "0" ) {
            $new_arrival_html .= '<del>' . number_format($sub_row['consumerprice']) . '</del>';
        }

        $new_arrival_html .= number_format($sub_row['sellprice']) . '
                        </p>
                    </div>
            </a></li>';


    }

    // ================================================================================================================================
    // 2,3차 카테고리 리스트 보여주기
    // ================================================================================================================================

    $sub_sql  = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx,cate_sort FROM tblproductcode ";
    $sub_sql .= "WHERE code_a = '" . $cateListA_row->code_a . "' AND code_b != '000' AND group_code !='NO' AND display_list is NULL ";
    $sub_sql .= "ORDER BY code_a, code_b, code_c, code_d, cate_sort ASC ";

    $sub_result = pmysql_query($sub_sql);

    // 4depth 카테고리를 따로 배열에 저장

    $arrSecondDepthCate = array();  // 2차 카테고리 

    $arrLastDepthCate = array();    // 4차 카테고리
    while ( $sub_row = pmysql_fetch_array($sub_result) ) {
        if ( $sub_row['code_c'] == "000" ) {
            // 2차 카테고리
            array_push($arrSecondDepthCate, array($sub_row['cate_sort'], $sub_row['code_a'], $sub_row['code_b'], $sub_row['code_c'], $sub_row['code_d'], $sub_row['code_name']));
        } elseif ( $sub_row['code_d'] !== "000" ) {
            // 4차 카테고리
            $arrKey = $sub_row['code_a'].$sub_row['code_b'].$sub_row['code_c'];

            // 3depth 밑에 4depth를 배열에 저장
            if ( !isset($arrLastDepthCate[$arrKey]) ) {
                $arrLastDepthCate[$arrKey] = array();
            }
//            array_push($arrLastDepthCate[$arrKey], '<li><a href="' . $Dir. FrontDir . "productlist.php?code=" . $sub_row['cate_code'] . '">' . $sub_row['code_name'] . '</a></li>');
            array_push($arrLastDepthCate[$arrKey], array($sub_row['cate_sort'], '<li><a href="' . $Dir. FrontDir . "productlist.php?code=" . $sub_row['cate_code'] . '">' . $sub_row['code_name'] . '</a></li>') );
        }
    }
    pmysql_free_result($sub_result);

    sort($arrSecondDepthCate);

    $category_list_html = '';
    foreach ( $arrSecondDepthCate as $arrCateInfo ) {
        $firstCateCode = $arrCateInfo[1];
        $secondCateCode = $arrCateInfo[2];

        if ( $category_list_html !== "" ) {
            $category_list_html .= '</ul></div>';
        } 

        $category_list_html .= '<div class="submenu_container">
                                    <h3>' . $arrCateInfo[5] . '</h3>
                                    <ul>';

        $sub_sql  = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx FROM tblproductcode ";
        $sub_sql .= "WHERE code_a = '" . $firstCateCode . "' AND code_b = '" . $secondCateCode . "' AND code_c <> '000' AND code_d = '000' ";
        $sub_sql .= "AND group_code !='NO' AND display_list is NULL ";
        $sub_sql .= "ORDER BY cate_sort ASC ";

        $sub_result = pmysql_query($sub_sql);

        while ( $sub_row = pmysql_fetch_array($sub_result) ) {
            $cate_code = $sub_row['cate_code'];
            $cate_code_3depth = $sub_row['code_a'].$sub_row['code_b'].$sub_row['code_c'];

            if ( isset($arrLastDepthCate[$cate_code_3depth]) ) {
                $category_list_html .= '<li>';
            } else {
                $category_list_html .= '<li class="depth4-none">';
            }

            $category_list_html .= '<a href="' . $Dir.FrontDir."productlist.php?code=" . $cate_code . '">' . $sub_row['code_name'] . '</a>';

            if ( isset($arrLastDepthCate[$cate_code_3depth]) ) {
                $overClassName = "";

                // 카테고리가 5개 이상인 경우에 클래스 부여
                if ( count($arrLastDepthCate[$cate_code_3depth]) >= 5 ) {
                    $overClassName = "over-ea5";
                }

                $category_list_html .= '<div class="depth4 ' . $overClassName . '">
                    <ul class="depth4-list">';

                // 4depth를 노출순으로 정렬
                $arrTmp = $arrLastDepthCate[$cate_code_3depth];
                sort($arrTmp);

                foreach ( $arrTmp as $key => $rVal ) {
                    $category_list_html .= $rVal[1];
                }

                $category_list_html .= '
                    </ul>
                </div>';
            }

            $category_list_html .= '</li>';
        }
        pmysql_free_result($sub_result);

    }

    $category_list_html .= '</ul></div>';
?>
                <?=$category_list_html?>

                <div class="subgoods_container">
                  <h3><?=$gnb_banner_title?></h3>
                  <ul>
                    <?=$new_arrival_html?>
                  </ul>
                </div>
              </div>
            </div>
          </li>
<?
		}   
?>      
        </ul>
      </div>






      <div class="etc-menu-wrap">
        <ul>
<?
		// 우측 메뉴를 불러온다.
		$nav_sql	= "select * from tblmainmenu where menu_type ='2' and menu_display = '1' order by menu_sort asc";			
		$nav_res = pmysql_query($nav_sql,get_db_conn());

		while($nav_row = pmysql_fetch_object($nav_res)){
			$nav_title	= $nav_row->menu_title;
			if(strpos($nav_row->menu_url, "javascript") !== false) {
				$nav_url		= $nav_row->menu_url;
			} else {
				$nav_url		= $Dir.$nav_row->menu_url;
			}
			
			$nav_class			= "";
			$nav_subwrap	= "off";

			if ($nav_title == 'PROMTION') $nav_class			= " class='slidein'";
			if ($nav_title != 'COLLECTION') $nav_subwrap	= "on";
?>
          <li<?=$nav_class?>><a href="<?=$nav_url?>">
		    <h2><?=$nav_title?></h2>
			</a>
<?
			if ($nav_subwrap == "on") {
?>
            <div class="container_wrap">
              <div class="container_box">
<?
				if ($nav_title == 'PROMOTION') {
?>
                <div class="typea_container">
                  <ul>
<?
    $sql  = "SELECT * FROM tblpromo WHERE display_type in ('A', 'P') AND hidden = 1 AND is_gnb = '1' ORDER BY rdate desc LIMIT 2 ";
    $result = pmysql_query($sql);
    while ($row = pmysql_fetch_object($result)) {
?>
                    <li><a href="/front/promotion_detail.php?idx=<?=$row->idx?>"><span class="img"><img src="/data/shopimages/timesale/<?=$row->thumb_img?>" width="345" height="117" alt=""></span></a></li>
<?
    }
?>
                  </ul>
                </div>
                <div class="typeb_container">
<?
                    $sql  = "select * from tblmainbannerimg ";
                    $sql .= "where banner_no = 108 AND banner_hidden = 1 ";
                    $sql .= "ORDER BY banner_sort asc ";

                    $sql  = "SELECT a.banner_img, b.idx, b.title, b.start_date, b.end_date ";
                    $sql .= "FROM tblmainbannerimg a left join tblpromo b on a.promo_idx = b.idx::integer ";
                    $sql .= "WHERE a.banner_no = 108 AND a.banner_hidden = 1 ";
                    $sql .= "ORDER BY a.banner_sort asc ";

                    $result = pmysql_query($sql);
                    while ($row = pmysql_fetch_object($result)) {
?>
					<div class="promotion-with-goods">
						<div class="promotion-fade-banner-wrap">
							<ul class="promotion-fade-banner">
								<li>
									<a href="/front/promotion_detail.php?idx=<?=$row->idx?>">
										<img src="/data/shopimages/mainbanner/<?=$row->banner_img?>" alt="">
										<div class="infobox">
											<!--p><strong>[CASH]</strong>MERCIU 브랜드 입점기념! 10% 할인쿠폰</p-->
                                            <p><?=$row->title?></p>
											<p class="date">기간 : <?=str_replace("-",".",$row->start_date)?> ~ <?=str_replace("-",".",$row->end_date)?></p>
										</div>
									</a>
								</li>
							</ul>
						</div>
						<ul class="thumnail">

<?
    // 기획전에 연관된 상품들의 썸네일을 보여준다.
    $sub_sql  = "SELECT b.special_list ";
    $sub_sql .= "FROM tblpromotion a left join tblspecialpromo b on a.seq = b.special::integer ";
    $sub_sql .= "WHERE a.promo_idx = '{$row->idx}' ";
    $sub_sql .= "ORDER BY a.display_seq asc ";
    $sub_result = pmysql_query($sub_sql);

    $limit_count = 6;
    $arrProdCode = array();
    while ($sub_row = pmysql_fetch_object($sub_result)) {
        $arrTmpProdCode = explode(",", $sub_row->special_list);
        $arrProdCode = array_merge($arrProdCode, $arrTmpProdCode);

        if ( count($arrProdCode) >= $limit_count ) { break; }
    }
    pmysql_free_result($sub_result);

    $whereProdCode = implode("','", $arrProdCode);

    $sub_sql  = "SELECT * FROM tblproduct WHERE display = 'Y' AND productcode IN ( '{$whereProdCode}' ) ";
    $sub_sql .= "ORDER BY FIELD ( productcode, '{$whereProdCode}' ) ";
    $sub_sql .= "LIMIT {$limit_count} ";

    $sub_result = pmysql_query($sub_sql);
    while ($sub_row = pmysql_fetch_object($sub_result)) {
        $prodImg = getProductImage($Dir."/data/shopimages/product/", $sub_row->minimage);
?>                    

							<!--li><a href="#"><span class="img"><img src="../static/img/test/menu_promotion_117x117.jpg" alt=""></span></a></li-->
                            <li><a href="/front/productdetail.php?productcode=<?=$sub_row->productcode?>"><span class="img"><img src="<?=$prodImg?>" alt="" width="117" height="117"></span></a></li>
<?
    }
?>
						</ul>
					</div><!-- //.promotion-with-goods -->
<?                  } ?>

				</div><!-- //.typeb_container -->
<?
				} ; // end of promotion

				if ($nav_title == 'STUDIO') {

                    $sub_sql  = "SELECT * FROM tblstudiognb ";
                    $sub_sql .= "WHERE hidden = 1 ORDER BY regdate desc LIMIT 1 ";
                    $sub_result = pmysql_query($sub_sql);
                    $sub_row = pmysql_fetch_object($sub_result);
                    pmysql_free_result($sub_result);
?>
                <div class="typec_container">
                  <h3><?=$sub_row->left_title?></h3>
                  <ul>
                    <? 
                        for ( $i = 1; $i <= 2; $i++ ) { 
                            $varName_img = "img{$i}";
                            $varName_link = "link{$i}";
                            $varName_target = "target{$i}";
                            $varName_display = "display{$i}";

                            // 노출 여부
                            $display = $sub_row->$varName_display;

                            if ( $display == "1" ) {

                                // 링크 url
                                $link = $sub_row->$varName_link;
                                if ( empty($link) ) {
                                    $link = "javascript:;";
                                }

                                // 링크 target
                                $target = $sub_row->$varName_target;

                                $targetStr = "";
                                if ( $target == "1" ) {
                                    // 새창
                                    $targetStr = "target='_blank'";
                                }

                                echo '<li><a href="' . $link . '" ' . $targetStr . '><span class="img"><img src="/data/shopimages/studiognb/' . $sub_row->$varName_img . '" width="270" height="270" alt=""></span></a></li>';
                            } else {
                                echo '<li><a><span class="img">&nbsp;</span></a></li>';
                            }
                        } 
                    ?>
                  </ul>
                </div>
                <div class="typed_container">
                  <h3><?=$sub_row->right_title?></h3>
                  <ul>
                    <?
                        for ( $i = 3; $i <= 7; $i++ ) {
                            $width = 133;
                            $height = 133;
                            if ( $i == 3 ) {
                                $width = 270;
                                $height = 270;
                            }

                            $varName_img = "img{$i}";
                            $varName_link = "link{$i}";
                            $varName_target = "target{$i}";
                            $varName_display = "display{$i}";

                            // 노출 여부
                            $display = $sub_row->$varName_display;

                            if ( $display == "1" ) {

                                // 링크 url
                                $link = $sub_row->$varName_link;
                                if ( empty($link) ) {
                                    $link = "javascript:;";
                                }

                                // 링크 target
                                $target = $sub_row->$varName_target;

                                $targetStr = "";
                                if ( $target == "1" ) {
                                    // 새창
                                    $targetStr = "target='_blank'";
                                }

                                echo '<li><a href="' . $link . '" ' . $targetStr . '><span class="img"><img src="/data/shopimages/studiognb/' . $sub_row->$varName_img . '" width="' . $width . '" height="' . $height . '" alt=""></span></a></li>';
                            } else {
                                echo '<li><a><span class="img">&nbsp;</span></a></li>';
                            }
                        }
                    ?>
                  </ul>
                </div>
<?
				}
?>
              </div>
            </div>
<?
			}
?>
		  </li>
<?
		}
		pmysql_free_result($nav_res);
?>
        </ul>
      </div>
    </div>
    
    <!-- //.menu-divide --> 
    
  </div>
  <!-- //.header-wrap --> 
  
</div>
<!-- //#header -->
<!-- 카운트..제발 지우지 좀 마!!!! -->
<span style="display:none;"><?=$_data->countpath?></span>
