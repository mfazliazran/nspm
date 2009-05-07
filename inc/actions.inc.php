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
/* Actions on tables, chains or rules */
/****************************************************************************/

// Reverse security policy
if (getParam('reverse') && getParam('reverse', true))
{
	$iptables->reversePolicy($table, $chain);

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}

// Move up a specific rule
elseif (getParam('up') && Security::check(getParam('up', true), INT))
{
	// Decrease rule index
	if ($iptables->getRule($table, $chain, getParam('up', true)) && $iptables->getRule($table, $chain, getParam('up', true)-1))
	{
		$iptables->moveRule($table, $chain, getParam('up', true), getParam('up', true)-1);
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}
// Move up a set of rules
elseif (getPost('up_x') && getPost('rules') && is_array(getPost('rules', true)))
{
	// Sort indexes so that rules keep the right order after reordering
	$rules = getPost('rules', true);
	sort($rules);

	// Browse selected rules
	foreach ($rules as $index)
	{
		// Decrease rule index
		if (Security::check($index, INT) && $iptables->getRule($table, $chain, $index) && $iptables->getRule($table, $chain, $index-1))
		{
			$iptables->moveRule($table, $chain, $index, $index-1);
		}
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}

// Move down a specific rule
elseif (getParam('down') && Security::check(getParam('down', true), INT))
{
	// Increase rule index
	if ($iptables->getRule($table, $chain, getParam('down', true)) && $iptables->getRule($table, $chain, getParam('down', true)+1))
	{
		$iptables->moveRule($table, $chain, getParam('down', true), getParam('down', true)+1);
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}
// Move down a set of rules
elseif (getPost('down_x') && getPost('rules') && is_array(getPost('rules', true)))
{
	// Sort indexes so that rules keep the right order after reordering
	$rules = getPost('rules', true);
	rsort($rules);

	// Browse selected rules
	foreach ($rules as $index)
	{
		// Increase rule index
		if (Security::check($index, INT) && $iptables->getRule($table, $chain, $index) && $iptables->getRule($table, $chain, $index+1))
		{
			$iptables->moveRule($table, $chain, $index, $index+1);
		}
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}

// Disable a specific rule
elseif (getParam('disable') && Security::check(getParam('disable', true), INT))
{
	// Disable rule at given index
	if ($iptables->getRule($table, $chain, getParam('disable', true)))
	{
		$iptables->disableRule($table, $chain, getParam('disable', true));
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}
// Disable a set of rules
elseif (getPost('disable_x') && getPost('rules') && is_array(getPost('rules', true)))
{
	// Browse selected rules
	foreach (getPost('rules', true) as $index)
	{
		// Disable current rule
		if (Security::check($index, INT) && $iptables->getRule($table, $chain, $index))
		{
			$iptables->disableRule($table, $chain, $index);
		}
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}

// Enable a specific rule
elseif (getParam('enable') && Security::check(getParam('enable', true), INT))
{
	// Enable rule at given index
	if ($iptables->getRule($table, $chain, getParam('enable', true)))
	{
		$iptables->enableRule($table, $chain, getParam('enable', true));
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}
// Enable a set of rules
elseif (getPost('enable_x') && getPost('rules') && is_array(getPost('rules', true)))
{
	// Browse selected rules
	foreach (getPost('rules', true) as $index)
	{
		// Enable current rule
		if (Security::check($index, INT) && $iptables->getRule($table, $chain, $index))
		{
			$iptables->enableRule($table, $chain, $index);
		}
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}

// Copy a specific rule
elseif (getParam('copy') && Security::check(getParam('copy', true), INT))
{
	// Copy rule at given index
	if ($iptables->getRule($table, $chain, getParam('copy', true)))
	{
		$iptables->copyRule($table, $chain, getParam('copy', true));
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}

// Delete a specific rule
elseif (getParam('delete') && Security::check(getParam('delete', true), INT))
{
	// Delete rule at given index
	if ($iptables->getRule($table, $chain, getParam('delete', true)))
	{
		$iptables->deleteRule($table, $chain, getParam('delete', true));
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}
// Delete a set of rules
elseif (getPost('delete_x') && getPost('rules') && is_array(getPost('rules', true)))
{
	// Sort indexes so that rules keep the right order after deletion
	$rules = getPost('rules', true);
	rsort($rules);

	// Browse selected rules
	foreach ($rules as $index)
	{
		// Delete current rule
		if (Security::check($index, INT) && $iptables->getRule($table, $chain, $index))
		{
			$iptables->deleteRule($table, $chain, $index);
		}
	}

	// Redirect to base page
	require_once('./inc/post.inc.php');
	header('Location: ./'. $page .'.php');
	exit;
}
?>
