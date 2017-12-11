function stockChart(data,SymbolName)
{
    // console.log(data);
	Highcharts.stockChart('container', {

        

        rangeSelector: {

            buttons:[{
                type: 'week',
                count: 1,
                text: '1w'
            }, {
                type: 'month',
                count: 1,
                text: '1m'
            },{
                type: 'month',
                count: 3,
                text: '3m'
            }, {
                type: 'month',
                count: 6,
                text: '6m'
            },{
                type: 'ytd',
                text: 'YTD'
            },{
                type: 'year',
                count: 1,
                text: '1y'
            }, {
                type: 'all',
                text: 'All'
            }],
            selected: 0
        },

        title: {
            text: SymbolName+'Stock Price'
        },

        tooltip: {
            useHTML: true,
        formatter: function () {
            var s = Highcharts.dateFormat('%A, %b %e, %Y', this.x);

            $.each(this.points, function () {
                s += '<br/><span style="color: rgb(133,181,231)">&#x25cf;</span>'+SymbolName +':<b>' + this.y + ' </b>';

            });

            return s;
        }
    },

         subtitle: {
            useHTML:true,
        text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>'
        },
         yAxis: {
            title: {
                text: 'Stock Value'
            }
        },

        series: [{
            name: 'AAPL Stock Price',
            data: data,
            type: 'area',
            threshold: null,
            tooltip: {
                valueDecimals: 2
            }
           
        }]
    });
}