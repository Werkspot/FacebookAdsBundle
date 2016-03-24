<?php
namespace Werkspot\FacebookAdsBundle\Model\Batch;

use Werkspot\FacebookAdsBundle\Model\Batch\Enum\HttpMethod;
use Werkspot\FacebookAdsBundle\Model\ParamsInterface;

class Request
{
    /**
     * @var HttpMethod
     */
    private $method;

    /**
     * @var string
     */
    private $relativeUrl;

    /**
     * @var string
     */
    private $body;

    public function __construct()
    {
        $this->method = HttpMethod::get(HttpMethod::METHOD_GET);
    }

    /**
     * @param HttpMethod $method
     */
    public function setMethod(HttpMethod $method)
    {
        $this->method = $method;
    }

    /**
     * @param string $relativeUrl
     * @param ParamsInterface $params
     */
    public function setRelativeUrl($relativeUrl, ParamsInterface $params = null)
    {
        if ($params) {
            $this->relativeUrl = $relativeUrl . $params->getBatchQuery();
        } else {
            $this->relativeUrl = $relativeUrl;
        }
    }

    /**
     * @param string $body
     *
     * @throws \Exception
     */
    public function setBody($body)
    {
        if ($this->method == HttpMethod::get(HttpMethod::METHOD_GET) || $this->method == HttpMethod::get(HttpMethod::METHOD_DELETE)) {
            throw new \Exception('Body is only allowed for POST & PUT method');
        }
        $this->body = $body;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        $result['method'] = $this->method->getValue();
        $result['relative_url'] = $this->relativeUrl;
        if ($this->body !== null) {
            $result['body'] = $this->body;
        }

        return $result;
    }

}
