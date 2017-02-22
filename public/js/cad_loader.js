/*
 * einfache loader klasse, die an den wrapper gekoppelt
 * ist und damit gemeinsam mit message, sperre
 * und catch_esc interagieren können
 */
CAD.Loader = function(el) {
  
  if ( !el ) {
    this._element = jQuery('<img id="loader" src="/images/content/statisch/grafiken/ajax-loader_black_trans.gif" alt="loading..." />');
    jQuery('body').add(this._element);
  } else {
    this._element = el;
  }
};

CAD.Loader.prototype = new CAD.Wrapper;
// CAD.Loader.prototype.constructor = CAD.Loader;
CAD.Loader.parent = CAD.Wrapper.prototype;

CAD.Loader.prototype.open = function(src) {
  
  if ( null === this._element)
  {
    this._element = document.getElementById('loader');
  }
  
  CAD.instance_sperre.open();
  CAD.Loader.parent.element = this._element;
  CAD.Loader.parent.open(this);

	if(!jQuery('#loader').length)
	{
		jQuery('body').append('<img id="loader" src="#" style="position: absolute;" alt="wird geladen..." />');
	}
	
	if(!jQuery('.message').is(':visible'))
	{
		if(src)
		{
			jQuery('#loader').attr("src", src);
		}
		else
		{
			jQuery('#loader').attr("src", '/images/content/statisch/grafiken/ajax-loader_black_trans.gif');
		}
		jQuery('#loader').CAD_center().fadeIn(function()
		{
			jQuery(this).data("data-flag-visible", true);
		});
	}
	
  return this;
};

CAD.Loader.prototype.close = function(b_hide_sperre) {
  
  CAD.Loader.parent.close(this);
  if ( true === b_hide_sperre)
  {
	  CAD.instance_sperre.close();
  }

	jQuery('#loader').data("data-flag-visible", false);
	jQuery('#loader').hide();
	
  return this;
};
