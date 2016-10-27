$(document).ready(function () {
    
    setupDropzone();
});

// Returns the current action (show/list/edit) from url
function getAction() {

    return window.location.href.split("/").pop();
}

//Get current object id from url
function getOwnerId() {

    var splitUrl = window.location.href.split("/");

    splitUrl.pop();

    return splitUrl.pop();
}

function getOwnerType() {

    var splitUrl = window.location.href.split("/");

    splitUrl.pop();
    splitUrl.pop();

    return splitUrl.pop();
}

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

    var tempId = generateUUID();

    //append the temporary id to the form so it can be retrieved in CreateAction
    $('form[role="form"]').append('<input type="hidden" name="temp_id" value="' + tempId + '"/>');

    //register callback on dropzone send event
    dropzone.on("sending", function (file, xhr, formData) {
        //add the temporary id to the ajax call formData
        formData.append("temp_id", $('input[name="temp_id"]').val());
    });

    //prevent submitting of the form when add files button is clicked
    $('.add_files').click(function (e) {

        e.preventDefault();
    });

//    $('button.upload').click(function (e) {
//        e.preventDefault();
//        dropzone.processQueue();
//    });

    dropzone.on("queuecomplete", function (progress) {

        updateProgressBar(0);
    });

    //handles removal of already uploaded files
    dropzone.on("removedfile", function (file) {

        var tempId = $('input[name="temp_id"]').val();

        $.get('/librinfo/media/remove/' + file.name + '/' + file.size + '/' + tempId, function (response) {

            console.log(response);
        });
    });


    dropzone.on("addedfile", function (file) {
        
        //file size validation
        if (file.size > 5 * 1024 * 1024) {

            dropzone.cancelUpload(file);
            dropzone.emit('error', file, 'Max file size(5mb) exceeded');
        }

        updateProgressBar(1);

        //replace generated tempId with existing files tempId
        if (getAction() != 'create') {
            $('input[name="temp_id"]').attr("value", file.tempId);
        }

        //add file info to html tag for ajax call
        $('button.inline').attr('file_name', file.name);
        $('button.inline').attr('file_size', file.size);
    });

    //retrieve existing attachments in edit action
    if (getAction() != 'create')
        retrieveFiles(dropzone);
}

function retrieveFiles(dropzone) {

    $.get('/librinfo/media/load/' + getOwnerId() + '/' + getOwnerType(), function (files) {
        
        for (var i = 0; i < files.length; i++) {
            dropzone.emit('addedfile', files[i]);
            dropzone.createThumbnailFromUrl(files[i], generateImgUrl(files[i]));
            dropzone.emit('complete', files[i]);
        }
        
        if(getAction() != 'edit'){
            var tempIdInput = $('input[name="temp_id"]');
            var newTempId = generateUUID();
            
            $.post('/librinfo/media/update',
                {
                    'temp_id': tempIdInput.val(),
                    'new_temp_id': newTempId,
                    'owner_type' : getOwnerType()
                },
                function(data){
                    tempIdInput.val(data);
                }
            );
        }
    });
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

function generateUUID() {

    var d = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = (d + Math.random() * 16) % 16 | 0;
        d = Math.floor(d / 16);
        return (c == 'x' ? r : (r & 0x7 | 0x8)).toString(16);
    });
    return uuid.toUpperCase();
}

function generateImgUrl(file){
   return 'data:' + file.mimeType + ';base64,' + file.file;
}