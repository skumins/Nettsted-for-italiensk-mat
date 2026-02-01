<?php
require_once 'config.php';

if (!isset($_SESSION['idusers'])) {
    header("Location: login.php?message=Du_må_logge_inn_for_å_se_menyen");
    exit;
}


$idusers = $_SESSION['idusers'];
$message = '';

// Cancel Booking (DELETE)
if (isset($_GET['cancel'])) {
    $sql = "DELETE FROM registrations WHERE iduser = ?";
    $stmt = $tilkobling->prepare($sql);
    $stmt->bind_param("i", $idusers);
    if ($stmt->execute()) {
        $message = "Din reservasjon er kansellert.";
    }
    $stmt->close();
    // Refresh page to remove the ?cancel parameter
    header("Location: menu.php");
    exit;
}

// Make Booking (CREATE)
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_lunch'])) {
    $menu_id = $_POST['idmenu']; // The ID of the dish they chose
    
    // Check if user ALREADY has a booking
    $check_sql = "SELECT idreg FROM registrations WHERE iduser = ?";
    $check_stmt = $tilkobling->prepare($check_sql);
    $check_stmt->bind_param("i", $idusers);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows == 0) {
        // If 0 bookings, create a new one
        $sql = "INSERT INTO registrations (iduser, idmenu, regdate) VALUES (?, ?, NOW())";
        $stmt = $tilkobling->prepare($sql);
        $stmt->bind_param("ii", $idusers, $menu_id); // "ii" - два integer
        if ($stmt->execute()) {
            $message = "Du er nå påmeldt lunsjen!";
        }
        $stmt->close();
    } else {
        // User already has a booking
        $message = "Du har allerede en reservasjon denne uken.";
    }
    $check_stmt->close();
    // Refresh page to show the new status
    header("Location: menu.php");
    exit;
}

// Get Current State (READ)
// Do they have an active booking? (This is for |State 2|)
$my_booking = null;
$sql_booking = "SELECT m.day, m.dish, m.description, m.image 
                FROM registrations r
                JOIN menu m ON r.idmenu = m.idmenu
                WHERE r.iduser = ?";
$stmt_booking = $tilkobling->prepare($sql_booking);
$stmt_booking->bind_param("i", $idusers);
$stmt_booking->execute();
$result_booking = $stmt_booking->get_result();
if ($result_booking->num_rows > 0) {
    $my_booking = $result_booking->fetch_assoc(); // Save booking data
}
$stmt_booking->close();


// Get Full Menu (This is for |State 1|)
// Get all menu items, grouped by day
$menu_by_day = []; // Create an empty array
$sql_menu = "SELECT * FROM menu ORDER BY FIELD(day, 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag')";
$menu_result = $tilkobling->query($sql_menu);

// Loop through each row
while ($row = $menu_result->fetch_assoc()) {
    // Add the row to the correct day in our array
    // e.g. $menu_by_day['Mandag'][] = $row;
    $menu_by_day[$row['day']][] = $row;
}

?>

<?php 
include 'header.php'; 
?>

<main class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <div class="page-header">
        <h1>Ukens Meny</h1>
        <p>Registrer deg for lunsj. Du kan kun velge en rett per uke.</p>
    </div>

    <?php if ($message): ?>
        <div class="form-message"><?php echo $message; ?></div>
    <?php endif; ?>


    <?php 
    // HVIS DET FINNES EN BESTILLING    |   Є ЗАМОВЛЕННЯ
    if ($my_booking): 
    ?>
    
    <div class="booking-card">
        <h2>Din Registrerte Lunsj</h2>
        <p>Du er påmeldt lunsj på <strong><?php echo htmlspecialchars($my_booking['day']); ?></strong>.</p>
        <p>Din rett: <strong><?php echo htmlspecialchars($my_booking['dish']); ?></strong></p>
        
        <?php if ($my_booking['image']): ?>
            <img src="<?php echo htmlspecialchars($my_booking['image']); ?>" alt="<?php echo htmlspecialchars($my_booking['dish']); ?>">
        <?php endif; ?>
        
        <br>
        <a href="menu.php?cancel=true" class="btn-cancel" onclick="return confirm('Er du sikker på at du vil avbestille?');">Avbestill</a>
    </div>

    <?php 
    // HVIS DET IKKE ER NOEN BESTILLING     |    НЕМАЄ ЗАМОВЛЕННЯ  
    else: 
    ?>

    <div class="menu-wrapper">
        
        <?php foreach ($menu_by_day as $day => $items): ?>
            
            <section class="menu-day-section">
                <h2 style="color: var(--dark-green); margin-bottom: 1.5rem;"><?php echo htmlspecialchars($day); ?></h2>
                
                <div class="menu-grid">
                    
                    <?php foreach ($items as $item): ?>
                        
                        <div class="feature-card" style="text-align: left; display: flex; flex-direction: column;">
                            
                            <div>
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['dish']); ?>" style="width: 100%; border-radius: 5px; margin-bottom: 1rem;">
                                <?php endif; ?>
                                
                                <h3 style="color: var(--dark-green);"><?php echo htmlspecialchars($item['dish']); ?></h3>
                                <p><?php echo htmlspecialchars($item['description']); ?></p>
                            </div>
                            
                            <form action="menu.php" method="POST">
                                <input type="hidden" name="idmenu" value="<?php echo $item['idmenu']; ?>">
                                
                                <button type="submit" name="register_lunch" class="cta-button" style="width: 100%;">
                                    Velg denne
                                </button>
                            </form>
                        </div>
                    
                    <?php endforeach; // Кінець циклу страв ?>
                    
                </div>
            </section>
        
        <?php endforeach; // Кінець циклу днів ?>

    </div>
    
    <?php endif; // Кінець перевірки if($my_booking) ?>

</main>

<?php 
include 'footer.php'; 
?>
