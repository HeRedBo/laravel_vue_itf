<?php

namespace App\Repositories;

use App\Models\Venue;

class VenueRespository
{
	use BaseRepository;

	
	protected $model;

	public function __construct(Venue $venue)
	{
		$this->model = $venue;
	}

}
