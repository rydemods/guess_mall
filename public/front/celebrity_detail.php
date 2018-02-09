<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$sql="SELECT agreement FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$agreement=stripslashes($row->agreement);
}
pmysql_free_result($result);

if(ord($agreement)==0) {
	$agreement=file_get_contents($Dir.AdminDir."agreement.txt");
	$agreement="<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td  style=\"padding:10\">{$agreement}</td></tr></table>";
}

$pattern=array("[SHOP]","[COMPANY]");
$replace=array($_data->shopname, $_data->companyname);
$agreement = str_replace($pattern,$replace,$agreement);

$board_num = $_REQUEST['board_num'];

function issueDetail($board_num){
	$sql = "select * from tblbrand_board where board_num={$board_num}";
	$result = pmysql_query($sql,get_db_conn());
	return $issue = pmysql_fetch_object($result);
}

function issueItem($board_num){
	$issue_item = "";
	$sql = "select * from tblbrand_boarditem a join tblproduct b on a.productcode=b.productcode where a.board_num={$board_num}";
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$issue_item[] = $row;
	}
	return $issue_item;
}

$issue = issueDetail($board_num);
$issue_item = issueItem($board_num);
//exdebug($issue_item);
//exdebug($issue);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - ISSUE</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<!-- start contents -->
<div class="containerBody sub_skin">
	<h3 class="title">
		CELEBRITY
		<p class="line_map"><a>홈</a> &gt; <a>BRAND</a> &gt; <span>CELEBRITY</span></p>
	</h3>

	<div class="brand_detail">
		<div class="comment">
			<!--<img src="../front/images/issue_img_detail.jpg" alt="" />
			<p class="mt_20">
				인기스타들의 HOT ITEM! <br >
				스타들도 사랑하는 IT BAG을 지금 만나보세요
			</p>
			-->
			<?=$issue->board_content?>
		</div>

		<div class="detail_list">
			<?for($i=0; $i<count($issue_item); $i++){?>
			<a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$issue_item[$i]->productcode?>">
				<img src="../data/shopimages/product/<?=$issue_item[$i]->minimage?>" alt="" class="pic130" />
				<span class="subject"><?=$issue_item[$i]->productname?></span>
				<span class="price"><?=number_format($issue_item[$i]->sellprice)?>원</span>
			</a>
			<?}?>
			<!--
			<a href="#">
				<img src="../data/shopimages/product/product_20.jpg" alt="" class="pic130" />
				<span class="subject">Norah Medium O5SBTT27_BN Norah Medium O5SBTT27_BNNorah Medium O5SBTT27_BNNorah Medium O5SBTT27_BN</span>
				<span class="price">398,000원</span>
			</a>
			<a href="#">
				<img src="../data/shopimages/product/product_21.jpg" alt="" class="pic130" />
				<span class="subject">Yany O4FBCL21_BK</span>
				<span class="price">168,000원</span>			
			</a>
			<a href="#">
				<img src="../data/shopimages/product/product_22.jpg" alt="" class="pic130" />
				<span class="subject">Sydney Large O4FBSD010_CM</span>
				<span class="price">428,000원</span>			
			</a>
			<a href="#">
				<img src="../data/shopimages/product/product_23.jpg" alt="" class="pic130" />
				<span class="subject">Chelsea Phone Wallet O4FBSL0..</span>
				<span class="price">219,000원</span>			
			</a>
			<a href="#">
				<img src="../data/shopimages/product/product_24.jpg" alt="" class="pic130" />
				<span class="subject">Yany O4FBCL21_BK</span>
				<span class="price">168,000원</span>			
			</a>
			-->
		</div>
	</div>

</div>



<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
</HTML>
