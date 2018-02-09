<?php
include_once('outline/header_m.php');


if(strlen($_MShopInfo->getMemid())==0) {
	echo ("<script>location.replace('/m/login.php?chUrl=".getUrl()."');</script>");
	exit;
}

$sql = "SELECT  pid, regdt, body, point, use_point, expire_date, tot_point 
                FROM    tblpoint_staff 
                WHERE   mem_id = '".$_MShopInfo->getMemid()."' 
                ORDER BY pid DESC
            ";

//echo $sql;

$paging = new New_Templet_mobile_paging($sql, 3,  5, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
?>
<script>
<!--
function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
-->
</script>

	<div class="sub-title">
		<h2>임직원 마일리지</h2>
		<a class="btn-prev" href="mypage.php"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
	</div>

	<div class="mileage-list-wrap">
		<div class="my-mileage-info">
			<div class="user" style="min-height:30px;">
				<strong class="name"><?=$_mdata->name?><span>(<?=$_mdata->id?>)</span> 님</strong>
				<a href="/m/mypage_benefit.php" class="btn-benefit">등급별 혜택</a>
			</div>
			<div class="mileage-info">
				<div style="padding: 4px 8px 4px 8px; border-bottom:0px solid #838383;">사용가능한 마일리지<strong><?=number_format($_mdata->staff_reserve)?> <span>M</span></strong></div>
			</div>
		</div>
		<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">
		<ul class="mileage-list">
<?
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			$regdt = substr($row->regdt,0,4).".".substr($row->regdt,4,2).".".substr($row->regdt,6,2);
            $expire_date = substr($row->expire_date,0,4).".".substr($row->expire_date,4,2).".".substr($row->expire_date,6,2);
?>
			<li>
				<p class="date"><?=$regdt?></p>
				<p class="type"><?=$row->body?></p>
				<p class="mileage"><?=number_format( $row->point )?></p>
			</li>
<?
		
			$i++;
		}
		pmysql_free_result($result);
?>			
		</ul>
		</form>

		<div class="paginate">
			<div class="box">
				<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
			</div>
		</div>
		
		<dl class="attention margin"><!-- 기본 안내사항 -->
			<dt>유의사항</dt>
			<dd>쇼핑몰에서 발행한 종이쿠폰/시리얼쿠폰/모바일쿠폰 등의 인증번호를 등록하시면 온라인쿠폰으로 발급되어 사용이 가능합니다.</dd>
			<dd>쿠폰은 주문 시 1회에 한해 적용되며, 1회 사용시 재 사용이 불가능합니다.</dd>
			<dd>쿠폰은 적용 가능한 상품이 따로 적용되어 있는 경우 상품 구매 시에만 사용이 가능합니다.</dd>
			<dd>특정한 종이쿠폰/시리얼쿠폰/모바일쿠폰의 경우 단 1회만 사용이 가능할 수 있습니다.</dd>
		</dl>

	</div><!-- //.mileage-list -->

<? include_once('outline/footer_m.php'); ?>
