<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if($_SERVER["QUERY_STRING"]!=""){
$url = "index.htm?".$_SERVER["QUERY_STRING"];
} else {
$url = "index.htm";
}
if(isdev()) {
    echo "<script>alert('외부 접근을 막기위해 별도로 index.htm 을 만들었습니다.');</script>";
    header("Location:$url");
} else {
    //header("Location:http://hot-t.co.kr");
	header("Location:$url");
}
?>
