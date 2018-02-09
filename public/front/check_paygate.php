<head>
<title>한국사이버결제 플러그인 설치 페이지</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<meta http-equiv="Pragma" content="no-cache"/>
<style type="text/css">
	a.btn_D {display:inline-block; color:#fff !important; height:30px; padding:0px 10px; text-align:center; border-radius:2px; font:bold 12px/30px dotum; background-color:#44474c}
	a.btn_D:hover {background-color:#ff4e08}
	span.btn_E {display:inline-block; color:red; height:18px; padding:0px 8px ; border:1px solid red; text-align:center; border-radius:1px;font:13px/20px dotum; background-color:#FFF; vertical-align:middle; letter-spacing:-1px;cursor:pointer;font-weight: bold; }
</style>
<script src="../js/jquery-1.11.1.min.js" type="text/javascript"></script>
<script language='javascript' src='http://pay.kcp.co.kr/plugin/payplus_un.js'></script>
<script type="text/javascript" >
if ( navigator.userAgent.indexOf('MSIE') > 0 )
{
	StartSmartUpdate();
} else {
	kcpTx_install();
}

function SuccessSubmit() {
	clearTimeout(jspT);
	window.parent.layer_close();
	//opener.document.form1.submit();
	//window.close();
}

function jsf__chk_plugin()
{
	
	// IE 일경우 기존 로직을 타게끔
	if ( (navigator.userAgent.indexOf('MSIE') > 0) || (navigator.userAgent.indexOf('Trident/7.0') > 0) )
	{
		if ( document.Payplus.object != null )
		{
			SuccessSubmit();
		} else {
			window.parent.layer_open('layer1');
		}
	}
	// 그 외 브라우져에서는 체크로직이 변경됩니다.
	// 크롬 브라우져에서는 사용을 안함
	else
	{
		if( $('#kcp_mask').css('display') == 'block' ){
			alert("Payplus Plug-in이 설치되지 않았습니다.\n하단의 [수동설치]를 통해 Payplus를 설치해 주세요.\n( 2~3초 후 수동설치 파일이 다운로드 됩니다. )");
		} else {
			SuccessSubmit();	
		}
	}
}
jspT = setTimeout("jsf__chk_plugin()","1000");
</script>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 >



</body>