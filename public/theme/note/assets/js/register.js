function setPassword() {
    var password = $('#password').val();
    var repassword = $('#repassword').val();
    if (password == '' || repassword == ''){
        $('.btn-primary').text('请非常认真的输入密码!');
        return false;
    }
    if (repassword == password) {
        return true;
    }
    else{
        $('.btn-primary').text('两次输入的密码不一致,请重新输入!');
        return false;
    }

}