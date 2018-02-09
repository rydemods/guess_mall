<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

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

	if(selcode.length==12 && selcode!="000000000000") {
		document.form2.mode.value="";
		document.form2.code.value=selcode;
		document.form2.target="ListFrame";
		document.form2.action="product_assemble.list.php";
		if(document.ListFrame.form1)
			document.form2.Scrolltype.value = document.ListFrame.form1.Scrolltype.value
		document.form2.submit();
	} else {
		document.form2.mode.value="";
		document.form2.code.value="";
		document.form2.target="ListFrame";
		document.form2.action="product_assemble.list.php";
		if(document.ListFrame.form1)
			document.form2.Scrolltype.value = document.ListFrame.form1.Scrolltype.value
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

function assembleFormSubmit() {
	form = document.form1;
	
	if(form.assemble_title.selectedIndex<0) {
		document.assembleForm.assemble_title_code.value = "";
		document.assembleForm.assemble_title_list.value = "";
		document.assembleForm.assemble_basic_code.value = "";
	} else {
		document.assembleForm.assemble_title_code.value = form.assemble_title.selectedIndex;
		assemble_array_num = form.assemble_title.options[form.assemble_title.selectedIndex].value.split('');
		document.assembleForm.assemble_title_list.value = assemble_array_num[2];
		document.assembleForm.assemble_basic_code.value = assemble_array_num[3];
	}
	document.assembleForm.submit();
}

function assembleuse_change() {
	form = document.form1;
	if(document.getElementById("assembleidx")) {
		if(form.assembleuse[0].checked) {
			document.getElementById("assembleidx").style.display="none";
		} else if(form.assembleuse[1].checked) {
			document.getElementById("assembleidx").style.display="";
		}
	}
}

function assemble_title_change(assembleFormtype) {
	form = document.form1;
	form.assemble_update_type.value="";
	form.assemble_title_name.value="";
	form.assemble_type.checked=true;
	form.assemble_modify_list.value="";
	form.assemble_basic_code.value="";
	if(document.getElementById("btnidx")){
		document.getElementById("btnidx").src = "images/btn_input.gif";
	}

	if(assembleFormtype=="Y") {
		assembleFormSubmit();
	}
}

function assemble_title_move(movetype) {
	form = document.form1;
	if(form) {
		if(form.assemble_title.selectedIndex<0) {
			alert("이동할 구성 상품 타이틀을 선택해 주세요.");
			form.assemble_title.focus();
		} else {
			var moveobj_value = form.assemble_title.options[form.assemble_title.selectedIndex].value;
			var moveobj_text = form.assemble_title.options[form.assemble_title.selectedIndex].text;

			if(movetype=="up") {
				if(form.assemble_title.selectedIndex>0 && form.assemble_title.options[form.assemble_title.selectedIndex-1]) {
					var movego_value = form.assemble_title.options[form.assemble_title.selectedIndex-1].value;
					var movego_text = form.assemble_title.options[form.assemble_title.selectedIndex-1].text;
					form.assemble_title.options[form.assemble_title.selectedIndex-1].value=moveobj_value;
					form.assemble_title.options[form.assemble_title.selectedIndex-1].text=moveobj_text;
					form.assemble_title.options[form.assemble_title.selectedIndex].value=movego_value;
					form.assemble_title.options[form.assemble_title.selectedIndex].text=movego_text;
					form.assemble_title.options[form.assemble_title.selectedIndex].selected=false;
					form.assemble_title.options[form.assemble_title.selectedIndex-1].selected=true;
				}
			} else {
				if(form.assemble_title.options[form.assemble_title.selectedIndex+1]) {
					var movego_value = form.assemble_title.options[form.assemble_title.selectedIndex+1].value;
					var movego_text = form.assemble_title.options[form.assemble_title.selectedIndex+1].text;
					form.assemble_title.options[form.assemble_title.selectedIndex+1].value=moveobj_value;
					form.assemble_title.options[form.assemble_title.selectedIndex+1].text=moveobj_text;
					form.assemble_title.options[form.assemble_title.selectedIndex].value=movego_value;
					form.assemble_title.options[form.assemble_title.selectedIndex].text=movego_text;
					form.assemble_title.options[form.assemble_title.selectedIndex].selected=false;
					form.assemble_title.options[form.assemble_title.selectedIndex+1].selected=true;
				}
			}
			assemble_title_change('');
		}
	}
}

function assemble_title_delete() {
	form = document.form1;
	if(form) {
		if(form.assemble_title.selectedIndex<0) {
			alert("삭제할 구성 상품 타이틀을 선택해 주세요.");
			form.assemble_title.focus();
		} else {
			if(confirm("선택된 구성 상품 타이틀을 삭제 하겠습니까?")) {
				form.assemble_title.options[form.assemble_title.selectedIndex]=null;
				assemble_title_change('Y');
			}
		}
	}
}

function assemble_title_modify() {
	form = document.form1;
	if(form) {
		if(form.assemble_title.selectedIndex<0) {
			alert("수정할 구성 상품 타이틀을 선택해 주세요.");
			form.assemble_title.focus();
		}
		else {
			form.assemble_update_type.value="modify";
			assemble_array_num=form.assemble_title.options[form.assemble_title.selectedIndex].value.split('');
			
			form.assemble_type.checked=(assemble_array_num[0]=="Y"?true:false);
			form.assemble_title_name.value=assemble_array_num[1];
			form.assemble_modify_list.value=assemble_array_num[2];
			form.assemble_basic_code.value=assemble_array_num[3];

			if(document.getElementById("btnidx")){
				document.getElementById("btnidx").src = "images/btn_modify.gif";
			}
		}
	} else {
		alert("구성 상품 타이틀 추가 중 오류가 발생됐습니다.");
		return;
	}
}

function assemble_title_update() {
	form = document.form1;
	if(form) {
		if(form.assemble_title_name.value.length>0) {
			if(form.assemble_update_type.value=="modify") {
				form.assemble_title.options[form.assemble_title.selectedIndex].value=(form.assemble_type.checked?"Y":"N")+""+form.assemble_title_name.value+""+form.assemble_modify_list.value+""+form.assemble_basic_code.value;
				form.assemble_title.options[form.assemble_title.selectedIndex].text=form.assemble_title_name.value+(form.assemble_type.checked?" : 필수(O)":" : 필수(X)");
			} else {
				form.assemble_title.options[form.assemble_title.length] = new Option(form.assemble_title_name.value+(form.assemble_type.checked?" : 필수(O)":" : 필수(X)"),(form.assemble_type.checked?"Y":"N")+""+form.assemble_title_name.value+""+"", false, false);
			}
			assemble_title_change('');
		} else {
			alert("구성 상품 타이틀을 입력해 주세요.");
			form.assemble_title_name.focus();
			return;
		}
	} else {
		alert("구성 상품 타이틀 추가 중 오류가 발생됐습니다.");
		return;
	}
}

function assemble_list_update(assemblelistproduct,assemblebasicproduct) {
	form = document.form1;
	if(form.assemble_title.selectedIndex<0) {
		alert('구성 상품 타이틀이 선택 안돼 있습니다.');
		form.assemble_title.focus();
		return false;
	} else {
		form.assemble_update_type.value="modify";
		assemble_array_num=form.assemble_title.options[form.assemble_title.selectedIndex].value.split('');
		form.assemble_title.options[form.assemble_title.selectedIndex].value=(assemble_array_num[0]=="Y"?"Y":"N")+""+assemble_array_num[1]+""+assemblelistproduct+""+assemblebasicproduct;
		form.assemble_title.options[form.assemble_title.selectedIndex].text=assemble_array_num[1]+(assemble_array_num[0]=="Y"?" : 필수(O)":" : 필수(X)");
		assemble_title_change('');
		return true;
	}
}
</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 320;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 카테고리/상품관리 &gt;<span>코디/조립 상품 관리</span></p></div></div>
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
					<div class="title_depth3">코디/조립 상품 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>코디/조립 상품으로 등록된 상품의 구성 상품 등록/수정/삭제 할 수 있습니다.<br>구성 상품으로 지정된 상품에서 참조값 : 상품명, 판매가격, 재고량, 상품진열여부</span></div>
				</td>
			</tr>
			
			<tr><td height="20"></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=code>
			<input type=hidden name=Scrolltype>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%" height="1090">
				<tr>
					<td valign="top">
					<!--<DIV onmouseover="divAction(this,2);" id="cateidx" style="position:absolute;z-index:0;width:242px;bgcolor:#FFFFFF;">-->
					<DIV id="cateidx" style="position:absolute;z-index:0;width:242px;bgcolor:#FFFFFF;">
					<table cellpadding="0" cellspacing="0" width="100%" height="1050">
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
							<IMG SRC="images/category_btn1.gif" WIDTH=22 HEIGHT=23 border="0"  onclick="AllOpen();">
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
							<DIV class=MsgrScroller id=contentDiv style="width=99%;height:100%;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
							<DIV id=bodyList>
							<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
							<tr>
								<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('');">최상위 카테고리</span></td>
							</tr>
							<tr>
								<!-- 상품카테고리 목록 -->
								<td id="code_list" nowrap valign=top></td>
								<!-- 상품카테고리 목록 끝 -->
							</tr>
							</table>
							</DIV>
							</DIV>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td><IMG SRC="images/category_boxdown.gif" border="0"></td>
					</tr>
					</table>
					</div>
					</td>
					<td style="padding-left:250px;"></td>
					<!--<td width="100%" valign="top" height="100%" onmouseover="divAction(document.getElementById('cateidx'),0);">-->
					<td width="100%" valign="top" height="100%">
					<DIV style="position:relative;z-index:1;width:100%;height:100%;bgcolor:#FFFFFF;"><IFRAME name="ListFrame" id="ListFrame" src="product_assemble.list.php" width=100% height=1000 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></div></td>
				</tr>
				</table>
				</td>
			</tr>
			<IFRAME name="HiddenFrame" src="/blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			</form>
			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<input type=hidden name=Scrolltype>
			</form>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
				
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>코디/조립상품 검색/등록 주의사항</span></dt>
							<dd>
							  - 코디/조립의 구성으로 지정된 상품에서 참조하는 값은 상품명, 판매가격, 재고량, 상품진열여부만 참조합니다. <br>
							  - 코디/조립의 상품유형 선택은 상품등록/수정 페이지의 판매가격에서 선택 등록해 주세요.<br>
							  - 코디/조립 상품은 구성상품 미등록시 구매가 불가능 합니다.<br>
							  - 코디/조립 상품의 구성상품 구성시 본사의 상품만 등록이 가능 합니다.<br>
							  - 코디/조립상품을 구성하는 상품을 등록/수정/삭제가 가능합니다.<br>
							  - 코디/조립 구성상품 선택시 필수 선택 또는 미필수 선택을 지정할 수 있습니다.<br>
							  - 코디/조립 기본 구성상품이란 코디/조립의 구성상품 중 하나를 기본으로 선택되어 판매 가격에 적용됩니다.
							  
							</dd>	
						</dl>
						
				</div>
				
				
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
