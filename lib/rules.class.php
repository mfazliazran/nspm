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

/**
* Rules management class
* @author Regis PHILIPPE
*/
class Rule
{
	/****************************************************************************/
	/* Class members */
	/****************************************************************************/

	/**
	* Rules components and arguments definition
	* @var array - Modules to load, commands to send and data to match
	*/
	private static $_config = array(
		'protocol'		=> array(
				'module'	=> '',
				'parent'	=> '',
				'command'	=> array('-p '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'source_interface'	=> array(
				'module'	=> '',
				'parent'	=> '',
				'command'	=> array('-i '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'source_address'	=> array(
				'module'	=> 'iprange',
				'parent'	=> '',
				'command'	=> array('--src-range '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'source_net'		=> array(
				'module'	=> '',
				'parent'	=> '',
				'command'	=> array('-s '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'source_type'		=> array(
				'module'	=> 'addrtype',
				'parent'	=> '',
				'command'	=> array('--src-type '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'source_ports'		=> array(
				'module'	=> 'multiport',
				'parent'	=> '',
				'command'	=> array('--sports ', '--sport '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'source_mac'		=> array(
				'module'	=> 'mac',
				'parent'	=> '',
				'command'	=> array('--mac-source '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'destination_interface'	=> array(
				'module'	=> '',
				'parent'	=> '',
				'command'	=> array('-o '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'destination_address'	=> array(
				'module'	=> 'iprange',
				'parent'	=> '',
				'command'	=> array('--dst-range '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'destination_net'	=> array(
				'module'	=> '',
				'parent'	=> '',
				'command'	=> array('-d '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'destination_type'	=> array(
				'module'	=> 'addrtype',
				'parent'	=> '',
				'command'	=> array('--dst-type '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'destination_ports'	=> array(
				'module'	=> 'multiport',
				'parent'	=> '',
				'command'	=> array('--dports ', '--dport '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'icmp'			=> array(
				'module'	=> '',
				'parent'	=> '',
				'command'	=> array('--icmp-type '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'states'		=> array(
				'module'	=> 'state',
				'parent'	=> '',
				'command'	=> array('--state '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'flags'			=> array(
				'module'	=> '',
				'parent'	=> '',
				'command'	=> array('--tcp-flags '),
				'data'		=> 'FIN,SYN,RST,PSH,ACK,URG $',
				'match'		=> '\S+'
		),
		'tos'			=> array(
				'module'	=> 'tos',
				'parent'	=> '',
				'command'	=> array('--tos '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'length'		=> array(
				'module'	=> 'length',
				'parent'	=> '',
				'command'	=> array('--length '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'limit'			=> array(
				'module'	=> 'limit',
				'parent'	=> '',
				'command'	=> array('--limit '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'comment'		=> array(
				'module'	=> 'comment',
				'parent'	=> '',
				'command'	=> array('--comment '),
				'data'		=> '"$"',
				'match'		=> '.+'
		), // TTL must be the last module in rule (Iptables bug)
		'ttl'			=> array(
				'module'	=> 'ttl',
				'parent'	=> '',
				'command'	=> array('--ttl-'),
				'data'		=> '$',
				'match'		=> '\S+ \S+'
		),
		'target'		=> array(
				'module'	=> '',
				'parent'	=> '',
				'command'	=> array('-j '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'nat_source'		=> array(
				'module'	=> '',
				'parent'	=> 'target',
				'command'	=> array('--to-source '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'nat_destination'	=> array(
				'module'	=> '',
				'parent'	=> 'target',
				'command'	=> array('--to-destination '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'nat_ports'	=> array(
				'module'	=> '',
				'parent'	=> 'target',
				'command'	=> array('--to-ports '),
				'data'		=> '$',
				'match'		=> '\S+'
		),
		'nat_map'			=> array(
				'module'	=> '',
				'parent'	=> 'target',
				'command'	=> array('--to '),
				'data'		=> '$',
				'match'		=> '\S+'
		)
	);


	/****************************************************************************/
	/* Default methods */
	/****************************************************************************/

	/**
	* Default class constructor
	* Init dynamic components
	* @param string - Rule to parse
	*/
	public function __construct($rule = null)
	{
		// Init dynamic class members from configuration array
		foreach (self::$_config as $k => $v)
		{
			$this->{'_'.$k} = null;
		}
		// Parse rule if given
		if (!is_null($rule))
		{
			$this->parse($rule);
		}
	}

	/**
	* Convert object to string
	* @return string - Recursive object content
	* @warning Use only in debug context
	*/
	public function __toString()
	{
		return '<pre>'.print_r($this, true).'</pre>';
	}


	/****************************************************************************/
	/* Rule constructors */
	/****************************************************************************/

	/**
	* Build object from rule full string
	* @param string - Rule line
	*/
	public function parse($rule)
	{
		$matches = array();

		// Browse configuration array
		foreach (self::$_config as $k => $v)
		{
			// Browse expected commands
			foreach ($v['command'] as $command)
			{
				// Look for expected strings to match
				if (preg_match('/'. $command . str_replace('$', '('.$v['match'].')', $v['data']) .'/', $rule, $matches))
				{
					$this->{'_'.$k} = $matches[1];
				}
			}
		}
	}

	/**
	* Export object to rule full string
	* @return string - Rule line
	*/
	public function make()
	{
		$args = '';

		// Browse configuration array
		foreach (self::$_config as $k => $v)
		{
			// Build rule from members
			if (!empty($this->{'_'.$k}))
			{
				$arg = ' ';
				// Load netfilter module
				if (!empty($v['module']) && !strpos($args, $v['module']))
				{
					$arg .= '-m '. $v['module'] .' ';
				}
				$arg .= $v['command'][0];

				$args .= $arg . str_replace('$', $this->{'_'.$k}, $v['data']);
			}
		}

		return $args;
	}


	/****************************************************************************/
	/* Getters and setters */
	/****************************************************************************/

	/**
	* Get class member
	* @param string - Member name
	* @return mixed - Member value
	*/
	public function get($var)
	{
		// Test member existency
		if (in_array($var, array_keys(self::$_config)))
		{
			return $this->{'_'.$var};
		}
		else
		{
			return false;
		}
	}

	/**
	* Set class member
	* @param string - Member name
	* @param mixed - Value to set
	* @return boolean - Setting success
	*/
	public function set($var, $val)
	{
		// Test member existency
		if (in_array($var, array_keys(self::$_config)))
		{
			$this->{'_'.$var} = $val;
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	* Get all class members
	* @return array - Members
	*/
	public function getAll()
	{
		$res = array();

		// Browse configuration array
		foreach (self::$_config as $k => $v)
		{
			$res[$k] = $this->{'_'.$k};
		}

		return $res;
	}

	/**
	* Get all class members which depend on parent
	* @param string - Parent name
	* @return array - Members
	*/
	public function getAllChildren($parent)
	{
		$res = array();

		// Browse configuration array
		foreach (self::$_config as $k => $v)
		{
			// Check parent dependency
			if ($v['parent'] == $parent)
			{
				$res[$k] = $this->{'_'.$k};
			}
		}

		return $res;
	}

	/**
	* Set all class members which depend on parent
	* @param string - Parent name
	* @param mixed - Value to set
	* @return boolean - Setting success
	*/
	public function setAllChildren($parent, $val)
	{
		// Browse configuration array
		foreach (self::$_config as $k => $v)
		{
			// Check parent dependency
			if ($v['parent'] == $parent)
			{
				$this->{'_'.$k} = $val;
			}
		}

		return true;
	}

	/**
	* Build command from member values
	* @param string - Member name
	* @return string - Rule command
	*/
	public function getCommand($var)
	{
		$cmd = '';

		// Test member existency and value
		if (in_array($var, array_keys(self::$_config)) && !empty($this->{'_'.$var}))
		{
			// Load netfilter module
			if (!empty(self::$_config[$var]['module']))
			{
				$cmd .= '-m '. self::$_config[$var]['module'] .' ';
			}
			$cmd .= self::$_config[$var]['command'][0];
			$cmd .= str_replace('$', $this->{'_'.$var}, self::$_config[$var]['data']);
		}

		return $cmd;
	}

}
?>
