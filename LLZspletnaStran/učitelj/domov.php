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
  <title>Domov - Učiteljska stran</title>
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
    .overview { display: flex; gap: 1.5rem; flex-wrap: wrap; }
    .card { background: #fff; border-radius: 8px; padding: 1.5rem; box-shadow: 0 4px 10px rgba(0,0,0,0.05); flex: 1; min-width: 250px; text-align: center; transition: transform 0.3s ease; }
    .card:hover { transform: scale(1.05); }
    .card i { font-size: 2.5rem; color: #1e3a8a; margin-bottom: 1rem; }
    .card h3 { font-size: 1.25rem; color: #1e3a8a; margin-bottom: 0.5rem; }
    .card p { font-size: 1.5rem; font-weight: 700; }
    @media (max-width: 768px) { nav ul { flex-direction: column; gap: 1.2rem; } .container { margin: 1.5rem; padding: 0 1rem; } section { padding: 1.5rem; } h2 { font-size: 1.75rem; } }
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
      <h1 style="color: #1e3a8a; font-size: 2.5rem; margin-bottom: 1rem; font-weight: 700;">Pozdravljen, <?php echo htmlspecialchars($_SESSION['ime'] ?? 'Učitelj'); ?>!</h1>
      <p style="color: #64748b; font-size: 1.1rem; margin-bottom: 2rem;">Prijavil si se v svojo učiteljsko učilnico.</p>
      <h2><i class="fas fa-dashboard"></i> Pregled</h2>
      <div class="overview">
        <div class="card">
          <i class="fas fa-book-open"></i>
          <h3>Predmeti</h3>
          <p id="stevilo-predmetov">0</p>
        </div>
        <div class="card">
          <i class="fas fa-users"></i>
          <h3>Učenci</h3>
          <p id="stevilo-ucencev">0</p>
        </div>
        <div class="card">
          <i class="fas fa-tasks"></i>
          <h3>Zadnje naloge</h3>
          <p id="zadnje-naloge">0 novih</p>
        </div>
      </div>
    </section>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Dinamično polnjenje pregleda
      async function osveziPregled() {
        try {
          const response = await fetch('../api/ucitelj_pregled.php');
          const data = await response.json();
          
          if (data.success) {
            document.getElementById('stevilo-predmetov').textContent = data.predmeti || 0;
            document.getElementById('stevilo-ucencev').textContent = data.ucenci || 0;
            document.getElementById('zadnje-naloge').textContent = data.zadnje_naloge || '0 novih';
          } else {
            console.error('Napaka pri pridobivanju podatkov:', data.message);
          }
        } catch (error) {
          console.error('Napaka pri osveževanju pregleda:', error);
        }
      }
      osveziPregled();
    });
  </script>
</body>
</html>