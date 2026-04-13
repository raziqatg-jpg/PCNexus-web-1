<?php
session_start();
if (isset($_SESSION['user_id']) || !empty($_SESSION['login'])) {
   header("Location: index.php");
   exit;
}
?>
<!DOCTYPE html>
   <html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <!--=============== REMIXICONS ===============-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">

      <!--=============== SWIPER CSS ===============-->
      <link rel="stylesheet" href="Adaftar/assets/css/swiper-bundle.min.css">

      <!--=============== CSS ===============-->
      <link rel="stylesheet" href="Adaftar/assets/css/styles.css?v=1">

      <title>Login page</title>
   </head>
   <body>
      <div class="login container grid">
        <div class="login__container grid">
            <div class="login__swiper swiper">
                <div class="login__swiper-data">
                    <p class="login__swiper-subtitel">Welcome back</p>
                    <h1 class="login__swiper-title">hello users <br>sign in before stared</h1>
                </div>
                <div class="login__swiper-social">
                    <p class="login__swiper-subtitle">our sosia media</p>
                    <div class="login__swiper-links">
                        <a href="https://www.instagram.com/ra_34170/" target="_blank" class="login__swiper-link">
                            <i class="ri-instagram-line"></i>
                        </a>
                        <a href="https://www.instagram.com/ra_34170/" target="_blank" class="login__swiper-link">
                            <i class="ri-instagram-line"></i>
                        </a>
                        <a href="https://www.instagram.com/ra_34170/" target="_blank" class="login__swiper-link">
                            <i class="ri-instagram-line"></i>
                        </a>
                        <a href="https://www.instagram.com/ra_34170/" target="_blank" class="login__swiper-link">
                            <i class="ri-instagram-line"></i>
                        </a>
                    </div>
                </div>
                <div class="swiper-wrapper">
                <div class="swiper-slide">
                     <img src="Adaftar/assets/img/img-1.png" alt="image" class="daftar__swiper-img">
                </div>
                <div class="swiper-slide">
                     <img src="Adaftar/assets/img/img-2.png" alt="image" class="daftar__swiper-img">
                </div>
                <div class="swiper-slide">
                     <img src="Adaftar/assets/img/img-3.png" alt="image" class="daftar__swiper-img">
                </div>
                </div>
                  <div class="swiper-pagination"></div>
            </div>
           <div class="login__area grid">
            <div class="login__data">
                <h1 class="login__title">Daftarkan <br>akunmu</h1>
                <p class="login__description">Please enter you details</p>
            </div>
           <form action="fungsi/pdaftar.php" method="POST" enctype="multipart/form-data" class="login__form">
     <div class="login__content grid">
    <?php if(isset($_GET['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['popup'])): ?>
      <?php if($_GET['popup']==='email_terdaftar'): ?>
        <div class="alert alert-warning">Email sudah digunakan, silakan gunakan email lain.</div>
      <?php elseif($_GET['popup']==='server_error'): ?>
        <div class="alert alert-danger">Terjadi kesalahan server, silakan coba lagi.</div>
      <?php endif; ?>
    <?php endif; ?>

    <div class="login__box">
      <input type="text" name="username" placeholder="username" required class="login__input">
    </div>

    <div class="login__box">
      <input type="text" name="nama_lengkap" placeholder="Nama lengkap" required class="login__input">
    </div>

    <div class="login__box">
      <input type="email" name="email" placeholder="Email" required class="login__input">
    </div>

    <div class="login__box">
      <input type="password" name="password" placeholder="Password" required class="login__input" id="loginpass">
      <i class="ri-eye-fill login__eye" id="logineye"></i>
    </div>

    <div class="form-group">
    <label class="upload-box">
    <input type="file" name="foto_profil" accept="image/*" hidden>
    <div class="upload-content">
      <span class="upload-icon">📷</span>
      <span class="upload-text">Upload Foto Profil</span>
      <small>JPG / PNG (max 2MB)</small>
    </div>
        </label>
                </div>
                    <button type="submit" class="login__button">Daftar</button>
                </form>
            </div>
        </div>
      </div>
      
      <!--=============== SWIPER JS ===============-->
      <script src="Adaftar/assets/js/swiper-bundle.min.js"></script>

      <!--=============== MAIN JS ===============-->
      <script src="Adaftar/assets/js/main.js"></script>
      <script>
        const params = new URLSearchParams(window.location.search);
        const popup = params.get('popup');
        const errorMsg = params.get('error');
        if (popup === 'email_terdaftar') {
          alert('Email sudah digunakan, silakan gunakan email lain.');
        }
        if (popup === 'server_error') {
          alert('Terjadi kesalahan server, silakan coba lagi.');
        }
        if (errorMsg) {
          alert(decodeURIComponent(errorMsg));
        }
      </script>
   </body>
</html>