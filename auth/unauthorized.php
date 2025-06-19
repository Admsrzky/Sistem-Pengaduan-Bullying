<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Akses Ditolak</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- opsional -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fef2f2;
            color: #991b1b;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
            padding: 2rem;
            background: #fff;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.1);
        }

        a {
            color: #2563eb;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🚫 Akses Ditolak</h1>
        <p>Anda tidak memiliki hak untuk mengakses halaman ini.</p>
        <p><a href="../login.php">Kembali ke Login</a></p>
    </div>
</body>

</html>