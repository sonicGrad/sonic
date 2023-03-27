var http = {
    fail: function(response = {}, sweetAlert = false, callback = function(){}){
        if(typeof(toastr) !== "undefined"){
            toastr.remove();
        }

        if(response.message === undefined){
            response.message = "";
        }

        if(response.errors !== undefined){
            var message = '';

            for (var error in response.errors) {
                for (var line in response.errors[error]) {
                    message += "\n" + ($.trim(error) !== "" ? error + ": " : "") + response.errors[error][line];
                }
            }

            response.message = message;
        }

        if(sweetAlert){
            swal({
                title: "فشلت العملية!",
                text: response.message,
                icon: "error",
                button: "موافق",
            })
            .then(() => {
                callback();
            });
            return;
        }
        
        toastr.options.progressBar = true;
        // toastr.options.rtl = true;
        toastr.options.positionClass = "toast-bottom-left";
        toastr.success(response.message, 'فشلت العملية!');
        callback();
    },
    success: function(response = {}, sweetAlert = false, callback = function(){}){
        if(typeof(toastr) !== "undefined"){
            toastr.remove();
        }

        if(response.message === undefined){
            response.message = "";
        }

        if(sweetAlert){
            swal({
                title: "تمت العملية بنجاح!",
                text: response.message,
                icon: "success",
                button: "موافق",
            })
            .then(() => {
                callback();
            });
            return;
        }

        toastr.options.progressBar = true;
        // toastr.options.rtl = true;
        toastr.options.positionClass = "toast-bottom-left";
        toastr.success(response.message, 'تمت العملية بنجاح!');
        callback();
    },
    loading: function(response = {}, callback = function(){}){
        if(response.message === undefined){
            response.message = "";
        }

        toastr.options.positionClass = "toast-bottom-left";
        toastr.info(response.message, 'يرجى الانتظار...', {timeOut: 0, extendedTimeOut: 0});
        callback();
    },
    checkRequiredFelids: function ($this) {  
        $this.find( 'select, textarea, input' ).each(function(){
            if( ! $( this ).prop( 'required' )){
    
            } else {
                if ( ! $( this ).val() ) {
                    fail = false;

                    $(this).closest('div').css({
                        'position': 'relative'
                    })
                    $(this).before('<span class="fa fa-exclamation-circle"></span>');
                    $(this).siblings('span').css({
                        'position' : "absolute",
                        'top' : '10px',
                        'right' : '20px',
                        'color' : '#be4b49'
                    })
                    $(this).css('border', '0.5px solid #be4b49')
                    $(this).siblings( ".invalid-feedback" ).text('هذا الحقل مطلوب').css('color', '#be4b49')
                }
    
            }
        });
    }
}