<?php
/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
class sercomm extends eqLogic {
    /*     * *************************Attributs****************************** */
  /*
   * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
   * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
	public static $_widgetPossibility = array();
   */

   public function SendTOcam($urltoSend)
   {
     // adm/set_group.cgi?group=WIRELESS&$property=$value
     $username = $this->getConfiguration('login');
     $password = $this->getConfiguration('password');
     $adresseIP = $this->getConfiguration('adresseip');

     log::add('sercomm', 'debug', 'Send to CAM URL='.$urltoSend, true);
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, "http://".$adresseIP."/".$urltoSend);
     curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $server_output = curl_exec($ch);
     $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

     if ($httpcode == 200) {
       log::add('sercomm', 'debug', '200 OK ='.$server_output, true);
       return true;
     } else {
       log::add('sercomm', 'debug', 'HTTP code='.$httpcode.' Err='.curl_error($ch));
       return $httpcode;
     }
     curl_close($ch);
   }

   public function ReadConfig($URLtoRead)
   {
     // http://192.168.0.19/adm/get_group.cgi?group=VIDEO
     $username = $this->getConfiguration('login');
     $password = $this->getConfiguration('password');
     $adresseIP = $this->getConfiguration('adresseip');

     log::add('sercomm', 'debug', 'Read param CAM URL='.$URLtoRead, true);
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, "http://".$adresseIP."/".$URLtoRead);
     curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $server_output = curl_exec($ch);
     $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

     if ($httpcode == 200) {
       log::add('sercomm', 'debug', '200 OK ='.$server_output, true);


         if (is_null($server_output) || empty($server_output)) {
           throw new Exception(__("The command", __FILE__) . " $command " . __('has failed or not returned any string.', __FILE__));
         }
         log::add('sercomm', 'debug', "Get information string $server_output from camera");

         # parse informations
         $informations = [];
         foreach (explode(PHP_EOL, $server_output) as $row) {
           if (empty($row)) {
             continue;
           }
           $info = explode('=', $row, 2);
       	  if (count($info) != 2) {
             log::add('sercomm', 'debug', "The information row $row is not parsable");
             continue;
           }
       	  $key = trim($info[0]);
       	  $value = trim($info[1]);
          log::add('sercomm', 'debug', "Get information key ($key) value : $value");

          if(strpos($key, "event_motion") > 0 ){
              // EVENT motion specific param
          }
          if(strpos($key, "event_audio") > 0 ){
              // EVENT audio specific param
          }

          if(strpos($key, "passw") == 0 ){
              $this->setConfiguration($key, $value);
          } else {
            log::add('sercomm', 'debug', "IGNORE update password detected ($key)");
          }

     	}
      $this->save();
       return true;
     } else {
       log::add('sercomm', 'debug', 'HTTP code='.$httpcode.' Err='.curl_error($ch));
       return $httpcode;
     }
     curl_close($ch);
   }

   public function WriteEventParam(){
     $SetGroup = true;
     /*
     is=1|es=0,|et=2|acts=op1:0;op2:0;email:1;ftpu:0;im:0;httpn:1;httppost:1;wlled:0;smbc:0;sd:0;op3:0;op4:0;smbc_rec:0;sd_rec:0|ei=0|ea=mp4,2,13,1|en=event_motion

      is=1|
      es=0,| SCHEDULE ? 0= all time
      et=2|
      acts=op1:0;op2:0;email:1;ftpu:0;im:0;httpn:1;httppost:1;wlled:0;smbc:0;sd:0;op3:0;op4:0;smbc_rec:0;sd_rec:0| ACTIONS
      ei=0| EVENT INTERVAL ???
      ea=mp4,2,13,1| EVENT ATTACH
      en=event_motion EVENT TRIGGER
      */

      for ($i=1; $i<=2; $i++) {
        $attachedFile = $this->getConfiguration('file'.$i);
        $startover = $this->getConfiguration('startover'.$i);
        $endat = $this->getConfiguration('endat'.$i);

        $int = $this->getConfiguration('int'.$i);

        $email = $this->getConfiguration('email'.$i);
        $httpn = $this->getConfiguration('httpn'.$i);
        $httppost = $this->getConfiguration('httppost'.$i);
        $wlled = $this->getConfiguration('wlled'.$i);
        $ftpu = $this->getConfiguration('ftpu'.$i);
        $smbc_rec = $this->getConfiguration('smbc_rec'.$i);
        $sd_rec = $this->getConfiguration('sd_rec'.$i);
        $im = $this->getConfiguration('im'.$i);

        if ($i == 1) {
          $triggerType = "event_motion";
        } elseif ($i == 2) {
          $triggerType = "event_audio";
        }

        $cfgstr = "is=1|es=0,|et=2|acts=op1:0;op2:0;email:$email;ftpu:$ftpu;im:$im;httpn:$httpn;httppost:$httppost;wlled:$wlled;smbc:0;sd:0;op3:0;op4:0;smbc_rec:$smbc_rec;sd_rec:$sd_rec|ei=$int|ea=$attachedFile,$startover,$endat,1|en=$triggerType";

        if ($i == 1) {
          $cParam = "event1_entry";
        } elseif ($i == 2) {
          $cParam = "event2_entry";
        }
        log::add('sercomm', 'debug', 'Event STR to cfg ='.$cfgstr, true);
        // Send URL
        $res = sercomm::SendTOcam("adm/set_group.cgi?group=EVENT&event".$i."_entry=".urlencode($cfgstr));
        log::add('sercomm', 'debug', 'Cfg URL = adm/set_group.cgi?group=EVENT&event'.$i.'_entry='.$cfgstr, true);
          if ($res == 200) {
            // Pass OK
            log::add('sercomm', 'debug', '> SET OK', true);
          } else {
            // Pass NOK
            $SetGroup = false;
            log::add('sercomm', 'warning', '> NOK Error code='.$res, true);
          }
      }
      return $SetGroup;
   }


   public function WriteConfig($cfgGroup, $ParamTOconfigure)
   {
     $SetGroup = true;
     $ConcatParam = "";
     // Concat config URL
     foreach ($ParamTOconfigure as $param){
       // Spécial CASE
       switch (strtolower($param)) {
           case 'send_email':
              $value = bindec($this->getConfiguration('addmail1').$this->getConfiguration('addmail2').$this->getConfiguration('addmail3'));
              break;
           case 'email_att':
              $value = bindec($this->getConfiguration('mailaddattachement1').$this->getConfiguration('mailaddattachement2').$this->getConfiguration('mailaddattachement3'));
              break;
           default:
                $value = urlencode($this->getConfiguration($param));
              }
       if ($value != NULL) {
         $ConcatParam = $ConcatParam."&".$param."=".$value;
         log::add('sercomm', 'debug', 'Add param='.$param.' Value='.$value, true);
       } else {
         log::add('sercomm', 'debug', 'IGNORE param='.$param, true);
       }

     }
     // Send URL
     $res = sercomm::SendTOcam("adm/set_group.cgi?group=".$cfgGroup.$ConcatParam);
     log::add('sercomm', 'debug', 'Cfg URL = adm/set_group.cgi?group='.$cfgGroup.$ConcatParam, true);
       if ($res == 200) {
         // Pass OK
         log::add('sercomm', 'debug', '> SET OK', true);
       } else {
         // Pass NOK
         $SetGroup = false;
         log::add('sercomm', 'warning', '> NOK Error code='.$res, true);
       }
     return $SetGroup;
   }

   public function getParamToConfigure($group)
   {
       switch (strtoupper($group)) {
           case 'VIDEO':
                return array('video_schedule', 'video_define1', 'video_define2', 'video_define3', 'video_define4', 'video_define5', 'video_define6', 'video_define7', 'video_define8', 'video_define9', 'video_define10',
               'time_stamp', 'text_overlay', 'text', 'power_line', 'color', 'exposure', 'sharpness', 'flip', 'mirror', 'hue', 'saturation', 'contrast', 'dn_sch',
               'dn_sch_hr', 'dn_sch_min', 'dn_hrend', 'dn_minend', 'night_mode', 'dn_interval', 'default_channel', 'dn_threshold', 'aspect_ratio');
           case 'HTTP_NOTIFY':
                return array('http_notify', 'http_url', 'http_proxy', 'http_proxy_no', 'http_method', 'http_user', 'http_password', 'proxy_user', 'proxy_password', 'event_data_flag');
           case 'EMAIL':
                return array('smtp_enable', 'smtp_server', 'pop_server', 'smtp_port', 'smtp_auth', 'smtp_account', 'smtp_password', 'smtp2_enable', 'smtp2_server', 'pop2_server', 'smtp2_port',
               'smtp2_auth', 'smtp2_account', 'smtp2_password', 'from_addr', 'from_addr2', 'to_addr1', 'to_addr2', 'to_addr3', 'send_email', 'email_att', 'subject');
           case 'MOTION':
                return array('md_mode', 'md_switch1', 'md_switch2', 'md_switch3', 'md_switch4', 'md_name1', 'md_name2', 'md_name3', 'md_name4', 'md_window1', 'md_window2', 'md_window3', 'md_window4', 'md_abs_window1',
               'md_abs_window2', 'md_abs_window3', 'md_abs_window4', 'md_threshold1', 'md_threshold2', 'md_threshold3', 'md_threshold4', 'md_sensitivity1', 'md_sensitivity2',
               'md_sensitivity3', 'md_sensitivity4', 'md_update_freq1', 'md_update_freq2', 'md_update_freq3', 'md_update_freq4');
          case 'AUDIO':
                return array('audio_in', 'in_volume', 'in_audio_type', 'audio_mode', 'operation_mode', 'au_trigger_en', 'au_trigger_volume', 'au_trigger_method', 'in_pcm_sr', 'audio_advanced_mode', 'in_volume_again', 'audio_out', 'out_volume');
          case 'FTP':
                return array('ftp1', 'ftp1_server', 'ftp1_account', 'ftp1_passwd', 'ftp1_path', 'ftp1_passive', 'ftp1_port', 'ftp2', 'ftp2_server', 'ftp2_account', 'ftp2_passwd', 'ftp2_path', 'ftp2_passive', 'ftp2_port');
          case 'HTTP':
                return array('http_mode', 'http_port2', 'http_port2_num', 'https_mode', 'ssport_enable', 'ssport_number');
          case 'UPNP':
                return array('upnp_mode', 'upnp_traversal', 'upnp_camera');
          case 'BONJOUR':
                return array('bonjour_name', 'bonjour_mode');
          case 'JPEG':
                return array('mode', 'resolution', 'quality_level', 'frame_rate', 'sp_uri', 'mode2', 'resolution2', 'quality_level2', 'frame_rate2', 'sp_uri2', 'mode3', 'resolution3', 'quality_level3',
                'frame_rate3', 'sp_uri3', 'bandwidth', 'cropping', 'bandwidth2', 'cropping2', 'bandwidth3', 'cropping3');
       }
   }

    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {
      }
     */

    /*
     * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
      public static function cron5() {
      }
     */

    /*
     * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
      public static function cron10() {
      }
     */

    /*
     * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
      public static function cron15() {
      }
     */

    /*
     * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
      public static function cron30() {
      }
     */

    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {
      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {
      }
     */

    /*     * *********************Méthodes d'instance************************* */
 // Fonction exécutée automatiquement avant la création de l'équipement
    public function preInsert() {
    }
 // Fonction exécutée automatiquement après la création de l'équipement
    public function postInsert() {
      $sercommCmd = new sercommCmd();
      $sercommCmd->setName(__('Rafraichir', __FILE__));
      $sercommCmd->setEqLogic_id($this->id);
      $sercommCmd->setType('action');
      $sercommCmd->setSubType('other');
      $sercommCmd->setLogicalId('refresh');
      $sercommCmd->save();

      $sercommCmd = new sercommCmd();
      $sercommCmd->setName(__('Redémarrer', __FILE__));
      $sercommCmd->setEqLogic_id($this->id);
      $sercommCmd->setType('action');
      $sercommCmd->setSubType('other');
      $sercommCmd->setLogicalId('reboot');
      $sercommCmd->save();
    }

 // Fonction exécutée automatiquement avant la mise à jour de l'équipement
    public function preUpdate() {
      if ($this->getConfiguration('password') == '') {
        throw new Exception(__('Le mot de passe ne peut pas être vide', __FILE__));
      }
      if ($this->getConfiguration('adresseip') == '') {
        throw new Exception(__('L\'adresse IP de la caméra est obligatoire', __FILE__));
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
    }

 // Fonction exécutée automatiquement avant la suppression de l'équipement
    public function preRemove() {
    }

 // Fonction exécutée automatiquement après la suppression de l'équipement
    public function postRemove() {
    }

    /*
     * Non obligatoire : permet de modifier l'affichage du widget (également utilisable par les commandes)
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire : permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire : permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class sercommCmd extends cmd {
    /*     * *************************Attributs****************************** */

    /*
      public static $_widgetPossibility = array();
    */

    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */


  // Exécution d'une commande
     public function execute($_options = array()) {



     }

    /*     * **********************Getteur Setteur*************************** */
}
