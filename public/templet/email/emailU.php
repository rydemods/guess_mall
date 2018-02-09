<?
$sub = explode("",$size);
$xsize = $sub[0];
$ysize = $sub[1];

$xsize = 450;
$ysize = 590;

if($num=strpos($content,"[NAME_")) {
	$s_tmp=explode("_",substr($content,$num+1,strpos($content,"]",$num)-$num-1));
	$name_style=$s_tmp[1];
}
if($num=strpos($content,"[SENDER_")) {
	$s_tmp=explode("_",substr($content,$num+1,strpos($content,"]",$num)-$num-1));
	$sender_style=$s_tmp[1];
}
if($num=strpos($content,"[SUBJECT_")) {
	$s_tmp=explode("_",substr($content,$num+1,strpos($content,"]",$num)-$num-1));
	$subject_style=$s_tmp[1];
}
if($num=strpos($content,"[CONTENT_")) {
	$s_tmp=explode("_",substr($content,$num+1,strpos($content,"]",$num)-$num-1));
	$content_style=$s_tmp[1];
}
if($num=strpos($content,"[FILE_")) {
	$s_tmp=explode("_",substr($content,$num+1,strpos($content,"]",$num)-$num-1));
	$file_style=$s_tmp[1];
}
if(strlen($name_style)==0) $name_style="width:99%";
if(strlen($sender_style)==0) $sender_style="width:99%";
if(strlen($subject_style)==0) $subject_style="width:99%";
if(strlen($content_style)==0) $content_style="width:99%;height:170px";
if(strlen($file_style)==0) $file_style="width:100%";

$name_txt="<input type=text name=sender_name maxlength=30 style=\"".$name_style."\" class=input>";
$sender_txt="<input type=text name=sender_email maxlength=50 style=\"".$sender_style."\" class=input>";
$subject_txt="<input type=text name=subject maxlength=100 style=\"".$subject_style."\" class=input>";
$message_txt="<textarea name=message style=\"".$content_style."\" class=input></textarea>";
$file_txt="<input type=file name=upfile style=\"".$file_style."\" onpropertychange=\"checkImgFormat(this.value);\">";

$ok_txt = "\"javascript:CheckForm()\"";
$close_txt = "\"javascript:window.close();\"";

$pattern=array(
	"(\[SENDER((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[NAME((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[SUBJECT((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[CONTENT((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[FILE((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[EMAIL\])","(\[OK\])","(\[CLOSE\])");
$replace=array($sender_txt,$name_txt,$subject_txt,$message_txt,$file_txt,$info_email,$ok_txt,$close_txt);
$content=preg_replace($pattern,$replace,$content);
echo "
	<script>
		window.moveTo(10,10);
		if (g_fIsSP2) window.resizeTo('$xsize','".($ysize+20)."');
		else window.resizeTo('$xsize','$ysize');        
	</script>
";
echo "<form name=email_form method=post action=\"".$_SERVER[PHP_SELF]."\" enctype=\"multipart/form-data\">\n";
echo "<input type=hidden name=mode>\n";
echo $content;
echo "</form>\n";
echo "<img id=\"addfile\" style=\"display:none;\">";
?>