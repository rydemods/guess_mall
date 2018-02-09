<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-5";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

if($_shopdata->estimate_ok=="N") {
	echo "
		<script>
		if(confirm('견적서가 설정되어 있지 않습니다.\\n\\n상품 견적서 기능설정 페이지로 이동하시겠습니까?')) {
			try {
				parent.topframe.GoMenu(1,'shop_estimate.php');
			} catch(e) {
				history.go(-1);
			}
		} else {
			history.go(-1);
		}
		</script>
	";
	exit;
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
var code="";
var estlist=new Array();
var old_selcode="";
function CodeProcessFun(_code) {
	old_selcode=_code;
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		document.all["code_top"].style.background="#dddddd";
		selcode="";
		seltype="";
		old_selcode="";

		if(_code!="out") {
			BodyInit2('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		BodyInit2(_code);
	}
}

var fontcolorid = "";
var temphtml="";
function BodyInit2(codeValue) {
	if(codeValue.length==0 || CodeInit_cnt==0) { 
		document.getElementById('code_list').innerHTML="";
		for(i=0;i<all_list.length;i++) {
			isest=false;
			for(k=0;k<estlist.length;k++) {
				if(all_list[i].code.substring(0,3)==estlist[k].substring(0,3)) {
					if(all_list[i].code==selcode) {
						selcode="";
					}
					isest=true;
					break;
				}
			}
			fontcolor="";
			filter="";
			if(isest) {
				fontcolor="#cacaca";
				filter="Alpha(Opacity=60) Gray";
			}
			tmpcode=all_list[i].code;
			plusimg="<img width=11 height=0><img id=\"img_"+tmpcode+"\" src=\"images/directory_folder_close.gif\" align=absmiddle onclick=\"ChangeCloseOpen('"+tmpcode+"');\"> ";
			if(all_list[i].ArrCodeB.length<=0) plusimg="<img width=20 height=0>";
			else if(all_list[i].display=="show" && all_list[i].open=="open") {
				plusimg="<img width=11 height=0><img id=\"img_"+tmpcode+"\" src=\"images/directory_folder_open.gif\" align=absmiddle onclick=\"ChangeCloseOpen('"+tmpcode+"');\"> ";
				all_list[i].open="open";
			}

			strcodename=all_list[i].code_name;
			if(all_list[i].list_type.substring(0,1)=="B") {
				strcodename+="(공구형)";
			}
			folder_gbn="1";
			if(all_list[i].type.substring(1,2)=="X") folder_gbn="3";
			if(all_list[i].type.substring(0,1)=="T") folder_gbn+="T";
			fontbgcolor="#FFFFFF";
			if(all_list[i].selected) {
				fontbgcolor="#dddddd";
				fontcolorid=tmpcode;
			}
			temphtml=plusimg+" <img src=\"images/directory_folder"+folder_gbn+".gif\" align=absmiddle> <span id=\"span_"+tmpcode+"\" style=\"cursor:default;background-color:"+fontbgcolor+"\" onmouseover=\"this.className='link_over'\" onmouseout=\"this.className='link_out'\" onclick=\"ChangeSelect('"+tmpcode+"')\">"+strcodename+"</span>";
			tempdisplay="";
			addCodeDiv2("div_"+tmpcode,temphtml,tempdisplay,fontcolor,filter);

			for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
				isest=false;
				for(k=0;k<estlist.length;k++) {
					if(all_list[i].ArrCodeB[ii].code.substring(0,6)==estlist[k].substring(0,6) || (all_list[i].ArrCodeB[ii].code.substring(0,3)==estlist[k].substring(0,3) && estlist[k].substring(3,6)=="000")) {
						if(all_list[i].ArrCodeB[ii].code==selcode) {
							selcode="";
						}
						isest=true;
						break;
					}
				}
				fontcolor="";
				filter="";
				if(isest) {
					fontcolor="#cacaca";
					filter="Alpha(Opacity=60) Gray";
				}

				tmpcode=all_list[i].ArrCodeB[ii].code;
				plusimg="<img width=29 height=0><img id=\"img_"+tmpcode+"\" src=\"images/directory_folder_close.gif\" align=absmiddle onclick=\"ChangeCloseOpen('"+tmpcode+"');\"> ";
				if(all_list[i].ArrCodeB[ii].ArrCodeC.length<=0) {
					plusimg="<img width=38 height=0>";
				} else if(all_list[i].ArrCodeB[ii].display=="show" && all_list[i].ArrCodeB[ii].open=="open") {
					plusimg="<img width=29 height=0><img id=\"img_"+tmpcode+"\" src=\"images/directory_folder_open.gif\" align=absmiddle onclick=\"ChangeCloseOpen('"+tmpcode+"');\"> ";
					all_list[i].ArrCodeB[ii].open="open";
				}
				strcodename=all_list[i].ArrCodeB[ii].code_name;
				if(all_list[i].ArrCodeB[ii].list_type.substring(0,1)=="B") {
					strcodename+="(공구형)";
				}
				folder_gbn="1";
				if(all_list[i].ArrCodeB[ii].type.substring(2,3)=="X") folder_gbn="3";
				if(all_list[i].ArrCodeB[ii].type.substring(0,1)=="T") folder_gbn+="T";

				fontbgcolor="#FFFFFF";
				if(all_list[i].ArrCodeB[ii].selected) {
					fontbgcolor="#dddddd";
					fontcolorid=tmpcode;
				}
				temphtml=plusimg+" <img src=\"images/directory_folder"+folder_gbn+".gif\" align=absmiddle> <span id=\"span_"+tmpcode+"\" style=\"cursor:default;background-color:"+fontbgcolor+"\" onmouseover=\"this.className='link_over'\" onmouseout=\"this.className='link_out'\" onclick=\"ChangeSelect('"+tmpcode+"')\">"+strcodename+"</span>";
				tempdisplay="none";
				if(all_list[i].ArrCodeB[ii].display!="none") tempdisplay="";
				addCodeDiv2("div_"+tmpcode,temphtml,tempdisplay,fontcolor,filter);

				for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
					isest=false;
					for(k=0;k<estlist.length;k++) {
						if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].code.substring(0,9)==estlist[k].substring(0,9) || (all_list[i].ArrCodeB[ii].ArrCodeC[iii].code.substring(0,3)==estlist[k].substring(0,3) && estlist[k].substring(3,6)=="000") || (all_list[i].ArrCodeB[ii].ArrCodeC[iii].code.substring(0,6)==estlist[k].substring(0,6) && estlist[k].substring(6,9)=="000")) {
							if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].code==selcode) {
								selcode="";
							}
							isest=true;
							break;
						}
					}
					fontcolor="";
					filter="";
					if(isest) {
						fontcolor="#cacaca";
						filter="Alpha(Opacity=60) Gray";
					}

					tmpcode=all_list[i].ArrCodeB[ii].ArrCodeC[iii].code;
					plusimg="<img width=48 height=0><img id=\"img_"+tmpcode+"\" src=\"images/directory_folder_close.gif\" align=absmiddle onclick=\"ChangeCloseOpen('"+tmpcode+"');\"> ";
					if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length<=0) {
						plusimg="<img width=57 height=0>";
					} else if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].display=="show" && all_list[i].ArrCodeB[ii].ArrCodeC[iii].open=="open") {
						plusimg="<img width=48 height=0><img id=\"img_"+tmpcode+"\" src=\"images/directory_folder_open.gif\" align=absmiddle onclick=\"ChangeCloseOpen('"+tmpcode+"');\"> ";
						all_list[i].ArrCodeB[ii].ArrCodeC[iii].open="open";
					}
					strcodename=all_list[i].ArrCodeB[ii].ArrCodeC[iii].code_name;
					if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].list_type.substring(0,1)=="B") {
						strcodename+="(공구형)";
					}
					folder_gbn="1";
					if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].type.substring(2,3)=="X") folder_gbn="3";
					if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].type.substring(0,1)=="T") folder_gbn+="T";

					fontbgcolor="#FFFFFF";
					if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].selected) {
						fontbgcolor="#dddddd";
						fontcolorid=tmpcode;
					}
					temphtml=plusimg+" <img src=\"images/directory_folder"+folder_gbn+".gif\" align=absmiddle> <span id=\"span_"+tmpcode+"\" style=\"cursor:default;background-color:"+fontbgcolor+"\" onmouseover=\"this.className='link_over'\" onmouseout=\"this.className='link_out'\" onclick=\"ChangeSelect('"+tmpcode+"')\">"+strcodename+"</span>";
					tempdisplay="none";
					if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].display!="none") tempdisplay="";
					addCodeDiv2("div_"+tmpcode,temphtml,tempdisplay,fontcolor,filter);
					
					if(CodeInit_cnt==0 && codeValue.substring(0,9)==tmpcode.substring(0,9)) {
						tempdisplaydefault="";
					} else {
						tempdisplaydefault="none";
					}

					for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
						isest=false;
						for(k=0;k<estlist.length;k++) {
							if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code==estlist[k] || (all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code.substring(0,3)==estlist[k].substring(0,3) && estlist[k].substring(3,6)=="000") || (all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code.substring(0,6)==estlist[k].substring(0,6) && estlist[k].substring(6,9)=="000") || (all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code.substring(0,9)==estlist[k].substring(0,9) && estlist[k].substring(9,12)=="000")) {
								if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code==selcode) {
									selcode="";
								}
								isest=true;
								break;
							}
						}
						fontcolor="";
						filter="";
						if(isest) {
							fontcolor="#cacaca";
							filter="Alpha(Opacity=60) Gray";
						}

						tmpcode=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code;
						plusimg="<img width=76 height=0>";
						strcodename=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code_name;
						if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type.substring(0,1)=="B") {
							strcodename+="(공구형)";
						}

						folder_gbn="3";
						if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].type.substring(1,1)=="T") {
							folder_gbn+="T";
						}

						fontbgcolor="#FFFFFF";
						if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].selected) {
							fontbgcolor="#dddddd";
							fontcolorid=tmpcode;
						}
						temphtml=plusimg+" <img src=\"images/directory_folder"+folder_gbn+".gif\" align=absmiddle> <span id=\"span_"+tmpcode+"\" style=\"cursor:default;background-color:"+fontbgcolor+"\" onmouseover=\"this.className='link_over'\" onmouseout=\"this.className='link_out'\" onclick=\"ChangeSelect('"+tmpcode+"')\">"+strcodename+"</span>";
						tempdisplay=tempdisplaydefault;
						if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].display!="none") tempdisplay="";
						addCodeDiv2("div_"+tmpcode,temphtml,tempdisplay,fontcolor,filter);
					}
				}
			}
		}
	}
}

function addCodeDiv2(id,html,display,fontcolor,filter) {
	var newDiv=document.createElement("div"); 
	newDiv.id=id;
	newDiv.style.display=display;
	newDiv.style.height="17";
	if(fontcolor.length>0) {
		newDiv.style.color=fontcolor;
	}
	if(filter.length>0) {
		newDiv.style.filter=filter;
	}
	newDiv.innerHTML=html;
	document.getElementById('code_list').appendChild(newDiv); 
}

function ProcessResult(gbn,_code) {	//추가/삭제 후 견적서 카테고리 배열에서 추가/삭제
	if(gbn=="insert") {
		estlist[estlist.length]=_code;
	} else if(gbn=="delete") {
		selcode=old_selcode;
		tmpestlist=new Array();
		y=0;
		for(k=0;k<estlist.length;k++) {
			if(estlist[k]!=_code) {
				tmpestlist[y]=estlist[k];
				y++;
			}
		}
		estlist=new Array();
		estlist=tmpestlist;
	}
	BodyInit2('');
}

function SendMode(mode) {
	if ((selcode.length!=12 || selcode=="000000000000") && mode=="insert") {
		alert("견적서 카테고리 목록에 추가할 등록 가능한 카테고리를 선택하세요.");
		return;
	}
	if (mode=="insert") {
		if (confirm("견적서 카테고리를 추가하시겠습니까?")) {
			document.form2.mode.value=mode;
			document.form2.etccode.value=selcode;
			document.form2.target="ListFrame";
			document.form2.action="product_estimate.list.php";
			document.form2.submit();
		}
	} else if (mode=="delete"){
		if(ListFrame.form1.est.selectedIndex==-1) {
			alert("삭제할 견적서 카테고리를 선택하세요.");
			ListFrame.form1.est.focus();
			return;
		}

		document.form2.etccode.value=ListFrame.form1.est.options[ListFrame.form1.est.selectedIndex].value;
		if (confirm("선택된 견적서 카테고리를 삭제하시겠습니까?")) {
			document.form2.target="ListFrame";
			document.form2.action="product_estimate.list.php";
			document.form2.mode.value=mode;
			document.form2.submit();
		}
	}
}

</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 310;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 사은품/견적/기타관리 &gt;<span>견적서 상품 등록/관리</span></p></div></div>
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
					<div class="title_depth3">견적서 상품 등록/관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 카테고리별 견적서 관리할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER["PHP_SELF"]?>" method=post>
			<input type=hidden name=mode value="">
			<input type=hidden name=code>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="242" valign="top" height="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="232" height="100%" valign="top" >
						<table cellpadding="0" cellspacing="0" width="242">
						<tr>
							<td bgcolor="white">
								<!-- 소제목 -->
								<div class="title_depth3_sub">전체 카테고리</div>
							</td>
						</tr>
						<tr>
							<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;" class="bd_editer">
								<DIV class=MsgrScroller id=contentDiv style="width=99%;height:394px;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
									<DIV id=bodyList>
										<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
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
							</td>
						</tr>
						</table>
						</td>
					</tr>
					
					</table>
					</td>
					<td><a href="javascript:SendMode('insert')"><img src="images/icon_nero1.gif" border="0"></a><br><br><a href="javascript:SendMode('delete')"><img src="images/icon_nero2.gif" border="0" vspace="10"></a></td>
					<td width="100%" valign="top" height="100%">
					<table cellpadding="0" cellspacing="0" width="100%" height="100%">
					<tr>
						<td width="100%" bgcolor="white">
							<div class="title_depth3_sub">등록된 카테고리 목록</div>
						</td>
					</tr>
					<tr>
						<td width="100%" height="100%" valign="top" style="BORDER-bottom:#0F8FCB 2px solid;BORDER-top:#0F8FCB 2px solid;" bgcolor=red><IFRAME name="ListFrame"
						id="ListFrame" src="product_estimate.list.php" width=100% height=100% frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			</form>
			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=etccode>
			</form>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>견적서 상품 등록/관리</span></dt>
							<dd>
								- 등록 카테고리 목록의 진열순서 변경을 했을 경우 [저장하기]를 누르셔야만 적용됩니다.<br>
								- 등록된 카테고리는 복수 등록이 되지 않습니다.<br>
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
$sql = "SELECT code_a||code_b||code_c||code_d as code FROM tblproductcode ";
$sql.= "WHERE estimate_set!=999 ";
$result=pmysql_query($sql,get_db_conn());
echo "<script>\n";
$k=0;
while($row=pmysql_fetch_object($result)) {
	echo "estlist[{$k}]='{$row->code}';\n";
	$k++;
}
echo "</script>\n";
pmysql_free_result($result);

$sql = "SELECT * FROM tblproductcode WHERE type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' ";
$sql.= "ORDER BY sequence DESC ";
include("codeinit.php");
include("copyright.php");
