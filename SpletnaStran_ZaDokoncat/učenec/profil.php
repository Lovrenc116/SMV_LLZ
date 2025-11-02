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
      <a href="profil.php" class="active flex items-center p-4 rounded-lg font-semibold">
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
    <header class="mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Uredi profil</h1>
      <p class="text-gray-600 mt-2">Posodobi svoje osebne podatke</p>
    </header>
    <section class="form-section bg-white p-8 rounded-xl shadow-lg max-w-lg">
      <form id="profilForm" action="/api/ucenec/profil" method="POST" class="space-y-5">
        <div>
          <label for="ime" class="block text-sm font-medium text-gray-700"><i class="fas fa-user mr-2"></i> Ime</label>
          <input type="text" id="ime" name="ime" required class="mt-1 w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label for="priimek" class="block text-sm font-medium text-gray-700"><i class="fas fa-user mr-2"></i> Priimek</label>
          <input type="text" id="priimek" name="priimek" required class="mt-1 w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700"><i class="fas fa-envelope mr-2"></i> Email</label>
          <input type="email" id="email" name="email" required class="mt-1 w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label for="geslo" class="block text-sm font-medium text-gray-700"><i class="fas fa-lock mr-2"></i> Geslo</label>
          <input type="password" id="geslo" name="geslo" required class="mt-1 w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="btn w-full py-3 bg-blue-600 text-white rounded-lg"><i class="fas fa-save mr-2"></i> Shrani profil</button>
      </form>
    </section>
  </main>

  <script>
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      const sidebar = document.querySelector('.sidebar');
      sidebar.classList.toggle('collapsed');
      document.querySelector('main').classList.toggle('ml-64');
      document.querySelector('main').classList.toggle('ml-20');
    });

    document.addEventListener('DOMContentLoaded', async () => {
      try {
        const response = await fetch('../api/ucenec_profil.php');
        const data = await response.json();
        if (data.success) {
          document.getElementById('ime').value = data.ime;
          document.getElementById('priimek').value = data.priimek;
          document.getElementById('email').value = data.email;
        } else {
          alert('Napaka pri nalaganju profila: ' + data.message);
        }
      } catch (error) {
        alert('Napaka pri nalaganju profila: ' + error.message);
      }
    });

    document.getElementById('profilForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const data = Object.fromEntries(formData);
      try {
        const response = await fetch('../api/ucenec_profil.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data),
        });
        const result = await response.json();
        if (result.success) {
          alert('Profil posodobljen!');
        } else {
          alert('Napaka pri posodabljanju profila: ' + result.message);
        }
      } catch (error) {
        alert('Napaka: ' + error.message);
      }
    });
  </script>
</body>
</html>