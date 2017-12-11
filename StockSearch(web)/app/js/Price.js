function Inprice(symbol,xDate,priceData,volumeData)
{
	// console.log(xDate);
	// console.log(priceData);
	// console.log(volumeData);
    var optionPrice={
        title: {
        text: symbol+' Stock Price and Volume'
    },
    chart: {
       
        zoomType: 'x'
    },

    subtitle: {
         useHTML:true,
        text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>'
    },


plotOptions:{
    series:{
        marker:{
            enabled:false,
        }

            }
},

exporting: {
        url: 'http://export.highcharts.com/'
    },


legend:
{
   align: 'center',
   verticalAlign: 'bottom',
   layout:'horizontal'
   
},

     xAxis: [{ 
         categories: xDate,
         startOnTick:true,
         // tickAmount:10,
         tickInterval:11
         // tickPositioner:function(){
         //    var res=[];
         //    for(var i=0;i<xDate.length;i++)
         //    {
         //        if(i%12==0)
         //            res.push(xDate.length-1-i);
         //    }
         //    return res;
         // }
        }],
    yAxis: [{ // Primary yAxis
        title: {
            text: 'Stock Price' 
        },
        
       // max:169.2400,
       // min:130.71142857143,
       tickAmount:4,
       startOnTick:true,
      
       
    },


    {
        title:{
            text:'Volume'
        },
         gridLineWidth:0,
        // max:214690842,
        // min:0,
        tickAmount:4,
        startOnTick:true,
        opposite: true
    }
],
    series:[
        {
    name:'Price',
    type:'area',
    // color:'rgb(211,44,32)',
    lineWidth:1,
    fillOpacity:0.55 ,
    data:priceData,
    pointFormat: '<span style="color:red;">\u25CF</span>{series.name}: <b>{point.y:,..2f}</b><br/>'         
    },

    {
        name:'Volume',
        type:'column',
        color:'rgb(211,44,32)',
        yAxis: 1,
         data:volumeData
}
]
    };
	
	Highcharts.chart('container',optionPrice);
    return optionPrice;
}
