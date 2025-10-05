# Online Voting Platform — Implementation & Features

## Overview
A lightweight, production-minded web application that separates frontend and backend responsibilities:  
a static **frontend (HTML / CSS / JavaScript)** and a **server-side backend (PHP with MySQL)**.  
Designed for clarity, maintainability, and straightforward extensibility.

---

## Implementation (High Level)
- **Frontend:** Static pages (`index`, `voter login`, `ballot`, `thanks`, `admin`) with responsive and accessible design.  
  Styled with an Indian-inspired color theme and large, readable typography for all demographics.  
  JavaScript is used minimally for form handling and UX enhancements.

- **Backend:** PHP scripts (using `mysqli`) exposing clean endpoints for authentication, voting, and admin actions.  
  Organized with a central database connection file for consistency and easy updates.

- **Database Design:** Normalized tables for:
  - `admins`
  - `voters`
  - `candidates`
  - `votes`

  The `votes` table includes a **UNIQUE constraint on `voter_id`** to prevent duplicate voting.

---

## Key Features
- **Voter Authentication:** Login with **Voter ID + Date of Birth** using session-based validation.  
- **Ballot System:** Display of candidate list (name, party, manifesto) with single-choice vote submission.  
- **Vote Integrity:** One-vote-per-user rule enforced via database constraints.  
- **Admin Panel:** Add candidates, manage voters, and view live vote counts.  
- **Accessibility:** Clear UI, labeled forms, readable fonts, and optional bilingual text (English/Hindi).  
- **Responsive Design:** Works across devices and screen sizes.

---

## Security & Best Practices
- All SQL queries use **Prepared Statements** to prevent injection attacks.  
- Passwords are stored using **secure hashing** (`password_hash()` / `password_verify()`).  
- **Session Regeneration** on login to protect session fixation.  
- **Database-level Constraints** ensure valid, consistent data.

---

## Extendability & Notes
- The codebase is **modular** — separate `frontend` and `backend` folders make it easy to extend.  
- Future enhancements can include OTP verification, multilingual support, analytics, or audit logs.  
- Focused on **clarity, simplicity, and transparency** for easy maintenance and demonstration.

---
