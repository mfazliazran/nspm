<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$smarty.const.LANG}" lang="{$smarty.const.LANG}">
<head>
	<title>NSPM - {$title|default:$smarty.const.TITLE}</title>

	<!-- Charset -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- Content types -->
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Language" content="{$smarty.const.LANG}" />

	<!-- Information -->
	<meta name="robots" content="noindex, nofollow" />
	<meta name="author" content="Regis PHILIPPE" />
	<meta name="copyright" content="Regis PHILIPPE" />

	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />

	<!-- CSS -->
	<link rel="stylesheet" media="screen" type="text/css" href="css/default.css" />

	<!-- Software license
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
	-->
</head>

<body id="page">
<div id="wrapper">
	<div id="auth">
		{if isset($failed) && ($failed == 1)}
		<p class="warning">
			Login verification failed.<br />
			Please check your credentials.
		</p>
		{/if}
		{if isset($logout) && ($logout == 1)}
		<p class="confirm">
			Logout successful.<br />
			Your session has been cleared.
		</p>
		{/if}
		{if ($method == 'session')}
		<form method="post" action="{$page}.php">
			<fieldset>
			<legend>Authentication</legend>
				<table>
				<tr>
					<th><label for="login">Username</label></th>
					<td>: <input type="text" name="login" id="login" /></td>
				</tr>
				<tr>
					<th><label for="password">Password</label></th>
					<td>: <input type="password" name="password" id="password" /></td>
				</tr>
				</table>
				<p class="centered">
					<input type="submit" name="submit" value="Submit" />
				</p>
			</fieldset>
		</form>
		{/if}
	</div>
</div>
</body>
</html>
