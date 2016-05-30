$(function () {
    //验证码
    $('#verify_code').click(function () {
        var self = $(this);
        var url = self.attr('src');
        self.attr("src", url + '/' + Math.random());
        return false;
    });

    $('.datetimepicker').datepicker();

    //ueditor
    //抓取远程图片是否开启,默认true
    UE.getEditor('editor1', {catchRemoteImageEnable: true});//new UE.ui.Editor().render("editor1");
    UE.getEditor('editor2', {catchRemoteImageEnable: false});

    //异步上传图片
    function uploadImg(id, dir, name, fileNumLimit) {
        var setting = {
            formData: {dir: dir, name: name},
            pick: {id: id, multiple: fileNumLimit > 0 ? true : false},
            fileNumLimit: fileNumLimit,
            auto: true,
            swf: swf_url,
            server: server_url,
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            }
        };
        var uploader = WebUploader.create(setting);
        uploader.on('fileQueued', function (file) {
            var $li = $('<div id="' + file.id + '" class="pull-left thumbnail" style="margin-left:5px;">' +
                '<img><input name="' + setting.formData.name + '" type="hidden">' +
                '<div class="uploaddel btn-warning">移除</div></div>'), $img = $li.find('img');
            $(id + '_file_list').append($li);
            uploader.makeThumb(file, function (error, src) {
                if (error) {
                    $img.replaceWith('<span>不能预览</span>');
                    return;
                }
                $img.attr('src', src);
            });
        });
        uploader.on('uploadSuccess', function (file, response) {
            var $li = $('#' + file.id);
            $li.find('input').val(response._raw);
            //移除刚刚上传的图片
            $li.find('.uploaddel').on('click', function () {
                $.post(del_url, {src: './Public/Uploads/' + setting.formData.dir + '/' + response._raw}, function () {
                }, 'json');
                $li.remove();
            });
        });
        uploader.on('uploadError', function (file) {
            alert(file.id + '上传失败');
        });
    }

    //移除已有图片
    $(document).on('click', '.remove-img', function () {
        if (confirm('确定要移除图片吗?')) {
            removeImg($(this).attr('data-src'), $(this).attr('data-name'));
            $(this).parents().removeClass('thumbnail');
            $(this).siblings('input').val('');
            $(this).siblings('img').remove();
            $(this).remove();
        }
    });
    //移除已有的图片
    function removeImg(src, name) {
        $.post(del_url, {src: src, name: name}, function () {
        }, 'json');
    }

    uploadImg('#site_erweima', 'Logo', 'site_erweima[]', 4);
    uploadImg('#default_user', 'User', 'default_user', 1);

    function uploadAttachment(id) {
        var setting = {
            pick: {id: id, multiple: false},
            fileNumLimit: 1,
            auto: true,
            swf: swf_url,
            server: attachment_url,
            accept: {
                title: 'Files',
                extensions: 'zip,rar,doc,docx,xls,xlsx,txt'
            }
        };
        var uploader = WebUploader.create(setting);
        uploader.on('fileQueued', function (file) {
        });
        uploader.on('uploadSuccess', function (file, response) {
            window.location.reload();
        });
        uploader.on('uploadError', function (file) {
            alert(file.id + '上传失败');
        });
    }

    uploadAttachment('#attachemnt');

    //ajax get请求-适用于删除等
    $(document).on('click', '.ajax-get', function (e) {
        e.preventDefault();
        var that = this, url = $(this).attr('action') || $(this).attr('href') || $(this).attr('url');
        if (!url) {
            return false;
        }
        if ($(this).hasClass('confirm')) {
            if (!confirm('确认要执行该操作吗?')) {
                return false;
            }
        }
        $.get(url).success(function (rs) {
            if (rs.status == 1) {
                if ($(that).hasClass('no-refresh')) {//不刷新页面
                    if ($(that).attr('remove')) {
                        $(that).parents($(that).attr('remove')).remove();
                    } else {
                        setTimeout(alert(rs.info), 300);
                    }
                } else if (rs.url) {//刷新到指定页面
                    location.href = rs.url;
                } else {//刷新当前页
                    location.reload();
                }
            } else {
                alert(rs.info);
            }
        });
    });

    window.uploadImg = uploadImg;
    window.uploadAttachment = uploadAttachment;
});