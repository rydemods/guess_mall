
<SCRIPT LANGUAGE="JavaScript">
<!--
function chk_writeForm(form) {
	if (typeof(form.tmp_is_secret) == "object") {
		form.up_is_secret.value = form.tmp_is_secret.options[form.tmp_is_secret.selectedIndex].value;
	}

	if (!form.up_name.value) {
		alert('�г����� �Է��Ͻʽÿ�.');
		form.up_name.focus();
		return false;
	}

	if (!form.up_passwd.value) {
		alert('��й�ȣ�� �Է��Ͻʽÿ�.');
		form.up_passwd.focus();
		return false;
	}

	if (!form.up_subject.value) {
		alert('������ �Է��Ͻʽÿ�.');
		form.up_subject.focus();
		return false;
	}
	<?if($_data->icon_type == 'tem_001'){?>
	var sHTML = oEditors.getById["ir1"].getIR();
	form.up_memo.value=sHTML;
	<?}?>
	if (!form.up_memo.value) {
		alert('������ �Է��Ͻʽÿ�.');
		form.up_memo.focus();
		return false;
	}

	form.mode.value = "up_result";
	reWriteName(form);
	form.submit();
}

function putSubject(subject) {
	document.writeForm.up_subject.value = subject;
}

function FileUp() {
	fileupwin = window.open("","fileupwin","width=50,height=50,toolbars=no,menubar=no,scrollbars=no,status=no");
	while (!fileupwin);
	document.fileform.action = "<?=$Dir.BoardDir?>ProcessBoardFileUpload.php"
	document.fileform.target = "fileupwin";
	document.fileform.submit();
	fileupwin.focus();
}
// -->
</SCRIPT>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<SCRIPT LANGUAGE="JavaScript" src="chk_form.js.php"></SCRIPT>
<form name=fileform method=post>
<input type=hidden name=board value="<?=$board?>">
<input type=hidden name=max_filesize value="<?=$setup[max_filesize]?>">
<input type=hidden name=btype value="<?=$setup[btype]?>">
</form>

<form name=writeForm method='post' action='<?= $_SERVER[PHP_SELF]?>' enctype='multipart/form-data'>
<input type=hidden name=mode value=''>
<input type=hidden name=pagetype value='write'>
<input type=hidden name=exec value='<?=$_REQUEST["exec"]?>'>
<input type=hidden name=num value=<?=$num?>>
<input type=hidden name=board value=<?=$board?>>
<input type=hidden name=s_check value=<?=$s_check?>>
<input type=hidden name=search value=<?=$search?>>
<input type=hidden name=block value=<?=$block?>>
<input type=hidden name=gotopage value=<?=$gotopage?>>
<input type=hidden name=pridx value=<?=$pridx?>>
<input type=hidden name=pos value="<?=$thisBoard[pos]?>">
<?if($mypageid){?><input type="hidden" name="mypageid" value="<?=$mypageid?>"><?}?>
<input type=hidden name=up_is_secret value="<?=$thisBoard[is_secret]?>">


<?if($_data->icon_type == 'tem_001'){?>
	<!-- start contents -->
	<div class="cs_contents">

		<div class="title">
			<h2><img src="<?=$Dir?>image/community/title_<?=$setup[board]?>.gif" alt="Ŀ�´�Ƽ" /></h2>
			<div class="path">
				<ul>
					<li class="home">Ȩ&nbsp;&gt;&nbsp;</li>
					<li>Ŀ�´�Ƽ&nbsp;&gt;&nbsp;</li>
					<li><?=$setup[board_name]?></li>
				</ul>
			</div>
		</div>
		<div class="sub_title"><img src="<?=$Dir?>image/community/community_title_<?=$setup[board]?>.png" alt="��������" /></div>
  <div class="board_writeWrap">
		<?
		if($exec=="write"){
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
		}else{
			if(strlen($row2['pridx'])>0 && $row2['pridx']>0) {
			$sql = "SELECT a.productcode,a.productname,a.etctype,a.sellprice,a.quantity,a.tinyimage ";
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= "WHERE pridx='".$row2['pridx']."' ";
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
		
	<div class="board_write">
		
		<ul>	
			<li>
				<span class="board_w_tit"><label for="reg_nm">*�г���</label></span>
				<input type="text" id="up_name" name="up_name" title="�г���" value="<?=$thisBoard[name]?>" class="input_text021" style="width:100px"  />		
			</li>
			<li>
				<span class="board_w_tit"><label for="reg_nm">*��й�ȣ</label></span>
				<input type="password" id="up_passwd" name="up_passwd" title="��й�ȣ" value="<?=$thisBoard[passwd]?>" class="input_text021" style="width:100px"  />		
			</li>
			<li>
			<span class="board_w_tit"><label for="reg_email">*�̸���</label></span>
			<input type="text" id="up_email" name="up_email" title="�̸���" value="<?=$thisBoard[email]?>" class="input_text021" style="width:200px"  />		
			</li>
			<!--
			<li>
			<span class="board_w_tit"><label for="reg_hompi">*Ȩ������</label></span>
			<input type="text" id="reg_email" name="reg_hompi" title="�̸���" value="" class="input_text021" style="width:200px"  />		
			</li>
			
			<li>
			<span class="board_w_tit"><label for="email_1">*���Ӹ�</label></span>
				<select name="" class="pre_txt">
				<option value="">����1</option>
				<option value="">����2</option>
				<option value="">����3</option>
				</select>
			</li>
-->
			<li>
				<span class="board_w_tit"><label for="bbs_title">*����</label></span>
				<input type="text" id="up_subject" name="up_subject" class="input_text021" style="width:60%" value="<?=$thisBoard[title]?>" title="������ �Է� �ϼ���."/>
			</li>
			<?= $hide_secret_start ?>
	
			<li>
				<span class="board_w_tit">*�������� </span><?=writeSecret($exec,$thisBoard[is_secret],$thisBoard[pos]) ?>
				<span class="secrt">��б�</span>�� NO. ������ �Բ� �����ؿ�~ ^^*
			</li>
			<?= $hide_secret_end ?>
			<?if($setup["first_subject_check"]=="Y" && $setup["first_subject"]!=""){
				$arr_f_subject=explode(",",$setup["first_subject"]);
				
				?>
			<li>
				<span class="board_w_tit"><label for="bbs_title">*�з�</label></span>
				<select name="category" class="input_text021">
					<?foreach($arr_f_subject as $k){
						$selected[$row2['category']]="selected";
						?>
					<option value="<?=$k?>" <?=$selected[$k]?>><?=$k?></option>
					<?}?>
					
				</select>
			</li>
			<?}?>
				
			<li>
				<span class="board_w_tit"><label for="bbs_content">*����</label></span>
				<div style="padding-top: 10px">
				<textarea name="up_memo" id="ir1" style="width:647px; height:350px; margin:0 0 10px 50px;" rows="" cols="" title="������ �Է� �ϼ���."><?=nl2br($thisBoard[content])?></textarea>
				</div>
			</li>
			<script>putSubject("<?=addslashes($thisBoard[title])?>");</script>
	
	
			<li>
				<span class="board_w_tit">
				<label for="filefield1">
				�̹���
				</label>
				</span>
				<input type="file" id="up_filename" name="up_filename[]" title="÷������2" class="input_file" style="width:550px;" maxlength="200" value=""  /> 					
				<? if ($thisBoard[filename]) { ?>
				<br><font color="#008C5C" style="font-size:11px;letter-spacing:-0.5pt;">* <?=$thisBoard[filename]?></font>
				<? } ?>	
			</li>

		</ul>
	</div>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	field = "";
	for(i=0;i<document.writeForm.elements.length;i++) {
		if(document.writeForm.elements[i].name.length>0) {
			field += "<input type=hidden name=ins4eField["+document.writeForm.elements[i].name+"]>\n";
		}
	}
	document.write(field);
	//-->
	</SCRIPT>

	<div class="btn_board_write">
	<ul>
		<li><a href="javascript:history.go(-1);"><img src="<?=$Dir?>image/board/bt_mini_back.gif" alt="��������"></a></li>
		<li><a href="<?=$_SERVER[REQUEST_URI]?>"><img src="<?=$Dir?>image/board/bt_mini_reset.gif" alt="�ʱ�ȭ"></a></li>
		<li><a href="javascript:chk_writeForm(document.writeForm);"><img src="<?=$Dir?>image/board/bt_mini_ok.gif" alt="Ȯ��"></a></li>
	</ul>
	</div>

  </div><!-- board_write �� -->
</div><!-- board_writeWrap �� -->
<script type="text/javascript">
var oEditors = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "ir1",
	sSkinURI: "../SE2/SmartEditor2Skin.html",	
	htParams : {
		bUseToolbar : true,				// ���� ��� ���� (true:���/ false:������� ����)
		bUseVerticalResizer : true,		// �Է�â ũ�� ������ ��� ���� (true:���/ false:������� ����)
		bUseModeChanger : true,			// ��� ��(Editor | HTML | TEXT) ��� ���� (true:���/ false:������� ����)
		//aAdditionalFontList : aAdditionalFontSet,		// �߰� �۲� ���
		fOnBeforeUnload : function(){
		}
	}, 
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

</script>

<?}else{?>



<TABLE cellSpacing="0" cellPadding="0" width="<?=$setup[board_width]?>" border="0">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellSpacing="0" cellPadding="0" width="100%" bgcolor="<?=$view_left_header_color?>"  class="th_left_st02">

	<col width="15%" style="padding-top:5px;padding-bottom:5px;letter-spacing:-0.5pt;background-color:#F8F8F8;"></col>
	<col width="35%" style="padding-left:3pt;padding-right:3pt;background-color:#FFFFFF;"></col>
	<col width="15%" style="letter-spacing:-0.5pt;background-color:#F8F8F8;"></col>
	<col width="35%" style="padding-left:3pt;padding-right:3pt;background-color:#FFFFFF;"></col>
	<?= $hide_secret_start ?>
	<TR>
		<th align="center"><font color="#333333">��ݱ��</font></th>
		<TD colspan="3" style="border-left:<?=$list_divider?> 1px solid;"><?= writeSecret($exec,$thisBoard[is_secret],$thisBoard[pos]) ?></TD>
	</TR>
	<?= $hide_secret_end ?>
	<TR>
		<th align="center"><font color="#333333">�г���</font></th>
		<TD style="border-left:<?=$list_divider?> 1px solid;"><input type=text name="up_name" value="<?=$thisBoard[name]?>" size="13" maxlength="20" style="border-color:#BDD9E5;BACKGROUND-COLOR:#F5FCFF;width:160px" class="input"></TD>
		<th align="center" style="border-left:<?=$list_divider?> 1px solid;"><font color="#333333">��й�ȣ</font></th>
		<TD style="border-left:<?=$list_divider?> 1px solid;"><input type=password name="up_passwd" value="<?=$thisBoard[passwd]?>" size="13" maxlength="20" style="border-color:#BDD9E5;BACKGROUND-COLOR:#F5FCFF;width:160px" class="input"></TD>
	</TR>
	<TR>
		<th align="center"><font color="#333333">�̸���</font></th>
		<TD colspan="3" style="border-left:<?=$list_divider?> 1px solid;"><input type=text name="up_email" value="<?=$thisBoard[email]?>" size="49" maxlength="60" style="border-color:#BDD9E5;BACKGROUND-COLOR:#F5FCFF;width:240px" class="input"> <font color="#0099CC" style="font-size:11px;letter-spacing:-0.5pt;">* �亯�� ������ E-mail�� �Է��ϼ���.</font></TD>
	</TR>
	<TR>
		<th align="center"><font color="#333333">������</font></th>
		<TD colspan="3" style="border-left:<?=$list_divider?> 1px solid;"><input type=text name="up_subject" value="<?=$thisBoard[title]?>" size="70" maxlength="200" class="input" style="border-color:#BDD9E5;BACKGROUND-COLOR:#F5FCFF;width:100%"></TD>
	</TR>
	<TR>
		<th align="center"><font color="#333333">�۳���</font></th>
		<TD colspan="3" style="border-left:<?=$list_divider?> 1px solid;">
		<table cellpadding="0" cellspacing="0" width="100%" class="style_none">
		<?=$hide_html_start?>
		<tr>
			<td style="padding-top:2px;padding-bottom:2px;"><B>HTML����</B> <input type=checkbox name="up_html" value="1" <?=$thisBoard[use_html]?> style="border:none;"></td>
		</tr>
		<?=$hide_html_end?>
		<tr>
			<td style="padding-top:2px;padding-bottom:2px;"><textarea name="up_memo" style="width:590; height:280px; border:1 solid <?=$list_divider?>;PADDING:5px;line-height:17px;font-size:9pt;color:333333;" wrap="<?=$setup[wrap]?>"><?=$thisBoard[content]?></textarea></td>
		</tr>
		</table>
		</TD>
	</TR>
	<script>putSubject("<?=addslashes($thisBoard[title])?>");</script>
	<TR>
		<th align="center"><font color="#333333">÷������</font></th>
		<TD colspan="3" style="padding-top:3px;border-left:<?=$list_divider?> 1px solid;"><input type=text name="up_filename" size="30" onfocus="this.blur();" style="border-color:#BDD9E5;width:75%;BACKGROUND-COLOR:#F5FCFF;" class="input"> <INPUT type=button value="����÷��" style="BORDER:#0099CC 1px solid;CURSOR:hand;font-size:9pt;color:#FFFFFF;height:19px;background-color:#0099CC" onclick="FileUp();"><br><font color="#0099CC" style="font-size:11px;letter-spacing:-0.5pt;">* �ִ� <b><?=($setup[max_filesize]/1024)?>KB</b>���� ���ε� �����մϴ�.</font>
		<? if ($thisBoard[filename]) { ?>
		<br><font color="#008C5C" style="font-size:11px;letter-spacing:-0.5pt;">* <?=$thisBoard[filename]?></span>
		<? } ?>
		</td>
	</TR>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	field = "";
	for(i=0;i<document.writeForm.elements.length;i++) {
		if(document.writeForm.elements[i].name.length>0) {
			field += "<input type=hidden name=ins4eField["+document.writeForm.elements[i].name+"]>\n";
		}
	}
	document.write(field);
	//-->
	</SCRIPT>
	
	</TABLE>
	<table cellSpacing="0" cellPadding="0"><tr><td height="10"></td></tr></table>
	<div align="center" class="ptb_20">
		<a href="#" onclick="chk_writeForm(document.writeForm);" class="btn_buy">����ϱ�</a> <a href="#" onClick="history.go(-1);" class="btn_gray">����ϱ�</a>
	</div>
	</td>
</tr>
</table>
<?}?>



</form>