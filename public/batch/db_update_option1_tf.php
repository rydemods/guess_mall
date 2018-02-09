<?php
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

exit;

echo "Start ".date("Y-m-d H:i:s")."<br>";
echo "<hr>";

$sql = "select productcode, option1 From tblproduct where option1 != '' order by pridx";
$result = pmysql_query($sql, get_db_conn());
echo "sql = ".$sql."<br>";

$i = 0;
while($row = pmysql_fetch_array($result)) {

    $i++;
    $opt1_tf = "";
    if( ($i % 10) == 0) echo $i."<br>";

    $opt1_tf_tmp = explode("@#", $row[option1]);
    for($i=0; $i < count($opt1_tf_tmp); $i++) {
        $opt1_tf .= "T"."@#";
    }
    $opt1_tf = substr($opt1_tf, 0, -2);

    echo "productcode = ".$row[productcode]." / opt1_tf = ".$opt1_tf."<br>";
    
    $qry = "Update tblproduct Set option1_tf = '".$opt1_tf."' Where productcode = '".$row[productcode]."'";
    pmysql_query($qry, get_db_conn());
    if($err=pmysql_error()) {
        echo "sql = ".$qry."<br>";
        echo $err."<br>";
        exit;
    }
    echo "<hr>";
}


echo "End ".date("Y-m-d H:i:s")."<br>";
?>
