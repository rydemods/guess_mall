function resetReviewView() {
    var reviewSubject = $('.view-bbs-list td.subject , td.review-subject , tr.my-write-review');
    var reviewContent = $('.view-bbs-list tr.open-content , .open-content');

    reviewContent.hide();
	/*
    reviewSubject.on("click", function(e){
        var idx = reviewSubject.index($(this)) - 1;

        reviewContent.eq(idx).toggle();
    });
	*/
}

//상품 상세
function prod_detail(productcode){
	var url = "../front/productdetail.php?productcode="+productcode;
	$(location).attr('href',url);
}

// 리뷰 on / off
$(document).on( "click", '.board td.title , td.review-subject , tr.my-write-review, .best-review a', function( e ) {
	var review_num = $(this).attr("ids");

	if( $(this).parent().parent().parent().hasClass('bbs-qna') ){
		var obj;
		var number = $(this).attr("id");
		
		if ( $(this).attr('class') === "my-write-review" ) { 
            obj = $(this).next();
        } else {
            obj = $(this).parent().next();
            obj2 = $(this).parent().parent().parent();
        }
		console.log($(obj).find('.content').length);
		console.log( $("input[name=data-member]").val());
		console.log($(".date"+number).attr("id"));

        if( $(obj).find('.content').length > 0 && $(obj).attr('data-secret') == '1' ){
        	if( $("input[name=data-member]").val() == '' &&  $(obj).attr('data-secret') == '1' ) {
                alert('비밀 글입니다.');
                $(obj2.find('tbody').attr("class",""));
            }else if($("input[name=data-member]").val() != $(".date"+number).attr("id") &&$(obj).attr('data-secret') == '1'){
            	alert('비밀 글입니다.');
            	 $(obj2.find('tbody').attr("class",""));
            }
        } else if( $(obj).attr('data-secret') == '1' ) {
        	 alert('비밀 글입니다.');
        	 $(obj2.find('tbody').attr("class",""));
        } else {
//            $(obj).toggle();
        }
        

    } else if ( review_num ) {
        var obj;

		console.log($(this).attr('class'));

        if ( $(this).attr('class') === "my-write-review" ) { 
            obj = $(this).next();
        } else {
            obj = $(this).parent().next();
        }

		console.log(obj);
        $(obj).toggle();
        if ( $(obj).css("display") != "none" ) {
            // 리뷰가 보여진 경우
        
            $.ajax({
                url  : "ajax_incr_review_count.php",
                type : "get",
                data : {review_num: review_num}
            }).success(function(data){
                // ; do nothing
            }).error(function () {
                // alert("다시 시도해 주세요.");
            });
        }
    }

});