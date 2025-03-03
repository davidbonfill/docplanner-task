document.addEventListener("DOMContentLoaded", async function () {
    const taskForm = document.getElementById("task-form");
    const taskInput = document.getElementById("task-input");
    const taskStatus = document.getElementById("task-status");
    const loginForm = document.getElementById("login-form");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const apiUrl = "http://localhost:5000/api/tasks";

    const isAuthenticated = await checkAuth();

    if (!isAuthenticated) {
        document.getElementById("auth-section").classList.remove("hidden");
        loginForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();
            if (!email || !password) return;

            const response = await fetch("http://localhost:5000/api/login", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ email, password })
            });

            if (response.ok) {
                const data = await response.json();
                // Se asume que el endpoint devuelve { token: "..." }
                localStorage.setItem("api_token", data.token);
                location.reload();
            } else {
                console.error("Login failed", await response.json());
            }
        });
        return;
    } else {
        document.getElementById("task-section").classList.remove("hidden");
    }

    const taskTable = $("#task-table").DataTable({
        serverSide: true,
        ajax: {
            url: apiUrl + '/datatable',
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${localStorage.getItem("api_token")}`
            },
            dataSrc: "data"
        },
        columns: [
            { data: "id" },
            { data: "description" },
            { data: "status" },
            {
                data: "created_at",
                render: function (data) {
                    return new Date(data).toLocaleString();
                }
            },
            {
                data: "updated_at",
                render: function (data) {
                    return new Date(data).toLocaleString();
                }
            },
            {
                data: "id",
                render: function (data) {
                    return `
                        <button class="edit-btn bg-yellow-500 text-white px-2 py-1 rounded" data-id="${data}">Edit</button>
                        <button class="delete-btn bg-red-500 text-white px-2 py-1 rounded" data-id="${data}">Delete</button>
                    `;
                },
            },
        ],
        order: [[3, 'desc']]
    });

    taskForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const taskName = taskInput.value.trim();
        const taskStatusValue = taskStatus.value;
        if (!taskName) return;

        fetch(apiUrl, {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": `Bearer ${localStorage.getItem("api_token")}`
            },
            body: JSON.stringify({ description: taskName, status: taskStatusValue })
        })
            .then(response => response.json())
            .then(() => {
                taskInput.value = "";
                taskTable.ajax.reload(null, false);
            })
            .catch(error => console.error("Error adding task:", error));
    });

    $("#task-table tbody").on("click", ".delete-btn", function () {
        const taskId = $(this).data("id");

        fetch(`${apiUrl}/${taskId}`, {
            method: "DELETE",
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${localStorage.getItem("api_token")}`
            }
        })
            .then(() => taskTable.ajax.reload(null, false))
            .catch(error => console.error("Error deleting task:", error));
    });

    $("#task-table tbody").on("click", ".edit-btn", function () {
        const taskId = $(this).data("id");
        const currentDescription = $(this).data("description");
        const currentStatus = $(this).data("status");

        const newDescription = prompt("Enter new task description:", currentDescription);
        if (newDescription === null) return;

        const newStatus = prompt("Enter new status (pending, in_progress, completed):", currentStatus);
        if (newStatus === null || !["pending", "in_progress", "completed"].includes(newStatus)) {
            alert("Invalid status. Use: pending, in_progress, or completed.");
            return;
        }

        fetch(`${apiUrl}/${taskId}`, {
            method: "PUT",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": `Bearer ${localStorage.getItem("api_token")}`
            },
            body: JSON.stringify({ description: newDescription, status: newStatus }),
        })
        .then(() => taskTable.ajax.reload(null, false))
        .catch(error => console.error("Error updating task:", error));
    });

    // Función para comprobar autenticación usando el token
    async function checkAuth() {
        const token = localStorage.getItem("api_token");
        if (!token) return false;
        try {
            const response = await fetch("http://localhost:5000/api/user", {
                method: "GET",
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${token}`
                }
            });
            return response.ok;
        } catch (error) {
            return false;
        }
    }
});
