<?php
   # ========================================================================#
   #  
   #  Title      Upload Class
   #  Author:    CreativeDream
   #  Website:   http://creativedream.net/plugins
   #  Version:	 1.0
   #  Date:      15-Nov-2013
   #  Purpose:   Check, Remove, Upload files
   #  Usage Example:           
   #      
   #      ---> 1 (create class) <---                       
   #      $uploadObj = new uploadClass();
   #      
   #      ---> 2 (upload files) <---   
   #      $uploadObj -> upload( (string) $filer, (array) $options, (int)  $filerIndex);
   #         $filer = 'file' //name of input with files
   #         $options = array('url'=>'options/filer.opts.ini') //user options from file that jquery.filer uses too
   #         $filerIndex = 0 //index of input, this is for removing Files input, basic it choose automatic
   #     --->3 (return data) <---
   #     $uploadObj -> status //upload status, if no errors occured then true
   #     $uploadObj -> data //return data
   #              ['files'] - all files in one string
   #              [ others ]- info from each file 
   #                        [name] => Array
   #							(
   #								[0] => new name
   #								[1] => uploaded name
   #							)
   #			
   #						[extension] => file extension
   #						[type] => Array
   #							(
   #								[0] => mime type part 1 :image/jpeg(image)
   #								[1] => mime type part 2 :image/jpeg(jpeg)
   #							)
   #			
   #						[size] => Array
   #							(
   #								[0] => new size
   #								[1] => uploaded size
   #							)
   #			
   #						[tmpName] => tmp_name
   #						[uploadDir] => upload directory
   #						[newFile] => directory to file
   #						[replaced] => if file replaced another
   #						[date] => date of uploading
   #						[perms] => file permission
   #        
   #     ... default options = Line (62) $options   ...
   #
   #     ** don't forget to change options in php.ini: file_uploads, upload_max_filesize, post_max_size, max_file_uploads
   #
   # ========================================================================#
	class uploadClass {
			public $status = true, //status, if no errors then true else false
			       $filer = 'file', //$_FILES[(string) $name_of_input]
			       $filerIndex = 0, //index of filer (!important for removed files input[name="inputOrdBox-{$filerIndex}]")
				   $fields = 'name,extension,type,size,tmpName,uploadDir,newFile,replaced,date,perms', //$data fields 'name,extension,type,size,tmpName,uploadDir,newFile,replaced,date,perms,image';
				   $data = array(), //return data
				   $options = array(
									'limit'=>12, //Limit of maximum upload files, if null than no limit.
									'maxSize'=>10, //Max size of each file, if null than any size.
									'title'=>array('auto',12,'file'), //new title for file: (type : 'auto','customTitle','name','nameAuto'; length of rand string; customTitle)
									'uploadDir'=>"uploads/", //Upload directory
									'types'=>"Image, Audio, Video", //Type of uploads, just for alert
									'extensions'=>array("jpg","jpeg","png","gif","mp3","wmv","mp4","zip"), //Allowed Extensions, if null than any extension.
									'removeFiles'=>true, //Option for removing files
									'required'=>true, //If files are required
									'onUpload'=>null, //On upload callback name - function($data, $file){return array('key'=>'value');}
									'onCheck'=>null, //On check callback name - function($file){return array('Error 1','Error2', 'Error3');}
								   );		   
			
			public function __construct(){
                // __construct function
			}
			public function upload($file=null,$options=null,$filerIndex=false){
				
				if($options != null){
					$this->_setOptions($options);
				}
				
				$this->_resetVars();
				
				if(!isset($_FILES[$file])){
					$this->status = false;
					return false;
				}else{
					$this->filer = $_FILES[$file];
					$newFormat = array('name'=>array(),'type'=>array(),'tmp_name'=>array(),'error'=>array(),'size'=>array());
					if(is_array($_FILES[$file]['name'])){
						for($i = 0; $i<count($_FILES[$file]['name']); $i++){
							$newFormat['name'][] = $this->filer['name'][$i];
							$newFormat['type'][] = $this->filer['type'][$i];
							$newFormat['tmp_name'][] = $this->filer['tmp_name'][$i];
							$newFormat['error'][] = $this->filer['error'][$i];
							$newFormat['size'][] = $this->filer['size'][$i];
						}
					}else{
						$newFormat['name'][] = $this->filer['name'];
						$newFormat['type'][] = $this->filer['type'];
						$newFormat['tmp_name'][] = $this->filer['tmp_name'];
						$newFormat['error'][] = $this->filer['error'];
						$newFormat['size'][] = $this->filer['size'];
					}
					$this->filer = $newFormat;
				}
				
				//Filer Index
				$this->filerIndex = ($filerIndex==false)?@array_search($file,array_keys($_FILES)):$filerIndex;
				if(count($_FILES) == 1){
					foreach($_POST as $key=>$value){
						$data = preg_match('/inputOrdBox-(\d+)/', $key);
						$num = substr(strrchr(strtolower($key), "-"),1);
						if(is_numeric($num)){
							$this->filerIndex = $num;
						}
					}
				}
				
				//Remove files
				if(!$this->_removedFiles()){
					$this->notAllowed = array();
			    }
				
				if($this->_checkFunction()){
					$this->_uploadFiles();
				}
			}
			private function _resetVars(){
				//Reset Class Variables
				
				$vars = get_class_vars(__CLASS__);
				array_pop($vars);
				
				foreach($vars as $var){
					unset($var);
				}
				
				$this->data = array('files'=>'');
			}
			private function _setOptions($newOpts){
				//Set Upload options
				
				if(!is_array($newOpts)){return false;}
				
				if(isset($newOpts['url'])){
					$options = @json_decode(file_get_contents($newOpts['url']),true);
				}else{
					$options = $newOpts;
				}
				
				$this->options = @array_merge($this->options,$options);
			}
			private function _checkFunction(){
				//Check Files Function
				
				$error = array();
				$file = $this->filer;
				$option = $this->options;
				$ini = array(ini_get('file_uploads'),((int) ini_get('upload_max_filesize')),((int) ini_get('post_max_size')));
				
				if(!isset($file) || !$ini[0]){$error[] = "File Input is not available!";}else{
					 if($option['required']==false && empty($file['name'][0])){$this->_error(); return false;}#Empty input
					 if($option['required']==true && empty($file['name'][0])){$this->_error(array('You must select a file!')); return false;}#Empty inpu but it is required
					 if($option["limit"] != null && (count($file['name']) - count($this->notAllowed)) > $option["limit"]){$error[] = "There are more files than ".$option["limit"];}#Max Files
					 if(!is_dir($option['uploadDir']) || !is_writable($option['uploadDir'])){error_log('uploadDir is not available, we created a new one, uploadClass'); mkdir($option["uploadDir"]);}#Make directory
					 if($option['extensions'] != null && (!is_array($option['extensions']) || empty($option['extensions']))){$error[] = "Extensions are not available!";}#Extension array check
					 
					 #validate each file
					 $totalSize = 0;
					 for($i=0; $i<count($file['name']);$i++){
						 $array = array($file['name'][$i],$file['size'][$i]/1048576,$file['error'][$i]);
						 
						 #size
						 if($array[1] <= 0){$error[] = "File <b>".$file['name'][$i]."</b> is empty!";}
						 if($option['maxSize']!= null && $array[1] > $option['maxSize']){$error[] = "File <b>".$file['name'][$i]."</b> is larger than ".$option['maxSize']."MB";}
						 if($array[1] > $ini[1]){$error[] = "File <b>".$file['name'][$i]."</b> is larger than ".$ini[1]."MB";}
						 $totalSize += $array[1];
						 
						 #erorr
				         if($array[2] > 0){$error[] = "An Error has occurred";}
						 
						 #extension is available
						 if($option['extensions']!= null && !in_array(substr(strrchr(strtolower($array[0]), "."),1),$option['extensions'])){$error[] = "File <b>".$file['name'][$i]."</b> is not allowed, please select: ".$option["types"]." files.";} 				 
					 }
					 if($totalSize > $ini[2]){$error[] = "Files are to large. Post size is ".$ini[2]."MB";}
					 
					 $func = $this->options['onCheck'];
					 if($func != null && function_exists($func)){
						 $func = $func($file);
						 if(is_array($func) && !empty($func)){
							 $error = @array_merge($error,$func);
						 }
					 }
				}
				
				#Return
				if(empty($error)){
                    return true;
				}else{
					$this->_error($error); 
					return false;
				}	 
			}
			private function _uploadFiles(){
				//Upload Files Function
				
				$file = $this->filer;
				$option = $this->options;
				$notAllowed = (isset($this->notAllowed) && is_array($this->notAllowed)?$this->notAllowed:array());
				
				#upload each file
				for($i = 0; $i<=count($file['name']); $i++){
				    if(isset($file['name'][$i]) || $i+1 == count($file['name'])){
						if(!in_array($i, $notAllowed)){
							
							$ext = substr(strrchr(strtolower($file['name'][$i]), "."),1);
							$name = substr( $file['name'][$i],0,-(strlen(strrchr($file['name'][$i],$ext))+1) );
							$type = preg_split('[/]', $file['type'][$i]);
							$size = $file['size'][$i];
							$tmp_name = $file['tmp_name'][$i];
							$newName = $this->_newFileName((isset($option['title'][0])?$option['title'][0]:'auto'),(isset($option['title'][1])?$option['title'][1]:15),(isset($option['title'][2])?$option['title'][2]:'file'),$name) . ".$ext";
							$newFile = $option["uploadDir"].$newName;
							$replaced = (file_exists($newFile))?'true':'false';
							$date = date('r');
	
							if(move_uploaded_file($tmp_name, $newFile)){
								$fileInfo = array('name'=>array($newName,$name.'.'.$ext),
												  'extension'=>$ext,
												  'type'=>$type,
												  'size'=>array(filesize($newFile),$size),
												  'tmpName'=>$tmp_name,
												  'uploadDir'=>$option['uploadDir'],
												  'newFile'=>$newFile,
												  'replaced'=>$replaced,
												  'date'=>$date,
												  'perms'=>fileperms($newFile),
												 );
												 
								//====== own function ======
								
								$fields = explode(',',$this->fields);
								$fileInfo = (is_array($fields) && !empty($fields)?array_intersect_key($fileInfo,array_flip($fields)):$fileInfo);
								
								#on Upload								
								$func = $this->options['onUpload'];
								if($func != null && function_exists($func)){
									$func = $func($fileInfo, $file);
									if(is_array($func)){
										$fileInfo = @array_merge($fileInfo,$func);
									}
								}
								
								(!empty($fileInfo)?$this->data[] = $fileInfo:null);
								$this->data['files'] .= $newFile . '|';				   
							}
						}
					}else{
						$this->data['files'] = substr($this->data['files'],0,-1);
						
						$this->_success();

						return true;
					}
				}
			}
			private function _removedFiles(){
				//Return array with removed files
				
				if(!isset($this->options['removeFiles']) || $this->options['removeFiles'] = false){return false;}
				if(!isset($_POST['inputOrdBox-'.$this->filerIndex]) || empty($_POST['inputOrdBox-'.$this->filerIndex])){return false;}
				
				$notAllowed = explode(',', $_POST['inputOrdBox-'.$this->filerIndex]);
                array_pop($notAllowed);
				
				$this->notAllowed = $notAllowed;
				return true;
			}
			private function _newFileName($type='auto',$num=12,$title='file',$name='file'){
				//Create a new name for your file
				
				$random = substr(str_shuffle("_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $num);
				
				switch ($type){
					case 'auto':
						$string = $random;
					break;
					case 'nameAuto':
					    $string = $name .'_'. $random;
					break;
					case 'customTitle':
					    $string = $title;
					break;
					case 'name':
					    $string = $name;
					break;
				}
		        return $string;
			}
			private function _success(){
				//Success Function
				
			}
			private function _error($error=''){
				//Error Function
				if(is_array($error) && !empty($error)){
					$this->status = false;
					$this->data = $error;
				}else{
					$this->data = array();
				}
			}	
	}
?>
