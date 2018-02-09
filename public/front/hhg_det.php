<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$member['id']=$_ShopInfo->getMemid();
$member['name']=$_ShopInfo->getMemname();
echo $Dir.MainDir.$_data->menu_type.".php";

?>
<!--php끝-->
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td>
<div style='position:absolute;top:200px;left:100px;width:1400px;height:500px;background-color:#fafafa;padding:10px'>
<SCRIPT LANGUAGE="JavaScript">
<!--
var track_num = 0;
var mod_num = 0;

var loading = true;
$(document).ready(function() {

	 load_result("Start");
     
	$("#results").scroll(function() {
		
		if (loading == true)
		{
			var elem = $("#results");
			if ( elem[0].scrollHeight - elem.scrollTop() == elem.outerHeight())
			{
				//alert("more");
				 load_result("more");
			}
		}
	});
});


function load_result(type) {
	if (type == 'Start')
	{
		$("#result_row").html("");
	}
	$("#loading").show();
	$.ajax({
		type: "POST",
		url: '../front/ajax_hhg_det.php',
		data: {
			track_num: track_num,
			type:type,
			mode:"list"
		},
		timeout: 2000,
		success: function(data){
			if(data){
				// 데이터를 JSON으로 파싱

				 var json = JSON.parse(data);
				 var len	= 0;
				 if(json.items.length <= 10) {
					  len	= json.items.length;
					loading = false;
				 } else {
					 len	= json.items.length - 1;
				 }
				for (var i =0; i < len; i++) {

					var num					= json.items[i].num;
					var id						= json.items[i].id;
					var name				= json.items[i].name;
					var ip						= json.items[i].ip;
					var comment			= json.items[i].comment;
					var comment_mod	= comment.replace(/<br>/g, '\n');
					var writetime			= json.items[i].writetime;
					
					var add_html	= "<li id='row"+num+"' style='padding:15px 0px;border-bottom:solid 1;border-color:#BDBDBD;position:relative'>\n";					
					//add_html	+= num+id+name+ip+comment+writetime;
					add_html	+= "<input type=hidden id='uid"+num+"' name='uid"+num+"' value='"+id+"'>\n";
					add_html	+= "<div id='viewRow"+num+"'><table border=0 cellSpacing=0 cellPadding=4 width=100% style='table-layout:fixed'>\n";
					add_html	+= "<tr>\n";
					add_html	+= "	<td style=\"font-size:11px;letter-spacing:-0.5pt;\" bgColor=\"#fafafa\"><b>"+name+"</b> ("+writetime+")</td>\n";
					add_html	+= "</tr>\n";
					add_html	+= "<tr bgColor='#fafafa'>\n";
					add_html	+= "	<td id ='viewRowComment"+num+"'>"+comment+"</td>\n";
					add_html	+= "</tr>\n";
					add_html	+= "<tr bgColor='#fafafa'>\n";
					add_html	+= "	<td><a href=\"javascript:comment_div('mod','"+num+"')\">[수정]</a> <a href=\"javascript:comment_div('del','"+num+"')\">[삭제]</a> </td>\n";
					add_html	+= "</tr>\n";
					add_html	+= "</table></div>\n";

					add_html	+= "<div id='modRow"+num+"' style='display:none;'><table border=0 cellSpacing=0 cellPadding=4 width=100% style='table-layout:fixed'>\n";
					add_html	+= "<tr>\n";
					add_html	+= "	<td style=\"font-size:11px;letter-spacing:-0.5pt;\" bgColor=\"#fafafa\">";
					add_html	+= "<b>"+name+"</b><input type=hidden id='up_name"+num+"' name='up_name"+num+"' value='"+name+"'>";
					if (id == '')
					{
						add_html	+= "&nbsp;&nbsp;비밀번호 <INPUT type=password id='up_passwd"+num+"' name='up_passwd"+num+"' value='' maxLength='20' size='10' class='input'>";
					} else {
						add_html	+= "<input type=hidden id='up_passwd"+num+"' name='up_passwd"+num+"' value=''>\n";
					}
					add_html	+= "	</td>\n";
					add_html	+= "</tr>\n";
					add_html	+= "<tr bgColor='#fafafa'>\n";
					add_html	+= "	<td>";
					
					add_html	+= "	<table border='0' cellSpacing='0' cellPadding='0' width='100%' style='table-layout:fixed'>\n";
					add_html	+= "	<col width=></col>\n";
					add_html	+= "	<col width='100'></col>\n";
					add_html	+= "	<tr>\n";
					add_html	+= "		<td ><textarea id='up_comment"+num+"' name='up_comment"+num+"' style='width:1300px;height:70px;line-height:17px;border:solid 1;border-color:#BDBDBD;font-size:9pt;color:333333;background-color:white;'>"+comment_mod+"</textarea></td>\n";
					add_html	+= "		<td align='center'><a href=\"javascript:chkCommentForm('mod','"+num+"')\" style='width:75px;height:68px;line-height:68px;border:solid 1;border-color:#BDBDBD;font-size:9pt;color:333333;display:block'>수정</A></td>\n";
					add_html	+= "	</tr>\n";
					add_html	+= "	</table>\n";

					add_html	+= "</td>\n";
					add_html	+= "</tr>\n";
					add_html	+= "<tr bgColor='#fafafa'>\n";
					add_html	+= "	<td><a href=\"javascript:comment_cancel('"+num+"')\">[취소]</a></td>\n";
					add_html	+= "</tr>\n";
					add_html	+= "</table></div>\n";

					add_html	+= "<div id='delRow"+num+"' style='display:none;position:absolute;bottom:13px;left:0px;background:#ffffff;'><table border=0 cellSpacing=0 cellPadding=0 width=100% style='table-layout:fixed;'>\n";
					add_html	+= "<tr>\n";
					add_html	+= "	<td bgColor=\"#fafafa\">";
					if (id == '')
					{
						add_html	+= "비밀번호 <input type=password id='del_passwd"+num+"' name='del_passwd"+num+"' value='' maxLength='20' size='10' class='input'>";
					} else {
						add_html	+= "<input type=hidden id='del_passwd"+num+"' name='del_passwd"+num+"' value=''>\n";
					}
					add_html	+= "&nbsp;&nbsp;<a href=\"javascript:chkCommentForm('del','"+num+"')\">[확인]</a> <a href=\"javascript:comment_cancel('"+num+"')\">[취소]</a></td>\n";
					add_html	+= "</tr>\n";
					add_html	+= "</table></div>\n";
					add_html	+= "</li>";

					$("#result_row").append(add_html);

					track_num = num;
				}
				
				$("#loading").hide();
			}
		}
	});
}

function comment_div(mode,num) {
	var uid			= $("#uid"+num).val();
	var mem_id	= $("#uid").val();
	if (uid == mem_id)
	{
		if (mod_num != 0)
		{		
			comment_cancel(mod_num);
		}
		if (mode == 'mod')
		{
			$("#viewRow"+num).hide();
			$("#delRow"+num).hide();
			$("#modRow"+num).show();
		} else if (mode == 'del')
		{	
			if (uid != '')
			{
				chkCommentForm(mode, num);
			} else {
				$("#modRow"+num).hide();
				$("#viewRow"+num).show();
				$("#delRow"+num).show();
			}
		}
		mod_num	= num;
	} else {
		if (mem_id == '')
		{
			alert("로그인후 가능합니다.");
		} else {
			alert("회원님이 등록한 댓글만 수정 및 삭제가 가능합니다.");		
		}
	}
}

function comment_cancel(num) {	
	$("#modRow"+num).hide();
	$("#delRow"+num).hide();
	$("#viewRow"+num).show();
}

function chkCommentForm(mode, num) {

	var uid	= $("#uid"+num).val();
	var up_name	= $("#up_name"+num).val();
	var up_passwd	= $("#up_passwd"+num).val();
	var up_comment	= $("#up_comment"+num).val();
	if (mode == 'del')
	{
		var up_passwd	= $("#del_passwd"+num).val();
		if (uid =='') {
			if ($("#del_passwd"+num).val() == '') {
				alert('비밀번호를 입력 하세요.');
				$("#del_passwd"+num).focus();
				return;
			}
		}
	} else {
		if ($("#up_name"+num).val() == '') {
			alert('이름을 입력 하세요.');
			$("#up_name"+num).focus();
			return;
		}
		if (uid =='') {
			if ($("#up_passwd"+num).val() == '') {
				alert('비밀번호를 입력 하세요.');
				$("#up_passwd"+num).focus();
				return;
			}
		}
		if ($("#up_comment"+num).val() == '') {
			alert('내용을 입력 하세요.');
			$("#up_comment"+num).focus();
			return;
		}
	}
	if (mode == 'del')
	{
		if (confirm("정말 삭제하시겠습니까??") == true){    //확인
			ajax_comment(mode, num, uid, up_name, up_passwd, up_comment);
		}else{   //취소
			return;
		}
	} else {
		ajax_comment(mode, num, uid, up_name, up_passwd, up_comment);
	}
}

function ajax_comment(mode, num, uid, up_name, up_passwd, up_comment) {
	$.post(
	'../front/ajax_hhg_det.php',
	{
		num:num,				
		uid: uid,
		up_name: up_name,
		up_passwd: up_passwd,
		up_comment: up_comment,
		mode:mode
	},
	function(data){
		if(data){
			if (data == "noPass")
			{
				if (mode == 'del')
				{
					$("#del_passwd"+num).val("");
				} else {
					$("#up_passwd"+num).val("");
				}
				alert("비밀번호가 일치하지 않습니다.");
			} else {
				if (mode == 'del')
				{
					$("#row"+num).remove();
					alert("삭제되었습니다.");
				} else {
						
					
					if (mode == 'ins')
					{
						if (uid =='') {
							$("#up_name"+num).val("");
							$("#up_passwd"+num).val("");
						}
						$("#up_comment"+num).val("");
						alert("등록되었습니다.");
						track_num = 0;
						loading = true;
						load_result("Start");
					
					} else if (mode == 'mod')
					{
						if (uid =='') {
							$("#up_passwd"+num).val("");
						}
						var comment_mod	= up_comment.replace(/\n/g, '<br>');
						$("#viewRowComment"+num).html(comment_mod);
						comment_cancel(num);
						alert("수정되었습니다.");
						
					}
				}
			}
		}else{
			alert("등록실패! 관리자에게 문의해주세요");
		}
	});
}
//-->
</SCRIPT>


<!-- 간단한 답변글 쓰기 -->
<table border=0 cellspacing=0 cellpadding=0 style="table-layout:fixed">
<tr>
	<td>
<form method=post name=comment_form action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return chkCommentForm('ins','');">
<input type=hidden id="uid" name="uid" value="<?=$member['id']?>">
	<table border="0" cellSpacing="0" cellPadding="4" width="100%" style="table-layout:fixed">
	<tr>
		<td style="font-size:11px;letter-spacing:-0.5pt;" bgColor="#fafafa"> 
		<? if ($member['id']) { ?>
		<B><?= $member[name] ?><input type=hidden id="up_name" name="up_name" value="<?=$member[name]?>"><input type=hidden id="up_passwd" name="up_passwd" value=""></b>
		<? } else { ?>
		이름 <input type=text id="up_name" name="up_name" size="13" maxlength="10" value="" class="input">
		&nbsp;&nbsp;비밀번호 <input type=password id="up_passwd" name="up_passwd" value="" maxLength="20" size="10" class="input">
		<? } ?></td>
	</tr>
	<tr bgColor="#fafafa" align="center">
		<td>
		<table border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
		<col width=></col>
		<col width="100"></col>
		<tr>
			<td ><textarea id="up_comment" name="up_comment" style="width:1300px;height:70px;line-height:17px;border:solid 1;border-color:#BDBDBD;font-size:9pt;color:333333;background-color:white;"></textarea></td>
			<td align="center"><a href="javascript:chkCommentForm('ins','')" style='width:75px;height:68px;line-height:68px;border:solid 1;border-color:#BDBDBD;font-size:9pt;color:333333;display:block'>등록</a></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
</form>
	</td>
</tr>
<tr><td height="1" bgcolor="#ededed"></td></tr>
</table>
<div id="results" style="width:100%;height:390px;overflow: auto;">
<div id="result_row">
</div>
<div id='loading' style='display:none;'>LOADING...</div>
</div>
</div>
<!-- 메인 컨텐츠 -->
<div class="ta_c" style="height:600px;background-image:url(../img/common/intro.jpg)" alt="" />
</div>

</td>
</tr>
</table>
<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
