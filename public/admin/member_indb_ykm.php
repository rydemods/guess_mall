<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/recipe.class.php");
	$file = new FILE();
	
	if($_REQUEST["module"]=="member"){
		//http://soapschool.ajashop.co.kr/admin/member_indb_ykm.php?module=member&page=1
		if($_REQUEST[page]) $sqlfile = file_get_contents("http://www.soapschool.co.kr/shop/admin/member/member_dump.php?page=".$_REQUEST[page]);
		$sqlarray = explode("insert into", $sqlfile);
		for($i=1; $i< count($sqlarray); $i++){
			$sql = "insert into ".$sqlarray[$i];
//			echo $i." : ";
			if(!pmysql_query($sql, get_db_conn())) echo $sql."<br>";
		}
		echo $_REQUEST[page]."완료!!!!";
		exit;
	}else if($_REQUEST["module"]=="member_all_check"){
		//http://soapschool.ajashop.co.kr/admin/member_indb_ykm.php?module=member_all_check
		//옮긴 데이터중에 옮겨지지 않은 데이터 출력
		$sqlfile = file_get_contents("http://www.soapschool.co.kr/shop/admin/member/member_dump.php");
		$sqlarray = explode("select!!!!", $sqlfile);
		for($i=1; $i< count($sqlarray); $i++){
			$result_total=pmysql_query("select id from tblmember where id = '".$sqlarray[$i]."'");
			$member_id=pmysql_fetch($result_total);
			if(!$member_id[id]){
				echo $sqlarray[$i];
				echo "<br>";
			}
		}
 
		exit;
	}else if($_REQUEST["module"]=="change_pass"){
		
		/*
		실행 쿼리
		update tblmember set passwd = md5(trim((substr(mobile, length(mobile)-3, length(mobile))))) WHERE substr(logindate, 0, 9) < '20140213' and length(substr(mobile, length(mobile)-3, length(mobile))) = 4
		*/

		exit;
	}
?>