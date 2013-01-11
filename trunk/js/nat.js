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
	if ((protocol == 'tcp') || (protocol == 'udp')) {
		show('row_destination_ports');
		if (getValue('target') != 'NETMAP') {
			show('row_nat_ports');
		}
	}
	else {
		hide('row_destination_ports');
		hide('row_nat_ports');
	}
}

/* Diplay or hide table rows according to selected target */
function updateTarget(target) {
	if ((target == 'DNAT') || (target == 'SNAT')) {
		show('row_nat_address');
		hide('nat_address_net');
		show('nat_address_range');
		setContent('nat_address_separator', '-');
	}
	else if (target == 'NETMAP') {
		show('row_nat_address');
		hide('nat_address_range');
		show('nat_address_net');
		setValue('nat_address_net', '32');
		setContent('nat_address_separator', '/');
	}
	else {
		hide('row_nat_address');
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

/* Update fields values according to selected interface */
function updateInterface(data) {
	var expr = /\((.+)\/.+\)/;
	var matches = expr.exec(data);
	setValue('nat_address', matches[1]);
}
