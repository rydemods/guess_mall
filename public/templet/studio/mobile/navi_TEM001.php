<?php 

$naviTitle = "";
$naviLink = $_SERVER['PHP_SELF'];

if ( $basename == "studio.php" || $basename == "lookbook_list.php" || $basename == "lookbook_view.php" || $basename == "mainStudio.html" ) {
    $naviTitle = "LOOKBOOK";
} elseif ( $basename == "play_the_star_view.php" || $basename == "play_the_star_detail.php" ) {
    $naviTitle = "PLAY THE STAR";
} elseif ( $basename == "press.php" ) {
    $naviTitle = "PRESS";
} elseif ( $basename == "want_star.php" ) {
    $naviTitle = "스타가되고싶니";
} elseif ( $basename == "sns.php" ) {
    $naviTitle = "SNS";
}
?>

<div class="sub-title">
    <h2><?=$naviTitle?></h2>
    <a class="btn-prev" href="javascript:history.go(-1);"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
    <div class="js-sub-menu">
        <button class="js-btn-toggle" title="펼쳐보기"><img src="./static/img/btn/btn_arrow_down.png" alt="메뉴"></button>
        <div class="js-menu-content">
            <ul>
                <li><a href="press.php">PRESS</a></li>
				<li><a href="want_star.php">스타가 되고싶니</a></li>
                <li><a href="play_the_star_view.php">PLAY THE STAR</a></li>
				<li><a href="studio.php">LOOKBOOK</a></li>
                <li><a href="sns.php">SNS</a></li>
            </ul>
        </div>
    </div>
</div>
