$('.icon-star').click(
    function(){
        var id = $(this).data("id");
        $.post("/admin/article/star", {
            'id'    : id
        }, function(data){
            if(data == 1){
                location.replace(location.href);
            }else{
                location.replace(location.href);
            }
        });
    }
);

$('.openlink').click(
    function(){
        var id = $(this).data("id");
        $.post("/admin/article/link", {
            'id'    : id
        }, function(data){
            console.log(data);
            if(data == 1){
                location.replace(location.href);
            }else{

                alert('已经生成了过外链了！');
            }
        });
    }
);

$('.icon-share').click(
    function(){
        var id = $(this).data("id");
        $.post("/admin/twitter/link", {
            'id'    : id
        }, function(data){
            console.log(data);
            if(data == 1){
                location.replace(location.href);
            }else{
                location.replace(location.href);
            }
        });
    }
);