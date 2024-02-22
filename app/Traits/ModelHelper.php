<?php

namespace App\Traits;

use Exception;

trait ModelHelper
{
    protected static function findByIdOrFail($modelClass, $object, $modelId, $type = 'male')
    {

        $model = $modelClass::find($modelId);

        if (!$model) {
            $objectType = '';
            if ($type == 'female') {
                $objectType = 'messages.objectNotFoundF';
            } else {
                $objectType = 'messages.objectNotFound';
            }
            throw new Exception(__($objectType, ['object' => __('objects.'.$object)]), 404);
        }
        return $model;
    }
}
