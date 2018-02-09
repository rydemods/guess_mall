<?php
$subTitle = "고객센터";
include_once('outline/header_m.php');

$search         = $_POST[search];
$faq_type       = $_REQUEST[faq_type];
$search_cate    = $_POST[category];
$page_num       = $_POST[page_num];

if(!$setup["list_num"]) $setup["list_num"] = '5';
if(!$setup["page_num"]) $setup["page_num"] = '5';

#####FAQ
$sql_faq = "SELECT a.*, b.faq_category_name as faq_type FROM tblfaq a ";
$sql_faq.= "LEFT JOIN tblfaqcategory b ON a.faq_type=b.num ";
$sql_faq.= "WHERE 1=1 ";

if($_POST[category]){
	$sql_faq .= "AND a.faq_type = '".$search_cate."' ";
}
$sql_faq.= "AND b.secret = '1' ";
$sql_faq.= "ORDER BY a.sort ASC, a.no ASC "; // (순서 추가로 2016-03-16 김재수 추가)

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

?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}

function faq_category(cnum){
	document.form1.category.value=cnum;
	if(cnum=="all"){
		document.form1.category.value="";
	}
	document.form1.submit();

}

function answer(no){
	if( $("#answer"+no).css('display') == 'none' ){
		$(".answer").hide();
		$("#answer"+no).show();
	}else{
		$("#answer"+no).hide();
	}
}
//-->
</SCRIPT>

<!-- 내용 -->
<main id="content" class="subpage">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>FAQ</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="cs_faq sub_bdtop">
		
		<div class="divide-box-wrap four">
			<ul class="divide-box faq_cate">
				<!-- <li class="on"><a href="javascript:;">전체</a></li> --><!-- [D] sorting된 해당 카테고리에 .on 클래스 추가 -->
				<!-- <li><a href="javascript:;">상품관련</a></li>
				<li><a href="javascript:;">주문/결제</a></li>
				<li><a href="javascript:;">배송관련</a></li>
				<li><a href="javascript:;">취소/환불</a></li>
				<li><a href="javascript:;">반품/교환</a></li>
				<li><a href="javascript:;">회원혜택</a></li>
				<li><a href="javascript:;">기타</a></li> -->
		<?php 
			if(strlen( $search_cate ) == 0){
				echo "<li class=\"on\"><a href=\"javascript:faq_category('all');\">전체</a></li>";
			} else {
				echo "<li ><a href=\"javascript:faq_category('all');\">전체</a></li>";
			}
			
			$cnt_faq = 1;
			foreach($cateData as $faqc){
				$a_class = '';
				if( $search_cate == $faqc[num] ) {
					$a_class = 'class="on"';
				}
				echo "<li ".$a_class."><a href=\"javascript:faq_category('".$faqc[num]."');\">".$faqc[faq_category_name]."</a></li>\n";
				$cnt_faq++;
			}
		?>
			</ul>
		</div>

		<table class="th-top accordion_tbl mt-15">
			<colgroup>
				<col style="width:85px;">
				<col style="width:auto;">
			</colgroup>
			<thead>
				<tr>
					<th>구분</th>
					<th>제목</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				foreach($data_faq as $faq){
					echo "<tr>";
					echo "	<td><span class=\"brightest\">".$faq['faq_type']."</span></td>";
					echo "	<td><a href=\"javascript:;\" class=\"subject accordion_btn\">".$faq['faq_title']."</a></td>";
					echo "</tr>";
					echo "<tr class=\"accordion_con\">";
					echo "	<td colspan=\"2\" class=\"answer_area\">";
					echo "	<div class=\"ans\">마이페이지 > ".nl2br($faq['faq_content'])."</div>";
					echo "	</td>";
					echo "</tr>";
				}
			?>
				<!-- 반복(리스트 5개씩 노출) -->
				<!-- <tr>
					<td><span class="brightest">상품관련</span></td>
					<td><a href="javascript:;" class="subject accordion_btn">A/S를 받고 싶은데 어떻게 해야 하나요?</a></td>
				</tr>
				<tr class="accordion_con">
					<td colspan="2" class="answer_area">
						<div class="ans">마이페이지 > 주문/배송조회 에서 상품이 배송중일 경우 [배송추적]을 통해 해당 상품을 배송하는 택배사의 사이트로 이동되어 상세한 배송정보를 확인하실 수 있습니다.</div>
					</td>
				</tr> -->
				<!-- //반복 -->

			</tbody>
		</table><!-- //.th-top -->

		<div class="list-paginate mt-15">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div><!-- //.list-paginate -->

	</section><!-- //.cs_faq -->

</main>
<!-- //내용 -->

<!-- 타입변경 폼 -->
<form name=form1 method="post" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="category" value="">
</form>

<form name="idxform" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<? include_once('outline/footer_m.php'); ?>