<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_main.php");
include_once($Dir."lib/timesale.class.php");
include_once($Dir."conf/config.php");
Header("Pragma: no-cache");
include_once($Dir."lib/shopdata.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");
include_once($Dir."lib/product.class.php");


$board = $_REQUEST['board']; //처음 링크 붙힐때 get으로 보드이름 공지사항으로 줘야될듯
$boardname_sql = "SELECT board_name FROM tblboardadmin WHERE board='{$board}'";
$boardname_res = pmysql_query($boardname_sql);
$boardname_row = pmysql_fetch_object($boardname_res);
pmysql_free_result($boardname_res);
$subTitle = $boardname_row->board_name;

include_once('outline/header_m.php');
?>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">


<main id="content" class="subpage">

<article class="mypage">
   <ul class="mypage_gnb">
	<li class="on">공지사항</li>
	<li><a href="csfaq.php">FAQ</a></li>
<?php if($_data->personal_ok=="Y")	:	?>
	<li><a href="mypage_personal.php">1:1문의</a></li>
<?php endif;	?>
   </ul>
 <section class="customer">
 <h3>공지사항</h3>

<table class="mypage_tb">
<colgroup>
<col width="10%" />
<col width="*" />
<col width="20%" />
</colgroup>
  <tr>
    <th scope="col">번호</th>
    <th scope="col">제목</th>
    <th scope="col">작성일</th>
  </tr>
		<?php
				$sql = "SELECT COUNT(*) as t_count FROM tblnotice ";
				$sql = "SELECT COUNT(*) as t_count FROM tblboard ";
				$sql.= "WHERE board='{$board}' ";
				$paging = new Paging($sql,5,10); // 쿼리,page_num,list_num
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;

				$sql = "SELECT num, writetime as date, title as subject, content FROM tblboard ";
				$sql.= "WHERE board='{$board}' ";
				$sql.= "ORDER BY date DESC ";
				$sql = $paging->getSql($sql);
				$result=pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

					$date = date("Y-m-d",$row->date);
					$re_date="-";

		?>
					<tr>
						<td><?=$number?></td>
						<td>&nbsp;<A HREF="javascript:ViewNotice('<?=$row->num?>','<?=$paging->block?>','<?=$paging->gotopage?>','<?=$board?>') "><?=strip_tags($row->subject)?></A></td>
						<td><?=$date?></td>
					</tr>
		<?php
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
		?>
					<tr height="30"><td colspan="5" align="center">공지사항이 없습니다.</td></tr>
		<?php
				}
		?>
</table>

<div class="paginate">
<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
<!--
<a href="#" class="pre">이전</a>
<?=$paging->t_count?>
<a href="#" class="next">다음</a>
-->
</div>
<form name=idxform method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="board" value="notice" />
<input type="hidden" name="block" />
<input type="hidden" name="gotopage" />
</form>
</section>

</article>
</div>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
function ViewNotice(boardnum,block,gotopage,board) {
	if(!block){
		var block = 0;
	}
	location.href="board_view.php?board="+board+"&boardnum="+boardnum+"&block="+block+"&gotopage="+gotopage;
}

</SCRIPT>
</main>

<? include_once('outline/footer_m.php'); ?>