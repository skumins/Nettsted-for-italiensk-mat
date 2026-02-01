<?php
require_once 'config.php';

if (!isset($_SESSION['idusers'])){
    header("Location: login.php");
    exit();
}

if($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$message = '';

// ---CREATE Dish---
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_dish'])){
    $day = $_POST['day'];
    $dish = $_POST['dish'];
    $desc = $_POST['description'];
    $image_path = '';

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $filename = uniqid() . '-' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)){
            $image_path = $target_file;
        } else {
            $message = "Feil ved upload av bilde.";
        }
    }

    /*
    // NEVER do this!
    $sql = "INSERT INTO menu (day, dish, description, image) 
        VALUES ('$day', '$dish', '$desc', '$image_path')";

    $result = $tilkobling->query($sql);  // Вразливе до ін'єкції (Susceptible to injection)! 
    */

    $sql = "INSERT INTO menu (day, dish, description, image) VALUES (?, ?, ?, ?)";
    $stmt = $tilkobling->prepare($sql);
    $stmt->bind_param("ssss", $day, $dish, $desc, $image_path);
    if($stmt->execute()) {
    $message = "Ny rett lagt til!";
    }else {
        $message = "Feil: " . $stmt->error;
    }
    $stmt->close();
}

// ---DELETE Dish---
if (isset($_GET['delete_dish_id'])) {
    $id_delete = $_GET['delete_dish_id'];
    $sql = "DELETE FROM menu WHERE idmenu = ?";
    $stmt = $tilkobling->prepare($sql);
    $stmt->bind_param("i", $id_delete);

    if($stmt->execute()) {
        $message = "Rett slettet!";
    }
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// ---UPDATE Dish---
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_dish'])) {
    $id_update = $_POST['idmenu'];
    $day = $_POST['day'];
    $dish = $_POST['dish'];
    $desc = $_POST['description'];

    $sql = "UPDATE menu SET day = ?, dish = ?, description = ? WHERE idmenu = ?";
    $stmt = $tilkobling->prepare($sql);
    $stmt->bind_param("sssi", $day, $dish, $desc, $id_update);

    if($stmt->execute()) {
        $message = "Rett oppdatert!";
    }
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// ---CHENGE/update role---
if(isset($_POST['update_role'])) {
    $user_id = $_POST['idusers'];
    $new_role = $_POST['role'];

    $sql = "UPDATE users SET role = ? WHERE idusers = ?";
    $stmt = $tilkobling->prepare($sql);
    $stmt->bind_param("si", $new_role, $user_id);
    if ($stmt->execute()) {
        $message = "Brukerrole oppdatert";
    }
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// ---DELETE user---
if(isset($_GET['delete_user_id'])) {
    $user_id_delete = $_GET['delete_user_id'];

    $sql = "DELETE FROM users WHERE idusers = ?";
    $stmt = $tilkobling->prepare($sql);
    $stmt->bind_param("i", $user_id_delete);
    if ($stmt->execute()) {
        $message = "Bruker slettet!";
    }
    $stmt->close();
    header("Location: admin.php");
    exit();
}



// ---READ all dishes---
$menu_result = $tilkobling->query("SELECT * FROM menu ORDER BY FIELD(day, 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag')");

$registrations_sql = "
    SELECT u.username, u.email, m.dish, m.day, r.regdate
    FROM registrations r
    JOIN users u ON r.iduser = u.idusers
    JOIN menu m ON r.idmenu = m.idmenu
    ORDER BY m.day, u.username
";
$registrations_result = $tilkobling->query($registrations_sql);
// ---READ all users---
$users_result = $tilkobling->query("SELECT idusers, username, email, role FROM users");
?>

<?php
include 'header.php';
?>

<main class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <div class="page-header">
        <h1>Adminpanel</h1>
        <p>Velkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </div>

    <?php if($message): ?>
        <div class="form-message"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php 
    // Logic update menu
    if (isset($_GET['edit_dish_id'])):
        $id_edit = $_GET['edit_dish_id'];
        $sql = "SELECT * FROM menu WHERE idmenu = ?";
        $stmt = $tilkobling->prepare($sql);
        $stmt->bind_param("i", $id_edit);
        $stmt->execute();
        $edit_result = $stmt->get_result();
        $dish = $edit_result->fetch_assoc();
        $stmt->close();
    ?>

    <section class="admin-section">
        <h2>Rediger Rett: <?php echo htmlspecialchars($dish['dish']); ?></h2>
        <form action="admin.php" method="POST" class="registration-form">
            <input type="hidden" name="idmenu" value="<?php echo $dish['idmenu']; ?>">
            <div class="form-group">
                <label for="day">Dag:</label>
                <select id="day" name="day" required>
                    <option value="Mandag" <?php if($dish['day'] == 'Mandag') echo 'selected'; ?>>Mandag</option>
                    <option value="Tirsdag" <?php if($dish['day'] == 'Tirsdag') echo 'selected'; ?>>Tirsdag</option>
                    <option value="Onsdag" <?php if($dish['day'] == 'Onsdag') echo 'selected'; ?>>Onsdag</option>
                    <option value="Torsdag" <?php if($dish['day'] == 'Torsdag') echo 'selected'; ?>>Torsdag</option>
                    <option value="Fredag" <?php if($dish['day'] == 'Fredag') echo 'selected'; ?>>Fredag</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dish">Navn på rett:</label>
                <input type="text" id="dish" name="dish" value="<?php echo htmlspecialchars($dish['dish']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Beskrivelse:</label>
                <textarea id="description" name="description" rows="4" style="width: 100%; padding: 0.8rem;"><?php echo htmlspecialchars($dish['description']); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit" name="update_dish" class="cta-button_1">Lagre Endringer</button>
                <a href="admin.php" style="margin-left: 1rem;">Avbryt</a>
            </div>
        </form>
    </section>

    <?php 
    else:
    ?>
    
    <section class="admin-section">
        <h2>Menyadministrasjon</h2>
        <form action="admin.php" method="POST" enctype="multipart/form-data" class="registration-form">
            <h3>Legg til ny rett</h3>
            <div class="form-group">
                <label for="day">Dag:</label>
                <select id="day" name="day" required>
                    <option value="Mandag">Mandag</option>
                    <option value="Tirsdag">Tirsdag</option>
                    <option value="Onsdag">Onsdag</option>
                    <option value="Torsdag">Torsdag</option>
                    <option value="Fredag">Fredag</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dish">Navn på rett:</label>
                <input type="text" id="dish" name="dish" required>
            </div>
            <div class="form-group">
                <label for="description">Beskrivelse:</label>
                <textarea id="description" name="description" rows="4" style="width:100%; padding: 0.8rem;"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Bilde (valgfritt):</label>
                <input type="file" id="image" name="image">
            </div>
            <div class="form-group">
                <button type="submit" name="add_dish" class="cta-button_1">Legg til rett</button>
            </div>
        </form>

        <h3>Eksisterende Meny</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Dag</th>
                    <th>Bilde</th>
                    <th>Rett</th>
                    <th>Beskrivelse</th>
                    <th>Handlinger</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $menu_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['day']); ?></td>
                        <td>
                            <?php if ($row['image']): ?>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Bilde">
                            <?php else: ?>
                                (Ingen bilde)
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['dish']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <a href="admin.php?edit_dish_id=<?php echo $row['idmenu']; ?>" class="link-edit">Rediger</a>
                            <br>
                            <a href="admin.php?delete_dish_id=<?php echo $row['idmenu']; ?>" class="link-delete" onclick="return confirm('Er du sikker på at du vil slette denne retten?')">Slett</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
    </section>

    <section class="admin-section">
        <h2>Påmeldte Deltakere</h2>
        <p>Dette er din liste for å se hvor mange porsjoner du skal lage.</p>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Dag</th>
                    <th>Rett</th>
                    <th>Student</th>
                    <th>E-post</th>
                    <th>Registrert</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $registrations_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['day']); ?></td>
                    <td><?php echo htmlspecialchars($row['dish']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['regdate']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <section class="admin-section">
        <h2>Brukeradministrasjon</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Brukernavn</th>
                    <th>E-post</th>
                    <th>Rolle</th>
                    <th>Handlinger</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $users_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['idusers']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <form action="admin.php" method="POST" style="display: flex; gap: 5px;">
                            <input type="hidden" name="idusers" value="<?php echo $row['idusers']; ?>">
                            <select name="role" style="padding: 5px;">
                                <option value="user" <?php if($row['role'] == 'user') echo 'selected'; ?>>user</option>
                                <option value="admin" <?php if($row['role'] == 'admin') echo 'selected'; ?>>admin</option>
                            </select>
                            <button type="submit" name="update_role" class="cta-button_1">Oppdater</button>
                        </form>
                    </td>
                    <td>
                        <a href="admin.php?delete_user_id=<?php echo $row['idusers']; ?>" class="link-delete" onclick="return confirm('Er du sikker på at du vil slette brukeren?')">Slett</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
    <?php endif; ?>
</main>

<?php
include 'footer.php';
?>

