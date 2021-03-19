<?php

namespace Wildside\Userstamps\Listeners;

use Illuminate\Support\Facades\Auth;

class Updating
{
    /**
     * When the model is being updated.
     *
     * @param Illuminate\Database\Eloquent $model
     * @return void
     */
    public function handle($model)
    {
        if ( !is_null(Auth::id()) ) {
            if ( $model->isUserstamping() ) {
                if ( !is_null($model->getUpdatedByColumn()) ) {
                    $model->{$model->getUpdatedByColumn()} = Auth::id();
                }
            }

            //touch for user
            foreach ($model->getTouchedRelations() as $relation) {
                if ( $model->$relation->isUserstamping() ) {
                    if ( !is_null($model->$relation->getUpdatedByColumn()) ) {
                        $model->$relation->{$model->getUpdatedByColumn()} = Auth::id();
                        $model->$relation->save();
                    }
                }
            }
        }
    }
}
