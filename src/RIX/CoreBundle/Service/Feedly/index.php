
<?php

require_once "Feedly.php";

/**
 * Read this before set your callback url while in Sandbox mode
 * @see https://groups.google.com/forum/#!topic/feedly-cloud/vSo0DuShvDg/discussion
 */
$sandboxMode = false;
$accesToken="AwTbHqtO4qGpLkA0vR4ZTT_7RFpu1hwVwhK9ZFtf8wgd0pXDN4-LVpNE_lw_3VL3X3VbtnUmRmG1mxWcNN9wOVEgVFmSRegcMOm5Mc-bBB3RGo75W9Dmn6AotqjRLPEjQKqPKg7zXygxinFg4I8Z0yXT5nPEDVcE3x8V_ha0_pggeCSQcF7kCLPru5BJ6pNqzG0OFb214ilDclXME7OkECbKqqUcHA:feedlydev";
$feedly = new Feedly($sandboxMode, false);

$sandboxMode ?
  $callback = "https://localhost/feedly/example/index.php" :
  $callback = "http://" . $_SERVER['HTTP_HOST'] .
  dirname($_SERVER['PHP_SELF']);

//$loginUrl = $feedly->getLoginUrl("sandbox", "https://localhost/feedly/example/index.php");

//$json=$feedly->getProfile($accesToken);

$json_search=$feedly->searchFeeds($_POST['search'],6,"en_EN",$accesToken);
//var_dump($json);

//$json=$feedly-> getFeedMetadata("feed/http://feeds.computerworld.com/Computerworld/News", $accesToken);
//$json=$feedly-> getCategories($accesToken) ;

//$json=$feedly-> getStreamContent("CvXTapverWJKxzHMFtdLAnIgkRkSysvOpa2jl/VsATM=_154cdfc50a3:454ec0b:97af7a9a", $count=3, $ranked=NULL,$unreadOnly=NULL, $newerThan=NULL, $continuation=NULL, $accesToken);

//$json=$feedly-> getStreamContent("feed/http://feeds.computerworld.com/Computerworld/News", $count=1, $ranked=NULL,$unreadOnly=NULL, $newerThan=NULL, $continuation=NULL, $accesToken);


$json_output_search = json_decode($json_search,true);
//var_dump($json_output);
$results_search=$json_output_search['results'];

foreach ($results_search as $result_search) 
{
    $feedId =$result_search['feedId'];
    $feedTitle =$result_search['title'];
    if(isset($result_search['description']))
    $feedDescription =$result_search['description'];
    else 
    $feedDescription =" Fara descriere";
    $feedSite =$result_search['website'];
    $feedSubscribers =$result_search['subscribers'];
    $feedTags =$result_search['deliciousTags'];
   
        //afisare_feed($feedId,$feedly,$accesToken);
    //echo $feedId;
    //echo "<p><a href=\"https://localhost/feedly/example/search_test.php?feedId=". $feedId "</a></p>";
    if(isset($result_search['visualUrl']))
    {
     $feedLogo =$result_search['visualUrl'];
    echo "<img src=\"".$feedLogo."\" >";
    }
    else
    {
         echo "<img src=\"https://www.dwolla.com/avatars/812-999-0092\" height=\"100\" width=\"100\" >";
  
    } 
    echo "<br>";
    echo "Descriere : " .$feedDescription;
    echo "<br>";
    echo "Tags : ";
    for($i=0;$i<sizeof($feedTags);$i++)
    {

    echo $feedTags[$i]." , ";
    
    }
    echo "<br>";
    echo "<a href=\"search_test.php?feedId=".$feedId."\">".$feedTitle."</a>"." <- Click to see feeds ";
    echo "<br>";

    echo "Website :" .$feedSite;
    echo "<br>";
    echo "Subcribers : " .$feedSubscribers;
    echo "<br>";

    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo "<br>";

}













//echo $json_output['title'];
//echo "<br> Autor ";
//echo $json_output['items']['0']['author'];

//echo $json_output['items']['0']['summary']['content'];

//var_dump($feedly-> getCategories($accesToken));
 
/*
 $url="https://cloud.feedly.com/v3/search/feeds?q=%23computer&n=1&l=en_EN";

 if (($r = @curl_init($url)) == false) {
            throw new Exception("Cannot initialize cUrl session.
                Is cUrl enabled for your PHP installation?");
        }

        curl_setopt($r, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($r, CURLOPT_ENCODING, "");

        curl_setopt($r, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($r, CURLOPT_CAINFO, "C:\wamp\bin\apache\Apache2.2.21\cacert.crt");

        
        curl_setopt($r, CURLOPT_HTTPHEADER, array (
            "Authorization: OAuth " . $accesToken
        ));

        $response = curl_exec($r);
        var_dump($response);
        curl_close($r);
*/
 
?>
