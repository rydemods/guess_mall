<?php 

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include($Dir."lib/paging.php");

$gotopage = $_POST['gotopage'];
$block = $_POST['block'];
$search_option = $_POST['search_option'];
$search_text = strip_tags($_POST['search_text']);

//검색내용

$qry = "";
if($search_option){
	switch($search_option){
		case 1 :	//제목
			$qry.= " AND title LIKE '%{$search_text}%' ";
			break;
		case 2 :	//본문
			$qry.= " AND content LIKE '%{$search_text}%' ";
			break;
		case 3 :	//제목+본문
			$qry.= " AND(title LIKE '%{$search_text}%' OR content LIKE '%{$search_text}%' )";
			break;
		case 4 :	//글쓴이
			$qry.= " AND name = '{$search_text}' ";
			break;
		default :
			$qry.= "";
			break;
	}
}
//보드내용 가져오기
$board_sql = "
	SELECT num,name,title,writetime,access 
	FROM tblboard 
	WHERE board='notice_home' 
	AND pos = 0 
	AND depth = 0 
	AND notice = '0' 
	AND is_secret = '0'
";
$board_sql.=$qry;
$board_sql.="ORDER BY thread, pos ";

$paging = new Tem001_saveheels_Paging($board_sql,10,10,'GoPage',true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
$board_sql = $paging->getSql($board_sql);

$board_res = pmysql_query($board_sql,get_db_conn());
while($board_row = pmysql_fetch_array($board_res)){
	$board_notice[] = $board_row;
}
pmysql_free_result($board_res);
//공지사항 가져오기
$notice_sql = "
	SELECT num,name,title,writetime,access 
	FROM tblboard 
	WHERE notice = '1' 
	AND notice_secret = '1' 
	ORDER BY thread, pos 
";
$notice_res = pmysql_query($notice_sql,get_db_conn());
while($notice_row = pmysql_fetch_array($notice_res)){
	$notice_list[] = $notice_row;
}
pmysql_free_result($board_res);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko" >

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
	<meta name="description" content="XNGOLF" />
	<meta name="keywords" content="" />

	<title>엑스넬스 코리아</title>

	<link rel="stylesheet" href="../css/c_xngolf.css" />
	<!--<script type="text/javascript" src="../css/select_type01.js" ></script>-->
	<script type="text/javascript" src="../css/jquery-1.10.1.js" ></script>
</head>	
<script type="text/javascript">

function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}

function goSearch(){
	$("#search_option").val($("#search_keyOption").val());
	$("#search_text").val($("#search_keyWord").val());
	$("#frm1").submit();
}
</script>
<div class="main_wrap">
		
<?php include "../outline/header.php"; ?>


	<div class="sub_top_wrap sub_pos_notice"></div>
	<div class="container960 mb_50">
		<table class="tb_style01" width="100%">
			<caption>NOTICE<span>엑스넬스의 소식을 알려드립니다</span></caption>
			<colgroup>
				<col style="width:80px" /><col style="width:auto" /><col style="width:120pxpx" /><col style="width:100px" /><col style="width:100px" />
			</colgroup>
			<tr>
				<th>순번</th>
				<th>제목</th>
				<th>글쓴이</th>
				<th>날짜</th>
				<th>조회</th>
			</tr>
<?php	//공지사항
		foreach($notice_list as $k=>$v){
?>
		<tr>
			<td>공지</td>
			<td class="ta_l"><a href="notice_view.php?num=<?=$v['num']?>"><?=$v['title']?></a></td>
			<td><?=$v['name']?></td>
			<td><?=date("Y-m-d",$v['writetime'])?></td>
			<td><?=$v['access']?></td>
		</tr>
<?
		}
?>			

<?php	//기본
		foreach($board_notice as $k=>$v){
			$number = ($t_count-(($gotopage-1)*10))-$k;

?>
			<tr>
				<td><?=$number?></td>
				<td class="ta_l"><a href="notice_view.php?num=<?=$v['num']?>"><?=$v['title']?></a></td>
				<td><?=$v['name']?></td>
				<td><?=date("Y-m-d",$v['writetime'])?></td>
				<td><?=$v['access']?></td>
			</tr>
<?php
		}
?>
		</table>
		
		<div class="page_num">
			<!--<a href="" class="on">1</a><a href="">2</a><a href="">3</a>-->
			<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
			<div class="right_area">
				<form name="searchform" method="POST" action="<?=$_SERVER['PHP_SELF']?>" >
				<div class="bbs_search_wrap">
					<ul>
						<li>
							<select name="search_keyOption" id="search_keyOption">
								<option value="1">제목</option>
								<option value="2">본문</option>
								<option value="3">제목+본문</option>
								<option value="4">글쓴이</option>
							</select>
						</li>
						<li><input type="text" name="search_keyWord" id="search_keyWord" /></li>
						<li><a href="javascript:goSearch();" class="btn_red">검색</a></li>
					</ul>
				</div>
				</form>
			</div>
		</div>
	</div>

<form name="form1" id="frm1" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="block" value="<?=$block?>"/>
<input type="hidden" name="gotopage" value="<?=$gotopage?>"/>
<input type="hidden" name="search_option" id="search_option" value="<?=$search_option?>" />
<input type="hidden" name="search_text" id="search_text" value="<?=$search_text?>" />
</form>



<?php include "../outline/footer.php"; ?>

</div>

</html>