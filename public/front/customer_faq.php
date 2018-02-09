<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$search         = $_POST[search];
$faq_type       = $_REQUEST[faq_type];
$search_cate    = $_POST[category];
$page_num       = $_POST[page_num];

#####FAQ
$sql_faq = "SELECT a.*, b.faq_category_name as faq_type FROM tblfaq a ";
$sql_faq.= "LEFT JOIN tblfaqcategory b ON a.faq_type=b.num ";
$sql_faq.= "WHERE 1=1 ";

if($_POST[category]){
	$sql_faq .= "AND a.faq_type = '".$search_cate."' ";
}
$sql_faq.= "AND b.secret = '1' ";
$sql_faq.= "ORDER BY a.sort ASC, a.no ASC "; // (순서 추가로 2016-03-16 김재수 추가)
//exdebug($sql_faq);
if(!$setup["list_num"]) $setup["list_num"] = '10';
if(!$setup["page_num"]) $setup["page_num"] = '5';

$tot_faq = pmysql_query($sql_faq);
/*
$total = pmysql_num_rows($tot_faq);
if($total>=11){
	$pageidx = $_POST["gotopage"] -1;
	if($_POST["gotopage"]==""){
		$pageidx = 0;
	}
	$total=$total-($pageidx*10);
}
*/
$paging = new New_Templet_paging($sql_faq,$setup["page_num"],$setup["list_num"]);
$gotopage = $paging->gotopage;
$t_count = $paging->t_count;

$sql_faq = $paging->getSql($sql_faq);
$res_faq = pmysql_query($sql_faq);

while($row_faq = pmysql_fetch_array($res_faq)){
	$data_faq[]=$row_faq;
}
#####//FAQ

##카테고리 쿼리
$cate_qry="select * from tblfaqcategory where secret='1' order by sort_num";
$cate_result=pmysql_query($cate_qry);
while($row_faq = pmysql_fetch_array($cate_result)){
	$cateData[]=$row_faq;
}
#####좌측 메뉴 class='on' 을 위한 페이지코드
//$page_code='csfaq';
$board = "csfaq";
$class_on['csfaq'] = " class='on'";
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
function faqOpen(faqnum){
	$('#faq_'+faqnum).toggle();
}

function faq_category(cnum){
	document.form1.category.value=cnum;
	if(cnum=="all"){
		document.form1.category.value="";
	}
	document.form1.submit();

}
function seachkey_check(){
	document.form1.category.value="";
	if(document.form1.searchkey.value==""){
		alert("검색어를 입력하세요.");
		return;
	}
	document.form1.submit();
}

//-->
</SCRIPT>

<!-- 20170328 신규퍼블리싱 적용 -->
<div id="contents">
	<div class="cs-page">

		<h2 class="page-title">FAQ</h2>

		<div class="inner-align page-frm clear">
			<?	
				$lnb_flag = 5;
				include ($Dir.MainDir."lnb.php");
			?>
			<article class="cs-content">
				
				<section class="faq-wrap" data-ui="TabMenu">
					<header class="cs-title">
						<h3 class="v-hidden">1:1문의</h3>
					</header>

					<div class="tabs top mb-50"> 
					<!-- 
						<button type="button" data-content="menu" class="active" onclick="javascript:faq_category('all')"><span>전체</span></button>
						<button type="button" data-content="menu" onclick="javascript:faq_category(<?=$faqc[num]?>)"><span>상품관련</span></button>
						<button type="button" data-content="menu" onclick="javascript:faq_category(<?=$faqc[num]?>)"><span>주문/결제</span></button>
						<button type="button" data-content="menu" onclick="javascript:faq_category(<?=$faqc[num]?>)"><span>배송관련</span></button>
						<button type="button" data-content="menu" onclick="javascript:faq_category(<?=$faqc[num]?>)"><span>취소/환불</span></button>
						<button type="button" data-content="menu" onclick="javascript:faq_category(<?=$faqc[num]?>)"><span>반품/교환</span></button>
						<button type="button" data-content="menu" onclick="javascript:faq_category(<?=$faqc[num]?>)"><span>회원혜택</span></button>
						<button type="button" data-content="menu" onclick="javascript:faq_category(<?=$faqc[num]?>)"><span>기타</span></button>
					 -->
					
					<button <? if( strlen( $search_cate ) == 0 ){ echo 'class="active"'; }?> type="button" data-content="menu" onclick="javascript:faq_category('all')"><span>전체</span></button>
						
					<?php
                        $cnt_faq = 1;

                        foreach($cateData as $faqc){
                            $a_class = '';
                            if( $search_cate == $faqc[num] ) {
                                $a_class = 'class="active"';
                            }
					?>
					    	<button <?=$a_class?> type="button" data-content="menu" onclick="javascript:faq_category(<?=$faqc[num]?>)"><span><?=$faqc[faq_category_name]?></span></button>
					<?php
                            $cnt_faq++;
                        }
					?>
					</div>
					
					<div data-content="content" class="active">
						<table class="th-top table-toggle">
							<caption>FAQ 목록</caption>
							<colgroup>
								<col style="width:55px">
								<col style="width:120px">
								<col style="width:auto">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">No.</th>
									<th scope="col">구분</th>
									<th scope="col">상세내역</th>
								</tr>
							</thead>
							<tbody>
							<?php
		                            $cnt=0;
									foreach($data_faq as $faq){
		                                $number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
							?>
								<tr>
									<td class="txt-toneB"><?=$number?></td>
									<td class="txt-toneB"><?=$faq['faq_type']?></td>
									<td class="subject"><a href="javascript:;" class="menu fw-bold"><?=$faq['faq_title']?></a></td>
								</tr>
								<tr class="hide">
									<td class="reset" colspan="3">
										<div class="answer-box">
											<div class="question editor-output">
												<?=nl2br($faq['faq_content'])?>
											</div>
										</div>
									</td>
								</tr>
							<?php
								//$total--;
		                        	$cnt++;
								}
							?>
							</tbody>
						</table>
						
						<!-- 페이징 처리 -->
						<div class="list-paginate mt-20">
							<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
						</div>
						<!-- 페이징 처리 -->
					</div>
					
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<!-- 타입변경 폼 -->
<form name=form1 method="post" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="category" value="">
</form>

<form name="idxform" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
