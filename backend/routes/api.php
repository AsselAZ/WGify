<?php

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\Task; //Damit Laravel die Klasse Task kennt

//Ausgaben
Route::get('/expenses', function () {
    return Expense::latest()->get()->map(function ($expense) {
        return [
            'id' => (string) $expense->id,
            'title' => $expense->title,
            'amount' => (float) $expense->amount,
            'paidBy' => $expense->paid_by,
            'category' => $expense->category,
            'date' => $expense->date,
        ];
    });
});

Route::post('/expenses', function (Request $request) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric',
        'paidBy' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'date' => 'required|date',
    ]);

    $expense = Expense::create([
        'title' => $validated['title'],
        'amount' => $validated['amount'],
        'paid_by' => $validated['paidBy'],
        'category' => $validated['category'],
        'date' => $validated['date'],
    ]);

    return response()->json([
        'id' => (string) $expense->id,
        'title' => $expense->title,
        'amount' => (float) $expense->amount,
        'paidBy' => $expense->paid_by,
        'category' => $expense->category,
        'date' => $expense->date,
    ], 201);
});

Route::patch('/expenses/{expense}', function (Request $request, Expense $expense) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric',
        'paidBy' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'date' => 'required|date',
    ]);

    $expense->update([
        'title' => $validated['title'],
        'amount' => $validated['amount'],
        'paid_by' => $validated['paidBy'],
        'category' => $validated['category'],
        'date' => $validated['date'],
    ]);

    return response()->json([
        'id' => (string) $expense->id,
        'title' => $expense->title,
        'amount' => (float) $expense->amount,
        'paidBy' => $expense->paid_by,
        'category' => $expense->category,
        'date' => $expense->date,
    ]);
});

Route::delete('/expenses/{expense}', function (Expense $expense) {
    $expense->delete();

    return response()->json([
        'message' => 'Ausgabe wurde gelöscht',
    ]);
});


//Aufgaben ----------------------------------------------------------------
Route::get('/tasks', function () {
    return Task::latest()->get()->map(function ($task) {
        return [
            'id' => (string) $task->id,
            'title' => $task->title,
            'assignedTo' => $task->assigned_to,
            'dueDate' => $task->due_date,
            'status' => $task->status,
        ];
    });
});

Route::post('/tasks', function (Request $request) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'assignedTo' => 'required|string|max:255',
        'dueDate' => 'required|date',
        'status' => 'required|in:offen,erledigt',
    ]);

    $task = Task::create([
        'title' => $validated['title'],
        'assigned_to' => $validated['assignedTo'],
        'due_date' => $validated['dueDate'],
        'status' => $validated['status'],
    ]);

    return response()->json([
        'id' => (string) $task->id,
        'title' => $task->title,
        'assignedTo' => $task->assigned_to,
        'dueDate' => $task->due_date,
        'status' => $task->status,
    ], 201);
});

Route::patch('/tasks/{task}', function (Request $request, Task $task) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'assignedTo' => 'required|string|max:255',
        'dueDate' => 'required|date',
        'status' => 'required|in:offen,erledigt',
    ]);

    $task->update([
        'title' => $validated['title'],
        'assigned_to' => $validated['assignedTo'],
        'due_date' => $validated['dueDate'],
        'status' => $validated['status'],
    ]);

    return response()->json([
        'id' => (string) $task->id,
        'title' => $task->title,
        'assignedTo' => $task->assigned_to,
        'dueDate' => $task->due_date,
        'status' => $task->status,
    ]);
});

Route::delete('/tasks/{task}', function (Task $task) {
    $task->delete();

    return response()->json([
        'message' => 'Aufgabe wurde gelöscht',
    ]);
});

Route::patch('/tasks/{task}/toggle', function (Task $task) {
    $task->update([
        'status' => $task->status === 'offen' ? 'erledigt' : 'offen',
    ]);

    return response()->json([
        'id' => (string) $task->id,
        'title' => $task->title,
        'assignedTo' => $task->assigned_to,
        'dueDate' => $task->due_date,
        'status' => $task->status,
    ]);
});


// Auth ----------------------------------------------------------------

Route::post('/register', function (Request $request) {
    $request->merge([
        'email' => strtolower($request->email),
    ]);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    return response()->json([
        'message' => 'Registrierung erfolgreich',
        'user' => [
            'id' => (string) $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
    ], 201);
});

Route::post('/login', function (Request $request) {
    $request->merge([
        'email' => strtolower($request->email),
    ]);

    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $validated['email'])->first();

    if (!$user || !Hash::check($validated['password'], $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['E-Mail oder Passwort ist falsch.'],
        ]);
    }

    return response()->json([
        'message' => 'Login erfolgreich',
        'user' => [
            'id' => (string) $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
    ]);
});

// Mitglieder ----------------------------------------------------------------

Route::get('/members', function () {
    return User::latest()->get()->map(function ($user) {
        return [
            'id' => (string) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'mitglied',
            'avatar' => strtoupper(substr($user->name, 0, 1)),
        ];
    });
});