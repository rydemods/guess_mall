<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$num=$_REQUEST["num"];
$mode = $_POST['mode'];
//리뷰 삭제
if($mode=="review_delete"){
	$review_num = $_POST["review_num"];
	// 등록 메세지
	$msg = '';
	if( strlen( $_ShopInfo->getMemid() ) > 0 ){	
		$rSql = "DELETE FROM tblproductreview WHERE num ='".$review_num."' AND id='".$_ShopInfo->getMemid()."' ";
		if( pmysql_query( $rSql, get_db_conn() ) ){
			$pr_sql = "UPDATE tblproduct SET review_cnt = review_cnt - 1 WHERE productcode ='".$productcode."'";
			pmysql_query( $pr_sql, get_db_conn() );
			$msg = '리뷰가 삭제되었습니다.';
		} else {
			$msg = '리뷰 삭제를 실패했습니다.';
		}
	} else {
		$msg = '리뷰 삭제를 실패했습니다.';
	}
	alert_go($msg,$Dir.FrontDir."mypage_review.php");
}

//리뷰 수정
if($mode=="review_modify"){
	$review_num = $_POST["review_num"];
	$rname=$_POST["rname"];
	$rcontent=$_POST["rcontent"];
	$rsubject = $_POST["rsubject"];
	$rmarks=$_POST["rmarks"];

	// 리뷰 에러체크
	$reviewChk = 0;
	// 등록 메세지

	$msg = '';
	if(ord($review_filter)) {	//사용후기 내용 필터링
		if(ReviewFilter($review_filter,$rcontent,$findFilter)) {
			//alert_go("사용하실 수 없는 단어를 입력하셨습니다.({$findFilter})\\n\\n다시 입력하시기 바랍니다.",-1);
			$msg = '사용하실 수 없는 단어를 입력하셨습니다. ( '.$findFilter.' ) \\n\\n다시 입력하시기 바랍니다. ';
			$reviewChk++;
		}
	}
	$rqry = 'num='.$review_num;
	if( $reviewChk == 0 && strlen( $_ShopInfo->getMemid() ) > 0 ) {
		$sql = "UPDATE tblproductreview SET ";
		$sql.= "subject = '".$rsubject."', ";
		$sql.= "content = '".$rcontent."', ";
		$sql.= "marks = '".$rmarks."' ";
		$sql.= "WHERE num ='".$review_num."' AND id='".$_ShopInfo->getMemid()."' ";
		if( pmysql_query( $sql, get_db_conn() ) ){
			$msg = "수정 되었습니다.";
		} else {
			$msg = '리뷰 수정을 실패했습니다.';
		}
	}
	
	alert_go($msg,"{$_SERVER["PHP_SELF"]}?{$rqry}");
}


$sql = "SELECT * FROM tblproductreview WHERE id='".$_ShopInfo->getMemid()."' AND num='{$num}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_pdata=$row;
	$date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."(".substr($row->date,8,2).":".substr($row->date,10,2).")";
} else {
	alert_go('해당 문의내역이 없습니다.','c');
}

pmysql_free_result($result);
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}

function ModifyReview(){
	if( confirm('리뷰를 수정하시겠습니까?') ){
		$('#rmarks_up').val( $('#rmarks_m').val() );
		$('#rsubject_up').val( $('#rsubject_m').val() );
		$('#rcontent_up').val( $('#rcontent_m').val() );

		if(!$('#rsubject_up').val() ) {
			alert("사용후기 제목을 입력하세요.");
			return;
		}

		if( !$('#rcontent_up').val() ) {
			alert("사용후기 내용을 입력하세요.");
			return;
		}
		$('#reviewMode').val("review_modify");
		$('#reviewform').submit();	
	}
}

function reviewDelete(){
	if( confirm('리뷰를 삭제하시겠습니까?') ){
		$('#reviewMode').val("review_delete");
		$('#reviewform').submit();
	}
}

$(document).on( 'click', '.rModify_pop', function(){
	reviewModify_pop();
});

//-->
</SCRIPT>


<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<tr>
	<td align="center">
	<div class="containerBody sub_skin">

	<!-- LNB -->
	
	<?	include ($Dir.FrontDir."mypage_TEM01_left.php");?>
	
	<!-- #LNB -->
	<div class="right_section">
		<h3 class="title mb_20">
			상품평
			<p class="line_map"><img src="../img/icon/home.gif" alt="" /> <a href="">홈</a> &gt; <a>나의메모</a>  &gt;  <a href="">상품평</a></p>
		</h3>
	
		<div class="customer_inquiry_wrap">
			<table class="write_table" summary="">
				<colgroup>
					<col style="width:121px" />
					<col style="width:auto" />
				</colgroup>
				<tbody>
					<tr height="40">
						<th scope="row">상품명</th>
						<td>
						<? $sql2 = "SELECT productname FROM tblproduct WHERE productcode='".$_pdata->productcode."'";
							$result2=pmysql_query($sql2,get_db_conn());
							$row2=pmysql_fetch_object($result2);
							echo $row2->productname;
							?>
						</td>
					</tr>
					<tr height="40">
						<th scope="row">제목</th>
						<td>
							<?=$_pdata->subject?>
						</td>
					</tr>
					<tr height="40">
						<th scope="row">날짜</th>
						<td>						
							<?=$date?>
						</td>
					</tr>
					<tr height="40">
						<th scope="row">평점</th>
						<td class="">				
							<?$colorStar = "";
							for($i=0;$i<$_pdata->marks;$i++) {
								$colorStar .= "★";
							}
							$noColorStar = "";
							for($i=$_pdata->marks;$i<5;$i++) {
								$noColorStar .= "★";
							}
						?>
						<div class="star_color ml_5"><?=$colorStar?><span><?=$noColorStar?></span></div>
						</td>
					</tr>
					<tr height="40">
						<th scope="row">내용</th>
						<td>
							<? if($_pdata->upfile){ ?>
								<img src="<?=$Dir.DataDir."shopimages/board/reviewbbs/".$_pdata->upfile ?>">
							<? } ?><br />
							<?=nl2br($_pdata->content)?>
						</td>
					</tr>
				</tbody>
			</table>	
			<div class="ta_c mt_10">
				<a href="javascript:history.back();" class="btn_D">목록</a>
				<a href="javascript:;" class="btn_D rModify_pop">수정</a>
				<a href="javascript:reviewDelete();" class="btn_D">삭제</a>
			</div>
		</div>
	</div>
	</div>
	</td>	
</tr>
</table>

<form name='reviewform' id = 'reviewform' method='POST' action="<?=$_SERVER['PHP_SELF']?>" >
	<input type='hidden' name='mode' id='reviewMode'>
	<input type="hidden" name="rsubject" id="rsubject_up" >
	<input type="hidden" name='rmarks' id='rmarks_up' >
	<input type="hidden" name="rcontent" id="rcontent_up" >
	<input type='hidden' name='review_num' id='review_num' value='<?=$_pdata->num?>' >
</form>

<!-- 구매평 수정하기 -->
<div class="revie_modify_pop"  >
	<h3 class="tit_review_pop">구매평 수정하기 <a href="javascript:;" onclick="reviewClose('revie_modify_pop','all_body')" class="btn_rpop_close"><img src="../images/content/btn_popup_close.gif" alt="닫기" ></a></h3>
	<div class="board_block">
		<div class="tbl_pop">
			<table>
			<caption>
			구매평 
			</caption>
			<colgroup>
			<col style="width:90px">
			<col >
			</colgroup>
			<tr>
				<th>평가</th>
				<td>
					<select class="star_select" id='rmarks_m' >
					<option value='5' <?if($_pdata->marks == '5'){echo'SELECTED';}?> >★★★★★</option>
					<option value='4' <?if($_pdata->marks == '4'){echo'SELECTED';}?> >★★★★☆</option>
					<option value='3' <?if($_pdata->marks == '3'){echo'SELECTED';}?> >★★★☆☆</option>
					<option value='2' <?if($_pdata->marks == '2'){echo'SELECTED';}?> >★★☆☆☆</option>
					<option value='1' <?if($_pdata->marks == '1'){echo'SELECTED';}?> >★☆☆☆☆</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>제목</th>
				<td><input type="text" id="rsubject_m" title="제목 입력" value='<?=$_pdata->subject?>' ></td>
			</tr>
			<tr>
				<th>상품평</th>
				<td>
					<textarea id="rcontent_m" title="내용입력" ><?=nl2br($_pdata->content)?></textarea><br>
					<p>구매후기에 적합하지 않은 내용은 통보없이 비공개 될 수 있습니다.</p>
				</td>
			</tr>
			</table>
		</div>
		<div class="btn_group_c">
			<a href="javascript:ModifyReview();" class="btn_black">상품평 수정하기</a>
		</div>
	</div>
</div>
<!-- //구매평 수정하기 -->		
<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
		