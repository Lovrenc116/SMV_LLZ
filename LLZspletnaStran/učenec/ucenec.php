<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'ucenec') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nadzorna plošča - LLZ spletna učilnica</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      background: #f3f4f6;
    }
    .sidebar {
      background: #ffffff;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
      transition: width 0.3s ease;
      position: fixed;
      height: 100%;
      overflow-y: auto;
    }
    .sidebar.collapsed {
      width: 80px;
    }
    .sidebar:not(.collapsed) {
      width: 260px;
    }
    .sidebar a {
      transition: all 0.3s ease;
    }
    .sidebar a:hover {
      background-color: #eff6ff;
      color: #1e40af;
    }
    .sidebar a.active {
      background-color: #dbeafe;
      color: #1e40af;
      border-left: 4px solid #3b82f6;
    }
    .sidebar .nav-text {
      display: inline;
    }
    .sidebar.collapsed .nav-text {
      display: none;
    }
    .dashboard-section {
      opacity: 0;
      animation: fadeIn 0.5s forwards;
    }
    .dashboard-section:nth-child(1) { animation-delay: 0.1s; }
    .dashboard-section:nth-child(2) { animation-delay: 0.2s; }
    .quick-link {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .quick-link:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    .btn {
      transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .btn:hover {
      background-color: #1e40af;
      transform: scale(1.05);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }
      .sidebar.collapsed {
        height: 60px;
      }
      main {
        margin-left: 0 !important;
        margin-top: 60px;
      }
    }
  </style>
</head>
<body class="antialiased flex">
  <aside class="sidebar text-gray-800 flex flex-col p-6">
    <div class="flex items-center justify-between mb-8">
      <h1 class="text-2xl font-bold nav-text">LLZ učilnica</h1>
      <button id="toggleSidebar" class="text-gray-600 hover:text-gray-800">
        <i class="fas fa-bars text-2xl"></i>
      </button>
    </div>
    <nav class="space-y-3">
      <a href="ucenec.php" class="active flex items-center p-4 rounded-lg font-semibold">
        <i class="fas fa-home mr-3 text-xl"></i> <span class="nav-text">Domov</span>
      </a>
      <a href="profil.php" class="flex items-center p-4 rounded-lg font-semibold">
        <i class="fas fa-user mr-3 text-xl"></i> <span class="nav-text">Profil</span>
      </a>
      <a href="predmeti.php" class="flex items-center p-4 rounded-lg font-semibold">
        <i class="fas fa-book mr-3 text-xl"></i> <span class="nav-text">Predmeti</span>
      </a>
      <a href="gradiva_naloge.php" class="flex items-center p-4 rounded-lg font-semibold">
        <i class="fas fa-folder-open mr-3 text-xl"></i> <span class="nav-text">Gradiva in naloge</span>
      </a>
      <a href="../api/logout.php" class="flex items-center p-4 rounded-lg font-semibold">
        <i class="fas fa-sign-out-alt mr-3 text-xl"></i> <span class="nav-text">Odjava</span>
      </a>
    </nav>
  </aside>

  <main class="flex-1 ml-64 p-8">
    <header class="mb-8 dashboard-section">
      <h1 class="text-3xl font-bold text-gray-800">Pozdravljen, <?php echo htmlspecialchars($_SESSION['ime'] ?? 'Učenec'); ?>!</h1>
      <p class="text-gray-600 mt-2">Prijavil si se v svojo šolsko učilnico.</p>
    </header>
    <section class="dashboard-section mb-8">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Hitri dostopi</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="predmeti.php" class="quick-link bg-blue-50 p-6 rounded-xl flex items-center">
          <i class="fas fa-book text-blue-600 text-3xl mr-4"></i>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Moji predmeti</h3>
            <p class="text-gray-600">Upravljaj svoje predmete</p>
          </div>
        </a>
        <a href="gradiva_naloge.php" class="quick-link bg-blue-50 p-6 rounded-xl flex items-center">
          <i class="fas fa-folder-open text-blue-600 text-3xl mr-4"></i>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Gradiva</h3>
            <p class="text-gray-600">Ogled učnih materialov</p>
          </div>
        </a>
        <a href="gradiva_naloge.php#naloge" class="quick-link bg-blue-50 p-6 rounded-xl flex items-center">
          <i class="fas fa-tasks text-blue-600 text-3xl mr-4"></i>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Oddaj naloge</h3>
            <p class="text-gray-600">Naloži svoje domače naloge</p>
          </div>
        </a>
      </div>
    </section>
    <section class="dashboard-section">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Zadnje aktivnosti</h2>
      <div class="bg-white p-6 rounded-xl shadow-lg">
        <ul id="zadnje-aktivnosti" class="space-y-4">
          <li class="flex items-center">
            <i class="fas fa-file-upload text-blue-500 mr-3"></i>
            <div>
              <p class="text-gray-800 font-medium">Oddana naloga: Matematika - Algebra</p>
              <p class="text-gray-600 text-sm">Datum: 28. september 2025</p>
            </div>
          </li>
          <li class="flex items-center">
            <i class="fas fa-folder-open text-blue-500 mr-3"></i>
            <div>
              <p class="text-gray-800 font-medium">Novo gradivo: Slovenščina - Esej</p>
              <p class="text-gray-600 text-sm">Datum: 27. september 2025</p>
            </div>
          </li>
        </ul>
      </div>
    </section>
  </main>

  <script>
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      const sidebar = document.querySelector('.sidebar');
      sidebar.classList.toggle('collapsed');
      document.querySelector('main').classList.toggle('ml-64');
      document.querySelector('main').classList.toggle('ml-20');
    });

    async function naloziAktivnosti() {
      try {
        const response = await fetch('../api/ucenec_aktivnosti.php');
        const aktivnosti = await response.json();
        const seznam = document.getElementById('zadnje-aktivnosti');
        seznam.innerHTML = aktivnosti.length
          ? aktivnosti.map(a => `
              <li class="flex items-center">
                <i class="fas ${a.tip === 'naloga' ? 'fa-file-upload' : 'fa-folder-open'} text-blue-500 mr-3"></i>
                <div>
                  <p class="text-gray-800 font-medium">${a.tip === 'naloga' ? 'Oddana naloga' : 'Novo gradivo'}: ${a.predmet} - ${a.naslov}</p>
                  <p class="text-gray-600 text-sm">Datum: ${new Date(a.datum).toLocaleDateString('sl-SI')}</p>
                </div>
              </li>
            `).join('')
          : '<li class="text-gray-500">Ni zadnjih aktivnosti.</li>';
      } catch (error) {
        document.getElementById('zadnje-aktivnosti').innerHTML = '<li class="text-red-500">Napaka pri nalaganju aktivnosti.</li>';
      }
    }

    document.addEventListener('DOMContentLoaded', naloziAktivnosti);
  </script>
</body>
</html>