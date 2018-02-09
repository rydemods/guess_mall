<?
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
$out_access_type	= "mobile";
include_once('outline/header_m.php');

$type = $_POST['type'];
if( $type == 'exit'){
	//$out_reson = $_POST['out_reason'];
	//$out_reason_content = $_POST['out_reason_content'];
	## 회원 탈퇴 처리 파일 ##
	## /public/lib/loginprocess.php ##
	## POST값을 물고 갈수 없어 temp에입력하고 나중에 탈퇴 테이블 입력시 temp 테이블 데이터 삭제 ##
	//$sqlTemp = "INSERT INTO tblmemberout_temp (id, out_reason, out_reason_content) VALUES ('".$_ShopInfo->getMemid()."', '".$out_reson."', '".$out_reason_content."')";
	//pmysql_query($sqlTemp, get_db_conn());
}

include_once($Dir."lib/shopdata.php");

$memoutinfo	= $_GET['memoutinfo'];

if($memoutinfo == '') {

	$sql = "SELECT a.*, b.group_name FROM tblmember a left join tblmembergroup b on a.group_code = b.group_code WHERE a.id='".$_MShopInfo->getMemid()."' ";
	//echo $sql;
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if($row->member_out=="Y") {
			$_MShopInfo->SetMemNULL();
			$_MShopInfo->Save();
			alert_go('회원 아이디가 존재하지 않습니다.',$Dir.MDir."login.php");
		}

		if($row->authidkey!=$_MShopInfo->getAuthidkey()) {
			$_MShopInfo->SetMemNULL();
			$_MShopInfo->Save();
			alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.MDir."login.php");
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

	if(strlen($_MShopInfo->getMemid())==0) {
		Header("Location:".$Dir.MDir."login.php?chUrl=".getUrl());
		exit;
	}

	if($_data->memberout_type=="N") {
		alert_go("회원탈퇴를 하실 수 없습니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.",-1);
	}
}
?>

<?
$my_passwd_check_arr	= explode("|", decrypt_md5($_GET['my_passwd_check']));
if ($my_passwd_check_arr[0] == $_MShopInfo->getMemid() && $my_passwd_check_arr[1] == 'Y') { // 비밀번호 확인페이지를 확인한 경우
?>
		<script>
			function CheckForm(auth_yn) {
				if (document.form1.out_reason.value == '')
				{
					alert("해지사유를 선택해 주세요.");
				} else {
					if (document.form1.t_order_cnt.value == 0)
					{
						if($("input[name=agree_check]").prop("checked")) {
							if (auth_yn =='Y') {
								var out_user_data	= 'exit';
								out_user_data += '!@'+$('input[name=out_reason]:checked').val();
								out_user_data += '!@'+$("input[name=out_reason_content]").val();
								out_user_data += '!@'+$("input[name=memoutinfo]").val();
								$('input[name=out_user_data]').val(out_user_data);
								document.auth_form.action = "./checkplus/checkplus_main.php";
								//document.auth_form.action = "./checkplus/checkplus_main_test.php"; // 테스트용
								document.auth_form.submit();
							} else {
								document.form1.type.value="exit";
								document.form1.submit();
							}
						} else {
							alert("데이터 삭제 관련 내용에 동의해주세요.");
							if(document.form1.agree_check) {
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
		</script>

			<!-- 서비스 해지 작성 -->
			<section class="top_title_wrap">
				<h2 class="page_local">
					<a href="javascript:history.back();" class="prev"></a>
					<span>서비스 해지</span>
				</h2>
			</section>
			<div class="mypage_sub member-out">
				<form id="form1" name='form1' action="<?=$_SERVER['PHP_SELF']?>" method="POST">
				<input type=hidden name=type>
				<input type=hidden name=t_order_cnt value="<?=$t_order_cnt?>">
				<input type=hidden name=mem_id value="<?=$_ShopInfo->getMemid()?>">
				<input type=hidden name=memoutinfo value="<?=encrypt_md5(($t_product_sale_cnt+$t_coupon_sale_cnt)."|".$_mdata->act_point."|".$_mdata->reserve."|".$_mdata->name."|".$_ShopInfo->getMemid())?>">
				<div class="memberout_msg">
					<strong><?=$_mdata->name?>님의 <span class="point-color">진행중인 주문 <?=number_format($t_order_cnt)?>건</span>이 있습니다.</strong><br>
					진행중인 주문이 완료 되어야 탈퇴처리 가능하십니다.
				</div>

				<div class="my-benfit">
					<p class="tit">소멸예정 내역</p>
					<ul class="now clear">
						<li>쿠폰<br><strong class="point-color"><?=number_format($t_product_sale_cnt+$t_coupon_sale_cnt)?></strong>건</li>
						<li>Action 포인트<br><strong class="point-color"><?=number_format($_mdata->act_point)?></strong>점</li>
						<!--<li>적립금<br><strong class="point-color"><?=number_format($_mdata->reserve)?></strong>점</li>-->
					</ul>
				</div>

				<div class="box_memberout">
					<p class="tit">서비스를 해지하시겠습니까?</p>
					<p class="txt">해지 사유를 작성해 주시면 회원님의 의견을 반영하여<br> 더욱 좋은 서비스를 만들어가도록 노력하겠습니다.</p>
					<ul>
					<?foreach($arrMemberOutReason as $k => $v){?>
						<li><label>
							<input type="radio" name="out_reason" class="custom_radio" value="<?=$k?>"<?=$k=='1'?' checked':'';?>><?=$v?>
							<?if ($k == '6'){?>
							<input type="text" name="out_reason_content" id="content-write" >
							<?}?>
						</label></li>
					<?}?>
					</ul>
				</div>

				<div class="agree_out">
					<input type="checkbox" id="agree_check" name="agree_check">
					<label for="agree_check"><?=$_mdata->name?>님의 회원기간동안 활동했던 데이터를 모두 삭제하는데 동의합니다.</label>
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
				<a href="javascript:CheckForm('<?=$auth_yn?>')" class="btn-point"><?=$auth_txt?></a>
				</form>
				<form method="GET" id="auth_form" name="auth_form">
					<input type=hidden name=out_user_data>
				</form>

			</div><!-- //.mypage_sub.member-out -->
			<!-- //서비스 해지 작성 -->
<?
} else {
	if($memoutinfo=='') {
?>

<SCRIPT LANGUAGE="JavaScript">
<!--


function CheckForm() {

	form=document.form1;

	//기존 비밀번호 유효성 체크
	var val	= $("input[name=oldpasswd]").val();
	var val2	= $("input[name=oldpasswdre]").val();
	if (val == '' || val2 =='') {
		if (val == '') {
			alert($("input[name=oldpasswd]").attr("title"));
			$("input[name=oldpasswd]").focus();
			return;
		} else if (val2 == '') {
			alert($("input[name=oldpasswdre]").attr("title"));
			return;
		}
	} else {
		if (val != val2) {
			alert("비밀번호가 일치하지 않습니다.");	
			return;
		} else {
			$.ajax({
				type: "GET",
				url: "<?=$Dir.FrontDir?>iddup.proc.php",
				data: "passwd=" + val + "&mode=passwd",
				dataType:"json",
				success: function(data) {
					if (data.code == 0) {
						alert(data.msg);
						return;
					} else if (data.code == '1') {
						if (document.form1.t_order_cnt.value == 0)
						{	
							if(confirm("회원탈퇴를 신청하시겠습니까?")) {
								document.form1.type.value="exit";
								document.form1.submit();
							}
							return;
						} else {
							alert("진행중인 주문이 완료 되어야 탈퇴처리 가능하십니다.");
							return;
						}
					} else {
						return;
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다.");
				}
			});
		}
	}
}
//-->
</SCRIPT>
<!-- 내용 -->
<main id="content" class="subpage with_bg">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>회원탈퇴</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="my_withdrawal sub_bdtop">
	<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<input type=hidden name=type>
	<input type=hidden name=t_order_cnt value="<?=$t_order_cnt?>">
	<input type=hidden name=mem_id value="<?=$_ShopInfo->getMemid()?>">
	<input type=hidden name=memoutinfo value="<?=encrypt_md5(($t_product_sale_cnt+$t_coupon_sale_cnt)."|".$_mdata->act_point."|".$_mdata->reserve."|".$_mdata->name."|".$_ShopInfo->getMemid())?>">
		<div class="attn">
			<p class="tit">신원몰 회원탈퇴 유의사항</p>
			<ul class="mt-5">
				<li>- 회원탈퇴시 적립된 포인트 및 쿠폰정보는 모두 소멸됩니다.</li>
				<li>- 회원탈퇴시 오프라인 전용 쿠폰 및 마일리지 역시 함께 삭제처리 됩니다</li>
				<li>- 동일 아이디로 재가입이 불가능합니다.</li>
			</ul>
		</div>

		<div class="my_modify_pw">
			<p>회원탈퇴를 위해 비밀번호를 입력해주세요.</p>
			<input type="password" class="w100-per mt-25" id="pwd" name="oldpasswd" title="비밀번호를 입력해 주시기 바랍니다." placeholder="비밀번호 입력">
			<input type="password" class="w100-per mt-5" id="pwdre" name="oldpasswdre" title="비밀번호를 재 입력해 주시기 바랍니다." placeholder="비밀번호 재입력">
			<div class="btn_area mt-15">
				<ul>
					<li><a href="javascript:CheckForm();" class="btn-point h-input">확인</a></li>
				</ul>
			</div>
		</div>
	</form>
	</section><!-- //.my_withdrawal -->

</main>
<!-- //내용 -->
<?
	} else {
		$memoutinfo_exp	= explode("|", decrypt_md5($memoutinfo));
?>

<!-- 내용 -->
<main id="content" class="subpage with_bg">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>회원탈퇴</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="my_withdrawal sub_bdtop">
		<div class="end_msg">
			<h3>회원탈퇴 완료</h3>
			<p class="mt-10">그동안 신원몰 서비스를 이용해 주셔서 감사합니다.<br>더나은 서비스로 찾아뵙겠습니다.</p>
			<div class="btn_area">
				<ul>
					<li><a href="/m/" class="btn-point h-input">메인으로 이동</a></li>
				</ul>
			</div>
		</div>

	</section><!-- //.my_withdrawal -->

</main>
<!-- //내용 -->

<script type='text/javascript'>  
var m_jn = 'withdraw'; 
var m_jid= '<?=$mem_id?>';
</script> 

<?
	}
}
?>
<? include_once('outline/footer_m.php'); ?>