$(document).ready(function () {
    
    setupDropzone();
});

//handles attachments upload
function setupDropzone() {

    Dropzone.autoDiscover = false;

    var template = Mustache.render($('#dropzone-template').html());

    var options = {
        url: '/librinfo/media/upload',
        paramName: "file",
        uploadMultiple: false,
        maxFiles: 5,
        maxFileSize: 5,
        previewTemplate: template,
        clickable: ".add_files",
        dictDefaultMessage: dropzoneMessages.defaultMessage,
        dictFallbackMessage: dropzoneMessages.fallbackMessage,
        dictFallbackText: dropzoneMessages.fallbackText,
        dictInvalidFileType: dropzoneMessages.invalidFileType,
        dictFileTooBig: dropzoneMessages.fileTooBig,
        dictResponseError: dropzoneMessages.responseError,
        dictMaxFilesExceeded: dropzoneMessages.maxFilesExceeded

    };
    //init dropzone plugin
    var dropzone = new Dropzone(".dropzone", options);

    //prevent submitting of the form when add files button is clicked
    $('.add_files').click(function (e) {

        e.preventDefault();
    });
    
    // check size and start progress bar when a file is added
    dropzone.on("addedfile", function (file) {
        
        if( file.id !== undefined )
            $(file.previewElement).data('file-id', file.id);
        
        //file size validation
        if (file.size > 5 * 1024 * 1024) {

            dropzone.cancelUpload(file);
            dropzone.emit('error', file, 'Max file size(5mb) exceeded');
        }

        updateProgressBar(1);
    });

    //Last uploaded file id is appended to the form so that it can be linked to owning entity on backend side
    dropzone.on("success", function( file, result ) {

        $(file.previewElement).data('file-id', result);
      
        insertInput(result);
    });

    //Reset progress bar when done uploading
    dropzone.on("queuecomplete", function (progress) {

        updateProgressBar(0);
    });
    
    //Removal of already uploaded files
    dropzone.on("removedfile", function (file) {
        
        var id = $(file.previewElement).data('file-id');
        
        $('input[name="file_ids[]"][value="' + id + '"]').remove();
       
        $.get('/librinfo/media/remove/' + id, function (response) {

            console.log(response);
        });
    });
    
    retrieveFiles(dropzone);
}

// Retrieval of already uploaded files
function retrieveFiles(dropzone) {
    
    var oldFiles = [];
    
    $('input[name="old_files[]"]').each(function(key, input){
        oldFiles.push($(input).val());
    });

    if(oldFiles.length > 0)
    $.post('/librinfo/media/load', 
        {
            old_files: oldFiles
        }, 
        function (files) {

            for (var i = 0; i < files.length; i++) {

                $('input[name="old_files[]"][value="' + files[i].id + '"]').remove();

                if( files[i].owned == false )
                    insertInput(files[i].id);

                dropzone.emit('addedfile', files[i]);
                dropzone.createThumbnailFromUrl(files[i], generateImgUrl(files[i]));
                dropzone.emit('complete', files[i]);
            }
        }
    );
}

function insertInput(id){
    $('<input type="hidden" name="file_ids[]" value=""/>')
        .val(id)
        .appendTo($('form[role="form"]'));
}

function updateProgressBar(e) {

    if (e === 1) {
        $('.progress').addClass("progress-striped");
        $('.progress-bar').removeClass("progress-bar-success");
        $('.progress-bar').addClass("progress-bar-info");
    } else {
        $('.progress-bar').removeClass("progress-bar-info");
        $('.progress-bar').addClass("progress-bar-success");
        $('.progress').removeClass("progress-striped");
    }
}

function generateImgUrl(file){
    
   return 'data:' + file.mimeType + ';base64,' + file.file;
}