<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include($Dir."lib/file.class.php");

$mode = $_POST['mode'];

if($mode=='insert'){
	$board_code = $_POST['board_code'];
	$page_code = $_POST['page_code'];
	if($page_code == "") $page_code = 0;
	//제목 타이틀
	$board_title = $_POST['board_title'];
	$board_content = $_POST['board_content'];
	//이미지
	$boardImagepath = $Dir.DataDir."shopimages/brandboard/";
	$board_file = new FILE($boardImagepath);
	$board_img = $board_file->upFiles();
	$big_image = $board_img['big_image'][0]['v_file'];
	$thumbnail_image = $board_img['thumbnail_image'][0]['v_file'];
	//대표상품
	$productcode = $_POST['productcode'];
	//관련상품
	$relationProduct = $_POST['relationProduct'];
	
	$sql = "INSERT INTO tblbrand_board ";
	$sql.= "( board_code, productcode, board_title, board_content, big_image, thumbnail_image, date, page_code) ";
	$sql.= "VALUES (".$board_code.",'".$productcode."','".pmysql_escape_string($board_title)."','".pmysql_escape_string($board_content)."','".$big_image."','".$thumbnail_image."','".date("YmdHis")."', '".$page_code."') ";
	$sql.= "RETURNING board_num ";
	
	$result = pmysql_query($sql,get_db_conn());
	
	if($row = pmysql_fetch_object($result)){
		if($relationProduct){
			for($i=0;$i<count($relationProduct);$i++){
				$relationProduct_sql = "INSERT INTO tblbrand_boarditem ";
				$relationProduct_sql.= "(board_code, board_num, productcode, date) ";
				$relationProduct_sql.= "VALUES (".$board_code.", ".$row->board_num.", '".$relationProduct[$i]."', '".date("YmdHis")."') ";
				pmysql_query($relationProduct_sql,get_db_conn());
			}
		}
		echo "<script>alert('입력이 완료되었습니다.'); location.replace('community_brandboard.php');</script>";
	}else{
		//echo "<script>alert('오류가 발생하였습니다.'); location.replace('community_brandboard.php');</script>";
		exdebug($sql);
	}
}else if($mode=="modify"){
	//게시판 번호
	$board_num = $_POST['board_num'];
	$board_code = $_POST['this_boardCode'];
	$modify_boardCode = $_POST["board_code"];
	$page_code = $_POST['page_code'];
	//제목 타이틀
	$board_title = $_POST['board_title'];
	$board_content = $_POST['board_content'];
	//이미지
	$boardImagepath = $Dir.DataDir."shopimages/brandboard/";
	$board_file = new FILE($boardImagepath);
	$board_img = $board_file->upFiles();
	$big_image = $board_img['big_image'][0]['v_file'];
	$thumbnail_image = $board_img['thumbnail_image'][0]['v_file'];
	//대표상품
	$productcode = $_POST['productcode'];
	//관련상품
	$relationProduct = $_POST['relationProduct'];
	
	$sql = "UPDATE tblbrand_board SET ";
	//$sql.= "board_num = ".$board_num.", ";
	if($modify_boardCode != ""){
		$sql.= "board_code = '".$modify_boardCode."', ";
	}
	$sql.= "board_title = '".pmysql_escape_string($board_title)."', ";
	$sql.= "board_content = '".pmysql_escape_string($board_content)."', ";
	$sql.= "productcode = '".$productcode."', ";
	if($board_img['big_image'][0]['v_file']){
		$sql.= "big_image = '".$board_img."', ";
	}
	if($board_img['thumbnail_image'][0]['v_file']){
		$sql.= "thumbnail_image = '".$thumbnail_image."', ";
	}
	if($page_code != ""){
		$sql.= "page_code = ".$page_code.", ";
	}
	$sql.= "modify_date = '".date("YmdHis")."' ";
	$sql.= "WHERE board_num = ".$board_num." ";
	
	pmysql_query($sql,get_db_conn());
	if(!pmysql_error()){
		if($relationProduct){
			$relationProduct_del = "DELETE FROM tblbrand_boarditem WHERE board_num = '".$board_num."' ";
			pmysql_query($relationProduct_del,get_db_conn());
			for($i=0;$i<count($relationProduct);$i++){
				$relationProduct_sql = "INSERT INTO tblbrand_boarditem ";
				$relationProduct_sql.= "(board_code, board_num, productcode, date) ";
				$relationProduct_sql.= "VALUES (".$board_code.", ".$board_num.", '".$relationProduct[$i]."', '".date("YmdHis")."') ";
				pmysql_query($relationProduct_sql,get_db_conn());
			}
		}
		echo "<script>alert('수정이 완료되었습니다.'); location.replace('community_brandboard.php');</script>";
	}else{
		echo "<script>alert('오류가 발생하였습니다.'); location.replace('community_brandboard.php');</script>";
	} 
	
}else if($mode=="delete"){
	$board_num = $_POST["board_num"];
	$relationProduct_del = "DELETE FROM tblbrand_boarditem WHERE board_num = '".$board_num."' ";
	pmysql_query($relationProduct_del,get_db_conn());
	
	$sql = "DELETE FROM tblbrand_board WHERE board_num=".$board_num;
	pmysql_query($sql,get_db_conn());
	
	echo "<script>alert('삭제가 완료되었습니다.'); location.replace('community_brandboard.php');</script>";
}

?>