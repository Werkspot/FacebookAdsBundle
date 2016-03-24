<?php
namespace Werkspot\FacebookAdsBundle\Tests\Model\Batch;

use PHPUnit_Framework_TestCase;
use Werkspot\FacebookAdsBundle\Model\Batch;
use Werkspot\FacebookAdsBundle\Model\Batch\Request;

class BatchTest extends PHPUnit_Framework_TestCase
{
    public function testAddRequest()
    {
        $batch = new Batch();
        $this->assertEquals(['batch' => "[]"], $batch->getArray());

        $request = new Request();
        $request->setRelativeUrl('12345/insights');

        $batch->addRequests($request);
        $this->assertEquals(['batch' => json_encode([$request->getArray()])], $batch->getArray());
    }
}
