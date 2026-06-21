/* ==========================================
   CONDOMÍNIO THREADS — INTERACTIVITY
   ========================================== */

// ========== NEIGHBORHOODS DATA ==========
const BAIRROS = [
  {
    id: 'premium',
    name: 'Bairro Premium',
    emoji: '🏆',
    tag: 'Elite',
    streets: ['Av. dos Verificados', 'Rua do Engajamento', 'Alameda dos Likes'],
    scoreRange: [85, 99],
  },
  {
    id: 'influenciadores',
    name: 'Torre dos Influenciadores',
    emoji: '🏢',
    tag: 'Top Creators',
    streets: ['Cobertura Viral', 'Penthouse do Alcance', 'Terraço da Fama'],
    scoreRange: [78, 95],
  },
  {
    id: 'experts',
    name: 'Alameda dos Experts',
    emoji: '💼',
    tag: 'Especialistas',
    streets: ['Rua do Conhecimento', 'Av. da Autoridade', 'Travessa do Nicho'],
    scoreRange: [65, 88],
  },
  {
    id: 'fitness',
    name: 'Vila do Fitness',
    emoji: '💪',
    tag: 'Saúde',
    streets: ['Rua da Disciplina', 'Av. do Shape', 'Travessa do Treino'],
    scoreRange: [60, 85],
  },
  {
    id: 'resenha',
    name: 'Vila da Resenha',
    emoji: '😂',
    tag: 'Humor',
    streets: ['Beco da Zoeira', 'Rua do Meme', 'Av. da Risada'],
    scoreRange: [55, 82],
  },
  {
    id: 'conversa',
    name: 'Praça da Conversa',
    emoji: '💬',
    tag: 'Debates',
    streets: ['Praça Central', 'Rua do Debate', 'Av. da Opinião'],
    scoreRange: [50, 78],
  },
  {
    id: 'iniciantes',
    name: 'Pracinha dos Iniciantes',
    emoji: '🌱',
    tag: 'Novatos',
    streets: ['Rua Bem-Vindo', 'Travessa do Começo', 'Av. da Esperança'],
    scoreRange: [30, 55],
  },
  {
    id: 'silencio',
    name: 'Subúrbio do Silêncio',
    emoji: '🔇',
    tag: 'Observadores',
    streets: ['Rua do Silêncio', 'Beco da Observação', 'Travessa do Lurker'],
    scoreRange: [20, 48],
  },
  {
    id: 'treta',
    name: 'Delegacia da Treta',
    emoji: '🚨',
    tag: 'Polêmicos',
    streets: ['Rua da Confusão', 'Av. do Cancelamento', 'Beco da Polêmica'],
    scoreRange: [40, 70],
  },
  {
    id: 'esquecido',
    name: 'Bairro Esquecido',
    emoji: '👻',
    tag: 'Inativos',
    streets: ['Rua Abandonada', 'Travessa do Sumiço', 'Beco da Saudade'],
    scoreRange: [5, 25],
  },
];

const ANALYSIS_STEPS = [
  { text: 'Conectando ao Threads...', duration: 800 },
  { text: 'Analisando seguidores...', duration: 700 },
  { text: 'Calculando engajamento...', duration: 900 },
  { text: 'Mapeando tipo de conteúdo...', duration: 700 },
  { text: 'Avaliando frequência de posts...', duration: 600 },
  { text: 'Identificando seu bairro...', duration: 1000 },
  { text: 'Preparando sua casa...', duration: 500 },
];

// ========== UTILITY FUNCTIONS ==========

/**
 * Simple hash function for deterministic results from username
 */
function hashUsername(username) {
  let hash = 0;
  const str = username.toLowerCase().trim();
  for (let i = 0; i < str.length; i++) {
    const char = str.charCodeAt(i);
    hash = ((hash << 5) - hash) + char;
    hash = hash & hash; // Convert to 32-bit integer
  }
  return Math.abs(hash);
}

/**
 * Generate a seeded random number between min and max
 */
function seededRandom(seed, min, max) {
  const x = Math.sin(seed) * 10000;
  const rand = x - Math.floor(x);
  return Math.floor(rand * (max - min + 1)) + min;
}

/**
 * Format large numbers with K/M suffix
 */
function formatNumber(num) {
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
  return num.toString();
}

/**
 * Animate counter from 0 to target
 */
function animateCounter(element, target, duration = 1500) {
  const isFormatted = typeof target === 'string';
  const numericTarget = isFormatted ? parseInt(target) : target;
  const suffix = isFormatted ? target.replace(/[\d.]/g, '') : '';
  const start = performance.now();

  function update(currentTime) {
    const elapsed = currentTime - start;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic

    const current = Math.floor(eased * numericTarget);
    element.textContent = suffix ? formatNumber(current) : current;

    if (progress < 1) {
      requestAnimationFrame(update);
    } else {
      element.textContent = isFormatted ? target : numericTarget;
    }
  }

  requestAnimationFrame(update);
}

// ========== SCROLL ANIMATIONS ==========
const observerOptions = {
  root: null,
  rootMargin: '0px 0px -60px 0px',
  threshold: 0.1,
};

const scrollObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      const delay = entry.target.dataset.delay || 0;
      setTimeout(() => {
        entry.target.classList.add('visible');
      }, parseInt(delay));
      scrollObserver.unobserve(entry.target);
    }
  });
}, observerOptions);

document.addEventListener('DOMContentLoaded', () => {
  // Observe all animated elements
  document.querySelectorAll('.animate-on-scroll').forEach((el) => {
    scrollObserver.observe(el);
  });

  // Animate hero stats counter
  const statNumbers = document.querySelectorAll('.hero-stat-number[data-count]');
  statNumbers.forEach((el) => {
    const target = parseInt(el.dataset.count);
    setTimeout(() => {
      animateCounter(el, target, 2000);
    }, 800);
  });
});

// ========== HEADER SCROLL ==========
let lastScroll = 0;
window.addEventListener('scroll', () => {
  const header = document.getElementById('header');
  const scroll = window.scrollY;

  if (scroll > 50) {
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }

  lastScroll = scroll;
}, { passive: true });

// ========== SMOOTH SCROLL FOR ANCHOR LINKS ==========
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// ========== MODAL MANAGEMENT ==========
function openLoginModal() {
  const overlay = document.getElementById('modal-overlay');
  overlay.classList.add('active');
  document.body.style.overflow = 'hidden';

  // Reset to step 1
  showModalStep(1);

  // Focus input
  setTimeout(() => {
    const input = document.getElementById('username-input');
    if (input) input.focus();
  }, 300);
}

function closeModals(event) {
  if (event && event.target !== event.currentTarget) return;
  const overlay = document.getElementById('modal-overlay');
  overlay.classList.remove('active');
  document.body.style.overflow = '';

  // Reset state
  setTimeout(() => {
    showModalStep(1);
    resetAnalysis();
  }, 300);
}

function showModalStep(step) {
  document.querySelectorAll('.modal-step').forEach((s) => s.classList.remove('active'));
  const target = document.getElementById(`login-step-${step}`);
  if (target) target.classList.add('active');
}

function resetAnalysis() {
  const progressBar = document.getElementById('analysis-progress-bar');
  const stepsContainer = document.getElementById('analysis-steps');
  if (progressBar) progressBar.style.width = '0%';
  if (stepsContainer) stepsContainer.innerHTML = '';
}

// Handle Enter key in input
document.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    const input = document.getElementById('username-input');
    if (document.activeElement === input) {
      startAnalysis();
    }
  }
  if (e.key === 'Escape') {
    closeModals();
  }
});

// ========== ANALYSIS FLOW ==========
async function startAnalysis() {
  const input = document.getElementById('username-input');
  const username = input.value.trim().replace('@', '');

  if (!username) {
    input.style.borderColor = 'var(--accent-red)';
    input.parentElement.style.borderColor = 'var(--accent-red)';
    setTimeout(() => {
      input.parentElement.style.borderColor = '';
    }, 2000);
    return;
  }

  // Move to step 2
  showModalStep(2);

  // Set avatar letter
  document.getElementById('analysis-avatar-letter').textContent =
    username.charAt(0).toUpperCase();

  // Run analysis animation
  const stepsContainer = document.getElementById('analysis-steps');
  stepsContainer.innerHTML = '';
  const progressBar = document.getElementById('analysis-progress-bar');

  // Create step items
  ANALYSIS_STEPS.forEach((step) => {
    const item = document.createElement('div');
    item.className = 'analysis-step-item';
    item.innerHTML = `
      <span class="step-status">⏳</span>
      <span>${step.text}</span>
    `;
    stepsContainer.appendChild(item);
  });

  const items = stepsContainer.querySelectorAll('.analysis-step-item');
  let totalDuration = ANALYSIS_STEPS.reduce((sum, s) => sum + s.duration, 0);
  let elapsed = 0;

  for (let i = 0; i < ANALYSIS_STEPS.length; i++) {
    const step = ANALYSIS_STEPS[i];
    const item = items[i];

    // Activate current step
    item.classList.add('active');
    item.querySelector('.step-status').textContent = '⏳';

    // Update title
    document.getElementById('analysis-title').textContent = step.text;

    await sleep(step.duration);

    // Mark as done
    item.classList.remove('active');
    item.classList.add('done');
    item.querySelector('.step-status').textContent = '✓';

    // Update progress
    elapsed += step.duration;
    progressBar.style.width = `${(elapsed / totalDuration) * 100}%`;
  }

  // Small pause before result
  await sleep(400);

  // Generate and show result
  const result = generateResult(username);
  showResult(result);
}

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

// ========== RESULT GENERATION ==========
function generateResult(username) {
  const hash = hashUsername(username);

  // Select neighborhood based on hash
  const bairroIndex = hash % BAIRROS.length;
  const bairro = BAIRROS[bairroIndex];

  // Generate metrics
  const score = seededRandom(hash, bairro.scoreRange[0], bairro.scoreRange[1]);
  const followers = seededRandom(hash * 2, 100, 500000);
  const following = seededRandom(hash * 3, 50, Math.min(followers * 2, 10000));
  const postsPerWeek = seededRandom(hash * 4, 0, 35);
  const engagementRate = (seededRandom(hash * 5, 10, 150) / 10).toFixed(1);

  // Generate address
  const streetIndex = hash % bairro.streets.length;
  const houseNumber = seededRandom(hash * 6, 1, 999);

  return {
    bairro,
    score,
    address: `${bairro.streets[streetIndex]}, nº ${houseNumber}`,
    metrics: {
      followers: formatNumber(followers),
      following: formatNumber(following),
      postsPerWeek,
      engagementRate: engagementRate + '%',
    },
    username,
  };
}

function showResult(result) {
  // Populate result card
  document.getElementById('result-emoji').textContent = result.bairro.emoji;
  document.getElementById('result-bairro').textContent = result.bairro.name;
  document.getElementById('result-address').textContent = result.address;

  // Populate metrics grid
  const metricsContainer = document.getElementById('result-metrics');
  metricsContainer.innerHTML = `
    <div class="result-metric">
      <div class="result-metric-value">${result.metrics.followers}</div>
      <div class="result-metric-label">Seguidores</div>
    </div>
    <div class="result-metric">
      <div class="result-metric-value">${result.metrics.engagementRate}</div>
      <div class="result-metric-label">Engajamento</div>
    </div>
    <div class="result-metric">
      <div class="result-metric-value">${result.metrics.postsPerWeek}/sem</div>
      <div class="result-metric-label">Frequência</div>
    </div>
    <div class="result-metric">
      <div class="result-metric-value">${result.metrics.following}</div>
      <div class="result-metric-label">Seguindo</div>
    </div>
  `;

  // Show step 3
  showModalStep(3);

  // Animate score ring
  setTimeout(() => {
    animateScoreRing(result.score);
    animateCounter(document.getElementById('result-score-number'), result.score, 1200);
  }, 300);

  // Launch confetti
  setTimeout(() => {
    launchConfetti();
  }, 500);

  // Save result for sharing
  window.__lastResult = result;
}

function animateScoreRing(score) {
  const circle = document.getElementById('score-circle');
  const circumference = 2 * Math.PI * 54; // r=54
  const offset = circumference - (score / 100) * circumference;
  circle.style.transition = 'stroke-dashoffset 1.5s cubic-bezier(0.16, 1, 0.3, 1)';
  circle.style.strokeDashoffset = offset;
}

// ========== CONFETTI ==========
function launchConfetti() {
  const canvas = document.getElementById('confetti-canvas');
  const ctx = canvas.getContext('2d');

  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;

  const particles = [];
  const colors = ['#C4994C', '#E8C869', '#8B6914', '#4A7C1B', '#F5F0E8', '#FF9800', '#EF5350', '#26C6DA'];

  // Create particles
  for (let i = 0; i < 120; i++) {
    particles.push({
      x: Math.random() * canvas.width,
      y: -20 - Math.random() * 200,
      w: Math.random() * 10 + 4,
      h: Math.random() * 6 + 3,
      color: colors[Math.floor(Math.random() * colors.length)],
      speed: Math.random() * 3 + 2,
      rotation: Math.random() * 360,
      rotSpeed: (Math.random() - 0.5) * 8,
      oscillation: Math.random() * 2,
      oscillationSpeed: Math.random() * 0.02 + 0.01,
      phase: Math.random() * Math.PI * 2,
    });
  }

  let frame = 0;
  const maxFrames = 180;

  function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    particles.forEach((p) => {
      p.y += p.speed;
      p.rotation += p.rotSpeed;
      p.x += Math.sin(p.phase + frame * p.oscillationSpeed) * p.oscillation;

      ctx.save();
      ctx.translate(p.x, p.y);
      ctx.rotate((p.rotation * Math.PI) / 180);
      ctx.fillStyle = p.color;
      ctx.globalAlpha = Math.max(0, 1 - frame / maxFrames);
      ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
      ctx.restore();
    });

    frame++;
    if (frame < maxFrames) {
      requestAnimationFrame(animate);
    } else {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
  }

  requestAnimationFrame(animate);
}

// ========== PREMIUM CHECKOUT (MOCK) ==========
function openPremiumCheckout() {
  // For now, show an alert — in production, this redirects to Stripe/MercadoPago
  const result = window.__lastResult;
  let message = '🏠 Funcionalidade em breve!\n\n';
  message += 'O checkout para geração de imagem com IA será integrado em breve.\n';
  if (result) {
    message += `\nSeu bairro: ${result.bairro.name}\n`;
    message += `Endereço: ${result.address}\n`;
    message += `Score: ${result.score}`;
  }
  alert(message);
}

// ========== SHARE RESULT ==========
function shareResult() {
  const result = window.__lastResult;
  if (!result) return;

  const text = `🏘️ Minha casa no Condomínio Threads!\n\n${result.bairro.emoji} ${result.bairro.name}\n📍 ${result.address}\n⭐ Score: ${result.score}/100\n\nDescubra a sua em condominiothreads.imb.br`;

  if (navigator.share) {
    navigator.share({
      title: 'Condomínio Threads — Minha Casa',
      text: text,
    }).catch(() => {
      // Fallback to clipboard
      copyToClipboard(text);
    });
  } else {
    copyToClipboard(text);
  }
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    // Show a brief toast
    showToast('Resultado copiado! Cole nas redes sociais 📋');
  }).catch(() => {
    // Final fallback
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    showToast('Resultado copiado! Cole nas redes sociais 📋');
  });
}

function showToast(message) {
  // Create toast element
  const toast = document.createElement('div');
  toast.style.cssText = `
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: rgba(22, 16, 10, 0.95);
    border: 1px solid rgba(196, 153, 76, 0.3);
    color: #F5F0E8;
    padding: 14px 24px;
    border-radius: 100px;
    font-family: 'Inter', sans-serif;
    font-size: 0.88rem;
    font-weight: 500;
    z-index: 400;
    backdrop-filter: blur(10px);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  `;
  toast.textContent = message;
  document.body.appendChild(toast);

  // Animate in
  requestAnimationFrame(() => {
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
  });

  // Remove after 3s
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(-50%) translateY(10px)';
    setTimeout(() => toast.remove(), 400);
  }, 3000);
}

// ========== BAIRROS CAROUSEL DRAG ==========
(() => {
  const carousel = document.getElementById('bairros-carousel');
  if (!carousel) return;

  let isDown = false;
  let startX;
  let scrollLeft;

  carousel.addEventListener('mousedown', (e) => {
    isDown = true;
    carousel.style.cursor = 'grabbing';
    startX = e.pageX - carousel.offsetLeft;
    scrollLeft = carousel.scrollLeft;
  });

  carousel.addEventListener('mouseleave', () => {
    isDown = false;
    carousel.style.cursor = '';
  });

  carousel.addEventListener('mouseup', () => {
    isDown = false;
    carousel.style.cursor = '';
  });

  carousel.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - carousel.offsetLeft;
    const walk = (x - startX) * 1.5;
    carousel.scrollLeft = scrollLeft - walk;
  });
})();

// ========== PARALLAX ON HERO ORBS ==========
if (window.matchMedia('(min-width: 768px)').matches) {
  window.addEventListener('mousemove', (e) => {
    const orbs = document.querySelectorAll('.hero-orb');
    const x = (e.clientX / window.innerWidth - 0.5) * 2;
    const y = (e.clientY / window.innerHeight - 0.5) * 2;

    orbs.forEach((orb, i) => {
      const speed = (i + 1) * 8;
      orb.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
    });
  }, { passive: true });
}

// ========== PRELOAD CRITICAL RESOURCES ==========
window.addEventListener('load', () => {
  // Preload premium image
  const img = new Image();
  img.src = 'assets/premium-house.png';
});
