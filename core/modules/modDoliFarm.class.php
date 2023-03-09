<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2020  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2022 Luigi Grillo - luigi.grillo@gmail.com (http://luigigrillo.com)
 * Last update: 05/01/2022 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   dolifarm     Module DoliFarm
 *  \brief      DoliFarm module descriptor.
 *
 *  \file       htdocs/dolifarm/core/modules/modDoliFarm.class.php
 *  \ingroup    dolifarm
 *  \brief      Description and activation file for module DoliFarm
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';
include_once DOL_DOCUMENT_ROOT.'/custom/dolifarm/lib/dolifarm.lib.php';

/**
 *  Description and activation class for module DoliFarm
 */
class modDoliFarm extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 500000; // TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'dolifarm';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "doliFARM";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModuleDoliFarmName' not found (DoliFarm is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// Module description, used if translation string 'ModuleDoliFarmDesc' not found (DoliFarm is name of module).
		$this->description = "DoliFarmDescription";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "DoliFarmDescription";

		// Author
		$this->editor_name = 'Luigi Grillo';
		$this->editor_url = 'https://www.dolifarm.com';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Url to the file with your last numberversion of this module
		//$this->url_last_version = 'http://www.example.com/versionmodule.txt';

		// Key used in llx_const table to save module status enabled/disabled (where DOLIFARM is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		$this->picto = 'dolifarm@dolifarm';

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 0,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 0,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(
				   '/dolifarm/css/dolifarm.css',
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				//   '/dolifarm/js/dolifarm.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			 'hooks' => array(
			//	   'data' => array(
			//	       'thirdpartycard',
				//       'hookcontext2',
			//	   ),
				  'entity' => '0',
			),
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/dolifarm/temp","/dolifarm/subdir");
		$this->dirs = array("/dolifarm/temp");

		// Config pages. Put here list of php page, stored into dolifarm/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@dolifarm");

		// Dependencies
		// A condition to hide module
		$this->hidden = false;
		// List of module class names as string that must be enabled if this module is enabled. Example: array('always1'=>'modModuleToEnable1','always2'=>'modModuleToEnable2', 'FR1'=>'modModuleToEnableFR'...)
		$this->depends = array();
		$this->requiredby = array(); // List of module class names as string to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array(); // List of module class names as string this module is in conflict with. Example: array('modModuleToDisable1', ...)

		// The language file dedicated to your module
		$this->langfiles = array("dolifarm@dolifarm");

		// Prerequisites
		$this->phpmin = array(5, 6); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(11, -3); // Minimum version of Dolibarr required by module

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		//$this->automatic_activation = array('FR'=>'DoliFarmWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(1 => array('DOLIFARM_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
		//                             2 => array('DOLIFARM_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
		// );
		$this->const = array();

		// Some keys to add into the overwriting translation tables
		/*$this->overwrite_translation = array(
			'en_US:ParentCompany'=>'Parent company or reseller',
			'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
		)*/

		if (!isset($conf->dolifarm) || !isset($conf->dolifarm->enabled)) {
			$conf->dolifarm = new stdClass();
			$conf->dolifarm->enabled = 0;
		}

		// Array to add new pages in new tabs
		$this->tabs = array();
		// Example:
		// $this->tabs[] = array('data'=>'objecttype:+tabname1:Title1:mylangfile@dolifarm:$user->rights->dolifarm->read:/dolifarm/mynewtab1.php?id=__ID__');  					// To add a new tab identified by code tabname1
		// $this->tabs[] = array('data'=>'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@dolifarm:$user->rights->othermodule->read:/dolifarm/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
		// $this->tabs[] = array('data'=>'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
		//
		// Where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view

		// Dictionaries
		$this->dictionaries = array();
		/* Example:
		$this->dictionaries=array(
			'langs'=>'dolifarm@dolifarm',
			// List of tables we want to see into dictonnary editor
			'tabname'=>array(MAIN_DB_PREFIX."table1", MAIN_DB_PREFIX."table2", MAIN_DB_PREFIX."table3"),
			// Label of tables
			'tablib'=>array("Table1", "Table2", "Table3"),
			// Request to select fields
			'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),
			// Sort order
			'tabsqlsort'=>array("label ASC", "label ASC", "label ASC"),
			// List of fields (result of select to show dictionary)
			'tabfield'=>array("code,label", "code,label", "code,label"),
			// List of fields (list of fields to edit a record)
			'tabfieldvalue'=>array("code,label", "code,label", "code,label"),
			// List of fields (list of fields for insert)
			'tabfieldinsert'=>array("code,label", "code,label", "code,label"),
			// Name of columns with primary key (try to always name it 'rowid')
			'tabrowid'=>array("rowid", "rowid", "rowid"),
			// Condition to show each dictionary
			'tabcond'=>array($conf->dolifarm->enabled, $conf->dolifarm->enabled, $conf->dolifarm->enabled)
		);
		*/
		
		$this->dictionaries=array(
			'langs'=>'dolifarm@dolifarm',
			// List of tables we want to see into dictonnary editor
			'tabname'=>array(MAIN_DB_PREFIX."dolifarm_dictionary"),
			// Label of tables
			'tablib'=>array("Dolifarm_dictionary"),
			// Request to select fields
			'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'dolifarm_dictionary as f'),
			// Sort order
			'tabsqlsort'=>array("label ASC"),
			// List of fields (result of select to show dictionary)
			'tabfield'=>array("code,label"),
			// List of fields (list of fields to edit a record)
			'tabfieldvalue'=>array("code,label"),
			// List of fields (list of fields for insert)
			'tabfieldinsert'=>array("code,label"),
			// Name of columns with primary key (try to always name it 'rowid')
			'tabrowid'=>array("rowid"),
			// Condition to show each dictionary
			'tabcond'=>array($conf->dolifarm->enabled)
		);

		// Boxes/Widgets
		// Add here list of php file(s) stored in dolifarm/core/boxes that contains a class to show a widget.
		$this->boxes = array(
			  0 => array(
			      'file' => 'box_harvests.php@dolifarm',
			      'note' => 'Last Harvests info',
			      'enabledbydefaulton' => 'dolifarm',
			  ),
			  1 => array(
			      'file' => 'box_certificates.php@dolifarm',
			      'note' => 'Last Harvests info',
			      'enabledbydefaulton' => 'dolifarm',
			  ),
			  2 => array(
			      'file' => 'box_manifacturingorder.php@dolifarm',
			      'note' => 'Last Harvests info',
			      'enabledbydefaulton' => 'dolifarm',
			  ),
 			  3 => array(
			      'file' => 'box_cropplans.php@dolifarm',
			      'note' => 'Last Harvests info',
			      'enabledbydefaulton' => 'dolifarm',
			  )

			//  ...
		);

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		$this->cronjobs = array(
			  0 => array(
			      'label' => 'DoliFarm checkLicense',
			      'jobtype' => 'method',
			      'class' => '/custom/dolifarm/class/certificates.class.php',
			      'objectname' => 'Certificate',
			      'method' => 'doScheduledJob',
			      'parameters' => '',
			      'comment' => 'Comment',
			      'frequency' => 2,
			      'unitfrequency' => 86400,
			      'status' => 1,
			      'test' => '$conf->dolifarm->enabled',
			      'priority' => 50,
			  ),
		);
		// Example: $this->cronjobs=array(
		//    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'$conf->dolifarm->enabled', 'priority'=>50),
		//    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'$conf->dolifarm->enabled', 'priority'=>50)
		// );

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions
		/* BEGIN MODULEBUILDER PERMISSIONS */
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Read objects of DoliFarm'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->dolifarm->myobject->read)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/Update objects of DoliFarm'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->dolifarm->myobject->write)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Delete objects of DoliFarm'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->dolifarm->myobject->delete)
		$r++;
		/* END MODULEBUILDER PERMISSIONS */

		// Main menu entries to add
		$this->menu = array();
		$r = 0;
		// Add here entries to declare new menus
		/* BEGIN MODULEBUILDER TOPMENU */
		$this->menu[$r++] = array(
			'fk_menu'=>'', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'top', // This is a Top menu entry
			'titre'=>'Traceability',
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'',
			'url'=>'/dolifarm/dolifarm_dashboard.php',
			'langs'=>'dolifarm@dolifarm', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000 + $r,
			'enabled'=>'$conf->dolifarm->enabled', // Define condition to show or hide menu entry. Use '$conf->dolifarm->enabled' if entry must be visible if module is enabled.
			'perms'=>'1', // Use 'perms'=>'$user->rights->dolifarm->myobject->read' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2, // 0=Menu for internal users, 1=external users, 2=both
		);
		/* END MODULEBUILDER TOPMENU */
		
		/* **** doliFARM DASHBOARD Menu **** */
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>$langs->trans('Dashboard'),
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolifarmOBJ',
			'url'=>'/dolifarm/dolifarm_dashboard.php',
			'langs'=>'dolicert@dolicert',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolifarm->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled.
			'perms'=>'1',   // '$user->rights->dolifarm->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);	
		/* **** doliTRACE  Menu **** */
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>$langs->trans('doliTRACE'),
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolitraceOBJ',
			'url'=>'/dolitrace/index.php',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolitrace->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->dolitrace->harvests->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitraceOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('FarmsNetwork'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolitraceFarms',
			'url'=>'/dolitrace/farms.php',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolifarm->enabled && $conf->global->DOLIFARM_MULTIFARMS',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->harvests->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitraceFarms',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('NewFarm'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'societe/card.php?action=create&type=f&typent_id='.farm_typent(),
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolifarm->enabled && $conf->global->DOLIFARM_MULTIFARMS',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->harvests->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitraceOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('CropsPlan'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolitraceCropPlans',
			'url'=>'/dolitrace/cropplans.php',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolitrace->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->cropplans->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		); 
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitraceCropPlans',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('NewCropPlan'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolitraceCropPlans',
			'url'=>'/dolitrace/cropplans_card.php?action=create',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolitrace->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->cropplans->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		); 

		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitraceOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('Harvests'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolitraceHarvest',
			'url'=>'/dolitrace/harvests.php',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolitrace->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->harvests->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		); 		
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitraceHarvest',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('NewHarvest'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolitrace/harvests_card.php?action=create',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolitrace->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->harvests->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		); 
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitraceOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('Operations'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolitraceOperations',
			'url'=>'/dolitrace/harvests.php',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolitrace->enabled and !$conf->global->DOLITRACE_SIMPLIFIED',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->harvests->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		); 		
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitraceOperations',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('NewOperation'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolitrace/harvests_card.php?action=create',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolitrace->enabled and !$conf->global->DOLITRACE_SIMPLIFIED',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->harvests->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		); 
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitraceOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('Plots'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolitracePlot',
			'url'=>'/dolitrace/plots.php',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolitrace->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->harvests->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		); 
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitracePlot',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('NewPlot'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolitrace/plots_card.php?action=create',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolitrace->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->harvests->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		/*
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolitraceOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('Traceability'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolitrace/traceability.php',
			'langs'=>'dolitrace@dolitrace',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolitrace->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolitrace->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);*/
		
		
		/* **** doliLOGISTIC DASHBOARD Menu **** */
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>$langs->trans('doliLOGISTIC'),
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolilogisticOBJ',
			'url'=>'/dolilogistic/index.php',
			'langs'=>'dolilogistic@dolilogistic',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolilogistic->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->dolilogistic->dolilogistic->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);		
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolilogisticOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('PricesListGenerator'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/doliprices/index.php',
			'langs'=>'dolilogistic@dolilogistic',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolilogistic->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolilogistic->dolilogistic->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);		
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolilogisticOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('ProductionOrders'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicertProductionOrder',
			'url'=>'/dolilogistic/productionorders_list.php',
			'langs'=>'dolilogistic@dolilogistic',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolilogistic->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolilogistic->dolilogistic->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolicertProductionOrder',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('NewProductionOrder'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolilogistic/productionorders_card.php?action=create',
			'langs'=>'dolilogistic@dolilogistic',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolilogistic->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolilogistic->dolilogistic->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolilogisticOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('PallettLoadSimulationTool'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolilogistic/pallett_load_simulation_tool.php',
			'langs'=>'dolilogistic@dolilogistic',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolilogistic->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolilogistic->dolilogistic->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolilogisticOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('CMRGenerator'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolilogistic/cmr_generator.php',
			'position'=>1000+$r,
			'enabled'=>'$conf->dolilogistic->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolilogistic->dolilogistic->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		
		/* **** doliCERT  Menu **** */
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>$langs->trans('doliCERT'),
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicertOBJ',
			'url'=>'/dolicert/index.php',
			'langs'=>'dolicert@dolicert',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolicert->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->dolicert->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolicertOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('Certificates'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolicert/certificates.php',
			'langs'=>'dolicert@dolicert',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolicert->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$conf->dolicert->enabled', // '$user->rights->dolicert->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolicertOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('Workbook'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolicert/workbook.php',
			'langs'=>'dolicert@dolicert',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolicert->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$conf->global->DOLICERT_WORKBOOK', // '$user->rights->dolicert->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolicertOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('SupplierList'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/societe/list.php?type=f&mainmenu=dolifarm&leftmenu=',
			'langs'=>'dolicert@dolicert',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolicert->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$conf->global->DOLICERT_SUPPLIERS', // '$user->rights->dolicert->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolicertOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('ComplaintsManagement'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolicert/complaints_management.php',
			'langs'=>'dolicert@dolicert',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolicert->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$conf->global->DOLICERT_COMPLIANTS', // '$user->rights->dolicert->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolicertOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('SalesRegister'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolicert/sales_register.php',
			'langs'=>'dolicert@dolicert',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolicert->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$conf->global->DOLICERT_SALES', // '$user->rights->dolicert->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolicertOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('PurchaseRegister'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolicert/purchase_register.php',
			'langs'=>'dolicert@dolicert',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolicert->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$conf->global->DOLICERT_COMPLIANTS', // '$user->rights->dolicert->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolicertOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('RawMaterials'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolicert/raw_materials.php',
			'langs'=>'dolicert@dolicert',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolicert->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$conf->global->DOLICERT_COMPLIANTS', // '$user->rights->dolicert->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		/*
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=dolicertOBJ',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>$langs->trans('CertificatesList'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicert_myobject_list',
			'url'=>'/dolicert/certificates.php',
			'langs'=>'dolicert@dolicert',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolicert->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>$conf->global->DOLICERT_CERTIFICATES, // '$user->rights->dolicert->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		*/
		
		/* **** doliCOMMERCE DASHBOARD Menu **** */
		
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>$langs->trans('doliCOMMERCE'),
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolicommerceOBJ',
			'url'=>'/dolicommerce/dolicommerceindex.php',
			'langs'=>'dolicommerce@dolicommerce',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolicommerce->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolicert->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->dolicommerce->myobject->read',			                // Use 'perms'=>'$user->rights->dolicert->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		
		/* BEGIN MODULEBUILDER LEFTMENU MYOBJECT
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'MyObject',
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'myobject',
			'url'=>'/dolifarm/dolifarmindex.php',
			'langs'=>'dolifarm@dolifarm',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolifarm->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolifarm->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->dolifarm->myobject->read',			                // Use 'perms'=>'$user->rights->dolifarm->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'List_MyObject',
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolifarm_myobject_list',
			'url'=>'/dolifarm/myobject_list.php',
			'langs'=>'dolifarm@dolifarm',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolifarm->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolifarm->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolifarm->myobject->read',			                // Use 'perms'=>'$user->rights->dolifarm->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=dolifarm,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'New_MyObject',
			'mainmenu'=>'dolifarm',
			'leftmenu'=>'dolifarm_myobject_new',
			'url'=>'/dolifarm/myobject_card.php?action=create',
			'langs'=>'dolifarm@dolifarm',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->dolifarm->enabled',  // Define condition to show or hide menu entry. Use '$conf->dolifarm->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->dolifarm->myobject->write',			                // Use 'perms'=>'$user->rights->dolifarm->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		END MODULEBUILDER LEFTMENU MYOBJECT */
		// Exports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER EXPORT MYOBJECT */
		/*
		$langs->load("dolifarm@dolifarm");
		$this->export_code[$r]=$this->rights_class.'_'.$r;
		$this->export_label[$r]='MyObjectLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_icon[$r]='myobject@dolifarm';
		// Define $this->export_fields_array, $this->export_TypeFields_array and $this->export_entities_array
		$keyforclass = 'MyObject'; $keyforclassfile='/dolifarm/class/myobject.class.php'; $keyforelement='myobject@dolifarm';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		//$this->export_fields_array[$r]['t.fieldtoadd']='FieldToAdd'; $this->export_TypeFields_array[$r]['t.fieldtoadd']='Text';
		//unset($this->export_fields_array[$r]['t.fieldtoremove']);
		//$keyforclass = 'MyObjectLine'; $keyforclassfile='/dolifarm/class/myobject.class.php'; $keyforelement='myobjectline@dolifarm'; $keyforalias='tl';
		//include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		$keyforselect='myobject'; $keyforaliasextra='extra'; $keyforelement='myobject@dolifarm';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$keyforselect='myobjectline'; $keyforaliasextra='extraline'; $keyforelement='myobjectline@dolifarm';
		//include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$this->export_dependencies_array[$r] = array('myobjectline'=>array('tl.rowid','tl.ref')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		//$this->export_special_array[$r] = array('t.field'=>'...');
		//$this->export_examplevalues_array[$r] = array('t.field'=>'Example');
		//$this->export_help_array[$r] = array('t.field'=>'FieldDescHelp');
		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'myobject as t';
		//$this->export_sql_end[$r]  =' LEFT JOIN '.MAIN_DB_PREFIX.'myobject_line as tl ON tl.fk_myobject = t.rowid';
		$this->export_sql_end[$r] .=' WHERE 1 = 1';
		$this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('myobject').')';
		$r++; */
		/* END MODULEBUILDER EXPORT MYOBJECT */

		// Imports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER IMPORT MYOBJECT */
		/*
		 $langs->load("dolifarm@dolifarm");
		 $this->export_code[$r]=$this->rights_class.'_'.$r;
		 $this->export_label[$r]='MyObjectLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		 $this->export_icon[$r]='myobject@dolifarm';
		 $keyforclass = 'MyObject'; $keyforclassfile='/dolifarm/class/myobject.class.php'; $keyforelement='myobject@dolifarm';
		 include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		 $keyforselect='myobject'; $keyforaliasextra='extra'; $keyforelement='myobject@dolifarm';
		 include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		 //$this->export_dependencies_array[$r]=array('mysubobject'=>'ts.rowid', 't.myfield'=>array('t.myfield2','t.myfield3')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		 $this->export_sql_start[$r]='SELECT DISTINCT ';
		 $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'myobject as t';
		 $this->export_sql_end[$r] .=' WHERE 1 = 1';
		 $this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('myobject').')';
		 $r++; */
		/* END MODULEBUILDER IMPORT MYOBJECT */
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		//$result = $this->_load_tables('/install/mysql/tables/', 'dolifarm');
		$result = $this->_load_tables('/dolifarm/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}
		
		// Add Dictionary Entry  // TODO Make a dolibarr PR for adding AUTO INCREMENT to the llx_c_typent table
		include_once DOL_DOCUMENT_ROOT.'/core/class/ctypent.class.php';
		$dictionary = new Ctypent($this->db);
		$dictionary->code = 'TE_FARM';
		$dictionary->libelle = 'Farm';
		$dictionary->active = 1;
		$dictionary->create($user);
		

		// Create extrafields during init
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		//$result1=$extrafields->addExtraField('dolifarm_myattr1', "New Attr 1 label", 'boolean', 1,  3, 'thirdparty',   0, 0, 'DEFAULT VALUE', '', 1, 'CALCOLO', 0, 0, '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');
		//$result2=$extrafields->addExtraField('dolifarm_myattr2', "New Attr 2 label", 'varchar', 1, 10, 'project',      0, 0, '', '', 1, '', 0, 0, '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');
		//$result3=$extrafields->addExtraField('dolifarm_myattr3', "New Attr 3 label", 'varchar', 1, 10, 'bank_account', 0, 0, '', '', 1, '', 0, 0, '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');
		//$result4=$extrafields->addExtraField('dolifarm_myattr4', "New Attr 4 label", 'select',  1,  3, 'thirdparty',   0, 1, '', array('options'=>array('code1'=>'Val1','code2'=>'Val2','code3'=>'Val3')), 1,'', 0, 0, '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');
		//$result5=$extrafields->addExtraField('dolifarm_myattr5', "New Attr 5 label", 'text',    1, 10, 'user',         0, 0, '', '', 1, '', 0, 0, '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');

		// (I use now the dictionary )$result1=$extrafields->addExtraField('farm', "Farm", 'boolean', 100,  3, 'thirdparty',0,1, '', '', 1, '', 1, $langs->trans("HelpFarmExtrafield"), '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');	
		$result2=$extrafields->addExtraField('bodyca', "BodyCA", 'select',  101,  3, 'thirdparty',   0, 0, '', array('options'=>array('1'=>'ICEA','2'=>'Suolo e salute','3'=>'Codex srl','4'=>'Ecogruppo','5'=>'AgroQualita','6'=>'BioAgricert','7'=>'Bios srl','8'=>'IMC','9'=>'QCI','10'=>'CCPB','11'=>'AIAB','12'=>'Codex','13'=>'SIDEL','14'=>'Ecocert','15'=>'CSQA')), 1,'', 1, $langs->trans("HelpBodyCAExtrafield"), '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');
		$result3=$extrafields->addExtraField('licensenumber', "LicenseNumber", 'varchar', 102,  30, 'thirdparty',1, 0, '', '', 1, '', 1, $langs->trans("HelpLicenseNumberExtrafield"), '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');
		// (I prefer to use the standard Forniteur code) $result4=$extrafields->addExtraField('farmtracecode', "FarmTraceCode", 'varchar', 103,  30, 'thirdparty',0, 0, '', '', 1, '', 1, $langs->trans("HelpFarmTraceCodeExtrafield"), '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');
		$result5=$extrafields->addExtraField("CertificateExpirationDate", 'date', 104, '', 'thirdparty',0, 0, '', '', 1, '', 1, $langs->trans("HelpCertificateExpirationDataExtrafield"), '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');
		$result6=$extrafields->addExtraField('agronomist', "Agronomist", 'sellist', 105,  30, 'societe',0, 0, '', 'a:1:{s:7:"options";a:1:{s:20:"user:firstname:rowid";N;}}', 1, '', 1,$langs->trans("HelpAgronomistExtrafield"), '', '', 'dolifarm@dolifarm', '$conf->dolifarm->enabled');

		// Permissions
		$this->remove($options);

		$sql = array();

		// Document templates
		$moduledir = dol_sanitizeFileName('dolifarm');
		$myTmpObjects = array();
		$myTmpObjects['MyObject'] = array('includerefgeneration'=>0, 'includedocgeneration'=>0);

		foreach ($myTmpObjects as $myTmpObjectKey => $myTmpObjectArray) {
			if ($myTmpObjectKey == 'MyObject') {
				continue;
			}
			if ($myTmpObjectArray['includerefgeneration']) {
				$src = DOL_DOCUMENT_ROOT.'/install/doctemplates/'.$moduledir.'/template_myobjects.odt';
				$dirodt = DOL_DATA_ROOT.'/doctemplates/'.$moduledir;
				$dest = $dirodt.'/template_myobjects.odt';

				if (file_exists($src) && !file_exists($dest)) {
					require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
					dol_mkdir($dirodt);
					$result = dol_copy($src, $dest, 0, 0);
					if ($result < 0) {
						$langs->load("errors");
						$this->error = $langs->trans('ErrorFailToCopyFile', $src, $dest);
						return 0;
					}
				}

				$sql = array_merge($sql, array(
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'standard_".strtolower($myTmpObjectKey)."' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('standard_".strtolower($myTmpObjectKey)."', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")",
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'generic_".strtolower($myTmpObjectKey)."_odt' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('generic_".strtolower($myTmpObjectKey)."_odt', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")"
				));
			}
		}
		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
	
}
