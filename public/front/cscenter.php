<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$search=$_POST[search];
$faq_type=$_REQUEST[faq_type];


##### 공지사항 
$sql_notice = "SELECT * FROM tblboard where board='notice' ORDER BY writetime DESC LIMIT 4 OFFSET 0";
$res_notice = pmysql_query($sql_notice);
while($row_notice = pmysql_fetch_array($res_notice)){
	$data_notice[]=$row_notice;
}
pmysql_free_result($res_notice);
##### //공지사항

#####FAQ
$sql_faq = "SELECT a.*, b.faq_category_name as faq_type FROM tblfaq a ";
$sql_faq.= "LEFT JOIN tblfaqcategory b ON a.faq_type=b.num ";
$sql_faq.= "WHERE a.faq_best='Y' ORDER BY a.no desc LIMIT 10";
$res_faq = pmysql_query($sql_faq);
while($row_faq = pmysql_fetch_array($res_faq)){
	$data_faq[]=$row_faq;
}
#####//FAQ


##카테고리 쿼리
$cate_qry="select * from tblfaqcategory where secret='1' order by sort_num";
$cate_result=pmysql_query($cate_qry);

#####좌측 메뉴 class='on' 을 위한 페이지코드
$page_code='cs_main';

?>

<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
function goView(num,board) {
	document.form2.action="/board/board.php";
	document.form2.num.value=num;
	document.form2.board.value=board;
	document.form2.submit();
}
function faqOpen(faqnum){
	$('#faq_'+faqnum).toggle();
}
function searchFaq(){
	document.form1.submit();
}
function goNonmember(str){
	if(str != ""){
		alert("비회원 전용 메뉴입니다.");
		return;
	}
	document.form3.mode.value="nonmember";
	document.form3.action="<?=$Dir.FrontDir?>login.php";
	document.form3.submit();
}
//-->
</SCRIPT>


<TITLE><?=$_data->shoptitle?> - 공지사항</TITLE>
<?php  
include ($Dir.MainDir.$_data->menu_type.".php");
?>




<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
 
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td>

	<?
	$subTop_flag = 3;
	//include ($Dir.MainDir."sub_top.php");
	?>
	<div class="containerBody sub_skin">
		
		<!-- 고객센터 LNB -->
		<?	$lnb_flag = 5;
			include ($Dir.MainDir."lnb.php");
		?>
		<!-- // 고객센터 LNB -->

		<div class="right_section">

			<div class="customer_main_wrap">
				<h3 class="title">
					고객센터
					<p class="line_map"><a>홈</a> &gt; <a class="on">고객센터</a></p>
				</h3>
				
				<div class="top_area mt_20">
					<div class="service_area hide">
						<h4 class="none">자주 이용하시는 서비스</h4>
						<ul>
							<li><a href="<?=$Dir.FrontDir?>mypage_orderlist.php" target="_self">주문/배송조회</a></li>
							<li><a href="<?=$Dir.FrontDir?>mypage_cancellist.php" target="_self">취소/교환/반품</a></li>
							<li><a href="<?=$Dir.FrontDir?>mypage_personal.php" target="_self">1:1문의하기</a></li>
							<li><a href="<?=$Dir.FrontDir?>mypage_usermodify.php" target="_self">회원정보 수정</a></li>
							<li><a href="javascript:goNonmember('<?=$_ShopInfo->getMemid()?>');" target="_self">비회원 주문내역</a></li>
							<li><a href="<?=$Dir?>board/board.php?board=global" target="_self"" target="_self">해외배송문의</a></li>
						</ul>
					</div>
					<div class="news_area">
						<h4>News &#38; Notice</h4>
						<ul>
						<?php
							foreach($data_notice as $notice){
								$reg_date = date("Y-m-d",$notice['writetime']);
						?>
							<li>
								<a href="javascript:goView('<?=$notice['num']?>','notice')" target="_self">
									<span class="title"><?=$notice['title']?></span>
									<span class="date"><?=$reg_date?></span>
								</a>
							</li>
						<?php
							}
						?>
						</ul>
						<a class="btn_more" href="/board/board.php?board=notice" target="_self"><img src="<?=$Dir?>img/button/customer_main_notice_more_btn.gif" alt="공지사항 더보기" /></a>
					</div>
					<div class="cs_area">
						<h4>CS CENTER</h4>
						<strong class="phone">070-8290-3187</strong>
						<dl>
							<!-- <dt>상담가능시간</dt> -->
							<dd>월요일~금요일 10:00~18:00<br />점심 : 12:00~13:00 (토, 일, 공휴일은 휴무)</dd>
						</dl>
					</div>
				</div>
				<!-- 중단배너 -->
				<?php 
					#####고객센터 배너
					$cs_banner = $mainBanner['csmain_banner'][1];
				?>
				<!-- <a class="btn_banner" href="<?=$cs_banner['banner_link']?>" target="_self"><img src="<?=$cs_banner['banner_img']?>" alt="아직 멤버쉽에 가입하지 않으셨나요?" /></a> -->

				<div class="faq_ques">
					<span class="ment">자주하는 질문</span>
					<span class="search">
						<div class="search">
							<form name="form1" method="POST" action="<?=$Dir.FrontDir?>csfaq.php">
								<input type="text" name="searchkey" id="" />
							</form>
							<a href="javascript:searchFaq();"></a>
						</div>
					</span>
					<span class="ex">배송, 카드결제, 회원결제</span>
				</div>

				<div class="faq_list">
				
					<div class="faq_list_icon">
					&nbsp;
					</div>
					<div class="faq_list_top10">
						<table class="best_table" summary="자주하는 질문의 글 번호, 분류, 제목을 확인할 수 있습니다.">
							<colgroup>
								<col style="width:20px" />
								<col style="width:auto" />
							</colgroup>
							<tbody>
							<?php
								$cnt_faq = 1;
								foreach($data_faq as $faq){
							?>
								<tr>
									<td><?=$cnt_faq?></td>
									<td class="title"><a href="javascript:faqOpen(<?=$cnt_faq?>)"><?=$faq['faq_title']?></a></td>
								</tr>
								<tr style="display:none" id="<?=faq_.$cnt_faq?>">
									<td colspan="2" class="answer">
										<ul class="faq_answer">
											<li class="ques"><?=$faq['faq_title']?></li>
											<li class="answer"><?=nl2br($faq['faq_content'])?> </li>
										</ul>
									</td>
								</tr>
							<?php
									$cnt_faq++;
								}
								
							?>
							</tbody>
						</table>
					</div>
				</div>

				<ul class="bottom_btn hide">
					<li><a href="#" class="btn01">반품/교환 절차안내</a></li>
					<li><a href="#" class="btn02">멤버쉽 안내</a></li>
					<li><a href="#" class="btn03">AS 안내</a></li>
				</ul>

			</div>
		</div>

	</div>




	</td>
</tr>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="pagetype" value="view">
<input type="hidden" name="listnum" value="<?=$listnum?>">
<input type="hidden" name="sort" value="<?=$sort?>">
<input type="hidden" name="block" value="<?=$block?>">
<input type="hidden" name="gotopage" value="<?=$gotopage?>">
<input type="hidden" name="category_code" value="<?=$category_code?>">
<input type="hidden" name="searchtxt" value="<?=$searchtxt?>">
<input type="hidden" name="tab" value="<?=$tab?>">
<input type="hidden" name="num" value="">
<input type="hidden" name="board" value="">
</form>

<form name=form3 method="POST" action="/board/board.php">
<input type=hidden name=num value="">
<input type=hidden name=mode value="">
</form>
</table>

<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
