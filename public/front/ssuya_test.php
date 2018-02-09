<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");




$sql = "SELECT * FROM tblmember WHERE mb_type='facebook'";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	echo $row->mb_facebook_email." > ".$row->passwd."<br>";
}

pmysql_free_result($result);
?>