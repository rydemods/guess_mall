<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include_once("../lib/adminlib.php");
include_once("../conf/config.php");
####################### 페이지 접근권한 check ###############
$PageCode = "ma-1";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

if($_GET[block]){
	$block = $_GET[block];
}
if($_GET[gotopage]){
	$gotopage = $_GET[gotopage];
}
$arrayCounselType = array("p"=>"전화", "m"=>"메일", "h"=>"기타");

?>
<link rel="stylesheet" href="style.css">
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype='multipart/form-data'>
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed;background:#FFFFFF;">
		<col width="40"><col width="1"><col width="100"><col width="1"><col width="80"><col width="1"><col width="407"><col width="1"><col width="80">
<?php
			$colspan=5;
			$sql = "SELECT COUNT(*) as t_count FROM tblmember_question WHERE id = '".$_GET[id]."' ";
			$paging = new Paging($sql, 10, 3);
			$t_count = $paging->t_count;	
			$gotopage = $paging->gotopage;				

			$sql = "SELECT * FROM tblmember_question WHERE id = '".$_GET[id]."' ORDER BY regdt DESC ";
			$sql = $paging->getSql($sql);
			$result = pmysql_query($sql,get_db_conn());
			$cnt=0;
			//No	상담일	처리자	내용	상담수단
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

				$conv_regdt =  $row->regdt;
				$conv_counsel_id =  $row->counsel_id;
				$conv_contents =  $row->contents;
				$conv_id =  $row->id;
				$conv_counsel_type =  $arrayCounselType[$row->counsel_type];
				
				echo "<TR class = 'viewData'>\n";
				echo "	<TD align = 'center' style = 'border:0px;'>".$number."</TD>\n";
				echo "	<TD style = 'border:0px;'><img src='./img/common/item_line1.gif'></TD>\n";
				echo "	<TD align = 'center' style = 'border:0px;'>".$conv_regdt."</TD>\n";
				echo "	<TD style = 'border:0px;'><img src='./img/common/item_line1.gif'></TD>\n";
				echo "	<TD align = 'center' style = 'border:0px;'>".$conv_counsel_id."</TD>\n";
				echo "	<TD style = 'border:0px;'><img src='./img/common/item_line1.gif'></TD>\n";
				echo "	<TD style = 'border:0px;padding-left:10px;'>
								".strcut_utf8($conv_contents, 40, True, '')."
								<a href=\"javascript:popupJquery('./member_question_reg.php?id=".$conv_id."&sno=".$row->sno."', 370, 316);\">
									<img src = './images/btn_cate_modify.gif' align = 'absmiddle' width = '30'>
								</a>
							</TD>\n";
				echo "	<TD style = 'border:0px;'><img src='./img/common/item_line1.gif'></TD>\n";
				echo "	<TD align = 'center' style = 'border:0px;'>".$conv_counsel_type."</TD>\n";
				echo "</TR>\n";
				echo "<TR class = 'hideData'>\n";
				echo "	<TD colspan = '9' style = 'padding-left:10px;'>".nl2br($conv_contents)."</TD>\n";
				echo "</TR>\n";
				$cnt++;
			}
			pmysql_free_result($result);

			if ($cnt==0) {
				//echo "<tr><td class=td_con2 colspan=9 align=center>".iconv("euc-kr", "utf-8", "검색된 정보가 존재하지 않습니다.")."</td></tr>";
                echo "<tr><td class=td_con2 colspan=9 align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
			}
?>
		<tr>
			<td colspan = '9' style = 'border:0px;border-top:1px solid #B9B9B9;'>
				<table cellpadding="0" cellspacing="0" width="100%" style = 'border:0px;'>
					<tr>
						<td align=center class="font_size" style = 'border:0px;'>
							<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=type>
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>