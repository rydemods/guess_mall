<? include dirname(__FILE__) . "/../library.php"; ?>
<title>이미지 삽입</title>
<style>
body {background:#D6D3CE}
body,td,input {font:9pt 굴림}
</style>
<script language="JScript">

var imgWidth = 300;
var imgHeight = 200;
var opener = window.dialogArguments;
var tmpImg = new Image();
var tmpCut;

function img_preview(src,cut)
{
	tmpCut = cut;
	document.getElementById('preview').innerHTML = "<img id=prevImg onload='img_resize(this)' onerror=this.src='images/preview.gif'>";
	tmpImg.src = src;
	tmpImg.onload = loadImgSize;
	document.getElementById('prevImg').src = tmpImg.src;
}

function loadImgSize()
{
	if (tmpCut==1) return;
	document.forms[0].imgWidth.value = tmpImg.width;
	document.forms[0].imgHeight.value = tmpImg.height;
}

function img_resize(obj)
{
	if (obj.width*2>obj.height*3){
		if (obj.width>imgWidth) obj.width = imgWidth;
	} else {
		if (obj.height>imgHeight) obj.height = imgHeight;
	}
}

</script>

<? include "../../admin/proc/warning_disk_msg.php"; # not_delete  ?>
<form method=post target=ifrm action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="imgUpload">

<table width=100%>
<tr>
	<td style="font:bold 22px tahoma;padding:10 10 0 10">
	INSERT IMAGE
	</td>
</tr>
<tr>
	<td>
	<table width=100%><tr><td nowrap>- 미리보기</td><td width=100%><hr></td></tr></table>
	</td>
</tr>
<tr>
	<td align=center>
	<table><tr><td id=preview align=center style="width:304px;height:204px;background:#ffffff;border:1 solid #000000">&nbsp;</td></tr></table>
	</td>
</tr>
<tr>
	<td>
	<table width=100%><tr><td nowrap>- 이미지삽입</td><td width=100%><hr></td></tr></table>
	</td>
</tr>
<tr>
	<td style="padding:0 10">
	
	삽입할 이미지 선택 :
	<table width=100%>
	<tr>
		<td><input type=file name=mini_file style="width:100%" onchange="img_preview(this.value)"></td>
	</tr>
	</table>
	
	</td>
</tr>
<tr>
	<td>
	<table width=100%><tr><td nowrap>- 이미지사이즈</td><td width=100%><hr></td></tr></table>
	</td>
</tr>
<tr>
	<td style="padding:0 10">
	
	<table width=100%>
	<tr>
		<td>가로사이즈 : <input type=text name=imgWidth size=10></td>
		<td>세로사이즈 : <input type=text name=imgHeight size=10></td>
	</tr>
	</table>
	
	</td>
</tr>
<tr><td><hr></td></tr>
<tr>
	<td align=center style="padding:5">
	
	<input type=submit value="확인" style="width:100">
	<input type=button value="취소" style="width:100" onclick="window.close()">
	
	</td>
</tr>
</table>

</form>
<iframe name=ifrm style="display:none"></iframe>

<script>
img_preview("images/preview.gif",1);
</script>
<SCRIPT LANGUAGE="JavaScript" SRC="../../admin/proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>