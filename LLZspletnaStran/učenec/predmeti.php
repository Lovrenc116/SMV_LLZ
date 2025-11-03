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
  <title>Profil - LLZ spletna učilnica</title>
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
    .form-section {
      opacity: 0;
      animation: fadeIn 0.5s forwards;
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
      <a href="ucenec.php" class="flex items-center p-4 rounded-lg font-semibold">
        <i class="fas fa-home mr-3 text-xl"></i> <span class="nav-text">Domov</span>
      </a>
      <a href="profil.php" class="flex items-center p-4 rounded-lg font-semibold">
        <i class="fas fa-user mr-3 text-xl"></i> <span class="nav-text">Profil</span>
      </a>
      <a href="predmeti.php" class="active flex items-center p-4 rounded-lg font-semibold">
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
    <header class="mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Moji predmeti</h1>
      <p class="text-gray-600 mt-2">Predmeti, ki jih obiskuješ</p>
    </header>
    
    <section class="bg-white p-8 rounded-xl shadow-lg">
      <h2 class="text-xl font-semibold text-gray-800 mb-6">Seznam predmetov</h2>
      <div id="predmeti-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Predmeti se bodo naložili dinamično -->
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

    // Nalaganje predmetov
    async function naloziPredmete() {
      try {
        const response = await fetch('../api/ucenec_predmeti.php');
        const data = await response.json();
        const container = document.getElementById('predmeti-container');
        
        if (data.success && data.predmeti.length > 0) {
          container.innerHTML = data.predmeti.map(predmet => `
            <div class="bg-blue-50 p-6 rounded-xl border border-blue-200 hover:shadow-lg transition-shadow">
              <div class="flex items-center mb-4">
                <i class="fas fa-book text-blue-600 text-2xl mr-3"></i>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">${predmet.naziv_predmeta}</h3>
                  <p class="text-sm text-gray-600">${predmet.kratica}</p>
                </div>
              </div>
              <div class="text-sm text-gray-600">
                <p><strong>Učitelji:</strong> ${predmet.ucitelji || 'Ni dodeljenih učiteljev'}</p>
              </div>
              <div class="mt-4">
                <a href="gradiva_naloge.php?predmet=${predmet.id}" class="text-blue-600 hover:text-blue-800 font-medium">
                  <i class="fas fa-folder-open mr-1"></i> Ogled gradiv in nalog
                </a>
              </div>
            </div>
          `).join('');
        } else {
          container.innerHTML = `
            <div class="col-span-full text-center py-12">
              <i class="fas fa-book text-gray-400 text-4xl mb-4"></i>
              <p class="text-gray-600 text-lg">Nimaš dodeljenih predmetov.</p>
              <p class="text-gray-500 text-sm mt-2">Kontaktiraj administratorja za dodelitev predmetov.</p>
            </div>
          `;
        }
      } catch (error) {
        console.error('Napaka pri nalaganju predmetov:', error);
        document.getElementById('predmeti-container').innerHTML = `
          <div class="col-span-full text-center py-12">
            <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
            <p class="text-red-600 text-lg">Napaka pri nalaganju predmetov.</p>
            <p class="text-gray-500 text-sm mt-2">Poskusite znova pozneje.</p>
          </div>
        `;
      }
    }

    // Naloži predmete ob zagonu strani
    document.addEventListener('DOMContentLoaded', naloziPredmete);
  </script>
</body>
</html>