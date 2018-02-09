<?php
/**
 * 퍼블리싱 파일 : /public/m/outline/header_m_lnb.php
**/

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

    shopSslChange();

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
	
	// 쿼리 위민트 170205
	include_once(".././main/tem_top001_sql.php");

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

	if ($_MShopInfo->getMemid()) { // 로그인 했을 경우
		$sql = "SELECT a.*, b.group_name FROM tblmember a left join tblmembergroup b on a.group_code = b.group_code WHERE a.id='".$_MShopInfo->getMemid()."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$_mdata=$row;
			if($row->member_out=="Y") {
				$_MShopInfo->SetMemNULL();
				$_MShopInfo->Save();
				alert_go('회원 아이디가 존재하지 않습니다.',$Dir.MDir."login.php");
			}

			if($row->authidkey!=$_MShopInfo->getAuthidkey()) {
				$_MShopInfo->SetMemNULL();
				$_MShopInfo->Save();
				alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.MDir."login.php");
			}
		}
		$staff_type = $row->staff_type;
		pmysql_free_result($result);

		// SHOPPING BAG
		//list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE id='".$_MShopInfo->getMemid()."'"));
		#핫딜 상품 장바구니수량에 포함 안시키기위한 쿼리 수정2016-09-21
		list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE basketidx not in ( SELECT  a.basketidx FROM tblbasket a left join tblproduct b on(a.productcode=b.productcode) WHERE b.hotdealyn='Y' and id='".$_MShopInfo->getMemid()."' group by a.basketidx) and id='".$_MShopInfo->getMemid()."'"));
	} else {
		// SHOPPING BAG
		//list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE id='' AND tempkey='".$_ShopInfo->getTempkey()."'"));
		#핫딜 상품 장바구니수량에 포함 안시키기위한 쿼리 수정2016-09-21
		list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE basketidx not in ( SELECT  a.basketidx FROM tblbasket a left join tblproduct b on(a.productcode=b.productcode) WHERE b.hotdealyn='Y' and id='' AND tempkey='".$_MShopInfo->getTempkey()."' group by a.basketidx) and  id='' AND tempkey='".$_MShopInfo->getTempkey()."'"));
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

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
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
    
    <!-- 리뉴얼 (2017.01.20 위민트) -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
	<link rel="stylesheet" href="/sinwon/m/static/css/common.css">
	<link rel="stylesheet" href="/sinwon/m/static/css/component.css">
	<link rel="stylesheet" href="/sinwon/m/static/css/content.css">
    <link rel="stylesheet" href="/sinwon/m/static/css/nouislider.css">

	<script type="text/javascript" src="/sinwon/m/static/js/jquery-1.12.0.min.js"></script>
	<script type="text/javascript" src="/sinwon/m/static/js/jquery.bxslider.min.js"></script>
	<script type="text/javascript" src="/sinwon/m/static/js/Carousel.js"></script>
	<script type="text/javascript" src="/sinwon/m/static/js/nouislider.min.js"></script>
	<script type="text/javascript" src="/sinwon/m/static/js/wNumb.js"></script>
	<script type="text/javascript" src="/sinwon/m/static/js/masonry.pkgd.min.js"></script>
	<script type="text/javascript" src="/sinwon/m/static/js/ui.js"></script>
	<script type="text/javascript" src="/sinwon/m/static/js/buildV63.js"></script>
	<!--// 리뉴얼 (2017.01.20 위민트@@@) -->

	<!-- 이전버젼 참고 (2017.01.20 위민트) -->
<!--     <link rel="stylesheet" href="./static/css/common.css?v=1"> -->
<!--     <link rel="stylesheet" href="./static/css/component.css?v=1"> -->
<!--     <link rel="stylesheet" href="./static/css/content.css"> -->
<!-- 	<link rel="stylesheet" href="./static/css/jquery.bxslider.css"> -->
    <link rel='shortcut icon' href="./static/img/common/hot-t.ico" type="image/x-ico" >

    <script type="text/javascript">
        var isAppAccess = "<?=get_session("ACCESS")?>";
    </script>

	
<!-- 	<script src="./static/js/jquery-1.12.0.min.js"></script> -->
<!-- 	<script src="./static/js/TweenMax-1.18.2.min.js"></script> -->
<!-- 	<script src="./static/js/deco_m_ui.js?v=20160503"></script> -->
<!--     <script src="../lib/lib.js.php" type="text/javascript"></script> -->
<!-- 	<script src="./static/js/jquery.transit.min.js"></script> -->
<!-- 	<script src="./static/js/jquery.bxslider.min.js"></script> -->
<!--     <script src="./static/js/masonry.pkgd.min.js"></script> -->
<!-- 	<script src="./static/js/ui.js?v=1"></script> -->
	<script src="./static/js/dev.js?v=11"></script>
<!-- 	<script src="./static/js/slick.min.js"></script> -->
	<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
	<script src="../js/jquery.blockUI.js"></script>

    <script src="./static/js/JsBarcode.all.min.js"></script>
    <!--// 이전버젼 참고 (2017.01.20 위민트) -->
    
    <script type="text/javascript">
    <!--
        //console.log( $.ajaxSetup() ); 
        // app일 경우 바코드 js 추가
        if(isAppAccess) {

            $(document).ready(function(){
                $("#barcode").JsBarcode("<?=$_mdata->mem_seq?>",{
                    width:1,
                    height:25,
                    quite: 10,
                    format:"CODE128",
                    backgroundColor:"#fff",
                    lineColor:"#000", 
                    displayValue: true, 
                    fontSize:15
                });
            });
        }        
    //-->
    </script>

	<script language="JavaScript">
		function proSearchChk() {
			if ( $("#search").val().trim() === "" ) {
				alert("검색어를 입력해주세요.");
				$("#search").focus();
				return;
			}

			document.formForSearch.submit();
		}

		// 로그인 여부 확인 스크립트 (APP에서 호출시 사용하기위한 함수 - 2016-04-04 추가)
		function loginChekData() {
			var jsonData	= { };
		<?if ($_MShopInfo->getMemid()) { // 로그인 했을 경우?>
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
	
	</script>

</head>
<body>

<a href="#content" onclick="focus_anchor($(this).attr('href'));return false;" class="skip">Skip to Content</a>


<!-- 헤더 -->
<header id="header">
	<h1><a href="#"><img src="/sinwon/m/static/img/common/logo.png" alt="SW eshop"></a></h1>
	<!-- LNB -->
	<div class="lnb-wrap">
		<button class="btn-lnb-open" type="button"><img src="/sinwon/m/static/img/btn/btn_lnb_open.png" alt="카테고리 메뉴 보기"></button>
		<div class="lnb-layer">
			<div class="lnb-layer-dim"></div>
			<div class="lnb-layer-inner">
				<div class="lnb-top">
					<a href="#" class="btn-lnb-home"><img src="/sinwon/m/static/img/btn/btn_lnb_home.png" alt="HOME"></a>
					<button type="button" class="btn-lnb-close"><img src="/sinwon/m/static/img/btn/btn_lnb_close.png" alt="카테고리 메뉴 숨기기"></button>
				</div><!-- //.lnb-top -->

				<div class="content">
					<div class="lnb-tab" data-ui="TabMenu">
						<div class="tab-menu clear">
							<a data-content="menu" class="w33-per active" title="선택됨">신상품</a>
							<a data-content="menu" class="w33-per">브랜드</a>
							<a data-content="menu" class="w33-per">아울렛</a>
						</div>

						<!-- 신상품 카테고리 -->
						<div class="tab-content active" data-content="content">
							<ul class="main_category">
								<!-- 컨텐츠 메뉴(신상품,브랜드,아울렛에서 고정 노출) -->
								<li>
									<a href="javascript:;">스타일</a>
									<ul class="sub_category">
										<li><a href="#">E-CATALOG</a></li>
										<li><a href="#">LOOKBOOK</a></li>
										<li><a href="#">MAGAZINE</a></li>
										<li><a href="#">INSTAGRAM</a></li>
										<li><a href="#">MOVIE</a></li>
									</ul>
								</li>
								<li>
									<a href="javascript:;">프로모션</a>
									<ul class="sub_category">
										<li><a href="#">출석체크</a></li>
										<li><a href="#">이벤트</a></li>
										<li><a href="#">기획전</a></li>
									</ul>
								</li>
								<li><a href="#">쇼윈도</a></li><!-- //[D] 하위메뉴 없는 경우 -->
								<li>
									<a href="javascript:;">고객센터</a>
									<ul class="sub_category">
										<li><a href="#">공지사항</a></li>
										<li><a href="#">FAQ</a></li>
										<li><a href="#">1:1문의</a></li>
										<li><a href="#">매장안내</a></li>
										<li><a href="#">입점문의</a></li>
										<li><a href="#">멤버쉽 안내</a></li>
										<li><a href="#">수선(A/S)안내</a></li>
									</ul>
								</li>
								<!-- //컨텐츠 메뉴 -->
							</ul>
						</div>
						<!-- //신상품 카테고리 -->

						<!-- 브랜드 카테고리 -->
						<div class="tab-content" data-content="content">
							<ul class="main_category">
								<?
								// 상품브랜드 정보
								foreach ($brand_list as $brand){
								?>
								<li>
									<a href="javascript:;"><?=$brand['brandname']?></a>
									<ul class="sub_category">
										<li><a href="#">브랜드 소개</a></li>
										<li><a href="#">매장</a></li>
										<li><a href="#">룩북</a></li>
										<li><a href="#">E-CATALOG</a></li>
										<li><a href="<?=$Dir.MDir?>brand_detail.php?bridx=<?=$brand['bridx']?>">SHOP</a></li>
									</ul>
								</li>
								<?php }?>
								<!-- 컨텐츠 메뉴 -->
								<li>
									<a href="javascript:;">스타일</a>
									<ul class="sub_category">
										<li><a href="#">E-CATALOG</a></li>
										<li><a href="#">LOOKBOOK</a></li>
										<li><a href="#">MAGAZINE</a></li>
										<li><a href="#">INSTAGRAM</a></li>
										<li><a href="#">MOVIE</a></li>
									</ul>
								</li>
								<li>
									<a href="javascript:;">프로모션</a>
									<ul class="sub_category">
										<li><a href="#">출석체크</a></li>
										<li><a href="#">이벤트</a></li>
										<li><a href="#">기획전</a></li>
									</ul>
								</li>
								<li><a href="#">쇼윈도</a></li><!-- //[D] 하위메뉴 없는 경우 -->
								<li>
									<a href="javascript:;">고객센터</a>
									<ul class="sub_category">
										<li><a href="#">공지사항</a></li>
										<li><a href="#">FAQ</a></li>
										<li><a href="#">1:1문의</a></li>
										<li><a href="#">매장안내</a></li>
										<li><a href="#">입점문의</a></li>
										<li><a href="#">멤버쉽 안내</a></li>
										<li><a href="#">수선(A/S)안내</a></li>
									</ul>
								</li>
								<!-- //컨텐츠 메뉴 -->
							</ul>
						</div>
						<!-- //브랜드 카테고리 -->

						<!-- 아울렛 카테고리 -->
						<div class="tab-content" data-content="content">
							<ul class="main_category">
								<!-- 컨텐츠 메뉴 -->
								<li>
									<a href="javascript:;">스타일</a>
									<ul class="sub_category">
										<li><a href="#">E-CATALOG</a></li>
										<li><a href="#">LOOKBOOK</a></li>
										<li><a href="#">MAGAZINE</a></li>
										<li><a href="#">INSTAGRAM</a></li>
										<li><a href="#">MOVIE</a></li>
									</ul>
								</li>
								<li>
									<a href="javascript:;">프로모션</a>
									<ul class="sub_category">
										<li><a href="#">출석체크</a></li>
										<li><a href="#">이벤트</a></li>
										<li><a href="#">기획전</a></li>
									</ul>
								</li>
								<li><a href="#">쇼윈도</a></li>
								<li>
									<a href="javascript:;">고객센터</a>
									<ul class="sub_category">
										<li><a href="#">공지사항</a></li>
										<li><a href="#">FAQ</a></li>
										<li><a href="#">1:1문의</a></li>
										<li><a href="#">매장안내</a></li>
										<li><a href="#">입점문의</a></li>
										<li><a href="#">멤버쉽 안내</a></li>
										<li><a href="#">수선(A/S)안내</a></li>
									</ul>
								</li>
								<!-- //컨텐츠 메뉴 -->
							</ul>
						</div>
						<!-- //아울렛 카테고리 -->
					</div>
				</div><!-- //.content -->
			</div>
		</div>
	</div>
	<!-- //LNB -->

	<a href="#" id="btn_search"><img src="/sinwon/m/static/img/btn/btn_search.png" alt="검색"></a>

	<div class="pop_search">
		<button type="button" class="close_search"><img src="/sinwon/m/static/img/btn/btn_layer_close.png" alt="닫기"></button>
		<form name=formForSearch action="<?=$Dir.MDir?>productsearch.php" method=get onsubmit="proSearchChk();return false;">
		<div class="container">
			<div class="searchbox">
				<input type="text" name="search" value="<?=$_data->search_info['defaultkeyword']?>">
				<button type="submit"><img src="/sinwon/m/static/img/btn/btn_search.png" alt="검색"></button>
			</div>
			
			<div class="searchtab" data-ui="TabMenu">
				<div class="tab-menu clear">
					<a data-content="menu" class="active" title="선택됨">추천 검색어</a>
					<a data-content="menu">최근 검색어</a>
				</div>
				<div class="tab-content active" data-content="content">
					<?php if(count($arrSearchKeyword) > 0){?>
					<ul class="search_word">
						<?php for ( $i = 0; $i < 10; $i++ ) {
							if($arrSearchKeyword[$i]){?>
						<li><a href="productsearch.php?search=<?=urlencode($arrSearchKeyword[$i])?>&thr=sw"><?=$i+1?>. <?=$arrSearchKeyword[$i]?></a></li>
						<?php }
						} ?>
					</ul><!-- //검색어 있는 경우(10개까지 노출) -->
					<?php }?>
					<?php if(count($arrSearchKeyword) == 0){?>
					<div class="search_word_none">추천 검색어가 없습니다.</div>
					<?php }?>
				</div>
				<div class="tab-content" data-content="content">
					<?php if(count($arrMyKeyword) > 0){?>
					<ul class="search_word">
						<?php for ( $i = 0; $i < count($arrMyKeyword); $i++ ) { ?>
						<li><a href="/front/productsearch.php?search=<?=urlencode($arrMyKeyword[$i])?>&thr=sw"><?=$arrMyKeyword[$i]?></a></li>
						<?php }?>
					</ul>
					<?php }?>
					<?php if(count($arrMyKeyword) == 0){?>
					<div class="search_word_none">최근 검색어가 없습니다.</div>
					<?php }?>
				</div>
			</div><!-- //.searchtab -->

			<!-- 검색결과가 없는 경우 -->
			<!-- <div class="search_result_none"><strong class="point-color">‘코트’</strong> 의 검색 결과 <strong class="point-color">총 0개</strong>입니다.</div>

			<div class="search_notice">
				<ul>
					<li>- 단어의 철자 및 띄어쓰기를 확인해주세요.</li>
					<li>- 검색어가 올바른지 다시 한번 확인해주세요.</li>
					<li>- 특수문자를 제외하고 검색해주세요.</li>
				</ul>
			</div> -->
			<!-- //검색결과가 없는 경우 -->
		</div>
		</form>
	</div><!-- //.pop_search -->

	<div class="rnb-wrap">
		<button class="btn-rnb-open" type="button"><img src="/sinwon/m/static/img/btn/btn_rnb_open.png" alt="마이페이지 메뉴 보기"></button>
		<div class="rnb-layer">
			<div class="rnb-layer-dim"></div>
			<div class="rnb-layer-inner">
				<div class="lnb-top">
					<?if ($_MShopInfo->getMemid()) { // 로그인 했을 경우?>
					<!-- 로그인 후 -->
					<a href="<?$Dir.MDir?>logout.php" class="btn-lnb-login">
						<img src="/sinwon/m/static/img/btn/btn_lnb_logout.png" alt="logout">
						<span>로그아웃</span>
					</a>
					<!-- //로그인 후 -->
					
					<?php } else {?>
					<!-- 로그인 전 -->
					<a href="login.php?chUrl=<?if (strstr($_SERVER[REQUEST_URI], 'login.php')) { echo trim(urlencode($_REQUEST["chUrl"])); } else { echo trim(urlencode($_SERVER[REQUEST_URI])); }?>" class="btn-lnb-login">
						<img src="/sinwon/m/static/img/btn/btn_lnb_login.png" alt="login">
						<span>로그인</span>
					</a>
					<!-- //로그인 전 -->
					<?php }?>

					<button type="button" class="btn-lnb-close"><img src="/sinwon/m/static/img/btn/btn_lnb_close.png" alt="마이페이지 메뉴 숨기기"></button>
				</div><!-- //.lnb-top -->

				<div class="content">
				
					<?if ($_MShopInfo->getMemid()) { // 로그인 했을 경우?>
					<!-- 로그인 후 -->
					<div class="benefit_info">
						<p class="msg"><strong><?=$_mdata->name?></strong>님은 <strong><?=$_mdata->group_name?></strong>입니다.</p>
						<p class="point point-color"><?=number_format($_mdata->act_point)?> AP</p>
						<p class="info">등급업 필요 포인트: 100 P</p>
					</div>
					<!-- //로그인 후 -->
					
					<?php } else {?>
					<!-- 로그인 전 -->
					<div class="benefit_info">
						<p class="info_none">회원님께 특별한 혜택을 드립니다.</p>
					</div>
					<!-- //로그인 전 -->
					<?php }?>

					<?if ($_MShopInfo->getMemid()) { // 로그인 했을 경우?>
					<div class="shortcut">
						<ul class="clear">
							<li>
								<a href="#">
									<span class="icon"><img src="static/img/icon/icon_like.png" alt="좋아요"></span>
									<span class="txt">좋아요</span>
								</a>
							</li>
							<li>
								<a href="<?$Dir.MDir?>basket.php">
									<span class="icon_cart">5</span>
									<span class="txt">장바구니</span>
								</a>
							</li>
						</ul>
					</div><!-- //.shortcut -->
					
					<div class="mycategory">
						<h2>쇼핑내역</h2>
						<ul>
							<li><a href="<?=$Dir.MDir?>mypage_orderlist.php">주문/배송조회</a></li>
							<li><a href="#">취소/교환/반품 신청</a></li>
							<li><a href="#">취소/교환/반품 현황</a></li>
						</ul>
						<h2>혜택정보</h2>
						<ul>
							<li><a href="#">회원등급 및 혜택</a></li>
							<li><a href="#">포인트</a></li>
							<li><a href="#">쿠폰</a></li>
						</ul>
						<h2>활동정보</h2>
						<ul>
							<li><a href="#">이벤트 참여현황</a></li>
							<li><a href="#">상품리뷰</a></li>
							<li><a href="#">상품문의</a></li>
							<li><a href="#">1:1문의</a></li>
						</ul>
						<h2>회원정보</h2>
						<ul>
							<li><a href="#">회원정보 수정</a></li>
							<li><a href="#">배송지 관리</a></li>
							<li><a href="#">환불계좌 관리</a></li>
							<li><a href="#">회원탈퇴</a></li>
						</ul>
					</div><!-- //.mycategory -->
					<?php }?>

				</div>
			</div>
		</div>
	</div><!-- //.rnb-wrap -->

</header>
<!-- // 헤더 -->



<!-- ajax loading img -->
<div class="dimm-loading" id="dimm-loading">
	<div id="loading"></div>
</div>
<!-- // ajax loading img-->

<?php
if($basename=="index.htm" ){

    // 모바일 플로팅 배너
    $curdate=date("Ymd");
    $_layerdata=array();
    $sql = "SELECT * FROM tbleventpopup WHERE start_date<='".$curdate."' AND end_date>='".$curdate."' AND is_mobile='Y' AND mobile_display = 'Y' ";
    $result=pmysql_query($sql,get_db_conn());
    while($row=pmysql_fetch_object($result)) {
        $_layerdata[]=$row;
    }
    pmysql_free_result($result);
    if(count($_layerdata)){
?>

<!--HTML Start -->
<!--<a href="#" class="btn-example" onclick="layer_open('layer2');return false;">예제-2 보기</a>-->
<div class="main-promotion-layer">
    <div class="bg"></div>
    <div id="promotion-layer-pop" class="pop-layer popup-layer-inner">
        <a class="btn-close notOpen" ><img src="/sinwon/m/static/img/btn/btn_close_popup_x.png" alt="팝업닫기"></a>

        <div class="popup-layer-content">
        <!--content //-->
        <?=$_layerdata[0]->content?>
        <!--// content-->
        </div>

        <div class="btn-place">
            
            <input type="checkbox" id='notOpen' >오늘 하루 열지 않음
            <a href="#" class="now-close notOpen"><img src="/sinwon/m/static/img/btn/btn_close_layer.gif" alt="삭제"></a>
        </div>

    </div>
</div>
<!--HTML END -->
<!--SCRIPT Start -->
<script type="text/javascript">
    $(document).ready(function(){
        //localStorage.removeItem("mobile_notOpen_day");
        //localStorage.removeItem("mobile_notOpen_day_expire");
        
        //열지 않음을 확인함
        if(localStorage.getItem("mobile_notOpen_day") == "Y" && ( new Date().getTime() < localStorage.getItem("mobile_notOpen_day_expire"))) 
        { 
            $("#promotion-layer-pop").hide(); 
        }else{
            $("body").bind('touchmove', function(e){e.preventDefault()}); //스크롤방지
            layer_open('promotion-layer-pop');
        }
        //오늘하루 열지 않기
        $(".notOpen").on("click",function(e){
            e.preventDefault();
            var dayLayer = $('#promotion-layer-pop');
            var dayLayerBG = dayLayer.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수
            if( $('#notOpen').prop( 'checked' ) ) {
                localStorage.setItem("mobile_notOpen_day","Y"); 
                localStorage.setItem("mobile_notOpen_day_expire", new Date().getTime() + (24*60*60*1000));
            }
            if(dayLayerBG){
                $('.main-promotion-layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
            }else{
                dayLayer.fadeOut();
            }
            $("body").unbind('touchmove'); //스크롤 시작
        });
    });
    function layer_open(el){

        var temp = $('#' + el);
        var bg = temp.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수
        
        if(bg){
            $('.main-promotion-layer').fadeIn();	//'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
             temp.fadeIn();
        }else{
            temp.fadeIn();
        }

        temp.css('top', '50px');
        temp.find('a.cbtn').click(function(e){
            if(bg){
                $('.main-promotion-layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
            }else{
                temp.fadeOut();
            }
            $("body").unbind('touchmove'); //스크롤 시작
            e.preventDefault();
        });

        $('.main-promotion-layer > .bg').click(function(e){	//배경을 클릭하면 레이어를 사라지게 하는 이벤트 핸들러
            $('.main-promotion-layer').fadeOut();
            $("body").unbind('touchmove'); //스크롤 시작
            e.preventDefault();
        });

    }
</script>
<!--SCRIPT END -->
<?php 
    } 
}
?>

<main id="content" class="subpage">