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
$UCARD_CFG->dbuser    = 'ucarduser';
$UCARD_CFG->dbpass    = 'ucardpasswd@mysql';

$UCARD_CFG->token = '';
$UCARD_CFG->server = 'http://learning.kl.edu.tw/';
$UCARD_CFG->dir = '/moodle.test';

