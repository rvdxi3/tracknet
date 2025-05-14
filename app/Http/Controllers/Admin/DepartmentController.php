<?php

// app/Http/Controllers/Admin/DepartmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('users')->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }
    
    public function create()
    {
        return view('admin.departments.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments'],
            'description' => ['nullable', 'string']
        ]);
        
        Department::create($request->only(['name', 'description']));
        
        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully');
    }
    
    public function show(Department $department)
    {
        $users = $department->users()->paginate(10);
        return view('admin.departments.show', compact('department', 'users'));
    }
    
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }
    
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'description' => ['nullable', 'string']
        ]);
        
        $department->update($request->only(['name', 'description']));
        
        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully');
    }
    
    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.departments.index')->with('success', 'Department deleted successfully');
    }
}