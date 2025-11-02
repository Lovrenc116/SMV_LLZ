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
  <title>Učiteljska stran - Šolska učilnica</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #f1f5f9;
      color: #1e293b;
      line-height: 1.6;
    }
    nav {
      background: #1e3a8a;
      color: #ffffff;
      padding: 1rem 2rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    nav ul {
      display: flex;
      list-style: none;
      gap: 1.5rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    nav a {
      color: #ffffff;
      text-decoration: none;
      font-weight: 500;
      font-size: 1rem;
      transition: color 0.3s ease;
    }
    nav a:hover {
      color: #93c5fd;
    }
    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }
    section {
      background: #ffffff;
      border-radius: 10px;
      padding: 2rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease;
    }
    section:hover {
      transform: translateY(-4px);
    }
    h2 {
      color: #1e3a8a;
      font-size: 1.75rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      max-width: 600px;
    }
    label {
      font-weight: 600;
      color: #374151;
      font-size: 0.95rem;
    }
    input[type="text"],
    input[type="file"],
    select {
      padding: 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 1rem;
      background: #f9fafb;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="file"]:focus,
    select:focus {
      border-color: #1e3a8a;
      box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
      outline: none;
    }
    button {
      padding: 0.75rem 1.5rem;
      background: #1e3a8a;
      color: #ffffff;
      font-size: 1rem;
      font-weight: 600;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }
    button:hover {
      background: #1e40af;
      transform: translateY(-2px);
    }
    button:active {
      transform: translateY(0);
    }
    .delete-button {
      background: #dc2626;
      padding: 0.5rem 1rem;
      font-size: 0.9rem;
    }
    .delete-button:hover {
      background: #b91c1c;
      transform: translateY(-2px);
    }
    .delete-button:active {
      transform: translateY(0);
    }
    .download-link {
      color: #1e3a8a;
      text-decoration: none;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .download-link:hover {
      text-decoration: underline;
    }
    .table-wrapper {
      overflow-x: auto;
      margin-top: 1.5rem;
      border-radius: 6px;
      border: 1px solid #e5e7eb;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.9rem;
    }
    th, td {
      border: 1px solid #e5e7eb;
      padding: 0.75rem 1rem;
      text-align: left;
      vertical-align: middle;
    }
    th {
      background: #1e3a8a;
      color: #ffffff;
      font-weight: 600;
      white-space: nowrap;
    }
    td {
      background: #ffffff;
    }
    tr:nth-child(even) td {
      background: #f9fafb;
    }
    tr:hover td {
      background: #f1f5f9;
    }
    @media (max-width: 768px) {
      nav ul {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
      }
      .container {
        margin: 1rem;
      }
      section {
        padding: 1.5rem;
      }
      h2 {
        font-size: 1.5rem;
      }
      table {
        min-width: unset;
      }
    }
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
    <!-- Nalaganje gradiv -->
    <section>
      <h2><i class="fas fa-upload"></i> Naloži novo gradivo</h2>
      <form id="form-gradivo" enctype="multipart/form-data">
        <label for="predmet-gradiva">Predmet</label>
        <select id="predmet-gradiva" name="predmet_id" required>
          <option value="">Izberi predmet</option>
          <!-- Možnosti bodo dinamično naložene iz baze -->
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
            <!-- Gradiva bodo dodana dinamično prek backend-a -->
          </tbody>
        </table>
      </div>
    </section>
    <!-- Pregled oddanih nalog -->
    <section>
      <h2><i class="fas fa-tasks"></i> Oddane naloge</h2>
      <div class="table-wrapper">
        <table id="tabela-naloge">
          <thead>
            <tr>
              <th>ID</th>
              <th>Predmet</th>
              <th>Učenec</th>
              <th>Naziv naloge</th>
              <th>Datoteka</th>
              <th>Datum oddaje</th>
            </tr>
          </thead>
          <tbody>
            <!-- Naloge bodo dodane dinamično prek backend-a -->
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <script>
    // JavaScript za brisanje gradiv
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
      document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', async () => {
          const id = button.getAttribute('data-id');
          const type = button.getAttribute('data-type');
          
          // Potrditev brisanja
          if (!confirm(`Ali res želite izbrisati ${type} z ID ${id}?`)) {
            return;
          }

          try {
            // Pošlji zahtevo na backend
            const response = await fetch(`delete_${type}.php`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({ id }),
            });
            
            const result = await response.json();
            
            if (result.success) {
              alert(`${type.charAt(0).toUpperCase() + type.slice(1)} uspešno izbrisan!`);
              button.closest('tr').remove();
            } else {
              alert(`Napaka pri brisanju: ${result.message}`);
            }
          } catch (error) {
            alert(`Napaka: ${error.message}`);
          }
        });
      });
    });
  </script>
</body>
</html>