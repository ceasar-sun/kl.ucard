<?php

require_once('../../config.php');
require_once('lib/lightopenid/openid.php');
$context = context_system::instance();
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/auth/openid/index.php', $params);

$city = optional_param('city', '', PARAM_TEXT);
$message = '';

global $CFG;

if (!empty($city)) {
    $server = '';
    switch ($city) {
        case 'kl':
        case 'hcc':
        case 'hc':
        case 'mlc':
        case 'cy':
        case 'kh':
        case 'ptc':
        case 'hlc':
        case 'ilc':
        case 'phc':
        case 'km':
        case 'matsu':
            $server = 'http://openid.'.$city.'.edu.tw';
            break;
        case 'ntpc':
        case 'tyc':
        case 'tc':
        case 'chc':
        case 'ntct':
        case 'ylc':
            $server = 'https://openid.'.$city.'.edu.tw';
            break;
        case 'tp':
            $server = 'https://sso.'.$city.'.edu.tw/home/op.action';
            break;
        case 'cyc':
            $server = 'http://openid.'.$city.'cc.tw';
            break;
        case 'ttct':
            $server = 'http://openid.boe.'.$city.'.edu.tw';
            break;
        case 'tn':
            $server = 'https://openid.'.$city.'.edu.tw/op';
            break;
    }

    if (!empty($server)) {
        try {
            $host = $CFG->wwwroot;
            $openid = new LightOpenID($host);
            $openid->identity = $server;
            $openid->required = array(
                'namePerson/friendly',
                'contact/email',
                'namePerson',
                'birthDate',
                'person/gender',
                'contact/postalCode/home',
                'contact/country/home',
                'pref/language',
                'pref/timezone',
            );
            $openid->returnUrl = $CFG->wwwroot . '/login/index.php';

            header('Location: ' . $openid->authUrl());
            exit;
        } catch(ErrorException $e) {
            $message = get_string('auth_openid_error', 'auth_openid');
        }
    }
}

$site = get_site();

$loginsite = get_string("loginsite");
/// Print the header
$PAGE->navbar->add($loginsite);
$PAGE->set_title("$site->fullname: $loginsite");
$PAGE->set_heading("$site->fullname");
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('auth_openid_tip', 'auth_openid'));
echo html_writer::start_tag('div', array('class' => 'loginerrors'));
echo $OUTPUT->error_text($message);
echo html_writer::end_tag('div');
echo '<br/>';

$cities = array(
    '02' => 'kl',
    '03' => 'ntpc',
    '01' => 'tp',
    '04' => 'tyc',
    '08' => 'hcc',
    '07' => 'hc',
    '09' => 'mlc',
    '10' => 'tc',
    '11' => 'chc',
    '12' => 'ntct',
    '14' => 'ylc',
    '15' => 'cyc',
    '13' => 'cy',
    '16' => 'tn',
    '17' => 'kh',
    '18' => 'ptc',
    '20' => 'ttct',
    '21' => 'hlc',
    '22' => 'ilc',
    '19' => 'phc',
    '05' => 'km',
    '06' => 'matsu',
);

$lis = array();
foreach ($cities as $key => $string) {
    $city = html_writer::start_tag('li', array('style' => 'float: left;'));
    $city .= html_writer::start_tag('a', array('href' => new moodle_url('', array('city' => $string))));
    $city .= html_writer::tag('div', html_writer::empty_tag('img', array('src' => 'pix/edu_img/'.$key.'.png')), array('class' => 'icon column'));
    $city .= html_writer::end_tag('a');
    $city .= html_writer::end_tag('li');
    $lis[] = $city;
}
echo html_writer::tag('ul', implode("\n", $lis), array('class' => 'unlist'));

echo $OUTPUT->footer();
