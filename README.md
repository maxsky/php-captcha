# php-captcha

Support Google reCaptcha, Cloudflare Turnstile, hCaptcha, Vaptcha validate.

## Install

```bash
composer require maxsky/php-captcha
```

## Simple usage

### Google Recaptcha

```php
$token = 'Recaptcha_Response_Value_From_Request'; // g-recaptcha-response
// $clientIp = 'Request_Client_IP'; // optional

$params = [
    'secret' => 'Your_Recaptcha_Verify_Key',
];

try {
    $result = CaptchaService::init($params)->recaptcha($token)->verify();
    // $result = CaptchaService::init($params)->recaptcha($token, $clientIp)->verify();
} catch (\Exception $e) {
    // Request or Response error
    // \MaxSky\Captcha\Exceptions\CaptchaRequestException
    // \MaxSky\Captcha\Exceptions\CaptchaResponseException
}

var_dump($result);
```

Value example:

```php
array (size=4)
  'success' => boolean true
  'challenge_ts' => string '2025-02-17T08:57:28Z' (length=20)
  'hostname' => string '127.0.0.1' (length=9)
  'score' => float 0.9
```

### Cloudflare Turnstile

```php
$token = 'Turnstile_Response_Value_From_Request'; // cf-turnstile-response
$clientIpOptional = 'Request_Client_IP'; // optional

$params = [
    'secret' => 'Your_Turnstile_Verify_Key'
];

$result = CaptchaService::init($params)->turnstile($token, $clientIpOptional)->verify();

var_dump($result);
```

Value example:

```php
array (size=7)
  'success' => boolean true
  'error-codes' => 
    array (size=0)
      empty
  'challenge_ts' => string '2025-02-17T08:59:11.241Z' (length=24)
  'hostname' => string '127.0.0.1' (length=9)
  'action' => string '' (length=0)
  'cdata' => string '' (length=0)
  'metadata' => 
    array (size=1)
      'interactive' => boolean false
```

### Hcaptcha

```php
// $params & $token same as recaptcha & turnstile
$result = CaptchaService::init($params)->hcaptcha($token, $clientIpOptional)->verify();

var_dump($result);
```

Value example:

```php
array (size=3)
  'success' => boolean true
  'challenge_ts' => string '2025-02-17T09:03:43.000000Z' (length=27)
  'hostname' => string '127.0.0.1' (length=9)
```

### Vaptcha

```php
$token = 'Vaptcha_Response_Token_From_Request'; // vaptcha_token
$server = 'Vaptcha_Response_Server_From_Request'; // vaptcha_server
$clientIp = 'Request_Client_IP'; // Required

$params = [
    'id' => 'Your_Vaptcha_VID',
    'secretkey' => 'Your_Vaptcha_Key',
    'scene' => 0, // scene ID, default 0
    'server' => $server
];

$result = CaptchaService::init($params)->vaptcha($token, $clientIp)->verify();

var_dump($result);
```

Value example:

```php
array (size=3)
  'msg' => string 'success' (length=7)
  'success' => int 1
  'score' => int 90
```

## Compatible usage

**Example by Laravel Framework**

### 1. Create config file

`config/captcha.php` file

```php
<?php

return [
    'enable' => (bool)env('ADMIN_ENABLE_LOGIN_CAPTCHA', false),
    'login' => [
        'type' => env('ADMIN_CAPTCHA_TYPE'),
    ],
    'provider' => [
        'recaptcha' => [
            'key' => env('GOOGLE_RECAPTCHA_CLIENT_KEY'),
            'secret' => env('GOOGLE_RECAPTCHA_SERVER_SECRET'),
            'threshold' => (float)env('GOOGLE_RECAPTCHA_VERIFY_THRESHOLD', 0.5)
        ],
        'turnstile' => [
            'key' => env('CLOUDFLARE_TURNSTILE_CLIENT_KEY'),
            'secret' => env('CLOUDFLARE_TURNSTILE_SERVER_SECRET'),
        ],
        'hcaptcha' => [
            'key' => env('HCAPTCHA_CLIENT_KEY'),
            'secret' => env('HCAPTCHA_SERVER_SECRET'),
        ],
        'vaptcha' => [
            'key' => env('VAPTCHA_VID'),
            'secret' => env('VAPTCHA_KEY'),
            'scene' => (int)env('VAPTCHA_SCENE', 0),
            'score' => (int)env('VAPTCHA_VERIFY_SCORE', 80)
        ]
    ]
];
```

### 2. Create environment config

`.env` file

```ini
ADMIN_ENABLE_LOGIN_CAPTCHA=true

# Values: recaptcha | turnstile | hcaptcha | vaptcha
ADMIN_CAPTCHA_TYPE=recaptcha

GOOGLE_RECAPTCHA_CLIENT_KEY=
GOOGLE_RECAPTCHA_SERVER_SECRET=
GOOGLE_RECAPTCHA_VERIFY_THRESHOLD=0.5

CLOUDFLARE_TURNSTILE_CLIENT_KEY=
CLOUDFLARE_TURNSTILE_SERVER_SECRET=

HCAPTCHA_CLIENT_KEY=
HCAPTCHA_SERVER_SECRET=

VAPTCHA_VID=
VAPTCHA_KEY=
VAPTCHA_SCENE=0
```

### 3. Controller verify captcha

```php
class AuthController {

    // define a response fields map constant
    private const CAPTCHA_RESPONSE_NAME = [
        'recaptcha' => 'g-recaptcha-response',
        'turnstile' => 'cf-turnstile-response',
        // if your js link not set recaptchacompat param off like under example,
        // hCaptcha will be use 'g-recaptcha-response' as param name same as Google reCaptcha.
        // https://js.hcaptcha.com/1/api.js?hl=zh-CN&recaptchacompat=off
        'hcaptcha' => 'h-captcha-response',
        'vaptcha' => 'vaptcha_token'
    ];
    
    public function login(Request $request) {
        if (config('captcha.enable')) {
            $captchaType = config('captcha.login.type');

            if ($captchaType) {
                $token = (string)$request->post(self::CAPTCHA_RESPONSE_NAME[$captchaType]);

                if (!$token) {
                    // response no challenge data error
                }

                $secret = config("captcha.provider.$captchaType.secret");

                if ($captchaType === 'vaptcha') {
                    $server = (string)$request->post('vaptcha_server');

                    if (!$server) {
                        // response no challenge data error
                    }

                    $params = [
                        'id' => config("captcha.provider.$captchaType.key"),
                        'secretkey' => $secret,
                        'scene' => config('captcha.provider.vaptcha.scene'),
                        'server' => $server
                    ];
                } else {
                    $params = [
                        'secret' => $secret
                    ];
                }

                try {
                    $result = CaptchaService::init($params)->{$captchaType}($token, $request->ip())->verify();

                    if (!$result || !$result['success']) {
                        Log::warning("$captchaType verify failed.", [
                            'UA' => $request->userAgent(),
                            'IP' => $result->ip()
                        ]);

                        Log::warning("$captchaType verify result.", $result);

                        // response challenge failed error
                    }
                } catch (CaptchaRequestException|CaptchaResponseException $e) {
                    // response verify request or response error
                }
            } else {
                // not supported type
            }
        }
        
        // TODO: do account login verify
    }
}
```
