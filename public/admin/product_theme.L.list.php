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

$sort=$_REQUEST["sort"];
$mode=$_POST["mode"];
$code=$_POST["code"];
$cproductcodes=$_POST["cproductcodes"];
$movecodename=$_POST["movecodename"];
$movecode=$_POST["movecode"];

if ($mode=="copy" && strlen($movecode)==12) {
	$cproductcodes=rtrim($cproductcodes,',');
	$cproductcode=explode("|",$cproductcodes);

	$size = sizeof($cproductcode);
	if ($size>100) {
		echo "<script>alert('한번에 100개 이하로 복사가 가능합니다.');parent.ReloadList();</script>";
		exit;
	}
	if ($size==0) {
		echo "<script>alert('가상 카테고리로 복사할 상품을 선택하세요.');parent.ReloadList();</script>";
		exit;
	}

	$movecount=0;
	for ($i=0;$i<=$size;$i++) {
		if (strlen($cproductcode[$i])==18) {
			$sql = "SELECT code FROM tblproducttheme ";
			$sql.= "WHERE productcode = '{$cproductcode[$i]}' AND code = '{$movecode}' ";
			$result = pmysql_query($sql,get_db_conn());
			if (!$row=pmysql_fetch_object($result)) {
				$sql = "INSERT INTO tblproducttheme(productcode,code,date) VALUES (
				'{$cproductcode[$i]}', 
				'{$movecode}', 
				'".date("YmdHis")."')";
				$insert = pmysql_query($sql,get_db_conn());

				$log_content = "## 가상카테고리입력 ## - 카테고리 : ".str_replace("'","",$movecodename)."({$movecode}) - 상품코드 ".$cproductcode[$i];
				ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
				$movecount++;
			}
		}
	}
	if ($movecount!=0) {
		$onload="<script>alert('$movecount 건의 데이터가 [".str_replace("\"","",$movecodename)."]에서 가상카테고리로 입력 되었습니다.');parent.ParentTReloadList();</script>";
	} else {
		$onload="<script>alert('하나의 가상 카테고리에는 같은 상품이 입력될수 없습니다.');</script>";
	}
}

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
<script>LH.add("parent.parent_resizeIframe('LListFrame')");</script>

<script language="JavaScript">
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function CheckForm(cnt) {
	document.form1.cproductcodes.value="";
	checkvalue=false;
	document.form1.movecode.value=parent.getCode();
	document.form1.movecodename.value=parent.getCodeName();

	if (document.form1.movecode.value.length==0) {
		alert("이동할 가상카테고리를 선택하세요.");
		return;
	}
	for(i=1;i<=cnt;i++){
		if(document.form1.cproductcode[i].checked){
			checkvalue=true;
			document.form1.cproductcodes.value+=document.form1.cproductcode[i].value+"|";
		}
	}
	if(checkvalue!=true){
		alert('복사할 상품을 선택하세요');
		return;
	}
	if (confirm("선택된 상품을 복사하시겠습니까?")) {
		document.form1.mode.value="copy";
		document.form1.block.value="";
		document.form1.gotopage.value="";
		document.form1.submit();
	}
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
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function CheckAll(cnt){
	checkvalue=document.form1.allcheck.checked;
	for(i=1;i<=cnt;i++){
		document.form1.cproductcode[i].checked=checkvalue;
		checkActive(document.form1.cproductcode[i],document.form1.cproductcode[i].value);
	}
}

function checkActive(checkObj,checkId)
{
	if(document.getElementById("pidx_"+checkId))
	{
		if(checkObj.checked)
			document.getElementById("pidx_"+checkId).style.backgroundColor = "#EFEFEF";
		else
			document.getElementById("pidx_"+checkId).style.backgroundColor = "#FFFFFF";
	}
}

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

function DivDefaultReset()
{
	if(!self.id)
	{
		self.id = self.name;
		parent.document.getElementById(self.id).style.height = parent.document.getElementById(self.id).height;
	}

	if(!parent.self.id)
		parent.self.id = parent.self.name;
	
	parent.parent.document.getElementById(parent.self.id).style.height = parent.parent.document.getElementById(parent.self.id).height;
}
DivDefaultReset();
</script>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text.gif" border="0"></td>
</tr>	
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=cproductcodes>
<input type=hidden name=movecode>
<input type=hidden name=movecodename>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=sort value="<?=$sort?>">
<tr>
	<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30"><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');">진열순</a> | <A HREF="javascript:GoSort('productname');">상품명순</a> | <A HREF="javascript:GoSort('price');">가격순</a></td>
	</tr>
	<tr>
		<td width="100%" valign="top">
		<DIV style="width:100%;height:100%;overflow:hidden;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
<?php
			$colspan=7;
			if($vendercnt>0) $colspan++;
?>
			<col width=45></col>
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
				<TD class="table_cell">선택</TD>
				<?php if($vendercnt>0){?>
				<TD class="table_cell1">입점업체</TD>
				<?php }?>
				<TD class="table_cell1" colspan="2">상품명/진열코드/특이사항</TD>
				<TD class="table_cell1">판매가격</TD>
				<TD class="table_cell1">수량</TD>
				<TD class="table_cell1">상태</TD>
				<TD class="table_cell1">수정</TD>
			</TR>
			<input type=hidden name=cproductcode>
<?php
			if (strlen($code)==12) {
				$page_numberic_type = 1;
				$qry = "AND productcode LIKE '{$code}%' ";
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
					echo "<tr>\n";
					echo "	<TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
					echo "<tr align=\"center\" id=\"pidx_{$row->productcode}\">\n";
					echo "	<TD class=\"td_con2\"><input type=checkbox name=cproductcode value=\"{$row->productcode}\" onclick=\"checkActive(this,'{$row->productcode}')\"></td>\n";
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
					echo "	<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
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
					echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">검색된 상품이 존재하지 않습니다.</td></tr>";
				}
			} else {
				$page_numberic_type="";
				echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">검색된 상품이 존재하지 않습니다.</td></tr>";
			}
?>
			<TR>
				<TD height="1" colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr>
			<td width="100%" background="images/blueline_bg.gif">
			<table cellpadding="0" cellspacing="0" width="100%">
<?php
			echo "<tr>\n";
			echo "	<td class=\"font_blue\" style=\"padding-bottom:2px;\"><input type=checkbox id=\"idx_allcheck\" name=allcheck value=\"{$cnt}\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\" onclick=\"CheckAll('{$cnt}')\"><label style=\"cursor:hand; TEXT-DECORATION: none\" onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_allcheck><span style=\"font-size:8pt;\">전체상품 선택</span></label>(가상카테고리에 복사할 상품을 선택하세요.)</td>";
			echo "</tr>\n";
			if($page_numberic_type) {
				echo "<tr>\n";
				echo "	<td height=\"32\" align=center>\n";
				echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
				echo "	</td>\n";
				echo "</tr>\n";
			}
?>
			</table>
			</td>
		</tr>
		<tr>
			<td style="padding-top:10px;BORDER-top:#0099CC 2px solid;"><img width="0" height="0"></td>
		</tr>
		</table>
		</div>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="10"></td>
</tr>
<tr>
	<td align="center"><a href="javascript:CheckForm('<?=$cnt?>');"><img src="images/btn_copy1.gif" border="0"></a></td>
</tr>
<tr>
	<td height="10"></td>
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
