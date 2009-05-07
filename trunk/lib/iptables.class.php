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
* Iptables management class
* @author Regis PHILIPPE
*/
class Iptables
{
	/****************************************************************************/
	/* Class members */
	/****************************************************************************/

	/**
	* Class singleton for unique object instantiation
	* @var object
	*/
	private static $_instance = null;

	/**
	* Current object state
	* 0 = unmodified
	* 1 = modified but not saved
	* 2 = modified and saved
	* @var int
	*/
	private $_state = 0;

	/**
	* Configuration data
	* @var array
	*/
	private $_config = null;


	/****************************************************************************/
	/* Singleton pattern */
	/****************************************************************************/

	/**
	* Singleton access method
	* Create object on first call
	* @param array - Configuration data
	* @return object - Object instance
	*/
	public static function getInstance($config = null)
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new Iptables($config);
		}

		return self::$_instance;
	}

	/**
	* Default class constructor
	* @param array - Configuration data
	*/
	private function __construct($config = null)
	{
		if (is_null($config))
		{
			$this->loadConfig();
		}
		else
		{
			$this->_config = $config;
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
	/* Data loading and registering */
	/****************************************************************************/

	/**
	* Load current system firewall configuration
	*/
	private function loadConfig()
	{
		$output = array();

		// Browse tables
		foreach ($GLOBALS['OPTIONS']['tables'] as $table => $chains)
		{
			// Browse chains
			foreach ($chains as $chain)
			{
				$this->_config[$table][$chain]['policy'] = 'ACCEPT';
			}
			// Init table by listing rules
			// Allows Iptables startup on fresh install
			System::systemCmd($this->cmdList($table));
		}

		// Dump full Iptables configuration
		$output = System::execCmd(IPTABLES_SAVE);
		$table = '';
		$chain = '';

		// Browse configuration lines
		foreach ($output as $line)
		{
			// New table definition
			if (preg_match('/^\*([a-z]+)/', $line, $matches))
			{
				$table = $matches[1];
			}
			// Chain and default policy definition
			elseif (preg_match('/^:([A-Z]+) ([A-Z]+) .*/', $line, $matches))
			{
				$chain = $matches[1];
				$this->_config[$table][$chain]['policy'] = $matches[2];
				$this->_config[$table][$chain]['rules'] = array();
			}
			// Rule definition
			elseif (preg_match('/^\-A ([A-Z]+) (.+)/', $line, $matches))
			{
				$this->_config[$table][$matches[1]]['rules'][] = new Rule($matches[2]);
			}
		}
	}

	/**
	* Register current configuration in system firewall
	*/
	private function saveConfig()
	{
		// Browse tables
		foreach ($this->_config as $table => $chains)
		{
			// Overwrite configuration for known tables only
			if (in_array($table, array_keys($GLOBALS['OPTIONS']['tables'])))
			{
				// Flush current system configuration
				System::systemCmd($this->cmdFlush($table));
				System::systemCmd($this->cmdFlushUser($table));
				System::systemCmd($this->cmdNewUser($table, 'disable'));

				// Browse chains
				foreach ($chains as $chain => $data)
				{
					// Set chain default policy
					System::systemCmd($this->cmdPolicy($table, $chain, $data['policy']));
					// Append rules
					foreach ($data['rules'] as $rule)
					{
						if ($rule->get('target') == 'disable')
						{
							$rule->set('nat', null);
						}
						System::systemCmd($this->cmdAppend($table, $chain, $rule->make()));
					}
				}
			}
		}
	}


	/****************************************************************************/
	/* Transactional mechanisms */
	/****************************************************************************/

	/**
	* Register current transaction configuration
	* @warning Changes will be immediatly applied to system!
	*/
	public function commit()
	{
		$this->saveConfig();
		$this->loadConfig();
		$this->_state = 2;
	}

	/**
	* Restore previous configuration state
	* @warning All changes will be definitly lost!
	*/
	public function rollback()
	{
		$this->loadConfig();
		$this->_state = 0;
	}


	/****************************************************************************/
	/* Iptables commands generators */
	/****************************************************************************/

	/**
	* List chain rules and counters
	* @param string - Table name
	* @param string - Chain name
	* @return string - Iptables command
	*/
	private function cmdList($table, $chain = '')
	{
		return IPTABLES . " -t $table -n -L $chain";
	}

	/**
	* Set chain default policy
	* @param string - Table name
	* @param string - Chain name
	* @param string - Policy name
	* @return string - Iptables command
	*/
	private function cmdPolicy($table, $chain, $policy)
	{
		return IPTABLES . " -t $table -P $chain $policy";
	}

	/**
	* Delete chain rules or all table rules if chain is ommited
	* @param string - Table name
	* @param string - Chain name
	* @return string - Iptables command
	*/
	private function cmdFlush($table, $chain = '')
	{
		return IPTABLES . " -t $table -F $chain";
	}

	/**
	* Delete user-defined chain or all table user-defined chains if chain is ommited
	* @param string - Table name
	* @param string - Chain name
	* @return string - Iptables command
	*/
	private function cmdFlushUser($table, $chain = '')
	{
		return IPTABLES . " -t $table -X $chain";
	}

	/**
	* Create user-defined chain
	* @param string - Table name
	* @param string - Chain name
	* @return string - Iptables command
	*/
	private function cmdNewUser($table, $chain)
	{
		return IPTABLES . " -t $table -N $chain";
	}

	/**
	* Append rule in chain
	* @param string - Table name
	* @param string - Chain name
	* @param string - Rule definition
	* @return string - Iptables command
	*/
	private function cmdAppend($table, $chain, $rule)
	{
		return IPTABLES . " -t $table -A $chain $rule";
	}

	/**
	* Insert rule in chain at given index or as first rule if index is ommited
	* @param string - Table name
	* @param string - Chain name
	* @param string - Rule definition
	* @param integer - Rule index
	* @return string - Iptables command
	*/
	private function cmdInsert($table, $chain, $rule, $index = 1)
	{
		return IPTABLES . " -t $table -I $chain $index $rule";
	}

	/**
	* Delete rule in chain at given index
	* @param string - Table name
	* @param string - Chain name
	* @param integer - Rule index
	* @return string - Iptables command
	*/
	private function cmdDelete($table, $chain, $index)
	{
		return IPTABLES . " -t $table -D $chain $index";
	}


	/****************************************************************************/
	/* Getters methods */
	/****************************************************************************/

	/**
	* Get full configuration data
	* @return array - Configuration data
	*/
	public function getConfig()
	{
		return $this->_config;
	}

	/**
	* Get table data
	* @param string - Table name
	* @return mixed - Table data
	*/
	public function getTable($table)
	{
		if (isset($this->_config[$table]))
		{
			return $this->_config[$table];
		}
		else
		{
			return false;
		}
	}

	/**
	* Get chain data
	* @param string - Table name
	* @param string - Chain name
	* @return mixed - Chain data
	*/
	public function getChain($table, $chain)
	{
		if (isset($this->_config[$table][$chain]))
		{
			return $this->_config[$table][$chain];
		}
		else
		{
			return false;
		}
	}

	/**
	* Get chain policy
	* @param string - Table name
	* @param string - Chain name
	* @return mixed - Chain policy
	*/
	public function getPolicy($table, $chain)
	{
		if (isset($this->_config[$table][$chain]['policy']))
		{
			return $this->_config[$table][$chain]['policy'];
		}
		else
		{
			return false;
		}
	}

	/**
	* Get chain rules
	* @param string - Table name
	* @param string - Chain name
	* @return mixed - Rules list
	*/
	public function getRules($table, $chain)
	{
		if (isset($this->_config[$table][$chain]['rules']))
		{
			return $this->_config[$table][$chain]['rules'];
		}
		else
		{
			return false;
		}
	}

	/**
	* Get a specific rule
	* @param string - Table name
	* @param string - Chain name
	* @param int - Rule index
	* @return mixed - Rule data
	*/
	public function getRule($table, $chain, $index)
	{
		if (isset($this->_config[$table][$chain]['rules'][$index-1]))
		{
			return $this->_config[$table][$chain]['rules'][$index-1];
		}
		else
		{
			return false;
		}
	}

	/**
	* Get chain rules count
	* @param string - Table name
	* @param string - Chain name
	* @return mixed - Rules count
	*/
	public function countRules($table, $chain)
	{
		if (isset($this->_config[$table][$chain]['rules']))
		{
			return count($this->_config[$table][$chain]['rules']);
		}
		else
		{
			return false;
		}
	}

	/**
	* Get transaction state
	* @return int - Current state
	*/
	public function getTransactionState()
	{
		return $this->_state;
	}


	/****************************************************************************/
	/* Setters methods */
	/****************************************************************************/

	/**
	* Set transaction state
	* @param int - New state
	*/
	public function setTransactionState($state)
	{
		return $this->_state = $state;
	}

	/**
	* Set chain default policy
	* @param string - Table name
	* @param string - Chain name
	* @param string - New policy
	*/
	public function setPolicy($table, $chain, $policy)
	{
		$this->_config[$table][$chain]['policy'] = $policy;
		$this->_state = 1;
	}

	/**
	* Reverse chain default policy
	* @param string - Table name
	* @param string - Chain name
	*/
	public function reversePolicy($table, $chain)
	{
		if ($this->_config[$table][$chain]['policy'] == 'ACCEPT')
		{
			$this->_config[$table][$chain]['policy'] = 'DROP';
		}
		else
		{
			$this->_config[$table][$chain]['policy'] = 'ACCEPT';
		}
		$this->_state = 1;
	}

	/**
	* Add a new rule
	* @param string - Table name
	* @param string - Chain name
	* @param string - New rule
	*/
	public function addRule($table, $chain, $rule)
	{
		$this->_config[$table][$chain]['rules'][] = $rule;
		$this->_state = 1;
	}

	/**
	* Replace an existing rule
	* @param string - Table name
	* @param string - Chain name
	* @param int - Rule index
	* @param string - New rule
	*/
	public function changeRule($table, $chain, $index, $rule)
	{
		$this->_config[$table][$chain]['rules'][$index-1] = $rule;
		$this->_state = 1;
	}

	/**
	* Move an existing rule
	* @param string - Table name
	* @param string - Chain name
	* @param int - Rule initial index
	* @param int - Rule new index
	*/
	public function moveRule($table, $chain, $index, $new)
	{
		$tmp = $this->_config[$table][$chain]['rules'][$index-1];
		$this->_config[$table][$chain]['rules'][$index-1] = $this->_config[$table][$chain]['rules'][$new-1];
		$this->_config[$table][$chain]['rules'][$new-1] = $tmp;
		$this->_state = 1;
	}

	/**
	* Copy an existing rule
	* @param string - Table name
	* @param string - Chain name
	* @param int - Rule index
	*/
	public function copyRule($table, $chain, $index)
	{
		$rule = $this->_config[$table][$chain]['rules'][$index-1];
		$this->_config[$table][$chain]['rules'][] = $rule;
		$this->_state = 1;
	}

	/**
	* Delete an existing rule
	* @param string - Table name
	* @param string - Chain name
	* @param int - Rule index
	*/
	public function deleteRule($table, $chain, $index)
	{
		array_splice($this->_config[$table][$chain]['rules'], $index-1, 1);
		$this->_state = 1;
	}

	/**
	* Disable a specific rule
	* @param string - Table name
	* @param string - Chain name
	* @param int - Rule index
	*/
	public function disableRule($table, $chain, $index)
	{
		$rule = $this->_config[$table][$chain]['rules'][$index-1];

		// Rule is not already disabled
		if ($rule->get('target') != 'disable')
		{
			// Current target is added to comment
			$rule->set('comment', '@'.$rule->get('target').' '.$rule->getCommand('nat').' @'.$rule->get('comment'));
			$rule->set('target', 'disable');
			$this->_state = 1;
		}
	}

	/**
	* Enable a specific rule
	* @param string - Table name
	* @param string - Chain name
	* @param int - Rule index
	*/
	public function enableRule($table, $chain, $index)
	{
		$matches = array();
		$rule = $this->_config[$table][$chain]['rules'][$index-1];

		// Rule is disabled and includes commented data to restore
		if (($rule->get('target') == 'disable') && preg_match('/\@([^\@]+)\@(.*)/', $rule->get('comment'), $matches))
		{
			$rule->set('target', $matches[1]);
			$rule->set('comment', (isset($matches[2]) ? $matches[2] : null));
			$rule->parse($rule->make());
			$this->_state = 1;
		}
	}
}
?>
