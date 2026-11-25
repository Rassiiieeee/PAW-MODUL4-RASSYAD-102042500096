<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'erwin@celoe.test'],
            [
                'name' => 'Erwin Laya',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '10101020',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'rassyad@celoe.test'],
            [
                'name' => 'Rassyad Laya',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '123456789',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission

        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'rassyad@celoe.test'],
            [
                'name' => 'Rassyad',
                'password' => Hash::make('rassyad123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '987654321',
            ]
        );

        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF204'],
            [
                'nama_mk' => 'Struktur Data dan Algoritma',
                'deskripsi' => 'Mata kuliah yang membahas analisis algoritma, struktur data linear dan non-linear, rekursi, serta optimasi efisiensi komputasi.',
                'sks' => 3,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF402'],
            [
                'nama_mk' => 'Keamanan Perangkat Lunak',
                'deskripsi' => 'Mata kuliah lanjutan yang membahas analisis keamanan kode, penanganan celah keamanan web (OWASP Top 10), kriptografi terapan, dan secure coding.',
                'sks' => 2,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // Enrollment Mahasiswa ke Mata Kuliah
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        $course2->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
            $mahasiswa2->id => ['status' => 'pending'],
        ]);

        $course3->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        // 8. Tugas Perkuliahan (Assignment)
        $assignment1 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 1 - Pembuatan Routing & Controller'],
            [
                'deskripsi' => 'Implementasikan routing CRUD, middleware, dan controller untuk modul CeLOE LMS sesuai spesifikasi praktikum.',
                'deadline' => now()->addDays(7),
            ]
        );

        $assignment2 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 2 - Integrasi ORM & Eloquent Relationship'],
            [
                'deskripsi' => 'Buat relasi antar model Course, Assignment, dan Submission dengan query builder yang efisien.',
                'deadline' => now()->addDays(14),
            ]
        );

        // 9. Contoh Data Submission (1 sudah dinilai dengan nilai 88, 1 belum dinilai)
        $sub1 = Submission::updateOrCreate(
            ['assignment_id' => $assignment1->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'https://github.com/ahmadpratama/celoe-routing-crud - Berhasil mengimplementasikan routing dan controller sesuai panduan.',
                'nilai' => 88,
                'catatan_dosen' => 'Implementasi rapi, struktur kode sudah mengikuti standar PSR-12 dengan baik.',
                'submitted_at' => now()->subDays(2),
            ]
        );
        $sub1->forceFill([
            'nilai' => 88,
            'catatan_dosen' => 'Implementasi rapi, struktur kode sudah mengikuti standar PSR-12 dengan baik.',
        ])->save();

        $sub2 = Submission::updateOrCreate(
            ['assignment_id' => $assignment1->id, 'user_id' => $mahasiswa2->id],
            [
                'jawaban' => 'https://github.com/nabil-celoe/tugas1-laravel - Tugas telah diselesaikan mencakup routing web dan resource controller.',
                'nilai' => null,
                'catatan_dosen' => null,
                'submitted_at' => now()->subDay(),
            ]
        );
        $sub2->forceFill([
            'nilai' => null,
            'catatan_dosen' => null,
        ])->save();
    }
}