<?
if($maincode!="index"){
	
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");



	include ("header.inc.php");
	$subTitle = "SHOP";
	include ("sub_header.inc.php");

}
?>
<script>
function goCate(code){
	location.href="productlist.php?code="+code;
}
</script>
<section class="shopping_1depth_wrap">

	<article class="shopping_1depth">
		<div class="pic">
			<a href="javascript:goCate('001001');">
				<img src="img/style_natural.jpg" alt="" />
				<div class="title">Natural<p class="arrow">></p></div>
			</a>
		</div>
	</article>

	<article class="shopping_1depth">
		<div class="pic">
			<a href="javascript:goCate('001002');">
				<img src="img/style_morden.jpg" alt="" />
				<div class="title">Morden<p class="arrow">></p></div>
			</a>
		</div>
	</article>

	<article class="shopping_1depth">
		<div class="pic">
			<a href="javascript:goCate('001003');">
				<img src="img/style_hotel.jpg" alt="" />
				<div class="title">Hotel<p class="arrow">></p></div>
			</a>
		</div>
	</article>

	<article class="shopping_1depth">
		<div class="pic">
			<a href="javascript:goCate('001004');">
				<img src="img/style_wedding.jpg" alt="" />
				<div class="title">Wedding<p class="arrow">></p></div>
			</a>
		</div>
	</article>

	<article class="shopping_1depth">
		<div class="pic">
			<a href="javascript:goCate('002');">
				<img src="img/style_bedding.jpg" alt="" />
				<div class="title">BEDDING<p class="arrow">></p></div>
			</a>
		</div>
	</article>

	<article class="shopping_1depth">
		<div class="pic">
			<a href="javascript:goCate('003');">
				<img src="img/style_baby.jpg" alt="" />
				<div class="title">BABY&KIDS<p class="arrow">></p></div>
			</a>
		</div>
	</article>

	<article class="shopping_1depth">
		<div class="pic">
			<a href="javascript:goCate('004');">
				<img src="img/style_decoration.jpg" alt="" />
				<div class="title">DECORATION<p class="arrow">></p></div>
			</a>
		</div>
	</article>

</section>

<?php
if($maincode!="index"){
 include ("footer.inc.php"); 
}
?>