<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$searchtype=0;
$code=$_POST["code"];

if(strlen($code)==12) {
	list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');	
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">

function Search() {
	//if (document.form1.code.value.length==0) {
	//	alert("상품카테고리를 선택하세요.");
	//	return;
	//}
	document.form1.search.value="OK";
	document.form1.target="ListFrame";
	document.form1.action="product_allupdate.list_t.php";
	document.form1.submit();
}


function ACodeSendIt(f,obj) {
	if(obj.ctype=="X") {
		f.code.value = obj.value+"000000000";
	} else {
		f.code.value = obj.value;
	}

	burl = "product_excelupload.ctgr.php?depth=2&code=" + obj.value;
	curl = "product_excelupload.ctgr.php?depth=3";
	durl = "product_excelupload.ctgr.php?depth=4";
	BCodeCtgr.location.href = burl;
	CCodeCtgr.location.href = curl;
	DCodeCtgr.location.href = durl;
}

function ViewLayer(layer) {
	if(layer=="layer2") {
		document.all["contentDiv"].disabled=true;
		document.all["layer2"].style.display="";
	} else {
		document.all["contentDiv"].disabled=false;
		document.all["layer2"].style.display="none";
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 상품 일괄관리 &gt;<span>상품 일괄 간편수정</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 일괄 간편수정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰에 등록된 상품의 가격을 포함한 적립금, 수량 등을 일괄 수정할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=search>
			<input type=hidden name=code>
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
							<TD class="table_cell" width="130"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>카테고리 선택</b></TD>
							<TD class="td_con1" width="600">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<col width=145></col>
							<col width=3></col>
							<col width=145></col>
							<col width=3></col>
							<col width=145></col>
							<col width=3></col>
							<col width=></col>
							<tr>
								<td>
								<select name="code1" style=width:145 onchange="ACodeSendIt(document.form1,this.options[this.selectedIndex])">
								<option value="">---- 대 분 류 ----</option>
		<?php
								$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
								$sql.= "WHERE code_b='000' AND code_c='000' ";
								$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
								$result=pmysql_query($sql,get_db_conn());
								while($row=pmysql_fetch_object($result)) {
									$ctype=substr($row->type,-1);
									if($ctype!="X") $ctype="";
									echo "<option value=\"{$row->code_a}\" ctype='{$ctype}'>{$row->code_name}";
									if($ctype=="X") echo " (단일분류)";
									echo "</option>\n";
								}
								pmysql_free_result($result);
		?>
								</select>
								</td>
								<td></td>
								<td>
								<iframe name="BCodeCtgr" src="product_excelupload.ctgr.php?depth=2" width="145" height="21" scrolling=no frameborder=no></iframe>
								</td>
								<td></td>
								<td><iframe name="CCodeCtgr" src="product_excelupload.ctgr.php?depth=3" width="145" height="21" scrolling=no frameborder=no></iframe></td>
								<td></td>
								<td><iframe name="DCodeCtgr" src="product_excelupload.ctgr.php?depth=4" width="145" height="21" scrolling=no frameborder=no></iframe></td>
							</tr>
							</table>
							</td>
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<tr>
							<TD class="table_cell" width="130"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>상품명 입력</b></TD>
							<td class="td_con1"><input type=text name=keyword size=50 value="<?=$keyword?>" onKeyDown="CheckKeyPress()" class="input" style=width:100%></td>							
						</tr>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<tr>
							<TD class="table_cell" width="130"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>재고 선택</b></TD>
							<td class="td_con1"><input type=radio id="idx_s_check1" name=s_check value=0 <?php if($s_check==0) echo " checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check1>전체상품</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_s_check2" name=s_check value=1 <?php if($s_check==1) echo " checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check2>재고상품</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_s_check3" name=s_check value=2 <?php if($s_check==2) echo " checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check3>품절상품</label></td>
						</tr>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<tr> 
							<td width="100%" colspan="2" style="padding:10 0 5 0"><p align="center"><a href="javascript:Search();"><img src="images/btn_search2.gif" width="50" height="25" border="0" align=absmiddle hspace="2"></a></td>
						</tr>
						</table>
						
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
				<table cellpadding="0" cellspacing="0" width="100%" height="594">
				<tr>
					
					
					<td width="100%" valign="top" height="100%" onmouseover="divAction(document.getElementById('cateidx'),0);"><DIV style="position:relative;z-index:1;width:100%;height:100%;bgcolor:#FFFFFF;"><IFRAME name="ListFrame" src="product_allupdate.list_t.php" width=100% height=594 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></div></td>
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
			</form>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>메뉴얼</p></div>
						<dl>
							<dt><span>상품 일괄 간편수정 주의사항</span></dt>
							<dd>
							- 시중가, 구입가, 판매가, 적립금, 수량 입력시 콤마(,)는 입력하지 마세요.
							</dd>
								
						</dl>
						<dl>
							<dt><span>상품 일괄 간편수정 방법</span></dt>
							<dd>
							① 상품보기 선택에 따라 카테고리 선택 또는 상품명으로 검색합니다.<br>
							② 출력된 상품들 중 수정을 원하는 상품만 입력내용을 수정합니다.<Br>
							③ 수정이 완료 됐으며 [적용하기] 버튼을 클릭합니다.
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
//include("codeinit.php");
include("copyright.php");
