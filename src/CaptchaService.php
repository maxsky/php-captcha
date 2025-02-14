<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2025 Feb 14
 * Time: 16:37
 */

namespace MaxSky\Captcha;

use MaxSky\Captcha\Services\HCaptcha;
use MaxSky\Captcha\Services\Recaptcha;
use MaxSky\Captcha\Services\Turnstile;
use MaxSky\Captcha\Services\Vaptcha;

class CaptchaService {

    private static $instance = null;
    private $params = [];
    private $requestOptions = [];

    public function __construct(array $params, array $options = []) {
        $this->params = $params;
        $this->requestOptions = $options;
    }

    /**
     * @param array $params
     * @param array $options
     *
     * @return CaptchaService
     */
    public static function init(array $params, array $options = []): CaptchaService {
        if (!self::$instance) {
            self::$instance = new self($params, $options);
        }

        return self::$instance;
    }

    /**
     * @param string      $response
     * @param string|null $client_ip
     *
     * @return AbstractCaptchaService
     */
    public function hCaptcha(string $response, ?string $client_ip = null): AbstractCaptchaService {
        return new HCaptcha($this->params, $response, $client_ip, $this->requestOptions);
    }

    /**
     * @param string      $response
     * @param string|null $client_ip
     *
     * @return AbstractCaptchaService
     */
    public function recaptcha(string $response, ?string $client_ip = null): AbstractCaptchaService {
        return new Recaptcha($this->params, $response, $client_ip, $this->requestOptions);
    }

    /**
     * @param string      $response
     * @param string|null $client_ip
     *
     * @return AbstractCaptchaService
     */
    public function turnstile(string $response, ?string $client_ip = null): AbstractCaptchaService {
        return new Turnstile($this->params, $response, $client_ip, $this->requestOptions);
    }

    /**
     * @param string $server
     * @param string $response
     * @param string $client_ip
     *
     * @return AbstractCaptchaService
     */
    public function vaptcha(string $server, string $response, string $client_ip): AbstractCaptchaService {
        return new Vaptcha(
            array_merge($this->params, ['server' => $server]), $response, $client_ip, $this->requestOptions
        );
    }
}
