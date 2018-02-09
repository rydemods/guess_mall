<?php include_once('outline/header_m.php'); ?>
<?
$mem_id		= $_MShopInfo->getMemid();
$imagepath	= $Dir.DataDir."shopimages/store_story/";

$sno				= $_GET["sno"];

$storySql = "SELECT s.*, st.name as store_name, h.section,
								COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'storestory' AND s.sno::varchar = tl.hott_code),0) AS hott_cnt,
								COALESCE((select COUNT( sc.cno )AS sc_cnt from tblstorestory_comment sc WHERE sc.sno = s.sno),0) AS sc_cnt ";
$storySql .= "FROM tblstorestory s LEFT JOIN tblstore st ON s.store_code=st.store_code LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'storestory' AND like_id = '".$mem_id."' GROUP BY hott_code, section ) h ON s.sno::varchar = h.hott_code ";
$storySql .= "WHERE s.sno='{$sno}' ";

$storyResult	= pmysql_query($storySql,get_db_conn());
$storyRow = pmysql_fetch_array($storyResult);

$story_img = getProductImage($imagepath,$storyRow['filename']);
$reg_date = substr($storyRow['regdt'], 0,4).'.'.substr($storyRow['regdt'], 4,2).'.'.substr($storyRow['regdt'], 6,2).' '.substr($storyRow['regdt'], 8,2).':'.substr($storyRow['regdt'], 10,2);

$storyRow_content = stripslashes($storyRow['content']);

// <br>태그 제거
$arrList = array("/<br\/>/", "/<br>/");
$storyRow_content_tmp = trim(preg_replace($arrList, "", $storyRow_content));

if ( !empty($storyRow_content_tmp) ) {
		//$storyRow_content	= str_replace(" ","&nbsp;",nl2br($storyRow_content));
		$storyRow_content	= str_replace("<p>","<div>",$storyRow_content);
		$storyRow_content	= str_replace("</p>","</div>",$storyRow_content);
}
?>

<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>STORE STORY</span>
		<a href="<?=$Dir.FrontDir?>" class="home"></a>
	</h2>
</section>

<div class="store-story-wrap view">
	<div class="cont-img"><img src="<?=$story_img?>" alt=""></div>
	<div class="name">
		<span class="store-name">@<?=$storyRow['store_name']?><?if ($mem_id == $storyRow['mem_id']) {?><a href="<?=$Dir.MDir?>store_story_write.php?sno=<?=$storyRow['sno']?>" class="btn-point">수정</a><?}?></span>
		<button class="like_s<?=$storyRow['sno']?> comp-like btn-like<?=$storyRow['section']?' on':''?>" onclick="detailSaveLike('<?=$storyRow['sno']?>','<?=$storyRow['section']?' on':'off'?>','storestory','<?=$mem_id?>','')" title="<?=$storyRow['section']?'선택됨':'선택 안됨'?>"><span  class="like_scount_<?=$storyRow['sno']?>"><strong>좋아요</strong><?=number_format($storyRow['hott_cnt'])?></span></button>
	</div>
	<div class="cont-txt">
		<p><?=$storyRow['title']?></p>
		<p class="s-name"><?=setEmailEncryp($storyRow['mem_id'])?> | <?=$reg_date?></p>
		<?=$storyRow_content?>
	</div>
	<div class="reply-list">
<?if (strlen($mem_id) == 0) {?>
		<div class="box_search clear">
			<div class="input_search">
				<input type="search" name="story_comment" id="story_comment" placeholder="좋아요 또는 댓글을 남기려면 로그인을 해주세요">
			</div>
			<button type="button" class="btn-point" onClick="javascript:commentSubmit(\'\');">로그인</button>
		</div>
<?} else {?>
		<div class="box_search clear">
			<form name="commentForm">
			<div class="input_search">
				<input type="search" name="comment" id="comment" onkeydown="return captureReturnKey(event)">
			</div>
			<button type="button" class="btn-point" onClick="javascript:commentSubmit('<?=$storyRow['sno']?>');">남기기</button>
			</form>
		</div>
<?}?>
	</div>
</div><!-- //.store-story-wrap -->

<script type="text/javascript">
var gBlock			= 0;
var gGotopage	= 1;
var now_sno		= '<?=$sno?>';
function commentSubmit(sno) {
<?if (strlen($mem_id) == 0) {?>
	document.location.href="<?=$Dir.FrontDir?>login.php?chUrl=<?=getUrl()?>";
<?} else {?>
	var comment	= $("form[name=commentForm]").find("input[name=comment]").val();
	if (comment == '') {
		alert('댓글을 입력해 주세요.');
		$("form[name=commentForm]").find("input[name=comment]").focus();
		return;
	}
	$.ajax({
		url : '<?=$Dir.FrontDir?>store_story_comment_proc.php',
		type: "POST",
		data: {
			mode : 'write', sno : sno, comment : comment
		},
		async: false,
		cache: false,
	}).success(function(data){
		if( data === "SUCCESS" ) {
			$("form[name=commentForm]").find("input[name=comment]").val("");
			GoPageAjax(0, 1);
			alert("등록되었습니다.");
		} else {
			var arrTmp = data.split("||");
			if ( arrTmp[0] === "FAIL" ) {
				alert(arrTmp[1]);
			} else {
				alert("등록에 실패하였습니다.");
			}
		}
	}).error(function(){
		alert("다시 시도해 주십시오.");
	});
<?}?>
}
function commentDel(cno) {
	$.ajax({
		url : '<?=$Dir.FrontDir?>store_story_comment_proc.php',
		type: "POST",
		data: {
			mode : 'delete', cno : cno
		},
		async: false,
		cache: false,
	}).success(function(data){
		if( data === "SUCCESS" ) {
			GoPageAjax(gBlock, gGotopage);
			alert("삭제되었습니다.");
		} else {
			var arrTmp = data.split("||");
			if ( arrTmp[0] === "FAIL" ) {
				alert(arrTmp[1]);
			} else {
				alert("삭제에 실패하였습니다.");
			}
		}
	}).error(function(){
		alert("다시 시도해 주십시오.");
	});
}

function GoPageAjax(block, gotopage) {
	gBlock = block;
	gGotopage = gotopage;
	var sno	= now_sno;
	$.ajax({
		type: "POST",
		url: "<?=$Dir.FrontDir?>ajax_store_story_detail.php",
		data: { detail_type : 'comment', sno : sno, block : block, gotopage : gotopage, view_type : 'm' },
		dataType : "html",
		async: false,
		cache: false,
		error:function(request,status,error){
			//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	}).done(function(data){
		$(".store-story-wrap").find(".reply-list").find('dl').remove();
		$(".store-story-wrap").find(".reply-list").find('.list-paginate').remove();
		$(".store-story-wrap").find(".reply-list").prepend(data);
	});
}
GoPageAjax(gBlock, gGotopage);
</script>


<? include_once('outline/footer_m.php'); ?>
