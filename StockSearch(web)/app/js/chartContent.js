$(document).ready(function(){

	$('form').submit(function(e){
		e.preventDefault();
		$.get('/symbol',$('#symbolSearch').val(),printContent);
		// $.get('/SMA',$('#symbolSearch').val(),printContent);
		// $.get('/EMA',$('#symbolSearch').val(),printContent);
		// $.get('/RSI',$('#symbolSearch').val(),printContent);
		// $.get('/ADX',$('#symbolSearch').val(),printContent);
		// $.get('/CCI',$('#symbolSearch').val(),printContent);

		// // //different url
		// $.get('/STOCH',$('#symbolSearch').val(),printContent);
		// $.get('/BBANDS',$('#symbolSearch').val(),printContent);
		// $.get('/MACD',$('#symbolSearch').val(),printContent);
	});
});


function printContent(content)
{
	
	

	var time=[];
	var i=0;
	var j=0;
	var xDate=[];
	var priceData=[];
	var volumeData=[];
	var getIndicator=[];
	var stockX=[];

	 console.log(content);

	$.each(content,function(key, value){
		getIndicator[i]=key;
		i++;
	});

	//related data for price	
	if(getIndicator[1]=='Time Series (Daily)')
	{
		$.each(content['Time Series (Daily)'],function(key, value){
		time[i]=key;
		i++;
	});

		for(i=131; i>=2; i--)
		{
			xDate[j]=time[i];
			// console.log(time[i]);
			priceData[j]=parseFloat(content['Time Series (Daily)'][time[i]]['4. close']);
			volumeData[j]=parseInt(content['Time Series (Daily)'][time[i]]['5. volume']);
			j++;
		}


		var stockData=[];
		var stock=new Array(1000);
		var tempX;
		j=0;
		for(i=1001; i>=2; i--)
		{
			
			// console.log(time[i]);
			stockData[j]=parseFloat(content['Time Series (Daily)'][time[i]]['4. close']);
			tempX=new Date(time[i]);
			stockX[j]=tempX.getTime();
			stock[j]=new Array(2);
			stock[j][0]=stockX[j];
			stock[j][1]=stockData[j];
			j++;
		}


		// console.log(xDate);
		// Inprice(xDate,priceData,volumeData);
		// stockChart(stock);
		console.log(stock);
	}
	
	
	var temp = getIndicator[1];
	console.log(temp);
	if(temp.substring(0,9)=='Technical')
	{
		var indicator=temp.substring(20,getIndicator[1].length);
		var singleData=[];
		var titeName=content['Meta Data']['2: Indicator'];
		var indicatorTime=[];
		var xIndicator=[];
		var xIndicatorValue=[];
		var k=0;


		// related to singleLine
			// if(true){
			// 		$.each(content[temp],function(key, value){
			// 		indicatorTime[i]=key;
			// 		xIndicatorValue[i]=value[indicator];
			// 		i++;
			// 	});

			// 	 // console.log(indicatorTime);
			// 	 var k=0;
				 
			// 	for(i=131;i>=2;i--)
			// 	{
			// 		xIndicator[k]=indicatorTime[i];
			// 		// console.log(xIndicator[k]);
			// 		singleData[k]=parseFloat(xIndicatorValue[i]);
			// 		k++;
			// 	}
			// 	console.log(singleData);
			// 	singLine(xIndicator,singleData,indicator,titeName);
			// }


			// data about for stoch
			if(indicator=='STOCH')
			{
				var SlowKTemp=[];
				var SlowDTemp=[];
				var SlowK=[];
				var SlowD=[];
				$.each(content[temp],function(key, value){
					indicatorTime[i]=key;
					SlowKTemp[i]=value.SlowK;
					SlowDTemp[i]=value.SlowD;
					i++;
				});

				for(i=131;i>=2;i--)
				{
					xIndicator[k]=indicatorTime[i];
					// console.log(xIndicator[k]);
					SlowK[k]=parseFloat(SlowKTemp[i]);
					SlowD[k]=parseFloat(SlowDTemp[i]);
					k++;
				}	
				stoch(xIndicator,SlowK,SlowD,indicator,titeName);
			}


			if(indicator=='BBANDS')
			{
				var Middle=[];
				var Upper=[];
				var Lower=[];
				var MiddleTemp=[];
				var UpperTemp=[];
				var LowerTemp=[];
				$.each(content[temp],function(key, value){
					indicatorTime[i]=key;
					MiddleTemp[i]=value['Real Middle Band'];
					UpperTemp[i]=value['Real Upper Band'];
					LowerTemp[i]=value['Real Lower Band'];
					i++;
				});

				for(i=131;i>=2;i--)
				{
					xIndicator[k]=indicatorTime[i];
					// console.log(xIndicator[k]);
					Middle[k]=parseFloat(MiddleTemp[i]);
					Upper[k]=parseFloat(UpperTemp[i]);
					Lower[k]=parseFloat(LowerTemp[i]);
					k++;
				}
				BBANDS(xIndicator,Middle,Upper,Lower,indicator,titeName);					
			}

			if(indicator=='MACD')
			{
				var Hist=[];
				var MACDD=[];
				var Signal=[];
				var HistTemp=[];
				var MACDTemp=[];
				var SignalTemp=[];

				$.each(content[temp],function(key, value){
					indicatorTime[i]=key;
					HistTemp[i]=value['MACD_Hist'];
					MACDTemp[i]=value['MACD'];
					SignalTemp[i]=value['MACD_Signal'];
					i++;
				});

				for(i=131;i>=2;i--)
				{
					xIndicator[k]=indicatorTime[i];
					// console.log(xIndicator[k]);
					Hist[k]=parseFloat(HistTemp[i]);
					MACDD[k]=parseFloat(MACDTemp[i]);
					Signal[k]=parseFloat(SignalTemp[i]);
					k++;
				}
				MACD(xIndicator,Hist,MACDD,Signal,indicator,titeName);


			}


			
	}
	
	

	
// 	var newHTML=[];
// 	$.each(content,function(index, value) {
//     newHTML.push('<span>' + value + '</span>');
// });
// 	$("#test").html(newHTML.join(""));
}