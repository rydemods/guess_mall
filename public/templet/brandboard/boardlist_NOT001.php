<?php

$boardArray = barandBoardList($board_code);
$gotopage = $boardArray[3];
$t_coiunt = $boardArray[2];
$paging = $boardArray[1];
$boardList = $boardArray[0];

?>

<div class="containerBody sub_skin">
	<h3 class="title">
		<?=$boardCate->board_name?>
		<p class="line_map"><a>Ȩ</a> &gt; <a>BRAND</a> &gt; <span><?=$boardCate->board_name?></span></p>
	<? if($brandBoardCate){ ?>
		<form name="cateChage" id="cateChage" method="GET" action="<?=$_SERVER['PHP_SELF']?>">
		<select name="board_code" style="position:absolute; right: 0; top: 25px;" onchange="this.form.submit()">
		<? foreach($brandBoardCate as $boardKey=>$boardVal){?>
			<option value="<?=$boardVal->board_code?>" <? if($boardVal->board_code == $board_code) echo "SELECTED"; ?> ><?=$boardVal->board_name?></option>
		<? } ?>
		</select>
		</form>
	<? } ?>
	</h3>
	<div class="mt_20">
		<?=$boardList[0]->board_content?>
	</div>
</div>