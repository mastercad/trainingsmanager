CAD.Catch_ESC = function() {
  
  this.init = function() {

	jQuery('html').keydown(function (e)
	{
	    if(parseInt(e.keyCode) == 27)
	    {
	    	CAD.removeLastObject();
	        return false;
	    }
	    return true;
	});
  };
  
  this.close = function(e) {
//    var a_opened_objects = CAD.getOpenedObjects();
//    if(a_opened_objects.length > 0) {
//      a_opened_objects[a_opened_objects.length - 1].close();
//    }
    
    return this;
  };
  
  this.closeAll = function() {
    var a_opened_objects = CAD.getOpenedObjects();
    for(var i = a_opened_objects.length - 1; i >= 0; i--) {
      a_opened_objects[i].close();
    }
    
    return this;
  };
};

CAD.Catch_ESC.prototype = new CAD.Wrapper;
