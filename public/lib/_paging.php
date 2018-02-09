<?php
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
                $a_first_block .= "<a href=\"javascript:;\" class=\"prev-all\" ></a> ";
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
                $a_prev_page .= "<a href=\"javascript:;\" class=\"prev\" ></a> ";
            }

            $a_prev_page = $a_first_block.$a_prev_page;
			
            // 일반 블럭에서의 페이지 표시부분 - 시작

            if (intval($total_block) <> intval($nowblock)) {
                $print_page = "";
                for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
                    if ((intval($nowblock * $setup['page_num']) + $gopage) == intval($this->gotopage)) {
                        $print_page .= "<a class=\"on\">".(intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
                    }else {
						if(strpos($link,'GoPage')===0) {
                        	$print_page .= "<a href=\"javascript:{$link}({$nowblock},".(intval($nowblock * $setup['page_num']) + $gopage).");\" >";
							$print_page .= (intval($nowblock * $setup['page_num']) + $gopage)."</a> ";
						} else {
							$print_page .= "<a href='{$link}&block={$nowblock}&gotopage=". (intval($nowblock*$setup['page_num']) + $gopage)."' >";
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
            if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
                $last_block    = ceil($this->t_count / ($setup['list_num'] * $setup['page_num'])) - 1;
                $last_gotopage = ceil($this->t_count / $setup['list_num']);

				if(strpos($link,'GoPage')===0) {
					$a_last_block .= "<a href=\"javascript:{$link}({$last_block},{$last_gotopage});\" class=\"next-all\" ></a> ";
				} else {
					$a_last_block .= " <a href='{$link}&block={$last_block}&gotopage={$last_gotopage}' class=\"next-all\" ></a> ";
				}
                $next_page_exists = true;
            } else {
                $a_last_block .= "<a href=\"javascript:;\" class=\"next-all\" ></a> ";
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
	            $a_next_page .= "<a href=\"javascript:;\"  class=\"next\" ></a> ";
            }

            $a_next_page .= $a_last_block;
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
