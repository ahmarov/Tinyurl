<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Tinyurl class
 *
 * @author     Dmitri Ahmarov
 * @copyright  (c) 2011 Dmitri Ahmarov
 * @license    MIT
 */
class Kohana_Tinyurl {

	private static $instance = null;

	public static function instance() {

		if (! self::$instance) {
			self::$instance = new Tinyurl_Core;
		}

		return self::$instance;
	}

	/**
	 * Returns short url
	 *
	 * 		$short_url = Tinyurl::gen_url('http://www.google.com');
	 *
	 * @param array $params
	 * @param Integer $life_time
	 * @return String
	 */
	public static function gen_url($redirect_url, $life_time = 7776000, $return_full_path = TRUE)
	{
		// Generate key
		$key = mb_strtolower(self::encode_id(Redisko::instance()->incr('tinyurl::id')));

		Redisko::instance()->setex('tinyurl::'.$key, $life_time, $redirect_url);
		
		return ($return_full_path ? url::base(TRUE) : '') . 't/'.$key;
	}

	/**
	 * Returns data from Redis
	 * 
	 * @param String $strHashKey
	 * @return String value
	 */
	public static function get_key_data($key)
	{
		return Redisko::instance()->get('tinyurl::'.$key);
	}

	/**
	 * Generates key
	 * 
	 * @param Integer $num
	 * @param String $vkey
	 * @return String
	 */
	private static function encode_id($num, $vkey = 'P71GACRZNT8I42Y5DQ9MULEHVKOX3JB6FSW')
	{
		if ($num > 0x7fffffff)
		{
			return 0;
		}

		$num   = self::mix_bits($num);
		$parts = array();

		do
		{
			$mod = $num % 35;
			$parts[] = $mod;

			if (count($parts) == 5)
			{
				$num = $num << 4;
			}
			else
			{
				$num -= $mod;
			}
			$num = $num / 35;

		} while($num > 35);

		// aAdd parts if less
		while (count($parts) < 6)
		{
			$parts[] = 0;
		}

		// Use our abc
		foreach($parts as $key => $part)
		{
			$parts[$key] = $vkey[$part];
		}

		// Compile code
		return implode($parts);
	}

	private static function mix_bits($num)
	{
		$mix_rule = array(
			24,28,31,6,12,18,
			17,5,11,30,27,23,
			22,26,1,4,10,16,
			21,25,2,3,9,15,
			29,13,19,8,7,14,20,
		);

		$num_2 = 0;

		for($i = 0; $i < count($mix_rule); $i++)
		{
			// get the bit from num
			$t = 1;
			if ($i) $t = $t << $i;
			$bit = $num & $t;
			$bit = $bit >> $i;

			// put the bit
			$t = $bit;
			if ($mix_rule[$i]) $t = $t << ($mix_rule[$i] - 1);
			$num_2 = $num_2 | $t;
		}

		return $num_2;
	}

} // EOF Kohana_Tinyurl