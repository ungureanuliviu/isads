var Client  = {};
Client.ajax = {};
/*
 * make a call to server
 * callObj should have the following struncture
 * callObj = {
 *              methodUrl: string,
 *              params: object // what data you want to send server
 *           }
 * the callback parameter is optional but it is good to pass it!!!
 * @todo pass a security token for each call
 */

/* ================= Documentation ==============================
 * 1. /API/session/login/ 
 *      Login a certain user to system
 *      method: POST
 *      params: 
 *              user_name       = the user's auth name
 *              user_password   = the user's password
 *      reponse: 
 *               if success =  '{"name":"Ungureanu Liviu","id":"1","authname":"liviu2","is_active":"1","activate_code":"0bc0ba9bcb34eccf77d6d762d937c296","email":"smartliviu@gmail.com","is_logged_in":"1"}';
 *               if fail    =  
 *               
 * 2. /API/session/logout/
 *      Logout a certain user from system
 *      params: 
 *              user_id = "user's id
 *      response: {"is_success":1,"userId":"1","message":"Logout success."}
 *      
 * 3. /API/ads/get_all/
 *      Get all ads from a specified category at a specified page
 *      params: 
 *              page            = the page for which the ads is requested
 *              category_id     = ads category
 *              ads_per_page    = how many ads you want on page
 * 4. /API/comments/add/
 *		Add a new comment to an ad. The user should be logged in.
 *		params:
 *				ad_id: the id of the current ad
 *				title: the comment's title
 *				content: the comment's content
 *				owner_user_id: the id of the user who add this comment
 *		response: {"is_success":1,"comment":{"id":259,"title":"dsgdsgds","content":"dsgdsgds","ad_id":"784","owner_user_id":"1","date":1316418989,"rating":0,"user_name":"Ungureanu Liviu"}}
 *
 * 5. /API/comments/get_all/
 *		Get all comments for an ad
 *		params:
 *				ad_id: the ad id
 *		response: {"is_success":1,"comments":[{"id":"254","title":"dsgdsgwegwe","content":"dsgdsgwegwe","ad_id":"784","owner_user_id":"1","date":"1316418703","rating":"0","user_name":"Ungureanu Liviu","user_id":"1"},{"id":"259","title":"dsgdsgds","content":"dsgdsgds","ad_id":"784","owner_user_id":"1","date":"1316418989","rating":"0","user_name":"Ungureanu Liviu","user_id":"1"},{"id":"258","title":"dsgdwsgwe","content":"dsgdwsgwe","ad_id":"784","owner_user_id":"1","date":"1316418715","rating":"0","user_name":"Ungureanu Liviu","user_id":"1"}]}
 *
 * 6. /API/comments/remove/
 *		Remove a comment
 *		params:
 *			id: the comment's id
 *			owner_user_id: the user_id of comment owner
 *
 *		response: {"is_success":1,"comment_id":"258","owner_user_id":"1"}
 *
 *	7. /API/ads/add/
 *		App a new ad into system
 *		params:
 *				title: the title of the new ad [required]
 *				content: the conente of the new ad [required]
 *				price: the price [optional]
 *				address: the address(if it is the case) [optional]
 *				category_id: the id of the catergory where this comment will be added [required]
 *				phone: ad owner's phone [required]
 *				email: ad owner's email [optional]
 *				user_id: the current user id [required]
 *				source: the source of this ad (iasianunta.info, Android app, etc)
 *
 *		response: {"is_success":1,"ad":{"id":852,"title":"teasdgds","content":"dgsgds","price":"32","address":"dsdgsdgs","cat_id":"4","user_id":"1","comments":[],"total_comments":0,"views":0,"source":"iasianunta.info","email":"smartliviu@gmail.com"}}
 **/
Client.ajax.call = function(pCallObj){
      pCallObj.params.client = "iasianunta.info";
      $.ajax({
          url: pCallObj.methodUrl,
          type: "POST",
          data: pCallObj.params,
          success: function(data){              
              if(typeof(pCallObj.callback) != 'undefined'){                  
                  if(typeof(data) == null || typeof(data) == 'undefined'){                  
                        pCallObj.callback(null);                      
                  } else{                      
                      pCallObj.callback(data);                                            
                  }
              } else{
                  alert("No callback passed on client.js.");
              }
          }
      });
}
