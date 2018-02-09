<?

$arrClassOn = array();
$naviTitle = "";
$naviLink = $_SERVER['PHP_SELF'];

if ( $_SERVER['PHP_SELF'] == "/front/studio.php" || $_SERVER['PHP_SELF'] == "/front/lookbook_list.php" || $_SERVER['PHP_SELF'] == "/front/lookbook_view.php" ) {
    $arrClassOn[0] = "on";
    $naviTitle = "LOOKBOOK";

    // LOOKBOOK 상세페이지에서는 링크를 리스트 페이지로
    if ( $_SERVER['PHP_SELF'] == "/front/lookbook_view.php" ) {
        $naviLink = "/front/lookbook_list.php";
    }   
} elseif ( $_SERVER['PHP_SELF'] == "/front/play_the_star_view.php" || $_SERVER['PHP_SELF'] == "/front/play_the_star_detail.php" ) {
    $arrClassOn[1] = "on";
    $naviTitle = "PLAY THE STAR";
} elseif ( $_SERVER['PHP_SELF'] == "/front/press.php" ) {
    $arrClassOn[2] = "on";
    $naviTitle = "PRESS";
} elseif ( $_SERVER['PHP_SELF'] == "/front/want_star.php" ) {
    $arrClassOn[3] = "on";
    $naviTitle = "스타가되고싶니";
} elseif ( $_SERVER['PHP_SELF'] == "/front/sns.php" ) {
    $arrClassOn[4] = "on";
    $naviTitle = "SNS";
}

?>
<script type="text/javascript">
	$(document).ready(function(){
		var defaultStyleLeft = $(".studio-tab-move").prev('span').css('left');
		var defaultStyleWidth = $(".studio-tab-move").prev('span').css('width');
		$(".studio-tab-move-wrap").mouseleave(function(){
			$('.studio-tab-move-wrap span').animate({left : defaultStyleLeft, width:defaultStyleWidth},200);
		})
	});
</script>
<div class="breadcrumb studio-top">
    <ul>
        <li><a href="#">HOME</a></li>
        <li><a href="/front/studio.php">STUDIO</a></li>
        <li class="on"><a href="<?=$naviLink?>"><?=$naviTitle?></a></li>
    </ul>
</div><!-- //.breadcrumb -->


<div class="studio-tab-move-wrap">
    <span></span>
    <ul class="studio-tab-move">
        <li class="<?=$arrClassOn[0]?>"><a href="lookbook_list.php">LOOKBOOK</a></li>
        <li class="<?=$arrClassOn[1]?>"><a href="play_the_star_view.php">PLAY THE STAR</a></li>
        <li class="<?=$arrClassOn[2]?>"><a href="press.php">PRESS</a></li>
        <li class="<?=$arrClassOn[3]?>"><a href="want_star.php">스타가되고싶니</a></li>
        <li class="<?=$arrClassOn[4]?>"><a href="sns.php">SNS</a></li>
    </ul>
</div>
