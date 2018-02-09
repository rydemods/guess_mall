<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$sort=$_POST["sort"];
$mode=$_POST["mode"];
$code=$_POST["code"];
$keyword=$_POST["keyword"];

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

$imagepath=$Dir.DataDir."shopimages/product/";
include("header.php"); 
?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<!--
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
-->

<script type="text/javascript">
 function init(){
  var doc= document.getElementById("divName");
  if(doc.offsetHeight!=0){
   pageheight = doc.offsetHeight;
   parent.document.getElementById("ListFrame").height=pageheight+"px";
   }
 }

 window.onload=function(){
  init();
 }
</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function ProductMouseOver(Obj) {
	obj = event.srcElement;
	WinObj=document.getElementById(Obj);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.display = "";
	
	if(!WinObj.height)
		WinObj.height = WinObj.offsetTop;

	WinObjPY = WinObj.offsetParent.offsetHeight;
	WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
	WinObjSY = WinObjST+WinObj.offsetHeight;

	if(WinObjPY < WinObjSY)
		WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
	else if(WinObjST < 0)
		WinObj.style.top = WinObj.offsetParent.scrollTop;
	else
		WinObj.style.top = WinObj.height;
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	WinObj = document.getElementById(Obj);
	WinObj.style.display = "none";
	clearTimeout(obj._tid);
}

function GoPage(block,gotopage,sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function GoPageReload() {
	document.form1.mode.value = "";
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function ProductSend(mode,prcode) {

	if(mode=="modify") {
		parent.ProductModify(prcode);
	}
}

function DivDefaultReset()
{
	if(!self.id)
	{
		self.id = self.name;
		parent.document.getElementById(self.id).style.height = parent.document.getElementById(self.id).height;
	}
}
DivDefaultReset();
//-->
</SCRIPT>
<div id="divName">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=keyword value="<?=$keyword?>">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_cate_function_title.gif" border="0"></td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30"><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');">진열순</a> | <A HREF="javascript:GoSort('productname');">상품명순</a> | <A HREF="javascript:GoSort('price');">가격순</a></td>
	</tr>
	<tr>
		<td width="100%">
		<DIV style="width:100%;height:100%;overflow:hidden;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
<?php
			$colspan=7;
			if($vendercnt>0) $colspan++;
?>
			<col width=40></col>
			<?php if($vendercnt>0){?>
			<col width=70></col>
			<?php }?>
			<col width=50></col>
			<col width=></col>
			<col width=70></col>
			<col width=45></col>
			<col width=45></col>
			<col width=45></col>
			<TR>
				<TD colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			<TR align="center">
				<TD class="table_cell">No</TD>
				<?php if($vendercnt>0){?>
				<TD class="table_cell1">입점업체</TD>
				<?php }?>
				<TD class="table_cell1" colspan="2">상품명/진열코드/특이사항</TD>
				<TD class="table_cell1">판매가격</TD>
				<TD class="table_cell1">수량</TD>
				<TD class="table_cell1">상태</TD>
				<TD class="table_cell1">수정</TD>
			</TR>
<?php
			if(strlen($code)==12) {
				$page_numberic_type=1;
				$code_a=substr($code,0,3);
				$code_b=substr($code,3,3);
				$code_c=substr($code,6,3);
				$code_d=substr($code,9,3);
				$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
				$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' ";
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				pmysql_free_result($result);
				if($row && strpos($row->type,'X')!==FALSE) {
					$qry = "AND productcode LIKE '{$code}%' ";
					if(strlen($keyword)>2) {
						$qry.= "AND productname LIKE '%{$keyword}%' ";
					}
					$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct WHERE 1=1 ";
					$sql0.= $qry;
					$paging = new Paging($sql0,10,10);
					$t_count = $paging->t_count;
					$gotopage = $paging->gotopage;
									
					$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
					$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
					$sql.= "FROM tblproduct WHERE 1=1 ";
					$sql.= $qry." ";
					if ($sort=="price")				$sql.= "ORDER BY sellprice ";
					elseif ($sort=="productname")	$sql.= "ORDER BY productname ";
					else							$sql.= "ORDER BY date DESC ";
					$sql = $paging->getSql($sql);
					$result = pmysql_query($sql,get_db_conn());
					$cnt=0;
					while($row=pmysql_fetch_object($result)) {
						$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
						$cnt++;
						
						echo "<tr>\n";
						echo "	<TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD>\n";
						echo "</tr>\n";
						echo "<tr align=\"center\">\n";
						echo "	<TD class=\"td_con2\">{$number}</td>\n";
						if($vendercnt>0) {
							echo "	<TD class=\"td_con1\"><B>".(ord($venderlist[$row->vender]->vender)?"<a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</a>":"-")."</B></td>\n";
						}
						echo "	<TD class=\"td_con1\">";
						if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
							echo "<img src='".$imagepath.$row->tinyimage."' height=40 width=40 border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
						} else {
							echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
						}
						echo "<div id=\"primage{$cnt}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
						echo "		<tr bgcolor=\"#FFFFFF\">\n";
						if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
							echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
						} else {
							echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
						}
						echo "		</tr>\n";
						echo "		</table>\n";
						echo "		</div>\n";
						echo "	</td>\n";
						echo "	<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><A HREF=\"javascript:ProductSend('modify','{$row->productcode}');\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."</A>&nbsp;</td>\n";
						echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
						echo "	<TD class=\"td_con1\">";
						if (ord($row->quantity)==0) echo "무제한";
						else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
						else echo $row->quantity;
						echo "	</TD>\n";
						echo "	<TD class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
						echo "	<TD class=\"td_con1\"><a href=\"javascript:ProductSend('modify','{$row->productcode}');\"><img src=\"images/icon_edit2.gif\" border=\"0\"></a></td>\n";
						echo "</tr>\n";
					}
					pmysql_free_result($result);
					if ($cnt==0) {
						$page_numberic_type="";
						echo "<tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
					}
				} else {
					$page_numberic_type="";
					echo "<tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
				}
			} else {
				$page_numberic_type="";
				echo "<tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
			}
?>
			<TR>
				<TD height="1" colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
<?php
		if($page_numberic_type) {
			echo "<tr>\n";
			echo "	<td height=\"52\" align=center background=\"images/blueline_bg.gif\">\n";
			echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
		}
?>
		<tr>
			<td style="padding-top:12px;BORDER-top:#0099CC 2px solid;"><img width="0" height="0"></td>
		</tr>
		</table>
		</div>
	</table>
	</td>
</tr>
</form>
<?php if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?php }?>
</table>
</div>
</body>
</html>
