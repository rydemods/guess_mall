<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$member_sql="select * from tblmembergroup where group_code='".$_ShopInfo->memgroup."'";
$member_result=pmysql_query($member_sql);
$member_row=pmysql_fetch_array($member_result);

//print_r($_SERVER);

$cut_url=reset(explode(".",end(explode("/",$_SERVER[PHP_SELF]))));
$checked[$cut_url]="class='on'";

?>
<!-- LNB -->
	<div class="left_lnb">

		<div class="lnb_wrap">
			<div class="lnb">
				<h1>마이페이지</h1>
				<ul>
					<li><a <?=$checked[mypage]?> href="<?=$Dir.FrontDir?>mypage.php">마이페이지</a></li>
					<li><a <?=$checked[mypage_orderlist]?> href="<?=$Dir.FrontDir?>mypage_orderlist.php">주문내역</a></li>
					<li><a <?=$checked[mypage_personal]?> href="<?=$Dir.FrontDir?>mypage_personal.php">1:1문의</a></li>
					<li><a <?=$checked[wishlist]?> href="<?=$Dir.FrontDir?>wishlist.php">WishList</a></li>
					<li><a <?=$checked[mypage_reserve]?> href="<?=$Dir.FrontDir?>mypage_reserve.php">적립금</a></li>
					<li><a <?=$checked[mypage_coupon]?> href="<?=$Dir.FrontDir?>mypage_coupon.php">쿠폰내역</a></li>
					<? if(getVenderUsed()) { ?>
					<li><a <?=$checked[mypage_custsect]?> href="<?=$Dir.FrontDir?>mypage_custsect.php">단골매장</a></li>
					<? } ?>
					<li><a <?=$checked[mypage_usermodify]?> href="<?=$Dir.FrontDir?>mypage_usermodify.php">회원정보</a></li>
					<li><a <?=$checked[mypage_memberout]?> href="<?=$Dir.FrontDir?>mypage_memberout.php">회원탈퇴</a></li>
					<li><a <?=$checked[board]?> href="../../board/board.php?board=qna&mypageid=1">상품문의</a></li>
				</ul>
			</div>
		</div>
	</div>
	