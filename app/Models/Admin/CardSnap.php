<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Support\Facades\DB;

class CardSnap extends Model implements Transformable
{
    
    use TransformableTrait;

    protected $table = 'admin_card_snap'; // 表名

    protected $fillable = [
    	"card_id","venue_id","type","name","number","unit","card_price","explain","status","created_at",
    ];

    /**
     * 该模型是否被自动维护时间戳
     * 
     * @var boolean
     */
    public $timestamps = false;


    public function createCardSnap(array $data)
    {
        try 
        {
            $card_snap = array_only($data, $this->fillable);
            $card_snap['card_id'] = $data['id'];
            $card_snap['created_at'] = date("Y-m-d H:i:s");
            return DB::table($this->table)->insertGetId($card_snap);
        } 
        catch (\Exception $e) 
        {
            logResult('【快照数据创建失败】'.$e->getMessage(),'error');
            throw new Exception($e->getmessage());       
        }
    }
}
