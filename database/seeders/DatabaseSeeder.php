<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Publisher;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ejecutar el seeder de roles primero
        $this->call(RoleSeeder::class);

        // 2. Crear Categorías y Editoriales de prueba
        Category::create(['name' => 'Ciencia Ficción']);
        Category::create(['name' => 'Tecnología']);
        Category::create(['name' => 'Historia']);

        Publisher::create(['name' => 'Penguin Random House']);
        Publisher::create(['name' => 'Planeta']);

        // 3. Crear USUARIO BIBLIOTECARIO (Tú)
        $admin = User::factory()->create([
            'name' => 'Admin Bibliotecario',
            'email' => 'admin@biblioteca.com',
            'password' => bcrypt('password'), // La clave es 'password'
        ]);
        $admin->assignRole('librarian');

        // 4. Crear USUARIO ESTUDIANTE (Prueba)
        $student = User::factory()->create([
            'name' => 'Pepito Estudiante',
            'email' => 'student@biblioteca.com',
            'password' => bcrypt('password'),
        ]);
        $student->assignRole('student');
    }
}
