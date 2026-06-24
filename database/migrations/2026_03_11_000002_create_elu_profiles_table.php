<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elu_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('code_insee')->nullable();
            $table->string('collectivite')->nullable();
            $table->string('epci_commune')->nullable();
            $table->string('secteur')->nullable();
            $table->string('nom_secteur')->nullable();
            $table->date('date_deliberation')->nullable();
            $table->string('visa_prefecture')->nullable();
            $table->text('probleme_delib')->nullable();
            $table->string('civilite')->nullable();
            $table->string('rt_ds_dt')->nullable();
            $table->string('titre')->nullable();
            $table->integer('ordre_suppleants')->nullable();
            $table->string('contact')->nullable();
            $table->string('mail_personnel')->nullable();
            $table->string('mail_2')->nullable();
            $table->string('telephone')->nullable();
            $table->string('adresse_1')->nullable();
            $table->string('adresse_2')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('profession')->nullable();
            $table->string('societe')->nullable();
            $table->date('date_naissance')->nullable();
            $table->boolean('newsletter')->default(false);
            $table->boolean('frais_route')->default(false);
            $table->boolean('rib_fourni')->default(false);
            $table->string('chevaux_fiscaux')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elu_profiles');
    }
};
