/**
 * Created by lyndon on 17-3-10.
 */
// document.write("<script src='/assets/js/jquery.formautofill.min.js'></script>");
$(function () {
    var filldata = JSON.parse($('#filldata').attr('data-value'));
    var $fileInputs = $('input[type="file"]');
    if ($fileInputs.length > 0) {
        $.each($fileInputs, function (i, item) {
            var $itemName = $(item).attr('name');
            var $fileUrl = filldata[$itemName];
            if ($fileUrl) {
                $(item).before('<a href="' + $fileUrl + '">上传的文件</a>');
            }
            delete filldata[$itemName];
        });
    }
    var autofillOpts = {
        restrict: false,
        findbyname: true
    };
    try {
        reanderedForm.autofill(filldata, autofillOpts);
    } catch (e) {
        console.log(e)
    }
});
