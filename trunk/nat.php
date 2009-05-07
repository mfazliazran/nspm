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

$table = 'nat';
$page = 'nat';
$tpl->assign('table', $table);
$tpl->assign('page', $page);


/****************************************************************************/
/* General context */
/****************************************************************************/

// Compose rule if chain parameter supplied
if (getParam('chain'))
{
	// Chain is expected and valid
	if (Security::checkOptionValue(getParam('chain', true), $OPTIONS['tables'][$table]))
	{
		$chain = getParam('chain', true);
		$tpl->assign('chain', $chain);
	}
	// Redirect to base page otherwise
	else
	{
		header('Location: ./'. $page .'.php');
		exit;
	}
}
// Display rules list if no parameter
else
{
	require_once('./inc/post.inc.php');
	$tpl->display('nat_list.tpl');
	exit;
}

require_once('./inc/system.inc.php');
array_walk_recursive($system, array('Security', 'sanitizeArray'), HTML);
$tpl->assign('system', $system);

$errors = array();
$values = array();
require_once('./inc/actions.inc.php');


/****************************************************************************/
/* Requests */
/****************************************************************************/

// Compose new rule
if (getParam('new') && Security::check(getParam('new', true), INT))
{
	$rule = new Rule();
}
// Edit existing rule
elseif (getParam('edit') && Security::check(getParam('edit', true), INT))
{
	// Index exists and can be loaded
	if ($iptables->getRule($table, $chain, getParam('edit', true)))
	{
		$rule = $iptables->getRule($table, $chain, getParam('edit', true));
		$values = $rule->getAll();
		$tpl->assign('edit', getParam('edit', true));
	}
	// Redirect to base page otherwise
	else
	{
		header('Location: ./'. $page .'.php');
		exit;
	}

	// Parse and register rule values
	require_once('./inc/load.inc.php');
}
// Process submited form
elseif (getPost('submit'))
{
	$rule = new Rule();
	// Check overall consistency and sanity
	require_once('./inc/check.inc.php');

	// Save rule if no error detected
	if (empty($errors))
	{
		// Edit existing rule
		if (getPost('edit') && Security::check(getPost('edit', true), INT))
		{
			$current = $iptables->getRule($table, $chain, getPost('edit', true));
			// Index exists and can be loaded
			if ($current)
			{
				// Rule is disabled and needs specific attention
				if ($current->get('target') == 'disable')
				{
					$iptables->enableRule($table, $chain, getPost('edit', true));
					$iptables->changeRule($table, $chain, getPost('edit', true), $rule);
					$iptables->disableRule($table, $chain, getPost('edit', true));
				}
				else
				{
					$iptables->changeRule($table, $chain, getPost('edit', true), $rule);
				}
			}
		}
		// Register new rule
		else
		{
			$iptables->addRule($table, $chain, $rule);
		}
		// Redirect to table overview
		require_once('./inc/post.inc.php');
		header('Location: ./'. $page .'.php');
		exit;
	}
	// Display form with submited data if errors detected
	else
	{
		$values = $_POST;
	}
}

array_walk_recursive($values, array('Security', 'sanitizeArray'), HTML);
$tpl->assign('errors', $errors);
$tpl->assign('values', $values);

require_once('./inc/post.inc.php');
$tpl->display('nat_edit.tpl');
?>
