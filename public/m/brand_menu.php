<ul class="depth2 <? if($page_cate == 'E-SHOP'){ ?>upper<?}?>">
	<li>
		<a href="javascript:;"><?=$page_cate?></a>
		<ul class="depth3">
			<li><a href="brand_main.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">BRAND</a></li>
			<li><a href="ecatalog_list.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">COLLECTION</a></li>
			<li><a href="lookbook_list.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">LOOKBOOK</a></li>
			<?if($brand_idx == "301" || $brand_idx == "302" || $brand_idx == "303" ) { //여성복(이사베이 제외)?>
			<li><a href="openguide.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">OPEN GUIDE</a></li>
			<?}?>
			<li><a href="brand_qna.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">Q&amp;A</a></li>
			<li><a href="brand_store.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">STORE</a></li>
			<li><a href="productlist.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">E-SHOP</a></li>
		</ul>
	</li>
</ul>
<div class="dimm_bg"></div>