<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$mode=$_POST["mode"];
if($mode=="update") {
	$skin_upload_flag=$_POST["skin_upload_flag"];
	$top_skin_seq=(int)$_POST["top_skin_seq"];
	$top_color_seq=(int)$_POST["top_color_seq"];
	$left_color_seq=(int)$_POST["left_color_seq"];
	$upfile=$_FILES["skin_upload_img"];

	if($top_color_seq==0 || $left_color_seq==0) {
		echo "<html></head><body onload=\"alert('���/���� ������ �����ϼ���.')\"></body></html>";exit;
	}

	if($skin_upload_flag=="N") {
		if($top_skin_seq==0) {
			echo "<html></head><body onload=\"alert('��� ������ �⺻��Ų ������ �ȵǾ����ϴ�.')\"></body></html>";exit;
		}
	} else {
		$top_skin_seq=0;
		if($upfile["size"]<=0) {
			echo "<html></head><body onload=\"alert('�̹����� ����ϼ���.(���� 925 X ���� 130�ȼ�, 500k �̸�, jpg, gif)')\"></body></html>";exit;
		} else if($upfile["size"] > 512000) {
			echo "<html></head><body onload=\"alert('���ε� ������ �̹��� �ִ� �뷮�� 500K �̸��Դϴ�.)')\"></body></html>";exit;
		} else {
			if (strlen($upfile['name'])>0 && file_exists($upfile['tmp_name'])) {
				$ext = strtolower(pathinfo($upfile["name"],PATHINFO_EXTENSION));
				if(in_array($ext,array('gif','jpg'))){
					$imagenameorg="top_".$_VenderInfo->getVidx().".gif";
					move_uploaded_file($upfile['tmp_name'],$Dir.DataDir."shopimages/vender/".$imagenameorg);
					chmod($Dir.DataDir."shopimages/vender/".$imagenameorg,0664);
				} else {
					echo "<html></head><body onload=\"alert('�̹��� ����� gif, jpg ���ϸ� ��� �����մϴ�.\\n\\nȮ�� �� �ٽ� ����Ͻñ� �ٶ��ϴ�.')\"></body></html>";exit;
				}
			} else {
				echo "<html></head><body onload=\"alert('�̹��� ������ �ȵǾ��ų� �߸��� �̹��� �����Դϴ�.\\n\\n���� Ȯ�� �� �ٽ� ����Ͻñ� �ٶ��ϴ�.')\"></body></html>";exit;
			}
		}
	}
	$skin=$top_skin_seq.",".$top_color_seq.",".$left_color_seq;
	$sql = "UPDATE tblvenderstore SET skin='".$skin."' WHERE vender='".$_VenderInfo->getVidx()."' ";
	if(pmysql_query($sql,get_db_conn())) {
		echo "<html></head><body onload=\"alert('��û�Ͻ� �۾��� �����Ͽ����ϴ�.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('��û�Ͻ� �۾��� ������ �߻��Ͽ����ϴ�.')\"></body></html>";exit;
	}
} else if($mode=="shopwidthmodify" && $_POST["shop_width"]>0) {
	$shop_width=(int)$_POST["shop_width"];
	if($shop_width>0) {
		$sql = "UPDATE tblvenderstore SET shop_width='".$shop_width."' ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		pmysql_query($sql,get_db_conn());
		echo "<html></head><body onload=\"alert('�̴ϼ� ��ü ������ ������ �Ϸ�Ǿ����ϴ�.')\"></body></html>";exit;
	}
	exit;
}

$skins=explode(",",$_venderdata->skin);
$topskinseq=(int)(strlen($skins[0])>0?$skins[0]:1);
$topcolorseq=(int)(strlen($skins[1])>0?$skins[1]:1);
$leftcolorseq=(int)(strlen($skins[2])>0?$skins[2]:1);

$topbackimg="top001";
$topskinrgb="EEEEEE";
$topfontcolor="000000";
$leftcolorrgb="EEEEEE";
$leftfontcolor="000000";

$sql = "SELECT * FROM tblvendertitleskin ORDER BY listorder ASC ";
$result=pmysql_query($sql,get_db_conn());
$ArrTitleSkin=array();
$maxSkin=0;
while($row=pmysql_fetch_object($result)) {
	$maxSkin++;
	$ArrTitleSkin[]=$row;
	if($row->seq==$topskinseq) $topbackimg=$row->backimg;
}
pmysql_free_result($result);

$sql = "SELECT * FROM tblvenderboxgroupcolor ";
$result=pmysql_query($sql,get_db_conn());
$ArrColor=array();
while($row=pmysql_fetch_object($result)) {
	if($row->seq==$topcolorseq) {
		$topskinrgb=$row->color;
		$topfontcolor=$row->fontcolor;
	}
	if($row->seq==$leftcolorseq) {
		$leftcolorrgb=$row->color;
		$leftfontcolor=$row->fontcolor;
	}
	$ArrColor[]=$row;
}
pmysql_free_result($result);

if($topskinseq>0){
	$thumbnailTopSrc="images/sample/design/".$topskinrgb."_".$topbackimg.".gif";
} else {
	$thumbnailTopSrc=$Dir.DataDir."shopimages/vender/top_".$_VenderInfo->getVidx().".gif";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

var thumbnailPath = "images/sample/design/";
var indexSkin = 1;
var maxSkin = 0;
var skin_id_list = new Array();
var skin_image_list = new Array();

maxSkin = <?=$maxSkin?>;

<?php
$skinlst=$ArrTitleSkin;
for($i=0;$i<count($skinlst);$i++) {
	echo "skin_id_list[".($i+1)."]=".$skinlst[$i]->seq.";\n";
	echo "skin_image_list[".($i+1)."]='".$skinlst[$i]->backimg.".gif';\n";
}

$colorlst=$ArrColor;
for($i=0;$i<count($colorlst);$i++) {
	echo "var mall_name_rgb_".$colorlst[$i]->color."='".$colorlst[$i]->fontcolor."';\n";
}
?>
function goSkin(increment) {
	var oldIndex = indexSkin; 
	indexSkin = indexSkin + increment;
	if ( indexSkin < 1 ) {
		indexSkin = 1;
	}
	if ( indexSkin > skin_id_list.length - 5 ) {
		indexSkin = skin_id_list.length - 5;
	}
	if ( indexSkin != oldIndex ) {
		for ( var i = 0; i < 5; i++ ) {
			eval("iForm.img_skin" + i + ".src = thumbnailPath + skin_image_list[indexSkin + i];");
		}
	}
}

function selectSkinTop(sel_skin_index) {
	if ( iForm.top_skin_seq.value != skin_id_list[indexSkin + sel_skin_index] ) {
		iForm.top_skin_seq.value = skin_id_list[indexSkin + sel_skin_index];
		iForm.top_skin_image.value = skin_image_list[indexSkin + sel_skin_index];
		changeThumbnailTop();
	}
}

function selectColorTop(seq,sel_skin_rgb) {
	var thumbnail_img;
	if ( iForm.top_skin_rgb.value != sel_skin_rgb ) {
		iForm.top_skin_rgb.value = sel_skin_rgb;
		iForm.top_font_color.value = eval("mall_name_rgb_"+sel_skin_rgb);
		iForm.top_color_seq.value = seq;
		changeThumbnailTop();
	}
}

function changeThumbnailTop() {
	var thumbnail_img;
	if(iForm.skin_upload_flag[1].checked){
		//���ε��� ���
		var org_skin_upload_img = iForm.org_skin_upload_img.value;
		var local_bg_img = iForm.skin_upload_img.value;
		if( org_skin_upload_img == '' && local_bg_img == ''){
			alert("���ε� �̹����� �����ϼ���");
			iForm.skin_upload_img.focus();
			return;
		}
		if (local_bg_img != "" && !ValidImageFile(iForm.skin_upload_img)){
			alert("�ùٸ� ��� �̹����� �����ϼ���.");
			iForm.skin_upload_img.focus();
			return;     
		}
		if(local_bg_img == ''){
			iForm.thumbnailTop.src = "images/sample/design";
		}else{
			iForm.thumbnailTop.src = local_bg_img;      
		}
	} else {
		//�⺻ ����
		thumbnail_img = thumbnailPath + iForm.top_skin_rgb.value + "_top";
		if ( iForm.top_skin_seq.value.length == 1 ) {
			thumbnail_img = thumbnail_img + "00";
		} else if ( iForm.top_skin_seq.value.length == 2 ) {
			thumbnail_img = thumbnail_img + "0";
		}
		thumbnail_img = thumbnail_img + iForm.top_skin_seq.value + ".gif";

		iForm.thumbnailTop.src = thumbnail_img;
	}

	introLayer.innerHTML =	"<table border=0 cellpadding=0 cellspacing=0>" +
							" <tr>" +
							"   <td style=\"padding-left:7;font-size:8pt;line-height:10pt\" letter-spacing:-1><font color=" + eval("mall_name_rgb_"+iForm.top_skin_rgb.value) + "><b>������</b>/��ǰ��/ID<br>��������</font></td>" +
							" <tr>" +
							"</table>"

}

function selectColorLeft(seq,sel_skin_rgb) {
	var thumbnail_img;
	if ( iForm.left_color_rgb.value != sel_skin_rgb ) {
		iForm.left_color_rgb.value = sel_skin_rgb;
		iForm.left_font_color.value = eval("mall_name_rgb_"+sel_skin_rgb);
		iForm.left_color_seq.value = seq;
		changeThumbnailLeft();
	}
}

function changeThumbnailLeft() {
	iForm.thumbnailLeft.src = thumbnailPath + iForm.left_color_rgb.value + "_m.gif";
}

function formSubmit(proc_type) {
	if(iForm.skin_upload_flag[1].checked){
		var org_skin_upload_img = iForm.org_skin_upload_img.value;
		var local_bg_img = iForm.skin_upload_img.value;
		if( org_skin_upload_img == '' && local_bg_img == ''){
			alert("���ε� �̹����� �����ϼ���");
			iForm.skin_upload_img.focus();
			return;
		}
		if ( local_bg_img != "" && !ValidImageFile(iForm.skin_upload_img)){
			alert("�ùٸ� ��� �̹����� �����ϼ���.");
			iForm.skin_upload_img.focus();
			return;     
		}
		iForm.image_path.value=iForm.skin_upload_img.value;
	}

	if (proc_type=="preview") {
		mallWin = windowOpenScroll("", "MinishopPreview", 920, 500);
		mallWin.focus();

		iForm.target = "MinishopPreview";
		iForm.action = "preview.minishop.php";
		iForm.submit();
		iForm.action = "";
	} else {
		if (confirm("�����Ͻ� ������ �����Ͻðڽ��ϱ�?")) {
			iForm.mode.value="update";
			iForm.target = "processFrame";
			iForm.submit();
		}
	}
}

function displayUploadFlag() {
	if(iForm.skin_upload_flag[1].checked){
		displaySelect.style.display = "none";
		displayUpload.style.display = "";
	}else{
		displaySelect.style.display = "";
		displayUpload.style.display = "none";
	}
}

function SaveShopwidth() {
	if(!IsNumeric(document.form2.shop_width.value)) {
		alert("�̴ϼ� ��ü ������ �Է��� ���ڸ� �Է� �����մϴ�.");
		document.form2.shop_width.focus();
		return;
	}
	if(document.form2.shop_width.value<900) {
		alert("�̴ϼ� ��ü �������� 900 pixel �̻� �Է��ϼž��մϴ�.\n\n(���� ������ : 900 pixel)");
		document.form2.shop_width.focus();
		return;
	}
	if(confirm("�̴ϼ� ��ü �������� �����Ͻðڽ��ϱ�?")) {
		document.form2.mode.value="shopwidthmodify";
		document.form2.target = "processFrame";
		document.form2.submit();
	}
}

</script>

<table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<col width=740></col>
<col width=80></col>
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>�̴ϼ� ������ ����</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>�̴ϼ� ������ ����</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> ȸ������ �̴ϼ��� ��︮�� �����ΰ� ������ �����Ͽ� �̴ϼ��� �ٸ纸����.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> �������� �����Ͻ� �� ���� ���� �̸����⸦ �Ͻø� �̴ϼ��� �������� �̸� Ȯ���Ͻ� �� �ֽ��ϴ�.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> ���� ������ �̹����� �̴ϼ� ����� �ٹ̱� �Ͻ� ��� ��������� �����Ͻ� �� �̹����� ����ϼ���.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- ó���� ���� ��ġ ���� -->
			<tr><td height=10></td></tr>
			<tr>
				<td style="padding:15">
				<table width="100%" border=0 cellspacing=0 cellpadding=0>
				<col width=12></col>
				<col width=></col>
				<form name=form2 method=post>
				<input type=hidden name=mode>
				<tr valign=top>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> �̴ϼ� ��ü ������ ����</td>
				</tr>
				<tr>
					<td bgcolor=#E1E1E1 style="padding:5">
					<table border=0 cellpadding=6 cellspacing=0 width=100% bgcolor=#FFFFFF>
					<tr>
						<td style="padding-left:20px">
						�̴ϼ� ��ü �������� <input type=text name=shop_width value="<?=$_venderdata->shop_width?>" size=4 maxlength=4 onkeyup="strnumkeyup(this)" style="font-weight:bold"> pixel ũ��� ����մϴ�. <img width=20 height=0><font color=#2A97A7>(���� ������ <B>900</B> pixel)</font>
						</td>
						<td align="right"><img src="images/btn_save02.gif" border="0" style="cursor:hand" onClick="SaveShopwidth()"></td>
					</tr>
					</table>
					</td>
				</tr>
				</form>
				</table>
				</td>
			</tr>
			<tr>
				<td valign=top style="padding:15">
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">

				<form name=iForm method=post enctype=multipart/form-data>
				<input type=hidden name=preview_type value="design">
				<input type=hidden name=mode>
				<input type=hidden name=top_skin_seq value="<?=$topskinseq?>">
				<input type=hidden name=top_skin_rgb value="<?=$topskinrgb?>">
				<input type=hidden name=top_skin_image value="<?=$topbackimg?>.gif">
				<input type=hidden name=top_color_seq value="<?=$topcolorseq?>">
				<input type=hidden name=top_font_color value="<?=$topfontcolor?>">
				<input type=hidden name=left_color_seq value="<?=$leftcolorseq?>">
				<input type=hidden name=left_color_rgb value="<?=$leftcolorrgb?>">
				<input type=hidden name=left_font_color value="<?=$leftfontcolor?>">
				<input type=hidden name="org_skin_upload_img" value="">
				<input type=hidden name="image_path" value="">

				<col width=320></col>
				<col width=15></col>
				<col width=></col>
				<tr>
					<td valign=top>

					<table width="100%" border="0" height="265" bgcolor="#DADADA" cellspacing="1" cellpadding="0">
					<tr>
						<td bgcolor="#FFFFFF" align="center">
						<table width="284" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td style="position:relative">
							<img id="thumbnailTop" src="<?=$thumbnailTopSrc?>" width="285" height="45">
                            <div style="position:absolute;left:0;top:0;" >
                              <img src="images/sample/design/thumbtop_img.gif" width="285" height="45">
                            </div>
							<div id="introLayer" style="position:absolute;left:60;top:5;">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td style="padding-left:7;font-size:8pt;line-height:10pt" letter-spacing:-1><font color=<?=$topfontcolor?>><b>������</b>/��ǰ��/ID<br>��������</font></td>
							</tr>
							</table>                              
							</div>
							</td>
						</tr>
						<tr>
							<td><img id="thumbnailLeft" src="images/sample/design/<?=$leftcolorrgb?>_m.gif" width="285" height="180"></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>


					</td>
					<td></td>
					<td valign=top>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
						<img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>��� ������ ����</B>
						<input type="radio" name="skin_upload_flag" value="N" onClick="displayUploadFlag();" <?if($topskinseq>0)echo"checked";?>> �⺻��Ų
						&nbsp;&nbsp;<input type="radio" name="skin_upload_flag" value="Y" onClick="displayUploadFlag();" <?if($topskinseq==0)echo"checked";?>> �������
						</td>
					</tr>
					<tr><td height=5></td></tr>
					<tr id="displayUpload" style="display:<?if($topskinseq>0)echo"none";?>">
						<td style="padding:5,5">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td height=5>
							<input type=file name="skin_upload_img" value="" size=36 class=button onChange="changeThumbnailTop()">
							</td>
						</tr>
						<tr>  
							<td colspan=2 style=color:2A97A7 style="font-size:8pt">(���� 925 X ���� 130�ȼ�, 500k �̸�, jpg, gif)</td>
						</tr> 
						<tr>
							<td height=5></td>
						</tr>
						<tr>
							<td colspan=2 style="font-size:8pt">-�̴ϼ� ��� ��� �̹����� ���� ����Ͻ� �� �ֽ��ϴ�.<BR>
							-�����Ƿΰ�ǥ�� �κ��� �����Ͻ� �� �����ϴ�.<BR>
							-������/��ǰ��/id/���� ������ �⺻ �ؽ�Ʈ�� ����˴ϴ�.<BR>
							-���ε��� �̹����� �̸����⸦ ���� Ȯ���� �ּ���.
							</td>
						</tr>
						</table>								  
						</td>
					</tr>
					<!-- �߰�E -->                
					<tr id="displaySelect" style="display:<?if($topskinseq==0)echo"none";?>">
						<td background=images/design_box.gif>
						<table width=100% border=0 cellspacing=0 cellpadding=0>
						<tr height=61 align=center>
							<td width=10%><a href="javascript:goSkin(-4);"><img src=images/btn_prev03.gif border=0></a></td>
							<td width=80%>
							<table border=0 cellspacing=13 cellpadding=0>
							<tr>
								<td><a href='javascript:selectSkinTop(0);'><img name=img_skin0 id=img_skin0 src=images/sample/design/top001.gif border=0></a></td>
								<td><a href='javascript:selectSkinTop(1);'><img name=img_skin1 id=img_skin1 src=images/sample/design/top002.gif border=0></a></td>
								<td><a href='javascript:selectSkinTop(2);'><img name=img_skin2 id=img_skin2 src=images/sample/design/top003.gif border=0></a></td>
								<td><a href='javascript:selectSkinTop(3);'><img name=img_skin3 id=img_skin3 src=images/sample/design/top004.gif border=0></a></td>
								<td><a href='javascript:selectSkinTop(4);'><img name=img_skin4 id=img_skin4 src=images/sample/design/top005.gif border=0></a></td>
							</tr>
							</table>
							</td>
							<td width=10%><a href="javascript:goSkin(4);"><img src=images/btn_next03.gif border=0></a></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr><td height=15></td></tr>
					<tr>
						<td>
						<img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>��� ���� ����</B>
						</td>
					</tr>
					<tr>
						<td style="padding-left:7">
						<table border=0 cellspacing=7 cellpadding=0>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
<?php
						$cgrplst=$ArrColor;
						for($i=0;$i<count($cgrplst);$i++) {
							if($i%10==0) echo "						<tr>\n";
							echo "						<td><a href=\"javascript:selectColorTop(".$cgrplst[$i]->seq.",'".$cgrplst[$i]->color."');\"><img src=images/sample/design/color_".$cgrplst[$i]->color.".gif border=0></a></td>\n";
							if($i%10==9) echo "						</tr>\n";
						}
						if($i%10!=0) {
							while(true) {
								$i++;
								echo "						<td>&nbsp;</td>\n";
								if($i%10==0) {
									echo "						</tr>\n";
									break;
								}
							}
						}
?>
						</table>
						</td>
					</tr>
					<tr><td height=15></td></tr>
					<tr>
						<td>
						<img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>���� ���� ����</B>
						</td>
					</tr>
					<tr>
						<td style="padding-left:7">
						<table border=0 cellspacing=7 cellpadding=0>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
						<col width=10%></col>
<?php
						$cgrplst=$ArrColor;
						for($i=0;$i<count($cgrplst);$i++) {
							if($i%10==0) echo "						<tr>\n";
							echo "						<td><a href=\"javascript:selectColorLeft(".$cgrplst[$i]->seq.",'".$cgrplst[$i]->color."');\"><img src=images/sample/design/color_".$cgrplst[$i]->color.".gif border=0></a></td>\n";
							if($i%10==9) echo "						</tr>\n";
						}
						if($i%10!=0) {
							while(true) {
								$i++;
								echo "						<td>&nbsp;</td>\n";
								if($i%10==0) {
									echo "						</tr>\n";
									break;
								}
							}
						}
?>
						</table>
						</td>
					</tr>

					<tr><td height=20></td></tr>
					<tr>
						<td align=center>
						<A HREF="javascript:formSubmit('preview')"><img src="images/btn_preview01.gif" border=0></A>
						&nbsp;
						<A HREF="javascript:formSubmit('')"><img src="images/btn_save01.gif" border=0></A>
						</td>
					</tr>

					</table>
					</td>
				</tr>

				</form>

				</table>
				</td>
			</tr>
			<!-- ó���� ���� ��ġ �� -->

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
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php include("copyright.php"); ?>
