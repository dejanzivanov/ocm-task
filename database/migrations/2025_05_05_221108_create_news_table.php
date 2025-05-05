<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->text('source_id')->nullable();
            $table->text('source_name');
            $table->text('author')->nullable();
            $table->text('title');
            $table->text('description')->nullable();
            $table->text('url');
            $table->text('url_to_image')->nullable();
            $table->timestamp('published_at');
            $table->text('content')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
