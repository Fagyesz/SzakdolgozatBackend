<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthAuthCodesTable extends Migration
{
    protected $schema;
    public function __construct()
    {
        $this->schema = Schema::connection($this->getConnection());
    }

    public function up()
    {
        $this->schema->create('oauth_auth_codes', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->uuid('user_id')->index();
            $table->unsignedBigInteger('client_id');
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            $table->dateTime('expires_at')->nullable();
        });
    }

    public function down()
    {
        $this->schema->dropIfExists('oauth_auth_codes');
    }

    public function getConnection()
    {
        return config('passport.storage.database.connection');
    }
}
