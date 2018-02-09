<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-2";
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
		design=sel_detail_type;
		if(sel_detail_type.length==6 && sel_detail_type.substring(5,6)=="U") {
			design="DU";
		}

		document.all["preview_img"].src="images/product/"+design+".gif";
		document.all["preview_img"].style.display="";

		document.form2.mode.value="";
		document.form2.type.value=seltype;
		document.form2.code.value=selcode;
		document.form2.detail_type.value=sel_detail_type;
		document.form2.target="MainPrdtFrame";
		document.form2.action="design_pdetail.list.php";
		document.form2.submit();
	} else {	//카테고리 선택시 현재 사용중인 디자인 및 디자인 목록 초기화
		document.all["preview_img"].src="";
		document.all["preview_img"].style.display="none";

		document.form2.mode.value="";
		document.form2.type.value="";
		document.form2.code.value="";
		document.form2.detail_type.value="";
		document.form2.target="MainPrdtFrame";
		document.form2.action="design_pdetail.list.php";
		document.form2.submit();
	}
}

function ModifyCodeDesign(_code,detail_type,is_design) {
	code_a=_code.substring(0,3);
	code_b=_code.substring(3,6);
	code_c=_code.substring(6,9);
	code_d=_code.substring(9,12);
	for(i=0;i<all_list.length;i++) {
		if(code_a!="000" && code_b=="000" && code_c=="000" && code_d=="000") {
			if(all_list[i].code==_code) {
				all_list[i].detail_type=detail_type;
				if(is_design==1) {
					for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
						all_list[i].ArrCodeB[ii].detail_type=detail_type;
						for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
							all_list[i].ArrCodeB[ii].ArrCodeC[iii].detail_type=detail_type;
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
							}
						}
					}
				}
				return;
			}
		} else {
			for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
				if(code_a!="000" && code_b!="000" && code_c=="000" && code_d=="000") {
					if(all_list[i].ArrCodeB[ii].code==_code) {
						all_list[i].ArrCodeB[ii].detail_type=detail_type;
						if(is_design==1) {
							for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].detail_type=detail_type;
								for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
									all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
								}
							}
						}
						return;
					}
				} else {
					for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
						if(code_a!="000" && code_b!="000" && code_c!="000" && code_d=="000") {
							if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].code==_code) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].detail_type=detail_type;
								if(is_design==1) {
									for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
									}
								}
								return;
							}
						} else {
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								if(code_a!="000" && code_b!="000" && code_c!="000" && code_d!="000") {
									if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code==_code) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
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

function CheckForm() {
	if(confirm("선택하신 디자인으로 변경하시겠습니까?")) {
		document.form1.type.value="update";
		document.form1.submit();
	}
}

function design_preview(design) {
	document.all["preview_img"].src="images/product/"+design+".gif";
}

</script>
<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 300;HEIGHT: 250;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 템플릿-메인, 카테고리 &gt;<span>상품 상세화면 템플릿</span></p></div></div>
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
					<div class="title_depth3">상품 상세화면 템플릿</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 상품 상세화면 디자인을 선택하여 사용하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품 상세화면 템플릿디자인</div>
				</td>
			</tr>
			<tr>
				<td height=3></td>
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
					<TD><div class="point_title03">현재 운영중인 상품 상세화면 템플릿</span></div></TD>
				</TR>
				<TR>
					<TD bgcolor="#f8f8f8" valign="top" style="padding:8pt;" width="48%"><button title="전체 트리확장" id="btn_treeall" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="AllOpen();"><IMG SRC="images/category_btn1.gif" border="0"></button>
					<DIV class=MsgrScroller id=contentDiv style="OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
					<DIV id=bodyList>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">상품 카테고리를 선택하세요</span></td>
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
					<TD align="center" bgcolor="#f8f8f8" style="padding:5pt;" width="48%"><p align="center">&nbsp;<img id="preview_img" style="display:none" border="0" vspace="0" class="imgline"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td><IFRAME name="MainPrdtFrame" src="design_pdetail.list.php" width=100% height=350 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>템플릿 선택</span></dt>
					  <dd>- <a href="javascript:parent.topframe.GoMenu(1,'product_code.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 카테고리 관리</span></a> 에서 각각 템플릿을 선택할 수 있습니다.<br />- 두 개의 메뉴에서 동시에 템플릿 선택이 가능하며 최종 적용한 메뉴의 설정이 적용됩니다.</dd>
                    </dl>
                    <dl>
                    	<dt><span>개별 디자인</span></dt>
                        <dd>- <a href="javascript:parent.topframe.GoMenu(2,'design_eachpdetail.php');"><span class="font_blue">디자인관리 > 개별디자인 - 페이지 본문 > 상품상세 화면 꾸미기</span></a> 에서 개별 디자인을 할 수 있습니다.<br />- 개별 디자인 사용시 템플릿은 적용되지 않습니다.</dd>
                    </dl>
                    <dl>
                    	<dt><span>템플릿 재적용</span></dt>
                        <dd>- 본 메뉴에서 원하는 템플릿으로 재선택하면 개별디자인은 해제되고 선택한 템플릿으로 적용됩니다.<br />- 개별디자인에서 [기본값복원] 또는 [삭제하기] -> 기본 템플릿으로 변경됨 -> 원하는  템플릿을 선택하시면 됩니다.</dd>
                    </dl>
                </div>				
                </td>
			</tr>
			<tr>
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
<form name=form2 action="" method=post>
<input type=hidden name=mode>
<input type=hidden name=code>
<input type=hidden name=type>
<input type=hidden name=detail_type>
</form>
</table>
<?=$onload?>
<?php
$sql = "SELECT * FROM tblproductcode ORDER BY sequence DESC ";
include("codeinit.php");
include("copyright.php"); 
