<?PHP

	//--------------------------------------------------------------------------------
	// PAYCO 주문 완료시 호출되는 RETURN 페이지 샘플 ( PHP EASYPAY / PAY1 )
	// payco_return.php
	// 2016-03-31	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//--------------------------------------------------------------------------------
	header('Content-type: text/html; charset: UTF-8');
	include("payco_config.php");

    $Dir="../../../";
    include_once($Dir."lib/init.php");
    include_once($Dir."lib/lib.php");

    if(strlen(RootPath)>0) {
        $hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
        $pathnum=@strpos($hostscript,RootPath);
        $shopurl=substr($hostscript,0,$pathnum).RootPath;
    } else {
        $shopurl=$_SERVER['HTTP_HOST']."/";
    }

	//---------------------------------------------------------------------------------
	// Payco 결제가 완료되었습니다.
	// 이 페이지는 PAYCO 결제창에서 "주문이 완료되었습니다." 라는 페이지에서 버튼을 눌러야 호출이 되는 페이지 입니다.
	// 고객이 결제창을 x 를 눌러 닫을 수 있기 때문에 주문 목록 페이지로 이동 할 수 없을 수 있습니다.
	// DB 작업이나 기타 작업을 이곳에 하시면 안됩니다.
	//---------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------
	// 오류가 발생했는지 기억할 변수와 결과를 담을 변수를 선언합니다.
	//-----------------------------------------------------------------------------
	
	$code   = $_REQUEST["code"];						// PAYCO 결과코드
	$cartNo = $_REQUEST["cartNo"];                      // 주문예약시 $returnUrlParam 값 
	$orderCode = $_REQUEST["orderCode"];                // 주문예약시 주문코드

    $return_resurl          = "http://".$shopurl.FrontDir."payresult.php?ordercode=".$orderCode;

    if ( $isMobile == 0 ) {
        $error_return_resurl    = "http://".$shopurl.MDir."basket.php";
    } else {
        $error_return_resurl    = "http://".$shopurl.FrontDir."basket.php";
    }
	
	//-----------------------------------------------------------------------------
	// (로그) 호출 시점과 호출값을 파일에 기록합니다.
	//-----------------------------------------------------------------------------
	Write_Log("payco_return.php is Called -  code : $code");
	Write_Log("payco_return.php is Called -  cartNo>>> : $cartNo");
	
	//-----------------------------------------------------------------------------
	// response 값이 없으면 에러(ERROR)를 돌려주고 로그를 기록한 뒤
	// 오류페이지를 보여주거나 주문되지 않았음을 고객에게 통보하는 페이지로 이동합니다.
	//-----------------------------------------------------------------------------
	if($code !== "0"){
	
		Write_Log("payco_return.php ERROR - code : $code");
	
		if($code == 2222){
			?>
			 			<html>
			 				<head>
			 					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			 					<title>사용자 취소</title>
			 				</head>
			 				<script type="text/javascript">
			 					alert("사용자에 의해 취소된 주문입니다. \n" + "ErrCode : <?=$code?> \n");
			 					var isMobile = <?=$isMobile?>;
			 					if(isMobile == 0){
			 						location.href = "<?=$error_return_resurl?>";
			 					}else{
			 						opener.location.href = "<?=$error_return_resurl?>";
			 						window.close(); 		 						 
			 					}
			 				</script>
			 				<body>			
			 				</body>
			 			</html>
			 		<?php	
			 			
			 }else{ 
					
			 		?>	
			 					
				<html>				
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
						<title>주문 실패</title>
					</head>
					<script type="text/javascript">
						alert("결제 승인에 실패했습니다. \n" + "code : <?=$code?> \n");
						var isMobile = <?=$isMobile?>;
						if(isMobile == 0){
							location.href = "<?=$error_return_resurl?>";
						}else{
							opener.location.href = "<?=$error_return_resurl?>";
							window.close(); 
						}
					</script>
					<body>					
					</body>
				</html>
	
			<?php
			
			}
			 
			return;	
		}	
		
	?>
	
		<html>			
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
					<title>주문 완료</title>
				</head>
				<script type="text/javascript">
//					alert("장바구니 <?=$cartNo?>번 의 주문이 정상적으로 완료되었습니다.");
					
					var isMobile = <?=$isMobile?>;
					
					if(isMobile == 0){
						location.href = "<?=$return_resurl?>";
					}else{
						//opener.location.href = "<?=$return_resurl?>"; // 주석 처리하면, 결제상세 조회 (검증용) 입력창에 값이 자동으로 입력 되어, 조회 가능함.

                        // PC버젼인 경우, 그냥 팝업창을 닫는 것으로 함.
						window.close(); 
					}
	
				</script>
				<body>			
				</body>
		</html>
