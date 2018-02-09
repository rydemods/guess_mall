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

$logoimagepath = $Dir.DataDir."shopimages/etc/";
$bannerimagepath = $Dir.DataDir."shopimages/banner/";

$type=$_POST["type"];
$up_logo=$_FILES["up_logo"];
$up_image=$_FILES["up_image"];
$up_border=$_POST["up_border"];
$up_url_type=$_POST["up_url_type"];
$up_url=$_POST["up_url"];
$up_target=$_POST["up_target"];
$up_banner_loc=$_POST["up_banner_loc"];
$place=$_POST["place"];

$CurrentTime = date("YmdHis");

if ($type=="up") {
	if ($up_logo['name']) {
		$ext = strtolower(pathinfo($up_logo['name'],PATHINFO_EXTENSION));
		if ($ext!="gif") {
			//$onload = "<script>alert (\"올리실 이미지는 gif파일만 가능합니다.\");</script>";
			$onload="<script>window.onload=function(){alert(\"올리실 이미지는 gif파일만 가능합니다.\");}</script>";
		} else if ($up_logo['size']>153600) {
			//$onload = "<script>alert (\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");</script>";
			$onload="<script>window.onload=function(){alert(\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");}</script>";
		} else {
			move_uploaded_file($up_logo['tmp_name'],$logoimagepath."logo.gif"); 
			chmod($logoimagepath."logo.gif",0606);
			//$onload = "<script>alert('쇼핑몰 로고 등록이 완료되었습니다.');</script>";
			$onload="<script>window.onload=function(){alert(\"쇼핑몰 로고 등록이 완료되었습니다.\");}</script>";
		}
	}
	if ($up_banner_loc) {
		$sql = "UPDATE tblshopinfo SET banner_loc='{$up_banner_loc}' ";
		pmysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");
		if(ord($onload)==0) {
			//$onload = "<script>alert('정보 수정이 완료되었습니다.');</script>";
			$onload="<script>window.onload=function(){alert(\"정보 수정이 완료되었습니다.\");}</script>";
		}
	}
} else if ($type=="logodel") {
	unlink($logoimagepath."logo.gif");
	//$onload="<script>alert ('쇼핑몰 로고 삭제가 완료되었습니다.');</script>";
	$onload="<script>window.onload=function(){alert(\"쇼핑몰 로고 삭제가 완료되었습니다.\");}</script>";
} else if ($type=="bannerdel") {
	if ($up_url) {
		$sql = "SELECT image FROM tblbanner ";
		$sql.= "WHERE date = '{$up_url}'";
		$result = pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->image && file_exists($bannerimagepath.$row->image)) {
				unlink($bannerimagepath.$row->image);
			}
		}
		pmysql_free_result($result);
		$sql = "DELETE FROM tblbanner WHERE date = '{$up_url}'";
		pmysql_query($sql,get_db_conn());
		//$onload = "<script>alert('배너 삭제가 완료되었습니다.');</script>";
		$onload="<script>window.onload=function(){alert(\"배너 삭제가 완료되었습니다.\");}</script>";
	}
} else if ($type=="banneradd") {
	if($up_image['name'] && $up_url) {
		if (strpos($up_image['name'],"html") || strpos($up_image['name'],"php") || strpos($up_image['name'],"htm")) $up_image['name'] = $up_image['name']."_";
		$banner_ext= strtolower(substr($up_image['name'],-4));
		if($banner_ext!=".gif" && $banner_ext!=".jpg" && $banner_ext!=".png"){
			//$onload = "<script>alert (\"올리실 이미지는 gif파일만 가능합니다.\");</script>";
			$onload="<script>window.onload=function(){alert(\"올리실 이미지는 gif파일만 가능합니다.\");}</script>";
		} else if ($up_image['size']>153600) {
			//$onload = "<script>alert (\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");</script>";
			$onload="<script>window.onload=function(){alert(\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");}</script>";
		} else {
			$sql = "SELECT COUNT(*) as cnt FROM tblbanner ";
			$result = pmysql_query($sql,get_db_conn());
			$row = pmysql_fetch_object($result);
			pmysql_free_result($result);
			$cnt=(int)$row->cnt;
			if ($cnt<10) {
				$banner_name = $up_image['name'];
				move_uploaded_file($up_image['tmp_name'],$bannerimagepath.$banner_name); 
				chmod($bannerimagepath.$banner_name,0606);
				$sql = "INSERT INTO tblbanner(
				date		,
				image		,
				border		,
				url_type	,
				url		,
				target) VALUES (
				'{$CurrentTime}', 
				'{$banner_name}', 
				'{$up_border}', 
				'{$up_url_type}', 
				'{$up_url}', 
				'{$up_target}')";
				pmysql_query($sql,get_db_conn());
				//$onload="<script>alert('배너 등록이 완료되었습니다.');</script>";
				$onload="<script>window.onload=function(){alert(\"배너 등록이 완료되었습니다.\");}</script>";
			} else {
				//$onload="<script>alert('배너 등록은 최대 10개까지만 등록이 가능합니다.');</script>";
				$onload="<script>window.onload=function(){alert(\"배너 등록은 최대 10개까지만 등록이 가능합니다.\");}</script>";
			}
		}
	}
} else if ($type=="bannersort") {
	$banner=explode(",",$place);
	$date1=date("Ym");
	$date=date("dHis");
	for($i=0;$i<count($banner);$i++){
		$date--;
		$date = sprintf("%08d",$date);
		$sql = "UPDATE tblbanner SET date='$date1$date' ";
		$sql.= "WHERE date = '{$banner[$i]}'";
		pmysql_query($sql,get_db_conn());
	}
}

$sql = "SELECT banner_loc FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$banner_loc = $row->banner_loc;
}
pmysql_free_result($result);
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm(type) {
	if (type=="logodel") {
		if (!confirm("쇼핑몰 로고를 삭제하시겠습니까?")) {
			return;
		}
	}
	form1.type.value=type;
	form1.submit();
}

function BannerDel(date) {
	if(confirm("배너를 삭제하시겠습니까?")) {
		form1.type.value="bannerdel";
		form1.up_url.value = date;
		form1.submit();
	}
}

function BannerAdd() {
	if(!form1.up_image.value){
		alert('배너 이미지를 등록하세요');
		form1.up_image.focus();
		return;
	}
	if(!form1.up_url.value){
		alert('배너에 연결할 URL를 입력하세요. \n(예: www.abc.com)');
		form1.up_url.focus();
		return;
	}
	form1.type.value="banneradd";
	form1.submit();
}

function BannerSort(cnt){
	arr_sort = new Array();
	var val;
	for(i=1;i<=cnt;i++){
		val=form1.bannerplace[i].options[form1.bannerplace[i].selectedIndex].value;
		if (arr_sort[val]) {
			alert("배너 순서가 중복되거나 잘못되었습니다.");
			return;
		} else {
			arr_sort[val] = form1.bannerdate[i].value;
		}
	}
	var result = arr_sort.join(",").substring(1);

	document.form1.place.value=result;
	document.form1.type.value="bannersort";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>로고/배너 관리</span></p></div></div>
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
					<div class="title_depth3">로고/배너 관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰의 로고 및 배너를 등록/관리하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 로고 등록</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=place>
			<input type=hidden name=bannerplace>
			<input type=hidden name=bannerdate>
			<tr>
				<td style="padding-top:3pt;">
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><center><img src="images/shop_logobanner_img1.gif" border="0"></center></th>
					<td class="td_con1" >
                    	<div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" width="100%"><img src="images/icon_point2.gif" border="0"><b>로고이미지 업로드</b></TD>
						</TR>
						<TR>
							<TD width="100%" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD  style="PADDING-RIGHT: 5px; PADDING-LEFT: 10px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left width="100%" bgColor=#ffffff><input type=file name=up_logo style="WIDTH: 100%"><br>* 로고 이미지 크기는 200X65 사이즈의 GIF파일로 제작하세요.</TD>
						</TR>
						<TR>
							<TD class=linebottomleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 10px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left width="100%" bgColor=#ffffff><p>
							<?php if (file_exists($logoimagepath."logo.gif")) {?>
							<img src="<?=$logoimagepath?>logo.gif?id=<?=time()?>" border=0 style="border-width:1pt; border-color:rgb(235,235,235); border-style:solid;"> <a href="javascript:CheckForm('logodel');"><img src="images/btn_del.gif" border="0" hspace="3"></a>
							<?php } else { ?>
							등록된 로고가 없습니다.
							<?php } ?></p></TD>
						</TR>
						</TABLE>
                        </div>
					</td>
				</TR>
				</TABLE>
				</div>
                </td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('up');"><img src="images/botteon_save.gif" border="0" vspace="3"></a></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 배너 관리</span></div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                <col width="60" />
                <col width="400" />
                <col width=""/>
                <col width="60" />
				<TR>
					<th>순서</th>
                    <th>배너이미지</th>
                    <th>링크주소</th>
                    <th>삭제</th>
				</TR>
				
<?php
	$sql0 = "SELECT COUNT(*) as cnt FROM tblbanner ";
	$result = pmysql_query($sql0,get_db_conn());
	$row = pmysql_fetch_object($result);
	pmysql_free_result($result);
	$cnt = $row->cnt;

	$sql = "SELECT * FROM tblbanner ORDER BY date DESC";
	$result = pmysql_query($sql,get_db_conn());
	$count=1;
	while($row=pmysql_fetch_object($result)){
		$image = $row->image;
		$url = $row->url;
?>
				<TR>
					<TD>
					<select name=bannerplace class="select">
<?		for($i=1;$i<=$cnt;$i++){
			echo "<option value=\"{$i}\"";
			if($i==$count) echo " selected";
			echo ">".($i);
		}
?>
					</select><input type=hidden name=bannerdate value="<?=$row->date?>"></TD>
					<TD><img src="<?=$bannerimagepath.$image?>" border="<?=$row->border?>" class="imgline"></TD>
					<TD> <a href=http<?=($row->url_type=="S"?"s":"")?>://<?=$url?> target=<?=$row->target?>><font color=#0000a0>http<?=($row->url_type=="S"?"s":"")?>://<?=$url?></font></a></TD>
					<TD><p align="center"><a href="javascript:BannerDel('<?=$row->date?>');"><img src="images/btn_del.gif" border="0"></a></p></TD>
				</TR>
<?php
		$count++;
	}
	pmysql_free_result($result);
	if($cnt==0) {
		echo "<TR><td class=lineleft colspan=4 align=center><font color=#383838>등록된 배너가 없습니다.</font></td></tr>";
	}
?>
				
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
<?php
	if ($cnt > 0) {
		echo "<a href=\"javascript:BannerSort('$cnt');\"><img src=\"images/icon_sort1.gif\" border=\"0\"></a>\n";
	}
?>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
				
				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1) GIF(gif), JPG(jpg), PNG(png)파일만 등록 가능합니다.</li>
						<li>2) 배너위치가 좌측 하단일 경우 가로 200픽셀을 권장, 우측 상단일 경우 가로 180픽셀 권장(세로사이즈 제한 없음).</li>
						<li>3) 이미지 용량 150KB 이하.</li>
					</ul>
				</div>
				
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white" >
					<TR>
						<TD width="100%"><div class="point_title">배너등록하기</div></TD>
					</TR>
                    <tr>
						<td width="100%">
                        <div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>배너 이미지</span></th>
							<TD class="td_con1" ><input type=file name=up_image style="WIDTH: 98%"></TD>
						</TR>
						<TR>
							<th><span>연결 URL</span></th>
							<TD class="td_con1"><select name=up_url_type class="select">
								<option value="H">http://
								<option value="S">https://
							</select> <input type=text name=up_url size=50 maxlength=200 onKeyUp="chkFieldMaxLen(200)" class="input" ></TD>
						</TR>
						<TR>
							<th><span>Target 및 Border</span></th>
							<TD class="td_con1">
							Target : <select name=up_target class="select">
<?php 
	$target=array("_blank","_top","_parent","_self");
	for($i=0;$i<4;$i++){
		echo "<option value=\"{$target[$i]}\">".$target[$i];
	}
?>
							</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Border : <select name=up_border class="select">
<?php
	for($i=0;$i<5;$i++){
		echo "<option value=\"{$i}\">".$i;
	}
?>
							</select>
							</TD>
						</TR>
						</TABLE>
                        </div>
                        </option>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt;"><p align="center"><a href="javascript:BannerAdd();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배너 위치 설정</span></div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><center><img src="images/shop_logobanner_img2.gif" border="0"></center></th>
                    <td class="td_con1" >
                    <div class="table_none">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td class="td_con1"><input type=radio id="idx_banner_loc1" name=up_banner_loc value="L" <?php if ($banner_loc=="L") echo "checked"; ?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_banner_loc1><font color=#0256A8>①</font> 왼쪽&nbsp;&nbsp;&nbsp;&nbsp;하단</label><br><input type=radio id="idx_banner_loc2" name=up_banner_loc value="R" <?php if ($banner_loc=="R") echo "checked"; ?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_banner_loc2><font color=#0256A8>②</font> 오른쪽 상단</label></td>					</tr>
                        </table>
                    </div>
					</td>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('up');"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr>
				<td height="25">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>배너 개별디자인</span></dt>
							<dd>
								- <a href="javascript:parent.topframe.GoMenu(2,'design_eachleftmenu.php');"><span class="font_blue">디자인관리 > 개별디자인 - 메인 및 상하단 > 왼쪽메뉴 꾸미기</span></a> 에서 직접 HTML로 디자인할 수 있습니다.<br>
								- <a href="javascript:parent.topframe.GoMenu(2,'design_easyleft.php');"><span class="font_blue">디자인관리 > Easy 디자인 관리 > Easy 왼쪽 메뉴 관리</span></a> 에서 직접 HTML로 디자인할 수 있습니다.</a>
							</dd>
						</dl>
						<dl>
							<dt><span>Target 과 Bordor (새창과 이미지외곽 테두리)</span></dt>
							<dd>
								- <b>Target</b><b>&nbsp;</b>: 정보를 출력할 윈도우나 프레임을 입력하는 속성.<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange">_blank</span> <b>&nbsp;</b>: 연결된 문서를 읽어 새로운 빈 윈도우에 표시한다.<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange">_top</span> &nbsp;&nbsp;<b>&nbsp;&nbsp;</b>: 연결된 문서를 읽어 최상위 윈도우에 표시한다.<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange">_parent</span> : 연결된 문서를 읽어 바로 위 부모창에 표시한다.<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange">_self</span> <b>&nbsp;&nbsp;&nbsp;</b>: 연결된 문서를 읽어 현재창에 표시한다.<br>
								<br>
								- <b>Border</b> : 이미지 외곽에 border 값만큼 두께의 테두리 라인이 생성됩니다.
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
<?=$onload?>
<?php 
include("copyright.php");
