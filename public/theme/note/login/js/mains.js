/**
 * Created by nobita on 3/22.
 */

// $('#validate').click(function () {
//     var mail = $('#email').val();
//     $.post("index.php?p=Admin&c=login&a=updateuser",
//         {
//             mail:mail
//         },
//         function(data,status){
//         var code = data;
//             if( code == '1' ){
//                 $('.tips').text('验证码已经发送到邮箱！');
//                 // $('.setp1').hide(300);
//                 // $('.setp2').show(300);
//             }
//             else{
//                 $('.tips').text('请输入正确的邮箱地址！');
//             }
//         });
// });

$('.submut_set').click(function () {
    var password = $('#password').val();
    var repassword = $('#repassword').val();
    if (password !== repassword){
        $('.retips').text('两次输入密码不一致！');
        return false;
    }
    else{
        return true;
    }
});

function validate() {
    var mail = $('#email').val();
    $.post("index.php?p=Admin&c=login&a=updateuser",
        {
            mail:mail
        },
        function(data,status){
            var code = data;
            if( code == '1' ){
                $('.tips').text('验证码已经发送到邮箱！');
                // $('.setp1').hide(300);
                // $('.setp2').show(300);
            }
            else{
                $('.tips').text('请输入正确的邮箱地址！');
            }
        });
}

function checkuser() {
    var username = $('#username').val();
    $.post("index.php?p=Admin&c=login&a=checkuser",
        {
            username:username
        },
        function (data) {
            var code = data;
            if( code == '1' ){
                $('.tips').text('用户名已经被注册！');
            }
            else{
                $('.tips').text('用户名还没有被注册！');
            }
        }
    );
}

function checkemail() {
    var email = $('#email').val();
    $.post("index.php?p=Admin&c=login&a=checkemail",
        {
            email:email
        },
        function (data) {
            var code = data;
            if( code == '1' ){
                $('.tips').text('邮箱地址已经被注册！');
            }
            else{
                $('.tips').text('邮箱地址还没有被注册！');
            }
        }
    );
}