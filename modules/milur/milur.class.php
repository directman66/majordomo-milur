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
  $this->title="milur";
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
 $this->getConfig();
 $out['API_URL']=$this->config['API_URL'];
 if (!$out['API_URL']) {
  $out['API_URL']='http://';
 }
 $out['API_KEY']=$this->config['API_KEY'];
 $out['API_USERNAME']=$this->config['API_USERNAME'];
 $out['API_PASSWORD']=$this->config['API_PASSWORD'];
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
 if ($this->data_source=='milur_devices' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_milur_devices') {
   $this->search_milur_devices($out);
  }
  if ($this->view_mode=='edit_milur_devices') {
   $this->edit_milur_devices($out, $this->id);
  }
  if ($this->view_mode=='delete_milur_devices') {
   $this->delete_milur_devices($this->id);
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
/**
* milur_devices edit/add
*
* @access public
*/
 function edit_milur_devices(&$out, $id) {
  require(DIR_MODULES.$this->name.'/milur_devices_edit.inc.php');
 }
/**
* milur_devices delete record
*
* @access public
*/
 function delete_milur_devices($id) {
  $rec=SQLSelectOne("SELECT * FROM milur_devices WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM milur_devices WHERE ID='".$rec['ID']."'");
 }
 function propertySetHandle($object, $property, $value) {
  $this->getConfig();
   $table='milur_devices';
   $properties=SQLSelect("SELECT ID FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
     //to-do
    }
   }
 }
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
   
   setGlobal('cycle_milurAutoRestart','1');	 	 
$classname='Milur';
addClass($classname); 


$OnChange='
$i=gg("current.P")/gg("current.U");
sg("current.I",  round($i,2));
SQLUpdate(\'objects\', array("ID"=>$this->id, "DESCRIPTION"=>gg(\'sysdate\').' '.gg(\'timenow\'))); 

//расчет потребленного электричества с момента последней проверки
//получаем дату предыдущей проверки
//$laststamp=getHistoryValue($this->getProperty(\'timestamp\'),);
$t1=gg("current.t1");
$t2=gg("current.t2");
$laststamp=gg(\'current.lasttimestamp\');
$diff=(gmdate(\'i\',trim(time()-$laststamp)));
$pattern = "|\b[0]+([1-9][\d]*)|is"; 
$diff2= preg_replace($pattern, "\\1", $diff); 
sg("current.proshlo_min",  $diff2);

//получаем последннее значение мощности
//$lastph=getHistoryMax($this->getProperty(\'P\'));
//$lastph=getHistoryMax("current.P");
$lastph=getHistoryValue("current.P", $laststamp-1,$laststamp+1);
//переведем в ват в мин
$lastpm=$lastph*0.0166667;
 //за последний период в минутах было потреблено ватт
$potrebleno=$lastpm*$diff2;
sg("current.potrebleno_w",  $potrebleno);
sg("current.lastph",  $lastph);
sg("current.lastpm",  $lastpm);
$time=date("H:i:s");
//sg("current.time",  $time);

$date_min = new DateTime("7:00"); // минимальное значение времени
$date_max = new DateTime("23:00"); // максимальное значение времени
$date_now = new DateTime($date); // текущее значение времени
// Проверяем, находится ли $date_now в диапазоне
if ($date_now >= $date_min && $date_now <= $date_max) 
{$tarif=1; 
sg("current.potrebleno_w_t1",  $potrebleno);
sg("current.potrebleno_w_t1_sum",  gg("current.potrebleno_w_t1_sum")+$potrebleno); 
//$st=$t1/16.6667;
 $st=$t1/1000;
sg("current.potrebleno_w_t1_rub",  $potrebleno*$st);
sg("current.potrebleno_w_rub",  $potrebleno*$st); 
} else
{$tarif=2;
sg("current.potrebleno_w_t2",  $potrebleno);
sg("current.potrebleno_w_t2_sum",  gg("current.potrebleno_w_t2_sum")+$potrebleno);  
//$st=$t2/16.6667;
 $st=$t2/1000;
sg("current.potrebleno_w_t2_rub",  $potrebleno*$st);
sg("current.potrebleno_w_rub",  $potrebleno*$st);
 
}
sg("current.tarif",  $tarif);
';

addClassMethod($classname,'OnChange',$OnChange);	 

$prop_id=addClassProperty($classname, 'I', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Сила тока, А'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'P', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Мгновенная мощность, Вт'; //   <-----------
$property['ONCHANGE']="OnChange"; //	   	       
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'potrebleno_w', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Израсходовано ват с момента последнего считывания'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'potrebleno_w_rub', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Израсходовано в руб.  с момента последнего считывания'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'potrebleno_w_t1', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Израсходовано в тарифе 1 с момента последнего считывания'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'potrebleno_w_t2', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Израсходовано в тарифе 2 с момента последнего считывания'; //   <-----------
SQLUpdate('properties',$property); } 	 

$prop_id=addClassProperty($classname, 'potrebleno_w_t1_rub', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Израсходовано в тарифе 2 с момента последнего считывания'; //   <-----------
SQLUpdate('properties',$property); } 	 

$prop_id=addClassProperty($classname, 'potrebleno_w_t2_rub', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Израсходовано в тарифе 2 с момента последнего считывания'; //   <-----------
SQLUpdate('properties',$property); } 	 

$prop_id=addClassProperty($classname, 'proshlo_min', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Отрезок времени в мин с последнего снятия показаний'; //   <-----------
SQLUpdate('properties',$property); } 


$prop_id=addClassProperty($classname, 'S1', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Счетчик 1'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'S2', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Счетчик 2'; //   <-----------
SQLUpdate('properties',$property); } 	 

$prop_id=addClassProperty($classname, 'U', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Напряжение'; //   <-----------
SQLUpdate('properties',$property); } 	 

$prop_id=addClassProperty($classname, 'timestamp ', 365);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Время в unx'; //   <-----------
SQLUpdate('properties',$property); } 	 


  $data = <<<EOD
 milur_config: parametr varchar(300)
 milur_config:  value varchar(100)  
EOD;
   parent::dbInstall($data);
	 
	 
		
$par['parametr'] = 'LASTCYCLE_TS';
$par['value'] = "0";		 
SQLInsert('milur_config', $par);						
		
$par['parametr'] = 'LASTCYCLE_TXT';
$par['value'] = "0";		 
SQLInsert('milur_config', $par);						
   
   
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgSmFuIDAzLCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
