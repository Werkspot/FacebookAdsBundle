<?php
namespace Werkspot\FacebookAdsBundle\Tests\Model\Batch\Request;

use PHPUnit_Framework_TestCase;
use Werkspot\FacebookAdsBundle\Model\AdSet\Params;
use Werkspot\FacebookAdsBundle\Model\Batch\Enum\Method;
use Werkspot\FacebookAdsBundle\Model\Batch\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getMethodEnumData
     *
     * @params Method $method
     */
    public function testMethod(Method $method)
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
        $url = 'test';
        $request = $this->getNewEmptyRequest();

        $request->setRelativeUrl($url);
        $this->assertEquals(
            ['method' => 'GET', 'relative_url' => $url],
            $request->getArray()
        );

        $request->setRelativeUrl($url, new Params());
        $this->assertEquals(
            ['method' => 'GET', 'relative_url' => $url . '?fields='],
            $request->getArray()
        );
    }

    public function testBody()
    {
        $body = 'I can be bet in an POST or PUT request';
        $request = $this->getNewEmptyRequest();

        $request->setMethod(Method::get(Method::METHOD_POST));
        $request->setBody($body);
        $this->assertEquals(
            ['method' => 'POST', 'body' => $body, 'relative_url' => null],
            $request->getArray()
        );

        $request = $this->getNewEmptyRequest();
        $request->setMethod(Method::get(Method::METHOD_PUT));
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
        $request->setBody('this should not be posible');
    }

    /**
     * @expectedException \Exception
     */
    public function testBody_CantSetMethodDELETE()
    {
        $request = $this->getNewEmptyRequest();
        $request->setBody('this should not be posible');
    }

    /**
     * @return array
     */
    public function getMethodEnumData()
    {
        $result = [];
        $fields = Method::getValidOptions();
        foreach ($fields as $key => $field) {
            $result[$field] = [Method::get($field)];
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
            ['method' => Method::METHOD_GET, 'relative_url' => null],
            $request->getArray()
        );

        return $request;
    }
}
