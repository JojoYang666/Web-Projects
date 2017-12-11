$('button #id').click(function (e) {
  //e.preventDefault();
  console.log("I am here again fb");
  FB.ui({
  method: 'feed',
  link: 'https://developers.facebook.com/docs/',
  caption: 'An example caption',
}, function(response){

});




})