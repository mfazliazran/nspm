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
/* Rule composition or edition */
/****************************************************************************/

// Protocol name
if (getPost('protocol'))
{
	// Protocol is known and valid
	if (Security::checkOptionValue(getPost('protocol', true), array_keys($OPTIONS['protocols'])))
	{
		$rule->set('protocol', getPost('protocol', true));
	}
	else
	{
		$errors['protocol'] = true;
	}
}
// Target and jump
if (getPost('target'))
{
	// Target is known and valid
	if (Security::checkOptionValue(getPost('target', true), array_keys($OPTIONS[$table])))
	{
		$rule->set('target', getPost('target', true));
	}
	else
	{
		$errors['target'] = true;
	}
}
// User comment
if (getPost('comment'))
{
	// Escape dangerous caracters and trim string to max length
	$rule->set('comment', Security::sanitize(getPost('comment', true), STRING, 1, 200));
}


/****************************************************************************/
/* Rule source and destination */
/****************************************************************************/

// Loop on both source and destination for common fields and criterias
foreach (array('source', 'destination') as $obj)
{
	// Check chain validity for interface context
	if (getPost($obj . '_interface') && (
		(($obj == 'source') && (($chain == 'INPUT') || ($chain == 'FORWARD') || ($chain == 'PREROUTING'))) ||
		(($obj == 'destination') && (($chain == 'FORWARD') || ($chain == 'OUTPUT') || ($chain == 'POSTROUTING')))))
	{
		// Interface is known and valid
		if (Security::checkOptionValue(getPost($obj . '_interface', true), array_keys($system['interfaces'])))
		{
			$rule->set($obj . '_interface', getPost($obj . '_interface', true));
		}
		else
		{
			$errors[$obj . '_interface'] = true;
		}
	}
	// Network address
	if (getPost($obj . '_address'))
	{
		// Valid IPv4 address
		if (Security::checkIP(getPost($obj . '_address', true)))
		{
			// CIDR formatting
			if (getPost($obj . '_address_net'))
			{
				// CIDR is valid integer within valid range (1-32)
				if (Security::check(getPost($obj . '_address_net', true), INT, 1, 32))
				{
					$rule->set($obj . '_net', getPost($obj . '_address', true). '/' .CIDRToMask(getPost($obj . '_address_net', true)));
				}
				else
				{
					$errors[$obj . '_address'] = true;
					$errors[$obj . '_address_net'] = true;
				}
			}
			// Range formatting
			elseif (getPost($obj . '_address_range'))
			{
				// Valid IPv4 address
				if (Security::checkIP(getPost($obj . '_address_range', true)))
				{
					$rule->set($obj . '_address', getPost($obj . '_address', true). '-' .getPost($obj . '_address_range', true));
				}
				else
				{
					$errors[$obj . '_address'] = true;
					$errors[$obj . '_address_range'] = true;
				}
			}
			// Single IP address
			else
			{
				$rule->set($obj . '_net', getPost($obj . '_address', true));
			}
		}
		else
		{
			$errors[$obj . '_address'] = true;
		}
	}
	// Address type
	if (getPost($obj . '_type'))
	{
		// Address type is known and valid
		if (Security::checkOptionValue(getPost($obj . '_type', true), array_keys($OPTIONS['addresses'])))
		{
			$rule->set($obj . '_type', getPost($obj . '_type', true));
		}
		else
		{
			$errors[$obj . '_type'] = true;
		}
	}
	// Application ports are only valid with TCP or UDP protocols
	if ((getPost($obj . '_ports') || getPost($obj . '_ports_range')) && ((getPost('protocol') == 'tcp') || (getPost('protocol') == 'udp')))
	{
		$ports = '';

		// Invalid ports list if not an array
		if (getPost($obj . '_ports') && (!is_array(getPost($obj . '_ports', true))))
		{
			$errors[$obj . '_ports'][0] = true;
		}
		// Non empty ports list
		elseif (getPost($obj . '_ports') && (implode('', getPost($obj . '_ports', true)) != ''))
		{
			// Browse unique ports
			foreach (array_unique(getPost($obj . '_ports', true)) as $i => $port)
			{
				// Empty, non integer or out of bounds port (1-65535)
				if (($port != '') && !Security::check($port, INT, 1, 65535))
				{
					$errors[$obj . '_ports'][$i] = true;
				}
				// Add to ports list string
				elseif ($port != '')
				{
					$ports .= (!empty($ports) ? ',' : '') . $port;
				}
			}
		}

		// Invalid ports range if not transmitted as an array
		if (getPost($obj . '_ports_range') && !is_array(getPost($obj . '_ports_range', true)))
		{
			$errors[$obj . '_ports_range'][0][0] = true;
		}
		elseif (getPost($obj . '_ports_range'))
		{
			// Browse ranges
			foreach (getPost($obj . '_ports_range', true) as $i => $range)
			{
				// Invalid ports range if not an array or without 2 components
				if (!is_array($range) || (count($range) != 2))
				{
					$errors[$obj . '_ports_range'][$i][0] = true;
				}
				// Non empty range
				elseif (implode('', $range) != '')
				{
					// Browse ports
					foreach ($range as $j => $port)
					{
						// Empty, non integer or out of bounds port (1-65535)
						if (($port != '') && !Security::check($port, INT, 1, 65535))
						{
							$errors[$obj . '_ports_range'][$i][$j] = true;
						}
					}
					$range = implode(':', $range);
					// Only first port in range given
					if (strpos($range, ':') == 0)
					{
						$errors[$obj . '_ports_range'][$i][0] = true;
					}
					// Only last port in range given
					elseif (strpos($range, ':') == strlen($range)-1)
					{
						$errors[$obj . '_ports_range'][$i][1] = true;
					}
					// Add to ports list string
					else
					{
						$ports .= (!empty($ports) ? ',' : '') . $range;
					}
				}
			}
		}
		if (!isset($errors[$obj . '_ports']) && !isset($errors[$obj . '_ports_range']) && !empty($ports))
		{
			$rule->set($obj . '_ports', $ports);
		}
	}
}
// MAC address is only valid before routing process
if (getPost('source_mac') && (($chain == 'INPUT') || ($chain == 'FORWARD') || ($chain == 'PREROUTING')))
{
	// Invalid address if not an array or without 6 components
	if (!is_array(getPost('source_mac', true)) || (count(getPost('source_mac', true)) != 6))
	{
		$errors['source_mac'][0] = true;
	}
	// Non empty address
	elseif (implode('', getPost('source_mac', true)) != '')
	{
		// Browse address bytes
		foreach (getPost('source_mac', true) as $i => $byte)
		{
			// Empty or out of bounds hexadecimal byte (00-FF)
			if (($byte == '') || (strlen($byte) != 2) || !Security::check($byte, HEXA, 0, 255))
			{
				$errors['source_mac'][$i] = true;
			}
		}
		// Capitalize MAC address
		if (!isset($errors['source_mac']))
		{
			$rule->set('source_mac', strtoupper(implode(':', getPost('source_mac', true))));
		}
	}
}


/****************************************************************************/
/* Advanced options */
/****************************************************************************/

// Connection states
if (getPost('states'))
{
	// Invalid states if not an array
	if (!is_array(getPost('states', true)))
	{
		$errors['states'][0] = true;
	}
	else
	{
		// Browse states
		foreach (getPost('states', true) as $i => $state)
		{
			// Unknown or invalid state
			if (!Security::checkOptionValue($state, array_keys($OPTIONS['states'])))
			{
				$errors['states'][$i] = true;
			}
		}
		// Build states list
		if (!isset($errors['states']))
		{
			$rule->set('states', implode(',', getPost('states', true)));
		}
	}
}
// ICMP types are only valid with ICMP protocol
if (getPost('icmp') && getPost('protocol') == 'icmp')
{
	// Type is known and valid
	if (Security::checkOptionValue(getPost('icmp', true), array_keys($OPTIONS['icmp'])))
	{
		$rule->set('icmp', getPost('icmp', true));
	}
	else
	{
		$errors['icmp'] = true;
	}
}
// TCP flags are only valid with TCP protocol
if (getPost('flags') && getPost('protocol') == 'tcp')
{
	// Invalid flags if not an array
	if (!is_array(getPost('flags', true)))
	{
		$errors['flags'][0] = true;
	}
	else
	{
		// Browse flags
		foreach (getPost('flags', true) as $i => $flag)
		{
			// Unknown or invalid flag
			if (!Security::checkOptionValue($flag, array_keys($OPTIONS['flags'])))
			{
				$errors['flags'][$i] = true;
			}
		}
		// Build flags list
		if (!isset($errors['flags']))
		{
			$rule->set('flags', implode(',', getPost('flags', true)));
		}
	}
}
// Connections limit
if (getPost('limit_value'))
{
	// Limit is valid integer within valid range (1-999)
	if (Security::check(getPost('limit_value', true), INT, 1, 999))
	{
		// Range formatting
		if (getPost('limit_suffix'))
		{
			// Time unit known and valid
			if (Security::checkOptionValue(getPost('limit_suffix', true), array_keys($OPTIONS['limits'])))
			{
				$rule->set('limit', getPost('limit_value', true). '/' .getPost('limit_suffix', true));
			}
			else
			{
				$errors['limit_value'] = true;
				$errors['limit_suffix'] = true;
			}
		}
		// Simple formatting
		else
		{
			$rule->set('limit', getPost('limit_value', true));
		}
	}
	else
	{
		$errors['limit_value'] = true;
	}
}
// Time to live
if (getPost('ttl_operator') && getPost('ttl_value'))
{
	// Unknown or invalid operator
	if (!Security::checkOptionValue(getPost('ttl_operator', true), array_keys($OPTIONS['operators'])))
	{
		$errors['ttl_value'] = true;
		$errors['ttl_operator'] = true;
	}
	// Empty, non integer or out of bounds value (1-255)
	elseif (!Security::check(getPost('ttl_value', true), INT, 1, 255))
	{
		$errors['ttl_value'] = true;
	}
	// Build TTL string
	else
	{
		$rule->set('ttl', getPost('ttl_operator', true). ' ' .getPost('ttl_value', true));
	}
}
// Packet length
if (getPost('length'))
{
	// Length is valid integer within valid range (1-65535)
	if (Security::check(getPost('length', true), INT, 1, 65535))
	{
		// Range formatting
		if (getPost('length_range'))
		{
			// Length is valid integer within valid range (1-65535)
			if (Security::check(getPost('length_range', true), INT, 1, 65535))
			{
				$rule->set('length', getPost('length', true). ':' .getPost('length_range', true));
			}
			else
			{
				$errors['length'] = true;
				$errors['length_range'] = true;
			}
		}
		// Simple formatting
		else
		{
			$rule->set('length', getPost('length', true));
		}
	}
	else
	{
		$errors['length'] = true;
	}
}
// Type of service
if (getPost('tos'))
{
	// Type is known and valid
	if (Security::checkOptionValue(getPost('tos', true), array_keys($OPTIONS['tos'])))
	{
		$rule->set('tos', getPost('tos', true));
	}
	else
	{
		$errors['tos'] = true;
	}
}


/****************************************************************************/
/* NAT translation */
/****************************************************************************/

// Check chain validity for DNAT and SNAT contexts
if (((getPost('target') == 'DNAT') && (($chain == 'PREROUTING') || ($chain == 'OUTPUT')))
	|| ((getPost('target') == 'SNAT') && ($chain == 'POSTROUTING')))
{
	// Valid IPv4 address
	if (getPost('nat_address') && Security::checkIP(getPost('nat_address', true)))
	{
		$addresses = getPost('nat_address', true);
		// Range formatting
		if (getPost('nat_address_range'))
		{
			// Valid IPv4 address
			if (Security::checkIP(getPost('nat_address_range', true)))
			{
				$addresses = getPost('nat_address', true). '-' .getPost('nat_address_range', true);
			}
			else
			{
				$errors['nat_address'] = true;
				$errors['nat_address_range'] = true;
			}
		}

		$ports = '';
		// Port translation is only valid with TCP or UDP protocols
		if (getPost('nat_port') && ((getPost('protocol') == 'tcp') || (getPost('protocol') == 'udp')))
		{
			// Port is valid integer within valid range (1-65535)
			if (Security::check(getPost('nat_port', true), INT, 1, 65535))
			{
				$ports = ':' . getPost('nat_port', true);

				// Range formatting
				if (getPost('nat_port_range'))
				{
					// Port is valid integer within valid range (1-65535)
					if (Security::check(getPost('nat_port_range', true), INT, 1, 65535))
					{
						$ports .= '-' . getPost('nat_port_range', true);
					}
					else
					{
						$errors['nat_port'] = true;
						$errors['nat_port_range'] = true;
					}
				}
			}
			else
			{
				$errors['nat_port'] = true;
			}
		}
		// Build NAT string
		if (!isset($errors['nat_port']) && !isset($errors['nat_port_range']))
		{
			$rule->set('nat', ((getPost('target', true) == 'DNAT') ? 'destination ' : 'source ') . $addresses . $ports);
		}
	}
	else
	{
		$errors['nat_address'] = true;
	}
}
// Check chain validity for REDIRECT and MASQUERADE contexts
elseif (((getPost('target') == 'REDIRECT') && (($chain == 'PREROUTING') || ($chain == 'OUTPUT')))
	|| ((getPost('target') == 'MASQUERADE') && ($chain == 'POSTROUTING')))
{
	$ports = '';
	// Port translation is only valid with TCP or UDP protocols
	if (getPost('nat_port') && ((getPost('protocol') == 'tcp') || (getPost('protocol') == 'udp')))
	{
		// Port is valid integer within valid range (1-65535)
		if (Security::check(getPost('nat_port', true), INT, 1, 65535))
		{
			$ports = getPost('nat_port', true);

			// Range formatting
			if (getPost('nat_port_range'))
			{
				// Port is valid integer within valid range (1-65535)
				if (Security::check(getPost('nat_port_range', true), INT, 1, 65535))
				{
					$ports .= '-' .getPost('nat_port_range', true);
				}
				else
				{
					$errors['nat_port'] = true;
					$errors['nat_port_range'] = true;
				}
			}
			// Build NAT string
			if (!isset($errors['nat_port']) && !isset($errors['nat_port_range']) && !empty($ports))
			{
				$rule->set('nat', 'ports ' . $ports);
			}
		}
		else
		{
			$errors['nat_port'] = true;
		}
	}
}
?>
