<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');

            $table->foreign('post_id')
                  ->references('id')
                  ->on('posts');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign('posts_user_id_foreign');
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign('comments_user_id_foreign');
            $table->dropForeign('comments_post_id_foreign');
        });
    }
}
