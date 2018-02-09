<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<?

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

#####좌측 메뉴 class='on' 을 위한 페이지코드
//$page_code='csfaq';
$board = "notice";
$class_on['notice'] = " class='on'";
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
function ViewNotice(num) {
	location.href="customer_notice_view.php?num="+num;
}

//-->
</SCRIPT>

<!-- 20170328 퍼블리싱 신규추가 -->
<div id="contents">
	<div class="cs-page">

		<h2 class="page-title">공지사항</h2>

		<div class="inner-align page-frm clear">

		<?	
			$lnb_flag = 5;
            include ($Dir.MainDir."lnb.php");
        ?>
			<article class="cs-content">
				
				<section>
					<header class="my-title">
						<h3 class="fz-0 v-hidden">공지사항 상세보기</h3>
					</header>
					
					<div class="board-view">
						<div class="title clear">
							<strong><?=$row->title?></strong>
							<span class="date"><?=$reg_date?></span>
						</div>
				<? if ($file_name1) { ?>
						첨부파일 : <?=$file_name1?>
				<? } ?>
						<div class="editor-output">
							<p></p>
							<?=$row->content?>							
						</div>
					</div>

					<div class="prev-next clear">
					<? if ($prevrow != null) { ?>
						<div class="prev clear"><span class="mr-20">PREV</span><a href="javascript:ViewNotice('<?=$tempprev?>')"><?=$prevrow->title?></a></div>
					<? } ?>
					<? if ($nextrow != null) { ?>
						<div class="next clear"><span class="ml-20">NEXT</span><a href="javascript:ViewNotice('<?=$tempnext?>')"><?=$nextrow->title?></a></div>
					<? } ?>
					</div>
					<!-- <div class="ta-c mt-40"><a href="../front/customer_notice.php" class="btn-point h-large w200">목록</a></div> -->
					<!-- <div class="ta-c mt-40"><a href="javascript:history.back();" class="btn-point h-large w200">목록</a></div> -->
					<div class="ta-c mt-40"><a href="../front/customer_notice.php" class="btn-point h-large w200">목록</a></div>
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
