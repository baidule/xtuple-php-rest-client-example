json-patch-php
================

Produce and apply json-patch objects.

Implements the IETF JSON-patch RFC 6902 and JSON-pointer RFC 6901:

http://tools.ietf.org/html/rfc6902

http://tools.ietf.org/html/rfc6901

Entry points
------------

- get($doc, $pointer) - get a value from a json document
- diff($src, $dst) - return patches to create $dst from $src
- patch($doc, $patches) - apply patches to $doc and return result

Arguments are PHP arrays, i.e. the output of
json_decode($json_string, 1)

All structures are implemented directly as PHP arrays.
An array is considered to be 'associative' (e.g. like a JSON 'object')
if it contains at least one non-numeric key.

Because of this, empty arrays ([]) and empty objects ({}) compare
the same, and (for instance) an 'add' of a string key to an empty
array will succeed in this implementation where it might fail in
others.

[![Build Status](https://secure.travis-ci.org/mikemccabe/json-patch-php.png)](http://travis-ci.org/mikemccabe/json-patch-php)
