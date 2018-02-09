<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once("../lib/file.class.php");

$mode=$_POST["mode"];
$idx = $_POST["idx"] ? $_POST["idx"] : $_GET["idx"];

$view_sql ="SELECT * FROM tblpersonal WHERE idx = '".$idx."'";
$result = pmysql_query($view_sql, get_db_conn());
$row = pmysql_fetch_object($result);
$data = $row;

$reply_sql = "SELECT * FROM tblpersonal WHERE parent = '".$idx."'";
$reply_result = pmysql_query($reply_sql, get_db_conn());
$reply_row = pmysql_fetch_object($reply_result);
$reply_data = $reply_row;

if($mode == "delete"){
	#파일삭제

	$filepath = $Dir.DataDir."shopimages/personal/";
	$up_file = new FILE($filepath);
	if ($data->up_filename !="") $up_file->removeFile($data->up_filename);
	#데이터삭제
	$dSql = "DELETE FROM tblpersonal WHERE idx = '".$idx."'";
	pmysql_query($dSql, get_db_conn());

	if(!pmysql_error()){
		echo  "<script>alert('삭제가 완료되었습니다..'); location.href=\"/front/mypage_personal.php\"</script>";
	}else{
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	}
}

?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="inner">
		<main class="mypage_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->

			<article class="mypage_content">
				<section class="mypage_main">
					<div class="title_box_border">
						<h3>1:1 문의</h3>
					</div>
					<!-- 게시판 쓰기/수정 -->
					<div class="myboard">
					<form name='qnaview_form' id='qnaview_form' action="<?=$_SERVER['PHP_SELF']?>" method='POST' enctype="multipart/form-data">
						<input type='hidden' id='mode' name='mode'>
						<input type='hidden' id='idx' name='idx'>
						<table class="th_left border_none">
							<caption>1:1 문의 작성</caption>
							<colgroup>
								<col style="width:160px">
								<col style="width:323px">
								<col style="width:160px">
								<col style="width:auto">
							</colgroup>
							<tbody>
								<tr>
									<th>상담유형</th>
									<td><?=$arrayCustomerHeadTitle[$data->head_title] ?></td>
									<th>작성일자</th>
									<td><?=date("Y-m-d",strtotime($data->date))?></td>
								</tr>
								<tr>
									<th>제목</th>
									<td colspan="3"><?=$data->subject ?></td>
								</tr>
								<tr>
									<th>문의내용</th>
									<td colspan="3">
										<div class="content">
											<p><?=nl2br($data->content) ?> </p>
										</div>
									</td>
								</tr>
								<tr>
									<th>답변내용</th>
									<td colspan="3">
										<div class="content">
											<p><?=nl2br($data->re_content) ?></p>
										</div>
									</td>
								</tr>
								<tr>
									<th>휴대폰 답변</th>
									<td><?=addMobile($data->HP) ?></td>
									<th>이메일 답변</th>
									<td><?=$data->email ?></td>
								</tr>
								<tr>
									<th>첨부</th>
									<td colspan="3">
									<?if ($data->up_filename) { ?>
									<div class="under_line btn-file-view"><a href="javascript:;" class=""><?=$data->ori_filename ?></a></div>
									<?} ?>
									</td>
								</tr>
							</tbody>
						</table>
						</form>
						<?if($data->re_id  == "") {?>
						<div class="btn_wrap_bottom mt-10 ta-r">
							<a href="../front/mypage_personalwrite.php?mode=modify&idx=<?=$data->idx ?>" class="btn-line">수정</a>
							<a href="javascript:row_delete(<?=$data->idx ?>);" class="btn-line">삭제</a>
						</div>
						<?} ?>
						<div class="btn_wrap mt-20"><a href="../front/mypage_personal.php" class="btn-type1">확인</a></div>
					</div>
					<!-- // 게시판 쓰기/수정 -->
				</section>
			</article>
		</main>
	</div>
</div><!-- //#contents -->
<!-- 첨부파일 상세 팝업 -->
<div class="layer-dimm-wrap pop-file-view"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner w500">
		<h3 class="layer-title">첨부파일 상세</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<?echo "<img src='".$Dir.DataDir."shopimages/personal/".$data->up_filename."' style='max-width:430px;'>"; ?>
		</div>
	</div>
</div>
<!-- //첨부파일 상세 팝업 -->
<script Language="JavaScript">
function row_delete(idx){
	if( confirm('삭제하시겠습니까?') ){
		$("#mode").val("delete");
		$("#idx").val(idx);
		$("#qnaview_form").submit();
	}else{
		return;
	}
}
</script>

<?php  include ($Dir."lib/bottom.php") ?>
