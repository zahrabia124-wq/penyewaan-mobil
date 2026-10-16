<!-- Judul halaman + breadcrumb (judul diambil dari $routes di index.php) -->
<div class="page-title dark-background" style="background-image: url(<?= $tpl ?>/img/page-title-bg.jpg);">
    <div class="container position-relative">
        <h1><?= htmlspecialchars($judul) ?></h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="index.php">Home</a></li>
                <li class="current"><?= htmlspecialchars($judul) ?></li>
            </ol>
        </nav>
    </div>
</div>