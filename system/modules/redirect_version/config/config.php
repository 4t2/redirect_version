<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][] = array('RedirectVersion', 'getPageIdFromUrl');

?>