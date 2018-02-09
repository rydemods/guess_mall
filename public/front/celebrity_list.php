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

//$brand_code = $_REQUEST['brand_code'];
$brand_code='4';
function issueList($brand_code){
	$issue = "";
	$issue_array = "";
	$product = "";
	$cntSql = "select count(*) from tblbrand_board a JOIN tblproduct b on a.productcode=b.productcode where a.board_code = {$brand_code}";
	
	$sql = "select * from tblbrand_board a JOIN tblproduct b on a.productcode=b.productcode where a.board_code = {$brand_code} ";
	$sql.= "ORDER BY a.date DESC ";
	$paging = new Tem001_saveheels_Paging($sql,10,10,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$issue[]=$row;
		//exdebug($issue);
	}
	$issue_array[0] = $issue;
	$issue_array[1] = $paging;
	$issue_array[2] = $t_count;
	$issue_array[3] = $gotopage;
	return $issue_array;
}

//리스트 + 페이징 부분 가져오기
$issue_array = issueList($brand_code);
$gotopage = $issue_array[3];
$t_coiunt = $issue_array[2];
$paging = $issue_array[1];
$issue = $issue_array[0];
?>
<!--php끝-->

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

	<div class="new_goods16ea">
		<ul class="list brand">
		<?if($issue){?>
			<?for($i=0; $i<count($issue); $i++){?>
			<li class="in_icon">
				<div class="goods_A">
					<a href="#">
						<p class="img190"><img src="../data/shopimages/product/<?=$issue[$i]->minimage?>" width="190" height="190" alt="" /></p>
						<span class="subject"><?=$issue[$i]->productname?></span>
					</a>
				</div>
				<?if($issue[$i]->option1) $option_chk=3; else $option_chk=1;?>
				<div class="layer_goods_icon" link_url="<?=$Dir.FrontDir.'issue_detail.php?board_num='.$issue[$i]->board_num?>">
					<p class="icon">
						<a class="view" title="상세보기" link_url="<?=$Dir.FrontDir.'issue_detail.php?board_num='.$issue[$i]->board_num?>"></a>
						<a href="#" class="cart" title="장바구니" option_chk="<?=$option_chk?>" cart_chk="<?=$issue[$i]->productcode?>"></a>
					</p>
				</div>
			</li>
			<?}?>
		<?}?>
		</ul>
	</div>

	<form id="paging" name="paging" method=POST action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" name="gotopage" value="<?=$gotopage?>"/>
        <input type="hidden" name="block" value="<?=$block?>"/>
		<input type="hidden" name="brand_code" value="<?=$brand_code?>"/>
	</form>
		<div class="paging">
			<!--
			<a class='on'>1</a>
			<a href="javascript:GoPage(0,2);" onMouseOver="window.status='페이지 : 2';return true">2</a>
			<a href="javascript:GoPage(0,3);" onMouseOver="window.status='페이지 : 3';return true">3</a>	
			-->
			<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
		</div>


<script type="text/javascript">
function GoPage(block,gotopage) {
        document.paging.block.value = block;
        document.paging.gotopage.value = gotopage;
		document.paging.submit();
    }

function CheckForm(gbn,temp2) {


	if(gbn=="ordernow") {
		document.form1.ordertype.value="ordernow";
	}

	if (gbn != "ordernow"){
		document.form1.action="../front/confirm_basket.php";
		document.form1.target="confirmbasketlist";
		document.form1.productcode.value= temp2;
		window.open("about:blank","confirmbasketlist","width=401,height=309,scrollbars=no,resizable=no, status=no,");
		document.form1.submit();
		document.back.submit();
	}

}

function change_quantity(gbn) {
	tmp=document.form1.quantity.value;
	if(gbn=="up") {
		tmp++;
	} else if(gbn=="dn") {
		if(tmp>1) tmp--;
	}
	var cons_qu = $("#constant_quantity").val();
	if (cons_qu != "" && cons_qu != "0"){
		if (cons_qu<tmp){
			alert('재고량이 부족 합니다.');
			return;
		}
	} else if(cons_qu == "0") {
		alert('품절 입니다.');
		return;
	}

			var tmp_price = $("#ID_goodsprice").val();
		tmp_price = Number(tmp_price)*Number(tmp);
		setDeliPrice(tmp_price,tmp);
		$("#result_total_price").html(jsSetComa(tmp_price));
		document.form1.quantity.value=tmp;
	
}


$(document).ready(function() {
	//Default Action
	var defaultType = 0;
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li").each(function(){
		if($(this).attr("class")=="active"){
			defaultType = 1;
			var tabId = $(this).find("a").attr("href");
			$(tabId).show();
		}
	});
	if(defaultType == 0){
		$("ul.tabs li:first").addClass("active").show(); //Activate first tab
		$(".tab_content:first").show(); //Show first tab content
	}

	//On Click Event
	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		return false;
	});

	$('.new_goods4ea ul.list li').mouseenter(function(){
	$(this).find('.layer_goods_icon').show();
	});
	$('.new_goods16ea ul.list li').mouseenter(function(){
	$(this).find('.layer_goods_icon').show();
	});
	$('.in_icon').mouseleave(function(){
	$('.layer_goods_icon').hide();
	});

});

</script>
<script>
$(document).ready(function(){

  $(".layer_goods_icon").on("click",function(e){
    	var target = e.target
    	if($(target).attr("class") == "cart" || $(target).attr("class") == "view" ) return; 
    	location.href = $(this).attr("link_url");
    });
    
    $(".cart").on("click",function(e){
		var chkOption = $(this).attr("option_chk");
    	var chkLink = $(this).attr("cart_chk");
    	if(chkOption == 1){
			CheckForm('',chkLink);
		}else if(chkOption == 3){
	    	$("#productlist_basket").attr("action","../front/productlist_basket.php");
	    	$("#productlist_basket").attr("target","basketOpen");
	    	$("#productcode2").val(chkLink);
			window.open("","basketOpen","width=440,height=420,scrollbars=no,resizable=no, status=no,");
			$("#productlist_basket").submit();
		} 
    });
    
    $(".view").on("click",function(){
    	location.href = $(this).attr("link_url");
    });
});
</script>

</div>

<form name="productlist_basket" id="productlist_basket">
<input type="hidden" name="productcode2" id="productcode2">
</form>

<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
</HTML>
