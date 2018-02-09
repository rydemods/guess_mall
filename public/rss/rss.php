<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_rss.php");
include_once($Dir."lib/shopdata.php");

$code=$_GET["code"];
$sprice=(int)$_GET["sprice"];
$search=$_GET["search"];

$sql = "SELECT productcode, productname, sellprice, quantity, reserve, reservetype, consumerprice, production, madein, ";
$sql.= "addcode, CASE WHEN quantity<=0 THEN '11111111111111' ELSE date END as date, minimage, tinyimage, etctype, option_price ";
$sql.= "FROM tblproduct WHERE 1=1 ";
if(strlen($code)>0) {
	$sql.= "AND productcode LIKE '".$code."%' ";
}
if($sprice>0) {
	switch($sprice) {
		case 20000:
			$sql.= "AND sellprice<=20000 ";
			break;
		case 50000:
			$sql.= "AND sellprice>=20000 AND sellprice<=50000 ";
			break;
		case 100000:
			$sql.= "AND sellprice>=50000 AND sellprice<=100000 ";
			break;
		case 300000:
			$sql.= "AND sellprice>=100000 AND sellprice<=300000 ";
			break;
		case 300001:
			$sql.= "AND sellprice>=300000 ";
			break;
		default:
			break;
	}
}
if(strlen($search)>0) {
	$skeys = explode(" ",$search);
	for($j=0;$j<count($skeys);$j++) {
		$skeys[$j]=trim($skeys[$j]);
		if(strlen($skeys[$j])>0) {
			$sql.= "AND (productname LIKE '%".$skeys[$j]."%' OR keyword LIKE '%".$skeys[$j]."%') ";
		}
	}
}
$sql.= "AND display='Y' ";
$sql.= "AND group_check='N' ";
$sql.= "ORDER BY date DESC LIMIT 20 ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$rss.="		<item>\n";
	$rss.="			<title>\n";
	$rss.="				<![CDATA[".$row->productname."]]>\n";
	$rss.="			</title>\n";
	$rss.="			<link>http://".$_data->shopurl."?productcode=".$row->productcode."</link>\n";
	$rss.="			<guid>http://".$_data->shopurl."?productcode=".$row->productcode."</guid>\n";
	$rss.="			<description>\n";
	$rss.="				<![CDATA[\n";

	if(strlen($row->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->minimage)) {
		$width=GetImageSize($Dir.DataDir."shopimages/product/".$row->minimage);
		if($width[0]>=300) $width[0]=300;
		else if (strlen($width[0])==0) $width[0]=300;
		$primg="<img src=\"http://".$_ShopInfo->getShopurl().DataDir."shopimages/product/".$row->minimage."\" border=0 width=".$width[0].">";
	} else {
		$primg="<img src=\"http://".$_ShopInfo->getShopurl()."images/no_img.gif\" border=0>";
	}

	$prsellprice ="<tr height=24><td><IMG SRC=\"http://".$_ShopInfo->getShopurl()."images/common/product/AD002/pdetail_skin_point.gif\" border=\"0\"></td>";
	$prsellprice.="<td>판매가격</td>";
	$prsellprice.="<td></td>";
	$prsellprice.="<td><IMG SRC=\"http://".$_ShopInfo->getShopurl()."images/common/won_icon.gif\" border=\"0\" align=absmiddle><b>".number_format($row->sellprice)."원</b></td></tr>";

	$prproduction="";
	if(strlen($row->production)>0) {
		$prproduction ="<tr height=24><td><IMG SRC=\"http://".$_ShopInfo->getShopurl()."images/common/product/AD002/pdetail_skin_point.gif\" border=\"0\"></td>";
		$prproduction.="<td>제조회사</td>";
		$prproduction.="<td></td>";
		$prproduction.="<td>".$row->production."</td></tr>";
	}
	$prmadein="";
	if(strlen($row->madein)>0) {
		$prmadein ="<tr height=24><td><IMG SRC=\"http://".$_ShopInfo->getShopurl()."images/common/product/AD002/pdetail_skin_point.gif\" border=\"0\"></td>";
		$prmadein.="<td>원산지</td>";
		$prmadein.="<td></td>";
		$prmadein.="<td>".$row->madein."</td></tr>";
	}
	$prconsumerprice="";
	if($row->consumerprice>0) {
		$prconsumerprice ="<tr height=24><td><IMG SRC=\"http://".$_ShopInfo->getShopurl()."images/common/product/AD002/pdetail_skin_point.gif\" border=\"0\"></td>";
		$prconsumerprice.="<td>시중가격</td>";
		$prconsumerprice.="<td></td>";
		$prconsumerprice.="<td><IMG SRC=\"http://".$_ShopInfo->getShopurl()."images/common/won_icon2.gif\" border=\"0\" align=absmiddle><strike>".number_format($row->consumerprice)."</strike>원</td></tr>";
	}
	$prreserve="";
	$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
	if($reserveconv>0) {
		$prreserve ="<tr height=24><td><IMG SRC=\"http://".$_ShopInfo->getShopurl()."images/common/product/AD002/pdetail_skin_point.gif\" border=\"0\"></td>";
		$prreserve.="<td>적립금</td>";
		$prreserve.="<td></td>";
		$prreserve.="<td><IMG SRC=\"http://".$_ShopInfo->getShopurl()."images/common/reserve_icon1.gif\" border=\"0\" align=absmiddle>".number_format($reserveconv)."원</td></tr>";
	}
	$praddcode="";
	if(strlen($row->addcode)>0) {
		$praddcode ="<tr height=24><td><IMG SRC=\"http://".$_ShopInfo->getShopurl()."images/common/product/AD002/pdetail_skin_point.gif\" border=\"0\"></td>";
		$praddcode.="<td>특이사항</td>";
		$praddcode.="<td></td>";
		$praddcode.="<td>".$row->addcode."</td></tr>";
	}

	$rss.="<html>";
	$rss.="<head>";
	$rss.="<title></title>";
	$rss.="<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>";
	$rss.="<link rel='stylesheet' href='http://".$_ShopInfo->getShopurl().RssDir."style.css' type='text/css'>";
	$rss.="</head>";
	$rss.="<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>";
	$rss.="<table border='0' cellpadding='0' cellspacing='0' width='100%'>";
	$rss.="<tr>";
	$rss.="	<td style='padding-left:5px;padding-right:5px;'>";
	$rss.="	<table border='0' cellpadding='0' cellspacing='0' width='100%'>";
	$rss.="	<tr>";
	$rss.="		<td style='padding-left:5px;padding-right:5px;'>";
	$rss.="		<table cellpadding='0' cellspacing='0' width='100%'>";
	$rss.="		<tr>";
	$rss.="			<td>";
	$rss.="			<table cellpadding='0' cellspacing='0' width='100%'>";
	$rss.="			<col width='46%'></col>";
	$rss.="			<col width='1%'></col>";
	$rss.="			<col width=></col>";
	$rss.="			<tr>";
	$rss.="				<td valign='top'>";
	$rss.="				".$primg."";
	$rss.="				</td>";
	$rss.="				<td></td>";
	$rss.="				<td valign='top'>";
	$rss.="				<table cellpadding='0' cellspacing='0' width='100%'>";
	$rss.="				<tr>";
	$rss.="					<td><font color='#3974CF' style='font-size:15px;letter-spacing:-0.5pt;'><b>".$row->productname."</b></font></td>";
	$rss.="				</tr>";
	$rss.="				<tr>";
	$rss.="					<td height='5'></td>";
	$rss.="				</tr>";
	$rss.="				<tr>";
	$rss.="					<td bgcolor='#3974CF' HEIGHT='3'></td>";
	$rss.="				</tr>";
	$rss.="				<tr>";
	$rss.="					<td height='5'></td>";
	$rss.="				</tr>";
	$rss.="				<tr>";
	$rss.="					<td width='100%'>";
	$rss.="					<table cellpadding='0' cellspacing='0' width='100%' border='0' bgcolor='#F5FDFF'>";
	$rss.="					<col width='14' align='center'></col>";
	$rss.="					<col width='64'></col>";
	$rss.="					<col width='13'></col>";
	$rss.="					<col width=></col>";
	$rss.="					".$prsellprice."";
	$rss.="					".$prconsumerprice."";
	$rss.="					".$prreserve."";
	$rss.="					".$praddcode."";
	$rss.="					".$prproduction."";
	$rss.="					".$prmadein."";
	$rss.="					</table>";
	$rss.="					</td>";
	$rss.="				</tr>";
	$rss.="				<tr>";
	$rss.="					<td height='3'></td>";
	$rss.="				</tr>";
	$rss.="				<tr>";
	$rss.="					<td HEIGHT='1' bgcolor='#3974CF'></td>";
	$rss.="				</tr>";
	$rss.="				<tr>";
	$rss.="					<td height='10'></td>";
	$rss.="				</tr>";
	$rss.="				<tr>";
	$rss.="					<td align='center'>";
	$rss.="					<table border=0 cellpadding=0>";
	$rss.="					<tr align='center'>";
	$rss.="						<td colspan='3'><a target='_blank' href='http://".$_ShopInfo->getShopurl()."?productcode=".$row->productcode."'><img src='http://".$_ShopInfo->getShopurl().RssDir."images/btn_go.gif' border='0'></a></td>";
	$rss.="					</tr>";
	$rss.="					</table>";
	$rss.="					</td>";
	$rss.="				</tr>";
	$rss.="				</table>";
	$rss.="				</td>";
	$rss.="			</tr>";
	$rss.="			</table>";
	$rss.="			</td>";
	$rss.="		</tr>";
	$rss.="		</table>";
	$rss.="		</td>";
	$rss.="	</tr>";
	$rss.="	</table>";
	$rss.="	</td>";
	$rss.="</tr>";
	$rss.="</table>";
	$rss.="</body>";
	$rss.="</html>";
	$rss.="				]]>\n";
	$rss.="			</description>\n";
	$rss.="			<author>".number_format($row->sellprice)."원</author>\n";
	//$rss.="			<pubDate></pubDate>\n";
	//$rss.="			<category></category>\n";
	$rss.="		</item>\n";
}
pmysql_free_result($result);

echo "<?xml version=\"1.0\" encoding=\"EUC-KR\"?>\n";
echo "<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
echo "<channel>\n";
echo "	<title>\n";
echo "		<![CDATA[".$_data->shoptitle."]]>\n";
echo "	</title>\n";
echo "	<link>http://".$_ShopInfo->getShopurl()."</link>\n";
echo "	<description>\n";
echo "		<![CDATA[".(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)."]]>\n";
echo "	</description>\n";
echo "	<language>ko</language>\n";
echo "	<copyright>Copyright ⓒ ".$_data->shopname." All Rights Reserved.</copyright>\n";
//echo "	<pubDate>".strftime ("%a, %d %b %Y %T %z",time())."</pubDate>\n";
echo $rss;
echo "</channel>\n";
echo "</rss>\n";
?>

<? if($HTML_CACHE_EVENT=="OK") ob_end_flush(); ?>
