<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Admin\Permission;

/**
 * Class PermissionTransformer
 * @package namespace App\Transformers;
 */
class PermissionTransformer extends TransformerAbstract
{

    /**
     * Transform the \Permission entity
     * @param \Permission $model
     *
     * @return array
     */
    public function transform(Permission $model)
    {
        return [
            'id'           => (int) $model->id,
            'name'         => $model->name,
            'display_name' => $model->display_name,
            'description'  => $model->description,
            'level'        => $model->level,
            'icon'         => $model->icon,
            'parent_id'    => (int) $model->parent_id,
            'order_num'    => (int) $model->order_num,
            'icon'         => $model->icon,
            'is_show'      => (bool)  $model->is_show,
            'created_at'   => $model->created_at,
            'updated_at'   => $model->updated_at
        ];
    }
}
