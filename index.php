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
        echo "<ul>";
        foreach ($albums as $album) {
            echo "<li>" .
            $album['id'] . ": " . $album['name'];
                $response = fbRequest('/'. $album['id'] . '/photos');
                $album_photos = $response->getGraphEdge()->asArray();
                echo "<ul>";
                foreach ($album_photos as $album_photo) {
                    echo "<li>" . $album_photo['id'] .
                    (isset($album_photo['name']) ? ": " .
                    $album_photo['name'] : "") . "</li>";

                    $response = fbRequest('/'. $album_photo['id'] .
                    '?fields=height,width,link,picture,images');

                    $photoObj = $response->getGraphObject()->asArray();

                    echo "<img src=\"". $photoObj['images'][0]['source'] .
                    "\" height=\"" . $photoObj['images'][0]['height'] .
                    "\" width=\"" . $photoObj['images'][0]['width'] ."\">";
                }
                echo "</ul>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo '<a href="' . htmlspecialchars($loginUrl) .
        '">Log in with Facebook!</a>';
    }
