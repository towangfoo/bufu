Uploader = Class.create();
Uploader.prototype = {
    config: null,
    id: 0,
    unfinishedUploads: 0,
    
    files: [],
    uploadFiles: [],
    
    completeMethod: false,
    progressMethod: false,
    selectMethod: false,
    errorMethod: false,
    
    fileIdPrefix: 'file_',
    
    statusComplete: 'full_complete',
    statusError: 'error',
    statusNew: 'new',
    
    htmlIdBlock: 'uploadBlock-',
    htmlIdBlocks: 'uploadBlocks',
    htmlIdIframe: 'uploadBlockIframe-',
    
    inputNameFile: 'image',
    inputNameFilename: 'Filename',
    inputNameFormKey: 'form_key',
    inputNameUpload: 'Upload',
    
    inputValueUpload: 'Submit Query',

    initialize: function(config)
    {
        this.config = config;
    },
    
    initUploadBlocks: function()
    {
        var uploadBlock = this.createUploadBlock(this.id);
        
        $(this.htmlIdBlocks).appendChild(uploadBlock);
        
        this.addUploadBlock(uploadBlock);
    },
    
    addListener: function(event, method)
    {
        
        switch(event)
        {
            case 'select':
                this.selectMethod = method;
                break;
            case 'complete':
                this.completeMethod = method;
                break;
            case 'progress':
                this.progressMethod = method;
                break;
            case 'error':
                this.errorMethod = method;
                break;
        }
        
    },
    
    addUploadBlock: function (uploadBlock)
    {
        var inputFile = this.getForm(uploadBlock).getElementsByTagName('input')[0];
        var that = this;
        
        inputFile.onchange = function()
        {
            this.style.display = 'none';
        
            ++that.id
            var newUploadBlock = that.createUploadBlock(that.id);
        
            this.parentNode.parentNode.parentNode.insertBefore(newUploadBlock,
                                                               this.parentNode.parentNode);
                                                               
            that.addUploadBlock(newUploadBlock);
            
            var file = Object();
            file.id = that.fileIdPrefix + (that.id - 1);
            file.name = this.value;
            file.status = that.statusNew;
            file.creator = null;
            file.size = 0;
            
            that.files.push(file);
            
            that.selectMethod(that.files);
        }
        
    },
    
    getFilesInfo: function()
    {
        return this.files;
    },
    
    upload: function()
    {
        var uploadBlocks = $(this.htmlIdBlocks).childNodes;
        var lastUploadBlockNumber = uploadBlocks.length - 1;

        for (var i = lastUploadBlockNumber; i > 0; --i)
        {
            var iframe = uploadBlocks[i].getElementsByTagName('iframe')[0];
            
            var id = this.getId(uploadBlocks[i].id, this.htmlIdBlock);

            ++this.unfinishedUploads;
            
            var that = this;

            var uploadFile = Object();
            uploadFile.id = this.fileIdPrefix + id;
            
            this.progressMethod(uploadFile);

            var iframeOnLoad = function(event)
            {
                var frame;
            
                if (window.addEventListener)
                {
                    frame = this;
                } else
                {
                    frame = event.srcElement; //for IE
                }
            
                var frameId = that.getId(frame.parentNode.id, that.htmlIdBlock);
                var response = frame.contentWindow.document.body.innerHTML;
                var status = that.statusError;
                var size = 0

                --that.unfinishedUploads;

                if (response.evalJSON().error == 0)
                {
                    status = that.statusComplete;
                    size = response.evalJSON().size;
                }

                var uploadFile = Object();
                uploadFile.id = that.fileIdPrefix + frameId;
                uploadFile.progress = Object();
                uploadFile.name = that.getForm(frame.parentNode).getElementsByTagName('input')[0].value;
                uploadFile.status = status;
                uploadFile.response = response;
                uploadFile.creator = null;
                uploadFile.http = 200;
                uploadFile.size = size;
                
                if (response.evalJSON().error != 0)
                {
                    uploadFile.errorText = response.evalJSON().error;
                    that.errorMethod(uploadFile);
                } else
                {
                    that.uploadFiles.push(uploadFile);
                }
                
                if (that.unfinishedUploads == 0)
                {
                    that.completeMethod(that.uploadFiles);
                    that.uploadFiles = [];
                }
            }
            
            if (window.addEventListener)
            {
                iframe.addEventListener('load', iframeOnLoad, false);
            } else
            {
                iframe.attachEvent('onload', iframeOnLoad); // for IE
            }

        }
        
        for (var i = lastUploadBlockNumber; i > 0; --i)
        {
            this.getForm(uploadBlocks[i]).submit();
        }
        
    },
    
    removeFile: function(fileId)
    {
        var id = this.getId(fileId, this.fileIdPrefix);
        var blocks = $(this.htmlIdBlocks);
        var block = document.getElementById(this.htmlIdBlock + id);

        blocks.removeChild(block);
        
        for (var i = 0; i < this.files.length; ++i)
        {
        
            if (this.files[i].id == fileId)
            {
                this.files.splice(i, 1);
            
                break;
            }
            
        }
        
    },
    
    createUploadBlock: function(id)
    {
        var uploadTarget = this.htmlIdIframe + id;
        
        var uploadForm = document.createElement('form');
        uploadForm.action = this.config.url;
        uploadForm.enctype = 'multipart/form-data';
        uploadForm.encoding = 'multipart/form-data'; //for IE
        uploadForm.method = 'post';
        uploadForm.target = uploadTarget;
        
        var uploadInputFile = document.createElement('input');
        uploadInputFile.type = 'file';
        uploadInputFile.name = this.inputNameFile;
        
        var uploadInputFilename = document.createElement('input');
        uploadInputFilename.type = 'hidden';
        uploadInputFilename.name = this.inputNameFilename;
        
        var uploadInputFormKey = document.createElement('input');
        uploadInputFormKey.type = 'hidden';
        uploadInputFormKey.name = this.inputNameFormKey;
        uploadInputFormKey.value = this.config.params.form_key;
        
        var uploadInputQuery = document.createElement('input');
        uploadInputQuery.type = 'hidden';
        uploadInputQuery.name = this.inputNameUpload;
        uploadInputQuery.value = this.inputValueUpload;
        
        uploadForm.appendChild(uploadInputFile);
        uploadForm.appendChild(uploadInputFilename);
        uploadForm.appendChild(uploadInputFormKey);
        uploadForm.appendChild(uploadInputQuery);
        
        var uploadBlock = document.createElement('div');
        uploadBlock.id = this.htmlIdBlock + id;
        
        var uploadIframe;
        
        try {
            uploadIframe = document.createElement('<iframe name="' + uploadTarget + '">'); //works only in IE
        } catch (exc)
        {
            uploadIframe = document.createElement('iframe'); //works in all other browsers
            uploadIframe.name = uploadTarget;
        }
        
        uploadIframe.style.display = 'none';
        
        uploadBlock.appendChild(uploadForm);
        uploadBlock.appendChild(uploadIframe);
        
        return uploadBlock;
    },
    
    getId: function(id, prefix)
    {
        return parseInt(id.gsub(prefix, ''));
    },
    
    getForm: function(block)
    {
        return block.getElementsByTagName('form')[0];
    }
    
}