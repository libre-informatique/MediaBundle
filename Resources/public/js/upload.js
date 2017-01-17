
var setupDropzones = function() {
    
    var dropzones = $('[data-librinfo-dropzone]');
    
    if(dropzones.length > 0){
    
        Dropzone.autoDiscover = false;
        
        $(dropzones).each(setupDropzone);
    }
};

var setupDropzone = function(key, instance){
    
    //template for file previews
    var template = Mustache.render($('#dropzone-template').html());
    var data = $(instance).data('librinfoDropzone');
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
    var dropzone = new Dropzone('#' + data.id, options);

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

        insertInput(result, 'add_files[]', dropzone);
    });

    //Reset progress bar when done uploading
    dropzone.on("queuecomplete", function (progress) {

        updateProgressBar(0);
    });

    //Removal of already uploaded files
    dropzone.on("removedfile", function (file) {

        var id = $(file.previewElement).data('file-id');

        $('input#' + id).remove();
        insertInput(id, 'remove_files[]', dropzone);

        $.get('/librinfo/media/remove/' + id, function (response) {

            console.log(response);
        });
    });

    retrieveFiles(dropzone, data.id);
};

// Retrieval of already uploaded files
var retrieveFiles = function(dropzone, dropzoneId) {
    
    var oldFiles = [];
    
    $('input[name="load_files[]"][data-dropzone-id="' + dropzoneId + '"]').each(function(key, input){
        oldFiles.push($(input).val());
    });

    if(oldFiles.length > 0)
    $.post('/librinfo/media/load', 
        {
            load_files: oldFiles
        }, 
        function (files) {

            for (var i = 0; i < files.length; i++) {

                $('input[name="load_files[]"][value="' + files[i].id + '"]').remove();

                if( files[i].owned == false )
                    insertInput(files[i].id, 'add_files[]', dropzone, dropzoneId);

                dropzone.emit('addedfile', files[i]);
                dropzone.createThumbnailFromUrl(files[i], generateImgUrl(files[i]));
                dropzone.emit('complete', files[i]);
            }
        }
    );
};

var insertInput = function(id, name, dropzone, dropzoneId){
    
    $('<input type="hidden"/>')
        .attr('id', id)
        .prop('name', name)
        .data('dropzone-id', dropzoneId)
        .val(id)
        .appendTo($(dropzone.element).closest('form'));
};

var updateProgressBar = function(e) {

    if (e === 1) {
        $('.progress').addClass("progress-striped");
        $('.progress-bar').removeClass("progress-bar-success");
        $('.progress-bar').addClass("progress-bar-info");
    } else {
        $('.progress-bar').removeClass("progress-bar-info");
        $('.progress-bar').addClass("progress-bar-success");
        $('.progress').removeClass("progress-striped");
    }
};

var generateImgUrl = function(file){
    
   return 'data:' + file.mimeType + ';base64,' + file.file;
};

$(document).ready(setupDropzones);
$(document).on('sonata-admin-setup-list-modal sonata-admin-append-form-element', setupDropzones);