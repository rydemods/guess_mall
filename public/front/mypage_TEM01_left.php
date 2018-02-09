<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$cut_url=reset(explode(".",end(explode("/",$_SERVER[PHP_SELF]))));
$checked[$cut_url]="class='active'";
if ($cut_url == 'mypage_orderlist_view') $checked["mypage_orderlist"] = "class='active'";

list($grp_name, $grp_level)=pmysql_fetch("select group_name, group_level from tblmembergroup where group_code='".$_ShopInfo->memgroup."'");
list($tot_sale)=pmysql_fetch("select sum(price) from tblorderinfo where id='".$_ShopInfo->memid."' AND deli_gbn = 'Y'");
list($reserve)=pmysql_fetch("select reserve from tblmember where id='".$_ShopInfo->memid."' ");
list($act_point)=pmysql_fetch("select act_point from tblmember where id='".$_ShopInfo->memid."' ");
list($sumsale)=pmysql_fetch("select sumprice from tblmember where id='".$_ShopInfo->memid."' ");
list($cnt_coupon)=pmysql_fetch("select count(*) from tblcouponissue where id='".$_ShopInfo->memid."' AND used != 'Y'");
$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
?>
<div class="my-lnb">
	<h3 class="lnb-title">마이페이지</h3>
	<dl>
		<dt>쇼핑내역</dt>
		<dd><a href="<?=$Dir.FrontDir?>basket.php" <?=$checked["basket"]?>>장바구니</a></dd> <!-- [D] 해당 페이지에서 a태그에 active 클래스추가 -->
		<dd><a href="<?=$Dir.FrontDir?>mypage_orderlist.php" <?=$checked["mypage_orderlist"]?>>주문/배송조회</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>mypage_cancellist.php" <?=$checked["mypage_cancellist"]?>>취소/교환/반품 신청</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>mypage_cancellistnow.php" <?=$checked["mypage_cancellistnow"]?>>취소/교환/반품 현황</a></dd>
	</dl>
	<dl>
		<dt>혜택정보</dt>
		<dd><a href="<?=$Dir.FrontDir?>benefit.php" <?=$checked["benefit"]?>>회원등급 및 혜택</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>mypage_act_point.php" <?=$checked["mypage_act_point"]?>>포인트</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>mypage_coupon.php" <?=$checked["mypage_coupon"]?>>쿠폰</a></dd>
	</dl>
	<dl>
		<dt>활동정보</dt>
		<dd><a href="<?=$Dir.FrontDir?>mypage_good.php" <?=$checked["mypage_good"]?>>좋아요</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>mypage_event.php" <?=$checked["mypage_event"]?>>이벤트 참여현황</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>mypage_review.php?type=list" <?=$checked["mypage_review"]?>>상품리뷰</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>mypage_qna.php" <?=$checked["mypage_qna"]?>>상품문의</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>mypage_personal.php" <?=$checked["mypage_personal"]?><?=$checked["mypage_personalwrite"]?><?=$checked["mypage_personalview"]?>>1:1문의</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>mypage_as.php" <?=$checked["mypage_as"]?>>AS접수</a></dd>
	</dl>
	<dl>
		<dt>회원정보</dt>
		<dd><a href="<?=$Dir.FrontDir?>mypage_usermodify.php" <?=$checked["mypage_usermodify"]?>>회원정보 수정</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>address_change.php" <?=$checked["address_change"]?>>배송지 관리</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>refund_account.php" <?=$checked["refund_account"]?>>환불계좌 관리</a></dd>
		<dd><a href="<?=$Dir.FrontDir?>mypage_memberout.php" <?=$checked["mypage_memberout"]?>>회원탈퇴</a></dd>
	</dl>
</div>

		<!-- lnb_mypage -->
		<aside class="lnb_mypage hide">
			<a href="<?=$Dir.FrontDir?>mypage.php"><h2>나의 핫티</h2></a>			
			<nav>
				<ul class="menu_list">
				<?if ($mem_auth_type != 'sns') {?>
					<li>
						<a href="<?=$Dir.FrontDir?>mypage_orderlist.php">쇼핑내역</a>
						<ul class="s_menu">
							<li><a <?=$checked["mypage_orderlist"]?> href="<?=$Dir.FrontDir?>mypage_orderlist.php">주문/배송</a></li>
							<li><a <?=$checked["mypage_cancellist"]?> href="<?=$Dir.FrontDir?>mypage_cancellist.php">취소/반품/교환</a></li>
							<li><a <?=$checked["mypage_receipt"]?> href="<?=$Dir.FrontDir?>mypage_receipt.php">증명서류 발급</a></li>
							<li><a <?=$checked["mypage_shoeshelf"]?> href="<?=$Dir.FrontDir?>mypage_shoeshelf.php">신발장</a></li>
						</ul>
					</li>
				<?}?>
					<li>
						<a href="<?=$Dir.FrontDir?>lately_view.php">관심내역</a>
						<ul class="s_menu">
							<li><a <?=$checked["lately_view"]?> href="<?=$Dir.FrontDir?>lately_view.php">최근 본 상품</a></li>
							<!--li><a href="<?=$Dir.FrontDir?>basket.php">장바구니</a></li-->
							<li><a <?=$checked["mypage_good"]?> href="<?=$Dir.FrontDir?>mypage_good.php">좋아요</a></li>
						</ul>
					</li>
					<li>
						<a href="<?=$Dir.FrontDir?>benefit.php">나의 혜택</a>
						<ul class="s_menu">
							<li><a <?=$checked["benefit"]?> href="<?=$Dir.FrontDir?>benefit.php">등급별 혜택</a></li>
						<?if ($mem_auth_type != 'sns') {?>
							<li><a <?=$checked["mypage_act_point"]?> href="<?=$Dir.FrontDir?>mypage_act_point.php">Action 포인트</a></li>
							<li><a <?=$checked["mypage_coupon"]?> href="<?=$Dir.FrontDir?>mypage_coupon.php">쿠폰</a></li>
						<?}?>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0)">활동내역</a>
						<ul class="s_menu">
						<?if ($mem_auth_type != 'sns') {?>
							<li><a <?=$checked["myforum_list"]?>href="<?=$Dir.FrontDir?>myforum_list.php">나의 포럼</a></li>
							<li><a <?=$checked["mypage_review"]?> href="<?=$Dir.FrontDir?>mypage_review.php?type=list">상품리뷰</a></li>
						<?}?>
							<li><a <?=$checked["mypage_qna"]?> href="<?=$Dir.FrontDir?>mypage_qna.php">상품문의</a></li>
							<li><a <?=$checked["mypage_personal"]?><?=$checked["mypage_personalwrite"]?><?=$checked["mypage_personalview"]?> href="<?=$Dir.FrontDir?>mypage_personal.php">1:1문의</a></li>
						</ul>
					</li>
					<li>
						<a href="<?=$Dir.FrontDir?>mypage_usermodify.php">회원정보</a>
						<ul class="s_menu">
						<?if ($mem_auth_type != 'sns') {?>
							<li><a <?=$checked["mypage_usermodify"]?> href="<?=$Dir.FrontDir?>mypage_usermodify.php">정보수정</a></li>
							<li><a <?=$checked["address_change"]?> href="<?=$Dir.FrontDir?>address_change.php">배송지 관리</a></li>
							<li><a <?=$checked["refund_account"]?> href="<?=$Dir.FrontDir?>refund_account.php">환불계좌</a></li>
						<?} else {?>
							<li><a <?=$checked["member_join"]?> href="<?=$Dir.FrontDir?>member_agree.php">정회원 전환</a></li>
						<?}?>
						</ul>
					</li>
				</ul>
			</nav>
		</aside><!-- // lnb_mypage -->