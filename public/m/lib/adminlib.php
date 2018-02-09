<?php
function noselectmenu($name,$url,$idx,$end){
	if($end==0 || $end==3){
		echo "<tr><td width=\"158\" bgcolor=\"#DFF2FF\" height=\"8\"></td></tr>";
	}
	$str_style_class="2depth_default";
	if ($idx == "YES") {
		$str_style_class = "2depth_select";
	}
	echo "<tr>\n";
	echo "	<td width=\"158\" style=\"padding-left:13pt;\" bgcolor=\"#DFF2FF\" class=\"{$str_style_class}\" height=\"19\"><img src=\"images/icon_leftmenu1.gif\" width=\"8\" height=\"10\" border=\"0\"><a href=\"{$url}\">{$name}</a></td>\n";
	echo "</tr>\n";
	if($end==2 || $end==3){
		echo "<tr><td width=\"158\" bgcolor=\"#DFF2FF\" height=\"8\"></td></tr>";
		echo "<tr><td width=\"158\"><img src=\"images/leftmenu_line.gif\" width=\"158\" height=\"1\" border=\"0\"></td></tr>";
		echo "<tr><td height=\"5\" bgcolor=\"#FFFFFF\"></td></tr>";
	}
}

function getDeligbn($arrdeli,$strdeli,$true=true) {
	$tempdeli=$arrdeli;
	$res=true;
	foreach($tempdeli as $key=>$val) {
		if($true) {
			if(!preg_match("/^({$strdeli})$/", $val)) {
				$res=false;
				break;
			}
		} else {
			if(preg_match("/^({$strdeli})$/", $val)) {
				$res=false;
				break;
			}
		}
	}
	return $res;
}

function getDeligbn_detail($ordercode,$deli_gbn) {
	//N:미처리, X:배송요청, S:발송준비, Y:배송완료, C:주문취소, R:반송, D:취소요청, E:환불대기[가상계좌일 경우만]
	$sql = "SELECT deli_gbn FROM tblorderproduct WHERE ordercode='{$ordercode}' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
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
		$sql = "UPDATE tblorderinfo SET deli_gbn='{$res}' WHERE ordercode='{$ordercode}' ";
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
    
    function __construct($sql_or_count,$page_num,$list_num,$link='GoPage',$block=0,$gotopage=0) {
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
                if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0)
                    $a_first_block .= "<a href=\"javascript:{$link}(0,1);\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";
                else
                    $a_first_block .= "<a href='{$link}&block=0&gotopage=1' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0  align=\"absmiddle\" width=\"17\" height=\"14\"></a> ";

                $prev_page_exists = true;
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
                if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0)
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
                        if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0)
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
                        if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0 )
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

                if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0 )
                    $a_last_block .= "&nbsp;&nbsp;<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";
                else
                    $a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0  align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

                $next_page_exists = true;
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
                if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0 ) 
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

class newPaging {
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
				if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0 )
                	$a_first_block .= "<li><a href=\"javascript:{$link}(0,1);\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><<</a></li>";
				else
					$a_first_block .= "<li><a href='{$link}&block=0&gotopage=1' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><<</a></li> ";

                $prev_page_exists = true;
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0)
                	$a_prev_page .= "<li><a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\"><</a>&nbsp;&nbsp;";
				else
					$a_prev_page .= "<li><a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\">[이전 {$setup['page_num']}개]</a></li> ";

                $a_prev_page = $a_first_block.$a_prev_page;
            }
			
            // 일반 블럭에서의 페이지 표시부분 - 시작

            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
                    if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= "<li class=\"this\"><a>".(intval($nowblock * $setup['page_num']) + $gopage)."</a></li>";
                    }else {
						if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0)
                        	$print_page .= "<li><a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a></li> ";
						else
							$print_page .= "<li><a href='{$link}&block={$nowblock}&gotopage=". (intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a></li> ";
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
                        $print_page .= "<li class=\"this\"><a>".(intval($nowblock * $setup['page_num']) + $gopage)."</a></li>";
                    }else {
						if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0)
                        	$print_page .= "<li><a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a></li> ";
						else
							$print_page .= "<li><a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a></li> ";
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
                $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
                $last_gotopage = ceil($this->t_count / $setup['list_num']);

				if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0)
                	$a_last_block .= "<li><a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">>></a></li>";
				else
					$a_last_block .= "<li> <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">>></a></li>";

                $next_page_exists = true;
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
           if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0 || strpos($link,'T_GoPage')===0 || strpos($link,'T_Brand_GoPage')===0)
                	$a_next_page .= "<li><a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">></a></li>";
				else
					$a_next_page .= " <li><a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">[이후 {$setup['page_num']}개]</a></li>";

                $a_next_page .= $a_last_block;
            }
        }else {
            $print_page = "<li class=\"this\"><a>1</a></li>";
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
