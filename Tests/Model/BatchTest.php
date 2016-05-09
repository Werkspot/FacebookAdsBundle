<?php
namespace Werkspot\FacebookAdsBundle\Tests\Model\Batch;

use PHPUnit_Framework_TestCase;
use Werkspot\FacebookAdsBundle\Model\Batch;
use Werkspot\FacebookAdsBundle\Model\Batch\Request;

class BatchTest extends PHPUnit_Framework_TestCase
{
    public function testAddRequest()
    {
        $batch = $this->getNewEmptyBatch();

        $request = new Request();
        $request->setRelativeUrl('12345/insights');

        $batch->addRequests($request);
        $this->assertEquals([$request->getArray()], $batch->getArray());
    }

    public function testAddMultipleRequest()
    {
        $batch = $this->getNewEmptyBatch();

        $request_1 = new Request();
        $request_1->setRelativeUrl('12345/insights');
        $batch->addRequests($request_1);

        $request_2 = new Request();
        $request_2->setRelativeUrl('67890/insights');
        $batch->addRequests($request_2);

        $this->assertEquals(
            [
                $request_1->getArray(),
                $request_2->getArray()
            ],
            $batch->getArray());
    }

    /**
     * @return Batch
     */
    private function getNewEmptyBatch()
    {
        $batch = new Batch();
//        var_dump($batch->getArray()); exit;
        $this->assertEquals([], $batch->getArray());
        return $batch;
    }
}
