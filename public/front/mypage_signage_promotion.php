<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$mode=$_POST["mode"];
$no=$_POST["no"];

if($mode=="ins" && $no){

	$update_qry="update tblsignage_promotion_member set check_yn='Y' where member_id='".$_ShopInfo->memid."' and no='".$no."'";

	pmysql_query($update_qry);
}

$signage_sql="select * from tblsignage_promotion_member where member_id='".$_ShopInfo->memid."' order by regdt desc, no desc";

# 페이징
$paging = new New_Templet_paging($signage_sql, 10,  10, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$signage_sql = $paging->getSql( $signage_sql );
$signage_result = pmysql_query( $signage_sql, get_db_conn() );



list($coupon_no)=pmysql_fetch("select couponcode from tblsignage_couponlist where id='".$_ShopInfo->memid."' order by index desc limit 1");


$signage_imagepath      = $cfg_img_path['signage_event'];


?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<script Language="JavaScript">
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}

function check_on(no){
	if(confirm('사용 하시겠습니까?')){
		$("#mode").val("ins");
		$("#no").val(no);
		$("#form1").submit();
	}
}

</script>
<form name=form1 id=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type="hidden" name="mode" id="mode">
<input type="hidden" name="no" id="no">
<input type=hidden name=block>
<input type=hidden name=gotopage>
<div id="contents">
	 <!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="<?=$Dir?>front/mypage.php">마이 페이지</a></li>
			<li class="on">오프라인 프로모션 당첨내역</li>
		</ul>
	</div>
	<!-- //네비게이션-->
	<div class="inner">
		<main class="mypage_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->

			<article class="mypage_content">
				<section class="mypage_main">
					<div class="title_type1">
						
						<h3>오프라인 프로모션 당첨내역</h3>

						<h3 class="subtit">회원가입 10% 할인쿠폰 : <?=$coupon_no?></h3>
					</div>

					<!-- 배송지 변경 -->
					<div class="mypage_address">
						<table class="th_top">
							<caption>오프라인 프로모션 당첨내역</caption>
							<colgroup>
								<col style="width:7%">
								<col style="width:12%">
								<col style="width:12%">
								<col style="width:auto">
								<col style="width:12%">
								<col style="width:10%">
							</colgroup>
							<thead>

								<tr>
									<th scope="col">NO.</th>
									<th scope="col">프로모션</th>
									<th scope="col">당첨이미지</th>
									<th scope="col">당첨명</th>
									<th scope="col">당첨일</th>
									<th scope="col">확인</th>
								</tr>

							</thead>
							<tbody>
							
						<?if($t_count){
							$cnt=0;
							?>
							<?while($signage_data=pmysql_fetch_array($signage_result)){
								$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt++ );
								list($promotion_title)=pmysql_fetch("select mtitle from tblsignage_promotion where no='".$signage_data['promotion_no']."'");

								?>
								<tr>
									<td><?=$number?></td>
									<td><?=$promotion_title?></td>
									<td><img src="<?=$signage_imagepath.$signage_data['promotion_img']?>" alt="HOT:T" width="200px"></td>
									<td class="ta-l" style="padding-left:30px;"><?=$signage_data["event_name"]?></td>
									<td><?=$signage_data["regdt"]?></td>
									<td>
										<?if($signage_data[check_yn]=="N"){?>
										<button type="button" class="btn-line" onClick="check_on('<?=$signage_data['no']?>')"><span>확인</span></button>
										<?}else{?>
										-
										<?}?>
									</td>
								</tr>
							<?} ?>
						<?}else{ ?>
							<tr>
								<td colspan="6">당첨내역이 없습니다.</td>
							</tr>
						<?} ?>
							
							</tbody>
						</table>
						<div class="list-paginate mt-20">
							<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
<!-- 							
						</div>
					</div>
					<!-- // 배송지 변경 -->
				</section>
			</article>
		</main>
	</div>
</div><!-- //#contents -->
</form>

<?php  include ($Dir."lib/bottom.php") ?>

