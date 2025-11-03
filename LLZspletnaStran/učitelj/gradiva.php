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
  <title>Gradiva - Učiteljska stran</title>
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
    input[type="text"], input[type="file"], select { padding: 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; background: #f9fafb; transition: border-color 0.3s ease, box-shadow 0.3s ease; }
    input[type="text"]:focus, input[type="file"]:focus, select:focus { border-color: #1e3a8a; box-shadow: 0 0 0 4px rgba(30,58,138,0.15); outline: none; }
    button { padding: 1rem 2rem; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; font-size: 1.1rem; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: background 0.3s ease, transform 0.2s ease; }
    button:hover { background: linear-gradient(135deg, #1e40af, #2563eb); transform: scale(1.05); }
    button:active { transform: scale(1); }
    .delete-button { background: linear-gradient(135deg, #dc2626, #f87171); padding: 0.75rem 1.5rem; font-size: 1rem; }
    .delete-button:hover { background: linear-gradient(135deg, #b91c1c, #ef4444); transform: scale(1.05); }
    .download-link { color: #1e3a8a; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s ease; }
    .download-link:hover { color: #2563eb; text-decoration: underline; }
    .table-wrapper { overflow-x: auto; margin-top: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; }
    table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
    th, td { border: 1px solid #e5e7eb; padding: 1rem 1.2rem; text-align: left; vertical-align: middle; }
    th { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; font-weight: 600; white-space: nowrap; }
    td { background: #fff; }
    tr:nth-child(even) td { background: #f9fafb; }
    tr:hover td { background: #f1f5f9; }
    @media (max-width: 768px) { nav ul { flex-direction: column; gap: 1.2rem; } .container { margin: 1.5rem; padding: 0 1rem; } section { padding: 1.5rem; } h2 { font-size: 1.75rem; } button { padding: 0.8rem 1.5rem; font-size: 1rem; } table { min-width: unset; } }
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
    </ul>
  </nav>
  <div class="container">
    <section>
      <h2><i class="fas fa-upload"></i> Naloži novo gradivo</h2>
      <form id="form-gradivo" enctype="multipart/form-data">
        <label for="predmet-gradiva">Predmet</label>
        <select id="predmet-gradiva" name="predmet_id" required>
          <option value="">Izberi predmet</option>
          <!-- Dinamično iz baze -->
        </select>
        <label for="naziv-gradiva">Naziv gradiva</label>
        <input type="text" id="naziv-gradiva" name="naziv_gradiva" placeholder="Vpiši naziv gradiva" required />
        <label for="datoteka-gradiva">Datoteka</label>
        <input type="file" id="datoteka-gradiva" name="datoteka_gradiva" accept=".pdf,.doc,.docx,.txt" required />
        <button type="submit"><i class="fas fa-upload"></i> Naloži gradivo</button>
      </form>
      <div class="table-wrapper">
        <table id="tabela-gradiva">
          <thead>
            <tr>
              <th>ID</th>
              <th>Predmet</th>
              <th>Naziv gradiva</th>
              <th>Datoteka</th>
              <th>Dejanja</th>
            </tr>
          </thead>
          <tbody>
            <!-- Dinamično iz baze -->
          </tbody>
        </table>
      </div>
    </section>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      // Nalaganje predmetov
      try {
        const response = await fetch('../api/ucitelj_predmeti.php');
        const data = await response.json();
        if (data.success) {
          const select = document.getElementById('predmet-gradiva');
          // Počisti obstoječe opcije (razen prve)
          select.innerHTML = '<option value="">Izberi predmet</option>';
          // Dodaj predmete
          data.predmeti.forEach(predmet => {
            const option = document.createElement('option');
            option.value = predmet.id;
            option.textContent = predmet.naziv_predmeta;
            select.appendChild(option);
          });
        } else {
          console.error('Napaka pri nalaganju predmetov:', data.message);
        }
      } catch (error) {
        console.error('Napaka pri nalaganju predmetov:', error);
      }

      // Funkcija za brisanje gradiv (se pokliče ko se tabela naloži)
      function nastaviBrisanjeGradiv() {
        document.querySelectorAll('.delete-button').forEach(button => {
          // Odstrani stare event listenerje
          const newButton = button.cloneNode(true);
          button.parentNode.replaceChild(newButton, button);
          
          newButton.addEventListener('click', async () => {
            const id = newButton.getAttribute('data-id');
            if (!confirm('Ali res želite izbrisati to gradivo?')) return;
            try {
              const response = await fetch('../api/gradiva.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
              });
              const result = await response.json();
              if (result.success) {
                alert('Gradivo uspešno izbrisano!');
                osveziGradiva();
              } else {
                alert('Napaka pri brisanju: ' + (result.message || 'Neznana napaka'));
              }
            } catch (error) {
              alert('Napaka: ' + error.message);
            }
          });
        });
      }

      // Osveževanje tabel
      async function osveziGradiva() {
        try {
          const select = document.getElementById('predmet-gradiva');
          const predmetId = select && select.value ? select.value : '';
          if (!predmetId) { return; }
          const response = await fetch(`../api/gradiva.php?predmet=${predmetId}`);
          const gradiva = await response.json();
          const tabela = document.querySelector('#tabela-gradiva tbody');
          tabela.innerHTML = '';
          const seznam = (gradiva.gradiva ?? gradiva) || [];
          seznam.forEach(gradivo => {
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>${gradivo.id}</td>
              <td>${gradivo.naziv_predmeta || ''}</td>
              <td>${gradivo.naziv_gradiva || gradivo.naziv || ''}</td>
              <td><a href="../${gradivo.datoteka}" class="download-link" download><i class="fas fa-download"></i> ${String(gradivo.datoteka || '').split('/').pop()}</a></td>
              <td><button class="delete-button" data-id="${gradivo.id}"><i class="fas fa-trash"></i> Izbriši</button></td>
            `;
            tabela.appendChild(row);
            
            // Nastavi event listener za brisanje
            const deleteBtn = row.querySelector('.delete-button');
            deleteBtn.addEventListener('click', async () => {
              const id = deleteBtn.getAttribute('data-id');
              if (!confirm('Ali res želite izbrisati to gradivo?')) return;
              try {
                const response = await fetch('../api/gradiva.php', {
                  method: 'DELETE',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ id })
                });
                const result = await response.json();
                if (result.success) {
                  alert('Gradivo uspešno izbrisano!');
                  osveziGradiva();
                } else {
                  alert('Napaka pri brisanju: ' + (result.message || 'Neznana napaka'));
                }
              } catch (error) {
                alert('Napaka: ' + error.message);
              }
            });
          });
        } catch (error) {
          console.error('Napaka pri osveževanju gradiv:', error);
        }
      }

      // Nalaganje gradiva
      document.getElementById('form-gradivo').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        try {
          const response = await fetch('../api/gradivo_upload.php', { method: 'POST', body: formData });
          const result = await response.json();
          if (result.success) {
            alert('Gradivo uspešno naloženo!');
            osveziGradiva();
            e.target.reset();
          } else {
            alert('Napaka pri nalaganju gradiva: ' + result.message);
          }
        } catch (error) {
          alert('Napaka: ' + error.message);
        }
      });

      // Osveži gradiva, ko se izbere predmet
      const predmetSelect = document.getElementById('predmet-gradiva');
      if (predmetSelect) {
        predmetSelect.addEventListener('change', () => {
          osveziGradiva();
        });
      }

      // Naloži gradiva ob zagonu, če je predmet že izbran
      osveziGradiva();
    });
  </script>
</body>
</html>