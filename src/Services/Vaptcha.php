<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2025 Feb 14
 * Time: 16:39
 */

namespace MaxSky\Captcha\Services;

use MaxSky\Captcha\AbstractCaptchaService;
use MaxSky\Captcha\Exceptions\CaptchaRequestException;
use MaxSky\Captcha\Exceptions\CaptchaResponseException;

class Vaptcha extends AbstractCaptchaService {

    /**
     * @return array
     * @throws CaptchaRequestException
     * @throws CaptchaResponseException
     */
    public function verify(): array {
        $server = $this->params['server'];
        unset($this->params['server']);

        return $this->request($server, [
            'json' => array_merge($this->params, [
                'token' => $this->response,
                'ip' => $this->clientIp
            ])
        ]);
    }
}
