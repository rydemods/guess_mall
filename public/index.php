<?php
$Dir="./";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

// if(isdev()) {
//     //echo "<script>alert('�ܺ� ������ �������� ������ index.htm �� �������ϴ�.');</script>";
//     header("Location:index.htm");
// } else {
//     header("Location:http://test-hott.ajashop.co.kr");
// }
$url = "index.htm?".$_SERVER["QUERY_STRING"];
header("Location:$url");
?>
