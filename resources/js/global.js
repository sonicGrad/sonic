$('a#chp').on('click', function () {  
    setTimeout(() => {
        $('.modal-backdrop').remove();
    }, 1000);
})
$('.topbar-item').on('click', function () {  
    setTimeout(() => {
        $('.offcanvas-overlay').remove();
    }, 1000);
})
function renameKey ( obj, oldKey, newKey ) {
    obj[newKey] = obj[oldKey];
    delete obj[oldKey];
    return obj;
}

$( "#change-password" ).on('submit',function( event ) {
    event.preventDefault();
    $this = $(this).find('button[type="submit"]');
    oldText =  $this.html();
    data = {
        _token: $("meta[name='csrf-token']").attr("content"),
        'current_pass' : $(this).find('input[name="current_pass"]').val(),
        'password' : $(this).find('input[name="password"]').val(),
        'password_confirmation' : $(this).find('input[name="password_confirmation"]').val()
    }
$.post($("meta[name='BASE_URL']").attr("content") + '/admin/users/change-password', data,
    function(response){
        http.success({ 'message': response.message });
        window.location.reload();
    })
    .fail(function(response){
        http.fail(response.responseJSON, true);
    })
    .always(function(){
        $this.html(oldText);
        $this.prop('disabled', false);
    });
});
$( "#logout" ).on('click',function( event ) {
    data = {
        _token: $("meta[name='csrf-token']").attr("content"),
    }
$.post($("meta[name='BASE_URL']").attr("content") + '/admin/logout', data,
    function(response){
        http.success({ 'message': response.message });
        window.location = $("meta[name='BASE_URL']").attr("content") + "/admin/login";
    })
    .fail(function(response){
        http.fail(response.responseJSON, true);
    })
});

$('form').find( 'select, textarea, input' ).each(function(){
    if( ! $( this ).prop( 'required' )){

    } else {
        
        $(this).closest('div').siblings('label'). append('<span style="color:#be4b49"> *</span>');
    }
});
function imageRemoveAndAppeared(image_type, $id){
    $('form').after(`
    <div class="grid-container"></div>
    `)
    $.get($("meta[name='BASE_URL']").attr("content") + '/admin/' + image_type +'/' + $id, {}, function (response, status) {
        response.forEach(element => {
         $('.grid-container').append(`
        <div class="grid-item"><div class="dz-preview dz-processing dz-image-preview dz-complete image_div">  
             <div class="dz-image">
                 <img data-dz-thumbnail="" alt="er_model.png" src="${element.url}" style="width: 130px;">
             </div>  
             <a class="dz-remove" href="" data-action="remove_image" data-id=${element.name}>Remove file</a>
         </div>
        `);
        });
 
     });
    setTimeout(() => {
        $('a[data-action="remove_image"').on('click', function (e) {  
            e.preventDefault();
            $name = $(this).attr('data-id');
            $this = $(this);
            $.ajaxSetup({
                headers:{
                   'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                }
             })
             $.ajax({
                url: $("meta[name='BASE_URL']").attr("content") + "/admin/categories/image-remove/"+ $name ,
                type: 'DELETE',
                data:{
                  _token: $("meta[name='csrf-token']").attr("content"),
                }
            })
            .done(function(response) {
                http.success({ 'message': response.message });
                $this.parent().remove(); 
            })
            .fail(function(response){
            http.fail(response.responseJSON, true);
            })

            // $.post($("meta[name='BASE_URL']").attr("content") + "/admin/categories/image-remove/"+ $name , {
            //   _token: $("meta[name='csrf-token']").attr("content"),
            // },
            // function (response, status) {
            //     http.success({ 'message': response.message });
            //     $this.parent().remove() 
            // })
            // .fail(function (response) {
            //     http.fail(response.responseJSON, true);
            // })
        });
       }, 1000);
}

function successfullyResponse(response){
    if($myDropzone.files.length  != 0){
        $myDropzone.userId = response.data.id
        // $myDropzone._token =  $("meta[name='csrf-token']").attr("content")
        $myDropzone.processQueue();
        $myDropzone.on("complete", function (file) {
            if ($myDropzone.getUploadingFiles().length === 0 && $myDropzone.getQueuedFiles().length === 0) {
                http.success({ 'message': response.message });
                window.location.reload();
            }
        });
    }else{
        http.success({ 'message': response.message });
        window.location.reload();
    }
}

function myDropzone($type){
   
    $('input[id="btn-submit"]').parent().before(`
            <div class="container">
            <div class="row" style="clear: both;margin: 18px auto; width:70%">
                <div class="col-12">
                <div class="dropzone" id="file-dropzone"></div>
                </div>
            </div>
        </div>`
        )

        Dropzone.options.fileDropzone = {
        userId: '',
        // _token: '',
        autoProcessQueue: false,
        method: 'POST',
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/' + $type+'/image-add',
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        addRemoveLinks: true,
        parallelUploads: 10,
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        
        init: function() {
            var myDropzone = this;
            $myDropzone = myDropzone;
            myDropzone.on('sending', function(file, xhr, formData){
                // formData.append('_token', myDropzone._token);
                formData.append('userId', myDropzone.userId);
                for (var pair of formData.entries()) {
                }
            });
        },
       
      }
}

function changeStatusForAdmin(model) {  
    setTimeout(() => {
        $('button[data-action="changeState"]').on('click', function (e) {  
            e.preventDefault();
            $this = $(this);
            
            $('button[data-action="save-new-status"]').on('click', function (e) {  
                $btn = $(this);
                var buttonText = $this.text();
                    $btn.attr('disabled', true);
                    $btn.html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');
                 $value =  $this.closest('tr').find('select[name="admin_status"]').val()
                $id =$(this).attr("data-id");
                $.ajax({
                    url: $("meta[name='BASE_URL']").attr("content") + '/admin/'+ model+'/change-status/' + $id,
                    type: 'POST',
                    data:{
                        _token: $("meta[name='csrf-token']").attr("content"),
                        status: $value
                    }
                })
                .done(function(response) {
                    http.success({ 'message': response.message });
                    window.location.reload();
                })
                .fail(function(response){
                http.fail(response.responseJSON, true);
                })
                .always(function () {
                    $btn.attr('disabled', false);
                    $btn.html(buttonText);
                });
             });
        
         })
     }, 2000);
}   
$(document).ready(function() {
    $('.js-example-basic-single').select2();
});  

setTimeout(() => {
    $('.disabled-select').attr('disabled', 'disabled-select')
}, 1000);
