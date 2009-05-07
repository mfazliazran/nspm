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

// Process error type if provided
if (getParam('error'))
{
	$tpl->assign('error', getParam('error', true));
}
else
{
	$tpl->assign('error', 'unknown');
}

require_once('./inc/post.inc.php');
$tpl->display('error.tpl');
?>
