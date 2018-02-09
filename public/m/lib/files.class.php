<?
class FILES{
	var $dir;
	var $files;
	var $ext;
	var $v_filename;
	var $r_filename;
	var $q_string;
	var $file_src;


	function FILES($p_dir=""){
		global $_FILES, $_SERVER, $f_config;
		$this->file_src = $f_config[file_src];
		$this->dir=$f_config[file_dir];
		$this->files=$_FILES;

	}

	function upFiles(){
		$file_num=0;
		if(is_array($this->files)){
			foreach($this->files as $name=>$data){
				for($i=0; $i<count($data[name]); $i++){
					$ext = $this->getExt($data[name][$i]);
					$v_filename = md5(time().$this->getFilename($data[name][$i])).".".$ext;
					$r_filename = $data[name][$i];
					if(move_uploaded_file($data[tmp_name][$i],$this->dir.$v_filename)){
						$r_data[$file_num]["name"] = $name;
						$r_data[$file_num]["r_file"] = $r_filename;
						$r_data[$file_num]["v_file"] = $v_filename;
						$r_data[$file_num]["error"] = false;
					}else{
						$r_data[$file_num]["error"] = true;
					}
					$file_num++;
				}
			}
		}
		return $r_data;
	}
	function removeFile($p_file){
		if(is_file($this->dir.$p_file)){
			unlink($this->dir.$p_file);
		}
	}
	function getExt($p_filename){
		return substr($p_filename,strrpos($p_filename,".")+1);
	}
	function getFilename($p_filename){
		return substr($p_filename,0,strrpos($p_filename,"."));
	}

	function downFile(){
		global $_SERVER;
		if (!eregi($_SERVER['HTTP_HOST'], $_SERVER['HTTP_REFERER'])) msg("외부에서는 다운로드 받으실수 없습니다."); 

		if( !$this->r_file || !$this->v_file || !$this->dir ) return 1; 
		if( eregi( "\\\\|\.\.|/", $this->v_file ) ) return 2; 
		
		$ext = $this->getExt($this->v_file);

		if ($ext=="avi" || $ext=="asf")         $file_type = "video/x-msvideo"; 
		else if ($ext=="mpg" || $ext=="mpeg")   $file_type = "video/mpeg"; 
		else if ($ext=="jpg" || $ext=="jpeg")   $file_type = "image/jpeg"; 
		else if ($ext=="gif")                   $file_type = "image/gif"; 
		else if ($ext=="png")                   $file_type = "image/png"; 
		else if ($ext=="txt")                   $file_type = "text/plain"; 
		else if ($ext=="zip")                   $file_type = "application/x-zip-compressed"; 


		if( file_exists($this->dir.$this->v_file) ) 
		{ 
				$fp = fopen($this->dir.$this->v_file,"r"); 
				$this->r_file = iconv("utf-8","euc-kr",$this->r_file);
				if( $file_type ) 
				{ 

						Header("Content-Length: ".filesize($this->dir.$this->v_file));     
						Header("Content-Disposition: attachment; filename=$this->r_file");   
						Header("Content-Transfer-Encoding: binary"); 
						header("Expires: 0"); 
				} 
				else 
				{ 
						if(eregi("(MSIE 5.0|MSIE 5.1|MSIE 5.5|MSIE 6.0)", $HTTP_USER_AGENT)) 
						{ 
								Header("Content-type: application/octet-stream"); 
								Header("Content-Length: ".filesize($this->dir.$this->v_file));     
								Header("Content-Disposition: attachment; filename=$this->r_file");   
								Header("Content-Transfer-Encoding: binary");   
								Header("Expires: 0");   
						} 
						else 
						{ 
								Header("Content-type: file/unknown");     
								Header("Content-Length: ".filesize($this->dir.$this->v_file)); 
								Header("Content-Disposition: attachment; filename=$this->r_file"); 
								Header("Content-Description: PHP3 Generated Data"); 
								Header("Expires: 0"); 
						} 
				} 


				fpassthru($fp); 
				fclose($fp); 
		} 
		else return 1; 
	} 

}
?>