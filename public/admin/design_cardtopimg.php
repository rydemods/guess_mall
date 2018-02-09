<?php // hsprak
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-3";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$cardimg=$_FILES["cardimg"];

$imagepath = $Dir.DataDir."shopimages/etc/";

if($type=="upload" && $cardimg['size']>0) {
	$ext = strtolower(pathinfo($cardimg['name'],PATHINFO_EXTENSION));
	if($ext!="gif") {
		alert_go('카드결제창 상단이미지는 gif 이미지 파일만 업로드 가능합니다.');
	}
	if($cardimg['size']>153600) {
		alert_go('올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.');
	}
	$size=getimageSize($cardimg['tmp_name']);
	if((435>$size[0] || $size[0]>445) || (54>$size[1] || $size[1]>64)){
		alert_go('카드결제창 상단이미지 사이즈는 440X59픽셀의 이미지로 업로드 하시기 바랍니다.');
	}
	$filepath = $imagepath."cardimg_kcp.gif";
	move_uploaded_file($cardimg['tmp_name'],$filepath);
	chmod($filepath,0666);
	$onload="<script>window.onload=function(){alert(\"카드결제창 상단이미지 등록이 완료되었습니다.\");}</script>";
} else if($type=="delete") {
	unlink($imagepath."cardimg_kcp.gif");
	$onload="<script>window.onload=function(){alert(\"카드결제창 상단이미지 삭제가 완료되었습니다.\");}</script>";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(document.form1.cardimg.value.length==0) {
		alert("업로드 이미지를 선택하세요.");
		return;
	}
	document.form1.type.value="upload";
	document.form1.submit();
}

function CheckDelete() {
	if(confirm("카드결제창 상단이미지를 삭제하시겠습니까?")) {
		document.form2.type.value="delete";
		document.form2.submit();
	}
}
</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 템플릿-페이지 본문 &gt;<span>카드결제창 로고</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" >
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 타이틀 -->
					<div class="title_depth3">카드결제창 로고</div>
					<div class="title_depth3_sub"><span>카드결제창의 상단이미지를 쇼핑몰에 맞게 변경/관리하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">디자인 영역</div>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr>
				<td align=center><img src="images/cardimg_sample1.gif" border="0" class="imgline"></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td height=3 style="padding-top:2px;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD width="100%"><div class="point_title">파일업로드 하기</div></TD>
						</TR>
						<TR>
							<TD width="100%" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD width="100%" style="padding:10pt;" bgcolor="#f8f8f8">
							<table cellpadding="0" cellspacing="0" width="100%">
<?php
			$uploadbtn="add2";
			if(file_exists($imagepath."cardimg_kcp.gif")) {
				echo "<tr>\n";
				echo "	<td width=\"100%\">\n";
				echo "		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				echo "			<tr>\n";
				echo "				<td><img src=\"{$imagepath}cardimg_kcp.gif\" border=\"0\"></td>\n";
				echo "				<td align=right><A HREF=\"javascript:CheckDelete()\"><img src=\"images/btn_del.gif\" border=\"0\"></A></td>\n";
				echo "			</tr>\n";
				echo "		</table>\n";
				echo "	</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td width=\"100%\"><hr size=\"1\" noshade color=\"#EBEBEB\"></td>\n";
				echo "</tr>\n";
				$uploadbtn="edit3";
			}
?>
							<tr>
								<td width="100%">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td class="td_con1" width="100%">
										<input type=file name="cardimg" size="50"><br>
									<!--
										<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly">
										<div class="file_input_div">
										<input type="button" value="찾아보기" class="file_input_button" /> 
										<input type=file name="cardimg" size="40" style="width:98%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" >>
									</div>	
									-->
									</td>
									<td width="102" align=rught><A HREF="javascript:CheckForm()"><img src="images/btn_<?=$uploadbtn?>.gif" border="0"></a></td>
								</tr>
								<tr>
									<td colspan="2"><span class="font_orange">* 이미지 파일 종류는 GIF(gif) 파일만 등록 가능합니다.<br>
									* 업로드 가능 사이즈는 150KB 이하입니다.<br>* 이미지 사이즈는 가로 440 X 세로 59 픽셀로 등록 가능합니다.</span></td>
								</tr>
								</table>
								</td>
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
			</form>
			<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
			<input type=hidden name=type>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>결제창 로고 변경</span></dt>
                        <dd>- 결제창 로고는 PG사마다 다를 수 있습니다.<br />- 결제창은 PG사의 페이지로 디자인 변경되지 않습니다.(템플릿 및 개별디자인 없음)</dd>
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
<?=$onload?>

<?php 
include("copyright.php"); 