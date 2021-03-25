<?php
//namespace Wildside\Userstamps;
namespace DaLiSoft\Userstamps;
use Illuminate\Support\Facades\Auth;

trait Userstamps
{
    /**
     * Whether we're currently maintaing userstamps.
     *
     * @param bool
     */
    protected $userstamping = true;
    static protected $usingSoftDeletes = null;


    /**
     * Boot the userstamps trait for a model.
     *
     * @return void
     */
    public static function bootUserstamps()
    {
        static::addGlobalScope(new UserstampsScope);

        static::registerListeners();
    }

    /**
     * Register events we need to listen for.
     *
     * @return void
     */
    public static function registerListeners()
    {
        static::creating('DaLiSoft\Userstamps\Listeners\Creating@handle');
        static::updating('DaLiSoft\Userstamps\Listeners\Updating@handle');
        static::deleting('DaLiSoft\Userstamps\Listeners\Deleting@handle');
            
        static::setUsingSoftDeletes();
        if (static::$usingSoftDeletes) {
         //   static::deleting('DaLiSoft\Userstamps\Listeners\Deleting@handle');
            static::restoring('DaLiSoft\Userstamps\Listeners\Restoring@handle');
        }
    }

    /**
     * Has the model loaded the SoftDeletes trait.
     *
     * @return void
     */
    public static function setUsingSoftDeletes()
    {
      //  static $usingSoftDeletes;

        if (is_null(static::$usingSoftDeletes)) {
            static::$usingSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(get_called_class()));
        }

       // return $usingSoftDeletes;
    }

    /**
     * Check if we're maintaing UsingSoftDeletes on the model.
     *
     * @return bool
     */
    public function getUsingSoftDeletes()
    {
        return static::$usingSoftDeletes;
    }



    /**
     * Get the user that created the model.
     */
    public function creator()
    {
        return $this->belongsTo($this->getUserClass(), $this->getCreatedByColumn());
    }

    /**
     * Get the user that edited the model.
     */
    public function editor()
    {
        return $this->belongsTo($this->getUserClass(), $this->getUpdatedByColumn());
    }

    /**
     * Get the user that deleted the model.
     */
    public function destroyer()
    {
        return $this->belongsTo($this->getUserClass(), $this->getDeletedByColumn());
    }

    /**
     * Get the name of the "created by" column.
     *
     * @return string
     */
    public function getCreatedByColumn()
    {
        return defined('static::CREATED_BY') ? static::CREATED_BY : 'created_by';
    }

    /**
     * Get the name of the "updated by" column.
     *
     * @return string
     */
    public function getUpdatedByColumn()
    {
        return defined('static::UPDATED_BY') ? static::UPDATED_BY : 'updated_by';
    }

    /**
     * Get the name of the "deleted by" column.
     *
     * @return string
     */
    public function getDeletedByColumn()
    {
        return defined('static::DELETED_BY') ? static::DELETED_BY : 'deleted_by';
    }

    /**
     * Check if we're maintaing Userstamps on the model.
     *
     * @return bool
     */
    public function isUserstamping()
    {
        return $this->userstamping;
    }

    /**
     * Stop maintaining Userstamps on the model.
     *
     * @return void
     */
    public function stopUserstamping()
    {
        $this->userstamping = false;
    }

    /**
     * Start maintaining Userstamps on the model.
     *
     * @return void
     */
    public function startUserstamping()
    {
        $this->userstamping = true;
    }

    /**
     * Get the class being used to provide a User.
     *
     * @return string
     */
    protected function getUserClass()
    {
        return config('auth.providers.users.model');
    }

    public function isCreator($user_id=null) {
        if( !isset($user_id) ) {
            $user_id == Auth::id();
        }
        return $this->{$this->getCreatedByColumn()} == $user_id;
    }

    /**
     * Get the user name.
     */

    Public function getCreatedByUserAttribute($by_column='name')
    {
        return $this->creator->{$by_column} ?? '';
    }
    Public function getUpdatedByUserAttribute($by_column='name')
    {
        return $this->editor->{$by_column} ?? '';
    }
    Public function getDeletedByUserAttribute($by_column='name')
    {
        return $this->destroyer->{$by_column} ?? '';
    }

}
