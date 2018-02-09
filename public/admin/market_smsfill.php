<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-4";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$sql = "SELECT companyaddr, info_email FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)){
	$companyaddr=$row->companyaddr;
	$info_email=$row->info_email;
}

$duoSmsData = duo_smsAuthCheck(); // newsms추가된 항목

#getDuoEmployEsntlKey()

$sql = "SELECT id, authkey, return_tel, sms_uname, admin_tel  FROM tblsmsinfo";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)){
	$return_tel = explode("-",$row->return_tel);
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
	$sms_uname=$row->sms_uname;
	$admin_tel=$row->admin_tel;
}
pmysql_free_result($result);

$isdisabled="1";
$totcnt=0;

// newsms수정된 항목 start
//if(ord($sms_id)==0 || ord($sms_authkey)==0 ) {
if ($duoSmsData[result] == "false"){
	$onload="<script>alert('SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\\n\\nSMS 아이디 및 인증키를 입력하시기 바랍니다.');</script>";
	$isdisabled="0";
} else if ($duoSmsData[result] == "true") {
	/*$smscountdata=getSmscount($sms_id, $sms_authkey);
	if(substr($smscountdata,0,2)=="OK") {
		$totcnt=substr($smscountdata,3);
	} else if(substr($smscountdata,0,2)=="NO") {
		$onload="<script>alert('SMS 회원 아이디가 존재하지 않습니다.\\n\\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="2";
	} else if(substr($smscountdata,0,2)=="AK") {
		$onload="<script>alert('SMS 회원 인증키가 일치하지 않습니다.\\n\\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="3";
	} else {
		$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
		$isdisabled="4";
	}*/
} else {
	$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
}
// newsms수정된 항목 end
$smspayment=array();
$resdata=getSmspaylist();

if(substr($resdata,0,2)=="OK") {
	$tempdata=explode("=",$resdata);
	$smspayment=unserialize($tempdata[1]);
} else {
	alert_go("SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.",-1);
}

include("header.php"); 
echo $onload;
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

<?php if(strstr("023",$isdisabled)){?>
function sms_join() {
	window.open("about:blank","smsjoin","width=1,height=1,scrollbars=no,status=yes");
	document.joinform.submit();
}
<?php }?>

function CheckForm() {
	<?if($isdisabled=="1"){?>
		if(document.smsform.itemprice.value.length==0) {
			alert("SMS머니 충전 사용료를 선택하세요.");
			return;
		}

		$("input[name='itemname']").val($(":radio[name='smspayment']:checked").parent().next().html());
		$("input[name='itemprice']").val($(":radio[name='smspayment']:checked").val());
		$("input[name='pay_count']").val($(":radio[name='smspayment']:checked").parent().parent().find('.pay_count').val());
		
		/*$("form[name='smsform']").submit();*/

		$.post("/front/duoSmsProc.php",$("form[name='smsform']").serialize(),
			function (data){
				if (data.result == "true"){
					alert('등록 되었습니다.');
					location.reload();
				} else {
					alert(data.msg);
					return false;
				}
			}
		);
	<?}?>


	/*
	<?php if($isdisabled=="1"){?>
		if(document.smsform.price.value.length==0) {
			alert("SMS머니 충전 사용료를 선택하세요.");
			return;
		}
		$("#sms_count").val($(":radio[name='smspayment']:checked").parent().next().html());
		window.open("about:blank","smspayment","width=580,height=460,scrollbars=no,status=yes");
		document.smsform.submit();
	<?php }elseif($isdisabled=="0"){?>
		alert("SMS 회원가입 후 SMS 머니 충전이 가능합니다.");
	<?php }elseif($isdisabled=="2"){?>
		alert("SMS 회원 아이디가 존재하지 않습니다.\n\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.");
	<?php }elseif($isdisabled=="3"){?>
		alert("SMS 회원 인증키가 일치하지 않습니다.\n\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.");
	<?php }elseif($isdisabled=="4"){?>
		alert("SMS 서버와 통신이 불가능합니다.\n\n잠시 후 이용하시기 바랍니다.");
	<?php }?>
		*/
}

function change_money(price) {
<?php if($isdisabled=="1"){?>
	document.smsform.itemprice.value=price;
<?php }?>
}

function smsfillinfo() {
<?php if($isdisabled=="1"){?>
	window.open("market_smsfillinfopop.php","smsfillinfo","width=450,height=460,scrollbars=no,status=yes");
<?php }elseif($isdisabled=="0"){?>
	alert("SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\n\nSMS 아이디 및 인증키를 입력하시기 바랍니다.");
<?php }elseif($isdisabled=="2"){?>
	alert("SMS 회원 아이디가 존재하지 않습니다.\n\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.");
<?php }elseif($isdisabled=="3"){?>
	alert("SMS 회원 인증키가 일치하지 않습니다.\n\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.");
<?php }elseif($isdisabled=="4"){?>
	alert("SMS 서버와 통신이 불가능합니다.\n\n잠시 후 이용하시기 바랍니다.");
<?php }?>
}
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; SMS 발송/관리 &gt;<span>SMS 충전하기</span></p></div></div>


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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<table cellpadding="0" cellspacing="0" width="100%">
			<input type=hidden name=type>
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">SMS 충전하기</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>SMS 발송시 필요한 사용료를 충전합니다.</span></div>
				</td>
			</tr>
			
			
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="760">
				<tr>
					<td width="208" valign="top">
					<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
					<TR>
						<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
					</TR>
					<TR>
						<TD height="90" background="images/sms_bg.gif">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="190" align="center">
							<FONT color="white" style="font-weight:bold;">
							<span style="font-size:12pt; letter-spacing:-1pt;">SMS</span>
							<span style="font-size:12pt; letter-spacing:-2pt;"> 발송가능건수<br>
							</span>
							</FONT>
							</td>
						</tr>
						<tr>
							<td width="190" height="45" align="center"><b><FONT style="FONT-SIZE: 19pt; LINE-HEIGHT: 30pt; FONT-FAMILY: 굴림" color="#FFFFCC" face="돋움"><span style="font-size:30pt; letter-spacing:-2pt;">
							<!--<?=number_format($totcnt)?>-->
							<?=number_format($duoSmsData['employ_sms_ea'])?>
							</span></b></FONT></td>
						</tr>
						</table>
						</TD>
					</TR>
					<TR>
						<TD height="26" background="images/sms_down_01.gif" align="center">&nbsp;</TD>
					</TR>
					<tr><td height=20></td></tr>
					<!--tr>
						<td align=center>
						<img src="images/btn_smsfill_view.gif" border="0" onclick="smsfillinfo();" style="cursor:hand;">
						</td>
					</tr-->
					</TABLE>
					</td>
					<td width="20" valign="top">&nbsp;</td>
					<td width="532" valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="522" class="font_size">
						<img src="images/icon_point.gif" width="15" height="11" border="0">핸드폰 문자메세지 서비스는 선불제 서비스입니다. 
						<br><img src="images/icon_point.gif" width="15" height="11" border="0">충전금액은 발송건수에 따라 국내 <b><span class="font_orange" style="font-size:8pt;">최저 20.9원/건당(부과세 포함금액)</span></b>입니다.
						<br><img src="images/icon_point.gif" width="15" height="11" border="0">충전하신 SMS머니는 환불되지 않습니다.<br><img src="images/icon_point.gif" width="15" height="11" border="0">성공한 건수만 과금되며 실패한 건수에 대해서는 과금하지 않습니다.&nbsp;<br>
						</td>
					</tr>
					<tr>
						<td width="522">&nbsp;</td>
					</tr>
					<tr>
						<td width="522" style="padding-bottom:2pt;">
						<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD><IMG SRC="images/market_smsfill_stitle.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
							<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
							<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="522">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD background="images/table_top_line.gif" colspan="4" width="532"></TD>
						</TR>
						<TR>
							<TD class="table_cell" align="center" width="55" align="center">선택하기</TD>
							<TD class="table_cell1" align="center" width="142" align="center"><FONT color=#3d3d3d><B>SMS발송건수</B></FONT></TD>
							<TD class="table_cell1" align="center" width="138" align="center"><FONT color=#3d3d3d><B>사용금액</B></FONT></TD>
							<TD class="table_cell1" align="center" width="132" align="center"><FONT color=#3d3d3d><B>사용단가</B></FONT></TD>
						</TR>
						<TR>
							<TD colspan="4" background="images/table_con_line.gif" width="532"></TD>
						</TR>
						<!-- 요금 -->
						<TR>
							<TD class="td_con2" align="center" width="71" align="center">
								<input type=radio name=smspayment value="3300" onclick="change_money(3300)">
							</TD>
							<TD class="td_con1" align="center" width="149" align="center">100건</TD>
							<TD class="td_con1" align="center" width="145" align="center"><FONT color=#FF8040><B>3,300원</B></FONT></TD>
							<TD class="td_con1" align="center" width="139" align="center">33원/1건<input type = 'hidden' class = 'pay_count' value = '100'></TD>
						</TR>
						<TR>
							<TD colspan="4" background="images/table_con_line.gif" width="532"></TD>
						</TR>
						<TR>
							<TD class="td_con2" align="center" width="71" align="center">
								<input type=radio name=smspayment value="14850" onclick="change_money(14850)">
							</TD>
							<TD class="td_con1" align="center" width="149" align="center">500건</TD>
							<TD class="td_con1" align="center" width="145" align="center"><FONT color=#FF8040><B>14,850원</B></FONT></TD>
							<TD class="td_con1" align="center" width="139" align="center">29.7원/1건<input type = 'hidden' class = 'pay_count' value = '500'></TD>
						</TR>
						<TR>
							<TD colspan="4" background="images/table_con_line.gif" width="532"></TD>
						</TR>
						<TR>
							<TD class="td_con2" align="center" width="71" align="center">
								<input type=radio name=smspayment value="26200" onclick="change_money(26200)">
							</TD>
							<TD class="td_con1" align="center" width="149" align="center">1,000건</TD>
							<TD class="td_con1" align="center" width="145" align="center"><FONT color=#FF8040><B>26,200원</B></FONT></TD>
							<TD class="td_con1" align="center" width="139" align="center">26.2원/1건<input type = 'hidden' class = 'pay_count' value = '1000'></TD>
						</TR>
						<TR>
							<TD colspan="4" background="images/table_con_line.gif" width="532"></TD>
						</TR>
						<TR>
							<TD class="td_con2" align="center" width="71" align="center">
								<input type=radio name=smspayment value="121000" onclick="change_money(121000)">
							</TD>
							<TD class="td_con1" align="center" width="149" align="center">5,000건</TD>
							<TD class="td_con1" align="center" width="145" align="center"><FONT color=#FF8040><B>121,000원</B></FONT></TD>
							<TD class="td_con1" align="center" width="139" align="center">24.2원/1건<input type = 'hidden' class = 'pay_count' value = '5000'></TD>
						</TR>
						<TR>
							<TD colspan="4" background="images/table_con_line.gif" width="532"></TD>
						</TR>
						<TR>
							<TD class="td_con2" align="center" width="71" align="center">
								<input type=radio name=smspayment value="220000" onclick="change_money(220000)">
							</TD>
							<TD class="td_con1" align="center" width="149" align="center">10,000건</TD>
							<TD class="td_con1" align="center" width="145" align="center"><FONT color=#FF8040><B>220,000원</B></FONT></TD>
							<TD class="td_con1" align="center" width="139" align="center">22원/1건<input type = 'hidden' class = 'pay_count' value = '10000'></TD>
						</TR>
						<TR>
							<TD colspan="4" background="images/table_con_line.gif" width="532"></TD>
						</TR>
						<TR>
							<TD class="td_con2" align="center" width="71" align="center">
								<input type=radio name=smspayment value="418000" onclick="change_money(418000)">
							</TD>
							<TD class="td_con1" align="center" width="149" align="center">20,000건</TD>
							<TD class="td_con1" align="center" width="145" align="center"><FONT color=#FF8040><B>418,000원</B></FONT></TD>
							<TD class="td_con1" align="center" width="139" align="center">20.9원/1건<input type = 'hidden' class = 'pay_count' value = '20000'></TD>
						</TR>
						<TR>
							<TD colspan="4" background="images/table_con_line.gif" width="532"></TD>
						</TR>
						<!-- /요금-->
<?php
						/*
						$default_money=0;
						$ii=0;
						for($i=0;$i<count($smspayment);$i++) {
							if($smspayment[$i]["used"]=="Y") {
								if($ii==0) {
									$default_money=$smspayment[$i]["money"];
								}
								echo "<TR>\n";
								echo "	<TD class=\"td_con2\" align=\"center\" width=\"71\" align=\"center\"><input type=radio name=smspayment value=\"{$smspayment[$i]["money"]}\" onclick=\"change_money({$smspayment[$i]["money"]})\" ".($isdisabled=="1"?"":"disabled")."></TD>\n";
								echo "	<TD class=\"td_con1\" align=\"center\" width=\"149\" align=\"center\">".number_format($smspayment[$i]["val"])."건</TD>\n";
								echo "	<TD class=\"td_con1\" align=\"center\" width=\"145\" align=\"center\"><FONT color=#FF8040><B>".number_format($smspayment[$i]["money"])."원</B></FONT></TD>\n";
								echo "	<TD class=\"td_con1\" align=\"center\" width=\"139\" align=\"center\">{$smspayment[$i]["unit"]}원/1건<input type = 'hidden' class = 'pay_count' value = '".$smspayment[$i]["val"]."'></TD>\n";
								echo "</TR>\n";
								echo "<TR>\n";
								echo "	<TD colspan=\"4\" background=\"images/table_con_line.gif\" width=\"532\"></TD>\n";
								echo "</TR>\n";
								$ii++;
							}
						}
						*/
?>						
						<TR>
							<TD background="images/table_top_line.gif" colspan="4" width="532"></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="522" align="center">
						<?php if($isdisabled=="1" && $sms_id && $sms_authkey){?>
						<a href="javascript:CheckForm();"><img src="images/btn_smssave.gif" width="156" height="38" border="0" vspace="10" alt="SMS머니 충전하기"></a>
						<!--a href="javascript:sms_login();"><img src="images/btn_smsmembermodify.gif" width="156" height="38" border="0" vspace="10" alt="SMS 회원정보 수정"></a-->
						<?php }else if(strstr("023",$isdisabled)){?>

						<a href="javascript:sms_join();"><img src="images/btn_smsmemberjoin.gif" width="156" height="38" border="0" vspace="10" alt="SMS 회원가입"></a>

						<?php }?>

						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
			<tr>
				<td>
				
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>						
						<dl>
							<dt><span>SMS 충전하기</span></dt>
							<dd>- SMS 서비스를 도입하시면 저렴한 비용으로 강력한 고객관리를 하실 수 있습니다.<BR>
							- SMS 서비스는 고객관리 기능 확대로 기업매출 증가, 무상 솔루션 제공으로 시스템의 확장성 및 호환성을 확보하실 수 있습니다.
							<br>
							- SMS 충전 입금계좌 : 국민은행 074337-04-010053
							&nbsp;예금주 : (주)커머스랩
							<br>
							 - 신청 버튼 클릭 및 결제 후 반드시 02-3448-0911로 확인전화 부탁드립니다.
							</dd>	
						</dl>

					</div>
					
				
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</form>
			<?php if($isdisabled=="1"){?>
				<!--
				<?$sms_host="front.ajashop.co.kr";?>
				<form name=smsform method=post action="http://<?=$sms_host.$sms_path?>/smspay/sms.pay.php" target="smspayment">
				<input type=hidden name=shopid value="<?=$sms_id?>">
				<input type=hidden name=enckey value="<?=getEncKey($sms_id)?>">
				<input type=hidden name=shopurl value='<?=$_SERVER['HTTP_HOST']?>'>
				<input type=hidden name=price value='<?=$default_money?>'>
				<input type=hidden name=sms_uname value='<?=$sms_uname?>'>
				<input type=hidden name=admin_tel value='<?=$admin_tel?>'>
				<input type=hidden name=companyaddr value='<?=$companyaddr?>'>
				<input type=hidden name=info_email value='<?=$info_email?>'>
				<input type=hidden name=sms_count id=sms_count value=''>
				<input type=hidden name=shop_url  value='<?=$shopurl?>'>
				</form>
				-->

				<form name=smsform method=post action="../front/duoSmsProc.php">
					<input type='hidden' name='mode'  value='pay_add'>
					<input type='hidden' name='pay_count'  value='0'>
					<input type='hidden' name='pay_method'  value='BANK'>
					<input type='hidden' name='user_name'  value='<?=$sms_uname?>'>
					<input type='hidden' name='user_no'  value='0'>
					<input type='hidden' name='shop_id'  value='0'>
					<input type='hidden' name='itemprice'  value=''>
					<input type='hidden' name='itemname'  value=''>
					<input type='hidden' name='result_code'  value=''>
					<input type='hidden' name='pay_logs_code'  value='002003'>
					<input type='hidden' name='tno'  value='0'>
					<input type='hidden' name='auth_code'  value='0'>
				</form>
				<form name=loginform method=get action="http://<?=$sms_host.$sms_path?>/member/login.html" target="smslogin">
				</form>
			<?php }elseif(strstr("023",$isdisabled)){?>
			<?list($sms_host,$sms_path)=getSmshost($sms_path);?>
			<!--<form name=joinform method=post action="http://<?=$sms_host.$sms_path?>/member/member_join.html" target="smsjoin">-->
			<form name=joinform method=post action="./member_join.html" target="smsjoin">
			<input type=hidden name=shopurl value="<?=$shopurl?>">
			</form>
			<form name=findform method=post action="http://<?=$sms_host.$sms_path?>/member/member_join2.html" target="smsjoin">
			<input type=hidden name=shopurl value="<?=$shopurl?>">
			</form>
			<?php }?> 
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<?php 
include("copyright.php");
