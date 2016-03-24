<?php
namespace Werkspot\FacebookAdsBundle\Model\Batch \Enum;

use Werkspot\ApiLibrary\Enum\AbstractEnum;

class Method extends AbstractEnum
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
}
