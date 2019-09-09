<?php
declare(strict_types=1);

class CurlAdapter
{
    /**
     * @var string
     */
    private $responseCode;

    /**
     * @var bool|string
     */
    private $responseBody;

    /**
     * @var array
     */
    private $options;

    /**
     * @var resource
     */
    private $curlHandler;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    private function setOpt($optionKey, $optionValue)
    {
        curl_setopt($this->curlHandler, $optionKey, $optionValue);
    }

    public function fireRequest($url, $method = 'get', $data = [])
    {
        $this->curlHandler = curl_init();

        if (strtolower($method) === 'post') {
            curl_setopt($this->curlHandler, CURLOPT_POST, 1);
            curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $data);
        } else {
            $url .= '?' . http_build_query($data);
        }

        $this->setOpt(CURLOPT_URL, $url);
        //$this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);

        foreach($this->options as $key => $value){
            $this->setOpt($key, $value);
        }

        $responseBody = curl_exec($this->curlHandler);
        if(is_string($responseBody)){
            $this->setResponseBody($responseBody);
        }

        $this->setResponseCode(curl_getinfo($this->curlHandler, CURLINFO_RESPONSE_CODE));

        if (is_resource($this->curlHandler)) {
            curl_close($this->curlHandler);
        }
        return curl_error($this->curlHandler) ?: $this->responseBody;
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param string $responseCode
     */
    private function setResponseCode($responseCode): void
    {
        $this->responseCode = $responseCode;
    }

    /**
     * @return bool|string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @param bool|string $responseBody
     */
    private function setResponseBody($responseBody): void
    {
        $this->responseBody = $responseBody;
    }
}