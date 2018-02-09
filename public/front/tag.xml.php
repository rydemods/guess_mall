<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

header("Cache-Control: no-cache, must-revalidate"); 
header("Content-Type: text/xml; charset=EUC-KR");

$mode=$_GET["mode"];
$tagname=$_GET["tagname"];

$sort=$_GET["sort"];
$block=$_GET["block"];
$gotopage=$_GET["gotopage"];
$productcode=$_GET["productcode"];

$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

############# 상품상세페이지에서의 태그등록 ############
if($mode=="prtagreg") {
	if($_data->ETCTYPE["TAGTYPE"]=="N") {
		echo "NO|현재 페이지는 미사용 중 입니다.";
		exit;
	}

	if(strlen($_ShopInfo->getMemid())==0) {
		echo "NO|로그인 후 TAG등록이 가능합니다.";
		exit;
	}

	if(strlen($productcode)!=18 || strlen($tagname)<2) {
		echo "NO|TAG등록 정보가 잘못되었습니다.";
		exit;
	}

	$id="<".$_ShopInfo->getMemid().">,";
	$tag="<{$tagname}>,";
	$sql = "SELECT tagname,ids FROM tbltagproduct WHERE productcode='{$productcode}' AND tagname='{$tagname}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if(preg_match("/{$id}/",$row->ids)) {
			echo "NO|고객님이 이미 등록하신 태그 입니다.";
			exit;
		} else {
			$sql = "UPDATE tbltagproduct SET cnt=cnt+1, ids='{$id}'||ids ";
			$sql.= "WHERE productcode='{$productcode}' AND tagname='{$tagname}' ";
			pmysql_query($sql,get_db_conn());
			echo "OK|0|".$tagname;
			exit;
		}
	} else {
		$sql = "INSERT INTO tbltagproduct (productcode,tagname,cnt,ids) VALUES ('{$productcode}','{$tagname}','1','{$id}') ";
		pmysql_query($sql,get_db_conn());

		$sql = "UPDATE tblproduct SET tag='{$tag}'||tag, tagcount=tagcount+1 ";
		$sql.= "WHERE productcode='{$productcode}' ";
		pmysql_query($sql,get_db_conn());

		echo "OK|1|".$tagname;
		exit;
	}
	pmysql_free_result($result);

#상품상세페이지에서의 태그등록 완료시 태그데이타 요청
} else if($mode=="prtagget") {
	if($_data->ETCTYPE["TAGTYPE"]=="N") {
		echo "NO|현재 페이지는 미사용 중 입니다.";
		exit;
	}

	if(strlen($productcode)!=18) {
		echo "NO|TAG등록 정보가 잘못되었습니다.";
		exit;
	}
	$sql = "SELECT tag FROM tblproduct WHERE productcode='{$productcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		echo "OK|";
		$taglist=explode(",",$row->tag);
		$jj=0;
		for($i=0;$i<count($taglist);$i++) {
			$taglist[$i]=preg_replace("/<|>/","",$taglist[$i]);
			if(ord($taglist[$i])) {
				if($jj>0) echo ",&nbsp;&nbsp;";
				echo "<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$i])."\" onmouseover=\"window.status='{$taglist[$i]}';return true;\" onmouseout=\"window.status='';return true;\">{$taglist[$i]}</a>";
				$jj++;
			}
		}
	} else {
		echo "NO|태그정보 로딩중 오류가 발생하였습니다.";
	}
	pmysql_free_result($result);

###################### 태그검색 #######################
} else if(($mode=="taglink" || $mode=="search") && ord($tagname)) {
	
	$sql = "SELECT code_a, code_b, code_c, code_d FROM tblproductcode ";
	if(strlen($_ShopInfo->getMemid())==0) {
		$sql.= "WHERE group_code!='' ";
	} else {
		$sql.= "WHERE group_code!='".$_ShopInfo->getMemgroup()."' AND group_code!='ALL' AND group_code!='' ";
	}
	$result=pmysql_query($sql,get_db_conn());
	$not_qry="";
	while($row=pmysql_fetch_object($result)) {
		$tmpcode=$row->code_a;
		if($row->code_b!="000") $tmpcode.=$row->code_b;
		if($row->code_c!="000") $tmpcode.=$row->code_c;
		if($row->code_d!="000") $tmpcode.=$row->code_d;
		$not_qry.= "AND a.productcode NOT LIKE '{$tmpcode}%' ";
	}
	pmysql_free_result($result);

	$qry = "WHERE a.display!='N' ";
	$qry.= "AND a.tag LIKE '%<{$tagname}>%' ";
	if(ord($not_qry)) $qry.= $not_qry;

	$sql = "SELECT COUNT(*) as t_count ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry;
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$paging = new Paging($sql,10,20,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$leftmenu="Y";
	if($_data->design_tagsearch=="U") {
		$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='tagsearch'";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$body=$row->body;
			$body=str_replace("[DIR]",$Dir,$body);
			$leftmenu=$row->leftmenu;
			$newdesign="Y";
		}
		pmysql_free_result($result);
	}

	echo "<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	if ($leftmenu!="N") {
		echo "<tr>\n";
		if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/tagsearch_title.gif")) {
			echo "<td><img src=\"".$Dir.DataDir."design/tagsearch_title.gif\" border=0 alt=\"쇼핑태그검색\"></td>";
		} else {
			echo "<td>\n";
			echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
			echo "<TR>\n";
			echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/tagsearch_title_head.gif ALT=></TD>\n";
			echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/tagsearch_title_bg.gif></TD>\n";
			echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/tagsearch_title_tail.gif ALT=></TD>\n";
			echo "</TR>\n";
			echo "</TABLE>\n";
			echo "</td>\n";
		}
		echo "</tr>\n";
	}
	echo "<tr>\n";
	echo "	<td align=center>\n";
	include ($Dir.TempletDir."tagsearch/tagsearch{$_data->design_tagsearch}.php");
	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	if(/*$mode=="search" &&*/ $t_count>0) {	//태그검색일 경우에만 DB업데이트
	    pmysql_query("SELECT f_inc_tbltagsearchall('{$tagname}')",get_db_conn());
	    pmysql_query("SELECT f_inc_tbltagsearch('".date("Ymd")."','{$tagname}')",get_db_conn());
	}

######################## 장바구니 담기 ##########################
} else if($mode=="basketin") {
	if(strlen($productcode)!=18) {
		echo "NO|상품정보가 잘못되었습니다.";
		exit;
	}
	//장바구니 인증키 확인
	if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
		$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
	}

	$quantity=1;
	$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check FROM tblproduct ";
	$sql.= "WHERE productcode='{$productcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if($row->display!="Y") {
			$errmsg="해당상품은 판매가 되지 않는 상품입니다.\n";
		}
		if($row->group_check!="N") {
			if(strlen($_ShopInfo->getMemid())>0) {
				$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
				$sqlgc.= "WHERE productcode='{$productcode}' ";
				$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
				$resultgc=pmysql_query($sqlgc,get_db_conn());
				if($rowgc=@pmysql_fetch_object($resultgc)) {
					if($rowgc->groupcheck_count<1) {
						$errmsg="해당 상품은 지정 등급 전용 상품입니다.\n";
					}
					@pmysql_free_result($resultgc);
				} else {
					$errmsg="해당 상품은 지정 등급 전용 상품입니다.\n";
				}
			} else {
				$errmsg="해당 상품은 회원 전용 상품입니다.\n";
			}
		}
		if(ord($errmsg)==0) {
			$miniq=1;
			$maxq="?";
			if(ord($row->etctype)) {
				$etctemp = explode("",$row->etctype);
				for($i=0;$i<count($etctemp);$i++) {
					if(strpos($etctemp[$i],"MINIQ=")===0)     $miniq=substr($etctemp[$i],6);
					if(strpos($etctemp[$i],"MAXQ=")===0)      $maxq=substr($etctemp[$i],5);
				}
			}

			if(strlen(dickerview($row->etctype,0,1))>0) {
				$errmsg="해당상품은 판매가 되지 않습니다. 다른 상품을 주문해 주세요.\n";
			}
		}
		if(ord($errmsg)==0) {
			if ($miniq!=1 && $miniq>1 && $quantity<$miniq) $quantity=$miniq;

			if(empty($option1) && ord($row->option1))  $option1=1;
			if(empty($option2) && ord($row->option2))  $option2=1;
			if(ord($row->quantity)) {
				if ($quantity>$row->quantity) {
					if ($row->quantity>0)
						$errmsg.="해당상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$row->quantity} 개 입니다.")."\n";
					else
						$errmsg.= "[".str_replace("'","",$row->productname)."]상품이 다른 고객의 주문으로 품절되었습니다.\n";
				}
			}
			if(ord($row->option_quantity)) {
				$optioncnt = explode(",",ltrim($row->option_quantity,','));
				if($option2==0) $tmoption2=1;
				else $tmoption2=$option2;
				$optionvalue=$optioncnt[(($tmoption2-1)*10)+($option1-1)];
				if($optionvalue<=0 && $optionvalue!="")
					$errmsg.="해당상품의 옵션은 다른 고객의 주문으로 품절되었습니다.\n";
				else if($optionvalue<$quantity && $optionvalue!="")
					$errmsg.="해당상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\n";
			}
		}
	} else {
		$errmsg="해당상품이 존재하지 않습니다.\n";
	}
	pmysql_free_result($result);

	if(ord($errmsg)) {
		echo "NO|".$errmsg; exit;
	}

	// 이미 장바구니에 담긴 상품인지 검사하여 있으면 카운트만 증가.
	if (empty($option1))  $option1=0;
	if (empty($option2))  $option2=0;
	if (empty($opts))  $opts="0";

	$sql = "SELECT * FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode='{$productcode}' ";
	$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);

	if($row) {

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
	echo "OK|장바구니에 등록되었습니다."; exit;
######################### 위시리스트 담기 ########################
} else if($mode=="wishin") {
	$opts="0";
	$option1=0;
	$option2=0;

	if(strlen($_ShopInfo->getMemid())>0) {
		list($code_a,$code_b,$code_c,$code_d) = sscanf($productcode,'%3s%3s%3s%3s');

		$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c='{$code_c}' AND code_d='{$code_d}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->group_code=="NO") {	//숨김 분류
				echo "NO|판매가 종료된 상품입니다"; exit;
			} else if(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
				echo "NO|해당 분류의 접근 권한이 없습니다."; exit;
			}
		} else {
			echo "NO|해당 분류가 존재하지 않습니다."; exit;
		}
		pmysql_free_result($result);

		$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check FROM tblproduct ";
		$sql.= "WHERE productcode='{$productcode}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->display!="Y") {
				$errmsg="해당 상품은 판매가 되지 않는 상품입니다.\n";
			}
			if($row->group_check!="N") {
				if(strlen($_ShopInfo->getMemid())>0) {
					$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
					$sqlgc.= "WHERE productcode='{$productcode}' ";
					$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
					$resultgc=pmysql_query($sqlgc,get_db_conn());
					if($rowgc=@pmysql_fetch_object($resultgc)) {
						if($rowgc->groupcheck_count<1) {
							$errmsg="해당 상품은 지정 등급 전용 상품입니다.\n";
						}
						@pmysql_free_result($resultgc);
					} else {
						$errmsg="해당 상품은 지정 등급 전용 상품입니다.\n";
					}
				} else {
					$errmsg="해당 상품은 회원 전용 상품입니다.\n";
				}
			}
			if(ord($errmsg)==0) {
				if(strlen(dickerview($row->etctype,0,1))>0) {
					$errmsg="해당 상품은 판매가 되지 않습니다.\n";
				}
			}
			if(empty($option1) && ord($row->option1))  $option1=1;
			if(empty($option2) && ord($row->option2))  $option2=1;
		} else {
			$errmsg="해당 상품이 존재하지 않습니다.\n";
		}
		pmysql_free_result($result);
		
		if(ord($errmsg)) {
			echo "NO|".$errmsg; exit;
		}

		$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
		$result2=pmysql_query($sql,get_db_conn());
		$row2=pmysql_fetch_object($result2);
		$totcnt=$row2->totcnt;
		pmysql_free_result($result2);
		$maxcnt=100;
		if($totcnt>=$maxcnt) {
			$sql = "SELECT b.productcode ";
			$sql.= "FROM tblwishlist a, tblproduct b ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
			$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
			$sql.= "AND b.display='Y' ";
			$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
			$sql.= "GROUP BY b.productcode ";
			$result2=pmysql_query($sql,get_db_conn());
			$i=0;
			$wishprcode="";
			while($row2=pmysql_fetch_object($result2)) {
				$wishprcode.="'{$row2->productcode}',";
				$i++;
			}
			pmysql_free_result($result2);
			$totcnt=$i;
			$wishprcode=rtrim($wishprcode,',');
			if(ord($wishprcode)) {
				$sql = "DELETE FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode NOT IN ({$wishprcode}) ";
				pmysql_query($sql,get_db_conn());
			}
		}
		if($totcnt<$maxcnt) {
			$sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode='{$productcode}' ";
			$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
			$result2=pmysql_query($sql,get_db_conn());
			$row2=pmysql_fetch_object($result2);
			$cnt=$row2->cnt;
			pmysql_free_result($result2);
			if($cnt<=0) {
				$sql = "INSERT INTO tblwishlist(
				id			,
				productcode	,
				opt1_idx	,
				opt2_idx	,
				optidxs		,
				date		) VALUES (
				'".$_ShopInfo->getMemid()."', 
				'{$productcode}', 
				'{$option1}', 
				'{$option2}', 
				'{$opts}', 
				'".date("YmdHis")."')";
				pmysql_query($sql,get_db_conn());
			} else {
				$sql = "UPDATE tblwishlist SET date='".date("YmdHis")."' ";
				$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
				$sql.= "AND productcode='{$productcode}' ";
				$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
				pmysql_query($sql,get_db_conn());
			}
			echo "OK|등록되었습니다."; exit;
		} else {
			echo "NO|WishList에는 {$maxcnt}개 까지만 등록이 가능합니다.\n\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다."; exit;
		}
	} else {
		echo "NO|로그인 후 이용이 가능합니다.";
		exit;
	}

######################### 인기태그 (정렬 포함) ####################
} else {
	if($sort!="name" && $sort!="best") $sort="name";
	$pretime=strtotime('first day of -1 month');
	$predate=date("Ym",$pretime);

	$temptime=strtotime('00:00:00 -1 day');

	$sql = "SELECT tagname, SUM(cnt) as cnt FROM tbltagsearch WHERE (date LIKE '{$predate}%' OR date LIKE '".date("Ym",$temptime)."%') ";
	$sql.= "GROUP BY tagname ORDER BY cnt DESC LIMIT 100 ";

	$var=array();
	$success=false;
	$filename="tbltagsearch".date("Ymd").".cache";
	if(file_exists($Dir.DataDir."cache/sql/".$filename)) {
		$szdata = file_get_contents($Dir.DataDir."cache/sql/".$filename);
		$var=unserialize($szdata);
		$success=true;
	}
	if(!$success) {
		proc_matchfiledel($Dir.DataDir."cache/sql/tbltagsearch*");

		$result=pmysql_query($sql,get_db_conn());
		if($err=pmysql_error())
			trigger_error($err, E_USER_ERROR);
		while($row=pmysql_fetch_object($result)){
			$var[]=$row;
		}
		pmysql_free_result($result);
		$ret = WriteCache($var, $filename);
	}
	$count=count($var);

	$rank1=ceil(0.1*$count);
	$rank2=ceil(0.2*$count);
	$rank3=ceil(0.3*$count);
	$rank4=ceil(0.4*$count);

	$tagkey=array();
	$taglist=array();
	$i=0;
	while(@list($key,$row)=@each($var)) {
		$i++;

		if($i<=$rank1) {
			$rank=1;
		} else if($i<=($rank1+$rank2)) {
			$rank=2;
		} else if($i<=($rank1+$rank2+$rank3)) {
			$rank=3;
		} else {
			$rank=4;
		}
/*
		if($i>=1 && $i<=$rank1)					$rank=1;
		else if($i>=($rank1+1) && $i<=$rank2)	$rank=2;
		else if($i>=($rank2+1) && $i<=$rank3)	$rank=3;
		else if($i>=($rank3+1))	$rank=4;
*/

		$tagkey[$row->tagname]["tagname"]=$row->tagname;
		$tagkey[$row->tagname]["cnt"]=$row->cnt;
		$tagkey[$row->tagname]["rank"]=$rank;

		$taglist[$row->tagname]=$rank."-".$i;
	}

	if($sort=="name") {
		@ksort ($taglist);
	} else if($sort=="best") {
		@asort ($taglist);
	}
	@reset ($taglist);

	//집계기간
	$start_date=(date("Y",$pretime)==date("Y")?date("m월d일",$pretime):date("Y년m월d일",$pretime));
	$end_date=(date("Y",$temptime)==date("Y")?date("m월d일",$temptime):date("Y년m월d일",$temptime));

	$leftmenu="Y";
	if($_data->design_tag=="U") {
		$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='tag'";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$body=$row->body;
			$body=str_replace("[DIR]",$Dir,$body);
			$leftmenu=$row->leftmenu;
			$newdesign="Y";
		}
		pmysql_free_result($result);
	}

	echo "<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	if ($leftmenu!="N") {
		echo "<tr>\n";
		if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/tag_title.gif")) {
			echo "<td><img src=\"".$Dir.DataDir."design/tag_title.gif\" border=0 alt=\"최근인기태그\"></td>";
		} else {
			echo "<td>\n";
			echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
			echo "<TR>\n";
			echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/tag_title_head.gif ALT=></TD>\n";
			echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/tag_title_bg.gif></TD>\n";
			echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/tag_title_tail.gif ALT=></TD>\n";
			echo "</TR>\n";
			echo "</TABLE>\n";
			echo "</td>\n";
		}
		echo "</tr>\n";
	}
	echo "<tr>\n";
	echo "	<td align=center>\n";
	include ($Dir.TempletDir."tag/tag{$_data->design_tag}.php");
	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
