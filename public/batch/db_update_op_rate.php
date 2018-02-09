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
/*
$sql = "
        select op.*, vi.rate as v_rate 
        from tblorderproduct op
        join tblvenderinfo vi on op.vender = vi.vender 
        where op.rate = 0 
        and vi.rate > 0
        order by ordercode desc
        ";
*/
$sql = "
        select op.*, vi.rate as v_rate 
        from tblorderproduct op
        join tblvenderinfo vi on op.vender = vi.vender 
        where op.rate != vi.rate 
        order by ordercode desc
        ";
$result = pmysql_query($sql, get_db_conn());
echo "sql = ".$sql."<br>";
echo "<hr>";

$i=1;
while($row = pmysql_fetch_object($result)) {
    echo "i = ".$i."<br>";
    echo "ordercode = ".$row->ordercode."<br>";
    echo "productcode = ".$row->productcode."<br>";
    echo "idx = ".$row->idx."<br>";
    echo "vender = ".$row->vender."<br>";
    echo "v_rate = ".$row->v_rate."<br>";
    /*
    $sql = "update tblorderproduct set 
            rate = '".$row->v_rate."' 
            Where   rate = 0  
            ";
    */
    $sql = "update tblorderproduct set 
            rate = '".$row->v_rate."' 
            Where   idx = ".$row->idx."
            ";
    pmysql_query($sql, get_db_conn());
    echo "sql = ".$sql."<br>";
    if($err=pmysql_error()) echo $err."<br>";

    echo "<hr>";
    $i++;
}


echo "<hr>";

echo "End ".date("Y-m-d H:i:s")."<br>";
?>
