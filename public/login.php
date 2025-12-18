<?php 
session_start();

// setelah login berhasil
$_SESSION['login'] = true;
$_SESSION['role'] = 'admin';


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin | Lawakfest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            min-height: 100vh;
        }
        .login-card {
            border-radius: 15px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg login-card">
                <div class="card-body p-4">

                    <!-- Judul --> 
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                        <h4 class="mt-2 fw-bold">Login Admin</h4>
                        <p class="text-muted mb-0">Sistem Buku Tamu Lawakfest</p>
                    </div>

                    <!-- Pesan Error -->
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger text-center">
                            Username atau password salah
                        </div>
                    <?php endif; ?>

                    <!-- Form Login -->
                    <form action="login_proses.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </button>
                    </form>

                </div>
            </div>

            <!-- Footer kecil -->
            <p class="text-center text-light mt-3 small">
                © <?= date('Y') ?> Lawakfest — Admin Panel
            </p>
        </div>
    </div>
</div>

</body>
</html>
