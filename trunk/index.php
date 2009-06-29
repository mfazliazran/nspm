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

// Include common functions
require_once('./inc/utils.inc.php');

session_start();

// Redirect to startup page if application session exists and no check requested
if (isset($_SESSION['auth']['user']) && !getParam('check'))
{
	header('Location: ./status.php');
	exit;
}
else
{
	// Check for fresh install without compiled templates
	if (is_dir('./templates/templates_c/'))
	{
		$nb = count(scandir('./templates/templates_c/'));
	}
	// Redirect to initial tests page on fresh install or on request
	// 4 stands for ".", "..", ".svn" and "index.html" entries
	if (getParam('check') || ($nb <= 4))
	{
		header('Location: ./check.php');
		exit;
	}
	// Redirect to authentication page
	else
	{
		header('Location: ./auth.php');
		exit;
	}
}
?>
