<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$code=$_POST["code"];
$search=$_POST["search"];
$s_check=(int)$_POST["s_check"];

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
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
<script language="JavaScript">
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function ProductInfo(prcode) {
	code=prcode.substring(0,12);
	popup="YES";
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}
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
	document.form1.search.value = "OK";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.sort.value = sort;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.search.value = "OK";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
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
</script>

<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text.gif" border="0"></td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30"><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');"><B>진열순</B></a> | <A HREF="javascript:GoSort('price');"><B>가격순</B></a> | <A HREF="javascript:GoSort('productname');"><B>상품명순</B></a></td>
	</tr>
	<tr>
		<td width="100%" valign="top">
		<DIV style="width:100%;height:100%;overflow:hidden;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
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
				<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
			</TR>
			<TR align="center">
				<TD class="table_cell" style="font-size:11px;">No</TD>
				<?php if($vendercnt>0){?>
				<TD class="table_cell1" style="font-size:11px;">입점업체</TD>
				<?php }?>
				<TD class="table_cell1" colspan="2" style="font-size:11px;">상품명/진열코드/특이사항</TD>
				<TD class="table_cell1" style="font-size:11px;">판매가격</TD>
				<TD class="table_cell1" style="font-size:11px;">수량</TD>
				<TD class="table_cell1" style="font-size:11px;">상태</TD>
				<TD class="table_cell1" style="font-size:11px;">수정</TD>
			</TR>
<?php
			$cnt=0;
			if ($search=="OK" && strlen($code)==12) {
				$page_numberic_type=1;
				$sql = "SELECT code_a||code_b||code_c||code_d as code, type,code_name FROM tblproductcode ";
				$result = pmysql_query($sql,get_db_conn());
				while ($row=pmysql_fetch_object($result)) {
					$code_name[$row->code] = $row->code_name;
				}
				pmysql_free_result($result);

				list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
				$likecode=$code_a;
				if($code_b!="000") {
					$likecode.=$code_b;
					if($code_c!="000") {
						$likecode.=$code_c;
						if($code_d!="000") {
							$likecode.=$code_d;
						}
					}
				}

				$qry.= "WHERE productcode LIKE '{$likecode}%' ";
				if($s_check==1)		$qry.="AND (quantity is NULL OR quantity > 0) ";
				elseif($s_check==2)$qry.="AND quantity <= 0 ";

				$sql = "SELECT COUNT(*) as t_count FROM tblproduct ".$qry;
				$paging = new Paging($sql,10,10);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;

				$sql = "SELECT productcode,display,addcode,productname,quantity,tinyimage,vender,sellprice,reserve,reservetype,selfcode,assembleuse FROM tblproduct ";
				$sql.= $qry;
				if ($sort=="price")				$sql.= "ORDER BY sellprice ";
				elseif ($sort=="productname")	$sql.= "ORDER BY productname ";
				else							$sql.= "ORDER BY date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

					if (strlen($row->quantity) == 0) $quantity = "무제한";
					elseif ($row->quantity > 0) $quantity = "$row->quantity";
					elseif ($row->quantity < 1) $quantity = "<font color=red>품절</font>";

					$codename="";
					$code_a = substr($row->productcode,0,3);
					$code_b = substr($row->productcode,3,3);
					$code_c = substr($row->productcode,6,3);
					$code_d = substr($row->productcode,9,3);
					if($code_b=="000") $code_b="";
					if($code_c=="000") $code_c="";
					if($code_d=="000") $code_d="";
					$codename.=$code_name[$code_a."000000000"];
					if(ord($code_name[$code_a.$code_b."000000"])) {
						$codename.=" > ".$code_name[$code_a.$code_b."000000"];
					}
					if(ord($code_name[$code_a.$code_b.$code_c."000"])) {
						$codename.=" > ".$code_name[$code_a.$code_b.$code_c."000"];
					}

					if(ord($code_name[$code_a.$code_b.$code_c.$code_d])) {
						$codename.=" > ".$code_name[$code_a.$code_b.$code_c.$code_d];
					}

					echo "<tr>\n";
					echo "	<TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
					echo "<tr align=center>";
					echo "	<TD align=center class=\"td_con2\">{$number}</td>\n";
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
					echo "	<TD align=left class=\"td_con1\" style=\"word-break:break-all;\"><span style=\"font-size:8pt; letter-spacing:-0.5pt;\"><span class=\"font_orange\"><b>카테고리 : </b></span>{$codename}</span>";
					echo "		<br><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><font color=#3D3D3D>".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."</font>";
					echo "	</td>\n";
					echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
					echo "	<TD class=\"td_con1\">";
					if (ord($row->quantity)==0) echo "무제한";
					else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
					else echo $row->quantity;
					echo "	</TD>\n";
					echo "	<TD class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
					echo "	<TD class=\"td_con1\"><a href=\"javascript:ProductInfo('{$row->productcode}');\"><img src=\"images/icon_newwin1.gif\" border=\"0\"></a></td>\n";
					echo "</tr>\n";
					$cnt++;
				}
				pmysql_free_result($result);

				if ($cnt==0) {
					$page_numberic_type="";
					echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
				}
			} else {
				$page_numberic_type="";
				echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 없습니다.</td></tr>";
			}
?>
			<TR>
				<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
<?php
		if($page_numberic_type) {
			echo "<tr>\n";
			echo "	<td width=\"100%\" height=\"52\" background=\"images/blueline_bg.gif\" align=center>\n";
			echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
		}
?>
		<tr>
			<td style="padding-top:12px;BORDER-top:#0099CC 2px solid;"><img width="0" height="0"></td>
		</tr>
		</table>
		</div>
		</td>
	</tr>
	</table>
	</td>
</tr>
</form>
<form name=form_reg action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>
<?php if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?php }?>
</table>
<?=$onload?>
</body>
</html>
