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

/****************************************************************************/
/* Common functions */
/****************************************************************************/

// Application failsafe exit
function niceExit($error = false, $message = false, $command = false, $output = false)
{
	global $tpl;
	$tpl->assign('error', $error);
	$tpl->assign('message', $message);
	$tpl->assign('command', $command);
	$tpl->assign('output', $output);
	$tpl->display('error.tpl');
	exit;
}

// Fetch GET parameter
function getParam($value, $force = false)
{
	if ($force || (isset($_GET[$value]) && (!empty($_GET[$value]) || ($_GET[$value] == '0'))))
	{
		if (($_GET[$value] == '0') && !$force)
		{
			return true;
		}
		else
		{
			return $_GET[$value];
		}
	}
	else
	{
		return false;
	}
}

// Fetch POST parameter
function getPost($value, $force = false)
{
	if ($force || (isset($_POST[$value]) && (!empty($_POST[$value]) || ($_POST[$value] == '0'))))
	{
		if (($_POST[$value] == '0') && !$force)
		{
			return true;
		}
		else
		{
			return $_POST[$value];
		}
	}
	else
	{
		return false;
	}
}

// Convert network mask to CIDR
function MaskToCIDR($mask)
{
	$address = '';
	$bin = '';

	$data = explode('/', $mask);
	// Address given before network mask
	if (isset($data[1]))
	{
		$address = $data[0].'/';
		$mask = $data[1];
	}
	// Valid network mask given
	if (substr($mask, 0, 3) == '255')
	{
		$bytes = explode('.', $mask);
		foreach ($bytes as $byte)
		{
			$bin .= sprintf('%08s', decbin($byte));
		}
		$cidr = strpos($bin.'0', '0');
	}
	// No valid network mask
	else
	{
		$cidr = $mask;
	}

	return $address . $cidr;
}

// Convert CIDR to network mask
function CIDRToMask($cidr)
{
	$address = '';
	$mask = '';

	$data = explode('/', $cidr);
	// Address given before CIDR
	if (isset($data[1]))
	{
		$address = $data[0].'/';
		$cidr = $data[1];
	}
	// Valid CIDR given
	if (strlen($cidr) <= 2)
	{
		$mask = str_repeat('255.', floor(($cidr-1)/8));
		$mask .= bindec(sprintf('%0-8s', str_repeat('1', ($cidr-1)%8+1)));
		$mask .= str_repeat('.0', 3 - substr_count($mask, '.'));
	}
	// No valid CIDR
	else
	{
		$mask = $cidr;
	}

	return $address . $mask;
}
?>
