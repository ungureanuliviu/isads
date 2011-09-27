var OTT = {};
var jQ = jQuery;
OTT.UI = {};
OTT.CACHE = {prices:{}, session:{request:{fields:{}}}};

OTT.UI.Overlay = {
			
			elems: 	{
						
					},
			
			html: {
					transparent_bg:'<div class="transparent_overlay" class="hidden"></div>',
					overlay:'<div class="overlay_content_wrap" class="hidden"><div class="overlay_positioner"><div class="overlay_outer"></div></div></div>',
					content:'<div class="overlay"></div>'
				},
			
			selector: {
				body:'body',
				overlay_parent:'.overlay_outer',
				overlay:'.overlay'
			},
			
			prepare: function() {
				
				this.elems.trans_back = this.elems.trans_back || jQ(this.html.transparent_bg).appendTo('body');
				this.elems.overlay_wrap = this.elems.overlay_wrap || jQ(this.html.overlay).appendTo('body');
				
				this.elems.overlay = this.elems.overlay_wrap.find(this.selector.overlay_parent);
				
				var bodyHeight=jQ(this.selector.body).height();
				var contentHeight = OTT.UI.get_content_height();
				
				this.elems.trans_back.removeClass('hidden').
							height(bodyHeight>contentHeight?bodyHeight:contentHeight).
							fadeTo(100,0.5);
							
				//if that's IE six we need to hide selects as well
				if (jQ.browser.msie && parseInt(jQ.browser.version,10)<7) {
					
					jQ('select').css('visibility','hidden');
					
				}
			
			},
			
			hide: function(callback) {
				
				var self = this;
				var all_elems = self.elems.trans_back.add(OTT.UI.Overlay.elems.overlay_wrap);
				
				jQ(all_elems).fadeTo('fast',0, function() {
	
					jQ(all_elems).addClass('hidden');
				
					if (jQ.browser.msie && parseInt(jQ.browser.version,10)<7) {
				
						jQ('select').css('visibility','visible');
				
					}
				
				});
				
				self.elems.content.unbind_events();
				
				if (callback) {
						setTimeout(function() {callback.call(self.elems.overlay)},1);
				}
		
			},
			
			show: function(content, callback) {
				
				var self = this;
				
				//this function will be massively different for IE6
				//so branching is used to create once not to bother
				//good browsers with this trivia
				var unveil_and_position = (function() {
						
					
							return function() {
								self.elems.overlay_wrap.removeClass('hidden').fadeTo(100,1, function() {
								if (jQ.browser.msie && parseInt(jQ.browser.version,10)<7) {
								//IE 6 keeps forgetting what 100% is when the overlay is shown
										jQ('.overlay_content_wrap').height(document.documentElement.clientHeight);
									}
								}
								)
							}
						
				})();
				
				if (content) {
				
					self.prepare();
					self.elems.content = content;

					unveil_and_position();
				
					self.elems.overlay.html(content.content);
					
					content.bind_events();
					
					if (callback) {
						setTimeout(function() {callback.call(self.elems.overlay)},1);
					}
				
				}
								
			}


}

OTT.UI.Overlay_content = 	function(content, events) {
								
								this.content = content || '';
								
								this.events = events || [];
								
								this.events.push(
															{
																selector:'.hide_overlay',
																event:'click',
																fn:function() {OTT.UI.Overlay.hide();return false}
															}
												);
								
								this.bind_events = function() {
									
									for (var e=0;e<this.events.length;e++) {
										var event = this.events[e];
										event.event = event.event || 'click';
										jQ(event.selector).bind(event.event, event.fn);
									}
									
								};
								
								this.unbind_events = function() {
									
									for (var e=0;e<this.events.length;e++) {
										var event = this.events[e];
										event.event = event.event || 'click';
										jQ(event.selector).unbind(event.event);
									}
									
								};
								
								

							};

//creates dropdown
OTT.UI.populate_select =        function(source,value,label,preselected) {

								//value and label are in relation to source

								r = [];

								for (var o=0;o<source.length;o++) {

										r.push('<option '+
														(preselected === o ? 'selected="selected "':'')+
														(value?'value="'+source[o][value]:'')+'"'+
														'>'+
														label.call(source[o])+
														'</option>');
								};

								return r.join("\n");

                            };

OTT.UI.get_content_height = function () {

	return jQ('#sur_head').height()+jQ('#sur_content').height()+jQ('#sur_foot').height();

};
							
OTT.Domain_search = function(API, UI, CONF) {
	
	var self = this;
	this.API = new API(self);
	this.UI = new UI(self);
	this.CONF = new CONF(self)
	
	$(self).bind('domain_results_ready', function(e,data_obj) {
		self.present_results(data_obj);
	});
	
	$(self).bind('domain_call_unsuccessful', function(e,data_obj) {
	});
	
	$(self).bind('domain_chosen', function(e,data_obj) {
		var r = new OTT.Request(OTT.CACHE.session.request.action, 'post', data_obj);
		r.send();
	});
	
	$(self).bind('domain_search_initiated', function(e,data_obj) {
		self.UI.display_search_progress();
		self.get_results(data_obj);
	});
	
	this.get_results = function(data_obj) {
		
		self.get_more_results(data_obj);
		
	}
	
	this.get_more_results = function(data_obj) {
	
		if (this.CACHE.extensions_index && this.CACHE.extensions_index < this.CONF.extensions.length) {
			this.CACHE.extensions_index++;
		}
		
		this.CACHE.current_extensions = this.CONF.extensions[this.CACHE.extensions_index];
		
		this.API.check_domain_availability(data_obj);
			
	}
	
	this.present_results = function(data_obj) {
			
		this.UI.present_results(data_obj);
	}
		
	this.init = function() {
		this.UI.capture_domain_search();
	}
	//all should be static
	this.CACHE = {
		extensions_index:0,
		current_extensions:[]
	};

};

OTT.Request = function(action, method, fields) {
	
	var form_html = ['<form action="'+(action||'')+'" method="'+(method||'post')+'">'];
	
	for (var i=0; i<fields.length; i++) {
		
		form_html.push('<input type="'+(fields[i].type||'hidden')+'" name="'+fields[i].name+'" value="'+fields[i].value+'" />');
			
	}
	
	form_html.push('</form>');
	
	var $form = jQ(form_html.join(''));
	
	this.send = function() {
		jQ('body').append($form);
		$form.submit();
	}
	
	var destroy = function() {
		$form.remove();
	}
};

F= {};


F.create_cookie =		function(name,value,days) {
									if (days) {
										var date = new Date();
										date.setTime(date.getTime()+(days*24*60*60*1000));
										var expires = "; expires="+date.toGMTString();
									}
									else {
										var expires = "";
									}
									document.cookie = name+"="+value+expires+"; path=/";
						};

F.read_cookie = 		function (name) {
									var nameEQ = name + "=";
									var ca = document.cookie.split(';');
									for (var i=0; i < ca.length; i++) {
										var c = ca[i];
										while (c.charAt(0)==' ') {
											c = c.substring(1,c.length);
										}
										if (c.indexOf(nameEQ) == 0) {
											return c.substring(nameEQ.length,c.length);
										}
									}
									return null;
						};

F.erase_cookie =		function(name) {
									DAC.utils.createCookie(name,"",-1);
						};
								
F.get_hostname_extension = function(domain_input, extensions) {
	
	var hostname =	domain_input.toLowerCase(),
		extension =	'';
	
	if(domain_input.indexOf('www')===0){
		//get rid of www and assign new value to var
		hostname = domain_input.replace('www.', '');		
	};

	//roughly sanitize
	hostname = hostname.replace(/[^\w-.]/g,'');
	
	//correct order of validating domain
	//1. sanitize roughly
	//this will make sure that space or weird characters in final position
	//don't interfere with extension recognision
	//like www.-sdas-12()&.com%
	//2. identify and chop off extension
	//3. sanitize thoroughly the remaining hostname
	
		for (var i=0;i<extensions.length;i++) {
			
			var ext = extensions[i];
			//if extension can be found in domain input
			//and found extension is in the last position
			//and found extension is the longest found extension
			if (hostname.lastIndexOf(ext) > 0 && hostname.lastIndexOf(ext) + ext.length === hostname.length && ext.length>extension.length) {
					extension = ext;
					var ext_index = hostname.lastIndexOf(ext);
			}
		}
	
	
	
	if (extension) {
		hostname = hostname.substring(0,ext_index);
	}
	
	return {
				hostname:hostname
				,
				extension:extension
		
			};
			
};


F.cleanup_hostname = function(hostname, extension) {
	
	//3-63 characters, alphanumeric+'-'
	var re=/[^\w-]/g;

	//add dot for name
	if (extension==='.name') {var re=/[^\w\.{1}-]/g}

	var re2=/(^-)-*/;
	var re3=/-*(-$)/;
	var re4=/_/g;


	hostname=hostname.replace(re,'').replace(re2,'').replace(re3,'').replace(re4, '').toLowerCase();

	hostname=hostname.substr(0,63);

	//change it to an empty string if it's undefined
	hostname=hostname||'';

	return hostname;

};

F.to_price =						function (num_amount, str_currency) {
										
										//returns nicely formatted price
										//with 2 decimal places
										
										var price, symbol;
										
										if (typeof num_amount === 'number') {
											
											switch (str_currency) {
											
													case 'GBP':
													symbol='&pound;';
													break;
													
													case 'USD':
													symbol='&#36;';
													break;
													
													default:
													symbol='&pound;';
													break;
											
											}
											
											return symbol+num_amount.toFixed(2);
										
										}
										
									
									};

F.getScroll =						function() {
	
										var scrOfX = 0, scrOfY = 0;
	
										if( typeof( window.pageYOffset ) == 'number' ) {
										//Mozilla compliant
											scrOfY = window.pageYOffset;
											scrOfX = window.pageXOffset;
										}
										
										else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
											//DOM compliant
											scrOfY = document.body.scrollTop;
											scrOfX = document.body.scrollLeft;
										}
										
										else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
											//IE6 standards compliant mode
											scrOfY = document.documentElement.scrollTop;
											scrOfX = document.documentElement.scrollLeft;
										}
										
										return [ scrOfX, scrOfY ];
			
									};

F.toJSONString = 			function(obj) {
			
									var isArray=function(_obj) {
									return _obj instanceof Array;
									};
									
									var isObject=function(_obj) {
									return (_obj instanceof Object) && !(_obj instanceof Array);
									};
									
									var isString=function(_obj) {
									return !!(typeof _obj =='string');
									};
									
									var isNumber=function(_obj) {
									return !!(typeof _obj =='number');
									};
									
									var qt = function(str) {
									return "\""+str+"\"";
									};
											if (isArray(obj)) {
													var output=[];
													for (var i=0,l=obj.length;i<l;i++) {
													output.push(arguments.callee(obj[i]));
													}
													return '['+output.join(', ')+']';
											}
									
											if (isObject(obj)) {
													var output=[];
													for (var i in obj) {
													output.push(qt(i)+":"+arguments.callee(obj[i]));
													}
													return '{'+output.join(', ')+'}';
											}
									
											if (isString(obj)) {
													return qt(obj);
											}
									
											if (isNumber(obj)) {
													return obj;
											}
									
								};

F.toObject = 					function(str_JSON) {
									if (str_JSON.indexOf('{')==0 && str_JSON.lastIndexOf('}')==str_JSON.length-1) {
										return eval('(' + str_JSON + ')');
									}
									else {
										return null;
									}
								};

OTT.hoverTooltip = 							function(){
															jQ("td.plain em").hover(
																						function () {
																									jQ(this).next().fadeIn(50);
																									},
																						function() {
																									jQ(this).next().fadeOut(50);
																									});
							
															jQ("td.plain span").hover(
																						function () {
																									jQ(this).next().fadeIn(50);
																									},
																						function() {
																									jQ(this).next().fadeOut(50);
																									});
							
							
															};
															
OTT.passwordStrength = 						function(password){

														var score = 0;
    
														password = jQ.trim(password);
														//username = jQ.trim(username);
														
														function checkRepetition(pLen,str) {
														res = ""
														for ( i=0; i<str.length ; i++ ) {
															repeated=true;
															for (j=0;j < pLen && (j+i+pLen) < str.length;j++) {
																repeated=repeated && (str.charAt(j+i)==str.charAt(j+i+pLen));
															}
															if (j<pLen) {repeated=false}
															if (repeated) {
																i+=pLen-1;
																repeated=false;
															}
															else {
																res+=str.charAt(i);
															}
														}
															return res;
														};
														
														if (!password) {
															return 'no_input';		
														}
														
														//password < 4
														if ( password.length < 6 ) {
															return 'too_short';
														}
														
														//password == username
														/*if (username && (password.toLowerCase() === username.toLowerCase())) { 
															return 'matches_login';
														}*/
														
														//password length
														score += password.length * 4;
														score += ( checkRepetition(1,password).length - password.length ) * 1;
														score += ( checkRepetition(2,password).length - password.length ) * 1;
														score += ( checkRepetition(3,password).length - password.length ) * 1;
														score += ( checkRepetition(4,password).length - password.length ) * 1;
													
														//password has 3 numbers
														if (password.match(/(.*[0-9].*[0-9].*[0-9])/))  {
															score += 5;
														}
														
														//password has 2 sybols
														if (password.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)) {
															score += 5;
														}
														
														//password has Upper and Lower chars
														if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
															score += 10;
														}
														
														//password has number and chars
														if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) {
															score += 15;
														}
														//
														//password has number and symbol
														if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([0-9])/)) {
															score += 15;
														}
														
														//password has char and symbol
														if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([a-zA-Z])/)) {
															score += 15;
														}
														
														//password is just a nubers or chars
														if (password.match(/^\w+$/) || password.match(/^\d+$/) ) {
															score -= 10;
														}
														
														if (password.length < 6) {
															score -=10;	
														}
														
														//verifing 0 < score < 100
														if ( score < 0 ) {
															score = 0;
														}
														if ( score > 100 ) {
															score = 100;
														}
														
														if (score < 34 )  {
															return 'weak';
														}
														if (score < 68 ) {
															return 'good'
														}
														
														return 'strong'
												};
												
OTT.measure_password_strength = 			function () {
													
												setInterval(function () {

													jQ('input.pass').each(function(i,val) {
														
														var current_value = jQ(val).val();

															jQ('#password_strength_'+(i+1)+' ul').attr("class","").addClass(OTT.passwordStrength(current_value));
															
															

														
													})

													},250);
													
											}
											
OTT.show_forward_input = 					function () {
				
													var t = jQ(this);
    												var name= t.attr('name');
    												var number = name.replace('drop_down_action_','');

    												var tr = jQ('.drop_down_action_'+number);
													var tr_psw = jQ('.psw_strength_'+number);
																	
													if (t.val()!=='mail_forward') {
														tr.addClass('hide');
														tr_psw.removeClass('hide');
													}
													else {
														tr.removeClass('hide');
														tr_psw.addClass('hide');
													}
											
											}
											
jQuery.fn.tooltip_on_hover = function() {
	
	//only augument this class

		this.hover(
			
			function(e) {
				var self = this;
				setTimeout(function() {
				jQ(self).siblings('.extra_info')./*animate({opacity:1},50)*/
					show();
				},250);
			},
			
			function(e) {
				var self = this;
				setTimeout(function() {
				jQ(self).siblings('.extra_info')./*animate({opacity:0},50)*/
					hide();
				}, 250);
			}
		
		);
	
	return this;

};
OTT.otherBrands =
			function () {
					//jQuery("body:not([class*=cp_page])").css("background-position", "left 10px"); 	 
					//jQuery("body:not([class*=cp_page]) #skipnav").after('<div id="blue_top_bar"></div>');
                                        
					// add code on presales
                                        jQuery("body:not([class*=cp_page]):not([class*=blog_page]) #sur_head_wrapper").prepend('<div id="show_other_brands"><a href="#">SEE OUR OTHER BRANDS</a></div><div id="other_brands"><div><a target="_blank" href="http://www.donhost.co.uk/"><img class="floatRight" src="library/images/v2/dh_minibanner.png" alt="donhost.co.uk"></a><a target="_blank" href="http://www.webfusion.co.uk/"><img src="library/images/v2/wf_minibanner.png" alt="webfusion.co.uk"></a><br style="clear: both;" /></div><a id="hide_other_brands" href="#">HIDE OUR OTHER BRANDS</a></div>');
                                        // add code on blog
                                        jQuery("body[class*=blog_page] #sur_head_wrapper").prepend('<div id="show_other_brands"><a href="#">SEE OUR OTHER BRANDS</a></div><div id="other_brands"><div><a target="_blank" href="http://www.donhost.co.uk/"><img class="floatRight" src="library/images/dh_minibanner.png" alt="donhost.co.uk"></a><a target="_blank" href="http://www.webfusion.co.uk/"><img src="/library/images/wf_minibanner.png" alt="webfusion.co.uk"></a><br style="clear: both;" /></div><a id="hide_other_brands" href="#">HIDE OUR OTHER BRANDS</a></div>');
					
                                        jQuery("#show_other_brands > a").click(function () { 	 
						jQuery("#other_brands").slideDown("slow"); 	 
					}); 	 
					jQuery("#hide_other_brands").click(function () { 	 
						jQuery("#other_brands").slideUp("slow"); 	 
					}); 
					
			};
OTT.tabs =
/* 	NOTE:
  	this is a simple script for tabs
	try to keep it simple if you need to modify it
	
	DESCRIPTION:
	on click:
	- get the tab id you want to show
	- reset style to all tab links
	- hide all tabs content
	- highlight the tab link that was pressed
	- show the tab content with the specified id
*/

			function() {
				jQuery("ul.tabs li").click(
					function () {
						var _ti = jQuery(this).children("a").attr("href"),
							_tc = jQuery("div.tabs .content");
						
						jQuery(this).parent().find(".active").removeClass("active");
						_tc.children(".visible").removeClass("visible");
						
						jQuery(this).addClass("active");
						_tc.children(_ti).addClass("visible");
						
						return false;
					});
    };
OTT.accordionS =
/* 	NOTE:
  	this is a simple accordion script
	it's not meant for menus or anything complicated
	
	DESCRIPTION:
	on click on a different element than the active one:
	- get the content id you want to show
	- get the parent container (I had issues when had more than one accordion per page)
	- reset style to all links
	- hide all content that is visible at the moment
	- set the link to active
	- show the content with the specified id
	
*/

			function() {
				jQuery(".accordion h3 a").click(
					function () {
						if (!jQuery(this).hasClass("active")) {
									var _oi = jQuery(this).attr("href"),
										_pa = jQuery(this).parent().parent();
									_pa.find(".active").removeClass("active");
									_pa.children(".visible").slideUp().removeClass("visible");
									
									jQuery(this).addClass("active");
									_pa.children(_oi).slideDown().addClass("visible");
						}
						
						return false;
					});
    };
OTT.dd_menu =
			function (container) {
                                        var containerWidth = parseInt(jQuery(container).width(), 10);
                                        jQuery(".mainmenu > li").each(function(){
                                            if (!(jQuery.browser.msie && (jQuery.browser.version == 6))) {
                                                jQuery(this).children("ul.js_dd").children("li.first").empty().append('<span class="block_1"></span><span class="block_2"></span><span class="block_3"></span><span class="block_4"></span><span class="hidden clear"></span>');
                                            }
                                            var liWidth = jQuery(this).width();
                                            liWidth += parseInt(jQuery(this).css("padding-left"), 10) + parseInt(jQuery(this).css("padding-right"), 10);
                                            
                                            var ddWidth = jQuery(this).children("ul.js_dd").width();
                                            var ddLeftPadding = parseInt(jQuery(this).find("li.last").css("padding-left"), 10);
                                            var ddRightPadding = parseInt(jQuery(this).find("li.last").css("padding-right"), 10);
                                            var tabOffset = parseInt(jQuery(this).offset().left, 10) - parseInt(jQuery(container).offset().left, 10);
                                            
                                            
                                            if ((tabOffset + ddWidth - ddLeftPadding - ddRightPadding) > containerWidth) {
                                                var block_4 = liWidth + ddRightPadding;
                                                var block_1 = ddWidth - liWidth - ddRightPadding - 8;
                                                jQuery(this).children("ul.js_dd").addClass("js_right");
                                                if (!(jQuery.browser.msie && (jQuery.browser.version == 6))) {
                                                    jQuery(this).find("span.block_1").css("width", block_1+"px");
                                                    jQuery(this).find("span.block_4").css("width", block_4+"px");
                                                }
                                            } else {
                                                var block_1 = liWidth + ddLeftPadding;                                               
                                                var block_4 = ddWidth - liWidth - ddLeftPadding - 8;
                                                jQuery(this).children("ul.js_dd").addClass("js_left");
                                                if (!(jQuery.browser.msie && (jQuery.browser.version == 6))) {
                                                    jQuery(this).find("span.block_1").css("width", block_1+"px");
                                                    jQuery(this).find("span.block_4").css("width", block_4+"px");
                                                }
                                            };
                                            jQuery(this).children("ul").removeClass("js_dd");
                                            
                                            
                                            
                                        });
                                        
                                        jQuery(".mainmenu > li").hover(
                                            function () {
                                                  var delayTime = 150;
                                                  var MenuProp = {
                                                      object: this,
                                                      timerId: -1,
                                                      isActive: true
                                                  };
                                                  var idx = MENU_OBJECT.length;
                                                  MENU_OBJECT.push(MenuProp);
                                                  MENU_OBJECT[idx].timerId = setTimeout("ShowMenu("+idx+")",delayTime);
                                            },
                                            function () {
                                              for(var i = 0; i < MENU_OBJECT.length; i++){
                                                if(MENU_OBJECT[i].object === this){

                                                                           try{
                                                                               clearTimeout(MENU_OBJECT[i].timerId);
                                                                           }catch(e){

                                                                           }
                                                                           MENU_OBJECT[i].isActive = false;
                                                                           MENU_OBJECT[i].timerId = -1;

                                                }
                                              }
                                              jQuery(this).children("ul").hide();
                                            }
                                        );
			};
function ShowMenu(idx){
  if(MENU_OBJECT[idx].isActive){
                                if (jQuery.browser.msie && (jQuery.browser.version == 7)) {
                                      //stupid IE7 shows a black border (instead of transparency) when fade
                                      jQuery(MENU_OBJECT[idx].object).children("ul").show();
                                } else {
                                      jQuery(MENU_OBJECT[idx].object).children("ul").fadeIn(200);
                                }
                                MENU_OBJECT[idx].isActive = false;
                                MENU_OBJECT[idx].timerId = -1;

  }

};

// tooltips from WF US
OTT.UI.customTooltips =			function () {
										var tooltipsLinks = 'a.show_hint, input.show_hint, span.show_hint',
										timeout;
										
										jQuery(tooltipsLinks).
									
											mouseover(function () {
														jQuery(".tooltip").remove();
														clearTimeout(timeout);
														
														if (jQuery('.triggerred').length) {
															
															jQuery('.triggerred').remove();
															return false;
														
														}
														
														var _t=jQuery(this),
														_title = jQuery.data(_t.get(0),'ttl');
										
														var  _o=_t.offset(),
															_parent=jQuery('body'),
															_Ph=_parent.height(),
															_Po=_parent.offset(),
															_timestamp=new Date().getTime(),
															_tooltip=jQuery(	'<div class="tooltip">'+
																					'<div class="tooltip_content">'+
																						_title+
																					'</div>'+
																					'<div class="tooltip_footer"></div>'+
																				'</div>').
														css('bottom', _Ph-_o.top-_Po.top-2+'px').
														css('left', _o.left-_Po.left-136+_t.width()/2+'px').
														attr('id', _timestamp);
									
														//get the mouse coords
														//update when table expands
														jQuery.data(_t.get(0),'tooltip',_timestamp);
														
														if (!arguments[1]) {
															timeout=setTimeout(function () {
                                                                                                                                                                        if(_parent.find(".tooltip").length)
                                                                                                                                                                            _parent.find(".tooltip").remove();

																					_parent.append(_tooltip).find('#'+_timestamp);
																				}, 500);
														}
														
														else if(arguments[1]==='trigger') {
															_tooltip.addClass('hidden').addClass('triggerred').appendTo(_parent).show('slow');
														}
														
														return false;
														
														}).
									
											mouseout(function () {
														clearTimeout(timeout);
                                                                                                                jQuery(".tooltip").remove();
														var _t=jQuery(this),
														_title = jQuery.data(_t.get(0),'ttl'),
														_tooltip = '#'+jQuery.data(_t.get(0),'tooltip');
									
														jQuery(_tooltip).remove();
														}).
														each (function () {
														var _t=jQuery(this);
														jQuery.data(_t.get(0),'ttl',_t.attr('title'));
														_t.removeAttr('title');
														});
	
										};

OTT.UI.showWSSLpopup =
		function () {
			jQuery(".showWSSLpopup").click(
					function () {
						var overlay_html = [

								'<div class="overlay">',
									'<div class="sslBox">',
										'<h3>Wildcard SSL<br /><span>cover all subdomains</span></h3>',
										'<p class="price"><span class="hidden">only &pound;79.99 a year</span></p>',
										'<ul>',
											'<li>Secure multiple sub-domains with Wildcard SSL</li>',
											'<li>Available with 123-SSL, Domain SSL and Organisational SSL.</li>',
										'</ul>',
										'<p class="findMore"><a href="/ssl-certificates/wildcard-ssl-certificates.shtml" title="Find out more"><img src="/library/images/v2/ssl/btn-more-wildcard.png" alt="Find out more" /></a></p>',
									'</div>',
									'<a href="#hide" class="hide_overlay" id="ssl">hide me</a>',
								'</div>'
						
								].join("\n");
						var overlay_content = new OTT.UI.Overlay_content(overlay_html);
						OTT.UI.Overlay.show(overlay_content);
						return false;
					});

		};

// == newsletters sign-up ===
var EmailSender = {};

EmailSender.validate = function(selector){

	if(jQ(selector) == null || jQ(selector) == undefined || jQ(selector).length <= 0)
			return false;
	else if(jQ(selector).val() == undefined)
			return false;
	else if(jQ(selector).val().length == 0)
			return false;
	else if(jQ(selector).val().replace("your@emailaddress.com","") == "")
			return false;
	else if(jQ(selector).val().match(/^[a-zA-Z0-9._-]+@[^\.][a-zA-Z0-9.-.{1,}]+\.[a-zA-Z]{2,4}$/) == null)
			return false;
	else return true;
}


MENU_OBJECT = new Array();
jQ(document).ready(function() {
	OTT.hoverTooltip();
	OTT.measure_password_strength();
	//jQ('body.create_email table.email_setup').addClass("js");
	jQ('.drop_down_action').each(OTT.show_forward_input).change(OTT.show_forward_input);
	OTT.otherBrands();
	OTT.tabs();
	OTT.accordionS();
	if(jQ(".gallery_invitation, .stepcarousel").length > 0)
		setTimeout(OTT.dd_menu, 1000, '#sur_head_main');
	else
		OTT.dd_menu('#sur_head_main');
	OTT.UI.customTooltips();
	OTT.UI.showWSSLpopup();	
	
	if(jQ("#offer_sign_ul_form").length){
		jQ("#offer_sign_ul_form").bind("submit", function(){

			if(EmailSender.validate(".email_input") && jQ("input[name='validate']").length > 0){
				//alert(jQ("input[name='validate']").val());
				if(parseInt(jQ("input[name='validate']").val()) == 0){
					return true;
				}
			}
			else
				if(EmailSender.validate(".email_input")){
					jQ("#offer_sign_ul_form .errors").html(" ");
					jQ.post("/subscribe_to_newsletter.pl", {email: jQ(".email_input").val(), validate: 1}, function(data){
						var response = eval('(' + data + ')');
						//alert(response.is_success);
						if(response.is_success==1){
							jQ("#offer_sign_ul_form .errors").html("Validating...");
							jQ("#offer_sign_ul_form").append("<input type='hidden' name='validate' value='0'/>");
							jQ("#offer_sign_ul_form").trigger("submit");
						}
						else{
							jQ("#offer_sign_ul_form .errors").html("Please type a valid email address.");
						}
					});
				}
				else{
					jQ("#offer_sign_ul_form .errors").html("Please type a valid email address.");
				}

			return false;
		});
	}

	// february fever countdown
	if(jQ("#ff_countdown_content").length){
		var today		= new Date();
		var currentDay	= today.getDate();
		var remaingDays = 28 - currentDay + 1;
		
		if(remaingDays == 1)
			jQ("#ff_countdown_content").css({"background" : 'transparent url("/library/images/v2/ff_countdown/ff_countdown_background2.png") left top no-repeat'});

		jQ("#ff_countdown_content span").css({"background" : "url(/library/images/v2/ff_countdown/ff_digit_" + remaingDays + ".png) center center no-repeat"});
	}
});