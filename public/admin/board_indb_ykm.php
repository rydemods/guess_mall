<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/recipe.class.php");
	$file = new FILE();

	if($_REQUEST["module"]=="product_review"){

		$sqlfile = file_get_contents("http://www.soapschool.co.kr/shop/admin/member/board_dump_ykm.php?mode=product_review");
		$sqlarray = explode("insert into", $sqlfile);
		for($i=1; $i< count($sqlarray); $i++){

			$sql = "insert into ".$sqlarray[$i];
			echo $i." : ";
			$status = pmysql_query($sql, get_db_conn());
			echo ": ";
			echo $status?$status:$sql;
			echo "<br>";
		}
		exit;
	}
	if($_REQUEST["module"]=="product_review_match"){
		$query = "	select p.productcode, m.id, pr.num, m.name from tblproductreview pr
					left join tblproduct p on pr.goodsno = p.goodsno
					left join tblmember m on m.m_no = pr.m_no
				 ";
		$result = pmysql_query($query,get_db_conn());
		while($row = pmysql_fetch_array($result)){
			$sql = " update tblproductreview set productcode = '".$row[productcode]."', name='".$row[name]."', id='".$row[id]."' where num='".$row[num]."'";
			echo pmysql_query($sql);
			echo "<br>";
		}
		exit;
	}

	if($_REQUEST["module"]=="board_admin"){

		$sqlfile = file_get_contents("http://www.soapschool.co.kr/shop/admin/member/board_dump_ykm.php?mode=board_admin");
		$sqlarray = explode("insert into", $sqlfile);
		for($i=1; $i< count($sqlarray); $i++){

			$sql = "insert into ".$sqlarray[$i];
			echo $i." : ";
			$status = pmysql_query($sql, get_db_conn());
			echo ": ";
			echo $status?$status:$sql;
			echo "<br>";
		}
		exit;
	}
	if($_REQUEST["module"]=="board_tip"){

		$sqlfile = file_get_contents("http://www.soapschool.co.kr/shop/admin/member/board_dump_ykm.php?mode=board_tip");

		$sqlarray = explode("update", $sqlfile);
		for($i=1; $i< count($sqlarray); $i++){

			$sql = "update ".$sqlarray[$i];
			echo $i." : ";
			$status = pmysql_query($sql, get_db_conn());
			echo ": ";
			echo $status?$status:$sql;
			echo "<br>";
		}
		exit;
	}

	if($_REQUEST["module"]=="board"){
		if($_REQUEST[page]) $sqlfile = file_get_contents("http://www.soapschool.co.kr/shop/admin/member/board_dump_ykm.php?page=".$_REQUEST[page]);
		$sqlarray = explode("insert into", $sqlfile);
		for($i=1; $i< count($sqlarray); $i++){
			$sql = "insert into ".$sqlarray[$i];
			echo $i." : ";
			echo pmysql_query($sql, get_db_conn());
			echo "<br>";
		}
		exit;
	}else if($_REQUEST["module"]=="comment"){
		if($_REQUEST[page]) $sqlfile = file_get_contents("http://www.soapschool.co.kr/shop/admin/member/board_comment_dump_ykm.php?page=".$_REQUEST[page]);
		$sqlarray = explode("insert into", $sqlfile);
		for($i=1; $i< count($sqlarray); $i++){
			$sql = "insert into ".$sqlarray[$i];
			echo $i." : ";
			echo pmysql_query($sql, get_db_conn());
			echo "<br>";
		}
		exit;
	}
?>