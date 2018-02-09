<?php 
header("Content-Type: text/plain");
header("Content-Type: text/html; charset=euc-kr");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");


//상품QNA 게시판 존재여부 확인 및 설정정보 확인
$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
if(ord($prqnaboard)) {
	$sql = "SELECT * FROM tblboardadmin WHERE board='{$prqnaboard}' ";
	$result=pmysql_query($sql,get_db_conn());
	$qnasetup=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($qnasetup->use_hidden=="Y") $qnasetup=null;
}

$pridx = $_GET['pridx'];

$colspan=4;
if($qnasetup->datedisplay!="N") $colspan=5;

$sql = "SELECT COUNT(*) as t_count FROM tblboard WHERE board='{$qnasetup->board}' AND pridx='{$pridx}' "; //AND is_secret = '0'
if ($qnasetup->use_reply != "Y") {
	$sql.= "AND pos = 0 AND depth = 0 ";
}
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$t_count = (int)$row->t_count;
pmysql_free_result($result);
$qnapaging = new amg_Paging($t_count,10,5,'GoPageAjaxQna');
$qnagotopage = $qnapaging->gotopage;
$pagecount = (($t_count - 1) / $qnasetup->list_num) + 1;

$isgrantview=false;
if($qnasetup->grant_view=="N") {
	$isgrantview=true;
} else if($setup['grant_view']=="U") {
	if(strlen($_ShopInfo->getMemid())>0) {
		$isgrantview=true;
	}
}


$imgdir=$Dir.BoardDir."images/skin/".$qnasetup->board_skin;
$sql = "SELECT * FROM tblboard WHERE board='{$qnasetup->board}' AND pridx='{$pridx}'  "; //AND is_secret = '0' 
if ($qnasetup->use_reply != "Y") $sql.= "AND pos = 0 AND depth = 0 ";
$sql.= "ORDER BY thread,pos";

$sql = $qnapaging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());

$j=0;
$loopQna = array();
while($row=pmysql_fetch_array($result)) {
	$replyFlag = '';
	$number = ($t_count-( 5 * ($qnagotopage-1))-$j);
	$subject='';
	/*
	if ($row['deleted']!="1") {
		if($isgrantview) {
			if($row['is_secret']!="1") {
				$subject = "<a href=\"javascript:view_qnacontent('{$j}')\">";
			} else {
				$subject = "<a href=\"javascript:view_qnacontent('S')\">";
			}
		} else {
			$subject = "<a href=\"javascript:view_qnacontent('N')\">";
		}
	} else {
		$subject = "<a href=\"javascript:view_qnacontent('D')\">";
	}
	$depth = $row['depth'];
	if($qnasetup->title_length>0) {
		$len_title = $qnasetup->title_length;
	}
	$wid = 1;
	if ($depth > 0) {
		if ($depth == 1) {
			$wid = 6;
		} else {
			$wid = (6 * $depth) + (4 * ($depth-1));
		}
		$subject .= "<img src=\"{$imgdir}/x.gif\" width=\"{$wid}\" height=\"2\" border=\"0\">";
		$replyFlag = 'reply';
		if ($len_title) {
			$len_title = $len_title - (3 * $depth);
		}
	}
	$title = $row['title'];
	if ($len_title) {
		$title = titleCut($len_title,$title);
	}
	$subject .=  $title;
	if ($row['deleted']!="1") {
		$subject .= "</a>";
	}
	*/

	$title = $row['title'];
	$subject =  $title;
	$reg_date = date("Y.m.d H:i:s", $row['writetime']);

	$row['number'] = $number;
	$row['subject'] = $subject;
	$row['reg_date'] = $reg_date;
	$row['reply_flag'] = $replyFlag;
	$row['content'] = nl2br($row['content']);
	$row['file_length'] = strlen( $row['vfilename'] );
	$row['file'] = $row['vfilename'];

	$loopQna[] = $row;
	$j++;
}
pmysql_free_result($result);

?>

	<table class="board bbs-qna">
		<caption>상품과 관련된 문의사항이 있으신 분은 게시글을 남겨주시기 바랍니다.</caption>
			<colgroup>
				<col style="width:auto">
				<col style="width:20%">
				<col style="width:10%">
			</colgroup>
<?php 
if( count( $loopQna ) > 0  ) {
	foreach( $loopQna as $qKey=>$qVal ) {?>
		<tbody>
			<tr class="btn-toggle">
				<td class="title" id="<?=$qVal['number']?>"><a href="javascript:;" ><?=$qVal['title']?></a>
				<?if( $qVal['is_secret'] == '1' ){?>
					<i><img src="../static/img/icon/icon_lock.png" alt="비밀글"></i>
				<?} // is_secret if?>
				</td>
				<td class="date<?=$qVal['number']?>" id="<?=$qVal['mem_id'] ?>"><?=setIDEncryp($qVal['mem_id'])?><br>(<?=$qVal['reg_date']?>)</td>

				<?if($qVal['total_comment'] >= 1 ) {?>
				<td class="reply">답변완료</td>
				<?}else{ ?>
				<td class="reply"><span class="txt_type">답변대기</span></td>
				<?} ?>
			</tr>

			<tr class="data-secret" data-secret='<?=$qVal['is_secret']?>' >
<?php
		if( $qVal['is_secret'] == '0' || $_ShopInfo->getmemid() == $qVal['mem_id'] || $_ShopInfo->getId() ){
?>
				<td colspan="3">
					<div class="content">
						<p><?=nl2br( $qVal['content'] )?></p>
						<div class="buttonset">
						<?if( $_ShopInfo->getmemid() == $qVal['mem_id'] ) {?>
							<?if($qVal['total_comment'] == 0){?>
							<a href="javascript:ModifyQna('<?=$qVal['num']?>','<?=$qVal['title']?>','<?=$qVal['content']?>','<?=$qVal['is_secret']?>','<?=$qVal['passwd']?>', '<?=$qVal['hp']?>','<?=$qVal['email']?>','<?=$qVal['sms_send']?>','<?=$qVal['email_send']?>');" name="modifyQna" id="modifyQna"  idx='<?=$qVal['num']?>'>수정</a>
							<?} ?>
							<a href='javascript:DeleteAjaxQna(this ,"<?=$qVal['num']?>");'>삭제</a>
							<input type='hidden' name='modify_subject' value='<?=$qVal['title']?>' >
							<input type='hidden' name='modify_memo' value='<?=$qVal['content']?>' >
							<input type='hidden' name='modify_secret' value='<?=$qVal['is_secret']?>' >
							<input type='hidden' name='modify_passwd' value='<?=$qVal['passwd']?>' >
							<input type='hidden' name='chk_sms' value="0">
							<input type='hidden' name='chk_mail' value="0">
							<input type='hidden' name='data-member' value='<?=$_ShopInfo->getmemid()?>' >
						<?} // memid if?>
						</div>
					</div>
					<?
					$qna_reply_sql = "SELECT name, writetime, comment FROM tblboardcomment WHERE board = 'qna' and parent = '".$qVal['num']."' order by num desc";
					$qna_reply_res = pmysql_query($qna_reply_sql,get_db_conn());
					while( $qna_reply_row = pmysql_fetch_object( $qna_reply_res ) ) {
					?>
					<div class="answer">
						<span class="name"><?=$qna_reply_row->name?> (<?=date( "Y.m.d H:i:s" , $qna_reply_row->writetime)?>)</span>
						<p><?=date( "Y.m.d H:i:s" , $qna_reply_row->writetime)?></p>
					</div>
					<?} // qna_reply_row while
						pmysql_free_result( $qna_reply_res );
					?>
				</td>
<?php
		} // is_secret || mem_id if
?>
			</tr>
		</tbody>
<?php
	} // loopQna foreach

} else{
?>
		<tbody>
			<tr><td colspan='3'><p class="none">등록된 질문이 없습니다</p></td></tr>
		</tbody>
<?php

}// loopQna else
?>
	</table>

	<!-- 페이징 -->
	<div class="list-paginate mt-20">
		<?=$qnapaging->a_prev_page.' '.$qnapaging->print_page.' '.$qnapaging->a_next_page?>
	</div>
	<!-- // 페이징 -->
