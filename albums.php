<?php
include_once 'constants.php';
require_once __DIR__ . '/vendor/autoload.php';
session_start();
$fb = new Facebook\Facebook([
    'app_id' => APP_ID,
    'app_secret' => APP_SECRET,
    'default_graph_version' => 'v2.2',
]);

if (!empty($_SESSION['fb_access_token']) &&
    isset($_SESSION['fb_access_token'])) {

    $accessToken = $_SESSION['fb_access_token']->getValue();
    function fbRequest($value='/me')
    {
        global $fb, $accessToken;
        try {
          return $fb->get($value, $accessToken);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }
    }
}

$album_id = $_GET['album_id'];
$response = fbRequest('/'. $album_id . '/photos');
$album_photos = $response->getGraphEdge()->asArray();
$new = [];
foreach ($album_photos as $album_photo) {

    $response = fbRequest('/'. $album_photo['id'] .
    '?fields=images');

    $photoObj = $response->getGraphObject()->asArray();
    array_push($new, array(
        'id' => $photoObj['id'],
        'url' => $photoObj['images'][0]['source']
    ));
    // print_r($photoObj['images'][0]['source']);
}

echo json_encode($new);
