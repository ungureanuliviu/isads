<?php
        session_start();           
        include "php_files/DBManager.php";
        $htmlAdID           = "ad_" . time();
        $adsPerPage         = 10;
        $currentPage        = intval((isset ($_GET['current_page']) ? $_GET['current_page'] : 0)); 
        $dbMan              = new DBManager();        
        $currentCategoryId  = (isset ($_GET['category_id']) ? $_GET['category_id'] : 1);
        $categoryName       = (isset ($_GET['category_name']) ? $_GET['category_name'] : "All");
        $totalPages         = $dbMan->getTotalPages($currentCategoryId, $adsPerPage);
        $results            = $dbMan->getPageAds($currentPage, $currentCategoryId, $adsPerPage);     
        $categories         = $dbMan->getCategories();         
        $adToShow           = NULL;		
        
        if(isset ($_GET['ad_id'])){
             $adToShow = $dbMan->getAdById($_GET['ad_id']);             
        }                
?>
<html>
    <head>
        <title>iasianunta.info</title>        
        <link rel="stylesheet" href="/library/styles/index.css" type="text/css" /> 
    </head>
    <body>  
        <div id="header">
            <div class="top">
                <a href="/" id="logo"><i>www.iasianunta.info</i></a>
                <div class="login floatedRight">                            
                        <?php 
                            if(isset ($_SESSION['user'])){
                        ?>                            
                            <div class="isLoggedIn clear">
                                <p class="welcome_user">Salut <?php echo($_SESSION['user']['name']); ?></p>
                                <input id ="logoutButton" type="button" class="input-button" value="Logout" /> 
                            </div>
                            <form name="loginForm" action="/" method="POST">
                                <div class="isLoggedOut hidden clear">
                                    <input type="text" value="Utilizator" id="userName" class="floatedLeft"/>                    
                                    <input type="password" value="Parola" id="userPassword" class="floatedLeft"/> <br class="clear" />
                                    <input id ="loginButton" type="submit" class="input-button floatedRight" value="Login" />
                                    <a href="/inregistrare/" class="floatedRight" id="signUpLink">Inregistrare</a>                                                
                                </div>                        
                            </form>
                                                                     
                        <?php
                            } else{
                        ?>
                            <div class="isLoggedIn hidden clear">
                                <p class="welcome_user">Salut <?php echo($_SESSION['user']['name']); ?></p>
                                <input id ="logoutButton" type="button" class="input-button" value="Logout" /> 
                            </div>                 
                            <form name="loginForm" action="/" method="POST">
                                <div class="isLoggedOut clear">
                                    <input type="text" value="Utilizator" id="userName" class="floatedLeft"/>                    
                                    <input type="password" value="Parola" id="userPassword" class="floatedLeft"/> <br class="clear" />
                                    <input id ="loginButton" type="submit" class="input-button floatedRight" value="Login" />
                                    <a href="/inregistrare/" class="floatedRight" id="signUpLink">Inregistrare</a>                                                
                                </div>
                            </form>
                        <?php
                            }
                        ?>                    
                </div>
            </div>
            <div class="bottom">
                <ul class="menu">                  
                    <?php 
                        foreach ($categories['categories'] as $category) {                           
                            echo('<li><a rel="' . $category['id'] . '" href="/categorie/' .  $category['name']. '/'. $category['id'] . '/">' . $category['name'] . '</a></li>');
                        }
                    ?>                   
                </ul>                
            <input id ="newAdButton" type="button" class="input-button-blue floatedRight" value="Adauga anunt" />                
            </div>            
        </div>
        <div id="contentWrap">
            <div class="newAd">
                <h3>Adauga un anunt nou...</h3>
                <div class="images floatedRight">
                    <h4>Pas 2. Imagini</h4>                                        
                    <form id="uploadImages" enctype="multipart/form-data" target="upload_target" action="/upload.php" method="POST">                        
                        <iframe id="upload_target" name="upload_target" src="" style="width:0;height:0;border:0px solid #fff;"></iframe>    
                        <table>
                            <tr>
                                <td>
                                    <input type="file" name="image1"  class="floatedLeft" accept="image/gif,image/jpeg,image/png"/>
                                </td>
                                <td>
                                    <a href="#Remove" class="remove"></a>
                                </td>
                            </tr>                                
                        </table>
                        <span class="loader">Uploading...</span>
                    </form>                                
                    <input type="button" id="addNewImage"class="input-button-blue" value="Adauga inca o imagine" />
                    <ul id="uploadedImages"></ul>
                    <br class="clear" />
                    <input id ="cancelButton" type="button" class="input-button floatedLeft" value="Renunta" />                                      
                    <input id ="addItNow" type="button" class="input-button-blue floatedRight" value="Adauga" />                       
                </div>
                <form name="newAdForm">       
                    <h4>Pas 1. Detalii</h4>
                    <label class="floatedLeft">Titlu*</label>
                    <input class="floatedLeft" type="text" name="ad_title" />
                    <br class="clear" />
                    
                    <label class="first floatedLeft">Continut*</label>
                    <textarea class="floatedLeft" name="ad_content"></textarea>
                    <br class="clear" />
                    
                    <label class="first floatedLeft">Pret</label>
                    <input class="floatedLeft" type="text" name="ad_price" value="-" />                        
                    <br class="clear" />
                    
                    <label class="first floatedLeft">Adresa</label>
                    <input class="floatedLeft" type='text' name="ad_address" value="-"/>                        
                    <br class="clear" />                    
                    
                    <label class="first floatedLeft">Categorie*</label>                            
                    <select class="floatedLeft" name="ad_category">
                         <?php 
                            foreach ($categories['categories'] as $category) {                           
                                echo('<option value="' .  $category['id']. '">' . $category['name'] . '</option>');
                            }
                        ?>                                                       
                    </select>                            
                    <br class="clear" />                   
                    
                    <label class="first floatedLeft">Telefon*</label>
                    <input type="text"class="floatedLeft" name="ad_phone"/>
                    <br class="clear" />
                    
                    <label class="first floatedLeft">Email</label>
                    <input class="floatedLeft" type="text" name="ad_email" value="<?php echo($_SESSION['user']['email']); ?>"/>                        
                    <br class="clear" />                                                                        
                </form>
            </div>
            <div id="content">
                <div class="title">                    
                    <h2><?php echo($categoryName); ?></h2>
                    <ul class="pages">
                        <?php
                            for($i = 0; $i < $totalPages; $i++){
                                if($i == 0){
                                    echo('<li class="active"><a href="/pages/' . $categoryName . '/' . $currentCategoryId . '/'. $i .'/" rel="'. $i . '">' .($i + 1).'</a></li>');                
                                } else if($i < 5){
                                    echo('<li><a href="/pages/' . $categoryName . '/' . $currentCategoryId . '/'. $i .'/" rel="'. $i . '">' .($i + 1).'</a></li>');                                                                                                                          
                                } else if($i == 6){
                                    echo('<li>...</li>');                                                
                                } else if($i > $totalPages - 5 && $i < $totalPages){
                                    echo('<li><a href="/pages/' . $categoryName . '/' . $currentCategoryId . '/'. $i .'/" rel="'. $i . '">' .($i + 1).'</a></li>');                                                    
                                }                                                                    
                            }
                        ?>
                    </ul>
                </div>
                <br class="clear" />
                <div class="content_right" id="side">
                    <p>test</p>                    
                </div>
                <div class="content_left">                    
                    <span class="loader"></span>
                    <ul class="ads">
                      <!--  <li>
                            <div id="ad_1316113169553" class="ad">
                                <h3>LOCURI DE MUNCA - alte oferte<span>September 15, 2011</span></h3>
                                <div class="content">
                                    SC Huge Construct SRL angajeaza topometrist cu atestat. CV:  hugeconstruct@yahoo.com, tel 0232/296024.
                                </div>
                                <div class="footer">
                                    0 Vizualizari
                                </div>                                
                            </div>
                            <div class="actions">                                                                                            
                                <input type="button" value="CITESTE" class="floatedRight input-button-green">
                            </div>
                            <br class="clear">
                        </li>                        
                      -->
                        <?php                         
                            foreach ($results['ads'] as $ad) {                                        
                                $link = "/anunt/";
                                $link .= preg_replace("/[^a-zA-Z0-9]+/", "-", substr($ad['content'], 0, 60));
                                $link .= "/" . $ad['id'] . "/";
                                
                                 echo('<li>' .  
                                    '<div class="ad" id="' . $htmlAdID.$ad['id'] . '">'.
                                        '<h3>'.$ad['title']. '<span>'. date("F j, Y", $ad['date']) . '</span></h3>'.
                                        '<div class="content">'.
                                            $ad['content'].
                                        '</div>'.
                                         '<div class="footer">' .                                            
                                            $ad['views'] . ($ad['views'] == 1 ? ' Vizualizare' : ' Vizualizari') .
                                         '</div>'.                                                    
                                    '</div>'. 
                                     '<div class="actions">' .
                                        '<a href="' . $link . '" class="floatedRight input-button-green">CITESTE</a>'.
                                     '</div><br class="clear" />'.                                         
                                '</li>');                                
                            }                        
                        ?>
                    </ul>
                    <br class="clear" />
                    <ul class="pages">
                        <?php
                            for($i = 0; $i < $totalPages; $i++){
                                if($i == 0){
                                    echo('<li class="active"><a href="/pages/' . $categoryName . '/' . $currentCategoryId . '/'. $i .'/" rel="'. $i . '">' .($i + 1).'</a></li>');                
                                } else if($i < 5){
                                    echo('<li><a href="/pages/' . $categoryName . '/' . $currentCategoryId . '/'. $i .'/" rel="'. $i . '">' .($i + 1).'</a></li>');                                                                                                                          
                                } else if($i == 6){
                                    echo('<li>...</li>');                                                
                                } else if($i > $totalPages - 5 && $i < $totalPages){
                                    echo('<li><a href="/pages/' . $categoryName . '/' . $currentCategoryId . '/'. $i .'/" rel="'. $i . '">' .($i + 1).'</a></li>');                                                    
                                }                                                                    
                            }
                        ?>
                    </ul>                                                                                 
                </div>
            </div>
            <br class="clear" />
        </div>
        <div id="footer">

        </div>        
        <div id="alert">
            <span>this is an alert text</span>
        </div>
        <div id="full_overlay"></div>
        <div id="overlay_wrap">
            <div id="overlay_content">                
                <span class="date floatedRight">11 sept 2011</span>
                <h3>Acesta este titlul</h3>
                <div class="map floatedRight">
                    <h4>Directii</h4>
                    
                </div>
                <p class="floatedLeft content">Continut</p>
                <br class="clear" />
                <div class="overlay_footer">
                    <input id="closeButton" type="button" class="floatedRight input-button" value="Close"/>
                </div>
            </div>
        </div>        
     <!-- footer -->
     <script type="text/javascript">
        var CURRENT_PAGE  = <?php echo($currentPage); ?>;;
        var TOTAL_PAGES   = <?php echo($totalPages); ?>;
        var ADS_PER_PAGE  = <?php echo($adsPerPage); ?>; 
        var ADS_JSON      = '<?php echo(json_encode($results)); ?>';
        var ADS_CATEGORIES= '<?php echo(json_encode($categories)); ?>';
        var CURRENT_CAT   = '<?php echo(json_encode(array("name" => $categoryName, "id" => $currentCategoryId))); ?>';
        var BASE_ID       = '<?php echo($htmlAdID); ?>';		
        var CURRENT_USER  = '<?php echo(isset ($_SESSION["user"]) ? json_encode($_SESSION["user"]) : "null"); ?>';
        var adToShow      = '<?php echo($adToShow != NULL ? json_encode($adToShow) : ""); ?>';
    </script>
    <script type="text/javascript" src="/library/js/jquery-1.6.3.min.js"> </script>
    <script type="text/javascript" src="/library/js/json2.js"></script>
    <script type="text/javascript" src="/library/js/client.js"> </script>
    <script type="text/javascript" src="/library/js/main.js"> </script>            
    </body>
</html>
