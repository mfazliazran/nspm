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

// Include templates library
require_once('./lib/smarty.class.php');

// Include common functions
require_once('./inc/utils.inc.php');

session_start();
$page = 'auth';

// No authentication required
if (AUTH == 'none')
{
	$_SESSION['auth']['user'] = LOGIN;
	$authorized = true;
}
// HTTP authentication required
elseif (AUTH == 'http')
{
	// Web server internal authentication
	if (isset($_SERVER['REMOTE_USER']))
	{
		$_SESSION['auth']['user'] = $_SERVER['REMOTE_USER'];
		$authorized = true;
	}
	// PHP-based authentication credentials check
	elseif (isset($_SERVER['PHP_AUTH_USER']) && ($_SERVER['PHP_AUTH_USER'] == LOGIN) && (sha1($_SERVER['PHP_AUTH_PW']) == PASSWORD))
	{
		$_SESSION['auth']['user'] = $_SERVER['PHP_AUTH_USER'];
		$authorized = true;
	}
	// PHP-based authentication prompt
	else
	{
		// Send HTTP authentication headers
		header('WWW-Authenticate: Basic realm="' . REALM . '"');
		header('HTTP/1.0 401 Unauthorized');
		header('Status: 401 Access Denied');
		$authorized = false;
	}
}
// Built-in authentication required
elseif (AUTH == 'session')
{
	// Credentials form submission
	if (getPost('submit'))
	{
		if (getPost('login') && getPost('password'))
		{
			// Credentials check
			if ((getPost('login', true) == LOGIN) && (sha1(getPost('password', true)) == PASSWORD))
			{
				$_SESSION['auth']['user'] = getPost('login', true);
				$authorized = true;
			}
			else
			{
				$authorized = false;
			}
		}
		else
		{
			$authorized = false;
		}
	}
}

// Redirect to front page if authorization is granted
if (isset($authorized) && ($authorized))
{
	header('Location: ./index.php');
	exit;
}
// Display authentication page otherwise
else
{
	$tpl = new Page();
	$tpl->assign('page', $page);
	$tpl->assign('method', AUTH);
	if (isset($authorized) && (!$authorized))
	{
		$tpl->assign('failed', '1');
	}
	$tpl->display('auth.tpl');
}
?>
