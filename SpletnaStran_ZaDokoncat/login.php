<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Preusmeri glede na vlogo, če je uporabnik že prijavljen
    switch ($_SESSION['vloga']) {
        case 'ucenec':
            header('Location: učenec/ucenec.php');
            exit;
        case 'ucitelj':
            header('Location: učitelj/ucitelj.php');
            exit;
        case 'administrator':
            header('Location: administrator.php');
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LLZ spletna učilnica - Prijava</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Roboto', sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(-45deg, #667eea, #764ba2, #f77062, #43cea2);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
    }
    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .login-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 50px 40px;
      border-radius: 20px;
      width: 380px;
      text-align: center;
      box-shadow: 0 15px 40px rgba(0,0,0,0.25);
      transition: transform 0.3s ease;
    }
    .login-container:hover {
      transform: translateY(-5px);
    }
    .login-container h1 {
      font-size: 26px;
      font-weight: 700;
      color: #764ba2;
      margin-bottom: 10px;
    }
    .login-container h2 {
      color: #333;
      margin-bottom: 25px;
      font-weight: 500;
    }
    .login-container input, .login-container select {
      width: 100%;
      padding: 15px;
      margin: 12px 0;
      border-radius: 10px;
      border: 1px solid #ddd;
      font-size: 16px;
      transition: border 0.3s ease;
    }
    .login-container input:focus, .login-container select:focus {
      border-color: #764ba2;
      outline: none;
    }
    .login-container button {
      width: 100%;
      padding: 15px;
      margin-top: 20px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      border: none;
      border-radius: 12px;
      color: #fff;
      font-size: 18px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }
    .login-container button:hover {
      background: linear-gradient(135deg, #764ba2, #667eea);
      transform: translateY(-2px);
    }
    .login-container p {
      margin-top: 18px;
      font-size: 14px;
      color: #666;
    }
    .login-container a {
      color: #764ba2;
      text-decoration: none;
      font-weight: 500;
    }
    .login-container a:hover {
      text-decoration: underline;
    }
    @media (max-width: 420px) {
      .login-container {
        width: 90%;
        padding: 40px 20px;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>LLZ spletna učilnica</h1>
    <h2>Prijava v sistem</h2>
    <form id="loginForm" action="api/login.php" method="POST">
      <select name="vloga" required>
        <option value="">Izberi vlogo</option>
        <option value="ucenec">Učenec</option>
        <option value="ucitelj">Učitelj</option>
        <option value="administrator">Administrator</option>
      </select>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="geslo" placeholder="Geslo" required>
      <button type="submit">Prijava</button>
    </form>
    <p>Še nimaš računa? <a href="register.php">Registriraj se</a></p>
  </div>

  <script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      try {
        const response = await fetch('api/login.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        if (result.success) {
          switch (formData.get('vloga')) {
            case 'ucenec':
              window.location.href = 'učenec/ucenec.php';
              break;
            case 'ucitelj':
              window.location.href = 'učitelj/ucitelj.php';
              break;
            case 'administrator':
              window.location.href = 'administrator.php';
              break;
            default:
              alert('Neznana vloga uporabnika.');
          }
        } else {
          alert('Napaka pri prijavi: ' + result.message);
        }
      } catch (error) {
        alert('Napaka pri prijavi: ' + error.message);
      }
    });
  </script>
</body>
</html>