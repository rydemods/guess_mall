<?
$fp = file("menu_mobileShop.txt");
//$Path = dirname(__FILE__) . "/menu_product.txt";

//system(chmod("/home/duometis001/shop_laravel/public/admin/menu_product.txt",0707));

foreach($fp as $v){
	if(trim($v))$v = trim($v);
	if(substr($v,0,1) == "[" && substr($v,-1,1) == "]"){
		$menu['main_title'][] = str_replace(array('[',']'),"",$v);
	}else if(substr($v,0,1) == "<" && substr($v,-1,1) == ">"){
		$menu['title'][] = str_replace(array('<','>'),"",$v);
	}else{
		$k = count($menu[title]) - 1;
		$tmp = explode('= ',$v);
		if(trim($tmp[0])){
			$menu['subject'][$k][] = $tmp[0];
			$url = trim(str_replace('"','',$tmp[1]));
			if (preg_match("/^..\//", $url)) $menu['value'][$k][] = $url;
			else if (preg_match("/^javascript/i", $url)) $menu['value'][$k][] = $url;
			else $menu['value'][$k][] = $url;
			
		}
	}
}

?>

<!-- LNB 리뉴얼 -->



<div class="admin_lnb_wrap">
	<div class="close_btn" style="display:none" id="close_img_off"><a href="javascript:hiddenLeft()"><img src="img/btn/admin_lnb_open.gif" alt="OPEN" /></a></div>
	<div class="container" id="leftMenus">

		<div class="close_btn"><a href="javascript:hiddenLeft()"><img src="img/btn/admin_lnb_close.gif" alt="CLOSE" /></a></div>
		
		<div class="admin_name">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th><?=$_ShopInfo->id?><span> 접속중</span></th>
					<td><a href="logout.php"><img src="img/btn/admin_lnb_logout.gif" alt="로그아웃" /></a></td>
				</tr>
			</table>
		</div>

		<div class="lnb_menu_wrap">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th><img src="img/common/admin_lnb_round_top.gif" alt="" /></th>
					<tr>
						<td>
							<div class="lnb_sub_tit">
								<p class="sub01"><span><?=$menu['main_title'][0]?></span></p>
							</div>
							<?
							$menuopen=0;
							for ($i=0,$m=sizeof($menu['title']);$i<$m;$i++) {
								if($menu['title'][$i] && count($menu['subject'][$i])){
								
							?>
							
							<div class="lnb_sub_menu">
							<dl>
							<input type="hidden" name="menu_open[]">
								<dt <?=$i==0?"class='this'":"";?> id='on_dt<?=$i?>'><a href="javascript:menu_over('<?=$i?>')"><span><?=$menu['title'][$i]?></span></a></dt>
								<dd style="display:<?=$i==0?block:none;?>" id='on_dd<?=$i?>'>
									<ul>
							<? for ($j=0;$j<count($menu['subject'][$i]);$j++){
								if($menu['subject'][$i][$j]){
							?>
								<!-------------------- 측면 작은메뉴 시작 ------------------------------->
								<li>
								<?if(trim($menu['value'][$i][$j])){?>
								<a href="<?=$menu['value'][$i][$j]?>" name="navi" <?if(isset($menu['target'][$i][$j])) {?>target="<?=$menu['target'][$i][$j]?>"<?}?> <?if(preg_match('/'.str_replace('/','\/',$menu['value'][$i][$j]).'/',$_SERVER['SCRIPT_FILENAME'])){?>class="this"<?}?>>
								<?}?>
								<?=trim($menu['subject'][$i][$j])?>
								<?if(trim($menu['value'][$i][$j])){?></a><?}?>
								<?if(preg_match('/'.str_replace('/','\/',$menu['value'][$i][$j]).'/',$_SERVER['SCRIPT_FILENAME'])){$menuopen=$i;}?>
								</li>
									
							<? }} ?>
									</ul>
								</dd>
							</dl>
							</div>
							<?}}?>
						</td>
					</tr>
					<tr><th><img src="img/common/admin_lnb_round_bottom.gif" alt="" /></th></tr>
				</tr>
			</table>
		</div>

	</div>
</div>
<!-- LNB 리뉴얼 -->
<script>on_menu(<?=$menuopen?>);</script>