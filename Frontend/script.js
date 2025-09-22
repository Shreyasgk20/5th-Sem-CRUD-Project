// Active nav highlighting on load
document.querySelectorAll('.nav-link').forEach(link=>{
  const same = link.getAttribute('href') === location.pathname.split('/').pop();
  if(same) link.classList.add('active');
});

// Theme handling (light default)
const THEME_KEY = 'ovs_theme';
function applyTheme(theme){
  // Put theme class on <html> and mirror some backgrounds via CSS
  document.documentElement.classList.toggle('theme-dark', theme === 'dark');
  const btn = document.getElementById('themeToggle');
  if(btn){ btn.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode'; }
  // swap hero image if present
  const heroImg = document.getElementById('heroImage');
  if(heroImg){
    const src = theme === 'dark' ? heroImg.getAttribute('data-dark-src') : heroImg.getAttribute('data-light-src');
    if(src) heroImg.setAttribute('src', src);
  }
}
const stored = localStorage.getItem(THEME_KEY) || 'light';
applyTheme(stored);
const toggle = document.getElementById('themeToggle');
if(toggle){
  toggle.addEventListener('click', ()=>{
    const next = document.documentElement.classList.contains('theme-dark') ? 'light' : 'dark';
    localStorage.setItem(THEME_KEY, next);
    applyTheme(next);
  });
}

// Ripple effect for buttons and options
function attachRipple(el){
  el.addEventListener('click',function(ev){
    const rect = this.getBoundingClientRect();
    const ripple = document.createElement('span');
    ripple.className = 'ripple';
    const size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = (ev.clientX - rect.left - size/2) + 'px';
    ripple.style.top = (ev.clientY - rect.top - size/2) + 'px';
    this.appendChild(ripple);
    setTimeout(()=>ripple.remove(), 650);
  });
}
document.querySelectorAll('[data-ripple]').forEach(attachRipple);

// Simple intersection reveal for hero text
const reveals = document.querySelectorAll('.gradient-title, .subtitle, .cta-button, .card-illustration');
const io = new IntersectionObserver((entries)=>{
  entries.forEach(e=>{
    if(e.isIntersecting){
      e.target.style.transition = 'transform .6s ease, opacity .6s ease';
      e.target.style.transform = 'translateY(0)';
      e.target.style.opacity = '1';
    }
  })
},{threshold:.1});
reveals.forEach(el=>{el.style.transform='translateY(12px)';el.style.opacity='0';io.observe(el)});

// Login form demo validation
const loginForm = document.getElementById('loginForm');
if(loginForm){
  const submitBtn = document.getElementById('loginSubmit');
  function handle(e){
    e.preventDefault();
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    if(!email.checkValidity() || !password.checkValidity()){
      alert('Please enter a valid email and a 6+ character password.');
      return;
    }
    localStorage.setItem('ovs_demo_user', email.value);
    location.href = 'poll.html';
  }
  loginForm.addEventListener('submit', handle);
  if(submitBtn){ submitBtn.addEventListener('click', handle); }
}

// Poll page interactions (demo only, local state)
const optionsEl = document.getElementById('options');
if(optionsEl){
  const percentEls = [...optionsEl.querySelectorAll('.option-percent')];
  const barEls = [...optionsEl.querySelectorAll('.option-bar span')];
  const hint = document.getElementById('voteHint');
  let counts = JSON.parse(localStorage.getItem('ovs_demo_counts')||'{}');

  function updateUI(){
    const total = Object.values(counts).reduce((a,b)=>a+b,0) || 0;
    optionsEl.querySelectorAll('.option').forEach((btn,idx)=>{
      const key = btn.dataset.option;
      const c = counts[key]||0;
      const pct = total? Math.round(c*100/total):0;
      percentEls[idx].textContent = pct + '%';
      barEls[idx].style.width = pct + '%';
    });
    if(total>0 && hint) hint.textContent = `Total votes: ${total} (demo, device-local)`;
  }

  optionsEl.addEventListener('click',(e)=>{
    const btn = e.target.closest('.option');
    if(!btn) return;
    const key = btn.dataset.option;
    counts[key] = (counts[key]||0) + 1;
    localStorage.setItem('ovs_demo_counts', JSON.stringify(counts));
    updateUI();
  });

  updateUI();
}


