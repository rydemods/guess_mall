<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


if(strlen($_ShopInfo->getMemid())>0) {
	header("Location:../index.php");
	exit;
}


$certiSrc = "member_agree.php";
/*세이브 힐즈는 약관 동의와 인증선택이 한페이지에 있기 때문에 다음을 지움
$CertificationData = pmysql_fetch_object(pmysql_query("select realname_id, realname_password, realname_check, realname_adult_check, ipin_id, ipin_password, ipin_check, ipin_adult_check from tblshopinfo"));
if($CertificationData->realname_check || $CertificationData->ipin_check){
	$certiSrc = "member_certi.php";
}else{
	$certiSrc = "member_agree.php";
}
*/

if(!$_data->shop_mem_type){
	header("Location:".$certiSrc."?jointype=0");
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 회원가입</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>


<script>
function join_next(link){

	jtype = $("input[name='jointype']:checked").val();
	if(jtype){
		document.location.href=link+"?mem_type="+jtype;
	}else{
		alert("회원구분을 선택해주세요.");
	}
	
}

function join_next_tem01(link,val){
	document.location.href=link+"?mem_type="+val;
}

</script>

</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<!-- header 끝-->



<!--div><a href="<?=$certiSrc?>">인증페이지 이동</a></div-->

<?if ($_data->icon_type=="tem_001"){?>

<!-- 상세페이지 -->
<div class="main_wrap">
	<div class="container">
		
		<h1 class="sub_title">회원가입</h1>
		<h2 class="sub_title">회원가입을 하시면 다양한 혜택과 이벤트에 참여 하실 수 있습니다.</h2>
		<div class="member_join_wrap">
			<p class="visual_img"><img src="../../images/tem_001/join_type_img.jpg" alt="" /></p>
			<div class="join_type">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed">
					<tr valign=top>
						<td align=center>
							<div class="join_box">
								<h2>일반회원 가입</h2>
								<h3>만 14세 이상 일반인</h3>
								<a href="javascript:join_next_tem01('<?=$certiSrc?>','0');" class="btn_buy w300">일반회원 가입</a>
							</div>
						</td>
						<td align=center>
							<div class="join_box">
								<h2>기업회원 가입</h2>
								<h3>사업자등록이 되어있는 기업회원</h3>
								<a href="javascript:join_next_tem01('<?=$certiSrc?>','1');" class="btn_buy w300">기업회원 가입</a>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

	</div>
</div>
<?}else{?>

<div class="w690">

	<h1 class="type01"><img src="../images/001/h1_member_join.gif" alt="회원가입" /></h1>
	<div class="table_style">
		<h2>※회원구분 선택</h2>
		<form>
		<table width=100% cellpadding=0 cellspacing=0 border=0 >
			<colgroup>
				<col width="20%" /><col width="" />
			</colgroup>
			<tr>
				<th>회원구분</th>
				<td>
					<input type="radio" name="jointype" id="jointype1" value="0" checked/>
					<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="jointype1">일반회원 가입</LABEL>
					<br/> 
					<input type="radio" name="jointype" id="jointype2" value="1"/>
					<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="jointype2">기업회원 가입</LABEL>
				</td>
			</tr>
		</table>
		</form>
		<p class="btn_c">
			<a href="javascript:join_next('<?=$certiSrc?>');"><img src="../images/001/btn_join01.gif" alt="회원가입하기" /></a> &nbsp; <a href="../index.php"><img src="../images/001/btn_back.gif" alt="돌아가기" /></a>
		</p>
	</div>


</div>
<?}?>
<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
