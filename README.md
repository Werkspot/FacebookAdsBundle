FacebookAdsBundle
===============
The FacebookAdsBundle provides a simple integration of the [Facebook Ads API][facebookadsapi] for your Symfony project.

Checkout the Facebook Ads full [documentation][facebookDocumentation]


**Warning: Currently in development**

[![Build Status](https://travis-ci.org/Werkspot/FacebookAdsBundle.svg?branch=master)](https://travis-ci.org/Werkspot/FacebookAdsBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Werkspot/FacebookAdsBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Werkspot/FacebookAdsBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Werkspot/FacebookAdsBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Werkspot/FacebookAdsBundle/?branch=master)

**TODO**
- Create more Reports
- Clean up code


Installation
------------
With [composer](http://packagist.org), add:

```json
{
    "require": {
        "werkspot/facebook-ads-api-bundle": "dev-master"
    }
}
```

Then enable it in your kernel:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        //...
        new Werkspot\FacebookAdsBundle\WerkspotFacebookAdsBundle(),
        //...
```
Configuration
-------------
```yaml
# app/config/config.yml

# Facebook Ads API
werkspot_facebook_ads:
  app_id: "%facebook_ads.api_id%" #<-- Keep them save! (in parameters.yml)
  app_secret: "%facebook_ads.api_secres%" #<-- Keep them save! (in parameters.yml)
  system_user: "%facebook_ads.system_user%" [optional] #<-- Keep them save! (in parameters.yml)
```

Usage
-----

The bundle registers the `werkspot.facebook_ads.client` service witch allows you to call the api;

### Get AD Set from account

```php
use FacebookAds\Object\AdAccount;

$facebookAdsApi = $this->get('werkspot.facebook_ads.client');
$account =  new AdAccount("act_<You're AccountId>");
$cursor = $facebookAdsApi->getAdSetFromAccount($account);
$adSetArray = $cursor->getArrayCopy(); 
```

### Get Insights from AD Set

```php
use FacebookAds\Object\AdAccount;

$facebookAdsApi = $this->get('werkspot.facebook_ads.client');
$campaign = new Campaign("<You're AdSetId>"");

$params = new Param();
$params->setDatePreset(DatePreset::get(DatePreset::YESTERDAY));
$params->setTimeRange(new Params\TimeRange(new DateTime('-2day')));
$params->addAllFields();

$cursor = $facebookAdsApi->getInsights($campaign, $params);

```

Credits
-------

FacebookAdsBundle is based on the officical [facebook Ads API][facebookadsapi].
FacebookAdsBundle has been developed by [LauLaman][LauLaman].

[facebookadsapi]: https://packagist.org/packages/facebook/php-ads-sdk
[facebookDocumentation]: https://developers.facebook.com/docs/marketing-apis
[LauLaman]: https://github.com/LauLaman
