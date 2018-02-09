<?php 

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$num = $_GET['num'];
if($num){
	$acces_sql = "UPDATE tblboard SET access = access+1 WHERE num={$num} ";	
	pmysql_query($acces_sql,get_db_conn());
	
	$sql = "
		SELECT a.*, b.title as prev_title, c.title as next_title 
		FROM tblboard a 
		LEFT JOIN tblboard b ON a.prev_no=b.num
		LEFT JOIN tblboard c ON a.next_no=c.num
		WHERE a.board='{$page_type}' 
		AND a.num='{$num}' 
	";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	if($row->is_secret=='1'){
		alert_go("비밀글입니다.","notice.php");
	}
}else{
	alert_go("잘못된 접근입니다.","notice.php");
}

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
	<script type="text/javascript" src="../css/select_type01.js" ></script>
	<script type="text/javascript" src="../css/jquery-1.10.1.js" ></script>
</head>	

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
				<th class="subject"><?=$row->title?></th>
			</tr>
			<tr>
				<td class="view">
<?php
			if(is_file($Dir."data/shopimages/board/notice_home/".$row->vstorefilename)){
?>
				<img src=<?=$Dir."data/shopimages/board/notice_home/".$row->vstorefilename?>" alt="" /><br />
<?php
			}
?>
					<?=$row->content?>
				</td>
			</tr>
		</table>
		
		<div class="ta_c mt_20"><a href="notice.php" class="btn_red">목록</a></div>
		
		<ul class="bbs_next_prev mt_30">
			<li><span>다음글 -</span><a href="notice_view.php?num=<?=$row->prev_no?>"><?=$row->prev_title?></a></li>
			<li><span>이전글 -</span><a href="notice_view.php?num=<?=$row->next_no?>"><?=$row->next_title?></a></li>
		</ul>

		
	</div>


<?php include "../outline/footer.php"; ?>

</div> 

</html>