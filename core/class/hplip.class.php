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
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class hplip extends eqLogic {
  /*     * *************************Attributs****************************** */

  /*
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
  public static $_encryptConfigKey = array('param1', 'param2');
  */

  /*     * ***********************Methode static*************************** */

  public static function cron() {
		$dateRun = new DateTime();
		foreach (self::byType('hplip', true) as $eqLogic) {
			$autorefresh = $eqLogic->getConfiguration('autorefresh');
			if ($eqLogic->getIsEnable() == 1){
				if ($autorefresh == '') {
					$autorefresh = '*/5 * * * *';
				}
				try {
					$c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
					if ($c->isDue($dateRun)) {
						try {
							$eqLogic->refresh();
						} catch (Exception $exc) {
							log::add('hplip', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
						}
					}
				} 
				catch (Exception $exc) {
					log::add('hplip', 'error', __('Expression cron non valide pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $autorefresh);
				}
			}
		}
	}

  /*
  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  public static function cron5() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  public static function cron10() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  public static function cron30() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  public static function cronHourly() {}
  */

  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
  */

  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  public function preInsert() {
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
    if ($this->getConfiguration('ip') == '') {
			throw new Exception('L\'adresse IP ne peut pas être vide');
	 	}
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
    $hplipCmd = $this->getCmd(null, 'refresh');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Rafraichir', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('refresh');
      $hplipCmd->setType('action');
      $hplipCmd->setSubType('other');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink1type');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Type Cartouche 1', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink1type');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('string');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink1state');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Etat Cartouche 1', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink1state');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('binary');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink1perc');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Pourcentage restant Cartouche 1', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink1perc');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('string');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink2type');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Type Cartouche 2', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink2type');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('string');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink2state');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Etat Cartouche 2', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink2state');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('binary');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink2perc');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Pourcentage restant Cartouche 2', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink2perc');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('string');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink3type');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Type Cartouche 3', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink3type');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('string');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink3state');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Etat Cartouche 3', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink3state');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('binary');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink3perc');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Pourcentage restant Cartouche 3', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink3perc');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('string');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink4type');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Type Cartouche 4', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink4type');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('string');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink4state');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Etat Cartouche 4', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink4state');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('binary');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'ink4perc');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Pourcentage restant Cartouche 4', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('ink4perc');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('string');
      $hplipCmd->save();
	  }
    $hplipCmd = $this->getCmd(null, 'name');
		if (!is_object($hplipCmd)) {
			$hplipCmd = new hplipCmd();
		  $hplipCmd->setName(__('Nom', __FILE__));
      $hplipCmd->setEqLogic_id($this->getId());
      $hplipCmd->setLogicalId('name');
      $hplipCmd->setType('info');
      $hplipCmd->setSubType('string');
      $hplipCmd->save();
	  }
    if ($this->getConfiguration('ip')!="" && $this->getConfiguration('installer')!='OK') {
      

        $installation=exec('sudo hp-setup -i -a -x ' . hplip::getConfiguration("ip") . ' && 2 | grep TEST');
      if ($installation!="") {
        $this->setConfiguration('installer', 'OK');
        $this->save();
        log::add('hplip', 'info', 'Imprimante Installée');
      }
      else{
        log::add('hplip', 'error', 'Problème lors de l\'installation de l\'imprimante, vérifier qu\'elle est bien alimentée et que l\'adresse IP rentrée dans le plugin est valide');
      }
    }
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
    exec('sudo hp-setup -i -a -r ' . hplip::getConfiguration("ip"));
    log::add('hplip', 'info', 'Imprimante désinstallée');
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }
  public function refresh() {
    set_time_limit(60);
		//log::add('hplip', 'debug', 'test ');
    $hplip_dir = realpath(dirname(__FILE__)) .'/../../data/infos.txt';
		$hplip_ip = $this->getConfiguration('ip');
    
		$hplip_cmd = 'hp-info -i > ' . $hplip_dir;
		log::add('hplip', 'info', 'Commande refresh');
		exec($hplip_cmd);

    $hplip_sup = array("agent1-desc", " ");
    $hplip_data = str_replace($hplip_sup, "", exec('grep agent1-desc '. $hplip_dir));
    $this->checkAndUpdateCmd('ink1type', $hplip_data);
    
    $hplip_sup = array("agent1-level ", " ");
    $hplip_data1 = str_replace($hplip_sup, "", exec('grep agent1-level '. $hplip_dir));
    $this->checkAndUpdateCmd('ink1perc', $hplip_data1);
    
    $hplip_sup = array("agent1-health", " ");
    $hplip_data2 = str_replace($hplip_sup, "", exec('grep agent1-health '. $hplip_dir));
    $this->checkAndUpdateCmd('ink1state', $hplip_data2);

    log::add('hplip', 'debug', 'Agent1: '. $hplip_data . ', Pourcentage:  ' . $hplip_data1 . ', Etat: ' . $hplip_data2);

    

    
	}
  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
  public function decrypt() {
    $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
  }
  public function encrypt() {
    $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
  }
  */

  /*
  * Permet de modifier l'affichage du widget (également utilisable par les commandes)
  public function toHtml($_version = 'dashboard') {}
  */

  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3( $value ) {
    // do some checks or modify on $value
    return $value;
  }
  */

  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3($value) {
    // no return value
  }
  */

  /*     * **********************Getteur Setteur*************************** */

}

class hplipCmd extends cmd {
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */

  // Exécution d'une commande
  public function execute($_options = array()) {
    $eqlogic = $this->getEqLogic();
    $eqlogic->refresh();
  }

  /*     * **********************Getteur Setteur*************************** */

}
