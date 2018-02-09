<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

//$search=$_POST[search];
$searchkey = $_POST[searchkey];
$search_type = $_POST[search_type];
$gotopage=$_POST[gotopage];

##### 공지사항 
$sql_notice = "SELECT * FROM tblboard where board='notice' ORDER BY writetime DESC LIMIT 4";
$res_notice = pmysql_query($sql_notice);
while($row_notice = pmysql_fetch_array($res_notice)){
	$data_notice[]=$row_notice;
}
pmysql_free_result($res_notice);

##### //공지사항

##### SEARCH
$qry = "WHERE 1=1 ";
if($searchkey){
	if($search_type=="productname") $qry .= "AND upper(b.productname) LIKE upper('%".$searchkey."%') ";
	if($search_type=="content") $qry .= "AND upper(a.tutorial_ex) LIKE upper('%".$searchkey."%') ";
}

#####


##### 튜토리얼 

$sql = "
	SELECT 
	a.* 
	,b.productname 
	FROM tblproduct_tutorial a 
	JOIN tblproduct b ON a.prcode=b.productcode 
";
$sql .= $qry;
$sql .= "AND upper(tutorial_ex) != upper('<br>') ";
$sql .= "ORDER BY productname ASC, sort ASC ";


// 번호

$tot_sql = pmysql_query($sql);
$total = pmysql_num_rows($tot_sql);
if($total>=11){
	$pageidx = $_POST["gotopage"] -1;
	if($_POST["gotopage"]==""){
		$pageidx = 0;
	}
	$total=$total-($pageidx*10);
}


$paging = new Tem001_saveheels_Paging($sql,10,10);	
$sql = $paging->getSql($sql);
$gotopage = $paging->gotopage;

$res = pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_array($res)){
	$tutorial_content[] = $row;
}
pmysql_free_result($res);

##### 튜토리얼

#####좌측 메뉴 class='on' 을 위한 페이지코드
$class_on['tutorial'] = " class='on'";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - FAQ</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
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
</HEAD>
<style type="text/css">
table.list_table td.answer ul.faq_answer {background:none !important; border:none !important;}
</style>


<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>


<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


 
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td>

		<!-- 메인 컨텐츠 -->
		<div class="main_wrap">
			<?
			$subTop_flag = 3;
			include ($Dir.MainDir."sub_top.php");
			?>
			
			<div class="container1100 sub_skin">
				<!-- 고객센터 LNB -->
				<?	$lnb_flag = 5;
						include ($Dir.MainDir."lnb.php");
					?>
				<!-- // 고객센터 LNB -->

				<div class="right_section mb_80">
					
					<h3 class="title">
						Tutorial
						<p class="line_map"><a>홈</a> &gt; <a>고객센터</a> &gt; <a class="on">Tutorial</a></p>
					</h3>

					<div class="customer_notice_wrap">
						
						<form name=form1 method="post" action="<?=$_SERVER['PHP_SELF']?>">
						<input type="hidden" name="category" value=""/>
						<!--검색-->						
						<div class="list_search">
							<div class="search_box">
								<?php
									$sel_type[$_POST[search_type]] = " selected";
								?>
								<select name="search_type" title="검색 유형을 선택하세요.">
									<option value="productname"<?=$sel_type['productname']?>>제품명</option>
									<option value="content"<?=$sel_type['content']?>>내용</option>
								</select>
								<input type="text" name="searchkey" title="검색어를 입력하세요." value="<?=$_POST[searchkey]?>"/>
								<a href="javascript:seachkey_check()" target="_self" class="btn_util">검색</a>
							</div>
						</div><!--// 검색-->
						</form>
						
						<!--FAQ LIST-->
						<table class="list_table" summary="">
							<caption><strong>Tutorial</strong></caption>
							<colgroup>
								<col style="width:60px" />
								<col style="width:auto;" />
							</colgroup>
							<thead>
								<tr>
									<th scope="col">번호</th>
									<th scope="col">제목</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$cnt = 0;
								foreach($tutorial_content as $tKey=>$tVal){
									/*$tContent = $tVal[tutorial_content];
									$matchNum = strpos($tContent,'src=');
									$matchStr = preg_match('/youtube\.com.*(\?v=|\/embed\/)(.{11})/',$tContent,$matchesC);
									$youtubeUrl = "<img src='http://img.youtube.com/vi/".$matchesC[count($matchesC)-1]."/1.jpg'>";*/
							?>
								<tr>
									<td><?=$total-$cnt?></td>
									<td class="title" style="text-align: center;" ><a href="<?=$Dir.FrontDir?>tutorial_view.php?num=<?=$tVal['tutoidx']?>"><?=$tVal['productname']?></a></td>
								</tr>
							<?php
									$cnt++;
								}								
							?>
								
							</tbody>
						</table><!--// FAQ LIST-->
						
						<!--PAGING-->
						<div class="paging">
						<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
						</div>
						<!--// PAGING-->
					</div>
				</div>

			</div>

		</div>
		<!-- //메인 컨텐츠 -->
	</td>
</tr>
<form name="idxform" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
</table>

<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
