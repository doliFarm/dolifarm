<?php
/* Copyright (C) 2022 Alice Adminson <luigi.grillo@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    dolifarm/lib/dolifarm.lib.php
 * \ingroup dolifarm
 * \brief   Library files with common functions for DoliFarm
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function dolifarmAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("dolifarm@dolifarm");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/dolifarm/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	$head[$h][0] = dol_buildpath("/dolifarm/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$head[$h][2] = 'myobject_extrafields';
	$h++;

	$head[$h][0] = dol_buildpath("/dolifarm/admin/setupcropslists.php", 1);
	$head[$h][1] = $langs->trans("CropsList");
	$head[$h][2] = 'cropslist';
	$h++;
	
	$head[$h][0] = dol_buildpath("/dolifarm/admin/setuptreatments.php", 1);
	$head[$h][1] = $langs->trans("Treatments");
	$head[$h][2] = 'treatments';
	$h++;
	

	$head[$h][0] = dol_buildpath("/dolifarm/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@dolifarm:/dolifarm/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@dolifarm:/dolifarm/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'dolifarm@dolifarm');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'dolifarm@dolifarm', 'remove');

	return $head;
}


function fetchobj($table, $sortkey='rowid', $max=5)
 {
	global $conf,$db;

	$arrayobj = false;

	$sql = "SELECT * FROM ".MAIN_DB_PREFIX.$table;
	// $sql .= " WHERE ".$field." = '".$this->db->escape($key)."'";
	$sql .= " ORDER BY ".$sortkey." ASC"; 
	$sql .= " LIMIT ".$max;

	$resql = $db->query($sql);
	$arrayobj = NULL;
	if ($resql) {
		 $arrayobj = array();
		 dol_syslog($sql, LOG_DEBUG);
		 for ($i=0;$i< $db->num_rows($resql);$i++) {
			$arrayobj[$i] = $db->fetch_array($resql);
		 }
	} else {
		 dol_syslog('errrrrr '.$sql, LOG_DEBUG);
	}

	 return $arrayobj;
 }



function farm_typent($tcode='TE_FARM'){
	global $db;
    $code = NULL;	
	  $sqlfarmtype = "SELECT t.id, t.libelle, t.code FROM ".MAIN_DB_PREFIX."c_typent as t";
	  $sqlfarmtype .= " WHERE t.code = '".$tcode."'";
	  $resqlfarmtype = $db->query($sqlfarmtype);
	  if ($resqlfarmtype) {
		  if ($db->num_rows($resqlfarmtype)) {
			 $farmtype = $db->fetch_object($resqlfarmtype);
			 $code = $farmtype->id;
		}
	  }
	return $code;
}

/**
	 * Action executed by scheduler
	 * CAN BE A CRON TASK. In such a case, parameters come from the schedule job setup field 'Parameters'
	 * Use public function doScheduledJob($param1, $param2, ...) to get parameters
	 * @param   int  	intervall between the expiration date and today date
	 * @param   int   	which certificate to check (0=all, 1=organic,...) - not used up to now
	 * @return	array	list of the farm rowid whom certificate is going to expire within the specified intervall 
	 */
function listExpiringCertificate($interval=10,$type=0) {
		global $conf, $langs, $db;
		$now = dol_now();
		require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';	
		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
		$farm = new Societe($db);
		$extrafields = new ExtraFields($db);
		$extrafields->fetch_name_optionals_label($farm->table_element);
		
		// Select the Farm
		$sql = "SELECT s.rowid";
		$sql .= " FROM ".MAIN_DB_PREFIX."societe as s";
		$sql .= " WHERE s.fk_typent=".farm_typent();   // TODO(5) Search for several language
		$resql = $db->query($sql);
		if (!$resql) {
			dol_print_error($db);
			$error = -1;
		}
		$num = $db->num_rows($resql);  // Number of Farms
		$i = 0;
		$j=0;
		$farmList=array();
		while ($i < $num ) {
			$obj = $db->fetch_object($resql);
			$farm->fetch_optionals($obj->rowid);
			$d = num_between_day($now,$farm->array_options['options_certificateexpirationdate']);
			if ($d < $interval) {
			   $farmList[$j]["farmid"] = $obj->rowid;
			   $farmList[$j]["expirationDays"] = $d;
			   $farmList[$j]["bodyCA"] = $farm->array_options['options_bodyca'];
			   $farmList[$j]["expirationdate"] = $farm->array_options['options_certificateexpirationdate'];
			   $j++;
			}
			$i++;
			dol_syslog("****listExpiringCertificate: Farm: ".$farm->nom." days: ".$d,LOG_DEBUG);
		}
		if ($j) {
			/* TO DO avoid the same messages 
			if (!isset($_COOKIE["DOLHIDEMESSAGE"."certificateexpirationdate"])) {
				 setcookie("DOLHIDEMESSAGE"."certificateexpirationdate", "certificateexpirationdate", time()+60*60*24*1);
			}  */
			$datecol = array_column($farmList, 'expirationdate');
			array_multisort($datecol, SORT_ASC, $farmList);
			setEventMessages($langs->trans("CertificateExpiration",$j,$interval,DOL_URL_ROOT."/custom/dolitrace/farms.php?sortfield=ef.certificateexpirationdate"), null, 'mesgs',"DOLHIDEMESSAGE".'certificateexpirationdate',1);
		}
		return $farmList;
	}
	
		
	function bodyCA($id,$soc){
		global  $conf, $langs,$db;
		$extrafields = new ExtraFields($db);
		// fetch optionals attributes and labels
		$extrafields->fetch_name_optionals_label($soc->table_element);		
		$soc->fetch($id);
		$sql = "SELECT t.param FROM ".MAIN_DB_PREFIX."extrafields as t";
		$sql.= " WHERE t.name = 'bodyca'";
		$res = $db->query($sql);
		if ($res) {
		  if ($db->num_rows($res)) {
				 $f = $db->fetch_object($res);
		  }
		}
		$data = unserialize($f->param);
		return $data['options'][$id];
	}
	
	function filePublicShare($f){
		/*
		global  $conf, $langs,$db;
		
		
		  $sql = 'SELECT * FROM '..'_ecm_files WHERE filename LIKE $filearray[0]["fullname"]';
                
				$sql = 'UPDATE '..'_ecm_files SET share = '..' WHERE '..'_ecm_files.rowid ="'.$filenameRowId.'"';

		$extrafields = new ExtraFields($db);
		// fetch optionals attributes and labels
		$extrafields->fetch_name_optionals_label($soc->table_element);		
		$soc->fetch($id);
		$sql = "SELECT t.param FROM ".MAIN_DB_PREFIX."extrafields as t";
		$sql.= " WHERE t.name = 'bodyca'";
		$res = $db->query($sql);
		if ($res) {
		  if ($db->num_rows($res)) {
				 $f = $db->fetch_object($res);
		  }
		}
		$data = unserialize($f->param);
		return $data['options'][$id];
		*/
	}
