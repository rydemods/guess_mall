<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/file.class.php");
include("access.php");

$mode=$_POST[mode];
$no=$_POST[no];

$date=date("YmdHis");

if($mode=='cwl_cate_ins'){
	$category_name=$_POST[category_name];
	$max_sort=pmysql_fetch_object(pmysql_query("select max(sort_num) as maxnum from tblcwlcategory"));
	$sortnum=$max_sort->maxnum+1;
	
	$icofile = $_FILES["icofile"];
	$vicoImage = $_POST["vicoImage"];
	
	//print_r($_FILES);
	//exit;

	if(ord($icofile["name"])){
		$icofilePATH = $Dir.DataDir."shopimages/cwl/category/";
		
		if ( is_file($icofilePATH.$vicoImage) ) {
			unlink($icofilePATH.$vicoImage);
		}
		
		if (ord($icofile['name']) && file_exists($icofile['tmp_name'])) {
			$ext = strtolower(pathinfo($icofile['name'],PATHINFO_EXTENSION));
			//exdebug($ext);
			if(in_array($ext,array('gif','jpg','png'))) {
				$up_icoFile = date("YmdHis")."_ico".".".$ext;
				move_uploaded_file($icofile['tmp_name'], $icofilePATH.$up_icoFile);
				chmod($icofilePATH.$up_icoFile,0664);
			} else {
				$up_icoFile	="";
			}
		} 
						
	}
	
	$infofile = $_FILES["infofile"];
	$vinfoImage = $_POST["vinfoImage"];
	if(ord($infofile["name"])){
		$infofilePATH = $Dir.DataDir."shopimages/cwl/category/";
		
		if ( is_file($infofilePATH.$vinfoImage) ) {
			unlink($infofilePATH.$vinfoImage);
		}
		
		if (ord($infofile['name']) && file_exists($infofile['tmp_name'])) {
			$ext = strtolower(pathinfo($infofile['name'],PATHINFO_EXTENSION));
			//exdebug($ext);
			if(in_array($ext,array('gif','jpg','png'))) {
				$up_infoFile =  date("YmdHis")."_info".".".$ext;
				move_uploaded_file($infofile['tmp_name'], $infofilePATH.$up_infoFile);
				chmod($infofilePATH.$up_infoFile,0664);
			} else {
				$up_infoFile="";
			}
		} 						
	}

	$qry="insert into tblcwlcategory (category_name,icoimage,infoimage,sort_num,date)values('{$category_name}', '{$up_icoFile}', '{$up_infoFile}', '{$sortnum}', '{$date}')";
	
	pmysql_query($qry);
	$msg="카테고리가 등록 되었습니다.";
	msg($msg,"cwl_category.php");	
	
}else if($mode=='cwl_cate_mod'){
	$category_name=$_POST[category_name];
	
	$icofile = $_FILES["icofile"];
	$vicoImage = $_POST["vicoImage"];
	
	//print_r($_FILES);
	//exit;

	if(ord($icofile["name"])){
		$icofilePATH = $Dir.DataDir."shopimages/cwl/category/";
		
		if ( is_file($icofilePATH.$vicoImage) ) {
			unlink($icofilePATH.$vicoImage);
		}
		
		if (ord($icofile['name']) && file_exists($icofile['tmp_name'])) {
			$ext = strtolower(pathinfo($icofile['name'],PATHINFO_EXTENSION));
			//exdebug($ext);
			if(in_array($ext,array('gif','jpg','png'))) {
				$up_icoFile = date("YmdHis")."_ico".".".$ext;
				move_uploaded_file($icofile['tmp_name'], $icofilePATH.$up_icoFile);
				chmod($icofilePATH.$up_icoFile,0664);
			} else {
				$up_icoFile	="";
			}
		} 
						
	}
	
	$infofile = $_FILES["infofile"];
	$vinfoImage = $_POST["vinfoImage"];
	if(ord($infofile["name"])){
		$infofilePATH = $Dir.DataDir."shopimages/cwl/category/";
		
		if ( is_file($infofilePATH.$vinfoImage) ) {
			unlink($infofilePATH.$vinfoImage);
		}
		
		if (ord($infofile['name']) && file_exists($infofile['tmp_name'])) {
			$ext = strtolower(pathinfo($infofile['name'],PATHINFO_EXTENSION));
			//exdebug($ext);
			if(in_array($ext,array('gif','jpg','png'))) {
				$up_infoFile =  date("YmdHis")."_info".".".$ext;
				move_uploaded_file($infofile['tmp_name'], $infofilePATH.$up_infoFile);
				chmod($infofilePATH.$up_infoFile,0664);
			} else {
				$up_infoFile="";
			}
		} 						
	}
	
	$qry	 = "update tblcwlcategory set category_name='{$category_name}' ";
	if ($up_icoFile) $qry	.= ", icoimage='{$up_icoFile}' ";
	if ($up_infoFile) $qry	.= ", infoimage='{$up_infoFile}' ";
	$qry	.= "where num='{$no}'";
	
	pmysql_query($qry);
	$msg="카테고리가 수정 되었습니다.";
	msg($msg,"cwl_category.php");	
}else if($mode=='cwl_cate_del'){
	
	$del_row=pmysql_fetch_object(pmysql_query("select icoimage, infoimage from tblcwlcategory where num='{$no}'"));
	$icoimage=$del_row->icoimage;
	$infoimage=$del_row->infoimage;

	$infofilePATH = $Dir.DataDir."shopimages/cwl/category/";

	if ($icoimage) {
		if ( is_file($infofilePATH.$icoimage) ) {
			unlink($infofilePATH.$icoimage);
		}
	}

	if ($infoimage) {
		if ( is_file($infofilePATH.$infoimage) ) {
			unlink($infofilePATH.$infoimage);
		}
	}

	$qry="delete from tblcwlcategory where num='{$no}'";
	pmysql_query($qry);
	
	//$del_qry="delete from tblcwl where cwl_type='{$no}'";
	//pmysql_query($del_qry);
	
	$msg="카테고리가 삭제 되었습니다.";
	msg($msg,"cwl_category.php");	
	
}else if($mode=="cwl_cate_secret"){
	$num=$_POST[num];
	$secret=$_POST[secret];
	$sort_num=$_POST[sort_num];
	
	foreach($num as $k){
		$up_qry="update tblcwlcategory set sort_num='{$sort_num[$k]}' where num='{$k}'";
		pmysql_query($up_qry);
		
		$up_qry="update tblcwlcategory set secret='0' where num='{$k}'";
		pmysql_query($up_qry);
	}
	
	foreach($secret as $v){
		$up_qry="update tblcwlcategory set secret='1' where num='{$v}'";
		pmysql_query($up_qry);
	}
	$msg="변경되었습니다.";
	msg($msg,"cwl_category.php");	
}else if($mode=='cwl_ins'){
	$category_num	= $_POST[category_num];	
	$title					= $_POST[title];	
	$productcode		= $_POST[productcode];	
	$titlefile				= $_FILES["titlefile"];
	$vtitleImage		= $_POST["vtitleImage"];
	
	if(ord($titlefile["name"])){
		$titlefilePATH = $Dir.DataDir."shopimages/cwl/board/";
		
		if ( is_file($titlefilePATH.$vtitleImage) ) {
			unlink($titlefilePATH.$vtitleImage);
		}
		
		if (ord($titlefile['name']) && file_exists($titlefile['tmp_name'])) {
			$ext = strtolower(pathinfo($titlefile['name'],PATHINFO_EXTENSION));
			//exdebug($ext);
			if(in_array($ext,array('gif','jpg','png'))) {
				$up_titleFile = date("YmdHis")."_title".".".$ext;
				move_uploaded_file($titlefile['tmp_name'], $titlefilePATH.$up_titleFile);
				chmod($titlefilePATH.$up_titleFile,0664);
			} else {
				$up_titleFile	="";
			}
		} 
						
	}

	$qry="insert into tblcwlboard (category_num,title,image,productcode,secret,date)values('{$category_num}', '{$title}', '{$up_titleFile}', '{$productcode}', '1', '{$date}')";
	
	pmysql_query($qry);
	$msg="등록 되었습니다.";
	msg($msg,"cwl_board.php");	
	
}else if($mode=='cwl_mod'){
	$category_num	= $_POST[category_num];	
	$title					= $_POST[title];	
	$productcode		= $_POST[productcode];	
	$titlefile				= $_FILES["titlefile"];
	$vtitleImage		= $_POST["vtitleImage"];

	if(ord($titlefile["name"])){
		$titlefilePATH = $Dir.DataDir."shopimages/cwl/board/";
		
		if ( is_file($titlefilePATH.$vtitleImage) ) {
			unlink($titlefilePATH.$vtitleImage);
		}
		
		if (ord($titlefile['name']) && file_exists($titlefile['tmp_name'])) {
			$ext = strtolower(pathinfo($titlefile['name'],PATHINFO_EXTENSION));
			//exdebug($ext);
			if(in_array($ext,array('gif','jpg','png'))) {
				$up_titleFile = date("YmdHis")."_title".".".$ext;
				move_uploaded_file($titlefile['tmp_name'], $titlefilePATH.$up_titleFile);
				chmod($titlefilePATH.$up_titleFile,0664);
			} else {
				$up_titleFile	="";
			}
		} 
						
	}
	
	$qry	 = "update tblcwlboard set category_num='{$category_num}', title='{$title}', productcode='{$productcode}' ";
	if ($up_titleFile) $qry	.= ", image='{$up_titleFile}' ";
	$qry	.= "where num='{$no}'";
	
	pmysql_query($qry);
	$msg="수정 되었습니다.";
	msg($msg,"cwl_board.php");	
}else if($mode=='cwl_del'){
	
	$del_row=pmysql_fetch_object(pmysql_query("select image from tblcwlboard where num='{$no}'"));
	$titleimage=$del_row->image;

	$infofilePATH = $Dir.DataDir."shopimages/cwl/board/";

	if ($titleimage) {
		if ( is_file($infofilePATH.$titleimage) ) {
			unlink($infofilePATH.$titleimage);
		}
	}

	$qry="delete from tblcwlboard where num='{$no}'";
	pmysql_query($qry);
	
	//$del_qry="delete from tblcwl where cwl_type='{$no}'";
	//pmysql_query($del_qry);
	
	$msg="삭제 되었습니다.";
	msg($msg,"cwl_board.php");	
	
}else if($mode=="cwl_secret"){
	$num=$_POST[num];
	$secret=$_POST[secret];
	
	foreach($num as $k){		
		$up_qry="update tblcwlboard set secret='0' where num='{$k}'";
		pmysql_query($up_qry);
	}
	
	foreach($secret as $v){
		$up_qry="update tblcwlboard set secret='1' where num='{$v}'";
		pmysql_query($up_qry);
	}
	$msg="변경되었습니다.";
	msg($msg,"cwl_board.php");	
}
?>
