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
  public function preSave() {}

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
      $hplipCmd->setOrder(1);
      $hplipCmd->save();
	  }
      $hplipCmd = $this->getCmd(null, 'model');
      if (!is_object($hplipCmd)) {
        $hplipCmd = new hplipCmd();
        $hplipCmd->setName(__('Model', __FILE__));
        $hplipCmd->setEqLogic_id($this->getId());
        $hplipCmd->setLogicalId('model');
        $hplipCmd->setType('info');
        $hplipCmd->setSubType('string');
        $hplipCmd->setOrder(2);
        $hplipCmd->save();
      }
      $hplipCmd = $this->getCmd(null, 'state');
      if (!is_object($hplipCmd)) {
        $hplipCmd = new hplipCmd();
        $hplipCmd->setName(__('Connectée', __FILE__));
        $hplipCmd->setEqLogic_id($this->getId());
        $hplipCmd->setLogicalId('state');
        $hplipCmd->setType('info');
        $hplipCmd->setSubType('binary');
        $hplipCmd->setOrder(3);
        $hplipCmd->save();
      }
      $this->refresh();
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
    
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {}

  public function refresh() {
		$hplip_ip = $this->getConfiguration('ip');



    $hplip_cmd = 'http://'. $hplip_ip .'/DevMgmt/ProductUsageDyn.xml';

    log::add('hplip', 'debug', 'Lancement de l\'actualisation  ' . $hplip_cmd);
    $hplip_context = stream_context_create(array('http' => array('ignore_errors' => true),));
    $hplip_infos = file_get_contents($hplip_cmd, false, $hplip_context);
    //fwrite($hplip_infos, realpath(dirname(__FILE__)) .'/../../data/test.xml');
    //$parametres = simplexml_load_file(realpath(dirname(__FILE__)) .'/../../data/test.xml');
    $parametres = simplexml_load_string($hplip_infos);
 
    list($site_root) = $parametres->xpath("parametre[@name='pudyn:ConsumableSubunit']");
    log::add('hplip', 'debug', 'brut' .  $site_root);
        
		
    
    
    /*f (exec('grep agent1-desc '. $hplip_dir )==null) 
    {
      $this->checkAndUpdateCmd('state', false);
      log::add('hplip', 'info', 'Imprimante déconnectée');
      return;
    }
    log::add('hplip', 'info', 'Imprimante connectée, actualisation des données');
    $this->checkAndUpdateCmd('state', true);

    $hplip_sup = array("model-ui", " ");
    $hplip_data = str_replace($hplip_sup, "", exec('grep model-ui '. $hplip_dir .' | head -n 1'));
    log::add('hplip', 'debug', 'Model: '. $hplip_data);
    $this->checkAndUpdateCmd('model', $hplip_data);

      

      $hplipCmd = $this->getCmd(null, 'ink1type');
      if (!is_object($hplipCmd)) {
        $hplipCmd = new hplipCmd();
        $hplipCmd->setName(__('Type Cartouche 1', __FILE__));
        $hplipCmd->setEqLogic_id($this->getId());
        $hplipCmd->setLogicalId('ink1type');
        $hplipCmd->setType('info');
        $hplipCmd->setSubType('string');
        $hplipCmd->setOrder(4);
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
        $hplipCmd->setConfiguration('invertBinary', '1');
        $hplipCmd->setOrder(5);
        $hplipCmd->save();
      }
      $hplipCmd = $this->getCmd(null, 'ink1perc');
      if (!is_object($hplipCmd)) {
        $hplipCmd = new hplipCmd();
        $hplipCmd->setName(__('Pourcentage restant Cartouche 1', __FILE__));
        $hplipCmd->setEqLogic_id($this->getId());
        $hplipCmd->setLogicalId('ink1perc');
        $hplipCmd->setType('info');
        $hplipCmd->setSubType('numeric');
        $hplipCmd->setConfiguration('minValue', '0');
        $hplipCmd->setConfiguration('maxValue', '100');
        $hplipCmd->setOrder(6);
        $hplipCmd->save();
        
      }

      //1
      $hplip_sup = array("agent1-desc", " ");
      $hplip_data = str_replace($hplip_sup, "", exec('grep agent1-desc '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink1type', $hplip_data);
      
      $hplip_sup = array("agent1-level ", " ");
      $hplip_data1 = str_replace($hplip_sup, "", exec('grep agent1-level '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink1perc', $hplip_data1);
      
      $hplip_sup = array("agent1-health", " ");
      $hplip_data2 = str_replace($hplip_sup, "", exec('grep agent1-health '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink1state', $hplip_data2);
      log::add('hplip', 'debug', 'Cartouche1: '. $hplip_data . ', Pourcentage:  ' . $hplip_data1 . ', Etat: ' . $hplip_data2);

    
    if (exec('grep agent2-desc '. $hplip_dir )!=null) 
    {

      $hplipCmd = $this->getCmd(null, 'ink2type');
      if (!is_object($hplipCmd)) {
        $hplipCmd = new hplipCmd();
        $hplipCmd->setName(__('Type Cartouche 2', __FILE__));
        $hplipCmd->setEqLogic_id($this->getId());
        $hplipCmd->setLogicalId('ink2type');
        $hplipCmd->setType('info');
        $hplipCmd->setSubType('string');
        $hplipCmd->setOrder(7);
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
        $hplipCmd->setConfiguration('invertBinary', '1');
        $hplipCmd->setOrder(8);
        $hplipCmd->save();
      }
      $hplipCmd = $this->getCmd(null, 'ink2perc');
      if (!is_object($hplipCmd)) {
        $hplipCmd = new hplipCmd();
        $hplipCmd->setName(__('Pourcentage restant Cartouche 2', __FILE__));
        $hplipCmd->setEqLogic_id($this->getId());
        $hplipCmd->setLogicalId('ink2perc');
        $hplipCmd->setType('info');
        $hplipCmd->setSubType('numeric');
        $hplipCmd->setConfiguration('minValue', '0');
        $hplipCmd->setConfiguration('maxValue', '100');
        $hplipCmd->setOrder(9);
        $hplipCmd->save();
      }
      //2
      $hplip_sup = array("agent2-desc", " ");
      $hplip_data = str_replace($hplip_sup, "", exec('grep agent2-desc '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink2type', $hplip_data);
      
      $hplip_sup = array("agent2-level ", " ");
      $hplip_data1 = str_replace($hplip_sup, "", exec('grep agent2-level '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink2perc', $hplip_data1);
      
      $hplip_sup = array("agent2-health", " ");
      $hplip_data2 = str_replace($hplip_sup, "", exec('grep agent2-health '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink2state', $hplip_data2);
      log::add('hplip', 'debug', 'Cartouche2: '. $hplip_data . ', Pourcentage:  ' . $hplip_data1 . ', Etat: ' . $hplip_data2);
    }

    if (exec('grep agent3-desc '. $hplip_dir )!=null) 
    {
      $hplipCmd = $this->getCmd(null, 'ink3type');
      if (!is_object($hplipCmd)) {
        $hplipCmd = new hplipCmd();
        $hplipCmd->setName(__('Type Cartouche 3', __FILE__));
        $hplipCmd->setEqLogic_id($this->getId());
        $hplipCmd->setLogicalId('ink3type');
        $hplipCmd->setType('info');
        $hplipCmd->setSubType('string');
        $hplipCmd->setOrder(10);
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
        $hplipCmd->setConfiguration('invertBinary', '1');
        $hplipCmd->setOrder(11);
        $hplipCmd->save();
      }
      $hplipCmd = $this->getCmd(null, 'ink3perc');
      if (!is_object($hplipCmd)) {
        $hplipCmd = new hplipCmd();
        $hplipCmd->setName(__('Pourcentage restant Cartouche 3', __FILE__));
        $hplipCmd->setEqLogic_id($this->getId());
        $hplipCmd->setLogicalId('ink3perc');
        $hplipCmd->setType('info');
        $hplipCmd->setSubType('numeric');
        $hplipCmd->setConfiguration('minValue', '0');
        $hplipCmd->setConfiguration('maxValue', '100');
        $hplipCmd->setOrder(12);
        $hplipCmd->save();
      }

      //3
      $hplip_sup = array("agent3-desc", " ");
      $hplip_data = str_replace($hplip_sup, "", exec('grep agent3-desc '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink3type', $hplip_data);
      
      $hplip_sup = array("agent3-level ", " ");
      $hplip_data1 = str_replace($hplip_sup, "", exec('grep agent3-level '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink3perc', $hplip_data1);
      
      $hplip_sup = array("agent3-health", " ");
      $hplip_data2 = str_replace($hplip_sup, "", exec('grep agent3-health '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink3state', $hplip_data2);
      log::add('hplip', 'debug', 'Cartouche3: '. $hplip_data . ', Pourcentage:  ' . $hplip_data1 . ', Etat: ' . $hplip_data2);
    }

    if (exec('grep agent4-desc '. $hplip_dir )!=null) 
    {

      $hplipCmd = $this->getCmd(null, 'ink4type');
      if (!is_object($hplipCmd)) {
        $hplipCmd = new hplipCmd();
        $hplipCmd->setName(__('Type Cartouche 4', __FILE__));
        $hplipCmd->setEqLogic_id($this->getId());
        $hplipCmd->setLogicalId('ink4type');
        $hplipCmd->setType('info');
        $hplipCmd->setSubType('string');
        $hplipCmd->setOrder(13);
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
        $hplipCmd->setConfiguration('invertBinary', '1');
        $hplipCmd->setOrder(14);
        $hplipCmd->save();
      }
      $hplipCmd = $this->getCmd(null, 'ink4perc');
      if (!is_object($hplipCmd)) {
        $hplipCmd = new hplipCmd();
        $hplipCmd->setName(__('Pourcentage restant Cartouche 4', __FILE__));
        $hplipCmd->setEqLogic_id($this->getId());
        $hplipCmd->setLogicalId('ink4perc');
        $hplipCmd->setType('info');
        $hplipCmd->setSubType('numeric');
        $hplipCmd->setOrder(15);
        $hplipCmd->save();
      }
     
      //4
      $hplip_sup = array("agent4-desc", " ");
      $hplip_data = str_replace($hplip_sup, "", exec('grep agent4-desc '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink4type', $hplip_data);
      
      $hplip_sup = array("agent4-level ", " ");
      $hplip_data1 = str_replace($hplip_sup, "", exec('grep agent4-level '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink4perc', $hplip_data1);
      
      $hplip_sup = array("agent4-health", " ");
      $hplip_data2 = str_replace($hplip_sup, "", exec('grep agent4-health '. $hplip_dir .' | head -n 1'));
      $this->checkAndUpdateCmd('ink4state', $hplip_data2);
      log::add('hplip', 'debug', 'Cartouche4: '. $hplip_data . ', Pourcentage:  ' . $hplip_data1 . ', Etat: ' . $hplip_data2);
    }

*/
    
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
