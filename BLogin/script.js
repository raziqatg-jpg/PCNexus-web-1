window.addEventListener("load", () => {
  const text = document.querySelector(".hero-text");
  const image = document.querySelector(".hero-image img");

  setTimeout(() => {
    text.style.transition = "0.8s";
    text.style.opacity = 1;
    text.style.transform = "translateX(0)";
  }, 300);

  setTimeout(() => {
    image.style.transition = "0.8s";
    image.style.opacity = 1;
    image.style.transform = "translateX(0)";
  }, 600);
});
