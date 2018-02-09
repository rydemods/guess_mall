<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>

<?php
include ($Dir.MainDir.$_data->menu_type.".php");
/* lnb 호출 */
/*
$page_code = "default";
$lnb_flag = 1;
include ($Dir.MainDir."lnb.php"); 
*/
?>
<div id="contents" >
	<div class="containerBody" >
		

<style type="text/css">
.studio-tab-move-wrap {margin-top:5px;border-top:1px solid #dcdcdc; border-bottom:2px solid #aaa;  position:relative; height:26px;}
	.studio-tab-move-wrap span {display:; position:absolute; top:0; left:0px; width:0px; height:26px; background:#efefef; border-right:1px solid #e5e5e5; border-left:1px solid #e5e5e5;box-sizing:border-box;}
.studio-tab-move {text-align:center; height:26px;}
	.studio-tab-move li {position:relative; display:inline-block; width:200px; height:26px; margin-left:-4px;}
	.studio-tab-move li:nth-of-type(1) {width:140px;}
	.studio-tab-move li:nth-of-type(2) {width:160px;}
	.studio-tab-move li:nth-of-type(3) {width:105px;}
	.studio-tab-move li:nth-of-type(4) {width:155px;}
	.studio-tab-move li:nth-of-type(5) {width:95px;}
	.studio-tab-move li a {display:block; position:absolute; top:0; left:0; width:100%; line-height:26px; color:#9e9e9e; }
	.studio-tab-move li a:hover,
	.studio-tab-move li.on a {color:#6a6a6a; }
</style>

<script type="text/javascript">
$(function(){
	var studio_tabBg = $('.studio-tab-move-wrap span');
	var tabBg_local1 = $('.studio-tab-move li:nth-of-type(1).on');
	
	class_move();

	$('.studio-tab-move li:nth-of-type(1)').mouseenter(function(){
		studio_tabBg.animate({left : 252, width:140},200);
	});

	$('.studio-tab-move li:nth-of-type(2)').mouseenter(function(){
		studio_tabBg.animate({left : 392, width:160},200);
	});

	$('.studio-tab-move li:nth-of-type(3)').mouseenter(function(){
		studio_tabBg.animate({left : 553, width:105},200);
	});

	$('.studio-tab-move li:nth-of-type(4)').mouseenter(function(){
		studio_tabBg.animate({left : 658, width:155},200);
	});

	$('.studio-tab-move li:nth-of-type(5)').mouseenter(function(){
		studio_tabBg.animate({left : 813, width:95},200);
	});

	
});

function class_move(){
	var move_index = 0;

	$('.studio-tab-move li').each( function( i, obj ) {
		if( $(this).attr('class') == 'on' ) move_index = i;
	});

	switch( move_index ){
		case 0:
				$('.studio-tab-move-wrap span').css( 'left', '252px' ).css( 'width', '140px' );
			break;
		case 1:
				$('.studio-tab-move-wrap span').css( 'left', '392px' ).css( 'width', '160px' );
			break;
		case 2:
				$('.studio-tab-move-wrap span').css( 'left', '553px' ).css( 'width', '105px' );
			break;
		case 3:
				$('.studio-tab-move-wrap span').css( 'left', '658px' ).css( 'width', '155px' );
			break;
		case 4:
				$('.studio-tab-move-wrap span').css( 'left', '813px' ).css( 'width', '95px' );
			break;
		default :
			break;
	}

}
</script>




<div class="studio-tab-move-wrap">
	<span></span>
	<ul class="studio-tab-move">
		<li class="on"><a href="lookbook_list.php">LOOKBOOK</a></li>
		<li><a href="play_the_star_view.php">PLAY THE STAR</a></li>
		<li><a href="press.php">PRESS</a></li>
		<li><a href="want_star.php">스타가되고싶니</a></li>
		<li><a href="sns.php">SNS</a></li>
	</ul>
</div>


	</div>
</div>

<?php
include ($Dir."lib/bottom.php") 
?>

</BODY>
</HTML>
 