<?if($_data->icon_type == 'tem_001'){?>
	<div class="cs_contents">
	

			<div class="title">
			<h2><img src="<?=$Dir?>image/community/title_<?=$setup[board]?>.gif" alt="커뮤니티" /></h2>
			<div class="path">
				<ul>
					<li class="home">홈&nbsp;&gt;&nbsp;</li>
					<li>커뮤니티&nbsp;&gt;&nbsp;</li>
					<li><?=$setup[board_name]?></li>
				</ul>
			</div>
		</div>
		<div class="sub_title"><img src="<?=$Dir?>image/community/community_title_<?=$setup[board]?>.png" alt="공지사항" /></div>
		<?=$setup[board_header]?>
<?}?>