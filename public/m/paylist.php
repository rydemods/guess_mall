<?php 
include("agspay/paylist.inc.php");
?>
<html>
<head>
<title>결제</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--

var paiedtm;
var chargepop;
function checkOrderPaied(){
<?if($mobile){?>	
	$.get("/paygate/chkpaied.php?ordercode=<?=$ordercode?>", function(data){
		if(data){
			chargepop.close();
			parent.document.location.href='/';
		}
	});

	clearTimeout(paiedtm);
	paiedtm=setTimeout(function(){
	//			chargepop.close();
				checkOrderPaied();
				},3000);
	
<?}?>
}



// 카드결제창을 호출한다.
function PaymentOpen() {
	var cval = getCookie("okpayment");
	if (cval!="result") {
		chargepop=window.open("<?=$pgurl?>","pgopen","width=576,height=316,status=yes,menubar=no,toolbar=no,location=no,scrollbars=no,directories=no");
		if (!chargepop) parent.ProcessWaitPayment(); 

		//checkOrderPaied();

	} else if (cval=="result") { 
		alert("결제시스템과의 연결이 이미 끝났습니다.");
		history.go(1);
	}
}
//-->
</SCRIPT>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onload="PaymentOpen()">

</body>
</html>
