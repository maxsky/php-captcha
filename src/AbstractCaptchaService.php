<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2025 Feb 14
 * Time: 16:32
 */

namespace MaxSky\Captcha;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use MaxSky\Captcha\Exceptions\CaptchaRequestException;

abstract class AbstractCaptchaService {

    private $httpClient;
    private $options;

    protected $params;
    protected $response;
    protected $clientIp;

    /**
     * @param array       $params
     * @param string      $response
     * @param string|null $client_ip
     * @param array       $options
     */
    public function __construct(array $params, string $response, ?string $client_ip = null, array $options = []) {
        $this->httpClient = new Client();

        $this->params = $params;
        $this->response = $response;
        $this->clientIp = $client_ip;
        $this->options = $options;
    }

    /**
     * @return array
     * @throws CaptchaRequestException
     */
    abstract public function verify(): array;

    /**
     * @param string $url
     * @param array  $data
     *
     * @return array
     * @throws CaptchaRequestException
     */
    protected function request(string $url, array $data): array {
        $data = array_merge($this->options, $data);

        try {
            $response = $this->httpClient->post($url, $data)->getBody();
        } catch (GuzzleException $e) {
            throw new CaptchaRequestException($e->getMessage(), $e->getCode(), $e);
        }

        return json_decode($response, true);
    }
}
