<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Root extends Model {
	use SoftDeletes;

	protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}