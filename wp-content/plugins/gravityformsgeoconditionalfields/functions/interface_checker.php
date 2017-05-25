<?php

/**
 * A quick bit of code to make sure PHP 5.3 is supported
 */
if(!interface_exists("JsonSerializable")) {
	/**
	 * JsonSerializable interface
	 *
	 * PHP 5.3 compatibility for PHP 5.4's \JsonSerializable
	 *
	 * @author Sam-Mauris Yong / mauris@hotmail.sg
	 * @copyright Copyright (c) 2010-2012, Sam-Mauris Yong
	 * @license http://www.opensource.org/licenses/bsd-license New BSD License
	 * @link http://www.php.net/manual/en/class.jsonserializable.php
	 */
	interface JsonSerializable
	{

		public function jsonSerialize();

	}
}