// Sélection des éléments
const slides = Array.from(document.querySelectorAll('.slide'));
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');
const dotsWrap = document.querySelector('.dots');

let index = 0;
let timer = null;
const DURATION = 5000;

// Crée les puces (dots)
slides.forEach((_, i) => {
  const dot = document.createElement('button');
  dot.className = 'dot' + (i === 0 ? ' is-active' : '');
  dot.setAttribute('aria-label', 'Aller à la diapositive ' + (i + 1));
  dot.addEventListener('click', () => { goTo(i); startAuto(); });
  dotsWrap.appendChild(dot);
});

function goTo(i){
  slides[index].classList.remove('is-active');
  dotsWrap.children[index]?.classList.remove('is-active');

  index = (i + slides.length) % slides.length;

  slides[index].classList.add('is-active');
  dotsWrap.children[index]?.classList.add('is-active');
}
function next(){ goTo(index + 1); }
function prev(){ goTo(index - 1); }

function startAuto(){
  stopAuto();
  timer = setInterval(next, DURATION);
}
function stopAuto(){
  if (timer){ clearInterval(timer); timer = null; }
}

// Contrôles
nextBtn.addEventListener('click', ()=>{ next(); startAuto(); });
prevBtn.addEventListener('click', ()=>{ prev(); startAuto(); });

// Pause au survol
const carousel = document.querySelector('.carousel');
carousel.addEventListener('mouseenter', stopAuto);
carousel.addEventListener('mouseleave', startAuto);

// Démarrage
startAuto();
