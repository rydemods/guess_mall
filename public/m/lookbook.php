<?php include_once('outline/header_m.php'); ?>
<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$imagepath = $Dir.DataDir."shopimages/lookbook/";
$search = $_POST["search"];
$sort = $_POST["sort"] ? $_POST["sort"] : 'latest';
$year = $_POST["year"];

$Today = time();
$Count_y=date("Y",$Today);
$Count_1y=date("Y",strtotime("-1 year",$Today));
$Count_2y=date("Y",strtotime("-2 year",$Today));

$arrYear[] = $Count_y;
array_push($arrYear, $Count_1y,$Count_2y);

//검색 조건
$where = "";
$order = "";
if(!empty($search)){
	$where .= " AND ( l.title iLIKE '%{$search}%' OR l.content iLIKE '%{$search}%' OR l.tag = '%{$search}%')  ";
}
if(!empty($sort)){
	if($sort == "latest"){
		$order .= " ORDER BY regdate desc, no desc";
	}else if($sort == "best"){
		$order .= " ORDER BY access desc, no desc";
	}else if($sort == "like"){
		$order .= " ORDER BY hott_cnt desc, no desc";
	}
}
if(!empty($year)){
	$where .= " AND regdate >= '".$year."0101000000' AND regdate <= '".$year."1231235959' " ;
}

//룩북 리스트
$sql = "SELECT ROW_NUMBER() OVER(".$order.") AS ROWNUM, * FROM (";
$sql .= "SELECT l.*, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tbllookbook l ";
$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code ";
$sql .= "WHERE l.display = 'Y' ";
$sql .= $where;
$sql .= $order;
$sql .= ") INFO ORDER BY ROWNUM ";
$sql .= "LIMIT 10";
/*$paging = new New_Templet_paging($sql,10,16,'GoPage',true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
$sql = $paging->getSql($sql);
*/
// exdebug($sql);
$result = pmysql_query($sql);
while ($row = pmysql_fetch_array($result)) {
	$arrLookbookList[] = $row;
	$rownum = $row['rownum'];
}

//데이터가 있는지 체크
$check_sql = "SELECT * FROM (";
$check_sql .= "SELECT ROW_NUMBER() OVER(".$order.") AS ROWNUM, * FROM (";
$check_sql .= "SELECT l.*, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tbllookbook l ";
$check_sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code ";
$check_sql .= "WHERE l.display = 'Y' ";
$check_sql .= $where;
$check_sql .= $order;
$check_sql .= " ) a";
$check_sql .= ") INFO WHERE rownum > ".$rownum." ORDER BY ROWNUM LIMIT 1";
$chk_result = pmysql_query($check_sql);
$chk_row = pmysql_fetch_object( $chk_result );
$chk_rownum = $chk_row->rownum;
?>

<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="../m/lookbook.php" class="prev"></a>
		<span>LOOKBOOK</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>
<div class="wrap_magazine">
	<form name="searchForm" method="post" action="<?=$_SERVER['PHP_SELF']?>">
    <div class="sorting_area">
		<select class="select_def" onchange="yearSelect(this.value)">
			<option value="">ALL</option>
			<?foreach( $arrYear as $yVal ){?>
			<option value="<?=$yVal ?>" <?=$yVal==$year?"selected":""?>><?=$yVal ?></option>
			<?} ?>
		</select>
		<div class="searchbox clear">
			<input type="search" name="search" id="search" value="<?=$search ?>">
			<button type="submit" class="btn-def" >검색</button>
		</div>
		<div class="list_sort">
			<ul class="clear">
				<li><a href="javascript:sortSelect('latest');">최신순</a></li>
				<li><a href="javascript:sortSelect('best');">인기순</a></li>
				<li><a href="javascript:sortSelect('like');">좋아요</a></li>
			</ul>
		</div>
	</div><!-- //.sorting_area -->
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type="hidden" name="sort" value="<?=$sort ?>">
	<input type="hidden" name="year" value="<?=$year ?>">
	</form>
    <div>
    <?if(count($arrLookbookList) > 0){?>
		<ul class="clear lookbook_list">
			<?foreach( $arrLookbookList as $key=>$val ){
			$reg_date	= substr($val['regdate'],0,4).".".substr($val['regdate'],4,2).".".substr($val['regdate'],6,2);
			?>
			<li>
				<span>
					<a href="javascript:detail('<?=$val['no'] ?>');"><img src="<?=$imagepath.$val['img_file'] ?>" alt=""></a>
					<div class="btn-posting">
						<button class="like_l<?=$val['no'] ?> comp-like btn-like <?=$val['section'] ? 'on' : '' ?>" onclick="detailSaveLike('<?=$val['no'] ?>', '<?=$val['section']?'on':'off' ?>', 'lookbook', '<?=$_ShopInfo->getMemid() ?>','')" title="<?=$val['section'] ? '선택됨' : '선택 안됨'  ?>"><span class="like_lcount_<?=$val['no'] ?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
					</div>
				</span>
			</li>
		<?} ?>
		</ul>
	<?} ?>
	</div><!-- //.list_magazine -->
	<?if($chk_rownum != ""){ ?>
	<div class="btn_list_more more_btn">
		<a href="javascript:moreView();" class="more_view">더보기</a>
	</div><!-- //.btn_list_more -->
	<?} ?>

</div><!-- //.wrap_magazine -->
<input type="hidden" id="rownum" value="<?=$rownum ?>" />
<script type="text/javascript">
function GoPage(block,gotopage) {
	document.searchForm.block.value=block;
	document.searchForm.gotopage.value=gotopage;
	document.searchForm.submit();
}

//정렬 순 검색
function sortSelect(val){
	$("input[name=sort]").val(val);
	$("form[name='searchForm']").submit();
}

//년도 검색
function yearSelect(year){
	$("input[name=year]").val(year);
	$("form[name='searchForm']").submit();
}

//룩북 상세
function detail(no){
	var year = $("input[name=year]").val();
	var sort = $("input[name=sort]").val();
	if(sort == ""){
		var url = "../m/lookbook_detail.php?no="+no+"&year="+year
	}else if(year == ""){
		var url = "../m/lookbook_detail.php?no="+no+"&sort="+sort
	}else{
		var url = "../m/lookbook_detail.php?no="+no+"&sort="+sort+"&year="+year
	}
	accessPlus(no,"tbllookbook","access","no");
	$(location).attr('href', url);
}

//더 보기
function moreView(){
	var rownum = $("#rownum").val();
	var year = $("input[name=year]").val();
	var sort = $("input[name=sort]").val();
	var search = $("#search").val();
	//이전 체크 rownum 제거
	$("#chk_rownum").remove();
	if(rownum){
    	$.ajax({
    		type: "POST",
    		url: "../front/ajax_lookbook_more.php",
    		dataType:"json",
    		data: {"year" : year, "sort" : sort, "search" : search, "rownum" : rownum, "kind" : "mobile"},

    	}).done(function(data){
			$(".lookbook_list").append(data);
    		
    		if($("#chk_rownum").val() == ""){
    			$(".btn_list_more").hide();
    		}else{
    			$("#rownum").val($("#chk_rownum").val()-1);
    		}	
    		if(data.length > 0){
    			$(".lookbook-list").addClass("on");
    		}		
    	});
	}
}
</script>

<? include_once('outline/footer_m.php'); ?>
