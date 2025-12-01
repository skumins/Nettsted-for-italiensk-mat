<?php require_once 'config.php';
$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT idusers, username, password, role FROM users WHERE email = ?";
    $stmt = $tilkobling->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])){
            $_SESSION['idusers'] = $user['idusers'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit;
        } else{
            $message = 'Feil passord';
        }
    } else {
        $message = 'Feil e-post';
        }
    
        $stmt->close();
    }

?>

<?php include 'header.php'; ?>

<main class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <div class="page-header">
       <h1>Logg inn</h1>
    </div>

    <form action="login.php" method="POST" class="registration-form">
        <?php if ($message): ?>
            <div class="form-message" style="background: var(--red);"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="email">E-post:</label>
            <input type="email" id="email" name="email" required>
        </div>
    
        <div class="form-group">
            <label for="password">Passord:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <button type="submit" class="cta-button_1">Logg inn</button>
        </div>

        <div style="text-align: center; margin-top: 1rem;">
            <p>Har du ikke en konto? <a href="register.php" style="color: var(--green);">Register deg her</a></p>
        </div>
    </form>
</main>
<?php include 'footer.php'; ?>