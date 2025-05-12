<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

// Fetch some data for the admin dashboard (example)
$query = "SELECT * FROM users";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            display: flex;
            margin: 0;
        }
        .sidebar {
            width: 200px;
            background-color: #f4f4f4;
            padding: 15px;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar a {
            display: block;
            color: #333;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #ddd;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="index.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <header>
            <h1>Admin Dashboard</h1>
        </header>
        <main>
            <h2>Welcome, Admin</h2>
            <section>
                <h3>User List</h3>
                <table border="1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistema de Filas</p>
        </footer>
    </div>
</body>
</html>