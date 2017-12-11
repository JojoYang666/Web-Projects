(function(angular) {
  'use strict';
  angular.module('myModule', ['ui.toggle']);
  angular.module('ngViewExample', ['myModule','ngRoute', 'ngAnimate','ngMaterial','ngMessages'])
  .config(['$routeProvider', 
    function($routeProvider) {
      $routeProvider
       .when('/', {
          templateUrl: 'favoriteList.html',
          controller:'loveControl',
          controllerAs: 'love'
          
        })
       .when('/chartTable', {
          templateUrl: 'chartTable.html',
          
        })
       .when('/back', {
          templateUrl: 'favoriteList.html',
          controller:'loveControl',
          controllerAs: 'love'
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
    $rootScope.news=false; 
    $rootScope.loadFavrite=true;    
  })

  .controller('autoCom', ['$scope','$http',
        function autoCom($scope, $http){

          $scope.selectValue=function(selectedItem){
            selectedItem=selectedItem.symbol;
            console.log("chage succ?");
          }

          $scope.query=function(searchText){
          return $http.get('/auto'+searchText)
                .then(function(result){

                  var dataAuto=[];
                  var res;
                  for(var i=0; i<result.data.length; i++)
                  {
                    res=result.data[i];
                    console.log(res);
                    // var temp = display: res.Symbol+' - '+res.Name+' ('+res.Exchange+')';
                    var temp={
                      display: res.Symbol+' - '+res.Name+' ('+res.Exchange+')',
                      symbol:res.Symbol,
                      Name:res.Name,
                      Exchange:res.Exchange
                    };
                    dataAuto.push(temp);
                  }
                  console.log(result.data);
                  return dataAuto;
                });
            };


        }])

  .controller('MainCtrl', ['$scope','$rootScope','$http',
    function MainCtrl($scope,$rootScope,$http) {
        
        $rootScope.InpriceState=1;
        $rootScope.stochState=1;
        $rootScope.MACDState=1;
        $rootScope.BBANDState=1; 
        $rootScope.SMAstate=1; 
        $rootScope.EMAstate=1;      
        $rootScope.RSIstate=1;
        $rootScope.ADXstate=1;
        $rootScope.CCIstate=1;
        $rootScope.articalState=1;
        $scope.getData=function(Search){
          $rootScope.notFinished=true;
          $rootScope.replace=false;
          console.log(Search);
           　
          var globalStore=[];
          console.log("temp of symbol is"+Search);
          // if($('#symbolSearch').val())
          // {
          //   console.log("not empty");
          // }
          // else
          // {
          //   console.log("empty");
          // }
          var time=[];
          var i=0;
          var j=0;
          var xDate=[];
          var priceData=[];
          var volumeData=[];
          var getIndicator=[];
          var stockX=[]; 


          $.when(

                $.get('/symbol',$('#symbolSearch').val(),function(price){
                    globalStore.price=price;
                    $rootScope.InpriceState=1;
                    if(globalStore.price['Time Series (Daily)']==undefined)
                    {
                      $rootScope.InpriceState=0;
                    }
                    else
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
                          

                          
         
                  $rootScope.InpricexDate=xDate;
                  $rootScope.InpricepriceData=priceData;
                  $rootScope.InpricevolumeData=volumeData;
                  $rootScope.STOCKfraph=stock;


                   
          
                  $rootScope.nameAgain='Inprice';

                    }
                    
                  }).fail(function(){
                    console.log("fails");            
                    $rootScope.InpriceState=0;
                  }),


                $.get('/SMA',$('#symbolSearch').val(),function(SMA){
                  globalStore.SMA=SMA;

                  $rootScope.SMAstate=1;
                  var indicatorTime=[];
                  var xIndicatorValue=[];
                  var xIndicator=[];
                  var singleData=[];
                  // console.log(globalStore.SMA);
                  console.log('SMA');
                  console.log( globalStore.SMA);
                  var i=0;
                  var k=0;
                  if(globalStore.SMA['Meta Data']==undefined)
                  {
                     $rootScope.SMAstate=0;
                  }
                  else
                  {
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
                  }
                  
                    }).fail(function(){
                      console.log("fails");
                      $rootScope.SMAstate=0;
                    }),



                $.get('/EMA',$('#symbolSearch').val(),function(EMA){
                      globalStore.EMA=EMA;
                      $rootScope.EMAstate=1;
                       var singleData=[];
                      var indicatorTime=[];
                      var xIndicator=[];
                      var xIndicatorValue=[];
                      console.log('EMA'); 
                       console.log(globalStore.EMA);                       
                      var k=0;
                      var temp ='Technical Analysis: EMA'; 
                      if(globalStore.EMA['Meta Data']==undefined)
                      {
                        $rootScope.EMAstate=0;
                      }
                      else
                      {
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
                      }
                      
                }).fail(function(){
                  var indicatorTime=[];
                  // console.log("fails");
                  $rootScope.EMAstate=0;
                }),


              $.get('/RSI',$('#symbolSearch').val(),function(RSI){
                    globalStore.RSI=RSI;
                    $rootScope.RSIstate=1;
                    var xIndicator=[];
                    var singleData=[];
                    var xIndicatorValue=[];
                    console.log('RSI');
                    var i=0;
                    var k=0;
                    var indicatorTime=[];
                    var temp ='Technical Analysis: RSI'; 
                    if(globalStore.RSI['Meta Data']==undefined)
                    {
                      $rootScope.RSIstate=0;
                    }
                    else{
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
                    }
                    
              }).fail(function(){
                // console.log("fails");
                $rootScope.RSIstate=0;
              }),


              $.get('/ADX',$('#symbolSearch').val(),function(ADX){
                    globalStore.ADX=ADX;
                     var xIndicator=[];
                      var singleData=[];
                    $rootScope.ADXstate=1;
                     var indicatorTime=[];
                     var xIndicatorValue=[];
                    console.log('ADX');
                    var i=0;
                    var k=0;
                    var temp ='Technical Analysis: ADX'; 
                    if(globalStore.ADX['Meta Data']==undefined)
                    {
                      $rootScope.ADXstate=0;
                    }
                    else
                    {
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
                    }
                   
              }).fail(function(){

                // console.log("fails");
                $rootScope.ADXstate=0;
              }),


              $.get('/CCI',$('#symbolSearch').val(),function(CCI){
                    globalStore.CCI=CCI;

                    $rootScope.CCIstate=1;
                     var singleData=[];
                     var xIndicator=[];
                    console.log('CCI');
                     console.log(globalStore.CCI);
                    var indicatorTime=[];
                    var xIndicatorValue=[];
                    var i=0;
                    var k=0;
                    var temp ='Technical Analysis: CCI'; 
                    if(globalStore.CCI['Meta Data']==undefined)
                    {
                      $rootScope.CCIstate=0; 
                    }
                    else
                    {
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
                    }
                    
                     //CCI(xIndicator,singleData,'CCI',titeName); 
              }).fail(function(){
                // console.log("fails");
                $rootScope.CCIstate=0;
              }),


                  //multiple line
              $.get('/STOCH',$('#symbolSearch').val(),function(stoch){
                    globalStore.stoch=stoch;
                    $rootScope.stochState=1;
                     var xIndicator=[];
                     var temp ='Technical Analysis: STOCH';                       
                    console.log(globalStore.stoch);                        
                    var singleData=[];
                    if(globalStore.stoch['Meta Data']==undefined)
                    {
                      $rootScope.stochState=0;
                    }
                    else
                    {
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
                    }
                         
              }).fail(function(){
                // console.log("fails");
                $rootScope.stochState=0;
              }),


              $.get('/BBANDS',$('#symbolSearch').val(),function(BBANDS){
                      globalStore.BBANDS=BBANDS;
                      $rootScope.BBANDState=1;
                       var xIndicator=[];
                      console.log(globalStore.BBANDS);
                      var xIndicatorValue=[];
                      if(globalStore.BBANDS['Meta Data']==undefined)
                      {
                        $rootScope.BBANDState=0;
                      }
                      else{
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
                      var indicatorTime=[];
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
                      }
              }).fail(function(){
                // console.log("fails");
                $rootScope.BBANDState=0; 
              }),


              $.get('/MACD',$('#symbolSearch').val(),function(MACD){
                    globalStore.MACD=MACD;
                    $rootScope.MACDState=1;
                     var xIndicator=[];
                    var indicatorTime=[];
                    console.log('MACD');
                    if(globalStore.MACD['Meta Data']==undefined)
                    {
                      $rootScope.MACDState=0;
                    }
                   else{
                     var temp ='Technical Analysis: MACD'; 
                    var titeName=globalStore.MACD['Meta Data']['2: Indicator'];
                    var Hist=[];
                    var MACDD=[];
                    var Signal=[];
                    var HistTemp=[];
                    var MACDTemp=[];
                    var SignalTemp=[];
                    var xIndicator=[];
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
                   }
                    // MACD(xIndicator,Hist,MACDD,Signal,'MACD',titeName);
              }).fail(function(){
                // console.log("fails");
                $rootScope.MACDState=0;
              }),

              
              $.get('/artical',$('#symbolSearch').val(),function(artical){
                globalStore.artical=artical;
                $rootScope.articalState=1;
                 var k=0;
                  var container=[];
                    console.log(globalStore.artical);
                    if(globalStore.artical.rss==undefined)
                    {
                      $rootScope.articalState=0;
                    }
                    else{
                      var item=globalStore.artical.rss.channel["0"].item;

                    var itemLength=item.length;
                    for(var i=0; i<itemLength;i++){
                      if(item[i]['link'].includes("artical"));
                      {
                        container[k]=item[i];
                        k++;
                      }
                      if(k==5)
                      {
                        break;
                      }                         

                    }
                    $rootScope.contentNews=container;


                    console.log(container);
                    }
                    // console.log(globalStore.artical.rss.channel["0"]);
                    
              }).fail(function(){
                // console.log("fails");
                $rootScope.articalState=0;
              })

            )
          .then(function(){

                            console.log('success, good');
                            $rootScope.$apply(function(){
                            $rootScope.notFinished=false;
                             $rootScope.replace=true; 
                            
                          });
                            if($rootScope.InpriceState==0)
                            {
                              $("#container").hide();
                              $("#chartAlert").show();
                              $("#chartAlert").html("Error! Failed to get Price data.");
                              $("#tableContent").hide();
                              $("#tableAlert").show();
                              $("#tableAlert").html("Error! Failed to get current stock data.");
                            }
                            else
                            {
                               $("#container").show();
                               $("#tableContent").show();
                               $("#tableAlert").hide();
                              $rootScope.fbShare=Inprice(xDate,priceData,volumeData);
                               $("#chartAlert").hide();
                            }
          },
              function(){
                             console.log('fail, sad');
                            $rootScope.$apply(function(){
                            $rootScope.notFinished=false;
                            $rootScope.replace=true; 
                           
            });
          });


                
                     
                     

                    
                      
                    

                   
                    

                    

                    

                    

                    

                    

                   

                    
                      
                                  
              
        

         };      
        
         
          $rootScope.BBANDS=function(event){
           event.preventDefault();
          if($rootScope.BBANDState==0)
          {
            $("#container").hide();
            $("#chartAlert").show();
            $("#chartAlert").html("Error! Failed to get BBANDS data.")
          }
          else
          {$("#chartAlert").hide();
         $("#container").show();
             $rootScope.fbShare=BBANDS($rootScope.BBANDSxIndicator,$rootScope.BBANDSMiddle,$rootScope.BBANDSUpper,$rootScope.BBANDSLower,'BBANDS',$rootScope.BBANDStiteName);
             
          }
          $rootScope.nameAgain='BBANDS';
          
          
        }; 


         $rootScope.STOCH=function(event){
           event.preventDefault();
         if($rootScope.stochState==0)
          {
            $("#container").hide();
            $("#chartAlert").show();
            $("#chartAlert").html("Error! Failed to get STOCH data.")
          }
          else
          {
            $("#chartAlert").hide();
             $("#container").show();
            $rootScope.nameAgain='stoch';
            $rootScope.fbShare=stoch($rootScope.STOCHxIndicator,$rootScope.STOCHSlowK,$rootScope.STOCHSlowD,'STOCH',$rootScope.STOCHtiteName);
             
          }
          
          
        }; 


        $rootScope.CCI=function(event){
           event.preventDefault();
           if($rootScope.CCIstate==0)
          {
            $("#container").hide();
            $("#chartAlert").show();
            $("#chartAlert").html("Error! Failed to get CCI data.")
          }
          else
          {

            $("#chartAlert").hide();
             $("#container").show();
           $rootScope.nameAgain='CCI';
          $rootScope.fbShare=CCI($rootScope.CCIxIndicator,$rootScope.CCIsingleData,'CCI', $rootScope.CCItiteName);   
          }         
          
        };  


        $rootScope.ADX=function(event){
           event.preventDefault();
          if($rootScope.ADXstate==0)
          {
            $("#container").hide();
            $("#chartAlert").show();
            $("#chartAlert").html("Error! Failed to get ADX data.")

          }
          else
          {
            $("#chartAlert").hide();
            $rootScope.nameAgain='ADX';
          $rootScope.fbShare=ADX($rootScope.ADXxIndicator,$rootScope.AADXsingleData,'ADX',$rootScope.ADXtiteName);
           $("#container").show();
          }
         
         
        }; 


         $rootScope.RSI=function(event){
           event.preventDefault();
          if($rootScope.RSIstate==0)
          {
            $("#container").hide();
            $("#chartAlert").show();
            $("#chartAlert").html("Error! Failed to get RSI data.")
          }
          else
          {
            $("#chartAlert").hide();
            $("#container").show();
             $rootScope.nameAgain='RSI';
             $rootScope.fbShare= RSI($rootScope.RSIxIndicator,$rootScope.RSIsingleData,'RSI',$rootScope.RSItiteName);    
              $("#container").show();  
          }
         
          
        };  


        $rootScope.EMA=function(event){
           event.preventDefault();
           if($rootScope.EMAstate==0)
          {
            $("#container").hide();
            $("#chartAlert").show();
            $("#chartAlert").html("Error! Failed to get EMA data.")
          }
          else
          {
            $("#chartAlert").hide();
            $rootScope.nameAgain='EMA';
        $rootScope.fbShare=EMA($rootScope.EMAxIndicator,$rootScope.EMAsingleData,'EMA',$rootScope.EMAtiteName);   
         $("#container").show();       
          }
         
          console.log('hello agaon');
        };  
        $rootScope.SMA=function(event){
           event.preventDefault();
           if($rootScope.SMAstate==0)
          {
            $("#container").hide();
            $("#chartAlert").show();
            $("#chartAlert").html("Error! Failed to get SMA data.")
          }
          else
          {
            $("#chartAlert").hide();
             $("#container").show();
            $rootScope.nameAgain='SMA';
             $rootScope.fbShare=SMA($rootScope.SMAxIndicator,$rootScope.SMAsingleData,'SMA',$rootScope.SMAtiteName);        
          }
          
          console.log('hello agaon');
        }; 
         $rootScope.SMAS=function(event){
           event.preventDefault();
            if($rootScope.InpriceState==0)
          {
            $("#container").hide();
            $("#chartAlert").show();
            $("#chartAlert").html("Error! Failed to get Price data.")
          }
          else
          {
            
            $("#chartAlert").hide();
             $("#container").show();
            $rootScope.nameAgain='Inprice';
            $rootScope.fbShare=Inprice($rootScope.InpricexDate,$rootScope.InpricepriceData,$rootScope.InpricevolumeData);       
          }
           
           
            console.log('hello price');
        };  
        $rootScope.MACD=function(event){
           event.preventDefault();
           if($rootScope.MACDState==0)
          {
            $("#container").hide();
            $("#chartAlert").show();
            $("#chartAlert").html("Error! Failed to get MACD data.")
          }
          else
          {
            $("#chartAlert").hide();
             $("#container").show();
             $rootScope.nameAgain='MACD';
            $rootScope.fbShare=MACD($rootScope.MACDxIndicator,$rootScope.MACDHist,$rootScope.MACDMACDD,$rootScope.MACDSignal,'MACD',$rootScope.MACDtiteName);            
          }
         
          console.log('hello agaon');
        };


        $rootScope.stock=function(event){
           event.preventDefault();
            $rootScope.stockShow=true;          
            $rootScope.tableCon=false;
            $rootScope.news=false;    
            if($rootScope.InpriceState==0)
          {
            $("#chartAlert").show();
            $("#chartAlert").html("Error! Failed to get Price data.")
          }
          else
          {
            $("#chartAlert").hide();
            stockChart($rootScope.STOCKfraph);
            console.log('hello price');          
          }
                                      
          
            
        };

        $rootScope.tableContent=function(event){
           event.preventDefault();
           if($rootScope.InpriceState==0)
              {
                
                $("#tableContent").hide();
                $("#tableAlert").show();
                $("#tableAlert").html("Error! Failed to get current stock data.");
              }
              else
              {
                
                 $("#tableContent").show();
                 $("#tableAlert").hide();
              
              }
           $rootScope.stockShow=false;          
            $rootScope.tableCon=true;
            $rootScope.news=false;     
                                    
         
        };  

        $rootScope.NewsFeeds=function(event){
           event.preventDefault();
           if($rootScope.articalState===0)
           {
              $("#firstPartArtical").hide();
              $("#secondPartArtical").show();
           }
           else{
              $("#secondPartArtical").hide();
              $("#firstPartArtical").show();
           }
           $rootScope.news=true; 
           $rootScope.stockShow=false;              
          $rootScope.tableCon=false; 
                                    
         
        };     

         $rootScope.fb=function(){
            var obj = {};          
            var exportUrl = $rootScope.fbShare.exporting.url;
            obj.options = JSON.stringify($rootScope.fbShare);
            obj.type = 'image/png';
            obj.async = true;
             $.ajax({
                  type: 'post',
                  url: exportUrl,
                  data: obj,
                  success: function (data) {
                    var urlFb=exportUrl + data;
                      FB.ui({
                      method: 'feed',
                      picture: urlFb
                    }, function(response){

                    });
                  }
                });
             console.log("I am here again fb");
        }; 


         $rootScope.drawAgain=function(){
          $rootScope.$on('$viewContentLoaded', function(){
            //Here your view content is fully loaded !!
             Highcharts.chart('container',$rootScope.fbShare);
          });       
          };

          $rootScope.loveIt=function(){
              if(typeof(Storage)!=='undefined'){
                var loveContent={
                  symbol:$rootScope.Symbol,
                  price:$rootScope.price,
                  change:$rootScope.change,
                  volume:$rootScope.volume
                };
                localStorage.setItem($rootScope.Symbol,JSON.stringify(loveContent));
              }else{
                console.log("error cannot support localStorage");
              }              
          };

          $rootScope.changeState=function(){
            $rootScope.loadFavrite==false;
          };

          
          
                 
}])



     .controller('loveControl', ['$scope','$rootScope',
      function loveControl($scope,$rootScope) {
        $scope.ifDisabled=true;
        this.name='love';
        $rootScope.loveStock=[];
        if($rootScope.loadFavrite==true){
            console.log("I am here for love");
            console.log(localStorage);

              for(var i=0;i<localStorage.length;i++)
              {

                var temp= localStorage.getItem(localStorage.key(i));
                var tempO=JSON.parse(temp);
                 console.log(i);
                 console.log(tempO);
                 tempO.price=parseFloat(tempO.price);
                 tempO.volume=parseInt(tempO.volume);
                 $rootScope.loveStock[i]=tempO;
                // $('#favoriteTable').append("<tr><th>"+tempO.symbol+"</th><th>"+tempO.price+"</th><th>"+tempO.change+"</th><th>"+tempO.volume+"</th></tr>")
              }
          //   $rootScope.$on('$viewContentLoaded', function(){
              
              
          // }); 
          }

          $scope.myFunc=function(){
             if($scope.selectedName=='Default')
            {
              $scope.firstTable=true;
              $scope.secondTable=false;
              $scope.ifDisabled=true;
            }
            else{
              $scope.firstTable=false;
              $scope.secondTable=true;
              $scope.ifDisabled=false;
                if($scope.selectedName=='Symbol')
                {
                  $scope.sortWay='symbol';
                }
                if($scope.selectedName=='Price')
                {
                  $scope.sortWay = 'price';
                }
                if($scope.selectedName=='Change')
                {
                  $scope.sortWay = 'change';
                }
                if($scope.selectedName=='Change Percent')
                {
                  $scope.sortWay = 'change';
                }
                if($scope.selectedName=='Volume')
                {
                  $scope.sortWay = 'volume';
                }

                if($scope.SecondselectedName=='Descending')
                {
                  $scope.sortWay = '-'+$scope.sortWay;
                }
              
            }
          };

           $scope.deleteLove=function(symbol){
            console.log("delete item" +symbol );
           };

           $scope.search=function(symbol){
            console.log("go to another page" +symbol );
           };

         




      }]);




 
})(window.angular);

