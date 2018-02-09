<?php 
include($Dir."paygate/paylist.inc.php");
$mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';
$mobile_path = $sendReferer['path'];
$pgurl.='&mobile_path='.$mobile_path;
?>
<html>
<head>
<title>결제</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=UTF-8">
<script type="text/javascript" src="<?=$Dir?>static/js/jquery-1.12.0.min.js" ></script>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
// 카드결제창을 호출한다.
var _pgurl = "<?=$pgurl?>";
var cancel = true;

function PaymentOpen() {
	var cval = getCookie("okpayment");

	var winWidth  = parent.document.body.clientWidth;  // 현재창의 너비
	var winHeight = parent.window.screen.height; // 현재창의 높이
	var winX      = parent.window.screenX || parent.window.screenLeft || 0;// 현재창의 x좌표
	
	if (cval!="result") {
        var paygate_frame = window.parent.document.getElementById('CHECK_PAYGATE');
<?php
if ( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && $mobile_path == '/m/order.php' ) {
?>
        //chargepop=window.open("<?=$pgurl?>","pgopen","width=576,height=316,status=yes,menubar=no,toolbar=no,location=no,scrollbars=no,directories=no");
        window.location.href="<?=$pgurl?>";
<?php
} else if( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && $mobile_path != '/m/order.php' ) {
?>
        chargepop=window.open("<?=$pgurl?>","pgopen","width=576,height=316,status=yes,menubar=no,toolbar=no,location=no,scrollbars=no,directories=no");
        popupBlockerChecker.check( chargepop );
<?php
} else if ( $pg_type == "E" ) {
        // 다날 휴대폰 결제인 경우
        // 팝업창 띄우기
?>
        chargepop=window.open("<?=$pgurl?>","danal_pgopen","width=700,height=505,status=no,menubar=no,resizable=yes,toolbar=no,location=no,scrollbars=no,directories=no");
        popupBlockerChecker.check( chargepop );
<?php
} else if ( $pg_type == "F" ) {
        // 페이코 결제인 경우
        // 팝업창 띄우기
?>
		var popupX = (winX + ((winWidth - 700) / 2));
		var popupY = (winHeight - 505) / 2;
		chargepop=window.open("<?=$pgurl?>","payco_pgopen","width=700,height=505,status=no,menubar=no,resizable=yes,toolbar=no,location=no,scrollbars=no,directories=no");
        popupBlockerChecker.check( chargepop );
<?php
} else if (  $pg_type == "G"  ) {
?>
        // nice 결제인 경우
        // 팝업창 띄우기
        cancel = true;
        chargepop=window.open("<?=$pgurl?>","nice_pgopen","width=700,height=505,status=no,menubar=no,resizable=yes,toolbar=no,location=no,scrollbars=no,directories=no");
        popupBlockerChecker.check( chargepop );

<?php
} else {
?>
        paygate_frame.src = "<?=$pgurl?>";
<?php
}
?>
		//if (!chargepop) parent.ProcessWaitPayment(); 
	} else if (cval=="result") { 
		alert("결제시스템과의 연결이 이미 끝났습니다.");
        parent.location.replace('../front/basket.php');
		//history.go(1);
	}
}
function setCancel() {
	cancel = false;
}

function GetPgUrl(){
    return _pgurl;
}

var popupBlockerChecker = {
    check: function(popup_window){
        var _scope = this;
        if( popup_window == null ) {
            _scope._displayError();
            //alert('1');
        } else if ( popup_window ) {
            if(/chrome/.test(navigator.userAgent.toLowerCase())){
                setTimeout(function () {
                    _scope._is_popup_blocked(_scope, popup_window);
                 },200);
            }else{
                popup_window.onload = function () {
                    _scope._is_popup_blocked(_scope, popup_window);
                };
            }

            if ( popup_window.name == "danal_pgopen" ) {
                // 다날 휴대폰 결제 팝업창을 닫는 경우

                var resUrl = null;
                var timer = setInterval(function() {   
                    try {
                        resUrl = popup_window.resUrl;
                    }
                    catch(err) {}

                    if(popup_window.closed) {  
                        clearInterval(timer);  

                        if ( resUrl != null ) {
                            parent.location.href = resUrl;
                        } else {
                            alert("결제가 취소되었습니다.");
                            parent.location.href = '/front/basket.php';
                        }
                    }
                }, 1000); 
            } else if ( popup_window.name == "payco_pgopen" ) {
                // 페이코 결제창

                var timer = setInterval(function() {   
                    if(popup_window.closed) {  
                        clearInterval(timer);  

                        $.ajax({
                            async: false,
                            url: '/data/backup/payco/<?=$ordercode?>.txt', 
                            success: function(data){
                                // 결제완료

                                parent.location.href = '/front/payresult.php?ordercode=<?=$ordercode?>';
                            },
                            error: function(data){
                                // 결제취소

                                alert("결제가 취소되었습니다.");
                                parent.location.href = '/front/basket.php';
                            },
                        })
                    }
                }, 1000); 

            } else if( popup_window.name == 'nice_pgopen' ){
                // NICEPAY 결제창
                var timer = setInterval(function() {   

                    if(popup_window.closed) {  
                        clearInterval(timer);  

                        if ( cancel ) {
                            alert("결제가 취소되었습니다.");
                            parent.location.href = '/front/basket.php';
                        }
                    }
                }, 1000); 
            }

        }else{
            //alert('2');
            _scope._displayError();
        }
    },
    _is_popup_blocked: function(scope, popup_window){
        if ( popup_window.name != "danal_pgopen" && popup_window.name != "payco_pgopen" ) {
            if ((popup_window.innerHeight > 0)==false){ scope._displayError(); }
        }
    },
    _displayError: function(){
        alert("팝업이 차된되었습니다. 인터넷 설정에서 팝업을 허용해 주시기 바랍니다.");
        //parent.location.href = 'basket.php';
    }
};
//-->
</SCRIPT>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onload="PaymentOpen()">
</body>
</html>
