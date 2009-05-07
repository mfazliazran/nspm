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

// This page does not include any library, template nor specific language properties on purpose.
// Its goal is only to check if current server and client configuration fit the needs of the application.

require_once('./inc/config.inc.php');
error_reporting(0);
$warning = false;

// Return appropriate status icon
function getStatus($bool)
{
	global $warning;
	if (isset($bool) and ($bool === true))
	{
		return '<img src="img/icons/ok.png" width="16" height="16" alt="OK" />';
	}
	else
	{
		$warning = true;
		return '<img src="img/icons/warning.png" width="16" height="16" alt="Warning" />';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANG;?>" lang="<?php echo LANG;?>">
<head>
	<title><?php echo TITLE;?></title>

	<!-- Charset -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- Content types -->
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Language" content="<?php echo LANG;?>" />

	<!-- Information -->
	<meta name="robots" content="noindex, nofollow" />
	<meta name="author" content="Regis PHILIPPE" />
	<meta name="copyright" content="Regis PHILIPPE" />

	<!-- CSS -->
	<link rel="stylesheet" media="screen" type="text/css" href="css/default.css" title="page" />

	<script type="text/javascript">
		function redirect() {
			window.location.href = './status.php';
		}
	</script>

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
<div id="global">
	<div id="content">
		<h1>Compatibility test</h1>

		<table class="list">
		<tr class="even">
			<th style="width:100px;" rowspan="2">Web</th>
			<td style="width:300px;">Server user</td>
			<td style="width:16px;"><?php echo getStatus(getmyuid() != 0); ?></td>
			<td><?php echo get_current_user(); ?> <em>(root strongly discouraged)</em></td>
		</tr>
		<tr class="even">
			<td><?php echo HTML_DIR; ?></td>
			<td><?php echo getStatus(is_readable(trim(HTML_DIR))); ?></td>
			<td><?php echo substr(sprintf('%o', fileperms(trim(HTML_DIR))), -3); ?> <em>(requires read permission)</em></td>
		</tr>

		<tr class="odd">
			<th rowspan="2">PHP</th>
			<td>Version &gt;5.1</td>
			<td><?php echo getStatus(version_compare(PHP_VERSION, '5.1', '>=')); ?></td>
			<td><?php echo PHP_VERSION; ?></td>
		</tr>
		<tr class="odd">
			<td><?php echo ini_get('session.save_path'); ?></td>
			<td><?php echo getStatus(is_writable(ini_get('session.save_path'))); ?></td>
			<td><?php echo substr(sprintf('%o', fileperms(ini_get('session.save_path'))), -3); ?> <em>(requires write permission)</em></td>
		</tr>

		<tr class="even">
			<th rowspan="3">Smarty</th>
			<td><?php echo SMARTY_DIR; ?></td>
			<td><?php echo getStatus(is_readable(trim(SMARTY_DIR))); ?></td>
			<td><?php echo substr(sprintf('%o', fileperms(trim(SMARTY_DIR))), -3); ?> <em>(requires read permission)</em></td>
		</tr>
		<tr class="even">
			<td><?php echo HTML_DIR . 'templates/cache/'; ?></td>
			<td><?php echo getStatus(is_writable('templates/cache')); ?></td>
			<td><?php echo substr(sprintf('%o', fileperms('templates/cache')), -3); ?> <em>(requires write permission)</em></td>
		</tr>
		<tr class="even">
			<td><?php echo HTML_DIR . 'templates/templates_c/'; ?></td>
			<td><?php echo getStatus(is_writable('templates/templates_c')); ?></td>
			<td><?php echo substr(sprintf('%o', fileperms('templates/templates_c')), -3); ?> <em>(requires write permission)</em></td>
		</tr>

		<tr class="odd">
			<th>Sudo</th>
			<td><?php echo SUDO; ?></td>
			<td><?php echo getStatus(is_executable(trim(SUDO))); ?></td>
			<td><?php echo substr(sprintf('%o', fileperms(trim(SUDO))), -3); ?> <em>(requires execution permission)</em></td>
		</tr>

		<tr class="even">
			<th rowspan="4">Iptables</th>
			<td>Version &gt;1.3</td>
			<td>
			<?php
				$output = array();
				$exit = 0;
				exec(IPTABLES.' -h', $output, $exit);
				$version = (isset($output[0]) ? substr(array_pop(explode(' ', $output[0])), 1) : 0);
				echo getStatus($version >= '1.3');
			?>
			</td>
			<td><?php echo $version; ?></td>
		</tr>
		<tr class="even">
			<td><?php echo IPTABLES; ?></td>
			<td>
			<?php
				echo getStatus(!$exit);
			?>
			</td>
			<td><?php echo substr(sprintf('%o', fileperms(array_pop(explode(' ', IPTABLES)))), -3); ?> <em>(requires execution permission)</em></td>
		</tr>
		<tr class="even">
			<td><?php echo IPTABLES_SAVE; ?></td>
			<td>
			<?php
				$output = array();
				$exit = 0;
				exec(IPTABLES_SAVE, $output, $exit);
				echo getStatus(!$exit);
			?>
			</td>
			<td><?php echo substr(sprintf('%o', fileperms(array_pop(explode(' ', IPTABLES_SAVE)))), -3); ?> <em>(requires execution permission)</em></td>
		</tr>
		<tr class="even">
			<td><?php echo IPTABLES_RESTORE; ?></td>
			<td>
			<?php
				$output = array();
				$exit = 0;
				exec(IPTABLES_RESTORE, $output, $exit);
				echo getStatus(!$exit);
			?>
			</td>
			<td><?php echo substr(sprintf('%o', fileperms(array_pop(explode(' ', IPTABLES_RESTORE)))), -3); ?> <em>(requires execution permission)</em></td>
		</tr>

		<tr class="odd">
			<th rowspan="3">Browser</th>
			<td>Firefox compatible</td>
			<td><?php echo getStatus(strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko') !== false); ?></td>
			<td></td>
		</tr>
		<tr class="odd">
			<td>JavaScript enabled</td>
			<td>
			<script type="text/javascript">
				document.write('<img src="img/icons/ok.png" width="16" height="16" alt="OK" />');
			</script>
			<noscript>
				<img src="img/icons/warning.png" width="16" height="16" alt="Warning" />
			</noscript>
			</td>
			<td></td>
		</tr>
		<tr class="odd">
			<td>Cookies enabled</td>
			<td>
			<script type="text/javascript">
				if (navigator.cookieEnabled) {
					document.write('<img src="img/icons/ok.png" width="16" height="16" alt="OK" />');
				}
				else {
					document.write('<img src="img/icons/warning.png" width="16" height="16" alt="Warning" />');
				}
			</script>
			<noscript>
				<img src="img/icons/warning.png" width="16" height="16" alt="Warning" />
			</noscript>
			</td>
			<td></td>
		</tr>
		</table>

		<?php if ($warning) { ?>
		<p class="centered">
			<img src="img/icons/warning.png" width="16" height="16" alt="Warning" /><br />
			Warnings were raised during pre-flight configuration tests.<br />
			You may not be able to use all the features of this application.<br /><br />
			<a href="./check.php">Check again</a> - <a href="./status.php">Ignore warnings</a>
		</p>
		<?php } else { ?>
		<p class="centered">
			<img src="img/icons/ok.png" width="16" height="16" alt="OK" /><br />
			No important warning was raised, you system configuration is optimal.<br />
			You will now be redirected to the application front page.<br /><br />
			<script type="text/javascript">setTimeout(redirect, 10000);</script>
			<a href="./status.php">Start now</a>
		</p>
		<?php } ?>
	</div>
</div>
</body>
</html>
