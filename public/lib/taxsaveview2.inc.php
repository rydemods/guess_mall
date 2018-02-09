<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>현금영수증</title>
<style type="text/css">
body {margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;font-family: "돋움", "돋움체";font-size: 8pt;color: #303030;}
.total {font-family: "돋움", "돋움체";font-size: 8pt;font-weight: bold;color: #FF5400;}
.gray {font-family: "돋움", "돋움체";font-size: 8pt;color: #7F7F7F;}
</style>
<script language="javascript">
    function cleanPrinting()
    {
        for( i = 1; i <= 2; i++ ) document.getElementById( "bottomImg" + i ).style.display = "none";
        alert( "출력시 배경이미지가 나오지 않을 경우,\n\n[도구]메뉴 - [인터넷 옵션] - [고급]탭 - 인쇄 항목을 확인해주시기 바랍니다." );
        window.print();
        document.getElementById( "bottomImg2" ).style.display = "inline";
    }

	function PageResize() {
		var oWidth = document.all.table_body.clientWidth + 10;
		var oHeight = document.all.table_body.clientHeight + 80;

		window.resizeTo(oWidth,oHeight);
	}

</script>
</head>
<body style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onLoad="PageResize();">

<?if($_taxdata["msg_type"]=="7102"){?>
<div id="Layer1" style="position:absolute; left:250px; top:390px; width:116px; height:114px; z-index:1; visibility: visible"><img src="<?=$Dir?>images/taxsaveviewpop_mark.gif" width="104" height="106"></div>
<?}?>

<table width="360" border="0" cellspacing="0" cellpadding="0" id=table_body>
<tr>
	<td style="padding-top:20px; padding-left:20px; padding-right:20px; padding-bottom:5px">
	<table border=0 cellpadding=10 cellspacing=1 bgcolor=#eeeeee>
	<tr>
		<td bgcolor=#FFFFFF>
		<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td>
			<!--★거래정보 시작-->
			<table width="305" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td height="2" colspan="2" bgcolor="#FACAB7"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EE9B6A"></td>
				</tr>
				<tr>
					<td height="17" bgcolor="#F6F5ED"><img src="<?=$Dir?>images/taxsaveviewpop_bul01.gif" width="13" height="10" align="absmiddle">
						<strong>전화번호/주민등록번호</strong>
					</td>
					<td bgcolor="#F6F5ED"><img src="<?=$Dir?>images/taxsaveviewpop_bul01.gif" width="13" height="10" align="absmiddle"><strong>거래유형(사용용도)</strong></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#FACAB7"></td>
				</tr>
				<tr>
					<td height="17"><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle">
					<?=str_pad(substr($_taxdata["id_info"],0,6),strlen($_taxdata["id_info"]),"*")?>
					</td>
					<td><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle">
					현금결제<?=($_taxdata["msg_type"]=="7102"?"취소":"")?>(소득공제)                
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#FACAB7"></td>
				</tr>
				<tr>
					<td height="17" bgcolor="#F6F5ED"><img src="<?=$Dir?>images/taxsaveviewpop_bul01.gif" width="13" height="10" align="absmiddle">
					<strong>이름</strong>                
					</td>
					<td bgcolor="#F6F5ED"><img src="<?=$Dir?>images/taxsaveviewpop_bul01.gif" width="13" height="10" align="absmiddle"><strong>거래일시</strong></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#FACAB7"></td>
				</tr>
				<tr>
					<td height="17"><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle"><?=$_taxdata["cons_name"]?></td>
					<td><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle">
					<?=($_taxdata["msg_type"]=="7100"?"20".substr($_taxdata["tsdtime"],0,2)."-".substr($_taxdata["tsdtime"],2,2)."-".substr($_taxdata["tsdtime"],4,2)." ".substr($_taxdata["tsdtime"],6,2).":".substr($_taxdata["tsdtime"],8,2).":".substr($_taxdata["tsdtime"],10,2):"")?>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#FACAB7"></td>
				</tr>
				<tr>
					<td height="17" bgcolor="#F6F5ED"><img src="<?=$Dir?>images/taxsaveviewpop_bul01.gif" width="13" height="10" align="absmiddle"><strong>승인번호</strong></td>
					<td bgcolor="#F6F5ED"><img src="<?=$Dir?>images/taxsaveviewpop_bul01.gif" width="13" height="10" align="absmiddle"><strong>거래취소일시</strong></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#FACAB7"></td>
				</tr>
				<tr>
					<td height="17"><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle">
					<?=$authno?>                
					</td>
					<td><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle">
					<?=($_taxdata["msg_type"]=="7102"?"20".substr($_taxdata["tsdtime"],0,2)."-".substr($_taxdata["tsdtime"],2,2)."-".substr($_taxdata["tsdtime"],4,2)." ".substr($_taxdata["tsdtime"],6,2).":".substr($_taxdata["tsdtime"],8,2).":".substr($_taxdata["tsdtime"],10,2):"")?>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#FACAB7"></td>
				</tr>
				<tr>
					<td height="17" bgcolor="#F6F5ED"><img src="<?=$Dir?>images/taxsaveviewpop_bul01.gif" width="13" height="10" align="absmiddle"><strong>주문번호</strong></td>
					<td bgcolor="#F6F5ED"><img src="<?=$Dir?>images/taxsaveviewpop_bul01.gif" width="13" height="10" align="absmiddle"><strong>상품명</strong></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#FACAB7"></td>
				</tr>
				<tr>
					<td height="17"><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle"><?=$_taxdata["orderid"]?></td>
					<td><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle"><?=titleCut(20,$_taxdata["prod_name"])?></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EE9B6A"></td>
				</tr>
				<tr>
					<td height="2" colspan="2" bgcolor="#FACAB7"></td>
				</tr>
			</table>
			<!--★거래정보 끝-->
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td>
			<!--★결제금액 시작-->
			<table width="305" border="0" cellspacing="0" cellpadding="3">
				<tr>
					<td height="108" align="right" background="<?=$Dir?>images/taxsaveviewpop_table.gif">
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="23" height="17">&nbsp;</td>
								<td width="23">&nbsp;</td>
								<td width="23">&nbsp;</td>
								<td width="23">&nbsp;</td>
								<td width="23">&nbsp;</td>
								<td width="23">&nbsp;</td>
								<td width="23">&nbsp;</td>
								<td width="23">&nbsp;</td>
								<td width="23">&nbsp;</td>
							</tr>
							<!--금액-->
							<tr>
							<?
							for($i=0;$i<9;$i++) {
								$tempnum="";
								if((9-$i)<=strlen($_taxdata["amt2"])) {
									$jj=strlen($_taxdata["amt2"])-(9-$i);
									$tempnum=substr($_taxdata["amt2"],$jj,1);
								}
								if(strlen($tempnum)==0) {
									echo "<td height='17' align='center'>&nbsp;</td>";
								} else {
									echo "<td height='17' align='center'><strong>".$tempnum."</strong></td>";
								}
							}
							?>
							</tr>
							<tr>
								<td height="17">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
							<!--VAT-->
							<?
							for($i=0;$i<9;$i++) {
								$tempnum="";
								if((9-$i)<=strlen($_taxdata["amt4"])) {
									$jj=strlen($_taxdata["amt4"])-(9-$i);
									$tempnum=substr($_taxdata["amt4"],$jj,1);
								}
								if(strlen($tempnum)==0) {
									echo "<td height='17' align='center'>&nbsp;</td>";
								} else {
									echo "<td height='17' align='center'><strong>".$tempnum."</strong></td>";
								}
							}
							?>
							</tr>
							<tr>
								<td height="17">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<!--합계-->
							<tr>
							<?
							for($i=0;$i<9;$i++) {
								$tempnum="";
								if((9-$i)<=strlen($_taxdata["amt1"])) {
									$jj=strlen($_taxdata["amt1"])-(9-$i);
									$tempnum=substr($_taxdata["amt1"],$jj,1);
								}
								if(strlen($tempnum)==0) {
									echo "<td height='17' align='center' class='total'>&nbsp;</td>";
								} else {
									echo "<td height='17' align='center' class='total'><strong>".$tempnum."</strong></td>";
								}
							}
							?>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<!--★결제금액 끝-->
				</td>
			</tr>
			<tr>
				<td>
				<!--★현금영수증 사업장정보 시작-->
				<table width="305" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td height="17" colspan="2" bgcolor="#F4F4F4"><img src="<?=$Dir?>images/taxsaveviewpop_tt.gif" width="131" height="16"></td>
				</tr>
				<tr>
					<td height="2" colspan="2" bgcolor="#D6D6D6"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#AFAFAF"></td>
				</tr>
				<tr>
					<td height="17" colspan="2" bgcolor="#F4F4F4"><img src="<?=$Dir?>images/taxsaveviewpop_bul02.gif" width="13" height="10" align="absmiddle"><strong>가맹점명</strong></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#D6D6D6"></td>
				</tr>
				<tr>
					<td height="17" colspan="2"><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle"><?=$_taxdata["mcht_name"]?></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#D6D6D6"></td>
				</tr>
				<tr>
					<td height="17" bgcolor="#F4F4F4"><img src="<?=$Dir?>images/taxsaveviewpop_bul02.gif" width="13" height="10" align="absmiddle"><strong>대표자명</strong></td>
					<td bgcolor="#F4F4F4"><img src="<?=$Dir?>images/taxsaveviewpop_bul02.gif" width="13" height="10" align="absmiddle"><strong>전화번호</strong></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#D6D6D6"></td>
				</tr>
				<tr>
					<td height="17"><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle"><?=$_taxdata["sell_name"]?></td>
					<td><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle">
					<?=$_taxdata["sell_tel"]?>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#D6D6D6"></td>
				</tr>
				<tr>
					<td height="17" colspan="2" bgcolor="#F4F4F4"><img src="<?=$Dir?>images/taxsaveviewpop_bul02.gif" width="13" height="10" align="absmiddle"><strong>사업자번호</strong></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#D6D6D6"></td>
				</tr>
				<tr>
					<td height="17" colspan="2"><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle"><?=substr($_taxdata["tax_no"],0,3)."-".substr($_taxdata["tax_no"],3,2)."-".substr($_taxdata["tax_no"],5,5)?></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#D6D6D6"></td>
				</tr>
				<tr>
					<td height="17" colspan="2" bgcolor="#F4F4F4"><img src="<?=$Dir?>images/taxsaveviewpop_bul02.gif" width="13" height="10" align="absmiddle"><strong>주소</strong></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#D6D6D6"></td>
				</tr>
				<tr>
					<td height="17" colspan="2"><img src="<?=$Dir?>images/taxsaveviewpop_pad.gif" width="13" height="10" align="absmiddle"><?=$_taxdata["sell_addr"]?></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#AFAFAF"></td>
				</tr>
				<tr>
					<td height="2" colspan="2" bgcolor="#D6D6D6"></td>
				</tr>
				</table>
				<!--★현금영수증 사업장정보 끝-->
				</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td height="25" class="gray">고객센터 : 1544-2020 | http://현금영수증/kr </td>
			</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td align=center>
	<table border=0>
        <tr>
            <td height="55" align="center" valign="top">
            <img src="<?=$Dir?>images/taxsaveviewpop_print.gif" width="97" height="36" border="0" onclick="JavaScript:cleanPrinting();" style="cursor:pointer;" id="bottomImg1">
            <img src="<?=$Dir?>images/taxsaveviewpop_close.gif" width="66" height="36" border="0" onclick="JavaScript:self.close();" style="cursor:pointer;" id="bottomImg2">
            </td>
        </tr>
	</table>
	</td>
</tr>
</table>
</body>
</html>
