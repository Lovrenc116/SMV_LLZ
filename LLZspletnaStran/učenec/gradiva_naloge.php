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
  <title>Gradiva in naloge - LLZ spletna učilnica</title>
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
      <a href="predmeti.php" class="flex items-center p-4 rounded-lg font-semibold">
        <i class="fas fa-book mr-3 text-xl"></i> <span class="nav-text">Predmeti</span>
      </a>
      <a href="gradiva_naloge.php" class="active flex items-center p-4 rounded-lg font-semibold">
        <i class="fas fa-folder-open mr-3 text-xl"></i> <span class="nav-text">Gradiva in naloge</span>
      </a>
      <a href="../api/logout.php" class="flex items-center p-4 rounded-lg font-semibold">
        <i class="fas fa-sign-out-alt mr-3 text-xl"></i> <span class="nav-text">Odjava</span>
      </a>
    </nav>
  </aside>

  <main class="flex-1 ml-64 p-8">
    <header class="mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Gradiva in naloge</h1>
      <p class="text-gray-600 mt-2">Ogled gradiv in oddaja nalog za tvoje predmete</p>
    </header>
    <section class="form-section bg-white p-8 rounded-xl shadow-lg max-w-2xl">
      <div>
        <label for="predmetSelect" class="block text-sm font-medium text-gray-700"><i class="fas fa-book mr-2"></i> Izberi predmet</label>
        <select id="predmetSelect" onchange="prikaziGradivaNaloge()" class="mt-1 w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
          <option value="">Izberi...</option>
          <!-- Dinamično polnjenje prek JavaScript -->
        </select>
      </div>
      <div id="gradivaSection" class="mt-6 hidden">
        <h3 class="text-xl font-semibold text-gray-800">Gradiva</h3>
        <ul id="gradivaList" class="mt-4 space-y-3"></ul>
      </div>
      <div id="nalogeSection" class="mt-6 hidden">
        <h3 class="text-xl font-semibold text-gray-800">Naloge</h3>
        <ul id="nalogeList" class="mt-4 space-y-3"></ul>
        <form id="nalogaForm" enctype="multipart/form-data" class="mt-6 space-y-4">
          <div>
            <label for="naslovNaloge" class="block text-sm font-medium text-gray-700"><i class="fas fa-file-alt mr-2"></i> Naslov naloge</label>
            <input type="text" id="naslovNaloge" name="naslov_naloge" required class="mt-1 w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label for="datoteka" class="block text-sm font-medium text-gray-700"><i class="fas fa-upload mr-2"></i> Izberi datoteko</label>
            <input type="file" id="datoteka" name="datoteka" required class="mt-1 w-full p-3 border rounded-lg">
          </div>
          <button type="submit" class="btn w-full py-3 bg-blue-600 text-white rounded-lg"><i class="fas fa-upload mr-2"></i> Oddaj nalogo</button>
        </form>
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

    async function naloziPredmete() {
      try {
        const response = await fetch('../api/ucenec_predmeti.php');
        const data = await response.json();
        const select = document.getElementById('predmetSelect');
        
        if (data.success && data.predmeti && data.predmeti.length > 0) {
          select.innerHTML = '<option value="">Izberi...</option>' + 
            data.predmeti.map(p => `<option value="${p.id}">${p.naziv_predmeta}</option>`).join('');
        } else {
          select.innerHTML = '<option value="">Nimaš dodeljenih predmetov</option>';
          console.warn('Učenec nima dodeljenih predmetov.');
        }
      } catch (error) {
        console.error('Napaka pri nalaganju predmetov:', error);
        const select = document.getElementById('predmetSelect');
        select.innerHTML = '<option value="">Napaka pri nalaganju predmetov</option>';
      }
    }

    async function prikaziGradivaNaloge() {
      const select = document.getElementById('predmetSelect');
      const gradivaSection = document.getElementById('gradivaSection');
      const nalogeSection = document.getElementById('nalogeSection');
      const gradivaList = document.getElementById('gradivaList');
      const nalogeList = document.getElementById('nalogeList');

      if (select.value) {
        gradivaSection.classList.remove('hidden');
        nalogeSection.classList.remove('hidden');
        try {
          const [gradivaResponse, nalogeResponse] = await Promise.all([
            fetch(`../api/gradiva.php?predmet=${select.value}`),
            fetch(`../api/naloge.php?predmet=${select.value}`)
          ]);
          const gradiva = await gradivaResponse.json();
          const naloge = await nalogeResponse.json();

          const gradivaSeznam = gradiva.gradiva || gradiva || [];
          const nalogeSeznam = naloge.naloge || naloge || [];

          gradivaList.innerHTML = gradivaSeznam.length
            ? gradivaSeznam.map(g => `<li class="text-gray-700 bg-gray-100 p-3 rounded-lg flex items-center"><i class="fas fa-file-alt text-blue-600 mr-2"></i> ${g.naziv_gradiva || g.naziv || g.ime} <a href="../uploads/${g.datoteka}" class="ml-auto text-blue-600 hover:underline" download>Prenesi</a></li>`).join('')
            : '<li class="text-gray-500">Ni gradiv.</li>';

          nalogeList.innerHTML = nalogeSeznam.length
            ? nalogeSeznam.map(n => `<li class="text-gray-700 bg-gray-100 p-3 rounded-lg flex items-center"><i class="fas fa-tasks text-blue-600 mr-2"></i> ${n.naslov_naloge || n.naslov} <span class="ml-auto text-gray-600">${n.datum_oddaje ? 'Oddano: ' + new Date(n.datum_oddaje).toLocaleDateString('sl-SI') : 'Ni oddano'}</span></li>`).join('')
            : '<li class="text-gray-500">Ni nalog.</li>';
        } catch (error) {
          gradivaList.innerHTML = '<li class="text-red-500">Napaka pri nalaganju gradiv.</li>';
          nalogeList.innerHTML = '<li class="text-red-500">Napaka pri nalaganju nalog.</li>';
        }
      } else {
        gradivaSection.classList.add('hidden');
        nalogeSection.classList.add('hidden');
      }
    }

    document.getElementById('nalogaForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const predmetId = document.getElementById('predmetSelect').value;
      if (!predmetId) {
        alert('Prosimo, izberite predmet.');
        return;
      }
      const formData = new FormData(e.target);
      formData.append('predmet_id', predmetId);

      // Preveri, ali naloga že obstaja
      const naslov = formData.get('naslov_naloge');
      try {
        const checkResponse = await fetch(`../api/naloge_check.php?predmet=${predmetId}&naslov=${encodeURIComponent(naslov)}`);
        const checkResult = await checkResponse.json();
        if (checkResult.exists && !confirm('Naloga že obstaja. Želite prepisati?')) {
          return;
        }

        const response = await fetch('../api/naloge_oddaj.php', {
          method: 'POST',
          body: formData,
        });
        const result = await response.json();
        if (result.success) {
          alert('Naloga uspešno oddana!');
          e.target.reset();
          prikaziGradivaNaloge();
        } else {
          alert('Napaka pri oddaji naloge: ' + result.message);
        }
      } catch (error) {
        alert('Napaka: ' + error.message);
      }
    });

    document.addEventListener('DOMContentLoaded', naloziPredmete);
  </script>
</body>
</html>