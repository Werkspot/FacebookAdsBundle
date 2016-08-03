<?php
namespace Werkspot\FacebookAdsBundle\Model;

use Werkspot\FacebookAdsBundle\Model\Batch\Request;

class Batch
{
    const MAX_REQUESTS_PER_BATCH = 50;

    /**
     * @var Request[]
     */
    private $requests = [];

    /**
     * @param Request $requests
     */
    public function addRequests(Request $requests)
    {
        $this->requests[] = $requests;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        $result = [];
        foreach ($this->requests as $request) {
            $result[] = $request->getArray();
        }

        return $result;
    }
}
