<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<?php include ($Dir.MainDir.$_data->menu_type.".php") ?>
<?php
$left_name=end(explode('_',$setup['board_skin']));
//echo "left_name = ".$left_name."<br>";
?>

<!-- [D] 20160821 퍼블리싱 공지사항 추가 -->
<div id="contents">
	<div class="inner">
		<main class="mypage_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->

        <!-- 고객센터 LNB -->
        <?	$lnb_flag = 5;
        include ($Dir.MainDir."lnb.php");
        ?>
        <!-- // 고객센터 LNB -->