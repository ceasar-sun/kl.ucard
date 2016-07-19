<?php

/**
 * @Func:	取用 ucard 資料庫的全域設定
 * @License:	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:	Ceasar Sun, Thomas Tsai 
 * @Note:
 *
*/

unset($UCARD_CFG);
global $UCARD_CFG;
$UCARD_CFG = new stdClass();

$UCARD_CFG->dbhost    = 'localhost';
$UCARD_CFG->dbname    = 'ucard.test';
$UCARD_CFG->dbuser    = 'root';
$UCARD_CFG->dbpass    = 'okok7480';

$UCARD_CFG->token = '851fc9fb3410e174ff156b65689f6922';
$UCARD_CFG->server = 'http://moodle.nchc.org.tw/';
$UCARD_CFG->dir = '/moodle';

