<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
<div id="auth-section" class="container mx-auto bg-white p-8 rounded-lg shadow-lg w-full max-w-3xl hidden">
    <h1 class="text-3xl font-bold mb-6 text-center">Login</h1>
    <form id="login-form" class="flex flex-col space-y-4">
        <input type="email" id="email" class="p-3 border rounded" placeholder="Email" required value="test@example.com">
        <input type="password" id="password" class="p-3 border rounded" placeholder="Password" required value="password">
        <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded">Login</button>
    </form>
</div>

<div id="task-section" class="container mx-auto bg-white p-8 rounded-lg shadow-lg w-full max-w-7xl hidden">
    <h1 class="text-3xl font-bold mb-6 text-center">Task Manager</h1>
    <form id="task-form" class="flex flex-col space-y-4 mb-6">
        <input type="text" id="task-input" class="p-3 border rounded" placeholder="Enter task description" required>
        <select id="task-status" class="p-3 border rounded" required>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
        <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded">Add Task</button>
    </form>
    <div class="overflow-x-auto">
        <table id="task-table" class="display w-full border rounded-lg nowrap">
            <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="app.js"></script>
</body>
</html>
