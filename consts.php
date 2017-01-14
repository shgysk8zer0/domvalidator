<?php
namespace shgysk8zer0\DOMValidator;

abstract class Consts
{
	const PATTERNS = [
		'color' => '#?([a-fA-F\d]{3}){1,2}',
		'credit-card' => '[0-9]{13,16}',
		'password' => '(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$',
		'tel' => '\d{3}[\-]\d{3}[\-]\d{4}',
		'datetime' => '(20\d{2}-(0\d|1[02])-([01]\d|3[01]))|((0?\d|1[0-2])\/([0-2]?\d|3[01])\/20\d{2})|[A-z]{3,8}\.? ([0-2]?\d|3[01])(th|st|nd|rd)?(, 20\d{2})?[T ](([01]\d|2[0-3])(:[0-5]\d){1,2})|((0?\d|1[0-2])(:[0-5]\d){1,2} ?[AaPp]\.?[Mm]\.?)',
		'date' => '(20\d{2}-(0\d|1[02])-([01]\d|3[01]))|((0?\d|1[0-2])\/([0-2]?\d|3[01])\/20\d{2})|[A-z]{3,8}\.? ([0-2]?\d|3[01])(th|st|nd|rd)?(, 20\d{2})?',
		'time' => '(([01]\d|2[0-3])(:[0-5]\d){1,2})|((0?\d|1[0-2])(:[0-5]\d){1,2} ?[AaPp]\.?[Mm]\.?)',
		'week' => '20\d{2}[- ][Ww](eek)?([0-4]\d|5[1-3])',
		'month' => '(20\d{2}-\d{1,2}|(0?\d|1[0-2])\/20\d{2}|[A-z]{3,8}\.?(,? 20\d{2})?)',
	];

	const INPUT_TAGS = [
		'input',
		'select',
		'textarea',
	];
}
