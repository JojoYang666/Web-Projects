var $table = $('#table');

$(function () {
    $('#btn_add').click(function () {
        $.get('/web/wechat/add-platform', function (data) {
            if (data.status == 0) {
                window.open(data.url);
            } else {
                toastr.error(data.error);
            }
        });
    });

});

// https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=002b13e33327cff5d22698934b4403f62b8d8329&lang=zh_CN
function tableInit($columns) {
    $columns = [
        {
            field: "state",
            checkbox: "true",
            align: "center"
        },
        {
            field: "service_type_info",
            title: "公众号类型",
            align: "center",
            sortable: true,
            filterControl: "input"
        },
        {
            field: "nick_name",
            title: "微信公众号",
            align: "center",
            sortable: true,
            filterControl: "input"
        },
        {
            field: "verify_type_info",
            title: "是否认证",
            align: "center",
            sortable: true,
            visible: false,
            filterControl: "input"
        },
        {
            field: "appid",
            title: "APPID",
            align: "center",
            sortable: true,
            visible: false,
            filterControl: "input"
        },
        {
            field: "appsecret",
            title: "APPSECRET",
            align: "center",
            sortable: true,
            visible: false,
            filterControl: "input",
            editable: true
        },
        {
            field: "token",
            title: "TOKEN",
            align: "center",
            visible: false,
            sortable: true,
            filterControl: "input",
            editable: true
        },
        {
            field: "func_info",
            title: "授权功能",
            align: "center",
            sortable: true,
            filterControl: "input",
            editable: false
        },
        {
            field: "updated_at",
            title: "更新时间",
            align: "center",
            visible: false,
            sortable: true,
            filterControl: "input"
        },
        {
            field: "remark",
            title: "备注",
            align: "center",
            sortable: true,
            filterControl: "input",
            editable: true
        },
        {
            field: "operate",
            title: "操作",
            align: "center",
            events: "operateEvents",
            formatter: "operateFormatter"
        }
    ];
    $table.bootstrapTable({
        columns: $columns,
        url: '/web/wechat/getPlatforms',
        method: 'get',
        escape: false,
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

        editable: true,

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
                if(typeof(row[item.field]) == "undefined") {
                    row[item.field] = '-'
                }
                if (typeof(item.title) != "undefined") {
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
                var $func_info = '';
                $.each(row.func_info, function (i, func) {
                    $func_info += '<p>'+func+'</p>';
                });
                row.func_info = $func_info;

                row.nick_name = '<p><img src="'+row.head_img+'" style=" width: 100px;"/></p><p>'+row.nick_name+'</p>';
                row.service_type_info = row.service_type_info==2?'服务号':'订阅号';
                row.verify_type_info = row.verify_type_info==-1?'未认证':'已认证';
            });
            return res;
        },
        onEditableSave: function (field, row, oldValue, $el) {
            $.ajax({
                type: "post",
                url: "/web/wechat/edit-platform",
                data: {
                    id: row._id,
                    field: field,
                    newValue: JSON.stringify(row),
                    oldValue: oldValue
                },
                success: function (data) {
                    if (data.status == 0) {
                        toastr.success('编辑成功');
                    }else{
                        toastr.error('编辑失败');
                    }
                },
                error: function () {
                    toastr.error('编辑失败');
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
            url:'/web/wechat/'+row._id,
            type:'DELETE',
            data:{}
        });
        $table.bootstrapTable('remove', {
            field: '_id',
            values: [row._id]
        });
        toastr.success('删除成功');
    }
};