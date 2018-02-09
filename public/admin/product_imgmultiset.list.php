<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-2";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$sort=$_REQUEST["sort"];
$Scrolltype=$_REQUEST["Scrolltype"];

$mode=$_POST["mode"];
$code=$_POST["code"];
$prcode=$_POST["prcode"];
$keyword=$_POST["keyword"];
$searchtype=$_POST["searchtype"];
if(ord($searchtype)==0) $searchtype=0;
$onload=$_POST["onload"];

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
?>

<?php include("header.php"); ?>
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
var ProductInfoStop="";

<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	ProductInfoStop = "1";
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function GoSort(sort) {
	document.form1.type.value = "";
	document.form1.sort.value = sort;
	document.form1.submit();
}

function CheckForm(type) {
	checked=false;
	for(i=1;i<=10;i++) {
		gbn=i;
		if(gbn<10)gbn="0"+gbn;
		if(document.form2["mulimg"+gbn].value.length>0) {
			checked=true;
			break;
		}
	}

	if(type!="delete" && checked==false){
		alert('등록하실 이미지를 선택하세요.');
		document.form2.mulimg01.focus();
		return;
	}
	if(type!="delete" || confirm("이미지를 삭제하시겠습니까?")){
		document.form2.type.value=type;
		document.form2.submit();
	}
}

function mulimgdel(no) {
	if(confirm("해당 이미지를 삭제하시겠습니까?")){
		document.form2.type.value="delete";
		document.form2.mulimgno.value=no;
		document.form2.submit();
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

function SelectList(idx)
{
	if(ProductInfoStop)
		ProductInfoStop = "";
	else
	{
		if(idx != document.form1.prcode.value)
		{
			document.form1.prcode.value = idx;
			document.form1.type.value="";
			document.form1.submit();
		}
	}
}

function ProductInfo(prcode) {
	ProductInfoStop = "1";
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

function DivScrollActive(arg1)
{
	if(!self.id)
	{
		self.id = self.name;
		parent.document.getElementById(self.id).style.height = parent.document.getElementById(self.id).height;
	}
	
	if(document.getElementById("divscroll") && document.getElementById("ListTTableId") && document.getElementById("ListLTableId") && parent.document.getElementById(self.id))
	{
		if(!document.getElementById("divscroll").height)
			document.getElementById("divscroll").height=document.getElementById("divscroll").offsetHeight;
		
		if(arg1>0)
		{
			if(document.getElementById("ListLTableId").offsetHeight > document.getElementById("divscroll").offsetHeight)
			{
				document.getElementById("divscroll").style.height="100%";
				parent.document.getElementById(self.id).style.height=document.getElementById("ListTTableId").offsetHeight;
			}
		}
		else
		{
			var default_divwidth = document.getElementById("ListTTableId").offsetHeight-document.getElementById("divscroll").offsetHeight+document.getElementById("divscroll").height;
			document.getElementById("divscroll").style.height=document.getElementById("divscroll").height;

			if(document.getElementById("divscroll2") && document.getElementById("ListLLTableId"))
				parent.document.getElementById(self.id).style.height=default_divwidth;
			else
				parent.document.getElementById(self.id).style.height="100%";
		}

		if(document.form1 && document.form1.Scrolltype)
			document.form1.Scrolltype.value=arg1;
		
		if(document.form2 && document.form2.Scrolltype)
			document.form2.Scrolltype.value=arg1;
		
		location.hash="#prcodelink";
	}
}
//-->
</SCRIPT>
<div id="divName">
<table id="ListTTableId" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=type>
<input type=hidden name=searchtype value="<?=$searchtype?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=keyword value="<?=$keyword?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=Scrolltype value="<?=$Scrolltype?>">
<input type=hidden name=prcode value="<?=$prcode?>">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text.gif" border="0"></td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top" style="background-color:#FFFFFF;BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30">
		<TABLE cellSpacing=0 cellPadding=0 border=0 width="100%">
		<tr>
			<td><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');">진열순</a> | <A HREF="javascript:GoSort('productname');">상품명순</a> | <A HREF="javascript:GoSort('price');">가격순</a></td>
			<td align="right"><a href="javascript:DivScrollActive(1);"><span style="letter-spacing:-0.5pt;" class="font_orange"><b>전체펼침</b></span></a>&nbsp;&nbsp;<a href="javascript:DivScrollActive(0);"><b>펼침닫기</b></a></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td width="100%" valign="top">
		<table cellpadding="0" cellspacing="0" width="100%">
		<TR>
			<TD background="images/table_top_line.gif"></TD>
		</TR>
		<tr>
			<td width="100%" valign="top">
			<DIV id="divscroll" style="position:relative;width:100%;height:278px;bgcolor:#FFFFFF;overflow-x:hidden;overflow-y:auto;">
			<TABLE id="ListLTableId" border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
<?php
			$colspan=7;
			if($vendercnt>0) $colspan++;
?>
			<col width=50></col>
			<?php if($vendercnt>0){?>
			<col width=70></col>
			<?php }?>
			<col width=50></col>
			<col width=></col>
			<col width=70></col>
			<col width=45></col>
			<col width=45></col>
			<col width=45></col>
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
			if (($searchtype=="0" && strlen($code)==12) || ($searchtype=="1" && strlen($keyword)>2)) {
				$prcode_selected[$prcode] = " bgcolor=\"#EFEFEF\"";
				$prcode_link[$prcode] = "<a name=\"prcodelink\">";

				$sql = "SELECT option_price,productcode,productname,production,sellprice,consumerprice, ";
				$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
				$sql.= "FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode) ";
				if ($searchtype=="0" && strlen($code)==12) {
					$sql.= "WHERE b.c_category LIKE '{$code}%' ";
				} else {
					$sql.= "WHERE productname LIKE '%{$keyword}%' ";
				}
				if ($sort=="price")				$sql.= "ORDER BY sellprice ";
				else if ($sort=="productname")	$sql.= "ORDER BY productname ";
				else							$sql.= "ORDER BY date DESC ";
				$result = pmysql_query($sql,get_db_conn());
				$t_count = @pmysql_num_rows($result);
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					echo "<tr>\n";
					echo "	<TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\">{$prcode_link[$row->productcode]}</TD>\n";
					echo "</tr>\n";
					echo "<tr align=\"center\"{$prcode_selected[$row->productcode]} onclick=\"SelectList('{$row->productcode}')\" id=\"pidx_{$row->productcode}\" style=\"cursor:hand;\">\n";
					echo "	<TD class=\"td_con2\">{$number}<br><img src=\"images/btn_select1a.gif\" border=\"0\"></td>\n";
					if($vendercnt>0) {
						echo "	<TD class=\"td_con1\"><B>".(ord($venderlist[$row->vender]->vender)?"<span onclick=\"viewVenderInfo({$row->vender});\">{$venderlist[$row->vender]->id}</span>":"-")."</B></td>\n";
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
					echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format(exchageRate($row->sellprice))."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="N"?number_format(exchageRate($row->reserve)):$row->reserve."%")."</TD>\n";
					echo "	<TD class=\"td_con1\">";
					if (ord($row->quantity)==0) echo "무제한";
					else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
					else echo $row->quantity;
					echo "	</TD>\n";
					echo "	<TD class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
					echo "	<TD class=\"td_con1\"><img src=\"images/icon_newwin1.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"ProductInfo('{$row->productcode}');\"></td>\n";
					echo "</tr>\n";
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">검색된 상품이 존재하지 않습니다.</td></tr>";
				}
			} else {
				echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">검색된 상품이 존재하지 않습니다.</td></tr>";
			}
?>
			<TR>
				<TD colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
			</TR>
			</table>
			</div>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="top" style="padding-top:3pt;padding-bottom:3pt;BORDER-top:#0099CC 2px solid;BORDER-bottom:#0099CC 2px solid;">* 상품정보에서 큰이미지를 등록하셔야만 다중이미지 기능이 지원됩니다.<br>* 상품다중이미지의 경우 상품 이미지 사이즈를 동일하게 등록하시길 권장합니다.</td>
	</tr>
	</form>
	<tr><td height="10"></td></tr>
<?php
if(strlen($prcode)==18){
	$sql = "SELECT * FROM tblmultiimages ";
	$sql.= "WHERE productcode = '{$prcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)){
		$mulimg_name = array ("01"=>&$row->primg01,"02"=>&$row->primg02,"03"=>&$row->primg03,"04"=>&$row->primg04,"05"=>&$row->primg05,"06"=>&$row->primg06,"07"=>&$row->primg07,"08"=>&$row->primg08,"09"=>&$row->primg09,"10"=>&$row->primg10);
		$type="update";
	} else {
		$type="insert";
	}
?>
	<tr>
		<td width="100%">
		<DIV id="divscroll2" style="position:relative;width:100%;height:100%;bgcolor:#FFFFFF;overflow-x:hidden;overflow-y:hidden;">
		<table id="ListLLTableId" cellpadding="0" cellspacing="0" width="100%">
		<TR>
			<TD height=15>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
			<TR>
				<TD><IMG SRC="images/sub4_stitle_img1.gif" WIDTH=20 HEIGHT=24 ALT=""></TD>
				<TD width="100%" background="images/sub4_stitle_bg.gif" class="font_white">상품 다중이미지 등록</TD>
				<TD><IMG SRC="images/sub4_stitle_img2.gif" WIDTH="20" HEIGHT=24 ALT=""></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<TR>
			<TD>
			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form2 action="product_imgmultiprocess.php" method=post enctype="multipart/form-data">
			<tr>
				<td width="100%">
				<TABLE cellSpacing="1" cellPadding=0 width="100%" border=0 bgcolor="#DEDEDE">
<?php
			$urlpath=$Dir.DataDir."shopimages/multi/";
			for($i=1;$i<=10;$i+=2){
				$gbn1=sprintf("%02d",$i);
				$gbn2=sprintf("%02d",$i+1);
?>
				<TR bgColor=#f0f0f0>
					<TD class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="50%" bgcolor="#F9F9F9"><input type=file name=mulimg<?=$gbn1?> style="width:100%"><input type=hidden name=oldimg<?=$gbn1?> value="<?=$mulimg_name[$gbn1]?>"></TD>
					<TD class=line style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="50%" bgcolor="#F9F9F9"><input type=file name=mulimg<?=$gbn2?> style="width:100%"><input type=hidden name=oldimg<?=$gbn2?> value="<?=$mulimg_name[$gbn2]?>"></TD>
				</TR>
				<?php if(ord($mulimg_name[$gbn1]) || ord($mulimg_name[$gbn2])){?>
				<TR>
					<TD class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px" align=middle width="50%" bgcolor="#F9F9F9">
						<?php if(ord($mulimg_name[$gbn1])){?>
						 <img src="<?=$urlpath."s".$mulimg_name[$gbn1]?>" width="100" height="100" border="0"><A HREF="javascript:mulimgdel('<?=$gbn1?>');"><img src="images/icon_del1.gif" border="0"></a>
						<?php }else{echo"&nbsp;";}?>
					</TD>
					<TD class=line style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px" align=middle width="50%" bgcolor="#F9F9F9">
						<?php if(ord($mulimg_name[$gbn2])){?>
						<img src="<?=$urlpath."s".$mulimg_name[$gbn2]?>" width="100" height="100" border="0"><A HREF="javascript:mulimgdel('<?=$gbn2?>');"><img src="images/icon_del1.gif" border="0"></a>
						<?php }else{echo"&nbsp;";}?>
					</TD>
				</TR>
				<?php }?>
<?php
			}
?>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td width="100%" align=center style="padding-top:5pt; padding-bottom:5pt;">
				<?php if($type=="update"){?>
				<a href="javascript:CheckForm('<?=$type?>');"><img src="images/btn_edit2.gif" border="0"></a>
				<?php }else{?>
				<a href="javascript:CheckForm('<?=$type?>');"><img src="images/btn_fileup.gif"border="0"></a>
				<?php }?>
				&nbsp;<a href="javascript:<?=($type=="insert"?"alert('등록된 다중이미지가 없습니다.');":"CheckForm('delete');")?>"><img src="images/btn_del5.gif" width="113" height="38" border="0"></a></td>
			</tr>
			<input type=hidden name=type>
			<input type=hidden name=mulimgno>
			<input type=hidden name=searchtype value="<?=$searchtype?>">
			<input type=hidden name=keyword value="<?=$keyword?>">
			<input type=hidden name=code value="<?=$code?>">
			<input type=hidden name=productcode value="<?=$prcode?>">
			<input type=hidden name=sort value="<?=$sort?>">
			<input type=hidden name=Scrolltype value="<?=$Scrolltype?>">
			</form>
			<form name=prform method=post action="product_register.php" target="_parent">
			<input type=hidden name=prcode value="<?=$prcode?>">
			</form>
			</table>
			</td>
		</tr>
		</table>
		</div>
		</td>
	</tr>
<?php
}
?>
	</table>
	</td>
</tr>
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
<?=($Scrolltype?"<script>DivScrollActive(1);location.hash=\"#prcodelink\";</script>":"<script>DivScrollActive(0);location.hash=\"#prcodelink\";parent.location.hash=\"#\";</script>")?>
<?=stripslashes($onload)?>
</div>
</body>
</html>
