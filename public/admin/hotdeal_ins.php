<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.php");
####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_REQUEST['mode'];
$sno=$_REQUEST['sno'];
$imagepath = $cfg_img_path['hotdeal'];
$filedata= new FILE($imagepath);
$errmsg = $filedata->chkExt();


if($mode=='ins'){

	//재고체크
	$cqry="select quantity from tblproduct where productcode='".$_POST['pdt_code'][0]."'";
	$cres=pmysql_query($cqry);
	$crow=pmysql_fetch_array($cres);

	//동일상품 등록체크
	$chkqry="select count(*) as cnt from tblhotdeal where ((sdate between '".$_POST['sdate']."' and '".$_POST['edate']."') or (edate between '".$_POST['sdate']."' and '".$_POST['edate']."'))";

	$chkres=pmysql_query($chkqry);
	$chkrow=pmysql_fetch_array($chkres);

	if($chkrow['cnt']>0){
		msg('이미 중복된 시간의 상품이 있습니다.','hotdeal_reg.php?mode=ins');
	}else{
		if($errmsg==''){
			$up_file = $filedata->upFiles();
		}
		if($_POST['view_type']=="1"){
			pmysql_query("update tblhotdeal set view_type='0'");
		}
		$qry="insert into tblhotdeal(title, productcode, view_img, view_type, sdate, regdt, view_img_m, bottom_img, bottom_img_m)
		values('".$_POST['title']."','".$_POST['pdt_code'][0]."','".$up_file["view_img"][0]["v_file"]."', '".$_POST['view_type']."','".$_POST['sdate']."',now(),'".$up_file["view_img_m"][0]["v_file"]."','".$up_file["bottom_img"][0]["v_file"]."','".$up_file["bottom_img_m"][0]["v_file"]."')";
					
		if(pmysql_query($qry)){
			msg('등록되었습니다.','hotdeal_list.php');
		}else{	
			msg('등록실패','hotdeal_reg.php?mode=ins');
		}
	}
}else if($mode=='mod'){
	
	$sno=$_REQUEST['sno'];

	$cgqry="select count(*) as cnt from tblhotdeal where ((sdate between '".$_POST['sdate']."' and '".$_POST['edate']."') or (edate between '".$_POST['sdate']."' and '".$_POST['edate']."')) and sno!='".$sno."'";
	$cgres=pmysql_query($cgqry);
	$cgrow=pmysql_fetch_array($cgres);

	//재고체크
	$cqry="select quantity from tblproduct where productcode='".$_POST['pdt_code']."'";
	$cres=pmysql_query($cqry);
	$crow=pmysql_fetch_array($cres);

	if($chkrow['cnt']>0){
		msg('이미 중복된 시간의 상품이 있습니다.','hotdeal_reg.php?mode=ins');
	}else{
		if($errmsg==''){
			$up_file = $filedata->upFiles();
		}

		if($_POST['view_type']=="1"){
			pmysql_query("update tblhotdeal set view_type='0'");
		}
		
		$qry="update tblhotdeal set
		title='".$_POST['title']."',
		productcode='".$_POST['pdt_code'][0]."',
		sdate='".$_POST['sdate']."',
		view_type='".$_POST['view_type']."'";		
		
		if($up_file["view_img"][0]["v_file"]){
			list($view_img)=pmysql_fetch("select view_img from tblhotdeal where sno='".$sno."'");
			
			if($view_img) @unlink($imagepath.$view_img);
			$qry.=", view_img='".$up_file["view_img"][0]["v_file"]."'";
		}

		if($up_file["view_img_m"][0]["v_file"]){
			list($view_img_m)=pmysql_fetch("select view_img_m from tblhotdeal where sno='".$sno."'");
			
			if($view_img_m) @unlink($imagepath.$view_img_m);
			$qry.=", view_img_m='".$up_file["view_img_m"][0]["v_file"]."'";
		}

		if($up_file["bottom_img"][0]["v_file"]){
			list($bottom_img)=pmysql_fetch("select bottom_img from tblhotdeal where sno='".$sno."'");
			
			if($bottom_img) @unlink($imagepath.$bottom_img);
			$qry.=", bottom_img='".$up_file["bottom_img"][0]["v_file"]."'";
		}

		if($up_file["bottom_img_m"][0]["v_file"]){
			list($bottom_img_m)=pmysql_fetch("select bottom_img_m from tblhotdeal where sno='".$sno."'");
			
			if($bottom_img_m) @unlink($imagepath.$bottom_img_m);
			$qry.=", bottom_img_m='".$up_file["bottom_img_m"][0]["v_file"]."'";
		}
		
		$qry.="where sno='".$sno."'";

		if(pmysql_query($qry)){
			msg('수정되었습니다.',"hotdeal_reg.php?sno=$sno&mode=$mode");
		}else{	
			msg('수정실패',"hotdeal_reg.php?sno=$sno&mode=$mode");
		}
	}
}else if($mode=='del'){
	list($view_img)=pmysql_fetch("select view_img from tblhotdeal where sno='".$sno."'");
	if($view_img) @unlink($imagepath.$view_img);

	list($view_img_m)=pmysql_fetch("select view_img_m from tblhotdeal where sno='".$sno."'");
	if($view_img_m) @unlink($imagepath.$view_img_m);

	list($bottom_img)=pmysql_fetch("select bottom_img from tblhotdeal where sno='".$sno."'");
	if($bottom_img) @unlink($imagepath.$bottom_img);

	list($bottom_img_m)=pmysql_fetch("select bottom_img_m from tblhotdeal where sno='".$sno."'");
	if($bottom_img_m) @unlink($imagepath.$bottom_img_m);
		
	$qry="delete from tblhotdeal where sno='".$sno."'";
	if(pmysql_query($qry)){
		msg('삭제되었습니다.','hotdeal_list.php');
	}else{	
		msg('삭제되었습니다','hotdeal_list.php');
	}
}


?>