#jquery.filer
jquery.Filer - Simple HTML5 File Uploader, a plugin tool for Jquery wich change completely File Input and make it with multiple file selection, drag&drop support, different validations, thumbnails, instant upload, progress bars and other options for your jQuery. It also includes simple PHP uploading script with validation and other options for your upload.

<b><a href="http://creativedream.net/jquery.filer/#demos" target="blank">Demo</a></b> | <b><a href="http://creativedream.net/jquery.filer/#documentation" target="blank">Documentation</a></b>

Features
-------
*Completely change File Input
*Upload files after choosing
*Validate files(limit,size,extension,image)
*Create thumbs
*Custom thumbs icons(psd included) for all type of files
*Custom templates for: new Input and thumbs
*Remove Choosed/Uploaded Files
*Drag & Drop Option (it will return a FormData)

*Simple PHP usage
*Uploading files via global variable: $_FILES
*Multiple and single files Uploading
*Simple options to customize
*Custom title for uploaded files
*Validate files(limit,size,extension,required)
*Remove Files
*Special returning data after upload

Options
-------

The plugin supports the following options when it is initialized for a source:

* __makeClone__ can be true or false. Default is false. If true, the actual source element won't be the
  element that is dragged but rather a clone of it.
* __sourceClass__ can be the name of a CSS class. This class is applied to the source element
  in its original position (if visible) while it is dragged.
* __sourceHide__ can be true or false. When true, the original element is set to invisible while the
  dragging occurs.
* __dragClass__ can be the name of a CSS class. If specified, it is applied to the element that is
  being dragged while the drag operation is active. Note that if makeClone is false, this is also
  the actual source element.
* __canDropClass__ can be the name of a CSS class. If specified, will be applied to the droppable
  area element whenever a dragged element is hovering over it, to signify that the user can drop
  at this time.
* __dropClass__ can be the name of a CSS class. This class name is used to identify droppable
  area elements. The default is "drop". If a callback function is specified under "canDrop", this
  class name has no effect.
* __container__ can be a jQuery element of a container. If specified, elements dragged will not be able
  to move outside of that container.
* __canDrag__ can be a callback function that returns true or false. You can use this callback if you'd
  like to apply the plugin to a larger container, and then only make specific elements inside that
  container draggable by returning true from the callback if you've determined the current element
  as eligable for dragging.
* __canDrop__ can be a callback function that returns true or false. Return true if the dragged element
  can be dropped on the specified element. If this function is used, the "dropClass" setting has
  no effect.
* __didDrop__ can be a callback function. If specified, it is assumed to take care of all operations
  and effects to occur after a successful drag and drop has been performed. Otherwise, the default
  operation is to restore the class on the source element and if makeClone is false the
  element will be appended as a child to the droppable element.

Usage
-----

Include the jQuery libraray file and jquery.filer script in your html page.
~~~~ html
<script src="//code.jquery.com/jquery-latest.min.js"></script>
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

#License

> Licensed under <a href="http://opensource.org/licenses/MIT">MIT license</a>.
