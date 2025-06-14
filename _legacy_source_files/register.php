<?php
/*
 * register.php
 * Handles new user registration.
 */
require_once __DIR__ . '/../config/config.php'; // Includes session_start(), $conn

// If user is already logged in, redirect them to their profile page
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$pageTitle = "Inscription - Eiganights";
$error_message = '';    // For displaying registration errors
$username_value = '';   // To repopulate username field on error

// Determine redirect URL after successful registration (usually profile)
$redirectAfterRegister = 'profile.php'; // Default
// Check if a redirect was passed (e.g., from login page if user chose to register)
if (isset($_GET['redirect']) && !empty(trim($_GET['redirect']))) {
    $postedRedirectUrl = trim($_GET['redirect']);
    $urlComponents = parse_url($postedRedirectUrl);
    if ((empty($urlComponents['host']) || $urlComponents['host'] === $_SERVER['HTTP_HOST']) &&
        !preg_match('/(logout|register|login)\.php/i', $postedRedirectUrl)) { // Avoid redirecting to auth pages
        $redirectAfterRegister = $postedRedirectUrl;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation for presence of required fields
    if (!isset($_POST['username'], $_POST['password'], $_POST['password_confirm']) ||
        empty(trim($_POST['username'])) ||
        empty($_POST['password']) || // Password cannot be empty
        empty($_POST['password_confirm'])) {

        $error_message = "Tous les champs sont requis.";
        if (isset($_POST['username'])) {
            $username_value = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
        }
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password']; // Do not trim password
        $password_confirm = $_POST['password_confirm'];

        // Store username for repopulation in case of other errors
        $username_value = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

        // --- Further Validations ---
        // Username validation (length, characters - example)
        if (strlen($username) < 3 || strlen($username) > 50) {
            $error_message = "Le nom d'utilisateur doit contenir entre 3 et 50 caractères.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $error_message = "Le nom d'utilisateur ne peut contenir que des lettres, des chiffres et des underscores (_).";
        }
        // Password validation (length, complexity - example)
        elseif (strlen($password) < 6) {
            $error_message = "Le mot de passe doit contenir au moins 6 caractères.";
        }
        // Password confirmation
        elseif ($password !== $password_confirm) {
            $error_message = "Les mots de passe ne correspondent pas.";
        }

        // If no validation errors so far, proceed to check username availability and insert
        if (empty($error_message)) {
            // Check if username already exists using a prepared statement
            $sqlCheck = "SELECT id FROM users WHERE username = ?";
            $stmtCheck = $conn->prepare($sqlCheck);

            if (!$stmtCheck) {
                error_log("Prepare failed for username check: (" . $conn->errno . ") " . $conn->error . " (Code: REG_PREP_CHK)");
                $error_message = "Une erreur système est survenue. Veuillez réessayer. (R01)";
            } else {
                $stmtCheck->bind_param("s", $username);
                if (!$stmtCheck->execute()) {
                    error_log("Execute failed for username check: (" . $stmtCheck->errno . ") " . $stmtCheck->error . " (Code: REG_EXEC_CHK)");
                    $error_message = "Une erreur système est survenue. Veuillez réessayer. (R02)";
                } else {
                    $stmtCheck->store_result();
                    if ($stmtCheck->num_rows > 0) {
                        $error_message = "Ce nom d'utilisateur est déjà pris. Veuillez en choisir un autre.";
                    } else {
                        // Username is available, proceed to insert new user
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        if ($hashedPassword === false) {
                             error_log("password_hash() failed. (Code: REG_PW_HASH)");
                             $error_message = "Une erreur critique est survenue lors de la création de votre compte. (R03)";
                        } else {
                            $sqlInsert = "INSERT INTO users (username, password) VALUES (?, ?)"; // profile_visibility defaults to 'public' via DB schema
                            $stmtInsert = $conn->prepare($sqlInsert);
                            if (!$stmtInsert) {
                                error_log("Prepare failed for user insert: (" . $conn->errno . ") " . $conn->error . " (Code: REG_PREP_INS)");
                                $error_message = "Une erreur système est survenue. Veuillez réessayer. (R04)";
                            } else {
                                $stmtInsert->bind_param("ss", $username, $hashedPassword);
                                if ($stmtInsert->execute()) {
                                    $newUserId = $conn->insert_id; // Get the ID of the newly inserted user
                                    $_SESSION['user_id'] = $newUserId;
                                    $_SESSION['username'] = $username; // Store username for convenience
                                    
                                    session_regenerate_id(true); // Security: Prevent session fixation

                                    $_SESSION['message'] = "Inscription réussie ! Bienvenue, " . htmlspecialchars($username) . ".";
                                    header('Location: ' . $redirectAfterRegister);
                                    exit;
                                } else {
                                    error_log("Execute failed for user insert: (" . $stmtInsert->errno . ") " . $stmtInsert->error . " (Code: REG_EXEC_INS)");
                                    $error_message = "Une erreur est survenue lors de la création de votre compte. (R05)";
                                }
                                $stmtInsert->close();
                            }
                        }
                    }
                }
                $stmtCheck->close();
            }
        }
    }
}

include_once ROOT_PATH . '/includes/header.php';
?>

<main class="container auth-form-container">
    <h1>Inscription</h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    <?php // Display general session messages if any (e.g., from a redirect) ?>
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php<?php echo $redirectAfterRegister !== 'profile.php' ? '?redirect=' . urlencode($redirectAfterRegister) : ''; ?>" novalidate>
        <div class="form-group">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" placeholder="Choisissez un nom d'utilisateur" 
                   value="<?php echo $username_value; ?>" required 
                   pattern="^[a-zA-Z0-9_]{3,50}$" 
                   title="3-50 caractères. Lettres, chiffres, et underscores uniquement." 
                   autofocus />
            <small class="form-text">3-50 caractères. Lettres, chiffres, et underscores (_) uniquement.</small>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" placeholder="Créez un mot de passe" required 
                   minlength="6" title="Au moins 6 caractères." />
            <small class="form-text">Au moins 6 caractères.</small>
        </div>
        <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe:</label>
            <input type="password" id="password_confirm" name="password_confirm" placeholder="Retapez votre mot de passe" required />
        </div>
        <div class="form-group">
            <input type="submit" value="S'inscrire" class="button-primary" />
        </div>
    </form>
    <p class="auth-links">
        Déjà un compte ? <a href="login.php<?php echo $redirectAfterRegister !== 'profile.php' ? '?redirect=' . urlencode($redirectAfterRegister) : ''; ?>">Connectez-vous ici</a>.
    </p>
</main>

<?php
// $conn->close(); // Optional
include_once ROOT_PATH . '/includes/footer.php';
?>