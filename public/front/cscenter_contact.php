<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$mode=$_POST["mode"];

$u_name=$_POST["u_name"];
$u_subject=$_POST["u_subject"];
$u_email=$_POST["u_email"];
$u_content=$_POST["u_content"];
$u_hp=$_POST["u_hp"];
$chk_login = $_ShopInfo->getMemid();

if($mode=="write") {
	$ip=$_SERVER["REMOTE_ADDR"];
	$u_hp=(int)$u_hp;
	$date=time();
	
	$sql = "
				INSERT INTO 
					tblboard 
					(
						board,
						name,
						email,
						ip,
						title,
						content	,
						m_no,
						writetime
					) 
				VALUES 
					(
						'contactus',
						'{$u_name}', 
						'{$u_email}', 
						'{$ip}', 
						'{$u_subject}', 
						'{$u_content}', 
						{$u_hp},
						($date)				
					)
	";
	
	if(pmysql_query($sql,get_db_conn())) {
		echo  "<script>alert(' 정상적으로 등록되었습니다..'); location.href=\"/front/cscenter_contact.php\"</script>";
	} else {
		echo "<script>alert(' 오류가 발생하였습니다.'); location.href=\"/front/cscenter_contact.php\"</script>";
	}
}

$sql="SELECT privercy FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$privercy_exp=@explode("=", $row->privercy);
$privercy=$privercy_exp[1];
pmysql_free_result($result);

$pattern=array("[SHOP]","[COMPANY]");
$replace=array($_data->shopname, $_data->companyname);
$agreement = str_replace($pattern,$replace,$agreement);

if(ord($privercy)==0) {
	$buffer = file_get_contents($Dir.AdminDir."privercy2.txt");
	$privercy=$buffer;
}

$pattern=array("[SHOP]","[NAME]","[EMAIL]","[TEL]");
$replace=array($_data->shopname,$_data->privercyname,"<a href=\"mailto:{$_data->privercyemail}\">{$_data->privercyemail}</a>",$_data->info_tel);
$privercy = str_replace($pattern,$replace,$privercy);


$pattern=array("[CONTRACT]","[PRIVERCY]","[CHECK]","[CHECKP]","[OK]","[REJECT]");
$replace=array($privercy,"<input type=checkbox id=\"idx_agree\" name=agree style=\"border:none;\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_agree>","<input type=checkbox id=\"idx_agreep\" name=agreep style=\"border:none;\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_agreep>","javascript:CheckForm()","javascript:history.go(-1)");
$body=str_replace($pattern,$replace,$body);
echo "<tr>\n";
echo "	<td align=center>{$body}</td>";
echo "</tr>\n";
echo "<tr>\n";
echo "</form>";
echo "</table>";

#####좌측 메뉴 class='on' 을 위한 페이지코드
$page_code='contact_us';

?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<TITLE><?=$_data->shoptitle?> - CONTACT US</TITLE>

<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function select_domain(val){
	document.form1.em1.value=val;
}
function CheckForm() {
	//alert(document.form1.agree[1].checked);
	var cnt =6;
	var chk_login = "<?=$chk_login?>";
	if(!chk_login){
		if(document.form1.agree[1].checked==true){
			alert("개인정보 수집에 동의해 주세요.");
			cnt = cnt-1;
			return;
		}
	}
	document.form1.u_email.value=document.form1.em0.value+"@"+document.form1.em1.value;
	document.form1.u_hp.value=document.form1.hp0.value+"-"+document.form1.hp1.value+"-"+document.form1.hp2.value;
	
	if(document.form1.u_name.value.length==0) {
		alert("이름을 입력하세요.");
		document.form1.u_name.focus();
		cnt = cnt-1;
		return ;
	}

	if(document.form1.u_hp.value.length==0) {
		alert("전화번호를 입력하세요.");
		document.form1.u_hp.focus();
		cnt = cnt-1;
		return ;
	}

	if(document.form1.u_email.value.length>0) {
		if(!IsMailCheck(document.form1.u_email.value)) {
			alert("이메일 입력이 잘못되었습니다.");
			document.form1.u_email.focus();
			cnt = cnt-1;
			return;
		}
	}

	if(document.form1.u_subject.value.length==0) {
		alert("문의제목을 입력하세요.");
		document.form1.u_subject.focus();
		cnt = cnt-1;
		return;
	}

	

	if(document.form1.u_content.value.length==0) {
		alert("문의내용을 입력하세요.");
		document.form1.u_content.focus();
		cnt = cnt-1;
		return;
	}

	if( cnt ==6){
		document.form1.mode.value="write";
		document.form1.submit();
	}
	
}
	
//-->
</SCRIPT>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
 

<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<input type=hidden name=mode>
<tr>
	<td>

			<?
			$subTop_flag = 3;
			//include ($Dir.MainDir."sub_top.php");
			?>
			<div class="containerBody sub_skin">
				
				<div class="left_lnb">
				<?	$lnb_flag = 5;
					$board = "contact_us";
					include ($Dir.MainDir."lnb.php");
				?>
				</div>

				<div class="right_section">

					<div class="customer_notice_wrap">
						
						<h3 class="title">
							CONTACT US
							<p class="line_map"><a>홈</a> &gt; <a>고객센터</a> &gt; <a class="on">CONTACT US</a></p>
						</h3>
						<?if(!$chk_login){?>
						<div class="join_agree">
							<div class="title">
								<h3>개인정보 수집 동의</h3>
							</div>
							<div class="agree_ment" style="width:838px">
								<?=$privercy?>
							</div>
							<div class="ment">
								비회원 글작성에 대한 개인정보 수집에 동의합니다.
								<span><input type="radio" name="agree" /> 동의함</span>
								<span><input type="radio" name="agree" checked="checked"/> 동의하지 않음</span>
							</div>
						</div><!-- //이용약관 -->
						<?}?>
								
						<!--작성-->						
						<div class="customer_notice_wrap mt_40">
							<table class="write_table" summary="작성할 글의 제목, 작성자, 전화번호, 이메일, 문의내용을 작성하고 파일을 업로드 할 수 있습니다.">
								<caption>광고/제휴 문의</caption>
								<colgroup>
									<col style="width:121px" />
									<col style="width:auto" />
								</colgroup>
								<tbody>
									
									<tr>
										<th scope="row">이름</th>
										<td class="name"><input type="text" name="u_name" value="<?=$u_name?>" title="이름을 입력하세요." /></td>
									</tr>
									<tr>
										<th scope="row">전화번호</th>
										<td class="phone">
											<input type="text" name="hp0" title="전화번호 앞 자리를 입력하세요." maxlength='3'/><span>-</span>
											<input type="text" name="hp1" title="전화번호 가운데 자리를 입력하세요." maxlength='4'/><span>-</span>
											<input type="text" name="hp2" title="전화번호 뒷 자리를 입력하세요." maxlength='4'/>
											<input type="hidden" name="u_hp" value="<?=$u_hp?>"/>
										</td>
									</tr>
									<tr>
										<th scope="row">이메일</th>
										<td class="email">
											<input type="text" name="em0" title="이메일 아이디를 입력하세요." /><span>@</span>
											<input type="text" name="em1" title="이메일 도메인을 입력하세요." />
											<select title="이메일 도메인을 선택하세요."  onchange="select_domain(this.value)">
												<option value="">선택하세요</option>
												<option value="naver.com">naver.com</option>
												<option value="daum.net">daum.net</option>
												<option value="nate.com">nate.com</option>
												<option value="gmail.com">gmail.com</option>
												<option value="hotmail.com">hotmail.com</option>
												<option value="yahoo.co.kr">yahoo.co.kr</option>
												<option value="">직접입력</option>
											</select>
											<input type="hidden" name="u_email" value="<?=$u_email?>"/>
										</td>
									</tr>
									<tr>
										<th scope="row">제목</th>
										<td class="title">
											<input type="text" name="u_subject" value="<?=$u_subject?>"title="제목을 입력하세요." />
										</td>
									</tr>
									<tr>
										<th scope="row">내용</th>
										<td class="title">
											<textarea  name="u_content" id="" cols="30" rows="10" style="width:585px"><?=$u_content?></textarea>
										</td>
									</tr>
								</tbody>
							</table>
			
							<div class="ta_c mt_30">
								<a href="javascript:CheckForm();" target="_self" class="btn_D">등록</a>
							</div>
						</div><!--//작성-->
						
					</div>
				</div>

			</div>

	</td>
</tr>

</form>
</table>
<div id="create_openwin" style="display:none"></div>
<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
