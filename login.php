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
   <!-- BUG FIX #1: "Blogin" diganti "BLogin" (huruf kapital L) 
        Linux case-sensitive! Blogin != BLogin => CSS tidak ketemu => layout hancur -->
   <link rel="stylesheet" href="BLogin/assets/css/swiper-bundle.min.css">

   <!--=============== CSS ===============-->
   <link rel="stylesheet" href="BLogin/assets/css/styles.css">

   <title>Login - PC Nexus</title>
</head>
<!-- BUG FIX #2: hapus class "login-page" dari body (tidak perlu, tidak didefinisikan) -->
<body>

   <!-- BUG FIX #3: hapus class "container" dan "grid" dari div.login
        Kedua class itu tidak ada di CSS custom dan bisa bentrok -->
   <div class="login">
      <div class="login__container">

         <!-- ===== SWIPER SIDE (kiri, muncul hanya di desktop) ===== -->
         <div class="login__swiper swiper">
            <div class="login__swiper-data">
               <p class="login__swiper-subtitle">Welcome back</p>
               <h1 class="login__swiper-title">Hello Users <br>Sign in to get started</h1>
            </div>

            <div class="login__swiper-social">
               <p class="login__swiper-subtitle">Our social media</p>
               <div class="login__swiper-links">
                  <a href="https://www.instagram.com/ra_34170/" target="_blank" class="login__swiper-link">
                     <i class="ri-instagram-line"></i>
                  </a>
                  <a href="#" target="_blank" class="login__swiper-link">
                     <i class="ri-twitter-x-line"></i>
                  </a>
                  <a href="#" target="_blank" class="login__swiper-link">
                     <i class="ri-facebook-circle-line"></i>
                  </a>
                  <a href="#" target="_blank" class="login__swiper-link">
                     <i class="ri-youtube-line"></i>
                  </a>
               </div>
            </div>

            <div class="swiper-wrapper">
               <div class="swiper-slide">
                  <img src="BLogin/assets/img/img-1.png" alt="slide 1" class="login__swiper-img">
               </div>
               <div class="swiper-slide">
                  <img src="BLogin/assets/img/img-2.png" alt="slide 2" class="login__swiper-img">
               </div>
               <div class="swiper-slide">
                  <img src="BLogin/assets/img/img-3.png" alt="slide 3" class="login__swiper-img">
               </div>
            </div>
            <div class="swiper-pagination"></div>
         </div>

         <!-- ===== FORM SIDE (kanan) ===== -->
         <!-- BUG FIX #4: hapus class "grid" dari login__area
              class "grid" tidak didefinisikan di CSS, bikin layout aneh -->
         <div class="login__area">

            <!-- Header & Google button -->
            <div class="login__data">
               <h1 class="login__title">Welcome To Pc Nexus</h1>
               <p class="login__description">Please enter your details</p>
               <button class="login__button-border">
                  <i class="ri-google-fill"></i> Sign in with Google
               </button>
            </div>

            <span class="login__line">or</span>

            <!-- BUG FIX #5: form struktur diperbaiki
                 login__forgot dan login__button dipindah ke DALAM login__content
                 sebelumnya mereka ada di luar, bikin spacing berantakan -->
            <form action="fungsi/masuk.php" method="POST" class="login__form">

               <div class="login__content">

                  <?php if(isset($_GET['gagal'])){ ?>
                     <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['gagal']); ?></div>
                  <?php } ?>

                  <?php if(isset($_GET['sukses'])){ ?>
                     <div class="alert alert-success"><?php echo htmlspecialchars($_GET['sukses']); ?></div>
                  <?php } ?>

                  <div class="login__box">
                     <input type="email" name="email" placeholder="Email" required class="login__input">
                     <i class="ri-mail-line"></i>
                  </div>

                  <div class="login__box">
                     <input type="password" name="password" placeholder="Password" required class="login__input" id="loginpass">
                     <i class="ri-eye-fill login__eye" id="logineye"></i>
                  </div>

               </div><!-- end login__content -->

               <a href="#" class="login__forgot">Forgot password?</a>

               <button type="submit" class="login__button">Log in</button>

            </form>

            <p class="login__switch">
               Don't have an account?
               <a href="daftar.php" class="login__sign">Sign up</a>
            </p>

         </div><!-- end login__area -->

      </div><!-- end login__container -->
   </div><!-- end login -->

   <!--=============== SWIPER JS ===============-->
   <!-- BUG FIX #6: path JS juga difix dari "Blogin" ke "BLogin" -->
   <script src="BLogin/assets/js/swiper-bundle.min.js"></script>

   <!--=============== MAIN JS ===============-->
   <script src="BLogin/assets/js/main.js"></script>

   <script>
      const popup = new URLSearchParams(window.location.search).get('popup');

      if (popup === 'belum_daftar') {
         alert('Akun belum terdaftar, silakan daftar terlebih dahulu');
         window.location.href = 'daftar.php';
      }

      if (popup === 'password_salah') {
         alert('Password salah, silakan coba lagi');
      }
   </script>

</body>
</html>




