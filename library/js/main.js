var debug = function(message){
    console.debug("DEBUG: " + message);
}   
// a global month names array
var gsMonthNames = new Array(
                        'Ian',
                        'Feb',
                        'Mar',
                        'Apr',
                        'Mai',
                        'Iun',
                        'Iul',
                        'August',
                        'Sept',
                        'Oct',
                        'Nov',
                        'Dec'
                    );
// a global day names array
var gsDayNames = new Array(
                        'Duminica',
                        'Luni',
                        'Marti',
                        'Miercuri',
                        'Joi',
                        'Vineri',
                        'Sambata'
                       );
// Zero-Fill
String.prototype.zf = function(l) { 
    return '0'.string(l - this.length) + this; 
}
Number.prototype.zf = function(l) { 
    return this.toString().zf(l); 
}
String.prototype.string = function(l) {var s = '', i = 0;while (i++ < l) {s += this;}return s;}

Date.prototype.format = function(f){
    if (!this.valueOf())
        return ' ';
    var d = this;
    return f.replace(/(yyyy|mmmm|mmm|mm|dddd|ddd|dd|hh|nn|ss|a\/p)/gi,
        function($1){
            switch ($1.toLowerCase()){
            case 'yyyy':return d.getFullYear();
            case 'mmmm':return gsMonthNames[d.getMonth()];
            case 'mmm':return gsMonthNames[d.getMonth()].substr(0, 3);
            case 'mm':return (d.getMonth() + 1).zf(2);
            case 'dddd':return gsDayNames[d.getDay()];
            case 'ddd':return gsDayNames[d.getDay()].substr(0, 3);
            case 'dd':return d.getDate().zf(2);
            case 'hh':return ((h = d.getHours() % 12) ? h : 12).zf(2);
            case 'nn':return d.getMinutes().zf(2);
            case 'ss':return d.getSeconds().zf(2);
            case 'a/p':return d.getHours() < 12 ? 'a' : 'p';
            }
        }
    );
}

/*
 *- nord: Copou , ?ic?u , Crucea Ro?ie, Ia?i , S?r?rie , Podul de Fier
- est: Tudor Vladimirescu (cartier în Ia?i) , T?t?ra?i , Oancea , Ciurchi , Metalurgie, Ia?i , Avia?iei, Ia?i , Zona industrial?
- sud: Bularga , Bucium , Socola , Frumoasa , Podu Ro? , Dimitrie Cantemir, Ia?i , Nicolina, Ia?i 1, 2 ?i 3, CUG 1 ?i 2, Galata, Ia?i 1 ?i 2, Podul de Piatr?
- vest: Mircea cel B?trân, Ia?i , Alexandru cel Bun (cartier în Ia?i) , ?igarete, Ia?i , Dacia, Ia?i , P?curari , Canta , P?cure?
 */
var getCartier = function(content){
    var cartiere = [
                        {reg: /copou|universitate|cuza/, name: "Copou"},
                        {reg: /ticau/, namme: "Ticau"},
                        {reg: /crucea rosie/, name: "Crucea Rosie"},
                        {reg: /sararie/, name: "Sararie"},
                        {reg: /podu[l]* de fier/, name: "Podul de fier"},
                        {reg: /tudor/, name: "Tudor Vladimirescu"},
                        {reg: /zona industriala/, name: "Zona Industriala"},
                        {reg: /bularga/, name: "Bularga"},
                        {reg: /bucium/, name: "Bucium"},
                        {reg: /socola/, name: "Socola"},
                        {reg: /frumoasa/, name: "Frumoasa"},
                        {reg: /tatarasi/, name: "Tatarasi"},
                        {reg: /oancea/, name: "Oancea"},
                        {reg: /ciurchi/, name: "Ciurchi"},
                        {reg: /metalurgie/, name: "Metalurgie"},
                        {reg: /aviatie/, name: "Aviatiei"},
                        {reg: /podu[l]* ros/, name: "Podu Ros"},
                        {reg: /cantemir/, name: "Dimitrie Cantemir"},
                        {reg: /nicolina/, name: "Nicolina"},
                        {reg: /cug/, name: "Cug"},
                        {reg: /galata/, name: "Galata"},
                        {reg: /podu[l]* de piatra/, name: "Podul de Piatra"},
                        {reg: /mircea/, name: "Mircea Cel Batran"},
                        {reg: /alexandru/, name: "Alexandru Cel Bun"},
                        {reg: /gara/, name: "Gara"},
                        {reg: /tigarete/, name: "Tigarete"},
                        {reg: /dancu/, name: "Dancu"},
                        {reg: /tomesti/, name: "Tomesti"},
                        {reg: /miroslava/, name: "Miroslava"},
                        {reg: /dacia/, name: "Dacia"},
                        {reg: /pacurari/, name: "Pacurari"},
                        {reg: /canta/, name: "Canta"},
                        {reg: /pacuret/, name: "Pacuret"},
                        {reg: /hala/, name: "Hala Centrala"},
                        {reg: /moldova/, name: "Moldova Mall"},
                        {reg: /iulius|mall/, name: "Iulius Mall"},
                        {reg: /centru/, name: "Stefan Cel Mare"}
                ];          
   var cartier = null;
   for(var i = 0; i < cartiere.length; i++){       
       if(cartiere[i].reg.test(content)){
           cartier = cartiere[i].name;
       }
   }                    
   
   return cartier;
}
var Overlay = {};
Overlay.init = function(){
    jQ("#closeButton").bind("click", function(){
        Overlay.hide();
        return false;
    });
}
Overlay.show = function(){
    jQ("#full_overlay, #overlay_wrap").fadeIn(200);
}

Overlay.hide = function(){
    jQ("#full_overlay, #overlay_wrap").fadeOut(200);
}

var jQ = jQuery;
var App = {};

App.uploader = {
    data: ["image1"],
    init: function(){
        jQ("#addNewImage").bind("click", function(){
           var newTr = jQ("#uploadImages table tr:nth(0)").clone(true);
           newTr.find("input").attr("name", "image" + (App.uploader.data.length + 1)).val("");
           jQ("#uploadImages table").append(newTr);
           App.uploader.data.push("image" + (App.uploader.data.length + 1));
        });
        
        jQ("#uploadImages table tr td .remove").live("click", function(){
            if(App.uploader.data.length - 1 == 0){
                App.alert(this, "Acest element nu poate fi sters.", false);
                return false;
            }
            var parent = jQ(this).parent().parent();
            var fieldName = jQ(parent).find("input[type='file']").attr("name");            
            for(var i = 0; i < App.uploader.data.length; i++){
                if(fieldName.replace(App.uploader.data[i], "") == ""){
                    App.uploader.data.splice(i, 1);
                    parent.remove();
                    return false;
                }             
            }                                    
            return false;
        });
    },
    startUpload: function(onLoadCallback){
        jQ("#uploadImages table").fadeOut(300, function(){            
            jQ("#uploadImages .loader").css({"display" : "block"});
        });
        jQ("#addNewImage").hide();
        
        jQ("#upload_target").load(function(){
            jQ("#uploadImages .loader").css({"display" : "none"});             
            if(typeof(onLoadCallback) != 'undefined'){
                var text = jQ("#upload_target").contents().text();                
                try{
                    var uploadedData = JSON.parse(text);
                    onLoadCallback(uploadedData);   
                } catch(e){
                    onLoadCallback(null);   
                }
            }            
        });
        jQ("#uploadImages").trigger("submit");
    }
};
App.comments = {	
        getCommentsCount: function(adId){
            var count = 0;
            for(var i = 0; i < App.ads.data.length; i++){
                if(App.ads.data[i].id == adId){
                    debug("count: " + App.ads.data[i].comments.toString());                    
                        if(App.ads.data[i].comments.length == 0 || App.ads.data[i].comments.length  != parseInt(App.ads.data[i].total_comments, 10))
                            count = parseInt(App.ads.data[i].total_comments, 10);
                        else
                            count = parseInt(App.ads.data[i].comments.length, 10);                    
                }
            }
            debug("getCommentsCount adId: " + adId + " count: " + count);
            return count;
        },
        removeComment: function(commentId){        
          debug("removeComment: " + commentId);
          for(var i = 0; i < App.ads.data.length; i++){              
              if(typeof(App.ads.data[i].comments) != 'undefined'){
                  for(var j = 0; j < App.ads.data[i].comments.length; j++){                      
                      if(App.ads.data[i].comments[j].id == commentId){
                          // remove this comment
                          try{                              
                              App.ads.data[i].comments.splice(j, 1);                              
                              App.ads.data[i].total_comments = parseInt(App.ads.data[i].total_comments, 10) - 1;
                              return App.ads.data[i].id;
                          }catch(e){
                              // nothing here...
                              alert("remove comment error: " + e.toString());
                          }
                      }
                  }
              }
          }  
          return null;
        },
	addComment: function(pComment, resetCounter){
                debug("pComment: " + pComment.toString());
		if(pComment != null){
			if(pComment.content != null && pComment.id != -1){
				for(var i = 0; i < App.ads.data.length; i++){                                                                                
					if(App.ads.data[i].id == pComment.ad_id){
                                                if(resetCounter){
                                                    App.ads.data[i].total_comments = 0;
                                                }
						if(typeof(App.ads.data[i].comments) != 'undefined'){
                                                        debug("added " + pComment.toString() + " ad: " + App.ads.data[i].toString());
							App.ads.data[i].comments[App.ads.data[i].comments.length] = pComment;
                                                        App.ads.data[i].total_comments = parseInt(App.ads.data[i].total_comments, 10) + 1;
							return true;
						} else{
							App.ads.data[i]['comments'] = [];
							App.ads.data[i].comments.push(pComment);
                                                        debug("added2 " + pComment.toString() + " ad: " + App.ads.data[i].toString());
                                                        App.ads.data[i].total_comments = parseInt(App.ads.data[i].total_comments, 10) + 1;
							return true;
						}
					}
				}
			} else{
				return false;
			}
		} else{
                    return false;
                }
	},
	constructComment: function(pComment){
                /*
                var now  = new Date();                
		var diff = parseInt(now.getTime()) - parseInt(pComment.date);
                //alert("now: " + parseInt(now.getTime()) + "  commentDate: "  + parseInt(pComment.date));
                // constants
                var ONE_SECOND    = 1000;
                var ONE_MINUTE    = ONE_SECOND * 60;
                var ONE_HOUR      = ONE_MINUTE * 60;
                var ONE_DAY       = ONE_HOUR   * 24;
                var ONE_MONTH     = ONE_DAY    * 31;
                
                var seconds    = parseInt((((diff % ONE_DAY) % ONE_HOUR) % ONE_MINUTE) / ONE_SECOND);   // number of remaining seconds from this minute
                var days       = parseInt((diff / ONE_DAY));                                 // number of remaining days
                var hours      = parseInt((diff % ONE_DAY) / ONE_HOUR);                                 // number of remaining hours from this day
                var minutes    = parseInt(((diff % ONE_DAY) % ONE_HOUR) / ONE_MINUTE);                  // number of remaining minutes of current hour         
                               
                
                var when        = hours + (hours == 1 ? " ora" : " ore") + ", " + minutes + (minutes == 1 ? " minut" : " minute") + " " + seconds + (seconds == 1 ? " secunda" : " secunde");
                */
		var commentHtml = "";
		if(pComment.content != null){
                    
                var removeButton = "";
                if(App.user.current_user != null){
                    if(App.user.current_user.id == pComment.owner_user_id){
                        removeButton = '<a href="#Remove" class="remove_comment"></a>';
                    }
                }
		commentHtml = '<li>' +
							'<div class="comment" id="' + pComment.id + '">' +                                                                                                                                
                                                                removeButton + 
								'<h3>' + pComment.title + '<br /><span class="commentUser">' + pComment.user_name +  '</span></h3>' +
								'<p class="commentContent">' +
									pComment.content+
								'</p>' +
							'</div>' +
						'</li>';
		}
		return commentHtml;
	}
}
App.alert = function(parent, message, isPositive){
    var position = jQ(parent).offset();
    if(typeof(position.left) != 'undefined'){
        jQ("#alert span").html(message);
        var bgColor = (isPositive == true ? "#A4C417" : "#e33611");
        jQ("#alert").css({"left" : (position.left + 10 - jQ("#alert").width()/2), "top" : (position.top + 80),  "background" : bgColor});
        jQ("#alert").fadeIn(100, function(){
            setTimeout('jQ("#alert").fadeOut(200)', 1000);
        });                               
    }
}

App.ui = {};
App.ui.showAd = function(data){        
    var adHtml = "";    
    var date = new Date(data.date * 1000);
    if(data.content){            
        var link = data.content.substring(0, 60);
        link = "/anunt/" + link.replace(/[^a-zA-Z0-9]+/g,'-');
        link += "/" + data.id + "/";            

        adHtml += '<li id="' + App.ads.baseId + "" + data.id + '">' +
                        '<div class="ad">'+
                            '<h3>'+ data.title + '<span>'+ date.format('dddd, dd mmmm') + '</span></h3>'+
                            App.ui.constructImages(data) +
                            '<div class="content">'+
                                data.content+
                            '</div>'+
                             '<div class="footer">' +                                            
                                data.views + (data.views == 1 ? ' Vizualizare' : ' Vizualizari') +
                             '</div>'+                                                    
                        '</div>'+
                         '<div class="actions">' +
                         '<ul class="comments">' +
                         '</ul>' +
                         '<a href="' + link + '" class="floatedRight input-button-green">CITESTE</a>'+							                                                                                                                
                         '<input type="button" class="floatedRight input-button showComments" value=" ' + (data.total_comments == 0 ? "Adauga comentariu" : 'Comentarii(' + data.total_comments + ')') +  '"/>'+		
                         '<div class="add_comment">' +                                                                
                                '<textarea name="new_comment" val="Add new comment" class="floatedLeft">Adauga un comentariu...</textarea><br class="clear"/>' +
                                '<input type="button" name="button_add_comment" class="input-button" value="Adauga" />' +
                                (data.total_comments > 1 ? '<span class="add_more_comments">Mai multe comentarii</span>' : "") +
                         '</div>' +
                         '</div><br class="clear" />'+                                         
                    '</li>';           
    }        
    jQ(".ads").prepend(adHtml);
}
App.ui.constructImages = function(ad){  
    var ulImages = "";
    if(typeof(ad.images) != 'undefined'){
        if(ad.images != null){
            if(ad.images.length > 0){
                for(var i = 0; i < ad.images.length; i++){
                    //ulImages += '<li><img src="/library/img/ads_img/th/' + ad.images[i].name + '" alt="' + ad.images[i].name + '"/></li>';
                }
                ulImages += '<li><img src="/library/img/ads_img/th/' + ad.images[0].name + '" alt="' + ad.images[0].name + '"/></li>';
            }
        } 
    }
    
    if(ulImages.length > 0){
        return '<ul class="adImages">' + ulImages + '</ul>';
    } else{
        return "";
    }
}

App.ui.showAds = function(data){        
    var adsHtml = "";
    for(var i = 0; i < data.length; i++){
        var date = new Date(data[i].date * 1000);
        if(data[i].content){            
            var link = data[i].content.substring(0, 60);
            link = "/anunt/" + link.replace(/[^a-zA-Z0-9]+/g,'-');
            link += "/" + data[i].id + "/";
                    var comment = {
                            id: data[i].com_id,
                            title: data[i].com_title,
                            ad_id: data[i].com_ad_id,
                            owner_user_id: data[i].com_user_id,
                            user_name: data[i].com_username,
                            date: data[i].com_date,
                            content: data[i].com_content,
                            rating: data[i].com_rating
                    };

            adsHtml += '<li id="' + App.ads.baseId + "" + data[i].id + '">' +
                            '<div class="ad">'+
                                '<h3>'+ data[i].title + '<span>'+ date.format('dddd, dd mmmm') + '</span></h3>'+
                                App.ui.constructImages(data[i]) +
                                '<div class="content">'+
                                    data[i].content+
                                '</div>'+
                                 '<div class="footer">' +                                            
                                    data[i].views + (data[i].views == 1 ? ' Vizualizare' : ' Vizualizari') +
                                 '</div>'+                                                    
                            '</div>'+
                             '<div class="actions">' +
                             '<ul class="comments">' +
                                    App.comments.constructComment(comment) +
                                '</ul>' +
                                                            '<a href="' + link + '" class="floatedRight input-button-green">CITESTE</a>'+							                                                                                                                
                                                                                     '<input type="button" class="floatedRight input-button showComments" value=" ' + (data[i].total_comments == 0 ? "Adauga comentariu" : 'Comentarii(' + data[i].total_comments + ')') +  '"/>'+		
                                                            '<div class="add_comment">' +                                                                
                                                                    '<textarea name="new_comment" val="Add new comment" class="floatedLeft">Adauga un comentariu...</textarea><br class="clear"/>' +
                                                                    '<input type="button" name="button_add_comment" class="input-button" value="Adauga" />' +
                                                                    (data[i].total_comments > 1 ? '<span class="add_more_comments">Mai multe comentarii</span>' : "") +
                                                            '</div>' +
                             '</div><br class="clear" />'+                                         
                        '</li>';           
        }    
    }
    
    jQ(".content_left .loader").addClass("hidden");
    jQ(".ads").removeClass("hidden").html(adsHtml);
}
App.categories = {};
App.categories.currentCategory = null;
App.categories.data = null;

App.categories.init = function(currentCategory, allCategories){
    App.categories.currentCategory = JSON.parse(currentCategory);
    App.categories.data = JSON.parse(allCategories);
}

App.categories.getCurrentCategory = function(){
    return App.categories.currentCategory;
}

App.pages = {};
App.pages.currentPage = 0;
App.pages.totalPages  = 0;
App.pages.adsPerPage  = 0;
App.pages.data        = {};

// methods
App.pages.init = function(pTotalPages, pCurrentPage, pAdsPerPage){
    App.pages.currentPage = pCurrentPage;
    App.pages.totalPages  = pTotalPages;
    App.pages.adsPerPage  = pAdsPerPage;    
    // get page first and last ad
    var startAd = (App.ads.getAdAt(App.pages.currentPage) == null ? 0 :  App.ads.getAdAt(App.pages.currentPage));
    var endAd   = (App.ads.getAdAt(App.pages.currentPage + App.pages.adsPerPage) == null ? 0 : App.ads.getAdAt(App.pages.currentPage + App.pages.adsPerPage));
    App.pages.data[App.pages.currentPage] = {startAdId: startAd.id, endAdId: endAd.id};
    
    debug("App.pages.data[App.pages.currentPage]: " + App.pages.data[App.pages.currentPage].toString());
    // prepare the pages
    App.pages.updatePageNumbers();
}

App.pages.bindLinks = function(){
    jQ(".pages li a").unbind("click").bind("click", function(){
        var page = parseInt(jQ(this).attr("rel"), 10);
        
        debug("App.page.bindLinks: page = " + page);          
        if(page == App.pages.currentPage && App.ads.data.length > 0){
            debug("You request the current page: no need to grab it again, it is in your face.");
            return false;
        } else{
            jQ(".ads").addClass("hidden");
            jQ(".content_left .loader").removeClass("hidden");                  
            App.ads.getAdsFromServer(page, App.categories.getCurrentCategory().id, App.pages.adsPerPage);        
        }
        return false;
    });
}

App.pages.updatePageNumbers = function(){
    jQ(".pages").empty();
    debug("currentCat: " + App.categories.getCurrentCategory().id + " currentPage: " + App.pages.currentPage);
    for(var i = 0; i < App.pages.totalPages; i++){
        if(i  == App.pages.currentPage){
            jQ(".pages").append('<li class="active"><a href="/pages/' + App.categories.getCurrentCategory().name + '/' + App.categories.getCurrentCategory().id + '/' + i + '/" rel="' + i + '">' + (i == 0 ? "Prima pagina" : i) + '</a></li>');
            if(typeof(window.history.pushState) != "undefined"){
                window.history.pushState(null, App.categories.getCurrentCategory().name + "(" + i + ")", '/pages/' + App.categories.getCurrentCategory().name + '/' + App.categories.getCurrentCategory().id + '/' + i + '/');
            }
        } else if(i < App.pages.currentPage + 5){
            jQ(".pages").append('<li><a href="/pages/' + App.categories.getCurrentCategory().name + '/' + App.categories.getCurrentCategory().id + '/' + i + '/" rel="' + i + '">' + (i == 0 ? "Prima pagina" : i) + '</a></li>');
        } else if(App.pages.currentPage == 20){
            jQ(".pages").append('<li><a href="/pages/' + App.categories.getCurrentCategory().name + '/' + App.categories.getCurrentCategory().id + '/' + i + '/" rel="' + i + '">' + (i == 0 ? "Prima pagina" : i) + '</a></li>');            
        }
        else if(i == App.pages.currentPage + 6){
            jQ(".pages").append('<li>.......</li>');
        } else if(i > App.pages.totalPages - 5 && i < App.pages.totalPages){
            jQ(".pages").append('<li><a href="/pages/' + App.categories.getCurrentCategory().name + '/' + App.categories.getCurrentCategory().id + '/' + i + '/" rel="' + i + '">' + (i == 0 ? "Prima pagina" : i) + '</a></li>');
        }                
    }
    
    App.pages.bindLinks();
}

App.ads = {};
App.ads.baseId = 0;
App.ads.data = [];

App.ads.init = function(pBaseId){    
    App.ads.baseId  = pBaseId;  
}

App.ads.addAd = function(ad){
    App.ads.data.push(ad);
};

App.ads.getAdAt = function(pIndex){
    debug("App.ads.getAdAt: " + pIndex);
    for(var i = 0; i < App.ads.data.length; i++){
        if(i == pIndex){
            return App.ads.data[pIndex];
        }
    }    
    return null;
}

App.ads.getAdById = function(adId){  
    debug("App.ads.getAdById: " + adId);
    for(var i = 0; i < App.ads.data.length; i++){
        if(App.ads.data[i].id == adId){
            return App.ads.data[i];
        }
    }
    return null;
}

App.ads.getAdsFromServer = function(pCurrentPage, pCategoryId, pAdsPerPage){
        
    var getAdsObj = {
        methodUrl: "/API/ads/get_all/",
        params: {
            page:pCurrentPage,
            category_id: pCategoryId,
            ads_per_page: pAdsPerPage
        },
        callback: function(data){            
            if(typeof(data) != 'undefined'){
                if(data.is_success == 1){
                    // we have some ads :)
                    App.ads.data = data.ads;
                    App.ui.showAds(App.ads.data);
                    App.pages.currentPage = this.params.page;
                    App.pages.updatePageNumbers();
                }
            }
        }
    };
    
    Client.ajax.call(getAdsObj);    
}
// current user's methods
App.user = {};
App.user.current_user = {};

// set the current_login is a sessions exists
App.user.setCurrentUser = function(currentUserJson){
    debug("App.user.current_user: " + currentUserJson);
    if(currentUserJson != null){
        try{
            App.user.current_user = JSON.parse(currentUserJson);
        }catch(e){
            App.user.current_user = null;
        }
    } else{
        App.user.current_user = null;
    }
}

/* user login */
App.user.prepareUI = function(){
    jQ("#userName, #userPassword").focus(function(){
        if(jQ(this).val() == "Parola" || jQ(this).val() == "Utilizator"){            
            jQ(this).val("");
        }
        jQ(this).css({"background" : "#ffffff"});
    }).blur(function(){
        if(jQ(this).val().length == 0){            
            jQ(this).val(jQ(this).attr("id") == "userName" ? "Utilizator" : "Parola");
        }
        jQ(this).css({"background" : "#F5F5ED"});        
    });
    
    jQ("form[name='loginForm']").submit(function(){
        if(jQ("#userName").val() == "Utilizator" || jQ("#userPassword").val() == "Parola")
            App.alert(jQ(this), "Va rugam completati datele dvs. de logare.", false);
        else if(jQ("#userName").val().length == 0 || jQ("#userPassword").val().length == 0)
            App.alert(jQ(this), "Va rugam completati datele dvs. de logare.", false);
        else{                        
            var callObj = {
                methodUrl: "/API/session/login/",
                params:{
                    user_name: jQ("#userName").val(),
                    user_password: jQ("#userPassword").val()
                }, 
                callback: function(data){
                    if(data != null){
                        if(data.is_success == 1){
                            App.user.current_user = data.user;
                            jQ(".isLoggedIn p").text("Salut " + App.user.current_user.name);    
                            jQ(".isLoggedOut").addClass("hidden");
                            jQ(".isLoggedIn").removeClass("hidden");
                        } else{
                            App.alert(jQ("#userName"), "Parola sau numele utilizatorului sunt incorecte.", false);
                        }
                    }
                }
            };
            
            // make the call
            Client.ajax.call(callObj);
        }
        return false;
    });
    
    jQ("#logoutButton").bind("click", function(){
        if(App.user.current_user == null){
            App.alert(jQ(this), "No user selected.", false);     
            return false;
        }
        
       // do logout       
       App.alert(jQ(this), "Deconectare....", true);
       var callObj = {
           methodUrl: "/API/session/logout/",
           params: {
               user_id: App.user.current_user.id
           },
           callback: function(data){
                if(data != null){
                    if(data.is_success == 1){
                        App.user.current_user = null;            
                        jQ(".isLoggedIn").addClass("hidden");
                        jQ(".isLoggedOut").removeClass("hidden");                        
                    }
                }
           }
       };
       // make the call 
       Client.ajax.call(callObj);
       return false;
    });    
};

jQ(document).ready(function(){
    Overlay.init();
    
    // init Uploader 
    App.uploader.init();
    
    // categories init
    App.categories.init(CURRENT_CAT, ADS_CATEGORIES);
    
    // pages
    App.ads.init(BASE_ID);
    App.pages.init(TOTAL_PAGES, CURRENT_PAGE, ADS_PER_PAGE);
    
    // user
    App.user.setCurrentUser(CURRENT_USER);
    App.user.prepareUI();
    
    // get first ads
    App.ads.getAdsFromServer(App.pages.currentPage, App.categories.getCurrentCategory().id, App.pages.adsPerPage);
    
    if(adToShow.length > 0){
        // we have to show an ad
        
        var ad = JSON.parse(adToShow).ad;
        var uiId = App.ads.baseId + ad.id;
       // update the overlay
       jQ("#overlay_content h3").html(ad.title);
       jQ("#overlay_content p.content").html(ad.content);              
       var cartier = getCartier(ad.content.toLowerCase());
       if(cartier != null){
           jQ("#overlay_content .map").html('<iframe width="300" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q=iasi+' + cartier + '&output=embed&iwloc=near&addr&amp;"></iframe><br /><small><a target="_blank" href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=iasi+' + cartier + '" style="color:#575757;text-align:left">Mareste</a></small>');
       } else{
           jQ("#overlay_content .map").html(" ");
       }
       jQ("#overlay_content .date").html(jQ("#" + uiId + " h3 span").html());
       
       // increase the views number
       var callObj = {
           
       };       
       
       Overlay.show();        
    }
    
    jQ(".ads li .actions .add_comment input[name='button_add_comment']").live("click", function(){
		var parent = jQ(this).parent().parent().parent();
		var id = jQ(parent).attr("id");
		var adId = id.replace(App.ads.baseId, "");
		if(App.user.current_user != null){
			if(jQ("textarea[name='new_comment']", parent).val().length > 0){
				var commentContent = jQ("textarea[name='new_comment']", parent).val();
				var currentAd = App.ads.getAdById(adId);
				if(currentAd != null){
					var callObj ={
						methodUrl: "/API/comments/add/",
						params: {
							ad_id: adId,
							title: commentContent.substr(0, 40),
							content: commentContent,
							owner_user_id: App.user.current_user.id
						},
						callback: function(data){							
                                                        if(typeof(data) != 'undefined'){
                                                            if(data.is_success == 1){
                                                                App.alert(jQ("#" + App.ads.baseId + this.params.ad_id + " .actions textarea"), "Comentariu adaugat", true);                                                                
                                                                if(App.comments.addComment(data.comment, false)){
                                                                    // add comment in page
                                                                    var htmlComment = App.comments.constructComment(data.comment);
                                                                    htmlComment = htmlComment.replace("<li>", "<li style='display: none;'>");
                                                                    
                                                                    if(htmlComment != null){
                                                                        jQ("#" + App.ads.baseId + this.params.ad_id + " .comments").append(htmlComment);                                                                        
                                                                        jQ("#" + App.ads.baseId + this.params.ad_id + " .comments li").last().show(500);
                                                                        var commentsCount = App.comments.getCommentsCount(this.params.ad_id);
                                                                        jQ("#" + App.ads.baseId + this.params.ad_id + " .showComments").val("Comentarii(" + commentsCount + ")");
                                                                    } else{
                                                                        App.alert(jQ("#" + App.ads.baseId + this.params.ad_id + " .comments"), "1Momentan comentariul dvs. nu poate fi adaugat!", false);                                        
                                                                    }                                                                    
                                                                } else{
                                                                    App.alert(jQ("#" + App.ads.baseId + this.params.ad_id + " .comments"), "2Momentan comentariul dvs. nu poate fi adaugat!", false);                                    
                                                                }                                                                
                                                            } else{
                                                                App.alert(jQ("#" + App.ads.baseId + this.params.ad_id + " .comments"), "3Momentan comentariul dvs. nu poate fi adaugat!", false);                                    
                                                            }
                                                        } else{
                                                            App.alert(jQ("#" + App.ads.baseId + this.params.ad_id + " .comments"), "4Momentan comentariul dvs. nu poate fi adaugat!", false);                                    
                                                        }
						}
					};
                                        jQ("textarea[name='new_comment']", parent).val("Adauga un comentariu...");
					Client.ajax.call(callObj);					
				}else{
					App.alert(jQ(this), "Comentariul dvs. nu poate fi gol!", false);
				}
			} else{
				App.alert(jQ(this), "Comentariul dvs. nu poate fi gol!", false);
			}
		} else{
			App.alert(jQ(this), "Pentru a putea adauga un comentariu, trebuie sa fiti logat.", false);
		}
		return false;
	});
    jQ(".ads li .actions a.input-button-green").live("click", function(){
       var adId = $(this).parent().parent().attr("id").replace(App.ads.baseId, "");
       var uiId = App.ads.baseId + adId;
       var ad   = App.ads.getAdById(adId);
       
       if(ad == null){
           debug("cannot find an ad with id: " + adId);
           return false;
       }
       
       // here we have the add loaded.
       // update the overlay
       jQ("#overlay_content h3").html(ad.title);
       jQ("#overlay_content p.content").html(ad.content);              
       var cartier = getCartier(ad.content.toLowerCase());
       if(cartier != null){
           jQ("#overlay_content .map").html('<iframe width="300" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q=iasi+' + cartier + '&output=embed&iwloc=near&addr&amp;"></iframe><br /><small><a target="_blank" href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=iasi+' + cartier + '" style="color:#575757;text-align:left">Mareste</a></small>');
       } else{
           jQ("#overlay_content .map").html(" ");
       }
       jQ("#overlay_content .date").html(jQ("#" + uiId + " h3 span").html());
       
       // increase the views number
       var callObj = {
           
       };       
       
       Overlay.show();
       return false;
    });
    
    jQ(".ads li .actions .add_more_comments").live("click", function(){
       var parent = jQ(this).parent().parent().parent();
       var adId   =  parent.attr("id").replace(App.ads.baseId, "");
       if(jQ(this).hasClass("commentsVisible")){           
           jQ("#" + App.ads.baseId + adId + " .showComments").trigger("click");
           //jQ(this).removeClass("commentsVisible");
           return;
       }
       
       var callObj = {
           methodUrl: "/API/comments/get_all/",
           params:{
               ad_id: adId
           },
           callback: function(data){
               if(typeof(data) != undefined){
                   if(data.is_success == 1){
                       var htmlComments = "";
                       for(var i = 0; i < data.comments.length; i++){
                           if(App.comments.addComment(data.comments[i], (i == 0 ? true : false))){
                                htmlComments += App.comments.constructComment(data.comments[i]);
                           }
                       }
                       jQ("#" + App.ads.baseId + this.params.ad_id + " .add_more_comments") .html("Ascunde comentariile").addClass("commentsVisible");
                       jQ("#" + App.ads.baseId + this.params.ad_id + " .comments").hide().html(htmlComments).show(200);                       
                   }
               }
           }           
       };
       $(this).html("Preiau comantariile....");
       Client.ajax.call(callObj);       
    });
    
    jQ(".ads .remove_comment").live("click", function(){
       var parent = $(this).parent();
       var commentId = parent.attr("id");       
       
       var callObj = {
           methodUrl: "/API/comments/remove/",
           params: {
               id: commentId,
               owner_user_id: App.user.current_user.id        
           },
           callback: function(data){
               if(typeof(data) != 'undefined'){                   
                   if(data.is_success == 1){                       
                       // remove the commet from page
                       var showRequestComments = false;
                       
                       if(parent.parent().parent().find("li").length == 1){
                           showRequestComments = true;
                       }
                       
                       parent.parent().hide(300, function(){
                           parent.parent().remove();
                       });
                       
                       var adId = App.comments.removeComment(this.params.id);
                       var commentsCount = App.comments.getCommentsCount(adId);                       
                       if(commentsCount == 0)
                            jQ("#" + App.ads.baseId + adId + " .showComments").val("Adauga comentariu");
                        else
                            jQ("#" + App.ads.baseId + adId + " .showComments").val("Comentarii(" + commentsCount + ")");
                       if(showRequestComments){
                           jQ("#" + App.ads.baseId + adId + " .add_more_comments").trigger("click");                            
                       }
                   } else{
                       App.alert(parent, "Pentru moment, aces comment nu poate fi sters/", false);
                   }
               }
           }
       };
       Client.ajax.call(callObj);
       return false;
    });
    
    jQ(".showComments").live("click", function(){
       var parent = $(this).parent().parent();
       var id = parent.attr("id");       
       if(jQ(this).hasClass("input-button")){
           // we have to display the comments
           jQ("#" + id  + " .comments").show(100);
           jQ("#" + id  + " .add_comment").show(100);
           jQ(this).removeClass("input-button").addClass("input-button-blue");
       } else{
           // we have to hide the comments
           jQ("#" + id  + " .comments").hide(100);
           jQ("#" + id  + " .add_comment").hide(100);
           jQ(this).removeClass("input-button-blue").addClass("input-button");
       }
    });
    
    jQ("textarea[name='new_comment']").live("focus", function(){        
       if(jQ(this).val().replace("Adauga un comentariu...") != jQ(this).val()){
           jQ(this).val("");
       }
    });
    
    jQ("textarea[name='new_comment']").live("blur", function(){        
       if(jQ(this).val().length == 0){
           jQ(this).val("Adauga un comentariu...");
       }
    });    
    
    jQ("#newAdButton").bind("click", function(){       
        var newAd = jQ(".newAd");
        var overlay = jQ("#full_overlay");
       if(newAd.css("display") == "block"){
           newAd.fadeOut(200);
           overlay.hide();  
           jQ(this).css({"z-index" : "0"});
           jQ(this).val("Adauga anunt");
       } else{
           jQ(this).css({"z-index" : "100"});
           newAd.fadeIn(200);
           overlay.show();
           jQ(this).val("Inapoi");
           if(jQ(window).scrollTop() != 0)
                $('html, body').animate({scrollTop: '0px'}, 300);
       }
    });
    
    jQ("#addItNow").bind("click", function(){  
       if(App.user.current_user == null){
           alert("Va rugam sa va autentificati pentru a putea adauga un anunt.");
           return false;
       }

       var ad_title   = jQ("input[name='ad_title']").val();
       var ad_content = jQ("textarea[name='ad_content']").val();
       var ad_price   = jQ("input[name='ad_price']").val();
       var ad_addr    = jQ("input[name='ad_address']").val();
       var ad_category= jQ("select[name='ad_category']").val();
       var ad_phone   = jQ("input[name='ad_phone']").val();
       var ad_email   = jQ("input[name='ad_email']").val();       

       if(ad_title.length == 0 || ad_content.length == 0 || ad_phone.length == 0){
           alert("Va rugam completati toate campurile marcate cu *");
           return false;
       }
       
       App.uploader.startUpload(function(data){
           if(typeof(data) != 'undefined'){
               var htmlImgs = "";
               for(var i in data){
                   var img = data[i];
                   htmlImgs += '<li><img src="' + img.url + '" alt="' + img.name + '" /></li>';
               }
               
               if(htmlImgs.length > 0){
                   jQ("#uploadedImages").html(htmlImgs);                   
               } else{
                   jQ("#uploadImages table").fadeIn(1000);     
                   jQ("#addNewImage").show();
               }
           }

           var callObj = {
             methodUrl: "/API/ads/add/",
             params: {
                 title: ad_title,
                 content: ad_content,
                 price: (ad_price.length == 0 ? 0 : ad_price),
                 address: (ad_addr.length == 0 ? "-" : ad_addr),
                 category_id: ad_category,
                 phone: ad_phone,
                 email: (ad_email.length == 0 ? "-" : ad_email),
                 user_id: App.user.current_user.id,
                 source: "iasianunta.info",
                 images: (htmlImgs.length > 0 ? data : null)
             },
             callback: function(data){
                 if(typeof(data) != 'undefined'){
                     if(data.is_success == 1){
                         App.ads.addAd(data.ad);
                         jQ("#newAdButton").trigger("click");
                         
                         if(data.ad.cat_id == App.categories.getCurrentCategory().id)
                            App.ui.showAd(data.ad);                       
                        
                         // restore default state
                         jQ("input[name='ad_title']").val("");
                         jQ("textarea[name='ad_content']").val("");
                         jQ("input[name='ad_price']").val("");
                         jQ("input[name='ad_address']").val("");
                         jQ("select[name='ad_category']").val("");
                         jQ("input[name='ad_phone']").val("");
                         jQ("input[name='ad_email']").val("");                           
                         jQ("#uploadImages table").fadeIn(1000);     
                         jQ("#addNewImage").show();                         
                         jQ("#uploadedImages").html("");                          
                     }
                 }
                 //alert("anunt adaugat:  " + data.toString());
             }
           };
           Client.ajax.call(callObj);
           return false;
       });
    });
    
    jQ("#cancelButton").bind("click", function(){
        jQ("#newAdButton").trigger("click");
    });
    var bottomPart = jQ(".bottom");
     $(window).scroll(function(data){
         if($(this).scrollTop() == 0){
             bottomPart.css({"position" : "relative"});
         } else if(bottomPart.css("position") != "fixed"){
             bottomPart.css({"position" : "fixed", "top": "0px"});
         }
         
     });
        
    jQ("#full_overlay").bind("click", function(){
       if(jQ(".newAd").css("display") == "block"){
           jQ("#newAdButton").trigger("click");
       }
    });
});

