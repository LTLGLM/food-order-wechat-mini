$(function () {

    $(".input").focus(function () {
        $(this).attr("class", "inputOn");
    }).blur(function () {
        $(this).attr("class", "input");
    });

    $("#dbhost").focus();
});

function CheckForm() {

    var format = /^[a-zA-Z0-9_@!.-]+$/;

    if ($("#dbhost").val() == "") {
        alert("请输入数据库服务器！");
        $("#dbhost").focus();
        return false;
    }

    if ($("#dbname").val() == "") {
        alert("请输入数据库名！");
        $("#dbname").focus();
        return false;
    }

    if (!format.exec($("#dbname").val())) {
        alert("数据库名非法，请使用 [a-zA-Z0-9_@!.-] 范围内字符。");
        $("#dbname").focus();
        return false;
    }

    if ($("#dbuser").val() == "") {
        alert("请输入数据库用户！");
        $("#dbuser").focus();
        return false;
    }

    if ($("#admin_account").val() == "") {
        alert("请输入后台管理员账号！");
        $("#admin_account").focus();
        return false;
    }

    if ($("#admin_password").val() == "") {
        alert("请输入后台管理员密码！");
        $("#admin_password").focus();
        return false;
    }

    if ($("#admin_password").val().length < 6) {
        alert("管理员密码长度不能小于 6 位。");
        $("#admin_password").focus();
        return false;
    }

    if ($("#cpwd").val() == "false") {
        $.ajax({
            url: '/install/index.php',
            data: {
                s: 63832,
                dbhost: $("#dbhost").val(),
                dbuser: $("#dbuser").val(),
                dbpwd: $("#dbpwd").val(),
            },
            type: 'get',
            dataType: 'html',
            success: function (data) {
                if (data == 'true') {
                    $('#cpwdTxt').html('<span class="correct">可用</span>');
                    $('#cpwd').val("true");
                    document.form.submit();
                    return;
                } else {
                    $('#cpwdTxt').html('<span class="error">不可用</span>');
                    $("#dbpwd").focus();
                    $('#cpwd').val("false");
                    return false;
                }
            }
        });
    } else {
        document.form.submit();
        return;
    }
}

function CheckPwd() {
    $.ajax({
        url: '/install/index.php',
        data: {
            s: 63832,
            dbhost: $("#dbhost").val(),
            dbuser: $("#dbuser").val(),
            dbpwd: $("#dbpwd").val(),
        },
        type: 'get',
        dataType: 'html',
        success: function (data) {
            if (data == 'true') {
                $('#cpwdTxt').html('<span class="correct">可用</span>');
                $('#cpwd').val("true");
            } else {
                $('#cpwdTxt').html('<span class="error">不可用</span>');
                $('#cpwd').val("false");
            }
        }
    });
}
