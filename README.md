# ParseSQLIdentifier

Efficiently splits SQL identifier strings, like `schema.table.column` into an array `["schema", "table", "column"]`. However, it specializes in handling particularly nasty identifiers, like ```````` ```a```.```b``c``d```.```````e````f``` ```````` which parses to ````["`a`", "`b`c`d`", "```e``f`"]````. Supports ANSI mode quotations (i.e. replacing `` ` `` with `"`) and any other quote-like symbol through constructor arguments: any string that escapes through character repetition can be parsed with this script.

## Use cases?

This is a particularly useful method for checking if one identifier is a subset of another, especially in cases where backticks within or surrounding identifier descriptions render regex useless. The method appears relatively bulletproof, so if the database naming scheme is automatically generated, unknown or intrinsically nasty, and you need robust parsing, this should be robust enough to handle all _syntactically proper strings_ thrown at it.