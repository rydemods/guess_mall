<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-7";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/etc/";

$type=$_POST["type"];
$selimage=$_POST["selimage"];
$okdesign=$_POST["okdesign"];

if($type=="delete_bgimage") {	//백그라운드 삭제
	$bgimage = $imagepath."easymenubg.gif";
	if (file_exists($bgimage)) {
		unlink($bgimage);
		$onload="<script>window.onload=function(){ alert('왼쪽메뉴 배경 이미지 삭제가 완료되었습니다.'); }</script>";
	} else {
		$onload="<script>window.onload=function(){ alert('등록된 왼쪽메뉴 배경 이미지가 존재하지 않습니다.'); }</script>";
	}
} else if($type=="delete_menuimage") {	//해당 메뉴 이미지 삭제
	$menuimage=$imagepath.$selimage.".gif";
	if(file_exists($menuimage)) {
		unlink($menuimage);
		$onload="<script>window.onload=function(){ alert('해당 메뉴 이미지 삭제가 완료되었습니다.'); }</script>";
	} else {
		$onload="<script>window.onload=function(){ alert('해당 메뉴 이미지가 존재하지 않습니다.'); }</script>";
	}
} else if($type=="update") {	//정보 업데이트
	while (list($key, $vals) = each($_POST)) {
		${$key}=$_POST[$key];
	}
	while (list($key, $vals) = each($_FILES)) {
		${$key}=$_FILES[$key];
	}
	$number=array(&$num0,&$num1,&$num2,&$num3,&$num4);

	$iserror=false;
	if (file_exists($background["tmp_name"])) {
		$imgname=$background["name"];
		$ext = strtolower(pathinfo($imgname,PATHINFO_EXTENSION));		
		$rimage="easymenubg.gif";
		if(in_array($ext,array('gif','jpg'))) {
			move_uploaded_file($background["tmp_name"],$imagepath.$rimage);
			chmod($imagepath.$rimage,0664);
		} else {
			$iserror=true;
		}
	}

	$cnt=5;
	for($i=0;$i<$cnt;$i++) {
		for($j=0;$j<$number[$i];$j++) {
			$rimage=${"easymenucode".$i.$j}.".gif";
			$img=${"file".$i.$j};
			$imgname=$img["name"];

			if (ord($imgname) && file_exists($img["tmp_name"])) {
				$ext = strtolower(pathinfo($imgname,PATHINFO_EXTENSION));
				if($ext=="gif" || $ext=="jpg") {
					move_uploaded_file($img["tmp_name"],$imagepath.$rimage);
					chmod($imagepath.$rimage,0664);
				} else {
					$iserror=true;
				}
			}
		}
	}

	if($iserror) {
		alert_go('이미지 등록이 잘못되었습니다.',-1);
	}

	$menulist=substr($menulist,1);

	$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$cnt=(int)$row->cnt;
	pmysql_free_result($result);
	$qry="";
	if($cnt==0) {
		$sql = "INSERT INTO tbldesign(left_set,left_xsize,left_image) VALUES (
		'Y', 
		'{$left_xsize}', 
		'{$menulist}')";
	} else {
		$sql = "UPDATE tbldesign SET 
		left_set	= 'Y', 
		left_xsize	= '{$left_xsize}', 
		left_image	= '{$menulist}' ";
	}
	pmysql_query($sql,get_db_conn());
	DeleteCache("tbldesign.cache");

	if($okdesign=="Y") {
		$sql = "UPDATE tblshopinfo SET menu_type='menue' ";
		pmysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");

		$_shopdata->menu_type="menue";
	}
	$onload = "<script>window.onload=function(){ alert('정보 수정이 완료되었습니다.'); }</script>";
}

$sql = "SELECT * FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
if($row->left_set=="Y") {
	$left_xsize=$row->left_xsize;
	$imgtype=$row->left_image;
} else {
	$imgtype="1,2,3,4";
}
pmysql_free_result($result);

$allimgname=array("로그인","상품 검색","상품 카테고리","커뮤니티","고객센터","배너","이벤트/고객알림");
$allimgtype=",0,1,2,3,4,5,6,";

$cnt = count($allimgname);

$ar_imgtype = explode(",",$imgtype);
$cnt2 = count($ar_imgtype);
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(document.form1.left_xsize.value.length==0) {
		alert("왼쪽메뉴 사이즈를 입력하세요.");
		document.form1.left_xsize.focus();
		return;
	}
	if(!IsNumeric(document.form1.left_xsize.value)) {
		alert("왼쪽메뉴 사이즈는 숫자만 입력하세요.");
		document.form1.left_xsize.focus();
		return;
	}
	document.form1.menulist.value="";
	for(i=0;i<document.form1.inmenu.options.length;i++) {
		document.form1.menulist.value+=","+document.form1.inmenu.options[i].value;
	}
	if(document.form1.menulist.value.length==0) {
		alert("왼쪽메뉴를 하나 이상 노출하셔야 합니다.");
		return;
	}
	document.form1.type.value="update";
	document.form1.submit();
}

function SendMode(mode) {
	if (document.form1.outmenu.selectedIndex==-1 && mode=="insert") {
		alert("왼쪽메뉴에 추가할 메뉴를 선택하세요.");
		return;
	} else if(document.form1.inmenu.selectedIndex==-1 && mode=="delete") {
		alert("왼쪽메뉴에서 삭제할 메뉴를 선택하세요.");
		return;
	}
	if (mode=="insert") {
		text=document.form1.outmenu.options[document.form1.outmenu.selectedIndex].text;
		value=document.form1.outmenu.options[document.form1.outmenu.selectedIndex].value;
		document.form1.inmenu.options[document.form1.inmenu.options.length]=new Option(text,value);
		document.form1.outmenu.options[document.form1.outmenu.selectedIndex]=null;
	} else if (mode=="delete"){
		text=document.form1.inmenu.options[document.form1.inmenu.selectedIndex].text;
		value=document.form1.inmenu.options[document.form1.inmenu.selectedIndex].value;
		document.form1.outmenu.options[document.form1.outmenu.options.length]=new Option(text,value);
		document.form1.inmenu.options[document.form1.inmenu.selectedIndex]=null;
	}
}

function move(gbn) {
	change_idx = document.form1.inmenu.selectedIndex;
	if (change_idx<0) {
		alert("순서를 변경할 메뉴를 선택하세요.");
		return;
	}
	if (gbn=="up" && change_idx==0) {
		alert("선택하신 메뉴는 더이상 위로 이동되지 않습니다.");
		return;
	}
	if (gbn=="down" && change_idx==(document.form1.inmenu.length-1)) {
		alert("선택하신 메뉴는 더이상 아래로 이동되지 않습니다.");
		return;
	}
	if (gbn=="up") idx = change_idx-1;
	else idx = change_idx+1;

	idx_value = document.form1.inmenu.options[idx].value;
	idx_text = document.form1.inmenu.options[idx].text;

	document.form1.inmenu.options[idx].value = document.form1.inmenu.options[change_idx].value;
	document.form1.inmenu.options[idx].text = document.form1.inmenu.options[change_idx].text;

	document.form1.inmenu.options[change_idx].value = idx_value;
	document.form1.inmenu.options[change_idx].text = idx_text;

	document.form1.inmenu.selectedIndex = idx;
}

var layer=new Array("layer0","layer1","layer2","layer3","layer4","layer5","layer6");
function change_layer(val) {
	if(document.all){
		for(i=0;i<layer.length;i++) {
			document.all[layer[i]].style.display="none";
		}
		document.all["layer"+val].style.display="";
	} else if(document.getElementById){
		for(i=0;i<layer.length;i++) {
			document.getElementById(layer[i]).style.display="none";
		}
		document.getElementById("layer"+val).style.display="";
	} else if(document.layers){
		for(i=0;i<layer.length;i++) {
			document.layers[layer[i]].display="none";
		}
		document.layers["layer"+val].display="";
	}
}

function delet_bgimage() {
	if(confirm("왼쪽메뉴 배경 이미지를 정말 삭제하시겠습니까?")) {
		document.form1.type.value="delete_bgimage";
		document.form1.submit();
	}
}

function delete_menuimage(val) {
	if(confirm("해당 완쪽메뉴 이미지를 정말 삭제하시겠습니까?")) {
		document.form1.type.value="delete_menuimage";
		document.form1.selimage.value=val;
		document.form1.submit();
	}
}

</script>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<style>
	.innput {font-size:12px;background-color:#ffffff;border:1px solid blur}
</style>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; Easy디자인 관리 &gt;<span>Easy 왼쪽 메뉴 관리</span></p></div></div>
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
					<div class="title_depth3">Easy 왼쪽 메뉴 관리</div>
					<div class="title_depth3_sub"><span>쇼핑몰 왼쪽메뉴 디자인을 직접 제작하신 이미지파일을 이용하여, 간단하게 디자인을 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					 <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) HTML을 몰라도 왼쪽메뉴의 디자인과 순서를 쉽게 변경 가능합니다.</li>
                            <li>2) Easy 디자인 해제 : 메인 템플릿 선택 또는 개별디자인 선택하면 됩니다.</li>
                            <li><IMG SRC="images/design_eachleftmenu_img.gif" ALT="" align="baseline"></li>
                            
                        </ul>
                    </div>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="200" valign="top" height="100%">
					<table cellpadding="0" cellspacing="0" width="100%" height="100%" style="overflow:hidden;display:inline-block;">
					<tr>
						<td width="100%" height="100%" valign="top" background="images/category_boxbga.gif">
						<table cellpadding="0" cellspacing="0" width="100%" height="100%">
						<tr>
							<td><IMG SRC="images/category_box1a.gif" ALT=""></td>
						</tr>
						<tr>
							<td bgcolor="#0F8FCB" style="padding-top:4pt; padding-bottom:6pt;"><p align="center">&nbsp;<B><font color="white">쇼핑몰 실제 왼쪽이미지</font></B></p></td>
						</tr>
						<tr>
							<td width="234"><IMG SRC="images/category_box2a.gif" ALT=""></td>
						</tr>
						<tr>
							<td width="100%" height="100%">
							<TABLE cellSpacing=0 cellPadding="10" width="100%" border="0" height="100%">
							<TR>
								<TD height="100%" valign="top" width="100%"><iframe name=contents src="/<?=RootPath.MainDir?>menue.php?preview=OK" frameborder=no scrolling=auto style="WIDTH:100%;HEIGHT:100%;border;border-width:1pt; border-color:#D3E2F5; border-style:solid;"></iframe></td>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="232" height="13"><IMG SRC="images/category_boxdowna.gif" ALT=""></td>
					</tr>
					</table>
					</TD>
					<td width="5" valign="top"></TD>
					<td width="100%" valign="top" style="padding-left:8px">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%">
						<!-- 소제목 -->
						<div class="title_depth3_sub" style="margin-top:0px;">쇼핑몰 왼쪽이미지 설정</div>
						</td>
					</tr>
					<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
					<input type=hidden name=type>
					<input type=hidden name=menulist>
					<input type=hidden name=selimage>
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>왼쪽 사이즈 설정</span></th>
							<TD class="td_con1"><img src="images/icon_width1.gif" border="0"> : <input type=text name="left_xsize" value="<?=$left_xsize?>" size=7 maxlength=3 onkeyup="strnumkeyup(this)" class="input">픽셀&nbsp;&nbsp;(권장 : 200픽셀)</TD>
						</TR>
						<TR>
							<th><span>왼쪽 배경이미지</span></th>
							<TD class="td_con1">
							<input type=file name=background size=50>
							<!--
							<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly"> 
							<div class="file_input_div">
							<input type="button" value="찾아보기" class="file_input_button" /> 
							<input type=file name=background size=15 style="WIDTH: 88%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" >
							</div>
							-->
							<?php if(file_exists($imagepath."easymenubg.gif")) { ?> <input type="button" onClick="delet_bgimage()" value="삭제" class="submit1"><?php }?><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;"> * 미등록시 흰색 배경, 배경이미지 사이즈가 작을 경우 반복되는 현상 발생됩니다.</span></TD>
						</TR>
						</TABLE>
						</div>
						</td>
					</tr>
					<tr>
						<td width="100%">
						<!-- 소제목 -->
						<div class="title_depth3_sub">쇼핑몰 왼쪽 메뉴 순서조정 및 관리</div>
						</td>
					</tr>
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <TR>
                            <TD><div class="point_title02">미사용중인 메뉴명</div></TD>
							<td></td>
                            <TD><div class="point_title03">현재 사용중인 왼쪽메뉴명</div></TD>
                        </TR>
						<TR>
							<TD width="40%" align="center" valign="top" style="padding:5pt; background-color:#f8f8f8;">
								<select name=outmenu size=13 style="width:100%;" class="select">
								<?php
											for($i=0;$i<$cnt;$i++){
												if(strstr($imgtype,$i)===FALSE){
													echo "<option value=\"{$i}\">{$allimgname[$i]}\n";
												}
											}
								?>
								</select>
							</TD>
							<TD  align="center" width="50"><a href="JavaScript:SendMode('insert')"><img src="images/btn_next.gif" border="0"><br><span style="color:#000000;font-size:11px;letter-spacing:-0.5pt;">보이기</span></a><br><br><a href="JavaScript:SendMode('delete')"><img src="images/btn_back.gif" border="0"><br><span style="color:#000000;font-size:11px;letter-spacing:-0.5pt;">숨기기</span></a></TD>
							<TD width="60%" class="td_con1" align="center" valign="top" style="padding:5pt;" bgcolor="#f8f8f8">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD noWrap align=middle width="100%">
										<select name=inmenu size=13 style="width:100%" onchange="change_layer(this.value)" class="select">
										<?php
													for($i=0;$i<$cnt2;$i++){
														echo "<option value=\"{$ar_imgtype[$i]}\">{$allimgname[$ar_imgtype[$i]]}\n";
													}
										?>
										</select>
									</TD>
									<TD style="padding-left:5px;">
										<div class="table_none">
											<table cellpadding="0" cellspacing="0" width="34">
											<TR>
												<TD align=middle><a href="JavaScript:move('up')"><IMG src="images/code_up.gif" align=absMiddle border=0 vspace="2"></A></td>
											</tr>
											<TR>
												<TD align=middle><IMG src="images/code_sort.gif" ></td>
											</tr>
											<TR>
												<TD align=middle><a href="JavaScript:move('down')"><IMG src="images/code_down.gif" align=absMiddle border=0 vspace="2"></A></td>
											</tr>
											</table>
										</div>
									</TD>
								</TR>
								</TABLE>
								<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* 메뉴명을 클릭하면 해당 메뉴의 이미지를 등록할 수 있습니다.</span>

							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="100%">
<?php
				$menuname=array("로그인","상품검색","상품카테고리","커뮤니티","고객센터");
				$commonmenu = array("타이틀 이미지 등록","하단 이미지 등록","배경 이미지 등록");
				$menucode=array(
							array("easylogintitle","easyloginbottom","easyloginbg","easyidimage","easypwimage","easyloginbutton","easylogoutbutton"),
							array("easysearchtitle","easysearchbottom","easysearchbg","easysearchbutton"),
							array("easyproducttitle","easyproductbottom","easyproductbg"),
							array("easyboardtitle","easyboardbottom","easyboardbg"),
							array("easycustomertitle","easycustomerbottom","easycustomerbg"));

				$button=array(
							array("아이디","비밀번호","로그인 버튼","로그아웃 버튼"),
							array("검색버튼"));

				$sql = "SELECT code_a as code, type, code_name FROM tblproductcode 
				WHERE group_code!='NO' 
				AND (type='L' OR type='T' OR type='LX' OR type='TX') ORDER BY sequence DESC ";
				$result=pmysql_query($sql,get_db_conn());
				$cnt=0;$cnt2=3;
				while($row=pmysql_fetch_object($result)) {
					$button[2][$cnt++]=$row->code_name;
					$menucode[2][$cnt2++]="easy".$row->code;
				}
				if($_shopdata->estimate_ok=="Y" || $_shopdata->estimate_ok=="O") {
					$button[2][$cnt++]="온라인 견적서";
					$menucode[2][$cnt2++]="easyestimate";
				}

				$sql = "SELECT board,board_name FROM tblboardadmin ORDER BY date DESC ";
				$result=pmysql_query($sql,get_db_conn());
				$cnt=0;$cnt2=3;
				while ($row=pmysql_fetch_object($result)) {
					$button[3][$cnt++]=$row->board_name;
					$menucode[3][$cnt2++]="easy".$row->board;
				}
				if ($_shopdata->ETCTYPE["REVIEW"]=="Y") {
					$button[3][$cnt++]="사용후기 모음";
					$menucode[3][$cnt2++]="easyreviewall";
				}
				
				$mess=array("<img src=\"images/design_easyleft_img1.gif\" border=\"0\" >", 
				"<img src=\"images/design_easyleft_img4.gif\" border=\"0\">", 
				"<img src=\"images/design_easyleft_img2.gif\" border=\"0\">", 
				"<img src=\"images/design_easyleft_img5.gif\" border=\"0\">", 
				"<img src=\"images/design_easyleft_img3.gif\" border=\"0\">");

				for($i=0;$i<=4;$i++) {
?>
						<div id=layer<?=$i?> style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<div class="point_title">[<?=$menuname[$i]?>] 메뉴 이미지 등록</div>
                   		<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%">
                            <div class="table_style02">
							<TABLE cellspacing="0" cellPadding=0 width="100%" border=0>
                            <col width="100" />
                            <col width="120" />
                            <col width="" />
                            <tr>
								<th>왼쪽 메뉴명</th>
								<th>해당 이미지명</th>
								<th>해당 이미지 등록(JPG/GIF 파일_150K)</th>
                            </TR>
							<TR>
								<TD rowspan="3"><B><?=$menuname[$i]?></B></TD>
<?php
						for($j=0;$j<=2;$j++) {
							if($j!=0) echo "<tr>";
?>
								<TD><?=$commonmenu[$j]?></p></td>
								<?php if(file_exists($imagepath.$menucode[$i][$j].".gif")) { ?>
								<TD>
                                <div class="table_none">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="600" class="td_con1">
									<p align="left">
									<input type=file name=file<?=$i.$j?> size=50>
									<!--
									<input type="text" id="fileName[$i]" class="file_input_textbox w400" readonly="readonly"> 
									<div class="file_input_div">
									<input type="button" value="찾아보기" class="file_input_button" />
									<input type=file name=file<?=$i.$j?> style="WIDTH: 100%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName[$i]').value = this.value" >
									</div>
									-->
									</td>
									<td width="125" style="padding-left:2px"><input type="button" value="삭제" class="submit1" onClick="delete_menuimage('<?=$menucode[$i][$j]?>')"></td>
								</tr>
								</table>
                                </div>
                                
<?php } else { ?>
								<TD>
                                <div class="table_none">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="600" class="td_con1"><p align="left">
									<input type=file name=file<?=$i.$j?> size=50>
									<!--
									<input type="text" id="fileName[$i]" class="file_input_textbox w400" readonly="readonly"> 
									<div class="file_input_div">
									<input type="button" value="찾아보기" class="file_input_button" /> 
									<input type=file name=file<?=$i.$j?> style="WIDTH: 100%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName[$i]').value = this.value" >
									</div>-->
									
									</td>
									<td width="125"></td>
								</tr>
								</table>
                                </div>
<?php } ?>
								<input type=hidden name=easymenucode<?=$i.$j?> value="<?=$menucode[$i][$j]?>">
								</td></tr>
<?php
						}
						$cnt = count($button[$i]);
						for($k=0;$k<$cnt;$k++){
?>							
							<tr>
								<TD title='[<?=str_replace("'","",$button[$i][$k])?>]' colspan=2>[<?=titleCut(13,strip_tags($button[$i][$k]))?>] 이미지 등록</td>
								<?php if(file_exists($imagepath.$menucode[$i][$k+$j].".gif")) { ?>
								<TD>
                                <div class="table_none">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="600" class="td_con1"><p align="left">
									<input type=file name=file<?=$i.($k+$j)?> size=50>
									<!--
									<input type="text" id="fileName[$i]" class="file_input_textbox w400" readonly="readonly"> 
									<div class="file_input_div">
									<input type="button" value="찾아보기" class="file_input_button" /> 									
									<input type=file name=file<?=$i.($k+$j)?> style="WIDTH: 100%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName[$i]').value = this.value" >
									</div>									
									-->
									</td>
									<td width="125" style="padding-left:2px"><input type="button" value="삭제" class="submit1" onClick="delete_menuimage('<?=$menucode[$i][$k+$j]?>')"></td>
								</tr>
								</table>
                                </div>
								<?php } else { ?>
								<TD>
                                <div class="table_none">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="600" class="td_con1"><p align="left">
									<input type=file name=file<?=$i.($k+$j)?> size=50>
									<!--
									<input type="text" id="fileName[$i]" class="file_input_textbox w400" readonly="readonly"> 
									<div class="file_input_div">
									<input type="button" value="찾아보기" class="file_input_button" /> 
									<input type=file name=file<?=$i.($k+$j)?> style="WIDTH: 100%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName[$i]').value = this.value" >
									</div>									
									-->
									</td>
									<td width="125"></td>
								</tr>
								</table>
                                </div>
								<?php } ?>
								<input type=hidden name=easymenucode<?=$i.($k+$j)?> value="<?=$menucode[$i][$k+$j]?>">
								</td>
							</tr>
<?php
						}
?>
							<input type=hidden name=num<?=$i?> value="<?=$k+$j?>">
							</TABLE>
                            </div>
							</td>
						</tr>
						<tr>
							<td width="100%">
                            <div class="help_info01_wrap">
                       			<ul>
                            		<li><?=$mess[$i]?></li>
                                </ul>
                            </div>
							</td>
						</tr>
						</table>
						</div>
<?php
				}
?>
						<div id=layer5 style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%">
							<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
							<TR>
								<TD><IMG SRC="images/sub4_stitle_img1.gif" ALT=""></TD>
								<TD width="100%" background="images/sub4_stitle_bg.gif" class="font_white"><B><FONT color=#ffffff>[배너] 메뉴 이미지 등록</FONT></B></TD>
								<TD><IMG SRC="images/sub4_stitle_img2.gif" ALT=""></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						<tr>
							<td width="100%">
							<TABLE cellSpacing="1" cellPadding=0 width="100%" border=0 bgcolor="#DEDEDE">
							<TR bgColor=#f0f0f0>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="96" bgcolor="#F9F9F9">왼쪽 메뉴명</TD>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="133" bgcolor="#F9F9F9">해당 이미지명</TD>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="274" bgcolor="#F9F9F9">해당 이미지 등록<span class="font_blue">(JPG/GIF 파일_150K)</span></TD>
							</TR>
							<TR>
								<TD class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px" align=middle width="96" bgcolor="white"><B>배너</B></TD>
								<TD class="td_con2" style="line-height:125%;" align=middle width="434" bgcolor="white" colspan="2"><p align="left">배너 등록 및 관리는<br><span class="font_orange" style="letter-spacing:-0.5pt;">&quot;<a href="shop_logobanner.php">상점관리 &gt; 쇼핑몰환경설정 &gt; 로고/배너관리</a>&quot;</span>에서 하시면 됩니다.<br>Easy디자인에서는 [배너영역]의 노출 유무와 위치 순서만을 조정할 수 있습니다.</p></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</div>
						<div id=layer6 style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%">
							<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
							<TR>
								<TD><IMG SRC="images/sub4_stitle_img1.gif" ALT=""></TD>
								<TD width="100%" background="images/sub4_stitle_bg.gif" class="font_white"><B><FONT color=#ffffff>[이벤트/고객알림] 메뉴 이미지 등록</FONT></B></TD>
								<TD><IMG SRC="images/sub4_stitle_img2.gif" ALT=""></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						<tr>
							<td width="100%">
							<TABLE cellSpacing="1" cellPadding=0 width="100%" border=0 bgcolor="#DEDEDE">
							<TR bgColor=#f0f0f0>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="96" bgcolor="#F9F9F9">왼쪽 메뉴명</TD>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="133" bgcolor="#F9F9F9">해당 이미지명</TD>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="274" bgcolor="#F9F9F9">해당 이미지 등록<span class="font_blue">(JPG/GIF 파일_150K)</span></TD>
							</TR>
							<TR>
								<TD class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px;letter-spacing:-0.5pt;" align=middle width="96" bgcolor="white"><B>이벤트/고객알림</B></TD>
								<TD class="td_con2" style="line-height:125%;letter-spacing:-0.5pt;" align=middle width="434" bgcolor="white" colspan="2"><p align="left">자유 디자인의 등록&amp;관리는<br><span class="font_orange">&quot;<a href="shop_mainleftinform.php">상점관리 &gt; 쇼핑몰환경설정 &gt; 왼쪽고객알림</a>&quot;</span>에서 하시면 됩니다.<br>Easy디자인에서는[자유 디자인]의 노출 유무와 위치 순서만을 조정할 수 있습니다.</p></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</div>
						</td>
					</tr>
					</table>
					</TD>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align="left">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="205"><p>&nbsp;</p></TD>
					<td style="letter-spacing:-0.5pt;"><p><?php if($_shopdata->menu_type!="menue") {?><input type=checkbox name=okdesign value="Y" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><span class="font_orange"><b>Easy 디자인을 쇼핑몰에 곧바로 반영합니다.(템플릿, 개별디자인 모두 해제됨)</b><br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;이미 적용중일 때는 체크박스 생성되지 않습니다.<br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 사용중일 때 디자인 수정 - [적용하기] 클릭하면 쇼핑몰에 곧바로 반영됩니다.<br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 미사용일 때 디자인 수정 - 체크박스를 체크하지 않은 상태에서 [적용하기] 클릭하면 저장만 됩니다.</span><br><?php }?></TD>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="195"><p>&nbsp;</p></TD>
					<td width="553"><p align=center><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0" vspace="5"></a></TD>
				</tr>
				</table>
				</td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>Easy 디자인 해제 방법</span></dt>
                        <dd>- 템플릿으로 변경 : 메인템플릿 선택(Easy 디자인, 개별디자인이 모두 해제되고 선택된 템플릿의 상단으로 변경)<br />- 개별디자인으로 변경 : <a href="javascript:parent.topframe.GoMenu(2,'design_option.php');">디자인관리 > 웹FTP 및  개별적용 선택 > 개별디자인 적용선택</a> 메뉴에서<br>
						<span style="padding:0px 67px"></span>[상단+왼쪽 동시 적용][상단만 적용] 선택 - 개별디자인 내용으로 변경됩니다.<br>
						<span style="padding:0px 128px"></span>[적용안함] 선택 - 사용중인 템플릿으로 변경됩니다.</dd>
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
<script>
document.form1.inmenu.selectedIndex=0;
change_layer(document.form1.inmenu.options[0].value);
</script>
<?=$onload?>
<?php 
include("copyright.php");
