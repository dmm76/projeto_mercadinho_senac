<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= htmlspecialchars($title ?? 'Home') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body class="bg-light">
<div class="container py-4">
<h1 class="h3 mb-3">Mercadinho Borba Gato</h1>
<p>Boas-vindas! Scaffold MVC no ar.</p>
<a class="btn btn-primary" href="<?= \App\Core\Url::to('/admin') ?>">Ir para Admin</a>

</div>
<script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>