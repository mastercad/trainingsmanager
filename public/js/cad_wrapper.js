
CAD.Wrapper = function() {
  
  this.element = undefined;
};
  
CAD.Wrapper.prototype.open = function(obj) {
  
  if(undefined === obj) {
    obj = this;
  }
  
  if ( true !== obj.b_visible) {
    obj.b_visible = true;
    this.addObject(obj);
  }
  
  return this;
};

CAD.Wrapper.prototype.close = function(obj) {
  
  if(undefined === obj) {
    obj = this;
  }
  
  if ( false !== obj.b_visible) {
    obj.b_visible = false;
    this.removeObject(obj);
  }
  
  return this;
};

CAD.Wrapper.prototype.setElement = function(el) {
  if(undefined != el) {
    this.element = el;
  }
};

CAD.Wrapper.prototype.getElement = function() {
  return this.element;
};

CAD.Wrapper.prototype.setStyle = function(prop, value) {
  this.element.style[prop] = value;
  
  return this;
};

CAD.Wrapper.prototype.addObject = function(obj) {
  CAD.addOpenedObject(obj);
  
  return this;
};

CAD.Wrapper.prototype.removeObject = function(obj) {
  CAD.removeOpenedObject(obj);
  
  return this;
};
