<?
	header("Content-Type: text/plain");
	header("Content-Type: text/html; charset=euc-kr");
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	//include_once($Dir."lib/shopdata.php");
	
	$code_lev = $_REQUEST['c_lev'];
	$c_code = $_POST['c_code'];
	$presentCode = $_POST['presentCode'];
	$result = "";
	if ($code_lev == "a"){
		$cate_sql = "
			select 
			code_a
			,code_b
			,code_c
			,code_d
			,code_name 
			from tblproductcode 
			where type = 'L' 
			AND group_code!='NO' 
			order by cate_sort
		";
		$result=pmysql_query($cate_sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$selectedCode = "";
			if(substr($presentCode,0,3)==$row->code_a) $selectedCode = "selected";
			echo "<option value='".$row->code_a."' ".$selectedCode.">".$row->code_name."</option>";
		}
	} else if ($code_lev == "b") {
		$cate_sql = "
			select 
			code_a 
			,code_b
			,code_c
			,code_d
			,code_name
			,type 
			from tblproductcode 
			where code_a = '".$c_code."' 
			and code_b != '000' 
			and code_c = '000' 
			AND group_code!='NO' 
			order by cate_sort
		";
		$result=pmysql_query($cate_sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$selectedCode = "";
			if(substr($presentCode,0,6)==$row->code_a.$row->code_b) $selectedCode = "selected";
			echo "<option value='".$row->code_a.$row->code_b.$row->code_c.$row->code_d."' ".$selectedCode." >".$row->code_name."</option>";
		}
	} else if ($code_lev == "c") {

	} else {
		$result = "0";
	}
	//echo $result;
	//pmysql_free_result($result);

	//echo $result;
?>