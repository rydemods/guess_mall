<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	$productcode    = $_GET['productcode'];
	$listnum           = 4;
	$page              = $_GET['gotopage'];


	// 리뷰 작성 가능 리스트 조회
	$sql  = "SELECT tblResult.ordercode, tblResult.idx ";
	$sql .= "FROM ";
	$sql .= "   ( ";
	$sql .= "       SELECT a.*, b.regdt  ";
	$sql .= "       FROM tblorderproduct a LEFT JOIN tblorderinfo b ON a.ordercode = b.ordercode ";
	$sql .= "       WHERE a.productcode = '" . $productcode . "' AND b.id = '" . $_ShopInfo->getMemid()  . "' and ( (b.oi_step1 = 3 AND b.oi_step2 = 0) OR (b.oi_step1 = 4 AND b.oi_step2 = 0) ) ";
	$sql .= "       ORDER BY a.idx DESC ";
	$sql .= "   ) AS tblResult LEFT ";
	$sql .= "   OUTER JOIN tblproductreview tpr ON tblResult.productcode = tpr.productcode and tblResult.ordercode = tpr.ordercode and tblResult.idx = tpr.productorder_idx ";
	$sql .= "WHERE tpr.productcode is null ";
	$sql .= "ORDER BY tblResult.idx asc ";
	$sql .= "LIMIT 1 ";

	$result = pmysql_query($sql);
	list($review_ordercode, $review_order_idx) = pmysql_fetch($sql);
	pmysql_free_result($result);

	$qry = "WHERE a.productcode='{$productcode}' ";
	$sql = "SELECT COUNT(*) as t_count, SUM(a.marks) as totmarks FROM tblproductreview a ";
	$sql.= $qry;
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$t_count_review = (int)$row->t_count;
	$totmarks = (int)$row->totmarks;
	$marks=@ceil($totmarks/$t_count_review);
	pmysql_free_result($result);
	$paging = new New_Templet_mobile_paging($t_count_review,10,4,'GoPageAjax');
	$gotopage = $paging->gotopage;

	# 리뷰 리스트를 불러온다
	//$reviewlist = 'Y';
	$sql  = "SELECT a.*, b.productname FROM tblproductreview a LEFT JOIN tblproduct b ON a.productcode = b.productcode ";
	$sql .= "{$qry} ORDER BY a.date DESC, a.num DESC ";

	$sql = $paging->getSql($sql);
	$result=pmysql_query($sql,get_db_conn());
	$j=0;
	$reviewList = array();
	while($row=pmysql_fetch_object($result)) {
		
		$reviewComment = array();

		$reviewList[$j]['idx'] = $row->num;
		$reviewList[$j]['num'] = $row->num;
		$reviewList[$j]['number'] = ($t_count_review-($setup['list_num'] * ($gotopage-1))-$j);
		$reviewList[$j]['id'] = $row->id;
		$reviewList[$j]['name'] = $row->name;
		$reviewList[$j]['subject'] = $row->subject;
		$reviewList[$j]['productcode'] = $row->productcode;
		$reviewList[$j]['productname'] = $row->productname;
		$reviewList[$j]['ordercode'] = $row->ordercode;
		$reviewList[$j]['productorder_idx'] = $row->productorder_idx;
		$reviewList[$j]['marks'] = $row->quality + 3;
		$reviewList[$j]['hit'] = $row->hit;
		$reviewList[$j]['type'] = $row->type;
		$reviewList[$j]['size'] = $row->size;
		$reviewList[$j]['foot_width'] = $row->foot_width;
		$reviewList[$j]['color'] = $row->color;
		$reviewList[$j]['quality'] = $row->quality;

	    // 별표시하기
		$reviewList[$j]['marks_width'] = ($row->quality +3) * 20;;
		$reviewList[$j]['marks_sp'] = $row->quality + 3;
		
		$reviewList[$j]['best_type'] = $row->best_type;

		$reviewList[$j]['upfile'] = $row->upfile;       // 첨부파일1
		$reviewList[$j]['upfile2'] = $row->upfile2;     // 첨부파일2
		$reviewList[$j]['upfile3'] = $row->upfile3;     // 첨부파일3
		$reviewList[$j]['upfile4'] = $row->upfile4;     // 첨부파일4
		$reviewList[$j]['upfile5'] = $row->upfile5;     // 첨부파일5

		$reviewList[$j]['up_rfile'] = $row->up_rfile;   // 첨부파일1(실제 업로드한 파일명)
		$reviewList[$j]['up_rfile2'] = $row->up_rfile2; // 첨부파일2(실제 업로드한 파일명)
		$reviewList[$j]['up_rfile3'] = $row->up_rfile3; // 첨부파일3(실제 업로드한 파일명)
		$reviewList[$j]['up_rfile4'] = $row->up_rfile4; // 첨부파일4(실제 업로드한 파일명)
		$reviewList[$j]['up_rfile5'] = $row->up_rfile5; // 첨부파일5(실제 업로드한 파일명)
		
		//exdebug($reviewList);
		$reviewList[$j]['date'] = substr($row->date,0,4).".".substr($row->date,4,2).".".substr($row->date,6,2);
		$reviewList[$j]['date'].= '&nbsp;'.substr($row->date,8,2).":".substr($row->date,10,2).":".substr($row->date,12,2);
		$reviewList[$j]['content'] = explode("=",$row->content);

		# 코멘트 가져오기
		$comment_sql  = "SELECT no, id, name, content, regdt, pnum ";
		$comment_sql .= "FROM tblproductreview_comment ";
		$comment_sql .= "WHERE pnum = '".$row->num."' ";
		$comment_sql .= "ORDER BY no desc ";

		$comment_res = pmysql_query( $comment_sql, get_db_conn() );
		while( $comment_row = pmysql_fetch_object( $comment_res ) ){
			$reviewComment[] = $comment_row;
		}
		pmysql_free_result( $comment_res );
		$reviewList[$j]['comment'] = $reviewComment;
		$j++;
	}
	pmysql_free_result($result);

	//exdebug( $_SERVER );

	$htmlResult = '';
	$htmlResult .= '<ul class="board_list_wrap">';
	if( count( $reviewList ) > 0 ) {
		foreach( $reviewList as $rKey=>$rVal ) { 
			$number = ( $paging->t_count - ( $setup['list_num'] * ( $gotopage - 1 ) ) - $rKey );
// 			$ico_photo	= '';
// 			if( $rVal['type'] == "1" ) {
// 				$ico_photo	= '<img class="ico-photo" src="'.$Dir.'/m/static/img/icon/ico_review_photo.png" alt="사진첨부">';
// 			}
			$htmlResult .= '
									<input type="hidden" class="CLS_reviewsize_idx" value="'.$rVal['num'].'" />
									<input type="hidden" id="reviewSizeDetail_'.$rVal['num'] .'" value="'.$rVal['size'] .'" />
									<input type="hidden" id="reviewFootWidthDetail_'.$rVal['num'] .'" value="'.$rVal['foot_width'].'" />
									<input type="hidden" id="reviewColorDetail_'.$rVal['num'] .'" value="'.$rVal['color'].'" />
									<input type="hidden" id="reviewQualityDetail_'.$rVal['num'].'" value="'.$rVal['quality'].'" />
                    				<li>
										<div>
											<span class="comp-star star-score"><strong style="width:'.$rVal['marks_width'].'%;">5점만점에 '.$rVal['marks_sp'].'점</strong></span>
                        					<i>'.setIDEncryp($rVal['id']).'('.substr($rVal['date'],0,4)."-".substr($rVal['date'],4,2)."-".substr($rVal['date'],6,2).')</i>
										</div	
                        				<p class="title" ids="'.$rVal['idx'].'">'.$rVal['subject'].'</p>	
                        				<div class="cont_txt">';
			$htmlResult .= nl2br($rVal['content'][0]) . "<br>";
			if ( !empty($rVal['upfile']) ) {$htmlResult .= '<br><img src="'.$Dir.DataDir.'shopimages/review/'. $rVal['upfile'] .'" />'; }
			if ( !empty($rVal['upfile2']) ) {$htmlResult .= '<br><img src="'.$Dir.DataDir.'shopimages/review/'. $rVal['upfile2'] .'" />'; }
			if ( !empty($rVal['upfile3']) ) {$htmlResult .= '<br><img src="'.$Dir.DataDir.'shopimages/review/'. $rVal['upfile3'] .'" />'; } 							
			if ( !empty($rVal['upfile4']) ) {$htmlResult .= '<br><img src="'.$Dir.DataDir.'shopimages/review/'. $rVal['upfile4'] .'" />'; }   							
			if ( !empty($rVal['upfile5']) ) {$htmlResult .= '<br><img src="'.$Dir.DataDir.'shopimages/review/'. $rVal['upfile5'] .'" />'; } 							
			$htmlResult .= '</div>';
			if ( $_ShopInfo->getMemid() == $rVal['id'] ) {
				$htmlResult .= '<div class="buttonset">
						<a href="javascript:;" onclick="javascript:send_review_write_page(
							\'' . $rVal['productcode'] . '\', 
							\'' . $rVal['ordercode'] . '\', 
							\'' . $rVal['productorder_idx'] . '\', 
							\'' . $rVal['num'] . '\');">수정</a>
						<a href="javascript:;" onclick="javascript:delete_review(\''. $rVal['num'].'\');">삭제</a>
						</div>';
			}
			$htmlResult .= '<div class="answer_area">
										<form onsubmit="return false;">
										<input type="hidden" name="pnum" value="'.$rVal['idx'].'">
										<input type="hidden" name="mem_id" value="'.$_ShopInfo->getMemid().'">
										<input type="hidden" name="now_date" value="'.date("Y.m.d").'">
										<input type="hidden" name="return" value="OK">
										<div><input type="text" name="review_comment" style="width:100%;"></div>';
			if(strlen($_ShopInfo->getMemid())==0) { 
				$htmlResult .= '<span><a class="btn_answer btn-type1" href="javascript:;" onClick="javascript:goLogin();">입력</a></span';
			}else{
				$htmlResult .= '<span><a class="btn_answer btn-type1" href="javascript:;" onClick="javascript:review_comment_write(this);">입력</a></span>';
			}
			$htmlResult .= '		</form>
									</div>';
			if( count( $rVal['comment'] ) > 0 ){
				$htmlResult .= '<div class="admin_answer_list" id="reply_comment_'.$rVal['idx'].'">';
				foreach( $rVal['comment'] as $commentKey=>$commentVal ){
					$htmlResult .= '<div class="admin_answer">';
					$htmlResult .= '<span class="admin_name">' . setIDEncryp($commentVal->id) . ' (' . substr($commentVal->regdt,0,4)."-".substr($commentVal->regdt,4,2)."-".substr($commentVal->regdt,6,2) . ')</span>';
					$htmlResult .= '<p>' .$commentVal->content.'</p>';
					if ( $commentVal->id == $_ShopInfo->getMemid() ) {
						$htmlResult .= ' <div class="buttonset"><a class="btn-delete" href="javascript:;" onClick="javascript:delete_review_comment(this);" ids="' . $commentVal->no . '" ids2="' . $commentVal->pnum . '">삭제</a></div>';
					}
					$htmlResult .= '</div>';
					$htmlResult .= '<div class="btn-feeling mb-10 ml-20" id="'.$commentVal->no.'" >
												<a class="btn-good-feeling" href="javascript:select_feeling(\''.$commentVal->no.'\',\'product_review_comment\',\'good\',\''.$_ShopInfo->getMemid().'\');" id="feeling_good_comment_'.$commentVal->no.'">'.totalFeeling($commentVal->no, 'product_review_comment', 'good').'</a>
												<a class="btn-bad-feeling" href="javascript:select_feeling(\''.$commentVal->no.'\',\'product_review_comment\',\'bad\',\''.$_ShopInfo->getMemid().'\');" id="feeling_bad_comment_'. $commentVal->no.'">'.totalFeeling($commentVal->no, 'product_review_comment', 'bad').'</a>
											</div>';
				}
			
				$htmlResult .= '</div>';
			
			}
			$htmlResult .= '</li>';
			
		} // reviewList foreach
	}  // reviewList else
	$htmlResult .= '
                </ul>
                <div class="list-paginate mt-20">
                        '.$paging->a_prev_page.' '.$paging->print_page.' '.$paging->a_next_page.'
                </div>';



echo $htmlResult;
?>
