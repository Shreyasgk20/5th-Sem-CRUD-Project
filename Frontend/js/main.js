// main.js - minimal UX helpers
document.addEventListener('DOMContentLoaded', () => {
  // populate candidates on ballot page
  if (document.getElementById('candidates')) {
    fetch('/Online_Voting/Backend/get_candidates.php')
      .then(r => r.json())
      .then(list => {
        const wrap = document.getElementById('candidates');
        if (!Array.isArray(list) || list.length === 0) {
          wrap.innerHTML = '<p>No candidates available.</p>';
          return;
        }
        const html = list.map(c => {
          return `<label class="candidate">
                    <input type="radio" name="candidate_id" value="${c.id}" required>
                    <strong>${escapeHtml(c.name)}</strong> <em>${escapeHtml(c.party||'')}</em>
                    <div class="manifesto">${escapeHtml(c.manifesto)}</div>
                  </label>`;
        }).join('');
        wrap.innerHTML = html;
      })
      .catch(err => {
        document.getElementById('candidates').innerHTML = '<p>Error loading candidates.</p>';
      });
  }

  // admin login: on success show admin tools (simple UX)
  const adminLoginForm = document.getElementById('admin-login');
  if (adminLoginForm) {
    adminLoginForm.addEventListener('submit', (e) => {
      // allow normal form submit (server redirect) - or you can enhance with AJAX
    });
  }

  // add candidate (AJAX)
  const addCandidateBtn = document.getElementById('addCandidateBtn');
  if (addCandidateBtn) {
    addCandidateBtn.addEventListener('click', () => {
      const form = document.getElementById('add-candidate');
      const data = new FormData(form);
      data.append('action','add_candidate');
      fetch('/Online_Voting/Backend/admin_actions.php', { method:'POST', body:data, credentials:'include' })
        .then(r=>r.json()).then(j => alert(j.success ? 'Candidate added' : 'Error: '+(j.message||'')));
    });
  }

  const addVoterBtn = document.getElementById('addVoterBtn');
  if (addVoterBtn) {
    addVoterBtn.addEventListener('click', () => {
      const form = document.getElementById('add-voter');
      const data = new FormData(form);
      data.append('action','add_voter');
      fetch('/Online_Voting/Backend/admin_actions.php', { method:'POST', body:data, credentials:'include' })
        .then(r=>r.json()).then(j => alert(j.success ? 'Voter added' : 'Error: '+(j.message||'')));
    });
  }
});

// simple html escape
function escapeHtml(s) {
  if (!s) return '';
  return s.replace(/[&<>"'`=\/]/g, function (c) {
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[c];
  });
}
