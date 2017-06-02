<?php
   header('content-type: application/json; charset=utf-8');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET, POST');  
        date_default_timezone_set('UTC');
        require_once __DIR__ . '/php-graph-sdk-5.0.0/src/Facebook/autoload.php';  
        
        $APP_ID=            '1691655584460409';
        $APP_SECRET     = '510c75400fdfbc3fe27c1aa53df8998c';
        $APP_ACCESS_TOKEN='EAAYCjUIxrnkBAL0IpQuzeq6ET3ZB8cLYBKdJPMNJWMOnK1RQiyE4KpNI1egpgdsPDugReGpGFJNIus6SZAbazZBnAdrR8PKZCgSgnqJA6fJ8XbTY7LscEVQVG2dx7eCrxE7oQncrfIewjZA36ZB04FLjLQgd6bnF0ZD';
$incomingQ= $_GET['key'];
$incomingType=$_GET['type'];
$incomingLat=$_GET['lat'];
$incomingLong=$_GET['long'];
$incomingId=$_GET['id'];
         $fb = new Facebook\Facebook([
            'app_id' => $APP_ID,
            'app_secret' => $APP_SECRET,
            'default_graph_version' => 'v2.8',
                                    ]);
            $queryFields="id,name,picture.width(700).height(700)";


if($incomingId!=null){
    ///id?fields=albums.limit(5){name,photos.limit(2){name, picture}},posts.limit(5){created_time,message}
    $localURL="/".$incomingId."?fields=albums.limit(5){name,photos.limit(2){name, picture,images}},posts.limit(5){created_time,message}";
    //echo $localURL;
    
    
    
}

else{
if($incomingType!="place")
            $localURL='/search?q='.$incomingQ."&type=".$incomingType."&fields=".$queryFields;
else
$localURL='/search?q='.$incomingQ."&type=".$incomingType."&fields=".$queryFields."&center=".$incomingLat.",".$incomingLong;
}

try {
        
           
    
            //Below getting the global variables
            $fb=$GLOBALS['fb'];
            $APP_ACCESS_TOKEN=$GLOBALS['APP_ACCESS_TOKEN'];
            
         
            $response = $fb->get($localURL,$APP_ACCESS_TOKEN);
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
        
       
            /*echo "<pre>";
            print_r($response->getDecodedBody());
            echo "</pre>";*/
            //echo "myFunc(".$response.");";
        
           echo json_encode($response->getDecodedBody(),true);
	    
?>
