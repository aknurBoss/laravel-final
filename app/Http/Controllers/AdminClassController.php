<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminClassController extends Controller
{
    public function index(): View
    {
        $classes = SchoolClass::withCount('students')->orderBy('name')->paginate(20);

        return view('admin.classes.index', compact('classes'));
    }

    public function create(): View
    {
        return view('admin.classes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:school_classes,name'],
        ]);

        SchoolClass::create($data);

        return redirect()->route('admin.classes.index')->with('status', "Класс «{$data['name']}» создан.");
    }

    public function edit(SchoolClass $class): View
    {
        return view('admin.classes.edit', compact('class'));
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', "unique:school_classes,name,{$class->id}"],
        ]);

        $class->update($data);

        return redirect()->route('admin.classes.index')->with('status', "Класс обновлён → «{$data['name']}».");
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('admin.classes.index')->with('status', 'Класс удалён.');
    }
}
