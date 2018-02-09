/* ===================================================
 * admin_layer_product_sel.js
 * /admin/layer_prlist_sel_demo.php - demo page
 * ===================================================
 * 2016.01.18 김재수
 *
 * 레이어 팝업에서 상품 리스트를 출력하고 선택시 추가되는 스크립트
 * ========================================================== */
function T_layer_open(el,onMode, no){

	var temp = $('#' + el);
	var bg = temp.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수
	$('#prlistMode').val(onMode);	
	
	if(bg){
		temp.parent().fadeIn();	//'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
	}else{
		temp.fadeIn();
	}

	T_layerResize(el);
	$('#box_no').val(no);
	$(document).on('click', '#' + el +' a.cbtn',function(e){
		if(bg){
			temp.parent().fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
			T_outLayer();
		}else{
			temp.fadeOut();
			T_outLayer();
		}
		e.preventDefault();
	});


}

function T_layerResize(el){
	var temp = $('#' + el);
	// 화면의 중앙에 레이어를 띄운다.
	if (temp.outerHeight() < $(document).height() ) temp.css('margin-top', '-'+temp.outerHeight()/2+'px');
	else temp.css('top', '0px');
	//if (temp.outerWidth() < $(document).width() ) temp.css('margin-left', '-'+temp.outerWidth()/2+'px');
    if (temp.outerWidth() < $(document).width() ) temp.css('margin-left', '-'+temp.outerWidth()/4+'px');
	else temp.css('left', '0px');

}

function T_outLayer(){
	$("#s_keyword").val("");
	$("#productList").html("");
	$('#prlistMode').val("");
}

function T_productListSearch(){
	var code_a = $("#t_code_a").val();
	var code_b = $("#t_code_b").val();
	var code_c = $("#t_code_c").val();
	var code_d = $("#t_code_d").val();
    var sel_vender = $("#sel_vender").val();
	var s_keyword = $("#s_keyword").val();
	var s_prod_keyword = $("#s_prod_keyword").val();
	var prlistMode = $("#prlistMode").val();
	var box_no = $("#box_no").val();
	$.post(
		"layer_rproduct.php",
		{
			code_a:code_a,
			code_b:code_b,
			code_c:code_c,
			code_d:code_d,
            sel_vender:sel_vender,
			s_keyword:s_keyword,
			s_prod_keyword:s_prod_keyword,
			prlistMode:prlistMode,
			box_no:box_no
		},
		function(data){
			$("#productList").html(data);
			T_layerResize('layer_product_sel');
		}
	);
}

function T_GoPage(block,gotopage){
	var code_a = $("#t_code_a").val();
	var code_b = $("#t_code_b").val();
	var code_c = $("#t_code_c").val();
	var code_d = $("#t_code_d").val();
    var sel_vender = $("#sel_vender").val();
	var s_keyword = $("#s_keyword").val();
	var prlistMode = $("#prlistMode").val();
	var box_no = $("#box_no").val();
	/*
	$.post(
		"layer_prlistPost.php",
		{
			code_a:code_a,
			code_b:code_b,
			code_c:code_c,
			code_d:code_d,
            sel_vender:sel_vender,
			prlistMode:prlistMode,
			s_keyword:s_keyword,
			block:block,
			gotopage:gotopage,
			box_no:box_no
		},
		function(data){
			$("#productList").html(data);
			T_layerResize('layer_product_sel');
		}
	);
	*/
}

function T_onProductcode(recordname, prname,prcode,primg){
	var upList = true;
	var appHtml = "";
	var limit	= $("input[name='limit_"+recordname+"']").val();

	$("input[name='"+recordname+"[]']").each(function(){
		if($(this).val() == prcode){
			alert('상품이 중복되었습니다.');
			upList = false;
			return;
		}
	});

	if (limit == 1)
	{
		if(upList){
			$("#check_"+recordname).find("tr").remove();
		}
	} else {
		if (limit != '')
		{
			if($("input[name='"+recordname+"[]']").length >= limit){
				alert('상품은 '+limit+'개까지 등록이 가능합니다.');
				upList = false;
				return;
			}
		}
	}

	if(upList){
		
		appHtml = "<tr align=\"center\">\n";
		if (limit > 1 || limit =='')
		{
			appHtml+= "	<td style='border:0px'>\n";
			appHtml+= "		<a name=\"pro_upChange\" style=\"cursor: hand;\">\n";
			appHtml+= "			<img src=\"images/btn_plus.gif\" border=\"0\" style=\"margin-bottom: 3px;\" />\n";
			appHtml+= "		</a>\n";
			appHtml+= "		<br>\n";
			appHtml+= "		<a name=\"pro_downChange\" style=\"cursor: hand;\">\n";
			appHtml+= "			<img src=\"images/btn_minus.gif\" border=\"0\" style=\"margin-top: 3px;\" />\n";
			appHtml+= "		</a>\n";
			appHtml+= "	</td>\n";
		}
		appHtml+= "	<td style='border:0px'>\n";
		appHtml+= "		<img style=\"width: 40px; height:40px;\" src=\""+primg+"\" border=\"1\"/>\n";
		appHtml+= "		<input type='hidden' name='"+recordname+"[]' class='"+recordname+"' value='"+prcode+"'>\n";
		appHtml+= "	</td>\n";
		appHtml+= "	<td style='border:0px' align=\"left\">"+prname+"&nbsp;&nbsp;<img src=\"images/icon_del1.gif\" onclick=\"javascript:T_relationPrDel('"+prcode+"','"+recordname+"');\" border=\"0\" style=\"cursor: hand;vertical-align:middle;\" />\n";
		appHtml+= "	</td>\n";
		appHtml+= "</tr>\n";
		$("#check_"+recordname).append(appHtml);

		if (limit == 1)
		{
			var temp = $('#layer_product_sel');
			var bg = temp.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수
			if(bg){
				temp.parent().fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
				T_outLayer();
			}else{
				temp.fadeOut();
				T_outLayer();
			}
		}
	}
}

function T_onProductcode_pop(recordname, prname,prcode,primg, box_no){
	$("input[name='p_number"+box_no+"']").val(prcode);
	var temp = $('#layer_product_sel');
	var bg = temp.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수
	if(bg){
		temp.parent().fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
		T_outLayer();
	}else{
		temp.fadeOut();
		T_outLayer();
	}
	
}

function T_relationPrDel(prcode, recordname){
	if(confirm('상품을 삭제하시겠습니까?')){
		$("input[name='"+recordname+"[]']").each(function(){
			if($(this).val() == prcode){
				$(this).parent().parent().remove();
			}
		});
	}
}

//위로 이동
$(document).on('click', 'a[name=pro_upChange]',function(e){
	//클릭된 TR
	var targetTR = $(e.target).parent().parent().parent();
	//alert($(targetTR).prev().length);
	if($(targetTR).prev().length == 0) return;
	$(targetTR).prev().before($(targetTR));
});
//아래로 이동
$(document).on('click', 'a[name=pro_downChange]', function(e){
	//클릭된 TR
	var targetTR = $(e.target).parent().parent().parent();
	//alert($(targetTR).next().length);
	if($(targetTR).next().length == 0) return;
	$(targetTR).next().after($(targetTR));
});

// ====================================================================
// 브랜드 검색용
// ====================================================================

function T_brandListSearch() {
	var s_keyword = $("#s_brand_keyword").val();

	$.post(
		"layer_brandListPost.php",
		{
			s_keyword:s_keyword,
		},
		function(data){
			$("#brandList").html(data);
			T_layerResize('layer_brand_sel');
		}
	);

}

function T_onBrandcode(brandName, bridx, s_img){
    // 일단 이미 등록된 브랜드인지 체크
    if ( $("#tr_brand_" + bridx).length == 1 ) {
        alert("이미 선택한 브랜드입니다.");
        return false;
    }

    var s_html = '<tr align="center" id="tr_brand_' + bridx + '">';
    s_html += '<td style="border:0px">';
    s_html += '<img style="width: 40px; height:40px;" src="' + s_img + '" border="1">';
    s_html += '<input type="hidden" name="s_brand[]" value="' + bridx + '">';
    s_html += '</td>';
    s_html += '<td style="border:0px" align="left">';
    s_html += brandName + '&nbsp;&nbsp;<img src="images/icon_del1.gif" border="0" style="cursor: hand;vertical-align:middle;" onClick="javascript:T_delBrandList(\'' + bridx + '\');">';
    s_html += '</td>';        
    s_html += '</tr>';

    $("#sel_brand_list").append(s_html);
}

function T_delBrandList(bridx) {
    $("#tr_brand_" + bridx).remove();
}

function T_Brand_GoPage(block,gotopage){
	var s_keyword = $("#s_brand_keyword").val();

	$.post(
		"layer_brandListPost.php",
		{
			s_keyword:s_keyword,
			block:block,
			gotopage:gotopage,
		},
		function(data){
			$("#brandList").html(data);
			T_layerResize('layer_brand_sel');
		}
	);
}





