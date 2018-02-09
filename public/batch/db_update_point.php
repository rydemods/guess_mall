<?php
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

echo "Start ".date("Y-m-d H:i:s")."<br>";
echo "<hr>";

exit;
/** .
 *  rel_job 이 null 인것 유니크값 넣어주기..
 *  이미 지급된 포인트 중복체크에서 값이 다 널이라 중복있는걸로 나옴.
**/
echo "Start 2"."<br>";
echo "<hr>";
// 2.
$sql = "select * from tblpoint where rel_job = '' order by pid";
$result = pmysql_query($sql, get_db_conn());
echo "sql = ".$sql."<br>";
echo "<hr>";

while($row = pmysql_fetch_object($result)) {
    echo "pid = ".$row->pid."<br>";
    echo "id = ".$row->mem_id."<br>";
    echo "unique = "."admin-".uniqid('')."<br>";

    $sql = "update tblpoint set 
                    rel_job = 'admin-".uniqid('')."' 
            Where   pid = '".$row->pid."' 
            ";
    pmysql_query($sql, get_db_conn());
    echo "sql = ".$sql."<br>";
    if($err=pmysql_error()) echo $err."<br>";

    echo "<hr>";
}


echo "<hr>";

echo "End ".date("Y-m-d H:i:s")."<br>";
?>
