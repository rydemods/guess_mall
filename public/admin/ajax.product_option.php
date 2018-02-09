<?php
/********************************************************************* 
// 파 일 명		: ajax.product_option.php 
// 설     명		: 상품 옵션 리스트
// 상세설명	: 상품코드를 받아서 옵션 리스트를 보낸다.
// 작 성 자		: 2016.02.04 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$productcode	= $_REQUEST['productcode'];	// 상품코드
	$option_code	= $_REQUEST['option_code']; // 옵션코드
	$option_depth	= $_REQUEST['option_depth']; // 옵션 차수
	if ($option_depth == '') $option_depth = 0;
	//exdebug( get_option( $productcode, $option_code, $option_depth ));
	$option_arr	="";
	if ($productcode) {
		$option_arr	= get_option( $productcode, $option_code, $option_depth );
	}
	//exdebug($option_arr);
	echo urldecode(json_encode($option_arr));
?>