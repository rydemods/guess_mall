<?php
$subTitle = "고객센터";
include_once('outline/header_m.php');

$page_num       = $_POST[page_num];
$search_name       = $_POST['search_name'];

$sql = "SELECT  *
        FROM    tblboard a
        WHERE   1=1
        AND     a.board = 'notice'
        AND     a.notice='0'
        AND     a.deleted='0'
        AND     a.pos = 0
        AND     a.depth = 0
        ";
if($search_name == null){
	$sql .= "ORDER BY a.thread, a.pos";
} else {
	$sql .= "AND		a.title like '%{$search_name}%'
	ORDER BY a.thread, a.pos";
}

$paging = new New_Templet_paging($sql, 5,  5, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$ret = pmysql_query($sql,get_db_conn());
//exdebug($sql);

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

function SearchKeyWord (){
	document.form1.submit();
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
		<div class="breadcrumb">
			<ul class="depth2">
				<li>
					<a href="javascript:;">고객센터</a>
					<ul class="depth3">
						<li><a href="<?=$Dir.MDir?>customer_notice.php">공지사항</a></li>
						<li><a href="<?=$Dir.MDir?>customer_faq.php">FAQ</a></li>
						<li><a href="<?=$Dir.MDir?>mypage_personal.php">1:1문의</a></li>
						<li><a href="<?=$Dir.MDir?>storeList.php">매장안내</a></li>
						<li><a href="<?=$Dir.MDir?>contactUs.php">입점문의</a></li>
						<li><a href="<?=$Dir.MDir?>customer_grade.php">멤버쉽안내</a></li>
						<li><a href="<?=$Dir.MDir?>customer_cs.php">A/S안내</a></li>
					</ul>
				</li>
			</ul>
			<div class="dimm_bg"></div>
		</div>
	</section><!-- //.page_local -->

	<section class="cs_notice sub_bdtop">
		<form name="form1" action="customer_notice.php" method="POST">
		<div class="board_search">
			<div class="input_addr">
				<input type="text" name="search_name">
				<div class="btn_addr"><a href="javascript:SearchKeyWord();" class="btn-point h-input">검색</a></div>
			</div>
		</div><!-- //.board_search -->
		</form>
		<table class="th-top">
			<colgroup>
				<col style="width:auto;">
				<col style="width:22.35%;">
			</colgroup>
			<thead>
				<tr>
					<th>제목</th>
					<th>등록일</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$cnt=0;
			if ($t_count > 0) {	
				while($row = pmysql_fetch_object($ret)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					$reg_date = date("Y-m-d", $row->writetime);
					echo "<tr>\n";
					echo "<td><a href=\"javascript:ViewNotice('{$row->num}');\" class=\"subject\">{$row->title}</a></td>\n";
					echo "<td><span class=\"brightest\">{$reg_date}</span></td>\n";
					echo "</tr>\n";
					
				}
			} else {
				echo "<tr>";
				echo "<td colspan=\"2\" class=\"none\">검색결과가 없습니다.</td>";
				echo "</tr>";
			}		
			?>
				<!-- [D] 게시물이 없는 경우 -->
				<!-- <tr>
					<td colspan="2" class="none">검색결과가 없습니다.</td>
				</tr>
				<tr>
					<td colspan="2" class="none">등록된 게시물이 없습니다.</td>
				</tr> -->
				<!-- //[D] 게시물이 없는 경우 -->
			</tbody>
		</table><!-- //.th-top -->

		<div class="list-paginate mt-20">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div>

	</section><!-- //.cs_notice -->

</main>
<!-- //내용 -->

<? include_once('outline/footer_m.php'); ?>