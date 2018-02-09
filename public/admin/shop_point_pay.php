<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

if($_POST['mode'] == "point") {

    //print_r($_POST);

    extract($_POST);

    $f = fopen($Dir."conf/config.ap_point.php","w");
	fwrite($f,"<?\n");
	fwrite($f,"\$pointSet['join']['point'] = '$joinPoint'; \n");

	fwrite($f,"\$pointSet['addRecommand']['point'] = '$addRecommandPoint'; \n");

	fwrite($f,"\$pointSet['recommand']['count'] = '$recommandCount'; \n");
	fwrite($f,"\$pointSet['recommand']['point'] = '$recommandPoint'; \n");

	fwrite($f,"\$pointSet['login']['count'] = '$loginCount'; \n");
	fwrite($f,"\$pointSet['login']['point'] = '$loginPoint'; \n");
	
	fwrite($f,"\$pointSet['photo']['count'] = '$photoCount'; \n");
	fwrite($f,"\$pointSet['photo']['point'] = '$photoPoint'; \n");

	fwrite($f,"\$pointSet['textr']['count'] = '$photoCount'; \n");
	fwrite($f,"\$pointSet['textr']['point'] = '$textrPoint'; \n");

	fwrite($f,"\$pointSet['best']['count'] = '$bestCount'; \n");
	fwrite($f,"\$pointSet['best']['point'] = '$bestPoint'; \n");

	fwrite($f,"\$pointSet['board']['count'] = '$boardCount'; \n");
	fwrite($f,"\$pointSet['board']['point'] = '$boardPoint'; \n");

	fwrite($f,"\$pointSet['comment']['count'] = '$commentCount'; \n");
	fwrite($f,"\$pointSet['comment']['point'] = '$commentPoint'; \n");

	fwrite($f,"\$pointSet['like']['point'] = '$likePoint'; \n");

	fwrite($f,"\$pointSet['feelingUp']['point'] = '$feelingUpPoint'; \n");
	fwrite($f,"\$pointSet['feelingDown']['point'] = '$feelingDownPoint'; \n");
	
	fwrite($f,"\$pointSet['sns']['point'] = '$snsPoint'; \n");


	fwrite($f,"?>\n");
	fclose($f);
	@chmod($Dir."conf/config.ap_point.php",0777);
}
?>

<?php 
include("header.php"); 
include_once($Dir."conf/config.ap_point.php");
?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;
	form.submit();
}

function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode < 48 || charCode > 57){
		return false;
	}
	// Textbox value

	return true;
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>활동포인트 지급 설정</span></p></div></div>
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
					<div class="title_depth3">활동포인트 지급 설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>활동포인트 지급 기준을 설정할 수 있습니다.</span></div>
				</td>
			</tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">활동포인트 설정</div>
				</td>
			</tr>
            <form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
            <input type=hidden name=mode value="point">
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>가입</b></li>
						<li style='margin-top:8px'>- 가입시 지급되는 포인트를 설정한다.</li>
						<li>- 가입시 추천인을 입력한 경우 추가 포인트를 설정할수 있다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>지급 포인트</span></th>
                    <td><input type='text' name="joinPoint" value="<?=$pointSet['join']['point']?>" size=10 label="가입 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				<tr>
					<th><span>추천인 등록 추가 포인트</span></th>
                    <td><input type='text' name="addRecommandPoint" value="<?=$pointSet['addRecommand']['point']?>" size=10 label="추천인 추가 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>추천인</b></li>
						<li style='margin-top:8px'>- 추천받은 회원에게 지급되는 포인트를 설정한다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>지급 횟수제한</span></th>
					<td><input type='text' name="recommandCount" value="<?=$pointSet['recommand']['count']?>" size=10 label="추천받는 횟수" onkeypress="return isNumberKey(event)" class=input> 번 (추천받은수기준)</td>
				</tr>
				<tr>
					<th><span>지급 포인트</span></th>
                    <td><input type='text' name="recommandPoint" value="<?=$pointSet['recommand']['point']?>" size=10 label="추천받을때 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
            	<td style="padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap" style='min-height:30px;width:auto;'>
                        <ul style='margin:15px 0px 15px 50px;'>
                            <li><b style='font-size:14px;'>로그인</b></li>
                            <li style='margin-top:8px'>- 로그인시 지급되는 포인트를 설정한다.</li>
                        </ul>
                    </div>                    
            	</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>지급횟수제한</span></th>
					<td><input type='text' name="loginCount" value="<?=$pointSet['login']['count']?>" size=10 label="로그인 횟수" onkeypress="return isNumberKey(event)" class=input readonly> 번 (하루기준)</td>
				</tr>
				<tr>
					<th><span>지급 포인트</span></th>
                    <td><input type='text' name="loginPoint" value="<?=$pointSet['login']['point']?>" size=10  label="로그인 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>리뷰</b></li>
						<li style='margin-top:8px'>- 일반 리뷰 작성 및 베스트 리뷰 선정시 지급되는 포인트를 설정한다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>리뷰 지급 횟수제한</span></th>
					<td><input type='text' name="photoCount" value="<?=$pointSet['photo']['count']?>" size=10 label="포토리뷰 횟수" onkeypress="return isNumberKey(event)" class=input> 번 (구매상품당)</td>
				</tr>
				<tr>
					<th><span>포토 리뷰 지급 포인트</span></th>
                    <td><input type='text' name="photoPoint" value="<?=$pointSet['photo']['point']?>" size=10 label="포토리뷰 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				<tr>
					<th><span>텍스트 리뷰 지급 포인트</span></th>
                    <td><input type='text' name="textrPoint" value="<?=$pointSet['textr']['point']?>" size=10 label="텍스트리뷰 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>베스트 리뷰 지급 횟수제한</span></th>
					<td><input type='text' name="bestCount" value="<?=$pointSet['best']['count']?>" size=10 label="베스트리뷰 횟수" onkeypress="return isNumberKey(event)" class=input> 번 (하루기준)</td>
				</tr>
				<tr>
					<th><span>베스트 리뷰 지급 포인트</span></th>
                    <td><input type='text' name="bestPoint" value="<?=$pointSet['best']['point']?>" size=10  label="베스트리뷰 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>게시판</b></li>
						<li style='margin-top:8px'>- 게시판 게시글 작성 및 댓글 작성시 지급되는 포인트를 설정한다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>게시글 지급 횟수제한</span></th>
					<td><input type='text' name="boardCount" value="<?=$pointSet['board']['count']?>" size=10 label="게시글작성 횟수" onkeypress="return isNumberKey(event)" class=input> 번 (하루기준)</td>
				</tr>
				<tr>
					<th><span>게시글 지급 포인트</span></th>
                    <td><input type='text' name="boardPoint" value="<?=$pointSet['board']['point']?>" size=10 label="게시글작성 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>댓글 지급 횟수제한</span></th>
					<td><input type='text' name="commentCount" value="<?=$pointSet['comment']['count']?>" size=10 label="댓글작성 횟수" onkeypress="return isNumberKey(event)" class=input> 번 (하루기준)</td>
				</tr>
				<tr>
					<th><span>댓글 지급 포인트</span></th>
                    <td><input type='text' name="commentPoint" value="<?=$pointSet['comment']['point']?>" size=10 label="댓글작성 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>좋아요</b></li>
						<li style='margin-top:8px'>- 좋아요 선택시 지급되는 포인트를 설정한다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>좋아요 지급 포인트</span></th>
					<td><input type='text' name="likePoint" value="<?=$pointSet['like']['point']?>" size=10 label="좋아요 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>호감/비호감</b></li>
						<li style='margin-top:8px'>- 리뷰글 또는 댓글의 호감/비호감 선택시 지급되는 포인트를 설정한다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>호감 지급 포인트</span></th>
					<td><input type='text' name="feelingUpPoint" value="<?=$pointSet['feelingUp']['point']?>" size=10 label="호감 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				<tr>
					<th><span>비호감 지급 포인트</span></th>
                    <td><input type='text' name="feelingDownPoint" value="<?=$pointSet['feelingDown']['point']?>" size=10 label="비호감 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>SNS</b></li>
						<li style='margin-top:8px'>- SNS 공유시 지급되는 포인트를 설정한다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>SNS 공유 포인트</span></th>
					<td><input type='text' name="snsPoint" value="<?=$pointSet['sns']['point']?>" size=10 label="sns 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트</td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
						</dl>
					</div>
				</td>
			</tr>
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
