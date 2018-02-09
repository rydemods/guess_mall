<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-1";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
include("header.php"); 
?>

<?
#########################################################
$sql = "select * from tbldeveloperfund order by idx asc";
$result = pmysql_query( $sql,get_db_conn() );
$use_type = $_shopdata->developer_fund;
if($use_type){
	$chk_use1 = "checked";
}else{
	$chk_use2 = "checked";
}
#########################################################
?>
<!--#############스크립트 영역################-->
<link rel="stylesheet" href="jscript/jquery-ui-1.11.4.custom/jquery-ui.css">
<script src="jscript/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
<script src="jscript/input_money.js"></script>
<script>
$(function() {
	
	$( "#btn_add").click( function () {//새로운 리스트 생성
		$.post('shop_developerfund_ajax.php',{mode:'add'},function(data){
			if(data){
				alert('추가되었습니다');
				history.go(0);
			}
		});
	});

	$( "#btn_delete").click( function () {//맨아래 리스트 부터 삭제
		var idx = $("#list_table").children().find(".develop_list").last().attr('idx');
		$.post('shop_developerfund_ajax.php',{mode:'delete',idx:idx},function(data){
			if(data){
				alert('삭제되었습니다');
			}
		});
		history.go(0);
	});

});//$(function) end 

function formSubmit(){//등록 했을때의 동작입니다. 입력금액들 유효성 검사를 합니다.

	var formData	= $("#developer_form").serialize();

	$.ajax({type:'POST', url: 'shop_developerfund_ajax.php', data:$('#developer_form').serialize(), success: function(response) {
		if(response){
			alert('저장되었습니다.');
		}
	}});
}
</script>
<!--##################스크립트 영역 끝######################-->

<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>발전기금 설정</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">발전기금 설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>기본 지급비율을 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">발전기금 사용여부 설정</div>
				</td>
			</tr>

			<tr>
			<!--발전기금 세팅 영역 넣자 ㅇㅇ-->
				<td>
                <form name="developer_form" id="developer_form" method="post">
				<input type="hidden" name="mode" value="insert">
				<div class="table_style02"  data-role="page">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 id="list_table">
				<colgroup>
					<col width=45>
					<col width=>
					<col width=100>
				</colgroup>
				<TR>
					<td colspan=3>
						<div style="float:right;">
							<img src="images/btn_add1.gif" id="btn_add" style="cursor:pointer;">
							<img src="images/btn_del.gif" id="btn_delete" style="cursor:pointer;">
						</div>
					</td>
				</TR>
				<TR id="list_th">
					<th>no</th><th>기준 매출액</th><th>적립율</th>
				</TR>
			
			<?$i = 1;?>
			<?while($row=pmysql_fetch_object($result)){?>
				<TR class="develop_list" idx='<?=$row->idx?>' >
					<td>
						<?=$i?>
						<input type="hidden" name="idx[]" value='<?=$row->idx?>'>
					</td>
					<td>
						<input type="text" class="money" name="amt_s[]" value='<?=$row->amt_s?>' chk='min' style='text-align:right;'>원 [이상] ~ <input type="text" class="money" name="amt_e[]" value='<?=$row->amt_e?>' chk='max' style='text-align:right;'>원 [이하]
					</td>
					<td>
						<input type="text" name="per[]" style='width:30px;text-align:right;' value=<?if($row->per){echo $row->per;}else{echo "0";}?>> %
					</td>
				</TR>
			<?$i++;?>
			<?}?>
				</TABLE>
				</div>
				</form>

				<div class="button_area">
					<center><img src="images/botteon_save.gif" style="cursor:pointer;" onclick="formSubmit();"></center>
				</div>

				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>-</span></dt>
							<dd>-</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</div>
<!--iframe 영역-->
<iframe name="hidden_form" style="display:none;"></iframe>
<!--// iframe 영역-->
<?=$onload?>
<?php 
include("copyright.php");
