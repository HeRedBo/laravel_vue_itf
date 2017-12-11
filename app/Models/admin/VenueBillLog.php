<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class VenueBillLog extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'venue_bill_log';
    protected $fillable = [];
     /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}
