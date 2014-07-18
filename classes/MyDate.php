<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Library
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Contao;
use DateTime,DateTimeZone;


/**
 * Converts dates and date format string
 *
 * The class converts arbitrary date strings to Unix timestamps and provides
 * extended information like the begin or end of the day, week, month or year.
 *
 * Usage:
 *
 *     $date = new Date();
 *     echo $date->datim;
 *
 *     $date = new Date('2011-09-18', 'Y-m-d');
 *     echo $date->monthBegin;
 *
 *     Date::formatToJs('m/d/Y H:i');
 *
 * @package   Library
 * @author    Leo Feyer <https://github.com/leofeyer>
 * @copyright Leo Feyer 2005-2014
 */
class Date
{

	/**
	 * Date string
	 * @var int
	 */
	protected $strDate;

	/**
	 * Format string
	 * @var string
	 */
	protected $strFormat;

	/**
	 * Formatted date
	 * @var string
	 */
	protected $strToDate;

	/**
	 * Formatted time
	 * @var string
	 */
	protected $strToTime;

	/**
	 * Formatted date and time
	 * @var string
	 */
	protected $strToDatim;

	/**
	 * Date range
	 * @var array
	 */
	protected $arrRange = array();


	/**
	 * Create the object properties and date ranges
	 *
	 * @param integer $strDate   An optional date string
	 * @param string  $strFormat An optional format string
	 */
	public function __construct($strDate=null, $strFormat=null)
	{
		$this->strDate = ($strDate !== null) ? $strDate : time();
		$this->strFormat = ($strFormat !== null) ? $strFormat : static::getNumericDateFormat();

		if (!preg_match('/^\-?[0-9]+$/', $this->strDate) || preg_match('/^[a-zA-Z]+$/', $this->strFormat))
		{
			$this->dateToUnix();
		}

		// Create the formatted dates
		$this->strToDate = static::parse(static::getNumericDateFormat(), $this->strDate);
		$this->strToTime = static::parse(static::getNumericTimeFormat(), $this->strDate);
		$this->strToDatim = static::parse(static::getNumericDatimFormat(), $this->strDate);

		$intYear = date('Y', $this->strDate);
		$intMonth = date('m', $this->strDate);
		$intDay = date('d', $this->strDate);

		// Create the date ranges
		$this->arrRange['day']['begin'] = mktime(0, 0, 0, $intMonth, $intDay, $intYear);
		$this->arrRange['day']['end'] = mktime(23, 59, 59, $intMonth, $intDay, $intYear);
		$this->arrRange['month']['begin'] = mktime(0, 0, 0, $intMonth, 1, $intYear);
		$this->arrRange['month']['end'] = mktime(23, 59, 59, $intMonth, date('t', $this->strDate), $intYear);
		$this->arrRange['year']['begin'] = mktime(0, 0, 0, 1, 1, $intYear);
		$this->arrRange['year']['end'] = mktime(23, 59, 59, 12, 31, $intYear);
	}


	/**
	 * Return an object property
	 *
	 * Supported keys:
	 *
	 * * timestamp:  the Unix timestamp
	 * * date:       the formatted date
	 * * time:       the formatted time
	 * * datim:      the formatted date and time
	 * * dayBegin:   the beginning of the current day
	 * * dayEnd:     the end of the current day
	 * * monthBegin: the beginning of the current month
	 * * monthEnd:   the end of the current month
	 * * yearBegin:  the beginning of the current year
	 * * yearEnd:    the end of the current year
	 * * format:     the date format string
	 *
	 * @param string $strKey The property name
	 *
	 * @return mixed|null The property value
	 */
	public function __get($strKey)
	{
		switch ($strKey)
		{
			case 'tstamp':
			case 'timestamp':
				return $this->strDate;
				break;

			case 'date':
				return $this->strToDate;
				break;

			case 'time':
				return $this->strToTime;
				break;

			case 'datim':
				return $this->strToDatim;
				break;

			case 'dayBegin':
				return $this->arrRange['day']['begin'];
				break;

			case 'dayEnd':
				return $this->arrRange['day']['end'];
				break;

			case 'monthBegin':
				return $this->arrRange['month']['begin'];
				break;

			case 'monthEnd':
				return $this->arrRange['month']['end'];
				break;

			case 'yearBegin':
				return $this->arrRange['year']['begin'];
				break;

			case 'yearEnd':
				return $this->arrRange['year']['end'];
				break;

			case 'format':
				return $this->strFormat;
				break;
		}

		return null;
	}


	/**
	 * Return the begin of the week as timestamp
	 *
	 * @param integer $intStartDay The week start day
	 *
	 * @return integer The Unix timestamp
	 */
	public function getWeekBegin($intStartDay=0)
	{
		$intOffset = date('w', $this->strDate) - $intStartDay;

		if ($intOffset < 0)
		{
			$intOffset += 7;
		}

		return strtotime('-' . $intOffset . ' days', $this->strDate);
	}


	/**
	 * Return the end of the week as timestamp
	 *
	 * @param integer $intStartDay The week start day
	 *
	 * @return integer The Unix timestamp
	 */
	public function getWeekEnd($intStartDay=0)
	{
		return strtotime('+1 week', $this->getWeekBegin($intStartDay)) - 1;
	}


	/**
	 * Return a regular expression to check a date
	 *
	 * @param string $strFormat An optional format string
	 *
	 * @return string The regular expression string
	 *
	 * @throws \Exception If $strFormat is invalid
	 */
	public static function getRegexp($strFormat=null)
	{
		if ($strFormat === null)
		{
			$strFormat = static::getNumericDateFormat();
		}

		if (!static::isNumericFormat($strFormat))
		{
			throw new \Exception(sprintf('Invalid date format "%s"', $strFormat));
		}

		return preg_replace_callback('/[a-zA-Z]/', function($matches)
			{
				// Thanks to Christian Labuda
				$arrRegexp = array
				(
					'a' => '(?P<a>am|pm)',
					'A' => '(?P<A>AM|PM)',
					'd' => '(?P<d>0[1-9]|[12][0-9]|3[01])',
					'g' => '(?P<g>[1-9]|1[0-2])',
					'G' => '(?P<G>[0-9]|1[0-9]|2[0-3])',
					'h' => '(?P<h>0[1-9]|1[0-2])',
					'H' => '(?P<H>[01][0-9]|2[0-3])',
					'i' => '(?P<i>[0-5][0-9])',
					'j' => '(?P<j>[1-9]|[12][0-9]|3[01])',
					'm' => '(?P<m>0[1-9]|1[0-2])',
					'n' => '(?P<n>[1-9]|1[0-2])',
					's' => '(?P<s>[0-5][0-9])',
					'Y' => '(?P<Y>[0-9]{4})',
					'y' => '(?P<y>[0-9]{2})',
				);

				return isset($arrRegexp[$matches[0]]) ? $arrRegexp[$matches[0]] : $matches[0];
			}
		, preg_quote($strFormat));
	}


	/**
	 * Return an input format string for a particular date (e.g. YYYY-MM-DD)
	 *
	 * @param string $strFormat An optional format string
	 *
	 * @return string The input format string
	 *
	 * @throws \Exception If $strFormat is invalid
	 */
	public static function getInputFormat($strFormat=null)
	{
		if ($strFormat === null)
		{
			$strFormat = static::getNumericDateFormat();
		}

		if (!static::isNumericFormat($strFormat))
		{
			throw new \Exception(sprintf('Invalid date format "%s"', $strFormat));
		}

		$arrCharacterMapper = array
		(
			'a' => 'am',
			'A' => 'AM',
			'd' => 'DD',
			'j' => 'D',
			'm' => 'MM',
			'n' => 'M',
			'y' => 'YY',
			'Y' => 'YYYY',
			'h' => 'hh',
			'H' => 'hh',
			'g' => 'h',
			'G' => 'h',
			'i' => 'mm',
			's' => 'ss',
		);

		$arrInputFormat = array();
		$arrCharacters = str_split($strFormat);

		foreach ($arrCharacters as $strCharacter)
		{
			if (isset($arrCharacterMapper[$strCharacter]))
			{
				$arrInputFormat[$strFormat] .= $arrCharacterMapper[$strCharacter];
			}
			else
			{
				$arrInputFormat[$strFormat] .= $strCharacter;
			}
		}

		return $arrInputFormat[$strFormat];
	}


	/**
	 * Convert a date string into a Unix timestamp using the format string
	 *
	 * @throws \Exception            If the format string is invalid
	 * @throws \OutOfBoundsException If the timestamp does not map to a valid date
	 */
	protected function dateToUnix()
	{
		if (!static::isNumericFormat($this->strFormat))
		{
			throw new \Exception(sprintf('Invalid date format "%s"', $this->strFormat));
		}

		$intCount  = 0;
		$intDay    = '';
		$intMonth  = '';
		$intYear   = '';
		$intHour   = '';
		$intMinute = '';
		$intSecond = '';

		$blnMeridiem = false;
		$blnCorrectHour = false;

		$arrCharacterMapper = array
		(
			'd' => 'intDay',
			'j' => 'intDay',
			'm' => 'intMonth',
			'n' => 'intMonth',
			'y' => 'intYear',
			'Y' => 'intYear',
			'h' => 'intHour',
			'H' => 'intHour',
			'g' => 'intHour',
			'G' => 'intHour',
			'i' => 'intMinute',
			's' => 'intSecond'
		);

		$arrCharacters = str_split($this->strFormat);

		foreach ($arrCharacters as $strCharacter)
		{
			$var = isset($arrCharacterMapper[$strCharacter]) ? $arrCharacterMapper[$strCharacter] : 'dummy';

			switch ($strCharacter)
			{
				case 'a':
				case 'A':
					$blnCorrectHour = true;
					$blnMeridiem = (strtolower(substr($this->strDate, $intCount, 2)) == 'pm') ? true : false;
					$intCount += 2;
					break;

				case 'd':
				case 'm':
				case 'y':
				case 'h':
				case 'H':
				case 'i':
				case 's':
					$$var .= substr($this->strDate, $intCount, 2);
					$intCount += 2;
					break;

				case 'j':
				case 'n':
				case 'g':
				case 'G':
					$$var .= substr($this->strDate, $intCount++, 1);

					if (preg_match('/[0-9]+/', substr($this->strDate, $intCount, 1)))
					{
						$$var .= substr($this->strDate, $intCount++, 1);
					}
					break;

				case 'Y':
					$$var .= substr($this->strDate, $intCount, 4);
					$intCount += 4;
					break;

				default:
					++$intCount;
					break;
			}
		}

		$intHour = (int) $intHour;

		if ($blnMeridiem)
		{
			$intHour += 12;
		}

		if ($blnCorrectHour && ($intHour == 12 || $intHour == 24))
		{
			$intHour -= 12;
		}

		if (!strlen($intMonth))
		{
			$intMonth = 1;
		}

		if (!strlen($intDay))
		{
			$intDay = 1;
		}

		if ($intYear == '')
		{
			$intYear = 1970;
		}

		// Validate the date (see #5086)
		if (checkdate($intMonth, $intDay, $intYear) === false)
		{
			throw new \OutOfBoundsException(sprintf('Invalid date "%s"', $this->strDate));
		}

		$this->strDate =  mktime((int) $intHour, (int) $intMinute, (int) $intSecond, (int) $intMonth, (int) $intDay, (int) $intYear);
	}


	/**
	 * Convert a PHP format string into a JavaScript format string
	 *
	 * @param string $strFormat The PHP format string
	 *
	 * @return mixed The JavaScript format string
	 */
	public static function formatToJs($strFormat)
	{
		$chunks = str_split($strFormat);

		foreach ($chunks as $k=>$v)
		{
			switch ($v)
			{
				case 'D': $chunks[$k] = 'a'; break;
				case 'j': $chunks[$k] = 'e'; break;
				case 'l': $chunks[$k] = 'A'; break;
				case 'S': $chunks[$k] = 'o'; break;
				case 'F': $chunks[$k] = 'B'; break;
				case 'M': $chunks[$k] = 'b'; break;
				case 'a': $chunks[$k] = 'p'; break;
				case 'A': $chunks[$k] = 'p'; break;
				case 'g': $chunks[$k] = 'l'; break;
				case 'G': $chunks[$k] = 'k'; break;
				case 'h': $chunks[$k] = 'I'; break;
				case 'i': $chunks[$k] = 'M'; break;
				case 's': $chunks[$k] = 'S'; break;
				case 'U': $chunks[$k] = 's'; break;
			}
		}

		return preg_replace('/([a-zA-Z])/', '%$1', implode('', $chunks));
	}


	/**
	 * Check for a numeric date format
	 *
	 * @param string $strFormat The PHP format string
	 *
	 * @return boolean True if the date format is numeric
	 */
	public static function isNumericFormat($strFormat)
	{
		return !preg_match('/[BbCcDEeFfIJKkLlMNOoPpQqRrSTtUuVvWwXxZz]+/', $strFormat);
	}


	/**
	 * Return the numeric date format string
	 *
	 * @return string The numeric date format string
	 */
	public static function getNumericDateFormat()
	{
		if (TL_MODE == 'FE')
		{
			global $objPage;

			if ($objPage->dateFormat != '' && static::isNumericFormat($objPage->dateFormat))
			{
				return $objPage->dateFormat;
			}
		}

		return $GLOBALS['TL_CONFIG']['dateFormat'];
	}


	/**
	 * Return the numeric time format string
	 *
	 * @return string The numeric time format string
	 */
	public static function getNumericTimeFormat()
	{
		if (TL_MODE == 'FE')
		{
			global $objPage;

			if ($objPage->timeFormat != '' && static::isNumericFormat($objPage->timeFormat))
			{
				return $objPage->timeFormat;
			}
		}

		return $GLOBALS['TL_CONFIG']['timeFormat'];
	}


	/**
	 * Return the numeric datim format string
	 *
	 * @return string The numeric datim format string
	 */
	public static function getNumericDatimFormat()
	{
		if (TL_MODE == 'FE')
		{
			global $objPage;

			if ($objPage->datimFormat != '' && static::isNumericFormat($objPage->datimFormat))
			{
				return $objPage->datimFormat;
			}
		}

		return $GLOBALS['TL_CONFIG']['datimFormat'];
	}


	/**
	 * Parse a date format string and translate textual representations
	 *
	 * @param string  $strFormat The date format string
	 * @param integer $intTstamp An optional timestamp
	 *
	 * @return string The textual representation of the date
	 */
	public static function parse($strFormat, $intTstamp=null)
	{
		$strModified = str_replace
		(
			array('l', 'D', 'F', 'M'),
			array('w::1', 'w::2', 'n::3', 'n::4'),
			$strFormat
		);

		if ($intTstamp === null)
		{
			if (TL_MODE == 'FE' && $GLOBALS['TL_LANGUAGE'] == 'fa') {
				$strDate = PersianDate::date($strFormat,$intTstamp);
			} else {
				$strDate = date($strModified);
			}
		}
		elseif (!is_numeric($intTstamp))
		{
			return '';
		}
		else
		{
			if (TL_MODE == 'FE' && $GLOBALS['TL_LANGUAGE'] == 'fa') {
				$strDate = PersianDate::date($strFormat,$intTstamp);
			} else {
				$strDate = date($strModified);
			}
		}

		if (strpos($strDate, '::') === false)
		{
			return $strDate;
		}

		if (!$GLOBALS['TL_LANG']['MSC']['dayShortLength'])
		{
			$GLOBALS['TL_LANG']['MSC']['dayShortLength'] = 3;
		}

		if (!$GLOBALS['TL_LANG']['MSC']['monthShortLength'])
		{
			$GLOBALS['TL_LANG']['MSC']['monthShortLength'] = 3;
		}

		$strReturn = '';
		$chunks = preg_split("/([0-9]{1,2}::[1-4])/", $strDate, -1, PREG_SPLIT_DELIM_CAPTURE);

		foreach ($chunks as $chunk)
		{
			list($index, $flag) = explode('::', $chunk);

			switch ($flag)
			{
				case 1:
					$strReturn .= $GLOBALS['TL_LANG']['DAYS'][$index];
					break;

				case 2:
					$strReturn .= $GLOBALS['TL_LANG']['DAYS_SHORT'][$index];
					break;

				case 3:
					$strReturn .= $GLOBALS['TL_LANG']['MONTHS'][($index - 1)];
					break;

				case 4:
					$strReturn .= $GLOBALS['TL_LANG']['MONTHS_SHORT'][($index - 1)];
					break;

				default:
					$strReturn .= $chunk;
					break;
			}
		}

		return $strReturn;
	}
}


/**
 * Class PersianDate
 *
 * @copyright  2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class PersianDate
{

    /**
     * Defaults
     */
    private static $jalali   = true; //Use Jalali Date, If set to false, falls back to gregorian
    private static $convert  = true; //Convert numbers to Farsi characters in utf-8
    private static $timezone = null; //Timezone String e.g Asia/Tehran, Defaults to Server Timezone Settings
    private static $temp = array();

    /**
     * jDateTime::Constructor
     *
     * Pass these parameteres when creating a new instance
     * of this Class, and they will be used as defaults.
     * e.g $obj = new jDateTime(false, true, 'Asia/Tehran');
     * To use system defaults pass null for each one or just
     * create the object without any parameters.
     *
     * @author Sallar Kaboli
     * @param $convert bool Converts numbers to Farsi
     * @param $jalali bool Converts date to Jalali
     * @param $timezone string Timezone string
     */
    public function __construct($convert = null, $jalali = null, $timezone = null)
    {
        if ( $jalali   !== null ) self::$jalali   = (bool) $jalali;
        if ( $convert  !== null ) self::$convert  = (bool) $convert;
        if ( $timezone !== null ) self::$timezone = $timezone;
    }

    /**
     * jDateTime::Date
     *
     * Formats and returns given timestamp just like php's
     * built in date() function.
     * e.g:
     * $obj->date("Y-m-d H:i", time());
     * $obj->date("Y-m-d", time(), false, false, 'America/New_York');
     *
     * @author Sallar Kaboli
     * @param $format string Acceps format string based on: php.net/date
     * @param $stamp int Unix Timestamp (Epoch Time)
     * @param $convert bool (Optional) forces convert action. pass null to use system default
     * @param $jalali bool (Optional) forces jalali conversion. pass null to use system default
     * @param $timezone string (Optional) forces a different timezone. pass null to use system default
     * @return string Formatted input
     */
    public static function date($format, $stamp = false, $convert = null, $jalali = null, $timezone = null)
    {
        //Timestamp + Timezone
        $stamp    = ($stamp !== false) ? $stamp : time();
        $timezone = ($timezone != null) ? $timezone : ((self::$timezone != null) ? self::$timezone : date_default_timezone_get());
        $obj      = new DateTime('@' . $stamp, new DateTimeZone($timezone));
        $obj->setTimezone(new DateTimeZone($timezone));

        if ( (self::$jalali === false && $jalali === null) || $jalali === false ) {
            return $obj->format($format);
        }
        else {

            //Find what to replace
            $chars  = (preg_match_all('/([a-zA-Z]{1})/', $format, $chars)) ? $chars[0] : array();

            //Intact Keys
            $intact = array('B','h','H','g','G','i','s','I','U','u','Z','O','P');
            $intact = self::filterArray($chars, $intact);
            $intactValues = array();

            foreach ($intact as $k => $v) {
                $intactValues[$k] = $obj->format($v);
            }
            //End Intact Keys


            //Changed Keys
            list($year, $month, $day) = array($obj->format('Y'), $obj->format('n'), $obj->format('j'));
            list($jyear, $jmonth, $jday) = self::toJalali($year, $month, $day);

            $keys   = array('d','D','j','l','N','S','w','z','W','F','m','M','n','t','L','o','Y','y','a','A','c','r','e','T');
            $keys   = self::filterArray($chars, $keys, array('z'));
            $values = array();

            foreach ($keys as $k => $key) {

                $v = '';
                switch ($key) {
                    //Day
                    case 'd':
                        $v = sprintf('%02d', $jday);
                        break;
                    case 'D':
                        $v = self::getDayNames($obj->format('D'), true);
                        break;
                    case 'j':
                        $v = $jday;
                        break;
                    case 'l':
                        $v = self::getDayNames($obj->format('l'));
                        break;
                    case 'N':
                        $v = self::getDayNames($obj->format('l'), false, 1, true);
                        break;
                    case 'S':
                        $v = 'ام';
                        break;
                    case 'w':
                        $v = self::getDayNames($obj->format('l'), false, 1, true) - 1;
                        break;
                    case 'z':
                        if ($jmonth > 6) {
                            $v = 186 + (($jmonth - 6 - 1) * 30) + $jday;
                        }
                        else {
                            $v = (($jmonth - 1) * 31) + $jday;
                        }
                        self::$temp['z'] = $v;
                        break;
                    //Week
                    case 'W':
                        $v = is_int(self::$temp['z'] / 7) ? (self::$temp['z'] / 7) : intval(self::$temp['z'] / 7 + 1);
                        break;
                    //Month
                    case 'F':
                        $v = self::getMonthNames($jmonth);
                        break;
                    case 'm':
                        $v = sprintf('%02d', $jmonth);
                        break;
                    case 'M':
                        $v = self::getMonthNames($jmonth, true);
                        break;
                    case 'n':
                        $v = $jmonth;
                        break;
                    case 't':
                    	if ($jmonth>=1 && $jmonth<=6) $v=31;
                    	else if ($jmonth>=7 && $jmonth<=11) $v=30;
                    	else if($jmonth==12 && $jyear % 4 ==3) $v=30;
                    	else if ($jmonth==12 && $jyear % 4 !=3) $v=29;
                        break;
                    //Year
                    case 'L':
                        $tmpObj = new DateTime('@'.(time()-31536000));
                        $v = $tmpObj->format('L');
                        break;
                    case 'o':
                    case 'Y':
                        $v = $jyear;
                        break;
                    case 'y':
                        $v = $jyear % 100;
                        break;
                    //Time
                    case 'a':
                        $v = ($obj->format('a') == 'am') ? 'ق.ظ' : 'ب.ظ';
                        break;
                    case 'A':
                        $v = ($obj->format('A') == 'AM') ? 'قبل از ظهر' : 'بعد از ظهر';
                        break;
                    //Full Dates
                    case 'c':
                        $v  = $jyear.'-'.sprintf('%02d', $jmonth).'-'.sprintf('%02d', $jday).'T';
                        $v .= $obj->format('H').':'.$obj->format('i').':'.$obj->format('s').$obj->format('P');
                        break;
                    case 'r':
                        $v  = self::getDayNames($obj->format('D'), true).', '.sprintf('%02d', $jday).' '.self::getMonthNames($jmonth, true);
                        $v .= ' '.$jyear.' '.$obj->format('H').':'.$obj->format('i').':'.$obj->format('s').' '.$obj->format('P');
                        break;
                    //Timezone
                    case 'e':
                        $v = $obj->format('e');
                        break;
                    case 'T':
                        $v = $obj->format('T');
                        break;

                }
                $values[$k] = $v;

            }
            //End Changed Keys

            //Merge
            $keys   = array_merge($intact, $keys);
            $values = array_merge($intactValues, $values);

            //Return
            $ret = strtr($format, array_combine($keys, $values));
            return
                ($convert === false ||
                ($convert === null && self::$convert === false) ||
                ( $jalali === false || $jalali === null && self::$jalali === false ))
                ? $ret : self::convertNumbers($ret);

        }

    }

    /**
     * jDateTime::gDate
     *
     * Same as jDateTime::Date method
     * but this one works as a helper and returns Gregorian Date
     * in case someone doesn't like to pass all those false arguments
     * to Date method.
     *
     * e.g. $obj->gDate("Y-m-d") //Outputs: 2011-05-05
     *      $obj->date("Y-m-d", false, false, false); //Outputs: 2011-05-05
     *      Both return the exact same result.
     *
     * @author Sallar Kaboli
     * @param $format string Acceps format string based on: php.net/date
     * @param $stamp int Unix Timestamp (Epoch Time)
     * @param $timezone string (Optional) forces a different timezone. pass null to use system default
     * @return string Formatted input
     */
    public static function gDate($format, $stamp = false, $timezone = null)
    {
        return self::date($format, $stamp, false, false, $timezone);
    }

    /**
     * jDateTime::Strftime
     *
     * Format a local time/date according to locale settings
     * built in strftime() function.
     * e.g:
     * $obj->strftime("%x %H", time());
     * $obj->strftime("%H", time(), false, false, 'America/New_York');
     *
     * @author Omid Pilevar
     * @param $format string Acceps format string based on: php.net/date
     * @param $stamp int Unix Timestamp (Epoch Time)
     * @param $convert bool (Optional) forces convert action. pass null to use system default
     * @param $jalali bool (Optional) forces jalali conversion. pass null to use system default
     * @param $timezone string (Optional) forces a different timezone. pass null to use system default
     * @return string Formatted input
     */
    public static function strftime($format, $stamp = false, $convert = null, $jalali = null, $timezone = null)
    {
        $str_format_code = array(
            '%a', '%A', '%d', '%e', '%j', '%u', '%w',
            '%U', '%V', '%W',
            '%b', '%B', '%h', '%m',
            '%C', '%g', '%G', '%y', '%Y',
            '%H', '%I', '%l', '%M', '%p', '%P', '%r', '%R', '%S', '%T', '%X', '%z', '%Z',
            '%c', '%D', '%F', '%s', '%x',
            '%n', '%t', '%%'
        );

        $date_format_code = array(
            'D', 'l', 'd', 'j', 'z', 'N', 'w',
            'W', 'W', 'W',
            'M', 'F', 'M', 'm',
            'y', 'y', 'y', 'y', 'Y',
            'H', 'h', 'g', 'i', 'A', 'a', 'h:i:s A', 'H:i', 's', 'H:i:s', 'h:i:s', 'H', 'H',
            'D j M H:i:s', 'd/m/y', 'Y-m-d', 'U', 'd/m/y',
            '\n', '\t', '%'
        );

        //Change Strftime format to Date format
        $format = str_replace($str_format_code, $date_format_code, $format);

        //Convert to date
        return self::date($format, $stamp, $convert, $jalali, $timezone);
    }

   /**
     * jDateTime::Mktime
     *
     * Creates a Unix Timestamp (Epoch Time) based on given parameters
     * works like php's built in mktime() function.
     * e.g:
     * $time = $obj->mktime(0,0,0,2,10,1368);
     * $obj->date("Y-m-d", $time); //Format and Display
     * $obj->date("Y-m-d", $time, false, false); //Display in Gregorian !
     *
     * You can force gregorian mktime if system default is jalali and you
     * need to create a timestamp based on gregorian date
     * $time2 = $obj->mktime(0,0,0,12,23,1989, false);
     *
     * @author Sallar Kaboli
     * @param $hour int Hour based on 24 hour system
     * @param $minute int Minutes
     * @param $second int Seconds
     * @param $month int Month Number
     * @param $day int Day Number
     * @param $year int Four-digit Year number eg. 1390
     * @param $jalali bool (Optional) pass false if you want to input gregorian time
     * @param $timezone string (Optional) acceps an optional timezone if you want one
     * @return int Unix Timestamp (Epoch Time)
     */
    public static function mktime($hour, $minute, $second, $month, $day, $year, $jalali = null, $timezone = null)
    {
        //Defaults
        $month = (intval($month) == 0) ? self::date('m') : $month;
        $day   = (intval($day)   == 0) ? self::date('d') : $day;
        $year  = (intval($year)  == 0) ? self::date('Y') : $year;

        //Convert to Gregorian if necessary
        if ( $jalali === true || ($jalali === null && self::$jalali === true) ) {
            list($year, $month, $day) = self::toGregorian($year, $month, $day);
        }

        //Create a new object and set the timezone if available
        $date = $year.'-'.sprintf('%02d', $month).'-'.sprintf('%02d', $day).' '.$hour.':'.$minute.':'.$second;

        if ( self::$timezone != null || $timezone != null ) {
            $obj = new DateTime($date, new DateTimeZone(($timezone != null) ? $timezone : self::$timezone));
        }
        else {
            $obj = new DateTime($date);
        }

        //Return
        return $obj->format('U');
    }

    /**
     * jDateTime::Checkdate
     *
     * Checks the validity of the date formed by the arguments.
     * A date is considered valid if each parameter is properly defined.
     * works like php's built in checkdate() function.
     * Leap years are taken into consideration.
     * e.g:
     * $obj->checkdate(10, 21, 1390); // Return true
     * $obj->checkdate(9, 31, 1390);  // Return false
     *
     * You can force gregorian checkdate if system default is jalali and you
     * need to check based on gregorian date
     * $check = $obj->checkdate(12, 31, 2011, false);
     *
     * @author Omid Pilevar
     * @param $month int The month is between 1 and 12 inclusive.
     * @param $day int The day is within the allowed number of days for the given month.
     * @param $year int The year is between 1 and 32767 inclusive.
     * @param $jalali bool (Optional) pass false if you want to input gregorian time
     * @return bool
     */
    public static function checkdate($month, $day, $year, $jalali = null)
    {
        //Defaults
        $month = (intval($month) == 0) ? self::date('n') : intval($month);
        $day   = (intval($day)   == 0) ? self::date('j') : intval($day);
        $year  = (intval($year)  == 0) ? self::date('Y') : intval($year);

        //Check if its jalali date
        if ( $jalali === true || ($jalali === null && self::$jalali === true) )
        {
            $epoch = self::mktime(0, 0, 0, $month, $day, $year);

            if( self::date('Y-n-j', $epoch,false) == "$year-$month-$day" ) {
                $ret = true;
            }
            else{
                $ret = false;
            }
        }
        else //Gregorian Date
        {
            $ret = checkdate($month, $day, $year);
        }

        //Return
        return $ret;
    }

    /**
     * System Helpers below
     * ------------------------------------------------------
     */

    /**
     * Filters out an array
     */
    private static function filterArray($needle, $heystack, $always = array())
    {
        return array_intersect(array_merge($needle, $always), $heystack);
    }

    /**
     * Returns correct names for week days
     */
    private static function getDayNames($day, $shorten = false, $len = 1, $numeric = false)
    {
        $days = array(
            'sat' => array(1, 'شنبه'),
            'sun' => array(2, 'یکشنبه'),
            'mon' => array(3, 'دوشنبه'),
            'tue' => array(4, 'سه شنبه'),
            'wed' => array(5, 'چهارشنبه'),
            'thu' => array(6, 'پنجشنبه'),
            'fri' => array(7, 'جمعه')
        );

        $day = substr(strtolower($day), 0, 3);
        $day = $days[$day];

        return ($numeric) ? $day[0] : (($shorten) ? self::substr($day[1], 0, $len) : $day[1]);
    }

    /**
     * Returns correct names for months
     */
    private static function getMonthNames($month, $shorten = false, $len = 3)
    {
        // Convert
        $months = array(
            'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
        );
        $ret    = $months[$month - 1];

        // Return
        return ($shorten) ? self::substr($ret, 0, $len) : $ret;
    }

    /**
     * Converts latin numbers to farsi script
     */
    private static function convertNumbers($matches)
    {
        $farsi_array   = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $english_array = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

        return str_replace($english_array, $farsi_array, $matches);
    }

    /**
     * Division
     */
    private static function div($a, $b)
    {
        return (int) ($a / $b);
    }

    /**
     * Substring helper
     */
    private static function substr($str, $start, $len)
    {
        if( function_exists('mb_substr') ){
            return mb_substr($str, $start, $len, 'UTF-8');
        }
        else{
            return substr($str, $start, $len * 2);
        }
    }

    /**
     * Gregorian to Jalali Conversion
     * Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
     */
    public static function toJalali($g_y, $g_m, $g_d)
    {

        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        $gy = $g_y-1600;
        $gm = $g_m-1;
        $gd = $g_d-1;

        $g_day_no = 365*$gy+self::div($gy+3, 4)-self::div($gy+99, 100)+self::div($gy+399, 400);

        for ($i=0; $i < $gm; ++$i)
            $g_day_no += $g_days_in_month[$i];
        if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
            $g_day_no++;
        $g_day_no += $gd;

        $j_day_no = $g_day_no-79;

        $j_np = self::div($j_day_no, 12053);
        $j_day_no = $j_day_no % 12053;

        $jy = 979+33*$j_np+4*self::div($j_day_no, 1461);

        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::div($j_day_no-1, 365);
            $j_day_no = ($j_day_no-1)%365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
            $j_day_no -= $j_days_in_month[$i];
        $jm = $i+1;
        $jd = $j_day_no+1;

        return array($jy, $jm, $jd);

    }

    /**
     * Jalali to Gregorian Conversion
     * Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
     *
     */
    public static function toGregorian($j_y, $j_m, $j_d)
    {

        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        $jy = $j_y-979;
        $jm = $j_m-1;
        $jd = $j_d-1;

        $j_day_no = 365*$jy + self::div($jy, 33)*8 + self::div($jy%33+3, 4);
        for ($i=0; $i < $jm; ++$i)
            $j_day_no += $j_days_in_month[$i];

        $j_day_no += $jd;

        $g_day_no = $j_day_no+79;

        $gy = 1600 + 400*self::div($g_day_no, 146097);
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) {
            $g_day_no--;
            $gy += 100*self::div($g_day_no,  36524);
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365)
                $g_day_no++;
            else
                $leap = false;
        }

        $gy += 4*self::div($g_day_no, 1461);
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;

            $g_day_no--;
            $gy += self::div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
        $gm = $i+1;
        $gd = $g_day_no+1;

        return array($gy, $gm, $gd);

    }

}
