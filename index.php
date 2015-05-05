<?php
// include and register Twig auto-loader
require_once './vendor/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
//use Creode\Facebook\CreodeFacebookData;


/*
Encapsulates data retrieved from Facebook as a GraphObject
*/
class CreodeFacebookData
{
      /*
      Array of data.
      */
      private $data;

      public function setData($data)
      {
          $this->data = $data;
      }

      public function getData()
      {
           return $this->data;
      }
}

try {

  //parse confg file
  $config = json_decode(file_get_contents("./config/config.json"),1);

  //get facebook stuff
  $appid = $config['fb_appid'];
  $secret = $config['fb_secret'];
  FacebookSession::setDefaultApplication($appid, $secret);

  //obtain a public access token
  $token = file_get_contents('https://graph.facebook.com/oauth/access_token?client_id='.$appid.'&client_secret='.$secret.'&grant_type=client_credentials');
  $token = explode('=', $token)[1];
  $session = new FacebookSession($token);

  $request = new FacebookRequest($session, 'GET', '/64230776274/feed');
  $response = $request->execute();
  $graphObject = $response->getGraphObject();
  $dataArray = $graphObject->asArray();

  //save the data in a Creode object... not sure what the point of this is
  $data = new CreodeFacebookData();
  $data->setData($dataArray["data"]);


  echo "<pre>";
  var_dump($dataArray["data"][3]);
  echo "</pre>";

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