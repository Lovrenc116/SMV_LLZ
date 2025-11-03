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
  <title>Naloge - Učiteljska stran</title>
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
    .download-link { color: #1e3a8a; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s ease; }
    .download-link:hover { color: #2563eb; text-decoration: underline; }
    .table-wrapper { overflow-x: auto; margin-top: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; }
    table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
    th, td { border: 1px solid #e5e7eb; padding: 1rem 1.2rem; text-align: left; vertical-align: middle; }
    th { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; font-weight: 600; white-space: nowrap; }
    td { background: #fff; }
    tr:nth-child(even) td { background: #f9fafb; }
    tr:hover td { background: #f1f5f9; }
    @media (max-width: 768px) { nav ul { flex-direction: column; gap: 1.2rem; } .container { margin: 1.5rem; padding: 0 1rem; } section { padding: 1.5rem; } h2 { font-size: 1.75rem; } table { min-width: unset; } }
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
            <!-- Dinamično iz baze -->
          </tbody>
        </table>
      </div>
    </section>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Nalaganje predmetov in nalog
      let predmetiList = [];
      
      async function naloziPredmete() {
        try {
          const response = await fetch('../api/ucitelj_predmeti.php');
          const data = await response.json();
          if (data.success) {
            predmetiList = data.predmeti;
            if (predmetiList.length > 0) {
              // Če obstaja izbira predmeta, jo naloži
              const selectElement = document.getElementById('predmet-select');
              if (selectElement) {
                predmetiList.forEach(predmet => {
                  const option = document.createElement('option');
                  option.value = predmet.id;
                  option.textContent = predmet.naziv_predmeta;
                  selectElement.appendChild(option);
                });
              }
            }
          }
        } catch (error) {
          console.error('Napaka pri nalaganju predmetov:', error);
        }
      }
      
      // Osveževanje tabele nalog
      async function osveziNaloge(predmetId = null) {
        try {
          // Če ni izbranega predmeta, prikaži naloge za vse predmete učitelja
          let url = '../api/naloge.php';
          if (predmetId) {
            url += '?predmet=' + predmetId;
          } else if (predmetiList.length > 0) {
            // Prikaži naloge za prvi predmet
            url += '?predmet=' + predmetiList[0].id;
          } else {
            // Če ni predmetov, ne prikazuj ničesar
            document.querySelector('#tabela-naloge tbody').innerHTML = '<tr><td colspan="6" class="text-center">Izberi predmet za ogled nalog.</td></tr>';
            return;
          }
          
          const response = await fetch(url);
          const data = await response.json();
          const naloge = data.naloge || [];
          const tabela = document.querySelector('#tabela-naloge tbody');
          tabela.innerHTML = '';
          
          if (naloge.length === 0) {
            tabela.innerHTML = '<tr><td colspan="6" class="text-center">Ni oddanih nalog za ta predmet.</td></tr>';
            return;
          }
          
          naloge.forEach(naloga => {
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>${naloga.id}</td>
              <td>${naloga.naziv_predmeta || ''}</td>
              <td>${naloga.ucenec_ime || 'Neznano'}</td>
              <td>${naloga.naslov_naloge || ''}</td>
              <td><a href="../uploads/${naloga.datoteka}" class="download-link" download><i class="fas fa-download"></i> ${(naloga.datoteka || '').split('/').pop()}</a></td>
              <td>${naloga.datum_oddaje ? new Date(naloga.datum_oddaje).toLocaleDateString('sl-SI') : ''}</td>
            `;
            tabela.appendChild(row);
          });
        } catch (error) {
          console.error('Napaka pri osveževanju nalog:', error);
          document.querySelector('#tabela-naloge tbody').innerHTML = '<tr><td colspan="6" class="text-center text-red-600">Napaka pri nalaganju nalog.</td></tr>';
        }
      }
      
      naloziPredmete().then(() => osveziNaloge());
    });
  </script>
</body>
</html>