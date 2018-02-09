<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");


if($_GET[sno]){
	$sql ="select * from tblmember_question where sno = '".$_GET[sno]."'";
	$result = pmysql_query($sql);
	$data=pmysql_fetch($result);

	$id = $data['id'];
	$counsel_id = $data['counsel_id'];
	$contents = $data['contents'];
	$regdt = $data['regdt'];
	$counsel_type = $data['counsel_type'];

	$selected['counsel_Type'][$counsel_type] = "selected";
	$mode = "update";
}else{
	$id = $_GET['id'];
	$counsel_id = $_ShopInfo->id;

	$regdt = date('Y-m-d H:i:s');
	$mode = "write";
}

?>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$('#pop_close').click(function () {
			$('#dialog-overlay, #dialog-box', parent.document).hide();
			return false;
		});

		$("#submitFrm").click(function(){
			if($(".question_contents").val()){
				$("form[name='Crm_writeForm']").submit();
			}else{
				alert("상담내용을 입력해주세요.");
			}
		})
	});
</script>
<!-- 회원 상담내역 등록 start -->
<body style = 'overflow-y:hidden; background:#FFFFFF;'>
<div style="z-index:10;" id="Crm_writeFormID">
	<form name="Crm_writeForm" method="post" action="member_question_indb.php">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
			<?if($_GET[sno]){?>
			<input type="hidden" name="sno" value="<?=$_GET[sno]?>">
			<?}?>
			<input type="hidden" name="id" value="<?=$id?>">
			<input type="hidden" name="counsel_id" value="<?=$counsel_id?>">
			<input type="hidden" name="mode" id = 'question_mode' value="<?=$mode?>">			
			<tr>
				<td colspan="4" height='30' align=center bgcolor="#f9f9f9" id='write_TitleID'><font class=def color=636363><b>상담내역 등록</b></td>
			</tr>
			<tr>
				<td width="60" height='25' style="color:#7C7C7C;font:bold 8pt verdana;" align='center'><font class=small1 color=636363>처리자ID : </td>
				<td width="115"><font class=small1 color=636363><?=$_ShopInfo->id?></td>
				<td width="90" style="color:#7C7C7C;font:bold 8pt verdana;" align='center'><font class=small1 color=636363>상담수단 : </td>
				<td width="85">
				<select name="counsel_Type">
					<option value="p" <?=$selected['counsel_Type']['p']?>>전화</option>
					<option value="m" <?=$selected['counsel_Type']['m']?>>메일</option>
					<option value="h" <?=$selected['counsel_Type']['h']?>>기타</option>
				</select>
				</td>
			</tr>
			<tr>
				<td height='25' align='center'><font class=small1 color=636363>상담시간 : </td>
				<td colspan="3" height="25"><input type="text" name="regdt" value="<?=$regdt?>" style="width:50%;height:18px;" class=ver71></td>
			</tr>

			<tr>
				<td colspan="4" height="70" style="padding-left:10"><textarea name="contents" style="width:97%;height:200px" class="question_contents"><?=($contents)?></textarea></td>
			</tr>

			<tr>
				<td colspan='2' align='right' height="25" style="padding-right:3px;">
					<?if($_GET[sno]){?>
						<div onclick="javascript:;" id = "submitFrm" style="cursor:pointer;"><img src = './images/btn_cate_modify.gif' align = 'absmiddle'></div>
					<?}else{?>
						<div onclick="javascript:;" id = "submitFrm" style="cursor:pointer;"><img src = './images/btn_cate_reg.gif' align = 'absmiddle'></div>
					<?}?>
				</td>
				<td colspan='2' align='left' height="25" style="padding-left:3px;">
					<div onclick="javascript:;" id = "pop_close" style="cursor:pointer;"><img src = './images/btn_close.gif' align = 'absmiddle'></div>
				</td>
			</tr>
			<tr><td colspan=5 height=8></td></tr>
		</table>
	</form>
</div>
</body>
<!-- 회원 상담내역 등록 end -->
