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

class Recaptcha extends AbstractCaptchaService {

    /**
     * @return array
     * @throws CaptchaRequestException
     * @throws CaptchaResponseException
     */
    public function verify(): array {
        return $this->request(CAPTCHA_VERIFY_API_RECAPTCHA, [
            'form_params' => array_merge($this->params, [
                'response' => $this->response,
                'remoteip' => $this->clientIp
            ])
        ]);
    }
}
