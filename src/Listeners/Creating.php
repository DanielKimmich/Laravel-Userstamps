<?php
//namespace Wildside\Userstamps\Listeners;
namespace DaLiSoft\Userstamps\Listeners;

use Illuminate\Support\Facades\Auth;

class Creating
{
    /**
     * When the model is being created.
     *
     * @param Illuminate\Database\Eloquent $model
     * @return void
     */
    public function handle($model)
    {
        if ( !is_null(Auth::id()) ) {
            if ( $model->isUserstamping() ) {
                if ( !is_null($model->getCreatedByColumn()) ) {
                    if (is_null($model->{$model->getCreatedByColumn()}) ) {
                        $model->{$model->getCreatedByColumn()} = Auth::id();
                    }
                }

                if ( !is_null($model->getUpdatedByColumn()) ) {
                     if (is_null($model->{$model->getUpdatedByColumn()}) {
                        $model->{$model->getUpdatedByColumn()} = Auth::id();
                    }
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
