export const members = [
  { id: '1', name: 'Assel', email: 'assel@wgify.de', role: 'admin', avatar: 'A' },
  { id: '2', name: 'Max', email: 'max@wgify.de', role: 'mitglied', avatar: 'M' },
  { id: '3', name: 'Lisa', email: 'lisa@wgify.de', role: 'mitglied', avatar: 'L' },
  { id: '4', name: 'Tom', email: 'tom@wgify.de', role: 'mitglied', avatar: 'T' },
]

export const categories = ['Miete', 'Strom', 'Internet', 'Lebensmittel', 'Haushalt', 'Sonstiges']

export const expenses = [
  { id: '1', title: 'Internet', amount: 39.99, category: 'Internet', paidBy: 'Assel', date: '2024-01-15' },
  { id: '2', title: 'Strom', amount: 85.50, category: 'Strom', paidBy: 'Max', date: '2024-01-10' },
  { id: '3', title: 'Einkauf REWE', amount: 67.30, category: 'Lebensmittel', paidBy: 'Lisa', date: '2024-01-08' },
  { id: '4', title: 'Putzzeug', amount: 23.80, category: 'Haushalt', paidBy: 'Tom', date: '2024-01-05' },
  { id: '5', title: 'Klopapier', amount: 12.99, category: 'Haushalt', paidBy: 'Assel', date: '2024-01-03' },
]

export const tasks = [
  { id: '1', title: 'Kueche putzen', assignedTo: 'Assel', dueDate: '2024-01-20', status: 'offen' },
  { id: '2', title: 'Muell rausbringen', assignedTo: 'Max', dueDate: '2024-01-18', status: 'erledigt' },
  { id: '3', title: 'Bad schrubben', assignedTo: 'Lisa', dueDate: '2024-01-22', status: 'offen' },
  { id: '4', title: 'Einkaufen gehen', assignedTo: 'Tom', dueDate: '2024-01-19', status: 'offen' },
  { id: '5', title: 'Flur saugen', assignedTo: 'Assel', dueDate: '2024-01-17', status: 'erledigt' },
]