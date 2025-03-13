<?php 
  session_start();
  if(isset($_SESSION['unique_id'])){
    header("location: users.php");
  }
?>

<?php include_once "header.php"; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MagroChat - Signup</title>
    <link rel="stylesheet" href="style.css" />
	 <link rel="stylesheet" href="cookie-style.css" />
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
    <script src="script.js" defer></script>
  </head>
  <body>
    <div class="wrapper">
      <section class="form signup">
        <header>MagroChat</header>
        <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
          <div class="error-text"></div>
          <div class="name-details">
            <div class="field input">
              <label>Vorname</label>
              <input type="text" name="fname" placeholder="Max" required>
            </div>
            <div class="field input">
              <label>Nachname</label>
              <input type="text" name="lname" placeholder="Mustermann" required>
            </div>
          </div>
          <div class="field input">
            <label>Email Adresse</label>
            <input type="text" name="email" placeholder="max.muster@gmail.com" required>
          </div>
          <div class="field input">
            <label>Password</label>
            <input type="password" name="password" placeholder="EinDummesPasswort1234" required>
            <i class="fas fa-eye"></i>
          </div>
          <div class="field image">
            <label>Wähle dein Profilbild</label>
            <input type="file" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
          </div>
          <div class="field button">
            <input type="submit" name="submit" value="Chat Starten">
          </div>
        </form>
        <div class="link">Bist du bereits registriert? <a href="login.php">Jetzt Anmelden</a></div>
      </section>
    </div>

<div class="cookie-wrapper">
        <header>
            <i class="bx bx-cookie"></i>
            <h2>Cookies Zustimmung</h2>
        </header>
        <div class="data">
            <p>Diese Website nutzt Cookies für eine bessere Erfahrung. <a href="#">Mehr erfahren...</a></p>
        </div>
        <div class="buttons">
            <button class="button" id="acceptBtn">Akzeptieren</button>
            <button class="button" id="declineBtn">Ablehnen</button>
        </div>
    </div>

    <script src="javascript/pass-show-hide.js"></script>
    <script src="javascript/signup.js"></script>
    <script src="javascript/cookie-consent.js"></script>
  </body>
</html>