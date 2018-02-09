$( document ).ready(function() {
	//<![CDATA[
	Kakao.init($(".share-kakao-script").val());
	$(".kakao-story-btn").click(function(){
		Kakao.Story.share({
			url: $(".share-url").val(),
			text: $(".share-title").val()
		});

	})

	Kakao.Link.createTalkLinkButton({
		container: '.kakao-talk-btn',
		label: $(".share-title").val(),
		image: {
			src: $(".share-img").val(),
			width: '300',
			height: '300'
		},
		webButton: {
			text: "사이트로 이동",
			url: $(".share-url").val()
		}
	});













	window.fbAsyncInit = function() {
		FB.init({
			appId      : '293499384317080', // 앱 ID
			status     : true,          // 로그인 상태를 확인
			cookie     : true,          // 쿠키허용
			xfbml      : true           // parse XFBML
		});
	};

	$(".facebook-btn").click(function(){
		//window.open("http://www.facebook.com/sharer.php?u=" + $(".share-url").val(), "popupShareFacebook", "width=500,height=400,left=200,top=5,scrollbars,toolbar=0,resizable");
		
		FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
				FB.api('/me', function(user) {
					if (user) {
						FB.ui(obj, callback);
					}
				});
			} else {
				FB.login(function(logresponse){
		            var fbname;  
		            var accessToken = logresponse.authResponse.accessToken; 
			        FB.api('/me', function(response) {
						//document.location.reload();
			        });
			    });
			}
		});


		var obj = {
			method: 'feed',
			link: 'http://test-sejung.ajashop.co.kr/front/member_agree.backup.php',
			picture: 'http://test-sejung.ajashop.co.kr/static/img/test/@top_mini_banner.jpg',
			name: '광민',
			caption: '캡션',
			description: '내용'
		};
		
		function callback(response) {
			console.log(response);
			if(response && response.post_id){
				alert("성공");
			}else{
				alert("실패");
			}
		}
	})









	var tweetUrlBuilder = function(o){
		return [
			'https://twitter.com/intent/tweet?tw_p=tweetbutton',
			'&url=', encodeURI(o.url),
			'&text=', o.text
		].join('');
	};

	$(".twitter-btn").click(function(){
		//window.open("https://twitter.com/intent/tweet?text=" + $(".share-text").val() + "&url=" + $(".share-url").val(), "popupShareFacebook", "width=500,height=400,left=200,top=5,scrollbars,toolbar=0,resizable");

		var url = tweetUrlBuilder({
			url : $(".share-url").val(),
			text: $(".share-text").val()
		});
		window.open(url, 'Tweet', 'width=500,height=400,left=200,top=5,scrollbars,toolbar=0,resizable');
	})
	/*
	var callback = function(e){
		console.log("------------------------------------------");
		if(e && e.data){
			var data;
			try{
				data = JSON.parse(e.data);
				console.log(data);
			}catch(e){
				console.log("Error");
				// Don't care.
			}
			if(data && data.params && data.params.indexOf('tweet') > -1){
				console.log('Thanks for the tweet!');
			}
		}
		console.log("------------------------------------------");
	};
	window.addEventListener ? window.addEventListener("message", callback, !1) : window.attachEvent("onmessage", callback);
	*/
	//]]>
});