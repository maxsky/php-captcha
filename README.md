# php-captcha（PHP 人机验证库）

## Install

```bash
composer require maxsky/php-captcha
```

## Usage

### Simple

#### Google Recaptcha

```php
$params = [
    'secret' => 'Your_Recaptcha_Server_Key',
];

$token = 'Recaptcha_Response_Token'; // g-recaptcha-response
$clientIp = 'Client_Request_IP_Address';

$result = CaptchaService::init($params)->recaptcha($token, $clientIp)->verify();

var_dump($result);
```

#### Cloudflare Turnstile
