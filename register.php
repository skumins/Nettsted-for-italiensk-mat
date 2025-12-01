<?php
require_once 'config.php';
$message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = 'user';

    if(empty($username) || empty($email) || empty($password)) {
        $message = "Vennligst fyll ut alle feltene";
    } elseif ($password != $password_confirm) {
        $message = "Passordene stemmer ikke overens.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try{
            $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
            
            $stmt = $tilkobling->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
            
            if($stmt->execute()) {
                $message = "Du er nå registrert! <a href='login.php'>Logg inn her</a>.";
            }

            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if($e->getCode() == 1062) {
                $message = "Brukernavnet eller e-posten er allerede registrert.";
            } else{
                $message = "En feil oppstod: " . $e->getMessage();
            }
        }
    }
}
?>

<?php include 'header.php'; ?>

<main class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <div class="page-header">
        <h1>Registrering</h1>
        <p>Opprett en konto for å registrere deg for lunsj</p>
    </div>

    <form action="register.php" method="POST" class="registration-form">
        
        <?php if ($message): ?>
            <div class="form-message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="username">Brukernavn:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">E-post:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Passord:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Bekreft Passord:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <div class="form-group">
            <button type="submit" class="cta-button_1">Registrer deg</button>
        </div>
        <div style="text-align: center; margin-top: 1rem;">
            <p>Har du allerede en konto? <a href="login.php" style = "color: var(--green);">Logg inn her</a></p>
        </div>
    </form>
</main>

<?php include 'footer.php'; ?>