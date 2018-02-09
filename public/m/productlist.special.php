<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

$dispLine = 2;				//���� ��ǰ�� ���� ���μ�
$dispcnt = $dispLine*3;		//���μ��� ���� ��ǰ��
$spe_code = ($_POST['spe_code'])?$_POST['spe_code']:"1";

//���� ����
$sort = $_POST['sort'];
switch($sort){
	case "new":
		$orderby = "a.regdate desc";
		break;
	case "low_price":
		$orderby = "a.consumerprice asc";
		break;
	case "high_price":
		$orderby = "a.consumerprice desc";
		break;
	case "product_name":
		$orderby = "a.productname asc";
		break;

	default:
		$orderby = "a.regdate desc";
		break;
}

//////////////////�� ������ �κ� (lib.inc.php ���� ������)///////////////////
	$sp_prcode="";
	$sql = "SELECT special_list FROM tblspecialmainMobile ";
	$sql.= "WHERE special='".$spe_code."' ";
	$result=pmysql_query($sql,get_mdb_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
		//debug($sp_prcode);
	pmysql_free_result($result);

	$sql = "SELECT a.pridx, a.productcode, a.productname, a.sellprice, a.quantity, ";
	$sql.= "a.tinyimage, a.maximage, a.date, a.etctype, a.consumerprice, a.tag, a.selfcode FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY $orderby ";
	$sql.= "LIMIT ".$dispcnt;
	$result=pmysql_query($sql,get_mdb_conn());
//////////////////������ �κ� ��////////////////////////////


//$result=getMainproductlist($spe_code,6);

$t_count = getCountMainproductlistMobile($spe_code);

include ("header.inc.php");
$category_name = array("�Ż�ǰ","�α��ǰ","��õ��ǰ","Ư����ǰ");
$subTitle = $category_name[$spe_code-1]."(".number_format($t_count)."��)";
include ("sub_header.inc.php");
?>
<article class="list_wrap">
  <section>
 	<h3 class="blind">��ǰ ī�װ�, ����</h3>
	<ul class="product-sort">
		<li class="sub-category">
		<?php// if($t_count>0) : ?>
			<?=$_cateselectlist?>

	<form name="frmSort" id="frmSort" method="POST">
			<select name="sort" onchange="chgSort();">
				<option value="new" <?if($sort=="new")echo"selected";?>>�ֽż�</option>
				<option value="low_price" <?if($sort=="low_price")echo"selected";?>>�������ݼ�</option>
				<option value="high_price" <?if($sort=="high_price")echo"selected";?>>�������ݼ�</option>
				<option value="product_name" <?if($sort=="product_name")echo"selected";?>>��ǰ���</option>
			</select>
	</form>
		<?php //endif; ?>
		</li>
	</ul>
  <div class="list_sorting">
       <ul id="listUL">


		<?php while ($row = pmysql_fetch_object($result)) : ?>
		<?php
		if (!$row->maximage) {
			$row->maximage = $Dir."images/acimage.gif";
		} else {
			$row->maximage = $imagepath.$row->maximage;
		}
		$row->reserve=getReserveConvert($row->reserve,$row->reservetype,$row->sellprice,"Y");

		$r_cnt=0;
		$r_marks=0;
		$r_totscore=0;
		if($_data->review_type=="Y" || $_data->review_type=="A") {
			$sql = "SELECT COUNT(*) as r_cnt, SUM(marks) as r_marks FROM tblproductreview ";
			$sql.= "WHERE productcode='".$row->productcode."' ";
			if($_data->review_type=="A") $sql.= "AND display='Y' ";
			$sql.= "GROUP BY productcode ";
			$result2=pmysql_query($sql,get_mdb_conn());
			$row2=pmysql_fetch_object($result2);
			pmysql_free_result($result2);
			$r_cnt=(int)$row2->r_cnt;
			$r_marks=(int)$row2->r_marks;
			$r_totscore=0;

			if($r_cnt>0) {
				$r_totscore=ceil(($r_marks*20)/$r_cnt);
			}
		}
		?>

		<li><a href="productdetail.php?pridx=<?=$row->pridx?>"><div class="thumb">
		<div><img src="<?=$row->maximage?>" alt="" /></div>
		<span class="name"><?=strip_tags($row->productname)?></span> <span class="price"><?=number_format($row->consumerprice);?>��</span>
		</div></a></li>

		
		<?php endwhile; ?>

       </ul>
   </div>

    <div class="more_btn">
    <?php if($t_count>$dispcnt): ?>
	<input type="button" value="������" onclick="morePrd();"/>
	<?php endif;?>
 </div>
  </section>
<script type="text/javascript">
	var displayLine = <?=$dispLine?>;	//����Ǵ� ���μ�
	var offsetLine = displayLine;		//���� �������� �ִ� ���μ�
	
	function morePrd(){
		$.post('ajaxproductlist.special.php',{spe_code:"<?=$spe_code?>",dispaly:displayLine,offsetLine:offsetLine,sort:"<?=$sort;?>"},function(data){
			data = trim(data);
			if(data){
				$("#listUL").html($("#listUL").html()+data);
				offsetLine+=displayLine;
			}else{
				alert("���̻� ��ǰ�� �����ϴ�.");
			}
		});
	}
	
	function chgSort(){
		$("#frmSort").submit();
	}
</script>

</article>


<? include ("footer.inc.php"); ?>