<?
Header('Content-Type: text/html; charset=euc-kr');
include("../../lib/init.php");
include("../../lib/lib.php");
$sql = "SELECT * FROM tblnewsletter order by date DESC , no DESC";
$result = pmysql_query($sql,get_db_conn());
$qq ;
	if( $row=pmysql_fetch_object($result) ) {
			echo $row->html ;
	}
pmysql_free_result($result);
?>
