<?php

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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