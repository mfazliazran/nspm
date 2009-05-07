<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$smarty.const.LANG}" lang="{$smarty.const.LANG}">
<head>
	<title>{$title|default:$smarty.const.TITLE}</title>

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

	{if isset($redirect)}
	<!-- Redirection -->
	<meta http-equiv="refresh" content="5; url={$redirect}" />
	{/if}

	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />

	<!-- CSS -->
	<link rel="stylesheet" media="screen" type="text/css" href="css/default.css" />
	<link rel="stylesheet" media="screen" type="text/css" href="css/{$smarty.const.COLOR|default:white}.css" />

	<!-- JavaScript -->
	<script type="text/javascript" src="js/utils.js"></script>
	<script type="text/javascript">
	{literal}
		<!--
		var onLoads = new Array();
		function bodyOnLoad() {
			for ( var i = 0; i < onLoads.length; i++ ) {
				onLoads[i]();
			}
		}
		-->
	{/literal}
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

<body id="page" onload="bodyOnLoad()">
<div id="wrapper">
	<div id="wrapper_r">
		<div id="header">
			<div id="header_l">
				<div id="header_r">
					<div id="logo" onclick="window.location.href='./index.php';">
						<span>{$smarty.const.TITLE}</span>
					</div>
					<div id="status">
						<span>
						{if isset($state) && $state == 0} 
						Configuration unchanged<br />
						<a href="./restore.php">Force reload</a>

						{elseif isset($state) && $state == 1} 
						Configuration modified<br />
						<a href="./save.php" onclick="if(!confirm('This action will immediately register and apply current configuration to your system.\nAre you sure you want to proceed?')) return false;">Commit</a> -
						<a href="./restore.php" onclick="if(!confirm('This action will definitely erase all modifications performed since last commit.\nAre you sure you want to proceed?')) return false;">Restore</a>

						{elseif isset($state) && $state == 2} 
						Configuration saved<br />
						<a href="./restore.php">Force reload</a>

						{else} 
						Configuration not loaded<br />
						<a href="./restore.php">Force reload</a>
						{/if} 
						</span>
					</div>
				</div>
			</div>
		</div>

		<div id="tabarea">
			<div id="tabarea_l">
				<div id="tabarea_r">
					<table cellpadding="0" cellspacing="0" class="pill">
						<tr>
							<td class="pill_l">&nbsp;</td>
							<td class="pill_m">
							<div id="pillmenu">
								<ul>
								<li><a href="./status.php">Status</a></li>
								<li><a href="./policy.php">Policy</a></li>
								<li><a href="./filter.php">Filter</a></li>
								<li><a href="./nat.php">NAT</a></li>
								<li><a href="./support.php">Help</a></li>
								</ul>
							</div>
							</td>
							<td class="pill_r">&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
		</div>

		<div id="whitebox">
			<div id="whitebox_t">
				<div id="whitebox_tl">
					<div id="whitebox_tr"></div>
				</div>
			</div>

			<div id="whitebox_m">
				<div id="main">
