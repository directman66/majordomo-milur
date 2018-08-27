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

        if ((time() - gg('cycle_milurRun')) < 360*2 ) {
			$out['CYCLERUN'] = 1;
		} else {
			$out['CYCLERUN'] = 0;
		}

$out['MODEL']=SETTINGS_APPMILUR_MODEL;
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
 $out['MODEL']=SETTINGS_APPMILUR_MODEL;		

$cmd_rec = SQLSelectOne("SELECT VALUE FROM milur_config where parametr='DEBUG'");
$out['MSG_DEBUG']=$cmd_rec['VALUE'];




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
 if ($this->view_mode=='get') {
setGlobal('cycle_milurControl','start'); 
$this->getdata();
//echo "start"; 
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
$this->getdata();   

		 
	$this->config['LATEST_UPDATE']=time();
	//$this->saveConfig();

   } 
  }


 function getdata() {
SQLexec("update milur_config set value='' where parametr='DEBUG'");	    

$debug=date('m/d/Y H:i:s', time())."<br>";

$host= SETTINGS_APPMILUR_IP;
$port= SETTINGS_APPMILUR_PORT;;
   $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));  // Create Socket
        if (socket_connect($socket, $host, $port)) {  //Connect

$debug.='Socket сonnected '.$host.'('. $port.')<br>';


//$objname='current';         
$objname=SETTINGS_APPMILUR_MODEL;
addClassObject('Milur',$objname);
sg($objname.".lasttimestamp",gg($objname.".timestamp"));                    
         
         
//circle 1
        $sendStr = 'ff 08 00 ff ff ff ff ff ff 4f 2d';  // 16 hexadecimal data
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex

$debug.="cicle 1<br>";
$debug.=" send:".$sendStr."<br>" ; 
$debug.=" answer:" . $receiveStr."<br>";   
$debug.=" answerSTR:" .hex2str($receiveStrHex)."<br>";
$debug.=" answerHEX:" . $receiveStrHex.'<br>';
//echo $debug;


         
       //цикл 2
/*         
        $sendStr = 'ff 01 20 41 b8';  // модель
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }

            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex

$debug.="send:".$sendStr ; 
$debug.=" answer:" . $receiveStr;   
$debug.=" answerSTR:" .hex2str($receiveStrHex);
$debug.=" answerHEX:" . $receiveStrHex.'<br>';

if ($receiveStr<>0)        sg($objname.".model",$receiveStr);  
if ($receiveStr<>0) sg($objname.".timestamp",time());            
/* 
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
$debug.=   "U:".$sendStr ; 
$debug.=" answer:" . $receiveStr;   
$debug.=" answerSTR:" .hex2str($receiveStrHex);
$debug.=" answerHEX:" . $receiveStrHex;    
$debug.=" answerUHEX:" . $uhex;   
$debug.=" answerU:" . $u.'<br>'; 
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
$debug.= " answer:" . $receiveStr;   
$debug.= " answerSTR:" .hex2str($receiveStrHex);
$debug.= " answerHEX:" . $receiveStrHex;    
$debug.= " answerS0HEX:" . $s1hex;   
$debug.= " answerS0:" . $s0;
$debug.= " answerSK0:" . $sk0;                  
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
$debug.= " answer:" . $receiveStr;   
$debug.= " answerSTR:" .hex2str($receiveStrHex);
$debug.= " answerHEX:" . $receiveStrHex;    
$debug.= " answerS1HEX:" . $s1hex;   
$debug.= " answerS1:" . $s1;
$debug.= " answerSK1:" . $sk1;                  
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
$debug.= " answer:" . $receiveStr;   
$debug.= " answerSTR:" .hex2str($receiveStrHex);
$debug.= " answerHEX:" . $receiveStrHex;    
$debug.= " answerS2HEX:" . $s2hex;   
$debug.= " answerS2:" . $s2;
$debug.= " answerSK2:" . $sk2;         
//echo '<br>'; 
if ($s2<>0)    sg($objname.".S2",$s2);                  
if ($s2hex<>0) sg($objname.".S1hex",$s2hex);            
*/

        socket_close($socket);  // Close Socket       
$debug.='Socked closed.<br>';


        } 
else 
{$debug='Error create socket '.$host.'('. $port.')';}
     socket_close($socket);  // Close Socket       
SQLexec("update milur_config set value='$debug' where parametr='DEBUG'");	    
sg($objname.'.debug',$debug);



SQLexec("update milur_config set value=UNIX_TIMESTAMP() where parametr='LASTCYCLE_TS'");		   
SQLexec("update milur_config set value=now() where parametr='LASTCYCLE_TXT'");		   	   

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
  SQLExec('DROP TABLE IF EXISTS milur_config');
  SQLExec('delete from settings where NAME like "%APPMILUR%"');

SQLExec("delete from pvalues where property_id in (select id FROM properties where object_id in (select id from objects where class_id = (select id from classes where title = 'Milur')))");
SQLExec("delete from properties where object_id in (select id from objects where class_id = (select id from classes where title = 'Milur'))");
SQLExec("delete from objects where class_id = (select id from classes where title = 'Milur')");
SQLExec("delete from methods where class_id = (select id from classes where title = 'Milur')");	 
SQLExec("delete from classes where title = 'Milur'");	 



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

$onChange='

$objn=$this->object_title;

$i=gg($objn.".P")/gg($objn.".U");
sg($objn.".I",  round($i,2));
SQLUpdate("objects", array("ID"=>$this->id, "DESCRIPTION"=>gg("sysdate")." ".gg("timenow"))); 


//расчет потребленного электричества с момента последней проверки
//получаем дату предыдущей проверки
//$laststamp=getHistoryValue($this->getProperty("timestamp"),);
$t1=gg($objn.".t1");
$t2=gg($objn.".t2");
$laststamp=gg($objn.".lasttimestamp");
$diff=(gmdate("i",trim(time()-$laststamp)));
$pattern = "|\b[0]+([1-9][\d]*)|is"; 
$diff2= preg_replace($pattern, "\\1", $diff); 
sg($objn.".proshlo_min",  $diff2);


//получаем последннее значение мощности
//$lastph=getHistoryMax($this->getProperty("P"));
//$lastph=getHistoryMax("current.P");
$lastph=getHistoryValue($objn.".P", $laststamp-1,$laststamp+1);
//переведем в ват в мин
$lastpm=$lastph*0.0166667;
 //за последний период в минутах было потреблено ватт
$potrebleno=$lastpm*$diff2;
sg($objn.".potrebleno_w",  $potrebleno);
sg($objn.".lastph",  $lastph);
sg($objn.".lastpm",  $lastpm);
$time=date("H:i:s");


$date_min = new DateTime("7:00"); // минимальное значение времени
$date_max = new DateTime("23:00"); // максимальное значение времени
$date_now = new DateTime($date); // текущее значение времени
// Проверяем, находится ли $date_now в диапазоне
if ($date_now >= $date_min && $date_now <= $date_max) 
{$tarif=1; 
sg($objn.".potrebleno_w_t1",  $potrebleno);
sg($objn.".potrebleno_w_t1_sum",  gg($objn.".potrebleno_w_t1_sum")+$potrebleno); 
//$st=$t1/16.6667;
 $st=$t1/1000;
sg($objn.".potrebleno_w_t1_rub",  $potrebleno*$st);
sg($objn.".potrebleno_w_rub",  $potrebleno*$st); 
} else
{$tarif=2;
sg($objn.".potrebleno_w_t2",  $potrebleno);
sg($objn.".potrebleno_w_t2_sum",  gg($objn.".potrebleno_w_t2_sum")+$potrebleno);  
//$st=$t2/16.6667;
 $st=$t2/1000;
sg($objn.".potrebleno_w_t2_rub",  $potrebleno*$st);
sg($objn.".potrebleno_w_rub",  $potrebleno*$st);
 
}
sg($objn.".tarif",  $tarif);

';

setGlobal('cycle_milurAutoRestart','1');	 	 
$classname='Milur';
addClass($classname); 
addClassMethod($classname,'OnChange',$onChange);	 

$prop_id=addClassProperty($classname, 'I', 30);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Сила тока'; //   <-----------
SQLUpdate('properties',$property);} 


$prop_id=addClassProperty($classname, 'P', 30);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Мгновенная потребляемая мощность'; //   <-----------
SQLUpdate('properties',$property);} 

$prop_id=addClassProperty($classname, 'S1', 30);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Значение счеткика тариф 1'; //   <-----------
SQLUpdate('properties',$property);} 

$prop_id=addClassProperty($classname, 'S2', 30);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Значение счеткика тариф 2'; //   <-----------
SQLUpdate('properties',$property);} 

$prop_id=addClassProperty($classname, 'timestamp', 30);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='timestamp'; //   <-----------
SQLUpdate('properties',$property);} 

$prop_id=addClassProperty($classname, 'U', 30);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Мгновенное напряжение'; //   <-----------
$property['ONCHANGE']="OnChange"; //	   	       
SQLUpdate('properties',$property);} 


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
 milur_config: value varchar(10000)  
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

$par['parametr'] = 'DEBUG';
$par['value'] = "";		 
SQLInsert('milur_config', $par);						


 }
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

/*
*
* TW9kdWxlIGNyZWF0ZWQgSmFuIDAzLCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/

