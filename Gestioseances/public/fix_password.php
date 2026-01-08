<?php

require_once dirname(__DIR__) . '/config.php';

echo "<h1>🔧 Correction des mots de passe</h1>";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $password = 'password123';
    $newHash = password_hash($password, PASSWORD_BCRYPT);

    echo "<p>Nouveau hash généré sur ce serveur :</p>";
    echo "<code>$newHash</code>";

    $stmt = $pdo->prepare("UPDATE users SET password = ?");
    $stmt->execute([$newHash]);

    $count = $stmt->rowCount();
    echo "<p style='color:green; font-size:20px'>✅ $count utilisateurs mis à jour !</p>";

    echo "<h2>Vérification</h2>";
    $stmt = $pdo->prepare("SELECT email, password FROM users WHERE email = ?");
    $stmt->execute(['mohammed.tazi@eidia.ma']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<p>Email : " . $user['email'] . "</p>";
        echo "<p>Hash : " . $user['password'] . "</p>";

        if (password_verify('password123', $user['password'])) {
            echo "<p style='color:green; font-size:24px'>✅ SUCCESS ! Le mot de passe 'password123' fonctionne maintenant !</p>";
            echo "<p><a href='login' style='font-size:20px'>👉 Aller à la page de connexion</a></p>";
        } else {
            echo "<p style='color:red'>❌ Échec de la vérification</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color:red'>Erreur : " . $e->getMessage() . "</p>";
}

echo "<hr><p style='color:orange'>⚠️ <strong>SUPPRIME CE FICHIER (fix_password.php) APRÈS !</strong></p>";
?>
