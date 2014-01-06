<?php

	include('upload.class.php');
	
	//Basic
	$upload = new uploadClass();
	$obj = $upload->upload($file = 'file');
	
	#return
	$status = $upload->status;
	
	if($status == true){
		//success
		$data = $upload->data;
		echo '<h2>First Input</h2>';
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}else{
		//error
		echo '<h1>Error!!!</h1>';
		$data = $upload->data;
		echo implode('<br>',$data);
	}
	
	/* ================================================================ */
	
	//Custom Options & filerIndex
	$upload = new uploadClass();
	
	$upload->fields = 'name,extension,type,size,tmpName,uploadDir,newFile,replaced,date,perms'; //custom return fields
	$obj2 = $upload->upload($file = 'file2', 
	                        $options = array(
								'limit'=>10, //Limit of maximum upload files
								'maxSize'=>50, //Max size of each file
								'title'=>array('auto',12,'file'), //new title for file
								'uploadDir'=>"uploads/", //Upload directory
								'types'=>"Image, Audio, Video", //Type of uploads, just for alert
								'extensions'=>array("jpg","jpeg","png","gif","mp3","wmv","mp4",'txt','zip'), //Allowed Extensions, if null than any extension.
								'removeFiles'=>true, //Option for removing files
								'required'=>true, //If files are required
								'onUpload'=>'onUploadCallback',
								'onCheck'=>'onCheckCallback',
							   ), 
							 $filerIndex = 1
							);
															   
	#return
	$status = $upload->status;
	if($status == true){
		//success
		$data = $upload->data;
		echo '<h2>Second Input</h2>';
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}else{
		//error
		echo '<h1>Error!!!</h1>';
		$data = $upload->data;
		echo implode('<br>',$data);
	}
	
	/* CALLBACKS */
	
	function onUploadCallback($data, $file){
        /*On Upload callback*/
		$array = array();
		
		if($data['type'][0] == 'image' && @getimagesize($data['newFile'])){
			$imgInfo = @getimagesize($data['newFile']);
			
			$array['image'] = array('width'=>$imgInfo[0],'height'=>$imgInfo[1]);
		}
		
		return $array;
	}
	
	function onCheckCallback($file){
		/*On Check callback*/
		
		$array = array();
		
		for($i=0; $i<count($file['name']);$i++){
			//$array[] = 'Error';
		}
		
		return $array;
	}
?>
