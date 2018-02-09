<?if($_data->icon_type == 'tem_001'){?>
		
		<div class="boardview_warp">
		<?
		if($setup['btype']=="L") {
			if(strlen($pridx)>0 && $pridx>0) {
				$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
				if($prqnaboard!=$board) $pridx="";
			}
			if(strlen($pridx)>0) {
				$sql = "SELECT a.productcode,a.productname,a.etctype,a.sellprice,a.quantity,a.tinyimage ";
				$sql.= "FROM tblproduct AS a ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
				$sql.= "WHERE pridx='".$pridx."' ";
				$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
				$result=pmysql_query($sql,get_db_conn());
				if($_pdata=pmysql_fetch_object($result)) {
					include("prqna_top_tem001.php");
				} else {
					$pridx="";
				}
				pmysql_free_result($result);
			}
		}
		?>
			<div class="boardview_title"><?=$strSubject?></div>
			<div class="boardview_info">
				<ul>
					<li><?=$strName?></li>
					<li>/&nbsp;&nbsp;Posted at <?=str_replace("/", "-", $strDate)?> </li>
				</ul>
			</div>
			<?/*?>
			<? if (count($file_name1)) { ?>
			<div>
				<ul><font color="#FF6600">梅何颇老 : <?=implode(",",$file_name1)?></font></ul>
			</div><br>
			<? } ?>
			<?*/?>
			<div class="boardview_contents">
				<?if ($upload_file1) {?>
				<span style="width:100%;line-height:160%;text-align:<?=$setup[img_align]?>"> 
				<?=$upload_file1?>
				</span><br>
				<?}?>
				<?=$memo?>
			</div>
		</div><!-- boardview_warp 场 -->
<?}else{?>

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
				<TD align="right" class="list_text" height="30" style="padding-right:20px;"><font color="#FF6600">梅何颇老 : <?=$file_name1?><?=($strIp?" ,".$strIp:"")?></font></TD>
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
<?}?>