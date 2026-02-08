<!DOCTYPE html>
<html lang="<?php echo $lang ?? 'fr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo $pageTitle ?? t('meta_title'); ?>
    </title>
    <meta name="description" content="<?php echo $pageDesc ?? t('meta_desc'); ?>">

    <?php if (isset($canonicalUrl)): ?>
    <link rel="canonical" href="<?php echo $canonicalUrl; ?>" />
    <?php
endif; ?>

    <?php if (isset($alternates)):
    foreach ($alternates as $altLang => $altUrl): ?>
    <link rel="alternate" hreflang="<?php echo $altLang; ?>" href="<?php echo $altUrl; ?>" />
    <?php
    endforeach;
endif; ?>

    <!-- OPEN GRAPH -->
    <meta property="og:title" content="<?php echo $pageTitle ?? t('meta_title'); ?>">
    <meta property="og:description" content="<?php echo $pageDesc ?? t('meta_desc'); ?>">
    <meta property="og:type" content="website">
    <?php if (isset($canonicalUrl)): ?>
    <meta property="og:url" content="<?php echo $canonicalUrl; ?>">
    <?php
endif; ?>

    <!-- ASSETS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(to right, #3b82f6, #8b5cf6);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>

    <?php if (isset($extraHead))
    echo $extraHead; ?>
</head>

<body class="min-h-screen flex flex-col">