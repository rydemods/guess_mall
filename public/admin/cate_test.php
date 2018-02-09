<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-2";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
include("header.php"); 
?>



<?
$sql = "SELECT code_a, code_b, code_c, code_d, type, code_name FROM tblforumcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
$i=0;
$ii=0;
$iii=0;

$strcodelist = "";
$strcodelist.= "<script>\n";
$result = pmysql_query($sql,get_db_conn());
$selcode_name="";
while($row=pmysql_fetch_object($result)) {
	$strcodelist.= "var clist=new CodeList();\n";
	$strcodelist.= "clist.code_a='{$row->code_a}';\n";
	$strcodelist.= "clist.code_b='{$row->code_b}';\n";
	$strcodelist.= "clist.code_c='{$row->code_c}';\n";
	$strcodelist.= "clist.code_d='{$row->code_d}';\n";
	$strcodelist.= "clist.type='{$row->type}';\n";
	$strcodelist.= "clist.code_name='{$row->code_name}';\n";
	if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
		$strcodelist.= "lista[{$i}]=clist;\n";
		$i++;
	}
	if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
		if ($row->code_c=="000" && $row->code_d=="000") {
			$strcodelist.= "listb[{$ii}]=clist;\n";
			$ii++;
		} else if ($row->code_d=="000") {
			$strcodelist.= "listc[{$iii}]=clist;\n";
			$iii++;
		} 
	}
	$strcodelist.= "clist=null;\n\n";
}
pmysql_free_result($result);
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";


$codeA_list = "<select name=code_a id=code_a style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,1)\" {$disabled} Multiple>\n";
$codeA_list.= "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
$codeA_list.= "</select>\n";

$codeB_list = "<select name=code_b id=code_b style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,2)\" {$disabled} Multiple>\n";
$codeB_list.= "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
$codeB_list.= "</select>\n";

$codeC_list = "<select name=code_c id=code_c style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,3)\" {$disabled} Multiple>\n";
$codeC_list.= "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
$codeC_list.= "</select>\n";
// 스크립트 작성완료

?>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script> 

	<table cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
		<tr>
			<th><span>카테고리 선택</span> <font color='#FF0000' > *필수 </font> </th>
			<td colspan="3">
			<?php
				//카테고리 SELECT BOX를 불러온다
				echo $codeA_list;
				echo $codeB_list;
				echo $codeC_list;
				//카테고리 SELECT 버튼을 불러온다
				//echo $codeSelect;
				//카테고리 스크립트 실행
				echo $strcodelist;
				echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";					
			?>
			</td>
		</tr>
	</table>
