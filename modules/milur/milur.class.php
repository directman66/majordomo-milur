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
//ini_set('max_execution_time', '600');
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
  $this->title="Счетчики Милур";
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

 $out['TS']=date('m/d/Y H:i:s',gg(SETTINGS_APPMILUR_MODEL.".lasttimestamp"));		
 $out['COUNTTS']=date('m/d/Y H:i:s',gg(SETTINGS_APPMILUR_MODEL.".countersts"));		

 $out['P']=gg(SETTINGS_APPMILUR_MODEL.".P");		
 $out['U']=gg(SETTINGS_APPMILUR_MODEL.".U");		
 $out['I']=gg(SETTINGS_APPMILUR_MODEL.".I");		



 $out['S0']=gg(SETTINGS_APPMILUR_MODEL.".S0");		
 $out['S1']=gg(SETTINGS_APPMILUR_MODEL.".S1");		
 $out['S2']=gg(SETTINGS_APPMILUR_MODEL.".S2");		

$now=date();

$out['MONTH_WATT']=round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt1', $now-2629743 ,$now))+round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt2', $now-2629743 ,$now));
$out['MONTH_RUB']=(round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt1', $now-2629743 ,$now)*SETTINGS_APPMILUR_T1))+(round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt2', $now-2629743 ,$now)*SETTINGS_APPMILUR_T2));

$out['DAY_WATT']=round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt1', $now-86400 ,$now))+round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt2', $now-86400 ,$now));
$out['DAY_RUB']=(round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt1', $now-86400 ,$now)*SETTINGS_APPMILUR_T1))+(round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt2', $now-86400 ,$now)*SETTINGS_APPMILUR_T2));

$out['WEEK_WATT']=round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt1', $now-604800 ,$now))+round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt2', $now-604800 ,$now));
$out['WEEK_RUB']=(round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt1', $now-604800 ,$now)*SETTINGS_APPMILUR_T1))+(round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt2', $now-604800 ,$now)*SETTINGS_APPMILUR_T2));

$out['YEAR_WATT']=round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt1', $now-31556926 ,$now))+round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt2', $now-31556926 ,$now));
$out['YEAR_RUB']=(round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt1', $now-31556926 ,$now)*SETTINGS_APPMILUR_T1))+(round(getHistorySum(SETTINGS_APPMILUR_MODEL.'.rashodt2', $now-31556926 ,$now)*SETTINGS_APPMILUR_T2));


$cmd_rec = SQLSelectOne("SELECT VALUE FROM milur_config where parametr='DEBUG'");
$out['MSG_DEBUG']=$cmd_rec['VALUE'];



 if ($this->view_mode=='get') {
setGlobal('cycle_milurControl','start'); 
$this->getdata();
//echo "start"; 
}  

if ($this->view_mode=='getcounters') {
$this->getcounters();
}  

if ($this->view_mode=='getinfo') {
$this->getinfo2();
}  

if ($this->view_mode=='getpu') {
$this->getpu();
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
    'DEFAULT'=>'192.168.1.1'
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

 ,  array(
    'NAME'=>'APPMILUR_T1', 
    'TITLE'=>'Tariff 1, RUB',
    'TYPE'=>'text',
    'DEFAULT'=>'2.99'

    )
 ,  array(
    'NAME'=>'APPMILUR_T2', 
    'TITLE'=>'Tariff 2, RUB',
    'TYPE'=>'text',
    'DEFAULT'=>'1.42'

    )

	  
	  
,array(	  
'NAME'=>'APPMILUR_INTERVAL', 
    'TITLE'=>'Interval (min):', 
    'TYPE'=>'text',
    'DEFAULT'=>'5'
    )
,   array(
    'NAME'=>'APPMILUR_ENABLE', 
    'TITLE'=>'Enable cycle',
    'TYPE'=>'yesno',
    'DEFAULT'=>'1'
    )
,   array(
    'NAME'=>'APPMILUR_ENABLEDEBUG', 
    'TITLE'=>'Enable debug',
    'TYPE'=>'yesno',
    'DEFAULT'=>'2'
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
//   $every=$this->config['EVERY'];
 $every=SETTINGS_APPMILUR_EVERY;

$cmd_rec = SQLSelectOne("SELECT VALUE FROM milur_config where parametr='LASTCYCLE_TS'");
$latest=$cmd_rec['VALUE'];

   $tdev = time()-$latest;
   $has = $tdev>$every*60;
   if ($tdev < 0) {$has = true;}
   
   if ($has) {  

$enable=SETTINGS_APPMILUR_ENABLE;
if ($enable==1) {$this->getpu();   }


  } 
  }

//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
 function getdata() {
getinfo2();
getpu();
getcounters();
}






//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
 function getpu() {

$enabledebug=SETTINGS_APPMILUR_ENABLEDEBUG;
SQLexec("update milur_config set value='' where parametr='DEBUG'");	    

if ($enabledebug==1) {$debug=date('m/d/Y H:i:s', time())."<br>";}

$host= SETTINGS_APPMILUR_IP;
$port= SETTINGS_APPMILUR_PORT;
//   $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));  // Create Socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket,SOL_SOCKET, SO_RCVTIMEO, array("sec"=>5, "usec"=>0));


        if (socket_connect($socket, $host, $port)) {  //Connect




if ($enabledebug==1) {$debug.='Socket сonnected '.$host.'('. $port.')<br>';}


//$objname='current';         
$objname=SETTINGS_APPMILUR_MODEL;

addClassObject('Milur',$objname);


$t1=gg($objname.".t1");
$t2=gg($objname.".t2");
if (!isset($t1)) {sg($objname.".t1",SETTINGS_APPMILUR_T1);}
if (!isset($t2)) {sg($objname.".t2",SETTINGS_APPMILUR_T1);}

//sg($objname.".lasttimestamp",gg($objname.".timestamp"));                    

         
         
//circle 1
        $sendStr = 'ff 08 00 ff ff ff ff ff ff 4f 2d';  // 16 hexadecimal data
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex

if ($enabledebug==1) {
$debug.="cicle 1 connect<br>";
$debug.=" send:".$sendStr."<br>" ; 
//$debug.=" answer:" . $receiveStr."<br>";   
//$debug.=" answerSTR:" .hex2str($receiveStrHex)."<br>";
$debug.=" answerHEX:" . $receiveStrHex.'<br>';
}
//echo $debug;


         
 
//цикл 3
        $sendStr = 'ff 01 03 00 61';  // P
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 2048);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
        
$phex=substr($receiveStrHex,12,2).substr($receiveStrHex,10,2).substr($receiveStrHex,8,2);
$p=hexdec($phex)/1000;          

if ($enabledebug==1) {
$debug.="cicle 3 P:<br>";
$debug.=     "P:".$sendStr .'<br>'; 
//$debug.=    " answer:" . $receiveStr;   
//$debug.=    " answerSTR:" .hex2str($receiveStrHex);
$debug.=    " answerHEX:" . $receiveStrHex.'<br>';
$debug.=    " answerPHEX:" . $phex.'<br>';   
$debug.=    " answerP:" . $p.'<br>';
}
          if ($p<>0)       sg($objname.".P",round($p));
//          if ($p<>0) sg($objname.".timestamp",time());                     

         
    
     //цикл 4
        $sendStr = 'ff 01 01 81 a0 ';  // U
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 2048);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
       
$uhex=substr($receiveStrHex,12,2).substr($receiveStrHex,10,2).substr($receiveStrHex,8,2);
$u=hexdec($uhex)/1000;       

if ($enabledebug==1) {
$debug.="cicle 4 U:<br>";
$debug.=   "U:".$sendStr.'<br>' ; 
//$debug.=" answer:" . $receiveStr;   
//$debug.=" answerSTR:" .hex2str($receiveStrHex);
$debug.=" answerHEX:" . $receiveStrHex.'<br>';    
$debug.=" answerUHEX:" . $uhex.'<br>';   
$debug.=" answerU:" . $u.'<br>'; 
}
            if ($u<>0)    sg($objname.".U",round($u));        

socket_close($socket);  // Close Socket       
if ($enabledebug==1) {$debug.='Socked closed.<br>';}


        } 
else 
{
if ($enabledebug==1) {$debug='Error create socket '.$host.'('. $port.')';}
}
     socket_close($socket);  // Close Socket       
if ($enabledebug==1) {
SQLexec("update milur_config set value='$debug' where parametr='DEBUG'");	    
sg($objname.'.debug',$debug);}



SQLexec("update milur_config set value=UNIX_TIMESTAMP() where parametr='LASTCYCLE_TS'");		   
SQLexec("update milur_config set value=now() where parametr='LASTCYCLE_TXT'");		   	   

 }




//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
 function getinfo2() {
$enabledebug=SETTINGS_APPMILUR_ENABLEDEBUG;
SQLexec("update milur_config set value='' where parametr='DEBUG'");	    

if ($enabledebug==1) {$debug=date('m/d/Y H:i:s', time())."<br>";}

$host= SETTINGS_APPMILUR_IP;
$port= SETTINGS_APPMILUR_PORT;
//   $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));  // Create Socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket,SOL_SOCKET, SO_RCVTIMEO, array("sec"=>5, "usec"=>0));



        if (socket_connect($socket, $host, $port)) {  //Connect

if ($enabledebug==1) {$debug.='Socket сonnected '.$host.'('. $port.')<br>';}


//$objname='current';         
$objname=SETTINGS_APPMILUR_MODEL;

addClassObject('Milur',$objname);

$t1=gg($objname.".t1");
$t2=gg($objname.".t2");
if (!isset($t1)) {sg($objname.".t1",SETTINGS_APPMILUR_T1);}
if (!isset($t2)) {sg($objname.".t2",SETTINGS_APPMILUR_T1);}

//sg($objname.".lasttimestamp",gg($objname.".timestamp"));                    

         
         
//circle 1
        $sendStr = 'ff 08 00 ff ff ff ff ff ff 4f 2d';  // 16 hexadecimal data
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex

if ($enabledebug==1) {
$debug.="cicle 1 connect<br>";
$debug.=" send:".$sendStr."<br>" ; 
//$debug.=" answer:" . $receiveStr."<br>";   
//$debug.=" answerSTR:" .hex2str2($receiveStrHex)."<br>";
$debug.=" answerHEX:" . $receiveStrHex.'<br>';
}
//echo $debug;


         
       //цикл 2
         
        $sendStr = 'ff 01 20 41 b8';  // модель
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }

            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
if ($enabledebug==1) {
$debug.="cicle 2 model:<br>";
$debug.="send:".$sendStr ."<br>"; 
//$debug.="answer:" . $receiveStr."<br>";   
//$debug.="answerSTR:" .hex2str2($receiveStrHex)."<br>";
$debug.="answerHEX:" . $receiveStrHex.'<br>';
}

if ($receiveStr<>0)        sg($objname.".model",$receiveStr);  
//if ($receiveStr<>0) sg($objname.".timestamp",time());            
 

socket_close($socket);  // Close Socket       
if ($enabledebug==1) {$debug.='Socked closed.<br>';}


        } 
else 
{
if ($enabledebug==1) {$debug='Error create socket '.$host.'('. $port.')';}
}
     socket_close($socket);  // Close Socket       
if ($enabledebug==1) {
SQLexec("update milur_config set value='$debug' where parametr='DEBUG'");	    
sg($objname.'.debug',$debug);}



SQLexec("update milur_config set value=UNIX_TIMESTAMP() where parametr='LASTCYCLE_TS'");		   
SQLexec("update milur_config set value=now() where parametr='LASTCYCLE_TXT'");		   	   

 }


//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////


 function getcounters() {

$enabledebug=SETTINGS_APPMILUR_ENABLEDEBUG;
SQLexec("update milur_config set value='' where parametr='DEBUG'");	    

if ($enabledebug==1) {$debug=date('m/d/Y H:i:s', time())."<br>";}

$host= SETTINGS_APPMILUR_IP;
$port= SETTINGS_APPMILUR_PORT;
//   $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));  // Create Socket

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket,SOL_SOCKET, SO_RCVTIMEO, array("sec"=>5, "usec"=>0));


        if (socket_connect($socket, $host, $port)) {  //Connect

if ($enabledebug==1) {$debug.='Socket сonnected '.$host.'('. $port.')<br>';}


//$objname='current';         
$objname=SETTINGS_APPMILUR_MODEL;

addClassObject('Milur',$objname);

$t1=gg($objname.".t1");
$t2=gg($objname.".t2");
if (!isset($t1)) {sg($objname.".t1",SETTINGS_APPMILUR_T1);}
if (!isset($t2)) {sg($objname.".t2",SETTINGS_APPMILUR_T2);}

//sg($objname.".lasttimestamp",gg($objname.".timestamp"));                    

         
         
//circle 1
        $sendStr = 'ff 08 00 ff ff ff ff ff ff 4f 2d';  // 16 hexadecimal data
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex

if ($enabledebug==1) {
$debug.="cicle 1 connect<br>";
$debug.=" send:".$sendStr."<br>" ; 
//$debug.=" answer:" . $receiveStr."<br>";   
//$debug.=" answerSTR:" .hex2str2($receiveStrHex)."<br>";
$debug.=" answerHEX:" . $receiveStrHex.'<br>';
}
//echo $debug;

         //цикл 5 счетчик общий
        $sendStr = 'ff 01 04 41 a3';  // S0
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
       
//$s0hex=substr($receiveStrHex,12,2).substr($receiveStrHex,10,2).substr($receiveStrHex,8,2);
$s0hex=substr($receiveStrHex,7,8);


$s0=strrev($s0hex)/1000;

//$s0=hexdec($s0hex)/1000;       

//джоули в ват/часы
///1 J = 0.00027777777777778 Wh
//$sk0=$s0*0.00027777777777778;         
// echo  "S0:".$sendStr ; 
if ($enabledebug==1) {
$debug.="cicle 5 S0:<br>";
//$debug.= " answer:" . $receiveStr;   
//$debug.= " answerSTR:" .hex2str2($receiveStrHex);
$debug.= " answerHEX:" . $receiveStrHex.'<br>';    
$debug.= " answerS0HEX:" . $s1hex.'<br>';   
$debug.= " answerS0:" . $s0.'<br>';
$debug.= " answerSK0:" . $sk0.'<br>';                  
}
//           echo '<br>'; 
if ($s0<>0)    {sg($objname.".S0",$s0); sg($objname.".countersts",time());                 }
         
//цикл 6 счетчик тариф 1
        $sendStr = 'ff 01 05 80 63';  // S1
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
//$receiveStrHex=ff 01 05 04 56 59 16 00 21a1       
//$s1hex=substr($receiveStrHex,12,2).substr($receiveStrHex,10,2).substr($receiveStrHex,8,2);
$s1hex=substr($receiveStrHex,7,8);
//$s1=hexdec($s1hex)/1000;       
//$sk1=$s1*0.00027777777777778;         

$s1=strrev($s1hex)/1000;

// echo  "S1:".$sendStr ; 
if ($enabledebug==1) {
$debug.="cicle 6.1 S1:<br>";
//$debug.= " answer:" . $receiveStr;   
//$debug.= " answerSTR:" .hex2str2($receiveStrHex);
$debug.= " answerHEX:" . $receiveStrHex.'<br>';    
$debug.= " answerS1HEX:" . $s1hex.'<br>';   
$debug.= " answerS1:" . $s1.'<br>';
$debug.= " answerSK1:" . $sk1.'<br>';                  
$debug.= " answerS2hex:" . $receiveStrHex.'<br>';         
sg($objname."S1hex",$receiveStrHex);          

}
//echo           '<br>'; 
if ($s1<>0) {  sg($objname.".S1",$s1); sg($objname.".countersts",time());                }         

//if ($s1hex<>0)    sg($objname."S1hex",$s1hex);          
         
//цикл 6 счетчик тариф 2
        $sendStr = 'ff 01 06 c0 62';  // S2
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2);  // The 16 binary data into a set of two arrays
     
                      for ($j = 0; $j <count ($sendStrArray); $j++) {
                              socket_write ($socket, Chr (hexdec ($sendStrArray[$j])));   // by group data transmission
            }
            $receiveStr = "";
            $receiveStr = socket_read($socket, 1024, PHP_BINARY_READ);  // The 2 band data received 
                      $receiveStrHex = bin2hex ($receiveStr);   // the 2 hexadecimal data convert 16 hex
       
//$s2hex=substr($receiveStrHex,12,2).substr($receiveStrHex,10,2).substr($receiveStrHex,8,2);
$s2hex=substr($receiveStrHex,7,8);
//$s2=hexdec($s2hex)/1000;  
$s2=strrev($s2hex)/1000;


//джоули в ват/часы
///1 J = 0.00027777777777778 Wh
   
//$sk2=$s2*0.00027777777777778;
// echo  "S2:".$sendStr ; 
if ($enabledebug==1) {
$debug.="cicle 6.2 S2:<br>";
//$debug.= " answer:" . $receiveStr;   
//$debug.= " answerSTR:" .hex2str2($receiveStrHex);
$debug.= " answerHEX:" . $receiveStrHex.'<br>';    
$debug.= " answerS2HEX:" . $s2hex.'<br>';   
$debug.= " answerS2:" . $s2.'<br>';
$debug.= " answerSK2:" . $sk2.'<br>';         
$debug.= " answerS2hex:" . $receiveStrHex.'<br>';         
sg($objname."S2hex",$receiveStrHex);          
}
//echo '<br>'; 

if ($s2<>0)    {sg($objname.".S2",$s2); sg($objname.".countersts",time());                 }
//if ($s2hex<>0) sg($objname.".S2hex",$s2hex);            


socket_close($socket);  // Close Socket       
if ($enabledebug==1) {$debug.='Socked closed.<br>';}


        } 
else 
{
if ($enabledebug==1) {$debug='Error create socket '.$host.'('. $port.')';}
}
     socket_close($socket);  // Close Socket       
if ($enabledebug==1) {
SQLexec("update milur_config set value='$debug' where parametr='DEBUG'");	    
sg($objname.'.debug',$debug);}

}
	
function processSubscription($event_name, $details='') {
  if ($event_name=='HOURLY') {
		$this->getcounters();
  }
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
subscribeToEvent($this->name, 'HOURLY');
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

$ChangeT1='
$objn=$this->object_title;
//$currentcount=$this->getProperty("S1");
$currentcount=gg($objn.".S1");
$lasttotal=gg($objn.".lasts1");

SQLUpdate("objects", array("ID"=>$this->id, "DESCRIPTION"=>gg($objn.".FIO")." P:".gg($objn.".P")." U:".gg($objn.".U")." ".gg("sysdate")."  ".gg("timenow"))); 
if (IsSet($lasttotal) and ($lasttotal<>0) )
{
$rashod=$currentcount-$lasttotal;
sg($objn.".rashodt1",$rashod);}


sg($objn.".lasttimestamp", time());
sg($objn.".lasts1", $currentcount);';
$ChangeT2='
$objn=$this->object_title;
//$currentcount=$this->getProperty("S2");
$currentcount=gg($objn.".S2");
$lasttotal=gg($objn.".lasts2");

SQLUpdate("objects", array("ID"=>$this->id, "DESCRIPTION"=>gg($objn.".FIO")." P:".gg($objn.".PvT")." U:".gg($objn.".U")." ".gg("sysdate")."  ".gg("timenow"))); 
if (IsSet($lasttotal) and ($lasttotal<>0) )
{
$rashod=$currentcount-$lasttotal;
sg($objn.".rashodt2",$rashod);}


sg($objn.".lasttimestamp", time());
sg($objn.".lasts2", $currentcount);
';



$onChange='
$objn=$this->object_title;
$i=gg($objn.".P")/gg($objn.".U");
sg($objn.".I",  round($i,2));
';

setGlobal('cycle_milurAutoRestart','1');	 	 
$classname='Milur';
addClass($classname); 
addClassMethod($classname,'ChangeT1',$ChangeT1);	 
addClassMethod($classname,'ChangeT2',$ChangeT2);	 
addClassMethod($classname,'onChange',$onChange);	 




$prop_id=addClassProperty($classname, 'I', 100);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Сила тока'; //   <-----------
SQLUpdate('properties',$property);} 


$prop_id=addClassProperty($classname, 'P', 100);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Мгновенная потребляемая мощность'; //   <-----------
SQLUpdate('properties',$property);} 

$prop_id=addClassProperty($classname, 'S1', 1000);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Значение счетчика тариф 1'; //   <-----------
$property['ONCHANGE']="ChangeT1"; //	   	       
SQLUpdate('properties',$property);} 

$prop_id=addClassProperty($classname, 'S2', 1000);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Значение счетчика тариф 2'; //   <-----------
$property['ONCHANGE']="ChangeT2"; //	   	       
SQLUpdate('properties',$property);} 

$prop_id=addClassProperty($classname, 'S0', 1000);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Значение счетчика, общее'; //   <-----------
SQLUpdate('properties',$property);} 



$prop_id=addClassProperty($classname, 'timestamp', 1);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='timestamp'; //   <-----------
SQLUpdate('properties',$property);} 

$prop_id=addClassProperty($classname, 'U', 60);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Мгновенное напряжение'; //   <-----------
$property['ONCHANGE']="OnChange"; //	   	       
SQLUpdate('properties',$property);} 



$prop_id=addClassProperty($classname, 'rashodt1', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Израсходовано по тарифу 1'; //   <-----------
SQLUpdate('properties',$property); }

$prop_id=addClassProperty($classname, 'rashodt2', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Израсходовано по тарифу 1'; //   <-----------
SQLUpdate('properties',$property); }




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
	

function strToHex2($string){
    $hex='';
    for ($i=0; $i < strlen($string); $i++){
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}


function hexToStr2($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

function hex2str2($hex) {
    $str = '';
    for($i=0;$i<strlen($hex);$i+=2) $str .= chr(hexdec(substr($hex,$i,2)));
    return $str;
}	


/*
*
* TW9kdWxlIGNyZWF0ZWQgSmFuIDAzLCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/


