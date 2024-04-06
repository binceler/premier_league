<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Match
 *
 * @property int $id
 * @property int $week_id
 * @property int $home
 * @property int $away
 *
 * @package App\Models
 */
class MatchGame extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'week_id' => 'int',
		'home' => 'int',
		'away' => 'int'
	];

	protected $fillable = [
		'week_id',
		'home',
		'away'
	];
}
