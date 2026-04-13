/*=============== HIDE & SHOW PASSWORD ===============*/
const ShowHiddenPass = (password, eye) => {
  const input = document.getElementById(password),
        iconEye = document.getElementById(eye)

  iconEye.addEventListener('click', () => {
    input.type = input.type === 'password' ? 'text' : 'password'
    iconEye.classList.toggle('ri-eye-off-line')
    iconEye.classList.toggle('ri-eye-line')
  })
}

ShowHiddenPass('loginpass','logineye')

/*=============== SWIPER IMAGES ===============*/
const swiperLogin = new Swiper('.login__swiper', {
    loop: true,
    spacebetwen: '24',
    grapcursor: true,
    speed: 600,

  pagination: {
    el: '.swiper-pagination',
    clickable: true,
  },
 autoplay:{
    delay: 3000,
    disableonintraction: false,
    }
});
