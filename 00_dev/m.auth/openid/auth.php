<?php
/**
 * for Taiwan Edu. OpenID User
 * @package auth_openid
 * @author Eric Hsin
 * @license  
 * @bug fix:
 * 20160629 : Ceasar Sun
 * 	* use 'construct' to initial main class. Avoid to PHP error in verbose mode 
 */
 
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/authlib.php');
require_once('lib/lightopenid/openid.php');

class auth_plugin_openid extends auth_plugin_base {

    /**
     * Constructor: Fix via Ceasar 
     */

    public function __construct() {
    // function auth_plugin_openid() {
        $this->authtype = 'openid';
		$this->config = get_config('auth/openid');
		//user profile fields you want to update
		set_config('field_updatelocal_firstname', 'onlogin', 'auth/openid');
		set_config('field_updatelocal_lastname', 'onlogin', 'auth/openid');
		set_config('field_updatelocal_email', 'onlogin', 'auth/openid');
		// Add what field you want to receive by openID
		set_config('field_updatelocal_idnumber', 'onlogin', 'auth/openid');
    }

    /**
     * Old syntax of class constructor for backward compatibility.
     */
    public function auth_plugin_openid() {
        self::__construct();
    }

    function loginpage_hook() {
        global $frm, $SESSION, $CFG;
        $host = $CFG->wwwroot;
        $openid = new LightOpenID($host);
        if ($openid->mode) {
            $attributes = $openid->getAttributes();
	    // print all message into log 
	    error_log('user attri: ' . print_r($attributes, true));
            if ($openid->validate()) {
				//將「http://」去除
                $identity = rtrim(preg_replace("(^https?://)", "", $openid->identity), "/");
                $frm = new stdClass();
                $frm->username = $identity;
                $frm->password = md5($identity);
                $SESSION->auth_plugin_openid = $identity;    
                $CFG->nolastloggedin = true;
            }
        }
    }

    function get_userinfo($username) {
        global $CFG;
        $host = $CFG->wwwroot;
        $openid = new LightOpenID($host);
        if ($openid->mode) {
            $attributes = $openid->getAttributes();
            $openid->validate();
            $name = '';
            if (isset($attributes['namePerson'])) $name = $attributes['namePerson'];
            if (isset($attributes['ax_value_namePerson__1'])) $name = $attributes['ax_value_namePerson__1'];
			//取得名字
            if (isset($attributes['ext1_fullname'])) $name = $attributes['ext1_fullname'];

            $email = '';
            if (isset($attributes['contact/email'])) $email = $attributes['contact/email'];
            if (isset($attributes['ext1_email'])) $email = $attributes['ext1_email'];

	    // 取得 guid 放入 idnumber 欄位
	    // 因暫時無法取得 guid 資訊，用 birthday 測試用
            $idnumber = '';
            if (isset($attributes['ax_value_person_guid_1'])) {
		$idnumber = $attributes['ax_value_person_guid_1'];
	    }
	    else {
		$idnumber = 'no-guid';
	    }

            return array(
                'firstname' => mb_substr($name, 1, NULL, 'UTF-8'),
                'lastname' => mb_substr($name, 0, 1, 'UTF-8'),
                'email' => $email,
                'idnumber' => $idnumber,
            );
        }

        return false;
    }

	 //還沒弄清楚
    function user_login($username, $password) {
	    global $SESSION,$CFG;
		//要先偵測是採用OpenID驗證才執行
		$host = $CFG->wwwroot;
        $openid = new LightOpenID($host);
		if ($openid->mode) {
			$openid = $SESSION->auth_plugin_openid;
			return ($username === $openid) and ($password === md5($openid));
		}
		
		return false;
    }

    function prevent_local_passwords() {
        return true;
    }

    function is_internal() {
        return false;
    }

    function can_change_password() {
        return false;
    }

    function is_synchronised_with_external() {
        return true;
    }
	
	/**
     * Returns true if plugin can be manually set.
     * @return bool
     */
    function can_be_manually_set() {
        return true;
    }

	function config_form($config, $err, $user_fields) {
		global $OUTPUT;	
        include "config.html";
    }
	
	/**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        return true;
    }
	
	//在網站登入的首頁加上 OpenID登入按鈕
    function loginpage_idp_list($wantsurl) {
        global $CFG;
        $idps = array();
        $idps[] = array(
            'url'  => new moodle_url($CFG->wwwroot . '/auth/openid', array('city' => 'kl')),
            //'url'  => new moodle_url($CFG->wwwroot . '/auth/openid'),
			//'url'  => new moodle_url($CFG->wwwroot . '/auth/openid', array('city' => 'hcc')),
			// 若要特定縣市   'url'  => new moodle_url($CFG->wwwroot . '/auth/openid', array('city' => 'hcc')),
            'icon' => new pix_icon('open_id', get_string('auth_login_button', 'auth_openid'),'auth_openid'),
            'name' => ''
        );
        return $idps;
    }
}
