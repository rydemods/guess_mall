<?php

	$isMobile = false;
    if ( strpos($_SERVER['PHP_SELF'], "/m/") == 0 ) {
        $isMobile = true;
    }

	$Dir="../";
	$basename=basename($_SERVER["PHP_SELF"]);

	$opt=$_REQUEST["poption"]; //productlist에서 옵션으로 상품 정렬할때 필요한 변수

	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	//app 체크하여 세션을 저장한다.
	if ($_REQUEST['act'] == 'app') {
		set_session("ACCESS", "app");
    } else if($_REQUEST['act'] == 'mobile') {
		set_session("ACCESS", null);
	}

	include_once($Dir."lib/cache_main.php");
	include_once($Dir."lib/timesale.class.php");
	include_once($Dir."conf/config.php");
	if ( $basename != "mypage_memberout.php" ) { // 회원 탈퇴일 경우 부르지 않는다
		include_once($Dir."lib/shopdata.php");
	}
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");
	include_once($Dir."lib/product.class.php");

    // ==========================================================================
    // 최상단 띠배너
    // ==========================================================================
    $sql  = "SELECT * FROM tblmainbannerimg ";
    $sql .= "WHERE banner_no = 107 and banner_hidden='1' ORDER BY banner_sort LIMIT 1";
    $result = pmysql_query($sql);

    $main_top_banner_html = '';
    while ($row = pmysql_fetch_array($result)) {
        $p_img= getProductImage($Dir.DataDir.'shopimages/mainbanner/', $row['banner_img_m']);

        if ( !empty($row['banner_link']) ) {
            if ( strpos($row['banner_link'],'/front/') !== false) { // 경로 재설정을 한다.
                $row['banner_link'] = str_replace("/front/","/m/", $row['banner_link']);
            }

            $main_top_banner_html .= '<a href="' . $row['banner_link'] . '" target="' . $row['banner_target'] . '">';
        }

        $main_top_banner_html .= '<img src="' . $p_img . '" alt="">';

        if ( !empty($row['banner_link']) ) {
            $main_top_banner_html .= '</a>';
        }

        $main_top_banner_html .= '<button class="js-btn-close" type="button"><img src="./static/img/btn/btn_close_x.png" alt="배너 숨기기"></button>';
    }
    pmysql_free_result($result);

	#productcode값을 받았을때 상품 상세보기 페이지에 메뉴 다르게 뿌려주기 위한 쿼리. 상품리스트는 code로 메뉴 뿌려주는데 상품상세는 productcode로 상품을 뿌려주기 때문
	if($_REQUEST["productcode"]!=0){
		$sql = "SELECT a.* ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode='".$_REQUEST["productcode"]."' AND a.display='Y' ";
		$sql.= "AND a.staff_product != '1' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
		$result=pmysql_query($sql,get_mdb_conn());
		$_pdata=pmysql_fetch_object($result);
		pmysql_free_result($result);

		$prLink_sql = "SELECT c_category FROM tblproductlink WHERE c_productcode='".$_pdata->productcode."' AND c_maincate = 1";
		$prLink_res = pmysql_query($prLink_sql,get_db_conn());
		$prLink = pmysql_fetch_object($prLink_res);

		$code	= $prLink->c_category;
		pmysql_free_result($prLink_res);
	}

	#productcode값을 받았을때 상품 상세보기 페이지에 메뉴 다르게 뿌려주기 위한 쿼리 end

	if ($code) {
		#코드값가지고 메뉴 뿌려주는조건을 위한 부분s
		$codeA=substr($code,0,3);
		$codeB=substr($code,3,3);
		$codeC=substr($code,6,3);
		$codeD=substr($code,9,3);
		if(strlen($codeA)!=3) $codeA="000";
		if(strlen($codeB)!=3) $codeB="000";
		if(strlen($codeC)!=3) $codeC="000";
		if(strlen($codeD)!=3) $codeD="000";
		$code=$codeA.$codeB.$codeC.$codeD;
		#코드값가지고 메뉴 뿌려주는조건을 위한 부분end
	}

	$nav_sql	= "select * from tblmainmenu where menu_type ='2' and menu_display = '1' order by menu_sort asc";
	$nav_res = pmysql_query($nav_sql,get_db_conn());
	while($nav_row = pmysql_fetch_object($nav_res)){
		$nav_title	= $nav_row->menu_title;
		$nav_img	= $nav_row->menu_img;
		$nav_url		= $nav_row->menu_url;
		$nav_url		= str_replace("../","", $nav_url);
		$nav_url		= str_replace("front/","", $nav_url);

		if(strpos($nav_row->menu_url, "javascript") !== false) {
			$nav_url_arr	= explode('"', $nav_url);
			$nav_url_ori	= $nav_url_arr[1];
		} else {
			$nav_url_ori	= $nav_url;
		}

		$nav_url_ori_exp	= explode('?', $nav_url_ori);
		if (count($nav_url_ori_exp) > 1) {
			$nav_url_ori	= $nav_url_ori_exp[0];
		}
		$now_basename	= basename($_SERVER["PHP_SELF"]);
		if ($basename == "event_view.php") $now_basename = "event_list.php";
		$nav_cate_on	= "";
		if ($nav_url_ori == $now_basename) {
			if ($nav_cate_code) {
				if($nav_cate_code == $codeA) $nav_cate_on	= "on";
			} else {
				$nav_cate_on	= "on";
			}
		} else {
			if ($nav_cate_code) {
				if($nav_cate_code == $codeA) $nav_cate_on	= "on";
			}
		}

		$nav_array	= array('menu_title'=>$nav_title, 'menu_url'=>$nav_url, 'nav_cate_on'=>$nav_cate_on);
		$navList_row	= (object) $nav_array;
		$navList[]=$navList_row;
	}

	if ($_ShopInfo->getMemid()) { // 로그인 했을 경우
		$sql = "SELECT a.*, b.group_name FROM tblmember a left join tblmembergroup b on a.group_code = b.group_code WHERE a.id='".$_ShopInfo->getMemid()."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$_mdata=$row;
			if($row->member_out=="Y") {
				$_ShopInfo->SetMemNULL();
				$_ShopInfo->Save();
				alert_go('회원 아이디가 존재하지 않습니다.',$Dir.MDir."login.php");
			}

			if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
				$_ShopInfo->SetMemNULL();
				$_ShopInfo->Save();
				alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.MDir."login.php");
			}
		}
		$staff_type = $row->staff_type;
		pmysql_free_result($result);

		// SHOPPING BAG
		//list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE id='".$_MShopInfo->getMemid()."'"));
		#핫딜 상품 장바구니수량에 포함 안시키기위한 쿼리 수정2016-09-21
		list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE basketidx not in ( SELECT  a.basketidx FROM tblbasket a left join tblproduct b on(a.productcode=b.productcode) WHERE b.hotdealyn='Y' and id='".$_ShopInfo->getMemid()."' group by a.basketidx) and id='".$_ShopInfo->getMemid()."'"));
	} else {
		// SHOPPING BAG
		//list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE id='' AND tempkey='".$_ShopInfo->getTempkey()."'"));
		#핫딜 상품 장바구니수량에 포함 안시키기위한 쿼리 수정2016-09-21
		list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE basketidx not in ( SELECT  a.basketidx FROM tblbasket a left join tblproduct b on(a.productcode=b.productcode) WHERE b.hotdealyn='Y' and id='' AND tempkey='".$_ShopInfo->getTempkey()."' group by a.basketidx) and  id='' AND tempkey='".$_ShopInfo->getTempkey()."'"));
	}
	//=====================================================================================================================================
	// 검색어 리스트
	// =====================================================================================================================================
		$arrSearchKeyword = explode( ",", $_data->search_info['keyword'] );
//
    // ===================================================================================================
    // 쿠키관련 작업들은 여기서 진행
    // ===================================================================================================

    /*if ( $basename == "productdetail.php" ) {

        $productcode=$_REQUEST["productcode"];

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

    } else

	if ( $basename == "lately_view.php" ) {
        $mode       = $_POST["mode"];
        $idx        = (array)$_POST["idx"];      // 여러건 삭제시
        $del_item   = $_POST["del_item"];        // 한건 삭제시

        if ( count($idx) == 0 && !empty($del_item) ) {
            $idx[0] = $del_item;
        }

        $wish_idx   = $_POST["wish_idx"];
        $up_marks   = (int)$_POST["up_marks"];
        $up_memo    = $_POST["up_memo"];

        if($mode=="delete" && count($idx)>0) {	//상품 삭제
            // 쿠키에서 삭제한다.
            $arrProdList = explode(",", trim($_COOKIE['ViewProduct'],','));

            $arrCookie = array();
            for ( $i = 0; $i < count($arrProdList); $i++ ) {
                $tmpArr = explode("||", $arrProdList[$i]);
                $tmpProdCode = $tmpArr[0];  // 상품코드

                // 해당 상품이 삭제되어야 할 상품인지
                if ( !in_array( $tmpProdCode, $idx ) ) {
                    array_push($arrCookie, $arrProdList[$i]);
                }
            }

            setCookie("ViewProduct", implode(",", $arrCookie),time()+60*60*24*3,"/".RootPath);  // 쿠키값을 변경
            echo '<script>alert(\'선택하신 상품을 삭제하였습니다.\'); location.href=\'' . $_SERVER['PHP_SELF'] . '\';</script>';
        }
    }*/

    #상품 페이스북 공유
    $facebook_share = '';
    if( $_GET['productcode'] ){
        $facebook_share = FacebookShare( $_GET['productcode'] );
        $twitter_share = TwitterShare( $_GET['productcode'] );
    }

    #프로모션 페이스북, 트위터 메타테그 생성 (2016-03-17 김재수 추가)
     if (strpos($_SERVER["REQUEST_URI"],'promotion_detail.php') !== false && $_GET['idx']) {

        list($share_title, $share_content, $share_img)=pmysql_fetch_array(pmysql_query("select  title, content, thumb_img from  tblpromo WHERE idx = '".$_GET['idx']."'"));

        if( is_file($Dir.'/data/shopimages/timesale/'.$share_img) ){
            $share_thumb_img = "http://".$_SERVER[HTTP_HOST]."/data/shopimages/timesale/".$share_img;
        }

        $facebook_share  = "<meta property='og:site_name' content='".$_data->shoptitle."'/>\n";
        $facebook_share .= "<meta property=\"og:type\" content=\"website\" />\n";
        $facebook_share .= "<meta property=\"og:title\" content=\"".$_data->shoptitle."\" />\n";
        $facebook_share .= "<meta property=\"og:url\" content=\"http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]."\" />\n";
        $facebook_share .= "<meta property=\"og:description\" content=\"PROMOTION - ".addslashes($share_title)."\" />\n";
        $facebook_share .= "<meta property=\"og:image\" content=\"".$share_thumb_img."\" />\n";

        $twitter_share  = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
        $twitter_share .= "<meta name=\"twitter:site\" content=\"@".$_data->shoptitle."\">\n";
        $twitter_share .= "<meta name=\"twitter:title\" content=\"".$_data->shoptitle."\">\n";
        $twitter_share .= "<meta name=\"twitter:description\" content=\"PROMOTION - ".addslashes($share_title)."\">\n";
        $twitter_share .= "<meta name=\"twitter:image\" content=\"".$share_thumb_img."\">\n";
    }else if(strpos($_SERVER["REQUEST_URI"],'magazine_detail.php') !== false && $_GET['no']){
    //매거진 페이스북, 트위터 메타태그 생성 (2016-09-22 김대엽 추가)
    	list($share_title, $share_content, $share_img)=pmysql_fetch_array(pmysql_query("select  title, content, img_file from  tblmagazine WHERE no = '".$_GET['no']."'"));

    	if( is_file($Dir.'/data/shopimages/magazine/'.$share_img) ){
    		$share_thumb_img = "http://".$_SERVER[HTTP_HOST]."/data/shopimages/magazine/".$share_img;
    	}
    		$facebook_share  = "<meta property='og:site_name' content='".$_data->shoptitle."'/>\n";
    		$facebook_share .= "<meta property=\"og:type\" content=\"website\" />\n";
    		$facebook_share .= "<meta property=\"og:title\" content=\"".$_data->shoptitle."\" />\n";
    		$facebook_share .= "<meta property=\"og:url\" content=\"http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]."\" />\n";
    		$facebook_share .= "<meta property=\"og:description\" content=\"PROMOTION - ".addslashes($share_title)."\" />\n";
    		$facebook_share .= "<meta property=\"og:image\" content=\"".$share_thumb_img."\" />\n";

    		$twitter_share  = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    		$twitter_share .= "<meta name=\"twitter:site\" content=\"@".$_data->shoptitle."\">\n";
    		$twitter_share .= "<meta name=\"twitter:title\" content=\"".$_data->shoptitle."\">\n";
    		$twitter_share .= "<meta name=\"twitter:description\" content=\"PROMOTION - ".addslashes($share_title)."\">\n";
    		$twitter_share .= "<meta name=\"twitter:image\" content=\"".$share_thumb_img."\">\n";
    }else if(strpos($_SERVER["REQUEST_URI"],'lookbook_detail.php') !== false && $_GET['no']){
 	//룩북 상세 페이스북, 트위터 메타태그 추가(2016-09-24)
 		list($share_title, $share_content, $share_img)=pmysql_fetch_array(pmysql_query("select  title, content, img_file from  tbllookbook WHERE no = '".$_GET['no']."'"));

 		if( is_file($Dir.'/data/shopimages/lookbook/'.$share_img) ){
 			$share_thumb_img = "http://".$_SERVER[HTTP_HOST]."/data/shopimages/lookbook/".$share_img;
 		}

	 	$facebook_share  = "<meta property='og:site_name' content='".$_data->shoptitle."'/>\n";
	 	$facebook_share .= "<meta property=\"og:type\" content=\"website\" />\n";
	 	$facebook_share .= "<meta property=\"og:title\" content=\"".$_data->shoptitle."\" />\n";
	 	$facebook_share .= "<meta property=\"og:url\" content=\"http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]."\" />\n";
	 	$facebook_share .= "<meta property=\"og:description\" content=\"이벤트 - ".addslashes($share_title)."\" />\n";
	 	$facebook_share .= "<meta property=\"og:image\" content=\"".$share_thumb_img."\" />\n";

	 	$twitter_share  = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
	 	$twitter_share .= "<meta name=\"twitter:site\" content=\"@".$_data->shoptitle."\">\n";
	 	$twitter_share .= "<meta name=\"twitter:title\" content=\"".$_data->shoptitle."\">\n";
	 	$twitter_share .= "<meta name=\"twitter:description\" content=\"이벤트 - ".addslashes($share_title)."\">\n";
	 	$twitter_share .= "<meta name=\"twitter:image\" content=\"".$share_thumb_img."\">\n";
 	}

	$bodyClass = "";

    if ( $basename == "index.php" || $basename=="main.php") {
        $bodyClass = "class=\"js-main\"";
    }
?>
<!doctype html>
<html lang="ko">

<head>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no, address=no, email=no">
    <meta name="Keywords" content="<?=$_data->shopkeyword?>">
    <meta name="Description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">

    <!-- 페이스북 쉐어 2016-02-11 유동혁 -->
    <?=$facebook_share?>
    <!-- 트위터 쉐어 2016-02-11 유동혁 -->
    <?=$twitter_share?>
    <title><?=$_data->shoptitle?></title>

    <!--<link rel="stylesheet" href="./static/css/common.css">-->
	<link rel="stylesheet" href="./static/css/common1.css"> <!-- common.css로 합친 후 삭제 -->
    <link rel="stylesheet" href="./static/css/component.css">
    <link rel="stylesheet" href="./static/css/content.css">
	<link rel="stylesheet" href="./static/css/jquery.bxslider.css">

    <script type="text/javascript">
        var isAppAccess = "<?=get_session("ACCESS")?>";
    </script>

	<script src="./static/js/jquery-1.12.0.min.js"></script>
	<script src="./static/js/TweenMax-1.18.2.min.js"></script>
	<script src="./static/js/deco_m_ui.js?v=20160503"></script>
    <script src="../lib/lib.js.php" type="text/javascript"></script>
	<script src="./static/js/jquery.transit.min.js"></script>
	<script src="./static/js/jquery.bxslider.min.js"></script>
    <script src="./static/js/masonry.pkgd.min.js"></script>
	<script src="./static/js/ui.js"></script>
	<script src="./static/js/dev.js"></script>
	<script src="./static/js/slick.min.js"></script>
	<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
	<script src="../js/jquery.blockUI.js"></script>


	<script language="JavaScript">
	<!--

		function proSearchChk() {
			if ( $("#search").val().trim() === "" ) {
				alert("검색어를 입력해주세요.");
				$("#search").val("").focus();
				return false;
			}

			document.formForSearch.submit();
		}

		// 로그인 여부 확인 스크립트 (APP에서 호출시 사용하기위한 함수 - 2016-04-04 추가)
		function loginChekData() {
			var jsonData	= { };
		<?if ($_ShopInfo->getMemid()) { // 로그인 했을 경우?>
			jsonData.loginCheck	= true;
			jsonData.loginLink		= "/m/logout.php";
		<?} else {?>
			jsonData.loginCheck	= false;
			jsonData.loginLink		= "/m/login.php?chUrl=<?=$_SERVER['REQUEST_URI']?>";
		<?}?>
			var json	= JSON.stringify(jsonData);
			//console.log(json);
			return json;
		}
		//loginChekData();


		function setSessionApp(){
			$.ajax({
				type:"POST"
				,url:"/app/index.php"
				,data:"set_app=on"
				,success:function(){
					//성공하면...
					isAppAccess = "app";
				}
			});
		}
	//-->
	</script>

</head>
<body>

<nav class="js-skipnav"><a href="#content" onclick="focus_anchor($(this).attr('href'));return false;">본문 바로가기</a></nav>

<!--
     [D] 페이지상단부분을 없애야 할 때는 아래와 같이 클래스를 추가
     ex)  <header class="none">
// -->

<!-- 헤더 -->
<header id="header">
	<div class="header_inner">
		<div class="inner">
			<a class="btn_lnb_open" href="javascript:;"><img src="./static/img/btn/btn_lnb_open.png" alt="카테고리 메뉴 보기"></a>
			<h1 class="logo"><a href="/m/"><img src="./static/img/common/logo.png" alt="핫티 메인 바로가기"></a></h1>
			<ul class="util">
				<li class="btn_search">
					<a href="javascript:;">검색</a>
				</li>
				<li>
					<a href="<?$Dir.MDir?>basket.php">
						<img src="./static/img/btn/btn_top_cart.png" alt="장바구니">
						<span><em><?=number_format($icon_gnb_basket_cnt)?></em></span>
					</a>
				</li>
			</ul>
		</div>

		<!-- [D] 검색영역 레이어 -->
		<section class="search_area">
		<div class="box_search clear">
			<form name=formForSearch action="<?=$Dir.MDir?>productsearch.php" method=get onsubmit="proSearchChk();return false;">
			<div class="input_search">
				<input type="search" name="search" placeholder="검색어를 입력하세요." value="">
				<button type="button" class="btn_remove" onclick="javascript:$(this).parent().find('input[name=search]').val('');"><img src="static/img/btn/btn_delete.png" alt="검색어 삭제"></button>
			</div>
			<button type="submit" class="btn-point" value="검색">검색</button>
			</form>

			<div class="hot_search">
				<h3>인기검색어</h3>
				<ol>
					<?php for ( $i = 0; $i < count($arrSearchKeyword); $i++ ) { ?>
					<li><a href="productsearch.php?search=<?=urlencode($arrSearchKeyword[$i])?>&thr=sw"><?=$i+1?>. <?=$arrSearchKeyword[$i]?></a></li>
					<?php } ?>
				</ol>
			</div>
		</div>
		</section>
		<!-- // [D] 검색영역 레이어 -->

	</div>

	<!-- [D] lnb 클릭시 노출되는 메뉴 -->
	<nav>
		<div class="dimmed"></div>
		<div class="btn_lnb_close"><a href="javascript:;"><img src="./static/img/btn/btn_lng_close.png" alt="카테고리 닫기"></a></div>
		<div class="lnb">
		<?if ($_ShopInfo->getMemid()) { // 로그인 했을 경우?>
			<!-- [D] 로그인 시 -->
			<!--<a href="<?$Dir.MDir?>mypage.php" class="member_img"><img src="./static/img/btn/btn_gnb_member.png" alt=""> <?=$_mdata->name?> 님</a>-->
			<ul class="member_logout">
				<li>
					<div>
						<em>홍길동</em> 님의 회원등급
						<p><img src="./static/img/common/member_grade01.jpg" alt="MANIA">MANIA</p>
					</div>
				</li>
				<li>
					<div>
						<em class="point-color">25,000</em> AP
						<p><a href="#" class="btn-line">주문/배송 내역</a></p>
					</div>
				</li>
			</ul>
			<!-- // [D] 로그인 시 -->
		<?} else {?>
			<!-- [D] 로그아웃 일 때 -->
			<ul class="member_login">
				<li><a href="login.php?chUrl=<?if (strstr($_SERVER[REQUEST_URI], 'login.php')) { echo trim(urlencode($_REQUEST["chUrl"])); } else { echo trim(urlencode($_SERVER[REQUEST_URI])); }?>">로그인</a></li>
				<li><a href="#">회원가입</a></li>
				<li><a href="#">계정찾기</a></li>
			</ul>
			<!-- // [D] 로그아웃 일 때 -->
		<?}?>

			<div class="lnb_menu_wrap">
				<ul class="sub_menu1">
					<li><a href="new_product.php">NEW <span class="new-point">신상품</span></a></li>
					<li class="has_sub">
						<a href="javascript:;">SHOP<button type="button" class="btn_sub_open">펼쳐보기</button></a>
						<ul class="sublnb sub_m">
							<li class="has_sub border">
								<a href="javascript:;">BRAND<button type="button" class="btn_sub_open">펼쳐보기</button></a>
								<ul class="sublnb">
									<?
										$t_getBrandList	= getAllBrandList();
										foreach( $t_getBrandList as $t_brandKey => $t_brandVal){
									?>
										<li><a href="<?=$Dir.MDir?>brand_detail.php?bridx=<?=$t_brandVal->bridx?>"><?=$t_brandVal->brandname?></a></li>
									<?}?>
								</ul>
							</li>
							<li class="has_sub border">
								<div class="wrap_link">
									<a href="productlist.php?code=001">MEN</a>
									<button type="button" class="btn_sub_open">펼쳐보기</button>
								</div>
								<ul class="sublnb sub_m">
									<li><a href="#">SPORTS1</a></li>
									<li><a href="#">STREET</a></li>
									<li><a href="#">ACC</a></li>
								</ul>
							</li>
							<li class="has_sub border">
								<div class="wrap_link">
									<a href="productlist.php?code=002">WOMEN</a>
									<button type="button" class="btn_sub_open">펼쳐보기</button>
								</div>
								<ul class="sublnb sub_m">
									<li><a href="#">SPORTS1</a></li>
									<li><a href="#">STREET</a></li>
									<li><a href="#">ACC</a></li>
								</ul>
							</li>
							<li class="has_sub border">
								<div class="wrap_link">
									<a href="#">ETC</a>
									<button type="button" class="btn_sub_open">펼쳐보기</button>
								</div>
								<ul class="sublnb sub_m">
									<li><a href="#">SPORTS1</a></li>
									<li><a href="#">STREET</a></li>
									<li><a href="#">ACC</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li class="has_sub">
						<a href="javascript:;">SHOWCASE<button type="button" class="btn_sub_open">펼쳐보기</button></a>
						<ul class="sub_menu2 sublnb">
							<li><a href="#"><img src="./static/img/common/mbrand_logo01.png" alt="NIKE"></a></li>
							<li><a href="#"><img src="./static/img/common/mbrand_logo02.png" alt="BIRKENSTOCK"></a></li>
							<li><a href="#"><img src="./static/img/common/mbrand_logo03.png" alt="REEBOK"></a></li>
							<li><a href="#"><img src="./static/img/common/mbrand_logo04.png" alt="ADIDAS"></a></li>
							<li><a href="#"><img src="./static/img/common/mbrand_logo05.png" alt="CONVERSE"></a></li>
							<li><a href="#"><img src="./static/img/common/mbrand_logo06.png" alt="PUMA"></a></li>
							<li><a href="#"><img src="./static/img/common/mbrand_logo07.png" alt="SUPERGA"></a></li>
							<li><a href="#"><img src="./static/img/common/mbrand_logo08.png" alt="TAVA"></a></li>
							<li><a href="#"><img src="./static/img/common/mbrand_logo09.png" alt="KANGAROOS"></a></li>
							<li><a href="#"><img src="./static/img/common/mbrand_logo10.png" alt="ADIDAS"></a></li>
						</ul>
					</li>
					<li class="has_sub">
						<a href="#">HOT TREND<button type="button" class="btn_sub_open">펼쳐보기</button></a>
						<ul class="sub_menu3 sublnb">
							<li><a href="<?=$Dir.MDir?>magazine_list.php">MAGAZINE</a></li>
							<li><a href="<?=$Dir.MDir?>lookbook.php">LOOKBOOK</a></li>
							<li><a href="<?=$Dir.MDir?>instagram_list.php">INSTAGRAM</a></li>
							<li><a href="<?=$Dir.MDir?>store_story.php">STORE STORY</a></li>
						</ul>
					</li>
					<li><a href="<?=$Dir.MDir?>forum_main.php">FORUM</a></li>
				</ul>
				<ul class="sub_menu4">
					<li><a href="<?=$Dir.MDir?>promotion.php">이벤트/기획전</a></li>
					<li><a href="<?=$Dir.MDir?>hotdeal.php">RELEASE</a></li>
					<li><a href="<?=$Dir.MDir?>customer_notice.php">공지사항</a></li>
					<li><a href="<?=$Dir.MDir?>customer_faq.php">고객센터</a></li>
					<li><a href="<?=$Dir.MDir?>company.php">핫티소개</a></li>
					<li><a href="<?=$Dir.MDir?>store.php">매장위치</a></li>
				</ul>
			</div>
			<div class="sns_wrap">
				<a href="https://www.facebook.com/HOTTofficial" target="_blank"><img src="./static/img/icon/f_facebook.png" alt="facebook"></a>
				<a href="http://blog.naver.com/shoemarker_" target="_blank"><img src="./static/img/icon/f_blog.png" alt="blog"></a>
				<a href="https://www.instagram.com/hott_official/" target="_blank"><img src="./static/img/icon/f_instagram.png" alt="instagram"></a>
			</div>
			<div class="ta-c mt-10"><a href="<?$Dir.MDir?>logout.php" class="btn-line">로그아웃</a></div> <!-- // [D] 로그아웃 -->
		</div>
	</nav>
	<!-- // [D] lnb 클릭시 노출되는 메뉴 -->
</header>
<!-- // 헤더 -->

<!-- ajax loading img -->
<div class="dimm-loading" id="dimm-loading">
	<div id="loading"></div>
</div>
<!-- // ajax loading img-->
<main id="content" class="subpage">