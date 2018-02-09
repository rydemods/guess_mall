<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$imagepath=$Dir.DataDir."shopimages/etc/main_logo.gif";
$flashpath=$Dir.DataDir."shopimages/etc/main_logo.swf";

if (file_exists($imagepath)) {
	$mainimg="<img src=\"".$imagepath."\" border=\"0\" align=\"absmiddle\">";
} else {
	$mainimg="";
}
if (file_exists($flashpath)) {
	if (preg_match("/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/",$_data->shop_intro,$match)) {
		$width=$match[1];
		$height=$match[2];
	}
	$mainflash="<script>flash_show('".$flashpath."','".$width."','".$height."');</script>";
} else {
	$mainflash="";
}
$pattern=array("(\[DIR\])","(\[MAINIMG\])","/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/");
$replace=array($Dir,$mainimg,$mainflash);
$shop_intro=preg_replace($pattern,$replace,$_data->shop_intro);


$mb_qry="select * from tblmainbannerimg order by banner_sort";

$mb_result=pmysql_query($mb_qry);
while($mb_data=pmysql_fetch_object($mb_result)){
	$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_img"]=$mb_data->banner_img;
	$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_title"]=$mb_data->banner_title;
	$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_hidden"]=$mb_data->banner_hidden;
	$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_category"]=$mb_data->banner_category;
	$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_link"]=$mb_data->banner_link;
	$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_number"]=$mb_data->banner_number;
	$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_t_link"]=$mb_data->banner_t_link;
	$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_no"]=$mb_data->no;
}


if (stripos($shop_intro,"<table")!==false || strlen($mainflash)>0)
	$main_banner=$shop_intro;
else
	$main_banner=nl2br($shop_intro);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<TITLE><?=$_data->shoptitle?></TITLE>
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
</head>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<?php 
include ($Dir.MainDir.$_data->menu_type.".php");
########################## 인트로 #############################

?>
<!-- start container -->
	<div id="container">

	<?include "{$Dir}/templet/recipe/side.php"?>
	<!--contents start-->
	<div class="con_cen">
			<!-- visual-->
			<div class="visual">


			<!-- slide start -->
				<div style="position:relative;">
					<div class="tabs1">
					<?
					$i=1;
					foreach($mainbanner[top_rolling] as $k){
						if($k["banner_hidden"]){
					?>
						<a class="customLink1 tab<?=$i?>" rel="<?=$i?>"><?=$k["banner_title"]?></a>
					<?$i++;}}?>
					</div>
					<div id="slider1" class="slider1">
						<ul>				
						<?
						foreach($mainbanner[top_rolling] as $k){
							if($k["banner_hidden"]){
						?>
						<li><?if($k["banner_link"]!=''){?><a href="<?=$k["banner_link"]?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$k["banner_img"]?>" alt="visual" /></a></li>
						
						<?}}?>
							
						</ul>
					</div>
				</div>
			</div>
			<!--// visual -->
			<ul class="banner" style="margin-top:-2px">
				<?
				$i=1;
				foreach($mainbanner[top_bottom] as $k){
					if($k["banner_hidden"]){
						if($i=="1")$classbanner="class='banner1'";
						else $classbanner="";
				?>
					<li <?=$classbanner?>> <?if($k["banner_link"]!=''){?><a href="<?=$k["banner_link"]?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$k["banner_img"]?>" alt="banner" /></a></li>
												
				<?$i++;}}?>
				
			</ul>
	</div><!--//end con_cen-->

	<div class="con_right">
		<dl>
			<dd><?if($mainbanner[top_right][1]["banner_link"]!=''){?><a href="<?=$mainbanner[top_right][1]["banner_link"]?>" <?=strstr($mainbanner[top_right][1]["banner_link"],"http")?"target='_blank'":""?>><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$mainbanner[top_right][1]["banner_img"]?>" alt="product" width='220' height=305/></a></dd>
			<dd><?if($mainbanner[top_right][6]["banner_link"]!=''){?><a href="<?=$mainbanner[top_right][6]["banner_link"]?>" <?=strstr($mainbanner[top_right][6]["banner_link"],"http")?"target='_blank'":""?>><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$mainbanner[top_right][6]["banner_img"]?>" alt="product" /></a></dd>
			
		<?/*?>
			<?
			$i=1;
			foreach($mainbanner[top_right] as $k){
				if($k["banner_hidden"]){
					if($i=="1"){
						
			?>
				<dt><?if($k["banner_link"]!=''){?><a href="<?=$k["banner_link"]?>" class="over"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$k["banner_img"]?>" alt="피톤치드 festival"/></a></dt>
					<?}else{?>
				<dd><?if($k["banner_link"]!=''){?><a href="<?=$k["banner_link"]?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$k["banner_img"]?>" alt="product" /></a></dd>
					<?}
			$i++;}
			}?>
		<?*/?>	
		</dl>
		
	</div>

   <div class="hot_now">
	 <dl>
	 	<dt><img src="<?=$Dir?>image/main/hot_now.jpg" alt="hot now" /></dt>
		<?
			foreach($mainbanner[hot] as $k){
				if($k["banner_hidden"]){
			?>
				<dd><?if($k["banner_link"]!=''){?><a href="<?=$k["banner_link"]?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$k["banner_img"]?>" alt="banner" /></a></dd>
				
			<?}}?>
		
	 </dl>
   </div>

   <div class="issue_item">
	<h3><img src="<?=$Dir?>image/main/issue_item.jpg" alt="issue item" /></h3>
	<!--<p class="more"><a href="#" class="over"><img src="<?=$Dir?>image/main/more_btn.gif" alt="더보기" /></a></p>-->

	  <div>
		<ul id="tabMenu1" class="tabZone">
			<?
			foreach($mainbanner[issue] as $k){
				if($k["banner_hidden"]){
			?>
			<li><a href="<?=$k["banner_t_link"]?>" class="tab1"><img src="<?=$Dir?>image/main/issue_0<?=$k["banner_number"]+1?><?if($k["banner_number"]=="0"){echo "_over";}?>.gif" /></a>
				<ul>
					<li>
					<?
					########################## issue #############################
					$sql = "SELECT issue_list FROM tblissuemain ";
					$sql.= "WHERE issue_number='".$k["banner_no"]."' ";
					$result=pmysql_query($sql,get_db_conn());
					$sp_prcode="";
					if($row=pmysql_fetch_object($result)) {
						$sp_prcode=str_replace(',','\',\'',$row->issue_list);
					}
					pmysql_free_result($result);

					if(strlen($sp_prcode)>0) {
						$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.option1, a.option2, a.option_quantity, ";
						$sql.= "a.maximage, a.date, a.etctype, a.consumerprice, a.reserve, a.reservetype, a.tag, a.selfcode FROM tblproduct AS a ";
						$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
						$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
						$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
						$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
						$sql.= "LIMIT 3";
						$result=pmysql_query($sql,get_db_conn());
						while($row=pmysql_fetch_object($result)){
								$temp = $row->option1;
								$tok = explode(",",$temp);
								$goods_count=count($tok);
								
								$check_optea='0';
								if($goods_count>"1"){
									$check_optea="1";	
								}
								
								$optioncnt = explode(",",ltrim($row->option_quantity,','));
								$check_optout=array();
								$check_optin=array();
								for($gi=1;$gi<$goods_count;$gi++) {
									
									if(strlen($row->option2)==0 && $optioncnt[$gi-1]=="0"){ $check_optout[]='1';}
									else{  $check_optin[]='1';}
								}

						
					?>
						<dl>
							<dt><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>"><img src="<?=$Dir.DataDir?>shopimages/product/<?=urlencode($row->maximage)?>" alt="product" style="width:130px; height:130px;"/></a></dt>
							<dd class="name"><?=viewproductname(mb_strimwidth($row->productname,0,25,'...','euc_kr'),$row->etctype,$row->selfcode)?></dd>
							<dd class="price">
								<?
								echo dickerview_tem001($row->etctype,number_format($row->sellprice)." 원");
								if ($_data->ETCTYPE["MAINSOLD"]=="Y" && ($row->quantity=="0" || (count($check_optin)=='0' && $check_optea))) echo soldout();
								?>
							</dd>
						</dl>
					<?	}
					}
					?>
						<div class="recipe_new">
							<dl>								
								<dd><?if($k["banner_link"]!=''){?><a href="<?=$k["banner_link"]?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$k["banner_img"]?>" alt="product" /></a></dd>
							</dl>
						</div>
					</li>
				</ul>
			</li>
				
			<?}}?>
			
		</ul>
	</div>
	</div><!-- //end issue item -->


<?

########################## 신상품 #############################
$sql = "SELECT special_list FROM tblspecialmain ";
$sql.= "WHERE special='1' ";
$result=pmysql_query($sql,get_db_conn());
$sp_prcode="";
if($row=pmysql_fetch_object($result)) {
	$sp_prcode=str_replace(',','\',\'',$row->special_list);
}
pmysql_free_result($result);

if(strlen($sp_prcode)>0) {
	$main_newprdt=explode("|",$_data->main_newprdt);
	$main_new_num=$main_newprdt[0];
	$main_new_cols=$main_newprdt[1];
	$main_new_type=$main_newprdt[2];
	$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.option1, a.option2, a.option_quantity, ";
	$sql.= "a.maximage, a.date, a.etctype, a.consumerprice, a.reserve, a.reservetype, a.tag, a.selfcode FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
	$sql.= "LIMIT ".$main_new_num;
	$result=pmysql_query($sql,get_db_conn());
	
?>
	
	<div class="new_arrivals">
	
		<div class="tit">
			<h3><img src="<?=$Dir?>image/main/new_arrival_tit.gif" alt="new arrivals" /></h3>
			<!--
			<div class="plus"><a href="#" class="over"><img src="<?=$Dir?>image/main/plus_btn.gif" alt="plus" /></a></div>
			-->
        </div>

	<div class="new_prd" style="position:relative;">
		<div id="slider2" class="slider2">
			<ul>

<?
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		$i++;
		
		$temp = $row->option1;
		$tok = explode(",",$temp);
		$goods_count=count($tok);
		
		$check_optea='0';
		if($goods_count>"1"){
			$check_optea="1";	
		}
		
		$optioncnt = explode(",",ltrim($row->option_quantity,','));
		$check_optout=array();
		$check_optin=array();
		for($gi=1;$gi<$goods_count;$gi++) {
			
			if(strlen($row->option2)==0 && $optioncnt[$gi-1]=="0"){ $check_optout[]='1';}
			else{  $check_optin[]='1';}
		}


		//타임세일 가격변경
		$timesale_data=$timesale->getPdtData($row->productcode);
		$time_sale_now='';
		if($timesale_data['s_price']>0){
			$time_sale_now='Y';
			$row->sellprice = $timesale_data['s_price'];
		}
		//타임세일 가격변경
			
		if (strlen($row->maximage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->maximage)) {
?>
			<li>
				<dl>
					<dt><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>"><img src="<?=$Dir.DataDir?>shopimages/product/<?=urlencode($row->maximage)?>" alt="product" width=135 height=130/></a></dt>
					<dd class="name"><?=viewproductname(mb_strimwidth($row->productname,0,25,'...','euc_kr'),$row->etctype,$row->selfcode)?></dd>
					<dd class="price">
						<?
						echo dickerview_tem001($row->etctype,number_format($row->sellprice)." 원");
						if ($_data->ETCTYPE["MAINSOLD"]=="Y" && ($row->quantity=="0" || (count($check_optin)=='0' && $check_optea))) echo soldout();
						?>
						
					</dd>
				</dl>
			</li>
		
<?			
		} else {
?>			
			<li><img src="<?=$Dir?>images/no_img.gif" alt="상품" width=240 height=240 /></li>
<?
		}
	}
?>
			</ul>
		</div>
	</div>
	</div><!-- //end new_arrivals -->
<?
}

########################## 신상품 END #############################
?>
   
   <div class="best">
	<h3><img src="<?=$Dir?>image/main/best_tit.gif" alt="category best" /></h3>
	<!--<p class="more"><a href="#" class="over"><img src="<?=$Dir?>image/main/best_plus_btn.gif" alt="더보기" /></a></p>-->
	

	  <div class="best1">
		<ul id="tabMenu2" class="tabZone">
<?
	$cate_array=array("003000000000","004000000000","006000000000","002000000000","034000000000","009000000000","010000000000","007000000000","001000000000");
	
	$i=1;
	foreach($cate_array as $k){
		
?>
			<li class="first"><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$k?>" class="tab1"><img src="<?=$Dir?>image/main/best_menu_btn_0<?=$i?><?if($i=="1"){echo "_over";}?>.gif" /></a>
				<ul>
					<li>
					   <div class="best_left">
					  
						<?
							$cate_qry="select * from tblmainbannerimg where banner_category='".$k."' order by banner_sort";
							$cate_result=pmysql_query($cate_qry);
							$cn=1;
							while($cate_data=pmysql_fetch_object($cate_result)){
							if($cate_data->banner_hidden){
								if($cn<=3){
									if($cn==1){
						?>
									<dl class="top">
									<dt class="blind">product</dt>
									<dd><?if($cate_data->banner_link!=''){?><a href="<?=$cate_data->banner_link?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$cate_data->banner_img?>" alt="product" /></a></dd>
						<?			}else if($cn==3){?>
									<dd><?if($cate_data->banner_link!=''){?><a href="<?=$cate_data->banner_link?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$cate_data->banner_img?>" alt="product" /></a></dd>
									</dl>
						<?			}else{?>
									<dd><?if($cate_data->banner_link!=''){?><a href="<?=$cate_data->banner_link?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$cate_data->banner_img?>" alt="product" /></a></dd>
						<?			}
									
								}else{
									if($cn==4){
						?>
									<dl class="bottom">
									<dt class="blind">product</dt>
									<dd><?if($cate_data->banner_link!=''){?><a href="<?=$cate_data->banner_link?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$cate_data->banner_img?>" alt="product" /></a></dd>
						<?			}else if($cn==5){?>
									<dd class='right'><?if($cate_data->banner_link!=''){?><a href="<?=$cate_data->banner_link?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$cate_data->banner_img?>" alt="product" /></a></dd>
									</dl>
						<?			}
								}
							$cn++;
							}
							}
						?>
						
						</div>
					
						<div class="best_right">
						<?
						$sql = "SELECT special_list FROM tblspecialcode ";
						$sql.= "WHERE code = '{$k}' AND special='2' ";
						$result = pmysql_query($sql,get_db_conn());
						if($row = pmysql_fetch_object($result)){
							$sp_prcode=str_replace(',','\',\'',$row->special_list);
							
						
						$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, option1, option2, option_quantity, ";
						$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,maximage,selfcode,assembleuse ";
						$sql.= "FROM tblproduct ";
						$sql.= "WHERE productcode IN ('{$sp_prcode}') ORDER BY FIELD(productcode,'".$sp_prcode."') limit 4";
						$result = pmysql_query($sql,get_db_conn());

						while($row=pmysql_fetch_object($result)) {
							//타임세일 가격변경
							$timesale_data=$timesale->getPdtData($row->productcode);
							$time_sale_now='';
							if($timesale_data['s_price']>0){
								$time_sale_now='Y';
								$row->sellprice = $timesale_data['s_price'];
							}
							//타임세일 가격변경


							$temp = $row->option1;
							$tok = explode(",",$temp);
							$goods_count=count($tok);
							
							$check_optea='0';
							if($goods_count>"1"){
								$check_optea="1";	
							}
							
							$optioncnt = explode(",",ltrim($row->option_quantity,','));
							$check_optout=array();
							$check_optin=array();
							for($gi=1;$gi<$goods_count;$gi++) {
								
								if(strlen($row->option2)==0 && $optioncnt[$gi-1]=="0"){ $check_optout[]='1';}
								else{  $check_optin[]='1';}
							}

						?>
						<dl>
						<dt><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>"><img src="<?=$Dir.DataDir?>shopimages/product/<?=urlencode($row->maximage)?>" alt="product" width=190 height=190/></a></dt>
						<dd class="name"><?=viewproductname(mb_strimwidth($row->productname,0,35,'...','euc_kr'),$row->etctype,$row->selfcode)?></dd>
						<dd class="price">
							<?
							echo dickerview_tem001($row->etctype,number_format($row->sellprice)." 원");
							if ($_data->ETCTYPE["MAINSOLD"]=="Y" && ($row->quantity=="0" || (count($check_optin)=='0' && $check_optea))) echo soldout();
							?>
							
						</dd>
						</dl>
						
						<?}}?>
						
					   </div>
					</li>
				</ul>
			</li>
<?$i++;}?>
		</ul>
	</div>
   </div><!-- //end best -->

   <div class="ecoWrap">
	   <div class="eco">
			<ul>
				<?if($mainbanner[bottom][1]["banner_hidden"]){?>
				<li class="left"><?if($mainbanner[bottom][1]["banner_link"]!=''){?><a href="<?=$mainbanner[bottom][1]["banner_link"]?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$mainbanner[bottom][1]["banner_img"]?>" alt="academy banner" /></a></li>
				<?}?>
				
				<?if($mainbanner[bottom][2]["banner_hidden"]){?>
				<li class="month">
				<!--div style="position:relative;">
				<div><?for($i=0; $i< strlen((int)date("m")); $i++){?><img src="/image/main/month_no/<?=substr((int)date("m"),$i,1)?>.png" style="float:left;"> <?}?><img src="/image/main/month_no/mo.png"></div>
				<div style="clear:both;"><img src="/image/main/this_month.png"></div>
				<div  style="position:absolute; top:0px; z-index:100;"><?if($mainbanner[bottom][2]["banner_link"]!=''){?><a href="<?=$mainbanner[bottom][2]["banner_link"]?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$mainbanner[bottom][2]["banner_img"]?>" alt="이달의 작품"/></a></div>
				</div-->
				

					<table width="283" border="0" cellspacing="0" cellpadding="0"  class="monthly_work">
					  <tr>
						<td width="123">
						
						<dl class="monthwarp">
					  <dt>
					  <script language="JavaScript">
					function gdate(){
						var sImg = "<img src=/image/main/month_no/";
						var eImg = ".png border=0>";
						var now = new Date();
						var month = (now.getMonth() + 1);
							now = null;     month += ""; 
						var text = "";
						for (var i = 0; i < month.length; ++i) {
							text += sImg + month.charAt(i) + eImg;
						}
						document.write(text);
					}
					</script>
					<script language="JavaScript">gdate();</script>
					  <img src="/image/main/month_no/mo.png" alt="이달의 작품" />
					  </dt>

						<dd><a href="#"><img src="/image/main/this_month.png" alt="이달의 작품" /></a></dd>
					</dl>
						
						</td>
						<td width="160"><?if($mainbanner[bottom][2]["banner_link"]!=''){?><a href="<?=$mainbanner[bottom][2]["banner_link"]?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$mainbanner[bottom][2]["banner_img"]?>" alt="이달의 작품"/></a></td>
					  </tr>
					</table>
				</li>
				<?}?>
				
				<?if($mainbanner[bottom][3]["banner_hidden"]){?>
				<li><?if($mainbanner[bottom][3]["banner_link"]!=''){?><a href="<?=$mainbanner[bottom][3]["banner_link"]?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$mainbanner[bottom][3]["banner_img"]?>" alt="best blogger" /></a></li>
				<?}?>

			</ul>
	   </div>
	   <div class="reviewWrap">
	   <div class="review">
			<h3><img src="<?=$Dir?>image/main/best_review_tit.gif" alt="best review" /></h3>
			<dl class="item">
				<dt><a href="<?=$Dir.FrontDir?>reviewall.php" class="over"><img src="<?=$Dir?>image/main/item_review_tit.gif" alt="item review" /></a></dt>
				<?
				//$p_review_qry="select a.content,b.maximage,b.productname, b.productcode from tblproductreview a left join tblproduct b on (a.productcode=b.productcode) where a.display='Y' order by a.date desc limit 3";
				$p_review_qry="select a.content,b.maximage,b.productname, b.productcode from tblproductreview a left join tblproduct b on (a.productcode=b.productcode) where best_type='1' order by a.date desc limit 3";
				
				$p_review_result=pmysql_query($p_review_qry);
				?>
				<!-- slide start -->
				<dd style="position:relative;">
				<div id="slider3" class="slider3">
				<ul>
				<?
					
					while($p_review_data=pmysql_fetch_object($p_review_result)){
						
				?>
					<li>
						<dl>
						<dt class="blind">review</dt>
						<dd><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$p_review_data->productcode?>"><img src="<?=$Dir.DataDir?>shopimages/product/<?=urlencode($p_review_data->maximage)?>" alt="product" style="width:214px; height:136px;"/></a></dd>
						<dd class="detail_word"><span class="name"><?=$p_review_data->productname?></span><br />
						<?=mb_strimwidth($p_review_data->content,0,50,'...','euc_kr')?></dd>
						</dl>
					</li>
				<?}?>
				
				<!--
					<li>
						<dl>
						<dt class="blind">review</dt>
						<dd><a href="#"><img src="<?=$Dir?>image/main/item_review_img1.jpg" alt="image" /></a><dd>
						<dd><span class="name">워셔블 클렌징 오일</span><br />
						레시피 약간 변형했지만 아주 맘에 들어요ㅎㅎ</dd>
						</dl>
					</li>
					<li>
						<dl>
						<dt class="blind">review</dt>
						<dd><a href="#"><img src="<?=$Dir?>image/main/item_review_img2.jpg" alt="image" /></a><dd>
						<dd><span class="name">워셔블 클렌징 오일</span><br />
						레시피 약간 변형했지만 아주 맘에 들어요ㅎㅎ</dd>
						</dl>
					</li>
					-->
				</ul>
				
				</div>
				</dd>
			</dl>
				<?
				$recipe->list_size=3;
				$rlist = $recipe->getRecipeCommentList2();

				?>

			<dl class="recipe">
				<dt><a href="/front/recipe_review.php" class="over"><img src="<?=$Dir?>image/main/recipe_review_tit.gif" alt="recipe review" /></a></dt>
				<!-- slide start -->
				<dd style="position:relative;">
				<div id="slider4" class="slider4">
				<ul>
				<?
				foreach($rlist as $rdata){
				$link = "/front/recipe_view.php?no=".$rdata[no]."&listUrl=".urlencode("/front/recipe.php");
				?>
					<li>
						<dl>
						<dt class="blind">recipe</dt>
						<dd><a href="<?=$link?>"><img src="<?=$rdata[timg_src]?>" alt="image" width="214" height="136"/></a><dd>
						<dd class="detail_word"><span class="name"><?=$rdata[subject]?></span><br />
						<?=$rdata[comment]?></dd>
						</dl>
					</li>
				<?}?>
				</ul>
				
				</div>
				</dd>
			</dl>
	   </div>
	   </div>
   </div><!-- //end ecoWrap -->

   <div class="info_menu">
	  <ul>
	  	<li><a href="<?=$Dir.FrontDir?>company.php" class="rollover" ><img src="<?=$Dir?>image/main/infor_01.gif" alt="회사소개" /><img src="<?=$Dir?>image/main/infor_01_over.gif" alt="회사소개" class="over" /></a></li>
		<li><a href="<?=$Dir.BoardDir?>board.php?board=Paper" class="rollover" ><img src="<?=$Dir?>image/main/infor_02.gif" alt="성적서" /><img src="<?=$Dir?>image/main/infor_02_over.gif" alt="회사소개" class="over" /></a></li>
		<li><a href="<?=$Dir.BoardDir?>board.php?board=story" class="rollover" ><img src="<?=$Dir?>image/main/infor_03.gif" alt="어느 별에서 왔니" /><img src="<?=$Dir?>image/main/infor_03_over.gif" alt="회사소개" class="over" /></a></li>
		<li><a href="<?=$Dir.BoardDir?>board.php?board=choice" class="rollover" ><img src="<?=$Dir?>image/main/infor_04.gif" alt="쏘울 팩토리" /><img src="<?=$Dir?>image/main/infor_04_over.gif" alt="회사소개" class="over" /></a></li>
		<li><a href="<?=$Dir.BoardDir?>board.php?pagetype=view&num=93315&board=notice&block=&gotopage=1&search=&s_check=" class="rollover" ><img src="<?=$Dir?>image/main/infor_05.gif" alt="학교 및 비영리 단체" /><img src="<?=$Dir?>image/main/infor_05_over.gif" alt="회사소개" class="over" /></a></li>
		<li><a href="<?=$Dir.BoardDir?>board.php?board=commongsoon" class="rollover" ><img src="<?=$Dir?>image/main/infor_06.gif" alt="품절입고 게시판" /><img src="<?=$Dir?>image/main/infor_06_over.gif" alt="회사소개" class="over" /></a></li>
		<li><a href="<?=$Dir.BoardDir?>board.php?board=qana" class="rollover" ><img src="<?=$Dir?>image/main/infor_07.gif" alt="고객문의 게시판" /><img src="<?=$Dir?>image/main/infor_07_over.gif" alt="회사소개" class="over" /></a></li>
		<li><a href="<?=$Dir.BoardDir?>board.php?board=studyb" class="rollover" ><img src="<?=$Dir?>image/main/infor_08.gif" alt="레시피 문의 게시판" /><img src="<?=$Dir?>image/main/infor_08_over.gif" alt="회사소개" class="over" /></a></li>
	  </ul>
   </div><!-- //end ecoWrap -->

  </div><!-- //end container -->



<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>
<?//이벤트 팝업창 (main???.php에만 include)?>
<?php 
include($Dir."lib/eventlayer.php") 
?>
</div>
</BODY>
</HTML>
