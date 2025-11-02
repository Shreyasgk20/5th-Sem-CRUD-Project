// frontend/js/main.js
document.addEventListener('DOMContentLoaded', () => {
  // -------- VOTER LOGIN --------
  const loginForm = document.getElementById('voter-login-form');
  const loginMsg = document.getElementById('form-msg');

  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (loginMsg) loginMsg.style.color = '#000'; loginMsg.textContent = 'Logging in...';

      const url = '/Online_Voting/backend/auth.php'; // always correct backend URL
      const formData = new FormData(loginForm);

      try {
        const res = await fetch(url, {
          method: 'POST',
          body: formData,
          credentials: 'include',
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await res.json();
        if (data.success) {
          if (loginMsg) { loginMsg.style.color = '#006400'; loginMsg.textContent = 'Login successful — redirecting...'; }
          setTimeout(() => window.location.href = data.redirect, 200);
        } else {
          if (loginMsg) { loginMsg.style.color = '#B00020'; loginMsg.textContent = data.message || 'Login failed'; }
        }
      } catch (err) {
        console.error('Login fetch error:', err);
        if (loginMsg) loginMsg.textContent = 'Server error during login. See console.';
      }
    });
  }

  // -------- BALLOT PAGE: LOAD CANDIDATES --------
  const candidatesContainer = document.getElementById('candidates');
  const voteMsg = document.getElementById('vote-msg');
  const ballotForm = document.getElementById('ballot-form');

  if (candidatesContainer) {
    candidatesContainer.innerHTML = '<p>Loading candidates...</p>';

    fetch('/Online_Voting/backend/get_all_candidates.php', {
      method: 'GET',
      credentials: 'include'
    })
      .then(res => res.json())
      .then(data => {
        if (!Array.isArray(data)) throw new Error('Invalid candidates data');

        if (data.length === 0) {
          candidatesContainer.innerHTML = '<p>No candidates found.</p>';
          return;
        }

        let html = '';
        data.forEach(c => {
          html += `
            <div class="candidate-card">
              <input type="radio" name="candidate_id" value="${c.id}" id="c${c.id}" required>
              <label for="c${c.id}">
                <div class="candidate-name">${c.name}</div>
                <div class="candidate-party">${c.party}</div>
                ${c.party_symbol ? `<img class="party-symbol" src="${c.party_symbol}" alt="${c.party} symbol">` : ''}
                <p class="candidate-manifesto">${c.manifesto.substring(0, 250)}</p>
              </label>
            </div>
          `;
        });

        candidatesContainer.innerHTML = html;
      })
      .catch(err => {
        console.error('Failed to load candidates:', err);
        candidatesContainer.innerHTML = '<p>Error loading candidates. Try refreshing.</p>';
      });
  }

  // -------- CAST VOTE --------
  if (ballotForm) {
    ballotForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (voteMsg) voteMsg.style.color = '#000'; voteMsg.textContent = 'Submitting vote...';

      const url = ballotForm.action || '/Online_Voting/backend/vote.php';
      const formData = new FormData(ballotForm);

      try {
        const res = await fetch(url, {
          method: 'POST',
          body: formData,
          credentials: 'include'
        });

        const text = await res.text();
        // If backend redirects with PHP headers, we can't parse JSON. Just redirect
        if (res.redirected) {
          window.location.href = res.url;
          return;
        }

        // Optional: handle JSON response if backend returns JSON
        let data;
        try { data = JSON.parse(text); } catch { data = null; }

        if (data && data.success && data.redirect) {
          window.location.href = data.redirect;
        } else {
          if (voteMsg) voteMsg.style.color = '#B00020'; voteMsg.textContent = 'Vote submission failed';
        }
      } catch (err) {
        console.error('Vote fetch error:', err);
        if (voteMsg) voteMsg.textContent = 'Error connecting to server';
      }
    });
  }
});
// Candidate list & delete feature
document.getElementById("listCandidatesBtn").addEventListener("click", async () => {
  const candidateList = document.getElementById("candidate-list");
  candidateList.style.display = "block";
  candidateList.innerHTML = "Loading...";

  try {
    const response = await fetch("/Online_Voting/backend/get_candidates.php");
    const data = await response.json(); // expects JSON array of candidates

    if (data.length === 0) {
      candidateList.innerHTML = "No candidates found.";
      return;
    }

    let html = "<ul class='candidate-items'>";
    data.forEach(candidate => {
      html += `
        <li>
          <strong>${candidate.name}</strong> (${candidate.party || "No Party"})
          <button class="btn warning delete-btn" data-id="${candidate.id}">Delete</button>
        </li>`;
    });
    html += "</ul>";

    candidateList.innerHTML = html;

    // Delete candidate
    document.querySelectorAll(".delete-btn").forEach(btn => {
      btn.addEventListener("click", async () => {
        const id = btn.getAttribute("data-id");
        if (confirm("Are you sure you want to delete this candidate?")) {
          const res = await fetch("/Online_Voting/backend/delete_candidate.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
          });
          const result = await res.json();
          if (result.success) {
            btn.parentElement.remove();
            alert("Candidate deleted successfully.");
          } else {
            alert("Error deleting candidate.");
          }
        }
      });
    });

  } catch (error) {
    candidateList.innerHTML = "Error loading candidates.";
    console.error(error);
  }
});
