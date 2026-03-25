/**
 * TechBurk — Expert Informatique Burkina Faso
 * script.js — Animations, interactions, formulaire
 */

/* ============================================================
   1. LOADER
   ============================================================ */
window.addEventListener('load', () => {
  const loader = document.getElementById('loader');
  setTimeout(() => {
    loader.classList.add('hidden');
    // Lancer les animations hero après le loader
    document.querySelectorAll('.hero-title, .hero-sub, .hero-cta, .hero-stats')
      .forEach(el => el.style.animationPlayState = 'running');
  }, 1200);
});

/* ============================================================
   2. NAVBAR — scroll + menu burger
   ============================================================ */
const navbar  = document.getElementById('navbar');
const burger  = document.getElementById('burger');
const navLinks = document.getElementById('navLinks');

// Navbar scroll effect
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 50);
  updateActiveLink();
  toggleBackToTop();
});

// Burger menu
burger.addEventListener('click', () => {
  burger.classList.toggle('open');
  navLinks.classList.toggle('open');
  document.body.style.overflow = navLinks.classList.contains('open') ? 'hidden' : '';
});

// Fermer le menu en cliquant sur un lien
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', () => {
    burger.classList.remove('open');
    navLinks.classList.remove('open');
    document.body.style.overflow = '';
  });
});

// Fermer le menu au clic extérieur
document.addEventListener('click', (e) => {
  if (!navbar.contains(e.target) && navLinks.classList.contains('open')) {
    burger.classList.remove('open');
    navLinks.classList.remove('open');
    document.body.style.overflow = '';
  }
});

/* ============================================================
   3. ACTIVE NAV LINK selon la section visible
   ============================================================ */
function updateActiveLink() {
  const sections = document.querySelectorAll('section[id]');
  const scrollPos = window.scrollY + 100;

  sections.forEach(section => {
    const top    = section.offsetTop;
    const height = section.offsetHeight;
    const id     = section.getAttribute('id');
    const link   = document.querySelector(`.nav-link[href="#${id}"]`);

    if (link) {
      link.classList.toggle('active', scrollPos >= top && scrollPos < top + height);
    }
  });
}

/* ============================================================
   4. BACK TO TOP
   ============================================================ */
const backToTop = document.getElementById('backToTop');

function toggleBackToTop() {
  backToTop.classList.toggle('visible', window.scrollY > 400);
}

backToTop.addEventListener('click', () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

/* ============================================================
   5. SCROLL ANIMATIONS (AOS maison)
   ============================================================ */
const animatedElements = document.querySelectorAll('[data-aos]');

const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const el    = entry.target;
      const delay = parseInt(el.dataset.delay || 0);

      setTimeout(() => {
        el.classList.add('animated');
        // Animer les skill bars si présentes
        const fills = el.querySelectorAll('.skill-fill');
        fills.forEach(fill => {
          fill.style.width = fill.dataset.width + '%';
        });
      }, delay);

      observer.unobserve(el);
    }
  });
}, observerOptions);

animatedElements.forEach(el => observer.observe(el));

/* ============================================================
   6. COMPTEURS ANIMÉS (stats hero)
   ============================================================ */
function animateCounter(el, target, suffix) {
  let start = 0;
  const duration = 1800;
  const step = target / (duration / 16);

  const timer = setInterval(() => {
    start += step;
    if (start >= target) {
      el.textContent = target;
      clearInterval(timer);
    } else {
      el.textContent = Math.floor(start);
    }
  }, 16);
}

// Observer pour les stats
const statsSection = document.querySelectorAll('.stat');
const statsObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const statEl  = entry.target;
      const numEl   = statEl.querySelector('.stat-num');
      const target  = parseInt(statEl.dataset.count);
      animateCounter(numEl, target);
      statsObserver.unobserve(statEl);
    }
  });
}, { threshold: 0.5 });

statsSection.forEach(el => statsObserver.observe(el));

/* ============================================================
   7. SKILL BARS (section À propos)
   ============================================================ */
const skillSection = document.querySelector('.about');
if (skillSection) {
  const skillObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.querySelectorAll('.skill-fill').forEach(fill => {
          fill.style.width = fill.dataset.width + '%';
        });
        skillObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });
  skillObserver.observe(skillSection);
}

/* ============================================================
   8. FILTRES BOUTIQUE
   ============================================================ */
const filterBtns   = document.querySelectorAll('.filter-btn');
const productCards = document.querySelectorAll('.product-card');

filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    // Activer le bouton
    filterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const filter = btn.dataset.filter;

    productCards.forEach(card => {
      const cat = card.dataset.category;
      if (filter === 'all' || cat === filter) {
        card.classList.remove('hidden');
        // Micro-animation
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(() => {
          card.style.opacity = '1';
          card.style.transform = 'scale(1)';
          card.style.transition = 'opacity 0.3s, transform 0.3s';
        }, 50);
      } else {
        card.classList.add('hidden');
      }
    });
  });
});

/* ============================================================
   8. DÉTAILS PRODUITS — Isolation par produit
   ============================================================ */
document.querySelectorAll('.btn-details').forEach(button => {
  button.addEventListener('click', () => {
    // Trouver la carte produit parente
    const productCard = button.closest('.product-card');
    
    // Chercher UNIQUEMENT dans cette carte
    const details = productCard.querySelector('.product-details');

    if (details) {
      details.classList.toggle('open');

      // Mettre à jour le texte du bouton
      if (details.classList.contains('open')) {
        button.textContent = "Masquer détails";
      } else {
        button.textContent = "Voir détails";
      }
    }
  });
});

/* ============================================================
   9. FORMULAIRE DE CONTACT — envoi AJAX
   ============================================================ */
const contactForm = document.getElementById('contactForm');

if (contactForm) {
  // ─── Fonction pour créer un mailto: prérempli ───
  function createMailtoLink() {
    const nom = document.getElementById('nom').value || 'Non renseigné';
    const telephone = document.getElementById('telephone').value || 'Non renseigné';
    const email = document.getElementById('email').value || 'Non renseigné';
    const service = document.getElementById('service').value || 'Non renseigné';
    const message = document.getElementById('message').value || 'Non renseigné';
    
    const emailBody = `
NOUVEAU MESSAGE DE CONTACT — TECHBURK
=====================================

Nom          : ${nom}
Téléphone    : ${telephone}
Email        : ${email}
Service      : ${service}
Message      : 
${message}

=====================================
Envoyé depuis le formulaire de contact
    `.trim();
    
    const mailtoLink = `mailto:lamiendonaldo179@gmail.com?subject=[TechBurk] Nouveau message de contact de ${encodeURIComponent(nom)}&body=${encodeURIComponent(emailBody)}`;
    return mailtoLink;
  }

  // ─── Bouton fallback "Envoyer par email" ───
  const emailFallbackBtn = document.getElementById('emailFallbackBtn');
  if (emailFallbackBtn) {
    emailFallbackBtn.addEventListener('click', (e) => {
      e.preventDefault();
      
      // Vérifier que les champs obligatoires sont remplis
      if (!document.getElementById('nom').value) {
        alert('Veuillez remplir votre nom');
        return;
      }
      if (!document.getElementById('telephone').value) {
        alert('Veuillez remplir votre téléphone');
        return;
      }
      if (!document.getElementById('service').value) {
        alert('Veuillez choisir un service');
        return;
      }
      if (!document.getElementById('message').value) {
        alert('Veuillez remplir votre message');
        return;
      }
      
      // Ouvrir le client mail avec le formulaire prérempli
      window.location.href = createMailtoLink();
    });
  }

  // ─── Soumission du formulaire ───
  contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn     = document.getElementById('submitBtn');
    const btnText       = submitBtn.querySelector('.btn-text');
    const btnLoading    = submitBtn.querySelector('.btn-loading');
    const successAlert  = document.getElementById('form-success');
    const errorAlert    = document.getElementById('form-error');

    // Afficher le loading
    btnText.style.display    = 'none';
    btnLoading.style.display = 'inline-flex';
    submitBtn.disabled       = true;
    successAlert.style.display = 'none';
    errorAlert.style.display   = 'none';

    try {
      const formData = new FormData(contactForm);
      const response = await fetch(contactForm.action, {
        method: 'POST',
        body: formData
      });

      // Lire la réponse brute
      const responseText = await response.text();
      console.log('Status:', response.status, 'Response:', responseText);

      // Essayer de parser JSON
      let result = {};
      try {
        result = JSON.parse(responseText);
      } catch (e) {
        console.error('Erreur JSON:', e);
        throw new Error('Réponse non-JSON du serveur');
      }

      // Vérifier le succès
      if (result.success === true) {
        successAlert.style.display = 'flex';
        contactForm.reset();
        // Scroll vers le message
        successAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      } else {
        errorAlert.style.display = 'flex';
        console.error('Erreur du serveur:', result.message || result);
      }

    } catch (err) {
      console.error('Erreur fetch:', err.message);
      errorAlert.style.display = 'flex';
    } finally {
      btnText.style.display    = 'inline-flex';
      btnLoading.style.display = 'none';
      submitBtn.disabled       = false;
    }
  });
}

/* ============================================================
   10. SMOOTH SCROLL pour tous les liens ancre
   ============================================================ */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', (e) => {
    const target = document.querySelector(anchor.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth' });
    }
  });
});

/* ============================================================
   11. ANNÉE COURANTE dans le footer
   ============================================================ */
const yearEl = document.getElementById('currentYear');
if (yearEl) yearEl.textContent = new Date().getFullYear();

/* ============================================================
   12. WHATSAPP FLOAT — apparaît après 3s
   ============================================================ */
const waFloat = document.getElementById('waFloat');
if (waFloat) {
  waFloat.style.opacity = '0';
  waFloat.style.transform = 'scale(0.5)';
  waFloat.style.transition = 'opacity 0.4s, transform 0.4s';
  setTimeout(() => {
    waFloat.style.opacity = '1';
    waFloat.style.transform = 'scale(1)';
  }, 3000);
}

/* ============================================================
   13. PARALLAX léger sur le hero
   ============================================================ */
window.addEventListener('scroll', () => {
  const scrolled = window.scrollY;
  const heroContent = document.querySelector('.hero-content');
  const glows = document.querySelectorAll('.glow');

  if (heroContent && scrolled < window.innerHeight) {
    heroContent.style.transform = `translateY(${scrolled * 0.15}px)`;
  }

  glows.forEach((glow, i) => {
    const factor = i === 0 ? 0.08 : 0.12;
    glow.style.transform = `translateY(${scrolled * factor}px)`;
  });
}, { passive: true });

/* ============================================================
   14. EFFET TYPING dans le hero (optionnel)
   ============================================================ */
(function typingEffect() {
  const tag = document.querySelector('.hero-badge');
  if (!tag) return;

  const messages = [
    '🟢 Disponible · Ouagadougou & environs',
    '💻 Réparation rapide · 7j/7',
    '🔧 Diagnostic gratuit en ligne'
  ];
  let i = 0;

  setInterval(() => {
    i = (i + 1) % messages.length;
    tag.style.opacity = '0';
    setTimeout(() => {
      // Préserver le point vert
      const dot = tag.querySelector('.pulse-dot');
      tag.innerHTML = '';
      if (dot) tag.appendChild(dot.cloneNode(true));
      tag.appendChild(document.createTextNode(' ' + messages[i].replace(/^[^\s]+\s/, '')));
      tag.style.opacity = '1';
      tag.style.transition = 'opacity 0.4s';
    }, 300);
  }, 4000);
})();

console.log('%c🚀 TechBurk — Expert Informatique Burkina Faso', 'color:#1a7fff; font-size:14px; font-weight:bold;');
console.log('%cSite développé avec ❤️ pour les clients Burkinabè', 'color:#8494a8; font-size:12px;');
