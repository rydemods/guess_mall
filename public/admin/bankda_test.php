<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");
	/*
		02-3663-3247 (23)  최필웅 팀장
		문의
			- 계좌 등록 했는데 거래내역 수집중 상태유지.
			- 계좌 즉시조회결과 확인 했더니 error:즉시조회 작업중

		•URL : https://ssl.bankda.com/partnership/user/renovation_ck.php
			◦parameter1 : directAccess - 직접요청임을 표시. 값은 y로 고정
			◦parameter2 : service_type - 서비스유형 (standard or basic or entry)
			◦parameter3 : partner_id - 제휴사ID
			◦parameter4 : user_id - 이용자ID
			◦parameter5 : user_pw - 이용자PW
			◦parameter6 : bkacctno - 계좌번호 '-'없이 전송
	*/
	$_POST['directAccess'] = "y";
	$_POST['service_type'] = "standard";
	$_POST['partner_id'] = "duometis";
	$_POST['user_id'] = "deconc";
	$_POST['user_pw'] = "ever0096!";
	$_POST['bkacctno'] = "620194906550";
	$bankdaUrl = "https://ssl.bankda.com/partnership/user/renovation_ck.php?".http_build_query($_POST);
	$rs = file_get_contents($bankdaUrl);

	print_r($rs);


	/*
	•URL : https://ssl.bankda.com/partnership/user/renovation.php
		◦parameter1 : directAccess - 직접요청임을 표시. 값은 y로 고정
		◦parameter2 : service_type - 서비스유형 (standard or basic or entry)
		◦parameter3 : partner_id - 제휴사ID
		◦parameter4 : user_id - 이용자ID
		◦parameter5 : user_pw - 이용자PW
		◦parameter6 : bkacctno - 계좌번호 '-'없이 전송


	$_POST['directAccess'] = "y";
	$_POST['service_type'] = "standard";
	$_POST['partner_id'] = "duometis";
	$_POST['user_id'] = "deconc";
	$_POST['user_pw'] = "ever0096!";
	$_POST['bkacctno'] = "620194906550";
	$bankdaUrl = "https://ssl.bankda.com/partnership/user/renovation.php?".http_build_query($_POST);
	$rs = file_get_contents($bankdaUrl);

	print_r($rs);
	*/
?>