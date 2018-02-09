<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title> new document </title>
	<meta charset="euc-kr">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<meta http-equiv="Content-Style-Type" content="text/css">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
<style>
#layer{
	display:none;
	border:5px solid;position:fixed;
	width:400px;height:560px;
	left:50%;margin-left:-205px;top:50%;margin-top:-285px;overflow:hidden;-webkit-overflow-scrolling:touch;
}
#layer2{
	display:none;
	background-color:red;position:fixed;
	width:350px;height:45px;
	left:50%;margin-left:-175px;top:50%;margin-top:225px;overflow:hidden;-webkit-overflow-scrolling:touch;
}
</style>
<script type="text/javascript" src="/js/jquery-1.10.1.min.js"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.js"></script>
</head>

<body>
<input type="text" id="postcode1"> - <input type="text" id="postcode2">
<input type="button" onclick="showDaumPostcode()" value="우편번호 찾기"><br>
<input type="text" id="address">
<input type="text" id="addressEnglish">

<div id="layer" style="">
<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px" onclick="closeDaumPostcode()" alt="닫기 버튼">

</div>
<!--<div id="layer2"></div>-->

<script>
    // 우편번호 찾기 iframe을 넣을 element
    var element = document.getElementById('layer');

    function closeDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element.style.display = 'none';
		$("#layer2").hide();
    }

    function showDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
                // 우편번호와 주소 및 영문주소 정보를 해당 필드에 넣는다.
                document.getElementById('postcode1').value = data.postcode1;
                document.getElementById('postcode2').value = data.postcode2;
                document.getElementById('address').value = data.address;
                document.getElementById('addressEnglish').value = data.addressEnglish;
                // iframe을 넣은 element를 안보이게 한다.
                element.style.display = 'none';
            },
            width : '100%',
            height : '610px'
        }).embed(element);
		$("#layer2").show();
        // iframe을 넣은 element를 보이게 한다.
        element.style.display = 'block';	
    }
	$(function(){
		$("#test").click(function(){
		});
	});
</script>
<input type="button" value="test" id="test" >
</body>
</html>
