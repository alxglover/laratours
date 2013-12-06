<?php

require_once dirname(__FILE__) . '/lib.php';

$view = preg_replace('/\.php$/', '', basename(__FILE__));

renderView($view);
