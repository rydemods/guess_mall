<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");


	$logoimagepath = $Dir.DataDir."shopimages/etc/";
	$useyn=$_POST["useyn"];
	$mobile_logo=$_FILES["mobile_logo"];

	if($useyn){
		//$sql="insert into tblmobileShopInfo (useyn)values('".$useyn."')";	
		$sql="update tblmobileShopInfo set useyn='".$useyn."'";
		pmysql_query($sql,get_db_conn());
	}

	if ($mobile_logo['name']) {

		$ext = strtolower(pathinfo($mobile_logo['name'],PATHINFO_EXTENSION));
		if ($ext!="gif") {
			msg("올리실 이미지는 gif파일만 가능합니다.","mobileShop_set.php");
		} else if ($mobile_logo['size']>153600) {
			msg("올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.","mobileShop_set.php");
		} else {
			move_uploaded_file($mobile_logo['tmp_name'],$logoimagepath."logo.gif"); 
			chmod($logoimagepath."logo.gif",0606);

			$sql="update tblmobileShopInfo set logo_img='".$logoimagepath."logo.gif'";
			pmysql_query($sql,get_db_conn());
		}
	}

	msg("적용되었습니다.","mobileShop_set.php");

?>