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
            ],
            [
                'name' => 'Ni Made Dewi',
                'email' => 'made@smkn2.com',
                'asal_sekolah' => 'SMKN 2 Denpasar',
                'no_wa' => '087777778888',
            ],
            [
                'name' => 'I Wayan Adi',
                'email' => 'wayan@sman5.com',
                'asal_sekolah' => 'SMAN 5 Denpasar',
                'no_wa' => '089999990000',
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

        // 3. Membuat Peserta dengan Status Verified (Sudah Diverifikasi)
        $peserta_verified = [
            [
                'name' => 'Angelica Yzreel Juliana',
                'email' => 'ana@upc.com',
                'asal_sekolah' => 'SMAN 3 Denpasar',
                'no_wa' => '081333334444',
            ],
            [
                'name' => 'Komang Suastika',
                'email' => 'komang@smkn4.com',
                'asal_sekolah' => 'SMKN 4 Denpasar',
                'no_wa' => '082222223333',
            ]
        ];

        foreach ($peserta_verified as $peserta) {
            User::create([
                'name' => $peserta['name'],
                'email' => $peserta['email'],
                'password' => Hash::make('password'),
                'asal_sekolah' => $peserta['asal_sekolah'],
                'no_wa' => $peserta['no_wa'],
                'status_verifikasi' => 'verified',
            ]);
        }

        // 4. Membuat Peserta dengan Status Rejected (Ditolak)
        $peserta_rejected = [
            [
                'name' => 'Agus Wijaya',
                'email' => 'agus@smkn5.com',
                'asal_sekolah' => 'SMKN 5 Denpasar',
                'no_wa' => '084444445555',
            ]
        ];

        foreach ($peserta_rejected as $peserta) {
            User::create([
                'name' => $peserta['name'],
                'email' => $peserta['email'],
                'password' => Hash::make('password'),
                'asal_sekolah' => $peserta['asal_sekolah'],
                'no_wa' => $peserta['no_wa'],
                'status_verifikasi' => 'rejected',
            ]);
        }

        // 5. Memanggil Seeder Lain (Jika Ada)
        // $this->call([
        //     CompetitionSeeder::class,
        //     RegistrationSeeder::class,
        //     PaymentSeeder::class,
        //     ExamResultSeeder::class,
        // ]);
    }
}