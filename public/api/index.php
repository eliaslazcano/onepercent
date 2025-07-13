<?php

use Eliaslazcano\Helpers\HttpHelper;

require_once __DIR__ . '/vendor/autoload.php';

date_default_timezone_set("America/New_York");

HttpHelper::setAllowCredentials(true);
HttpHelper::setAllowOrigin($_SERVER['HTTP_ORIGIN'] ?? '*');
HttpHelper::useRouter(__DIR__.'/public', __DIR__ . '/logs/phperrors.log');
