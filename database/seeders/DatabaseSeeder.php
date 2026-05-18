<?php

namespace Database\Seeders;

use App\Models\DisciplineRule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name'      => 'Админ Системы',
            'email'     => 'admin@example.com',
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $class10a = SchoolClass::create(['name' => '10А']);
        $class9b = SchoolClass::create(['name' => '9Б']);

        $homeroomUser = User::factory()->create([
            'name'      => 'Иван Петров',
            'email'     => 'teacher@example.com',
            'role'      => 'teacher',
            'is_active' => true,
        ]);
        $homeroomTeacher = Teacher::create([
            'user_id'             => $homeroomUser->id,
            'is_homeroom_teacher' => true,
        ]);
        $class10a->update(['homeroom_teacher_id' => $homeroomTeacher->id]);

        $regularUser = User::factory()->create([
            'name'      => 'Айгуль Смагулова',
            'email'     => 'teacher2@example.com',
            'role'      => 'teacher',
            'is_active' => true,
        ]);
        Teacher::create([
            'user_id'             => $regularUser->id,
            'is_homeroom_teacher' => false,
        ]);

        $starting = Student::startingPoints();

        $students = [
            ['name' => 'Алия Касымова', 'email' => 'student@example.com', 'class_id' => $class10a->id, 'points' => $starting],
            ['name' => 'Данияр Омаров', 'email' => 'student2@example.com', 'class_id' => $class10a->id, 'points' => $starting + 5],
            ['name' => 'Мадина Сейтова', 'email' => 'student3@example.com', 'class_id' => $class9b->id, 'points' => $starting - 3],
            ['name' => 'Ерлан Нурланов', 'email' => 'student4@example.com', 'class_id' => $class9b->id, 'points' => $starting - 8],
        ];

        foreach ($students as $data) {
            $user = User::factory()->create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'role'      => 'student',
                'is_active' => true,
            ]);
            Student::create([
                'user_id'        => $user->id,
                'class_id'       => $data['class_id'],
                'current_points' => $data['points'],
            ]);
        }

        $rules = [
            ['name' => 'Активность на уроке', 'description' => 'Ответы, участие в дискуссии', 'points' => 2, 'type' => 'reward'],
            ['name' => 'Помощь однокласснику', 'description' => 'Взаимопомощь и наставничество', 'points' => 3, 'type' => 'reward'],
            ['name' => 'Опоздание', 'description' => 'Опоздание на урок без уважительной причины', 'points' => -1, 'type' => 'penalty'],
            ['name' => 'Нарушение дресс-кода', 'description' => 'Форма одежды', 'points' => -2, 'type' => 'penalty'],
            ['name' => 'Отличная контрольная', 'description' => 'Оценка 5 за письменную работу', 'points' => 5, 'type' => 'reward'],
        ];

        foreach ($rules as $rule) {
            DisciplineRule::create([
                ...$rule,
                'is_active' => true,
            ]);
        }
    }
}
