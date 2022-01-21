<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <link rel="stylesheet" href="<?= base_url() ?>/bootstrap/css/bootstrap.min.css">
    <script src="<?= base_url() ?>/bootstrap/js/bootstrap.min.js"></script>
</head>

<body>

    <nav class="navbar bg-light navbar-light navbar-expand-lg mb-5">

        <div class="container">

            <a href="/" class="navbar-brand">
                Inventario
            </a>

            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav">
                    <li class="navbar-item">
                        <a href="/dashboard/product" class="nav-link">
                            Productos
                        </a>
                    </li>
                    <li class="navbar-item">
                        <a href="/dashboard/category" class="nav-link">
                            Categorias
                        </a>
                    </li>
                    <li class="navbar-item">
                        <a href="/dashboard/tag" class="nav-link">
                            Tags
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">