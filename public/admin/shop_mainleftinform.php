<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-2";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath = $Dir.DataDir."shopimages/etc/";

$type=$_POST["type"];
$old_image=$_POST["old_image"];
$up_design_type=$_POST["up_design_type"];
$up_body=chop($_POST["up_body"]);
$up_image=$_FILES["up_image"];

$chk_false=0;

if ($type=="up") {
	if ($up_design_type==1) {
		$ext = strtolower(pathinfo($up_image['name'],PATHINFO_EXTENSION));
		if ($up_image['name'] && in_array($ext,array('gif','jpg'))) {
			if ($up_image['size']<153600) {
				$up_image['name']="leftevent.".$ext;
				if(ord($old_image) && file_exists($imagepath.$old_image)) {
					unlink($imagepath.$old_image);
				}
				move_uploaded_file($up_image['tmp_name'],$imagepath.$up_image['name']);
				chmod($imagepath.$up_image['name'],0606);
			} else {
				$up_image['name'] = $old_image;

				$chk_false=1;
			}
		} else {
			$up_image['name'] = $old_image;

			$chk_false=2;
		}
	} else {
		@unlink($imagepath.$old_image);
	}

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage ";
	$sql.= "WHERE type = 'leftevent'";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	pmysql_free_result($result);
	$cnt=$row->cnt;
	
	if($chk_false==0){
		if ($cnt==1) {
			$sql="UPDATE tbldesignnewpage SET ";
			$sql.= "filename	= '{$up_image['name']}', ";
			$sql.= "body		= '{$up_body}', ";
			$sql.= "code		= '{$up_design_type}' ";
			$sql.= "WHERE type = 'leftevent'";
			$onload="<script>window.onload=function(){ alert('알림영역 내용 수정이 완료되었습니다.'); }</script>";
		} else {
			$sql="INSERT INTO tbldesignnewpage (type,subject,body,filename,code) VALUES ('leftevent','메인 고객 알림영역 디자인','{$up_body}','{$up_image['name']}','{$up_design_type}')";
			$onload="<script>window.onload=function(){ alert('알림영역 내용 등록이 완료되었습니다.'); }</script>";
		}
		pmysql_query($sql,get_db_conn());
	}else{
		
		if($chk_false==1){
			$onload="<script>window.onload=function(){ alert('150KB이하만 등록가능합니다.'); }</script>";
		}else if($chk_false==2){
			$onload="<script>window.onload=function(){ alert('gif,jpg만 등록 가능합니다.'); }</script>";
		}
		
	}
} else if ($type=="del") {
	$sql="DELETE FROM tbldesignnewpage WHERE type = 'leftevent'";
	$result = pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert('알림영역이 초기화 되었습니다.'); }</script>";
}

$sql = "SELECT body,filename,code FROM tbldesignnewpage ";
$sql.= "WHERE type = 'leftevent'";
$result = pmysql_query($sql,get_db_conn());
if ($row = pmysql_fetch_object($result)) {
	$body=$row->body;
	$filename=$row->filename;
	$design_type=$row->code;
} else {
	$filename="";
	$design_type="1";
}
pmysql_free_result($result);

${"chk_type".$design_type} = "checked";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script>
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
	if (confirm("고객 알림영역을 초기화 하시겠습니까?")) {
		document.form1.type.value="del";
		document.form1.submit();
	}
}
function CheckForm(){
	var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.up_body.value=sHTML;

	var design_type = "";
	for(var i=0;i<form1.up_design_type.length;i++){
		if(form1.up_design_type[i].checked){
			design_type=form1.up_design_type[i].value;
			break;
		}
	}
	if (design_type.length==0) {
		alert("알림영역 디자인 타입을 선택하세요");
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>왼쪽 고객 알림 디자인</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">왼쪽 고객 알림 디자인</div>
					<br />
					<div class="help_info01_wrap">
						<ul>
							<li>1) 쇼핑몰 메인 왼쪽 하단 공간에 고객에게 알리는 이벤트/고객알림 등을 등록할 수 있습니다.</li>
							<li>2) <a href="javascript:parent.topframe.GoMenu(2,'design_eachleftmenu.php');">디자인관리 > 개별디자인 - 메인 및 상하단 > 왼쪽메뉴 꾸미기</a>를 사용 할 경우에는 적용되지 않습니다.</li>
							<li>3) 이미지 등록방식, html등록방식중 1개만 선택가능합니다.</li>
						</ul>
					</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr>
				<td>
					<dl class="setup_info">
						<dt>이미지 등록</dt>
						<dd>
							<ul>
								<li>1) GIF(gif), JPG(jpg)파일, 가로 200픽셀을 권장(세로사이즈 제한 없음). 200픽셀 이상일 경우 해당 부분은 미출력.</li>
								<li>2) 업로드 가능한 이미지 용량은 150KB 이하입니다.</li>
							</ul>
						</dd>
					</dl>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9;">
				<TR>
					<TD class="table_cell" align="center"><input type=radio id="idx_design_type1" name=up_design_type value=1 <?=$chk_type1?> onclick="ChangeType(1)"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_design_type1><b>알림영역 이미지로 등록하기</b></label></td>
				</TR>
				<TR>
					<TD class="td_con1" align="center"><input type=file name=up_image >
<?php
	if (ord($filename)) {
		if (file_exists($imagepath.$filename)) {
			$width = getimagesize($imagepath.$filename);
			if ($width[0]>=200) $width=" width=200 ";
		}
?>
						<img width=20 height=0><img src="<?=$imagepath.$filename?>" <?=$width?>>
						<input type=hidden name=old_image value="<?=$filename?>">
<?php
	}
?>
					</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">HTML 편집</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9;">
				<TR>
					<TD class="table_cell" align="center"><input type=radio id="idx_design_type2" name=up_design_type value=2 <?=$chk_type2?> onclick="ChangeType(2)"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_design_type2>알림영역 HTML로 편집하기</label>(가로 200픽셀 권장, 높이 제한 없음)</b>&nbsp;&nbsp;<span class="font_orange">* 알림영역 이미지로 등록된 파일은 자동 삭제 처리됩니다.</span></TD>
				</TR>
				<TR>
					<TD class="td_con1" align="left"><textarea name=up_body rows=10 wrap=off style="WIDTH: 100%; BACKGROUND-COLOR: #efefef" class="textarea" id = 'ir1'><?=$body?></textarea></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:del();"><img src="images/btn_initialization.gif" border="0" hspace="2"></a></td>
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
    	                    <dt><span>이벤트, 고객상담시간, 계좌번호등</span></dt>
	                        <dd><img src="images/shop_mainleftinform_img.gif" border="0" align="left">
                                - 이미지 또는 직접 html로 왼쪽 공간을 활용할 수 있습니다.<br />- 여러 개의 배너를 html로 등록하거나 이벤트 상품소개등 다양한 디자인 공간으로 활용하세요.<br />- 개별디자인, Easy디자인에서 순서를 왼쪽의 배치순서를 변경 할 수 있습니다.<br />&nbsp;&nbsp;&nbsp;<img src="images/shop_mainleftinform_img1.gif" border="0">
                            </dd>
                        </dl>
                        <dl>
                        	<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
                        </dl>
                     </div>
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
<script type="text/javascript">
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

<script>ChangeType(<?=$design_type?>);</script>
<?=$onload?>
<?php 
include("copyright.php");
