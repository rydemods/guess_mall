<?
if(substr(getenv("SCRIPT_NAME"),-17)=="/shopdata.inc.php") {
	header("HTTP/1.0 404 Not Found");
}

$http_user_agent=$_SERVER["HTTP_USER_AGENT"];

if(!class_exists('Paging',false)) {
	include_once('../lib/paging.php');
}
/*
if((strpos($http_user_agent,"AppleWebKit")===false || strpos($http_user_agent,"Mobile")===false) && strpos($http_user_agent,"Opera Mini")===false) {
	header("Location:".$Dir);
	exit;
}
*/
/*잠시 막아둠
if(strpos($http_user_agent,"iPhone")===false
	&& strpos($http_user_agent,"iPad")===false
	&& strpos($http_user_agent,"Android")===false
	&& strpos($http_user_agent,"Windows Phone")===false
	&& strpos($http_user_agent,"Windows CE")===false
	&& strpos($http_user_agent,"WOW64")===false) {

	header("Location:".$Dir);
	//exit;
}
*/

if(strlen(RootPath)>0) {
	$hostscript=getenv("HTTP_HOST").getenv("SCRIPT_NAME");
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=getenv("HTTP_HOST")."/";
}

/*
if(getenv("HTTPS")=="on") {
	//http로 리다이렉트한다.
	header("Location:http://".$shopurl);
	exit;
}
*/

$imagepath=$Dir.DataDir."shopimages/product/";

$old_shopurl=$_MShopInfo->getShopurl();

$ref=(isset($_REQUEST["ref"])?$_REQUEST["ref"]:"");
if (strlen($ref)==0 && strlen(getenv("HTTP_REFERER"))>0) {
	$ref=strtolower(str_replace("http://","",getenv("HTTP_REFERER")));

}

if (strlen($_MShopInfo->getShopurl())==0) {
	$sql = "SELECT * FROM tblshopinfo ";
	$result=pmysql_query($sql,get_mdb_conn());
	if ($row=pmysql_fetch_object($result)) {
		$_MShopInfo->setShopurl($shopurl);
		$_MShopInfo->Save();
	} else {
		header("Content-Type: text/html; charset=euc-kr");
		echo "<script>alert('쇼핑몰 정보 등록이 안되었습니다.');</script>\n"; exit;
	}
	pmysql_free_result($result);
}
$_MShopData=new _MShopdata($_MShopInfo);

$_data=$_MShopData->shopdata;
$_data->shopurl=$shopurl;


//모바일 페이지 허용 ip 셋팅
/*
$agree_ip_arr[]="218.234.32.3";
$agree_ip_arr[]="218.234.32.10";
$agree_ip_arr[]="218.234.32.11";
$agree_ip_arr[]="218.234.32.12";
$agree_ip_arr[]="218.234.32.13";
$agree_ip_arr[]="223.62.169.17";
$agree_ip_arr[]="175.223.16.6";
$agree_ip_arr[]="218.234.32.28";
$agree_ip_arr[]="223.62.169.77";
$agree_ip_arr[]="218.234.32.8";
$agree_ip_arr[]="183.100.235.102";
$agree_ip_arr[]="218.234.32.14";

$agree_flag = 0;
foreach($agree_ip_arr as $agree_ip){
	if($_SERVER['REMOTE_ADDR']==$agree_ip){
		$agree_flag = 1;
	}
}
if($agree_flag){

}else{
	if($_data->smart_use!='y'){
		header("Location:".$Dir);
	}
}
*/

$id = $_COOKIE["smart_id"];
$md5_id = md5($id);

if($AJAX_USE !== true) {

	if(strlen($_COOKIE["smart_pw"])>0) {

		$passwd=decrypt_md5($_COOKIE["smart_pw"]);	

		$passwd=reset(explode('U',$passwd));
	}

	if ($_data->adult_type=="Y" || $_data->adult_type=="B") {
		Header("Location:".$Dir);
		echo "성인인증 및 b2b 쇼핑몰이다";
		exit;
    } else if ($_data->adult_type=="M" && strlen($_MShopInfo->getMemid())==0) {

        if (($_COOKIE["auto_login"]!="OK" || strlen($id)==0 && strlen($passwd)==0) && substr($_SERVER["SCRIPT_NAME"],1,11)!="m/login.php") {
            $login_chk_shop = true;
            include_once "login.php";
            exit;
        }
    }
    if (strlen($_MShopInfo->getMemid())==0) {

        if ($_COOKIE["auto_login"]=="OK" && strlen($id)>0 && strlen($passwd)>0) {
			$resdata=loginprocess($id, $passwd);

			if($resdata=="CONFIRM") {
				response(false, "쇼핑몰 운영자 인증 후 로그인이 가능합니다.\\n\\n전화로 문의바랍니다.\\n\\n".$_data->info_tel);
			} else if($resdata=="OK") {
                $smart_id = $id;
                $login_chk = true;
			} else {
				if($_COOKIE["save_id"]!='Y'){
					setcookie("smart_id", "", 0, "/".RootPath."m/", getCookieDomain());				
				}
				setcookie($md5_id."_key", "", 0, "/".RootPath."m/", getCookieDomain());

				$smart_id = $id;
			}
        }else{
			$smart_id = $id;
		}
		
    } 
	
	/*	
	else {
        $smart_id = $id;
    }
	*/
} else {
    $chk_action = array("WISHLIST_ADD", "WISHLIST_DEL");
    if (strlen($_MShopInfo->getMemid())==0 && array_search(strtoupper($_REQUEST["action_mode"]), $chk_action) !== false) {
        response(false, "로그인이 필요한 서비스입니다.", "LOGIN");

    }
}
?>
