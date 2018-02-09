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

$type=$_POST["type"];
$design=(int)$_POST["design"];
$filelogo=$_FILES["filelogo"];

$imagepath = $Dir.DataDir."shopimages/etc/";

if($type=="update") {
	if($design==0) {
		$sql = "DELETE FROM tbldesignnewpage WHERE type='bottom' ";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='bottom' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		if($row->cnt==0) {
			$sql = "INSERT INTO tbldesignnewpage(type,subject,code) VALUES('bottom','쇼핑몰 하단','{$design}')";
			pmysql_query($sql,get_db_conn());
		} else {
			$sql = "UPDATE tbldesignnewpage SET 
			code		= '{$design}' 
			WHERE type='bottom' ";
			pmysql_query($sql,get_db_conn());
		}
		pmysql_free_result($result);
	}
	$onload="<script>window.onload=function(){alert(\"쇼핑몰 하단 템플릿 설정이 완료되었습니다.\");}</script>";
} elseif($type=="logo" && $filelogo['size']>0) {
	$filelogo['name'] = str_replace(" ","",strtolower($filelogo['name']));
	if(ord($filelogo['name'])) {
		$ext = strtolower(pathinfo($filelogo['name'],PATHINFO_EXTENSION));
		if(!in_array($ext,array('gif','jpg','png'))) {
			alert_go('쇼핑몰 로고는 gif, jpg, png 이미지 파일만 업로드 가능합니다.');
		}
		$size=getimageSize($filelogo['tmp_name']);
		if((190>$size[0] || $size[0]>210) || (69>$size[1] || $size[1]>79)){
			alert_go('쇼핑몰 로고 이미지 사이즈는 200X74픽셀의 이미지로 업로드 하시기 바랍니다.');
		}
		$filepath = $imagepath."bottom_logo.gif";
		unlink("$filepath");
		move_uploaded_file($filelogo['tmp_name'],$filepath);
		chmod($filepath,0606);
		$onload="<script>window.onload=function(){alert(\"쇼핑몰 로고 등록이 완료되었습니다.\");}</script>";
	}
}

$sql = "SELECT code FROM tbldesignnewpage WHERE type='bottom' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$design=$row->code;
	if($design<3) $design=0;
} else {
	$design=0;
}
pmysql_free_result($result);
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(confirm("선택하신 디자인으로 변경하시겠습니까?\n\n지금 변경하시면 하단 개별디자인은 무시됩니다.")) {
		document.form1.type.value="update";
		document.form1.submit();
	}
}

function CheckForm2() {
	try {
		if(document.form2.filelogo.value.length==0) {
			alert("쇼핑몰 로고 이미지를 선택하세요.");
			document.form2.filelogo.focus();
			return;
		}
	} catch (e) {
		alert("쇼핑몰 로고 이미지를 선택하세요.");
		return;
	}
	document.form2.type.value="logo";
	document.form2.submit();
}
</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 템플릿-메인, 카테고리 &gt;<span>쇼핑몰 하단 템플릿</span></p></div></div>

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
					<div class="title_depth3">쇼핑몰 하단 템플릿</div>
				</td>
			</tr>            
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 하단 화면 디자인을 선택하여 사용하실 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><input type=radio name="design" value="0" <?php if($design==0)echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><B>텍스트형</B></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="21">&nbsp;</td>
						<td width="739"><img src="images/design_bottom_img1.gif" border="0" class="imgline"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><p>&nbsp;</p></td>
				</tr>
				<tr>
					<td><input type=radio name="design" value="3" <?php if($design==3)echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><B>표준 약관형</B></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="21" rowspan="2"><p>&nbsp;</p></td>
						<td width="739"><p><img src="images/design_bottom_img2.gif" border="0" class="imgline"></p></td>
					</tr>
					<tr>
						<td width="739" class="font_orange" style="padding-top:3pt;"><p>* 공정위 표준 약관 사용하는 경우에만 사용하세요.</p></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><p>&nbsp;</p></td>
				</tr>
				<tr>
					<td><input type=radio name="design" value="4" <?php if($design==4)echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><B>쇼핑몰 로고형</B></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="21" rowspan="2"><p>&nbsp;</p></td>
						<td width="739" height="96" style="background:url('images/design_bottom_img3.gif') no-repeat;">
                            <table class="imgline" cellpadding="0" cellspacing="0" width="100%" height="100%">
                            <tr>
                                <td>
                                <table cellpadding="0" cellspacing="0" width="200" height="74">
                                <tr>
                                    <td><p align="center">
                                    <?php
                                    if(file_exists($imagepath."bottom_logo.gif")) {
                                        echo "<img src=\"{$imagepath}bottom_logo.gif\" align=absmiddle>";
                                    } else {
                                        echo "&nbsp;";
                                    }
                                    ?></p></td>
                                </tr>
                                </table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					
					</table>
					</td>
				</tr>
				<tr>
					<td><p>&nbsp;</p></td>
				</tr>
				<tr>
					<td><input type=radio name="design" value="5" <?php if($design==5)echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><B>TEM001 형</B></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="21" rowspan="2"><p>&nbsp;</p></td>
						<td width="739" height="96" style="background:url('images/design_bottom_img3.gif') no-repeat;">
                            <table class="imgline" cellpadding="0" cellspacing="0" width="100%" height="100%">
                            <tr>
                                <td>
                                <table cellpadding="0" cellspacing="0" width="200" height="74">
                                <tr>
                                    <td><p align="center">
                                    <?php
                                    if(file_exists($imagepath."bottom_logo.gif")) {
                                        echo "<img src=\"{$imagepath}bottom_logo.gif\" align=absmiddle>";
                                    } else {
                                        echo "&nbsp;";
                                    }
                                    ?></p></td>
                                </tr>
                                </table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					
					</table>
					</td>
				</tr>
				<tr>
					<td><p>&nbsp;</p></td>
				</tr>
				<tr>
					<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
				</tr>
				<tr>
					<td><p>&nbsp;</p></td>
				</tr>
				</table>
				</td>
			</tr>
			</form>
            <tr>
            	<td>
                <table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%" bgcolor="#0099CC">
							<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
							<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
							<input type=hidden name=type>
							<tr>
								<td width="100%">
                                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                            <TR>
                                <TD width="100%"><div class="point_title">쇼핑몰 로고 등록</div></TD>
                            </TR>
								<TR>
									<TD width="100%" background="images/table_con_line.gif"></TD>
								</TR>
								<TR>
									<TD width="100%" style="padding:7pt;" bgcolor="#f8f8f8">
									<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="100%">
										<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<TD class="td_con1" width="580">
												<input type=file name="filelogo" >
												<!--div class="file_input_div">
												<input type="button" value="찾아보기" class="file_input_button" />
												<input type=file name="filelogo" style="width:80%;" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" >
												</div-->
												<INPUT style="FONT-SIZE: 8pt; BORDER-LEFT-COLOR: #666666; BORDER-BOTTOM-COLOR: #666666; WIDTH: 110px; COLOR: #ffffff; BORDER-TOP-COLOR: #666666; FONT-FAMILY: Tahoma; BACKGROUND-COLOR: #666666; BORDER-RIGHT-COLOR: #666666" onclick="CheckForm2();" type=button value="쇼핑몰 로고 등록">
											</td>
										</tr>
										</table>
										</td>
									</tr>
									<tr>
										<td width="100%" height="25" class="font_orange"><p class="LIPoint">* 쇼핑몰 로고 크기는 <B>200</B>X<B>74</B>픽셀로 제작하셔야 합니다.<br>
										* 업로드 가능 이미지는 GIF(gif), JPG(jpg), PNG(png)만 가능합니다.</p></td>
									</tr>
									</table>
									</TD>
								</TR>
								</TABLE>
								</td>
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
					  <dt><span>하단 안내글 내용 표기</span></dt>
                      <dd>- <a href="javascript:parent.topframe.GoMenu(1,'shop_basicinfo.php');"><span class="font_blue">상점관리 > 상점 기본정보 설정 > 상점 기본정보 관리</span></a> 에서 입력되는 상호 및 연락처가 자동으로 표기됩니다.</dd>
                    </dl>
                    <dl>
                    	<dt><span>개별 디자인</span></dt>
                        <dd>- <a href="javascript:parent.topframe.GoMenu(2,'design_eachbottom.php');"><span class="font_blue">디자인관리 > 개별디자인-메인 및 상하단 > 하단화면 꾸미기</span></a> 에서 개별 디자인을 할 수 있습니다.<br />- 개별 디자인 사용시 템플릿은 적용되지 않습니다.</dd>
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
</table>
<?=$onload?>
<?php 
include("copyright.php");
