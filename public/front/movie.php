<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging.php");

$brand_code='5';
function issueList($brand_code){
	$issue = "";
	$issue_array = "";
	$product = "";

	$sql = "select * from tblbrand_board where board_code = {$brand_code} ";
	$paging = new Tem001_saveheels_Paging($sql,10,2,'GoPage');
	$sql.= "ORDER BY date DESC ";
	$sql = $paging->getSql($sql);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$issue[]=$row;
	}
	$issue_array[0] = $issue;
	$issue_array[1] = $paging;
	$issue_array[2] = $t_count;
	$issue_array[3] = $gotopage;
	return $issue_array;
}

//리스트 + 페이징 부분 가져오기
$issue_array = issueList($brand_code);
$gotopage = $issue_array[3];
$t_coiunt = $issue_array[2];
$paging = $issue_array[1];
$issue = $issue_array[0];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - MOVIE</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>-->

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<!-- start contents -->
<div class="containerBody sub_skin">
	<h3 class="title">
		MOVIE
		<p class="line_map"><a>홈</a> &gt; <a>BRAND</a> &gt; <span>MOVIE</span></p>
	</h3>

	<div class="movie_wrap">
		<!-- 2015.08.06 수정 S -->
		<div class="movie_top">
			<div class="inner">
				<a href="#" class="link-box">2015 Collection<img src="./img/bg_movie_arr.gif" alt=""></a>
				<ul class="move-view">
					<li><a href="#">2015 Collection</a></li>
					<li><a href="#">2015 Collection</a></li>
				</ul>
			</div>
		</div>
		<!-- 2015.08.06 수정 E -->

		<!--<h4 class="movie_title">후아유 학교2015 김소현 Folly Backpack</h4>
		<div class="movie_area">
			<iframe width="100%" height="480" src="https://www.youtube.com/embed/38XNCtDj6M4" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
		</div>

		<h4 class="movie_title">BASED ON NEWYORK CITY</h4>
		<div class="movie_area">
			<iframe width="100%" height="480" src="https://www.youtube.com/embed/38XNCtDj6M4" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
		</div>-->
		<?foreach($issue as $mKey=>$mVal){?>
			<h4 class="movie_title"><?=$mVal->board_title?></h4>
			<div class="movie_area">
				<?=nl2br($mVal->board_content)?>
			</div>
		<?}?>
	</div>

	<!--<form name=form3 method=get action="/front/productlist.php">
		<div class="paging">
			<a class='on'>1</a>
			<a href="javascript:GoPage(0,2);" onMouseOver="window.status='페이지 : 2';return true">2</a>
			<a href="javascript:GoPage(0,3);" onMouseOver="window.status='페이지 : 3';return true">3</a>		
		</div>
	</form>-->
	<div class="paging">
		<!--
		<a class='on'>1</a>
		<a href="javascript:GoPage(0,2);" onMouseOver="window.status='페이지 : 2';return true">2</a>
		<a href="javascript:GoPage(0,3);" onMouseOver="window.status='페이지 : 3';return true">3</a>	
		-->
		<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
	</div>

</div>
<form name="paging" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="block" value="<?=$block?>"/>
	<input type="hidden" name="gotopage" value="<?=$gotopage?>"/>
</form>
<script type="text/javascript">
function GoPage(block,gotopage) {
    document.paging.block.value = block;
    document.paging.gotopage.value = gotopage;
	document.paging.submit();
}
</script>


<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
</HTML>
