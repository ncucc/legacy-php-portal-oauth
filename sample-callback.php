<?php
include_once('sample-config.php');

$provider = new PortalProvider($oauth_settings);

if (!isset($_GET['code'])) {
    if (isset ($_GET['error']) and $_GET['error'] == 'access_denied') {
        $err = 'canceled';
    } else {
        $err = 'incorrect parameter';
    }
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
} else {
    try {
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        $resourceOwner = $provider->getResourceOwner($accessToken);
        $result = $resourceOwner->toArray();

        if (array_key_exists('identifier', $result)) {
            $loggedInUser = $result['identifier'];

            echo $loggedInUser . "Login successfully";
            // set session and redirect to the proper location
        } else {
            $err = "User needs to authorized his/her identifier to proceed.";
        }
    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        $err = $e->getMessage();
    }
}

