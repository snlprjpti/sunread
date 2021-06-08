<?php

$rules = [
	"text" => "string",
	"textarea" => "string",
	"price" => "decimal",
	"boolean" => "boolean",
	"number" => "integer",
	"select" => null,
	"multiselect" => null,
	"datetime" => "timestamp",
	"date" => "timestamp",
	"image" => "image",
	"file" => "file",
	"checkbox" => null
];

return array_map(function($rule) {
	$rule;
}, $rules);
