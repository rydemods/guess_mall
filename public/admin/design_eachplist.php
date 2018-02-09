<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-5";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
var code="<?=$code?>";
function CodeProcessFun(_code) {
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		document.all["code_top"].style.background="#dddddd";
		selcode="";
		seltype="";
		sel_list_type="";
		sel_detail_type="";

		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		BodyInit(_code);
	}

	if(selcode.length==12 && selcode!="000000000000") {	//카테고리 선택시 현재 사용중인 디자인 및 디자인 목록 새로고침
		design=sel_list_type;
		if(sel_list_type.length==6 && sel_list_type.substring(5,6)=="U") {
			design="LU";
		}
		document.all["preview_img"].src="images/product/"+design+".gif";
		document.all["preview_img"].style.display="";

		document.form2.mode.value="";
		document.form2.type.value=seltype;
		document.form2.code.value=selcode;
		document.form2.list_type.value=sel_list_type;
		document.form2.target="MainPrdtFrame";
		document.form2.action="design_eachplist.list.php";
		document.form2.submit();
	} else {	//카테고리 선택시 현재 사용중인 디자인 및 디자인 목록 초기화
		document.all["preview_img"].src="";
		document.all["preview_img"].style.display="none";

		document.form2.mode.value="";
		document.form2.type.value="";
		document.form2.code.value="";
		document.form2.list_type.value="";
		document.form2.target="MainPrdtFrame";
		document.form2.action="design_eachplist.list.php";
		document.form2.submit();
	}
}

function ModifyCodeDesign(_code,list_type,is_design) {
	code_a=_code.substring(0,3);
	code_b=_code.substring(3,6);
	code_c=_code.substring(6,9);
	code_d=_code.substring(9,12);
	for(i=0;i<all_list.length;i++) {
		if(code_a!="000" && code_b=="000" && code_c=="000" && code_d=="000") {
			if(all_list[i].code==_code) {
				all_list[i].list_type=list_type;
				if(is_design==1) {
					for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
						all_list[i].ArrCodeB[ii].list_type=list_type;
						for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
							all_list[i].ArrCodeB[ii].ArrCodeC[iii].list_type=list_type;
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
							}
						}
					}
				}
				ChangeSelect(_code);
				return;
			}
		} else {
			for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
				if(code_a!="000" && code_b!="000" && code_c=="000" && code_d=="000") {
					if(all_list[i].ArrCodeB[ii].code==_code) {
						all_list[i].ArrCodeB[ii].list_type=list_type;
						if(is_design==1) {
							for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].list_type=list_type;
								for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
									all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
								}
							}
						}
						ChangeSelect(_code);
						return;
					}
				} else {
					for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
						if(code_a!="000" && code_b!="000" && code_c!="000" && code_d=="000") {
							if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].code==_code) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].list_type=list_type;
								if(is_design==1) {
									for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
									}
								}
								ChangeSelect(_code);
								return;
							}
						} else {
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								if(code_a!="000" && code_b!="000" && code_c!="000" && code_d!="000") {
									if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code==_code) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
										ChangeSelect(_code);
										return;
									}
								}
							}
						}
					}
				}
			}
		}
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

</script>
<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 300;HEIGHT: 250;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-메인, 카테고리 &gt;<span>상품 카테고리 꾸미기</span></p></div></div>
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
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 카테고리 꾸미기</div>
                	<div class="title_depth3_sub"><span>상품 카테고리별로 자유롭게 디자인을 할 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품 카테고리별 개별디자인</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD>
					<div class="point_title02">전체 카테고리</div>
                    </TD>
					<TD>&nbsp;</TD>
					<TD><div class="point_title03">현재 상품 카테고리별 템플릿</span></div></TD>
				</TR>
				<TR>
					<TD bgcolor="#f8f8f8" valign="top" style="padding:8pt;" width="48%"><button title="전체 트리확장" id="btn_treeall" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="AllOpen();"><IMG SRC="images/category_btn1.gif" border="0"></button>
					<DIV class=MsgrScroller id=contentDiv style="OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
					<DIV id=bodyList>
					<table border=0 cellpadding=0 cellspacing=0 width="100%">
					<tr>
						<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">모든 카테고리 일괄 개별디자인</span></td>
					</tr>
					<tr>
						<!-- 상품카테고리 목록 -->
						<td id="code_list" nowrap></td>
						<!-- 상품카테고리 목록 끝 -->
					</tr>
					</table>
					</DIV>
					</DIV>
					</TD>
                    <TD align="center" width="55"><img src="images/btn_next1.gif" border="0" hspace="5"></TD>
					<TD align="center" bgcolor="#f8f8f8" style="padding:5pt;" width="48%"><p align="center"><b>&quot;모든 카테고리 일괄 개별디자인&quot; </b>을 적용할 경우 개별 디자인 사용중인 카테고리를 제외한 템플릿을 사용하는 모든 카테고리가 개별디자인으로 일괄 변경됩니다.<br><img id="preview_img" style="display:none" border="0" vspace="5" class="imgline"><p align="left"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td><IFRAME name="MainPrdtFrame" id="MainPrdtFrame" src="design_eachplist.list.php" width=100% height=350 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
			</tr>
			</form>
			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<input type=hidden name=type>
			<input type=hidden name=list_type>
			</form>
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
<?=$onload?>
<?php
$sql = "SELECT * FROM tblproductcode ORDER BY sequence DESC ";
include("codeinit.php");
include("copyright.php"); 
