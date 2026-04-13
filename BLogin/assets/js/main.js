document.addEventListener('DOMContentLoaded', () => {

  /* SHOW / HIDE PASSWORD */
  const ShowHiddenPass = (password, eye) => {
    const input = document.getElementById(password)
    const iconEye = document.getElementById(eye)

    if (!input || !iconEye) return

    iconEye.addEventListener('click', () => {
      input.type = input.type === 'password' ? 'text' : 'password'
      iconEye.classList.toggle('ri-eye-off-line')
      iconEye.classList.toggle('ri-eye-line')
    })
  }

  ShowHiddenPass('loginpass','logineye')

  /* SWIPER */
  if (document.querySelector('.login__swiper')) {
    new Swiper('.login__swiper', {
      loop: true,
      spaceBetween: 24,
      grabCursor: true,
      speed: 600,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      }
    })
  }

})