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

$imagepath=$Dir.DataDir."shopimages/etc/";

$type=$_POST["type"];
$eventloc=$_POST["eventloc"];
$old_image=$_POST["old_image"];
$up_design_type=$_POST["up_design_type"];
$up_body=chop($_POST["up_body"]);
$up_image=$_FILES["up_image"];

if($type=="up") {
	if ($up_design_type==1) {
		$ext = strtolower(pathinfo($up_image['name'],PATHINFO_EXTENSION));
		if ($up_image['name'] && in_array($ext,array('gif','jpg'))) {
			if($up_image['size'] < 153600) {
				$up_image['name']="eventprdetail".substr($up_image['name'],-4);
				if(ord($old_image) && file_exists($imagepath.$old_image)) {
					unlink($imagepath.$old_image);
				}
				move_uploaded_file($up_image['tmp_name'],$imagepath.$up_image['name']);
				chmod($imagepath.$up_image['name'],0606);
			} else {
				$up_image['name'] = $old_image;
			}
		} else {
			$up_image['name'] = $old_image;
		}
	} else {
		@unlink($imagepath.$old_image);
	}

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type = 'detailimg'";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	pmysql_free_result($result);
	$cnt=$row->cnt;
	if ($cnt==1) {
		$sql="UPDATE tbldesignnewpage SET ";
		$sql.= "filename	= '{$up_image['name']}', ";
		$sql.= "body		= '{$up_body}', ";
		$sql.= "leftmenu	= '{$eventloc}', ";
		$sql.= "code		= '{$up_design_type}' ";
		$sql.= "WHERE type = 'detailimg'";
		$onload="<script>window.onload=function(){ alert('내용 수정이 완료되었습니다.'); }</script>";
	} else {
		$sql="INSERT INTO tbldesignnewpage (type,subject,body,filename,leftmenu,code) VALUES ('detailimg','상품 상세 공통 이벤트','{$up_body}','{$up_image['name']}','{$eventloc}','{$up_design_type}')";
		$onload="<script>window.onload=function(){ alert('내용 등록이 완료되었습니다.'); }</script>";
	}
	pmysql_query($sql,get_db_conn());

} else if($type=="del") {
	$sql="DELETE FROM tbldesignnewpage WHERE type = 'detailimg'";
	$result = pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('상품상세 공통이벤트 정보가 초기화 되었습니다.'); }</script>";
}

$sql = "SELECT body,filename,code,leftmenu FROM tbldesignnewpage WHERE type = 'detailimg' ";
$result = pmysql_query($sql,get_db_conn());
if ($row = pmysql_fetch_object($result)) {
	$body=$row->body;
	$filename=$row->filename;
	$design_type=$row->code;
	$eventloc=$row->leftmenu;
} else {
	$filename="";
	$design_type="1";
	$eventloc="";
}
pmysql_free_result($result);
${"chk_type".$design_type} = "checked";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ChangeType(type){
	if (type==1) {
		document.form1.up_image.disabled=false;
		document.form1.up_body.disabled=true;
		document.form1.up_body.style.backgroundColor = '#EFEFEF';
		document.form1.up_image.style.backgroundColor = '#FFFFFF';
	} else {
		document.form1.up_image.disabled=true;
		document.form1.up_body.disabled=false;
		document.form1.up_body.style.backgroundColor = '#FFFFFF'; 
		document.form1.up_image.style.backgroundColor = '#EFEFEF';
	}
}
function del(){
	if (confirm("상품 상세 공통이벤트를 초기화 하시겠습니까?")) {
		document.form1.type.value="del";
		document.form1.submit();
	}
}
function CheckForm(){
	var eventloc = "";
	for(var i=0;i<form1.eventloc.length;i++){
		if(form1.eventloc[i].checked){
			eventloc=form1.eventloc[i].value;
			break;
		}
	}
	if(eventloc.length==0) {
		alert("공통 이벤트 위치를 선택하세요.");
		document.form1.eventloc[0].focus();
		return;
	}

	var design_type = "";
	for(var i=0;i<form1.up_design_type.length;i++){
		if(form1.up_design_type[i].checked){
			design_type=form1.up_design_type[i].value;
			break;
		}
	}
	if (design_type.length==0) {
		alert("이벤트 영역 디자인 타입을 선택하세요");
		form1.up_design_type[0].focus();
		return;
	} else if (design_type==1) {
		if (form1.up_image.value.length==0 && "<?=$filename?>"=="") {
			alert("이미지 파일을 선택하세요");
			form1.up_image.focus();
			return;
		}
		form1.up_body.value="";
	} else if (design_type==2) {
		if (form1.up_body.value.length==0) {
			alert("내용을 입력하세요");
			form1.up_body.focus();
			return;
		}
	}
	if (confirm("등록하시겠습니까?")) {
		document.form1.type.value="up";
		document.form1.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>상품 상세 공통 이벤트 관리</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 상세 공통 이벤트 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품 상세페이지 정보란에 진행중인 이벤트를 표기할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<div class="point_title">공통이벤트 위치 선택</div>
					<td class="bd_editer">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" bgcolor="white">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD width="100%" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
						</TR>
						<TR>
							<TD width="100%" style="padding:10pt;">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td align=center valign="top"><IMG src="images/market_detailevent1.gif" border=0 class="imgline"><br><INPUT id=idx_eventloc1 type=radio value=1 <?php if($eventloc==1)echo"checked";?> name=eventloc><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_eventloc1>상품 스펙 바로 아래</label>&nbsp;</td>
								<td align=center valign="top"><IMG src="images/market_detailevent2.gif" border=0 class="imgline"><br><INPUT id=idx_eventloc2 type=radio value=2 <?php if($eventloc==2)echo"checked";?> name=eventloc><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_eventloc2>상품 상세정보 바로 위</label>&nbsp;</td>
								<td align=center valign="top"><IMG src="images/market_detailevent3.gif" border=0 class="imgline"><br><INPUT id=idx_eventloc3 type=radio value=3 <?php if($eventloc==3)echo"checked";?> name=eventloc><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_eventloc3>상품 상세정보 바로 아래</label>&nbsp;</td>
							</tr>
							</table>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
					<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<tr>
							<th><span>디자인타입 선택</span></th>
							<td>
								<INPUT id=idx_design_type1 onclick=ChangeType(1) type=radio value=1 <?=$chk_type1?> name=up_design_type><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_design_type1>이미지로 등록하기</LABEL> 
								<INPUT id=idx_design_type2 onclick=ChangeType(2) type=radio value=2 <?=$chk_type2?> name=up_design_type><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_design_type2>HTML로 편집하기</LABEL>
							</td>
						</tr>
					</table>
				</div>
				</td>
			</tr>
			<tr>
				<td class="td_con1">
				<!--이미지로 등록시 출력-->
				<INPUT type=file name=up_image size="50"><br>
				<!--
				<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly"> 
				<div class="file_input_div">
				<input type="button" value="찾아보기" class="file_input_button" /> 
				<INPUT style="WIDTH:70%;" type=file name=up_image class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ><br />
				</div>
				-->
				&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* 이미지는 150KB 이하의 GIF, JPG만 가능</span>
<?php
				if (ord($filename)) {
					if (file_exists($imagepath.$filename)) {
						$width = getimagesize($imagepath.$filename);
						if ($width[0]>=700) $width=" width=700 ";
					}
?>
					<img width=20 height=0><img src="<?=$imagepath.$filename?>" <?=$width?>>
					<input type=hidden name=old_image value="<?=$filename?>">
<?php
				}
?>
				<!--이미지로 등록시출력 끝 -->
				<!--html로 등록시 출력-->
				<TEXTAREA style="WIDTH:100%;" disabled name=up_body rows=10 wrap=off class="textarea"><?=$body?></TEXTAREA>
				<!--html로 등록시 출력 끝 -->
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;<a href="javascript:del();"><img src="images/btn_initialization.gif"  border="0" hspace="1"></a></td>
			</tr>
			<tr>
				<td height="20">;</td>
			</tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>상품상세 공통이벤트 관리 주의사항</span></dt>
							<dd>
								- 상품상세 공통이벤트는 "상품 상세화면 템플릿"을 사용하는 모든 상품에 출력됩니다.<br>
						<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(2,'design_pdetail.php');"><span class="font_blue">디자인 관리 > 템플릿-메인 및 카테고리 > 상품 상세화면 템플릿</span></a><Br>
								- [초기화] 버튼 클릭시 모든 내용은 삭제되며 복원되지 않으므로 신중히 처리하시기 바랍니다.
							</dd>
							
						</dl>
						<dl>
							<dt><span class="font_orange">상품상세 공통이벤트 등록 방법</span></dt>
							<dd>
								① 상품상세에 공통이벤트를 출력할 위치(상품 스펙 바로 아래, 상품 상세정보 바로 위, 상품 상세정보 바로 아래)를 선택합니다.<br>
								② 디자인 타입을 선택 후 디자인을 입력합니다.<br>
								③ 디자인을 모두 입력하였다면 [적용하기] 버튼을 클릭합니다.
							</dd>
						</dl>
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<script>ChangeType(<?=$design_type?>);</script>
<?=$onload?>
<?php 
include("copyright.php");
