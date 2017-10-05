<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
/*
function facedetection_install() {
	$cron = cron::byClassAndFunction('facedetection', 'FaceAnalyse');
	if (!is_object($cron)) {
		$cron = new cron();
		$cron->setClass('facedetection');
		$cron->setFunction('FaceAnalyse');
		$cron->setEnable(1);
		$cron->setDeamon(1);
		$cron->setSchedule('* * * * *');
		$cron->setTimeout('999999');
		$cron->save();
	}
}

function facedetection_update() {
    $cron = cron::byClassAndFunction('facedetection', 'FaceAnalyse');
    if (is_object($cron)) {
		$cron->stop();
        $cron->remove();
		$cron = new cron();
		$cron->setClass('facedetection');
		$cron->setFunction('FaceAnalyse');
		$cron->setEnable(1);
		$cron->setDeamon(1);
		$cron->setSchedule('* * * * *');
		$cron->setTimeout('999999');
		$cron->save();
	}
}

function facedetection_remove() {
    $cron = cron::byClassAndFunction('facedetection', 'FaceAnalyse');
    if (is_object($cron)) {
		$cron->stop();
        $cron->remove();
    }
}
*/
?>