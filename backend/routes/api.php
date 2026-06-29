<?php

use App\Models\Expense;
use App\Models\Task;
use App\Models\User;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Mail\ApartmentInviteMail;
use App\Mail\MemberRemovedMail;
use App\Mail\TaskAssignedToMemberMail;
use App\Mail\MemberLeftNotificationMail;
use App\Mail\ApartmentLeftMail;
use App\Mail\MemberJoinsMail;
use App\Models\Invitation;
use App\Mail\TaskOverdueMail;

$getCurrentUser = function (Request $request) {
    $userId = $request->header('X-User-Id');

    if (!$userId) {
        abort(401, 'Kein Benutzer angemeldet.');
    }

    return User::with('apartment')->findOrFail($userId);
};

$refreshInviteCodeIfExpired = function (Apartment $apartment) {
    $lastUpdated = $apartment->invite_code_updated_at
        ? Carbon::parse($apartment->invite_code_updated_at)
        : null;

    if ($lastUpdated && $lastUpdated->gt(now()->subMinutes(5))) {
        return $apartment;
    }

    do {
        $newInviteCode = strtoupper(Str::random(6));
    } while (Apartment::where('invite_code', $newInviteCode)
        ->where('id', '!=', $apartment->id)
        ->exists());

    $apartment->invite_code = $newInviteCode;
    $apartment->invite_code_updated_at = now();
    $apartment->save();

    return $apartment->fresh();
};

$userPayload = function (User $user) use ($refreshInviteCodeIfExpired) {
    $user->load('apartment');

    //Der User bekommt den aktuellen code
    if ($user->apartment) {
        $refreshInviteCodeIfExpired($user->apartment);
        $user->load('apartment');
    }

    return [
        'id' => (string) $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'avatar' => $user->avatar_path,
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
Route::get('/tasks/overdue', function (Request $request) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        return [];
    }

    $overdueTasks = Task::where('apartment_id', $currentUser->apartment_id)
        ->where('status', 'offen')
        ->whereDate('due_date', '<', now()->toDateString())
        ->get();

    $members = User::where('apartment_id', $currentUser->apartment_id)->get();

    foreach ($overdueTasks as $task) {
        if (!$task->overdue_notification_sent) {
            foreach ($members as $member) {
                Mail::to($member->email)->send(
                    new TaskOverdueMail(
                        $member->name,
                        $task->title,
                        $task->due_date
                    )
                );
            }

            $task->update([
                'overdue_notification_sent' => true,
            ]);
        }
    }

    return $overdueTasks->map(function ($task) {
        return [
            'id' => (string) $task->id,
            'title' => $task->title,
            'assignedTo' => $task->assigned_to,
            'dueDate' => $task->due_date,
            'status' => $task->status,
        ];
    });
});
// Route::post('/tasks', function (Request $request) use ($getCurrentUser) {
//     $currentUser = $getCurrentUser($request);

//     if (!$currentUser->apartment_id) {
//         abort(403, 'Du bist in keiner WG.');
//     }

//     $validated = $request->validate([
//         'title' => 'required|string|max:255',
//         'assignedTo' => 'required|string|max:255',
//         'dueDate' => 'required|date',
//         'status' => 'required|in:offen,erledigt',
//     ]);

//     $task = Task::create([
//         'apartment_id' => $currentUser->apartment_id,
//         'title' => $validated['title'],
//         'assigned_to' => $validated['assignedTo'],
//         'due_date' => $validated['dueDate'],
//         'status' => $validated['status'],
//     ]);

//     return response()->json([
//         'id' => (string) $task->id,
//         'title' => $task->title,
//         'assignedTo' => $task->assigned_to,
//         'dueDate' => $task->due_date,
//         'status' => $task->status,
//     ], 201);
// });

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

    // User holen
    $member = User::where('name', $validated['assignedTo'])
    ->where('apartment_id', $currentUser->apartment_id)
    ->firstOrFail();

    // Mail senden
    Mail::to($member->email)->send(
        new TaskAssignedToMemberMail(
            $member->name,
            $task->title,
            $task->due_date
        )
    );

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
        'name' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'currency' => 'required|in:EUR,USD,CHF,GBP',
    ]);

    $currentUser->apartment->update([
        'name' => $validated['name'] ?? null,
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
        'invite_code_updated_at' => now(),
        $apartment->save(),
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
    Invitation::where('apartment_id', $apartment->id)
    ->where('email', $currentUser->email)
    ->where('status', 'pending')
    ->update([
        'status' => 'accepted',
    ]);

    return response()->json([
        'message' => 'Du bist der WG beigetreten.',
        'user' => $userPayload($currentUser->fresh()),
    ]);
});

// Einladung per E-Mail senden ---------------------------------------------------
Route::post('/apartments/invite', function (Request $request) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        abort(403, 'Du bist in keiner WG.');
    }

    if ($currentUser->role !== 'admin') {
        abort(403, 'Nur Admins dürfen Einladungen versenden.');
    }

    $validated = $request->validate([
        'email' => 'required|email',
    ]);

    $apartment = $currentUser->apartment;

    Invitation::create([
        'apartment_id' => $currentUser->apartment_id,
        'email' => $validated['email'],
        'status' => 'pending',
        'expires_at' => now()->addWeek(),
    ]);

    Mail::to($validated['email'])->send(
        new ApartmentInviteMail(
            $apartment->invite_code,
            $apartment->name
        )
    );

    return response()->json([
        'message' => 'Einladung wurde gesendet.',
    ]);
});
Route::get('/invitations/pending-count', function (Request $request) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        return response()->json([
            'count' => 0,
        ]);
    }

    $count = Invitation::where('apartment_id', $currentUser->apartment_id)
        ->where('status', 'pending')
        ->where('expires_at', '>', now())
        ->count();

    return response()->json([
        'count' => $count,
    ]);
});

Route::post('/profile/avatar', function (Request $request) use ($getCurrentUser, $userPayload) {
    $currentUser = $getCurrentUser($request);

    $validated = $request->validate([
        'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    if ($currentUser->avatar_path) {
        Storage::disk('public')->delete($currentUser->avatar_path);
    }

    $path = $validated['avatar']->store('avatars', 'public');

    $currentUser->update([
        'avatar_path' => $path,
    ]);

    return response()->json([
        'message' => 'Profilbild wurde gespeichert.',
        'user' => $userPayload($currentUser->fresh()),
    ]);
});

Route::delete('/profile/avatar', function (Request $request) use ($getCurrentUser, $userPayload) {
    $currentUser = $getCurrentUser($request);

    if ($currentUser->avatar_path) {
        Storage::disk('public')->delete($currentUser->avatar_path);
    }

    $currentUser->update([
        'avatar_path' => null,
    ]);

    return response()->json([
        'message' => 'Profilbild wurde gelöscht.',
        'user' => $userPayload($currentUser->fresh()),
    ]);
});
// Auth ----------------------------------------------------------------

Route::post('/apartments/join', function (Request $request) use ($getCurrentUser, $userPayload, $refreshInviteCodeIfExpired) {
    $request->merge([
        'email' => strtolower($request->email),
        'inviteCode' => $request->inviteCode ? strtoupper(trim($request->inviteCode)) : null,
    ]);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'mode' => 'required|in:create,join',
        'apartmentName' => 'required_if:mode,create|nullable|string|max:255',
        'inviteCode' => 'required_if:mode,join|nullable|string|max:20',
    ], [
        'name.required' => 'Bitte gib deinen Namen ein.',
        'email.required' => 'Bitte gib deine E-Mail-Adresse ein.',
        'email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
        'email.unique' => 'Diese E-Mail-Adresse ist bereits registriert.',
        'password.required' => 'Bitte gib ein Passwort ein.',
        'password.min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
        'password.confirmed' => 'Die Passwörter stimmen nicht überein.',
        'mode.required' => 'Bitte wähle aus, ob du eine WG erstellen oder beitreten möchtest.',
        'mode.in' => 'Bitte wähle eine gültige Option aus.',
        'apartmentName.required_if' => 'Bitte gib einen WG-Namen ein.',
        'inviteCode.required_if' => 'Bitte gib einen Einladungscode ein.',
    ]);

    if ($validated['mode'] === 'create') {
        do {
            $inviteCode = strtoupper(Str::random(6));
        } while (Apartment::where('invite_code', $inviteCode)->exists());

        $apartment = Apartment::create([
            'name' => $validated['apartmentName'],
            'currency' => 'EUR',
            'invite_code' => $inviteCode,
            'invite_code_updated_at' => now(),
            $apartment->save(),
        ]);

        $role = 'admin';
    } else {
        $apartment = Apartment::where('invite_code', $validated['inviteCode'])->first();

        if (!$apartment) {
            throw ValidationException::withMessages([
                'inviteCode' => ['Dieser Einladungscode ist ungültig oder abgelaufen.'],
            ]);
        }

        $refreshInviteCodeIfExpired($apartment);
        $apartment->refresh();

        if ($apartment->invite_code !== $validated['inviteCode']) {
            throw ValidationException::withMessages([
                'inviteCode' => ['Dieser Einladungscode ist abgelaufen. Bitte fordere den neuen Code an.'],
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
    Invitation::where('apartment_id', $apartment->id)
    ->where('email', $validated['email'])
    ->where('status', 'pending')
    ->update([
        'status' => 'accepted',
    ]);

    // NUR wenn WG beigetreten wird
    if ($validated['mode'] === 'join') {
        $otherMembers = User::where('apartment_id', $apartment->id)
            ->where('name', '!=', $user->name)
            ->get();

        foreach ($otherMembers as $member) {
            Mail::to($member->email)->send(
                new MemberJoinsMail(
                    $member->name,
                    $user->name,
                    $apartment->name
                )
            );
        }
    }

    return response()->json([
        'message' => 'Registrierung erfolgreich',
        'user' => $userPayload($user),
    ], 201);
});

//Für den neuen generierten code
Route::get('/apartment/invite-code', function (Request $request) use ($getCurrentUser, $refreshInviteCodeIfExpired) {
    $currentUser = $getCurrentUser($request);

    if (!$currentUser->apartment_id) {
        abort(403, 'Du bist in keiner WG.');
    }

    $apartment = $refreshInviteCodeIfExpired($currentUser->apartment);

    return response()->json([
        'inviteCode' => $apartment->invite_code,
        'validUntil' => Carbon::parse($apartment->invite_code_updated_at)
            ->addMinutes(5)
            ->toISOString(),
    ]);
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

    // E-Mail verschicken
    Mail::to($member->email)->send(
    new MemberRemovedMail(
        $member->name,
        $currentUser->apartment->name
    ));

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

    // WICHTIG: Mitglieder VOR Änderung holen
    $members = User::where('apartment_id', $apartment->id)->get();

    // alle anderen Mitglieder (außer der User selbst)
    $remainingMembers = $members->where('id', '!=', $user->id);

    if ($members->count() === 1) {

        Mail::to($user->email)->send(
            new ApartmentLeftMail($user->name, $apartment->name)
        );

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

    // Admin-Wechsel falls nötig
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

    

    // Mail an verbleibende Mitglieder
    foreach ($remainingMembers as $member) {
        Mail::to($member->email)->send(
            new MemberLeftNotificationMail(
                $member->name,
                $user->name,
                $apartment->name
            )
        );
    }

    // Mail an den User selbst (gesondert)
    Mail::to($user->email)->send(
        new ApartmentLeftMail(
            $user->name,
            $apartment->name
        )
    );

    // User entfernen
    $user->update([
        'apartment_id' => null,
        'role' => 'mitglied',
    ]);

    return response()->json([
        'message' => 'Du hast die WG verlassen.',
        'user' => $userPayload($user->fresh()),
    ]);
});

Route::post('/user/password', function (Request $request) use ($getCurrentUser) {
    $currentUser = $getCurrentUser($request);

    $validated = $request->validate([
        'current_password' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
    ], [
        'current_password.required' => 'Bitte gib dein aktuelles Passwort ein.',
        'password.required' => 'Bitte gib dein neues Passwort ein.',
        'password.min' => 'Das neue Passwort muss mindestens 8 Zeichen lang sein.',
        'password.confirmed' => 'Die neuen Passwörter stimmen nicht überein.',
    ]);

    if (!Hash::check($validated['current_password'], $currentUser->password)) {
        return response()->json([
            'message' => 'Das aktuelle Passwort ist falsch.',
            'errors' => [
                'current_password' => ['Das aktuelle Passwort ist falsch.'],
            ],
        ], 422);
    }

    $currentUser->password = Hash::make($validated['password']);
    $currentUser->save();

    return response()->json([
        'message' => 'Passwort wurde erfolgreich geändert.',
    ]);
});