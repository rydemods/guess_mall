<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

function brandBoardCategory(){
	$sql = "SELECT board_code,board_name FROM tblbrand_boardadmin ORDER BY date ASC";
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$brandBoardCategory[$row->board_code] = $row;
	}
	if(sizeof($brandBoardCategory) > 0){
		return $brandBoardCategory;
	}else{
		return false;
	}
}

function thisBrandCate($board_code){
	$sql = "SELECT * FROM tblbrand_boardadmin WHERE board_code = ".$board_code;
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	$boardCate = $row;
	pmysql_free_result($result);
	
	return $boardCate;
}

function barandBoardList($board_code,$boardSearch,$listNum=10){
	$boardArray = array();
	$searchText = "";
	if(strlen($boardSearch)>0){
		$searchText = "AND board_title||board_content LIKE '%".$boardSearch."%' ";
	}
	$sql = "SELECT * FROM tblbrand_board WHERE board_code = {$board_code} ".$searchText;
	$sql.= "ORDER BY date DESC ";
	
	$paging = new Tem001_saveheels_Paging($sql,10,$listNum,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	$sql = $paging->getSql($sql);

	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$boardList[]=$row;
	}
	pmysql_free_result($result);
	$boardArray[0] = $boardList;
	$boardArray[1] = $paging;
	$boardArray[2] = $t_count;
	$boardArray[3] = $gotopage;
	
	$cntSql = "UPDATE tblbrand_boardadmin SET board_count = board_count + 1 WHERE board_code = {$board_code} ";
	pmysql_query($cntSql,get_db_conn());
	
	return $boardArray;
}

function Item($board_num){//관련 상품 가져오는 함수
	$item = array();
	$sql = "select * from tblbrand_boarditem a join tblproduct b on a.productcode=b.productcode where a.board_num={$board_num}";
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$item[] = $row;
	}
	return $item;
}
// movie, campain 보드리스트
function sortBoardList( $page_code, $board_code, $boardSearch = "" ){
	$pageArray = "";
	$searchText = "";
	if(strlen($boardSearch)>0){
		$searchText = "AND b.board_title||b.board_content LIKE '%".$boardSearch."%' ";
	}
	$sql = "SELECT bp.page_code, bp.board_code, bp.page_name, ";
	$sql.= "b.board_num, b.board_title, b.board_content, b.big_image, b.thumbnail_image, b.date ";
	$sql.= "FROM tblbrand_boardpage bp ";
	$sql.= "JOIN tblbrand_board b ON bp.page_code = b.page_code ";
	$sql.= "WHERE bp.use_yn = 'Y' ";
	if( strlen($boardSearch)>0 ) {
		$sql.= $searchText;
	} else {
		$sql.= "AND bp.page_code = ".$page_code." ";
	}
	$sql.= "AND bp.board_code = ".$board_code." ";
	$sql.= "ORDER BY b.date DESC ";
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_array($result)){
		$pageArray[] = $row;
	}
	
	return $pageArray;
}
// movie, campain 보드 내부 카테고리
function sortBoardCate( $board_code, $page_code = false ){
	$cateArray = "";
	$sql = "SELECT page_code, board_code, page_name ";
	$sql.= "FROM tblbrand_boardpage WHERE use_yn = 'Y' ";
	$sql.= "AND board_code = ".$board_code." ";
	$sql.= "ORDER BY page_code ";
	$result = pmysql_query( $sql, get_db_conn() );
	$cnt = 0;
	while( $row = pmysql_fetch_array($result) ) {
		if( !$page_code ) {
			if( $cnt == 0 ) {
				$cateArray["on"] = $row;
			} else {
				$cateArray[] = $row;
			}
		} else {
			if( $page_code == $row[page_code] ) {
				$cateArray["on"] = $row;
			}else{
				$cateArray[] = $row;
			}
		}
		$cnt++;
	}
	return $cateArray;
}

$board_code = $_GET['board_code'];
$boardSearch = $_GET['boardSearch'];
$page_code = $_GET['page_code'];
$boardCate = thisBrandCate($board_code);
$brandBoardCate = brandBoardCategory();
?>
<!--php끝-->
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?php include ($Dir.TempletDir."/brandboard/boardlist_".$boardCate->board_skin.".php") ?>

<form name="searchForm" id="searchForm" method="GET" >
	<input type="hidden" name="board_code" id="searchBoardCode" value="<?=$board_code?>"/>
	<input type="hidden" name="block"/>
	<input type="hidden" name="gotopage"/>
	<input type="hidden" name="boardSearch" id="boardSearch" value="<?=$boardSearch?>"/>
	<input type="hidden" name="page_code" id="page_code" value="<?=$page_code?>" />
</form>

<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
