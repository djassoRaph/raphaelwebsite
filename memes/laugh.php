<?php $base_path = '../'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Universal Darwinism | Memes</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    .gallery-section {
      margin: 40px 0;
    }

    .image-gallery {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 24px;
      margin-top: 30px;
    }

    .gallery-item {
      background: var(--color-surface);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: var(--shadow-card);
      border: 1px solid var(--color-border);
      transition: all 0.3s ease;
    }

    .gallery-item:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }

    .blurred-image {
      width: 100%;
      height: 280px;
      object-fit: cover;
      filter: blur(10px);
      transition: all 0.3s ease;
    }

    .gallery-item:hover .blurred-image {
      filter: blur(5px);
      transform: scale(1.05);
    }

    @media (max-width: 768px) {
      .image-gallery {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
      }

      .blurred-image {
        height: 200px;
      }
    }

    @media (max-width: 480px) {
      .image-gallery {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/../includes/theme-toggle.php'; ?>
  <?php include __DIR__ . '/../includes/mobile-menu.php'; ?>
</body>
</html>
