<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<?
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
		$order .= " ORDER BY l.regdate desc";
	}else if($sort == "best"){
		$order .= " ORDER BY l.access desc";
	}else if($sort == "like"){
		$order .= " ORDER BY hott_cnt desc";
	}
}
if(!empty($year)){
	$where .= " AND regdate >= '".$year."0101000000' AND regdate <= '".$year."1231235959' " ;
}

//룩북 리스트
$sql = "SELECT l.*, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tbllookbook l ";
$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code ";
$sql .= "WHERE l.display = 'Y' ";
$sql .= $where;
$sql .= $order;
$paging = new New_Templet_paging($sql,10,16,'GoPage',true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
$sql = $paging->getSql($sql);
// exdebug($sql);
$result = pmysql_query($sql);
while ($row = pmysql_fetch_array($result)) {
	$arrLookbookList[] = $row;
}

?>

<div id="contents" class="bg">
	<!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li class="on">LOOKBOOK</li>
		</ul>
	</div>
	<!-- // 네비게이션 -->
	<div class="inner">
		<main class="lookbook-wrap">
			<h2>LOOKBOOK</h2>
			<div class="tab-category">
				<ul>
					<li><a href="javascript:yearSelect('');" class="idx-menu  <?=$year == '' ? 'on' : '' ?>">ALL</a></li>
				<?foreach( $arrYear as $yVal ){?>
					<li><a href="javascript:yearSelect('<?=$yVal ?>');" class="idx-menu <?=$yVal == $year ? 'on' : '' ?>"><?=$yVal ?></a></li>
				<?} ?>
				</ul>
			</div>
			<div class="search-form-wrap">
<!--  			<form name="searchForm" method="post" action="<?=$_SERVER['PHP_SELF']?>">-->
				<div class="form-wrap">
					<fieldset class="instagram_search_form">
					<legend>상세검색</legend>
						<input type="text" title="상세검색" name="search" id="search_value"  onkeypress="if( event.keyCode==13 ){goSearch();}">
						<button  id="search" onclick="goSearch();">상세검색</button>
					</fieldset>
					<div class="my-comp-select" style="width:150px;">
						<select name="" class="required_value" id="sort" onchange="sortSelect(this.value)">
							<option value="latest" <?=$sort=="latest"?"selected":""?>>최신순</option>
							<option value="best" <?=$sort=="best"?"selected":""?>>인기순</option>
							<option value="like" <?=$sort=="like"?"selected":""?>>좋아요</option>
						</select>
					</div>
				</div>
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<input type="hidden" name="sort" value="<?=$sort ?>">
				<input type="hidden" name="year" value="<?=$year ?>">
<!--  			</form>-->
			</div>
			<div class="lookbook-list idx-content on">
				<div class="clear">
				<? for($i=1;$i<=3;$i++) { ?>
					<ul class="CLS_lookbook_list" id="ul_<?=$i?>"><!-- [D] 3개의 ul에 li가 번갈아가면서 하나씩 추가되는 구조로 변경되었습니다. (2016-10-06) -->

					</ul>
				<?} ?>	
				</div>
				<div class="btn_list_more mt-50" id="more6">
					<a href="javascript:moreView();" class="more_view" id="6">더보기</a>
				</div>

				<div class="list-paginate mt-30 hide"><!-- [D] 디자인 변경으로 hide(2016-10-06) -->
				<?if( $paging->pagecount > 1 ){
					echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;
					}?>
				</div>
			</div>
		</main>
	</div>
</div>
<input type="hidden" id="rownum" value="" />
<input type="hidden" id="lastrest" value="" />
<input type="hidden" id="lastidx" value="" />
<!-- // [D] 룩북 퍼블 추가 -->
<script type="text/javascript">

$(document).ready( function() {
	list();
});

//룩북 리스트
function list(){
	var year = $("input[name=year]").val();
	var sort = $("input[name=sort]").val();
	var search = $("#search_value").val();
	var rownum = $("#rownum").val();
	$.ajax({
		type: "POST",
		url: "../front/ajax_lookbook_list.php",
		data: {"year" : year, "sort" : sort, "search" : search, "rownum" : rownum},
		dataType:"JSON"
	}).done(function(data){
		for(i=0;i<data.length;i++) {	
// 			console.log("index:"+i);
// 			console.log("나머지 :"+(i % 3));
// 			console.log("위치 :"+((i % 3)+1));
			//넘어오는 hidden값 때문에 -2
			if(i == data.length -2){
				$("#lastrest").val((i % 3));
				$("#lastidx").val(i);
			}		
			$('#ul_'+((i % 3)+1)).append(data[i]);
		}
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

//더 보기
function moreView(){
	var rownum = $("#rownum").val();
	var year = $("input[name=year]").val();
	var sort = $("input[name=sort]").val();
	var search = $("#search_value").val();
	var lastrest = $("#lastrest").val();
	var lastidx = $("#lastidx").val();
	//이전 체크 rownum 제거
	$("#chk_rownum").remove();
	if(rownum){
    	$.ajax({
    		type: "POST",
    		url: "../front/ajax_lookbook_more.php",
    		dataType:"json",
    		data: {"year" : year, "sort" : sort, "search" : search, "rownum" : rownum},

    	}).done(function(data){
        	console.log(data);
    		for(i=0;i<data.length;i++) {
//     			console.log("index:"+(parseInt(i+1)+parseInt(lastidx)));
//     			console.log("나머지 :"+((parseInt(i+1)+parseInt(lastidx)) % 3));
//     			console.log("위치 :"+(((parseInt(i+1)+parseInt(lastidx)) % 3)+1));
    			if(i == data.length -2){
    				$("#lastrest").val((i % 3));
    				$("#lastidx").val((parseInt(i)+parseInt(lastidx)));
    			}		

    			$('#ul_'+(((parseInt(i+1)+parseInt(lastidx))% 3)+1)).append(data[i]);
    		}
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

function GoPage(block,gotopage) {
	document.searchForm.block.value=block;
	document.searchForm.gotopage.value=gotopage;
	document.searchForm.submit();
}

//정렬 순 검색
function sortSelect(val){
	lookbook_reset();
	$("input[name=sort]").val(val);
	list();
// 	$("form[name='searchForm']").submit();
}

//년도 검색
function yearSelect(year){
	lookbook_reset();
	$("input[name=year]").val(year);
	list();
// 	$("form[name='searchForm']").submit();
}

//검색어
function goSearch(){
	lookbook_reset();
	list();
}

//룩북 상세
function detail(no){
	var year = $("input[name=year]").val();
	var sort = $("input[name=sort]").val();
	if(sort == ""){
		var url = "../front/lookbook_view.php?no="+no+"&year="+year
	}else if(year == ""){
		var url = "../front/lookbook_view.php?no="+no+"&sort="+sort
	}else{
		var url = "../front/lookbook_view.php?no="+no+"&sort="+sort+"&year="+year
	}
	accessPlus(no,"tbllookbook","access","no");
	$(location).attr('href', url);
}

function lookbook_reset(){
	$(".CLS_lookbook_list").empty("");
}
</script>

<?php
include ($Dir."lib/bottom.php")
?>
