function timestampToTime(timestamp) {
    var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
    Y = date.getFullYear() + '-';
    M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
    D = date.getDate() + ' ';
    h = date.getHours() + ':';
    m = date.getMinutes() + ':';
    s = date.getSeconds();
    return Y+M+D+h+m+s;
}
function geturlvar(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (pair[0] == variable) {
            return pair[1];
        }
    }
    return (false);
}
var opencreate = geturlvar('opensidebar');
if (opencreate == 'open'){
    $('#qv-form-center').addClass("reveal");
    $('body').append('<div class="app-backdrop backdrop-quickview" data-target="#qv-form-center"></div>');
}
function showtips(value) {
    $('.header-tips').text(value);
    $('.show-tips').show(500);
    setTimeout(function(){$('.show-tips').hide(500)},2000);
}
function showreload(value) {
    $('.header-tips').text(value);
    $('.show-tips').show(500);
    setTimeout(function(){location.reload();},2000);
}

$('.text-truncate').click(
    function(){
        var id = $(this).data("id");
        $.post("/admin/article/show", {
            'id'    : id
        }, function(data){
            var item = JSON.parse(data)
            $('.content').html(item.content);
            $('.article-title').html(item.title);
            $('.create-times').html(timestampToTime(item.create_time));
        });
    }
);

$('.a-notice').click(
    function () {
        var id = $(this).data("id");
        $.post("/admin/notice/show", {
            'id'    : id
        }, function(data){
            var item = JSON.parse(data)
            $('.notice-content').html(item.content);
            $('.notice-title').html(item.title);
        });
    }
);

$('.editcategory').click(
    function () {
        var id = $(this).data("id");
        $.post("/admin/category/edit",{
            'id' :id
        },function (data) {
            if (data){
                $('.categoryid').val(data.id);
                $('.categoryname').val(data.name);
                $('.categoryslug').val(data.slug);
            }
            else{
                showtips('请求出错，请重试!')
            }
        });
    }
);


function checkAll() {
    var all=document.getElementById('all');//获取到点击全选的那个复选框的id
    var one=document.getElementsByName('checkname[]');//获取到复选框的名称
//因为获得的是数组，所以要循环 为每一个checked赋值
    for(var i=0;i<one.length;i++){
        one[i].checked=all.checked; //直接赋值不就行了嘛
    }
}


$('.ti-close').click(
    function () {
        $(".content").remove();
        setTimeout(function(){$(".quickview-block .form-group").prepend("<div class='content'></div>");},10);
        $('.ps-scrollbar-y-rail').css('top','0px');
    }
);

$('.editnote').click(
    function () {
        var id = $(this).data('id');
        $.post('/admin/notepad/edit',
            {
                'id' : id
            },
            function (data) {
                if (data){
                    $('.notepadid').val(data.id);
                    $('.notepadcontent').val(data.content);
                }
                else{
                    showtips('请求出错，请重试!')
                }
            }
        );
    }
);

function action(url,id,type,dbname,success,error) {
    $.post(url,
        {
            'id':id,
            'type':type,
            'dbname':dbname
        },function (data) {
            console.log(data);
            if(data == 1){
                showreload(success);
            }else{
                showtips(error);
            }
        }
    );
}