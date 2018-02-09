<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include($Dir."lib/file.class.php");
####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$board_code = $_REQUEST["board_code"];
$search = $_REQUEST["search"];
$delete_array = $_REQUEST['delete_array'];
$brandBoardMode = $_REQUEST["brandBoardMode"];
$Mode = $_REQUEST['Mode'];
$board_num = $_REQUEST['board_num'];
$row_number = $_REQUEST['row_number'];
//exdebug("row_number::".$row_number);
$max_row_number = $_REQUEST['max_row_number'];

if(!$brandBoardMode) $brandBoardMode = "list";
$includeMode = array('list'=>'community_brandboard_list.php','write'=>'community_brandboard_write.php','view'=>'community_brandboard_view.php','modify'=>'community_brandboard_write.php');


$imgdir = "images/board";
$up_file=new FILE("../data/shopimages/board/".$_REQUEST['up_board']."/");
include($Dir.BoardDir."file.inc.php");
$setup=array();
$file_icon_path = "images/board/file_icon";

include("header.php"); 
//보드 카테고리
function brandBoardCategory(){
	$sql = "SELECT board_code,board_name FROM tblbrand_boardadmin ";
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
//보드 리스트
function brandBoardList($board_code,$search) {
	$qry = "WHERE 1=1 ";
	if($board_code){
		$qry.= "AND board_code = ".$board_code." ";
	}else{
		$qry.= " ";
	}
	if(strlen($search) > 0){
		$qry.= "AND board_title||board_content LIKE '%".$search."%' ";
	}

	//$qry = "AND board_title||board_content = '".$search."' ";
	$cntSql = "SELECT COUNT(*) FROM tblbrand_board ".$qry;
	
	$paging = new Paging($cntSql,10,10);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	$sql = "SELECT * ,row_number()over(ORDER BY date DESC) FROM tblbrand_board ".$qry;
	//$sql.= "ORDER BY date DESC ";
	$sql = $paging->getSql($sql);
	//exdebug($sql);
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$brandBoardList['list'][] = $row;
		$brandBoardList['t_count'] = $t_count;
		$brandBoardList['gotopage'] = $gotopage;
		$brandBoardList['paging'] = $paging;
	}
	pmysql_free_result($result);
	if(sizeof($brandBoardList) > 0){
		$array = "";
		$array[0] = $brandBoardList;
		$array[1] = $t_count;
		return $array;
	}else{
		return false;
	}
}
//리스트 선택삭제
function brandBoardDelete($delete_array){
	$sql = "delete from tblbrand_board where board_num in ({$delete_array})";
	$result = pmysql_query($sql,get_db_conn(),'clean');
}
// 뷰
function brandBoardView($board_num){
	$sql = "SELECT a.board_code, a.board_num, a.productcode, a.board_title, a.board_content, "; 
	$sql.= "a.big_image, a.thumbnail_image, a.date, b.productname, b.sellprice, b.tinyimage, ";
	$sql.= "a.page_code ";
	$sql.= "FROM tblbrand_board a ";
	$sql.= "LEFT JOIN tblproduct b ON a.productcode = b.productcode ";
	$sql.= "WHERE a.board_num = ".$board_num;
	$result = pmysql_query($sql,get_db_conn());
	if($row = pmysql_fetch_array($result)){
		$thisBoard = $row;
	}
	//return $board_num;
	return $thisBoard;
}

//뷰 이전글 다음글 확인
function brandBoardView_privew($board_code,$board_num,$date=0){
	$qry = "";
	if($board_code){
		$qry = " AND board_code = ".$board_code." ";
	}
	$prev_sql = "SELECT board_num,board_title FROM tblbrand_board WHERE board_num > ".$board_num." ".$qry." ORDER BY board_num DESC LIMIT 1";
	$prev_result = pmysql_query($prev_sql,get_db_conn());
	$prev_row = pmysql_fetch_array($prev_result);
	$prev = $prev_row;
	pmysql_free_result($prev_result);
	
	$next_sql = "SELECT board_num,board_title FROM tblbrand_board WHERE board_num < ".$board_num." ".$qry." ORDER BY board_num DESC LIMIT 1";
	$next_result = pmysql_query($next_sql,get_db_conn());
	$next_row = pmysql_fetch_array($next_result);
	$next = $next_row;
	pmysql_free_result($next_result);
	
	$prevNextArray = array($prev,$next);
	return $prevNextArray;
}
//관련상품
function brandBoardItem($board_num){
	$sql = "SELECT a.productcode,b.productname,b.sellprice,b.tinyimage ";
	$sql.= "FROM tblbrand_boarditem a ";
	$sql.= "JOIN tblproduct b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.board_num= ".$board_num;
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_array($result)){
		$thisBosrdItem[] = $row;
	}
	return $thisBosrdItem;
}
//카테고리
function codeListScript(){
	$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
	$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ";
	$sql.= "order by code_a asc, cate_sort asc";
	$result  = pmysql_query($sql,get_db_conn());
	$i=0;
	$ii=0;
	$iii=0;
	$iiii=0;
	$strcodelist = "";
	$strcodelist.= "<script>\n";
	$selcode_name="";

	while($row=pmysql_fetch_object($result)) {
		$strcodelist.= "var clist=new CodeList();\n";
		$strcodelist.= "clist.code_a='{$row->code_a}';\n";
		$strcodelist.= "clist.code_b='{$row->code_b}';\n";
		$strcodelist.= "clist.code_c='{$row->code_c}';\n";
		$strcodelist.= "clist.code_d='{$row->code_d}';\n";
		$strcodelist.= "clist.type='{$row->type}';\n";
		$strcodelist.= "clist.code_name='{$row->code_name}';\n";
		if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
			$strcodelist.= "lista[{$i}]=clist;\n";
			$i++;
		}
		if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
			if ($row->code_c=="000" && $row->code_d=="000") {
				$strcodelist.= "listb[{$ii}]=clist;\n";
				$ii++;
			} else if ($row->code_d=="000") {
				$strcodelist.= "listc[{$iii}]=clist;\n";
				$iii++;
			} else if ($row->code_d!="000") {
				$strcodelist.= "listd[{$iiii}]=clist;\n";
				$iiii++;
			}
		}
		$strcodelist.= "clist=null;\n\n";
	}
	pmysql_free_result($result);
	$strcodelist.= "CodeInit();\n";
	$strcodelist.= "</script>\n";

	echo $strcodelist;

	echo "<select name='code_a' id='code_a' style=\"width:170px;\" onchange=\"SearchChangeCate(this,1)\">\n";
	echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name='code_b' id='code_b' style=\"width:170px;\" onchange=\"SearchChangeCate(this,2)\">\n";
	echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name='code_c' id='code_c' style=\"width:170px;\" onchange=\"SearchChangeCate(this,3)\">\n";
	echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name='code_d' id='code_d' style=\"width:170px;\">\n";
	echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
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

$brandBoardCategory = brandBoardCategory();

if($Mode == 'delete'){
	
	brandBoardDelete(substr($delete_array,0,-1));
	/*if($_SERVER['REMOTE_ADDR'] == '218.234.32.12'){
		
		exdebug(substr($delete_array,0,-1));
	}*/
}


?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>게시판 게시물 관리</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_community.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">게시판 게시물 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>등록된 게시판의 모든 게시물을 관리할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<? include ($includeMode[$brandBoardMode]) ?>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>게시판 게시물 관리</span></dt>
							<dd>
								- 쇼핑몰에 등록된 게시판의 모든 글을 수정/삭제 및 작성하실 수 있습니다.<br>
								- 회원 게시판에 별도의 로그인 없이 비밀글 열람 및 게시물 관리가 가능합니다.
							</dd>
						</dl>
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<?=$onload?>
<?php 
include("copyright.php");
