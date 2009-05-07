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

$page = 'status';
$tpl->assign('page', $page);

// Network information
$system['interfaces'] = System::getInterfaces();
$system['connections'] = System::getConnections();
$system['arp'] = System::getARPCache();
ksort($system['arp']);

// System information
$system['name'] = System::getSystemName();
$system['uptime'] = System::getSystemUptime();
$system['cpu'] = System::getSystemCPU();
$system['memory'] = System::getSystemMemory();

array_walk_recursive($system, array('Security', 'sanitizeArray'), HTML);
$tpl->assign('system', $system);

require_once('./inc/post.inc.php');
$tpl->display('status.tpl');
?>
