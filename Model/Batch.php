<?php
namespace Werkspot\FacebookAdsBundle\Model;

use Werkspot\FacebookAdsBundle\Model\Batch\Request;

class Batch
{

    /**
     * @var Request[]
     */
    private $requests=[];

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

        return ['batch' => json_encode($result)];
    }
}
