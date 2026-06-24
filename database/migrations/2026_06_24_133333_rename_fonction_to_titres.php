<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function mapTitre(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $mapping = [
            'Maire' => 'Président',
            'Conseiller municipal' => 'Membre du bureau',
        ];

        $new = $mapping[$value] ?? $value;

        return json_encode([$new]);
    }

    public function up(): void
    {
        // Users table: rename fonction → titres, change to json
        Schema::table('users', function (Blueprint $table) {
            $table->json('titres')->nullable()->after('is_admin');
        });

        DB::table('users')->orderBy('id')->each(function ($user) {
            $mapped = $this->mapTitre($user->fonction ?? null);
            DB::table('users')->where('id', $user->id)->update(['titres' => $mapped]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fonction');
        });

        // Elu profiles table: rename titre → titres, change to json
        Schema::table('elu_profiles', function (Blueprint $table) {
            $table->json('titres')->nullable()->after('rt_ds_dt');
        });

        DB::table('elu_profiles')->orderBy('id')->each(function ($profile) {
            $mapped = $this->mapTitre($profile->titre ?? null);
            DB::table('elu_profiles')->where('id', $profile->id)->update(['titres' => $mapped]);
        });

        Schema::table('elu_profiles', function (Blueprint $table) {
            $table->dropColumn('titre');
        });
    }

    public function down(): void
    {
        Schema::table('elu_profiles', function (Blueprint $table) {
            $table->string('titre')->nullable()->after('rt_ds_dt');
        });

        DB::table('elu_profiles')->orderBy('id')->each(function ($profile) {
            $titres = json_decode($profile->titres ?? 'null', true);
            $old = $titres[0] ?? null;
            $reverseMapping = [
                'Président' => 'Maire',
                'Membre du bureau' => 'Conseiller municipal',
            ];
            DB::table('elu_profiles')->where('id', $profile->id)->update([
                'titre' => $reverseMapping[$old] ?? $old,
            ]);
        });

        Schema::table('elu_profiles', function (Blueprint $table) {
            $table->dropColumn('titres');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('fonction')->nullable()->after('is_admin');
        });

        DB::table('users')->orderBy('id')->each(function ($user) {
            $titres = json_decode($user->titres ?? 'null', true);
            $old = $titres[0] ?? null;
            $reverseMapping = [
                'Président' => 'Maire',
                'Membre du bureau' => 'Conseiller municipal',
            ];
            DB::table('users')->where('id', $user->id)->update([
                'fonction' => $reverseMapping[$old] ?? $old,
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('titres');
        });
    }
};
