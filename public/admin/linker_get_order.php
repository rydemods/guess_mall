<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../js/layerPopup.js"></script>
<script language="JavaScript">
	function OnChangePeriod(val) {
		var pForm = document.form1;
		var period = new Array(7);
		period[0] = "<?=$period[0]?>";
		period[1] = "<?=$period[1]?>";
		period[2] = "<?=$period[2]?>";
		period[3] = "<?=$period[3]?>";
		

		pForm.search_start.value = period[val];
		pForm.search_end.value = period[0];
	}
	
	function getLinkerCode(val){
		if(!$("input[name='search_start']").val() && !$("input[name='search_end']").val()){
			alert("수집 시작일과 종료일을 입력하지 않으셨습니다.");
		}else if(!$("input[name='search_start']").val() && $("input[name='search_end']").val()){
			alert("수집 시작일을 입력하지 않으셨습니다.");
		}else if($("input[name='search_start']").val() && !$("input[name='search_end']").val()){
			alert("수집 종료일을 입력하지 않으셨습니다.");
		}else{
			if(val == 'o'){
				$("input[name='mode']").val("linkerOrderGet");
				//document.form1.submit();
			}else{
				$("input[name='mode']").val("linkerClameGet");
				//document.form1.submit();
			}
			$.ajax({
				type: "POST", 
				url: "linker_indb.php", 
				data: $("form[name='form1']").serialize(), 
				beforeSend: function () {
					popupJquery('linker_process_popup.php', 647, 200);
				}
			}).done(function ( data ) {
				$('#createIfrm').contents().find('.linker_pop_msg').html(data);
			});
		}
	}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 제휴몰 주문관리 &gt;<span>제휴몰 주문 / 클레임수집</span></p></div></div>

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
								<?php include("menu_order.php"); ?>
								</td>
								<td width="20" valign="top"><img src="images/space01.gif" height="1" border="0" width="20"></td>
								<td valign="top">
									<form name=form1 action="linker_indb.php" method=post enctype="multipart/form-data">
										<input type=hidden name=mode>
										<table cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td height="8"></td>
											</tr>
											<tr>
												<td>
													<!-- 페이지 타이틀 -->
													<div class="title_depth3">제휴몰 주문 / 클레임수집</div>
												</td>
												</td>
											</tr>
											<tr>
												<td align="center" height=10></td>
											</tr>
											<tr>
												<td>
													<div class="table_style01">
														<table cellspacing=0 cellpadding=0 width="100%" border=0>
														<tr>
															<th><span>수집일 선택</span></th>
															<td>
																<input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ 
																<input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
																<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
																<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
																<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
																<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
															</td>
														</tr>
														</table>
													</div>
												</td>
											</tr>
											<tr>
												<td align="center" height=10></td>
											</tr>
											<tr>
												<td align="center">
													<p>
														<a href="javascript:getLinkerCode('o');">[주문 수집<b> (확인불가) </b>]</a>&nbsp;&nbsp;
														<a href="javascript:getLinkerCode('c');">[클레임 수집<b> (확인불가) </b>]</a>
													</p>
												</td>
											</tr>
											<tr>
												<td height=20></td>
											</tr>
										</table>
									</form>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<style>
	/* 팝업 스타일 시작*/
	#dialog-overlay {	width:100%; height:100%;	filter:alpha(opacity=30); -moz-opacity:0.3; -khtml-opacity: 0.3; opacity: 0.3; background:#000; position:absolute; top:0; left:0; z-index:3000; display:none;}
	#dialog-box {	position:absolute; z-index:5000; display:none;}
	#dialog-box .dialog-content {text-align:left;	padding:3px; margin:13px;	color:#666; font-family:arial;font-size:11px; }
	/* extra styling */
	#dialog-box .dialog-content p {	font-weight:700; margin:0;}
	#dialog-box .dialog-content ul {margin:10px 0 10px 20px; padding:0; height:50px;}
	#dialog-message{border:3px solid #000;}
	/* 팝업 스타일 끝 */
</style>
<div id="dialog-overlay"></div>
<div id="dialog-box">
	<div class="dialog-content">
		<div id="dialog-message">
		</div>
	</div>
</div>
<?=$onload?>
<?php
include("copyright.php");
