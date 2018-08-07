function checkemail() {
    var email = $('#email').val();
    if(!email.match(/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/))
    {
        alert("格式不正确！请重新输入");
        $("#email").focus();
    }
}

// $('.validate').click(
//     function () {
//         var email = $('#email').val();
//         if(email == ''){
//             return false;
//         }
//     }
// );