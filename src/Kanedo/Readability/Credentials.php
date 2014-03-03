<?php
namespace Kanedo\Readability;
class Credentials implements \OAuth\Common\Consumer\CredentialsInterface {
    /**
     * @var string
     */
    protected $consumerId;

    /**
     * @var string
     */
    protected $consumerSecret;

    /**
     * @var string
     */
    protected $callbackUrl;

    /**
     * @param string $consumerId
     * @param string $consumerSecret
     * @param string $callbackUrl
     */
    public function __construct($consumerId, $consumerSecret, $callbackUrl)
    {
        $this->consumerId = $consumerId;
        $this->consumerSecret = $consumerSecret;
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * @return string
     */
    public function getConsumerId()
    {
        return $this->consumerId;
    }

    /**
     * @return string
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }
}