<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
	if (init('action') == 'updateFaceDetection') {
		log::remove('FaceDetection_update');
		//$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
		$cmd = 'sudo apt-get update -y';
		$cmd .= ' >> ' . log::getPathToLog('FaceDetection_update') . ' 2>&1 &';
		exec($cmd);
		$cmd = 'sudo apt-get install -y php5-gd';
		$cmd .= ' >> ' . log::getPathToLog('FaceDetection_update') . ' 2>&1 &';
		exec($cmd);
		$cmd = 'sudo chmod 777 -R /bin/bash ' . dirname(__FILE__) . '/../../ressources/';
		$cmd .= ' >> ' . log::getPathToLog('FaceDetection_update') . ' 2>&1 &';
		exec($cmd);
		ajax::success();
	}
	if (init('action') == 'SearchCamera') {
		$EqLogic = eqLogic::byType('camera');
    /*    if (!is_object($EqLogic)) {
			// ajax::success(false);
        }*/
		$return=array();
		foreach($EqLogic as $Camera)
			$return[]=array('Nom'=>$Camera->getName(),'Id'=>$Camera->getID());
		ajax::success($return);
    }
	


    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
