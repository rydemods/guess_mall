<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");


$code=$_POST["code"];
$disptype=$_POST["disptype"];
$s_check=$_POST["s_check"];
if(strlen($s_check)==0) $s_check="name";
$search=ltrim($_POST["search"]);

$qry = "WHERE 1=1 ";
if(strlen($code)>=3) {
	$qry.= "AND productcode LIKE '".$code."%' ";
}
$qry.= "AND vender='".$_VenderInfo->getVidx()."' ";
if($disptype=="Y") $qry.= "AND display='Y' ";
else if($disptype=="N") $qry.= "AND display='N' ";
if(strlen($search)>0) {
	if($s_check=="name") $qry.= "AND productname LIKE '%".$search."%' ";
	else if($s_check=="code") $qry.= "AND productcode='".$search."' ";
}


$setup[page_num] = 10;
$setup[list_num] = 12;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (empty($gotopage)) {
	$gotopage = 1;
}

$t_count=0;
$sql = "SELECT COUNT(*) as t_count FROM tblproduct ".$qry." ";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
$t_count = $row->t_count;
pmysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

?>
<html>
<head>
<title></title>
<link rel=stylesheet href="style.css" type=text/css>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('PrdtListIfrm')");</script>
<script language='JavaScript'>
function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}
function ChangeProduct(prcode) {
	parent.document.etcform.prcode.value=prcode;
	parent.document.etcform.target="PrdtImgIfrm";
	parent.document.etcform.action="product_imgmultiset.img.php";
	parent.document.etcform.submit();
}
</script>

</head>
<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>
<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF>
<form name="frmList" method="post">
<tr>
	<td colspan=4>
	<select name="selectPrdList" size=12 style="width:100%;" onchange="ChangeProduct(this.options[this.selectedIndex].value)">
<?
	if($t_count>0) {
		$sql = "SELECT productcode,productname,selfcode FROM tblproduct ".$qry." ";
		$sql.= "ORDER BY date DESC ";
		$sql.= "LIMIT " . $setup[list_num]." OFFSET ".($setup[list_num] * ($gotopage - 1));
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			echo "<option value=\"".$row->productcode."\">".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</option>";
			$i++;
		}
	}
?>
	</select>
	</td>
</tr>
</form>
<tr>
	<td width=50%> 전체 건수 <?=$t_count?> 건</td>
	<td width=204> </td>
	<td width=50% align=right>
<?
	if($i>0) {
		$total_block = intval($pagecount / $setup[page_num]);
		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}
		$total_block = $total_block - 1;
		if (ceil($t_count/$setup[list_num]) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
				$prev_page_exists = true;
			}
			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

				$a_prev_page = $a_first_block.$a_prev_page;
			}
			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
					}
				}
			} else {
				if (($pagecount % $setup[page_num]) == 0) {
					$lastpage = $setup[page_num];
				} else {
					$lastpage = $pagecount % $setup[page_num];
				}
				for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
					if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
						$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
					}
				}
			}
			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);
				$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
				$next_page_exists = true;
			}
			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<B>1</B>";
		}
		$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
		echo $pageing;
	}
?>
	</td>
</tr>
</table>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr><td height=5></td></tr>
<tr>
	<td style="color:#2A97A7">* 상품정보에서 큰이미지를 등록하셔야만 다중이미지 기능이 지원됩니다.</td>
</tr>
<tr>
	<td style="color:#2A97A7">* 상품다중이미지의 경우 상품 이미지 사이즈를 동일하게 등록하시길 권장합니다.</td>
</tr>

<form name="pageForm" method="post">
<input type=hidden name='code' value='<?=$code?>'>
<input type=hidden name='disptype' value='<?=$disptype?>'>
<input type=hidden name='s_check' value='<?=$s_check?>'>
<input type=hidden name='search' value='<?=$search?>'>
<input type=hidden name='sort' value='<?=$sort?>'>
<input type=hidden name='block' value='<?=$block?>'>
<input type=hidden name='gotopage' value='<?=$gotopage?>'>
</form>

</table>

</body>
</html>
