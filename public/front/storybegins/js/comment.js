var track_num = 0;
var mod_num = 0;
var loading = true;



 
function comment_reset() {
	$.ajax({
		type: "POST",
		url: '../../front/ajax_hhg_det.php',
		data: {
			mode:"loginCheck"
		},
		timeout: 2000,
		success: function(data){
			if(data){
				var login_data	= data.split("|");
				$("#uid").val(login_data[0]);
				$("#up_name").val(login_data[1]);

				$("#up_comment").val("내용을 입력해주세요.");
				track_num = 0;
				mod_num = 0;
				loading = true;
				load_result("Start");
			}
		}
	});
}
function load_more() {
	
	if (loading == true)
	{
		load_result("more");
	}
}



function load_result(type) {
	var mem_id	= $("#uid").val();
	if (type == 'Start')
	{
		$("#comment").find("ul").find("li").detach();    
	}

	$.ajax({
		type: "POST",
		url: '../../front/ajax_hhg_det.php',
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
				 if(json.items.length <= 5) {
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


					
					var add_html	= "<li id='row"+num+"'>\n";
					add_html	+= "<input type=hidden id='uid"+num+"' name='uid"+num+"' value='"+id+"'><input type=hidden id='up_name"+num+"' name='up_name"+num+"' value='"+name+"'><input type=hidden id='up_passwd"+num+"' name='up_passwd"+num+"' value=''>\n";
					add_html	+= "<div id='viewRow"+num+"'><span class='name'><strong>"+name+"</strong> ("+writetime+")</span>\n";
					add_html	+= "	<p id ='viewRowComment"+num+"'>"+comment+"</p>\n";
					add_html	+= "	<div class='button'>\n";
					if (mem_id == id)
					{
						add_html	+= "		<button type='button' onClick=\"javascript:comment_div('mod','"+num+"')\"><span><img src='img/comment/btn_modify.png' alt='수정' /></span></button>\n";
						add_html	+= "		<button type='button' onClick=\"javascript:chkCommentForm('del','"+num+"')\"><span><img src='img/comment/btn_delete.png' alt='삭제' /></span></button>\n";
					}
					add_html	+= "	</div>\n";
					add_html	+= "</div>\n";
					add_html	+= "<div id='modRow"+num+"' style='display:none;'><span class='name'><strong>"+name+"</strong> ("+writetime+")</span>\n";
					add_html	+= "	<textarea title='댓글수정' id='up_comment"+num+"' name='up_comment"+num+"'>"+comment_mod+"</textarea>\n";
					add_html	+= "	<div class='button'>\n";
					add_html	+= "		<button type='button' onClick=\"javascript:chkCommentForm('mod','"+num+"')\"><span><img src='img/comment/btn_modify.png' alt='수정' /></span></button>\n";
					add_html	+= "		<button type='button' onClick=\"javascript:comment_cancel('"+num+"')\"><span><img src='img/comment/btn_cancel.png' alt='취소' /></span></button>\n";
					add_html	+= "	</div>\n";
					add_html	+= "</div>\n";
					add_html	+= "</li>\n";

					$("#comment").find("#mCSB_1_container").append(add_html);

					track_num = num;
				}				
			}
		}
	});
}

function comment_div(mode,num) {
	var uid			= $("#uid"+num).val();
	var mem_id	= $("#uid").val();
	if (mem_id == '')
	{
		alert("로그인후 가능합니다.");
		gotologin();
	} else {
		if (uid == mem_id)
		{
			if (mod_num != 0)
			{		
				comment_cancel(mod_num);
			}
			if (mode == 'mod')
			{
				$("#viewRow"+num).hide();
				$("#modRow"+num).show();
			}
			mod_num	= num;
		} else {
			alert("회원님이 등록한 댓글만 수정 및 삭제가 가능합니다.");		
		}
	}
}

function comment_cancel(num) {	
	$("#modRow"+num).hide();
	$("#viewRow"+num).show();
}

function chkCommentForm(mode, num) {

	var uid	= $("#uid"+num).val();
	var up_name	= $("#up_name"+num).val();
	var up_comment	= $("#up_comment"+num).val();
	if (mode == 'ins' && uid == '')
	{
		alert("로그인후 가능합니다.");
		gotologin();
		return;
	}
	if (mode == 'del')
	{
		if (confirm("정말 삭제하시겠습니까??") == true){    //확인
			ajax_comment(mode, num, uid, up_name, '', up_comment);
		}else{   //취소
			return;
		}
	} else {
		if ($("#up_comment"+num).val() == '' || $("#up_comment"+num).val() == '내용을 입력해주세요.') {
			alert('내용을 입력해주세요.');
			$("#up_comment"+num).focus();
			return;
		}
		ajax_comment(mode, num, uid, up_name, '', up_comment);
	}
}

function ajax_comment(mode, num, uid, up_name, up_passwd, up_comment) {
	$.post(
	'../../front/ajax_hhg_det.php',
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
				alert("정보가 일치하지 않습니다.");
			} else if (data == "noConnect")
			{
				alert("로그인후 가능합니다.");
				gotologin();
			} else {
				if (mode == 'del')
				{
					$("#row"+num).remove();
					alert("삭제되었습니다.");
				} else {					
					
					if (mode == 'ins')
					{
						$("#up_comment"+num).val("");
						alert("등록되었습니다.");
						track_num = 0;
						loading = true;
						load_result("Start");
					
					} else if (mode == 'mod')
					{
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