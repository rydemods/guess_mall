<SCRIPT LANGUAGE="JavaScript">
<!--
function SendSMS() {
	window.open("sendsms.php","sendsmspop","width=220,height=350,scrollbars=no");
}
function MemberMemo() {
	window.open("member_memoconfirm.php","memopop","width=250,height=120,scrollbars=no");
}
//-->
</SCRIPT>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<div style="display:block;height:30px;"></div>
<?
$_ShopData=new ShopData($_ShopInfo);
$_data=$_ShopData->shopdata;


$bottom_body = "
<!-- [footer] -->

<div class=\"admin_footer_wrap\">
	<div class=\"info\" style=\"background:#ffffff\">
		<ul>
			<li>사업자번호 : [BIZNUM]<span>l</span>통신판매업 : [SALENUM]<span>l</span>[ADDRESS]&nbsp;&nbsp;[COMPANYNAME]</li>
			<li>대표이사 : [OWNER]<span>l</span>개인정보보호정책 및 담당 : [PRIVERCY]<span>l</span>전화 : [TEL]<span>l</span>팩스 : [FAX]<span>l</span>메일 : [INFOMAIL]</li>
			<li class=\"copy\">Copyright(C)<span>[COMPANYNAME]</span> ALL Rights Reserved</li>
		</ul>
	</div>
</div>
<!-- //[footer] -->";


$arcompa=array("-"," ",".","_",",");
$arcomre=array("", "", "", "", "");
$companynum=str_replace($arcompa,$arcomre,$_data->companynum);

if(strlen($companynum)==13) {
	$companynum=substr($companynum,0,6)."-*******";
} else {
	$companynum=substr($companynum,0,3)."-".substr($companynum,3,2)."-".substr($companynum,5);
}
$bottom_body=str_replace("[DIR]",$Dir,$bottom_body);

$pattern=array("[TEL]","[FAX]","[INFOMAIL]","[COMPANYNAME]","[BIZNUM]","[SALENUM]","[OWNER]","[PRIVERCY]","[ADDRESS]");

$replacelogin=array($_data->info_tel,$_data->info_fax,$_data->privercyemail,$_data->companyname,$companynum,$_data->reportnum,$_data->companyowner,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyname."</a>",$_data->info_addr);

$bottom_body = str_replace($pattern,$replacelogin,$bottom_body);
?>

<?=$bottom_body?>


</body>
</html> 