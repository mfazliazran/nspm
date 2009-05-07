<?php
/*
    Copyright (C) 2006-2009 Regis PHILIPPE

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Load Smarty PHP compiling template engine (http://smarty.php.net)
require_once(SMARTY_DIR . 'Smarty.class.php');

/**
* Templates management class
*/
class Page extends Smarty
{
	/**
	* Class constructor
	* Define template engine configuration settings
	*/
	function __construct()
	{
		parent::Smarty();
		$this->template_dir			= HTML_DIR . 'templates/';
		$this->compile_dir			= HTML_DIR . 'templates/templates_c/';
		$this->config_dir				= HTML_DIR . 'templates/configs/';
		$this->cache_dir				= HTML_DIR . 'templates/cache/';
		$this->plugins_dir			= array('plugins');
		$this->debugging				= false;
		$this->error_reporting		= E_ALL;
		$this->debug_tpl				= '';
		$this->debugging_ctrl		= 'NONE';
		$this->compile_check			= true;
		$this->force_compile			= true;
		$this->caching					= false;
		$this->cache_lifetime		= 3600;
		$this->cache_modified_check= false;
	}
}
?>
