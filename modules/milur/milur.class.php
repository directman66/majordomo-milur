<?php
/**
* milur 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 10:01:31 [Jan 03, 2018])
*/
//
//
class milur extends module {
/**
* milur
*
* Module class constructor
*
* @access private
*/
function milur() {
  $this->name="milur";
  $this->title="Милур";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $this->checkSettings();
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {


        if ((time() - gg('cycle_milurRun')) < 360*2 ) {
			$out['CYCLERUN'] = 1;
		} else {
			$out['CYCLERUN'] = 0;
		}

 $this->getConfig();

 if ($this->view_mode=='update_settings') {
   global $api_url;
   $this->config['API_URL']=$api_url;
   global $api_key;
   $this->config['API_KEY']=$api_key;
   global $api_username;
   $this->config['API_USERNAME']=$api_username;
   global $api_password;
   $this->config['API_PASSWORD']=$api_password;
   $this->saveConfig();
   $this->redirect("?");
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 
  
  
   $this->redirect("?");
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* milur_devices search
*
* @access public
*/
 function search_milur_devices(&$out) {
  require(DIR_MODULES.$this->name.'/milur_devices_search.inc.php');
 }



function checkSettings() {


  $settings=array(
   array(
    'NAME'=>'APPMILUR_IP', 
    'TITLE'=>'IP adress ser2net: (*)', 
    'TYPE'=>'text',
    'DEFAULT'=>'192.168.1.X'
    ),

   array(
    'NAME'=>'APPMILUR_PORT', 
    'TITLE'=>'Milur ser2net port',
    'TYPE'=>'text',
    'DEFAULT'=>'3000'
    )

 ,  array(
    'NAME'=>'APPMILUR_MODEL', 
    'TITLE'=>'Milur model',
    'TYPE'=>'select',
    'DEFAULT'=>'milur104',
    'DATA'=>'milur104=Milur 104|milur307=Milur 307'
    )
	  
	  
,array(	  
'NAME'=>'APPMILUR_INTERVAL', 
    'TITLE'=>'Interval (sec >= 5sec):', 
    'TYPE'=>'text',
    'DEFAULT'=>'20'
    )
,   array(
    'NAME'=>'APPMILUR_ENABLE', 
    'TITLE'=>'Enable',
    'TYPE'=>'yesno',
    'DEFAULT'=>'1'
    )


   );


   foreach($settings as $k=>$v) {
    $rec=SQLSelectOne("SELECT ID FROM settings WHERE NAME='".$v['NAME']."'");
    if (!$rec['ID']) {
     $rec['NAME']=$v['NAME'];
     $rec['VALUE']=$v['DEFAULT'];
     $rec['DEFAULTVALUE']=$v['DEFAULT'];
     $rec['TITLE']=$v['TITLE'];
     $rec['TYPE']=$v['TYPE'];
     $rec['DATA']=$v['DATA'];
     $rec['ID']=SQLInsert('settings', $rec);
     Define('SETTINGS_'.$rec['NAME'], $v['DEFAULT']);
    }
   }

 	
 
	
	
}

 function processCycle() {
   $this->getConfig();
   $every=$this->config['EVERY'];
   $tdev = time()-$this->config['LATEST_UPDATE'];
   $has = $tdev>$every*60;
   if ($tdev < 0) {
		$has = true;
   }
   
   if ($has) {  
//$this->getdatefnc();   

		 
	$this->config['LATEST_UPDATE']=time();
	//$this->saveConfig();
SQLexec("update milur_config set value=UNIX_TIMESTAMP() where parametr='LASTCYCLE_TS'");		   
SQLexec("update milur_config set value=now() where parametr='LASTCYCLE_TXT'");		   	   

   } 
  }


 function getdata() {

/*
$host= SETTINGS_APPMILUR_IP;
$port= SETTINGS_APPMILUR_PORT;;
   $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));  // Create Socket
        if (socket_connect($socket, $host, $port)) {  //Connect
         
sg("current.lasttimestamp",gg("current.timestamp"));                    
         
         
//circle 1
        $sendStr = 'ff 08 00 ff ff ff ff ff ff 4f 2d';  // 16 hexadecimal data
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
//         echo  "send:".$sendStr ; 
//         echo " answer:" . $receiveStr;   
//         echo " answerSTR:" .hex2str($receiveStrHex);
//         echo " answerHEX:" . $receiveStrHex.'<br>';
   
         
         //цикл 2
         
        $sendStr = 'ff 01 20 41 b8';  // модель
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
//         echo  "send:".$sendStr ; 
//         echo " answer:" . $receiveStr;   
//         echo " answerSTR:" .hex2str($receiveStrHex);
//         echo " answerHEX:" . $receiveStrHex.'<br>';
$objname='current';
if ($receiveStr<>0)        sg($objname.".model",$receiveStr);  
if ($receiveStr<>0) sg($objname".timestamp",time());            
 
         //цикл 3
        $sendStr = 'ff 01 03 00 61';  // P
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
        
$phex=substr($receiveStrHex,12,2).substr($receiveStrHex,10,2).substr($receiveStrHex,8,2);
$p=hexdec($phex)/1000;          
//         echo  "P:".$sendStr ; 
//         echo " answer:" . $receiveStr;   
//         echo " answerSTR:" .hex2str($receiveStrHex);
//         echo " answerHEX:" . $receiveStrHex;
//  echo " answerPHEX:" . $phex;   
//          echo " answerP:" . $p.'<br>';
          if ($p<>0)       sg($objname.".P",round($p));
          if ($p<>0) sg($objname.".timestamp",time());                     

         
    
     //цикл 4
        $sendStr = 'ff 01 01 81 a0 ';  // U
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
       
$uhex=substr($receiveStrHex,12,2).substr($receiveStrHex,10,2).substr($receiveStrHex,8,2);
$u=hexdec($uhex)/1000;       
// echo  "U:".$sendStr ; 
//         echo " answer:" . $receiveStr;   
//         echo " answerSTR:" .hex2str($receiveStrHex);
//         echo " answerHEX:" . $receiveStrHex;    
//      echo " answerUHEX:" . $uhex;   
//          echo " answerU:" . $u.'<br>'; 
            if ($u<>0)    sg($objname.".U",round($u));        

         //цикл 5 счетчик общий
        $sendStr = 'ff 01 04 41 a3';  // S1
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
       
$s0hex=substr($receiveStrHex,12,2).substr($receiveStrHex,10,2).substr($receiveStrHex,8,2);
$s0=hexdec($s0hex)/1000;       
$sk0=$s0*0.00027777777777778;         
 echo  "S0:".$sendStr ; 
//         echo " answer:" . $receiveStr;   
//         echo " answerSTR:" .hex2str($receiveStrHex);
//         echo " answerHEX:" . $receiveStrHex;    
//      echo " answerS0HEX:" . $s1hex;   
//          echo " answerS0:" . $s0;
//echo " answerSK0:" . $sk0;                  
//           echo '<br>'; 
            if ($s0<>0)    sg($objname.".S0",$s0);
         
//цикл 6 счетчик тариф 1
        $sendStr = 'ff 01 05 80 63';  // S1
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
       
$s1hex=substr($receiveStrHex,12,2).substr($receiveStrHex,10,2).substr($receiveStrHex,8,2);
$s1=hexdec($s1hex)/1000;       
$sk1=$s1*0.00027777777777778;         
 echo  "S1:".$sendStr ; 
//         echo " answer:" . $receiveStr;   
//         echo " answerSTR:" .hex2str($receiveStrHex);
//         echo " answerHEX:" . $receiveStrHex;    
//      echo " answerS1HEX:" . $s1hex;   
//          echo " answerS1:" . $s1;
//echo " answerSK1:" . $sk1;                  
//echo           '<br>'; 
if ($s1<>0)    sg($objname.".S1",$s1);         
if ($s1hex<>0)    sg($objname."S1hex",$s1hex);          
         
//цикл 6 счетчик тариф 2
        $sendStr = 'ff 01 06 c0 62';  // S2
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
       
$s2hex=substr($receiveStrHex,12,2).substr($receiveStrHex,10,2).substr($receiveStrHex,8,2);
$s2=hexdec($s2hex)/1000;       
$sk2=$s2*0.00027777777777778;
 echo  "S2:".$sendStr ; 
//         echo " answer:" . $receiveStr;   
//         echo " answerSTR:" .hex2str($receiveStrHex);
//         echo " answerHEX:" . $receiveStrHex;    
//      echo " answerS2HEX:" . $s2hex;   
//          echo " answerS2:" . $s2;
//echo " answerSK2:" . $sk2;         
//echo '<br>'; 
if ($s2<>0)    sg($objname.".S2",$s2);                  
if ($s2hex<>0) sg($objname.".S1hex",$s2hex);                   

         
        }
        socket_close($socket);  // Close Socket

*/

 }



/**
* milur_devices edit/add
*
* @access public
*/
 
/**
* milur_devices delete record
*
* @access public
*/
 
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS milur_devices');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data = '') {

setGlobal('cycle_milurAutoRestart','1');	 	 
$classname='Milur';
addClass($classname); 

/*
milur_devices - 
*/
  $data = <<<EOD
 milur_devices: ID int(10) unsigned NOT NULL auto_increment
 milur_devices: TITLE varchar(100) NOT NULL DEFAULT ''
 milur_devices: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 milur_devices: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);

  $data = <<<EOD
 milur_config: parametr varchar(300)
 milur_config: value varchar(100)  
EOD;
   parent::dbInstall($data);



$par['parametr'] = 'EVERY';
$par['value'] = 30;		 
SQLInsert('milur_config', $par);				
	
$par['parametr'] = 'LASTCYCLE_TS';
$par['value'] = "0";		 
SQLInsert('milur_config', $par);						
		
$par['parametr'] = 'LASTCYCLE_TXT';
$par['value'] = "0";		 
SQLInsert('milur_config', $par);						

 }
// --------------------------------------------------------------------
	

function strToHex($string){
    $hex='';
    for ($i=0; $i < strlen($string); $i++){
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}


function hexToStr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

function hex2str($hex) {
    $str = '';
    for($i=0;$i<strlen($hex);$i+=2) $str .= chr(hexdec(substr($hex,$i,2)));
    return $str;
}	
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgSmFuIDAzLCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/

