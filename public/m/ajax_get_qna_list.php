<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	$productcode    = $_GET['productcode'];
	$pridx			   = $_GET['pridx'];
	$listnum           = 4;
	$page              = $_GET['gotopage'];


	$qna_sql = "SELECT * FROM tblboard WHERE board='qna' AND pridx='".$pridx."'  "; //AND is_secret = '0'
	if ($qnasetup->use_reply != "Y") $qna_sql.= "AND pos = 0 AND depth = 0 ";
	$qna_sql.= "ORDER BY thread,pos";
	$qna_paging = new New_Templet_mobile_paging($qna_sql,10,4,'GoPageAjax2');
	$qna_t_count = $qna_paging->t_count;
	$qna_gotopage = $qna_paging->gotopage;

	$qna_sql = $qna_paging->getSql($qna_sql);
	$qna_result=pmysql_query($qna_sql,get_db_conn());

	$qnaList = array();
	while($row=pmysql_fetch_object($qna_result)) {
		$qnaList[] = $row;
	}
	pmysql_free_result($qna_result);

	//exdebug( $qnaList );
	$htmlResult = '';
	$htmlResult .= '							<a class="btn-write" href="javascript:chkLoginWriteLink();">문의글 작성하기</a>';
	$htmlResult .= '							<p class="tit_txt">상품과 관련된 문의사항이 있으신 분은 게시글을 남겨주시기 바랍니다.</p>';
	$htmlResult .= '							<ul class="board_list_wrap">';

	if( count( $qnaList ) > 0 ) {
		foreach( $qnaList as $rKey=>$rVal ) {
			$qna_date = date( "Y-m-d" , $rVal->writetime);

			list($qnaCount)=pmysql_fetch("SELECT count(num) FROM tblboardcomment WHERE board = 'qna' and parent = '".$rVal->num."'");
			$countStr = "";
			if($qnaCount > 0){
				$a_status	= "답변완료";
			} else {
				$a_status	= "답변대기";
			}

			$qna_reply_sql = "SELECT * FROM tblboardcomment WHERE board = 'qna' and parent = '".$rVal->num."' order by num desc";
			$qna_reply_res = pmysql_query($qna_reply_sql,get_db_conn());

	$htmlResult .= '								<li>
										<div class="qna_write">
											<i>'.setIDEncryp($rVal->mem_id).' ('.$qna_date.')</i>
											<span>'.$a_status.'</span>
										</div>
										<p class="title">'.$rVal->title;
										if($rVal->is_secret == '1') {
										$htmlResult .= '<span><img src="./static/img/icon/icon_lock.png" alt="비밀글"></span>';
										}
										$htmlResult .= '</p>
										<div class="cont_txt">';

										if( $rVal->is_secret == '0' ||  $_ShopInfo->getmemid() == $rVal->mem_id ) {
											$htmlResult .= nl2br($rVal->content);
										} else {
										$htmlResult .= '비밀글입니다.';
										}
										$htmlResult .= '</div>';
										if( $_ShopInfo->getmemid() == $rVal->mem_id ) {
											$htmlResult .= '<div class="buttonset">';
											if($qnaCount == 0){
												$htmlResult .= '<a href="javascript:location.href=\'product_qna_write.php?productcode='.$productcode.'&pridx='.$rVal->pridx.'&qna_num='.$rVal->num.'\'">수정</a>';
											}
											$htmlResult .= '<a href="javascript:PerDel(this,\''.$rVal->num.'\');">삭제</a>
											<input type="hidden" name="modify_passwd" value="'.$rVal->passwd.'" >';
											$htmlResult .= '</div>';
										}
										if( $rVal->is_secret == '0' ||  $_ShopInfo->getmemid() == $rVal->mem_id ) {
										
											while($qna_reply_row = pmysql_fetch_object($qna_reply_res)){
												$qna_reply_date = date( "Y-m-d" , $qna_reply_row->writetime);
										
										$htmlResult .= '<!-- 답변글 노출 -->
										<div class="admin_answer">
											<span class="admin_name">'.$qna_reply_row->name.' ('.$qna_reply_date.')</span>
											<p>'.nl2br($qna_reply_row->comment).'</p>
										</div>
										<!-- // 답변글 노출 -->';
											}
										}
									$htmlResult .= '</li>';

		}
	}
									
								$htmlResult .= '</ul>';

	if( count( $qnaList ) > 0 ) {

								$htmlResult .= '<!-- 페이징 -->
								<div class="list-paginate mt-20">
									'.$qna_paging->a_prev_page.' '.$qna_paging->print_page.' '.$qna_paging->a_next_page.'
								</div>
								<!-- //페이징 -->';

	}

	echo $htmlResult;
?>
