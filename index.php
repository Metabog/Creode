<?php
// include and register Twig auto-loader
require_once './vendor/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Creode\Facebook\CreodeFacebookData;


try {

  //parse confg file
  $config = json_decode(file_get_contents("./config/config.json"),1);

  //get facebook stuff
  $appid = $config['fb_appid'];
  $secret = $config['fb_secret'];
  $dbname = $config['dbname'];
  $dbhost = $config['dbhost'];
  $dbuser = $config['dbuser'];
  $dbpassword = $config['dbpassword'];
  $dbport = $config['dbport'];


  FacebookSession::setDefaultApplication($appid, $secret);

  //obtain a public access token
  $token = file_get_contents('https://graph.facebook.com/oauth/access_token?client_id='.$appid.'&client_secret='.$secret.'&grant_type=client_credentials');
  $token = explode('=', $token)[1];
  $session = new FacebookSession($token);

  $request = new FacebookRequest($session, 'GET', '/64230776274/feed');
  $response = $request->execute();
  $graphObject = $response->getGraphObject();
  $dataArray = $graphObject->asArray();

  //Connect to the postgres database
  $dbh = new PDO("pgsql:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);

  /*save the data in an array of Creode objects... not really useful here, just to fullfill tech test requirements. Also upsert the items into a postgres
  database. Existing posts will be updates instead of being re-inserted.*/

  $creodes = array();
  foreach($dataArray["data"] as $datum)
  {
    $post = new CreodeFacebookData();

    //populate data object with fields that exist.

    if(property_exists($datum, "message"))
    {
    $post->setMessage($datum->message);
    }
    if(property_exists($datum, "from"))
    {
    $post->setName($datum->from->name);
    }
    if(property_exists($datum, "likes"))
    {
    $post->setNumLikes(count($datum->likes->data));
    }
    if(property_exists($datum, "picture"))
    {
    $post->setPicture($datum->picture);
    }
    if(property_exists($datum, "link"))
    {
    $post->setLink($datum->link);
    }
    if(property_exists($datum, "created_time"))
    {
    $post->setCreatedTime($datum->created_time);
    }
    if(property_exists($datum, "id"))
    {
    $post->setUuid($datum->id);
    }


    array_push($creodes, $datum);


    //do an upsert to update the db with changes or now posts

    $uuid = $post->getUuid();
    $message = $post->getMessage();
    $name = $post->getName();
    $numlikes = $post->getNumLikes();
    $picture = $post->getPicture();
    $link = $post->getLink();
    $createdtime = $post->getCreatedTime();

    $stmt = $dbh->prepare("INSERT INTO posts (uuid, message, name, numlikes, picture, link, createdtime) SELECT :uuid, :message, :name, :numlikes, :picture, :link, :createdtime WHERE NOT EXISTS (SELECT 1 FROM posts WHERE uuid=:uuid)");

    $stmt->bindParam(':uuid', $uuid);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':numlikes', $numlikes);
    $stmt->bindParam(':picture', $picture);
    $stmt->bindParam(':link', $link);
    $stmt->bindParam(':createdtime', $createdtime);

    $stmt->execute();

    $stmt = $dbh->prepare("UPDATE posts SET uuid=:uuid, message=:message, name=:name, numlikes=:numlikes, picture=:picture, link=:link, createdtime=:createdtime WHERE uuid=:uuid");
    $stmt->bindParam(':uuid', $uuid);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':numlikes', $numlikes);
    $stmt->bindParam(':picture', $picture);
    $stmt->bindParam(':link', $link);
    $stmt->bindParam(':createdtime', $createdtime);

    

    $stmt->execute();



  }



  

  // specify where to look for templates
  $loader = new Twig_Loader_Filesystem('templates');
  
  /*
  https://graph.facebook.com/oauth/access_token?client_id=413314302012055&client_secret=9908c949cd6136eec10ee122a2ab6a37&grant_type=client_credentials
*/
  // initialize Twig environment
  $twig = new Twig_Environment($loader);
  
  // load template
  $template = $twig->loadTemplate('index.html');
  
  // render template
  echo $template->render(array('posts'=>$dataArray["data"]));
  
} catch (Exception $e) {
  die ('ERROR: ' . $e->getMessage());
}
?>