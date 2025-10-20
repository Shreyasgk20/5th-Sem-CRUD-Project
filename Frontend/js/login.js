// frontend/js/login.js
// Robust AJAX login + session-confirmation polling
// Usage: include after your login form HTML. Assumes form id="voter-login-form".

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('voter-login-form');
    if (!form) {
      console.warn('login.js: voter-login-form not found; skipping login handler.');
      return;
    }
  
    // message container (create if missing)
    let msgDiv = document.getElementById('form-msg');
    if (!msgDiv) {
      msgDiv = document.createElement('div');
      msgDiv.id = 'form-msg';
      msgDiv.className = 'form-msg';
      form.appendChild(msgDiv);
    }
  
    function setMsg(text, color = '#000') {
      msgDiv.style.color = color;
      msgDiv.textContent = text;
    }
  
    // Poll check_session.php until session shows up or timeout
    async function waitForSession(maxAttempts = 8, delayMs = 250) {
      const url = '/Online_Voting/backend/check_session.php';
      for (let i = 0; i < maxAttempts; i++) {
        try {
          const res = await fetch(url, {
            method: 'GET',
            credentials: 'include',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
          });
          if (!res.ok) {
            // non-200: maybe server error; still try again a few times
            console.warn('check_session non-ok:', res.status);
          }
          const text = await res.text();
          let data;
          try { data = JSON.parse(text); } catch (err) {
            console.warn('check_session returned non-JSON (attempt', i+1, '):', text);
            data = null;
          }
  
          if (data && data.logged_in) {
            return true;
          }
        } catch (err) {
          console.warn('Network error on check_session attempt', i+1, err);
        }
  
        // small wait before next attempt
        await new Promise(r => setTimeout(r, delayMs));
      }
      return false;
    }
  
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      setMsg('Logging in...', '#000');
  
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;
  
      const url = form.action || '/Online_Voting/backend/auth.php';
      const formData = new FormData(form);
  
      try {
        const res = await fetch(url, {
          method: 'POST',
          body: formData,
          credentials: 'include',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
  
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); } catch (err) {
          console.error('Login response not JSON:', text);
          setMsg('Server error during login (invalid response). See console.', '#B00020');
          if (submitBtn) submitBtn.disabled = false;
          return;
        }
  
        if (!data || !data.success) {
          setMsg(data && data.message ? data.message : 'Login failed', '#B00020');
          if (submitBtn) submitBtn.disabled = false;
          return;
        }
  
        // login success: but we must ensure session is actually set on server side
        setMsg('Login successful. Confirming session...', '#006400');
  
        const sessionOk = await waitForSession(12, 300); // ~3.6s total attempts
        if (sessionOk) {
          setMsg('Session confirmed. Redirecting...', '#006400');
          // use server-provided redirect if present
          const redirectUrl = data.redirect && String(data.redirect).trim() ? String(data.redirect).trim() : '/Online_Voting/frontend/ballot.html';
          // small delay so user sees message
          setTimeout(() => window.location.assign(redirectUrl), 250);
          return;
        } else {
          // session not detected — likely cookies blocked or server delay
          setMsg('Login succeeded but session not detected. Check cookies or server. Trying direct redirect...', '#B00020');
          // fallback redirect — still try
          const fallback = data.redirect || '/Online_Voting/frontend/ballot.html';
          setTimeout(() => window.location.assign(fallback), 600);
          return;
        }
  
      } catch (err) {
        console.error('Login fetch error:', err);
        setMsg('Error connecting to server', '#B00020');
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  });
  