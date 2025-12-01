<?php
require_once 'config.php';
include 'header.php';
?>

<section class="hero">
    <div class="hero-left">
        <h1 class="hero-title">Oppdag smaken av ekte Italia</h1>
        <div class="hero-text-content">
            <p class="hero-subtitle">Italiensk matuke i skolens kantine</p>
            <a href="register.php" class="cta-button">Registrer deg for lunsj</a>
        </div>
    </div>
    <div class="hero-image-container">
        <img src="photo/italy_trees_2560_1440.jpg" alt="Italien" class="hero-image">
    </div>
</section>

    <!-- About Event Section -->
     <section class="about-event">
        <div class="container">
            <h2>Om Arrangementet</h2>
            <div class="about-content">
                <p>Skolen inviterer deg til en uke med italiensk mat. Våre kantiner tilbereder autentiske italienske retter under veiledning av erfarne kokker.</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <h3>Autentiske Retter</h3>
                    <p>Laget etter tradisjonelle italienske oppskrifter</p>
                </div>
                <div class="feature-card">
                    <h3>Begrenset Kapasitet</h3>
                     <p>Registrer deg i forkant - begrenset antall plasser</p>
                </div>
            </div>
        </div>
     </section>

      <section class="how-it-works">
        <div class="container">
            <h2>Hvordan Fungerer Det?</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Se Menyen</h3>
                    <p>Se hvilke retter som serveres hver dag</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Velg Dag</h3>
                    <p>Velg en dag som passer for deg</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Registrer Deg</h3>
                    <p>Fyll ut en enkel registreringsskjema</p>
                </div>
            </div>
        </div>
    </section>

<?php
include 'footer.php';
?>
