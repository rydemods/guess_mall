<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################


include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.form.js"></script>
<script type="text/javascript">

	var total=120000;
	var donate;
	function donate_chk(){

		//document.getElementById("input_donate").value="test";
		donate=document.getElementById("input_donate").value;

		if(!donate){
			alert("기부 할 금액을 입력하세요");
		}else{

			if(donate > total){
				alert("총 적립금보다 큽니다");
			}else{
				total-=donate;
				document.getElementById("result_donate").value=total;
			}
		}
	}

</script>
<style>
body, html {margin:0px; padding:0px;}
img {border: none;}
</style>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 기부현황 &gt;<span> 기부</span></p></div></div>
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
			<?php include("menu_counter.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>

			<tr>
				<td>

					<div class="title_depth3">기부</div><br>
				</td>
			</tr>


					<div class="title_depth3_sub">
					총 기부 된 적립금&nbsp;&nbsp;
					<input type="text" readonly value="120000" style="border:0;" >
					</div>

					<div class="table_style01 pt_20">
					<table cellpadding="0" cellspacing="0" width="100%" style="  margin-top: 40px;">
					<tbody>

						<tr>
							<th><span>기부 할 적립금</span></th><td><input id="input_donate" type="text"></td><td><input type="button" size='40' value='기부적립금확인' onclick="donate_chk()">
							</td> <td></td> <td></td>
						</tr>

						<tr height='20px'>

						</tr>

						<tr>
							<th><span>기부 후 적립금</span></th><td><input type="text" id="result_donate" readonly></td><td></td> <td></td> <td></td>
						</tr>

						<tr>
							<th height='100px'>기부 내용 메모</th><td><textarea cols='40' rows='5'>ex)유니세프에 50,000포인트 기증하였습니다</textarea></td>
						</tr>

						<tr>
							<td></td><td></td><td><input type="button" value="기 부 하 기"></td>
						</tr>
					</tbody>
					</table>
					</div>

					<div class="table_style02">
					<table cellpadding="0" cellspacing="0" width="100%" style="margin-top:40px;">
					<tbody>
					<col width='25%'></col>
					<col width='25%'></col>
					<col width='25%'></col>
					<col width='25%'></col>
						<tr>
							<th>번호</th><th>날짜</th><th>기부메모</th><th>기부금액</th>
						</tr>
						<tr>
							<td>1</td><td>2015년6월10일</td> <td>A센터에 10만포인트 기증</td><td>기부금액:100,000P</td>
						</tr>
						<tr>
							<td>2</td><td>2015년6월11일</td> <td>B보육원에 6만포인트 기증</td><td>기부금액:60,000</td>
						</tr>
						<tr>
							<td>3</td><td>2015년6월11일</td> <td>C양로원에 6만포인트 기증</td><td>기부금액:60,000</td>
						</tr>
					</tbody>
					</table>
					</div>

				<!--컨텐츠-->
				</td>
			</tr>

			<tr><td height="30"></td></tr>
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
