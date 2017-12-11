/**
 * Created by Lyndon on 2016/11/25.
 */
var fid = window.location.href.split('/')[5];
var $table = $('#table');
var $reviewTimes = parseInt($table.attr('data-reviewtimes'));
var $status = ['初审', '复审', '三审', '四审', '五审', '六审', '七审', '八审', '九审', '十审'];


function tableInit($columns) {
    $columns = jQuery.parseJSON($columns.replace(/\&quot\;/g, '"'));
    $table.bootstrapTable({
        columns: $columns,
        url: '/web/form/' + fid + '/datadata' + window.location.search,
        method: 'get',
        /*queryParams:function(params) {
         return params;
         },*/
        escape: true,
        striped: true,
        pagination: true,
        sortStable: true,                     //是否启用排序
        sortOrder: "asc",                   //排序方式
        sidePagination: "client",
        pageNumber: 1,                       //初始化加载第一页，默认第一页
        pageSize: 10,                       //每页的记录行数（*）
        showPaginationSwitch: true,
        pageList: "[10, 25, 50, 100, ALL]",        //可供选择的每页的行数（*）
        search: true,
        searchOnEnterKey: true,

        filterControl: true,
        filterShowClear: true,

        // showFilter:true,//不兼容

        // advancedSearch:true,//不兼容
        // idTable:'table',
        // formatAdvancedSearch:'高级搜索',
        // formatAdvancedCloseButton:'关闭',

        showColumns: true,
        showRefresh: true,
        clickToSelect: true,
        toolbar: '#toolbar',
        showToggle: true,
        idField: '_id',
        uniqueId: '_id',
        detailView: true,
        detailFormatter: function (index, row) {
            var str = '';
            $.each($columns, function (i, item) {
                if (typeof(row[item.field]) != "undefined" && typeof(item.title) != "undefined") {
                    str += '<p><b>' + item.title + ':</b> ' + row[item.field] + '</p>';
                }
            });
            return str;
        },
        silentSort: false,
        showExport: true,
        exportDataType: "basic",
        responseHandler: function (res) {
            $.each(res, function (i, row) {
                //状态修改为中文
                var $now_status = row.status;
                row.st = $now_status;
                row.status = status2zh($now_status, $reviewTimes);
                //url->超链接
                var re = /(http:\/\/[\w.\/]+)(?![^<]+>)/gi;
                $.each($columns, function (i, item) {
                    var $value = row[item.field];
                    if($value && typeof $value=='string'){
                        row[item.field] = $value.replace(re, "<a href='$1'>$1</a>");
                    }
                });

            });
            return res;
        },
        rowStyle: function (row, index) {
            var strclass = "";
            if (row.status.indexOf('通过') != -1) {
                strclass = 'success';
            }
            else if (row.status.indexOf('拒绝') != -1) {
                strclass = 'danger';
            }
            else {
                return {};
            }
            return {classes: strclass}
        }
    });
}

function operateFormatter(value, row, index) {
    return [
        '<a class="form-open" href="javascript:void(0)" title="打开">',
        '<i class="glyphicon glyphicon-open"></i>',
        '</a>  ',
        '<a class="review" href="javascript:void(0)" title="审批">',
        '<i class="glyphicon glyphicon-cog"></i>',
        '</a>  ',
        '<a class="remove" href="javascript:void(0)" title="隐藏">',
        '<i class="glyphicon glyphicon-remove"></i>',
        '</a>'
    ].join('');
}
function loadOldReview($fdId) {
    $.get('/web/review/getReviews', {fd_id: $fdId}, function (data) {
        var $oldReview = $('#old-review');
        $oldReview.empty();
        $.each(data.reviews, function (i, item) {
            var $color = item.result == 1 ? 'success' : 'danger';
            $oldReview.append('<div class="panel panel-' + $color + '">' +
                '<div class="panel-heading">' + $status[item.status] + '·' + item.username + ' ' +
                '<small style="color: #5d6d7e">' + item.updated_at + '</small></div>' +
                '<div class="panel-body"> ' +
                '<p><span class="glyphicon glyphicon-pencil"></span> ' + item.evaluation + '</p> ' +
                '<p><span class="glyphicon glyphicon-comment"></span> ' + item.remark + '</p> ' +
                '</div>' +
                '</div>');
        });
        var $oldRemark = $('#old-remark');
        $oldRemark.empty();
        $.each(data.remarks, function (i, item) {
            $oldRemark.append('<div class="well">' +
                '<p>' + item.username + ' · ' +
                '<small style="color: #5d6d7e">' + item.updated_at + '</small></p>' +
                '<p>' + item.remark + '</p> ' +
                '</div>');
        });
    });
}
window.operateEvents = {
    'click .form-open': function (e, value, row, index) {
        window.open('/pub/info/' + row._id);
    },
    'click .review': function (e, value, row, index) {
        loadOldReview(row._id);
        $('#data-id').val(row._id);
        var $eachProgressWidth = parseInt(100 / $reviewTimes);
        var $progress = $('#progress');
        $progress.empty();
        if (row.st < 0 || row.st >= $reviewTimes) {
            var $primary = (-row.st - 1) * $eachProgressWidth;
            var $danger = $eachProgressWidth;
            $progress.append('<div class="progress-bar" style="width: ' + $primary + '%;"></div>');
            if (row.st < 0) {
                $progress.append('<div class="progress-bar progress-bar-danger" style="width: ' + $danger + '%;"></div>');
            } else {
                $progress.append('<div class="progress-bar progress-bar-success" style="width: ' + $danger + '%;"></div>');
            }
            $('.evaluation').hide();
            $('#review-pass').hide();
            $('#review-refuse').hide();
        } else {
            $('.evaluation').show();
            $('#review-pass').show();
            $('#review-refuse').show();
            $progress.append('<div class="progress-bar" style="width: ' + row.st * $eachProgressWidth + '%;"></div>');
            //是否需要自定义微信通知内容？获取微信模板定义
            var $noticeResultLang = {pass: '通过', refuse: '拒绝'};
            var $wechatNoticeDiv = $('.wechatNotice');
            $wechatNoticeDiv.empty();
            $.get('/web/form/' + fid + '/getNotice', {
                type: $wechatNoticeDiv.attr('data-type'),
                way: $wechatNoticeDiv.attr('data-way'),
                status: row.st + 1    //状态0->第一次审核
            }, function (data) {
                $.each(data, function (i, r) {
                    $.each(r.data, function (k, kd) {
                        if (kd.value.indexOf('{CUSTOM}') != -1) {
                            $wechatNoticeDiv.append(
                                '<div class="form-group">' +
                                '<label class="control-label" for="wechat-notice">微信通知——' + $noticeResultLang[i] + '-' + k + '</label>' +
                                '<textarea class="form-control wechat-notice wechat-notice-' + i + '" data-key="' + k + '" rows="1"></textarea>' +
                                '</div>'
                            );
                        }
                    });
                });
            });

        }
        $('#review').modal('show');
    },
    'click .remove': function (e, value, row, index) {
        $table.bootstrapTable('remove', {
            field: '_id',
            values: [row._id]
        });
    }
};

function review($result) {
    var $evaluation = $('#evaluation');
    var $remark = $('#remark');
    if ($evaluation.parent().hasClass('has-error')) {
        $evaluation.parent().removeClass('has-error');
    }
    if ($evaluation.val() == '') {
        $evaluation.parent().addClass('has-error');
        return;
    }
    var $wechatNoticeContent = {};
    var $wechatNoticeClass = $result == 1 ? '.wechat-notice-pass' : '.wechat-notice-refuse';
    var $wechatNoticeDivs = $($wechatNoticeClass);
    $.each($wechatNoticeDivs, function (i, item) {
        if ($(item).val() == '') {
            $(item).parent().addClass('has-error');
            return;
        }
        $wechatNoticeContent[$(item).attr('data-key')] = $(item).val();
    });
    $.post('/web/review/store', {
        fid: fid,
        fd_id: $('#data-id').val(),
        result: $result,
        evaluation: $evaluation.val(),
        remark: $remark.val(),
        wechatNotice: $wechatNoticeContent
    }, function (data) {
        if (data.status == 0) {
            $('#table').bootstrapTable('refresh', {silent: true});
            $evaluation.val('');
            $remark.val('');
            toastr.success('审核成功！');
            $('#review').modal('hide');
        } else {
            var $error = data.error ? data.error : '';
            toastr.error('审核失败！' + $error);
        }
    });
}
function remark() {
    var $remark = $('#remark');
    $remark.parent().removeClass('has-error');
    if ($remark.val() == '') {
        $remark.parent().addClass('has-error');
        return;
    }
    $.post('/web/review/remark', {
        fid: fid,
        fd_id: $('#data-id').val(),
        remark: $remark.val()
    }, function (data) {
        if (data.status == 0) {
            $remark.val('');
            toastr.success('评论成功！');
            loadOldReview($('#data-id').val());
        } else {
            toastr.error('评论失败！');
        }
    });
}