
var AdminToastr = function () {

    return {

        success: function (msg , title , options) {
            this.show(msg , title , "success" , options);
        },

        info: function (msg , title , options) {
            this.show(msg , title , "info" , options);
        },

        warning: function (msg , title , options) {
            this.show(msg , title , "warning" , options);
        },

        error: function (msg , title , options) {
            this.show(msg , title , "error" , options);
        },

        show: function (msg , title , type , options) {

            if(!options)
                var options = {} ;

            toastr.options.positionClass = options.positionClass || "toast-bottom-right";

            if (options.showDuration) {
                toastr.options.showDuration = options.showDuration;
            }

            if (options.hideDuration) {
                toastr.options.hideDuration = options.hideDuration;
            }

            if (options.timeOut) {
                toastr.options.timeOut = options.timeOut;
            }

            if (options.extendedTimeOut) {
                toastr.options.extendedTimeOut = options.extendedTimeOut;
            }

            if (options.showEasing) {
                toastr.options.showEasing = options.showEasing;
            }

            if (options.hideEasing) {
                toastr.options.hideEasing = options.hideEasing;
            }

            if (options.showMethod) {
                toastr.options.showMethod = options.showMethod;
            }

            if (options.hideMethod) {
                toastr.options.hideMethod = options.hideMethod;
            }

            var $toast = toastr[type](msg, title); // Wire up an event handler to a button in the toast, if it exists
            $toastlast = $toast;

            // body...
        }
    };

}();

// Admin Script Contains Helpers
var AjaxRequest = function () {

    var ajaxParams = {} ;

    return {

        //main function to initiate the module
        init: function () {
            // Right now, nothing to autoload
            return true;
            //initPickers();
        },

        setAjaxParam: function(name, value) {
            ajaxParams[name] = value;
        },
        getAjaxParam: function() {
            return ajaxParams;
        },

        load: function(url, data , target_obj) {
            response = this.fire(url, data) ;
            if(response.status == 1){
                target_obj.html(response.txt);
            }
        },

        fire: function(url, data) {
            var to_return = "";

            jQuery.ajax({
                url: url,
                type: "POST",
                data: data,
                async: false,  // Has to be false to be able to return response
                dataType: "json",  // Has to be false to be able to return response
                success: function(response) {
                    Loader.hide();

                    if(response.status == 0){
                        AdminToastr.error(response.txt,'Error');
                    }
                    else if(response.status == 1){
                        AdminToastr.success(response.txt,'Success');
                    }
                    to_return = response;
                },
                beforeSend: function()
                {
                    Loader.show();
                }
            });  // JQUERY Native Ajax End

            //this.response = to_return;
            return to_return;
            //return false;

        }, // End of ajaxRequest
        formrequest: function(url, data) {
            var to_return = "";

            jQuery.ajax({
                url: url,
                type: "POST",
                data: data,
                async: false,  // Has to be false to be able to return response
                dataType: "json",  // Has to be false to be able to return response
                success: function(response) {
                    Loader.hide();
                    to_return = response;
                },
                beforeSend: function()
                {
                    Loader.show();
                }
            });  // JQUERY Native Ajax End

            this.response = to_return;
            return to_return;

        } // End of ajaxRequest

    }; // End of class return

}(); // End of AdminScript


var FormRequest = function () {

    var ajaxParams = {} ;

    return {

        //main function to initiate the module
        init: function () {
            // Right now, nothing to autoload
            return true;
            //initPickers();
        },

        setAjaxParam: function(name, value) {
            ajaxParams[name] = value;
        },

        load: function(url, data , target_obj) {
            response = this.fire(url, data) ;
            if(response.status == 1){
                target_obj.html(response.txt);
            }
        },

        fire: function(url, data) {
            var to_return = "";

            jQuery.ajax({
                url: url,
                type: "POST",
                data: data,
                async: false,  // Has to be false to be able to return response
                dataType: 'json',  // Has to be false to be able to return response
                success: function(response) {
                    to_return = response;
                    Loader.hide();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    Loader.hide();
                    alert("Error: " + errorThrown);
                },
                beforeSend: function()
                {
                    Loader.show();
                }
            });  // JQUERY Native Ajax End

            //this.response = to_return;
            return to_return;
            //return false;

        } // End of ajaxRequest

    }; // End of class return

}(); // End of AdminScript


var FileUploadScript = function () {

    var ajaxParams = {} ;

    return {

        //main function to initiate the module
        init: function () {
            // Right now, nothing to autoload
            return true;
            //initPickers();
        },

        setAjaxParam: function(name, value) {
            ajaxParams[name] = value;
        },

        load: function(url, data , target_obj) {
            response = this.fire(url, data) ;
            if(response.status == 1){
                target_obj.html(response.txt);
            }
        },

        fire: function(url, data, type, is_reload,redirect_url = null) {
            var to_return = "";

            jQuery.ajax({
                url: url,
                type: "POST",
                data: data,
                enctype: 'multipart/form-data',
                processData: false,  // tell jQuery not to process the data
                contentType: false,   // tell jQuery not to set contentType
                dataType: type,
                success: function(response) {

                    Loader.hide();

                    if(response.status == 0){
                        AdminToastr.error(response.txt,'Error');
                    }
                    else if(response.status == 1){
                        AdminToastr.success(response.txt,'Success');
                        if(is_reload){
                            if(redirect_url!=null){
                                window.location = redirect_url;
                            }
                            else{
                                location.reload();
                            }
                        }
                    }

                },
                beforeSend: function()
                {
                    Loader.show();
                }
            });  // JQUERY Native Ajax End
            return false;
            //this.response = to_return;
            // return to_return;

        }, // End of ajaxRequest

        custom: function(url, data, type, is_reload) {
            var to_return = "";

            jQuery.ajax({
                url: url,
                type: "POST",
                data: data,
                enctype: 'multipart/form-data',
                processData: false,  // tell jQuery not to process the data
                contentType: false,   // tell jQuery not to set contentType
                dataType: type,
                success: function(response) {

                    Loader.hide();

                    if(response.status == 0){
                        AdminToastr.error(response.txt,'Error');
                    }
                    else if(response.status == 1){
                        to_return = response;
                        AdminToastr.success(response.txt,'Success');
                        if(is_reload){
                            location.reload();
                        }

                        /*$('#paypal_return').val(response.data.success_url);
                        $('#paypal_custom').val(response.data.custom);
                        $('#paypal_notify_url').val(response.data.notify_url);
                        $('#paypal_cancel_return').val(response.data.cancel_url);*/

                        //$('input[name=amount_1]').val($('.adshare-checkout-total').text());


                        // Set order ID
                        $('input[name=x_invoice_num]').val(response.data.custom);
                        $('input[name=x_fp_sequence]').val(response.data.sequence);
                        $('input[name=x_fp_timestamp]').val(response.data.time);
                        $('input[name=x_fp_hash]').val(response.data.x_fp_hash);
                        // SUbmit paypal transaction
                        $('#payment_form_submit').submit();

                    }

                },
                beforeSend: function()
                {
                    Loader.show();
                }
            });  // JQUERY Native Ajax End
            //this.response = to_return;
            //return to_return;
            //this.response = to_return;
            // return to_return;

        } // End of ajaxRequest

    }; // End of class return

}(); // End of AdminScript

 // End of AdminScript

var Loader = function () {

    return {

        show: function () {
            jQuery("#preloader").show();
        },

        hide: function () {
            jQuery("#preloader").hide();
        }
    };

}();