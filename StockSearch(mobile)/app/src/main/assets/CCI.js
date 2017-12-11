function CCI(xDate,totalData,indicatorNamee,titeName,symbol) {
	// console.log(xDate);
	// console.log(totalData);
	// console.log(indicatorNamee);
	// console.log(titeName);
	var optionCCI ={
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
						// 	series:{
						// 		marker:{
						// 			enabled:true,
						// 			radius: 2,
						// 			symbol: 'square'
						// 				}
						// 			}
						// 		},
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

		       				tickAmount:4,
		       				// tickPixelInterval: 150

		       
		    			}],
		    			series:[
		    			{
		    				name:symbol,
		    				// color:'rgb(211,44,32)',
		    				lineWidth:1,
		    				
		    				data:totalData
		    			}
		    	


		    			]

					};
	Highcharts.chart('container',optionCCI);
	return optionCCI;	


}