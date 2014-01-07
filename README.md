jquery.filer 1.0
============
jquery.Filer - Simple HTML5 File Uploader, a plugin tool for Jquery wich change completely File Input and make it with multiple file selection, drag&drop support, different validations, thumbnails, instant upload, progress bars and other options for your jQuery. It also includes simple PHP uploading script with validation and other options for your upload.

<b><a href="http://creativedream.net/jquery.filer/#demos" target="blank">Demo</a></b> | <b><a href="http://creativedream.net/jquery.filer/#documentation" target="blank">Documentation</a></b>

Features
-------
__Javascript:__
* Completely change File Input
* Upload files after choosing
* Validate files(limit,size,extension,image)
* Create thumbs
* Custom thumbs icons(psd included) for all type of files
* Custom templates for: new Input and thumbs
* Remove Choosed/Uploaded Files
* Drag & Drop Option (it will return a FormData)

__PHP:__
* Simple PHP usage
* Uploading files via global variable: $_FILES
* Multiple and single files Uploading
* Simple options to customize
* Custom title for uploaded files
* Validate files(limit,size,extension,required)
* Remove Files
* Special returning data after upload


Usage
-------
__Javascript:__

Include the jQuery libraray and jquery.filer script file in your html page.
~~~~ html
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script src="jquery.filer.min.js"></script>
~~~~
Create a file input.
~~~~ html
<input type="file" name="file" multiple="multiple" />
~~~~
The plugin is named "filer" and can be applied to a file input. You will probably also specify some options while applying the plugin.
~~~~ javascript
$("input:file").filer();

$('input:file').filer({
    types      : 'Image, Audio, Video',
    limit      : 12,
    maxSize    : 25,
    extensions : ['jpg','jpeg','png','gif','mp3','mp4','wmv'],
    newExt     : ['zip','psd'],
    changeInput: true,
    showThumbs : true,
    iconPath   : './images/',
    appendTo   : null,
    maxChar    : 15,
    removeFiles: true,
    template   : '<img src="%image-url%" title="%original-name%" /><em>%title%</em> %remove-icon%',
    uploadFile: {
        url:         'uploadEachFile.php',
        data:        {},
        beforeSend:  function(parent){ parent.append('<div class="progress-bar" />'); },
        success:     function(data, parent, progress){ },
        error:       function(e, parent, progress){ },
        progressEnd: function(progress){ progress.addClass('done-erase'); },
        onUploaded:  function(parent){ }
    },
    dragDrop: {
        dropBox:  '.dragDropBox',
        dragOver: function(e, parent){ },
        dragOut:  function(e, parent){ },
        drop:     function(e, formData, parent){ },
    },
    beforeShow : function(e,parent){ return true; },
    onEmpty    : function(parent, appendBox){ }
    onSelect   : function(e,parent,appendBox){ },
    onRemove   : function(e,parent){ return true; },
    inputText  : {choose:'Choose',feedback:'Choose files',feedback2:'files were chosen',feedback3:'No file chosen'}
});
~~~~

__PHP:__
~~~~ php
<?php
    include('upload.class.php');
        
    $upload = new uploadClass();
    
    $upload->fields = 'name,extension,type,size,tmpName,uploadDir,newFile,replaced,date,perms,image';
    
    $obj = $upload->upload($file = 'file',  
                           $options = array(
                                'limit'=>3,
                                'maxSize'=>3,
                                'title'=>array('auto',12),
                                'uploadDir'=>'uploads/',
                                'types'=>'Image, Audio, Video',
                                'extensions'=>array("jpg","jpeg","png","gif","mp3","wmv","mp4"),
                                'removeFiles'=>true,
                                'required'=>true,
                                'onCheck'=>'name of your callback function',
                                'onUpload'=>'name of your callback function',
                               )
                            );
    
    $status = $upload->status;
    
    if($status == true){
        echo 'Success';
        print_r($upload->data);
    }else{
        echo 'Error';
        print_r($upload->data);
    }
?>
~~~~


License
-------
> Licensed under <a href="http://opensource.org/licenses/MIT">MIT license</a>.
