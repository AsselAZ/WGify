<?php

use App\Models\Expense;
use App\Models\Task;
use App\Models\User;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

$getCurrentUser = function (Request $request) {
    $userId = $request->header('X-User-Id');

    if (!$userId) {
        abort(401, 'Kein Benutzer angemeldet.');
    }

    return User::with('apartment')->findOrFail($userId);
};

$userPayload = function (User $user) {
    $user->load('apartment');

    return [
        'id' => (string) $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'apartment_id' => $user->apartment_id ? (string) $user->apartment_id : null,
        'apartment' => $user->apartment ? [
            'id' => (string) $user->apartment->id,
            'name' => $user->apartment->name,
            'address' => $user->apartment->address,
            'currency' => $user->apartment->currency,
            'inviteCode' => $user->apartment->invite_code,
        ] : null,
    ];
};

// Ausgaben ----------------------------------------------------------------

Route::get('/expenses', function (Request $request) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        return [];
    }

    return Expense::where('apartment_id', $currentUser->apartment_id)
        ->latest()
        ->get()
        ->map(function ($expense) {
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

Route::post('/expenses', function (Request $request) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        abort(403, 'Du bist in keiner WG.');
    }

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric',
        'paidBy' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'date' => 'required|date',
    ]);

    $expense = Expense::create([
        'apartment_id' => $currentUser->apartment_id,
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

Route::patch('/expenses/{expense}', function (Request $request, Expense $expense) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if ($expense->apartment_id !== $currentUser->apartment_id) {
        abort(403, 'Du darfst diese Ausgabe nicht bearbeiten.');
    }

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

Route::delete('/expenses/{expense}', function (Request $request, Expense $expense) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if ($expense->apartment_id !== $currentUser->apartment_id) {
        abort(403, 'Du darfst diese Ausgabe nicht löschen.');
    }

    $expense->delete();

    return response()->json([
        'message' => 'Ausgabe wurde gelöscht',
    ]);
});

// Aufgaben ----------------------------------------------------------------

Route::get('/tasks', function (Request $request) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        return [];
    }

    return Task::where('apartment_id', $currentUser->apartment_id)
        ->latest()
        ->get()
        ->map(function ($task) {
            return [
                'id' => (string) $task->id,
                'title' => $task->title,
                'assignedTo' => $task->assigned_to,
                'dueDate' => $task->due_date,
                'status' => $task->status,
            ];
        });
});

Route::post('/tasks', function (Request $request) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        abort(403, 'Du bist in keiner WG.');
    }

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'assignedTo' => 'required|string|max:255',
        'dueDate' => 'required|date',
        'status' => 'required|in:offen,erledigt',
    ]);

    $task = Task::create([
        'apartment_id' => $currentUser->apartment_id,
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

Route::patch('/tasks/{task}', function (Request $request, Task $task) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if ($task->apartment_id !== $currentUser->apartment_id) {
        abort(403, 'Du darfst diese Aufgabe nicht bearbeiten.');
    }

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

Route::patch('/tasks/{task}/toggle', function (Request $request, Task $task) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if ($task->apartment_id !== $currentUser->apartment_id) {
        abort(403, 'Du darfst diese Aufgabe nicht ändern.');
    }

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

Route::delete('/tasks/{task}', function (Request $request, Task $task) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if ($task->apartment_id !== $currentUser->apartment_id) {
        abort(403, 'Du darfst diese Aufgabe nicht löschen.');
    }

    $task->delete();

    return response()->json([
        'message' => 'Aufgabe wurde gelöscht',
    ]);
});

// Apartment ----------------------------------------------------------------

Route::patch('/apartment/settings', function (Request $request) use ($getCurrentUser, $userPayload) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        abort(403, 'Du bist in keiner WG.');
    }

    if ($currentUser->role !== 'admin') {
        abort(403, 'Nur Admins dürfen WG-Einstellungen ändern.');
    }

    $validated = $request->validate([
        'address' => 'nullable|string|max:255',
        'currency' => 'required|in:EUR,USD,CHF,GBP',
    ]);

    $currentUser->apartment->update([
        'address' => $validated['address'] ?? null,
        'currency' => $validated['currency'],
    ]);

    return response()->json([
        'message' => 'WG-Einstellungen wurden gespeichert.',
        'user' => $userPayload($currentUser->fresh()),
    ]);
});

Route::post('/apartments/create', function (Request $request) use ($getCurrentUser, $userPayload) {
    $currentUser = $getCurrentUser($request);

    if ($currentUser->apartment_id) {
        throw ValidationException::withMessages([
            'apartment' => ['Du bist bereits in einer WG.'],
        ]);
    }

    $validated = $request->validate([
        'apartmentName' => 'required|string|max:255',
    ]);

    do {
        $inviteCode = strtoupper(Str::random(6));
    } while (Apartment::where('invite_code', $inviteCode)->exists());

    $apartment = Apartment::create([
        'name' => $validated['apartmentName'],
        'currency' => 'EUR',
        'invite_code' => $inviteCode,
    ]);

    $currentUser->update([
        'apartment_id' => $apartment->id,
        'role' => 'admin',
    ]);

    return response()->json([
        'message' => 'WG wurde erstellt.',
        'user' => $userPayload($currentUser->fresh()),
    ], 201);
});

Route::post('/apartments/join', function (Request $request) use ($getCurrentUser, $userPayload) {
    $currentUser = $getCurrentUser($request);

    if ($currentUser->apartment_id) {
        throw ValidationException::withMessages([
            'apartment' => ['Du bist bereits in einer WG.'],
        ]);
    }

    $request->merge([
        'inviteCode' => $request->inviteCode ? strtoupper(trim($request->inviteCode)) : null,
    ]);

    $validated = $request->validate([
        'inviteCode' => 'required|string|max:20',
    ]);

    $apartment = Apartment::where('invite_code', $validated['inviteCode'])->first();

    if (!$apartment) {
        throw ValidationException::withMessages([
            'inviteCode' => ['Dieser Einladungscode ist ungültig.'],
        ]);
    }

    $currentUser->update([
        'apartment_id' => $apartment->id,
        'role' => 'mitglied',
    ]);

    return response()->json([
        'message' => 'Du bist der WG beigetreten.',
        'user' => $userPayload($currentUser->fresh()),
    ]);
});

// Auth ----------------------------------------------------------------

Route::post('/register', function (Request $request) use ($userPayload) {
    $request->merge([
        'email' => strtolower($request->email),
        'inviteCode' => $request->inviteCode ? strtoupper(trim($request->inviteCode)) : null,
    ]);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'mode' => 'required|in:create,join',
        'apartmentName' => 'required_if:mode,create|nullable|string|max:255',
        'inviteCode' => 'required_if:mode,join|nullable|string|max:20',
    ]);

    if ($validated['mode'] === 'create') {
        do {
            $inviteCode = strtoupper(Str::random(6));
        } while (Apartment::where('invite_code', $inviteCode)->exists());

        $apartment = Apartment::create([
            'name' => $validated['apartmentName'],
            'currency' => 'EUR',
            'invite_code' => $inviteCode,
        ]);

        $role = 'admin';
    } else {
        $apartment = Apartment::where('invite_code', $validated['inviteCode'])->first();

        if (!$apartment) {
            throw ValidationException::withMessages([
                'inviteCode' => ['Dieser Einladungscode ist ungültig.'],
            ]);
        }

        $role = 'mitglied';
    }

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'apartment_id' => $apartment->id,
        'role' => $role,
    ]);

    return response()->json([
        'message' => 'Registrierung erfolgreich',
        'user' => $userPayload($user),
    ], 201);
});

Route::post('/login', function (Request $request) use ($userPayload) {
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
        'user' => $userPayload($user),
    ]);
});

// Mitglieder ----------------------------------------------------------------

Route::get('/members', function (Request $request) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        return [];
    }

    return User::where('apartment_id', $currentUser->apartment_id)
        ->latest()
        ->get()
        ->map(function ($user) {
            return [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => strtoupper(substr($user->name, 0, 1)),
            ];
        });
});

Route::delete('/members/{member}', function (Request $request, User $member) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        abort(403, 'Du bist in keiner WG.');
    }

    if ($currentUser->role !== 'admin') {
        abort(403, 'Nur Admins dürfen Mitglieder entfernen.');
    }

    if ($member->id === $currentUser->id) {
        throw ValidationException::withMessages([
            'member' => ['Du kannst dich hier nicht selbst entfernen. Nutze dafür WG verlassen.'],
        ]);
    }

    if ($member->apartment_id !== $currentUser->apartment_id) {
        abort(403, 'Dieses Mitglied gehört nicht zu deiner WG.');
    }

    $member->update([
        'apartment_id' => null,
        'role' => 'mitglied',
    ]);

    return response()->json([
        'message' => 'Mitglied wurde aus der WG entfernt.',
    ]);
});

// WG verlassen --------------------------------------------------------------

Route::post('/apartments/leave', function (Request $request) use ($getCurrentUser, $userPayload) {
    $user = $getCurrentUser($request);

    if (!$user->apartment_id) {
        throw ValidationException::withMessages([
            'apartment' => ['Du bist aktuell in keiner WG.'],
        ]);
    }

    $apartment = $user->apartment;
    $members = User::where('apartment_id', $apartment->id)->get();

    if ($members->count() === 1) {
        $user->update([
            'apartment_id' => null,
            'role' => 'mitglied',
        ]);

        $apartment->delete();

        return response()->json([
            'message' => 'Du hast die WG verlassen. Die WG wurde gelöscht, weil du das letzte Mitglied warst.',
            'user' => $userPayload($user->fresh()),
        ]);
    }

    if ($user->role === 'admin') {
        $nextAdmin = User::where('apartment_id', $apartment->id)
            ->where('id', '!=', $user->id)
            ->oldest()
            ->first();

        if ($nextAdmin) {
            $nextAdmin->update([
                'role' => 'admin',
            ]);
        }
    }

    $user->update([
        'apartment_id' => null,
        'role' => 'mitglied',
    ]);

    return response()->json([
        'message' => 'Du hast die WG verlassen.',
        'user' => $userPayload($user->fresh()),
    ]);
});