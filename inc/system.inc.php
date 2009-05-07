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
/* System information */
/****************************************************************************/

// Network interfaces
$system['interfaces'] = array();
$system['interfaces'][''] = 'All';
foreach (System::getInterfaces() as $k => $v)
{
	if (!empty($v['ipv4'][0]))
	{
		$system['interfaces'][$k] = $k.' ('. $v['ipv4'][0] .')';
	}
}

// Active connections
$system['connections_addresses'] = array();
$system['connections_ports'] = array();
$system['connections_addresses'][''] = '-';
$system['connections_ports'][''] = '-';
foreach (System::getConnections() as $k => $v)
{
	$system['connections_addresses'][$v['local_address']] = $v['local_address'];
	$system['connections_addresses'][$v['remote_address']] = $v['remote_address'];
	$system['connections_ports'][$v['local_port']] = $v['local_port'];
	$system['connections_ports'][$v['remote_port']] = $v['remote_port'];
}
natsort($system['connections_addresses']);
natsort($system['connections_ports']);

// ARP cache
$system['arp_cache'] = array();
$system['arp_cache'][''] = '-';
foreach (System::getARPCache() as $k => $v) {
	if (!empty($v['mac']))
	{
		$system['arp_cache'][$v['mac']] = $v['mac'];
	}
}
asort($system['arp_cache']);
?>
