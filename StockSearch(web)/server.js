/**
 * Module dependencies.
 */
var express = require('express');
var httpProxy = require('http-proxy');
var bodyParser = require('body-parser');
var xmlToJson =require('xml2js');
var requestt = require('ajax-request');

var ForwardingUrl = 'https://www.alphavantage.co/';
var parseString=xmlToJson.parseString;
// var ForwardingUrl = 'https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=MSFT&apikey=N8NYET91XJ8XEV8R';

var proxyOptions = {
    changeOrigin: true,
     ignorePath: true
};

httpProxy.prototype.onError = function (err) {
    console.log(err);
};

var chartProxy = httpProxy.createProxyServer(proxyOptions);


// console.log('Forwarding API requests to ' + ForwardingUrl);

// Node express server setup.
var server = express();

// server.use(function(req, res, next) {
//   console.log(`${req.method} request for '${req.url}' - ${JSON.stringify(req.body)}`);
//   next();
// });

server.set('port', 8081);
server.use(express.static(__dirname + '/app'));

server.all("/*", function(req, res) {
  var url =req.url;
  if(url.substring(1,5)=='auto')
  {

    var symbol=url.substring(5,url.length);
    var autoUrl='http://dev.markitondemand.com/MODApis/Api/v2/Lookup/json?input='+symbol;
    chartProxy.web(req, res, {target: autoUrl});
    console.log(autoUrl);
  }
  if(url.substring(1,7)=='symbol')
  {
   var symbol=url.substring(8,url.length);
    // console.log(symbol);
      // console.log('url is'+indicatorUrl);
    ForwardingUrl=ForwardingUrl+'query?function=TIME_SERIES_DAILY&symbol='+symbol+'&outputsize=full&apikey=N8NYET91XJ8XEV8R';
    chartProxy.web(req, res, {target: ForwardingUrl});
  }
  if(url.substring(1,8)=='artical')
  {
    var seperate = url.indexOf('?');
    var symbol=url.substring(seperate+1,url.length);
    var articalUrl='https://seekingalpha.com/api/sa/combined/'+symbol+'.xml';
    requestt({
      url:articalUrl,
      method:'GET'
    },function(err,ress,body)
    {
      // console.log(body);
      parseString(body, function (err, result) {
      // console.log(result);
      res.send(result);
      });
      
    });
    
  }
  if(url.substring(1,8)!='artical'&&url.substring(1,7)!='symbol'&&url.substring(1,5)!='auto')
  {
    ForwardingUrl = 'https://www.alphavantage.co/';
    var seperate = url.indexOf('?');
    var indicatorName=url.substring(1,seperate);
    var symbol=url.substring(seperate+1,url.length);
    // console.log(indicatorName);
    // console.log(symbol);
      if(indicatorName=='STOCH'){
        var indicatorUrl=ForwardingUrl+'query?function='+indicatorName+'&symbol='+symbol+'&interval=daily&slowkmatype=1&slowdmatype=1&apikey=N8NYET91XJ8XEV8R';
        console.log(indicatorUrl);
        chartProxy.web(req, res, {target: indicatorUrl});
      }
      else if(indicatorName=='BBANDS'){
        var indicatorUrl=ForwardingUrl+'query?function='+indicatorName+'&symbol='+symbol+'&interval=daily&time_period=5&series_type=close&nbdevup=3&nbdevdn=3&apikey=N8NYET91XJ8XEV8R';
        console.log(indicatorUrl);
        chartProxy.web(req, res, {target: indicatorUrl});
      }
      // else if(indicatorName=='MACD'){
      //   var indicatorUrl=ForwardingUrl+'query?function='+indicatorName+'&symbol='+symbol+'&interval=daily&time_period=10&series_type=close&apikey=N8NYET91XJ8XEV8R';
      //   console.log(indicatorUrl);
      //   chartProxy.web(req, res, {target: indicatorUrl});
      // }
      else{
        var indicatorUrl=ForwardingUrl+'query?function='+indicatorName+'&symbol='+symbol+'&interval=daily&time_period=10&series_type=close&apikey=N8NYET91XJ8XEV8R';
        console.log('url is'+indicatorUrl);
        chartProxy.web(req, res, {target: indicatorUrl});
      }
  }
 
 
    // chartProxy.web(req, res, {target: SMA});
});

server.use(bodyParser.json());
server.use(bodyParser.urlencoded({
    extended: true
}));

// Start Server.
server.listen(server.get('port'), function() {
    console.log('Express server listening on port ' + server.get('port'));
});