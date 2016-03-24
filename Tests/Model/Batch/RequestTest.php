<?php
namespace Werkspot\FacebookAdsBundle\Tests\Model\Batch\Request;

use PHPUnit_Framework_TestCase;
use Werkspot\FacebookAdsBundle\Model\AdSet\Params;
use Werkspot\FacebookAdsBundle\Model\Batch\Enum\HttpMethod;
use Werkspot\FacebookAdsBundle\Model\Batch\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getMethodEnumData
     *
     * @params Method $method
     */
    public function testMethod(HttpMethod $method)
    {
        $request = $this->getNewEmptyRequest();

        $request->setMethod($method);
        $this->assertEquals(
            ['method' => $method->getValue(), 'relative_url' => null],
            $request->getArray()
        );
    }

    public function testRelativeUrl()
    {
        $relativeUrl = '/example/';
        $request = $this->getNewEmptyRequest();

        $request->setRelativeUrl($relativeUrl);
        $this->assertEquals(
            ['method' => 'GET', 'relative_url' => $relativeUrl],
            $request->getArray()
        );

        $request->setRelativeUrl($relativeUrl, new Params());
        $this->assertEquals(
            ['method' => 'GET', 'relative_url' => $relativeUrl . '?fields='],
            $request->getArray()
        );
    }

    public function testBody()
    {
        $body = 'I can be bet in an POST or PUT request';
        $request = $this->getNewEmptyRequest();

        $request->setMethod(HttpMethod::get(HttpMethod::METHOD_POST));
        $request->setBody($body);
        $this->assertEquals(
            ['method' => 'POST', 'body' => $body, 'relative_url' => null],
            $request->getArray()
        );

        $request = $this->getNewEmptyRequest();
        $request->setMethod(HttpMethod::get(HttpMethod::METHOD_PUT));
        $request->setBody($body);
        $this->assertEquals(
            ['method' => 'PUT', 'body' => $body, 'relative_url' => null],
            $request->getArray()
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testBody_CantSetMethodGET()
    {
        $request = $this->getNewEmptyRequest();
        $request->setBody('this should not be possible');
    }

    /**
     * @expectedException \Exception
     */
    public function testBody_CantSetMethodDELETE()
    {
        $request = $this->getNewEmptyRequest();
        $request->setMethod(HttpMethod::get(HttpMethod::METHOD_DELETE));
        $request->setBody('this should not be possible');
    }

    /**
     * @return array
     */
    public function getMethodEnumData()
    {
        $result = [];
        $fields = HttpMethod::getValidOptions();
        foreach ($fields as $key => $field) {
            $result[$field] = [HttpMethod::get($field)];
        }

        return $result;
    }

    /**
     * @return Request
     */
    private function getNewEmptyRequest()
    {
        $request = new Request();
        $this->assertEquals(
            ['method' => HttpMethod::METHOD_GET, 'relative_url' => null],
            $request->getArray()
        );

        return $request;
    }
}
