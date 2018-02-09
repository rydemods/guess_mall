<?
include_once dirname(__FILE__)."/files.class.php";

class SOAP_FILES extends FILES{
	var $db;
	var $files;
	var $dir;
	var $file_src;

	function SOAP_FILES(){
		global $f_config;
//		include_once dirname(__FILE__)."/db.class.php";
//		$this->db = new DB(dirname(__FILE__)."/../config/db.cfg.php");
		$this->file_src = $f_config[file_src];
		$this->FILES();
	}

	function uploadFiles($params){
		$files = $this->upFiles();
		if(is_array($files)){
			foreach($files as $file){
				echo $file[error]."<br>"; 
				if(!$file[error]){
					$name = $file[name]?$file[name]:$params[name];
					$query = " insert into hp_file (id, c_id, c_name, r_file, v_file) values (hp_file_seq.nextval, '".$params[id]."', '".$name."', '".$file[r_file]."', '".$file[v_file]."')";
					$this->db->query($query);
				}
			}
		}
	}
	function getFile($id){
		$where[] = " id = '".$id."' ";

		$query = " select id, c_id, c_name, r_file, v_file, regdt from hp_file ";
		$query.= " where ".implode(" and ", $where)." ";
		$file = $this->db->fetch($query);

		return $file;

	}

	function getFiles($params){

		$where[] = " c_id = '".$params[id]."' ";
		$where[] = " c_name = '".$params[name]."' ";

		$query = " select id, c_id, c_name, r_file, v_file, regdt from hp_file ";
		$query.= " where ".implode(" and ", $where)." ";
		$query.= " order by id desc ";

		$result = $this->db->query($query);
		while($row = $this->db->fetch($result)){
			$data["src"] = $this->file_src.$row[v_file];
			$data["id"] = $row[id];
			$data["c_id"] = $row[c_id];
			$data["c_name"] = $row[c_name];
			$data["r_file"] = $row[r_file];
			$data["v_file"] = $row[v_file];
			$files[] = $data;
		}
		return $files;
	}
	
	function deleteFile($delfileid){
		
		$where[] = " id in ('".implode("','",$delfileid)."') ";

		#### 파일 삭제 처리
		$query = " select v_file from hp_file ";
		if($where) $query.= " where ".implode("','",$where)." ";
		$result = $this->db->query($query);
		while($row = $this->db->fetch($result)){
			$this->removeFile($row[v_file]);
		}

		#### DB 삭제 처리
		$query = " delete from hp_file ";
		if($where) $query.= " where ".implode("','",$where)." ";
		$this->db->query($query);

	}

	function deleteFiles($params){

		$where[] = " c_id = '".$params[id]."' ";
		$where[] = " c_name = '".$params[name]."' ";
		$query = "select * from hp_file ";
		if($where) $query.= " where ".implode(" and ",$where);

		$result = $this->db->query($query);
		while($row = $this->db->fetch($result)){
			$this->removeFile($row[v_file]);
		}

		#### DB 삭제 처리
		$query = " delete from hp_file ";
		if($where) $query.= " where ".implode("','",$where)." ";
		$this->db->query($query);

	}

	function fileInputForm($p_name, $p_files, $p_config){
		$p_config[type] = $p_config[type]?$p_config[type]:"image";
		$p_config[multi] = $p_config[multi]?$p_config[multi]:false;
		unset($str);

		$form_str = "<input type=file name=".$p_name."[] >";
		if($p_config[multi]){
			$multi_form_str.= " <div style='padding-left:5px; float:left;'><input type='button' value='+' onclick=\"addFileForm('".$form_str."','".$p_name."')\"></div>";
		}
		if(is_array($p_files)){
			$i=0;
			foreach($p_files as $file){
				$style_tag = $i=="0"?"style='float:left;'":"style='clear:both;'";
				if($file[id]){
					if($p_config[type]=="image") $str.= " <img src='".$file[src]."' > <br>";
					$str.= "<div ".$style_tag."> ".$file[r_file]." <input type='checkbox' name='delfileid[]' onclick=\"if(this.checked==true){document.getElementById('file_form_".$file[id]."').style.display='block'}else{document.getElementById('file_form_".$file[id]."').style.display='none'}\" value='".$file[id]."'> 삭제 </div>";
					if($multi_form_str){
						$str.= $multi_form_str;
						$multi_form_str="";
					}
					$str.= " <div id='file_form_".$file[id]."' style='clear:both;display:none;'><input type='file' name='".$p_name."[]' ></div>";
				}else{
					$str.= " <div id=file_form_".$file[id]." style='width:170px;float:left;'><input type=file name=".$p_name."[] ></div>";
					$str.= $multi_form_str;
				}
				$i++;
			}
		}else{
			$str.= " <div id=file_form_".$file[id]." style='width:170px;float:left;'><input type=file name=".$p_name."[] ></div>";
			$str.= $multi_form_str;
		}
			$str.= " <div id='addFileForm".$p_name."' style='clear:both; width:150px;'></div>";
		return $str;
	}
	function getFileForView($files){
		global $_COOKIE;
		$module = $files[0][c_name];
		$max_bg_cnt = count($files);
		
		
		if($max_bg_cnt>1){
			$vnum = $_COOKIE[$module][view_num];
			while($_COOKIE[$module][view_num]==$vnum){
				$_COOKIE[$module][view_num] = mt_rand(1,$max_bg_cnt);
			}
		}else{
			$_COOKIE[$module][view_num] = $max_bg_cnt;
		}
		return $files[$_COOKIE[$module][view_num]-1][src];

		

		$max = count($files)-1;
		return $files[mt_rand(0,$max)];
	}
	function getFileForDoc($files){
		foreach($files as $file){
			if($str) $str.= ", ";
			$str.="<a href='../lib/download.php?id=".$file[id]."'>".$file[r_file]."</a>";
		}
		return $str;
	}
	function downloadFile($id){
		$file_data=$this->getFile($id);
		$this->r_file = $file_data[r_file];
		$this->v_file = $file_data[v_file];
		$this->downFile();

	}
}
?>