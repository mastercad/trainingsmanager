
	var _b_blur = false;
		
		function json_serialize (mixed_value) {
			
			var val = '';
		    
			val += "{";
			
			for( key in mixed_value)
			{
//				if( !mixed_value[key].length || mixed_value[key] == undefined)
//					mixed_value[key] = '';
				
//				alert( key + " => " + mixed_value[key]);
				
				if( typeof mixed_value[key] == 'object')
				{
					val += "\"" + key + "\":" + json_serialize( mixed_value[key]) + ",";
				}
				else
				{
					if( mixed_value[key] == undefined)
					{
						val = '';
					}
					else
					{
						val = mixed_value[key];
					}
					val += "\"" + key + "\":\"" + mixed_value[key] + "\",";
				}
			}
			if( val.substr( val.length - 1, val.length) == ',')
			{
				val = val.substr( 0, val.length - 1);
			}
			val += "}";

			val = val.replace( 'undefined', '');
			val = val.replace( '{}', '[]');
			
		    return val;
		}

		
		function GetOffset (object, offset) {
		    if (!object)
		        return;
		    offset.x += object.offsetLeft;
		    offset.y += object.offsetTop;

		    GetOffset (object.offsetParent, offset);
		}

		function GetScrolled (object, scrolled) {
		    if (!object)
		        return;
		    scrolled.x += object.scrollLeft;
		    scrolled.y += object.scrollTop;

		    if (object.tagName.toLowerCase () != "html") {
		        GetScrolled (object.parentNode, scrolled);
		    }
		}

		function GetTopLeft ( div) {

		    var offset = {"x" : 0, "y" : 0};
		    GetOffset (div, offset);

		    var scrolled = {"x" : 0, "y" : 0};
		    GetScrolled (div.parentNode, scrolled);

		    posX = offset.x - scrolled.x;
		    posY = offset.y - scrolled.y;
		}


		var Url = {
		 	
			// public method for url encoding
			encode : function (string)
			{
				string = string.replace( /€/ig, "&euro;");
				return escape( this._utf8_encode(string));
			},
		 
			// public method for url decoding
			decode : function (string)
			{
				return this._utf8_decode(unescape(string));
			},
		 	
			// private method for UTF-8 encoding
			_utf8_encode : function (string)
			{
				string = string.replace(/\r\n/g,"\n");
				var utftext = "";
		 
				for (var n = 0; n < string.length; n++)
				{
					var c = string.charCodeAt(n);
		 
					if (c < 128)
					{
						utftext += String.fromCharCode(c);
					}
					else if((c > 127) && (c < 2048))
					{
						utftext += String.fromCharCode((c >> 6) | 192);
						utftext += String.fromCharCode((c & 63) | 128);
					}
					else
					{
						utftext += String.fromCharCode((c >> 12) | 224);
						utftext += String.fromCharCode(((c >> 6) & 63) | 128);
						utftext += String.fromCharCode((c & 63) | 128);
					}
				}
				return utftext;
			},
		 
			// private method for UTF-8 decoding
			_utf8_decode : function (utftext)
			{
				var string = "";
				var i = 0;
				var c = c1 = c2 = 0;
		 
				while ( i < utftext.length )
				{
					c = utftext.charCodeAt(i);
		 
					if (c < 128)
					{
						string += String.fromCharCode(c);
						i++;
					}
					else if((c > 191) && (c < 224))
					{
						c2 = utftext.charCodeAt(i+1);
						string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
						i += 2;
					}
					else
					{
						c2 = utftext.charCodeAt(i+1);
						c3 = utftext.charCodeAt(i+2);
						string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
						i += 3;
					}
				}
				return string;
			}
		};


		function trim(str, chars)
		{
			if(str)
			{
				return ltrim(rtrim(str, chars), chars);
			}
			return str;
		}
		 
		function ltrim(str, chars) {
			chars = chars || "\\s";
			return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
		}
		 
		function rtrim(str, chars) {
			chars = chars || "\\s";
			return str.replace(new RegExp("[" + chars + "]+jQuery", "g"), "");
		}
				
		function GeneratePassword() {
		    
			if(obj_blur_input)
			{
				obj_blur_input.value = '';
			}
			else
			{
				obj_blur_input = document.aForm.passField;
			}
			
		    if (parseInt(navigator.appVersion) <= 3) { 
		        alert("Sorry this only works in 4.0 browsers"); 
		        return true; 
		    }
		    
		    var length=8;
		    var sPassword = "";
		    length = document.aForm.charLen.options[document.aForm.charLen.selectedIndex].value;
		    
		    var noPunction = (document.aForm.punc.checked);
		    var randomLength = (document.aForm.rLen.checked);
		    
		    if (randomLength) { 
		        length = Math.random(); 
		        
		        length = parseInt(length * 100);
		        length = (length % 7) + 6;
		    }
		    
		    
		    for (var i = 0; i < length; i++) {
		    
		        numI = getRandomNum();
		        if (noPunction) { while (checkPunc(numI)) { numI = getRandomNum(); } }
		        
		        sPassword = sPassword + String.fromCharCode(numI);
		    }
		    
		    obj_blur_input.value = sPassword;
		    
		    return true;
		}

		function getRandomNum() {
		        
		    // between 0 - 1
		    var rndNum = Math.random();

		    // rndNum from 0 - 1000    
		    rndNum = parseInt(rndNum * 1000);

		    // rndNum from 33 - 127        
		    rndNum = (rndNum % 94) + 33;
		            
		    return rndNum;
		}

		function checkPunc(num) {
		    
		    if ((num >=33) && (num <=47)) { return true; }
		    if ((num >=58) && (num <=64)) { return true; }    
		    if ((num >=91) && (num <=96)) { return true; }
		    if ((num >=123) && (num <=126)) { return true; }
		    
		    return false;
		}

		function switchVisibility(obj)
		{
			if( obj.b_visible)
			{
				hide(obj);
			}
			else
			{
				show(obj);
			}
		}
		
		function hex2num(hex)
		{
			if(hex.charAt(0) == "#")
			{ 
				hex = hex.slice(1);
			}
			hex = hex.toUpperCase();
			var hex_alphabets = "0123456789ABCDEF";
			var value = new Array(3);
			var k = 0;
			var int1,int2;
			 
			for(var i = 0; i < 6; i += 2)
			{
				int1 = hex_alphabets.indexOf(hex.charAt(i));
				int2 = hex_alphabets.indexOf(hex.charAt(i+1));
				value[k] = (int1 * 16) + int2;
				k++;
			}
			 return(value);
		}

		//Give a array with three values as the argument and the function will return
		// the corresponding hex triplet.
		function num2hex(triplet)
		{
			var hex_alphabets = "0123456789ABCDEF";
			var hex = "#";
			var int1,int2;

			for(var i=0;i<3;i++)
			{
				int1 = triplet[i] / 16;
				int2 = triplet[i] % 16;

				hex += hex_alphabets.charAt(int1) + hex_alphabets.charAt(int2);
			}
			return(hex);
		}
		
		function startTimer()
		{
			
		}
		
		function endTimer()
		{
			
		}
		
		var delay = (function()
		{
		  var timer = 0;
		  return function(callback, ms)
		  {
		    clearTimeout (timer);
		    timer = setTimeout(callback, ms);
		  };
		})();

		// workaround um ein unbekanntes console event abzufangen
		if (typeof console == "undefined")
		{
		    this.console = {"log": function() {}};
		}
		
		Object.size = function(obj)
		{
		    var size = 0;
		    for (var key in obj)
		    {
		        if (obj.hasOwnProperty(key)) size++;
		    }
		    return size;
		};
		
		Array.prototype.size = function ()
		{
			var l = this.length ? --this.length : -1;
			for (var k in this)
			{
				l++;
			}
			return l;
		};
		
		Array.prototype.CAD_join = function (seperator)
		{
			var string = '';
			
			for (var key in this)
			{
				if(this.hasOwnProperty(key))
				{
					string += key + this[key] + seperator;
				}
			}
			string = string.substring(0, parseInt(string.length) - parseInt(seperator.length));
			return string;
		};
                
                function isNumber(n) {
                    return !isNaN(parseFloat(n)) && isFinite(n);
                  }