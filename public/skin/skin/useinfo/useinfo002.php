<table cellpadding="0" cellspacing="0" width="96%">
<tr>
	<td>&nbsp;- <?=$_data->shopname?>을 방문해 주셔서 감사합니다.
	<br>&nbsp;- <?=$_data->shopname?> 인터넷 쇼핑몰은 회원제를 실시하고 있습니다.
	<br>&nbsp;- 처음오신 분은 먼저 <a href="<?=$Dir.FrontDir?>member_agree.php"><b><font color="#0099CC">회원가입</font></b></a>을 하신 후 이용하시길 바랍니다.
	<? if ($_data->member_buygrant=="U") { ?>
	<br>&nbsp;- 회원가입을 안하시더라도 <b>비회원의 자격</b>으로 상품을 구입하실 수 있습니다.
	<?}?></td>
</tr>
<tr>
	<td height="20"></td>
</tr>
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_stbg.gif"><font color="#F02800"><b>상품주문안내</b></font></td>
			<td><img src="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_stend.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td style="padding-left:20px;">①&nbsp;각 코너를 클릭하셔서 들어갑니다.
		<br>②&nbsp;"바로가기"메뉴 또는 상품사진이나 상품명을 클릭하세요.
		<br>③&nbsp;"장바구니 담기"를 클릭하세요.
		<br>④&nbsp;"장바구니에 넣었습니다" 메시지가 출력되며 주문상품을 확인 후 "주문버튼"을 클릭하세요.
		<br>⑤&nbsp;주문버튼을 누르면, "주문서" 작성페이지가 출력되며 주문서를 작성 후 "주문"을 클릭하면 주문이 완료됩니다.</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_stbg.gif"><font color="#F02800"><b>쇼핑몰 연락처</b></font></td>
			<td><img src="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_stend.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td style="padding-left:20px;">①&nbsp;전화 : <?=$_data->info_tel?><br>
		② 주소 : <?=$_data->info_addr?></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_stbg.gif"><font color="#F02800"><b>배송안내</b></font></td>
			<td><img src="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_stend.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td style="padding-left:20px;">배송방법은 <font color="#FF4C00"><b>
<?
		if ($_data->deli_type=="T") echo "택배";
		else if ($_data->deli_type=="P") echo "빠른등기";
		else if ($_data->deli_type=="I") echo "일반등기";
		else if ($_data->deli_type=="X") echo "택배 + 빠른등기";
		else if ($_data->deli_type=="S") echo "택배 + 일반등기";
		else if ($_data->deli_type=="M") echo "직접배송";
?>
		</b></font> 입니다.<br>
		<br>① 주문하신 날로부터 <?=$_data->deli_setperiod?> ~ <?=$_data->deli_setperiod+3?>일 안에 받을 수 있습니다.
		<?  if ($_data->payment_type=="Y" || $_data->payment_type=="N") { $payment_mothod_C="③";?>
		<br>② 온라인 입금시 입금 확인 후 <?=$_data->deli_setperiod?> ~ <?=$_data->deli_setperiod+3?>일
		<?  } else { $payment_mothod_C="②"; }?>
		<?  if ($_data->payment_type=="Y" || $_data->payment_type=="C") { ?>
		<br><?=$payment_mothod_C?> 신용카드 결제시 주문 후 <?=$_data->deli_setperiod?> ~ <?=$_data->deli_setperiod+3?>일
		<?  } ?>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_stbg.gif"><font color="#F02800"><b>교환/반품/환불 안내</b></font></td>
			<td><img src="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_stend.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td style="padding-left:20px;">① 고객의 변심에 의한 교환 및 반품인 경우에는 배송비는 <font color="#FF4C00"><b><?=($_data->return2_type=="1"?"판매자":"소비자")?></b></font>부담입니다.
			<br>② 상품의 이상에 의한 교환 및 반품인 경우에는 배송비는 <font color="#FF4C00"><b><?=($_data->return1_type=="1"?"판매자":"소비자")?></b></font>부담입니다.
			<br>③ 문의 : <?=$_data->info_tel?></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_stbg.gif"><font color="#F02800"><b>개인정보 보호 정책 </font><A HREF="javascript:privercy();"><font color="#0099CC">[개인정보 보호정책]</b></font></a></td>
			<td><img src="<?=$Dir?>images/common/useinfo/<?=$_data->design_useinfo?>/design_useinfo_skin1_stend.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td style="padding-left:20px;">① 담당 : <font color="#F02800"><b><?=$_data->privercyname?></b></font>
			<br>② 전화 : <font color="#F02800"><b><?=$_data->info_tel?></b></font>
			<br>③ 메일 : <a href="mailto:<?=$_data->privercyemail?>"><?=$_data->privercyemail?></a></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="20"></td>
</tr>
</table>