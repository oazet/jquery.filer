<?php

	include('upload.class.php');
	
	$upload = new uploadClass();
	
	$upload->fields = 'name,type,replaced';
	
	$obj = $upload->upload($file = 'file3',  
	                       $options = array(
							  'limit'=>null, //Limit of maximum upload files
							  'maxSize'=>null, //Max size of each file
							  'title'=>array('name'), //new title for file: (type : 'auto','customTitle','name','nameAuto'; length of rand string; customTitle)
							  'uploadDir'=>"uploads/", //Upload directory
							  'types'=>"Files", //Type of uploads, just for alert
							  'extensions'=>null, //Allowed Extensions, if null than any extension.
							  'removeFiles'=>true, //Option for removing files
							  'required'=>false, //If files are required
							 )
						  );

	#return
	$status = $upload->status;
	if($status){
		$data = $upload->data['files'];
		
		echo $data . ' | ';
	}else{
		$data = $upload->data;
		
		echo '<h2>Error!!!</h2>';
		print_r($data);
	}
?>
