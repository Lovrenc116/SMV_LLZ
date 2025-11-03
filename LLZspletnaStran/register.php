<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['vloga'] === 'ucenec' ? 'učenec/ucenec.php' : ($_SESSION['vloga'] === 'ucitelj' ? 'učitelj/ucitelj.php' : 'administrator.php')));
    exit;
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registracija - LLZ spletna učilnica</title>
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
    .register-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 50px 40px;
      border-radius: 20px;
      width: 380px;
      text-align: center;
      box-shadow: 0 15px 40px rgba(0,0,0,0.25);
      transition: transform 0.3s ease;
    }
    .register-container:hover {
      transform: translateY(-5px);
    }
    .register-container h1 {
      font-size: 26px;
      font-weight: 700;
      color: #764ba2;
      margin-bottom: 10px;
    }
    .register-container h2 {
      color: #333;
      margin-bottom: 25px;
      font-weight: 500;
    }
    .register-container input {
      width: 100%;
      padding: 15px;
      margin: 12px 0;
      border-radius: 10px;
      border: 1px solid #ddd;
      font-size: 16px;
      transition: border 0.3s ease;
    }
    .register-container input:focus {
      border-color: #764ba2;
      outline: none;
    }
    .register-container button {
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
    .register-container button:hover {
      background: linear-gradient(135deg, #764ba2, #667eea);
      transform: translateY(-2px);
    }
    .register-container p {
      margin-top: 18px;
      font-size: 14px;
      color: #666;
    }
    .register-container a {
      color: #764ba2;
      text-decoration: none;
      font-weight: 500;
    }
    .register-container a:hover {
      text-decoration: underline;
    }
    @media (max-width: 420px) {
      .register-container {
        width: 90%;
        padding: 40px 20px;
      }
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h1>LLZ spletna učilnica</h1>
    <h2>Registracija</h2>
    <form id="registerForm" action="api/register.php" method="POST">
      <input type="text" name="ime" placeholder="Ime" required>
      <input type="text" name="priimek" placeholder="Priimek" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="geslo" placeholder="Geslo" required>
      <input type="text" name="razred" placeholder="Razred (npr. 1A)" required>
      <button type="submit">Registriraj se</button>
    </form>
    <p>Že imaš račun? <a href="login.php">Prijavi se</a></p>
  </div>

  <script>
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      try {
        const response = await fetch('api/register.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        if (result.success) {
          alert('Registracija uspešna! Prosimo, prijavite se.');
          window.location.href = 'login.php';
        } else {
          alert('Napaka pri registraciji: ' + result.message);
        }
      } catch (error) {
        alert('Napaka pri registraciji: ' + error.message);
      }
    });
  </script>
</body>
</html>