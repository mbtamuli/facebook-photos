<?php
require_once __DIR__ . '/Facebook/autoload.php';
  session_start();
  $fb = new Facebook\Facebook([
  'app_id' => '1231765253500563',
  'app_secret' => 'da199b58a28d39a5fa08c350f62ab61b',
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email','user_photos']; // Optional permissions
$loginUrl = $helper->getLoginUrl('http://myfbtest.com/facebook/fb-callback.php', $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
