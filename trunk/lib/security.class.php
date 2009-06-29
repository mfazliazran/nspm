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

// Based on PHP Filters library by Gavin Zuchlinski, Jamie Pratt and Hokkaido
// Updated and improved for security enhancement and PHP5/XHTML compliance

// Binary test flags
define('PARANOID', 1);
define('SQL', 2);
define('SYSTEM', 4);
define('HTML', 8);
define('STRING', 16);
define('INT', 32);
define('FLOAT', 64);
define('HEXA', 128);
define('MIN_INT_BOUNDARY', -2147483648);
define('MAX_INT_BOUNDARY', 2147483647);

/**
* Global security class
* @author Regis PHILIPPE
*/
class Security
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
	/* Private methods */
	/****************************************************************************/

	/**
	* Wrapper for addslashes depending on gpc_magic_quotes configuration
	* @param mixed - Data
	* @return mixed - Escaped data
	*/
	private function nice_addslashes($string)
	{
		// String is already quoted by Magic Quotes
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
		{
			return $string;
		}
		else
		{
			return addslashes($string);
		}
	}

	/**
	* Wrapper for stripslashes depending on gpc_magic_quotes configuration
	* @param mixed - Data
	* @return mixed - Unescaped data
	*/
	private function nice_stripslashes($string)
	{
		// String is already quoted by Magic Quotes
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
		{
			return stripslashes($string);
		}
		else
		{
			return $string;
		}
	}

	/**
	* Paranoid sanitization for alphanumeric only string
	* @param mixed - Data
	* @param integer - Minimum length
	* @param integer - Maximum length
	* @return string - Alphanumeric string
	*/
	private function sanitize_paranoid_string($string, $min='', $max='')
	{
		$string = preg_replace('/[^a-zA-Z0-9]/', '', $string);
		$len = strlen($string);
		if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
		{
			return false;
		}
		return $string;
	}

	/**
	* Sanitize a string for SQL queries
	* @param mixed - Data
	* @param integer - Minimum length
	* @param integer - Maximum length
	* @return string - Valid SQL query
	*/
	private function sanitize_sql_string($string, $min='', $max='')
	{
		$string = trim(self::nice_stripslashes($string));
		$len = strlen($string);
		if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
		{
			return false;
		}
		return mysql_escape_string($string);
	}

	/**
	* Sanitize a string for system command execution
	* @param mixed - Data
	* @param integer - Minimum length
	* @param integer - Maximum length
	* @return string - Valid system command
	*/
	private function sanitize_system_string($string, $min='', $max='')
	{
		$string = trim(self::nice_stripslashes($string));
		$len = strlen($string);
		if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
		{
			return false;
		}
		return escapeshellcmd($string);
	}

	/**
	* Sanitize a string for HTML usage
	* @param mixed - Data
	* @param integer - Minimum length
	* @param integer - Maximum length
	* @return string - Valid HTML string
	*/
	private function sanitize_html_string($string, $min='', $max='')
	{
		$string = trim(self::nice_stripslashes($string));
		$len = strlen($string);
		if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
		{
			return false;
		}
		return htmlspecialchars($string, ENT_QUOTES);
	}

	/**
	* Sanitize a string for common usage
	* @param mixed - Data
	* @param integer - Minimum length
	* @param integer - Maximum length
	* @return string - Valid string
	*/
	private function sanitize_string($string, $min='', $max='')
	{
		$string = trim(self::nice_stripslashes($string));
		$len = strlen($string);
		if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
		{
			return false;
		}
		return addslashes(str_replace('"', '\'', $string));
	}

	/**
	* Sanitize integer values
	* @param mixed - Data
	* @param integer - Minimum value
	* @param integer - Maximum value
	* @return integer - Valid integer data
	*/
	private function sanitize_int($integer, $min='', $max='')
	{
		$int = intval($integer);
		if (($min != '') && ($int < intval($min)))
		{
			return $min;
		}
		elseif (($max != '') && ($int > intval($max)))
		{
			return $max;
		}
		return $int;
	}

	/**
	* Sanitize float values
	* @param mixed - Data
	* @param integer - Minimum value
	* @param integer - Maximum value
	* @return float - Valid float data
	*/
	private function sanitize_float($float, $min='', $max='')
	{
		$float = floatval($float);
		if (($min != '') && ($float < floatval($min)))
		{
			return $min;
		}
		elseif (($max != '') && ($float > floatval($max)))
		{
			return $max;
		}
		return $float;
	}

	/**
	* Sanitize hexadecimal values
	* @param mixed - Data
	* @param integer - Minimum value
	* @param integer - Maximum value
	* @return hexa - Valid hexadecimal data
	*/
	private function sanitize_hexa($hexa, $min='', $max='')
	{
		$hexa = hexdec($hexa);
		if (($min != '') && ($hexa < hexdec($min)))
		{
			return $min;
		}
		elseif (($max != '') && ($hexa > hexdec($max)))
		{
			return $max;
		}
		return sprintf('%02X', $hexa);
	}

	/**
	* Sanitize input according to bit flags set
	* @param mixed - Data
	* @param integer - Binary test flags
	* @param integer - Minimum length or value
	* @param integer - Maximum length or value
	* @return mixed - Valid data
	*/
	private function full_sanitize($input, $flags, $min='', $max='')
	{
		if ($flags & PARANOID) $input = self::sanitize_paranoid_string($input, $min, $max);
		if ($flags & SQL) $input = self::sanitize_sql_string($input, $min, $max);
		if ($flags & SYSTEM) $input = self::sanitize_system_string($input, $min, $max);
		if ($flags & HTML) $input = self::sanitize_html_string($input, $min, $max);
		if ($flags & STRING) $input = self::sanitize_string($input, $min, $max);
		if ($flags & INT) $input = self::sanitize_int($input, $min, $max);
		if ($flags & FLOAT) $input = self::sanitize_float($input, $min, $max);
		if ($flags & HEXA) $input = self::sanitize_hexa($input, $min, $max);
		return $input;
	}

	/**
	* Check input validity according to bit flags set
	* @param mixed - Data
	* @param integer - Binary test flags
	* @param integer - Minimum length or value
	* @param integer - Maximum length or value
	* @return boolean - Data validity
	*/
	private function full_check($input, $flags, $min='', $max='')
	{
		$oldput = $input;
		if ($flags & PARANOID) $input = self::sanitize_paranoid_string($input, $min, $max);
		if ($flags & SQL) $input = self::sanitize_sql_string($input, $min, $max);
		if ($flags & SYSTEM) $input = self::sanitize_system_string($input, $min, $max);
		if ($flags & HTML) $input = self::sanitize_html_string($input, $min, $max);
		if ($flags & STRING) $input = self::sanitize_string($input, $min, $max);
		if ($flags & INT) $input = self::sanitize_int($input, $min, $max);
		if ($flags & FLOAT) $input = self::sanitize_float($input, $min, $max);
		if ($flags & HEXA) $input = self::sanitize_hexa($input, $min, $max);
		if (($input != $oldput) || ($input === false))
		{
			return false;
		}
		return true;
	}


	/****************************************************************************/
	/* Public methods */
	/****************************************************************************/

	/**
	* Secure data according to specific requirements
	* @param mixed - Data
	* @param integer - Binary test flags
	* @param integer - Minimum length or value
	* @param integer - Maximum length or value
	* @return mixed - Secured data
	*/
	public static function sanitize($input, $flags, $min = '', $max = '')
	{
		if (isset($input) && isset($flags))
		{
			return self::full_sanitize($input, $flags, $min, $max);
		}
		else
		{
			return false;
		}
	}

	/**
	* Callback function to secure an array of data according to specific requirements
	* @param mixed - Array value
	* @param mixed - Array key
	* @param integer - Binary test flags
	* @param integer - Minimum length or value
	* @param integer - Maximum length or value
	*/
	public static function sanitizeArray(&$value, $key, $flags, $min = '', $max = '')
	{
		if (isset($value) && isset($flags))
		{
			$value = self::full_sanitize($value, $flags, $min, $max);
		}
	}

	/**
	* Check data validity against specific requirements
	* @param mixed - Data
	* @param integer - Binary test flags
	* @param integer - Minimum length or value
	* @param integer - Maximum length or value
	* @return boolean - Data validity
	*/
	public static function check($input, $flags, $min = '', $max = '')
	{
		if (isset($input) && isset($flags))
		{
			return self::full_check($input, $flags, $min, $max);
		}
		else
		{
			return false;
		}
	}

	/**
	* Check data validity againt a set of expected values
	* @param mixed - Data
	* @param array - Expected values
	* @return boolean - Data validity
	*/
	public static function checkOptionValue($input, $options = array())
	{
		if (isset($input))
		{
			// Data is one of the expected values
			if (in_array($input, $options))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	* Check session integrity againt basic session hijacking
	* @return boolean - Session integrity
	*/
	public static function checkSession()
	{
		// Create session token based on client IP address and client user agent
		$token = sha1($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_SERVER['SERVER_ADDR']);
		
		// Session already exists
		if (isset($_SESSION['auth']['token']))
		{
			// Session token is valid
			if ($_SESSION['auth']['token'] == $token)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		// Session token registration
		else
		{
			$_SESSION['auth']['token'] = $token;
			return true;
		}
	}

	/**
	* Check mail address validity
	* @param string - Mail address
	* @return boolean - Address validity
	*/
	public static function checkMail($mail)
	{
		$test = preg_match('/^(([a-z0-9])|([a-z0-9]+[a-z0-9\.\+_-]*[a-z0-9]+))@(([a-z0-9])|([a-z0-9]+[a-z0-9\.-]*[a-z0-9]+))\.[a-z]{2,4}$/i', $mail);
		return ($test > 0 ? true : false);
	}

	/**
	* Check IPv4 address validity
	* @param string - IPv4 address
	* @return boolean - Address validity
	*/
	public static function checkIP($ip)
	{
		$test = preg_match('/^(((25[0-5])|(2[0-4][0-9])|(1[0-9]{2})|([1-9][0-9])|([0-9]))(\.|\z)){4}$/', $ip);
		return ($test > 0 ? true : false);
	}

	/**
	* Check date validity
	* @param integer - Day number
	* @param integer - Month number
	* @param integer - Year number
	* @param boolean - Future date
	* @return boolean - Date validity
	*/
	public static function checkDate($day, $month, $year, $future = false)
	{
		// Check date existance
		if (checkdate($month, $day, $year))
		{
			// Create date timestamp
			$date = mktime(0, 0, 0, $month, $day, $year);
			// Date is - and is expected to be - in the future
			if (($date > time()) && ($future))
			{
				return true;
			}
			// Date is not - and is not expected to be - in the future
			elseif (($date < time()) && (!$future))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}
?>
