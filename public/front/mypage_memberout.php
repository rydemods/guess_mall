
<!--@@ 이전소스 백업 @@ -->

<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if($_POST[type] == 'exit'){
	## 회원 탈퇴 처리 파일 ##
	## /public/lib/loginprocess.php ##
	## POST값을 물고 갈수 없어 temp에입력하고 나중에 탈퇴 테이블 입력시 temp 테이블 데이터 삭제 ##
	//$_POST[out_reason_content]	= $_POST[out_reason]=='6'?$_POST[out_reason_content]:'';
	//$sqlTemp = "INSERT INTO tblmemberout_temp (id, out_reason, out_reason_content) VALUES ('".$_ShopInfo->getMemid()."', '".$_POST[out_reason]."', '".$_POST[out_reason_content]."')";
	//pmysql_query($sqlTemp, get_db_conn());
}

include_once($Dir."lib/shopdata.php");

$memoutinfo	= $_GET['memoutinfo'];

if($memoutinfo == '') {
	$sql = "SELECT a.*, b.group_name FROM tblmember a left join tblmembergroup b on a.group_code = b.group_code WHERE a.id='".$_ShopInfo->getMemid()."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if($row->member_out=="Y") {
			$_ShopInfo->SetMemNULL();
			$_ShopInfo->Save();
			alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");
		}

		if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
			$_ShopInfo->SetMemNULL();
			$_ShopInfo->Save();
			alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");
		}

		$_mdata=$row;

		// 사용가능 쿠폰수
		$cdate = date("YmdH");
		//상품할인
		list($t_product_sale_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) as cnt from tblcouponissue tco LEFT JOIN tblcouponinfo tci ON tco.coupon_code = tci.coupon_code
		WHERE tco.id='".$_ShopInfo->getMemid()."' AND tco.used='N' AND (tco.date_end>='{$cdate}' OR tco.date_end='') AND tci.coupon_use_type !='1'"));

		//쿠폰할인
		list($t_coupon_sale_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) as cnt from tblcouponissue tco LEFT JOIN tblcouponinfo tci ON tco.coupon_code = tci.coupon_code
		WHERE tco.id='".$_ShopInfo->getMemid()."' AND tco.used='N' AND (tco.date_end>='{$cdate}' OR tco.date_end='') AND tci.coupon_use_type ='1'"));

		//진행중인 주문건
		list($t_order_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(a.*) t_order_cnt from tblorderinfo a join tblorderproduct b on a.ordercode=b.ordercode WHERE id='".$_ShopInfo->getMemid()."' 
AND b.op_step >= 40 AND b.op_step < 44 and b.order_conf = '0'"));
	}
	pmysql_free_result($result);

	if(strlen($_ShopInfo->getMemid())==0) {
		Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
		exit;
	}

	if($_data->memberout_type=="N") {
		alert_go("회원탈퇴를 하실 수 없습니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.",-1);
	}
}
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<?
if ($_POST['my_passwd_check'] != 'Y') { // 비밀번호 확인페이지를 확인 안한 경우
	if($memoutinfo=='') {
		$menu_title_text	= "회원탈퇴신청";
		$menu_title_val	= "out";
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td align="">
<?include ($Dir.TempletDir."mypage/mypage_mbpasscheck{$_data->design_mbmodify}.php");?>
	</td>
</tr>
</table>

<?
	} else {
		$menu_title_text	= "회원탈퇴완료";
		$memoutinfo_exp	= explode("|", decrypt_md5($memoutinfo));
?>
<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">회원탈퇴</h2>

		<div class="inner-align page-frm clear">

			<!-- LNB -->
			<?php
			 include ($Dir.FrontDir."mypage_TEM01_left.php");
			?>
			<!-- //LNB -->
			<article class="my-content">
				
				<div class="gray-box out-end">
					<div class="mb-20"><img src="../sinwon/web/static/img/icon/icon_confirm.png" alt="완료"></div>
					<p class="fw-bold fz-20">회원탈퇴 완료</p>
					<p class="end-comment">그동안 신원몰 서비스를 이용해 주셔서 감사합니다. <br>더나은 서비스로 찾아뵙겠습니다.</p>
					<a href="/" class="btn-point h-large mt-25">메인으로 이동</a>
				</div>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->
<?
	}
} else {
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
	function CheckForm(auth_yn) {
		if (document.form1.out_reason.value == '')
		{
			alert("해지사유를 선택해 주세요.");
		} else {
			if (document.form1.t_order_cnt.value == 0)
			{
				if($("input[name=agree_check]").prop("checked")) {
					if (auth_yn =='Y') {
						document.getElementById("ifrmHidden").src="./checkplus/checkplus_main.php";
						//document.getElementById("ifrmHidden").src="./checkplus/checkplus_main_test.php"; // 테스트용
					} else {
						document.form1.type.value="exit";
						document.form1.submit();
					}
				} else {
					alert("데이터 삭제 관련 내용에 동의해주세요.");
					if(document.form_agree.agree_check) {
						document.getElementById("agree_check").focus();
					}
				}
			} else {
				alert("진행중인 주문이 완료 되어야 탈퇴처리 가능하십니다.");
			}
		}
	}

	function ipin_chk(){
		document.getElementById("ifrmHidden").src="./member_chkid.php";
	}


	function certi_return(rt_yn, rt_name, rt_id, full_id){
		if(rt_yn=='0'){
			if (document.form1.mem_id.value == full_id) {
				document.form1.type.value="exit";
				document.form1.submit();
			} else {
				alert("회원정보와 인증하신 정보가 일치하지 않습니다.");
			}
		}else{
			alert("회원정보와 인증하신 정보가 일치하지 않습니다.");
		}
	}
//-->
</SCRIPT>

<div id="contents">
	<div class="inner">
		<main class="memberout_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=t_order_cnt value="<?=$t_order_cnt?>">
			<input type=hidden name=mem_id value="<?=$_ShopInfo->getMemid()?>">
			<input type=hidden name=memoutinfo value="<?=encrypt_md5(($t_product_sale_cnt+$t_coupon_sale_cnt)."|".$_mdata->act_point."|".$_mdata->reserve."|".$_mdata->name."|".$_ShopInfo->getMemid())?>">
			<section class="mypage_content">
				<div class="title_type1">
					<h2>HOT<span class="type_txt1">-T</span> 서비스 해지 신청</h2>
				</div>
				<div class="memberout_box">
					<div class="text_area">
						<p><?=$_mdata->name?>님의 <em>진행중인 주문 <?=number_format($t_order_cnt)?>건</em>이 있습니다</p>
						<p>진행중 주문이 완료되어야 탈퇴 처리가 가능합니다</p>
					</div>
					<ul class="summary_benefit">
						<li class="on">소멸예정 내역</li>
						<li>쿠폰<em><?=number_format($t_product_sale_cnt + $t_coupon_sale_cnt)?></em>건</li>
						<li>Action 포인트<em><?=number_format($_mdata->act_point)?></em>점</li>
						<li class="hide">적립금<em><?=number_format($_mdata->reserve)?></em>점</li>
					</ul>
				</div>
				<div class="form_box">
					<p>서비스를 해지하시겠습니까?</p>
					<p>해지 사유를 작성해 주시면 회원님의 의견을 반영하여 더욱 좋은 서비스를 만들어가도록 노력하겠습니다</p>
					<div class="radio_box">
					<?foreach($arrMemberOutReason as $k => $v){?>
						<p>
							<input type="radio" name="out_reason" id="view<?=$k?>" class="radio-def" value="<?=$k?>"<?=$k=='1'?' checked':'';?>>
							<label for="view<?=$k?>"><?=$v?></label>
							<?if ($k == '6'){?>
							<input type="text" name="out_reason_content" id="content-write" style="width:240px;">
							<?}?>
						</p>
					<?}?>
					</div>
					<div class="finish_chk">
						<input id="agree_check" name="agree_check" type="checkbox" class="chk_agree checkbox-def" >
						<label for="agree_check"> <?=$_mdata->name?>님의 회원기간동안 활동했던 데이터를 모두 삭제하는데 동의합니다</label>
					</div>
					<?
					if (strlen($_mdata->dupinfo) > 0 && $_mdata->auth_type == 'mobile') { // 본인인증 회원일 경우
						$auth_yn	= "Y";
						$auth_txt	= "본인인증";
					} else { // 인증을 안한 회원일 경우
						$auth_yn	= "N";
						$auth_txt	= "확인";
					}
					?>
					<div class="btn_wrap">
						<a href="javascript:CheckForm('<?=$auth_yn?>');" class="btn-type1"><?=$auth_txt?></a>
					</div>
				</div>
			</section>
			</form>
		</main>
	</div>
</div><!-- //#contents -->
<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width=1000 height=1000></iframe></div>
<?}?>

<?=$onload?>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
