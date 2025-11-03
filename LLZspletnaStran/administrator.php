<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'administrator') {
    header('Location: login.php');
    exit;
}

// Povezava z bazo za pridobivanje predmetov
try {
    $dbCandidates = ['spletna_ucilnica', 'llzspletnastranbaza'];
    $conn = null; $last = null;
    foreach ($dbCandidates as $dbName) {
        try {
            $tmp = new PDO("mysql:host=localhost;dbname=" . $dbName, "root", "");
            $tmp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn = $tmp; break;
        } catch (PDOException $ex) { $last = $ex; }
    }
    if ($conn === null) { throw $last; }
    $stmt = $conn->prepare("SELECT id, naziv_predmeta FROM predmeti");
    $stmt->execute();
    $predmeti = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $predmeti = [];
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administratorski vmesnik - LLZ spletna učilnica</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
        }
        
        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #764ba2;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 15px;
        }
        
        form {
            display: grid;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        input,
        select {
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        
        select[multiple] {
            min-height: 150px;
            padding: 8px;
            background-color: #fff;
            border: 2px solid #764ba2;
            border-radius: 8px;
        }
        
        select[multiple] option {
            padding: 8px 12px;
            margin: 2px 0;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        select[multiple] option:hover {
            background-color: #f0e6ff;
        }
        
        select[multiple] option:checked {
            background-color: #764ba2;
            color: #fff;
        }
        
        button {
            padding: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        
        button:hover {
            background: linear-gradient(135deg, #764ba2, #667eea);
            transform: translateY(-2px);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #764ba2;
            color: #fff;
        }
        
        tr:hover {
            background: #f5f5f5;
        }
        
        .action-buttons button {
            margin-right: 10px;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 15px;
            }
            form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Administratorski vmesnik</h1>
        <a href="api/logout.php" style="color: #764ba2; text-decoration: none; float: right;">Odjava</a>

        <!-- Upravljanje predmetov -->
        <div class="section">
            <h2>Upravljanje predmetov</h2>
            <form id="predmetForm">
                <input type="hidden" name="id" id="predmetId">
                <input type="text" name="naziv_predmeta" id="nazivPredmeta" placeholder="Naziv predmeta" required>
                <input type="text" name="kratica" id="kratica" placeholder="Kratica" required>
                <button type="submit">Dodaj/Uredi predmet</button>
            </form>
            <table id="predmetiTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naziv</th>
                        <th>Kratica</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Upravljanje učiteljev -->
        <div class="section">
            <h2>Upravljanje učiteljev</h2>
            <form id="uciteljForm">
                <input type="hidden" name="id" id="uciteljId">
                <input type="text" name="ime_ucitelja" id="imeUcitelja" placeholder="Ime učitelja" required>
                <input type="text" name="priimek_ucitelja" id="priimekUcitelja" placeholder="Priimek učitelja" required>
                <input type="email" name="email_ucitelja" id="emailUcitelja" placeholder="Email učitelja" required>
                <input type="password" name="geslo_ucitelja" id="gesloUcitelja" placeholder="Geslo učitelja (pusti prazno pri urejanju)" required>
                <label for="predmetiUcitelja" style="font-weight: 600; color: #333;">Predmeti (drži Ctrl/Cmd za izbiro več predmetov):</label>
                <select multiple name="predmeti" id="predmetiUcitelja" title="Drži Ctrl ali Cmd in klikni za izbiro več predmetov">
                    <?php foreach ($predmeti as $predmet): ?>
                        <option value="<?php echo $predmet['id']; ?>"><?php echo htmlspecialchars($predmet['naziv_predmeta']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Dodaj/Uredi učitelja</button>
            </form>
            <table id="uciteljiTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ime</th>
                        <th>Priimek</th>
                        <th>Email</th>
                        <th>Predmeti</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Upravljanje učencev -->
        <div class="section">
            <h2>Upravljanje učencev</h2>
            <form id="ucenecForm">
                <input type="hidden" name="id" id="ucenecId">
                <input type="text" name="ime_ucenca" id="imeUcenca" placeholder="Ime učenca" required>
                <input type="text" name="priimek_ucenca" id="priimekUcenca" placeholder="Priimek učenca" required>
                <input type="email" name="email_ucenca" id="emailUcenca" placeholder="Email učenca" required>
                <input type="password" name="geslo_ucenca" id="gesloUcenca" placeholder="Geslo učenca (pusti prazno pri urejanju)" required>
                <input type="text" name="razred_ucenca" id="razredUcenca" placeholder="Razred (npr. 1A)" required>
                <label for="predmetiUcenca" style="font-weight: 600; color: #333;">Predmeti (drži Ctrl/Cmd za izbiro več predmetov):</label>
                <select multiple name="predmeti" id="predmetiUcenca" title="Drži Ctrl ali Cmd in klikni za izbiro več predmetov">
                    <?php foreach ($predmeti as $predmet): ?>
                        <option value="<?php echo $predmet['id']; ?>"><?php echo htmlspecialchars($predmet['naziv_predmeta']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Dodaj/Uredi učenca</button>
            </form>
            <table id="ucenciTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ime</th>
                        <th>Priimek</th>
                        <th>Email</th>
                        <th>Razred</th>
                        <th>Predmeti</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script>
        // Upravljanje predmetov
        const predmetForm = document.getElementById('predmetForm');
        const predmetiTable = document.getElementById('predmetiTable').getElementsByTagName('tbody')[0];

        async function fetchPredmeti() {
            const response = await fetch('api/predmet.php');
            const predmeti = await response.json();
            predmetiTable.innerHTML = '';
            predmeti.forEach(predmet => {
                const row = predmetiTable.insertRow();
                row.innerHTML = `
                    <td>${predmet.id}</td>
                    <td>${predmet.naziv_predmeta}</td>
                    <td>${predmet.kratica}</td>
                    <td class="action-buttons">
                        <button onclick="editPredmet(${predmet.id}, '${predmet.naziv_predmeta}', '${predmet.kratica}')">Uredi</button>
                        <button onclick="deletePredmet(${predmet.id})">Izbriši</button>
                    </td>
                `;
            });
        }

        predmetForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(predmetForm);
            const data = Object.fromEntries(formData);
            await fetch('api/predmet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            predmetForm.reset();
            fetchPredmeti();
        });

        async function editPredmet(id, naziv, kratica) {
            document.getElementById('predmetId').value = id;
            document.getElementById('nazivPredmeta').value = naziv;
            document.getElementById('kratica').value = kratica;
        }

        async function deletePredmet(id) {
            if (confirm('Ali ste prepričani, da želite izbrisati predmet?')) {
                await fetch('api/predmet.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                fetchPredmeti();
            }
        }

        // Upravljanje učiteljev
        const uciteljForm = document.getElementById('uciteljForm');
        const uciteljiTable = document.getElementById('uciteljiTable').getElementsByTagName('tbody')[0];

        async function fetchUcitelji() {
            const response = await fetch('api/ucitelj.php');
            const ucitelji = await response.json();
            uciteljiTable.innerHTML = '';
            ucitelji.forEach(ucitelj => {
                const row = uciteljiTable.insertRow();
                row.innerHTML = `
                    <td>${ucitelj.id}</td>
                    <td>${ucitelj.ime}</td>
                    <td>${ucitelj.priimek}</td>
                    <td>${ucitelj.email}</td>
                    <td>${ucitelj.predmeti || 'Brez predmetov'}</td>
                    <td class="action-buttons">
                        <button onclick="editUcitelj(${ucitelj.id}, '${ucitelj.ime}', '${ucitelj.priimek}', '${ucitelj.email}')">Uredi</button>
                        <button onclick="deleteUcitelj(${ucitelj.id})">Izbriši</button>
                    </td>
                `;
            });
        }

        uciteljForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(uciteljForm);
            const predmeti = Array.from(document.getElementById('predmetiUcitelja').selectedOptions).map(option => option.value);
            const data = { ...Object.fromEntries(formData), predmeti };
            
            // Če je to urejanje, geslo ni obvezno
            if (document.getElementById('uciteljId').value && !data.geslo_ucitelja) {
                delete data.geslo_ucitelja;
            }
            
            await fetch('api/ucitelj.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            uciteljForm.reset();
            document.getElementById('uciteljId').value = '';
            document.getElementById('gesloUcitelja').required = true;
            fetchUcitelji();
        });

        async function editUcitelj(id, ime, priimek, email) {
            document.getElementById('uciteljId').value = id;
            document.getElementById('imeUcitelja').value = ime;
            document.getElementById('priimekUcitelja').value = priimek;
            document.getElementById('emailUcitelja').value = email;
            document.getElementById('gesloUcitelja').value = '';
            document.getElementById('gesloUcitelja').required = false;
            
            // Naloži predmete učitelja
            try {
                const response = await fetch(`api/ucitelj.php?id=${id}`);
                const data = await response.json();
                const predmetiSelect = document.getElementById('predmetiUcitelja');
                
                // Počisti vse izbire
                Array.from(predmetiSelect.options).forEach(option => {
                    option.selected = false;
                });
                
                // Izberi predmete učitelja
                if (data.success && data.predmeti) {
                    data.predmeti.forEach(predmetId => {
                        const option = predmetiSelect.querySelector(`option[value="${predmetId}"]`);
                        if (option) {
                            option.selected = true;
                        }
                    });
                }
            } catch (error) {
                console.error('Napaka pri nalaganju predmetov učitelja:', error);
            }
        }

        async function deleteUcitelj(id) {
            if (confirm('Ali ste prepričani, da želite izbrisati učitelja?')) {
                await fetch('api/ucitelj.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                fetchUcitelji();
            }
        }

        // Upravljanje učencev
        const ucenecForm = document.getElementById('ucenecForm');
        const ucenciTable = document.getElementById('ucenciTable').getElementsByTagName('tbody')[0];

        async function fetchUcenci() {
            const response = await fetch('api/ucenec.php');
            const ucenci = await response.json();
            ucenciTable.innerHTML = '';
            ucenci.forEach(ucenec => {
                const row = ucenciTable.insertRow();
                row.innerHTML = `
                    <td>${ucenec.id}</td>
                    <td>${ucenec.ime}</td>
                    <td>${ucenec.priimek}</td>
                    <td>${ucenec.email}</td>
                    <td>${ucenec.razred}</td>
                    <td>${ucenec.predmeti || 'Brez predmetov'}</td>
                    <td class="action-buttons">
                        <button onclick="editUcenec(${ucenec.id}, '${ucenec.ime}', '${ucenec.priimek}', '${ucenec.email}', '${ucenec.razred}')">Uredi</button>
                        <button onclick="deleteUcenec(${ucenec.id})">Izbriši</button>
                    </td>
                `;
            });
        }

        ucenecForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(ucenecForm);
            const predmeti = Array.from(document.getElementById('predmetiUcenca').selectedOptions).map(option => option.value);
            const data = { ...Object.fromEntries(formData), predmeti };
            
            // Če je to urejanje, geslo ni obvezno
            if (document.getElementById('ucenecId').value && !data.geslo_ucenca) {
                delete data.geslo_ucenca;
            }
            
            await fetch('api/ucenec.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            ucenecForm.reset();
            document.getElementById('ucenecId').value = '';
            document.getElementById('gesloUcenca').required = true;
            fetchUcenci();
        });

        async function editUcenec(id, ime, priimek, email, razred) {
            document.getElementById('ucenecId').value = id;
            document.getElementById('imeUcenca').value = ime;
            document.getElementById('priimekUcenca').value = priimek;
            document.getElementById('emailUcenca').value = email;
            document.getElementById('razredUcenca').value = razred;
            document.getElementById('gesloUcenca').value = '';
            document.getElementById('gesloUcenca').required = false;
            
            // Naloži predmete učenca
            try {
                const response = await fetch(`api/ucenec.php?id=${id}`);
                const data = await response.json();
                const predmetiSelect = document.getElementById('predmetiUcenca');
                
                // Počisti vse izbire
                Array.from(predmetiSelect.options).forEach(option => {
                    option.selected = false;
                });
                
                // Izberi predmete učenca
                if (data.success && data.predmeti) {
                    data.predmeti.forEach(predmetId => {
                        const option = predmetiSelect.querySelector(`option[value="${predmetId}"]`);
                        if (option) {
                            option.selected = true;
                        }
                    });
                }
            } catch (error) {
                console.error('Napaka pri nalaganju predmetov učenca:', error);
            }
        }

        async function deleteUcenec(id) {
            if (confirm('Ali ste prepričani, da želite izbrisati učenca?')) {
                await fetch('api/ucenec.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                fetchUcenci();
            }
        }

        // Inicializacija
        fetchPredmeti();
        fetchUcitelji();
        fetchUcenci();
    </script>
</body>
</html>