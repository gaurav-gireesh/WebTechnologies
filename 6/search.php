<!DOCTYPE html>
<html lang="en">
    
    <?php
      
    //setting the timezone for correct functioning of the Facebook SDK
        date_default_timezone_set('UTC');
   
        require_once __DIR__ . '/php-graph-sdk-5.0.0/src/Facebook/autoload.php';    
    
    
   
        //Facebook Apllication Credentials.
        $APP_ID=            '1691655584460409';
        $APP_SECRET     = '510c75400fdfbc3fe27c1aa53df8998c';
    //$APP_ACCESS_TOKEN='EAAYCjUIxrnkBAChgdR0yep4KJQN0NO57fqiteeBZCz0hOvrwlqdpg9djKSAvi5Vnn21VeZAkW51mJZBIFQpqHB4bnOUUNh9pZAz89qvCmq7IUZBX7znG4rUBK2b4DhZA7eg8ZCvZC8a5RWNM6z84fTlb5VGmKwp20C4ZD';
    $APP_ACCESS_TOKEN='EAAYCjUIxrnkBAL0IpQuzeq6ET3ZB8cLYBKdJPMNJWMOnK1RQiyE4KpNI1egpgdsPDugReGpGFJNIus6SZAbazZBnAdrR8PKZCgSgnqJA6fJ8XbTY7LscEVQVG2dx7eCrxE7oQncrfIewjZA36ZB04FLjLQgd6bnF0ZD';
    //Google API Key for Google Geocoding api call 
    $GOOGLE_API_KEY='AIzaSyBM6eiPJjTH76AXfmFJjxT8faVAglh96gE';
        
    //Creating an instance of Facebook App passing APP_ID and APP_SECRET
        $fb = new Facebook\Facebook([
            'app_id' => $APP_ID,
            'app_secret' => $APP_SECRET,
            'default_graph_version' => 'v2.8',
                                    ]);
  // $fb->setExtendedAccessToken();
    //$helper=$fb->getCanvasHelper();
   // echo "Before access token was :".$APP_ACCESS_TOKEN;
   /* $client=$fb->getOAuth2Client();
    $APP_ACCESS_TOKEN2=$client->getLongLivedAccessToken($APP_ACCESS_TOKEN);*/
   // echo "<br/> After the long lived request :".$APP_ACCESS_TOKEN2;
   // $APP_ACCESS_TOKEN=$helper->getAccessToken();
   // echo "The access token is :".$APP_ACCESS_TOKEN;
    
      $q=$type=$location=$distance="";
    
     //...the fields to be passed to different queries
        $queryFields="id,name,picture.width(700).height(700)";
        $albumPostQueryFields="albums.limit(5){name,photos.limit(2){name, picture}},posts.limit(5)";
    
    //following function takes the url parameter and does the request and returns the response array to the caller
    
    function callFBRest($queriedURL)
    {
        
        try {
        // Returns a `Facebook\FacebookResponse object
             $permissions=['user_posts','public_profile','user_friends'];
    
            //Below getting the global variables
            $fb=$GLOBALS['fb'];
            $APP_ACCESS_TOKEN=$GLOBALS['APP_ACCESS_TOKEN'];
            
         
            $response = $fb->get($queriedURL,$APP_ACCESS_TOKEN/*,$permissions*/);
            } 
            catch(Facebook\Exceptions\FacebookResponseException $e)
            {
                echo $e->getMessage();
                return null;
            } 
           catch(Facebook\Exceptions\FacebookSDKException $e) 
           {
               echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
        
        return $response;
    }
    
    
   
    
    function queryGeneric($incomingType)
    {
        //forming the query url to call callFBRest function
        
        //of the form '/search?q=usc&type=user&fields= id,name,picture.width(700).height(700)', $accessToken);
        
        if($incomingType=="Users")
        $localURL='/search?q='.$GLOBALS['q']."&type=user&fields=".$GLOBALS['queryFields'];
         if($incomingType=="Groups")
              $localURL='/search?q='.$GLOBALS['q']."&type=group&fields=".$GLOBALS['queryFields'];
              if($incomingType=="Pages")
                   $localURL='/search?q='.$GLOBALS['q']."&type=page&fields=".$GLOBALS['queryFields'];
                   if($incomingType=="Events")
                       $localURL='/search?q='.$GLOBALS['q']."&type=event&fields=".$GLOBALS['queryFields'].",place";
        
        if($incomingType=="Places"){
            
            $loc=$_POST['location'];
            $distance=$_POST['distance'];
            if($loc==null && $distance==null)
                $localURL="/search?q=".$GLOBALS['q']."&type=place&center=&distance=&fields=".$GLOBALS['queryFields'];
            else{
            $latLongArray=geocode($loc);
          
            if($latLongArray!=null && $latLongArray[0]!="" && $latLongArray[1]!="")
            $localURL="/search?q=".$GLOBALS['q']."&type=place"."&center=".$latLongArray[0].",".$latLongArray[1]."&distance=".$distance."&fields=".$GLOBALS['queryFields'];
          else return "Address is invalid";
           
            }
        }
        
        
        
       $userResultInitial=callFBRest($localURL);
      if($userResultInitial!=null)
       return ($userResultInitial->getDecodedBody()['data']);
        else return null;
    }
    
    function queryDetails($keyid)
    {
        //creating url for the form 
        /*
        
        https://graph.facebook.com/v2.8/id?
            fields=id,name,picture.width(700).height(700),albums.limit(5){name,photos.limit(2){name, picture}},posts.limit(5)&access_token=Your_Access_Token
        
        */
       
        $detailingURL="/".$keyid."?fields=".$GLOBALS['queryFields'].",".$GLOBALS['albumPostQueryFields'];
        $userDetailedResult=callFBRest($detailingURL);
        
    if($userDetailedResult!=null)
        return $userDetailedResult->getDecodedBody();else return null;
        
    }
    
    function queryHighResImageDetail($picId)
    {
      
        $picqueryurl="/$picId?fields=images";
       
        $highResImageResponse=callFBRest($picqueryurl);
        $imageurl=null;
        if($highResImageResponse!=null){
            for($im=0;$im<sizeof($highResImageResponse->getDecodedBody()['images']);$im++)
            {
                if($highResImageResponse->getDecodedBody()['images'][$im]['height']=="700")
                {
                    $imageurl=$highResImageResponse->getDecodedBody()['images'][$im]['source'];
                }
            }
            
            if($imageurl==null)
            {
        return $highResImageResponse->getDecodedBody()['images'][0]['source'];
            } else return $imageurl;}
           
        else return null;
/* handle the result */
      // return $highResImageResponse->getHeaders()['Location'];
        //echo $highResImageResponse->getDecodedBody()['data']['url'];
    }
    
    function queryHighResProfilePhoto($picId)
    {
      
        $picqueryurl="/$picId/picture";
       
        $highResImageResponse=callFBRest($picqueryurl);
        if($highResImageResponse!=null)
       return $highResImageResponse->getHeaders()['Location']; else return null;
        //echo $highResImageResponse->getDecodedBody()['data']['url'];
    }
    
function geocode($placeAddress){
 
    if($placeAddress=="" || $placeAddress== " ") return null;
    
  
    $address = urlencode($placeAddress);
     $googleAPIKey=$GLOBALS['GOOGLE_API_KEY'];
    
    // google map geocode api url
    $url = "https://maps.google.com/maps/api/geocode/json?address={$address}&key=$googleAPIKey";
 
    // get the json response
    $resp_json = file_get_contents($url);
     
    // decode the json
    $resp = json_decode($resp_json, true);
    //echo $resp['status'];
   
    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
 
        // get the important data
        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        $formatted_address = $resp['results'][0]['formatted_address'];
         
        // verify if data is complete
        if($lati && $longi){
       
            // put the data in the array
            $data_arr = array();            
             
            array_push(
                $data_arr, 
                    $lati, 
                    $longi 
                   
                );
             
            //return $data_arr;
             
        }else{
            return false;
        }
         
    }else{ 
        return false;
    }
   
   return $data_arr;
}
   ?>
    
    
    
    
    
    
    <head>
                <title>Assignment 6 - Gaurav</title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
                <style type="text/css">
                    .fbSearchBox
                    {
                        margin: auto;
                        display:block;
                        width: 600px;
                        height: 150px;
                        background-color: #f8f8f8;
                        border: 2px #c8c8c8 solid;
                        align-content: center;
                       
                    }
                </style>
        
        
        <!-- Javascript function to selectively hide/show fields on the basis of select menu option selected-->
        <script language="javascript">
            
            function showHiddenFields()
            {
                document.getElementById('location').value="";
                document.getElementById('distance').value="";
                typeDropElement=document.getElementById("typeDrop")    ;
                selectedValue=typeDropElement.options[typeDropElement.selectedIndex].value;
                if(selectedValue=="Places")
                      document.getElementById("placeSpecific").style.visibility="visible"; 
                else
                      document.getElementById("placeSpecific").style.visibility="hidden"; 
            
            }
            
            //Function for resetting and clearing or initializing the form contents.
            
            function resetToInitial()
            {
                
                document.getElementById("keyword").value="";
                if(document.getElementById("placeSpecific").style.visibility == "visible")
                        document.getElementById("placeSpecific").style.visibility = "hidden";
             
                 document.getElementById("location").value="";
                 document.getElementById("distance").value="";
                document.getElementById("typeDrop").value="";
                if(document.getElementById(  'resultBox')!=null)
                        document.getElementById(  'resultBox').style.display="none";
                document.getElementById("searchForm").submit();
               
            }
        
       
        
        </script>
        
                
    </head>

    <body>
    
    <div class="fbSearchBox">
        <div style="margin:3px;">
     
        <span ><i style="font-size:25px;font-weight:400;margin:200px;font-family:'Times new Roman';">Facebook Search</i></span>
        <hr style="background-color:#d0d0d0; height:1px;margin:2px;"/>
            
            
        <form style="" name="searchForm" id="searchForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            Keyword:<input autofocus style="margin-top:5px;margin-left:3px" type="text" id="keyword" size=15 value="<?php 
                if(isset($_POST["keyword"]))
                {echo $_POST["keyword"];}
                else
                {
                     if(isset($_GET['q']))
                        echo $_GET['q'];
                   // else echo "";
                }
                
                
                
                ?>" oninvalid="setCustomValidity('This cant be left empty'); " required name="keyword" oninput="setCustomValidity('')" ><br/>
            Type:
           
            <select onchange="showHiddenFields();"style="margin-left:27px;margin-top:2px;" name="typeDrop" id="typeDrop" size=1>
                            <option
                                    <?php
    if(isset($_POST['typeDrop'])){if($_POST['typeDrop']=="Users") {echo "selected";}} else{echo "selected";}
    
    
    ?> value="Users" >Users</option> 
                            <option
                                    <?php
    if(isset($_POST['typeDrop']))
    {if($_POST['typeDrop']=="Pages") echo "selected";
    } 
    else
    {
        if(isset($_GET['type']))
        {
            if($_GET['type']=="Pages") echo "selected";
        }
    }
    
    
    ?> value="Pages">Pages </option>
                            <option                                  <?php
    if(isset($_POST['typeDrop']))
    {if($_POST['typeDrop']=="Events") echo "selected";
    } 
    else
    {
        if(isset($_GET['type']))
        {
            if($_GET['type']=="Events") echo "selected";
        }
    }
    
    ?>  value="Events">Events </option>
                           
                            <option                                  <?php
    if(isset($_POST['typeDrop']))
    {if($_POST['typeDrop']=="Places") echo "selected";
    } 
    else
    {
        if(isset($_GET['type']))
        {
            if($_GET['type']=="Places") echo "selected";
        }
    }
    ?> value="Places">Places </option>
                 <option                                  <?php
    if(isset($_POST['typeDrop']))
    {
        
       
        if($_POST['typeDrop']=="Groups") echo "selected";
    } 
    else
    {
         if(isset($_GET['type']))
        {
            if($_GET['type']=="Groups") echo "selected";
        }
    }?> value="Groups">Groups </option>
            
            
                
                      </select><br/>
            
       <?php    if(isset($_POST['typeDrop'])){if($_POST['typeDrop']=="Places" ){ ?>
            <div id="placeSpecific" style="margin-top:2px;visibility:visible;">
            Location <input id="location"style="margin-left:6px" type="text" name="location" size=15 value="<?php 
                                                                                
                                                                                echo (isset($_POST['location'])) ?  $_POST['location']:
                                                                                (isset($_GET['location'])? $_GET['location']:"")
                                                                                ;?>"/>  
            Distance(metres)<input id="distance"style="margin-left:3px" type="text"  size=15 name="distance" value="<?php 
                                                                                
                                                                                echo (isset($_POST['distance'])) ?  $_POST['distance']:
                                                                                (isset($_GET['distance'])? $_GET['distance']:"")
                                                                                ;?>"/> <br/>
            </div>
<?php }else{?>
            <div id="placeSpecific" style="margin-top:2px;visibility:hidden;">
            Location <input id="location"style="margin-left:6px" type="text" name="location" size=15 value="<?php 
                                                                                
                                                                                echo (isset($_POST['location'])) ?  $_POST['location']:
                                                                                (isset($_GET['location'])? $_GET['location']:"")
                                                                                ;?>"/>  
            Distance(metres)<input id="distance"style="margin-left:3px" type="text"  size=15 name="distance" value="<?php 
                                                                                
                                                                                echo (isset($_POST['distance'])) ?  $_POST['distance']:
                                                                                (isset($_GET['distance'])? $_GET['distance']:"")
                                                                                ;?>"/> <br/>
            </div>
            
            <?php }}else{
              if(isset($_GET['type']) && $_GET['type']=="Places"){
            
            ?>
             <div id="placeSpecific" style="margin-top:2px;visibility:visible;">
            Location <input id="location"style="margin-left:6px" type="text" name="location" size=15 value="<?php 
                                                                                
                                                                                echo (isset($_POST['location'])) ?  $_POST['location']:
                                                                                (isset($_GET['location'])? $_GET['location']:"")
                                                                                ;?>"/>  
            Distance(metres)<input id="distance"style="margin-left:3px" type="text"  size=15 name="distance" value="<?php 
                                                                                
                                                                                echo (isset($_POST['distance'])) ?  $_POST['distance']:
                                                                                (isset($_GET['distance'])? $_GET['distance']:"")
                                                                                ;?>"/> <br/>
            </div><?php }else{?>
            
            <div id="placeSpecific" style="margin-top:2px;visibility:hidden;">
            Location <input id="location"style="margin-left:6px" type="text" name="location" size=15 value="<?php 
                                                                                
                                                                                echo (isset($_POST['location'])) ?  $_POST['location']:
                                                                                (isset($_GET['location'])? $_GET['location']:"")
                                                                                ;?>"/>  
            Distance(metres)<input id="distance"style="margin-left:3px" type="text"  size=15 name="distance" value="<?php 
                                                                                
                                                                                echo (isset($_POST['distance'])) ?  $_POST['distance']:
                                                                                (isset($_GET['distance'])? $_GET['distance']:"")
                                                                                ;?>"/> <br/>
            </div>
            
            
            <?php }} ?>
             <input style="margin-left:67px;margin-top:2px;"type="submit" value="Search" name="submitForm"> &nbsp; 
             <input type="reset" name="resetForm" value="Clear" onclick="resetToInitial(); "/>
            
        </form>    
        </div>
        
    </div>
        
     
   
 
    <div name="resultBox" id="resultBox" style="display:block;align-items:center;">
    
     <?php
            //If the form is submitted..
            if(isset($_POST['submitForm']))
            {
                $q=$_POST['keyword'];
                $type= $_POST['typeDrop'];
                $location=$_POST['location'];
                $distance=$_POST['distance'];
                
                
                if(isset($_POST['typeDrop']))
                {
                    if($type=='Users'||$type=='Events'||$type=='Groups'||$type=='Pages'||$type=='Places') {
                        
                        if($type=='Places' && $location==null && $distance!=null)
                        {
                           
                                //The case for invalid input
                                  echo
                                "<div  style=\" margin:auto; margin-top:30px; text-align:center;  border: 2px #dfdfdf solid;width:700px;height:22px;background-color: #fbfbfb;\" name='ZeroResultsPlaces' id='ZeroResultsPlaces'><div style=\"padding-top:3px\"><strong>Distance specified without location or address.</strong></div> </div>";
                            
                                
                           
                            
                        }
                        else{
                            
                            
                            
                        $initialQueryResult=queryGeneric($type);
                            if(gettype($initialQueryResult)=="string"){
                                 echo
                                "<div  style=\" margin:auto; margin-top:30px; text-align:center;  border: 2px #dfdfdf solid;width:700px;height:22px;background-color: #fbfbfb;\" name='ZeroResultsPlaces' id='ZeroResultsPlaces'><div style=\"padding-top:3px\"><strong>$initialQueryResult</strong></div> </div>";
                            }
                            
                        else
                         {   
                    
                    
                        if(sizeof($initialQueryResult)==0)
                        {
                            
                            echo
                                "<div  style=\" margin:auto; margin-top:30px; text-align:center;  border: 2px #dfdfdf solid;width:700px;height:22px;background-color: #fbfbfb;\" name='ZeroResults' id='ZeroResults'><div style=\"padding-top:3px\">No Records have been found</div> </div>";
                            
                            
                            
                        }
                    else
                    {
                        echo "
                        <table id= 'usersTable' name='usersTable' style=\"width:750px;font-family:serif arial; border: 1px solid #dfdfdf;border-spacing:0px;  margin:auto;margin-top:20px;background-color:#f8f8f8\">
                        <tr style=\"background-color:#e8e8e8;\">";
                        
                        $detailHeader="Details";
                        if($type=="Events")
                            $detailHeader="Place";
                       echo " <td style=\"border: 1px solid #dfdfdf;\"><strong>Profile Photo</strong></td><td style=\"border: 1px solid #dfdfdf;\"><strong>Name</strong></td><td style=\"border: 1px solid #dfdfdf;\"><strong>$detailHeader</strong></td></tr>
                        
                        
                        ";
                        
                        $resultIterator=0;
                        for($resultIterator=0;$resultIterator<sizeof($initialQueryResult);$resultIterator++)
                        {
                            echo "<tr> ";
                            
                            $itemId=$initialQueryResult[$resultIterator]['id'];
                          //  echo "<br>$itemId";
                            $itemName=$initialQueryResult[$resultIterator]['name'];
                           // if($type=='Events')
                            $smallPicUrl=$initialQueryResult[$resultIterator]['picture']['data']['url'];
                           // $smallPicUrl=queryHighResProfilePhoto($itemId);
                           // $smallPicUrl=queryHighResImageDetail($itemId);
                            $htmlImage="<img src=".$smallPicUrl.">";
                            
                           /* if($type=="Events" || $type=="Groups" || $type=="Places")
                            {//echo "Coming in events groups places";
                                $smallPicUrl=queryHighResProfilePhoto($itemId);
                                  $htmlImage="<img src=".$smallPicUrl.">";
                            }*/
                            if($type=="Events")
                            {
                                
                                if(in_array("place",array_keys($initialQueryResult[$resultIterator])))
                                $eventPlace=$initialQueryResult[$resultIterator]['place']['name'];
                                else $eventPlace="Unknown";
                            }
                          
                            
                             echo "<td style=\"border: 1px solid #dfdfdf; border-collapse:collapse\"><img width=40 height=30 src=\"".$smallPicUrl."\"
                             
                             onclick=\"win1=window.open();win1.document.write('$htmlImage');win1.document.close();\"
                             
                             ></td>";//window.document.write($htmlImage);
                         
                            echo "<td style=\"border: 1px solid #dfdfdf;border-collapse:collapse;\">".$itemName."</td>";
                            if($type=='Places')
                            {
                            echo "<td style=\"border: 1px solid #dfdfdf;border-collapse:collapse;\"><a href=\"".htmlspecialchars($_SERVER["PHP_SELF"])."?userKey=$itemId&q=$q&type=$type&location=$location&distance=$distance\">Details</a></td>";}
                            else
                             {  if($type!="Events") 
                             {//echo $itemId;
                                 echo "<td style=\"border: 1px solid #dfdfdf;border-collapse:collapse;\"><a href=\"".htmlspecialchars($_SERVER["PHP_SELF"])."?userKey=$itemId&q=$q&type=$type\">Details</a></td>";}
                              
                              else 
                              {echo "<td style=\"border: 1px solid #dfdfdf;border-collapse:collapse;\">
                              $eventPlace</td>";}
                             }
                            echo "</tr>";
                            
                        }
                        echo "</table>";
                        
                        
                        
                        
                        
                        
                    }}}}
                    
                }
                
            }
        
        //Else clicking on link Details Brought has reloaded the page
        else
        {
            
            if(isset($_GET['userKey']))
            {
                $userid=$_GET['userKey'];
                $initialDetailResult=queryDetails($userid);
               // $initialDetailResult=queryDetails("124984464200434");
                if($initialDetailResult!=null && in_array("albums",array_keys($initialDetailResult)))
                    
                {
               $noAlbums=sizeof($initialDetailResult['albums']['data']);
                
                $albums=$initialDetailResult['albums']['data'];}
                    else $noAlbums=0;
                
                
                if($initialDetailResult!=null && in_array("posts",array_keys($initialDetailResult))){
                    
                    
                    
               $noPosts=sizeof($initialDetailResult['posts']['data']);
                    $messagepresent=false;
                    for($pc=0;$pc<$noPosts;$pc++)
                    {
                        if(in_array("message",array_keys($initialDetailResult['posts']['data'][$pc])))
                        { $messagepresent=true;break;}
                    }
                    if($messagepresent==true)
                $posts=$initialDetailResult['posts']['data'];
                    else $noPosts=0;
                }
                    else $noPosts=0;
                
               // echo $noPosts;
                
                        if($noAlbums==0)
                        {
                            
                            echo
                                "<div  style=\" margin:auto; margin-top:20px; text-align:center;  border: 2px #dfdfdf solid;width:700px;height:22px;background-color: #fbfbfb;\" name='AlbumZeroResults' id='AlbumZeroResults'><div style=\"padding-top:3px\">No Albums have been found</div> </div>";
                            
                            
                            
                        }
                
                        else
                        {
                           echo" <div id=\"albumBox\" name=\"albumBox\" style=\" margin:auto; margin-top:30px; text-align:center; width:700px;height:22px;background-color:#c8c8c8;\" >
                   <span > <a href=\"#\" onclick=\"
                   if(document.getElementById('albumsList').style.display=='block')
                   {
                   document.getElementById('albumsList').style.display='none';
                   }
                   else
                   {
                    document.getElementById('albumsList').style.display='block';
                   }
                   
                   if(document.getElementById('postsList')!=null)
                   document.getElementById('postsList').style.display='none';\" name=\"albumsLink\" id=\"albumsLink\" ><div style=\" padding-top:0px;\">Albums</div></a></span>
        
        </div>
        <div name=\"albumsList\" id=\"albumsList\" style=\"display:none;\">
        
        
        
        
         <table id= 'albumsTable' name='albumsTable' style=\"width:700px;font-family: sans-serif arial; border: 2px solid #dfdfdf;border-spacing:0px;  margin:auto;margin-top:20px;background-color:#f8f8f8\">";
        
        for($albumIterator =0;$albumIterator<$noAlbums;$albumIterator++)
        {
                $albumName=$albums[$albumIterator]['name'];
                $noOfPics=(in_array("photos",array_keys($albums[$albumIterator])))?sizeof($albums[$albumIterator]['photos']['data']):0;
                echo "<tr><td style=\"border: 1px solid #cfcfcf;border-collapse:collapse;\">";
                if($noOfPics==0){ echo $albumName;}else
                {
                echo "
                <a href=\"#\"  onclick=\" 
                
                if(document.getElementById('album$albumIterator').style.display=='none')
                {
                 document.getElementById('album$albumIterator').style.display='block';  
                }
                else{
                document.getElementById('album$albumIterator').style.display='none';}
                \" >";
            
            
            echo $albumName."</a>
            
            </td></tr>";}
                echo "
                
                <tr name=\"hello\" id=\"album$albumIterator\" style=\"display:none;\" >";
               
                for($picIterator=0;$picIterator<$noOfPics;$picIterator++)
                {
                   
                  $picHighresId=$albums[$albumIterator]['photos']['data'][$picIterator]['id'];
                    $highrespicurl=queryHighResImageDetail($picHighresId);
                     $pichtml="<html><head><title>$highrespicurl</title></head><img src=".$highrespicurl."></html>";
                    $emptyurl="";
                  
                    echo "
                    <td style=\"border: 1px solid #cfcfcf;border-collapse:collapse;\">
                    <img src=\"".$albums[$albumIterator]['photos']['data'][$picIterator]['picture']."\" width=80px height=80px
                    onclick=\"win2=window.open($emptyurl);win2.document.write('$pichtml');win2.document.close();
                    
                    \" >
                    </td>  ";
                    
                }
            echo "</tr>";
           
        }
 
        
    echo " </table>   </div>  ";
                            
                        }
                
               
                
                  if($noPosts==0)
                        {
                            
                            echo
                                "<div  style=\" margin:auto; margin-top:20px; text-align:center;  border: 2px #dfdfdf solid;width:700px;height:22px;background-color: #fbfbfb;\" name='ZeroResults' id='ZeroResults'><div style=\"padding-top:3px\">No Posts have been found</div> </div>";
                            
                        }
                
                else
                     {
                    
                     echo" <div id=\"postBox\" name=\"postBox\" style=\" margin:auto; margin-top:30px; text-align:center; width:700px;height:22px;background-color:#c8c8c8;\" >
                   <span > <a href=\"#\"  onclick=\"
                   if( document.getElementById('postsList').style.display=='block')
                   document.getElementById('postsList').style.display='none';
                   else
                   document.getElementById('postsList').style.display='block';
                   
                   if(document.getElementById('albumsList')!=null)
                   document.getElementById('albumsList').style.display='none';\"  name=\"postsLink\" id=\"postsLink\" ><div style=\" padding-top:0px;\">Posts</div></a></span>
        
                </div>
        <div name=\"postsList\" id=\"postsList\" style=\"display:none;\">
        
       <table id= 'postsTable' name='postsTable' style=\"width:700px;font-family:serif arial; border: 2px solid #dfdfdf;border-spacing:0px;  margin:auto;margin-top:20px;background-color:#f8f8f8\">
       <tr><td><strong>Message</strong></td></tr>
       
       ";
                    
                    for($i=0;$i<$noPosts;$i++)
                    {
                        if(in_array("message",array_keys($posts[$i])))
                        {
                        echo "<tr style=\"border: 1px solid #dfdfdf; border-spacing:0px;\">";
                       
                        
                            echo " <td style=\"border: 1px solid #cfcfcf;border-collapse:collapse;\">";
                        
                        echo $posts[$i]['message'];
                        echo "</td></tr>";}
                        
                    }
                    
                    
                    
        echo "</table> </div>  ";
                    
                }
              
            }
            
        }
    
  
    
?>
</div>
        <noscript></noscript>
 </body>
</html>