<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

header("Content-type: application/json; charset=utf-8");
$numFunc = new functions($dbo);
if(!$numFunc->getConfig('ADMIN')){ api::printError(999, ""); }
