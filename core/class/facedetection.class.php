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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('core', 'FaceDetector', 'class', 'facedetection');

class facedetection extends eqLogic {
    /*     * *************************Attributs****************************** */

	

    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDayly() {

      }
     */



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
        
    }

    public function postSave() {
		self::AddCommande($this,'Face Detection','facedetection',"info", 'binary','');
		self::AddCommande($this,'Snapshot','snapshots',"action", 'other','');
		self::AddCommande($this,'Snapshot avec detection','snapshotfacedetect',"action", 'other','');
	//	self::AddCommande($this,'Repertoire Snapshot','snapshotdir',"action", 'other','<i class="fa fa-folder-open"></i>');    
    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
        
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*     * **********************Getteur Setteur*************************** */
	public static function AddCommande($eqLogic,$Name,$_logicalId,$Type="info", $SubType='binary',$icone) 
	{
		$Commande = $eqLogic->getCmd(null,$_logicalId);
		if (!is_object($Commande))
		{
			$Commande = new facedetectionCmd();
			$Commande->setId(null);
			$Commande->setName($Name);
			$Commande->setLogicalId($_logicalId);
			$Commande->setEqLogic_id($eqLogic->getId());
			$Commande->setType($Type);
			$Commande->setSubType($SubType);
			if ($icone!='')
				$Commande->setDisplay('icon',$icone);
			$Commande->save();
		}
		//$Commande->setEventOnly(1);
		return $Commande;
	}
	/*public static function FaceAnalyse() 
	{
		while(true)
		{
			$Cameras=facedetection::byType('facedetection');
			log::add('facedetection', 'debug', $Cameras->getEqLogic());
			foreach($FaceDetectCamera as $Cameras)
			{
				$EqLogic=$FaceDetectCamera->getEqLogic();
				log::add('facedetection', 'debug', 'Lancement d\'une détéction sur la camera '.$EqLogic->getName());
				$FaceDetectCamera->execute();
			}
		}
	}*/
}

class facedetectionCmd extends cmd {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */
	public function Snapshot($camurl, $image) {
		log::add('facedetection', 'debug', 'Telechargement du flux : '.$camurl);
		$f = fopen($camurl,"r") ;
		if(!$f)
			throw new Exception(__('Impossible de ce connecter a: '.$url, __FILE__));
		else
		{
			//**** URL OK
			while (substr_count($r,"Content-Length") != 2) 
				$r.=fread($f,512);
			$start=stripos($r,"Content-Length");
			$frame=substr($r,$start);
			$start=stripos($frame,"\n")+3;
			$frame=substr($frame,$start);
			$stop=stripos($frame,'--myboundary')-2;
			$frame=substr($frame,0,$stop);
			if(!$fp = fopen($image,'w'))
				throw new Exception(__('Impossible d\'ouvrir le dossier', __FILE__));
			fwrite($fp, $frame); 
			fclose($fp);   
		}
		fclose($f);
	}
	public function FaceDetect($image) {
		
		$detector = new FaceDetector('detection.dat');
		log::add('facedetection', 'debug', 'Début de l\'analyse: '.$image);
		$detector->faceDetect($image);
		$len=count($detector->getFace())/3;
		log::add('facedetection', 'debug', $len.' visage(s) détecté');
		$detector->toJpeg($image);//Encadre dans la photo le visage
		if ($len==0)
		{
			$this->setCollectDate('');
			$this->event(0);
			$this->save();
		}
		else
		{
			$this->setCollectDate('');
			$this->event(1);
			$this->save();
		}
		return $len.' visage(s) détecté';
	}
    public function execute($_options = array()) {
		$EqLogic=$this->getEqLogic();
		$Camera=camera::byId($EqLogic->getConfiguration('snapshots'));
		
		if (netMatch('192.168.*.*', getClientIp())) {
			$protocole = 'protocole';
		} else {
			$protocole = 'protocoleExt';
		}
		$camurl=$Camera->getUrl($Camera->getConfiguration('urlStream'), '', $protocole);
		switch($this->getLogicalId())
		{
			case 'facedetection':
				$image=dirname(__FILE__) . '/../../../../tmp/analyse.jpg';
				self::Snapshot($camurl,$image);
				self::FaceDetect($image);
			break;
			case 'snapshots':
				$image=dirname(__FILE__) .'/../../ressources/FaceDetection/Snapshot_'.date("YmdHis").'.jpg';
				self::Snapshot($camurl,$image);
			break;
			case 'snapshotfacedetect':
				$image=dirname(__FILE__) .'/../../ressources/Snapshots/Snapshot_'.date("YmdHis").'.jpg';
				self::Snapshot($camurl,$image);
				self::FaceDetect($image);
			break;
			case 'snapshotdir':
			break;
			
		}
    }

    /*     * **********************Getteur Setteur*************************** */
}

?>
