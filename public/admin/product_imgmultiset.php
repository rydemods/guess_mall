<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-2";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$searchtype=0;
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
var code="<?=$code?>";
function CodeProcessFun(_code) {
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		document.all["code_top"].style.background="#dddddd";
		selcode="";
		seltype="";

		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		BodyInit(_code);
	}

	if(selcode.length==12 && selcode!="000000000000" && seltype.indexOf("X")!=-1) {
		document.form2.mode.value="";
		document.form2.code.value=selcode;
		document.form2.target="ListFrame";
		document.form2.action="product_imgmultiset.list.php";
		if(document.ListFrame.form1)
			document.form2.Scrolltype.value = document.ListFrame.form1.Scrolltype.value;
		document.form2.submit();
	} else {
		document.form2.mode.value="";
		document.form2.code.value="";
		document.form2.target="ListFrame";
		document.form2.action="product_imgmultiset.list.php";
		if(document.ListFrame.form1)
			document.form2.Scrolltype.value = document.ListFrame.form1.Scrolltype.value;
		document.form2.submit();
	}
}

var allopen=false;
function AllOpen() {
	display="show";
	open1="open";
	if(allopen) {
		display="none";
		open1="close";
		allopen=false;
	} else {
		allopen=true;
	}
	for(i=0;i<all_list.length;i++) {
		if(display=="none" && all_list[i].code_a==selcode.substring(0,3)) {
			all_list[i].selected=true;
			selcode=all_list[i].code_a+all_list[i].code_b+all_list[i].code_c+all_list[i].code_d;
			seltype=all_list[i].type;
		}
		all_list[i].display=display;
		all_list[i].open=open1;
		for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
			if(display=="none") {
				all_list[i].ArrCodeB[ii].selected=false;
			}
			all_list[i].ArrCodeB[ii].display=display;
			all_list[i].ArrCodeB[ii].open=open1;
			for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
				if(display=="none") {
					all_list[i].ArrCodeB[ii].ArrCodeC[iii].selected=false;
				}
				all_list[i].ArrCodeB[ii].ArrCodeC[iii].display=display;
				all_list[i].ArrCodeB[ii].ArrCodeC[iii].open=open1;
				for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
					if(display=="none") {
						all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].selected=false;
					}
					all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].display=display;
					all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].open=open1;
				}
			}
		}
	}
	BodyInit('');
}

function ViewLayer(layer) {
	if(layer=="layer2") {
		document.all["contentDiv"].disabled=true;
		document.all["hide_code_div"].style.display="none";
		document.all["hide_code_div2"].style.display="";
		document.all["layer2"].style.display="";
	} else {
		document.all["contentDiv"].disabled=false;
		document.all["hide_code_div"].style.display="";
		document.all["hide_code_div2"].style.display="none";
		document.all["layer2"].style.display="none";
	}
}

function CheckSearch() {
	document.form1.mode.value = "";
	document.form1.code.value = "";
	if (document.form1.keyword.value.length<2) {
		if(document.form1.keyword.value.length==0) alert("검색어를 입력하세요.");
		else alert("검색어는 2글자 이상 입력하셔야 합니다."); 
		document.form1.keyword.focus();
		return;
	} else {
		document.form1.target="ListFrame";
		document.form1.action="product_imgmultiset.list.php";
		document.form1.Scrolltype.value = document.ListFrame.form1.Scrolltype.value;
		document.form1.submit();
	}
}

function CheckKeyPress(){
	ekey=event.keyCode;
	if (ekey==13) {
		CheckSearch();
	}
}

var divLeft=0;
var defaultLeft=0;
var timeOffset=0;
var setTObj;
var divName="";
var zValue=0;

function divMove()
{
	divLeft+=timeOffset;
		
	if(divLeft >= defaultLeft)
	{
		divLeft=defaultLeft;
		divName.style.left=divLeft;
		divName.style.zIndex = zValue;
		clearTimeout(setTObj);
		setTObj="";
	}
	else
	{
		timeOffset+=20;
		divName.style.left=divLeft;
		setTObj=setTimeout('divMove();',5);
	}
}

function divAction(arg1,arg2)
{
	if(zValue != arg2 && !setTObj)
	{
		defaultLeft = arg1.offsetLeft;
		divLeft = defaultLeft;
		zValue = arg2;
		divName = arg1;
		if(defaultLeft>0)
			timeOffset = -70;
		divMove();
	}
}
</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 315;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 다중이미지 관리 &gt;<span>상품 다중이미지 등록/관리</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_product.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 다중이미지 등록/관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품등록시 대/중/소 이미지 외 10여개의 이미지를 다중으로 더 보여줄 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr><td height="20"></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=code>
			<input type=hidden name=Scrolltype>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC" style="padding:6pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding="0" width="100%" border=0>
						<TR>
							<TD class="table_cell" width="214"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>상품보기 선택</b></TD>
							<TD class="td_con1"><input type=radio id="idx_searchtype1" name=searchtype value="0" onclick="ViewLayer('layer1')" <?php if($searchtype=="0") echo "checked";?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype1>카테고리별 상품 보기</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_searchtype2" name=searchtype value="1" onclick="ViewLayer('layer2')" <?php if($searchtype=="1") echo "checked";?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype2>검색으로 상품 보기</label></TD>
						</TR>
						</table>
						<div id=layer2 style="margin-left:0;display:hide; display:<?=($searchtype=="1"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<TABLE cellSpacing=0 cellPadding="0" width="100%" border=0>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="214"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>상품명 입력</b></TD>
							<TD class="td_con1">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="300"><input type=text name=keyword size=50 value="<?=$keyword?>" onKeyDown="CheckKeyPress()" class="input" style=width:300></td>
								<td><p align="left"><a href="javascript:CheckSearch();"><img src="images/btn_search2.gif" border="0" align=absmiddle hspace="2"></a></td>
							</tr>
							</table>
							</TD>
						</TR>
						</TABLE>
						</div>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%" height="648">
				<tr>
					<td valign="top">
					<!--<DIV onmouseover="divAction(this,2);" id="cateidx" style="position:absolute;z-index:0;width:242px;bgcolor:#FFFFFF;">-->
					<DIV  id="cateidx" style="position:absolute;z-index:0;width:242px;bgcolor:#FFFFFF;">
					<table cellpadding="0" cellspacing="0" width="100%" height="608">
					<tr>
						<td width="232" height="100%" valign="top" background="images/category_boxbg.gif">
						<table cellpadding="0" cellspacing="0" width="242" height="100%">
						<tr>
							<td bgcolor="#FFFFFF"><IMG SRC="images/product_totoacategory_title.gif" border="0"></td>
						</tr>
						<tr>
							<td><IMG SRC="images/category_box1.gif" border="0"></td>
						</tr>
						<tr>
							<td bgcolor="#0F8FCB" style="padding:2;padding-left:4">
							<IMG SRC="images/category_btn1.gif" border="0" onclick="AllOpen();">
							</td>
						</tr>
						<tr>
							<td bgcolor="#0F8FCB" style="padding-top:4pt; padding-bottom:6pt;"></td>
						</tr>
						<tr>
							<td><IMG SRC="images/category_box2.gif" border="0"></td>
						</tr>
						<tr>
							<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;">
							<div id=hide_code_div style="width=99%;height:100%;">
							<DIV class=MsgrScroller id=contentDiv style="width=99%;height:100%;OVERFLOW-x: auto; OVERFLOW-y: auto; z-index:1" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
							<DIV id=bodyList>
							<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor="#FFFFFF">
							<tr>
								<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span></td>
							</tr>
							<tr>
								<!-- 상품카테고리 목록 -->
								<td id="code_list" nowrap valign=top></td>
								<!-- 상품카테고리 목록 끝 -->
							</tr>
							</table>
							</DIV>
							</DIV>
							</div>
							<div id=hide_code_div2 style="width=99%;height:100%;display:none;">
							<DIV class=MsgrScroller id=contentDiv2 style="width=99%;height:100%;OVERFLOW-x: auto; OVERFLOW-y: auto; background:#f4f4f4" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" disabled>
							<DIV id=bodyList2>
							<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor="#FFFFFF">
							<tr>
								<td nowrap align=center>카테고리별 상품보기에서만<br>카테고리정보를 보실 수 있습니다.</td>
							</tr>
							</table>
							</DIV>
							</DIV>
							</div>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td><IMG SRC="images/category_boxdown.gif" border="0"></td>
					</tr>
					</table>
					</td>
					<td style="padding-left:250px;"></td>
					<!--<td width="100%" valign="top" height="100%" onmouseover="divAction(document.getElementById('cateidx'),0);">-->
					<td width="100%" valign="top" height="100%">
					<DIV style="position:relative;z-index:1;width:100%;height:100%;bgcolor:#FFFFFF;"><IFRAME name="ListFrame" id="ListFrame" src="product_imgmultiset.list.php" width=100% height=648 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></div></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			</form>
			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<input type=hidden name=Scrolltype>
			</form>
			<tr>
				<td>
				
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>상품보기 선택</span></dt>
							<dd>
							  - 카테고리별 상품 보기 : 상품이 등록된 최하위 카테고리를 선택할 경우 상품이 출력됩니다. <br>
							  - 검색으로 상품 보기 : 상품명으로 검색 후 검색된 상품이 있을 경우 상품이 출력됩니다.
							  
							</dd>	
						</dl>
						<dl>
							<dt><span>상품 다중이미지 등록/관리 주의사항</span></dt>
							<dd>
							  - 다중이미지 등록시 중,대 이미지는 등록한 다중 이미지로 교체되어 출력됩니다.<br>
						<b>&nbsp;&nbsp;</b>중,대 이미지 필요시 다중 이미지에 추가 등록해 주세요.<br>
							  - 삭제버튼 클릭시 해당 상품으로 등록된 다중이미지 모두 일괄로 삭제됩니다.
							  
							</dd>	
						</dl>
						<dl>
							<dt><span>다중이미지 등록방법</span></dt>
							<dd>
							  ① 상품보기 선택(카테고리별 상품 보기, 검색으로 상품 보기)에 따라 상품 출력. <br>
							  ② 다중이미지를 추가할 상품을 선택 후 최대 10개까지 입력할 수 있습니다.
							</dd>	
						</dl>
				</div>
				
				
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?php
$sql = "SELECT * FROM tblproductcode WHERE type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' ";
$sql.= "ORDER BY sequence DESC ";
include("codeinit.php");
include("copyright.php");
