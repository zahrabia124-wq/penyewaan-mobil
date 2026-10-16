<head>
   
    <?php if (isset($base)) { echo '<base href="' . htmlspecialchars($base) . '">'; } ?>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>E-Rent - Pelanggan</title>
    <meta name="description" content="Sistem penyewaan dan rental mobil E-Rent">

    <!-- Favicon -->
    <link href="<?= $tpl ?>/img/favicon.png" rel="icon">
    <link href="<?= $tpl ?>/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="<?= $tpl ?>/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $tpl ?>/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= $tpl ?>/vendor/aos/aos.css" rel="stylesheet">
    <link href="<?= $tpl ?>/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="<?= $tpl ?>/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- CSS utama template Logis -->
    <link href="<?= $tpl ?>/css/main.css" rel="stylesheet">

    <!-- CSS tambahan E-Rent (taruh setelah main.css). Meniru gaya komponen template Logis -->
    <style>
        html { scroll-padding-top: 90px; }

        /* Kotak & baris bergaya "pricing-item" template */
        .erent-box  { background: var(--surface-color); box-shadow: 0 3px 20px -2px rgba(0,0,0,.1); border-radius: 5px; border-top: 4px solid var(--accent-color); padding: 30px; height: 100%; }
        .erent-item { background: var(--surface-color); box-shadow: 0 3px 20px -2px rgba(0,0,0,.1); border-radius: 5px; padding: 20px 26px; }
        .erent-meta { color: color-mix(in srgb, var(--default-color), transparent 40%); font-size: 14px; }
        .erent-price { font-family: var(--heading-font); font-weight: 700; font-size: 24px; color: var(--accent-color); }
        .erent-row { display: flex; justify-content: space-between; gap: 16px; padding: 9px 0; border-bottom: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); }
        .erent-row:last-child { border-bottom: 0; }

        /* Label status (bentuk pil) */
        .erent-badge { display: inline-block; padding: 3px 14px; border-radius: 50px; font-size: 13px; font-weight: 500; font-family: var(--heading-font); color: #fff; background: #6c757d; }
        .b-success { background: #198754; } .b-warning { background: #ffc107; color: #212529; } .b-danger { background: #dc3545; }

        /* Tombol pil seperti "buy-btn" template */
        .erent-btn { display: inline-block; padding: 8px 30px 10px; border-radius: 50px; font-family: var(--heading-font); font-weight: 500; font-size: 15px; border: 1px solid var(--accent-color); color: var(--accent-color); background: transparent; transition: .3s; cursor: pointer; text-align: center; }
        .erent-btn:hover, .erent-btn.solid { background: var(--accent-color); color: var(--contrast-color); }
        .erent-btn.solid:hover { background: color-mix(in srgb, var(--accent-color), transparent 15%); }
        .services .card a.erent-btn.solid { color: var(--contrast-color); }
        .erent-btn.danger { border-color: #dc3545; color: #dc3545; background: transparent; }
        .erent-btn.danger:hover { background: #dc3545; color: #fff; }
        .erent-btn.sm { padding: 5px 18px 6px; font-size: 13px; }

        /* Kartu mobil (memakai .services .card template) */
        .services .card-img { position: relative; }
        .services .card-img img { width: 100%; height: 220px; object-fit: cover; }
        .services .card-img .erent-badge { position: absolute; top: 12px; right: 12px; }
        .car-noimg { height: 220px; background: #f1f4f9; color: #9aa7bd; font-size: 3rem; }

        /* Form */
        .erent-form label { font-weight: 500; font-family: var(--heading-font); color: var(--heading-color); margin-bottom: 6px; }
        .erent-form .form-control, .erent-form .form-select { border-radius: 8px; padding: 11px 14px; }
        .erent-form .form-control:focus, .erent-form .form-select:focus { border-color: var(--accent-color); box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--accent-color), transparent 85%); }

        /* Ikon bulat, foto kecil, langkah status */
        .erent-icon { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: color-mix(in srgb, var(--accent-color), transparent 90%); color: var(--accent-color); }
        .erent-icon.red   { background: rgba(220,53,69,.1);  color: #dc3545; }
        .erent-icon.green { background: rgba(25,135,84,.1);  color: #198754; }
        .erent-icon.amber { background: rgba(255,193,7,.2);  color: #b58100; }
        .erent-thumb { width: 84px; height: 60px; object-fit: cover; border-radius: 8px; flex-shrink: 0; background: #f1f4f9; }
        .erent-cover { width: 100%; height: 100%; min-height: 230px; object-fit: cover; border-radius: 5px; background: #f1f4f9; }
        .erent-steps { display: flex; margin: 22px 0 4px; }
        .erent-step { flex: 1; text-align: center; position: relative; font-size: 13px; color: #9aa7bd; }
        .erent-step::before { content: ""; position: absolute; top: 13px; left: -50%; width: 100%; height: 3px; background: #dde3ee; }
        .erent-step:first-child::before { display: none; }
        .erent-step i { position: relative; z-index: 1; display: inline-flex; width: 28px; height: 28px; border-radius: 50%; background: #dde3ee; color: #fff; align-items: center; justify-content: center; font-size: 14px; }
        .erent-step span { display: block; margin-top: 6px; }
        .erent-step.done { color: var(--heading-color); }
        .erent-step.done i, .erent-step.done::before { background: var(--accent-color); }

        /* Ringkasan pemesanan (form ajukan sewa) */
        .erent-sum { position: sticky; top: 100px; padding: 0; overflow: hidden; }
        .erent-sum img { width: 100%; height: 190px; object-fit: cover; }
        .erent-sum .body { padding: 24px 28px 28px; }
        .erent-sum .kosong { height: 190px; background: #f1f4f9; color: #9aa7bd; font-size: 3.5rem; }
        .erent-chip { border: 1px solid color-mix(in srgb, var(--default-color), transparent 80%); background: transparent; border-radius: 50px; padding: 4px 16px; font-size: 14px; color: var(--default-color); transition: .2s; }
        .erent-chip:hover { border-color: var(--accent-color); color: var(--accent-color); }

        .stats .stats-item span.num-md { font-size: 28px; }
        @media print { .header, .footer, .page-title, .d-print-none, #scroll-top { display: none !important; } }
    </style>
</head>