<?
if($type!="main"){
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");



include ("header.inc.php");
}
$subTitle = "BRAND";
include ("sub_header.inc.php");
?>


<!--<div class="main">
	<article class="mypage">
	<?
	$myp_no="1";
	$subTitle = "찾아오시는 길";
	include_once("brand_sub_header.inc.php");
	?>
	</article>
</div>
-->
<section>
	<ul class="cate_list">

<?
	//$brand_array = "348,349,350,133,134,136,137";
	/*카테고리*/
	$sql = "
		SELECT bridx,brandname
		FROM tblproductbrand
		ORDER BY brandname
	";
	$result=pmysql_query($sql,get_mdb_conn());
	$cnt=1;
	while($row=pmysql_fetch_object($result)) {
		$sql2 = "
			SELECT
			COUNT(DISTINCT b.c_productcode) as cnt 
			FROM tblproduct a 
			JOIN tblproductlink b ON a.productcode=b.c_productcode
			WHERE a.brand = '{$row->bridx}' 
		";
		$res2 = pmysql_query($sql2,get_db_conn());
		$row2 = pmysql_fetch_object($res2);
?>
		<li>
			<a href="javascript:brandsubmit('<?=$row->bridx?>')"><?=$row->brandname;?> (<?=$row2->cnt?>)</a>
		</li>
<?	$cnt++;
	}

	pmysql_free_result($result);

?>


	</ul>
</section>

<script type="text/javascript">

function brandsubmit(idx){
	window.location.href = "productlist.php?bridx="+idx;
	//window.location.href = "productlist.php?code="+"001004000000";
}

</script>

<?
if($type!="main"){
 include ("footer.inc.php");
}
?>