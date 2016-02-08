<?php
namespace ParseSQLIdentifier;
class Parser {
	protected $quote = '`', // quotation symbol. Typ. '`' unless using SQL in ANSI_QUOTES mode, in which case this should be changed to '"'
		$delimiter = '.', // delimiter between identifiers. Shouldn't stray from period: i.e. [schema].[table].[column]
		$escaped_quote = '``'; // MySQL escapes backticks/double-quotes [ANSI] by using two in a row
	
	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @param int	$quote		Identifier quotation character
	 * @param int	$delimiter	Identifier delimiter character, defaults to '.'
	 */
	
	public function __construct($quote = '`', $delimiter = '.') {
		$this->quote = $quote ?: $this->quote;
		$this->escaped_quote = $this->quote.$this->quote;
		$this->delimiter = $delimiter ?: $this->delimiter;
	}
	
	/**
	 * Extract the identifiers from the string. Don't validate the existence of any individual component.
	 *
	 * @since 1.0.0
	 * @param string	$str	Identifier string to parse
	 */
	
	public function parse($str) {
		$splits = array(); 
		$s = 0; // tracks split number
		$q = $str[0] === $this->quote; // counter for "levels" of quotation: if 0, we've left the identifier block
		$last_i = 0; // tracks the position of the last substring
		$f = false; // tracks quote parity and adjacency
		
		for($i=1; $i<=strlen($str); $i++) {
			$q -= $f && ($i === strlen($str) || $str[$i] !== $this->quote); //if the last character is an identifier quote, but this one isn't, then decrement $q (sets to 0 for now)
			
			$f = !$f && ($q && $str[$i] === $this->quote);
			
			if($q == 0 && ($i === strlen($str) || $str[$i] === $this->delimiter)) { // wait for end of string or delimiter before splitting
				$trimmed = trim(substr($str, $last_i, min(strlen($str), $i)-$last_i)); // trim errant spaces
				$trimmed = substr($trimmed, $trimmed[0] === $this->quote, -($trimmed[strlen($trimmed)-1] === $this->quote) ?: strlen($trimmed)); // ...then surrounding quotes: this will never remove quotes part of the name, as ``name is an invalid identifier without surrounding backticks
				$splits[$s++] = str_replace($this->escaped_quote, $this->quote, $trimmed); // replace escaped quote with single quote to get true identifier name
				$last_i = min(strlen($str), $i)+1; // increment the left boundary to skip the delimiter
			}
		}
		return $splits;
	}
}