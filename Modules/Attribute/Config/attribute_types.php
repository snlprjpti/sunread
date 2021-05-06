<?php

$types = [
	"text" => "string",
	"textarea" => "text",
	"price" => "decimal",
	"boolean" => "boolean",
	"number" => "integer",
	"select" => "string",
	"multiselect" => "text",
	"datetime" => "timestamp",
	"date" => "timestamp",
	"image" => "text",
	"file" => "text",
	"checkbox" => "text"
];

return array_map(function($data) {
	$data = ucfirst($data);
	return "Modules\Product\Entities\ProductAttribute{$data}";
}, $types);
