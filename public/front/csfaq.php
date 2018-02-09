<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$search=$_POST[search];
$faq_type=$_REQUEST[faq_type];
$search_cate = $_POST[category];

##### 공지사항 
$sql_notice = "SELECT * FROM tblboard where board='notice' ORDER BY writetime DESC LIMIT 4";
$res_notice = pmysql_query($sql_notice);
while($row_notice = pmysql_fetch_array($res_notice)){
	$data_notice[]=$row_notice;
}
pmysql_free_result($res_notice);
##### //공지사항
$page_num=$_POST[page_num];
$_POST[search_type] = ($_POST[search_type])?$_POST[search_type]:"faq_title";


#####FAQ
$sql_faq = "SELECT a.*, b.faq_category_name as faq_type FROM tblfaq a ";
$sql_faq.= "LEFT JOIN tblfaqcategory b ON a.faq_type=b.num ";

if($_POST[category]){
	$sql_faq .= "WHERE a.faq_type = '".$search_cate."' ";
}elseif($_POST[searchkey]){
	
	$sql_faq .= "WHERE a.".$_POST[search_type]." like '%".$_POST[searchkey]."%' ";
}
//$sql_faq.= "ORDER BY a.no desc ";
$sql_faq.= "ORDER BY a.sort ASC, a.no ASC "; // (순서 추가로 2016-03-16 김재수 추가)

if(!$setup["list_num"]) $setup["list_num"] = '10';
if(!$setup["page_num"]) $setup["page_num"] = '5';

$tot_faq = pmysql_query($sql_faq);
$total = pmysql_num_rows($tot_faq);
if($total>=11){
	$pageidx = $_POST["gotopage"] -1;
	if($_POST["gotopage"]==""){
		$pageidx = 0;
	}
	$total=$total-($pageidx*10);
}

$paging = new New_Templet_paging($sql_faq,$setup["page_num"],$setup["list_num"]);	
$sql_faq = $paging->getSql($sql_faq);
$gotopage = $paging->gotopage;

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
		
		<div class="containerBody sub-page">
			
			<div class="breadcrumb">
				<ul>
					<li><a href="/">HOME</a></li>
					<li><a href="/board/board.php?board=notice">CS CENTER</a></li>
					<li class="on"><a>FAQ</a></li>
				</ul>
			</div>
			<!-- 고객센터 LNB -->
			<?	$lnb_flag = 5;
					include ($Dir.MainDir."lnb.php");
				?>
			<!-- // 고객센터 LNB -->
			<div class="right_section">
				<div class="faq-wrap">
					<ul class="title-sort">
						<li><a <? if( strlen( $search_cate ) == 0 ){ echo 'class="on"'; } ?> href="javascript:faq_category('all')">전체</a></li>
						<?php
							$cnt_faq = 1;
							
							foreach($cateData as $faqc){
								$a_class = '';
								if( $search_cate == $faqc[num] ) { 
									$a_class = 'class="on"';
								} 
						?>
							<li><a <?=$a_class?> href="javascript:faq_category(<?=$faqc[num]?>)"><?=$faqc[faq_category_name]?></a></li>
						<?php
								$cnt_faq++;
							}								
						?>
					</ul>
					
					<form name=form1 method="post" action="<?=$_SERVER['PHP_SELF']?>">
					<input type="hidden" name="category" value="">
					<!--검색-->						
					<div class="list_search hide">
						<div class="search_box">
							<?php
								$sel_type[$_POST[search_type]] = " selected";
							?>
							<select name="search_type" title="검색 유형을 선택하세요.">
								<option value="faq_title"<?=$sel_type['faq_title']?>>제목</option>
								<option value="faq_content"<?=$sel_type['faq_content']?>>내용</option>
							</select>
							<input type="text" name="searchkey" title="검색어를 입력하세요." value="<?=$_POST[searchkey]?>">
							<a href="javascript:seachkey_check()" target="_self" class="btn_util">검색</a>
						</div>
					</div><!--// 검색-->
					</form>
					
					<!--FAQ LIST-->
					<table class="th-top util" summary="">
						<caption><strong>FAQ</strong></caption>
						<colgroup>
							<col style="width:56px" >
							<col style="width:96px" >
							<col style="width:auto" >
						</colgroup>
						<thead>
							<tr>
								<th scope="col">번호</th>
								<th scope="col">구분</th>
								<th scope="col">제목</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach($data_faq as $faq){
						?>
							<tr>
								<td><?=$total?></td>
								<td><?=$faq['faq_type']?></td>
								<td class="subject" ><a href="javascript:faqOpen(<?=$total?>)"><?=$faq['faq_title']?></a><span class="open-icon"></span></td>
							</tr>
							<tr style="display:none" id="<?=faq_.$total?>">
								<td colspan="3" class="open">
									<ul class="faq-answer">
										<li class="ques hide"><?=$faq['faq_title']?></li>
										<li class="answer"><?=nl2br($faq['faq_content'])?> </li>
									</ul>
								</td>
							</tr>
						<?php
								$total--;
							}								
						?>
							
						</tbody>
					</table><!--// FAQ LIST-->
					
					<!--PAGING-->
					<div class="paging">
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</div>
					<!--// PAGING-->
				</div><!-- //.faq-wrap -->
			</div>

		</div>
<form name="idxform" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
