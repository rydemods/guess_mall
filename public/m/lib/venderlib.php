<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

class _VenderInfo {
	var $vidx			= "";
	var $id				= "";
	var $authkey		= "";

	var $venderdata		= "";

	function _VenderInfo($_vinfo) {
		if ($_vinfo) {
			$savedata=unserialize(decrypt_md5($_vinfo));
			$this->vidx				= $savedata["vidx"];
			$this->id				= $savedata["id"];
			$this->authkey			= $savedata["authkey"];
		}
	}

	function Save() {
		$savedata["vidx"]			= $this->getVidx();
		$savedata["id"]				= $this->getId();
		$savedata["authkey"]		= $this->getAuthkey();

		$_vinfo = encrypt_md5(serialize($savedata));
		setcookie("_vinfo", $_vinfo, 0, "/".RootPath.VenderDir);
	}

	function setVidx($vidx)				{$this->vidx = $vidx;}
	function setId($id)					{$this->id = $id;}
	function setAuthkey($authkey)		{$this->authkey = $authkey;}

	function getVidx()					{return $this->vidx;}
	function getId()					{return $this->id;}
	function getAuthkey()				{return $this->authkey;}

	function getVenderdata()			{return $this->venderdata;}

	function VenderAccessCheck() {
		$sql = "SELECT a.*, b.*, c.date as sessiondate ";
		$sql.= "FROM tblvenderinfo a, tblvenderstore b, tblvendersession c ";
		$sql.= "WHERE a.vender='".$this->getVidx()."' AND a.id='".$this->getId()."' AND a.vender=b.vender ";
		$sql.= "AND a.id=b.id AND a.vender=c.vender AND c.authkey='".$this->getAuthkey()."' AND a.delflag='N' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$sessiondate=$row->sessiondate;
			$sessiontime=mktime((int)substr($sessiondate,8,2),(int)substr($sessiondate,10,2),0,(int)substr($sessiondate,4,2),(int)substr($sessiondate,6,2)+1,(int)substr($sessiondate,0,4));

			if($sessiontime<time()) {
				echo "<script>\n";
				echo "	alert(\"세션 시간이 만료되었습니다.\\n\\n다시 로그인 하시기 바랍니다.\");\n";
				echo "	if (opener) {\n";
				echo "		opener.parent.location.href=\"logout.php\";\n";
				echo "		window.close();\n";
				echo "	} else {\n";
				echo "		parent.location.href=\"logout.php\";\n";
				echo "	}\n";
				echo "</script>\n";
				exit;
			}
			$this->venderdata=$row;
		} else {
			echo "<script>\n";
			echo "	alert(\"정상적인 경로로 다시 접속하시기 바랍니다..\");\n";
			echo "	if (opener) {\n";
			echo "		opener.parent.location.href=\"logout.php\";\n";
			echo "		window.close();\n";
			echo "	} else {\n";
			echo "		parent.location.href=\"logout.php\";\n";
			echo "	}\n";
			echo "</script>\n";
			exit;
		}
		pmysql_free_result($result);
	}

	function ShopVenderLog($vidx,$ip,$content,$date="") {
		if (strlen($date)!=14) {
			$date=date("YmdHis");
		}
		
		if ($ip == '') $ip=$_SERVER['REMOTE_ADDR'];

		$sql = "INSERT INTO tblvenderlog ( ";
		$sql.= "vender, ";
		$sql.= "date, ";
		$sql.= "ip, ";
		$sql.= "content) VALUES (";
		$sql.= "'{$vidx}', ";
		$sql.= "'{$date}', ";
		$sql.= "'{$ip}', ";
		$sql.= "'{$content}') ";
		pmysql_query($sql,get_db_conn());
	}
}

class _MiniLib {
	var $vender		= "";
	var $MiniData	= "";
	var $isVender	= false;

	var $prdataA	= array();
	var $prdataB	= array();
	var $codecnt	= array();
	var $codename	= array();
	var $themeprdataA	= array();
	var $themeprdataB	= array();
	var $themecodecnt	= array();
	var $themecodename	= array();

	var $code_locname	= "";

	var $sch_codeA=array();
	var $sch_codeB=array();
	var $sch_codeC=array();
	var $sch_codeD=array();
	var $sch_prcnt=0;

	function _MiniLib($vender) {
		$this->vender=$vender;
	}

	function _MiniInit() {
		if(strlen($this->vender)>0) {
			if($this->getMinishop($row)) {
				$this->isVender=true;
				$this->MiniData=$row;
				if($this->MiniData->shop_width<=0) $this->MiniData->shop_width=900;
				if(strlen($this->MiniData->code_distype)==0) $this->MiniData->code_distype="YY";

				$arrskin=explode(",",$row->skin);
				$top_imgseq=(int)$arrskin[0];
				$top_colorseq=(int)$arrskin[1];
				$menu_colorseq=(int)$arrskin[2];

				$title_backimg=$this->getTitleskin($top_imgseq);
				$title_color=$this->getMenucolor($top_colorseq);
				if($top_imgseq==0) {
					$this->MiniData->top_backimg=DirPath.DataDir."shopimages/vender/top_".$this->vender.".gif";
				} else {
					$this->MiniData->top_backimg=DirPath."images/minishop/title_skin/".$title_color->color."_".$title_backimg.".gif";
				}
				$this->MiniData->top_fontcolor=$title_color->fontcolor;

				$menu_color=$this->getMenucolor($menu_colorseq);
				$this->MiniData->color=$menu_color->color;
				$this->MiniData->leftcolor=$menu_color->leftcolor;
				$this->MiniData->fontcolor=$menu_color->fontcolor;

				if(file_exists(DirPath.DataDir."shopimages/vender/logo_".$this->vender.".gif")) {
					$this->MiniData->logo=DirPath.DataDir."shopimages/vender/logo_".$this->vender.".gif";
				} else {
					$this->MiniData->logo=DirPath."images/minishop/logo.gif";
				}

				$this->setCustinfo();
			}
		}
	}

	function getMinishop(&$row) {
		$sql = "SELECT * FROM tblvenderstore a, tblvenderstorecount b ";
		$sql.= "WHERE a.vender='".$this->vender."' AND a.vender=b.vender ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$res=true;
		} else {
			$res=false;
		}
		pmysql_free_result($result);
		return $res;
	}

	function getTitleskin($seq) {
		$sql = "SELECT backimg FROM tblvendertitleskin WHERE seq='".$seq."' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		return $row->backimg;
	}

	function getMenucolor($seq) {
		$sql = "SELECT * FROM tblvenderboxgroupcolor WHERE seq='".$seq."' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		return $row;
	}

	function getCode($tgbn="",$code="") {
		GLOBAL $_ShopInfo;
		if(substr($this->MiniData->code_distype,0,1)!="Y") return;

		$sql = "SELECT SUBSTRING(a.productcode,1,6) as prcode, COUNT(*) as prcnt ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.vender='".$this->vender."' AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "GROUP BY prcode ";
		$result=pmysql_query($sql,get_db_conn());
		
		$codecnt=array();
		$codes=array();
		$ii=0;
		while($row=pmysql_fetch_object($result)) {
			$codes[$ii]["A"]=substr($row->prcode,0,3);
			$codes[$ii]["B"]=substr($row->prcode,3,3);
			$codecnt[substr($row->prcode,0,3)]+=$row->prcnt;
			$codecnt[$row->prcode]+=$row->prcnt;
			$codecnt["000"]+=$row->prcnt;
			$ii++;
		}
		pmysql_free_result($result);
		$this->codecnt=$codecnt;

		$prdataA=array();
		$prdataB=array();
		$codename=array();
		if(count($codes)>0) {
			$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
			$sql.= "WHERE (";
			$_=array();
			foreach($codes as $code) {
				$_[] = "(codeA='".$code["A"]."' AND (codeB='000' OR codeB='".$code["B"]."')) ";
			}
			$sql.=implode(" OR ",$_);
			$sql.= ") ";
			$sql.= "AND codeC='000' AND codeD='000' ";
			$sql.= "AND group_code!='NO' AND (type LIKE 'L%') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				$tmpcode3=$row->codeA.$row->codeB;
				$codename[$tmpcode3]=$row->code_name;
				if($row->codeB=="000") {
					$prdataA[]=$row;
					if($tgbn=="10") {
						if(substr($code,0,3)==$row->codeA) {
							$this->code_locname=$row->code_name;
						}
					}
				} else {
					$prdataB[$row->codeA][]=$row;
				}
			}
			pmysql_free_result($result);
			$this->prdataA=$prdataA;
			$this->prdataB=$prdataB;
			$this->codename=$codename;
		}
	}

	function getThemecode($tgbn="",$code="") {
		GLOBAL $_ShopInfo;
		if(substr($this->MiniData->code_distype,-1)!="Y") return;

		$sql = "SELECT a.themecode, COUNT(*) as prcnt ";
		$sql.= "FROM tblvenderthemeproduct a, tblproduct b ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
		$sql.= "WHERE a.vender='".$this->vender."' ";
		$sql.= "AND a.vender=b.vender AND a.productcode=b.productcode ";
		$sql.= "AND b.display='Y' ";
		$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "GROUP BY a.themecode ";
		$result=pmysql_query($sql,get_db_conn());
		
		$codecnt=array();
		$codes=array();
		$ii=0;
		while($row=pmysql_fetch_object($result)) {
			$codes[$ii]["A"]=substr($row->themecode,0,3);
			$codes[$ii]["B"]=substr($row->themecode,3,3);
			$codecnt[substr($row->themecode,0,3)]+=$row->prcnt;
			$codecnt[$row->themecode]+=$row->prcnt;
			$ii++;
		}
		pmysql_free_result($result);
		$this->themecodecnt=$codecnt;

		$themeprdataA=array();
		$themeprdataB=array();
		$themecodename=array();
		if(count($codes)>0) {
			$sql = "SELECT codeA,codeB,code_name FROM tblvenderthemecode ";
			$sql.= "WHERE vender='".$this->vender."' AND ( ";
			$_=array();
			foreach($codes as $code) {
				$_[] = "(codeA='".$code["A"]."' AND (codeB='000' OR codeB='".$code["B"]."')) ";
			}
			$sql.=implode(" OR ",$_);
			$sql.= ") ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				$tmpcode3=$row->codeA.$row->codeB;
				$themecodename[$tmpcode3]=$row->code_name;
				if($row->codeB=="000") {
					$themeprdataA[]=$row;
					if($tgbn=="20") {
						if(substr($code,0,3)==$row->codeA) {
							$this->code_locname=$row->code_name;
						}
					}
				} else {
					$themeprdataB[$row->codeA][]=$row;
				}
			}
			pmysql_free_result($result);
			$this->themeprdataA=$themeprdataA;
			$this->themeprdataB=$themeprdataB;
			$this->themecodename=$themecodename;
		}
	}

	function getSearchcode($likecode,$qry) {
		GLOBAL $_ShopInfo;
		$sql = "SELECT SUBSTRING(a.productcode,1,12) as prcode, COUNT(*) as prcnt ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "GROUP BY prcode ";
		$result=pmysql_query($sql,get_db_conn());
		$code_prcnt=0;
		$codeA=array();
		$codeB=array();
		$codeC=array();
		$codeD=array();
		while($row=pmysql_fetch_object($result)) {
			$c1=substr($row->prcode,0,3);
			$c2=substr($row->prcode,3,3);
			$c3=substr($row->prcode,6,3);
			$c4=substr($row->prcode,9,3);
			$code_prcnt+=$row->prcnt;
			$codeA[$c1]["cnt"]+=$row->prcnt;
			if($c2!="000") {
				$codeB[$c1][$c2]["cnt"]+=$row->prcnt;
				if($c3!="000") {
					$codeC[$c1][$c2][$c3]["cnt"]+=$row->prcnt;
					if($c4!="000") {
						$codeD[$c1][$c2][$c3][$c4]["cnt"]+=$row->prcnt;
					}
				}
			}
		}
		pmysql_free_result($result);

		if($code_prcnt>0) {
			$sql = "SELECT codeA, codeB, codeC, codeD, code_name FROM tblproductcode ";
			$sql.= "WHERE codeA IN (";
			$_=array();
			while(list($key,$val)=each($codeA)) {
				$_[] = "'".$key."'";
			}
			$sql.=implode(',',$_);
			$sql.= ") ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				$c1=$row->codeA;
				$c2=$row->codeB;
				$c3=$row->codeC;
				$c4=$row->codeD;
				if($c2=="000" && $c3=="000" && $c4=="000") {
					$codeA[$c1]["name"]=$row->code_name;
				} else if($c3=="000" && $c4=="000") {
					if(is_array($codeB[$c1][$c2])) {
						$codeB[$c1][$c2]["name"]=$row->code_name;
					}
				} else if($c4=="000") {
					if(is_array($codeC[$c1][$c2][$c3])) {
						$codeC[$c1][$c2][$c3]["name"]=$row->code_name;
					}
				} else {
					if(is_array($codeD[$c1][$c2][$c3][$c4])) {
						$codeD[$c1][$c2][$c3][$c4]["name"]=$row->code_name;
					}
				}
			}
			pmysql_free_result($result);
		}
		$this->sch_codeA=$codeA;
		$this->sch_codeB=$codeB;
		$this->sch_codeC=$codeC;
		$this->sch_codeD=$codeD;
		$this->sch_prcnt=$code_prcnt;
	}

	function setCustinfo() {
		$cust_data=array();
		$temp=explode("=",$this->MiniData->cust_info);
		for ($i=0;$i<count($temp);$i++) {
			if (substr($temp[$i],0,4)=="TEL=")			$cust_data["TEL"]=substr($temp[$i],4);
			else if (substr($temp[$i],0,4)=="FAX=")		$cust_data["FAX"]=substr($temp[$i],4);
			else if (substr($temp[$i],0,6)=="EMAIL=")	$cust_data["EMAIL"]=substr($temp[$i],6);
			else if (substr($temp[$i],0,6)=="TIME1=")	$cust_data["TIME1"]=substr($temp[$i],6);
			else if (substr($temp[$i],0,6)=="TIME2=")	$cust_data["TIME2"]=substr($temp[$i],6);
			else if (substr($temp[$i],0,6)=="TIME3=")	$cust_data["TIME3"]=substr($temp[$i],6);
		}
		if($cust_data["TIME1"]=="0") $cust_data["TIME1"]="휴무";
		if($cust_data["TIME2"]=="0") $cust_data["TIME2"]="휴무";
		if($cust_data["TIME3"]=="0") $cust_data["TIME3"]="휴무";
		$this->MiniData->custdata=$cust_data;
	}

	function getMiniData()	{return $this->MiniData;}
}

function noselectmenu($name,$url,$idx,$end){
	$str_name = $name;
	if ($idx == "YES") {
		$str_name = "<font color=#FF6000>".$name."</font>";
	}
	echo "<tr>\n";
	echo "	<td width=8><img src=images/icon_dot01.gif border=0 align=absmiddle></td>\n";
	echo "	<td><a href=\"".$url."\">".$str_name."</a></b></td>\n";
	echo "</tr>\n";
	if($end==2 || $end==3){
		echo "<tr><td colspan=2 height=8></td></tr>";
	}
}

function getDeligbn_detail($ordercode,$deli_gbn) {
	//N:미처리, X:배송요청, S:발송준비, Y:배송완료, C:주문취소, R:반송, D:취소요청, E:환불대기[가상계좌일 경우만]
	$sql = "SELECT deli_gbn FROM tblorderproduct WHERE ordercode='".$ordercode."' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	$sql.= "GROUP BY deli_gbn ";
	$result=pmysql_query($sql,get_db_conn());
	$arrdeli=array();
	while($row=pmysql_fetch_object($result)) {
		$arrdeli[$row->deli_gbn]=true;
	}
	pmysql_free_result($result);

	$res="";
	if($deli_gbn=="N" && count($arrdeli)>0) {
		$res="N";
	} else if($deli_gbn=="S" && count($arrdeli)>0) {
		if($arrdeli["N"]) $res="N";
		else $res="S";
	}
	
	if(strstr("NS",$res)) {
		//미처리, 발송준비 까지는 입점업체에서의 상태 변경시 바로 적용
		$sql = "UPDATE tblorderinfo SET deli_gbn='".$res."' WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());
	}
	return $res;
}


class Paging {
	public $block;
	public $gotopage;
	public $nowblock;
	public $t_count;
	public $pagecount;
	public $total_block;
	public $a_prev_page;
	public $print_page;
	public $a_next_page;
	
	function __construct($sql_or_count,$page_num=10,$list_num=20,$link='GoPage',$block=0,$gotopage=0) {
		global $setup;
		//리스트 세팅
		$setup['page_num'] = $page_num;
		$setup['list_num'] = $list_num;
		
		$this->gotopage = $gotopage;		
		if($link=='GoPage2') {
			$block=$_REQUEST["block2"];
			$this->gotopage=$_REQUEST["gotopage2"];			
		} elseif($block==0) {
			$block=$_REQUEST["block"];
			$this->gotopage=$_REQUEST["gotopage"];			
		}

		if ($block != "") {
			$nowblock = $block;
			$this->curpage  = $block * $setup['page_num'] + $this->gotopage;
		} else {
			$nowblock = 0;
		}

		if (empty($this->gotopage)) {
			$this->gotopage = 1;
		}
		if(is_int($sql_or_count)) {
			$this->t_count = $sql_or_count;	
		} else {
		    $result    = pmysql_query($sql_or_count,get_db_conn());
		    $row       = pmysql_fetch_array($result);
			$this->t_count = $row[0];
		    pmysql_free_result($result);
		}
		$this->pagecount = (($this->t_count - 1) / $setup['list_num']) + 1;
		
        $total_block = intval($this->pagecount / $setup['page_num']);

        if (($this->pagecount % $setup['page_num']) > 0) {
            $total_block += 1;
        }

        $total_block -= 1;
		
        if (ceil($this->t_count / $setup['list_num']) > 0) {
            // 이전    x개 출력하는 부분 - 시작
            $a_first_block = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0)
                	$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";
				else
					$a_first_block .= "<a href='{$link}&block=0&gotopage=1' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0  align=\"absmiddle\" width=\"17\" height=\"14\"></a> ";

                $prev_page_exists = true;
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0)
                	$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\">[prev]</a>&nbsp;&nbsp;";
				else
					$a_prev_page .= "<a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\">[이전 {$setup['page_num']}개]</a> ";

                $a_prev_page = $a_first_block.$a_prev_page;
            }
			
            // 일반 블럭에서의 페이지 표시부분 - 시작

            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
                    if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= "<span class=font_orange2><B>[".(intval($nowblock * $setup['page_num']) + $gopage)."]</B></span> ";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">[".(intval($nowblock * $setup['page_num']) + $gopage)."]</a> ";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=". (intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">[".(intval($nowblock*$setup['page_num']) + $gopage)."]</a> ";
                    }
                }
            }else {
                if (($this->pagecount % $setup['page_num']) == 0) {
                    $lastpage = $setup['page_num'];
                }else {
                    $lastpage = $this->pagecount % $setup['page_num'];
                }

                for ($gopage = 1; $gopage <= $lastpage; $gopage++) {					
                    if (intval($nowblock * $setup['page_num']) + $gopage == intval($this->gotopage)) {
                        $print_page .= "<span class=font_orange2><B>[".(intval($nowblock * $setup['page_num']) + $gopage)."]</B></span> ";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">[".(intval($nowblock * $setup['page_num']) + $gopage)."]</a> ";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">[".(intval($nowblock*$setup['page_num']) + $gopage)."]</a> ";
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
                $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
                $last_gotopage = ceil($this->t_count / $setup['list_num']);

				if(strpos($link,'GoPage')===0)
                	$a_last_block .= "&nbsp;&nbsp;<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";
				else
					$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0  align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

                $next_page_exists = true;
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0)
                	$a_next_page .= "&nbsp;&nbsp;<a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">[next]</a>";
				else
					$a_next_page .= " <a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">[이후 {$setup['page_num']}개]</a>";

                $a_next_page .= $a_last_block;
            }
        }else {
            $print_page = "<B><span class=font_orange2>[1]</span></B>";
        }
		$this->a_prev_page = $a_prev_page;		
		$this->a_next_page = $a_next_page;		
		$this->print_page = $print_page;
		
	}
	
	function getSql($sql) {
		global $setup;
		return $sql." LIMIT {$setup['list_num']} OFFSET ".($setup['list_num'] * ($this->gotopage - 1));
	}
	
}