<?php

$DOMAIN_NAME = "http://" . $_SERVER['SERVER_NAME'] . "/";

define('BASE_URL', $DOMAIN_NAME);
//added by wangxi for Meilishuo OA System
define("OA_VIEW_SWITCH", 'ON');
define("TEMPLATE_PATH",  ROOT_PATH . '/views/');
