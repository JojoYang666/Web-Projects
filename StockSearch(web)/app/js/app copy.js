(function(angular) {
  'use strict';
angular.module('ngViewExample', ['ngRoute', 'ngAnimate'])
  .config(['$routeProvider', 
    function($routeProvider) {
      $routeProvider
       .when('/', {
          templateUrl: 'favoriteList.html',
          controller:'MainCtrl',
          controllerAs: 'main'
          
        })
       .when('/chartTable', {
          templateUrl: 'chartTable.html',
          
        })
        .when('/currentStocK', {
          templateUrl: 'currentStocK.html',
          
        })
        .otherwise({
      	redirectTo: '/currentStocK',
    	});


//       $locationProvider.html5Mode({
//   enabled: true,
//   requireBase: false
// });

  }])

  .run(function($rootScope) {
    $rootScope.notFinished=true;
    $rootScope.replace=false;
    $rootScope.EMA=undefined;
    $rootScope.whichChart='none';
    $rootScope.xIndicator=[];
    $rootScope.SlowK=[];
    $rootScope.SlowK=[];
    $rootScope.titeName='none';  
    $rootScope.tableCon=true; 
    $rootScope.stockShow=false;     
  })

  .controller('MainCtrl', ['$scope','$rootScope','$http',
    function MainCtrl($scope,$rootScope,$http) {
        
        $rootScope.Inprice=0;
        $rootScope.stoch=0;
        $rootScope.MACD=0;
        $rootScope.BBANDS=0;
        

        





        $scope.getData=function(){
          $rootScope.notFinished=true;
          $rootScope.replace=false;
           var globalStore=[];
           console.log($scope.symbol);
              $.when(
                  $.get('/symbol',$('#symbolSearch').val(),function(price){
                    globalStore.price=price;

                  }),
                  $.get('/SMA',$('#symbolSearch').val(),function(SMA){
                    globalStore.SMA=SMA;

                  }),
                  $.get('/EMA',$('#symbolSearch').val(),function(EMA){
                    globalStore.EMA=EMA;

                  }),
                  $.get('/RSI',$('#symbolSearch').val(),function(RSI){
                    globalStore.RSI=RSI;

                  }),
                  $.get('/ADX',$('#symbolSearch').val(),function(ADX){
                    globalStore.ADX=ADX;

                  }),
                  $.get('/CCI',$('#symbolSearch').val(),function(CCI){
                    globalStore.CCI=CCI;

                  }),
                  //multiple line
                  $.get('/STOCH',$('#symbolSearch').val(),function(stoch){
                    globalStore.stoch=stoch;
                  }),
                  $.get('/BBANDS',$('#symbolSearch').val(),function(BBANDS){
                    globalStore.BBANDS=BBANDS;
                  }),
                  $.get('/MACD',$('#symbolSearch').val(),function(MACD){
                    globalStore.MACD=MACD;
                  })


                ).then(function(){
                      var time=[];
                      var i=0;
                      var j=0;
                      var xDate=[];
                      var priceData=[];
                      var volumeData=[];
                      var getIndicator=[];
                      var stockX=[];

                    if(globalStore.price!=undefined)
                    {

                        $.each(globalStore.price,function(key, value){
                        getIndicator[i]=key;
                        i++;
                        });


                      //handle price

                       $.each(globalStore.price['Time Series (Daily)'],function(key, value){
                        time[i]=key;
                        i++;
                        });

                          for(i=131; i>=2; i--)
                        {
                          xDate[j]=time[i];
                          // console.log(time[i]);
                          priceData[j]=parseFloat(globalStore.price['Time Series (Daily)'][time[i]]['4. close']);
                          volumeData[j]=parseInt(globalStore.price['Time Series (Daily)'][time[i]]['5. volume']);
                          j++;
                        }

                          var stockData=[];
                          var stock=new Array(1000);
                          var tempX;
                          j=0;
                          for(i=1001; i>=2; i--)
                          {
                            
                            // console.log(time[i]);
                            stockData[j]=parseFloat(globalStore.price['Time Series (Daily)'][time[i]]['4. close']);
                            tempX=new Date(time[i]);
                            stockX[j]=tempX.getTime();
                            stock[j]=new Array(2);
                            stock[j][0]=stockX[j];
                            stock[j][1]=stockData[j];
                            j++;
                          }

                     
                    

                          $scope.Symbol=$('#symbolSearch').val();
                          $rootScope.Symbol=$scope.Symbol
                          $rootScope.price=parseFloat(globalStore.price['Time Series (Daily)'][time[2]]['4. close']).toFixed(2);
                          var change=parseFloat(globalStore.price['Time Series (Daily)'][time[2]]['4. close'])-parseFloat(globalStore.price['Time Series (Daily)'][time[3]]['4. close']);
                          change=change.toFixed(2);
                          var changePercent=(change*100).toFixed(2)+"%";
                          $rootScope.change=change+'('+changePercent+')';
                          $rootScope.open=parseFloat(globalStore.price['Time Series (Daily)'][time[2]]['1. open']).toFixed(2);
                          $rootScope.close=parseFloat(globalStore.price['Time Series (Daily)'][time[2]]['4. close']).toFixed(2);
                          var range=parseFloat(globalStore.price['Time Series (Daily)'][time[2]]['2. high'])-parseFloat(globalStore.price['Time Series (Daily)'][time[2]]['3. low']);
                          range=range.toFixed(2);
                          $rootScope.range=range;
                          $rootScope.volume=globalStore.price['Time Series (Daily)'][time[2]]['5. volume'];
                          $rootScope.Inprice=1;

                          // $scope.updateStatus= function(){
                          //   setTimeout(function(){
                          //     $scope.notFinished=true;
                          //     $scope.replace=true; 
                          //     $scope.$apply();
                          //   },2000);
                          // };
                          $rootScope.$apply(function(){
                            $rootScope.notFinished=false;
                             $rootScope.replace=true; 
                            
                          });
                          $rootScope.InpricexDate=xDate;
                          $rootScope.InpricepriceData=priceData;
                          $rootScope.InpricevolumeData=volumeData;
                          $rootScope.STOCKfraph=stock;

                          Inprice(xDate,priceData,volumeData);

                           // stockChart(stock);

                    }
                      
                    

                   
                    
                    if(globalStore.stoch!=undefined)
                    { 
                        
                        
                        var temp ='Technical Analysis: STOCH';                       
                        console.log(globalStore.stoch);                        
                        var singleData=[];
                        var titeName=globalStore.stoch['Meta Data']['2: Indicator'];

                        var indicatorTime=[];
                        var xIndicator=[];
                        var xIndicatorValue=[];
                        var k=0;
                        var i=0;
                        var SlowKTemp=[];
                        var SlowDTemp=[];
                        var SlowK=[];
                        var SlowD=[];
                         console.log(globalStore.stoch[temp]);
                        $.each(globalStore.stoch[temp],function(key, value){
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

                        $rootScope.STOCHxIndicator=xIndicator;
                        $rootScope.STOCHSlowK=SlowK;
                        $rootScope.STOCHSlowK=SlowK;
                        $rootScope.STOCHtiteName=titeName;                          
                        // stoch(xIndicator,SlowK,SlowK,'STOCH',titeName);
                       
                    }


                    if(globalStore.BBANDS!=undefined)
                    {     
                          console.log(globalStore.BBANDS);
                          var temp ='Technical Analysis: BBANDS'; 
                          var titeName=globalStore.BBANDS['Meta Data']['2: Indicator'];
                          var i=0;
                          var k=0;
                          var Middle=[];
                          var Upper=[];
                          var Lower=[];
                          var MiddleTemp=[];
                          var UpperTemp=[];
                          var LowerTemp=[];
                          $.each(globalStore.BBANDS[temp],function(key, value){
                          indicatorTime[i]=key;
                            MiddleTemp[i]=value['Real Middle Band'];
                            UpperTemp[i]=value['Real Upper Band'];
                            LowerTemp[i]=value['Real Lower Band'];
                            i++;
                          });
                          console.log(LowerTemp);
                          for(i=131;i>=2;i--)
                          {
                            xIndicator[k]=indicatorTime[i];
                            // console.log(xIndicator[k]);
                            Middle[k]=parseFloat(MiddleTemp[i]);
                            Upper[k]=parseFloat(UpperTemp[i]);
                            Lower[k]=parseFloat(LowerTemp[i]);
                            k++;
                          }

                          $rootScope.BBANDSxIndicator=xIndicator;
                          $rootScope.BBANDSMiddle=Middle;
                          $rootScope.BBANDSUpper=Upper;
                          $rootScope.BBANDSLower=Lower;
                          $rootScope.BBANDStiteName=titeName;

                           // BBANDS(xIndicator,Middle,Upper,Lower,'BBANDS',titeName);

                    }

                    if(globalStore.MACD!=undefined)
                    {
                        console.log('MACD');
                        var temp ='Technical Analysis: MACD'; 
                        var titeName=globalStore.MACD['Meta Data']['2: Indicator'];
                        var Hist=[];
                        var MACDD=[];
                        var Signal=[];
                        var HistTemp=[];
                        var MACDTemp=[];
                        var SignalTemp=[];
                        var i=0;
                        var k=0;

                        $.each(globalStore.MACD[temp],function(key, value){
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

                        $rootScope.MACDxIndicator=xIndicator;
                        $rootScope.MACDHist=Hist;
                        $rootScope.MACDMACDD=MACDD;
                        $rootScope.MACDSignal=Signal;
                        $rootScope.MACDtiteName=titeName;
                        // MACD(xIndicator,Hist,MACDD,Signal,'MACD',titeName);
                    }

                    if(globalStore.SMA!=undefined)
                    {   
                        console.log(globalStore.SMA);
                        console.log('SMA');
                        var i=0;
                        var k=0;
                        var temp ='Technical Analysis: SMA'; 
                        var titeName=globalStore.SMA['Meta Data']['2: Indicator'];
                        $.each(globalStore.SMA[temp],function(key, value){
                           indicatorTime[i]=key;
                           xIndicatorValue[i]=value['SMA'];
                           i++;
                        });

                        var k=0;
         
                         for(i=131;i>=2;i--)
                         {
                           xIndicator[k]=indicatorTime[i];
                           // console.log(xIndicator[k]);
                           singleData[k]=parseFloat(xIndicatorValue[i]);
                           k++;
                         }
                         $rootScope.SMAxIndicator=xIndicator;
                         $rootScope.SMAsingleData=singleData;
                         $rootScope.SMAtiteName=titeName;
                         // SMA(xIndicator,singleData,'SMA',titeName);
                    }

                    if(globalStore.EMA!=undefined)
                    {
                        console.log('EMA');                        
                        var k=0;
                        var temp ='Technical Analysis: EMA'; 
                        var titeName=globalStore.EMA['Meta Data']['2: Indicator'];
                        var i=0;
                        $.each(globalStore.EMA[temp],function(key, value){
                           indicatorTime[i]=key;
                           xIndicatorValue[i]=value['EMA'];
                           i++;
                        });

                        var k=0;
         
                         for(i=131;i>=2;i--)
                         {
                           xIndicator[k]=indicatorTime[i];
                           // console.log(xIndicator[k]);
                           singleData[k]=parseFloat(xIndicatorValue[i]);
                           k++;
                         }
                         $rootScope.EMAxIndicator=xIndicator;
                         $rootScope.EMAsingleData=singleData;
                         $rootScope.EMAtiteName=titeName;
                         // EMA(xIndicator,singleData,'EMA',titeName);
                        
                    }

                    if(globalStore.RSI!=undefined)
                    {
                        console.log('RSI');
                        var i=0;
                        var k=0;
                        var temp ='Technical Analysis: RSI'; 
                        var titeName=globalStore.RSI['Meta Data']['2: Indicator'];
                        var i=0;
                        $.each(globalStore.RSI[temp],function(key, value){
                           indicatorTime[i]=key;
                           xIndicatorValue[i]=value['RSI'];
                           i++;
                        });

                        var k=0;
         
                         for(i=131;i>=2;i--)
                         {
                           xIndicator[k]=indicatorTime[i];
                           // console.log(xIndicator[k]);
                           singleData[k]=parseFloat(xIndicatorValue[i]);
                           k++;
                         }

                         $rootScope.RSIxIndicator=xIndicator;
                         $rootScope.RSIsingleData=singleData;
                         $rootScope.RSItiteName=titeName;
                         // RSI(xIndicator,singleData,'RSI',titeName);
                    }

                    if(globalStore.ADX!=undefined)
                    {
                      console.log('ADX');
                        var i=0;
                        var k=0;
                        var temp ='Technical Analysis: ADX'; 
                        var titeName=globalStore.ADX['Meta Data']['2: Indicator'];
                        $.each(globalStore.ADX[temp],function(key, value){
                           indicatorTime[i]=key;
                           xIndicatorValue[i]=value['ADX'];
                           i++;
                        });

                        var k=0;
         
                         for(i=131;i>=2;i--)
                         {
                           xIndicator[k]=indicatorTime[i];
                           // console.log(xIndicator[k]);
                           singleData[k]=parseFloat(xIndicatorValue[i]);
                           k++;
                         }

                         $rootScope.ADXxIndicator=xIndicator;
                         $rootScope.AADXsingleData=singleData;
                         $rootScope.ADXtiteName=titeName;
                         // ADX(xIndicator,singleData,'ADX',titeName);
                    }

                    if(globalStore.CCI!=undefined)
                    {
                      console.log('CCI');
                        
                        var i=0;
                        var k=0;
                        var temp ='Technical Analysis: CCI'; 
                        var titeName=globalStore.CCI['Meta Data']['2: Indicator'];
                        $.each(globalStore.CCI[temp],function(key, value){
                           indicatorTime[i]=key;
                           xIndicatorValue[i]=value['CCI'];
                           i++;
                        });

                        var k=0;
         
                         for(i=131;i>=2;i--)
                         {
                           xIndicator[k]=indicatorTime[i];
                           // console.log(xIndicator[k]);
                           singleData[k]=parseFloat(xIndicatorValue[i]);
                           k++;
                         }
                         $rootScope.CCIxIndicator=xIndicator;
                         $rootScope.CCIsingleData=singleData;
                         $rootScope.CCItiteName=titeName;
                         //CCI(xIndicator,singleData,'CCI',titeName); 
                    }               
                });

         };      
        
         
          $rootScope.BBANDS=function(event){
           event.preventDefault();
          console.log('hello');
           BBANDS($rootScope.BBANDSxIndicator,$rootScope.BBANDSMiddle,$rootScope.BBANDSUpper,$rootScope.BBANDSLower,'BBANDS',$rootScope.BBANDStiteName);
          console.log('hello agaon');
        }; 
         $rootScope.STOCH=function(event){
           event.preventDefault();
          console.log('hello');
          stoch($rootScope.STOCHxIndicator,$rootScope.STOCHSlowK,$rootScope.STOCHSlowD,'STOCH',$rootScope.STOCHtiteName);
          console.log('hello agaon');
        };  
        $rootScope.CCI=function(event){
           event.preventDefault();
          console.log('hello');
          CCI($rootScope.CCIxIndicator,$rootScope.CCIsingleData,'CCI', $rootScope.CCItiteName); 
          console.log('hello agaon');
        };  
        $rootScope.ADX=function(event){
           event.preventDefault();
          console.log('hello');
          ADX($rootScope.ADXxIndicator,$rootScope.AADXsingleData,'ADX',$rootScope.ADXtiteName);
          console.log('hello agaon');
        }; 
         $rootScope.RSI=function(event){
           event.preventDefault();
          console.log('hello');
         RSI($rootScope.RSIxIndicator,$rootScope.RSIsingleData,'RSI',$rootScope.RSItiteName);
          console.log('hello agaon');
        };  
        $rootScope.EMA=function(event){
           event.preventDefault();
          console.log('hello');
          EMA($rootScope.EMAxIndicator,$rootScope.EMAsingleData,'EMA',$rootScope.EMAtiteName);
          console.log('hello agaon');
        };  
        $rootScope.SMA=function(event){
           event.preventDefault();
          console.log('hello');
         SMA($rootScope.SMAxIndicator,$rootScope.SMAsingleData,'SMA',$rootScope.SMAtiteName);
          console.log('hello agaon');
        }; 
         $rootScope.SMAS=function(event){
           event.preventDefault();
           console.log('price');
           Inprice($rootScope.InpricexDate,$rootScope.InpricepriceData,$rootScope.InpricevolumeData);
            console.log('hello price');
        };  
        $rootScope.MACD=function(event){
           event.preventDefault();
          console.log('hello');
          MACD($rootScope.MACDxIndicator,$rootScope.MACDHist,$rootScope.MACDMACDD,$rootScope.MACDSignal,'MACD',$rootScope.MACDtiteName);
          console.log('hello agaon');
        };
        $rootScope.stock=function(event){
           event.preventDefault();
           console.log('price');
              $rootScope.stockShow=true;          
              $rootScope.tableCon=false;                            
          
            stockChart($rootScope.STOCKfraph);
            console.log('hello price');
        };

        $rootScope.tableContent=function(event){
           event.preventDefault();
           $rootScope.stockShow=false;          
            $rootScope.tableCon=true; 
                                    
         
        };          
}]);
 
})(window.angular);

