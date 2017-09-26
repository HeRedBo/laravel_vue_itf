<?php

namespace App\Repositories;

use App\Models\Admin\Venue;

class VenueRepository
{
	use BaseRepository;

	protected $model;

    protected $fields = [
        'name' => '',
        'federation_id' => '0',
        'logo' => '',
        'logo_thumb' => '',
        'parent_id' => '0',
        'province_code' => '0',
        'city_code' => '0',
        'district_code' => '0',
        'address' => '',
        'remark' => '',
        'operator_id' => 0,
    ];


	public function __construct(Venue $venue)
	{
		$this->model = $venue;
	}



}
