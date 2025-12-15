<?php $base_path = '../'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include __DIR__ . '/../includes/theme-init.php'; ?>
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

  <div class="container">
    <?php include __DIR__ . '/../includes/navigation.php'; ?>

    <!-- Gallery Section -->
    <section class="gallery-section">
      <h1>Universal Darwinism Gallery</h1>
      <p>A collection of memes exploring ideas through evolution. Images are blurred for copyright protection until custom versions are created.</p>
      <p>Gallery contains 87 images.</p>

      <div class="image-gallery">
    <div class="gallery-item">
      <img src="../public/darwin-images/0_X0IJurzM0jxaTmfP.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/1712932866714.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/1716472366159.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/1wb598mj99uf1.jpeg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/474472102_630916636157112_6903352930067050940_n.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/494617685_10163275630730931_3223096136337366374_n.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/519469962_10163760960834747_7229454387577484788_n.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/536272989_1306858410810451_7165752281907154145_n.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/540960058_1951219005641254_2667259700526448303_n.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/542756521_122221568372172356_3429659511391183144_n.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/545144511_122247945332218675_3449199010565129083_n.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/577744199_4259879757665860_6770392193912512888_n.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/6emskuvhh6cf1.png" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/7d3uf8s23osf1.jpeg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/9ycu9vvwm5mf1.jpeg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/Epic-Reflective-Space-Mask.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/G5m7jEQacAMhulF.png" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/IMG-20250213-WA0003.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/IMG-20250213-WA0004.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/IMG-20250214-WA0006.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/Untitled.png" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/a-cool-guide-of-stretching-exercises-v0-umttfqkzooye1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/a-cool-guide-on-the-things-ive-learned-that-really-help-me-v0-7ia6nxylu2zf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/a-wooden-door-from-the-1930s-with-an-owl-design-in-v0-ses5neeyrbpf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/a2wBEax3_700w_0.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/a6qAER8_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/a87vbLZ_700bwp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aAymMKg_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aBdA3jz_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aD2N1GN_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aGEXj4X_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aKGwjd3_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aKGwjvj_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aKGzP1j_700bwp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aLnbm4P_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aMV0NjV_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aO8Dvj6_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aVv4yjP_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/adBKYNN_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/ae9Zz6b_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/agree-or-not-v0-ck4fdxujm5uf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/ajPKp2x_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/ajPd1dw_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/an7d1rq_700bwp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/anz6EVn_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/aqy9OA7_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/awyG85y_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/ay2GQnW_460swp.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/b3bp4cdjit0g1.jpeg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/be kind.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/be-a-champion-v0-bzwimu5ok61f1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/be-aware-v0-uad2ojy85gnf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/bodyweight-workout-for-muscle-gain-9-exercises-to-build-v0-i423yxlpfucf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/channels4_profile.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/closure-everyone-should-know-v0-8smn84rf80sf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/conquer-all-the-realms-v0-m49x7p20r37f1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/dd4ihf5p5hyf1.jpeg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/do-consider-incorporating-these-habits-step-by-step-v0-5kpxndu6jhzf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/flip-the-script-v0-c0bvz3gnn28f1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/fuck you and fuck your account.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/how-do-i-forgive-myself-v0-bfkwc7du4ulf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/i-dont-know-the-original-source-but-this-seems-quite-v0-ryfunu1sfqmf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/improve-full-body-strength-power-10-no-equipment-exercises-v0-mebhnwwr4vlf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/kekistan.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/let-them-assume-let-them-do-whatever-they-do-v0-hajf89z7a22g1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/life lesson from a microwave.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/love is mutal effort is.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/m3j8ggzjo4jf1.png" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/map-of-the-constellations-on-the-celestial-sphere-v0-5z7m15u66isd1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/my  goodness.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/p617qp6xgwyf1.jpeg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/rcv93dh4upjf1.jpeg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/request-would-this-work-and-could-it-turn-a-profit-v0-4tamjgav4lnf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/responding-to-criticism-v0-wyqnzg7ag40f1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/rrqw1crto13f1.jpeg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/sad.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/share holders.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/start-now-v0-ja3gy6doablf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/the-goal-is-to-give-a-fuck-about-yourself-v0-etjgfmi4hdof1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/the-secret-is-to-keep-to-yourself-v0-j8wc92zovz0f1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/themythicalmanmonthchicken-v0-m64z9f84ravf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/this-is-kind-of-a-realistic-reason-naruto-had-troubles-v0-u3smim8q5paf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/too-small-to-fail-v0-3ejqan3ijiyf1.webp" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/ve248fhtnwuf1.png" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/w4ult2xaxjue1.png" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/wine stuff.jpg" alt="Darwin meme" class="blurred-image">
    </div>
    <div class="gallery-item">
      <img src="../public/darwin-images/sell.webp" alt="Darwin meme" class="blurred-image">
    </div>
      </div>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
  </div>

  <?php include __DIR__ . '/../includes/mobile-menu.php'; ?>
</html>
