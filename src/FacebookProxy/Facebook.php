<?php
namespace Werkspot\FacebookAdsBundle\FacebookProxy;

use Facebook\Facebook as RealFacebook;
use Facebook\HttpClients\HttpClientsFactory;

class Facebook extends RealFacebook implements FacebookInterface
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $config = array_merge([
            'app_id' => getenv(static::APP_ID_ENV_NAME),
            'app_secret' => getenv(static::APP_SECRET_ENV_NAME),
            'enable_beta_mode' => false,
            'http_client_handler' => null,
        ], $config);

        $this->app = new FacebookApp($config['app_id'], $config['app_secret']);
        $this->client = new FacebookClient(
            HttpClientsFactory::createHttpClient($config['http_client_handler']),
            $config['enable_beta_mode']
        );
    }
}
