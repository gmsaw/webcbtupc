<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat Akun Admin HIMAFI
        User::create([
            'name' => 'Admin HIMAFI UPC',
            'email' => 'admin@upc.com',
            'password' => Hash::make('password'), // Password default: password
            'asal_sekolah' => 'Universitas Udayana',
            'no_wa' => '081234567890',
            'status_verifikasi' => 'verified', // Admin otomatis terverifikasi
        ]);

        // 2. Membuat Beberapa Akun Peserta Dummy (Status Pending)
        $peserta_dummies = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@sman1.com',
                'asal_sekolah' => 'SMAN 1 Denpasar',
                'no_wa' => '081111112222',
            ],
            [
                'name' => 'Ayu Lestari',
                'email' => 'ayu@sman4.com',
                'asal_sekolah' => 'SMAN 4 Denpasar',
                'no_wa' => '083333334444',
            ],
            [
                'name' => 'Putu Gede',
                'email' => 'putu@smkn1.com',
                'asal_sekolah' => 'SMKN 1 Denpasar',
                'no_wa' => '085555556666',
            ]
        ];

        foreach ($peserta_dummies as $peserta) {
            User::create([
                'name' => $peserta['name'],
                'email' => $peserta['email'],
                'password' => Hash::make('password'), // Password default: password
                'asal_sekolah' => $peserta['asal_sekolah'],
                'no_wa' => $peserta['no_wa'],
                'status_verifikasi' => 'pending', // Masuk ke antrean verifikasi
            ]);
        }
    }
}