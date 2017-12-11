(function () {
  'use strict';
  angular
      .module('MyApp',['ngMaterial', 'ngMessages', 'material.svgAssetsCache'])
      .controller('autoCom', ['$scope', '$http',
      	function autoCom($scope, $http){
      		$scope.query=function(searchText){
	  		return $http.get(searchText)
	  		.then(function(data){
  			return data;
  		});
  	};
      	}]);
})();


/**
Copyright 2016 Google Inc. All Rights Reserved. 
Use of this source code is governed by an MIT-style license that can be foundin the LICENSE file at http://material.angularjs.org/HEAD/license.
**/