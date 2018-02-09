<?php 
session_start();
/**
* 
* 기존에 회원가입이 되어 있는지 안되어 있는지 체크하는 페이지
* member_agree.php의 ifram에서 실행하는 페이지
* 본래 member_join.php에서 체크해야 하지만 세이브힐즈의 경우 member_agree.php에서 
* 가입 결과를 레이어 팝업으로 띄워야 하기에 여기에서 체크한 다음 그 결과로 
* ipin_chk()를 호출함
* 
* 
* 
* 아이핀 인증의 경우 가입 확인 여부를 확인할 수 있지만 
* 핸드폰 인증의 경우 가입 여부를 확인 할 수 없다.
* 따라서 핸드폰 인증의 경우 본인 인증이 되면
* 무조건 회원가입 폼으로 넘어간다.
* 
*/


$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

#####실명인증 결과에 따른 분기
$CertificationData = pmysql_fetch_object(pmysql_query("select realname_id, realname_password, realname_check, realname_adult_check, ipin_id, ipin_password, ipin_check, ipin_adult_check from tblshopinfo"));

if($CertificationData->realname_check || $CertificationData->ipin_check){
	if($_SESSION[ipin][dupinfo]){
		#####아이핀 인증의 경우
		$check_ipin=pmysql_fetch_object(pmysql_query("select count(id) as check_id from tblmember where dupinfo='{$_SESSION[ipin][dupinfo]}'"));
		$check_ipin_data = pmysql_fetch_object(pmysql_query("select id,name from tblmember where dupinfo='{$_SESSION[ipin][dupinfo]}'"));
		$check_full_id = $check_ipin_data->id;
		$check_ipin_data->id = substr($check_ipin_data->id,0,-4)."****";
		if($check_ipin->check_id){
?>
			<script>
				parent.certi_return('0','<?=$check_ipin_data->name?>','<?=$check_ipin_data->id?>','<?=$check_full_id ?>');
			</script>
<?php
		}else{
?>
			<script>
				parent.certi_return('1','','');
			</script>
<?php
			
		}
	}else if($_SESSION[ipin][name]){
		#####핸드폰 인증의 경우
?>
			<script>
				parent.certi_return('1','','');
			</script>
<?php		
	}
}

?>