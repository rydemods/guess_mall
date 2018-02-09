<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/wonjae_lib.php");
?>
<?
$imagepath=$Dir.DataDir."shopimages/board/reviewbbs/";
$marks_array = array(1=>'★☆☆☆☆',2=>'★★☆☆☆',3=>'★★★☆☆',4=>'★★★★☆',5=>'★★★★★');
$productcode =$_POST['productcode'];
$productname = $_POST['productname'];
$offset = $_POST['offset'];
$sql = " select * from tblproductreview ";
$sql .= " where productcode='{$productcode}' ";
$sql .= " order by date desc ";
$sql .=" limit 3 ";
if($offset){
	$sql .= " offset ".$offset ;
}

$result = pmysql_query($sql,get_db_conn());
$count = 1;
?>
<table style="width:100%;">
	<div>[<?=$productname?>]상품에 등록된 리뷰</div>
	<tr class="ajax_list">
		<th>등록일</th><th>작성자정보</th><th>이미지</th><th>리뷰제목</th><th>별점</th>
	</tr>
	<?while( $row=pmysql_fetch_object( $result ) ){?>
	<?$page_numberic_type=1; ?>
	<tr>
		<td>
			<?=substr($row->date,0,4)?>-<?=substr($row->date,4,2)?>-<?=substr($row->date,6,2)?>
		</td>
		<td>
			<ul>
				<li><a><img src="images/icon_name.gif"><?=$row->name?></a></li>
				<li><a><img src="images/icon_id.gif"><?=$row->id?></a></li>
			</ul>
		</td>
		<td>
			<img src="<?=$imagepath.$row->upfile?>" onError="this.src='../images/no_img.gif'" width="100px" height="auto" onclick="go_product('<?=$productcode?>');" style="cursor:hand;">
		</td>
		<td><span style="cursor:hand;" onclick="JavaScript:ReviewReply('<?=$row->date?>','<?=$productcode?>');"><?=$row->subject?><span></td>
		<td><?=$marks_array[$row->marks]?></td>
	</tr>
	<?$count++;?>
	<?}?>
	<tr>
		<td colspan=5>
		<center>
		<input type="button" value="리뷰 등록하기" onclick="reg_review('<?=$productcode?>');">
		<?if($count > 1){?>
			<?if($offset >0){?>
				<input type="button" value="이전리스트" onclick="more(<?=$offset -3?>,'<?=$productcode?>','<?=$productname?>');">
			<?}?>
			<?if($count ==4){?>
				<input type="button" value="다음리스트" onclick="more(<?=$offset +3?>,'<?=$productcode?>','<?=$productname?>');">
			<?}?>
		<?}?>
		</center></td>
	</tr>
	
	<?if($count == 1){?>
	<script>
		$(".ajax_list").css("display","none");
	</script>
		<div width="100%">해당 상품에 등록된 리뷰가 없습니다</div>
	<?}?>
</table>
