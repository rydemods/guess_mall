//카카오스토리(웹)
function kakaoStory(){
    var snsData = {
            imageTitle  : $('#link-title').val(),
            linkUrl     : $('#link-url').val() 
    };
    var target   = '#kakaostory-link';
    
    Kakao.Story.share({
        url: snsData.linkUrl,
        text: snsData.imageTitle,

  });
	
}

//카카오톡
function kakaotalkShare(label, img, text, url){
    // 카카오 key
    Kakao.init('914d0b932f83f00a9693fefb9155ff76');
    
    Kakao.Link.createTalkLinkButton({
        container: '#kakao-link',
        label: label,
        image: {
            src: img,
            width: '300',
            height: '300'
        },
        webButton: {
            text: text,
            url: url // 앱 설정의 웹 플랫폼에 등록한 도메인의 URL이어야 합니다.
        }
    });
}

function sendSns(sns, url, txt)
{
    var o;
    var _url = encodeURIComponent(url);
    var _txt = encodeURIComponent(txt);
    var _br  = encodeURIComponent('\r\n');
    var snsData = {
        linkCode : $('#link-code').val(),
        linkMenu : $('#link-menu').val()	
    };
    if($("#link-memid").val() != ""){
	    $.ajax({
	        type        : "POST",
	        url           : "../front/sns_point_insert_proc.php",
	        data        : { snstype : sns, code : snsData.linkCode, menu : snsData.linkMenu },
	        async      : false
	    }).done(function ( result ) {
	    	
	    });
    }
    switch(sns)
    {
        case 'facebook':
            o = {
                method:'popup',
                url:'http://www.facebook.com/sharer/sharer.php?u=' + _url
            };
            break;
 
        case 'twitter':
            o = {
                method:'popup',
                url:'http://twitter.com/intent/tweet?text=' + _txt + '&url=' + _url
            };

            break;
 
        case 'kakaostory':
            o = {
                method:'web2app',
                param:'posting?post=' + _txt + _br + _url + '&apiver=1.0&appver=2.0&appid=dev.epiloum.net&appname=' + encodeURIComponent('Epiloum 개발노트'),
                a_store:'itms-apps://itunes.apple.com/app/id486244601?mt=8',
                g_store:'market://details?id=com.kakao.story',
                a_proto:'storylink://',
                g_proto:'scheme=kakaolink;package=com.kakao.story'
            };

            break;
 
        case 'band':
            o = {
                method:'web2app',
                param:'create/post?text=' + _txt + _br + _url,
                a_store:'itms-apps://itunes.apple.com/app/id542613198?mt=8',
                g_store:'market://details?id=com.nhn.android.band',
                a_proto:'bandapp://',
                g_proto:'scheme=bandapp;package=com.nhn.android.band'
            };

            break;
 
        default:
            alert('지원하지 않는 SNS입니다.');
            return false;
    }
 
    switch(o.method)
    {
        case 'popup':
            window.open(o.url);
            break;
 
        case 'web2app':
            if(navigator.userAgent.match(/android/i))
            {
                // Android
                setTimeout(function(){ location.href = 'intent://' + o.param + '#Intent;' + o.g_proto + ';end'}, 100);

            }
            else if(navigator.userAgent.match(/(iphone)|(ipod)|(ipad)/i))
            {
                // Apple
                setTimeout(function(){ location.href = o.a_store; }, 200);          
                setTimeout(function(){ location.href = o.a_proto + o.param }, 100);

            }
            else
            {
                alert('이 기능은 모바일에서만 사용할 수 있습니다.');
            }
            break;
    }
    
}

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
		url: "../front/product_like_proc.php",
		data: "code="+productCode+"&liketype="+likeType+"&section="+section+"&page="+menu+"&section2="+section2,
		dataType:"JSON"
	}).done(function(html){
// 		console.log(html);
		if(menu == "mypage_m"){
			location.reload();
			//$(".mypage-community-content").html(html);
		}else if(menu == "mypage_like_m"){
			gotoreload();
			//$(".comp-posting").html(html);
		}			
	});
}

//메인, 상품상세 페이지->좋아요
function detailSaveLike(productCode, likeType, section, memId, brand){
	if(memId != ""){
		$.ajax({
			type: "POST",
			url: "../front/product_like_proc.php",
			data: "code="+productCode+"&liketype="+likeType+"&section="+section+"&brand="+brand,
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
			
			$(".like_"+sec+"count_"+productCode).html("<strong>좋아요</strong>"+data[0]['hott_cnt']);

			//좋아요(상품, 인스타그램 구분)
			if(data[0]['section'] == "instagram"){
				$(".like_i"+productCode).addClass("on");
				$(".like_i"+productCode).removeAttr("onclick");
				$(".like_i"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"on\",\"instagram\",\""+memId+"\",\""+brand+"\")");
			}else if(data[0]['section'] == "product"){
				$(".like_p"+productCode).addClass("on");
				$(".like_p"+productCode).removeAttr("onclick");
				$(".like_p"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"on\",\"product\",\""+memId+"\",\""+brand+"\")");
			}else if(data[0]['section'] == "storestory"){ // 스토어 스토리 추가
				$(".like_s"+productCode).addClass("on");
				$(".like_s"+productCode).removeAttr("onclick");
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
					$(".like_p"+productCode).removeClass("on");
					$(".like_p"+productCode).removeAttr("onclick");
					$(".like_p"+productCode).attr("onclick","detailSaveLike(\""+productCode+"\",\"off\",\"product\",\""+memId+"\",\""+brand+"\")");
					
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
		var url = "../m/login.php?chUrl=/m/";
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
		var url = "../m/login.php?chUrl=/";
		$(location).attr('href',url);
	}	
}   

//배열 평균 구하기
function average(array) {
  var sum = 0;
  for (var i = 0; i < array.length; i++)
  	sum += parseInt( array[i], 10 );
  return sum / array.length;
}

//마이페이지 > 룩북 상세
function detail_lookbook(no){
	accessPlus(no,"tbllookbook","access","no");
	var url = "../m/lookbook_detail?no="+no			
	$(location).attr('href', url);
}

//마이페이지 > 매거진 상세
function detail_magazine(no){
	accessPlus(no,"tblmagazine","access","no");
	var url = "../m/magazine_detail.php?no="+no
	$(location).attr('href', url);
}

//마이페이지 > 포럼 상세
function detail_forum(index){
	var url = "../m/forum_view.php?index="+index
	$(location).attr('href', url);
}

// 2016-11-21 code 0 에러 뜨는거 방지위해 일단 타임아웃시간 연장..디폴트는 null 이라고 함.
$.ajaxSetup({
                timeout: 5000
            });

//모든 Ajax loading 이미지 
/*
$(document).ajaxStart(function () {
	$.blockUI({ message : $('.dimm-loading') });
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
        url           : "../front/access_plus_proc.php",
        data        : { idx : idx, table : table, column : column, seq_column : seq_column}
    }).done(function ( result ) {

    });
}

//엔터키 차단
function captureReturnKey(e) { 
    if(e.keyCode==13 && e.srcElement.type != 'textarea') 
    return false; 
} 