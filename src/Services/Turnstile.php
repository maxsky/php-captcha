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

class Turnstile extends AbstractCaptchaService {

    /**
     * @return array
     * @throws CaptchaRequestException
     */
    public function verify(): array {
        return $this->request(CAPTCHA_VERIFY_API_TURNSTILE, [
            'form_params' => array_merge($this->params, [
                'response' => $this->response,
                'remoteip' => $this->clientIp
            ])
        ]);
    }
}
