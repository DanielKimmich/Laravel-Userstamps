<?php
//namespace Wildside\Userstamps\Listeners;
namespace DaLiSoft\Userstamps\Listeners;

use Illuminate\Support\Facades\Auth;

class Deleting
{
    /**
     * When the model is being deleted.
     *
     * @param Illuminate\Database\Eloquent $model
     * @return void
     */
    public function handle($model)
    {
        if ( !is_null(Auth::id()) ) {
            //touch for user
            foreach ($model->getTouchedRelations() as $relation) {
                if ( $model->$relation->isUserstamping() ) {
                    if ( !is_null($model->$relation->getUpdatedByColumn()) ) {
                        $model->$relation->{$model->getUpdatedByColumn()} = Auth::id();
                        $model->$relation->save();
                    }
                }
            }

            if ( $model->isUserstamping() && $model->getUsingSoftDeletes() ) {
                if ( !is_null($model->getDeletedByColumn()) ) {
                    if (is_null($model->{$model->getDeletedByColumn()})) {
                        $model->{$model->getDeletedByColumn()} = Auth::id();
                    }
                }

                $dispatcher = $model->getEventDispatcher();
                $model->unsetEventDispatcher();
                $model->save();
                $model->setEventDispatcher($dispatcher);
            }
        }
    }
}
