
( function($) {
    // 상품 조합형 opt 검색
    jQuery.fn.mixture_opt_search = function ( options ){
        var default_opts = {
            opt_code : '',
            depth    : 1
        };
        var opts      = jQuery.extend( default_opts, options ); // 검색될 option
        var searchArr = []; // 옵션 중복방지용 arr
        var opt_obj   = []; // 출력될 option
        // 잘못된 검색을 막기
        if( opts.opt_code == '' || opts.depth <= 0 ) {
            return opt_obj;
        }
        // 해당 검색되는 obj 추출
        this.each( function(){
            var $originalElement = jQuery( this );
            var code  = $originalElement[0].code;
            if( code.indexOf( opts.opt_code ) != -1 ) {
                opt_obj.push( $originalElement[0] );
            }
        });

        // 마지막 depth가 아니면 코드가 중복되지 않게 처리
        if( opt_obj.length > 0 ){
            $.each( opt_obj, function( i,  v ){
                var split_code  = v.code.split( String.fromCharCode( 30 ) ); // 옵션을 depth별로 자른다
                var code_length = split_code.length;
                var depth       = opts.depth;
                var temp_code  = []; // 중복 방지용 코드 temp
                for( var j = 0; j < depth; j++ ){
                    temp_code.push( split_code[j] );
                }
                // 전체 코드의 depth는 0보다 커야하고 code의 depth보다는 같거나 작야아 한다
                if( code_length >= depth && depth > 0 ){
                    if( $.inArray( split_code[ depth - 1 ], searchArr ) == -1 ) { // 옵션명의 중복을 막는다
                        var code       = v.code;
                        var price      = v.price;
                        var qty        = v.qty;
                        var change_obj = {};
                        if( depth == code_length ){ // 조합형 옵션의 마지막 depth일 경우
                            change_obj = {
                                code      : temp_code.join( chr(30) ),
                                code_name : split_code[ depth - 1 ],
                                price     : price,
                                qty       : qty
                            };
                        } else { // 아닐경우
                            change_obj = {
                                code      : temp_code.join( chr(30) ),
                                code_name : split_code[ depth - 1 ],
                                price     : 0,
                                qty       : 0
                            }
                        }
                        $.extend( $(this)[0], change_obj ); // 변경된 내용을 적용
                        searchArr.push( split_code[ depth - 1 ] ); // 현제 depth에 중복되는 코드를 array에 넣어준다
                    } else {
                        delete opt_obj[i]; // 중복되는 코드는 삭제한다
                    }
                } else {
                    delete opt_obj[i]; // 해당 안되는 코드는 삭제한다
                }
            });
        }
        return opt_obj;
    };
    
})(jQuery);

// 카카오 링크설정
// //developers.kakao.com/sdk/js/kakao.min.js 필요
function KakaoInit( setData ){
    var snsData = {
        label       : encodeURIComponent( $('#link-label').val() ),
        imageTitle  : encodeURIComponent( $('#link-title').val() ),
        imageUrl    : $('#link-image').val(),
        imageWidth  : $('#link-image').data('width'),
        imageHeight : $('#link-image').data('height'),
        linkUrl     : encodeURIComponent( $('#link-url').val() )
    };
    var kakaoKey = '914d0b932f83f00a9693fefb9155ff76';
    var target   = '#kakao-link';
    if( setData ){
        if( setData.snsData ){
            snsData = $.extend( snsData, setData.snsData );
        }
        if( snsData.key ){
            kakaoKey = setData.key;
        }
        if( snsData.target ){
            target = setData.target;
        }
    }
    Kakao.init( kakaoKey );
    // // 카카오톡 링크 버튼을 생성합니다. 처음 한번만 호출하면 됩니다.
    Kakao.Link.createTalkLinkButton({
        container: target,
        label: snsData.label,
        image: {
            src: snsData.imageUrl,
            width: snsData.imageWidth,
            height: snsData.imageHeight
        },
        webButton: {
            text: snsData.imageTitle,
            url: snsData.linkUrl
        }
    });
}
// snsLink 클릭시 popup
function snsLinkPop( setData ){
    var elementTarget = $(this);
    var imgPath = $('#link-img-path').val();
    // 대상 정보
    var eventButton = {
        facebook : 'facebook-link',
        twitter  : 'twitter-link',
        band     : 'band-link'
    };
    var snsData = {
        label       : encodeURIComponent( $('#link-label').val() ),
        imageTitle  : encodeURIComponent( $('#link-title').val() ),
        imageUrl    : imgPath + $('#link-image').val(),
        imageWidth  : $('#link-image').data('width'),
        imageHeight : $('#link-image').data('height'),
        linkUrl     : encodeURIComponent( $('#link-url').val() ),
        linkCode : $('#link-code').val(),
        linkMenu : $('#link-menu').val()
    };
    var urlPop = '';
    // data가 수정되면 바꿔준다 eventButton / snsData
    if( setData ){
        if( setData.eventButton ){
            eventButton = $.extend( eventButton, setData.eventButton );
        }
        if( setData.snsData ){
            snsData = $.extend( snsData, setData.snsData );
        }
    }
    $.each( eventButton, function( i,  v ){
        if( $( elementTarget ).attr( 'id' ) == v ){
            if( i === 'facebook' ){
                urlPop = "http://www.facebook.com/sharer.php?u=" + snsData.linkUrl;
//            	FB.init({
//            	    appId      : '1068546396586164',
//            	    status     : true,
//            	    xfbml      : true,
//            	    version    : 'v2.7' // or v2.6, v2.5, v2.4, v2.3
//            	  });
//        		var obj = {
//        			method:'feed',
//        			link:$('#link-url').val(),// 네임 or 이미지에 첨부될 주소
//        			picture: snsData.imageUrl, //사진URL 
//        			name:$('#link-label').val(),  //제목
//        			caption:$('#link-label').val(), //약어
//        			description: $('#link-title').val() //내용
//        		}; function callback(response){
//        			  if (response && response.post_id) {
//
//        			  } else {
//
//        			  }
//                }FB.ui(obj,callback);
                if($("#link-memid").val() != ""){
		            $.ajax({
		                type        : "POST",
		                url           : "sns_point_insert_proc.php",
		                data        : { snstype : i, code : snsData.linkCode, menu : snsData.linkMenu }
		            }).done(function ( result ) {
		            	console.log(result);
		            });
            	}
            } else if( i === 'twitter' ) {
            	var url = 'https://twitter.com/intent/tweet?url=' +  $('#link-url').val()  + '&text=' + $('#link-title').val();		
            	$("#twitter-link").attr('href', url);
            	twttr.events.bind('tweet',function (ev) {
                    if($("#link-memid").val() != ""){
			            $.ajax({
			                type        : "POST",
			                url           : "sns_point_insert_proc.php",
			                data        : { snstype : i, code : snsData.linkCode, menu : snsData.linkMenu },
			            	async      : false
			            }).done(function ( result ) {
			            });
                    }
            	});

            } else if( i === 'band' ) {
                urlPop = 'http://band.us/plugin/share?body=' + snsData.imageTitle + '&route=' + snsData.linkUrl;
                if($("#link-memid").val() != ""){		
		            $.ajax({
		                type        : "POST",
		                url           : "sns_point_insert_proc.php",
		                data        : { snstype : i, code : snsData.linkCode, menu : snsData.linkMenu }
		            }).done(function ( result ) {
		            });
                }
            }
        }
    });

    if( urlPop ){
        var windowPop = window.open( urlPop, 'snsPop', "width=500, height=500" );

        windowPop.focus();
    }

}

//카카오스토리
function kakaoStory(){
    var snsData = {
            imageTitle  : $('#link-title').val(),
            linkUrl     : $('#link-url').val(),
            linkCode : $('#link-code').val(),
            linkMenu : $('#link-menu').val()
    };
    var target   = '#kakaostory-link';
    
    Kakao.Story.share({
        url: snsData.linkUrl,
        text: snsData.imageTitle,

  });
    if($("#link-memid").val() != ""){
	    $.ajax({
	        type        : "POST",
	        url           : "sns_point_insert_proc.php",
	        data        : { snstype : "kakaostory", code : snsData.linkCode, menu : snsData.linkMenu }
	    }).done(function ( result ) {
	    });
    }
}

function ClipCopy(url) {
	var IE=(document.all)?true:false;
	if (IE) {
		if(confirm("현제 페이지의 클립보드에 복사하시겠습니까?"))
			window.clipboardData.setData("Text", url);
	} else {
		temp = prompt("현제 페이지의 주소입니다. Ctrl+C를 눌러 클립보드로 복사하세요", url);
	}
}

//마이페이지->좋아요
function saveLike(productCode, likeType, section, section2 ,memId, menu){

	$.ajax({
		type: "POST",
		url: "product_like_proc.php",
		data: "code="+productCode+"&liketype="+likeType+"&section="+section+"&page="+menu+"&section2="+section2,
		dataType:"HTML"
	}).done(function(html){
// 		console.log(html);
		if(menu == "mypage"){
			$(".mypage-community-content").html(html);
		}else if(menu == "mypage_like"){
			$(".comp-posting").html(html);
		}			
	});
}

//메인, 상품상세 페이지->좋아요
function detailSaveLike(productCode, likeType, section, memId, brand){
//	console.log("detailSaveLike");

	if(memId != ""){
		$.blockUI();
        setTimeout($.unblockUI, 300); //중복방지
        var param = "code="+productCode+"&liketype="+likeType+"&section="+section+"&brand="+brand;
		$.ajax({
			type: "POST",
			url: "../front/product_like_proc.php",
			data: param,
			dataType:"JSON"
		}).done(function(data){
			if(section == "instagram"){
				var sec = "i";
			}else if(section == "product"){
				var sec = "p";
			}else if(section == "storestory"){
				var sec = "s";
			}else if(section == "magazine"){
				var sec = "m";
			}else if(section == "lookbook"){
				var sec = "l";
			}else if(section == "forum_list_mypage"){
				var sec = "f";
			}

//			$(".like_"+sec+"count_"+productCode).html("<strong>좋아요</strong>"+data[0]['hott_cnt']);
			
			//$(".like_"+sec+"count_"+productCode).html(data[0]['hott_cnt']);

			//$(".like-cnt-txt", _goodsPreview).text("("+data[0]['hott_cnt']+")");
						
			//$(".btn-line", _goodsPreview).attr("onclick","detailSaveLike(\""+productCode+"\",\""+btn_likeType+"\",\""+section+"\",\""+memId+"\",\""+brand+"\")");
			//$(".btn_like", _goodsPreview).attr("onclick","detailSaveLike(\""+productCode+"\",\""+btn_likeType+"\",\""+section+"\",\""+memId+"\",\""+brand+"\")");

			//좋아요(상품, 인스타그램 구분)
			if(data[0]['section'] == "instagram"){
				$(".like_i"+productCode).addClass("on");
				$(".like_i"+productCode).removeAttr("onclick");
				$(".like_i"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"on\",\"instagram\",\""+memId+"\",\""+brand+"\")");
				
			}else if(data[0]['section'] == "product"){
				
				$(".like-check-"+productCode).removeAttr("onclick");
				$(".like-check-"+productCode).attr("onclick","detailSaveLike('"+productCode+"','on','product','"+memId+"','"+brand+"')");
					
				$('.like-cnt-txt').html('('+data[0]['hott_cnt']+')');

								
			}else if(data[0]['section'] == "storestory"){ // 스토어 스토리 추가
				$(".like_s"+productCode).addClass("on");
				$(".like_s"+productCode).removeAttr("onclick");
//				$("#likehott_"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"on\",\"product\",\""+memId+"\",\""+brand+"\")");
				$(".like_s"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"on\",\"storestory\",\""+memId+"\",\""+brand+"\")");
			}else if(data[0]['section'] == "magazine"){ // 메거진 추가
				$(".like_m"+productCode).addClass("on");
				$(".like_m"+productCode).removeAttr("onclick");
				$(".like_m"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"on\",\"magazine\",\""+memId+"\",\""+brand+"\")");
			}else if(data[0]['section'] == "lookbook"){ // 룩북 추가
				$(".like_l"+productCode).addClass("on");
				$(".like_l"+productCode).removeAttr("onclick");
				$(".like_l"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"on\",\"lookbook\",\""+memId+"\",\""+brand+"\")");
			}else if(data[0]['section'] == "forum_list"){ // 포럼 추가
				$(".like_f"+productCode).addClass("on");
				$(".like_f"+productCode).removeAttr("onclick");
				$(".like_f"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"on\",\"forum_list_mypage\",\""+memId+"\",\""+brand+"\")");
			}else{
				//좋아요 취소 이후(상품, 인스타그램 구분)
				
				if(section== "instagram"){
					$(".like_i"+productCode).removeClass("on");
					$(".like_i"+productCode).removeAttr("onclick");
					$(".like_i"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"off\",\"instagram\",\""+memId+"\",\""+brand+"\")");
					
				}else if(section == "product"){
					$(".like-check-"+productCode).removeAttr("onclick");
					$(".like-check-"+productCode).attr("onclick","detailSaveLike('"+productCode+"','off','product','"+memId+"','"+brand+"')");
					$('.like-cnt-txt').html('('+data[0]['hott_cnt']+')');
					
//					$(".like_p"+productCode).removeClass("on");
//					$(".like_p_button"+productCode).removeAttr("onclick");
//					$(".like_p_button"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"off\",\"product\",\""+memId+"\",\""+brand+"\")");
//					$(".like_p"+productCode).removeClass("on");
//					$(".like_p"+productCode).removeAttr("onclick");
//					$(".like_p"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"off\",\"product\",\""+memId+"\",\""+brand+"\")");
					
				}else if(section == "storestory"){ // 스토어 스토리 추가
					$(".like_s"+productCode).removeClass("on");
					$(".like_s"+productCode).removeAttr("onclick");
					$(".like_s"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"off\",\"storestory\",\""+memId+"\",\""+brand+"\")");
				}else if(section == "magazine"){ // 메거진 추가
					$(".like_m"+productCode).removeClass("on");
					$(".like_m"+productCode).removeAttr("onclick");
					$(".like_m"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"off\",\"magazine\",\""+memId+"\",\""+brand+"\")");
				}else if(section == "lookbook"){ // 룩북 추가
					$(".like_l"+productCode).removeClass("on");
					$(".like_l"+productCode).removeAttr("onclick");
					$(".like_l"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"off\",\"lookbook\",\""+memId+"\",\""+brand+"\")");
				}else if(section == "forum_list_mypage"){ // 포럼 추가
					$(".like_f"+productCode).removeClass("on");
					$(".like_f"+productCode).removeAttr("onclick");
					$(".like_f"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"off\",\"forum_list_mypage\",\""+memId+"\",\""+brand+"\")");
				}
				
			}	
			
			

		});
	}else{
		//로그인 상태가 아닐때 로그인 페이지로 이동
		var url = "../front/login.php?chUrl=/";
		$(location).attr('href',url);
	}	
}

//파라미터 가져오기
function getUrlParams() {
    var params = {};
    window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(str, key, value) { params[key] = value; });
    return params;
} 

//호감/비호감
function select_feeling(num,section, feeling_type, memId){
	if(memId != ""){
	    var param =   { "num" : num, "section" : section, "feeling_type" : feeling_type }
	    $.ajax({
	        type        : "POST",
	        url         : "../front/ajax_good_feeling.php",
	        data        : param,
			dataType:"JSON"
	    }).done(function ( data ) {
	    	if(data[0]['no'] == 0){
				alert("이미 선택하셨습니다.");
	    	}else{
	        	if(data[0]['feeling_type'] == "good"){
	        		if(data[0]['point_type'] == "plus"){
	        			alert("호감을 선택하였습니다.");
	        		}else{
	        			alert("호감 선택을 해제 하였습니다.");
	        		}
	        		
	        	}else{
	        		if(data[0]['point_type'] == "plus"){
	        			alert("비호감을 선택하였습니다.");
	        		}else{
	        			alert("비호감 선택을 해제 하였습니다.");
	        		}
	        	}
	        	$("#feeling_"+feeling_type+"_"+section+"_"+num ).text(data[0]['feeling_cnt']);
	
	    	}		
	    });
	}else{
		//로그인 상태가 아닐때 로그인 페이지로 이동
		var url = "../front/login.php?chUrl=/";
		$(location).attr('href',url);
	}    
}    

//html 태그 제거
function strip_tags (input, allowed) {
    allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {        return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}

//인스타그램 상세 페이지 초기화
function reset(){
	$(".detail-like").remove(""); 
	$(".related-list").empty("");
	$("#instagram_img").attr("src","");
}

// 배열 평균 구하기
function average(array) {
  var sum = 0;
  for (var i = 0; i < array.length; i++)
  	sum += parseInt( array[i], 10 );
  return sum / array.length;
}

//마이페이지 > 룩북 상세
function detail_lookbook(no){
	accessPlus(no,"tbllookbook","access","no");
	var url = "../front/lookbook_view.php?no="+no			
	$(location).attr('href', url);
}

//마이페이지 > 매거진 상세
function detail_magazine(no){
	accessPlus(no,"tblmagazine","access","no");
	var url = "../front/magazine_detail.php?no="+no
	$(location).attr('href', url);
}

//마이페이지 > 포럼 상세
function detail_forum(index){
	var url = "../front/forum_view.php?index="+index
	$(location).attr('href', url);
}

// 2016-11-21 code 0 에러 뜨는거 방지위해 일단 타임아웃시간 연장..디폴트는 null 이라고 함.
$.ajaxSetup({
                timeout: 5000
            });

//모든 Ajax loading 이미지 
/*$(document).ajaxStart(function () {
	$.blockUI({ message : $('#dimm-loading') });
	//$.blockUI({ message : '<IMG src="/static/img/common/loading.gif">' });
});
$(document).ajaxSuccess(function() {
	$.unblockUI(); 
});
$( document ).ajaxError(function() {
	$.unblockUI(); 
});
*/

//조회수 증가
function accessPlus(idx, table, column, seq_column){
    $.ajax({
        type        : "POST",
        url           : "access_plus_proc.php",
        data        : { idx : idx, table : table, column : column, seq_column : seq_column}
    }).done(function ( result ) {

    });
}

//엔터키 차단
function captureReturnKey(e) { 
    if(e.keyCode==13 && e.srcElement.type != 'textarea') 
    return false; 
} 

function proSearchChk() {
    if ( $("#search").val().trim() === "" ) {
		alert("검색어를 입력해주세요.");
        $("#search").val("").focus();
		return;
	}
    document.form.submit();
}

function enterSearch(e){
	var search = $("#search").val();
	if (e.keyCode == 13) {
		if(search == ""){
			alert("검색어를 입력해주세요");
			$("#search").focus();
			return;
		}else{
			var url = "../front/productsearch.php?search="+search
			$(location).attr('href', url);
		}	
	}
}
