// 상품 상세 레이어 팝업 위민트 170203
var productPreview = "goodsPreview";
function fnShowProductPreviewPop(productcode){
// 	console.log(productcode);
	$.ajax({
		type: "POST",
		url: "../main/ajax_product_preview.php",
		data: "productcode="+productcode,
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		var _layer = $(html);
		_layer.find(".btn-close").bind("click", function(){
			$("."+productPreview).hide();		
		});
		$("body").find("."+productPreview).remove();
		$("body").append(_layer);
		$("."+productPreview).show();
	});
}

