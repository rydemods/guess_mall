<style type="text/css">
	ul.tip_layout {float:left; overflow:hidden; margin-left:20px; width:860px;}
	ul.tip_layout li {width:410px !important; float:left; margin-left:20px; }
	.ml_258 {margin-left:258px;}
</style>
<ul class="tip_layout">
<?
$board_cate = array("솝스쿨실험실","원료사전","아로마테라피","비누연구실","화장품연구실","에코리빙","소소한팁");

function getCategoryBoardList($board, $category=''){
	$query = "select * from tblboard where board='".$board."' and category='".$category."' order by num desc limit 5";
	$result = pmysql_query($query, get_db_conn());
	while($row = pmysql_fetch_array($result)){
		$row[regdt] = date("Y-m-d",$row[writetime]);
		$row[title_tag] = $row[title];
		$data[] = $row;
	}
	return $data;;
}
$i=0;
foreach($board_cate as $cate){
$i++;
$list = getCategoryBoardList("tip",$cate);
?>
<li>
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr>
 <td height="33" style="background:url(/data/shopimages/main/notice_bg.gif);"><div class=float><a href="/board/board.php?pagetype=list&board=tip&category=<?=$cate?>"><img src="/data/shopimages/main/notice_0<?=$i?>_more.gif" alt="" border="0" /></a></div>   <div style="float:right"></div>
 </td>
</tr>
<tr>
  <td height="10" style="background:url(/data/shopimages/main/notice_bg.gif);"> </td>
</tr>
<tr>
 <td bgcolor=#EEEEED valign=top>
 <table width=100% cellpadding=0 cellspacing=0 border=0>
<?foreach($list as $data){?>
<tr><td height=10 colspan=2></td></tr>
<tr><td width=220 height=19 style="padding: 2 0 6 10">- <a href="/board/board.php?pagetype=view&num=<?=$data[num]?>&board=tip&block=&gotopage=<?=$_REQUEST[gotopage]?>"><?=$data[title_tag]?></a></td>
 <td width=80 align=center><?=$data[regdt]?></td>
</tr>
<?}?>
<tr><td height=3 colspan=2></td></tr>
</table>
</td></tr>
<tr>
	<td height=10></td>
</tr>
</table>
</li>
<?}?>
</ul>