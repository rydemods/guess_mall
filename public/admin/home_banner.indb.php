<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/file.class.php");
	include("access.php");

	$imagepath=$Dir.DataDir."shopimages/homebanner/";
	$code = $_POST['code'];
	$banner_img=$_POST["banner_img"];
	$banner_sort=$_POST["banner_sort"];
	$banner_link=$_POST["banner_link"];
	$banner_title=$_POST["banner_title"];
	$banner_hidden=$_POST["banner_hidden"];
	$banner_no=$_POST["banner_no"];
	$banner_delete=$_POST["deleteRows"];

	$banner_file = new FILE($imagepath);

	$addImageAndTitle = array('home_history', 'home_business', 'home_brand');


	$result = pmysql_query("select * from tblhomebanner where title='".$code."'", get_db_conn());
	$row = pmysql_fetch_object($result);
	$banner_img = $banner_file->upFiles();

	foreach($_POST['banner_mode'] as $key => $val){
		if($val == 'ins'){
			if(in_array($code, $addImageAndTitle)){
				$addQueryCol = ", banner_img_title_on";
				$addQueryCol .= ", banner_img_title_out";

				$addQueryVal = ", '".$banner_img["banner_img_title_on"][$key]["v_file"]."'";
				$addQueryVal .= ", '".$banner_img["banner_img_title_out"][$key]["v_file"]."'";
			}
			$qry="insert into tblhomebannerimg (
			banner_no, 
			banner_img, 
			banner_sort, 
			banner_date, 
			banner_link, 
			banner_hidden,
			banner_number,
			banner_name ".$addQueryCol."
			)values(
			'".$row->no."',
			'".$banner_img["banner_img"][$key]["v_file"]."',
			'".$banner_sort[$key]."',
			'now()',
			'".$banner_link[$key]."',
			'".$banner_hidden[$key]."',
			'".$key."',
			'".$row->title."' ".$addQueryVal."
			)";
		}else{			
			$where = array();
			list($banner_db_img, $banner_img_title_on, $banner_img_title_out) = pmysql_fetch("select banner_img, banner_img_title_on, banner_img_title_out from tblhomebannerimg where no='".$banner_no[$key]."'", get_db_conn());

			if($banner_img["banner_img"][$key]["v_file"]){
				$banner_file->removeFile($banner_db_img);
				$where[]="banner_img='".$banner_img["banner_img"][$key]["v_file"]."'";
			}
			
			$where[]="banner_sort='".$banner_sort[$key]."'";
			$where[]="banner_link='".$banner_link[$key]."'";
			$where[]="banner_hidden='".$banner_hidden[$key]."'";
			



			if(in_array($code, $addImageAndTitle)){
				if($banner_img["banner_img_title_on"][$key]["v_file"]){
					$banner_file->removeFile($banner_img_title_on);
					$where[]="banner_img_title_on='".$banner_img["banner_img_title_on"][$key]["v_file"]."'";
				}
				if($banner_img["banner_img_title_out"][$key]["v_file"]){
					$banner_file->removeFile($banner_img_title_out);
					$where[]="banner_img_title_out='".$banner_img["banner_img_title_out"][$key]["v_file"]."'";
				}
			}


			$qry="update tblhomebannerimg set ";
			$qry.=implode(", ",$where);
			$qry.=" where no='".$banner_no[$key]."'";
		}
		pmysql_query($qry,get_db_conn());
	}

	if($banner_delete){
		$arrDeleteRow = explode("|", $banner_delete);
		foreach($arrDeleteRow as $val){
			if(!$val) continue;
			list($banner_db_img, $banner_img_title_on, $banner_img_title_out) = pmysql_fetch("SELECT banner_img, banner_img_title_on, banner_img_title_out FROM tblhomebannerimg WHERE no='".$val."'", get_db_conn());
			if($banner_db_img){
				$banner_file->removeFile($banner_db_img);
				$banner_file->removeFile($banner_img_title_on);
				$banner_file->removeFile($banner_img_title_out);
			}
			pmysql_query("DELETE FROM tblhomebannerimg WHERE no='".$val."'", get_db_conn());
		}
	}

	alert_go("해당 배너가 수정되었습니다.", "./home_banner.add.php?code=".$code);
?>