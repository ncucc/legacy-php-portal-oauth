# 傳統 PHP 程式如何介接中央大學 Portal

這個範例是提供傳統（非框架式）的 PHP 系統介接 Portal 的方式。

！請注意，在做導入前，請先備份好原本 Workable 的系統。可以考慮使用 git 做版本，更方便。

## 下載必要套件

用 composer 下載必要套件，如果沒有 composer，先下載 composer。
不管原本系統是否有導入 composer，先導入。
```bash
composer require league/oauth2-client
```

如果系統本身沒有用 composer 的程式，在 PortalProvider 的前面加。
其中，路徑可以做適當的調整。
```php
require_once __DIR__ . '/vendor/autoload.php'; 
```

## Config file
在你的系統中，找一個適當的地方放設定檔。

```php
<?php
$oauth_settings = [
    'clientId' => '請自填',
    'clientSecret' => '請自填',
    'redirectUri' => '請自填',
    'urlAuthorize' => 'https://portal.ncu.edu.tw/oauth2/authorization',
    'urlAccessToken' => 'https://portal.ncu.edu.tw/oauth2/token',
    'urlResourceOwnerDetails' => 'https://portal.ncu.edu.tw/apis/oauth/v1/info',
    'scopes' => ['identifier'], # 可適需要調整
    'scopeSeparator' => ' '
];
```

## PortalProvider
找一個適合的地方放 [PortalProvider.php](PortalProvider.php) 

## Login (Redirect to portal)
找適當的 path 放登入 Portal 的程式
```php
<?php
include_once('sample-config.php');

$provider = new PortalProvider($oauth_settings);
$authorizationUrl = $provider->getAuthorizationUrl();
$_SESSION['oauth2state'] = $provider->getState();

header('Location: ' . $authorizationUrl);
exit(0);
 
```

## Callback function
找適當的 path 放 callback 程式
```php
<?php
include_once('sample-config.php');

$provider = new PortalProvider($oauth_settings);

if (!isset($_GET['code'])) {
    if (isset ($_GET['error']) and $_GET['error'] == 'access_denied') {
        $err = 'canceled';
        // 使用者取消登入
    } else {
        $err = 'incorrect parameter';
        // 基本上，就是沒有照協定進行的，勿略，直接導回首頁
    }
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    // 取消
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
            // 到此，已經登入成功，請存 session 並轉址到適當的地方
        } else {
            $err = "User needs to authorized his/her identifier to proceed.";
            // 使用者如果沒有授權帳號資訊時，登入不成功，可以告訴使用者，請他一定要授權
        }
    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        $err = $e->getMessage();
        // 基本上，是例外的發生，可以忽略或考慮 log 下來
    }
}
```