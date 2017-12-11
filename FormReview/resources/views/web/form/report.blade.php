@extends('layouts.app')

@section('content')
    <div class="container">
        @include('web.form.nav')
        <div id="charts" class="charts row">
            <div v-for="item in fields" class="col-xs-12 col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">@{{ item.label }}</div>
                <div class="panel-body">
                    <canvas class="chart" :name="item.name"></canvas>
                </div>
            </div></div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{cdnAsset("/node_modules/vue/dist/vue.min.js")}}"></script>
    <script src="{{cdnAsset("/node_modules/chart.js/dist/Chart.js")}}"></script>
    <script>
        var $fid = '{{$form->id}}';
        $('#nav-report').addClass('active');
        var vm = new Vue({
            el: '#charts',
            data: {
                fields: null
            },
            created: function () {
                var that = this;
                $.get('/web/form/' + $fid + '/getStat', function (data) {
                    that.fields = data;
                });
                window.vm = this;
            },
            updated: function () {
                $.each($('.chart'), function (i, item) {
                    var index = $(item).attr('name');
                    var ctx = $(item).get(0).getContext("2d");
                    var itemData = vm.fields[index];
                    var labels = [];
                    var dataSet = [];
                    $.each(itemData.values, function (j, jtem) {
                        if (typeof itemData.stat[jtem.value] == "undefined") {
                            dataSet.push(0);
                        } else {
                            dataSet.push(itemData.stat[jtem.value]);
                        }
                        labels.push(jtem.label);
                    });
                    console.log(labels);
                    console.log(dataSet);
                    var chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: dataSet,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    },
                                    barThickness:1,
                                    stacked:true
                                }]
                            }
                        }
                    });
                });
            },
            method: {}
        });
    </script>
@endsection