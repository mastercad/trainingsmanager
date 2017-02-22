/*
 * namespace meiner toolsammlungen
 */

var CAD = {
  
  /* enthält die geöffneten objecte */
  a_objects: [],
  
  /* z-index der sperre */
  cad_sperre_z_index: 0,
  
  /* 
   * @TODO Sperre noch als singleton ausarbeiten
   * 
   * instance für sperre, da sie nur einmal aufgerufen können
   * werden soll und ich es nicht hinbekomme, sie von Wrapper 
   * erben zu lassen, auf ihr eigenes parent zugreifen zu 
   * lassen und sie als singleton zu erstellen, aber so 
   * gehts auch erstmal.
   */
  instance_sperre: null,
  
  /* gibt das array der geöffneten objecte zurück */
  getOpenedObjects: function() {
//	  console.log("Opened Objects:");
//	  console.log(this.a_objects);
    return this.a_objects;
  },
  
  /* gibt die anzahl der geöffneten objecte zurück */
  getCountOpenedObjects: function() {
    return this.a_objects.length;
  },
  
  /* fügt ein neues object ans ende des arrays */
  addOpenedObject: function(obj) {
	if( !this.cad_sperre_z_index)
	{
	   this.cad_sperre_z_index = jQuery('#sperre').css('z-index');
	}
	// alle elemente unter die sperre verschieben
	this.moveAllUnderSperre();
	jQuery(obj).css('z-index', parseInt(this.cad_sperre_z_index) + 1);
    this.a_objects.push(obj);
	this.moveLastOverSperre();
  },
  
  moveAllUnderSperre : function() {
//	  console.log("In moveAllUnderSperre");
//	  console.log(this.a_objects);
    if ( parseInt(this.a_objects.length) > 0)
	{
	   for(var i = this.a_objects.length - 1; i >= 0; i--) {
	//	 console.log(this.a_objects[i]._element);
	     jQuery(this.a_objects[i]._element).css('z-index', this.cad_sperre_z_index - i);
	   }
	}
  },
  
  moveLastOverSperre : function() {
    if ( parseInt(this.a_objects.length) > 0 &&
         this.a_objects[this.a_objects.length -1] != CAD.instance_sperre)
    {
//		console.log("Verschiebe folgendes element über sperre:");
//	    console.log(this.a_objects[this.a_objects.length - 1]._element);
		jQuery(this.a_objects[this.a_objects.length - 1]._element).css('z-index', this.cad_sperre_z_index + 1);
    }
  },
  
  removeLastObject : function(obj) {
    if ( parseInt(this.a_objects.length) > 0)
	{
//    	console.log("in RemoveLastObject!");
//		console.log(this.a_objects);
//	    console.log("in CAD.removeLastObject! Remove " + parseInt(this.a_objects.length));
	    this.a_objects[this.a_objects.length - 1].close(true);
//	    this.a_objects.splice(this.a_objects.length - 1, 1);
		this.moveLastOverSperre();
	}
  },
  
  /* sucht alle objecte vom selben typ und entfernt sie */
  removeOpenedObject: function(obj) {
    for(var i = 0, len = this.a_objects.length; i < len; i++) {
      if(this.a_objects[i] === obj) {
    	this.a_objects[i].close(true);
	    this.a_objects.splice(i, 1);
      }
    }
	this.moveLastOverSperre();
  }
}; 
