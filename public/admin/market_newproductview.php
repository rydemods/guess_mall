<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$quickmenu = array("U","001","002","003","004");

$type=$_POST["type"];
$checkadd=$_POST["checkadd"];
$x_to=$_POST["x_to"];
$y_to=$_POST["y_to"];
$scroll_auto=$_POST["scroll_auto"];
$prdt_cnt=$_POST["prdt_cnt"];
$design=$_POST["design"];
$content=$_POST["content"];

if($type=="insert") {
	if($checkadd=="Y") {
		$sql = "SELECT * FROM tbldesignnewpage WHERE type='r_banner' ";
		$result = pmysql_query($sql,get_db_conn());
		$rows=pmysql_num_rows($result);
		pmysql_free_result($result);
		if($rows<=0) {
			if(ord($x_to)==0) $x_to=0;
			if(ord($y_to)==0) $y_to=0;
			if(ord($scroll_auto)==0) $scroll_auto="Y";
			if(ord($prdt_cnt)==0) $prdt_cnt=5;
			if($design!="U") $content="";

			$subject=$x_to."".$y_to;
			$filename=$design."AA";

			$sql = "INSERT INTO tbldesignnewpage(
			type		,
			subject		,
			filename	,
			leftmenu	,
			body		,
			code) VALUES (
			'r_banner', 
			'{$subject}', 
			'{$filename}', 
			'{$scroll_auto}', 
			'{$content}', 
			'{$prdt_cnt}')";
			pmysql_query($sql,get_db_conn());
			$onload="<script>window.onload=function(){ alert(\"설정이 완료되었습니다.\"); }</script>";
		}
	} else {
		$onload="<script>window.onload=function(){ alert(\"설정이 완료되었습니다.\"); }</script>";
	}
} else if($type=="modify") {
	if($checkadd=="Y") {
		if(ord($x_to)==0) $x_to=0;
		if(ord($y_to)==0) $y_to=0;
		if(ord($scroll_auto)==0) $scroll_auto="Y";
		if(ord($prdt_cnt)==0) $prdt_cnt=5;
		if($design!="U") $content="";

		$subject=$x_to."".$y_to;
		$filename=$design."AA";

		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "subject		= '{$subject}', ";
		$sql.= "filename	= '{$filename}', ";
		$sql.= "leftmenu	= '{$scroll_auto}', ";
		$sql.= "body		= '{$content}', ";
		$sql.= "code		= '{$prdt_cnt}' ";
		$sql.= "WHERE type='r_banner' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert(\"설정이 완료되었습니다.\"); }</script>";
	} else {	//삭제
		$sql = "DELETE FROM tbldesignnewpage WHERE type='r_banner' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert(\"설정이 완료되었습니다.\"); }</script>";
	}
}

$sql = "SELECT * FROM tbldesignnewpage WHERE type='r_banner' ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$tmp=explode("",$row->subject);
	$x_to=$tmp[0];	//왼쪽위치
	$y_to=$tmp[1];	//위쪽위치
	
	$tmp=explode("",$row->filename);
	$design=$tmp[0];	//디자인
	$prdt_type=$tmp[1];	//상품타입(AA:이미지,AB:이미지+상품명)

	$scroll_auto=$row->leftmenu;	//스크롤 타입
	$content=$row->body;			//개별디자인 내용
	$prdt_cnt=$row->code;			//상품갯수 (1~9)

	$checkadd="Y";
	$type="modify";
} else {
	$scroll_auto="Y";
	$prdt_cnt=5;
	$checkadd="N";
	$type="insert";
}
pmysql_free_result($result);

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="Javascript1.2" src="htmlarea/editor.js"></script>
<script language="JavaScript">
_editor_url = "htmlarea/";
var quickcnt = <?=count($quickmenu)?>;
function ChangeEditer(mode,obj){
	if (mode==form1.htmlmode.value) {
		return;
	} else {
		obj.checked=true;
		editor_setmode('content',mode);
	}
	form1.htmlmode.value=mode;
}

function CheckForm(type) {
	if(document.form1.x_to.value.length==0 || document.form1.y_to.value.length==0) {
		alert("최근 본 상품의 위치 설정을 하세요.");
		document.form1.x_to.focus();
		return;
	}
	if(!IsNumeric(document.form1.x_to.value)) {
		alert("최근 본 상품의 위치 설정값은 숫자만 입력 가능합니다.");
		document.form1.x_to.focus();
		return;
	}
	if(!IsNumeric(document.form1.y_to.value)) {
		alert("최근 본 상품의 위치 설정값은 숫자만 입력 가능합니다.");
		document.form1.y_to.focus();
		return;
	}
	if(document.form1.scroll_auto[0].checked==false && document.form1.scroll_auto[1].checked==false) {
		alert("스크롤 타입을 선택하세요.");
		document.form1.scroll_auto[0].focus();
		return;
	}
	design=false;
	designval="";
	for(i=quickcnt;i<document.form1.design.length;i++) {
		if(document.form1.design[i].checked) {
			design=true;
			designval=document.form1.design[i].value;
			break;
		}
	}
	if(!design) {
		alert("최근 본 상품의 템플릿을 선택하세요.");
		return;
	}

	if(designval=="U" && document.form1.content.value.length==0) {
		alert("최근 본 상품의 편집내용을 입력하세요.");
		return;
	}
	if(type=="modify") {
		if(!confirm("최근 본 상품의 기능설정을 하시겠습니까?")) {
			return;
		}
	}
	document.form1.type.value=type;
	document.form1.submit();
}

function ChangeDesign(tmp) {
	tmp0=tmp;
	tmp=tmp + quickcnt;
	document.form1["design"][tmp].checked=true;
	if(tmp0==0) {
		document.all["layer1"].style.display="block";
		//document.all["layer2"].style.display="block";
	} else {
		document.all["layer1"].style.display="none";
		//document.all["layer2"].style.display="none";
	}
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>최근 본 상품 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			
			<input type=hidden name=type value="<?=$type?>">
			<input type=hidden name=htmlmode value='wysiwyg'>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">최근 본 상품 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>선택한 상품이 쇼핑몰 우측에 배너형식으로 따라다니면서 보여주는 기능 입니다. 쇼핑몰의 오른쪽 움직이는 Quick메뉴 창을 통하여 최근 기준으로 최대 9개의 상품이 노출됩니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">최근본 상품 기능 적용여부</div>
                </td>
            </tr>
            <tr>
            	<td style="padding-top:3pt; padding-bottom:3pt;">                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>Quick메뉴 관리에서 "최근 내가본 상품은 사용안함" 선택시에는 최근 본 상품은 출력되지 않습니다.</li>
                            <li><a href="javascript:parent.topframe.GoMenu(7,'market_quickmenu.php');">마케팅 지원 > 이벤트/사은품 기능 설정 > Quick메뉴 관리</a></li>
                        </ul>
                    </div>                    
            	</td>
            </tr>
			<tr>
				<td>
				<div class="table_style01">
					<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<tr>
							<th><span>최근본 상품 기능</span></th>
							<td>
								<INPUT id=idx_checkadd0 type=radio value=Y <?php if($checkadd=="Y")echo"checked";?> name=checkadd><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_checkadd0>적용하겠습니다.</LABEL>  &nbsp;&nbsp;&nbsp;
								<INPUT id=idx_checkadd1 type=radio value=N <?php if($checkadd=="N")echo"checked";?> name=checkadd><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_checkadd1>적용하지 않겠습니다.</LABEL>
							</td>
						</tr>
					</table>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">최근 본 상품 기능</div>
				</td>
			</tr>
			<tr>
				<td>

				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>최근 본 상품 위치 설정</span></th>
					<TD class="td_con1">왼쪽에서 <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=x_to value="<?=$x_to?>" class="input"> 픽셀 이동 후, 위쪽에서 <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=y_to value="<?=$y_to?>" class="input"> 픽셀 아래로 이동합니다.</TD>
				</TR>
				<TR>
					<th><span>스크롤 선택</span></th>
					<TD class="td_con1">
					<INPUT id=idx_scroll_auto1 type=radio value=Y <?php if($scroll_auto=="Y")echo"checked";?> name=scroll_auto><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_scroll_auto1>화면 스크롤에 맞추어 자동 스크롤</LABEL>  &nbsp;&nbsp;&nbsp;
					<INPUT id=idx_scroll_auto2 type=radio value=N <?php if($scroll_auto=="N")echo"checked";?> name=scroll_auto><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_scroll_auto2>위치 고정</LABEL>&nbsp;
					</TD>
				</TR>
				<TR>
					<th><span>상품 갯수 설정</span></th>
					<TD class="td_con1">
					<SELECT name=prdt_cnt style=width:50px size="1" class="select">
					<option value=1 <?php if($prdt_cnt==1)echo"selected";?>>1개</option>
					<option value=2 <?php if($prdt_cnt==2)echo"selected";?>>2개</option>
					<option value=3 <?php if($prdt_cnt==3)echo"selected";?>>3개</option>
					<option value=4 <?php if($prdt_cnt==4)echo"selected";?>>4개</option>
					<option value=5 <?php if($prdt_cnt==5)echo"selected";?>>5개</option>
					<option value=6 <?php if($prdt_cnt==6)echo"selected";?>>6개</option>
					<option value=7 <?php if($prdt_cnt==7)echo"selected";?>>7개</option>
					<option value=8 <?php if($prdt_cnt==8)echo"selected";?>>8개</option>
					<option value=9 <?php if($prdt_cnt==9)echo"selected";?>>9개</option>
					</SELECT>
					<span class="font_orange">＊고객이 클릭한 최근 상품품을 설정한 갯수만큼 보여주게 됩니다.</span>
					</TD>
				</TR>
				<TR>
					<th colspan="2">

					<div class="table_none">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						<div class="table_none">
						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
						<tr>
							<td>
							<div class="table_none">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD><p align="center"><div class="point_title">템플릿 선택 (＊사이즈 90px 권장)</div></TD>
							</TR>

							<TR>
								<TD width="100%" style="padding:0pt;">
								<div class="table_none">
								<TABLE cellSpacing=0 width="100%" border=0>
								<TR>
									<TD width="31" height="160" valign="middle"><p align="right"><img src="images/btn_back.gif" onMouseover='moveright()' onMouseout='clearTimeout(righttime)' style="cursor:hand;" border="0"></TD>
									<TD height="160">
									<div class="table_none">
									<table width="100%" cellspacing="0" cellpadding="0" border="0">
									<tr height=170>
										<td id=temp style="visibility:hidden;position:absolute;top:0;left:0">
				<?php
										echo "<script>";
										$jj=0;
										$menucontents = "";
										$menucontents .= "<table border=0 cellpadding=0 cellspacing=0><tr>";
										for($i=0;$i<count($quickmenu);$i++) {
											echo "thisSel = 'dotted #FFFFFF';";
											$menucontents .= "<td width='173'><p align='center'><input type=radio name='design' value='{$quickmenu[$i]}'";
											if($design==$quickmenu[$i]) $menucontents .= " checked";
											$menucontents .= " onclick='ChangeDesign({$i});'><img src='images/sample/newproductview{$quickmenu[$i]}.gif' border=0 hspace='5' style='border-width:1pt; border-color:#FFFFFF; border-style:solid;' onMouseOver='changeMouseOver(this);' onMouseOut='changeMouseOut(this,thisSel);' style='cursor:hand;' onclick='ChangeDesign({$i});'></td>";
											$jj++;
										}
										$menucontents .= "</tr></table>";
										echo "</script>";
				?>  

										<script language="JavaScript1.2">
										<!--
										function changeMouseOver(img) {
											 img.style.border='1 dotted #999999';
										}
										function changeMouseOut(img,dot) {
											 img.style.border="1 "+dot;
										}

										var menuwidth=1000
										var menuheight=170
										var scrollspeed=10
										var menucontents="<nobr><?=$menucontents?></nobr>";
										
										var iedom=document.all||document.getElementById
										if (iedom)
											document.write(menucontents)
										var actualwidth=''
										var cross_scroll, ns_scroll
										var loadedyes=0
										function fillup(){
											if (iedom){
												cross_scroll=document.getElementById? document.getElementById("test2") : document.all.test2
												cross_scroll.innerHTML=menucontents
												actualwidth=document.all? cross_scroll.offsetWidth : document.getElementById("temp").offsetWidth
											}
											else if (document.layers){
												ns_scroll=document.ns_scrollmenu.document.ns_scrollmenu2
												ns_scroll.document.write(menucontents)
												ns_scroll.document.close()
												actualwidth=ns_scroll.document.width
											}
											loadedyes=1
										}
										window.onload=fillup
										
										function moveleft(){
											if (loadedyes){
												if (iedom&&parseInt(cross_scroll.style.left)>(menuwidth-actualwidth)){
													cross_scroll.style.left=parseInt(cross_scroll.style.left)-scrollspeed
												}
												else if (document.layers&&ns_scroll.left>(menuwidth-actualwidth))
													ns_scroll.left-=scrollspeed
											}
											lefttime=setTimeout("moveleft()",50)
										}
										
										function moveright(){
											if (loadedyes){
												if (iedom&&parseInt(cross_scroll.style.left)<0)
													cross_scroll.style.left=parseInt(cross_scroll.style.left)+scrollspeed
												else if (document.layers&&ns_scroll.left<0)
													ns_scroll.left+=scrollspeed
											}
											righttime=setTimeout("moveright()",50)
										}
										
										if (iedom||document.layers){
											with (document){
												write('<td valign=top>')
												if (iedom){
													write('<div style="position:relative;width:'+menuwidth+';">');
													write('<div style="position:absolute;width:'+menuwidth+';height:'+menuheight+';overflow:hidden;">');
													write('<div id="test2" style="position:absolute;left:0">');
													write('</div></div></div>');
												}
												else if (document.layers){
													write('<ilayer width='+menuwidth+' height='+menuheight+' name="ns_scrollmenu">')
													write('<layer name="ns_scrollmenu2" left=0 top=0></layer></ilayer>')
												}
												write('</td>')
											}
										}
										//-->
										</script>
										</td>
									</tr>
									</table>
									</div>
									</TD>
									<TD width="31" height="160"><p align="right"><img src="images/btn_next.gif" onMouseover='moveleft()' onMouseout='clearTimeout(lefttime)' style="cursor:hand;" border="0"></TD>
								</TR>
								</TABLE>
								</div>
								</TD>
							</TR>
							</TABLE>
							</div>
							</td>
						</tr>
						</table>
						</div>
						</td>
					</tr>
					</table>
					</div>
					</th>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td class="bd_editer">
					<div id=layer1 style="margin-left:0;display:hide; display:<?=($design=="U"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" height="35">
						<INPUT type=radio id=idx_chk_webedit1 name=chk_webedit CHECKED onclick="JavaScript:ChangeEditer('wysiwyg',this)"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_chk_webedit1>웹편집기로 입력하기</LABEL>  &nbsp;&nbsp;&nbsp;
						<INPUT type=radio id=idx_chk_webedit2 name=chk_webedit onclick="JavaScript:ChangeEditer('textedit',this);"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_chk_webedit2>직접 HTML로 입력하기</LABEL> <BR>
						</td>
					</tr>
					<tr>
						<td width="100%"><TEXTAREA style="DISPLAY: yes; WIDTH: 100%" name=content rows="17" wrap=off class="textarea"><?=$content?></TEXTAREA></td>
					</tr>
					</table>
					</div>
				</td>
			</tr>
			<tr>
				<td class="pt_20" align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>최근 본 상품 관리</span></dt>
							<dd>
								- 최근 내가 본 상품 저장은 최대 30개, 출력은 9개의 상품까지 출력이 가능합니다.<br>
- 최근 내가 본 상품을 [화면 스크롤에 맞추어 자동 스크롤] 또는 [위치고정] 선택할 수 있습니다.<br>
- 다양한 템플릿을 기본으로 지원되며 또한 개별디자인도 가능합니다.
							</dd>
							
						</dl>
						<dl>
							<dt><span class="font_orange">입력 방법은 아래를 참조하세요.</span></dt>
							<dd>
								<table border=0 cellpadding=0 cellspacing=0 width=100%>
								<col width=150></col>
								<col width=></col>
								<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
								<tr>
									<td class=table_cell align=right style="padding-right:15">[NEW]</td>
									<td class=td_con1 style="padding-left:5;">
									최근 본 상품 리스트
									</td>
								</tr>
								<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
								<tr>
									<td class=table_cell align=right style="padding-right:15">[UP]/[DOWN]</td>
									<td class=td_con1 style="padding-left:5;">
									최근 본 상품 위/아래 이동<br><FONT class=font_blue>예:&lt;a href=[UP]>UP&lt;/a>, &lt;a href=[DOWN]>DOWN&lt;/a></font>
									</td>
								</tr>
								<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
								</table>
							</dd>
						</dl>
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			
			</table>
			</form>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<script>
editor_generate('content');
</script>
<?=$onload?>
<?php 
include("copyright.php");
