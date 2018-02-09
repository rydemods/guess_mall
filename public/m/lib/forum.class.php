<?php
//0920 원재
class FORUM
{
	public $write_form = null;
	public $recent_forum = null;
	public $recommend_forum = null;
	public $forum_list = null;
	public $forum_list_notice = null;
	public $forum_list_request = null;
	public $cate_A = null;
	public $cate_B = null;
	public $cate_C = null;
	public $forum_detail = null;
	public $reply_list;
	public $return_status = null;
	
	protected $limit_cookie = 10; //최근 본 포럼 리스트 숫자 제한

	//protected $imagepath = "../".DataDir."shopimages/forum/";

	public function __construct($mode)
	{

		if($mode =='myforum_list'){
			$this->my_forum_list();
		}
		
		if($mode == "write_form" || $mode=="modify_form"){ //포럼 글 쓰기or수정 폼 세팅
			$this->set_write_form($mode);
		}

		if($mode=='write'){
			$this->forum_write();
		}

		if($mode=="delete"){
			$this->forum_delete();
		}

		if($mode =="view"){//포럼 글 상세 보기
			$this->forum_view();
		}

		if($mode =="view_request"){//포럼 글 상세 보기
			$this->category_list_main();
			$this->forum_view_request();
		}

		if($mode =="main" || $mode=="list" || $mode=="list_request"){//리스트 또는 메인 호출시

			$this->category_list_main();

			if($mode=="main"){//최근 방문 포럼, 추천 포럼
				$this->recent_forum_list();
				$this->recommend_forum();
			}

			if($mode=="list"){//포럼 글 리스트
				$this->forum_list();
			}

			if($mode=="list_request"){//포럼 글 리스트
				$this->forum_list_request();
			}
		}

		if($mode == "write_reply"){
			$this->write_reply();
		}

		if($mode == "write_reply_request"){
			$this->write_reply_request();
		}

		if($mode == "delete_reply"){
			$this->delete_reply();
		}

		if($mode == "delete_reply_request"){
			$this->delete_reply_request();
		}

		if($mode == "request_write_form"){
			$this->set_write_form_request();
			$this->set_request_form();
		}

		if($mode == "request_write"){
			$this->request_write();
		}

		if($mode=="delete_request"){
			$this->forum_delete_request();
		}
	}

	public function recent_forum_list()//최근 방문 포럼리스트 가져오기 ㅠㅠ
	{
		if($_COOKIE['recent_forum']){
			$recent_forum_arr = (array) json_decode($_COOKIE['recent_forum']);
			$recent_forum_code = array();

			foreach($recent_forum_arr as $key=>$val){
				if($key < $this->limit_cookie ){
					$recent_forum_code[] = $val;
				}
			}

			$qry = implode( "','" , $recent_forum_code);
			$sql = " select code_name, code_a, code_b, code_c from tblforumcode ";
			$sql .= " where code_a || code_b || code_c in ('{$qry}') ";
			$sql .= " order by FIELD ( code_a || code_b || code_c , '{$qry}' )";
			$result = pmysql_query($sql);
			$return_data = array();
			while( $row = pmysql_fetch_object($result) ){
				$return_data[] = $row;
			}
			$this->recent_forum = $return_data;
		}
	}

	public function category_list_main()//메인 화면에 뿌려줄 포럼(카테고리)리스트
	{
		$sql = " select code.*, ";
		$sql .= " (select count(*) from tblforumlist where code = code.code_a||code.code_b||code.code_c ) as l_count  "; 
		$sql .= " from tblforumcode code where group_code !='N' ";
		$sql .= " order by code_a asc , cate_sort asc ";
		$result = pmysql_query($sql);
		$cate_list = array();
		while( $row = pmysql_fetch_object($result) ){
			if($row->code_b == '000'){
				$cate_A[] = $row; 
			}else if($row->code_c == '000'){
				$cate_B[$row->code_a][] = $row;
			}else{
				$cate_C[$row->code_a.$row->code_b][] = $row;
			}
		}
		$this->cate_A = $cate_A;
		$this->cate_B = $cate_B;
		$this->cate_C = $cate_C;
	}

	function recommend_forum()
	{
		$list = array();
		$sql = "
		select code.code_name,list.* ,
		(select count(*)from tblforumreply where list_no = list.index AND degree=1 ) as re
		from tblforumlist list
		join tblforumcode code
		on list.code = code.code_a||code.code_b||code.code_c 
		";
		$sql .= " where code.title_type ='Y' order by code ";
		$result = pmysql_query($sql);
		while( $row = pmysql_fetch_object($result) ){
			$row->content  = preg_replace("(\<(/?[^\>]+)\>)", "", $row->content);
			$list[$row->code][] = $row;
		}
		$this->recommend_forum = $list;
	}

	function set_write_form()//글쓰기, 글수정을 위한 기본세팅
	{
		$forum_code = $_REQUEST['code'];
		$type = $_REQUEST['type'];
		if(!$forum_code || !$type){
			echo "<script>alert('잘못된 요청입니다');location.href='/front/forum_main.php';</script>";
			exit;
		}
		$this->write_form['forum_code'] = $forum_code;
		$this->write_form['type'] = $type;
		##포럼 정보 조회
		$f_sql = " select * from tblforumcode where code_a||code_b||code_c = '{$forum_code}' ";
		$this->write_form['forum_info'] = pmysql_fetch_object( pmysql_query($f_sql) );

		##글 수정일 경우, 글 내용 및 정보 조회
		if($type=="modify"){
			$forum_index = $_REQUEST['index'];
			$view_sql = " select * from tblforumlist where index = {$forum_index} ";
			$view_data = pmysql_fetch_object( pmysql_query($view_sql) );
			$this->write_form['view'] = pmysql_fetch_object( pmysql_query($view_sql) );
		}
	}

	function set_write_form_request()
	{
		$this->write_form['type'] = $type;
		##글 수정일 경우, 글 내용 및 정보 조회
		$forum_index = $_REQUEST['index'];
		$view_sql = " select * from tblforumlist_request where index = {$forum_index} ";
		$view_data = pmysql_fetch_object( pmysql_query($view_sql) );
		$this->write_form['view'] = pmysql_fetch_object( pmysql_query($view_sql) );
	
	}

	function forum_write()//포럼 글 쓰기 or 수정
	{
		$Dir ="../";
		global $_ShopInfo;
		include_once($Dir."lib/file.class.php");

		$type = $_POST['type'];
		$callback = $_POST['callback'];

		$imagepath = $Dir.DataDir."shopimages/forum/";
		$imagefile = new FILE($imagepath);
		$up_imagefile = $imagefile->upFiles();
		$v_forum_file	    = $_POST["v_forum_file"];

		$forum_code = $_POST['forum_code'];
		$forum_index = $_POST['forum_index'];
		$id = $_ShopInfo->memid;
		$summary = $_POST["summary"];
		$title	            = pg_escape_string($_POST["title"]);
		$content	        = pg_escape_string($_POST["content"]);
		$hash_tags          = pg_escape_string($_POST["hash_tags"]);

        // 관리자가 수기로 작성자 및 날짜 변경 입력했을 경우..2016-10-24
        if($_POST[modi_id]) $id = trim($_POST[modi_id]);
        if($_POST[modi_date]) $modi_date = date("Y-m-d H:i:s", strtotime($_POST[modi_date]));
        else $modi_date = "";

		if($type=='write'){
			$w_sql = " insert into tblforumlist (code, id, name, tag, summary, title, img, content, writetime) values ( ";
			$w_sql .= " '{$forum_code}', ";
			$w_sql .= " '{$id}', ";
			$w_sql .= " '{$name}',";
			$w_sql .= " '{$hash_tags}', ";
			$w_sql .= " '{$summary}', ";
			$w_sql .= " '{$title}', ";
			$w_sql .= " '{$up_imagefile['forum_file'][0]['v_file']}', ";
			$w_sql .= " '{$content}', ";
			#$w_sql .= " now() ";
            if($modi_date) {
                $w_sql .= " '{$modi_date}' ";
            } else {
                $w_sql .= " now() ";
            }
			$w_sql .= " ) returning index";
			//pmysql_query($w_sql);
		
			try {
				//BeginTrans(); //오류남. 계속 롤백됨. 이유를 모르겠음
				$result = pmysql_fetch_object( pmysql_query($w_sql) );
				if( pmysql_error() ){
					throw new Exception( "등록실패" );
					break;
				}else{
					callNaver('forum', $result->index, 'reg');
					if($callback=='m'){
						echo "<script>alert('등록되었습니다');location.href='/m/forum_list.php?forum_code={$forum_code}';</script>";
						exit;
					}else{
						echo "<script>alert('등록되었습니다');location.href='/front/forum_list.php?forum_code={$forum_code}';</script>";
						exit;
					}
				}

			} catch( Exception $e ) {
				//RollbackTrans(); 왜 안되는거야;;
				$msg = $e->getMessage();
				echo "<script>alert('{$msg}');location.href='/front/forum_list.php?forum_code={$forum_code}';</script>";
				exit;
			}
		
		}//write end

		if($type=='modify'){
			$m_sql = " update tblforumlist set ";
			if( strlen( $up_imagefile["forum_file"][0]["v_file"] ) > 0 ){
				$m_sql .= " img = '{$up_imagefile['forum_file'][0]['v_file']}' , " ;
			}

			$chk_notice = $_REQUEST['chk_notice'] ? : 'N';

            // 관리자가 수기로 작성자 및 날짜 변경 입력했을 경우..2016-10-24
            $sub_sql = "";
            if($modi_date) $sub_sql = ", writetime = '".$modi_date."'";
			/*
			$m_sql .= " 
				code = '{$forum_code}', 
				id = '{$id}', 
				name = '{$name}', 
				tag = '{$hash_tags}', 
				title = '{$title}', 
				content = '{$content}', 
				summary = '{$summary}', 
				modifytime = now() 
			";
			*/
			$m_sql .= " 
				id = '{$id}', 
				name = '{$name}', 
				tag = '{$hash_tags}', 
				title = '{$title}', 
				content = '{$content}', 
				summary = '{$summary}', 
				notice = '{$chk_notice}',
				modifytime = now() 
                ".$sub_sql." 
			";
			$m_sql .= " where index = {$forum_index} ";
			

			try {
				//BeginTrans(); //오류남. 계속 롤백됨. 이유를 모르겠음
				pmysql_query($m_sql);
				if( pmysql_error() ){
					throw new Exception( "수정실패" );
					break;
				}else{
					callNaver('forum', $forum_index, 'modi');
					//새로 등록된 파일 있으면 기존 파일 삭제
					if( strlen( $up_imagefile["forum_file"][0]["v_file"] ) > 0 ){
						if( is_file( $imagepath.$v_forum_file[0] ) > 0 ){
							$imagefile->removeFile( $v_forum_file[0] );
						}
					}
					if($callback=='admin'){
						echo "<script>alert('수정되었습니다');opener.location.reload();window.close();</script>";
						exit;
					}else if($callback=='m'){
						echo "<script>alert('수정되었습니다');location.href='/m/forum_list.php?forum_code={$forum_code}';</script>";
						exit;
					}else{
						echo "<script>alert('수정되었습니다');location.href='/front/forum_list.php?forum_code={$forum_code}';</script>";
						exit;
					}
				}

			} catch( Exception $e ) {
				//RollbackTrans(); 왜 안되는거야;;
				$msg = $e->getMessage();
				echo "<script>alert('{$msg}');location.href='/front/forum_list.php?forum_code={$forum_code}';</script>";
				exit;
			}
		
		}//modify end

	}

	function request_write()//포럼 글 쓰기 or 수정
	{

		global $_ShopInfo;

		$type = $_POST['type'];
		$callback = $_POST['callback'];
		$forum_index = $_POST['forum_index'];
		$code_a = $_POST['code_a'];
		$code_b = $_POST['code_b'];
		$code_c = $_POST['code_c'];
		$custom_cate = $_POST['custom_cate'];
		if($code_b =="custom@#"){
			$code_b = $custom_cate;
		}
		$id = $_ShopInfo->memid;
		$name = $_POST['name'];
		$title = $_POST['title'];
		$content	        = pg_escape_string($_POST["content"]);

		if($type=='write'){
			$w_sql = " insert into tblforumlist_request (code_a, code_b, code_c, id, name, title,content, writetime) values ( ";
			$w_sql .= " '{$code_a}', ";
			$w_sql .= " '{$code_b}', ";
			$w_sql .= " '{$code_c}', ";
			$w_sql .= " '{$id}', ";
			$w_sql .= " '{$name}',";
			$w_sql .= " '{$title}', ";
			$w_sql .= " '{$content}', ";
			$w_sql .= " now() ";
			$w_sql .= " ) ";
		
			try {
				//BeginTrans(); //오류남. 계속 롤백됨. 이유를 모르겠음
				pmysql_query($w_sql);
				if( pmysql_error() ){
					throw new Exception( "등록실패" );
					break;
				}else{
					if($callback =='m'){
						echo "<script>alert('등록되었습니다');location.href='/m/forum_apply_list.php';</script>";
						exit;
					}else{
						echo "<script>alert('등록되었습니다');location.href='/front/forum_request_list.php';</script>";
						exit;
					}
				}

			} catch( Exception $e ) {
				//RollbackTrans(); 왜 안되는거야;;
				$msg = $e->getMessage();
				echo "<script>alert('{$msg}');location.href='/front/forum_request_list.php';</script>";
				exit;
			}
		
		}//write end

		if($type=='modify'){
			$m_sql = " update tblforumlist_request set ";
		
			$m_sql .= " 
				code_a = '{$code_a}', 
				code_b = '{$code_b}', 
				code_c = '{$code_c}', 
				id = '{$id}', 
				name = '{$name}', 
				title = '{$title}', 
				content = '{$content}', 
				modifytime = now() 
			";
			$m_sql .= " where index = {$forum_index} ";

			try {
				//BeginTrans(); //오류남. 계속 롤백됨. 이유를 모르겠음
				pmysql_query($m_sql);
				if( pmysql_error() ){
					throw new Exception( "수정실패" );
					break;
				}else{
					if($callback=='admin'){
						echo "<script>alert('수정되었습니다');opener.location.reload();window.close();</script>";
						exit;
					}else if($callback =='m'){
						echo "<script>alert('수정되었습니다');location.href='/m/forum_apply_list.php';</script>";
						exit;
					}else{
						echo "<script>alert('수정되었습니다');location.href='/front/forum_request_list.php;</script>";
						exit;
					}
				}

			} catch( Exception $e ) {
				//RollbackTrans(); 왜 안되는거야;;
				$msg = $e->getMessage();
				echo $m_sql;
				//echo "<script>alert('{$msg}');location.href='/front/forum_list.php?forum_code={$forum_code}';</script>";
				exit;
			}
		
		}//modify end

	}


	function forum_delete()
	{
		$forum_index = $_POST['forum_index'];
		$sql = " delete from tblforumlist where index = {$forum_index} ";
		try {
			//
			pmysql_query($sql);
			if( pmysql_error() ){
				throw new Exception( "fail" );
				break;
			}
		} catch( Exception $e) {
			$this->return_status = "F";
		}
		callNaver('forum', $forum_index, 'del'); 
		$this->return_status="S";
	}

	function forum_delete_request()
	{
		$forum_index = $_POST['forum_index'];
		$sql = " delete from tblforumlist_request where index = {$forum_index} ";
		try {
			//
			pmysql_query($sql);
			if( pmysql_error() ){
				throw new Exception( "fail" );
				break;
			}
		} catch( Exception $e) {
			$this->return_status = "F";
		}
		$this->return_status="S";
	}


	function up_view_count($forum_index)//포럼 글 카운트 증가
	{
		$sql = " update tblforumlist set view = view +1 where index = {$forum_index} ";
		$result = pmysql_query($sql);
	}

	function up_view_count_request($forum_index)//포럼 글 카운트 증가
	{
		$sql = " update tblforumlist_request set view = view +1 where index = {$forum_index} ";
		$result = pmysql_query($sql);
	}

	function forum_view()//포럼글 내용 보기
	{
		global $_ShopInfo;
		$id = $_ShopInfo->memid;
		$imagepath = $Dir.DataDir."shopimages/forum/";
		$forum_index = $_REQUEST['index'];
		$this->up_view_count($forum_index);

		$sql = " select code.code_name, list.* , mem.nickname, ";
		if($id){
			$sql .= " 	(select like_id from tblhott_like where section='forum_list' AND hott_code = '{$forum_index}' AND like_id = '{$id}' ) as chk_like , ";
		}
		$sql .="
			(select count(*) from tblhott_like where section='forum_list' AND hott_code = '{$forum_index}' ) as like
			from tblforumlist list
			join tblforumcode code
			on list.code = code.code_a||code.code_b||code.code_c
			left outer join tblmember mem
			on list.id = mem.id
		";
		$sql .= " where list.index = {$forum_index} ";
		$result = pmysql_query($sql);
		$row = pmysql_fetch_object($result);
		$row->tag2 = explode("," , $row->tag);
		$row->w_date = date("Y-m-d H:i:s", strtotime($row->writetime) );


		if($row->chk_like){
			$row->chk_like= "on";
		}else{
			$row->chk_like= "";
		}

		if($row->nickname){
			$row->w_name = $row->nickname;
		}else{
			$row->w_name = substr($row->id, 0,3)."*****";
		}

        //이전글 ( 더 최신글) 2016-11-17
        $sql = "select  index, title from tblforumlist 
                where   code = '".$row->code."' and notice = 'N' and to_char(writetime, 'YYYYMMDDHH24MISS') > '".date("YmdHis", strtotime($row->writetime) )."'
                Order by writetime asc 
                limit 1
                ";
        list($row->prev_idx, $row->prev_title) = pmysql_fetch($sql);

        //다음을 ( 더 예전글) 2016-11-17
        $sql = "select  index, title from tblforumlist 
                where   code = '".$row->code."' and notice = 'N' and to_char(writetime, 'YYYYMMDDHH24MISS') < '".date("YmdHis", strtotime($row->writetime) )."'
                Order by writetime desc 
                limit 1
                ";
        list($row->next_idx, $row->next_title) = pmysql_fetch($sql);


		$this->forum_detail = $row;

		##리플들 가져오기
		$r_list = array();
		$r_sql = " select list.*, ";
		if($id){
			$r_sql .= " 
				(select count(member_id) from tblgood_feeling where section ='forum_reply' AND feeling_type ='good' AND code='forum_reply_'||list.index AND list.id='{$id}' ) as chk_good ,
				(select count(member_id) from tblgood_feeling where section ='forum_reply' AND feeling_type ='bad' AND code = 'forum_reply_'||list.index AND list.id='{$id}' ) as chk_bad, 
			";
		}
		$r_sql .="
			(select count(feeling_type) from tblgood_feeling where section ='forum_reply' AND feeling_type ='good' AND code = 'forum_reply_'||list.index ) as good_count ,
			(select count(feeling_type) from tblgood_feeling where section ='forum_reply' AND feeling_type ='bad' AND code = 'forum_reply_'||list.index ) as bad_count ,
			mem.nickname, mem.icon
			from tblforumreply list 
			join tblmember mem
			on list.id = mem.id 
			where list.list_no = {$row->index} 
			order by list.degree asc, list.sort asc 
		";

		$r_result = pmysql_query($r_sql);
		$imagepath = $Dir.DataDir."shopimages/member_icon/";
		while( $r_row = pmysql_fetch_object($r_result) ){
			$r_row->writetime = date("Y-m-d", strtotime($r_row->writetime) );
			
			if($r_row->icon){
				$r_row->icon = $imagepath.$r_row->icon;
			}
			$r_row->id2 = $r_row->id;
			if($r_row->nickname){
				$r_row->id = $r_row->nickname;
			}else{
				$r_row->id = substr($r_row->id, 0,3)."*****";
			}
			

			if($id){
				if($r_row->chk_good){
					$r_row->chk_good ="on";
				}
				if($r_row->chk_bad){
					$r_row->chk_bad ="on";
				}
			}
			if($r_row->degree >1 ){//대댓글 조립
				$r_list[$r_row->degree][$r_row->reply_no][] = $r_row;
			}else{//일반 댓글 조립
				$r_list['1'][] = $r_row;
			}
		}
		$this->reply_list = $r_list;
	}

	function my_forum_list()
	{
		global $_ShopInfo;
		$list = array();
		$r_data = array();

		$type = $_REQUEST['type'] ? : 'list';

		$s_year=(int)$_POST["s_year"];
		$s_month=(int)$_POST["s_month"];
		$s_day=(int)$_POST["s_day"];

		$e_year=(int)$_POST["e_year"];
		$e_month=(int)$_POST["e_month"];
		$e_day=(int)$_POST["e_day"];

		if($e_year==0) $e_year=(int)date("Y");
		if($e_month==0) $e_month=(int)date("m");
		if($e_day==0) $e_day=(int)date("d");

		if($s_year==0) $s_year=(int)date("Y",$stime);
		if($s_month==0) $s_month=(int)date("m",$stime);
		if($s_day==0) $s_day=(int)date("d",$stime);

		$strDate1 = date("Y-m-d",strtotime("$s_year-$s_month-$s_day -1day"));
		$strDate2 = date("Y-m-d",strtotime("$e_year-$e_month-$e_day +1day"));
		//debug($strDate1);
		$search_qry =" 
			AND list.writetime > date('{$strDate1}') AND list.writetime < date('{$strDate2}')
		";

		if($type =='list'){
			$sql = "
			select code.code_name,list.* ,
			(select count(*)from tblforumreply where list_no = list.index AND degree=1 ) as re,
			(select count(*) from tblhott_like where section='forum_list' AND hott_code = list.index::varchar ) as like
			from tblforumlist list
			join tblforumcode code
			on list.code = code.code_a||code.code_b||code.code_c 
			";
			$sql .= " where 1=1 ";
			$sql .= " AND id = '{$_ShopInfo->memid}' ";
			$sql .= $search_qry;
			$sql .= " order by list.index desc ";
		}else{
			$sql ="
				select max(code.code_name) as code_name,
				list.title as title,
				max(list.id) as id,
				max(list.writetime) as writetime,
				max(list.view) as view,
				max(list.index) as index,
				(select count(*) from tblhott_like where section='forum_list' AND hott_code = max(list.index::varchar) ) as like ,
				(select count(*)from tblforumreply where list_no = max(list.index) AND degree=1 ) as re,
				( select nickname from tblmember where id= max(list.id) ) as nickname
				from tblforumlist list
				join tblforumcode code
				on list.code = code.code_a||code.code_b||code.code_c 
				join tblforumreply r
				on list.index = r.list_no
			";
			$sql .= " where 1=1 ";
			$sql .= " AND r.id = '{$_ShopInfo->memid}' ";
			$sql .= $search_qry;
			$sql .= " group by title ";
		}

		$listnum = 10; //한 페이지에 나타날 리스트 갯수
		$pagenum = 10; //한 리스트에 나타날 페이징 개수
		$sql_paging = $this->get_paging($sql, $listnum,$pagenum );//페이징 생성8ㅅ8
		
		$result = pmysql_query($sql_paging[0]);
		if( !pmysql_error() ){ 
			
		}
		$i = 0;
		while( $row = pmysql_fetch_object($result) ){
			$number = ( $sql_paging[1]->t_count - ( $listnum  * ($sql_paging[1]->gotopage-1))-$i); //리스트 번호 구하기
			$row->number = $number;
			if($row->nickname){
				$row->id = $row->nickname;
			}else{
				$row->id = substr($row->id, 0,3)."*****";
			}
			$row->w_time = date("Y-m-d", strtotime($row->writetime) );
			$list[] = $row;
			$i++; //글 번호를 주기 위함
		}
		$r_data['forum_name'] = $list[0]->code_name;
		$r_data['list'] = $list;
		$r_data['paging'] = $sql_paging[1];
		$r_data['forum_code'] = $forum_code;
		$r_data['type'] = $type;
		$this->forum_list = $r_data;
	}



	function forum_list()
	{
		$forum_code = $_REQUEST['forum_code'];
		$search_type = $_REQUEST['search_type'] ?  : 'title';
		$search_word = $_REQUEST['search_word'] ? : null;
		
		$search_word = strtolower($search_word);
		$search_word = str_replace("'", "''", $search_word);

		$list = array();
		$r_data = array();

		if($search_word){//검색 타입에 따른 검색 유형 설정 ㅠㅠ
			$search_type_list = array(
				"title"=>"AND lower( list.title ) like '%".$search_word."%'",
				"content"=>"AND lower( list.content ) like '%".$search_word."%'",
				"title_content"=>"AND(  lower( list.content ) like  '%".$search_word."%' or lower( list.title ) like  '%".$search_word."%' )",
				"id"=>"AND lower( list.id )like '%".$search_word."%'",
				"name"=>"AND lower( list.name ) like '%".$search_word."%'",
			);
			$search_qry = $search_type_list[$search_type];
		}
	
		$sql = "
		select code.code_name,list.* , mem.icon, mem.nickname ,
		(select count(*)from tblforumreply where list_no = list.index AND degree=1 ) as re,
		(select count(*) from tblhott_like where section='forum_list' AND hott_code = list.index::varchar ) as like
		from tblforumlist list
		join tblforumcode code on list.code = code.code_a||code.code_b||code.code_c 
		left join tblmember mem on list.id = mem.id
		";
		$sql .= " where list.code = '{$forum_code}' AND list.notice != 'Y' ";
		$sql .= $search_qry;
		//$sql .= " order by list.index desc ";
        $sql .= " order by list.writetime desc ";
        //exdebug($sql);
		$listnum = 10; //한 페이지에 나타날 리스트 갯수
		$pagenum = 10; //한 리스트에 나타날 페이징 개수
		$sql_paging = $this->get_paging($sql, $listnum,$pagenum );//페이징 생성8ㅅ8
		$sql_paging[0] = str_replace("AND list.notice != 'Y'" ," ", $sql_paging[0]); //페이징 된 쿼리에서 notice조건 해제해줌
		$result = pmysql_query($sql_paging[0]);
		if( !pmysql_error() ){ 
			//포럼 조회시, 최근 방문 포럼 세팅 ,일단 쿠키는 1주일로 생성
			if( $_COOKIE['recent_forum'] ){
				$recent_forum_arr = (array) json_decode($_COOKIE['recent_forum']);
				$chk_distinct = 0;
				foreach($recent_forum_arr as $key=>$chk_val){
					if( ($chk_val == $forum_code) || ($key +1 > $this->limit_cookie) ){
						unset($recent_forum_arr[$key]);
					}
				}
				array_unshift ($recent_forum_arr,$forum_code);
				$recent_forum_data = json_encode($recent_forum_arr);
				setcookie('recent_forum', '', time()-3600 , '/');
				setcookie('recent_forum', $recent_forum_data, time()+(3600*24*7) , '/');
			}else{
				$recent_forum_arr = array();
				array_push($recent_forum_arr,$forum_code);
				$recent_forum_data = json_encode($recent_forum_arr);
				setcookie('recent_forum', '', time()-3600 , '/');
				setcookie('recent_forum', $recent_forum_data, time()+(3600*24*7) , '/'); 
			}
		}
		$i = 0;
		
	
		while( $row = pmysql_fetch_object($result) ){
			if($row->notice =='N'){
				$number = ( $sql_paging[1]->t_count - ( $listnum  * ($sql_paging[1]->gotopage-1))-$i); //리스트 번호 구하기
				if($row->nickname){
					$row->id = $row->nickname;
				}else{
					$row->id = substr($row->id, 0,3)."*****";
				}
				$row->number = $number;
				$row->title = "[".$row->summary."]".$row->title;
				$row->w_time = date("Y-m-d", strtotime($row->writetime) );
				$list[] = $row;
				$i++; //글 번호를 주기 위함
			}else{
				$notice_list[] = $row;
			}
		}
		$r_data['forum_name'] = $list[0]->code_name;
		$r_data['list'] = $list;
		$r_data['paging'] = $sql_paging[1];
		$r_data['forum_code'] = $forum_code;
		$r_data['search_type'] = $search_type;
		$r_data['search_word'] = $search_word;
		$this->forum_list = $r_data;
		$this->forum_list_notice = $notice_list;
	}

	function forum_list_request()
	{
		$search_type = $_REQUEST['search_type'] ?  : 'title';
		$search_word = $_REQUEST['search_word'] ? : null;
		$search_word = strtolower($search_word);
		$search_word = str_replace("'", "''", $search_word);

		$list = array();
		$r_data = array();

		if($search_word){//검색 타입에 따른 검색 유형 설정 ㅠㅠ
			$search_type_list = array(
				"title"=>"AND lower( list.title ) like '%".$search_word."%'",
				"content"=>"AND lower( list.content ) like '%".$search_word."%'",
				"title_content"=>"AND(  lower( list.content ) like  '%".$search_word."%' or lower( list.title ) like  '%".$search_word."%' )",
				"id"=>"AND lower( list.id )like '%".$search_word."%'",
				"name"=>"AND lower( list.name ) like '%".$search_word."%'",
			);
			$search_qry = $search_type_list[$search_type];
		}
	
		$sql = "
		select list.* ,mem.icon, mem.nickname ,
		(select count(*)from tblforumreply_request where list_no = list.index AND degree=1 ) as re
		from tblforumlist_request list
		join tblmember mem on list.id = mem.id
		where 1=1 
		";
		$sql .= $search_qry;
		//$sql .= " order by list.index desc ";
        $sql .= " order by list.writetime desc ";
        //exdebug($sql);
		$listnum = 10; //한 페이지에 나타날 리스트 갯수
		$pagenum = 10; //한 리스트에 나타날 페이징 개수
		$sql_paging = $this->get_paging($sql, $listnum,$pagenum );//페이징 생성8ㅅ8
		
		$result = pmysql_query($sql_paging[0]);
		if( !pmysql_error() ){ 
			
			
		}
		$i = 0;
		
		while( $row = pmysql_fetch_object($result) ){
			$number = ( $sql_paging[1]->t_count - ( $listnum  * ($sql_paging[1]->gotopage-1))-$i); //리스트 번호 구하기
			if($row->nickname){
				$row->id = $row->nickname;
			}else{
				$row->id = substr($row->id, 0,3)."*****";
			}
			$row->number = $number;
			$row->w_time = date("Y-m-d", strtotime($row->writetime) );
			$list[] = $row;
			$i++; //글 번호를 주기 위함
		}
		$r_data['forum_name'] = $list[0]->code_name;
		$r_data['list'] = $list;
		$r_data['paging'] = $sql_paging[1];
		$r_data['forum_code'] = $forum_code;
		$r_data['search_type'] = $search_type;
		$r_data['search_word'] = $search_word;
		$this->forum_list_request = $r_data;
	}


	function forum_view_request()//포럼글 내용 보기
	{
		global $_ShopInfo;
		$id = $_ShopInfo->memid;
		$imagepath = $Dir.DataDir."shopimages/forum/";
		$forum_index = $_REQUEST['index'];
		$this->up_view_count_request($forum_index);

		$sql = " select list.* , mem.nickname, ";
		if($id){
			$sql .= " 	(select like_id from tblhott_like where section='forum_list_request' AND hott_code = '{$forum_index}' AND like_id = '{$id}' ) as chk_like , ";
		}
		$sql .="
			(select count(*) from tblhott_like where section='forum_list_request' AND hott_code = '{$forum_index}' ) as like
			from tblforumlist_request list
			join tblmember mem on list.id = mem.id 
		";
		$sql .= " where list.index = {$forum_index} ";
		$result = pmysql_query($sql);
		$row = pmysql_fetch_object($result);
		$row->tag2 = explode("," , $row->tag);
		$row->w_date = date("Y-m-d h:i:s", strtotime($row->writetime) );

		if($row->chk_like){
			$row->chk_like= "on";
		}else{
			$row->chk_like= "";
		}

		if($row->nickname){
			$row->w_name = $row->nickname;
		}else{
			$row->w_name = substr($row->id, 0,3)."*****";
		}

        //이전글 ( 더 최신글) 2016-11-18
        $sql = "select  list.index, list.title 
                from    tblforumlist_request list
                join    tblmember mem on list.id = mem.id
                where   to_char(list.writetime, 'YYYYMMDDHH24MISS') > '".date("YmdHis", strtotime($row->writetime) )."'
                Order by list.writetime asc 
                limit 1
                ";
        list($row->prev_idx, $row->prev_title) = pmysql_fetch($sql);

        //다음을 ( 더 예전글) 2016-11-18
        $sql = "select  list.index, list.title 
                from    tblforumlist_request list
                join    tblmember mem on list.id = mem.id
                where   to_char(list.writetime, 'YYYYMMDDHH24MISS') < '".date("YmdHis", strtotime($row->writetime) )."'
                Order by list.writetime desc 
                limit 1
                ";
        list($row->next_idx, $row->next_title) = pmysql_fetch($sql);

		$this->forum_detail = $row;

		##리플들 가져오기
		$r_list = array();
		$r_sql = " select list.*, ";
		if($id){
			$r_sql .= " 
				(select count(member_id) from tblgood_feeling where section ='forum_reply_request' AND feeling_type ='good' AND code='forum_reply_'||list.index AND list.id='{$id}' ) as chk_good ,
				(select count(member_id) from tblgood_feeling where section ='forum_reply_request' AND feeling_type ='bad' AND code = 'forum_reply_'||list.index AND list.id='{$id}' ) as chk_bad, 
			";
		}
		$r_sql .="
			(select count(feeling_type) from tblgood_feeling where section ='forum_reply_request' AND feeling_type ='good' AND code = 'forum_reply_request_'||list.index ) as good_count ,
			(select count(feeling_type) from tblgood_feeling where section ='forum_reply_request' AND feeling_type ='bad' AND code = 'forum_reply_request_'||list.index ) as bad_count,
			mem.nickname, mem.icon
			from tblforumreply_request list
			join tblmember mem on list.id = mem.id 
			where list.list_no = {$row->index} 
			order by list.degree asc, list.sort asc 
		";

		$r_result = pmysql_query($r_sql);
		$imagepath = $Dir.DataDir."shopimages/member_icon/";
		while( $r_row = pmysql_fetch_object($r_result) ){
			$r_row->writetime = date("Y-m-d", strtotime($r_row->writetime) );
			if($r_row->icon){
				$r_row->icon = $imagepath.$r_row->icon;
			}
			$r_row->id2 = $r_row->id;
			if($r_row->nickname){
				$r_row->id = $r_row->nickname;
			}else{
				$r_row->id = substr($r_row->id, 0,3)."*****";
			}
			
			if($id){
				if($r_row->chk_good){
					$r_row->chk_good ="on";
				}
				if($r_row->chk_bad){
					$r_row->chk_bad ="on";
				}
			}
			if($r_row->degree >1 ){//대댓글 조립
				$r_list[$r_row->degree][$r_row->reply_no][] = $r_row;
			}else{//일반 댓글 조립
				$r_list['1'][] = $r_row;
			}
		}
		$this->reply_list = $r_list;
	}

	public function get_paging($sql,$listnum,$pagenum)//페이징 함수
	{
		$paging = new New_Templet_paging($sql,$pagenum,$listnum,'GoPage',true);
		$sql = $paging->getSql($sql);
		$r_data[0] = $sql;
		$r_data[1] = $paging;
		return $r_data;
	}

	function write_reply()
	{
		global $_ShopInfo;
        $Dir ="../";
		include_once($Dir."conf/config.ap_point.php");				// 활동포인트 지급 정보
        $ap_comment_cnt		= $pointSet['comment']['count'];	// 댓글 지급 횟수제한
        $ap_comment_point	= $pointSet['comment']['point'];	// 댓글 지급 포인트

		$chk_admin = $_REQUEST['admin'];
		$degree = $_REQUEST['degree'];
		$list_no = $_REQUEST['list_no'];
		$reply_no = $_REQUEST['reply_no'];
		$content = $_REQUEST['comment'];
		$id = $_ShopInfo->memid;

		if($chk_admin=='admin'){
			$id ='관리자';
		}

		#댓글 순서 확인
		$sort_sql = " select max(sort) as sort from tblforumreply ";
		$sort_sql .= " where list_no = {$list_no} ";
		$sort_sql .= " AND degree = {$degree} ";
		if($reply_no){
			$sort_sql .= " AND reply_no = {$reply_no} ";
		}
		$sort = pmysql_fetch_object(pmysql_query($sort_sql))->sort;
		$sort = $sort ? $sort +1 : 1;

		#댓글 등록
		if($degree !=1){
			$r_sql = " insert into tblforumreply (list_no, reply_no, id, content, sort,degree, writetime ) values ";
			$r_sql .= " (
				{$list_no},
				{$reply_no},
				'{$id}',
				'{$content}',
				{$sort},
				{$degree},
				now()
			)";
		}else{
			$r_sql = " insert into tblforumreply (list_no, id, content, sort,degree, writetime ) values ";
			$r_sql .= " (
				{$list_no},
				'{$id}',
				'{$content}',
				{$sort},
				{$degree},
				now()
			)";
		}
		$result = pmysql_query($r_sql);

        ########### 댓글 포인트 지급 2016-11-18
        // 오늘 댓글 작성시 적립받은 갯수를 체크한다. 2016-11-18
        list($last_index) = pmysql_fetch("select max(index) from tblforumreply");
        $sql_cnt = "select sum(cnt) as cnt 
                    from 
                    (
                        select case when rel_flag = '@comment_in_point' then 1 
                                    when rel_flag = '@comment_in_m_point' then 1 
                                    when rel_flag = '@comment_del_point' then -1
                                    when rel_flag = '@comment_del_m_point' then -1
                                    end as cnt 
                        from tblpoint_act 
                        WHERE 1=1 
                        and rel_flag in ('@comment_in_point', '@comment_del_point', '@comment_in_m_point', '@comment_del_m_point')
                        and regdt >= '".date("Ymd")."000000' 
                        AND regdt <= '".date("Ymd")."235959'
                        AND mem_id = '".$id."'
                    ) a 
                    ";
    	list($cp_cnt) = pmysql_fetch($sql_cnt);
    	if ($cp_cnt < $ap_comment_cnt) { // 댓글 작성시 적립받은 갯수가 설정수보다 작으면
    		insert_point_act($id, $ap_comment_point, '댓글 작성 포인트', '@comment_in_point', "admin_".date("YmdHis"), "fc_".$last_index."_".date("YmdHis"), 0);
    	}
        ###########

		$this->return_status = "WRITE_OK";
	}

	function write_reply_request()
	{
		global $_ShopInfo;
        $Dir ="../";
		include_once($Dir."conf/config.ap_point.php");				// 활동포인트 지급 정보
        $ap_comment_cnt		= $pointSet['comment']['count'];	// 댓글 지급 횟수제한
        $ap_comment_point	= $pointSet['comment']['point'];	// 댓글 지급 포인트

		$degree = $_REQUEST['degree'];
		$list_no = $_REQUEST['list_no'];
		$reply_no = $_REQUEST['reply_no'];
		$content = $_REQUEST['comment'];
		$id = $_ShopInfo->memid;

		#댓글 순서 확인
		$sort_sql = " select max(sort) as sort from tblforumreply_request ";
		$sort_sql .= " where list_no = {$list_no} ";
		$sort_sql .= " AND degree = {$degree} ";
		if($reply_no){
			$sort_sql .= " AND reply_no = {$reply_no} ";
		}
		$sort = pmysql_fetch_object(pmysql_query($sort_sql))->sort;
		$sort = $sort ? $sort +1 : 1;

		#댓글 등록
		if($degree !=1){
			$r_sql = " insert into tblforumreply_request (list_no, reply_no, id, content, sort,degree, writetime ) values ";
			$r_sql .= " (
				{$list_no},
				{$reply_no},
				'{$id}',
				'{$content}',
				{$sort},
				{$degree},
				now()
			)";
		}else{
			$r_sql = " insert into tblforumreply_request (list_no, id, content, sort,degree, writetime ) values ";
			$r_sql .= " (
				{$list_no},
				'{$id}',
				'{$content}',
				{$sort},
				{$degree},
				now()
			)";
		}
		$result = pmysql_query($r_sql);

        ########### 댓글 포인트 지급 2016-11-18
        // 오늘 댓글 작성시 적립받은 갯수를 체크한다.
        list($last_index) = pmysql_fetch("select max(index) from tblforumreply_request");
        $sql_cnt = "select sum(cnt) as cnt 
                    from 
                    (
                        select case when rel_flag = '@comment_in_point' then 1 
                                    when rel_flag = '@comment_in_m_point' then 1 
                                    when rel_flag = '@comment_del_point' then -1
                                    when rel_flag = '@comment_del_m_point' then -1
                                    end as cnt 
                        from tblpoint_act 
                        WHERE 1=1 
                        and rel_flag in ('@comment_in_point', '@comment_del_point', '@comment_in_m_point', '@comment_del_m_point')
                        and regdt >= '".date("Ymd")."000000' 
                        AND regdt <= '".date("Ymd")."235959'
                        AND mem_id = '".$id."'
                    ) a 
                    ";
    	list($cp_cnt) = pmysql_fetch($sql_cnt);
    	if ($cp_cnt < $ap_comment_cnt) { // 댓글 작성시 적립받은 갯수가 설정수보다 작으면
    		insert_point_act($id, $ap_comment_point, '댓글 작성 포인트', '@comment_in_point', "admin_".date("YmdHis"), "fqc_".$last_index."_".date("YmdHis"), 0);
    	}
        ########### 

		$this->return_status = "WRITE_OK";
	}

	function delete_reply()
	{
		global $_ShopInfo;
        $Dir ="../";
		include_once($Dir."conf/config.ap_point.php");				// 활동포인트 지급 정보
        $ap_comment_cnt		= $pointSet['comment']['count'];	// 댓글 지급 횟수제한
        $ap_comment_point	= $pointSet['comment']['point'];	// 댓글 지급 포인트

		$index= $_REQUEST['reply_no'];
		$sql = " update tblforumreply set check_delete ='Y' where index = {$index} ";
		$result = pmysql_query($sql);

        ########### 댓글 포인트 환원 2016-11-18
        $id = $_ShopInfo->memid;
        insert_point_act($id, $ap_comment_point * -1, '댓글 삭제 포인트 환원', '@comment_del_point', "admin_".date("YmdHis"), "fc_".$index."_".date("YmdHis"), 0);
        ###########

		$this->return_status = "DELETE_OK";
	}

	function delete_reply_request()
	{
		global $_ShopInfo;
        $Dir ="../";
		include_once($Dir."conf/config.ap_point.php");				// 활동포인트 지급 정보
        $ap_comment_cnt		= $pointSet['comment']['count'];	// 댓글 지급 횟수제한
        $ap_comment_point	= $pointSet['comment']['point'];	// 댓글 지급 포인트

		$index= $_REQUEST['reply_no'];
		$sql = " update tblforumreply_request set check_delete ='Y' where index = {$index} ";
		$result = pmysql_query($sql);

        ########### 댓글 포인트 환원 2016-11-18
        $id = $_ShopInfo->memid;
        insert_point_act($id, $ap_comment_point * -1, '댓글 삭제 포인트 환원', '@comment_del_point', "admin_".date("YmdHis"), "fqc_".$index."_".date("YmdHis"), 0);
        ###########

		$this->return_status = "DELETE_OK";
	}

	function set_request_form()
	{
		$this->category_list_main();
	}


}
