function BBANDS(xDate,Middle,Upper,Lower,indicatorNamee,titeName,symbol) {
	var optionBB={
chart:{
type:'line',
 zoomType: 'x'
},
title:{
text:titeName
},
subtitle:{
 useHTML:true,
 text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>'
},
exporting: {
        url: 'http://export.highcharts.com/'
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
name: symbol+' Real Middle Band',

data:Middle
},
{
name: symbol+' Real Upper Band',

data:Upper
},
{

name: symbol+' Real Lower Band',

data:Lower
}
]

};
	Highcharts.chart('container', optionBB);
	return optionBB;	

}