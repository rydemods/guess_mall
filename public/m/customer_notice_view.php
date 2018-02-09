<?php
$subTitle = "고객센터";
include_once('outline/header_m.php');

$num = $_GET['num'];

$sql = "UPDATE tblboard SET access=access+1 WHERE num={$num} and board = 'notice' ";
pmysql_query($sql,get_db_conn());

$qry="select * from tblboard where num={$num} and board = 'notice' ";
$result = pmysql_query($qry);
$row = pmysql_fetch_object($result);

if(strlen($row->vfilename)>0) {
	$file_name1 = '';	//다운로드 링크
	$upload_file1 = '';	//이미지 태그
	$filepath = "../data/shopimages/board/notice/";
	$attachfileurl = $filepath."/".$row->vfilename;
	if(file_exists($attachfileurl)) {
		$file_name1="<a href=\"../lib/download.php?url=".$filepath."&file_name=".urlencode($row->vfilename)."\">".$row->filename."</a>";
		//echo "file = ".$file_name1."<br>";
	}
}
$reg_date = date("Y-m-d", $row->writetime);

// 전 공지제목 & 다음 공지 제목
$tempnext = $num + 1;
$nextsql="select * from tblboard where num={$tempnext} and board = 'notice' ";
$nextresult = pmysql_query($nextsql);
$nextrow = pmysql_fetch_object($nextresult);

$tempprev = $num - 1;
$prevsql="select * from tblboard where num={$tempprev} and board = 'notice' ";
$prevresult = pmysql_query($prevsql);
$prevrow = pmysql_fetch_object($prevresult);
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function ViewNotice(num) {
	location.href="customer_notice_view.php?num="+num;
}

//-->
</SCRIPT>

<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>공지사항</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="photo_type_view sub_bdtop">
		<h4 class="title_area with_brand">
			<span class="brand"><?=$row->title?> </span>
			<span class="date"><?=$reg_date?></span>
		</h4>

		<div class="editor_area2"><!-- [D] 에디터 영역 -->
			<?=$row->content?>
			<?php if($file_name1) {echo "<img src=\"".$file_name1."\" alt=\"첨부파일 이미지\">";}?>
			<!-- <img src="static/img/test/@notice_img01.jpg" alt="공지사항 이미지"> -->
		</div>

		<div class="other_posting">
		<?php 
			if ($prevrow != null) { 
				echo "<dl>";
				echo "<dt>PREV</dt>";
				echo "<dd><a href=\"javascript:ViewNotice('".$tempprev."');\">".$prevrow->title."</a></dd>";
				echo "</dl>";
			}
			
			if ($nextrow != null) {
				echo "<dl>";
				echo "<dt>NEXT</dt>";
				echo "<dd><a href=\"javascript:ViewNotice('".$tempnext."');\">".$nextrow->title."</a></dd>";
				echo "</dl>";
			}
		?>
			<!-- <dl>
				<dt>PREV</dt>
				<dd><a href="#">비오는 날을 좋아하는 당신의 패션</a></dd>
			</dl>
			<dl>
				<dt>NEXT</dt>
				<dd><a href="#">햇볕 좋은 날을 좋아하는 당신의 패션</a></dd>
			</dl> -->
		</div><!-- //.other_posting -->

		<div class="btn_area mt-20">
			<ul class="dib_type ea3">
				<li><a href="customer_notice.php" class="btn-point h-input">목록</a></li>
			</ul>
		</div>

	</section><!-- //.photo_type_view -->

</main>
<!-- //내용 -->

<? include_once('outline/footer_m.php'); ?>