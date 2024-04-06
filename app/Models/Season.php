<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Season
 *
 * @property int $id
 * @property string $name
 * @property bool $finished
 *
 * @package App\Models
 */
class Season extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'finished' => 'bool'
	];

	protected $fillable = [
		'name',
		'finished'
	];
}
