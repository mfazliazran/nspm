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
/* Source and destination parsing */
/****************************************************************************/

// Loop on both source and destination for common fields and criterias
foreach (array('source', 'destination') as $obj)
{
	// Network address
	if ($rule->get($obj . '_address'))
	{
		$data = explode('-', $rule->get($obj . '_address'));
		$values[$obj . '_address'] = $data[0];
		$values[$obj . '_address_range'] = $data[1];
	}
	// Network mask
	if ($rule->get($obj . '_net'))
	{
		$data = explode('/', $rule->get($obj . '_net'));
		$values[$obj . '_address'] = $data[0];
		if (isset($data[1]))
		{
			$values[$obj . '_address_net'] = MaskToCIDR($data[1]);
		}
	}
	// Application ports
	if ($rule->get($obj . '_ports'))
	{
		$values[$obj . '_ports'] = array();
		$values[$obj . '_ports_range'] = array();
		$data = explode(',', $rule->get($obj . '_ports'));

		// Browse ports groups
		foreach ($data as $v)
		{
			// Ports range
			if (strpos($v, ':') !== false)
			{
				$values[$obj . '_ports_range'][] = explode(':', $v);
			}
			// Single port
			else
			{
				$values[$obj . '_ports'][] = $v;
			}
		}
	}
	// MAC address
	if ($rule->get($obj . '_mac'))
	{
		$values[$obj . '_mac'] = explode(':', $rule->get($obj . '_mac'));
	}
}


/****************************************************************************/
/* Advanced options parsing */
/****************************************************************************/

// Connection states
if ($rule->get('states'))
{
	$values['states'] = explode(',', $rule->get('states'));
}
// TCP flags
if ($rule->get('flags'))
{
	$values['flags'] = explode(',', $rule->get('flags'));
}
// Connections limit and unit
if ($rule->get('limit'))
{
	$data = explode('/', $rule->get('limit'));
	$values['limit_value'] = $data[0];
	if (isset($data[1]))
	{
		$values['limit_suffix'] = $data[1];
	}
}
// Packet length or lengths range
if ($rule->get('length'))
{
	$data = explode(':', $rule->get('length'));
	$values['length'] = $data[0];
	if (isset($data[1]))
	{
		$values['length_range'] = $data[1];
	}
}
// TTL operator and value
if ($rule->get('ttl'))
{
	$data = explode(' ', $rule->get('ttl'));
	$values['ttl_op'] = $data[0];
	$values['ttl_value'] = $data[1];
}


/****************************************************************************/
/* NAT translation parsing */
/****************************************************************************/

// Source NAT
if ($rule->get('nat_source'))
{
	// Address or addresses range translation
	$to = explode(':', $rule->get('nat_source'));
	$addresses = explode('-', $to[0]);
	$values['nat_address'] = $addresses[0];
	if (isset($addresses[1]))
	{
		$values['nat_address_range'] = $addresses[1];
	}
	// Port or ports range translation
	if (isset($to[1]))
	{
		$ports = explode('-', $to[1]);
		$values['nat_port'] = $ports[0];
		if (isset($ports[1]))
		{
			$values['nat_port_range'] = $ports[1];
		}
	}
}
// Destination NAT
elseif ($rule->get('nat_destination'))
{
	// Address or addresses range translation
	$to = explode(':', $rule->get('nat_destination'));
	$addresses = explode('-', $to[0]);
	$values['nat_address'] = $addresses[0];
	if (isset($addresses[1]))
	{
		$values['nat_address_range'] = $addresses[1];
	}
	// Port or ports range translation
	if (isset($to[1]))
	{
		$ports = explode('-', $to[1]);
		$values['nat_port'] = $ports[0];
		if (isset($ports[1]))
		{
			$values['nat_port_range'] = $ports[1];
		}
	}
}
// Masquerading or redirection
elseif ($rule->get('nat_ports'))
{
	// Port or ports range translation
	$ports = explode('-', $rule->get('nat_ports'));
	$values['nat_port'] = $ports[0];
	if (isset($ports[1]))
	{
		$values['nat_port_range'] = $ports[1];
	}
}
// Network mapping
elseif ($rule->get('nat_map'))
{
	// Network translation
	$to = explode('/', $rule->get('nat_map'));
	$values['nat_address'] = $to[0];
	if (isset($to[1]))
	{
		$values['nat_address_net'] = $to[1];
	}
}
?>
