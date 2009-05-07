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

// Register session data
$_SESSION['config'] = $iptables->getConfig();
$_SESSION['state']['transaction'] = $iptables->getTransactionState();

// Send common information to template
$tpl->assign('config', $iptables->getConfig());
$tpl->assign('state', $iptables->getTransactionState());

// Send page processing duration
$tpl->assign('loading', microtime(true)-$loading);
?>
