<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if($_data->estimate_ok!="Y" && $_data->estimate_ok!="O") {
	alert_go('견적서 기능 선택이 안되었습니다.','c');
}

//장바구니 인증키 확인
if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}

$colspan=5;

$mode=$_POST["mode"];

if($mode=="basketin") {
	$basketval=$_POST["basketval"];
	if(ord($basketval)) {
		$basketarray=explode("|",$basketval);
		for($i=0;$i<count($basketarray);$i++) {
			$prarray=explode(",",$basketarray[$i]);
			$productcode=$prarray[0];
			$quantity=$prarray[1];
			if(strlen($productcode)==18 && $quantity>0) {
				$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check FROM tblproduct ";
				$sql.= "WHERE productcode='{$productcode}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					if($row->display!="Y") {
						$errmsg="[".str_replace("'","",$row->productname)."]상품은 판매가 되지 않는 상품입니다.\\n";
					}
					if($row->group_check!="N") {
						if(strlen($_ShopInfo->getMemid())>0) {
							$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
							$sqlgc.= "WHERE productcode='{$productcode}' ";
							$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
							$resultgc=pmysql_query($sqlgc,get_db_conn());
							if($rowgc=@pmysql_fetch_object($resultgc)) {
								if($rowgc->groupcheck_count<1) {
									$errmsg="[".str_replace("'","",$row->productname)."]상품은 지정 등급 전용 상품입니다.\\n";
								}
								@pmysql_free_result($resultgc);
							} else {
								$errmsg="[".str_replace("'","",$row->productname)."]상품은 지정 등급 전용 상품입니다.\\n";
							}
						} else {
							$errmsg="[".str_replace("'","",$row->productname)."]상품은 회원 전용 상품입니다.\\n";
						}
					}
					if(ord($errmsg)==0) {
						$miniq=1;
						$maxq="?";
						if(ord($row->etctype)) {
							$etctemp = explode("",$row->etctype);
							for($j=0;$j<count($etctemp);$j++) {
								if(strpos($etctemp[$j],"MINIQ=")===0)     $miniq=substr($etctemp[$j],6);
								if(strpos($etctemp[$j],"MAXQ=")===0)      $maxq=substr($etctemp[$j],5);
							}
						}

						if(strlen(dickerview($row->etctype,0,1))>0) {
							$errmsg="[".str_replace("'","",$row->productname)."]상품은 판매가 되지 않습니다. 다른 상품을 주문해 주세요.\\n";
						}
					}
					if(ord($errmsg)==0) {
						if ($miniq!=1 && $miniq>1 && $quantity<$miniq) 
							$errmsg="[".str_replace("'","",$row->productname)."]상품은 최소 {$miniq}개 이상 주문하셔야 합니다.\\n";
						if ($maxq!="?" && $maxq>0 && $quantity>$maxq)
							$errmsg.="[".str_replace("'","",$row->productname)."]상품은 최대 {$maxq}개 이하로 주문하셔야 합니다.\\n";

						if(empty($option1) && ord($row->option1))  $option1=1;
						if(empty($option2) && ord($row->option2))  $option2=1;
						if(ord($row->quantity)) {
							if ($quantity>$row->quantity) {
								if ($row->quantity>0)
									$errmsg.="[".str_replace("'","",$row->productname)."]상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$row->quantity} 개 입니다.")."\\n";
								else
									$errmsg.= "[".str_replace("'","",$row->productname)."]상품이 다른 고객의 주문으로 품절되었습니다.\\n";
							}
						}
						if(ord($row->option_quantity)) {
							$optioncnt = explode(",",ltrim($row->option_quantity,','));
							if($option2==0) $tmoption2=1;
							else $tmoption2=$option2;
							$optionvalue=$optioncnt[(($tmoption2-1)*10)+($option1-1)];
							if($optionvalue<=0 && $optionvalue!="")
								$errmsg.="[".str_replace("'","",$row->productname)."]상품의 옵션은 다른 고객의 주문으로 품절되었습니다.\\n";
							else if($optionvalue<$quantity && $optionvalue!="")
								$errmsg.="[".str_replace("'","",$row->productname)."]상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\\n";
						}
					}
				} else {
					$errmsg="[".str_replace("'","",$row->productname)."]상품이 존재하지 않습니다.\\n";
				}
				pmysql_free_result($result);
				
				if(ord($errmsg)) {
					alert_go($errmsg,-1);
				}

				if (empty($option1))  $option1=0;
				if (empty($option2))  $option2=0;
				if (empty($opts))  $opts="0";

				$sql = "SELECT * FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode='{$productcode}' ";
				$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
				$result = pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				pmysql_free_result($result);
				
				if($row) {
					$c = $row->quantity + $quantity;
					$sql = "UPDATE tblbasket SET quantity='{$c}', opt1_idx='{$option1}' ";
					$sql.= "WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
					$sql.= "AND productcode='{$productcode}' AND opt1_idx='{$option1}' ";
					$sql.= "AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
					pmysql_query($sql,get_db_conn());
				} else {
					$vdate = date("YmdHis");
					$sql = "SELECT COUNT(*) as cnt FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
					$result = pmysql_query($sql,get_db_conn());
					$row = pmysql_fetch_object($result);
					pmysql_free_result($result);
					if($row->cnt>=200) {
						echo "<script>alert('장바구니에는 총 200개까지만 담을수 있습니다.');</script>";
						break;
					} else {
						$sql = "INSERT INTO tblbasket(
						tempkey		,
						productcode	,
						opt1_idx	,
						opt2_idx	,
						optidxs		,
						quantity	,
						date		) VALUES (
						'".$_ShopInfo->getTempkey()."', 
						'{$productcode}', 
						'{$option1}', 
						'{$option2}', 
						'{$opts}', 
						'{$quantity}', 
						'{$vdate}')";
						pmysql_query($sql,get_db_conn());
					}
				}
			}
		}
	}
	echo "<html></head><body onload=\"opener.location.href='".$Dir.FrontDir."basket.php';window.close();\"></body></html>";
	exit;
}
?>
<html>
<head>
<title>온라인견적서</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
resizeTo(615,550);

var product_all=new Array();
var ok_list=new Array();

function DeleteFrontZero(str){
	val = new String(str)
	do {
		if (val.length==1) 
			break;
		if (val.substr(0,1)=='0')
			val = val.substr(1, val.length - 1);
		else
			break;
	} while (true);
	return val
}

///Int 형으로 변환한다.
function ToInt(val){
	val = DeleteFrontZero(val);
	return parseInt(val);
}

function product_list() {
	var argv = product_list.arguments;   
	var argc = product_list.arguments.length;
	
	//Property 선언
	this.classname		= "product_list"							//classname
	this.debug			= false;									//디버깅여부.
	this.productcode	= new String((argc > 0) ? argv[0] : "");	//상품코드
	this.sellprice		= ToInt((argc > 1) ? argv[1] : 0 );			//상품단가
	this.quantity		= ToInt((argc > 2) ? argv[2] : 0 );			//판매가능수량
	this.tinyimage		= new String((argc > 3) ? argv[3] : "");	//상품이미지
	this.cnt			= ToInt((argc > 4) ? argv[4] : 0 );			//선택수량
}

function settotal(idx) {
	total=0;
	for(i=0;i<ok_list.length;i++) {
		if(typeof(ok_list[i])!="undefined") {
			if(ok_list[i].cnt>0) {
				total = total+(ok_list[i].sellprice*ok_list[i].cnt);
			}
		}
	}
	document.all["idx_total"].innerHTML=number_format(total)+"원";
}

function calculation(idx,pidx) {
	if(pidx.length==0) {
		alert("상품을 선택하세요.");
		ok_list[idx].cnt=0;
		document.form1["prcnt["+idx+"]"].value=1;
		document.all["idx_sellprice"+idx].innerHTML="0원";
		document.all["idx_totprice"+idx].innerHTML="0원";
		document.all["img_"+idx].style.display="none";
		document.all["img_"+idx].src="<?=$Dir?>images/no_img.gif";
	} else {
		if(product_all[pidx].quantity==0) {
			alert("품절된 상품입니다.");
			document.form1["prcode["+idx+"]"].selectedIndex=0;
			document.form1["prcnt["+idx+"]"].value=1;
			document.all["idx_sellprice"+idx].innerHTML="0원";
			document.all["idx_totprice"+idx].innerHTML="0원";
			document.all["img_"+idx].src="<?=$Dir?>images/no_img.gif";
			document.all["img_"+idx].style.display="none";
			return;
		}
		cnt=document.form1["prcnt["+idx+"]"].value;
		price=product_all[pidx].sellprice;
		product_all[pidx].cnt=cnt;
		ok_list[idx]=product_all[pidx];
		document.all["idx_sellprice"+idx].innerHTML=number_format(price)+"원";
		document.all["idx_totprice"+idx].innerHTML=number_format((price*cnt))+"원";
		if(ok_list[idx].tinyimage.length>0) {
			document.all["img_"+idx].src="<?=$Dir.DataDir?>shopimages/product/"+ok_list[idx].tinyimage;
			document.all["img_"+idx].style.display="";
		} else {
			document.all["img_"+idx].src="<?=$Dir?>images/no_img.gif";
			document.all["img_"+idx].style.display="";
		}
	}

	settotal(idx);
}

function number_format(val) {
	var str=new String(val);
	var res="";
	for(i=str.length;i>0;i-=3) {
		if(i<str.length) res=","+res;
		res=str.substring(i-3,i)+res;
	}
	return res;
}

function change_cnt(idx) {
	tmpobj=document.form1["prcnt["+idx+"]"];
	if(!IsNumeric(tmpobj.value)) {
		alert("숫자만 입력하세요.");
		while(true) {
			if(tmpobj.value.length>0) {
				if(!IsNumeric(tmpobj.value)) {
					tmpobj.value=tmpobj.value.substring(0,(tmpobj.value.length-1));
				} else {
					break;
				}
			} else {
				break;
			}
		}
		document.form1["prcnt["+idx+"]"].focus();
	}
	cnt=tmpobj.value;

	if(typeof(ok_list[idx])!="undefined") {
		ok_list[idx].cnt=cnt;
		price=ok_list[idx].sellprice;
		document.all["idx_totprice"+idx].innerHTML=number_format((price*cnt))+"원";
	}

	settotal(idx);
}

function reset() {
	totcnt=document.form1.totcnt.value;
	for(i=0;i<totcnt;i++) {
		document.form1["prcode["+i+"]"].selectedIndex=0;
		document.form1["prcnt["+i+"]"].value=1;
		document.all["idx_sellprice"+i].innerHTML="0원";
		document.all["idx_totprice"+i].innerHTML="0원";
		document.all["img_"+i].style.display="none";
		document.all["img_"+i].src="<?=$Dir?>images/no_img.gif";
	}
	document.all["idx_total"].innerHTML="0원";
	ok_list=new Array();
}

function estimate_print() {
	printval="";
	for(i=0;i<ok_list.length;i++) {
		if(typeof(ok_list[i])!="undefined") {
			if(ok_list[i].cnt>0) {
				printval+="|"+ok_list[i].productcode+","+ok_list[i].cnt;
			}
		}
	}
	if(printval.length==0) {
		alert("선택된 상품이 없습니다.");
		return;
	}
	printval=printval.substring(1,printval.length);
	document.form2.printval.value=printval;
	document.form2.submit();
}

function basket_in() {
	basketval="";
	for(i=0;i<document.form1.totcnt.value;i++) {
		if(document.form1["prcode["+i+"]"].value.length>0 && document.form1["prcnt["+i+"]"].value.length>0 && document.form1["prcnt["+i+"]"].value>0) {
			if(basketval.length>0) basketval+="|";
			basketval+=product_all[document.form1["prcode["+i+"]"].value].productcode+","+document.form1["prcnt["+i+"]"].value;
		}
	}
	if(basketval.length==0) {
		alert("장바구니에 담을 상품이 선택되지 않았거나 수량입력이 잘못되었습니다.");
		return;
	}
	document.form1.mode.value="basketin";
	document.form1.basketval.value=basketval;
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red" topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td><img src="<?=$Dir?>images/common/estimate/estimate_wintitle.gif" border="0"></td>
</tr>
<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=mode>
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table align="center" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="padding-left:5px;padding-right:5px;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td style="font-size:11px;letter-spacing:-0.5pt;">* 아래 상품카테고리에 상품을 선택해주세요.<br>* 견적수량을 조정하시고 견적서 보기를 클릭하시면 됩니다.</td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<col></col>
			<col width="70"></col>
			<col width="50"></col>
			<col width="80"></col>
			<col width="100"></col>
			<tr>
				<td height="2" colspan="<?=$colspan?>" bgcolor="#000000"></td>
			</tr>
			<tr height="30" align="center" bgcolor="#F8F8F8">
				<td><font color="333333" style="letter-spacing:-0.5pt;"><b>상품선택</b></font></td>
				<td><font color="333333" style="letter-spacing:-0.5pt;"><b>상품이미지</b></font></td>	
				<td><font color="333333" style="letter-spacing:-0.5pt;"><b>견적수량</b></font></td>
				<td><font color="333333" style="letter-spacing:-0.5pt;"><b>상품단가</b></font></td>
				<td><font color="333333" style="letter-spacing:-0.5pt;"><b>상품가격</b></font></td>
			</tr>
			<tr>
				<td height="1" colspan="<?=$colspan?>" bgcolor="#DDDDDD"></td>
			</tr>
<?php 
	$script_str="";
	$sql = "SELECT * FROM tblproductcode ";
	if(strlen($_ShopInfo->getMemid())==0) {
		$sql.= "WHERE group_code='' ";
	} else {
		$sql.= "WHERE (group_code='".$_ShopInfo->getMemgroup()."' OR group_code='ALL' OR group_code='') ";
	}
	$sql.= "AND estimate_set!='999' AND type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' ";
	$sql.= "ORDER BY estimate_set ";
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	$j=0;
	while($row=pmysql_fetch_object($result)) {
		$likecode=$row->code_a;
		if($row->code_b!="000") $likecode.=$row->code_b;
		if($row->code_c!="000") $likecode.=$row->code_c;
		if($row->code_d!="000") $likecode.=$row->code_d;

		$sql = "SELECT a.productcode,a.productname,a.sellprice,a.production,a.quantity,a.tinyimage,a.etctype,a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode LIKE '{$likecode}%' AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "AND a.assembleuse!='Y' ";
		$sql.= "AND (a.package_num IS NULL OR a.package_num=0 OR a.package_num='') ";
		$sql.= "ORDER BY a.productcode ";
		$result2=pmysql_query($sql,get_db_conn());
		echo "<tr align=\"center\">\n";
		echo "	<td style=\"padding-bottom:3pt;padding-top:3pt;\">\n";
		echo "	<TABLE style=\"TABLE-LAYOUT: fixed\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\">\n";
		echo "	<tr>\n";
		echo "		<TD><B>".getCodeLoc($row->code_a.$row->code_b.$row->code_c.$row->code_d)."</B></td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>";
		echo "		<select name=\"prcode[{$i}]\" style=\"width:100%\" onchange=\"calculation({$i},this.value)\" style=\"color:#444444;font-size:11px;background-color:#404040;\">\n";
		echo "	<option value=\"\" style=\"color:#ffffff;\">------------- 상품을 선택하세요. -------------</option>\n";
		while($row2=pmysql_fetch_object($result2)) {
			if (strlen(dickerview($row2->etctype,$row2->sellprice,1))==0) {
				echo "<option value=\"{$j}\" style=\"color:#ffffff;\">".strip_tags(str_replace("<br>", " ", viewselfcode($row2->productname,$row2->selfcode)));
				if(ord($row2->quantity) && $row2->quantity<=0) echo " (품절)";
				echo "</option>\n";
				$quantity="";
				if(ord($row2->quantity) && $row2->quantity<=0) {
					$quantity=0;
				} else if(ord($row2->quantity) && $row2->quantity>0) {
					$quantity=$row2->quantity;
				} else {
					$quantity=999;
				}
				$script_str.="var plist=new product_list(); plist.productcode='{$row2->productcode}'; plist.sellprice='{$row2->sellprice}'; plist.quantity='{$quantity}'; plist.tinyimage='".(file_exists($Dir.DataDir."shopimages/product/".$row2->tinyimage) && ord($row2->tinyimage)?$row2->tinyimage:"")."'; plist.cnt=0; product_all[{$j}]=plist; plist=null;\n";
				$j++;
			}
		}
		pmysql_free_result($result2);
		echo "		</select>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "	<td><img id=\"img_{$i}\" border=\"0\" width=\"60\" style=\"display:none\"></td>\n";
		echo "	<td><input type=text name=\"prcnt[{$i}]\" value=\"1\" size=\"3\" maxlength=\"3\" style=\"text-align:right;font-size:11px;BORDER-RIGHT:#DFDFDF 1px solid;BORDER-TOP:#DFDFDF 1px solid;BORDER-LEFT:#DFDFDF 1px solid; BORDER-BOTTOM:#DFDFDF 1px solid;BACKGROUND-COLOR:#F7F7F7;\" onkeyup=\"change_cnt({$i})\"> 개</td>\n";
		echo "	<td align=\"right\" id=\"idx_sellprice{$i}\" style=\"padding-right:5\">0원</td>\n";
		echo "	<td align=\"right\" id=\"idx_totprice{$i}\" style=\"padding-right:5\">0원</td>\n";
		echo "</tr>\n";
		echo "<tr><td height=\"1\" colspan=\"{$colspan}\" bgcolor=\"#DDDDDD\"></td></tr>\n";
		$i++;
	}
	pmysql_free_result($result);
?>
			<tr>
				<td height="35" align="right" bgcolor="#FAFAFA" colspan="5" id="idx_total" style="padding-right:2px;"><font color="#FF3300" style="font-size:15px;"><b>0원</b></font></td>
			</tr>
			<tr>
				<td height="1" colspan="5" bgcolor="#DDDDDD"></td>
			</tr>												
			</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td align="center"><A href="javascript:reset()"><IMG src="<?=$Dir?>images/common/estimate/icon_reset.gif" border="0"></A><A HREF="javascript:estimate_print()"><IMG src="<?=$Dir?>images/common/estimate/icon_estimate_view.gif" border="0" hspace="6"></A><A HREF="javascript:basket_in();"><IMG src="<?=$Dir?>images/common/estimate/icon_basket.gif" border="0"></A></TD>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<input type=hidden name=totcnt value="<?=$i?>">
<input type=hidden name=basketval>
</form>
<form name=form2 method=post action="<?=$Dir.FrontDir?>estimate_print.php">
<input type=hidden name=printval>
</form>
<?php  echo "<script>{$script_str}</script>";?>
</table>
</body>
</html>
