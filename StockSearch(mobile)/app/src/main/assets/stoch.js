function stoch(xDate,SlowK,SlowD,indicatorNamee,titeName,symbol)
{
	// console.log("stoch"+SlowD);
	var optionStoch={
chart:{
type:'line',
 zoomType: 'x'
},
title:{
text:titeName
},
exporting: {
        url: 'http://export.highcharts.com/'
    },
subtitle:{
 useHTML:true,
 text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>'
},

// plotOptions:{
// 							series:{
// 								marker:{
// 									enabled:true,
// 									radius: 2,
// 									symbol: 'square'
// 										}
// 									}
// 								},
						legend:
{
   align: 'center',
   verticalAlign: 'bottom',
   layout:'horizontal'
   
},
xAxis: [{ 
		categories: xDate,
		 tickPositioner:function(){
					     	 	var res=[];
					     	 	for(var i=0;i<xDate.length;i++)
					     	 	{
					     	 		if(i%5==0)
					     	 			res.push(xDate.length-1-i);
					     	 	}
					     	 	return res;
					     	 }
	}],
	 yAxis: [{ // Primary yAxis
	 	title: {
			text: indicatorNamee 
		},
	tickAmount:5,
	// tickPixelInterval: 150

}],
series:[
{
// color:'rgb(211,44,32)',
name:symbol+'SlowK',
data:SlowK
},
{
// color:'rgb(166,203,238)',
name:symbol+'SlowD',
data:SlowD
}
]

};
Highcharts.chart('container',optionStoch);
return optionStoch;	
}