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
* System information class
* @author Regis PHILIPPE
*/
class System
{
	/****************************************************************************/
	/* Default methods */
	/****************************************************************************/

	/**
	* Default class constructor
	* @warning All public methods are static, DO NOT instantiate
	*/
	private function __construct()
	{
		// Nothing to do
	}


	/****************************************************************************/
	/* Public methods */
	/****************************************************************************/

	/**
	* Get network interfaces data
	* @return array - Interfaces data
	*/
	public static function getInterfaces()
	{
		$res = array();
		$matches = array();
		$output = self::execCmd(IFCONFIG);
		// Extract interfaces names only
		$ifs = preg_replace('/^(\S+) .*/', '$1', preg_grep('/^\S/', $output));

		// Browse interfaces
		foreach ($ifs as $if)
		{
			$output = self::execCmd(IFCONFIG . " $if");
			$output = implode("\n", $output);
			// Interface is an alias
			if (strpos($if, ':'))
			{
				$if = substr($if, 0, strpos($if, ':'));
				preg_match('/inet addr:\s*([0-9\.]*)\s.*Mask:\s*([0-9\.]*)\s/', $output, $matches);
				$res[$if]['ipv4'][] = (isset($matches[1]) ? $matches[1].'/'.MaskToCIDR($matches[2]) : '');

				preg_match('/inet6 addr:\s*([0-9a-f\/:]*)\s/', $output, $matches);
				$res[$if]['ipv6'][] = (isset($matches[1]) ? $matches[1] : '');
			}
			// Interface exists
			else
			{
				preg_match('/inet addr:\s*([0-9\.]*)\s.*Mask:\s*([0-9\.]*)\s/', $output, $matches);
				$res[$if]['ipv4'][] = (isset($matches[1]) ? $matches[1].'/'.MaskToCIDR($matches[2]) : '0.0.0.0/32');

				preg_match('/inet6 addr:\s*([0-9a-f\/:]*)\s/', $output, $matches);
				$res[$if]['ipv6'][] = (isset($matches[1]) ? $matches[1] : '::0/128');

				preg_match('/Link encap:\s*(\S*)/', $output, $matches);
				$res[$if]['link'] = (isset($matches[1]) ? $matches[1] : 'Unknown');

				preg_match('/HWaddr\s*([0-9a-f:]*)\s/i', $output, $matches);
				$res[$if]['mac'] = (isset($matches[1]) ? $matches[1] : '');

				preg_match('/MTU:\s*([0-9]*)\s/', $output, $matches);
				$res[$if]['mtu'] = (isset($matches[1]) ? $matches[1] : '');

				$res[$if]['up'] = preg_match('/\sUP\s/', $output);
				$res[$if]['running'] = preg_match('/\sRUNNING\s/', $output);

				preg_match('/RX bytes:\s*(\S*).*TX bytes:\s*(\S*)/', $output, $matches);
				$res[$if]['rx'] = (isset($matches[1]) ? $matches[1] : 0);
				$res[$if]['tx'] = (isset($matches[2]) ? $matches[2] : 0);
			}
		}

		return $res;
	}

	/**
	* Get ARP cache data
	* @return array - Local connections
	*/
	public static function getARPCache()
	{
		$res = array();
		$matches = array();
		$output = self::execCmd(ARP);

		// Browse ARP cache
		foreach ($output as $line)
		{
			preg_match('/\(([0-9\.]+)\)/', $line, $matches);
			if (isset($matches[1]))
			{
				$ip = $matches[1];

				preg_match('/at ([0-9A-F:]+)/', $line, $matches);
				$res[$ip]['mac'] = (isset($matches[1]) ? $matches[1] : '');

				preg_match('/\[(\S+)\]/', $line, $matches);
				$res[$ip]['link'] = (isset($matches[1]) ? $matches[1] : '');

				preg_match('/on (\S+)/', $line, $matches);
				$res[$ip]['interface'] = (isset($matches[1]) ? $matches[1] : '');
			}
		}

		return $res;
	}

	/**
	* Get active network connections data
	* @return array - Network connections
	*/
	public static function getConnections()
	{
		$res = array();
		$matches = array();
		$output = self::execCmd(NETSTAT);
		// Extract connections only
		$connections = preg_grep('/[A-Z]+$/', $output);

		// Browse connections
		foreach ($connections as $line)
		{
			preg_match('/^(\S+?)6?\s*(\S+)\s*(\S+)\s*(?:::ffff:)?(\S+):(\S+)\s*(?:::ffff:)?(\S+):(\S+)\s*(\S+)$/', $line, $matches);
			$res[] = array(
				'protocol'	=> $matches[1],
				'recvq'		=> $matches[2],
				'sendq'		=> $matches[3],
				'local_address'	=> $matches[4],
				'local_port'	=> $matches[5],
				'remote_address'=> $matches[6],
				'remote_port'	=> $matches[7],
				'state'		=> $matches[8]
			);
		}

		return $res;
	}

	/**
	* Get operating system data
	* @return string - System name and version
	*/
	public static function getSystemName()
	{
		$output = self::execCmd(UNAME);
		return $output[0];
	}

	/**
	* Get server uptime and current system load
	* @return string - System uptime and load
	*/
	public static function getSystemUptime()
	{
		$output = self::execCmd(UPTIME);
		return $output[0];
	}

	/**
	* Get current CPU usage and status
	* @return array - CPU data
	*/
	public static function getSystemCPU()
	{
		$res = array();
		$matches = array();
		$output = self::execCmd(CPUINFO);

		// Browse system data
		foreach ($output as $line)
		{
			preg_match('/^(.+)\s+:\s+(.+)\s*/', $line, $matches);
			if (!empty($matches))
			{
				$res[strtolower(str_replace(' ', '_', trim($matches[1])))] = $matches[2];
			}
		}

		return $res;
	}

	/**
	* Get current memory usage and status
	* @return array - Memory data
	*/
	public static function getSystemMemory()
	{
		$res = array();
		$matches = array();
		$output = self::execCmd(MEMINFO);

		// Browse system data
		foreach ($output as $line)
		{
			preg_match('/^(.+?)\s*:\s*(\d+)/', $line, $matches);
			if (!empty($matches))
			{
				$res[strtolower(str_replace(' ', '_', trim($matches[1])))] = $matches[2];
			}
		}

		return $res;
	}


	/****************************************************************************/
	/* System command execution methods */
	/****************************************************************************/

	/**
	* Execute system command with output result
	* @param string - System command
	* @return array - Output lines
	*/
	public static function execCmd($cmd)
	{
		$output = '';
		$exit = 0;
		$return = exec(Security::sanitize($cmd, SYSTEM).' 2>&1', $output, $exit);

		if ($exit != 0) {
			niceExit('system', "Exec error: non-null exit value ($exit)", Security::sanitize($cmd, SYSTEM), $output[0]);
		}

		return $output;
	}

	/**
	* Execute system command without output result
	* @param string - System command
	* @return string - Last line of command output
	*/
	public static function systemCmd($cmd)
	{
		$output = '';
		$exit = 0;
		$return = exec(Security::sanitize($cmd, SYSTEM).' 2>&1', $output, $exit);

		if ($exit != 0) {
			niceExit('system', "System error: non-null exit value ($exit)", Security::sanitize($cmd, SYSTEM), $output[0]);
		}

		return $return;
	}
}
?>
