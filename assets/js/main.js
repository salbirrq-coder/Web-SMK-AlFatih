/* ============================================================
   SMK Tahfizh Al-Fatih - Main JavaScript (with Animations)
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
  initNavbar();
  initReveal();
  initBackToTop();
  initFileUploads();
  initPageReady();
  initRipple();
  initCountUp();
  initHeroParallax();
  initHeroParticles();
  initTiltCards();
  initSmoothNavLinks();
});

/* ---------- Navbar ---------- */
function initNavbar() {
  var navbar = document.getElementById('navbar');
  var hamburger = document.getElementById('hamburger');
  var navMenu = document.getElementById('navMenu');
  var overlay = document.getElementById('mobileOverlay');

  function onScroll() {
    if (navbar) {
      if (window.scrollY > 30) navbar.classList.add('scrolled');
      else navbar.classList.remove('scrolled');
    }
  }
  window.addEventListener('scroll', onScroll);
  onScroll();

  function toggleMenu(open) {
    if (!navMenu) return;
    if (open) {
      navMenu.classList.add('active');
      hamburger && hamburger.classList.add('active');
      overlay && overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    } else {
      navMenu.classList.remove('active');
      hamburger && hamburger.classList.remove('active');
      overlay && overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  hamburger && hamburger.addEventListener('click', function () {
    toggleMenu(!navMenu.classList.contains('active'));
  });
  overlay && overlay.addEventListener('click', function () { toggleMenu(false); });

  if (navMenu) {
    navMenu.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { toggleMenu(false); });
    });
  }
}

/* ---------- Smooth scroll for nav links ---------- */
function initSmoothNavLinks() {
  document.querySelectorAll('a[href*="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var href = a.getAttribute('href');
      var hashIdx = href.indexOf('#');
      if (hashIdx === -1) return;
      var hash = href.substring(hashIdx);
      if (hash.length < 2) return;
      var target = document.querySelector(hash);
      if (target) {
        e.preventDefault();
        var offset = 80;
        var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top: top, behavior: 'smooth' });
      }
    });
  });
}

/* ---------- Scroll reveal (enhanced) ---------- */
function initReveal() {
  var els = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-rotate, .reveal-blur');
  if (!els.length) return;

  if (!('IntersectionObserver' in window)) {
    els.forEach(function (el) { el.classList.add('active'); });
    return;
  }

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('active');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

  els.forEach(function (el) { observer.observe(el); });
}

/* ---------- Back to top ---------- */
function initBackToTop() {
  var btn = document.getElementById('backToTop');
  if (!btn) return;
  window.addEventListener('scroll', function () {
    if (window.scrollY > 500) btn.classList.add('show');
    else btn.classList.remove('show');
  });
  btn.addEventListener('click', function (e) {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

/* ---------- File upload preview ---------- */
function initFileUploads() {
  document.querySelectorAll('.upload-box').forEach(function (box) {
    var input = box.querySelector('input[type=file]');
    if (!input) return;
    input.addEventListener('change', function () {
      var textEl = box.querySelector('.upload-text');
      if (textEl) {
        textEl.textContent = input.files && input.files[0] ? input.files[0].name : textEl.dataset.default || 'Pilih file';
      }
    });
  });
}

/* ---------- Toast ---------- */
function showToast(type, message) {
  var container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }
  var icons = { success: '\u2705', error: '\u274C', info: '\u2139\uFE0F' };
  var toast = document.createElement('div');
  toast.className = 'toast ' + (type || 'info');
  toast.innerHTML = '<span>' + (icons[type] || icons.info) + '</span> ' + message;
  container.appendChild(toast);
  setTimeout(function () {
    toast.classList.add('hide');
    setTimeout(function () { toast.remove(); }, 400);
  }, 3200);
}

/* ---------- Confirm modal helper ---------- */
function openConfirmModal(options) {
  var overlay = document.createElement('div');
  overlay.className = 'modal-overlay active';
  overlay.innerHTML =
    '<div class="modal">' +
      '<div class="modal-body" style="text-align:center;padding:36px;">' +
        '<div style="width:70px;height:70px;margin:0 auto 18px;border-radius:50%;background:' + (options.color || '#fee2e2') + ';display:flex;align-items:center;justify-content:center;font-size:32px;color:' + (options.iconColor || '#dc2626') + '">' + (options.icon || '!') + '</div>' +
        '<h3 style="font-size:20px;color:#052e1f;margin-bottom:10px;">' + options.title + '</h3>' +
        '<p style="color:#4b5d55;font-size:14px;margin-bottom:24px;">' + options.message + '</p>' +
        '<div style="display:flex;gap:12px;">' +
          '<button class="btn btn-green" id="confirmYes" style="flex:1">' + (options.confirmText || 'YA') + '</button>' +
          '<button class="btn btn-ghost" id="confirmNo" style="flex:1;background:var(--forest-50);color:var(--forest-600);border:1px solid var(--border)">' + (options.cancelText || 'BATAL') + '</button>' +
        '</div>' +
      '</div>' +
    '</div>';
  document.body.appendChild(overlay);
  return new Promise(function (resolve) {
    overlay.querySelector('#confirmYes').addEventListener('click', function () { overlay.remove(); resolve(true); });
    overlay.querySelector('#confirmNo').addEventListener('click', function () { overlay.remove(); resolve(false); });
  });
}

/* ---------- Button ripple effect ---------- */
function initRipple() {
  document.querySelectorAll('.btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      var rect = btn.getBoundingClientRect();
      var x = e.clientX - rect.left;
      var y = e.clientY - rect.top;
      var circle = document.createElement('span');
      circle.className = 'ripple-circle';
      var size = Math.max(rect.width, rect.height);
      circle.style.width = circle.style.height = size + 'px';
      circle.style.left = (x - size / 2) + 'px';
      circle.style.top = (y - size / 2) + 'px';
      btn.appendChild(circle);
      setTimeout(function () { circle.remove(); }, 600);
    });
  });
}

/* ---------- Count up numbers ---------- */
function initCountUp() {
  var els = document.querySelectorAll('.count-up');
  if (!els.length) return;

  function animateCount(el) {
    var target = parseInt(el.getAttribute('data-target') || el.textContent.replace(/[^0-9]/g, ''), 10);
    var suffix = el.getAttribute('data-suffix') || '';
    var prefix = el.getAttribute('data-prefix') || '';
    var duration = 1800;
    var start = 0;
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var current = Math.floor(eased * target);
      el.textContent = prefix + current.toLocaleString('id-ID') + suffix;
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = prefix + target.toLocaleString('id-ID') + suffix;
    }
    requestAnimationFrame(step);
  }

  if (!('IntersectionObserver' in window)) {
    els.forEach(function (el) { animateCount(el); });
    return;
  }

  var obs = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        animateCount(entry.target);
        entry.target.classList.add('revealed');
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.4 });

  els.forEach(function (el) { obs.observe(el); });
}

/* ---------- Hero mouse parallax ---------- */
function initHeroParallax() {
  var hero = document.querySelector('.hero');
  if (!hero) return;

  var shapes = hero.querySelectorAll('.hero-shape, .hero-geo, .hero-float-shape');
  var content = hero.querySelector('.hero-content');

  hero.addEventListener('mousemove', function (e) {
    var rect = hero.getBoundingClientRect();
    var x = (e.clientX - rect.left) / rect.width - 0.5;
    var y = (e.clientY - rect.top) / rect.height - 0.5;

    shapes.forEach(function (shape, i) {
      var depth = (i % 3 + 1) * 12;
      var moveX = x * depth;
      var moveY = y * depth;
      shape.style.transform = 'translate(' + moveX + 'px, ' + moveY + 'px)';
    });

    if (content) {
      content.style.transform = 'translate(' + (x * -4) + 'px, ' + (y * -4) + 'px)';
    }
  });

  hero.addEventListener('mouseleave', function () {
    shapes.forEach(function (shape) {
      shape.style.transform = 'translate(0, 0)';
      shape.style.transition = 'transform 0.6s ease';
      setTimeout(function () { shape.style.transition = ''; }, 600);
    });
    if (content) {
      content.style.transform = 'translate(0, 0)';
      content.style.transition = 'transform 0.6s ease';
      setTimeout(function () { content.style.transition = ''; }, 600);
    }
  });
}

/* ---------- Hero particle canvas ---------- */
function initHeroParticles() {
  var canvas = document.getElementById('heroCanvas');
  if (!canvas) return;

  var ctx = canvas.getContext('2d');
  var particles = [];
  var particleCount = 60;
  var mouse = { x: null, y: null };

  function resize() {
    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;
  }
  resize();
  window.addEventListener('resize', resize);

  canvas.parentElement.addEventListener('mousemove', function (e) {
    var rect = canvas.getBoundingClientRect();
    mouse.x = e.clientX - rect.left;
    mouse.y = e.clientY - rect.top;
  });
  canvas.parentElement.addEventListener('mouseleave', function () {
    mouse.x = null;
    mouse.y = null;
  });

  function Particle() {
    this.x = Math.random() * canvas.width;
    this.y = Math.random() * canvas.height;
    this.size = Math.random() * 2.5 + 0.5;
    this.speedX = (Math.random() - 0.5) * 0.6;
    this.speedY = (Math.random() - 0.5) * 0.6;
    this.opacity = Math.random() * 0.5 + 0.1;
    this.color = Math.random() > 0.5 ? '212,175,55' : '16,185,129';
  }

  Particle.prototype.update = function () {
    this.x += this.speedX;
    this.y += this.speedY;

    if (mouse.x !== null) {
      var dx = mouse.x - this.x;
      var dy = mouse.y - this.y;
      var dist = Math.sqrt(dx * dx + dy * dy);
      if (dist < 120) {
        var force = (120 - dist) / 120;
        this.x -= dx * force * 0.02;
        this.y -= dy * force * 0.02;
      }
    }

    if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
    if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
  };

  Particle.prototype.draw = function () {
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(' + this.color + ',' + this.opacity + ')';
    ctx.fill();
  };

  for (var i = 0; i < particleCount; i++) {
    particles.push(new Particle());
  }

  function connectParticles() {
    for (var a = 0; a < particles.length; a++) {
      for (var b = a + 1; b < particles.length; b++) {
        var dx = particles[a].x - particles[b].x;
        var dy = particles[a].y - particles[b].y;
        var dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 140) {
          var opacity = (1 - dist / 140) * 0.15;
          ctx.beginPath();
          ctx.moveTo(particles[a].x, particles[a].y);
          ctx.lineTo(particles[b].x, particles[b].y);
          ctx.strokeStyle = 'rgba(212,175,55,' + opacity + ')';
          ctx.lineWidth = 0.6;
          ctx.stroke();
        }
      }
    }
  }

  function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    particles.forEach(function (p) {
      p.update();
      p.draw();
    });
    connectParticles();
    requestAnimationFrame(animate);
  }
  animate();
}

/* ---------- 3D Tilt on cards ---------- */
function initTiltCards() {
  if (window.matchMedia('(pointer: coarse)').matches) return;

  document.querySelectorAll('.tilt-card').forEach(function (card) {
    card.addEventListener('mousemove', function (e) {
      var rect = card.getBoundingClientRect();
      var x = (e.clientX - rect.left) / rect.width;
      var y = (e.clientY - rect.top) / rect.height;
      var rotateX = (0.5 - y) * 8;
      var rotateY = (x - 0.5) * 8;
      card.style.transform = 'perspective(800px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-6px)';
    });
    card.addEventListener('mouseleave', function () {
      card.style.transform = 'perspective(800px) rotateX(0) rotateY(0) translateY(0)';
      card.style.transition = 'transform 0.5s cubic-bezier(0.22, 1, 0.36, 1)';
      setTimeout(function () { card.style.transition = ''; }, 500);
    });
  });
}

/* ---------- Page ready ---------- */
function initPageReady() {
  document.addEventListener('click', function (e) {
    if (e.target.classList && e.target.classList.contains('modal-overlay')) {
      e.target.remove();
    }
  });

  var bars = document.querySelectorAll('.bar-fill');
  if (bars.length && 'IntersectionObserver' in window) {
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.style.width = entry.target.dataset.width || entry.target.style.width;
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });
    bars.forEach(function (b) {
      b.dataset.width = b.style.width || '0%';
      b.style.width = '0%';
      obs.observe(b);
    });
  }
}
