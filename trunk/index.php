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

require_once('./inc/pre.inc.php');

// Redirect to startup page if application session exists and no check requested
if (isset($_SESSION['config']) && !getParam('check'))
{
	header('Location: ./status.php');
	exit;
}
else
{
	// Check fresh install
	if (isset($tpl->compile_dir) && is_dir($tpl->compile_dir))
	{
		$nb = count(scandir($tpl->compile_dir));
	}
	// Redirect to initial tests page on fresh install or on request
	if (getParam('check') || ($nb <= 3))
	{
		header('Location: ./check.php');
		exit;
	}
	else
	{
		header('Location: ./status.php');
		exit;
	}
}
?>
