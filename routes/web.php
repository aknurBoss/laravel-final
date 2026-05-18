<?php

use App\Http\Controllers\AdminClassController;
use App\Http\Controllers\AdminHistoryWebController;
use App\Http\Controllers\AdminPointController;
use App\Http\Controllers\AdminRuleController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminTeacherController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeacherHomeroomController;
use App\Http\Controllers\TeacherPointController;
use App\Http\Controllers\WebNotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');

    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/admin/classes', [AdminClassController::class, 'index'])->name('admin.classes.index');
    Route::get('/admin/classes/create', [AdminClassController::class, 'create'])->name('admin.classes.create');
    Route::post('/admin/classes', [AdminClassController::class, 'store'])->name('admin.classes.store');
    Route::get('/admin/classes/{class}/edit', [AdminClassController::class, 'edit'])->name('admin.classes.edit');
    Route::put('/admin/classes/{class}', [AdminClassController::class, 'update'])->name('admin.classes.update');
    Route::delete('/admin/classes/{class}', [AdminClassController::class, 'destroy'])->name('admin.classes.destroy');

    Route::get('/admin/students', [AdminStudentController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/create', [AdminStudentController::class, 'create'])->name('admin.students.create');
    Route::post('/admin/students', [AdminStudentController::class, 'store'])->name('admin.students.store');
    Route::get('/admin/students/{student}/edit', [AdminStudentController::class, 'edit'])->name('admin.students.edit');
    Route::put('/admin/students/{student}', [AdminStudentController::class, 'update'])->name('admin.students.update');
    Route::delete('/admin/students/{student}', [AdminStudentController::class, 'destroy'])->name('admin.students.destroy');

    Route::get('/admin/teachers', [AdminTeacherController::class, 'index'])->name('admin.teachers.index');
    Route::get('/admin/teachers/create', [AdminTeacherController::class, 'create'])->name('admin.teachers.create');
    Route::post('/admin/teachers', [AdminTeacherController::class, 'store'])->name('admin.teachers.store');
    Route::get('/admin/teachers/{teacher}/edit', [AdminTeacherController::class, 'edit'])->name('admin.teachers.edit');
    Route::put('/admin/teachers/{teacher}', [AdminTeacherController::class, 'update'])->name('admin.teachers.update');
    Route::delete('/admin/teachers/{teacher}', [AdminTeacherController::class, 'destroy'])->name('admin.teachers.destroy');

    Route::get('/admin/rules', [AdminRuleController::class, 'index'])->name('admin.rules.index');
    Route::get('/admin/rules/create', [AdminRuleController::class, 'create'])->name('admin.rules.create');
    Route::post('/admin/rules', [AdminRuleController::class, 'store'])->name('admin.rules.store');
    Route::get('/admin/rules/{rule}/edit', [AdminRuleController::class, 'edit'])->name('admin.rules.edit');
    Route::put('/admin/rules/{rule}', [AdminRuleController::class, 'update'])->name('admin.rules.update');
    Route::delete('/admin/rules/{rule}', [AdminRuleController::class, 'destroy'])->name('admin.rules.destroy');

    Route::get('/admin/points', [AdminPointController::class, 'index'])->name('admin.points');
    Route::post('/admin/points/assign', [AdminPointController::class, 'assign'])->name('admin.points.assign');
    Route::post('/admin/points/history/{history}/cancel', [AdminPointController::class, 'cancel'])->name('admin.points.cancel');

    Route::get('/admin/history/student/{student}', [AdminHistoryWebController::class, 'student'])->name('admin.history.student');
    Route::get('/admin/history/teacher/{user}', [AdminHistoryWebController::class, 'teacher'])->name('admin.history.teacher');
});

Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teacher', [DashboardController::class, 'teacher'])->name('teacher.dashboard');
    Route::get('/teacher/assignments', [TeacherPointController::class, 'assignments'])->name('teacher.assignments');
    Route::post('/teacher/assign', [TeacherPointController::class, 'assign'])->name('teacher.assign');
    Route::post('/teacher/history/{history}/cancel', [TeacherPointController::class, 'cancel'])->name('teacher.history.cancel');

    Route::get('/teacher/my-class', [TeacherHomeroomController::class, 'index'])->name('teacher.homeroom');
    Route::get('/teacher/my-class/students/{student}', [TeacherHomeroomController::class, 'studentHistory'])->name('teacher.homeroom.student');
});

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student', [DashboardController::class, 'student'])->name('student.dashboard');
});


Route::middleware('auth')->group(function () {
    Route::get('/notifications', [WebNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [WebNotificationController::class, 'markAsRead'])->name('notifications.read');
});
