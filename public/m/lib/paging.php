<?php
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
	
	function __construct($sql_or_count,$page_num=10,$list_num=10,$link='GoPage',$class=false) {
		global $setup;
		//리스트 세팅
		$setup['page_num'] = $page_num;
		$setup['list_num'] = $list_num;
		
		if($class) {
			$style_s = "<FONT class=\"choiceprlist\">";
			$style_e = "</FONT>";
			$style2_s = "<FONT class=\"prlist\">";
			$style2_e = "</FONT>";
		} else {
			$style_s = "<FONT color=red><B>";
			$style_e = "</B></FONT>";
		}
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
                	$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">{$style2_s}[1...]{$style2_e}</a>&nbsp;&nbsp;";
				else
					$a_first_block .= "<a href='{$link}&block=0&gotopage=1' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">{$style2_s}[1...]{$style2_e}</a> ";

                $prev_page_exists = true;
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0)
                	$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\">{$style2_s}[prev]{$style2_e}</a>&nbsp;&nbsp;";
				else
					$a_prev_page .= "<a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\">{$style2_s}[이전 {$setup['page_num']}개]{$style2_e}</a> ";

                $a_prev_page = $a_first_block.$a_prev_page;
            }
			
            // 일반 블럭에서의 페이지 표시부분 - 시작

            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
                    if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= "{$style_s}".(intval($nowblock * $setup['page_num']) + $gopage)."{$style_e} ";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">{$style2_s}[".(intval($nowblock * $setup['page_num']) + $gopage)."]{$style2_e}</a> ";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=". (intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">{$style2_s}[".(intval($nowblock*$setup['page_num']) + $gopage)."]{$style2_e}</a> ";
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
                        $print_page .= "{$style_s}".(intval($nowblock * $setup['page_num']) + $gopage)."{$style_e} ";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">{$style2_s}[".(intval($nowblock * $setup['page_num']) + $gopage)."]{$style2_e}</a> ";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">{$style2_s}[".(intval($nowblock*$setup['page_num']) + $gopage)."]{$style2_e}</a> ";
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
                $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
                $last_gotopage = ceil($this->t_count / $setup['list_num']);

				if(strpos($link,'GoPage')===0)
                	$a_last_block .= "&nbsp;&nbsp;<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">{$style2_s}[...{$last_gotopage}]{$style2_e}</a>";
				else
					$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">{$style2_s}[...{$last_gotopage}]{$style2_e}</a>";

                $next_page_exists = true;
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0)
                	$a_next_page .= "&nbsp;&nbsp;<a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">{$style2_s}[next]{$style2_e}</a>";
				else
					$a_next_page .= " <a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">{$style2_s}[이후 {$setup['page_num']}개]{$style2_e}</a>";

                $a_next_page .= $a_last_block;
            }
        }else {
            $print_page = "{$style2_s}1{$style2_e}";
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
			//$this->t_count = pmysql_num_rows( $result );
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
                	$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"pre\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"/image/common/page_pre.png\" alt=\"\" /></a>";
				else
					$a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"pre\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"/image/common/page_pre.png\" alt=\"\" /></a>";

                $prev_page_exists = true;
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0)
                	$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\"><</a>&nbsp;&nbsp;";
				else
					$a_prev_page .= "<a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\">[이전 {$setup['page_num']}개]</a>";

                $a_prev_page = $a_first_block.$a_prev_page;
            }
			
            // 일반 블럭에서의 페이지 표시부분 - 시작
		
            if (intval($total_block) <> intval($nowblock)) {
				
                $print_page = "";
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
					if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= "<a class=\"on\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=". (intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a>";
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
                        $print_page .= "<a class=\"on\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a> ";
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
                $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
                $last_gotopage = ceil($this->t_count / $setup['list_num']);

				if(strpos($link,'GoPage')===0)
                	$a_last_block .= "<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" class=\"pre\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../image/common/page_next.png\" alt=\"\" /></a>";
				else
					$a_last_block .= "<a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' class=\"pre\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../image/common/page_next.png\" alt=\"\" /></a>";

                $next_page_exists = true;
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
           if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0)
                	$a_next_page .= "<a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">></a>";
				else
					$a_next_page .= "<a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">[이후 {$setup['page_num']}개]</a>";

                $a_next_page .= $a_last_block;
            }
        }else {
            $print_page = "<a class=\"on\">1</a>";
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



class Tem001_Paging {
	public $block;
	public $gotopage;
	public $nowblock;
	public $t_count;
	public $pagecount;
	public $total_block;
	public $a_prev_page;
	public $print_page;
	public $a_next_page;
	public $first_class = 'pre_all';	// 첫번째 페이지 클래스명
	public $prev_class = 'pre';			// 이전 화살표 클래스명
	public $onpage_class = 'on';		// 현재 페이지 클래스명
	public $page_class = '';			// 보인는 페이지 클래스명
	public $next_class = 'next';		// 다음 화살표 클래스명
	public $last_class = 'next_all';	// 마지막 페이지 클래스명
	public $prev_str = '';				// 이전 화살표 문자
	public $next_str = '';				// 다음 화살표 문자
	public $show_edge_num = false;		// 첫페이지와 마지막 페이지를 숫자로 나타낼지 여부
	public $first_page_str = "";		// 첫번째 페이지를 숫자로 안나타낼때 대신 나타낼 문자
	public $last_page_str = "";			// 마지막 페이지를 숫자로 안나타낼때 대신 나타낼 문자
	
	function __construct($sql_or_count,$page_num=10,$list_num=10,$link='GoPage',$class=false) {
		$this->_exec($sql_or_count,$page_num,$list_num,$link='GoPage',$class=false);
	}
	
	function _exec($sql_or_count,$page_num,$list_num,$link='GoPage',$class=false){
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
			$this->t_count = pmysql_num_rows($result);
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
            
            //첫페이지 시작
            $a_first_block = "";
            //첫페이지를 나타낼 문자 설정
			if($this->show_edge_num){
				$first_gotopage_str = "1";
			}else{
				$first_gotopage_str = $this->first_page_str;
			}
			//문자 설정 끝 
			
			$f_class = $this->first_class;
			$l_class = $this->last_class;
			$p_class = $this->prev_class;
			$n_class = $this->next_class;
			$o_class = $this->onpage_class;
			$page_class = $this->page_class;
			$p_str = $this->prev_str;
			$n_str = $this->next_str;
			
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0)
                	$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"{$f_class}\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">[{$first_gotopage_str}]</a>";
				else
					$a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"{$f_class}\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">[{$first_gotopage_str}]</a> ";

                $prev_page_exists = true;
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0)
                	$a_prev_page .= " <a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" onMouseOut=\"window.status='';return true\" class=\"{$p_class}\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\">{$p_str}</a>";
				else
					$a_prev_page .= " <a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' onMouseOut=\"window.status='';return true\" class=\"{$p_class}\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\">{$p_str}</a> ";

                $a_prev_page = $a_first_block.$a_prev_page;
            }
			
            // 일반 블럭에서의 페이지 표시부분 - 시작

            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
                    if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= " <a><b>".(intval($nowblock * $setup['page_num']) + $gopage)."</b></a>";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= " <a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" class=\"{$page_class}\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">[".(intval($nowblock * $setup['page_num']) + $gopage)."]</a>";
						else
							$print_page .= " <a href='{$link}&block={$nowblock}&gotopage=". (intval($nowblock*$setup['page_num']) + $gopage)."' class=\"{$page_class}\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">[".(intval($nowblock*$setup['page_num']) + $gopage)."]</a>";
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
                        $print_page .= " <a class='{$o_class}'>".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= " <a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
						else
							$print_page .= " <a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a>";
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
 
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
                $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
                $last_gotopage = ceil($this->t_count / $setup['list_num']);

	           //마지막 페이지를 나타낼 문자 설정
				if($this->show_edge_num){
					$last_gotopage_str = $last_gotopage;
				}else{
					$last_gotopage_str = $first_page_str;
				}
				
				
				if(strpos($link,'GoPage')===0)
                	$a_last_block .= " <a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" onMouseOut=\"window.status='';return true\"  class=\"{$l_class}\" onMouseOver=\"window.status='마지막 페이지';return true\">[{$last_gotopage_str}]</a>";
				else
					$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' onMouseOut=\"window.status='';return true\"  class=\"{$l_class}\" onMouseOver=\"window.status='마지막 페이지';return true\">[{$last_gotopage_str}]</a>";

                $next_page_exists = true;
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0)
                	$a_next_page .= " <a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\"  class=\"{$n_class}\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">{$n_str}</a>";
				else
					$a_next_page .= " <a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."' onMouseOut=\"window.status='';return true\" class=\"{$l_class}\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">{$n_str}</a>";

                $a_next_page .= $a_last_block;
            }
        }else {
            $print_page = "<a><b>1</b></a>";
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

class Tem001_saveheels_Paging {
	public $block;
	public $gotopage;
	public $nowblock;
	public $t_count;
	public $pagecount;
	public $total_block;
	public $a_prev_page;
	public $print_page;
	public $a_next_page;
	
	function __construct($sql_or_count,$page_num=10,$list_num=10,$link='GoPage',$class=false) {
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
		    $row       = pmysql_num_rows($result);
			$this->t_count = $row;
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
                				//$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"next_prev\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"../img/icon/icon_page_prev_end.png\" alt=\"\" /></a>";
					$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"pre_all\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"></a>";
				else
					//$a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"next_prev\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"></a> ";
					$a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"pre_all\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"></a> ";

                $prev_page_exists = true;
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0)
                	//$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" onMouseOut=\"window.status='';return true\" class=\"next_prev\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\"><img src=\"../img/icon/icon_page_prev.png\" alt=\"\" /></a>";
                	$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" onMouseOut=\"window.status='';return true\" class=\"pre\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\"></a>";
				else
					//$a_prev_page .= "<a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' onMouseOut=\"window.status='';return true\" class=\"next_prev\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\"><img src=\"../img/icon/icon_page_prev.png\" alt=\"\" /></a> ";
					$a_prev_page .= "<a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' onMouseOut=\"window.status='';return true\" class=\"pre\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\"></a> ";

                $a_prev_page = $a_first_block.$a_prev_page;
            }
			
            // 일반 블럭에서의 페이지 표시부분 - 시작

            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
                    if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= "<a class=\"on\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=". (intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a>";
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
                        $print_page .= "<a class='on'>".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a>";
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
                $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
                $last_gotopage = ceil($this->t_count / $setup['list_num']);

				if(strpos($link,'GoPage')===0)
			                	//$a_last_block .= "<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" onMouseOut=\"window.status='';return true\"  class=\"next_prev\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../img/icon/icon_page_next_end.png\" alt=\"\" /></a>";
					$a_last_block .= "<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" onMouseOut=\"window.status='';return true\"  class=\"next_all\" onMouseOver=\"window.status='마지막 페이지';return true\"></a>";
				else
					//$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' onMouseOut=\"window.status='';return true\"  class=\"next_prev\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../img/icon/icon_page_next_end.png\" alt=\"\" /></a>";
					$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' onMouseOut=\"window.status='';return true\"  class=\"next_all\" onMouseOver=\"window.status='마지막 페이지';return true\"></a>";

                $next_page_exists = true;
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0)
			                	//$a_next_page .= "<a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\"  class=\"next_prev\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\"><img src=\"../img/icon/icon_page_next.png\" alt=\"\" /></a>";
			                $a_next_page .= "<a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\"  class=\"next\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\"></a>";
				else
					//$a_next_page .= " <a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."' onMouseOut=\"window.status='';return true\" class=\"next_prev\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\"><img src=\"../img/icon/icon_page_next.png\" alt=\"\" /></a>";
					$a_next_page .= " <a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."' onMouseOut=\"window.status='';return true\" class=\"next\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\"></a>";

                $a_next_page .= $a_last_block;
            }
        }else {
            $print_page = "<a class='on'>1</a>";
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





class Tem002_Paging {
	public $block;
	public $gotopage;
	public $nowblock;
	public $t_count;
	public $pagecount;
	public $total_block;
	public $a_prev_page;
	public $print_page;
	public $a_next_page;
	
	function __construct($sql_or_count,$page_num=10,$list_num=10,$link='GoPage',$class=false) {
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
                	$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"pre\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><<</a>&nbsp;&nbsp;";
				else
					$a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"pre\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><<</a> ";

                $prev_page_exists = true;
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0)
                	$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" onMouseOut=\"window.status='';return true\" class=\"pre\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\"><</a>&nbsp;&nbsp;";
				else
					$a_prev_page .= "<a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' onMouseOut=\"window.status='';return true\" class=\"pre\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\"><</a> ";

                $a_prev_page = $a_first_block.$a_prev_page;
            }
			
            // 일반 블럭에서의 페이지 표시부분 - 시작

            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
                    if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= "<a class=\"select\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a>";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=". (intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a> ";
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
                        $print_page .= "<a class=\"select\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a> ";
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
                $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
                $last_gotopage = ceil($this->t_count / $setup['list_num']);

				if(strpos($link,'GoPage')===0)
                	$a_last_block .= "&nbsp;&nbsp;<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" onMouseOut=\"window.status='';return true\"  class=\"pre\" onMouseOver=\"window.status='마지막 페이지';return true\">>></a>";
				else
					$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' onMouseOut=\"window.status='';return true\"  class=\"pre\" onMouseOver=\"window.status='마지막 페이지';return true\">>></a>";

                $next_page_exists = true;
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0)
                	$a_next_page .= "&nbsp;&nbsp;<a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\"  class=\"pre\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">></a>";
				else
					$a_next_page .= " <a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."' onMouseOut=\"window.status='';return true\" class=\"pre\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\">></a>";

                $a_next_page .= $a_last_block;
            }
        }else {
            $print_page = "<a class=\"select\">1</a>";
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








class ajaxPaging {
	public $block;
	public $gotopage;
	public $nowblock;
	public $t_count;
	public $pagecount;
	public $total_block;
	public $a_prev_page;
	public $print_page;
	public $a_next_page;
	
	function __construct($sql_or_count,$page_num=10,$list_num=10,$link='GoPage') {
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
                	$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"../images/common/btn_review_first.png\" alt=\"첫페이지\" /></a>&nbsp;&nbsp;";
				else
					$a_first_block .= "<a href='{$link}&block=0&gotopage=1' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"../images/common/btn_review_first.png\" alt=\"첫페이지\" /></a> ";

                $prev_page_exists = true;
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0)
                	$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\"><img src=\"../images/common/btn_review_prev.png\" alt=\"이전페이지\" /></a>&nbsp;&nbsp;";
				else
					$a_prev_page .= "<a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$setup['page_num']} 페이지';return true\"><img src=\"../images/common/btn_review_prev.png\" alt=\"이전페이지\" /></a> ";

                $a_prev_page = $a_first_block.$a_prev_page;
            }
			
            // 일반 블럭에서의 페이지 표시부분 - 시작

            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
                    if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= "<a class='on'>".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						//$print_page .= "<strong>".(intval($nowblock * $setup['page_num']) + $gopage)."</strong> ";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=". (intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a> ";
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
                        $print_page .= "<a class='on'><b>".(intval($nowblock * $setup['page_num']) + $gopage)."</b></a> ";
						//$print_page .= "<strong>".(intval($nowblock * $setup['page_num']) + $gopage)."</strong> ";
                    }else {
						if(strpos($link,'GoPage')===0)
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock * $setup['page_num']) + $gopage)."';return true\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						else
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">".(intval($nowblock*$setup['page_num']) + $gopage)."</a> ";
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
                $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
                $last_gotopage = ceil($this->t_count / $setup['list_num']);

				if(strpos($link,'GoPage')===0)
                	$a_last_block .= "&nbsp;&nbsp;<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../images/common/btn_review_last.png\" alt=\"마지막페이지\" /></a>";
				else
					$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../images/common/btn_review_last.png\" alt=\"마지막페이지\" /></a>";

                $next_page_exists = true;
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0)
                	$a_next_page .= "&nbsp;&nbsp;<a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\"><img src=\"../images/common/btn_review_next.png\" alt=\"다음페이지\" /></a>";
				else
					$a_next_page .= " <a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$setup['page_num']} 페이지';return true\"><img src=\"../images/common/btn_review_next.png\" alt=\"다음페이지\" /></a>";

                $a_next_page .= $a_last_block;
            }
        }else {
            $print_page = "<a class='on'> 1 </a>";
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



# DECO&E용 productlist 페이징 2016-02-01 유동혁
/*
class New_Templet_paging {
	public $block;
	public $gotopage;
	public $nowblock;
	public $t_count;
	public $pagecount;
	public $total_block;
	public $a_prev_page;
	public $print_page;
	public $a_next_page;
	
	function __construct($sql_or_count,$page_num=10,$list_num=10,$link='GoPage',$class=false) {
		global $setup;
		//리스트 세팅
		$setup['page_num'] = $page_num;
		$setup['list_num'] = $list_num;
		//var_dump($setup['list_num']);
		
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
		    $row       = pmysql_num_rows($result);
			$this->t_count = $row;
		    pmysql_free_result($result);
		}
		$this->pagecount = (($this->t_count - 1) / $setup['list_num']) + 1;

		if ($this->pagecount < 1) $this->pagecount = 1;

        $total_block = intval($this->pagecount / $setup['page_num']);

        if (($this->pagecount % $setup['page_num']) > 0) {
            $total_block += 1;
        }

        $total_block -= 1;

        if ( ceil($this->t_count / $setup['list_num']) > 0 || true ) {
            // 이전    x개 출력하는 부분 - 시작
            $a_first_block = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0){
					$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"prev-all\" ></a> ";
				} else {
					$a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"prev-all\" ></a> ";
				}
                $prev_page_exists = true;
            } else {
                if ( $this->gotopage == 1 ) {
                    $a_first_block .= "<a href=\"javascript:;\" class=\"prev-all\" ></a> ";
                } else {
                    if(strpos($link,'GoPage')===0){
                        $a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"prev-all\" ></a> ";
                    } else {
                        $a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"prev-all\" ></a> ";
                    }
                }
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0) {
//                 	$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" ";
                	$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".$nowblock.");\" ";
					$a_prev_page .= "class=\"prev\" ></a> ";
				} else {
					$a_prev_page .= "<a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' class=\"prev\" ></a> ";
				}
            } else {
            	if ( $this->gotopage == 1 ) {
                	$a_prev_page .= "<a href=\"javascript:;\" class=\"prev\" ></a> ";
            	} else {
            		if(strpos($link,'GoPage')===0){
            			$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock).",".($this->gotopage -1).");\" class=\"prev\" ></a> ";
            		} else {
            			$a_prev_page .= "<a href='{$link}&block=0&gotopage=1' class=\"prev\" ></a> ";
            		}
            	}

            }
            
            


            $a_prev_page = $a_first_block.$a_prev_page;
			
            // 일반 블럭에서의 페이지 표시부분 - 시작
            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                $lastpage = $this->pagecount % $setup['page_num'];
                for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
                    if ($gopage == intval($this->gotopage)) {
                        $print_page .= "<a class=\"on\">".$gopage."</a> ";
                    }else {
						if(strpos($link,'GoPage')===0) {
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".$gopage.");\" >";
							$print_page .= $gopage."</a> ";
						} else {
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=". $gopage."' >";
							$print_page .=  $gopage."</a> ";
						}
                    }
                }
            } else {
                if (($this->pagecount % $setup['page_num']) == 0) {
                    $lastpage = $setup['page_num'];
                } else {
                    $lastpage = $this->pagecount % $setup['page_num'];
                }

                for ($gopage = 1; $gopage <= $lastpage; $gopage++) {					
                    if (intval($nowblock * $setup['page_num']) + $gopage == intval($this->gotopage)) {
                        $print_page .= "<a class='on'>".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
                    } else {
						if(strpos($link,'GoPage')===0) {
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" >";
							$print_page .= (intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						} else {
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' >";
							$print_page .= (intval($nowblock*$setup['page_num']) + $gopage)."</a> ";
						}
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
            $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
            $last_gotopage = ceil($this->t_count / $setup['list_num']);

            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0) {
					$a_last_block .= "<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" class=\"next-all\" ></a> ";
				} else {
					$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' class=\"next-all\" ></a> ";
				}
                $next_page_exists = true;
            } else {
                if ( $this->gotopage == $last_gotopage || $last_block < 0 ) {
                    $a_last_block .= "<a href=\"javascript:;\" class=\"next-all\" ></a> ";
                } else {
                    if(strpos($link,'GoPage')===0) {
                        $a_last_block .= "<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" class=\"next-all\" ></a> ";
                    } else {
                        $a_last_block .= "<a href=\"{$link}&block={$last_block}&gotopage={$last_gotopage}\" class=\"next-all\" ></a> ";
                    }
                }
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0) {
	                $a_next_page .= "<a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\"  class=\"next\" ></a> ";
				} else {
					$a_next_page .= " <a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."'  class=\"next\" ></a> ";
				}
            } else {
            	if($this->gotopage == $last_gotopage || $last_block < 0) {
            		$a_next_page .= "<a href=\"javascript:;\"  class=\"next\" ></a> ";
            	}else{
            		if(strpos($link,'GoPage')===0) {
		                $a_next_page .= "<a href=\"javascript:{$link}(".($this->gotopage - $nowblock).",".($this->gotopage + 1).");\"  class=\"next\" ></a> ";
					} else {
						$a_next_page .= " <a href='{$link}&block=".($this->gotopage - $nowblock)."&gotopage=".($nowblock+1)."'  class=\"next\" ></a> ";
					}
            	}

            }

            $a_next_page .= $a_last_block;
        }else {
            $print_page = "<a class='on'>1</a>";
        }
		$this->a_prev_page = $a_prev_page?"<span class=\"border_wrap\">{$a_prev_page}</span>":"";		
		$this->a_next_page = $a_next_page?"<span class=\"border_wrap\">{$a_next_page}</span>":"";			
		$this->print_page = $print_page;

	}
	
	function getSql($sql) {
		global $setup;
		return $sql." LIMIT {$setup['list_num']} OFFSET ".($setup['list_num'] * ($this->gotopage - 1));
	}
	
}
*/

# 템플릿 페이징 ㅠㅠ
class New_Templet_paging {
	public $block;
	public $gotopage;
	public $nowblock;
	public $t_count;
	public $pagecount;
	public $total_block;
	public $a_prev_page;
	public $print_page;
	public $a_next_page;

	function __construct($sql_or_count,$page_num=10,$list_num=10,$link='GoPage',$class=false) {
		global $setup;
		//리스트 세팅
		$setup['page_num'] = $page_num;
		$setup['list_num'] = $list_num;
		//var_dump($setup['list_num']);

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
			$row       = pmysql_num_rows($result);
			$this->t_count = $row;
			pmysql_free_result($result);
		}
		$this->pagecount = (($this->t_count - 1) / $setup['list_num']) + 1;

		if ($this->pagecount < 1) $this->pagecount = 1;

		$total_block = intval($this->pagecount / $setup['page_num']);

		if (($this->pagecount % $setup['page_num']) > 0) {
			$total_block += 1;
		}

		$total_block -= 1;

        if ( ceil($this->t_count / $setup['list_num']) > 0 || true ) {
            // 이전    x개 출력하는 부분 - 시작
            $a_first_block = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0){
					$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"prev-all\" ></a> ";
				} else {
					$a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"prev-all\" ></a> ";
				}
                $prev_page_exists = true;
            } else {
                if ( $this->gotopage == 1 ) {
                    $a_first_block .= "<a href=\"javascript:;\" class=\"prev-all\" ></a> ";
                } else {
                    if(strpos($link,'GoPage')===0){
                        $a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"prev-all\" ></a> ";
                    } else {
                        $a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"prev-all\" ></a> ";
                    }
                }
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0) {
					$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" ";
					$a_prev_page .= "class=\"prev\" ></a> ";
				} else {
					$a_prev_page .= "<a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' class=\"btn m\" ></a> ";
				}
            } else {
            	if ( $this->gotopage == 1 ) {
                	$a_prev_page .= "<a href=\"javascript:;\" class=\"prev\" ></a> ";
            	} else {
            		if(strpos($link,'GoPage')===0){
            			$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock).",".($this->gotopage -1).");\" class=\"prev\" ></a> ";
            		} else {
            			$a_prev_page .= "<a href='{$link}&block=0&gotopage=1' class=\"prev\" ></a> ";
            		}
            	}

            }
            
            


            $a_prev_page = $a_first_block.$a_prev_page;
			
            // 일반 블럭에서의 페이지 표시부분 - 시작
            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                //echo "pagecount = ".$this->pagecount;
                //echo "page_num = ".$setup['page_num'];
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
					if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= "<a class='on'>".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
                    }else {
						if(strpos($link,'GoPage')===0) {
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" >";
							$print_page .= (intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						} else {
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' >";
							$print_page .= (intval($nowblock*$setup['page_num']) + $gopage)."</a> ";
						}
                    }
                }
            } else {
                if (($this->pagecount % $setup['page_num']) == 0) {
                    $lastpage = $setup['page_num'];
                } else {
                    $lastpage = $this->pagecount % $setup['page_num'];
                }

                for ($gopage = 1; $gopage <= $lastpage; $gopage++) {					
                    if (intval($nowblock * $setup['page_num']) + $gopage == intval($this->gotopage)) {
                        $print_page .= "<a class='on'>".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
                    } else {
						if(strpos($link,'GoPage')===0) {
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" >";
							$print_page .= (intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						} else {
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' >";
							$print_page .= (intval($nowblock*$setup['page_num']) + $gopage)."</a> ";
						}
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
            $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
            $last_gotopage = ceil($this->t_count / $setup['list_num']);

            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0) {
					$a_last_block .= "<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" class=\"next-all\" ></a> ";
				} else {
					$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' class=\"next-all\" ></a> ";
				}
                $next_page_exists = true;
            } else {
                if ( $this->gotopage == $last_gotopage || $last_block < 0 ) {
                    $a_last_block .= "<a href=\"javascript:;\" class=\"next-all\" ></a> ";
                } else {
                    if(strpos($link,'GoPage')===0) {
                        $a_last_block .= "<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" class=\"next-all\" ></a> ";
                    } else {
                        $a_last_block .= "<a href=\"{$link}&block={$last_block}&gotopage={$last_gotopage}\" class=\"next-all\" ></a> ";
                    }
                }
            }
            // 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0) {
					$a_next_page .= "<a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\"  class=\"next\" ></a> ";
				} else {
					$a_next_page .= " <a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."'  class=\"next\" >></a> ";
				}
			} else {
				$a_next_page .= "<a href=\"javascript:;\"  class=\"next\" ></a> ";
			}

            $a_next_page .= $a_last_block;
        }else {
            $print_page = "<a class='on'>1</a>";
        }
		$this->a_prev_page = $a_prev_page?"<span class=\"border_wrap\">{$a_prev_page}</span>":"";		
		$this->a_next_page = $a_next_page?"<span class=\"border_wrap\">{$a_next_page}</span>":"";			
		$this->print_page = $print_page;

	}

	function getSql($sql) {
		global $setup;
		return $sql." LIMIT {$setup['list_num']} OFFSET ".($setup['list_num'] * ($this->gotopage - 1));
	}

}


# DECO&E 모바일용 productlist 페이징 2016-02-01 유동혁
# HOTT에 맞게 재수정(수정일 : 2016-10-13 작성자 : 김대엽) 
class New_Templet_mobile_paging {
	public $block;
	public $gotopage;
	public $nowblock;
	public $t_count;
	public $pagecount;
	public $total_block;
	public $a_prev_page;
	public $print_page;
	public $a_next_page;
	
	function __construct($sql_or_count,$page_num=10,$list_num=10,$link='GoPage',$class=false) {
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
		    $row       = pmysql_num_rows($result);
			$this->t_count = $row;
		    pmysql_free_result($result);
		}
		
		$this->pagecount = (($this->t_count - 1) / $setup['list_num']) + 1;
		
		if ($this->pagecount < 1) $this->pagecount = 1;

        $total_block = intval($this->pagecount / $setup['page_num']);

        if (($this->pagecount % $setup['page_num']) > 0) {
            $total_block += 1;
        }

         $total_block -= 1;

        if (ceil($this->t_count / $setup['list_num']) > 0 || true ) {
            // 이전    x개 출력하는 부분 - 시작
            $a_first_block = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0){
					$a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"prev-all\" ></a> ";
				} else {
					$a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"prev-all\" ></a> ";
				}
                $prev_page_exists = true;
            } else {
                if ( $this->gotopage == 1 ) {
                    $a_first_block .= "<a href=\"javascript:;\" class=\"prev-all\" ></a> ";
                } else {
                    if(strpos($link,'GoPage')===0){
                        $a_first_block .= "<a href=\"javascript:{$link}(0,1);\" class=\"prev-all\" ></a> ";
                    } else {
                        $a_first_block .= "<a href='{$link}&block=0&gotopage=1' class=\"prev-all\" ></a> ";
                    }
                }
            }

            $a_prev_page = "";
            if ($nowblock > 0) {
				if(strpos($link,'GoPage')===0) {
                	$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock - 1).",".($setup['page_num'] * ($block - 1) + $setup['page_num']).");\" ";
					$a_prev_page .= "class=\"prev\" ></a> ";
				} else {
					$a_prev_page .= "<a href='{$link}&block=".($nowblock-1)."&gotopage=".($setup['page_num']*($block-1)+$setup['page_num'])."' class=\"prev\" ></a> ";
				}
            } else {
                 if ( $this->gotopage == 1 ) {
                	$a_prev_page .= "<a href=\"javascript:;\" class=\"prev\" ></a> ";
            	} else {
            		if(strpos($link,'GoPage')===0){
            			$a_prev_page .= "<a href=\"javascript:{$link}(".($nowblock).",".($this->gotopage -1).");\" class=\"prev\" ></a> ";
            		} else {
            			$a_prev_page .= "<a href='{$link}&block=0&gotopage=1' class=\"prev\" ></a> ";
            		}
            	}
            }

            $a_prev_page = $a_first_block.$a_prev_page;
            // 일반 블럭에서의 페이지 표시부분 - 시작
            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                //echo "pagecount = ".$this->pagecount;
                //echo "page_num = ".$setup['page_num'];
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
					if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= "<a class='on'>".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
                    }else {
						if(strpos($link,'GoPage')===0) {
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" >";
							$print_page .= (intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						} else {
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' >";
							$print_page .= (intval($nowblock*$setup['page_num']) + $gopage)."</a> ";
						}
                    }
                }
            } else {
                if (($this->pagecount % $setup['page_num']) == 0) {
                    $lastpage = $setup['page_num'];
                } else {
                    $lastpage = $this->pagecount % $setup['page_num'];
                }
                for ($gopage = 1; $gopage <= $lastpage; $gopage++) {					
                    if (intval($nowblock * $setup['page_num']) + $gopage == intval($this->gotopage)) {
                        $print_page .= "<a class='on'>".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
                    } else {
						if(strpos($link,'GoPage')===0) {
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" >";
							$print_page .= (intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						} else {
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=".(intval($nowblock*$setup['page_num']) + $gopage)."' >";
							$print_page .= (intval($nowblock*$setup['page_num']) + $gopage)."</a> ";
						}
                    }
                }
            }        // 마지막 블럭에서의 표시부분 - 끝

            $a_last_block = "";
            $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
            $last_gotopage = ceil($this->t_count / $setup['list_num']);

            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0) {
					$a_last_block .= "<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" class=\"next-all\" ></a> ";
				} else {
					$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' class=\"next-all\" ></a> ";
				}
                $next_page_exists = true;
            } else {
                if ( $this->gotopage == $last_gotopage || $last_block < 0 ) {
                    $a_last_block .= "<a href=\"javascript:;\" class=\"next-all\" ></a> ";
                } else {
                    if(strpos($link,'GoPage')===0) {
                        $a_last_block .= "<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" class=\"next-all\" ></a> ";
                    } else {
                        $a_last_block .= "<a href=\"{$link}&block={$last_block}&gotopage={$last_gotopage}\" class=\"next-all\" ></a> ";
                    }
                }
            }
            // 다음 10개 처리부분...

            $a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				if(strpos($link,'GoPage')===0) {
					$a_next_page .= "<a href=\"javascript:{$link}(".($nowblock + 1).",".($setup['page_num'] * ($nowblock + 1) + 1).");\"  class=\"next\" ></a> ";
				} else {
					$a_next_page .= " <a href='{$link}&block=".($nowblock+1)."&gotopage=".($setup['page_num']*($nowblock+1)+1)."'  class=\"next\" >></a> ";
				}
			} else {
				$a_next_page .= "<a href=\"javascript:;\"  class=\"next\" ></a> ";
			}

            $a_next_page .= $a_last_block;
        }else {
            $print_page = "<a class='on'>1</a>";
        }
		$this->a_prev_page = $a_prev_page?"<span class=\"border_wrap\">{$a_prev_page}</span>":"";		
		$this->a_next_page = $a_next_page?"<span class=\"border_wrap\">{$a_next_page}</span>":"";			
		$this->print_page = $print_page;

	}
	
	function getSql($sql) {
		global $setup;
		return $sql." LIMIT {$setup['list_num']} OFFSET ".($setup['list_num'] * ($this->gotopage - 1));
	}
	
}
