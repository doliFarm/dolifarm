<?php
/* Copyright (C) 2004-2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2021  Frédéric France     <frederic.france@netlogic.fr>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 * \file    htdocs/modulebuilder/template/core/boxes/dolifarmwidget1.php
 * \ingroup dolifarm
 * \brief   Widget provided by Dolifarm
 *
 * Put detailed description here.
 */

include_once DOL_DOCUMENT_ROOT."/core/boxes/modules_boxes.php";


/**
 * Class to manage the box
 *
 * Warning: for the box to be detected correctly by dolibarr,
 * the filename should be the lowercase classname
 */
class box_harvests extends ModeleBoxes
{
	/**
	 * @var string Alphanumeric ID. Populated by the constructor.
	 */
	public $boxcode = "harvestsbox";

	/**
	 * @var string Box icon (in configuration page)
	 * Automatically calls the icon named with the corresponding "object_" prefix
	 */
	public $boximg = "dolifarm@dolifarm";

	/**
	 * @var string Box label (in configuration page)
	 */
	public $boxlabel;

	/**
	 * @var string[] Module dependencies
	 */
	public $depends = array('dolifarm');

	/**
	 * @var DoliDb Database handler
	 */
	public $db;

	/**
	 * @var mixed More parameters
	 */
	public $param;

	/**
	 * @var array Header informations. Usually created at runtime by loadBox().
	 */
	public $info_box_head = array();

	/**
	 * @var array Contents informations. Usually created at runtime by loadBox().
	 */
	public $info_box_contents = array();

	/**
	 * @var string 	Widget type ('graph' means the widget is a graph widget)
	 */
	public $widgettype = 'graph';


	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 * @param string $param More parameters
	 */
	public function __construct(DoliDB $db, $param = '')
	{
		global $user, $conf, $langs;
		// Translations
		$langs->loadLangs(array("boxes", "dolifarm@dolifarm"));

		parent::__construct($db, $param);

		$this->boxlabel = $langs->transnoentitiesnoconv("HarvestsWidget");

		$this->param = $param;

		//$this->enabled = $conf->global->FEATURES_LEVEL > 0;         // Condition when module is enabled or not
		//$this->hidden = ! ($user->rights->dolifarm->myobject->read);   // Condition when module is visible by user (test on permission)
	}

	/**
	 * Load data into info_box_contents array to show array later. Called by Dolibarr before displaying the box.
	 *
	 * @param int $max Maximum number of records to load
	 * @return void
	 */
	public function loadBox($max = 5)
	{
		global $langs,$db;
		require_once DOL_DOCUMENT_ROOT.'/custom/dolifarm/lib/dolifarm.lib.php';
		require_once DOL_DOCUMENT_ROOT.'/custom/dolitrace/class/harvests.class.php';

		//require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
		require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

		$harvest = new Harvests($db);
		// Use configuration value for max lines count
		$this->max = $max;
		
		
		// Populate the head at runtime
		$text = $langs->trans("BoxHarvestHeader",$max);
		$this->info_box_head = array(
			// Title text
			'text' => $text,
			// Add a link
			'sublink' => 'http://example.com',
			// Sublink icon placed after the text
			'subpicto' => 'object_dolifarm@dolifarm',
			// Sublink icon HTML alt text
			'subtext' => '',
			// Sublink HTML target
			'target' => '',
			// HTML class attached to the picto and link
			'subclass' => 'center',
			// Limit and truncate with "…" the displayed text lenght, 0 = disabled
			'limit' => 0,
			// Adds translated " (Graph)" to a hidden form value's input (?)
			'graph' => false
		);
		$this->info_box_contents = array();
		$this->info_box_contents[0] =  array( // First line
															0 => array( // First Column
																//  HTML properties of the TR element. Only available on the first column.
																'tr' => 'class="left"',
																// HTML properties of the TD element
																'td' => 'class=bold',
																// Main text for content of cell
																'text' => $langs->trans('Ref'),
																// Link on 'text' and 'logo' elements
																'url' => '',
																// Link's target HTML property
																'target' => '_blank',
																// Fist line logo (deprecated. Include instead logo html code into text or text2, and set asis property to true to avoid HTML cleaning)
																//'logo' => 'monmodule@monmodule',
																// Unformatted text, added after text. Usefull to add/load javascript code
																'textnoformat' => '',
																// Main text for content of cell (other method)
																// 'text2' => '<p><strong>Another text</strong></p>',
																// Truncates 'text' element to the specified character length, 0 = disabled
																'maxlength' => 0,
																// Prevents HTML cleaning (and truncation)
																'asis' => false,
																// Same for 'text2'
																'asis2' => true
															),
															1 => array( // TR
															'tr' => 'class="left"',
															'td' => 'class=bold',
															'text' => $langs->trans('Yield')
															),
															2 => array( // TR
															'tr' => 'class="left"',
															'td' => 'class=bold',
															'text' => $langs->trans('Date')
															),
															3 => array( // TR
															'tr' => 'class="left"',
															'td' => 'class=bold',
															'text' => $langs->trans('Status')
															)
												);
		$objlist = array();
		$objlist = fetchobj('dolifarm_harvests', 'date', $max);
		if ($objlist) {	
			for ($i=0;$i<count($objlist);$i++) {
			// Populate the contents at runtime
						// $harvest->fetch($objlist[$i]['rowid']);
						$this->info_box_contents[$i+1] =  array( // First line
																0 => array( // First Column
																	//  HTML properties of the TR element. Only available on the first column.
																	'tr' => 'class="left"',
																	// HTML properties of the TD element
																	'td' => '',
																	// Main text for content of cell
																	'text' => $objlist[$i]['ref'],
																	// Link on 'text' and 'logo' elements
																	'url' => DOL_URL_ROOT.'/custom/dolitrace/harvests_card.php?id='.$objlist[$i]['rowid'],
																	// Link's target HTML property
																	'target' => '',
																	// Fist line logo (deprecated. Include instead logo html code into text or text2, and set asis property to true to avoid HTML cleaning)
																	//'logo' => 'monmodule@monmodule',
																	// Unformatted text, added after text. Usefull to add/load javascript code
																	'textnoformat' => '',
																	// Main text for content of cell (other method)
																	// 'text2' => '<p><strong>Another text</strong></p>',
																	// Truncates 'text' element to the specified character length, 0 = disabled
																	'maxlength' => 0,
																	// Prevents HTML cleaning (and truncation)
																	'asis' => false,
																	// Same for 'text2'
																	'asis2' => true
																),
																1 => array( // TR
																'tr' => 'class="left"',
																'text' => $objlist[$i]['yield']
																),
																2 => array( // TR
																'tr' => 'class="left"',
																'text' => dol_print_date(dol_stringtotime($objlist[$i]['date']))
																),
																3 => array( // TR
																'tr' => 'class="left"',
																'text' => ($objlist->status == 1) ? img_picto($langs->trans("low"), 'low'): img_picto($langs->trans("Ok"), 'statut4')
																)
																);
			}
		}else {
			$this->info_box_contents = array(
											0 => array( // Another line
															0 => array( // TR
																'tr' => 'class="left"',
																'text' => 'No Harvests'
															),
															1 => array( // TR
																'tr' => 'class="left"',
																'text' => ''
															)
														)
											);
		}
	}

	/**
	 * Method to show box. Called by Dolibarr eatch time it wants to display the box.
	 *
	 * @param array $head       Array with properties of box title
	 * @param array $contents   Array with properties of box lines
	 * @param int   $nooutput   No print, only return string
	 * @return string
	 */
	public function showBox($head = null, $contents = null, $nooutput = 0)
	{
		// You may make your own code here…
		// … or use the parent's class function using the provided head and contents templates
		return parent::showBox($this->info_box_head, $this->info_box_contents, $nooutput);
	}
}
