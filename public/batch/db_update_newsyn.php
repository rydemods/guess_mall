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
**/
echo "Start 2"."<br>";
echo "<hr>";
// 2.
$sql = "
        select m2 id, m22 sms_tf, m23 new_tf,
            (case when m22='T' and m23='T' then 'Y' 
                when m22!='T' and m23='T' then 'M'
                when m22='T' and m23!='T' then 'S'
                else 'N' end ) as news_yn 
        from deconc_member 
        order by m2
        ";
$result = pmysql_query($sql, get_db_conn());
echo "sql = ".$sql."<br>";
echo "<hr>";

while($row = pmysql_fetch_object($result)) {
    echo "id = ".$row->id."<br>";
    echo "sms_tf = ".$row->sms_tf."<br>";
    echo "new_tf = ".$row->new_tf."<br>";
    echo "news_yn = ".$row->news_yn."<br>";

    $sql = "update tblmember set 
            news_yn = '".$row->news_yn."' 
            Where   id = '".$row->id."' 
            ";
    pmysql_query($sql, get_db_conn());
    echo "sql = ".$sql."<br>";
    if($err=pmysql_error()) echo $err."<br>";

    echo "<hr>";
}


echo "<hr>";

echo "End ".date("Y-m-d H:i:s")."<br>";
?>
