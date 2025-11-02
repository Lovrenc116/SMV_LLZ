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
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
            background: linear-gradient(-45deg, #667eea, #764ba2, #f77062, #43cea2);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
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
        input, select {
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 16px;
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
        th, td {
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
                <button type="submit" id="submitPredmetBtn">Dodaj predmet</button>
                <button type="button" id="cancelEditBtn" style="display:none; background: #dc3545; margin-left: 10px;" onclick="cancelEditPredmet()">Prekliči</button>
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
                <input type="password" name="geslo_ucitelja" id="gesloUcitelja" placeholder="Geslo učitelja" required>
                <input type="text" name="razred_ucitelja" id="razredUcitelja" placeholder="Razred (npr. 1A, 2B) - neobvezno">
                <select multiple name="predmeti" id="predmetiUcitelja">
                    <!-- Predmeti se naložijo preko JavaScript-a -->
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
                        <th>Razred</th>
                        <th>Predmeti</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Upravljanje razredov -->
        <div class="section">
            <h2>Upravljanje razredov</h2>
            <div style="margin-bottom: 20px;">
                <label for="razred-filter" style="display: block; margin-bottom: 10px; font-weight: 600;">Filtriraj po razredu:</label>
                <select id="razred-filter" style="padding: 10px; border-radius: 10px; border: 1px solid #ddd; font-size: 16px; width: 200px;">
                    <option value="">Vsi razredi</option>
                </select>
            </div>
            <table id="razrediTable">
                <thead>
                    <tr>
                        <th>Razred</th>
                        <th>Število učencev</th>
                        <th>Učenci</th>
                        <th>Učitelji in predmeti</th>
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
                <input type="password" name="geslo_ucenca" id="gesloUcenca" placeholder="Geslo učenca" required>
                <input type="text" name="razred_ucenca" id="razredUcenca" placeholder="Razred (npr. 1A)" required>
                <select multiple name="predmeti" id="predmetiUcenca">
                    <!-- Predmeti se naložijo preko JavaScript-a -->
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
                        <button onclick="editPredmet(${predmet.id}, ${JSON.stringify(predmet.naziv_predmeta)}, ${JSON.stringify(predmet.kratica)})">Uredi</button>
                        <button onclick="deletePredmet(${predmet.id})">Izbriši</button>
                    </td>
                `;
            });
            // Osveži select elemente
            updatePredmetiSelects(predmeti);
        }

        function updatePredmetiSelects(predmeti) {
            // Osveži select za učitelje
            const predmetiUcitelja = document.getElementById('predmetiUcitelja');
            const selectedUcitelja = Array.from(predmetiUcitelja.selectedOptions).map(opt => opt.value);
            predmetiUcitelja.innerHTML = '';
            predmeti.forEach(predmet => {
                const option = document.createElement('option');
                option.value = predmet.id;
                option.textContent = predmet.naziv_predmeta;
                if (selectedUcitelja.includes(predmet.id.toString())) {
                    option.selected = true;
                }
                predmetiUcitelja.appendChild(option);
            });

            // Osveži select za učence
            const predmetiUcenca = document.getElementById('predmetiUcenca');
            const selectedUcenca = Array.from(predmetiUcenca.selectedOptions).map(opt => opt.value);
            predmetiUcenca.innerHTML = '';
            predmeti.forEach(predmet => {
                const option = document.createElement('option');
                option.value = predmet.id;
                option.textContent = predmet.naziv_predmeta;
                if (selectedUcenca.includes(predmet.id.toString())) {
                    option.selected = true;
                }
                predmetiUcenca.appendChild(option);
            });
        }

        predmetForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(predmetForm);
            const data = {};
            
            // Pravilno obdelaj podatke iz forme
            for (const [key, value] of formData.entries()) {
                if (key === 'id') {
                    // ID samo če ni prazen
                    if (value && value !== '') {
                        data[key] = parseInt(value);
                    }
                } else {
                    data[key] = value;
                }
            }
            
            try {
                const response = await fetch('api/predmet.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                
                if (result.success) {
                    // Resetiraj formo in osveži seznam
                    predmetForm.reset();
                    document.getElementById('predmetId').value = '';
                    document.getElementById('submitPredmetBtn').textContent = 'Dodaj predmet';
                    document.getElementById('cancelEditBtn').style.display = 'none';
                    await fetchPredmeti();
                } else {
                    alert('Napaka: ' + (result.message || 'Neznana napaka'));
                }
            } catch (error) {
                alert('Napaka pri pošiljanju: ' + error.message);
            }
        });

        async function editPredmet(id, naziv, kratica) {
            document.getElementById('predmetId').value = id;
            document.getElementById('nazivPredmeta').value = naziv;
            document.getElementById('kratica').value = kratica;
            
            // Spremeni gumb in prikaži gumb za preklic
            document.getElementById('submitPredmetBtn').textContent = 'Shrani spremembe';
            document.getElementById('cancelEditBtn').style.display = 'inline-block';
            
            // Premakni fokus na formo za lažje urejanje
            document.getElementById('nazivPredmeta').focus();
        }

        function cancelEditPredmet() {
            // Resetiraj formo
            predmetForm.reset();
            document.getElementById('predmetId').value = '';
            document.getElementById('submitPredmetBtn').textContent = 'Dodaj predmet';
            document.getElementById('cancelEditBtn').style.display = 'none';
        }

        async function deletePredmet(id) {
            if (confirm('Ali ste prepričani, da želite izbrisati predmet?')) {
                await fetch('api/predmet.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                await fetchPredmeti();
                // Osveži tudi podatke učiteljev in učencev, ker se morda spremenijo predmeti
                await fetchUcitelji();
                await fetchUcenci();
                fetchRazredi();
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
                    <td>${ucitelj.razred || 'Ni dodeljen'}</td>
                    <td>${ucitelj.predmeti || 'Brez predmetov'}</td>
                    <td class="action-buttons">
                        <button onclick="editUcitelj(${ucitelj.id}, ${JSON.stringify(ucitelj.ime)}, ${JSON.stringify(ucitelj.priimek)}, ${JSON.stringify(ucitelj.email)}, ${JSON.stringify(ucitelj.razred || '')})">Uredi</button>
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
            await fetch('api/ucitelj.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            uciteljForm.reset();
            await fetchUcitelji();
            fetchRazredi();
        });

        async function editUcitelj(id, ime, priimek, email, razred) {
            document.getElementById('uciteljId').value = id;
            document.getElementById('imeUcitelja').value = ime;
            document.getElementById('priimekUcitelja').value = priimek;
            document.getElementById('emailUcitelja').value = email;
            document.getElementById('razredUcitelja').value = razred || '';
            document.getElementById('gesloUcitelja').value = '';
        }

        async function deleteUcitelj(id) {
            if (confirm('Ali ste prepričani, da želite izbrisati učitelja?')) {
                await fetch('api/ucitelj.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                await fetchUcitelji();
                fetchRazredi();
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
            await fetch('api/ucenec.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            ucenecForm.reset();
            await fetchUcenci();
            fetchRazredi();
        });

        async function editUcenec(id, ime, priimek, email, razred) {
            document.getElementById('ucenecId').value = id;
            document.getElementById('imeUcenca').value = ime;
            document.getElementById('priimekUcenca').value = priimek;
            document.getElementById('emailUcenca').value = email;
            document.getElementById('razredUcenca').value = razred;
            document.getElementById('gesloUcenca').value = '';
        }

        async function deleteUcenec(id) {
            if (confirm('Ali ste prepričani, da želite izbrisati učenca?')) {
                await fetch('api/ucenec.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                await fetchUcenci();
                fetchRazredi();
            }
        }

        // Upravljanje razredov
        const razrediTable = document.getElementById('razrediTable').getElementsByTagName('tbody')[0];
        const razredFilter = document.getElementById('razred-filter');

        async function fetchRazredi() {
            try {
                // Pridobi vse učence z njihovimi razredi
                const response = await fetch('api/ucenec.php');
                const ucenci = await response.json();
                
                // Grupiraj učence po razredih
                const razrediMap = {};
                ucenci.forEach(ucenec => {
                    const razred = ucenec.razred || 'Nedoločen';
                    if (!razrediMap[razred]) {
                        razrediMap[razred] = [];
                    }
                    razrediMap[razred].push(ucenec);
                });

                // Osveži filter za razrede
                const razredi = Object.keys(razrediMap).sort();
                const currentFilter = razredFilter.value;
                razredFilter.innerHTML = '<option value="">Vsi razredi</option>';
                razredi.forEach(razred => {
                    const option = document.createElement('option');
                    option.value = razred;
                    option.textContent = razred;
                    razredFilter.appendChild(option);
                });
                if (currentFilter && razredi.includes(currentFilter)) {
                    razredFilter.value = currentFilter;
                }

                // Pridobi vse učitelje z njihovimi predmeti
                const uciteljiResponse = await fetch('api/ucitelj.php');
                const ucitelji = await uciteljiResponse.json();

                // Pridobi predmete za vsak razred (preko učencev v razredu)
                razrediTable.innerHTML = '';
                const filteredRazred = razredFilter.value;
                
                for (const razred of razredi) {
                    if (filteredRazred && razred !== filteredRazred) continue;
                    
                    const ucenciVRazredu = razrediMap[razred];
                    
                    // Pridobi vse predmete, ki jih imajo učenci v tem razredu
                    const predmetiSet = new Set();
                    ucenciVRazredu.forEach(ucenec => {
                        if (ucenec.predmeti) {
                            ucenec.predmeti.split(', ').forEach(p => predmetiSet.add(p));
                        }
                    });

                    // Pridobi učitelje za te predmete in tudi tiste, ki so dodeljeni temu razredu
                    const uciteljiVRazredu = [];
                    const predmetUcitelji = {};
                    
                    ucitelji.forEach(ucitelj => {
                        // Učitelj je dodeljen razredu
                        const uciteljRazred = (ucitelj.razred && ucitelj.razred.trim() !== '') ? ucitelj.razred.trim() : null;
                        const jeVRazredu = uciteljRazred !== null && uciteljRazred === razred;
                        
                        // Če je učitelj dodeljen razredu, se prikaže samo pri tem razredu
                        // Če ni dodeljen razredu, se prikaže pri vseh razredih, kjer učenci imajo njegove predmete
                        if (ucitelj.predmeti && ucitelj.predmeti.trim() !== '') {
                            // Razčleni predmete - API vrača z ', ' (presledek po vejici)
                            const predmetiList = ucitelj.predmeti.split(', ').map(p => p.trim()).filter(p => p !== '');
                            predmetiList.forEach(predmet => {
                                // Preveri, ali imajo učenci v tem razredu ta predmet
                                const predmetImajoUcenci = predmetiSet.has(predmet);
                                
                                // Učitelj se prikaže pri predmetu, če:
                                // 1. Predmet imajo učenci v razredu (učitelj poučuje ta predmet)
                                // IN
                                // 2. (Učitelj ni dodeljen nobenemu razredu ALI je dodeljen TEMU razredu)
                                const najSePrikaze = predmetImajoUcenci && (uciteljRazred === null || jeVRazredu);
                                
                                if (najSePrikaze) {
                                    if (!predmetUcitelji[predmet]) {
                                        predmetUcitelji[predmet] = [];
                                    }
                                    const uciteljInfo = `${ucitelj.ime} ${ucitelj.priimek}`;
                                    if (!predmetUcitelji[predmet].includes(uciteljInfo)) {
                                        predmetUcitelji[predmet].push(uciteljInfo);
                                    }
                                }
                            });
                        }
                    });

                    const row = razrediTable.insertRow();
                    const ucenciList = ucenciVRazredu.map(u => `${u.ime} ${u.priimek}`).join(', ');
                    const predmetiUciteljiList = Object.entries(predmetUcitelji)
                        .map(([predmet, uciteljiList]) => `<strong>${predmet}</strong>: ${uciteljiList.join(', ') || 'Brez učitelja'}`)
                        .join('<br>') || 'Ni predmetov';
                    
                    row.innerHTML = `
                        <td><strong>${razred}</strong></td>
                        <td>${ucenciVRazredu.length}</td>
                        <td style="max-width: 300px; font-size: 0.9em;">${ucenciList || 'Ni učencev'}</td>
                        <td style="max-width: 400px; font-size: 0.9em;">${predmetiUciteljiList}</td>
                    `;
                }

                // Če ni razredov
                if (razredi.length === 0) {
                    const row = razrediTable.insertRow();
                    row.innerHTML = `<td colspan="4" style="text-align: center; padding: 2rem; color: #666;">Ni razredov.</td>`;
                }
            } catch (error) {
                console.error('Napaka pri nalaganju razredov:', error);
            }
        }

        // Osveži razrede, ko se spremeni filter
        razredFilter.addEventListener('change', fetchRazredi);

        // Inicializacija
        fetchPredmeti();
        fetchUcitelji();
        fetchUcenci();
        fetchRazredi();
    </script>
</body>
</html>