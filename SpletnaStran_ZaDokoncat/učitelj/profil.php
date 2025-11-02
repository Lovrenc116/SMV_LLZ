<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'ucitelj') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profil - Učiteljska stran</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    /* Enak CSS kot v izboljšani učiteljski strani - za lepši videz */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #e0e7ff, #f1f5f9); color: #1e293b; line-height: 1.6; min-height: 100vh; }
    nav { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; padding: 1.2rem 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); position: sticky; top: 0; z-index: 1000; }
    nav ul { display: flex; list-style: none; gap: 2rem; max-width: 1200px; margin: 0 auto; align-items: center; }
    nav a { color: #fff; text-decoration: none; font-weight: 600; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s ease, transform 0.2s ease; }
    nav a:hover { color: #dbeafe; transform: translateY(-2px); }
    .container { max-width: 1200px; margin: 2.5rem auto; padding: 0 1.5rem; display: flex; flex-direction: column; gap: 2.5rem; }
    section { background: rgba(255,255,255,0.95); border-radius: 12px; padding: 2.5rem; box-shadow: 0 6px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; }
    section:hover { transform: translateY(-6px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
    h2 { color: #1e3a8a; font-size: 2rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; font-weight: 700; }
    form { display: flex; flex-direction: column; gap: 1.2rem; max-width: 600px; }
    label { font-weight: 600; color: #374151; font-size: 1rem; }
    input[type="text"], input[type="email"], input[type="password"], select { padding: 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; background: #f9fafb; transition: border-color 0.3s ease, box-shadow 0.3s ease; }
    input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, select:focus { border-color: #1e3a8a; box-shadow: 0 0 0 4px rgba(30,58,138,0.15); outline: none; }
    button { padding: 1rem 2rem; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; font-size: 1.1rem; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: background 0.3s ease, transform 0.2s ease; }
    button:hover { background: linear-gradient(135deg, #1e40af, #2563eb); transform: scale(1.05); }
    button:active { transform: scale(1); }
    @media (max-width: 768px) { nav ul { flex-direction: column; gap: 1.2rem; } .container { margin: 1.5rem; padding: 0 1rem; } section { padding: 1.5rem; } h2 { font-size: 1.75rem; } button { padding: 0.8rem 1.5rem; font-size: 1rem; } }
  </style>
</head>
<body>
  <nav>
    <ul>
      <li><a href="domov.php"><i class="fas fa-home"></i> Domov</a></li>
      <li><a href="gradiva.php"><i class="fas fa-book"></i> Gradiva</a></li>
      <li><a href="naloge.php"><i class="fas fa-tasks"></i> Naloge</a></li>
      <li><a href="profil.php"><i class="fas fa-user"></i> Profil</a></li>
      <li><a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Odjava</a></li>
      <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Odjava</a></li>
    </ul>
  </nav>
  <div class="container">
    <section>
      <h2><i class="fas fa-user-edit"></i> Uredi profil</h2>
      <form id="form-profil">
        <label for="ime">Ime</label>
        <input type="text" id="ime" name="ime" placeholder="Vpiši ime" required />
        <label for="priimek">Priimek</label>
        <input type="text" id="priimek" name="priimek" placeholder="Vpiši priimek" required />
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Vpiši email" required />
        <label for="geslo">Novo geslo</label>
        <input type="password" id="geslo" name="geslo" placeholder="Vpiši novo geslo" />
        <label for="potrdi-geslo">Potrdi geslo</label>
        <input type="password" id="potrdi-geslo" name="potrdi_geslo" placeholder="Potrdi novo geslo" />
        <button type="submit"><i class="fas fa-save"></i> Shranite spremembe</button>
      </form>
    </section>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Odjava
      document.querySelector('a[href="login.php"]').addEventListener('click', async (e) => {
        e.preventDefault();
        if (confirm('Ali res želite se odjaviti?')) {
          try {
            const response = await fetch('odjava.php', { method: 'POST', headers: { 'Content-Type': 'application/json' } });
            const result = await response.json();
            if (result.success) {
              window.location.href = 'login.php';
            } else {
              alert('Napaka pri odjavi: ' + result.message);
            }
          } catch (error) {
            alert('Napaka pri odjavi: ' + error.message);
          }
        }
      });

      // Urejanje profila
      document.getElementById('form-profil').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        try {
          const response = await fetch('update_profil.php', { method: 'POST', body: formData });
          const result = await response.json();
          if (result.success) {
            alert('Profil uspešno posodobljen!');
          } else {
            alert('Napaka pri posodobitvi profila: ' + result.message);
          }
        } catch (error) {
          alert('Napaka: ' + error.message);
        }
      });

      // Nalaganje obstoječih podatkov profila
      async function osveziProfil() {
        try {
          const response = await fetch('get_profil.php');
          const data = await response.json();
          document.getElementById('ime').value = data.ime || '';
          document.getElementById('priimek').value = data.priimek || '';
          document.getElementById('email').value = data.email || '';
        } catch (error) {
          console.error('Napaka pri nalaganju profila:', error);
        }
      }
      osveziProfil();
    });
  </script>
</body>
</html>