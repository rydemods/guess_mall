<!DOCTYPE html PUBtdC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=8;IE=EDGE">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body style="margin:0;padding:0; font-size:12px; font-family:dotum; color:#838383">
<table width="700" cellpadding=0 border=0 cellspacing=0 summary="가상계좌안내 메일">
	<tr>
		<td style="padding:17px 0 11px 0; border-bottom:3px solid #4b4b4b; text-align:center">
			<a href="#" target="_blank"><img border="0"  src="http://<?=$shopurl?>/static/img/mail/logo.gif" alt="C.A.S.H"></a>
		</td>
	</tr>
	<tr>
		<td align="center" style="padding:30px 0 22px 0"><img border="0"  src="http://<?=$shopurl?>/static/img/mail/title_imagine_account.gif" alt="가상계좌안내"></td>
	</tr>
	<tr>
		<td style="padding-left:20px; padding-bottom:40px">
		<!-- 본문 시작 -->
			
			<!-- 기본 안내 멘트 -->
			<table width="660" cellpadding=0 border=0 cellspacing=0 >
				<tr>
					<td bgcolor="#f3f3f3" style=" padding:20px;border-top:1px solid #ccc; border-bottom:1px solid #ccc;">
						<strong style="display:block;font-size:14px; color:#838383; font-family:dotum"><span style="color:#4b4b4b">[NAME]</span> 고객님 안녕하세요</strong>
						<div style="padding-top:20px; color:#838383; font-size:12px; font-family:dotum; line-height:1.3">
							고객님께서 주문하신 주문번호는 <strong style="color:#4b4b4b">[ORDERCODE] </strong>입니다.<br>
							무통장은 5일 안, 가상계좌는 2일 안에 입금해주세요. <br>
							주문내역은 <span style="color:#4b4b4b">MY PAGE > MY ORDER > 주문배송조회</span> 에서 확인하실 수 있습니다.
						</div>
					</td>
				</tr>
				<tr>
					<td style="text-align:center; padding-top:20px"><a href="http://<?=$shopurl?>/front/mypage.php" target="_blank"><img border="0" src="http://<?=$shopurl?>/static/img/mail/btn_mypage.gif" alt="마이페이지 이동"></a></td>
				</tr>
			</table>
			<!-- //기본 안내 멘트 -->

			<div style="padding-top:20px; text-align:left; color:#4b4b4b; font-size:13px; font-family:dotum;font-weight:bold">주문정보</div>
			<table width="660" cellpadding=0 border=0 cellspacing=0 >
				<colgroup><col style="width:25%"><col style="width:25%"><col style="width:25%"><col style="width:25%"></colgroup>
				<thead>
					<tr height="25" bgcolor="#4b4b4b">
						<th align="center" scope="col" style="font-size:12px; padding-top:2px; font-family:dotum;color:#fff">주문일자</th>
						<th align="center" scope="col" style="font-size:12px; padding-top:2px; font-family:dotum;color:#fff">주문번호</th>
						<th align="center" scope="col" style="font-size:12px; padding-top:2px; font-family:dotum;color:#fff">주문자 이름</th>
						<th align="center" scope="col" style="font-size:12px; padding-top:2px; font-family:dotum;color:#fff">결제방법</th>
					</tr>
				</thead>
				<tbody>
					<tr height="27">
						<td align="center" style="font-size:12px; padding-top:2px; color:#838383">[ORDERDATE]</td>
						<td align="center" style="font-size:12px; padding-top:2px; color:#838383">[ORDERCODE]</td>
						<td align="center" style="font-size:12px; padding-top:2px; color:#838383">[NAME]</td>
						<td align="center" style="font-size:12px; padding-top:2px; color:#838383">[PAYTYPE]</td>
					</tr>
					<tr><td colspan="4" bgcolor="#838383" height="1"></td></tr>
				</tbody>
			</table>

			<div style="padding-top:20px; text-align:left; color:#4b4b4b; font-size:13px; font-family:dotum;font-weight:bold">가상계좌안내</div>
			<table  width="660" cellpadding=0 border=0 cellspacing=0>
				<colgroup><col style="width:110px"><col style="width:550px"></colgroup>
				<tr><td colspan="7" bgcolor="#cccccc" height="1"></td></tr>
				<tr height="25">
					<th style="padding-left:10px; padding-top:2px; background-color:#f3f3f3; font-size:12px; color:#4b4b4b; text-align:left;font-family:dotum;">입금금액</th>
					<td style="font-size:12px; padding-top:2px;color:#838383; padding-left:10px;font-family:dotum;">[BANKPRICE]</td>
				</tr>
				<tr><td colspan="7" bgcolor="#cccccc" height="1"></td></tr>
				<tr height="25">
					<th style="padding-left:10px; padding-top:2px; background-color:#f3f3f3; font-size:12px; color:#4b4b4b; text-align:left;font-family:dotum;">선택은행</th>
					<td style="font-size:12px; padding-top:2px;color:#838383; padding-left:10px;font-family:dotum;">[BANKNAME]</td>
				</tr>
				<tr><td colspan="7" bgcolor="#cccccc" height="1"></td></tr>
				<tr height="25">
					<th style="padding-left:10px; padding-top:2px; background-color:#f3f3f3; font-size:12px; color:#4b4b4b; text-align:left;font-family:dotum;">입금계좌번호</th>
					<td style="font-size:12px; padding-top:2px;color:#838383; padding-left:10px;font-family:dotum;">[BANKNUM]</td>
				</tr>
				<tr><td colspan="7" bgcolor="#4b4b4b" height="1"></td></tr>
			</table>

		<!-- //본문 끝 -->
		</td>
	</tr>
	<tr>
		<td style="border-top:1px solid #838383; border-bottom:1px solid #4b4b4b; padding:15px 0">
		<!-- footer -->

			<table width="700" cellpadding=0 border=0 cellspacing=0 summary="C.A.S.H 메일 전송 정보">
				<tr>
					<td width="120" align="center" valign="top"><a href="http://[URL]" target="_blank"><img border="0"  src="http://<?=$shopurl?>/static/img/mail/logo_footer.gif" alt="C.A.S.H" width="73" height="27"></a></td>
					<td>
						<div style="font-size:11px; color:#aaa;font-family:dotum;">
							본 메일은 [CURDATE]기준, 회원님의 수신동의여부를 확인한 결과 수신동의를 하셨기에 발송되었습니다. <br>
							수신을 원치 않으시면 <a href="http://[URL]front/login.php?chUrl=/front/mypage.php" target="_blank" style="color:#4b4b4b;"><strong>이곳</strong></a>에 로그인 후 수신거부를 해주시기 바랍니다. <br>
							If you dont't want this type of information or e-mail, please <a href="http://[URL]front/login.php?chUrl=/front/mypage.php" target="_blank"  style="color:#4b4b4b;"><strong>click here.</strong></a><br>
							본 메일은 발신 전용으로 회신되지 않으며, <span  style="color:#4b4b4b;">관련문의는 <a href="http://[URL]front/mypage_personal.php?mode=write" target="_blank" style="color:#4b4b4b; text-decoration:none">Cs center</a></span>을 이용해주시기 바랍니다.
						</div>
						<div style="padding-top:15px"><img src="http://<?=$shopurl?>/static/img/mail/copyright.gif" alt="COPYRIGHT"></div>
					</td>
				</tr>
			</table>

		<!-- //footer -->
		</td>
	</tr>
</table>


</body>
</html>
