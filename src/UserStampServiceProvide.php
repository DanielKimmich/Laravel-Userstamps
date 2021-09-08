<?php

namespace DaLiSoft\Userstamps;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;


class UserStampServiceProvider extends ServiceProvider
{
    public function register()
    {
        Blueprint::macro(
            'userstamps', function () {
                $this->unsignedBigInteger('created_by')->nullable()
                    ->index();
                $this->unsignedBigInteger('updated_by')->nullable()
                    ->index();
                $this->unsignedBigInteger('deleted_by')->nullable()
                    ->index();
            }
        );
        Blueprint::macro(
            'addForiegnUserstamps', function () {
                $this->foreign('created_by')
                    ->references('id')->on('users')->onDelete('cascade');
                $this->foreign('updated_by')
                    ->references('id')->on('users')->onDelete('cascade');
                $this->foreign('deleted_by')
                    ->references('id')->on('users')->onDelete('cascade');
            }
        );
        Blueprint::macro(
            'dropUserstamps', function () {
                $this->dropColumn(['created_by', 'updated_by', 'deleted_by']);
            }
        );
        Blueprint::macro(
            'dropForeignUserstamps', function () {
                $this->dropForeign(['created_by', 'updated_by', 'deleted_by']);
            }
        );
    }
}

