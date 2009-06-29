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

$page = 'policy';
$tpl->assign('page', $page);

// Process submited form
if (getPost('submit'))
{
	// Browse tables
	foreach ($OPTIONS['tables'] as $table => $chains)
	{
		// Browse chains
		foreach ($chains as $chain)
		{
			if (Security::checkOptionValue($_POST[$table][$chain], array_keys($OPTIONS['policies'])))
			{
				$iptables->setPolicy($table, $chain, $_POST[$table][$chain]);
			}
		}
	}
}

require_once('./inc/post.inc.php');
$tpl->display('policy.tpl');
?>
