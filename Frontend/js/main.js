document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('voter-login-form');
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      const msgDiv = document.getElementById('form-msg');
      msgDiv.textContent = 'Logging in...';

      try {
        const res = await fetch(form.action, {
          method: 'POST',
          body: formData,
          credentials: 'include'
        });
        const data = await res.json();
        if (data.success) {
          window.location.href = data.redirect;
        } else {
          msgDiv.textContent = data.message || 'Login failed';
        }
      } catch (err) {
        msgDiv.textContent = 'Error connecting to server';
        console.error(err);
      }
    });
  }
});
