<?
class PAGE{

	function setSearch($param){
		if(is_array($param)){foreach($param as $f=>$v){
			$this->$f = $v;
		}}
	}

	function setQuery(){
		global $db, $_REQUEST;
		
		if(!$this->page_no) $this->page_no = $_REQUEST[page_no]?$_REQUEST[page_no]:1;
		if(!$this->page_size) $this->page_size = 10;

		$query = $this->distinct?" select count(distinct ".$this->distinct.") ":" select count(*) ";
		$query.= " from ".implode(",",$this->table);
		if($this->where)$query.= " where ".implode(" and ",$this->where);
//		if($this->group)$query.= " group by ".implode(",",$this->group);
//		if($this->sort)$query.= " order by ".implode(",",$this->sort);
		
//		echo $query;

		list($this->list_total) = pmysql_fetch_array(pmysql_query($query,get_db_conn()));


		$this->query = " select ".implode(",",$this->field)." from ".implode(",",$this->table);
		if($this->where)$this->query.= " where ".implode(" and ",$this->where);
		if($this->group)$this->query.= " group by ".implode(",",$this->group);
		if($this->sort)$this->query.= " order by ".implode(",",$this->sort);
		
		
		$this->page_no = $this->page_no?$this->page_no:1;
		$this->list_size = $this->list_size?$this->list_size:20;
		$this->list_start = ( $this->page_no - 1 ) * $this->list_size; 

		$this->vnum = $this->list_total - $this->list_start;

		if(!$this->all){
			$this->query.= " limit ".$this->list_size." offset ".$this->list_start;
			$this->page_total = ceil($this->list_total / $this->list_size);
		}
		return $this->query;
	}

	function getPageNavi(){
		if($this->page_no){
			$this->page_start = floor(($this->page_no -1) / $this->page_size)*$this->page_size+1;
			$this->page_end = $this->page_start + $this->page_size ;
			
			if($this->page_start > 1 ){
				echo "<li><a href=\"javascript:GoPage(1);\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><<</a></li>";
				echo "<li><a href=\"javascript:GoPage(".($this->page_start - $this->page_size).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 10 페이지';return true\"><</a></li>";
			}

			for($i=$this->page_start; $i<=$this->page_total &&$i<$this->page_end; $i++){
				if($i==$this->page_no)	echo "<li class=\"this\"><a>".$i."</a></li>";
				else					echo "<li><a href=\"javascript:GoPage(".$i.");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".$i."';return true\">".$i."</a></li>";
			}
			if($this->page_end <= $this->page_total ){
				echo "<li><a href=\"javascript:GoPage(".($this->page_start + $this->page_size).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 10 페이지';return true\">></a></li>";
				echo "<li><a href=\"javascript:GoPage(".($this->page_total).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">>></a></li>";
			}
		}
	}
}

/*
		<div class="page_navi">
			<ul>
				<li><a href="javascript:GoPage(0,1);" onMouseOver="window.status='???吏 : 1';return true">1</a></li> <li><a href="javascript:GoPage(0,2);" onMouseOver="window.status='???吏 : 2';return true">2</a></li> <li><a href="javascript:GoPage(0,3);" onMouseOver="window.status='???吏 : 3';return true">3</a></li> <li><a href="javascript:GoPage(0,4);" onMouseOver="window.status='???吏 : 4';return true">4</a></li> <li><a href="javascript:GoPage(0,5);" onMouseOver="window.status='???吏 : 5';return true">5</a></li> <li class="this"><a>6</a></li>							</ul>
		</div>
*/