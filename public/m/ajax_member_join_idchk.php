<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$id=$_REQUEST["id"];


if(strlen($id)<=0) {
    $message="-3";
}else if(strlen($id)<4 || strlen($id)>12) {
	$message="-1";
} else if(!IsAlphaNumeric($id)) {
	$message="-2";
} else if(!preg_match("/(^[0-9a-zA-Z]{4,12}$)/",$id)) {
	$message="-2";
} else if(preg_match("/(\'|\"|\,|\.|&|%|<|>|\/|\||\\\\|[ ])/",$id)) {
    $message="-2";
} else if(strtolower($id)=="admin") {
    $message="-4";
} else {
	$sql = "SELECT id FROM tblmember WHERE id='{$id}' ";
	$result = pmysql_query($sql,get_db_conn());

	if ($row=pmysql_fetch_array($result)) {
		$message=pmysql_num_rows($result);
	} else {
		$sql = "SELECT id FROM tblmemberout WHERE id='{$id}' ";
		$result2 = pmysql_query($sql,get_db_conn());
		if($row2=pmysql_fetch_object($result2)) {
			$message=pmysql_num_rows($result2);
		} else {
			$message="0";
		}
		pmysql_free_result($result2);
	}
	pmysql_free_result($result);
}

echo $message;

?>