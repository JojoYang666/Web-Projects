var fid = window.location.href.split('/')[5];
var $table = $('#table');
var $status = ['未处理', '已接收', '已拒绝', '已禁止'];
var $authorities = {
    'show': '查看概述',
    'update' : '编辑表单',
    'datalist' : '查看统计数据',
    'review':'审批',
    'remark':'评论',
    'report' : '查看报表'
};

function tableInit($columns) {
    $columns = jQuery.parseJSON($columns.replace(/\&quot\;/g, '"'));
    $table.bootstrapTable({
        columns: $columns,
        url: '/web/admin/' + fid + '/getAdmins',
        method: 'get',
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

        editable: false,

        showColumns: true,
        showRefresh: true,
        clickToSelect: true,
        toolbar: '#toolbar',
        showToggle: true,
        idField: 'id',
        uniqueId: 'id',
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
                var $now_status = row.handle;
                row.st = $now_status;
                row.handle = $status[$now_status];
                var auths = JSON.parse(row.authorities);
                var temp = '';
                $.each(auths, function (i, item) {
                    temp += '【' + $authorities[item] + '】';
                });
                row.authorities = temp;
                if (row.conditions == '[]') {
                    row.conditions = '无';
                } else {
                    var $conditions = JSON.parse(row.conditions);
                    row.conditions = '';
                    $.each($conditions, function (i, condition) {
                        row.conditions += '【' + condition.label + '在' + condition.value + "中】";
                    });
                }
                var $stages = JSON.parse(row.stage);
                row.stage = '';
                $.each($stages,function (i, $stage) {
                    row.stage+='【'+reviewTime2zh($stage)+'】';
                })

            });
            return res;
        },
        rowStyle: function (row, index) {
            var strclass = "";
            if (row.st == 1) {
                strclass = 'success';
            }
            else if (row.st == 2) {
                strclass = 'danger';
            }
            else {
                return {};
            }
            return {classes: strclass}
        },
        onEditableSave: function (field, row, oldValue, $el) {
            $.ajax({
                type: "post",
                url: "/Editable/Edit",
                data: {strJson: JSON.stringify(row)},
                success: function (data, status) {
                    if (status == "success") {
                        alert("编辑成功");
                    }
                },
                error: function () {
                    alert("Error");
                },
                complete: function () {

                }

            });
        }
    });
}

function operateFormatter(value, row, index) {
    return [
        '<a class="remove" href="javascript:void(0)" title="删除">',
        '<i class="glyphicon glyphicon-remove"></i>',
        '</a>'
    ].join('');
}
window.operateEvents = {
    'click .remove': function (e, value, row, index) {
        $.ajax({
            url:'/web/admin/'+row.id,
            type:'DELETE',
            data:{}
        });
        $table.bootstrapTable('remove', {
            field: 'id',
            values: [row.id]
        });
    }
};