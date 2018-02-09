<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
		
		### 분류트리순서 저장
		if ($_POST[cate1]) foreach ($_POST[cate1] as $k=>$v){
			 $qry="update tblproductcode set cate_sort=$k where code_a='$v' and code_b='000'";
			
			$res = pmysql_query($qry,get_db_conn());
		}

		if ($_POST[cate2]) foreach ($_POST[cate2] as $k=>$v){
			 $qry="update tblproductcode set cate_sort=$k where code_a || code_b = '$v' and code_c='000'";
			
			$res = pmysql_query($qry,get_db_conn());
		}
		if ($_POST[cate3]) foreach ($_POST[cate3] as $k=>$v){
			 $qry="update tblproductcode set cate_sort=$k where code_a || code_b || code_c = '$v' and code_d='000'";
			
			$res = pmysql_query($qry,get_db_conn());
		}
		if ($_POST[cate4]) foreach ($_POST[cate4] as $k=>$v){
			 $qry="update tblproductcode set cate_sort=$k where code_a || code_b || code_c || code_d = '$v'";
			
			$res = pmysql_query($qry,get_db_conn());
		}
		
		//go("product_code.property.php?code=$_POST[code]&mode=modify");
		//echo "<script>document.location.href='product_code.php?category=".$_POST[cate]."'</script>";
		echo "<script>parent.document.location.href='product_code.php?category=".$_POST[cate]."'</script>";
		

?>