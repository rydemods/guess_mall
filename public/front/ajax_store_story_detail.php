<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");

$mem_id		= $_ShopInfo->getMemid();
$imagepath	= $Dir.DataDir."shopimages/store_story/";

$view_type		= $_POST["view_type"]?$_POST["view_type"]:'';
$detail_type	= $_POST["detail_type"];

$sno				= $_POST["sno"];
$block			= $_POST['block'];
$gotopage		= $_POST['gotopage'];
$view_type		= $_POST['view_type']?$_POST['view_type']:'';
if ($view_type	=='m') {
	$list_num		= 5;
} else {
	$list_num		= 5;
}

$storyHtml					= "";
$storyCommentHtml	= "";

$sc_sql = "SELECT * FROM tblstorestory_comment ";
$sc_sql .= "WHERE sno='{$sno}' ";
$sc_sql .= "order by cno desc";
$sc_paging = new New_Templet_mobile_paging($sc_sql, 2, $list_num, 'GoPageAjax', true);
$sc_t_count = $sc_paging->t_count;
$gotopage = $sc_paging->gotopage;

$sc_sql = $sc_paging->getSql($sc_sql);
$sc_result = pmysql_query($sc_sql,get_db_conn());
if ($sc_t_count > 0 ) {
	while( $sc_row = pmysql_fetch_object($sc_result) ) {

		$storyCommentHtml .= '
					<dl class="clear">
						<dt>'.setEmailEncryp($sc_row->mem_id).'</dt>
						<dd>'.$sc_row->comment;
		if ($mem_id == $sc_row->mem_id) {
			$storyCommentHtml .= '
						&nbsp;&nbsp;<a href="javascript:;" class="btn-delete" onClick="javascript:commentDel(\''.$sc_row->cno.'\');"><img src="../static/img/btn/close.png" alt="삭제"></a>';
		}
		$storyCommentHtml .= '
						</dd>
					</dl>';
	}
	$storyCommentHtml .= '
					<div class="list-paginate mt-50">
					'.$sc_paging->a_prev_page.$sc_paging->print_page.$sc_paging->a_next_page.'
					</div>';
}
pmysql_free_result( $sc_result );

if ($detail_type == 'comment') {
	echo $storyCommentHtml;
	exit;
}

$storyCommentDivHtml	= '';
$storyCommentDivHtml .= '
			<div class="reply-list">
			'.$storyCommentHtml.'
			</div>';

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
$storyHtml .= '
		<div class="img-area">
			<img src="'.$story_img.'" alt="" onload="popupImgResize();">
		</div>
		<div class="cont-area">
			<div class="title">
				<h3>@'.$storyRow['store_name'].'<span class="pl-10">';
if ($mem_id == $storyRow['mem_id']) {
	$storyHtml .= '
				<a href="store_story_write.php?sno='.$storyRow['sno'].'" class="btn-type1">수정</a>';
}
$storyHtml .= '
				</span></h3>
				<button class="like_s'.$storyRow['sno'].' comp-like btn-like'.($storyRow['section']?' on':'').'" onclick="detailSaveLike(\''.$storyRow['sno'].'\',\''.($storyRow['section']?' on':'off').'\',\'storestory\',\''.$mem_id.'\',\'\')" title="'.($storyRow['section']?'선택됨':'선택 안됨').'"><span  class="like_scount_'.$storyRow['sno'].'"><strong>좋아요</strong>'.number_format($storyRow['hott_cnt']).'</span></button>
			</div>
			<div class="cont-view">
				<div class="inner">
					<p>'.$storyRow['title'].'</p>
					<p class="name">'.setEmailEncryp($storyRow['mem_id']).' | '.$reg_date.'</p>
					'.$storyRow_content.'
				</div>
				'.$storyCommentDivHtml.'
			</div>';
if (strlen($mem_id) == 0) {
	$storyHtml .= '
			<div class="reply-form">
				<input type="text" id="story_comment" name="story_comment" placeholder="좋아요 또는 댓글을 남기려면 로그인을 해주세요" title="">
				<a href="javascript:;" onClick="javascript:commentSubmit(\'\');" class="btn-type1 c1">로그인</a>
			</div>';
} else {
	$storyHtml .= '
			<div class="reply-form">
			<form name="commentForm">
				<input type="text" id="comment" name="comment"  placeholder="" title="" onkeydown="return captureReturnKey(event)">
				<a href="javascript:;" onClick="javascript:commentSubmit(\''.$storyRow['sno'].'\');" class="btn-type1 c1">남기기</a>
			</form>
			</div>';
}
$storyHtml .= '
		</div> <!-- // .cont-area -->
		<div class="btn-wrap">
			<a href="javascript:;" onClick="javascript:move_detail(\'prev\');" class="view-prev">이전</a>
			<a href="javascript:;" onClick="javascript:move_detail(\'next\');" class="view-next">다음</a>
		</div>';
pmysql_free_result( $storyResult );

echo $storyHtml;