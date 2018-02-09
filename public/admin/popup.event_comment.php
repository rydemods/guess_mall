<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$no = $_GET['no'];

if ( empty($no) ) {
    alert("aaa");
    exit;
}

include_once("../lib/adminlib.php");
include_once("../conf/config.php");

$listnum = 10;

$sql  = "SELECT count(*) FROM tblboardcomment_promo ";
$sql .= "WHERE parent = {$no} ";

$paging = new newPaging($sql, 10, $listnum);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql  = "SELECT * FROM tblboardcomment_promo ";
$sql .= "WHERE parent = {$no} ";
$sql .= "ORDER BY num desc ";

$sql  = $paging->getSql($sql);

$result = pmysql_query($sql);

$listHtml = "";
$num = ( ($gotopage - 1) * $listnum ) + 1;
while ( $row = pmysql_fetch_object($result) ) {
    $delete_link = "popup.event_comment_delete.php?no=" . $row->num . "&p_no=" . $no;

    $listHtml .= '<tr>';
    $listHtml .= '<!--td><input type="checkbox" name="idx[]" /></td-->';
    $listHtml .= '<td style="border:1px gray solid;">' . $num . '</td>';
    $listHtml .= '<td style="border:1px gray solid;">' . $row->c_mem_id . '</td>';
    $listHtml .= '<td style="border:1px gray solid; text-align:left; margin-left:5px;">' . nl2br($row->comment) . '</td>';
    $listHtml .= '<td style="border:1px gray solid;">' . date("Y-m-d H:i:s",$row->writetime) . '</td>';
    $listHtml .= '<td style="border:1px gray solid;"><a href="javascript:if(confirm(\'삭제 하시겠습니까?\')) location.href = \'' . $delete_link . '\'"><img src="./images/i_del.gif"/></a></td>';
    $listHtml .= '</tr>';

    $num++;
}
pmysql_free_result($result);

?>

<link rel="stylesheet" href="style.css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function CheckForm(form, no) {
    form.mode.value = "download";
    form.no.value = no;
    form.submit();
}

$(document).ready(function(){
	$(".CLS_allCheck").click(function(){
		if($(this).prop("checked")){
			$("input[name='productcode']").prop("checked", true);
		}else{
			$("input[name='productcode']").prop("checked", false);
		}
	})
})

</script>
<!-- 라인맵 -->
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td>
			<!-- 테이블스타일01 -->
			<div class="table_style01 pt_20">
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
					<colgroup>
						<col width="35" />
						<col width="" />
						<col width="35" />
					</colgroup>
					<tr>
						<th><span>이벤트 댓글 보기</span></th>
						<td>이벤트페이지에 등록된 댓글을 관리할 수 있습니다.</td>
                        <td><img src="images/btn_filedown.gif" id="downloadButton" border="0" style="cursor:hand" onclick="CheckForm(document.exceldown, '<?=$no?>');"></td>
					</tr>
				</table>
			</div>

			<div class="table_style02">
				<table width=100% cellpadding=1 cellspacing=1 border=0 style="border-collapse:collapse; border:1px gray solid;">
					<colgroup>
						<!--col width="35" /-->
						<col width="35" />
						<col width="80" />
						<col width="" />
						<col width="130" />
						<col width="35" />
					</colgroup>
					<tr>
						<!--th><input type = 'checkbox' class = 'CLS_allCheck'></th-->
						<th>No</th>
						<th>아이디</th>
						<th>내용</th>
						<th>날짜</th>
						<th>삭제</th>
					</tr>
                    <?=$listHtml?>
				</table>
			</div>

			<?
				//페이징
				echo '
                    <div id="page_navi01" style="height:40px;">
					    <div class="page_navi">
						    <ul>';
                echo $a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
                echo '
                            </ul>
                        </div>
                    </div>';
			?>
		</td>
	</tr>
	</table>

<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=get> 
    <input type=hidden name=no value="<?=$no?>"> 
    <input type=hidden name=block value="<?=$block?>"> 
    <input type=hidden name=gotopage value="<?=$gotopage?>"> 
</form>

<form name="exceldown" action="/admin/event_comment_excel_download.php" method=post>
    <input type="hidden" name="mode" />
    <input type="hidden" name="no" />
</form>

