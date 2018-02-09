<? 
	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	$homeBanner = homeBannerList();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko" >

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
	<meta name="description" content="XNGOLF" />
	<meta name="keywords" content="" />

	<title>엑스넬스 코리아</title>

	
	
	
</head>	

<div class="main_wrap">
		
<?php include "../outline/header.php"; ?>


	<div class="sub_top_wrap sub_pos_history"></div>
	<div class="container960 mb_50">
		<ul class="tap_ea5">
			<?
				$bannerCount = 0;
				foreach($homeBanner['home_history'] as $v){
					if($v["banner_hidden"]){
			?>
						<?if($bannerCount==0){?>
							<li>
								<a href="javascript:;" class = 'CLS_brandNavOn' id = '<?=$v['banner_no']?>'><img src="<?=$v["banner_img_title_on"]?>"/></a>
								<input type = 'hidden' value = '<?=$v["banner_img_title_on"]?>'>
								<input type = 'hidden' value = '<?=$v["banner_img_title_out"]?>'>
							</li>
						<?}else{?>
							<li>
								<a href="javascript:;" class = 'CLS_brandNavOff' id = '<?=$v['banner_no']?>'><img src="<?=$v["banner_img_title_out"]?>"/></a>
								<input type = 'hidden' value = '<?=$v["banner_img_title_on"]?>'>
								<input type = 'hidden' value = '<?=$v["banner_img_title_out"]?>'>
							</li>
						<?}?>
			<?
						$bannerCount++;
					}
				}
			?>
		</ul>
		<?
			$bannerCount = 0;
			foreach($homeBanner['home_history'] as $v){
				if($v["banner_hidden"]){
		?>
					<?if($bannerCount==0){?>
						<div class = 'CLS_brandContentsAll CLS_brandContents<?=$v['banner_no']?>'><img src="<?=$v["banner_img"]?>" alt="RECRUIT"/></div>
					<?}else{?>
						<div class = 'CLS_brandContentsAll CLS_brandContents<?=$v['banner_no']?>' style = 'display:none;'><img src="<?=$v["banner_img"]?>" alt="RECRUIT"/></div>
					<?}?>
		<?
					$bannerCount++;
				}
			}
		?>
	</div>


<? include "../outline/footer.php"; ?>

</div>

<script type="text/javascript">
<!--
	$(document).ready(function(){
		$(document).on("click", ".CLS_brandNavOff", function(e){
			$(".CLS_brandNavOn").attr('class', 'CLS_brandNavOff');
			$(".CLS_brandContentsAll").hide();
			
			$(this).attr('class', 'CLS_brandNavOn');	
			$(this).find('img').attr('src', $(this).next().val());
			$(".CLS_brandContents"+$(this).attr('id')).show();

			$(".CLS_brandNavOff").each(function(){
				$(this).find('img').attr('src', $(this).next().next().val());
			})
		
		})
	})
//-->
</script>

</html>