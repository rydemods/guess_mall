<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-5";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$sql = "select * from tblpushlist order by date desc";
$result = pmysql_query($sql,get_db_conn()); //수정일자 2017-09-13 푸시내역 불러오기

$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script src="../js/jquery.form.js"></script>
<script language="JavaScript">
function OnChangePeriod(val) {
	var pForm = document.frmTab1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
	$("input[name='buyType']").val(val);
}

$( document ).ready(function(e) {
	$(".imgUploadDiv").hide();
	$(".imgNoUploadDiv").show();
	$(".allMemberClass").show();
	$(".buyMemberClass").hide();

	$(".sendTypeClass").bind('click', function() {
		if($(this).val() == "t"){
			$(".imgUploadDiv").hide();
			$(".imgNoUploadDiv").show();
			$(".pushContentMixClass").hide();
			$(".pushContentTextClass").show();
		}else{
			$(".imgUploadDiv").show();
			$(".imgNoUploadDiv").hide();
			$(".pushContentMixClass").show();
			$(".pushContentTextClass").hide();
		}
	});

	$(".pushSendTypeClass").bind('click', function() {
		if($(this).val() == "n"){
			$("input[name='push_send_day']").attr("disabled", true);
			$("select[name='push_send_hour']").attr("disabled", true);
		}else{
			$("input[name='push_send_day']").attr("disabled", false);
			$("select[name='push_send_hour']").attr("disabled", false);
		}
	});

	$(".memSearchTypeClass").bind('click', function() {
		if($(this).val() == "a"){
			$(".allMemberClass").show();
			$(".buyMemberClass").hide();
		}else{
			$(".allMemberClass").hide();
			$(".buyMemberClass").show();
		}
	});

	$(".memLoad").bind('click', function() {
		var mode = "memLoad";
		var mem_search_type = $("input[name='mem_search_type']:checked").val();
		var search_start    = $("input[name='search_start']").val();
		var search_end      = $("input[name='search_end']").val();
		$.ajax({ 
			type: "POST", 
			url: "./market_app_push_indb.php", 
			data: "mode=" + mode + "&mem_search_type=" + mem_search_type + "&search_start=" + search_start + "&search_end=" + search_end,
			dataType:"html",
			success: function(result) {
				alert("대상자를 정상적으로 불러왔습니다."); 
				$(".loadMemberCount").html(result);
			},
			error: function(result) {
				alert("대상자를 불러오는데 실패했습니다.\r다시 시도해 주세요."); 
			}
		});
	});

	$("input[name='push_img']").on("change", function(){
		var filename = $(this).val();
		var extension = filename.replace(/^.*\./, '');
		if (extension == filename) {
			extension = '';
		} else {
			extension = extension.toLowerCase();
		}

		//이미지 파일은 JPG, PNG, GIF 확장자만 가능
		if( (extension != 'jpg') && (extension != 'png') && (extension != 'gif') ) {
			var control = $(this);
			control.replaceWith( control = control.clone( true ) );
			
			alert("이미지 파일은 JPG, PNG, GIF 확장자만 가능합니다.");
		}
	});

	$(".pushContentInputTextClass").bind('keyup', function() {
		$(".pushContentInputMixClass").val($(this).val());
	});

	$(".pushContentInputMixClass").bind('keyup', function() {
		$(".pushContentInputTextClass").val($(this).val());
	});

	var openWindow = "";

	$(".onSubmit").bind('click', function() {
		var submitFlag   = true;
		var pushSendType = $(".pushSendTypeClass:checked").val();
		var pushSendDay  = $("input[name='push_send_day']").val();
		var pushSendHour = $("select[name='push_send_hour']").val();
		var sendType     = $(".sendTypeClass:checked").val(); // 푸쉬방식 t, m
		var pushTitle    = $("input[name='push_title']").val();
		var pushImg      = $("input[name='push_img']").val();
		var pushUrl      = $("input[name='push_url']").val();

		if($(".loadMemberCount").html() == "0" || $(".loadMemberCount").html() == ""){
			alert("회원 검색을 하지 않았습니다.");
			submitFlag = false;
		}
		//Validate 셀렉터":visible" 추가
		$(".checkFiled:visible").each(function(){
			if(!$(this).val() && submitFlag == true){
				alert($(this).attr("mgr"));
				submitFlag = false;
			}
		})
		if(sendType == "m" && submitFlag == true){
			if(!pushImg){
				alert("이미지를 첨부하지 않았습니다.");
				submitFlag = false;
			}
		}
		if(pushSendType == "r" && submitFlag == true){
			if(!pushSendHour || !pushSendDay){
				alert("예약 발송은 날짜가 필수 입니다.");
				submitFlag = false;
			}
		}
		if(submitFlag){
			TopPosition  = (screen.height)/3;
			LeftPosition = (screen.width-250)/2;
			//width=250,height=190
			openWindow = window.open("./market_app_push_pop.php","pushSubmit","width=500,height=200,scrollbars=no,top="+TopPosition+",left="+LeftPosition+"");
		}
	});


	


	$(".frmTab1Class").ajaxForm({
		beforeSubmit: function (data, frm, opt) {
			//alert("전송전!!");
			return true;
		},
		//submit이후의 처리
		success: function(responseText, statusText){
			//alert("전송성공!!");
			var arrReturn = responseText.split("::::");
			if(arrReturn[1] == "0"){
				alert(arrReturn[0]);
				openWindow.close();
			}else{
				alert(arrReturn[0]);
				$.ajax({
					type: "POST",
					url: "./market_app_push_indb.php",
					data: "mode=sendAjaxPush&no=" + arrReturn[1],
					beforeSend: function () {
						// 구글과 통신 하여 PUSH 발송.
						$(".btn_center").hide();
						$(".btn_center_ing").show();
					}
				}).done(function ( succCnt ) {
					if(succCnt > 0){
						alert("전송이 완료되었습니다.");
						location.reload();
						openWindow.close();
					}else if(succCnt == 'n'){
						location.reload();
						openWindow.close();
					}else{
						alert("전송이 실패 했습니다.");
						$(".btn_center").show();
						$(".btn_center_ing").hide();
					}
				});
			}
		},
		//ajax error
		error: function(){
			//alert("에러발생!!");
			alert("등록에 실패했습니다.");
		}                               
	});




	$(".sendTypeClass:first").prop("checked",true).trigger("click");
	$(".pushSendTypeClass:first").prop("checked",true).trigger("click");
	$(".searchAllClass").trigger("click");
	
});

// Android ↔ iOS 탭전환
function togglePushTab(){
	if($("input[name='push_os']").val() == 'Android'){
		$("ul.tab li:first-child").removeClass('on');
		$("ul.tab li:nth-child(2)").addClass('on');
		$("input[name='push_os']").val('iOS');
		$(".android").hide();
		$(".ios").show();
	}else{
		$("ul.tab li:first-child").addClass('on');
		$("ul.tab li:nth-child(2)").removeClass('on');
		$("input[name='push_os']").val('Android');
		$(".android").show();
		$(".ios").hide();
	}
}
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 푸쉬발송 &gt;<span>푸쉬발송</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>
			<td></td>
			<td valign="top">
				<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
				<tr><td height="8"></td></tr>

				<tr>
					<td>
						<!-- 페이지 타이틀 -->
						<div class="title_depth3">앱 수동 푸쉬발송</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="push_warp">
							<form name = 'frmTab1' class = "frmTab1Class" method = "POST" action = "./market_app_push_indb.php" ENCTYPE="multipart/form-data">
							<input type="hidden" name="push_os" value="Android" />
							<div class="app_tab">
								<ul class="tab">
									<li class="on"><a href="javascript:togglePushTab()">안드로이드</a></li>
									<li><a href="javascript:togglePushTab()">아이폰</a></li>
								</ul>
							</div>
							<!-- 선택 -->
							<div class="android">
								<div class="title_depth3_sub">푸쉬방식</div>
								<p class="radio_style01">
									<label><input type="radio" name="sendType" value="t" class = "sendTypeClass">텍스트 푸시</label>
									<label><input type="radio" name="sendType" value="m" class = "sendTypeClass">텍스트+이미지 푸시</label>
								</p>
							</div>
							<div class="tab01">
								<input type = "hidden" name = "mode" value = "insertData">
								<input type = "hidden" name = "buyType" value = "0">
								<!-- 테이블 -->
								<div class="title_depth3_sub">Push 발송 타겟설정</div>
								<div class="table_style01">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
										<TR>
											<th><span>회원선택</span></th>
											<TD>
												<p class="radio_style01">
													<label><input type="radio" name="mem_search_type" value="a" class = "memSearchTypeClass" checked>전체회원</label>
													<label><input type="radio" name="mem_search_type" value="b" class = "memSearchTypeClass">구매회원</label>
												</p>
											</TD>
										</TR>
										<TR>
											<th><span>검색 조건</span></th>
											<TD>
												<div class = 'allMemberClass'>
													전체 회원을 선택하셨습니다.
												</div>
												<div class = 'buyMemberClass'>
													<input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)"/> ~ 
													<input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)"/>
													<img src='images/btn_day_total.gif' border='0' align='absmiddle' style="cursor:hand" class = 'searchAllClass' onclick="OnChangePeriod(0)">
													<img src='images/btn_today01.gif' border='0' align='absmiddle' style="cursor:hand" onclick="OnChangePeriod(1)">
													<img src='images/btn_day07.gif' border='0' align='absmiddle' style="cursor:hand" onclick="OnChangePeriod(2)">
													<img src='images/btn_day14.gif' border='0' align='absmiddle' style="cursor:hand" onclick="OnChangePeriod(3)">
													<img src='images/btn_day30.gif' border='0' align='absmiddle' style="cursor:hand" onclick="OnChangePeriod(4)">
												</div>
											</TD>
										</TR>
									</TABLE>
								</div>
								<p class="btn_center mb_40"><a href="javascript:;" class = "memLoad"><img src="images/btn_find.gif" border="0" alt="대상자 불러오기"></a></p>
								<!-- 타이틀 -->
								<div class="title_depth3_sub push_tit">Push 내용 작성하기 <p class="num"><span class = 'loadMemberCount'>0</span>건을 로드했습니다.</p></div>    
								<!-- 테이블 -->
								<div class="table_style01">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
									<TR class="android">
										<th><span>제목</span></th>
										<TD><input type="text" name = 'push_title' class="input checkFiled" style="width:98%" mgr = "제목을 입력하지 않았습니다."><p class="android">(android : 25byte 이내 권장) </p></TD>
									</TR>
									<TR>
										<th><span>메세지</span></th>
										<TD>
											<div class = "pushContentTextClass">
												<textarea name = 'push_content_text' style="width:98%; min-height:50px;" class = 'checkFiled pushContentInputTextClass' mgr = "메세지를 입력하지 않았습니다."></textarea>
											</div>
											<div class = "pushContentMixClass">
												<input type="text" name = 'push_content_mix' class="input checkFiled pushContentInputMixClass" style="width:98%" mgr = "메세지를 입력하지 않았습니다.">
											</div>
											<p class="android">(android : 44byte 이내 권장) </p>
											<p class="ios" style="display:none">(iOS : 88byte 이내 권장) </p>
										</TD>
									</TR>
									<TR class="android">
										<th><span>이미지 첨부</span></th>
										<TD>
											<div class = 'imgUploadDiv'>
												<input type="file" name = 'push_img' style="width:98%">
												<p>(권장 이미지 크기 : 가로 800px, 세로 400px / 권장 용량 : 2Mb 이내 / 파일형식 : jpg, png, gif / <b>젤리빈</b> 이전 버전은 이미지 전송이 되지 않습니다.) </p>
											</div>
											<div class = 'imgNoUploadDiv'>
												<p>텍스트 푸시 방식을 선택 하셨습니다.</p>
											</div>
										</TD>
									</TR>
									<TR>
										<th><span>랜딩페이지 URL</span></th>
										<TD><input type="text" name = 'push_url' class="input checkFiled" style="width:98%" mgr = "랜딩페이지를 입력하지 않았습니다."><!--<p>* 꼭 URL 뒤에 파라미터 act=app를 추가하시기 바랍니다. </p>--></TD>
									</TR>
									<TR>
										<th><span>발송 시간</span></th>
										<TD>
											<label class="mr_20"><input type="radio" name="push_send_type" class = 'pushSendTypeClass' value="n">즉시 발송</label>
											<label class="mr_30"><input type="radio" name="push_send_type" class = 'pushSendTypeClass' value="r">예약 발송</label>
											<!-- <select>
												<option value="">날짜선택</option>
											</select>
											<select>
												<option value="">시간선택</option>
											</select> -->
											<input class="input_bd_st01" type="text" name="push_send_day" OnClick="Calendar(event)"/>
											<select name="push_send_hour">
												<option value="">시간</option>
												<?for($i=8; $i <= 20; $i++){?>
													<?for($m=0; $m <= 1; $m++){?>
														<option value="<?=str_pad($i, 2, "0", STR_PAD_LEFT)?>:<?=str_pad($m*30, 2, "0", STR_PAD_LEFT)?>"><?=str_pad($i, 2, "0", STR_PAD_LEFT)?>시 <?=str_pad($m*30, 2, "0", STR_PAD_LEFT)?>분</option>
													<?}?>
												<?}?>
											</select>
										</TD>
									</TR>
								</TABLE>
								<p class="btn_center mt_20">
									<a href="javascript:;" class = 'onSubmit'><img src="images/btn_pushsand.gif" border="0" alt="Push 발송"></a>
									<input type = 'submit' class = "onSubmitHidden" value = "전송" style = "display:none;">
								</p>
								</div>
							</div>
							</form>
						</div>
						<!--푸시내역 노출 2017-09-13 -->
						<!-- 타이틀 -->
						<div class="title_depth3_sub push_tit">Push 내역 </div>    
						<!-- 테이블 -->
						<div class="table_style02">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<col width=50>
								<col width=80>
								<col width=auto>

								<col width=130>
								<col width=210>
								<!--<col width=70>-->
								<col width=100>
								<col width=100>
								<col width=100>
								<col width=100>
							<TR align=center>
								<th>No</th>
								<th>제목</th>
								<th>메시지</th>

								<th>이미지</th>
								<th>Push_Url</th>
								<!--<th>GNB 노출</th>-->
								<th>발송일자</th>
								<th>발송시간</th>
								<th>등록일</th>
								<th>기기종류</th>
							</TR>
							<?php $i=1;?>
							<?php while ($row=pmysql_fetch_object($result)) {?>
							<TR align=center>
								<td><?php echo $i;?></td>
								<td><?php echo $row->title;?></td>
								<td><?php echo $row->push_content;?></td>

								<td><?php echo $row->push_img;?></td>
								<td><?php echo $row->push_url;?></td>
								<td><?php echo $row->push_send_day;?></td>
								<td><?php echo $row->push_send_hour;?></td>
								<td><?php echo $row->date;?></td>
								<td><?php echo $row->push_os;?></td>
							</TR>
							<?php $i++;}?>
						</TABLE>
					</div>
					</td>
				</tr>
				
				<!--<tr>
					<td>
						<!-- 매뉴얼
						<div class="sub_manual_wrap">
							<div class="title"><p>매뉴얼</p></div>
							<dl>
								<dt><span>푸쉬발송</span></dt>
								<dd>
									
								</dd>	
							</dl>
						</div>
					</td>
				</tr>-->
				<tr><td height="50"></td></tr>
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

<?=$onload?>
<?php 
include("copyright.php");
