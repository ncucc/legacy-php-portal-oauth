<?php
include_once('sample-config.php');

$provider = new PortalProvider($oauth_settings);
$authorizationUrl = $provider->getAuthorizationUrl();
$_SESSION['oauth2state'] = $provider->getState();

header('Location: ' . $authorizationUrl);
exit(0);
