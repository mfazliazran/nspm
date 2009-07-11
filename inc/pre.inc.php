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

// Include configuration file
require_once('./inc/config.inc.php');

// Include options file
require_once('./inc/options.inc.php');

// Include security library
require_once('./lib/security.class.php');

// Include system libraries
require_once('./lib/system.class.php');
require_once('./lib/rules.class.php');
require_once('./lib/iptables.class.php');

// Include templates library
require_once('./lib/smarty.class.php');

// Include common functions
require_once('./inc/utils.inc.php');

// Define PHP error_reporting parameter
if (DEBUG)
{
	error_reporting(E_ALL);
}
else
{
	error_reporting(0);
}

// Register processing start time
$loading = microtime(true);
session_start();

// Check user authentication
if (!isset($_SESSION['auth']['user']))
{
	header('Location: ./auth.php');
	exit;
}

// Check session integrity violation
if (!Security::checkSession())
{
	// Delete all registered data
	session_unset();
	// Generate new session ID
	session_regenerate_id(true);
	$sid = session_id();
	// Write and close session files
	session_write_close();
	session_id($sid);
	// Generate new session and session token
	session_start();
	Security::checkSession();
	// Redirect to authentication page
	header('Location: ./auth.php');
	exit;
}

// Send common parameters to template
$tpl = new Page();
$tpl->assign('user', $_SESSION['auth']['user']);
$tpl->assign('options', $OPTIONS);

// Restore existing transaction state
if (isset($_SESSION['config']))
{
	$iptables = Iptables::getInstance($_SESSION['config']);
	$iptables->setTransactionState($_SESSION['state']['transaction']);
}
// Create a new transaction otherwise
else
{
	$iptables = Iptables::getInstance();
}
?>
