var corp = {

    init: function () {
        corp.subscribe();
        corp.subscribe_btn();
        corp.signup_button();
        corp.login_button();
        corp.contact_us();
        corp.reset_password();
    },

    // Subscribe start
    subscribe: function(){
        $(".form-subscribe").on('submit',function (e) {
            // Prevent form submitting
            e.preventDefault();
            //var data = $('#form-subscribe').serialize();
            var obj = $(this);
            var data = obj.serialize();
            var action = base_url+'subscribe/store';
            var response = AjaxRequest.fire(action, data);
            // success
            if (response.status) {
                $('#subs-email').val('');
                $('#subs-email-inner').val('');
            }
            return false;
        });
    },
    reset_password : function() {
            $('.btn-send1').on('click', function () {
            
            var obj = $(this).closest("form");
            var data = obj.serialize();
            var url = base_url+'account_front/forgotpasswordemail';
            $.post(url , data , function(response ){
                if(response.status == 1){
                    AdminToastr.success('Email Sent Please check Your E-mail','Success');
                    setTimeout(function(){ 
                            // window.location = "login";
                            location.reload();
                        }, 4000);
                }
                else{
                    AdminToastr.error(response.txt,'Error');
                }
            },"json");

            // Add return
            return false;
        });
    },
    // Subscribe end

    // Subscribe start
    Favourites_user: function(obj){
        var user_id = obj.attr('data-user');
        console.log(user_id);
        var data = {user_id : user_id};
        var action = base_url+'dashboard/custom/ajax_add_to_favourites';
        var response = AjaxRequest.formrequest(action, data);
        // success
        if (response.status) {
            AdminToastr.success(response.txt,'Success');
            obj.toggleClass('active');
        }
        else{
            AdminToastr.error(response.txt,'Error');
        }
        return false;
    },
    // Subscribe end

    // Subscribe anchor start
    subscribe_btn: function(){
        $(".btn-subscribe").on('click',function (e) {
            $("#form-subscribe").submit();
        });
    },
    // Subscribe anchor end
    //signup
    signup_button: function(){

        $('#signup-form').on('submit', function (e) {
            // Prevent form submit
            e.preventDefault();
            // Get form data
            var data = new FormData(document.getElementById("signup-form"));
            // Get post url
            var url = base_url+'account_front/save_signup';
            // Submit action
            var response = FileUploadScript.fire(url, data,'json',1);
            // if(response.status == 1){
            //     AdminToastr.success(response.txt);
            //     setInterval(function(){
            //         window.location = base_url+'account/find-tutor';
            //     } , 2000);
            // }
            // else{
            //     AdminToastr.error(response.txt);
            // }
            // Add return
            return false;
        });
    },
    login_button: function(){

        $('#login_btn-send').on('submit', function (e) {
            // Prevent form submit
            e.preventDefault();
            var obj = $("#login_btn-send");
            // Get form data
            var data = obj.serialize();
            // Get post url
            var url = base_url+'account_front/do_login';
            // Submit action
            var response = AjaxRequest.fire(url, data);
            if(response.status){
                setInterval(function(){

                    window.location = base_url;
                    // +'account/find-tutor';
                    // location.reload();

                } , 2000);
            }
            // Add return
            return false;
        });
    },
    // Contact us form start
    contact_us: function(){
        $('#contact-form').on('submit', function (e) {
            Loader.show();
            setTimeout(function () {
                // Prevent form submit
                e.preventDefault();
                var obj = $("#contact-form");
                // Get form data
                var data = obj.serialize();
                // Get post url
                var url = base_url+'contact_us/store';
                // Submit action
                var response = AjaxRequest.formrequest(url, data);
                if(response.status){
                    AdminToastr.success(response.txt);
                    obj[0].reset();
                    // location.reload();
                }
                else{
                    AdminToastr.error(response.txt);
                }
                // Add return
                return false;
            },1000);
        });
    }
    // Contact us form end
};

$(window).load(function(){
    corp.init();
})
$(document).ready(function(){
    corp.init();
});
// $('.FavouriteUser').click(function(){
//     corp.Favourites_user($(this));
// });