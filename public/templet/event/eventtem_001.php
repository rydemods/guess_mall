<?
if(strlen($Dir)==0) $Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if($one==1) {
	$sql = "SELECT * FROM tbleventpopup WHERE num='".$num."' ";
	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
}

$cookiename="eventpopup".$row->num;

if ($layer=="Y" && $row->end_date==$_COOKIE[$cookiename]) return;

$cookieTime = $_layerdata[$i]->cookietime;
$closeMent = "";
if($cookieTime == '1'){
	$closeMent = "하루동안 열지 않기";
}else if($cookieTime == '2'){
	$closeMent = "다시 열지 않기";
}else{
	$closeMent = "브라우저 종료까지 열지 않기";
}


if($layer!="Y") {
?>
<HTML>
<HEAD>
<TITLE><?=$row->title?></TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=euc-kr">
<style>
td {font-family:Tahoma;color:666666;font-size:9pt;}

tr {font-family:Tahoma;color:666666;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
parent.window.moveTo('<?=$row->x_to?>','<?=$row->y_to?>');
//-->
</SCRIPT>
</HEAD>
<BODY STYLE="MARGIN:0; PADDING:0">
<?}?>

<CENTER>

<TABLE border="0" cellspacing="0" cellpadding="0" height="100%">
<form name=event_form1 method=post action="<?=$Dir.FrontDir?>event.php">
<input type=hidden name=type value="close">
<input type=hidden name=num value="<?=$row->num?>">
<tr>
	<td>
		<!-- <div class="event-layer-top"><button class="close" type="button" onClick="javascript:close_popup_layer('<?=$_layerdata[$i]->num?>');"><span>닫기</span></button></div> -->
	</td>
</tr>
<TR>
	<TD>
		<?
			/*
			if($layer=="Y"){
				$check="<input type=checkbox name=no value=\"yes\" onclick=\"p_windowclose('".$row->num."','1');\" style=\"border:none\">";
				$close="\"JavaScript:p_windowclose('".$row->num."','0');\"";
			}else {
				$check="<input type=checkbox name=no value=\"yes\" style=\"border:none\">";
				$close="\"JavaScript:document.event_form1.submit()\"";
			}
			$pattern=array("[CHECK]","[CLOSE]");
			$replace=array($check,$close);
			$content=str_replace($pattern,$replace,$row->content);
			*/
			echo $row->content;
		?>
	</TD>
</TR>
<TR>
	<TD align = 'right' bgcolor="#515152" style="padding:5px; color:#fff">
		<?if($layer=='Y'){?>
			<input type="checkbox" class="checkbox-def" id="not_open_day<?=$_layerdata[$i]->num?>" idx = '<?=$_layerdata[$i]->num?>' time = '<?=$_layerdata[$i]->cookietime?>' style="/*vertical-align:bottom;*/" />
			<?=$closeMent?>
			<IMG src="<?=$Dir?>static/img/btn/close.png" border="0" style="margin-top:2px" align=absmiddle id="close_popup<?=$_layerdata[$i]->num?>" idx = '<?=$_layerdata[$i]->num?>'  time = '<?=$_layerdata[$i]->cookietime?>' class = 'close_main_layer' style="cursor:pointer; display:inline">
		<?}else{?>
			<input type=checkbox id="idx_no" name=no value="yes" style="border:none" <?=($layer=="Y"?" onclick=p_windowclose('".$row->num."','1');":"")?>>
			<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_no>
				<font color=#000000>현재 이 창을 다시 열지 않음</font>
			</label>&nbsp;&nbsp;
			<a href="JavaScript:<?=($layer=="Y"?"p_windowclose('".$row->num."','0')":"document.event_form1.submit();")?>">
				<IMG src="<?=$Dir?>images/common/event_popup_close.gif" border="0" align=absmiddle style="display:inline">
			</a>&nbsp;
		<?}?>
	</TD>
</TR>
</form>
</TABLE>

</CENTER>

<script type="text/javascript">
    //닫기(상단)
    function close_popup_layer(num) {
        $("#draggable"+num).hide(); 
    }
</script>

<?if($layer!="Y"){?>
</BODY>
</HTML>
<?}?>
