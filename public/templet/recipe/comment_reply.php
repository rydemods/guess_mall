<?
	$Dir = "../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/recipe.class.php");
?>
	<table>
	<form name="comment_reply" action="/admin/recipe_indb.php" method="post">
	<input type="hidden" name="module" value="recipe_contents">
	<input type="hidden" name="mode" value="add_comment_reply">
	<input type="text" name="num" id="num" value="">
	<input type="hidden" name="returnUrl" value="<?=$_SERVER[REQUEST_URI]?>">
		<tr>
			<td rowspan=2>
			<textarea name="comment" style="width:730px;min-height:89px" class=linebg required msgR="코멘트를 입력해주세요"></textarea>
			</td>
			<td><img src="/image/recipe/icon_name.gif" alt="이름" /></td>
			<td class="bold">
			<input type="hidden" name="memid" value="<?=$_ShopInfo->memid?>">					
			<input type="hidden" name="memname" value="<?=$_ShopInfo->memname?>">					
			<?=$_ShopInfo->memname?>
			</td>
			</tr>
			<tr>
			<td colspan=2>
			<input type=image src="/image/recipe/bt_comment.gif" onclick="addCommentReply(document.comment_reply[0])">
			</td>
		</tr>
	</form>
	</table>