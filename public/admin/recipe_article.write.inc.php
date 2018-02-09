<?php // hspark
$mode=$_REQUEST["mode"];
$exec=$_REQUEST["exec"];
$up_board=$_POST["up_board"];

	if($_REQUEST[no]){
		$recipe = new RECIPE();
		$data = $recipe->getRecipeDetail($_REQUEST[no]);
		$cate_array = $recipe->getRecipeCategoryListOnRecipe($_REQUEST[no]);
	}
	
	if($setup['use_lock']=="N") {
		$hide_secret_start="<!--";
		$hide_secret_end="-->";
	}

//	if(($_POST['mode']=="up_result") && ($_POST['ins4e'][mode]=="up_result") && ($_POST['up_subject']!="") && ($_POST['ins4e'][up_subject]!="")) {

	if ($mode == "reWrite") {
		$thisBoard=$_REQUEST["thisBoard"];
		$thisBoard['content']  = stripslashes(urldecode($thisBoard['content']));
		$thisBoard['title']  = stripslashes(urldecode($thisBoard['title']));
		$thisBoard['name']  = stripslashes(urldecode($thisBoard['name']));
	} else if (!$_REQUEST["mode"]) {
		$thisBoard['name'] = $member['name'];
		$thisBoard['email'] = $member['email'];
	}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--

function exec_add(frm)
{
	var ret;
	var str = new Array();
	var code_a=frm.code_a.value;
	var code_b=frm.code_b.value;
	var code_c=frm.code_c.value;
	var code_d=frm.code_d.value;
	
	if(!code_a) code_a="000";
	if(!code_b) code_b="000";
	if(!code_c) code_c="000";
	if(!code_d) code_d="000";
	sumcode=code_a+code_b+code_c+code_d;

	$.ajax({ 
		type: "POST", 
		url: "product_register.ajax.php",
		data: "code_a="+code_a+"&code_b="+code_b+"&code_c="+code_c+"&code_d="+code_d
	}).done(function(msg) {
	
	/*
	if(msg=='nocate'){
		alert("상품카테고리 선택이 잘못되었습니다.");
//		$("#catenm").html(msg);
		
	}else if(msg=='nolowcate'){
		alert("하위카테고리가 존재합니다.");
	//	$("#catenm").html("상품카테고리 선택이 잘못되었습니다.");
	}else{
		*/
	frm.code.value=sumcode;
	var code_a=document.getElementById("code_a");
	var code_b=document.getElementById("code_b");
	var code_c=document.getElementById("code_c");
	var code_d=document.getElementById("code_d");
	
	if(code_a.value){
		str[0]=code_a.options[code_a.selectedIndex].text;
	}
	if(code_b.value){
		str[1]=code_b.options[code_b.selectedIndex].text;
	}
	if(code_c.value){
		str[2]=code_c.options[code_c.selectedIndex].text;
	}
	if(code_d.value){
		str[3]=code_d.options[code_d.selectedIndex].text;
	}
	var obj = document.getElementById('Category_table');
	oTr = obj.insertRow();
	
	oTd = oTr.insertCell(0);
	oTd.id = "cate_name";
	oTd.innerHTML = str.join(" > ");
	oTd = oTr.insertCell(1);
	oTd.innerHTML = "\
	<input type=text name=category[] value='" + sumcode + "' style='display:none'>\
	";
	oTd = oTr.insertCell(2);
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='img/btn/btn_cate_del01.gif' align=absmiddle></a>";
	
	
//	}
	
	});
}

function cate_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('Category_table');
	obj.deleteRow(idx);
}

function chk_writeForm(form) {

	var sHTML = oEditors.getById["ir1"].getIR();
	form.contents.value=sHTML;

	
	if (typeof(form.tmp_is_secret) == "object") {
		form.up_is_secret.value = form.tmp_is_secret.options[form.tmp_is_secret.selectedIndex].value;
	}

	if (!form.up_name.value) {
		alert('이름을 입력하십시오.');
		form.up_name.focus();
		return false;
	}

	if (!form.up_subject.value) {
		alert('제목을 입력하십시오.');
		form.up_subject.focus();
		return false;
	}

	if (!form.contents.value) {
		alert('내용을 입력하십시오.');
		form.contents.focus();
		return false;
	}

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

<SCRIPT LANGUAGE="JavaScript" src="<?=$Dir.BoardDir?>chk_form.js.php"></SCRIPT>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<!--
<form name=fileform method=post>
<input type=hidden name=board value="<?=$up_board?>">
<input type=hidden name=max_filesize value="<?=$setup['max_filesize']?>">
<input type=hidden name=img_maxwidth value="<?=$setup['img_maxwidth']?>">
<input type=hidden name=use_imgresize value="<?=$setup['use_imgresize']?>">
<input type=hidden name=btype value="<?=$setup['btype']?>">
</form>
-->
<form name=writeForm method='post' action='recipe_indb.php' enctype='multipart/form-data'>
<input type=hidden name=module value='recipe_contents'>
<input type=hidden name=mode value='<?=$data[no]?"modify":"write"?>'>
<input type=hidden name=up_is_secret value="<?=$thisBoard['is_secret']?>">
<input type=hidden name=returnUrl value='<?=$_SERVER[REQUEST_URI]?>'>
<input type="hidden" name="code" value="">
<input type="hidden" name="no" value="<?=$data[no]?>">

<div class="table_style01">
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<TR>
	<th><span>카테고리</span></th>
	<TD class="td_con1" align="center" width="627">
							
<table width=100% cellpadding=0 cellspacing=1 id=Category_table>
<col><col width=50 style="padding-right:10"><col width=52 align=right>
				<col><col width=50 style="padding-right:10"><col width=52 align=right>
				<?
					if(is_array($cate_array)){
					foreach($cate_array as $v=>$k){
				?>
				<tr>
					<td id=cate_name><?=$k[c_codename]?></td>
					<td>
					<input type=text name=category[] value="<?=$k[category]?>" style="display:none">
					</td>
					<td>
					<!--<img src="../img/i_select.gif" border=0 onClick="cate_mod(document.forms[0]['cate[]'][0],this.parentNode.parentNode)" class=hand>-->
					<a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="img/btn/btn_cate_del01.gif" border=0 align=absmiddle></a>
					</td>
				</tr>
				<?	}}?>
</table>


	<p align="left">
		<?php


						$sql = "SELECT * FROM tblrecipecode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
						$i=0;
						$ii=0;
						$iii=0;
						$iiii=0;
						$strcodelist = "";
						$strcodelist.= "<script>\n";
						$result = pmysql_query($sql,get_db_conn());
						$selcode_name="";

						while($row=pmysql_fetch_object($result)) {
							$strcodelist.= "var clist=new CodeList();\n";
							$strcodelist.= "clist.code_a='{$row->code_a}';\n";
							$strcodelist.= "clist.code_b='{$row->code_b}';\n";
							$strcodelist.= "clist.code_c='{$row->code_c}';\n";
							$strcodelist.= "clist.code_d='{$row->code_d}';\n";
							$strcodelist.= "clist.type='{$row->type}';\n";
							$strcodelist.= "clist.code_name='{$row->code_name}';\n";
							if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
								$strcodelist.= "lista[{$i}]=clist;\n";
								$i++;
							}
							if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
								if ($row->code_c=="000" && $row->code_d=="000") {
									$strcodelist.= "listb[{$ii}]=clist;\n";
									$ii++;
								} else if ($row->code_d=="000") {
									$strcodelist.= "listc[{$iii}]=clist;\n";
									$iii++;
								} else if ($row->code_d!="000") {
									$strcodelist.= "listd[{$iiii}]=clist;\n";
									$iiii++;
								}
							}
							$strcodelist.= "clist=null;\n\n";
						}
						pmysql_free_result($result);
						$strcodelist.= "CodeInit();\n";
						
						$strcodelist.= "</script>\n";

						echo $strcodelist;
					/*	
						if($code && !$prcode){
							$disabled="disabled";
						}
					*/		
						

						echo "<select name=code_a id=code_a style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,1)\" {$disabled} Multiple>\n";
						echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
						echo "</select>\n";

						echo "<select name=code_b id=code_b style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,2)\" {$disabled} Multiple>\n";
						echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
						echo "</select>\n";

						echo "<select name=code_c id=code_c style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,3)\" {$disabled} Multiple>\n";
						echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
						echo "</select>\n";

						echo "<select name=code_d id=code_d style=\"width:150px; height:150px\" {$disabled} Multiple>\n";
						echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
						echo "</select>\n";

						echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
						
						
						echo "<span style=\"display:\" name=\"changebutton\"><input type=\"button\" value=\"선택\" onclick=\"javascript:exec_add(this.form)\"></span>";
//						echo "<span style=\"display:\" name=\"cateonbutton\"><input type=\"button\" value=\"수정\" onclick=\"javascript:disabledon()\"></span>";
		?>		
	<p>
	</TD>
</TR>
<TR>
	<th><span>글제목</span></th>
	<TD class="td_con1" align="center"><p align="left"><INPUT maxLength=200 size=70 name=up_subject value="<?=$data['subject']?>" style="width:100%" class="input"></TD>
</TR>
<TR>
	<th><span>글쓴이</span></th>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=20 size=13 name=up_name value="<?=$data['name']?$data['name']:$_ShopInfo->id;?>" style="width:100%" class="input"></TD>
</TR>
<TR>
	<th><span>범위지정</span></th>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><a href="javascript:inputs('1')">시작</a> &nbsp; <a href="javascript:inpute('1')">끝</a></TD>
</TR>
<TR>
	<th><span>글내용</span></th>
	<TD class="td_con1" width="627">
	<!--TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" id="ir1" name=up_memo wrap=<?=$setup['wrap']?> onfocus="alert()"><?=$thisBoard['content']?></TEXTAREA-->
	<TEXTAREA  id="ir1" style="WIDTH: 100%; HEIGHT: 280px" name=contents wrap=off required label="내용"><?=$data[contents]?></TEXTAREA>
	<!--
	<textarea name='contents' id='contents' style="width:100%;height:350px" type=editor required label="내용"><?=$data[contents]?></textarea>
	<script src=../lib/meditor/mini_editor.js></script>
	<script>mini_editor("../lib/meditor/")</script>
	-->
	</TD>
</TR>
<TR>
	<th><span>첨부파일</span></th>
	<TD class="td_con1" width="627">
		<input type="hidden" name="rfile" value="<?=$data[rfile_tag]?>">
		<input type="hidden" name="vfile" value="<?=$data[vfile_tag]?>">
		<table width=100% id=table cellpadding=0 cellspacing=0 border=0>
			<col class=engb align=center>

				<?					
					if (is_array($data['vfile'])){
						for ($tmp='',$i=0; $i < count($data['vfile']); $i++){
							$tmp .= "
							<tr id=".($i+1).">
								<td valign=\"top\" style=\"padding-top:3\">".($i+1)."</td>
								<td class=\"eng\">
								<input type=\"file\" name=\"file[]\" style=\"width:90%\" class=\"line\" onChange=\"preview(this.value,".($i+1).");\" /><br>
								<input type=\"checkbox\" name=\"del_file[$i]\" /> Delete Uploaded File .. {$data['vfile'][$i]}
								</td>
								<td id=\"prvImg".($i+1)."\"><a href=\"javascript:input(".($i+1).")\"><img src=\"".$config[file][src]."/".$data[vfile][$i]."\" width=\"50\" onload=\"if(this.height>this.width){this.height=50}\" onError=\"this.style.display='none'\" /></a></td>
							</tr>
							";
						}
						$dataModify['prvFile'] = $tmp;
					}
				?>
				<?= $dataModify['prvFile'] ?>

			<tr>
				<td width=20 nowrap>1</td>
				<td width=100%>
				<input type=file name="file[]" style="width:80%" class=linebg onChange="preview(this.value,0)">
				<a href="javascript:add()"><img src="http://www.soapschool.co.kr/shop/data/skin/season2/board/gallery/img/btn_upload_plus.gif" align=absmiddle></a>
				</td>
				<td id=prvImg0></td>
			</tr>
		</table>
	</TD>
</TR>
<TR>
	<th><span>쇼핑</span></th>
	<TD>
	<iframe name="recipeproductfrm" id="recipeproductfrm" src="./recipe_product_list.php?recipe_no=<?=$_REQUEST[no]?>" style="width:100%; height:400px;" frameborder="0"></iframe>
	</TD>
</TR>

</TABLE>
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

</form>

<div align=center>
	<img src="<?=$imgdir?>/butt-ok.gif" border=0 style="cursor:hand;" onclick="chk_writeForm(document.writeForm);"> &nbsp;&nbsp;
	<IMG SRC="<?=$imgdir?>/butt-cancel.gif" border=0 style="CURSOR:hand" onClick="history.go(-1);">
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

	var oEditors = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2Skin.html",	
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		}, 
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});

	function pasteHTML(sHTML) { 
		oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]); 
	}
</script>
<script>

function del(index)
{
	var table = document.getElementById('table');
    for (i=0;i<table.rows.length;i++) if (index==table.rows[i].id) table.deleteRow(i);
	calcul();
}

function calcul()
{
	var table = document.getElementById('table');
	for (i=0;i<table.rows.length;i++){
		table.rows[i].cells[0].innerHTML = i+1;
	}
}


function preview(El,no)
{
	var tmp = eval("document.getElementById('prvImg" + no +"')");
	tmp.innerHTML = "<a href='javascript:input(" + no + ")'><img src='" + El + "' width=50 onLoad=\"if(this.height>this.width){this.height=50}\" onError=\"this.style.display='none'\"></a>";
}


function input(index)
{
	var table = document.getElementById('table');
	for (i=0;i<table.rows.length;i++) if (index==table.rows[i].id){x = table.rows[i].cells[0].innerHTML;break}
	pasteHTML("\n[:이미지" + x + ":]");
	//document.frmWrite.contents.value += "\n[:이미지" + x + ":]";
//	mini_set_html(0,"\n[:이미지" + x + ":]");
}
function inputs(index)
{
//	mini_set_html(0,"\n[:시작:]");
	pasteHTML("\n[:시작:]");
}
function inpute(index)
{
//	mini_set_html(0,"\n[:끝:]");
	pasteHTML("\n[:끝:]");
}

function add(){
	var table = document.getElementById('table');
	if (table.rows.length>39){
		alert("다중 업로드는 최대 40개만 지원합니다");
		return;
	}
	date	= new Date();
	oTr		= table.insertRow( table.rows.length );
	oTr.id	= date.getTime();
	oTr.insertCell(0);
	oTd		= oTr.insertCell(1);
	tmpHTML = "<input type=file name='file[]' style='width:80%' class=line onChange='preview(this.value," + oTr.id +")'> <a href='javascript:del(" + oTr.id + ")'><img src='http://www.soapschool.co.kr/shop/data/skin/season2/board/gallery/img/btn_upload_minus.gif' align=absmiddle></a>";
	oTd.innerHTML = tmpHTML;
	oTd = oTr.insertCell(2);
	oTd.id = "prvImg" + oTr.id;
	calcul();
}
</script>