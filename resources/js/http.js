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
                title: "Process Fail",
                text: response.message,
                icon: "error",
                button: "OK",
            })
            .then(() => {
                callback();
            });
            return;
        }
        
        toastr.options.progressBar = true;
        // toastr.options.rtl = true;
        toastr.options.positionClass = "toast-bottom-left";
        toastr.success(response.message, 'Process Fail');
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
                title: "Process Success",
                text: response.message,
                icon: "success",
                button: "OK",
            })
            .then(() => {
                callback();
            });
            return;
        }

        toastr.options.progressBar = true;
        // toastr.options.rtl = true;
        toastr.options.positionClass = "toast-bottom-left";
        toastr.success(response.message, 'Process Success');
        callback();
    },
    loading: function(response = {}, callback = function(){}){
        if(response.message === undefined){
            response.message = "";
        }

        toastr.options.positionClass = "toast-bottom-left";
        toastr.info(response.message, 'Please Wait...', {timeOut: 0, extendedTimeOut: 0});
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
                    $(this).siblings( ".invalid-feedback" ).text('This Element is Required').css('color', '#be4b49')
                }
    
            }
        });
    }
}