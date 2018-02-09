<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

function getUnescape($str){
	return urldecode(preg_replace_callback('/%u([[:alnum:]]{4})/', 'UnescapeFunc', $str));
}

function UnescapeFunc($str){
	return mb_convert_encoding(chr(hexdec(substr($str[1], 2, 2))).chr(hexdec(substr($str[1],0,2))),"UHC","UTF-16LE");
}

$ref=$_REQUEST["ref"];
$url=$_ShopInfo->getShopurl();
if(ord($url)==0) $url = $_SERVER['HTTP_HOST'];

if(ord($_SERVER["HTTP_REFERER"])==0) { 
	echo "사이트에 링크하셔야 나옵니다.";
	exit; 
}

$cookie_set = $_COOKIE["shop_counter"];

$date = date("YmdH");
$date2 = substr($date,0,8);
$date3 = substr($date,0,6);

//시간별 페이지뷰 카운트(계속 증가 tblcounter.pagecnt)
pmysql_query("SELECT f_inc_tblcounter('{$date}')",get_db_conn());
if ($cookie_set != $date) {
	// 시간별 순 방문자 카운트(쿠키로 체크해서 시간에 1번씩만 증가)
	pmysql_query("UPDATE tblcounter SET cnt=cnt+1 WHERE date='{$date}' ",get_db_conn());
	setcookie("shop_counter",$date,0,"/".RootPath);
}
// 페이지뷰 설정
$page = str_replace("http://","",$_SERVER["HTTP_REFERER"]);
if (strpos($page,"/")!=0) {
	$pageurl = substr($page,0,strpos($page,"/"));
	$pageview = substr($page,strpos($page,"/")+1);
	// 상품코드 추출 
	if (strpos($page,"productdetail.php")>0 && strpos($pageview,"productcode=")>0) {
		$shopdetail = $pageview;
		$pos = strpos($shopdetail,"productcode=")+12;
        // 상품코드가 카테고리구조가 아닐수도 있으므로, 수정. 2015-11-11 jhjeong
		//$productcode = substr($shopdetail,$pos,18);
        $productcode_tmp = explode("&", substr($shopdetail,$pos,30));
        $productcode = $productcode_tmp[0];
		//if (strlen($productcode)>12) {
			pmysql_query("SELECT f_inc_tblcounterproduct('{$date2}','{$productcode}')",get_db_conn());
		//}
	} else if (strpos($page,"productlist.php")>0 && strpos($pageview,"code=")>0) {
		$codelink = $pageview;
		$pos = strpos($codelink,"code=")+5;
		$str = substr($codelink,$pos,12);
		$code="";
		for($i=0;$i<strlen($str);$i++){
			if("0"<=$str[$i] && $str[$i]<="9") $code.=$str[$i];
		}

		if (strlen($code)==3 || strlen($code)==12) {
			if(strlen($code)==3) $code.="000000000";
			pmysql_query("SELECT f_inc_tblcountercode('{$date2}','{$code}')",get_db_conn());
		} 
	} else if (strpos($page,"productsearch.php")>0 && strpos($pageview,"search=")>0) {
		//검색어
		$shopdetail = $pageview;
		$pos = strpos($shopdetail,"search=")+7;
		if ($pos>7) {
			$shopdetail = substr($shopdetail,$pos);
			if(strpos($shopdetail,"&")!=false) {
				$pos = strpos($shopdetail,"&");
				$search = substr($shopdetail,0,$pos);
			} else {
				$search = $shopdetail;
			}
			if (ord($search)) {
				$search=urldecode($search);
				pmysql_query("SELECT f_inc_tblcounterkeyword('{$date2}','{$search}')",get_db_conn());
			}
		}
	}
	if (strpos($pageview,"?")!=false) $pageview = substr($pageview,0,strpos($pageview,"?"));
}

// 페이지뷰체크
if (ord($pageview)) {
	//$pageview=@mb_convert_encoding(urldecode($pageview),"EUC-KR","auto");
    $pageview=urldecode($pageview);
	pmysql_query("SELECT f_inc_tblcounterpageview('{$date2}','{$pageview}')",get_db_conn());

	// 주문체크
	if (strpos($pageview,"orderend.php")>0) {
		pmysql_query("SELECT f_inc_tblcounterorder('{$date}')",get_db_conn());
	}
}
// 링크URL체크
if (strlen(trim($ref))>0 && strpos($ref,$url)===false) {
	$ref = str_replace("http://","",$ref);
	$searchword = str_replace("http://","",$ref);

	if (strpos($ref,"/")!=0) $ref = substr($ref,0,strpos($ref,"/"));

	$result = pmysql_query("SELECT COUNT(*) as cnt FROM tblcountersearchdomain WHERE domain='{$ref}'",get_db_conn());
	$row=pmysql_fetch_object($result);
	$cnt = $row->cnt;
	pmysql_free_result($result);

	// 검색엔진사이트일경우
	if ($cnt==1)  {
		pmysql_query("SELECT f_inc_tblcountersearchengine('{$date2}','{$ref}')",get_db_conn());

		switch ($ref) {
			case "kr.search.yahoo.com":
				$searchquery="p=";
				break;
			case "search.naver.com":
			case "web.search.naver.com":
			case "searchplus.nate.com":
			case "search.nate.com":
			case "search.hanafos.com":
				$searchquery="query=";
				break;
			case "search.korea.com":
			case "search.freechal.com":
				$searchquery="query=";
				break;
			case "search.paran.com":
				$searchquery="Query=";
				break;
			default :
				$searchquery="q=";
				break;
		}

		if (strpos($searchword,$searchquery)>0) {
			//if (($ref=="www.google.co.kr" && strpos($searchword,"UTF-8")>0) || ($ref=="search.msn.co.kr" && strpos($searchword,"MSNH")<=0)){
			if ($ref=="www.google.co.kr" || ($ref=="search.msn.co.kr" && strpos($searchword,"MSNH")<=0) || ($ref=="search.daum.net" && strpos($searchword,"utf8")>0) || ($ref=="kr.search.yahoo.com" && strpos($searchword,"kr-search_top")>0)){
				//$searchword=@mb_convert_encoding(urldecode($searchword),"EUC-KR","auto");
                $searchword=urldecode($searchword);
                //backup_save_logs("searchword", $searchword);
			}
            //backup_save_logs("searchword", $searchword);
			$searchword = trim(urldecode(substr($searchword,strpos($searchword,$searchquery)+strlen($searchquery))));
            //backup_save_logs("searchword", $searchword);
			if (strpos($searchword,"&")>0) $searchword = substr($searchword,0,strpos($searchword,"&"));

            //backup_save_logs("searchword", $searchword);
			if (ord($searchword)) {
				if($ref=="search.naver.com" || $ref=="web.search.naver.com") {
					$searchword = getUnescape($searchword);
				}
                //backup_save_logs("searchword", $searchword);
				//pmysql_query("SELECT f_inc_tblcountersearchword('{$date3}','{$ref}','".iconv("UTF-8","EUC-KR",$searchword)."')",get_db_conn());
                //backup_save_logs("searchword", "SELECT f_inc_tblcountersearchword('{$date3}','{$ref}','".iconv("UTF-8","EUC-KR",$searchword)."')");
                pmysql_query("SELECT f_inc_tblcountersearchword('{$date3}','{$ref}','".$searchword."')",get_db_conn());
                //backup_save_logs("searchword", "SELECT f_inc_tblcountersearchword('{$date3}','{$ref}','".$searchword."')");
			}
		}
	} else {
		pmysql_query("SELECT f_inc_tblcounterdomain('{$date2}','{$ref}')",get_db_conn());
	}
}

header("Content-type: image/gif"); 
$array = array(71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59); 
foreach ($array as $asc) echo chr($asc);
