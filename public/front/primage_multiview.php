<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$imagepath=$Dir.DataDir."shopimages/multi/";

$productcode=$_REQUEST["productcode"];
$scroll=$_REQUEST["scroll"];

if($scroll=="yes")	$scrollwidth=30;
else	$scrollwidth=0;

$sql = "SELECT multi_dispos, multi_changetype, multi_bgcolor FROM tblshopinfo";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);

$dispos=$row->multi_dispos;
$changetype=$row->multi_changetype;
$bgcolor=$row->multi_bgcolor;

$sql = "SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
$result=pmysql_query($sql,get_db_conn());

if($row=pmysql_fetch_object($result)) {
	pmysql_free_result($result);

	$tmpsize=explode("",$row->size);
	$insize="";
	$updategbn="N";

	$multi_imgs=array(&$row->primg01,&$row->primg02,&$row->primg03,&$row->primg04,&$row->primg05,&$row->primg06,&$row->primg07,&$row->primg08,&$row->primg09,&$row->primg10);

	$y=0;
	for($i=0;$i<10;$i++) {
		if(ord($multi_imgs[$i])) {
			$yesimage[$y]=$multi_imgs[$i];
			if(ord($tmpsize[$i])==0) {
				$size=getimagesize($Dir.DataDir."shopimages/multi/".$multi_imgs[$i]);
				$xsize[$y]=$size[0];
				$ysize[$y]=$size[1];
				$insize.="{$size[0]}X".$size[1];
				$updategbn="Y";
			} else {
				$insize.="".$tmpsize[$i];
				$tmp=explode("X",$tmpsize[$i]);
				$xsize[$y]=$tmp[0];
				$ysize[$y]=$tmp[1];
			}
			$y++;
		} else {
			$insize.="";
		}
	}

	if($y<=5) $addwidth=140;
	else $addwidth=240;
	if($dispos=="1") $addwidth=140;

	$addwidth=$addwidth+$scrollwidth;

	$maxresizewidth="";

	for($i=0;$i<$y;$i++) {
		if($xsize[$i]<400) $resizewidth[$i]=400+$addwidth;
		else $resizewidth[$i]=$xsize[$i]+$addwidth;

		if($dispos=="0") {
			$alignsize[$i]=($y>5?$y/2:$y);
			if($y==5) {
				$alignsize[$i]=4.5;
			}
			if($ysize[$i]<90*$alignsize[$i]) $resizeheight[$i]=$alignsize[$i]*90+178;
			else $resizeheight[$i]=$ysize[$i]+130;
		} else {
			if($y>5) $alignsize[$i]=333;
			else $alignsize[$i]=242;
			$resizeheight[$i]=$ysize[$i]+$alignsize[$i];
		}
		if($maxresizewidth<$resizewidth[$i]) $maxresizewidth=$resizewidth[$i];
	}


	if($updategbn=="Y"){
		$sql = "UPDATE tblmultiimages SET size='".ltrim($insize,'')."' ";
		$sql.= "WHERE productcode='{$productcode}'";
		pmysql_query($sql,get_db_conn());
	}

} else {
	echo "<html></head><body onload=\"window.close();\"></body></html>";exit;
}

?>

<html>
<head>
<title>상품확대보기</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<meta http-equiv="imagetoolbar" content="no">
<!--<script type="text/javascript" src="/include/lib.js.php"></script>-->
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td {font-family:돋음;color:666666;font-size:9pt;}

tr {font-family:돋음;color:666666;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
var g_fIsSP2 = false;
g_fIsSP2 = (window.navigator.userAgent.indexOf("SV1") != -1);

window.moveTo(10,10);

if (g_fIsSP2) window.resizeTo(<?=$resizewidth[0]?>,<?=$resizeheight[0]+20?>);
else window.resizeTo(<?=$resizewidth[0]?>,<?=$resizeheight[0]?>);

var imagepath="<?=$imagepath?>";
function primg_preview(img,width,height) {
	if(document.primg!=null) {
		document.primg.src=imagepath+img;
		if (g_fIsSP2) height=parseInt(height)+20;
		window.resizeTo(width,height);
	}
}

function primg_preview3(img,width,height) {
	if(document.primg!=null) {
		document.primg.src=imagepath+img;
		if (g_fIsSP2) height=parseInt(height)+20;
		window.resizeTo(width,height);
	}
}

function primg_preview2(img,width,height) {
	obj = event.srcElement;
	clearTimeout(obj._tid);
	obj._tid=setTimeout("primg_preview3('"+img+"','"+width+"','"+height+"')",500);
}

function WindowClose(){
	window.close();
}
//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 oncontextmenu="return false;">

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr height=25>
	<td align=center bgcolor=#696969>
	<FONT COLOR="#FFFFFF"><B>상품 확대보기</B></FONT>
	</td>
</tr>
<tr><td height=14></td></tr>
<tr>
	<td valign=top>

	<?php if($dispos=="0"){?>
	
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td width=<?=$maxresizewidth?> align=center>
		<img src="<?=$imagepath.$yesimage[0]?>" border=0 name="primg">
		</td>

		<td width=10 nowrap></td>

		<td valign=top>
		<?php if($y<=5){?>
		<table border=0 cellpadding=0 cellspacing=1 width=90 bgcolor=#dadada>
<?php 
		for($i=0;$i<$y;$i++) {
			echo "<tr height=90 bgcolor=#FFFFFF>\n";
			echo "<td width=90 align=center>";
			if($changetype=="0") {	//마우스 오버
				echo "<a href=\"javascript:primg_preview('{$yesimage[$i]}','{$resizewidth[$i]}','{$resizeheight[$i]}')\" onmouseover=\"primg_preview2('{$yesimage[$i]}','{$resizewidth[$i]}','{$resizeheight[$i]}')\">";
			} else {	//마우스 클릭
				echo "<a href=\"javascript:primg_preview('{$yesimage[$i]}','{$resizewidth[$i]}','{$resizeheight[$i]}')\">";
			}
			echo "<img src={$imagepath}s{$yesimage[$i]} border=0></a></td>";
			echo "</tr>\n";
		}
/*
		if($i%5!=0) {
			for($j=($i%5);$j<5;$j++) {
				echo "<tr height=90 bgcolor=#ffffff>\n";
				echo "	<td width=90 align=center bgcolor=#ffffff></td>";
				echo "</tr>\n";
			}
		}
*/
?>
		</table>
		<?php }else{?>
		<table border=0 cellpadding=0 cellspacing=1 width=190 bgcolor=#dadada>
<?php 
		for($i=0;$i<$y;$i++) {
			if($i==0) echo "<tr height=90 bgcolor=#FFFFFF>\n";
			if($i>0 && $i%2==0) echo "</tr><tr height=90 bgcolor=#FFFFFF>\n";
			echo "<td width=90 align=center>";
			if($changetype=="0") {	//마우스 오버
				echo "<a href=\"javascript:primg_preview('{$yesimage[$i]}','{$resizewidth[$i]}','{$resizeheight[$i]}')\" onmouseover=\"primg_preview2('{$yesimage[$i]}','{$resizewidth[$i]}','{$resizeheight[$i]}')\">";
			} else {	//마우스 클릭
				echo "<a href=\"javascript:primg_preview('{$yesimage[$i]}','{$resizewidth[$i]}','{$resizeheight[$i]}')\">";
			}
			echo "<img src={$imagepath}s{$yesimage[$i]} border=0></a></td>";
		}

		if($i%2!=0) {
			echo "<td width=90 align=center bgcolor=#ffffff></td>";
			echo "</tr>\n";
		}
?>
		</table>
		<?php }?>
		</td>
	</tr>
	</table>

	<?php }else{?>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td align=center>
		<img src="<?=$imagepath.$yesimage[0]?>" border=0 name="primg">
		</td>
	</tr>
	<tr><td height=20></td></tr>
	<tr>
		<td align=center>
		<table border=0 cellpadding=0 cellspacing=1 height=90 bgcolor=#dadada>
<?php 
		for($i=0;$i<$y;$i++) {
			if($i==0) echo "<tr height=90 bgcolor=#FFFFFF>\n";
			if($i>0 && $i%5==0) echo "</tr><tr height=90 bgcolor=#FFFFFF>\n";
			echo "<td width=90 align=center>";
			if($changetype=="0") {	//마우스 오버
				echo "<a href=\"javascript:primg_preview('{$yesimage[$i]}','{$resizewidth[$i]}','{$resizeheight[$i]}')\" onmouseover=\"primg_preview2('{$yesimage[$i]}','{$resizewidth[$i]}','{$resizeheight[$i]}')\">";
			} else {	//마우스 클릭
				echo "<a href=\"javascript:primg_preview('{$yesimage[$i]}','{$resizewidth[$i]}','{$resizeheight[$i]}')\">";
			}
			echo "<img src={$imagepath}s{$yesimage[$i]} border=0></a></td>";
		}
		if($i%5!=0) {
			if($i>5) {
				for($j=($i%5);$j<5;$j++) {
					echo "<td width=90 align=center bgcolor=#ffffff></td>";
				}
			}
			echo "</tr>\n";
		}
?>
		</table>
		</td>
	</tr>
	</table>
	<?php }?>

	</td>
</tr>
<tr><td height=20></td></tr>
<tr>
	<td height=30 align=center>
	<table border=0 cellpadding=0 cellspacing=0 width=100% height=30>
	<tr bgcolor=#C3C3C3>
		<td align=right valign=middle style="padding-right:10">
		<a href="javascript:WindowClose()"><img src="<?=$Dir?>images/common/imageview_close.gif" border=0 align=absmiddle></a>
		</td>
	</tr>
	</table>
	<td>
</tr>
</table>
</body>
</html>
