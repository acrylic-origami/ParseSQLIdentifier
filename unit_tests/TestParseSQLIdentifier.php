<?php
class TestParser extends PHPUnit_Framework_TestCase {
	public function testBasic() {
		$parser = new ParseSQLIdentifier\Parser();
		
		$this->assertEquals(array('a', 'bcd', 'ef'), $parser->parse('a.bcd.ef'));
		$this->assertEquals(array('a', 'bcd', 'ef'), $parser->parse('`a`.`bcd`.ef'));
		$this->assertEquals(array('a', 'bcd', 'ef'), $parser->parse('`a`.bcd.`ef`'));
		$this->assertEquals(array('a', 'bcd', 'ef'), $parser->parse('`a`.bcd.ef'));
	}
	public function testEmbeddedQuotes() {
		$parser = new ParseSQLIdentifier\Parser();
		
		$this->assertEquals(array('a', 'b`cd', 'ef'), $parser->parse('a.`b``cd`.ef'));
		$this->assertEquals(array('a`', 'b`cd', 'ef'), $parser->parse('`a```.`b``cd`.ef'));
		$this->assertEquals(array('a`', 'b`cd', '`ef`'), $parser->parse('`a```.`b``cd`.```ef```'));
	}
	public function testMultipleEmbeddedQuotes() {
		$parser = new ParseSQLIdentifier\Parser();
		
		$this->assertEquals(array('a``', 'b``cd', '```ef'), $parser->parse('`a`````.`b````cd`.```````ef`'));
		$this->assertEquals(array('`a`', '`b`c`d`', '```e``f`'), $parser->parse('```a```.```b``c``d```.```````e````f```'));
	}
	public function testANSIMode() {
		$parser = new ParseSQLIdentifier\Parser('"'); // use ANSI quote mode, run same test as $this->testMultipleEmbeddedQuotes.
		
		$this->assertEquals(array('a""', 'b""cd', '"""ef'), $parser->parse('"a"""""."b""""cd"."""""""ef"'));
		$this->assertEquals(array('"a"', '"b"c"d"', '"""e""f"'), $parser->parse('"""a"""."""b""c""d"""."""""""e""""f"""'));
	}
}