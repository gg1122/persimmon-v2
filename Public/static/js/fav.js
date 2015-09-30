$(function () {
    /**
     * Submit Fav
     */
    $('#submit').bind('click', function () {
        postData($(this), '', 2);
        return false;
    });
});


/** 启动收藏窗口
 javascript:void((function () {
        var title = document.title;
        var source = window.location.href;
        var iWidth = 700;
        var iHeight = 500;
        var iTop = (window.screen.availHeight - 30 - iHeight) / 2;
        var iLeft = (window.screen.availWidth - 10 - iWidth) / 2;
        var postUrl = encodeURI("http://ii.cong5.net/post.html?title=" + title + '&source=' + source);
        window.open(postUrl, "添加收藏", 'width=' + iWidth + ',height=' + iHeight + ',top=' + iTop + ', left=' + iLeft);
    })());
 */


/**
 * Post 数据
 * @param btnThis   [按钮自身的Dom]
 * @param second    [多少秒后跳转]
 * @returns {boolean}
 */
function postData(btnThis, second) {
    var second = second ? second : 1;
    var formID = btnThis.parents().find('form').attr('id');
    var formUrl = 'http://apii.cong5.net/v2/fav/post';
    var queryString = $('#' + formID).formSerialize();
    var loading;
    $.ajax({
        url: formUrl,
        type: "POST",
        data: queryString,
        beforeSend: function () {
            loading = layer.load(2, {time: 10 * 1000});
        },
        success: function (data) {
            layer.close(loading);
            if (data.status == 1) {
                layer.msg(data.info);
                if (formID == 'login') {
                    jump(1);
                } else {
                    closeWindow(1);
                }
            } else {
                layer.msg(data.info);
            }
        }
    });
    return false;
}

/**
 * 跳转URL
 * @param url [跳转的URL]
 * @param s [多长时间跳转]
 */
function closeWindow(s) {
    var s = s ? s : 1;
    setTimeout("window.close()", s * 1000);
    return false;
}

/**
 * 跳转URL
 * @param url [跳转的URL]
 * @param s [多长时间跳转]
 */
function jump(s) {
    var s = s ? s : 1;
    setTimeout("location.reload()", s * 1000);
    return false;
}