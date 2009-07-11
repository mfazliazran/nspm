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

// Define NSPM authentication method
// --> session: use built-in authentication module
// --> http: use HTTP or external Apache authentication
// --> none: no authentication (VERY unsecure)
define('AUTH', 'http');

// Define NSPM administrator credentials
// *** CHANGE for security concerns ***
// --> any login string (defaults to admin)
// --> any SHA1 hashed password (defaults to !nspm!)
define('LOGIN', 'admin');
define('PASSWORD', '6a7af64ec0a81c2af19bdc8d5a6aa6b708d4370b');

// Define application debug status
// --> true: internal errors display
// --> false: no output (production environment)
define('DEBUG', true);

// Define application language
// --> available languages: en
define('LANG', 'en');

// Define application layout color
// --> available colors: white, black, blue
define('COLOR', 'white');

// Define application title to display
// --> any text string: company name, server name, ...
define('TITLE', 'Network Security &amp; Policy Manager');

// Web directory and Smarty main class location
// -> Change paths to directories according to your installation
define('HTML_DIR', '/var/www/nspm/');
define('SMARTY_DIR', '/usr/share/php/smarty/libs/');

// Global access commands
// --> Change paths to binaries according to your distribution
// *** Preserve trailing spaces ***
define('SUDO', '/usr/bin/sudo ');
define('CAT', '/bin/cat ');

// Iptables configuration commands
// --> Change paths to binaries according to your distribution
define('IPTABLES', SUDO .'/sbin/iptables');
define('IPTABLES_SAVE', SUDO .'/sbin/iptables-save');
define('IPTABLES_RESTORE', SUDO .'/sbin/iptables-restore');

// Network information commands and options
// --> Change paths to binaries according to your distribution
define('IFCONFIG', '/sbin/ifconfig -a');
define('NETSTAT', '/bin/netstat -n -t -u');
define('ARP', '/usr/sbin/arp -a -n');

// System information commands and options
// --> Change paths to binaries according to your distribution
define('UNAME', '/bin/uname -s -n -r -m -o');
define('UPTIME', '/usr/bin/uptime');
define('CPUINFO', CAT .'/proc/cpuinfo');
define('MEMINFO', CAT .'/proc/meminfo');

// Internal parameters
define('VERSION', '0.2b2');
define('REALM', 'NSPM Authentication');
session_name('NSPM');
?>
