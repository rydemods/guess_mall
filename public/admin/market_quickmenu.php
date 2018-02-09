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

$maxcnt=10;
$quickmenu = array("U","001","002","003","004");

$type=$_POST["type"];
$mode=$_POST["mode"];
$up_quick_type=$_POST["up_quick_type"];
$num=$_POST["num"];
$used=$_POST["used"];
$design=$_POST["design"];
$x_to=$_POST["x_to"];
$y_to=$_POST["y_to"];
$x_size=$_POST["x_size"];
$y_size=$_POST["y_size"];
$scroll_auto=$_POST["scroll_auto"];
$title=$_POST["title"];
$content=$_POST["content"];
if(ord($used)==0) $used="N";

$quick_type=(int)$_shopdata->quick_type;

if($mode=="update" && ord($up_quick_type)) {
	if($quick_type!=$up_quick_type) {
		$sql = "UPDATE tblshopinfo SET quick_type = '{$up_quick_type}' ";
		pmysql_query($sql,get_db_conn());
		$quick_type=$up_quick_type;

		DeleteCache("tblshopinfo.cache");
	}
	$onload="<script>window.onload=function(){ alert('최근 본 상품 사용여부 설정이 완료되었습니다.'); }</script>";
}

if($type=="insert") {
	$sql = "SELECT COUNT(*) as cnt FROM tblquickmenu ";
	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($row->cnt<$maxcnt) {
		$sql = "INSERT INTO tblquickmenu(
		used		,
		reg_date	,
		design		,
		x_size		,
		y_size		,
		x_to		,
		y_to		,
		scroll_auto	,
		title		,
		content) VALUES (
		'N', 
		'".date("YmdHis")."', 
		'{$design}', 
		'{$x_size}', 
		'{$y_size}', 
		'{$x_to}', 
		'{$y_to}', 
		'{$scroll_auto}', 
		'{$title}', 
		'{$content}')";
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert('Quick메뉴 등록이 완료되었습니다.'); }</script>";
		$type=''; $used='';
		$design=''; $x_size=''; $y_size=''; $x_to='';
		$y_to=''; $scroll_auto=''; $title=''; $content='';
	} else {
		$onload="<script>window.onload=function(){ alert('Quick메뉴 등록은 최대 {$maxcnt}개 까지 등록 가능합니다.'); }</script>";
	}
} else if (($type=="modify_result" || $type=="modify") && ord($num)) {
	$sql = "SELECT * FROM tblquickmenu WHERE num = '{$num}' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		pmysql_free_result($result);
		if($type=="modify") {
			$used=$row->used;
			$design=$row->design;
			$x_size=$row->x_size;
			$y_size=$row->y_size;
			$x_to=$row->x_to;
			$y_to=$row->y_to;
			$scroll_auto=$row->scroll_auto;
			$title=$row->title;
			$content=$row->content;
		} else if($type=="modify_result") {
			$sql = "SELECT COUNT(*) as cnt, COUNT(CASE WHEN used='Y' THEN 1 ELSE NULL END) as cnt2 FROM tblquickmenu ";
			$sql.= "WHERE num!='{$num}' ";
			$result = pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);
			if($used=="Y" && $row->cnt2>=1) {
				$onload="<script>window.onload=function(){ alert('Quick메뉴는 등록 목록중 1가지만 사용할 수 있습니다.'); }</script>";
			} else {
				$sql = "UPDATE tblquickmenu SET ";
				$sql.= "used		= '{$used}', ";
				$sql.= "design		= '{$design}', ";
				$sql.= "x_size		= '{$x_size}', ";
				$sql.= "y_size		= '{$y_size}', ";
				$sql.= "x_to		= '{$x_to}', ";
				$sql.= "y_to		= '{$y_to}', ";
				$sql.= "scroll_auto	= '{$scroll_auto}', ";
				$sql.= "title		= '{$title}', ";
				$sql.= "content		= '{$content}' ";
				$sql.= "WHERE num = '{$num}' ";
				pmysql_query($sql,get_db_conn());
				$onload="<script>window.onload=function(){ alert('Quick메뉴 수정이 완료되었습니다.'); }</script>";
				$type=''; $used=''; $num='';
				$design=''; $x_size=''; $y_size=''; $x_to='';
				$y_to=''; $scroll_auto=''; $title=''; $content='';
			}
		}
	} else {
		pmysql_free_result($result);
		$onload="<script>window.onload=function(){ alert('수정하려는 Quick메뉴 정보가 존재하지 않습니다.'); }</script>";
	}
} else if ($type=="delete" && ord($num)) {
	$sql = "SELECT * FROM tblquickmenu WHERE num = '{$num}' ";
	$result = pmysql_query($sql,get_db_conn());
	$rows=pmysql_num_rows($result);
	pmysql_free_result($result);

	if($rows>0) {
		$sql = "DELETE FROM tblquickmenu WHERE num = '{$num}' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert('해당 Quick메뉴를 삭제하였습니다.'); }</script>";
		$type=''; $used=''; $num='';
		$design=''; $x_size=''; $y_size=''; $x_to='';
		$y_to=''; $scroll_auto=''; $title=''; $content='';
	}
}

if(ord($type)==0) $type="insert";
$type_name="images/botteon_save.gif";
if($type=="modify" || $type=="modify_result") $type_name="images/btn_edit2.gif";

if($type=="insert") $used_disabled="disabled";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
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
		alert("Quick메뉴 위치 설정을 하세요.");
		document.form1.x_to.focus();
		return;
	}
	if(!IsNumeric(document.form1.x_to.value)) {
		alert("Quick메뉴 위치 설정값은 숫자만 입력 가능합니다.");
		document.form1.x_to.focus();
		return;
	}
	if(!IsNumeric(document.form1.y_to.value)) {
		alert("Quick메뉴 위치 설정값은 숫자만 입력 가능합니다.");
		document.form1.y_to.focus();
		return;
	}
	if(document.form1.x_size.value.length==0 || document.form1.y_size.value.length==0) {
		alert("Quick메뉴 크기 설정을 하세요.");
		document.form1.x_size.focus();
		return;
	}
	if(!IsNumeric(document.form1.x_size.value)) {
		alert("Quick메뉴 크기 설정값은 숫자만 입력 가능합니다.");
		document.form1.x_size.focus();
		return;
	}
	if(!IsNumeric(document.form1.y_size.value)) {
		alert("Quick메뉴 크기 설정값은 숫자만 입력 가능합니다.");
		document.form1.y_size.focus();
		return;
	}
	if(document.form1.scroll_auto[0].checked==false && document.form1.scroll_auto[1].checked==false) {
		alert("스크롤 타입을 선택하세요.");
		document.form1.scroll_auto[0].focus();
		return;
	}
	if(document.form1.title.value.length==0) {
		alert("Quick메뉴 제목을 입력하세요.");
		document.form1.title.focus();
		return;
	}
	
	design=false;
	
	for(i=quickcnt;i<document.form1.design.length;i++) {
		if(document.form1.design[i].checked) {
			design=true;
			break;
		}
	}
	if(!design) {
		alert("Quick메뉴 템플릿을 선택하세요.");
		return;
	}
	var sHTML = oEditors.getById["ir1"].getIR();
	form1.content.value=sHTML;
	if(document.form1.content.value.length==0) {
		alert("Quick메뉴 내용을 입력하세요.");
		document.form1.content.focus();
		return;
	}
	if(type=="modify" || type=="modify_result") {
		if(!confirm("해당 Quick메뉴를 수정하시겠습니까?")) {
			return;
		}
		document.form1.type.value="modify_result";
	} else {
		document.form1.type.value="insert";
	}
	document.form1.submit();
}

function ModeSend(type,num) {
	if(type=="delete") {
		if(!confirm("해당 Quick메뉴를 삭제하시겠습니까?")) {
			return;
		}
	}
	document.form1.type.value=type;
	document.form1.num.value=num;
	document.form1.submit();
}

$(document).ready(function(){	
	$(document).on("click", ".templateImg", function(){
		$(this).parent().parent().parent().find(':radio').attr("checked", false);
		$(this).prev().trigger("click");
	})
})
function ChangeQuickType() {
	if(!confirm("최근 본 상품 사용여부 설정을 저장하시겠습니까?")) {
		return;
	}
	up_quick_type="";
	for(i=0;i<document.form1.up_quick_type.length;i++) {
		if(document.form1.up_quick_type[i].checked) {
			up_quick_type=document.form1.up_quick_type[i].value;
			break;
		}
	}
	document.form2.up_quick_type.value=up_quick_type;
	document.form2.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>Quick메뉴 관리</span></p></div></div>
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
			<input type=hidden name=type>
			<input type=hidden name=num value="<?=$num?>">
			<input type=hidden name=htmlmode value='wysiwyg'>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">Quick메뉴 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 전체페이지에서 항상 따라다니는 우측의 Quick메뉴를 관리할 수 있습니다. 쇼핑 편의 및 이벤트 홍보를 위해 활용하세요.</span></div>
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
                            <li>1) "최근 본 상품도 사용함"으로 설정할 경우 Quick메뉴와 출력 위치가 겹칠 수 있습니다.</li>
                            <li>2) 겹칠 경우 "위치설정"에서 상단 위치를 적절히 변경하시면 됩니다.</li>
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
									<INPUT id=idx_quick_type1 type=radio value=0 name=up_quick_type <?php if($quick_type=="0")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_quick_type1>최근 본 상품도 사용함</LABEL>  &nbsp;&nbsp;&nbsp;
									<INPUT id=idx_quick_type2 type=radio value=1 name=up_quick_type <?php if($quick_type=="1")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_quick_type2>최근 본 상품은 사용안함</LABEL>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:ChangeQuickType();"><img src="<?=$type_name?>"  border="0"></a></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">Quick메뉴 목록</div>
				</td>
			</tr>
			<tr>
				<td>				
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=50></col><col width=75></col><col width=><col width=65><col width=80><col width=60><col width=60></col>
				<TR align=center>
					<th>No</th>
					<th>사용여부</th>
					<th>Quick메뉴 제목</th>
					<th>스크롤</th>
					<th>등록일</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$colspan=7;
				$sql = "SELECT num, used, reg_date, title, scroll_auto FROM tblquickmenu ORDER BY num DESC ";
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$cnt++;
					$reg_date = substr($row->reg_date,0,4)."/".substr($row->reg_date,4,2)."/".substr($row->reg_date,6,2);
					if($row->scroll_auto=="Y")	$scroll_auto_name = "자동스크롤";
					else if($row->scroll_auto=="N")	$scroll_auto_name = "위치고정";
					if($row->used=="Y")	$used_name = "사용중";
					else if($row->used=="N")	$used_name = "사용안함";
					echo "<TR>\n";
					echo "	<TD>{$cnt}</TD>\n";
					echo "	<TD>{$used_name}</TD>\n";
					echo "	<TD><div class=\"ta_l\">{$row->title}</div></TD>\n";
					echo "	<TD>{$scroll_auto_name}</TD>\n";
					echo "	<TD>{$reg_date}</TD>\n";
					echo "	<TD><a href=\"javascript:ModeSend('modify','{$row->num}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD><a href=\"javascript:ModeSend('delete','{$row->num}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td colspan={$colspan} align=center>등록된 Quick메뉴가 없습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>

				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">Quick메뉴 등록/수정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>사용여부</span></th>
					<TD class="td_con1"><INPUT id=idx_used type=checkbox value=Y <?php if($used=="Y")echo"checked";?> name=used <?=$used_disabled?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_used>사용함</LABEL><br>* 사용함으로 되어 있는 경우에만 나타납니다. (모든 페이지에 나타남)<br><span class="font_orange">* 사용함 기능은 등록후에 변경할 수 있습니다.&nbsp;</span></TD>
				</TR>
				<TR>
					<th><span>Quick메뉴 위치 설정</span></th>
					<TD class="td_con1">왼쪽에서 <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=x_to value="<?=$x_to?>" class="input">픽셀 이동 후, 위쪽에서 <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=y_to value="<?=$y_to?>" class="input">픽셀 아래로 이동합니다.</TD>
				</TR>
				<TR>
					<th><span>Quick메뉴 크기 설정</span></th>
					<TD class="td_con1">가로: <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=x_size value="<?=$x_size?>" class="input">픽셀,  &nbsp;세로: <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=y_size value="<?=$y_size?>" class="input">픽셀 <b><span class="font_orange">(</b><B>＊가로사이즈는 90px을 권장)</span></B></TD>
				</TR>
				<TR>
					<th><span>스크롤 선택</span></th>
					<TD class="td_con1">
					<INPUT id=idx_scroll_auto1 type=radio value=Y name=scroll_auto <?php if($scroll_auto=="Y")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_scroll_auto1>화면 스크롤에 맞추어 자동 스크롤</LABEL>  &nbsp;&nbsp;&nbsp;
					<INPUT id=idx_scroll_auto2 type=radio value=N name=scroll_auto <?php if($scroll_auto=="N")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_scroll_auto2>위치 고정</LABEL><BR><span class="font_orange">＊자동 스크롤로 지정하는 경우, 고객은 항상 Quick메뉴를 볼 수 있습니다.</span>
					</TD>
				</TR>
				<tr>
					<th><span>Quick메뉴 제목</span></th>
					<TD class="td_con1"><INPUT style="WIDTH:100%" name=title value="<?=$title?>" class="input"><br><span class="font_orange">＊관리목록에서만 사용합니다. 간단히 입력해 주세요.</span></TD>
				</tr>
				<tr>
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
								<TD><div class="point_title">템플릿 선택 (＊사이즈 90px 권장)</div></TD>
							</TR>
							<TR>
								<TD width="100%" style="padding:0pt;">
								<div class="table_none">
								<TABLE cellSpacing=0  width="100%" border=0>
								<TR>
									<TD width="31" height="160" align=right valign="middle">
										<img src="images/btn_back.gif" onMouseover='moveright()' onMouseout='clearTimeout(righttime)' style="cursor:hand;" border="0">
									</TD>
									<TD height="160">
									<div class="table_none">
									<table width="100%" cellspacing="0" cellpadding="0" border="0">
									<tr height=170>
										<td id=temp style="visibility:hidden;position:absolute;top:0;left:0">
				<?php
										echo "<script>";
										$jj=0;
										$menucontents = "";
										$menucontents.= "<table border=0 cellpadding=0 cellspacing=0><tr>";
										for($i=0;$i<count($quickmenu);$i++) {
											echo "thisSel = 'dotted #FFFFFF';";
											$menucontents.= "<td width='173'><p align='center'><input type=radio class='disign_radio' name='design' value='{$quickmenu[$i]}'";
											if($design==$quickmenu[$i]) $menucontents.= " checked";
											$menucontents.= "><img src='images/sample/quick{$quickmenu[$i]}.gif' border=0 hspace='5' style='border-width:1pt; border-color:#FFFFFF; border-style:solid;' onMouseOver='changeMouseOver(this);' onMouseOut='changeMouseOut(this,thisSel);' style='cursor:hand;' class ='templateImg'></td>";
											$jj++;
										}
										$menucontents.= "</tr></table>";
										echo "</script>";
				?>
										<script>
										function changeMouseOver(img) {
											 img.style.border='1 solid #0c71c6';
										}
										function changeMouseOut(img,dot) {
											 img.style.border="1 "+dot;
										}

										var menuwidth=1000;
										var menuheight=170;
										var scrollspeed=10;
										var menucontents="<nobr><?=$menucontents?></nobr>";
										
										var iedom=document.all||document.getElementById
										if (iedom)
											document.write(menucontents);
										var actualwidth='';
										var cross_scroll, ns_scroll
										var loadedyes=0;
										function fillup(){
											
											if (iedom){
												cross_scroll=document.getElementById? document.getElementById("test2") : document.all.test2;
												cross_scroll.innerHTML=menucontents;
												actualwidth=document.all? cross_scroll.offsetWidth : document.getElementById("temp").offsetWidth;
											}
											else if (document.layers){
												
												ns_scroll=document.ns_scrollmenu.document.ns_scrollmenu2;
												ns_scroll.document.write(menucontents);
												ns_scroll.document.close();
												actualwidth=ns_scroll.document.width;
											}
											loadedyes=1;
										}
										window.onload=fillup;
										
										function moveleft(){
											if (loadedyes){
												if (iedom&&parseInt(cross_scroll.style.left)>(menuwidth-actualwidth)){
													cross_scroll.style.left=parseInt(cross_scroll.style.left)-scrollspeed;
												}
												else if (document.layers&&ns_scroll.left>(menuwidth-actualwidth))
													ns_scroll.left-=scrollspeed;
											}
											lefttime=setTimeout("moveleft()",50);
										}
										
										function moveright(){
											if (loadedyes){
												if (iedom&&parseInt(cross_scroll.style.left)<0)
													cross_scroll.style.left=parseInt(cross_scroll.style.left)+scrollspeed;
												else if (document.layers&&ns_scroll.left<0)
													ns_scroll.left+=scrollspeed;
											}
											righttime=setTimeout("moveright()",50);
										}
										
										if (iedom||document.layers){
											with (document){
												write('<td valign=top>');
												if (iedom){
													write('<div style="position:relative;width:'+menuwidth+';">');
													write('<div style="position:absolute;width:'+menuwidth+';height:'+menuheight+';overflow:hidden;">');
													write('<div id="test2" style="position:absolute;left:0">');
													write('</div></div></div>');
												}
												else if (document.layers){
													write('<ilayer width='+menuwidth+' height='+menuheight+' name="ns_scrollmenu">');
													write('<layer name="ns_scrollmenu2" left=0 top=0></layer></ilayer>');
												}
												write('</td>');
											}
										}
										</script>
										</td>
									</tr>
									</table>
									</div>
									</TD>
									<TD width="31" height="160"><img src="images/btn_next.gif" onMouseover='moveleft()' onMouseout='clearTimeout(lefttime)' style="cursor:hand;" border="0"></TD>
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

					</Th>
				</tr>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td class="bd_editer">
					<table cellpadding="0" cellspacing="0" width="100%">
					<!--
					<tr>
						<td width="100%" height="35">
						<INPUT type=radio id=idx_chk_webedit1 name=chk_webedit CHECKED onclick="JavaScript:ChangeEditer('wysiwyg',this)"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_chk_webedit1>웹편집기로 입력하기</LABEL>  &nbsp;&nbsp;&nbsp;
						<INPUT type=radio id=idx_chk_webedit2 name=chk_webedit onclick="JavaScript:ChangeEditer('textedit',this);"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_chk_webedit2>직접 HTML로 입력하기</LABEL> <BR>
						</td>
					</tr>
					-->
					<tr>
						<td><TEXTAREA style="DISPLAY: yes; WIDTH: 100%" name=content id=ir1 rows="17" wrap=off class="textarea"><?=$content?></TEXTAREA></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="pt_20" align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="<?=$type_name?>" border="0"></a></td>
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
							<dt><span>팝업창 사용가이드</span></dt>
							<dd>
								- Quick메뉴는 최대 10개 까지 등록 가능합니다.
						<br>- Quick메뉴는 등록 목록중 1가지만 사용할 수 있습니다.
						<br><span class="font_orange"><b>- 사용하지 않는 경우 모든 Quick메뉴를 사용하지 않음으로 변경하세요.</b></span>
							</dd>
							
						</dl>
						<dl>
							<dt><span class="font_orange">퀵메뉴 입력폼</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0>[MYPAGE] </TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="100%">&lt;a href=[MYPAGE]&gt;마이페이지&lt;/a&gt;<br> </TD>
								</TR>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0>[MEMBER] </TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">&lt;a href=[MEMBER]&gt;회원가입/수정&lt;/a&gt;<br>  </TD>
								</TR>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[ORDER] </TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%">&lt;a href=[ORDER]&gt;주문조회&lt;/a&gt; </TD>
								</TR>
								</TABLE>
							</dd>
						</dl>
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</form>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="update">
			<input type=hidden name=up_quick_type value="">
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
<SCRIPT LANGUAGE="JavaScript">
	var oEditors = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2Skin.html",	
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		}, 
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});
</script>
<?=$onload?>
<?php 
include("copyright.php");
