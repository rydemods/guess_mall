
<SCRIPT FOR=window EVENT=onload LANGUAGE="JScript">
  //onloadImgResize('<?=$setup[board_width]?>');
</SCRIPT>

<STYLE type=text/css>
	#menuBar {
	}
	#contentDiv {
		WIDTH: <?=$setup[board_width]?>; 
	}
</STYLE>
<table cellpadding="0" cellspacing="0" width="<?=$setup[board_width]?>">
<tr>
	<td bgcolor="#FFFFFF" style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td>
		<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0" style="table-layout:fixed">
		<TR>
			<TD>
			<div class="subject"><?=$strSubject?></div>
			</TD>
		</TR>
		<TR>
			<TD height="30" align="right" style="padding-right:5px;" class="list_text"><B><?=$strName?></B><?=($strName && ($strDate || !$hide_hit_start)?", ":"")?><?=$strDate?><?=($strDate && !$hide_hit_start?", ":"")?><?=($hide_hit_start?"":"HIT : ".$v_access)?></TD>
		</TR>
		<TR>
			<TD height="1" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD>
			<DIV class=MsgrScroller id="contentDiv" style="OVERFLOW-x: auto; OVERFLOW-y: hidden">
			<DIV id=bodyList>
			<TABLE border="0" cellspacing="0" cellpadding="10" style="table-layout:fixed">
			<TR>
				<TD style="word-break:break-all;" bgcolor="<?=$view_body_color?>" valign="top">
				<?if ($upload_file1) {?>
				<span style="width:100%;line-height:160%;text-align:<?=$setup[img_align]?>"> 
				<?=$upload_file1?>
				</span>
				<?}?>
				</td>
			</tr>
			<TR>
				<TD style="word-break:break-all;" bgcolor="<?=$view_body_color?>" valign="top">
				<span style="width:100%;line-height:160%;"> 
				<?=$memo?>
				</span>
				</TD>
			</TR>
			</TABLE>
			</DIV>
			</DIV>
			<TABLE border="0" cellspacing="0" cellpadding="10" width="<?=$setup[board_width]?>">
			<? if ($file_name1) { ?>
			<TR>
				<TD align="right" class="list_text" height="30" style="padding-right:20px;"><font color="#FF6600">Ã·ºÎÆÄÀÏ : <?=$file_name1?><?=($strIp?" ,".$strIp:"")?></font></TD>
			</TR>
			<? } else if($strIp) { ?>
			<TR>
				<TD align="right" class="list_text" height="30" style="padding-right:20px;"><font color="#FF6600"><?=$strIp?></font></TD>
			</TR>
			<? }?>
			</TABLE>
			</TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td>