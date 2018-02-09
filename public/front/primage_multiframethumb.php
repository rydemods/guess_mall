<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if (strpos($_SERVER['HTTP_REFERER'],FrontDir."primage")==false) exit;
$imagepath=$Dir.DataDir."shopimages/multi/";

$productcode=$_REQUEST["productcode"];
$maxsize=$_REQUEST["maxsize"];
?>

<html>
<head>
<title>상품확대보기</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<meta http-equiv="imagetoolbar" content="no">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td {font-family:돋음;color:666666;font-size:9pt;}

tr {font-family:돋음;color:666666;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
var imagepath="<?=$imagepath?>";
var setcnt=0;
function primg_preview(img,width,height) {
	if(parent.main.primg!=null) {
		setcnt=0;
		parent.main.primg.src=imagepath+img;
		parent.main.primg.width=width;
		parent.main.primg.height=height;
	} else {
		if(setcnt<=10) {
			setcnt++;
			setTimeout("primg_preview('"+img+"','"+width+"','"+height+"')",500);
		}
	}
}

function primg_preview3(img,width,height) {
	if(parent.main.primg!=null) {
		parent.main.primg.src=imagepath+img;
		parent.main.primg.width=width;
		parent.main.primg.height=height;
	}
}

function primg_preview2(img,width,height) {
	obj = event.srcElement;
	clearTimeout(obj._tid);
	obj._tid=setTimeout("primg_preview3('"+img+"','"+width+"','"+height+"')",500);
}
//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onselectstart="return false" ondragstart="return false" oncontextmenu="return false">
<?php 
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
	$multi_imgs=array(&$row->primg01,&$row->primg02,&$row->primg03,&$row->primg04,&$row->primg05,&$row->primg06,&$row->primg07,&$row->primg08,&$row->primg09,&$row->primg10);

	$tmpsize=explode("",$row->size);
	$insize="";
	$updategbn="N";

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

	//$maxnumsize=($maxsize/60);
	//if($y>=5 && $maxnumsize<=5) $y=5;
	//else if($maxnumsize<$y) $y=$maxnumsize;

	$makesize=$maxsize;
	for($i=0;$i<$y;$i++){
		if($xsize[$i]>$makesize || $ysize[$i]>$makesize) {
			if($xsize[$i]>=$ysize[$i]) {
				$tempxsize=$makesize;
				$tempysize=($ysize[$i]*$makesize)/$xsize[$i];
			} else {
				$tempxsize=($xsize[$i]*$makesize)/$ysize[$i];
				$tempysize=$makesize;
			}
			$xsize[$i]=$tempxsize;
			$ysize[$i]=$tempysize;
		}
	}
	if($updategbn=="Y"){
		$sql = "UPDATE tblmultiimages SET size='".ltrim($insize,'')."' ";
		$sql.= "WHERE productcode='{$productcode}'";
		pmysql_query($sql,get_db_conn());
	}

	pmysql_free_result($result);
}
?>

<table border=0 cellpadding=0 cellspacing=0 width="<?=$maxsize?>">
<tr>
	<td align=center>
	<table border=0 cellpadding=0 cellspacing=1 bgcolor=#DADADA>
<?php 
	for($i=0;$i<$y;$i++) {
		if($i==0) echo "<tr height=60 bgcolor=#FFFFFF>\n";
		if($i>0 && $i%5==0) echo "</tr><tr height=60 bgcolor=#FFFFFF>\n";
		echo "<td width=60 align=center>";
		if($changetype=="0") {	//마우스 오버
			echo "<a href=\"javascript:primg_preview('{$yesimage[$i]}','{$xsize[$i]}','{$ysize[$i]}')\" onmouseover=\"primg_preview2('{$yesimage[$i]}','{$xsize[$i]}','{$ysize[$i]}')\">";
		} else {	//마우스 클릭
			echo "<a href=\"javascript:primg_preview('{$yesimage[$i]}','{$xsize[$i]}','{$ysize[$i]}')\">";
		}
		echo "<img src={$imagepath}s{$yesimage[$i]} border=0";
		if($xsize[$i]>$ysize[$i]) echo " width=55";
		else echo " height=55";
		echo "></a></td>";
	}
	if($i%5!=0) {
		if($i>5) {
			for($j=($i%5);$j<5;$j++) {
				echo "<td width=60 align=center bgcolor=#ffffff></td>";
			}
		}
		echo "</tr>\n";
	}
?>
	</table>
	</td>
</tr>
</table>
<script>
primg_preview('<?=$yesimage[0]?>','<?=$xsize[0]?>','<?=$ysize[0]?>');
parent.main.document.bgcolor='<?=$bgcolor?>';
</script>
</body>
</html>
