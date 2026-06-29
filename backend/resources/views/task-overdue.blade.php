<x-mail::message>
# Überfällige Aufgabe

Hallo {{ $memberName }},

die Aufgabe **{{ $taskTitle }}** ist überfällig.

Fälligkeitsdatum: **{{ \Carbon\Carbon::parse($dueDate)->format('d.m.Y') }}**

Bitte schaut gemeinsam in der WG nach, wer die Aufgabe übernimmt.

Viele Grüße  
WGify
</x-mail::message>