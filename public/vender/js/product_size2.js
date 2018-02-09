$(document).ready(function() {

	$("input[type=radio][name=use_prsize]").change(function(){
		var chk = $(this).val();
		if(chk=='N'){
			alert('사이즈표 사용안함시, 변경된 사이즈표는 저장되지 않습니다');
			$("#tbody_product_size").css("display","none");
		}
		if(chk=='Y'){
			$("#tbody_product_size").fadeIn();
		}
	});
	
	// 행추가
	$('#add-row').click(function() {
		
		var s = "<tr>";
		s += "<td><input type=text name='sizex_subj[]' size=10 class=ed></td>"; // 행 제목 추가
		var len = $('#stock_thead td').length; // 열의 갯수에 따라 추가된 행의 열 추가
		var x_num = $("#stock_tbody tr").length;
		for (i=0; i<len-1; i++) {
			s += "<td><input type=text name='size_content["+x_num+"][]' size=10 class=ed></td>";
		}
		s += "</tr>";
		$('#stock_tbody').append(s);
	});

	// 열추가
	$('#add-col').click(function() {
	  $('#stock_thead tr').append("<td><input type=text name='sizey_subj[]' size=10 class=ed></td>");
		$('#stock_tbody tr').each(function(index) {
			$(this).append("<td><input type=text name='size_content["+index+"][]' size=10 class=ed></td>");
		});
	});

	// 행삭제
	$('#del-row').click(function() {
		$('#stock_tbody tr:last').remove();
	});

	// 열삭제
	$('#del-col').click(function() {
		$('#stock_thead td:last').remove();
		$('#stock_tbody tr').each(function(index) {
			$('td:last', this).remove();
		});
	});

});

function chk_product_size()
{
		
	$('input[name^="sizey_subj"]').each(function(){
		if($(this).val()==""){
			alert('값을 입력하셔야 합니다');
			$(this).focus();
			return;
		}
	});

	$('input[name^="sizex_subj"]').each(function(){
		if($(this).val()==""){
			alert('값을 입력하셔야 합니다');
			$(this).focus();
			return;
		}
	});
}