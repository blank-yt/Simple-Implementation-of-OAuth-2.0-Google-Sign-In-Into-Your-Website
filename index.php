<?php
session_start();
require_once("./login/vendor/autoload.php");
$redirect_uri = 'https://oauth2-google-blank.herokuapp.com/'; /* REPLACE WITH YOUR WEBSITE */

$client = new Google_Client();
$client->setAuthConfig(''); /* REPLACE WITH YOUR CREDENTIALS.json FILE NAME FROM GOOGLE */
$client->setRedirectUri($redirect_uri);
$client->addScope("email");
$client->addScope("profile");

$service = new Google_Service_Oauth2($client);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);
    $_SESSION['upload_token'] = $token;

    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

if (!empty($_SESSION['upload_token'])) {
    $client->setAccessToken($_SESSION['upload_token']);
    if ($client->isAccessTokenExpired()) {
        unset($_SESSION['upload_token']);
    }
} else {
    $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
    $userProfile = $service->userinfo->get();
    session_start();

    if (!empty($userProfile)) {
        $output = '<h1>Profile</h1>';
        $output .= '<img src="' . $userProfile['picture'] . '">';
        $output .= '<br/>Google ID: ' . $userProfile['id'];
        $output .= '<br/>Name: ' . $userProfile['given_name'] . ' ' . $userProfile['family_name'];
        $output .= '<br/>Email: ' . $userProfile['email'];
        $output .= '<br/>Locale: ' . $userProfile['locale'];
        $output .= '<br/><br/><a href="logout.php">Logout</a>';
    } else {
        $output = '<h3 style="color: red">Some problem occurred, please try again.</h3>';
    }
} else {
    $authUrl = $client->createAuthUrl();
    $output = '<a href="' . filter_var($authUrl, FILTER_SANITIZE_URL) . '"><img src="images/glogin.png" alt="login"/></a>';
}
?>

<div>
    <?php echo $output; ?>
</div>