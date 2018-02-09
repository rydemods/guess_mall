<?php 

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko" >

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
	<meta name="description" content="XNGOLF" />
	<meta name="keywords" content="" />

	<title>엑스넬스 코리아</title>

	<link rel="stylesheet" href="../css/c_xngolf.css" />
	<script type="text/javascript" src="../css/select_type01.js" ></script>
	<script type="text/javascript" src="../css/jquery-1.10.1.js" ></script>
</head>	

<div class="main_wrap">
		
<?php include "../outline/header.php"; ?>


	<div class="sub_top_wrap sub_pos_local"></div>
	<div class="container960 mb_50">
		<ul class="tap_ea2">
			<li><a href="location01.php"><img src="../img/common/map_tap01_off.gif" alt="본사 오시는 길" /></a></li>
			<li><a href="location02.php"><img src="../img/common/map_tap02_on.gif" alt="직영점 오시는 길" /></a></li>
		</ul>
		<img src="../img/common/location02.jpg" alt="직영점" />
	</div>


<?php include "../outline/footer.php"; ?>

</div>

</html>