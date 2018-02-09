<?php
/********************************************************************* 
// 파 일 명		: affiliates_register.php 
// 설     명		: 제휴 학교/회사 등록,수정,삭제 폼
// 상세설명	: 관리자 제휴 학교/회사 등록,수정,삭제 폼
// 작 성 자		: 2015.10.26 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/adminlib.php");
	include("access.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "me-4";
	$MenuCode = "member";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$no=$_GET[no];
	$mode=$_GET[mode];

	if(!$mode=="affiliates_mod"){
		$mode="affiliates_ins";
	}

	if ($mode=="affiliates_ins") {
		$title_text	= "등록";
	} else if ($mode=="affiliates_mod") {
		$title_text	= "수정";
	}

#---------------------------------------------------------------
# 번호에 해당하는 정보를 불러온다. (수정시)
#---------------------------------------------------------------
	$board_row=pmysql_fetch_object(pmysql_query("select *  from tblaffiliatesinfo where idx={$no}"));

#---------------------------------------------------------------
# 이미지 저장을 위한 기본정보를 설정한다.
#---------------------------------------------------------------
	$imagepath=$Dir.DataDir."shopimages/affiliates_logo/";
	
	include"header.php"; 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">
function affiliates_del(no){
	if(confirm("해당 게시물을 삭제 하시겠습니까?")){
		document.form2.no.value=no;
		document.form2.mode.value="affiliates_del";
		document.form2.submit();	
	}

}

function affiliates_mod(no){
	document.form1.no.value=no;
	document.form1.mode.value="affiliates_mod";
	document.form1.submit();
}

function affiliates_indb(no){
	
	if(document.form1.rf_name.value.length==0) {
		document.form1.rf_name.focus();
		alert("학교/기업명을 입력하세요");
		return;
	}

	document.form1.no.value=no;
	document.form1.action="affiliates_indb.php";
	document.form1.submit();
}

function goBackList(){
	location.href="affiliates_board.php";
}

function DeletePrdtImg(no, temp){
	if(confirm('해당 이미지를 삭제하시겠습니까?')){
		document.cForm.no.value=no;
		document.cForm.mode.value="affiliates_img_del";
		document.cForm.delprdtimg.value=temp;
		document.cForm.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 제휴 학교/회사 관리 &gt;<span>제휴 학교/회사 <?=$title_text?></span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">제휴 학교/회사 <?=$title_text?></div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>제휴 학교/회사를 <?=$title_text?>할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type="hidden" name="listMode" id="listMode" value=""/>
			<input type=hidden name=type>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=no value="<?=$board_row->idx?>">			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">제휴 학교/회사 <?=$title_text?></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<th><span>구분</span></th>
					<TD><select id="rf_type" name="rf_type">
					<option value="1"<?if ($board_row->type == '1') echo " selected";?>>학교</option>
					<option value="2"<?if ($board_row->type == '2') echo " selected";?>>기업</option>
					</select></TD>
				</tr>
				<tr>
					<th><span>지역</span></th>
					<TD>
					<select id="rf_area" name="rf_area">
						<option value="서울/경기"<?if ($board_row->area == '서울/경기') echo " selected";?>>서울/경기</option>
						<option value="경북/경남"<?if ($board_row->area == '경북/경남') echo "selected";?>>경북/경남</option>
						<option value="전북/전남"<?if ($board_row->area == '전북/전남') echo "selected";?>>전북/전남</option>
						<option value="충북/충남"<?if ($board_row->area == '충북/충남') echo "selected";?>>충북/충남</option>
						<option value="강원도"<?if ($board_row->area == '강원도') echo "selected";?>>강원도</option>
						<option value="제주"<?if ($board_row->area == '제주') echo "selected";?>>제주</option>
						<option value="기타"<?if ($board_row->area == '기타') echo "selected";?>>기타</option>
					</select></TD>
				</tr>
				<tr>
					<th><span>학교/회사명</span></th>
					<TD><INPUT maxLength=80 size=80 id="rf_name" name="rf_name" value="<?=$board_row->name?>"><br>
					<span class="font_orange">접속 되는 URL에 표시될 명칭을 입력해주세요.<br>학교명, 카페명 등을 입력해주세요.</span></TD>
				</tr>
				<tr>
					<th><span>배너 접속경로</span></th>
					<TD><INPUT maxLength=80 size=80 id="rf_referrer_url" name="rf_referrer_url" value="<?=$board_row->referrer_url?>"><br>
					<span class="font_orange">접속 되는 URL 를 입력해주세요.<br>http:// 를 제외한 실제 도메인 주소만 입력해주세요. <br>www. 도 제외합니다. (예: www.ygoon.com -> ygoon.com)</span></TD>
				</tr>
				<tr>
					<th><span>이메일 접속경로</span></th>
					<TD><INPUT maxLength=80 size=80 id="rf_referrer_email_url" name="rf_referrer_email_url" value="<?=$board_row->referrer_email_url?>"><br>
					<span class="font_orange">접속 이메일 도메인 주소를 입력해주세요.<br>@ 를 제외한 실제 이메일 도메인 주소만 입력해주세요. (예: admin@ygoon.com -> ygoon.com)</span></TD>
				</tr>
				<tr>
					<th><span>이동경로</span></th>
					<TD><INPUT maxLength=80 size=80 id="rf_url" name="rf_url" value="<?=$board_row->url?>"><br>
					<span class="font_orange">사용자페이지에서 클릭시 이동될 URL 를 넣어주세요.</span></TD>
				</tr>
				<tr>
					<th><span>로고 이미지</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="logofile" style="WIDTH: 400px"><br>
						<span class="font_orange">(권장이미지 : 268px X 30px)</span>
						<input type=hidden name="vlogoImage" value="<?=$board_row->logoimage?>">
	<?php
				if ($board_row) {
					if ( ord($board_row->logoimage) && file_exists($imagepath.$board_row->logoimage) ){
						echo "<br><img src='".$imagepath.$board_row->logoimage."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/affiliates_logo/{$board_row->logoimage}' style=\"width:84px\">";
						echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('{$no}','0')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
					} else {
						echo "<br><img src=images/space01.gif>";
					}
				}
	?>
					</td>
				</tr>
				<tr>
					<th><span>사용</span></th>
					<TD><select id="rf_use" name="rf_use">
					<option value="1"<?if ($board_row->use == '1') echo " selected";?>>사용</option>
					<option value="0"<?if ($board_row->use == '0') echo " selected";?>>사용안함</option>
					</select><br>
					<span class="font_orange">사용자 페이지에 노출 여부를 선택합니다.</span></TD>
				</tr>
				<tr>
					<th><span>출력</span></th>
					<TD><select id="rf_output" name="rf_output">
					<option value="1"<?if ($board_row->output == '1') echo " selected";?>>사용</option>
					<option value="0"<?if ($board_row->output == '0') echo " selected";?>>사용안함</option>
					</select><br>
					<span class="font_orange">사용자 페이지에 노출 여부를 선택합니다.</span></TD>
				</tr>
				<tr>
					<th><span>쿠폰번호</span></th>
					<TD><INPUT maxLength=40 size=40 id="rf_coupon" name="rf_coupon" value="<?=$board_row->coupon?>"><br>
					<span class="font_orange">레노버 쿠폰번호를 입력해주세요.</span></TD>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan=8 align=center>
			<?if($mode=="affiliates_ins"){?>
				<a href="javascript:affiliates_indb('<?=$no?>');"><img src="images/btn_confirm_com.gif"></a>
			<?}else{?>
				<a href="javascript:affiliates_indb('<?=$no?>');"><img src="images/btn_modify_com.gif"></a> <a href="javascript:affiliates_del('<?=$no?>');"><img src="images/btn_infodelete.gif"></a>
			
			<?}?><a href="javascript:goBackList();"><img src="img/btn/btn_list.gif"></a></td>
			</tr>			
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>제휴 학교/회사 <?=$title_text?></span></dt>
							<dd>- <br>
							- <br>
							- 
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>		
			<form name=form2 method=post action="affiliates_indb.php">
			<input type=hidden name=no>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=title>

			</form>
			<form name=cForm action="affiliates_indb.php" method=post>
			<input type=hidden name=no>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=delprdtimg>
			<input type=hidden name="vimage" value="<?php if ($board_row) echo $board_row->logoimage; ?>">
			</form>
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
