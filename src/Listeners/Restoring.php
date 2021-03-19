<?php

namespace Wildside\Userstamps\Listeners;

class Restoring
{
    /**
     * When the model is being restored.
     *
     * @param Illuminate\Database\Eloquent $model
     * @return void
     */
    public function handle($model)
    {
        if ( !is_null(Auth::id()) ) {
            if ( $model->isUserstamping() ) {
                if ( !is_null($model->getDeletedByColumn()) ) {
                    $model->{$model->getDeletedByColumn()} = null;
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
