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

// Session parameters
session_name('FIREWALL');

// Application parameters
define('DEBUG', true);
define('LANG', 'en');
define('COLOR', 'white');
define('TITLE', 'Network Security & Policy Manager');
define('VERSION', '0.2b1');

// Paths, binaries and command-line options
define('SMARTY_DIR', '/usr/share/php/smarty/libs/');
define('HTML_DIR', '/var/www/firewall/');
define('SUDO', '/usr/bin/sudo ');

define('IPTABLES', SUDO .'/sbin/iptables');
define('IPTABLES_SAVE', SUDO .'/sbin/iptables-save');
define('IPTABLES_RESTORE', SUDO .'/sbin/iptables-restore');

define('IFCONFIG', '/sbin/ifconfig -a');
define('NETSTAT', '/bin/netstat -n -t -u');
define('ARP', '/usr/sbin/arp -a -n');

define('UNAME', '/bin/uname -s -n -r -m -o');
define('UPTIME', '/usr/bin/uptime');
define('CPUINFO', '/bin/cat /proc/cpuinfo');
define('MEMINFO', '/bin/cat /proc/meminfo');
?>
