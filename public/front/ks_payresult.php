<?php
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


$sql = "SELECT id FROM tblmember WHERE id='oyaback' ";
$result = pmysql_query( $sql, get_db_conn() );
if( $row = pmysql_fetch_object( $result ) ) {
	//$this->order['ordercode'] = unique_id();
	//$this->order['id'] =  $row->id;
	$ordercode = unique_id();
	$id = $row->id;
	pmysql_free_result( $result );
}
date();
/*
$u_sql = "update tblorderinfo set ordercode='".$ordercode."',bank_date='20170906123946', staff_price=214500 where ordercode='2017083112391624208A'";
pmysql_query($u_sql,get_db_conn());
$up_sql_1 = "update tblorderproduct set ordercode='".$ordercode."',date='20170906', ori_price=399000 where ordercode='2017083112391624208A' and idx='26385'";
pmysql_query($up_sql_1,get_db_conn());
$up_sql_2 = "update tblorderproduct set ordercode='".$ordercode."',date='20170906',ori_price=269000,staff_price=214500 where ordercode='2017083112391624208A' and idx='26386'";
pmysql_query($up_sql_2,get_db_conn());
//$up_sql_3 = "update tblorderproduct set ordercode='".$ordercode."',date='20170906',ori_price=178000,staff_price=163500 where ordercode='2017083113081749203A' and idx='26372'";
//pmysql_query($up_sql_3,get_db_conn());
//$up_sql_4 = "update tblorderproduct set ordercode='".$ordercode."',date='20170906',ori_price=398000,staff_price=388500 where ordercode='2017083113081749203A' and idx='26373'";
//pmysql_query($up_sql_4,get_db_conn());
$uc_sql = "update tblcoupon_order set ordercode='".$ordercode."', date='20170906123916' where idx='6847'";
pmysql_query($uc_sql,get_db_conn());

echo $u_sql;
echo "<br/>";
echo $up_sql_1;
echo "<br/>";
echo $up_sql_2;
echo "<br/>";
echo $up_sql_3;
echo "<br/>";
echo $uc_sql;
/*
2017083112391624208A
bank_date= "20170831123946"
26385
26386
date="20170831"
date="20170831"
deli_date='',
ori_price=
99000
54500

6847
date="20170831123916"

2017090712341541715A

/*
2017083113054869828A
bank_date= "20170831130649"
26395
date="20170831"
deli_date='',
ori_price=
9500




2017090622095087619A

*/



exit;
/*
//매장 발송
$u_sql = "update tblorderinfo set ordercode='".$ordercode."',bank_date='20170906123946', staff_price= where ordercode='2017083112391624208A'";
pmysql_query($u_sql,get_db_conn());
$up_sql_1 = "update tblorderproduct set ordercode='".$ordercode."',date='20170906', ori_price=399000,staff_price= where ordercode='2017083112391624208A' and idx='26385'";
pmysql_query($up_sql_1,get_db_conn());
$up_sql_2 = "update tblorderproduct set ordercode='".$ordercode."',date='20170906',ori_price=269000,staff_price=214500 where ordercode='2017083112391624208A' and idx='26386'";
pmysql_query($up_sql_2,get_db_conn());
//$up_sql_3 = "update tblorderproduct set ordercode='".$ordercode."',date='20170906',ori_price=178000,staff_price=163500 where ordercode='2017083113081749203A' and idx='26372'";
//pmysql_query($up_sql_3,get_db_conn());
//$up_sql_4 = "update tblorderproduct set ordercode='".$ordercode."',date='20170906',ori_price=398000,staff_price=388500 where ordercode='2017083113081749203A' and idx='26373'";
//pmysql_query($up_sql_4,get_db_conn());
$uc_sql = "update tblcoupon_order set ordercode='".$ordercode."', date='20170906123916' where idx='6847'";
pmysql_query($uc_sql,get_db_conn());

echo $u_sql;
echo "<br/>";
echo $up_sql_1;
echo "<br/>";
echo $up_sql_2;
echo "<br/>";
echo $up_sql_3;
echo "<br/>";
echo $uc_sql;
/*
2017083112391624208A
bank_date= "20170831123946"
26385
26386
date="20170831"
date="20170831"
deli_date='',
ori_price=
99000
54500

6847
date="20170831123916"


*/


?>
