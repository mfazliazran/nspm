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

/* Diplay or hide table rows according to selected protocol */
function updateProtocol(protocol) {
	if (protocol == 'tcp') {
		showChildren('row_target', 6);
		hide('row_icmp');
		show('row_source_ports');
		show('row_destination_ports');
		show('row_flags');
	}
	else if (protocol == 'udp') {
		showChildren('row_target', 6);
		hide('row_icmp');
		hide('row_flags');
		show('row_source_ports');
		show('row_destination_ports');
	}
	else if (protocol == 'icmp') {
		hideChildren('row_target', 6);
		hide('row_flags');
		hide('row_source_ports');
		hide('row_destination_ports');
		show('row_icmp');
	}
	else {
		hideChildren('row_target', 6);
		hide('row_source_ports');
		hide('row_destination_ports');
		hide('row_icmp');
		hide('row_flags');
	}
}

/* Diplay or hide table rows according to selected target */
function updateTarget(target) {
	if (target == 'LOG') {
		hide('row_ulog_nlgroup');
		hide('row_ulog_prefix');
		show('row_log_level');
		show('row_log_prefix');
	}
	else if (target == 'ULOG') {
		hide('row_log_level');
		hide('row_log_prefix');
		show('row_ulog_nlgroup');
		show('row_ulog_prefix');
	}
	else {
		hide('row_log_level');
		hide('row_log_prefix');
		hide('row_ulog_nlgroup');
		hide('row_ulog_prefix');
	}
}

/* Change displayed IP address type (CIDR or range) */
function changeAddress(context) {
	if (isVisible(context + '_address_net')) {
		hide(context + '_address_net');
		show(context + '_address_range');
		cleanValue(context + '_address_net');
		setContent(context + '_address_separator', '-');
		setValue(context + '_address_range', getValue(context + '_address'));
	}
	else if (isVisible(context + '_address_range')) {
		hide(context + '_address_range');
		show(context + '_address_net');
		cleanValue(context + '_address_range');
		setContent(context + '_address_separator', '/');
	}
}

/* Update fields values according to external data */
function useAddress(context, value) {
	if (value) {
		setValue(context + '_address', value);
	}
}
function usePort(context, value) {
	if (value) {
		for (var i=1; i<=5; i++) {
			if (emptyValue(context + '_ports' + i)) {
				setValue(context + '_ports' + i, value);	
				return;
			}
		}
		setValue(context + '_ports5', value);
	}
}
function useMAC(context, value) {
	if (value) {
		var elements = value.split(':');
		for (var i=0; i<elements.length; i++) {
			setValue(context + '_mac' + (i+1), elements[i]);
		}
	}
}
