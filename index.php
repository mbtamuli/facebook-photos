<?php
    include_once 'constants.php';
    require_once __DIR__ . '/vendor/autoload.php';
    session_start();
    $fb = new Facebook\Facebook([
        'app_id' => APP_ID,
        'app_secret' => APP_SECRET,
        'default_graph_version' => 'v2.2',
    ]);

    $helper = $fb->getRedirectLoginHelper();

    $permissions = ['email','user_photos']; // Optional permissions
    $loginUrl = $helper->getLoginUrl(
                    'http://localserver.com/fpc/fb-callback.php', $permissions);

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

        function myvardump($value)
        {
            echo "<pre>";
            print_r($value);
            echo "</pre>";
        }

        $response = fbRequest('/me?fields=id,name');
        $user = $response->getGraphUser();

        $response = fbRequest('/'. $user['id'] . '/albums');
// ***** TODO: USE THIS IF ANYTHING ELSE DOESN'T WORK
        // $albums = $response->getDecodedBody();
        $albums = $response->getGraphEdge()->asArray();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
<?php
echo "<ul>";
foreach ($albums as $album) {
    echo "<li><a href=\"http://localserver.com/fpc".
    "/albums.php?album_id=" .
    . $album['id'] . "\">" . $album['id'] .
    ": " . $album['name'] . "</a></li>";
}
echo "</ul>";
} else {
    echo '<a href="' . htmlspecialchars($loginUrl) .
    '">Log in with Facebook!</a>';
}
?>
        <script src="jquery/dist/jquery.min.js"></script>
        <script src="bootstrap/dist/js/bootstrap.min.js"></script>
    </body>
</html>
