<?php
session_start();

// Lista de palabras para el juego
$palabras = ['elefante', 'jirafa', 'hipopotamo', 'rinoceronte', 'cocodrilo', 'camello', 'chimpance'];

// Inicializar el juego
if (!isset($_SESSION['palabra'])) {
    $_SESSION['palabra'] = $palabras[array_rand($palabras)];
    $_SESSION['vidas'] = 6; // Número máximo de vidas
    $_SESSION['letras_acertadas'] = str_repeat('?', strlen($_SESSION['palabra']));
    $_SESSION['letras_usadas'] = [];
}

// Procesar la letra enviada
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['letra'])) {
    $letra = strtolower($_POST['letra']);

    if (in_array($letra, $_SESSION['letras_usadas'])) {
        $mensaje = "⚠️ Ya has usado la letra '$letra'. Intenta con otra.";
    } else {
        $_SESSION['letras_usadas'][] = $letra;

        if (strpos($_SESSION['palabra'], $letra) !== false) {
            for ($i = 0; $i < strlen($_SESSION['palabra']); $i++) {
                if ($_SESSION['palabra'][$i] == $letra) {
                    $_SESSION['letras_acertadas'][$i] = $letra;
                }
            }
            $mensaje = "✅ ¡Bien hecho! La letra '$letra' está en la palabra.";
        } else {
            $_SESSION['vidas']--;
            $mensaje = "❌ La letra '$letra' no está en la palabra.";
        }
    }
}

if ($_SESSION['letras_acertadas'] == $_SESSION['palabra']) {
    echo "<div class='container'><h1>🎉 ¡Enhorabuena! Has ganado :)</h1>
          <p>La palabra era: " . $_SESSION['palabra'] . "</p>
          <a href=''>Jugar de nuevo</a></div>";
    session_destroy();
    exit();
} elseif ($_SESSION['vidas'] <= 0) {
    echo "<div class='container'><h1>😢 Lo siento, has perdido</h1>
          <p>La palabra era: " . $_SESSION['palabra'] . "</p>
          <a href=''>Jugar de nuevo</a></div>";
    session_destroy();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ahorcado</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>🎮 Juego del Ahorcado</h1>
    <div class="container">
        <p><strong>Palabra secreta:</strong> <?php echo $_SESSION['letras_acertadas']; ?></p>
        <p><strong>Vidas restantes:</strong> <?php echo $_SESSION['vidas']; ?></p>

        <form method="post">
            <label for="letra">Introduce una letra:</label><br>
            <input type="text" name="letra" id="letra" maxlength="1" required>
            <button type="submit">Adivinar</button>
        </form>

        <p><strong>Letras usadas:</strong> <?php echo implode(', ', $_SESSION['letras_usadas']); ?></p>

        <?php if (isset($mensaje)) echo "<p>$mensaje</p>"; ?>
    </div>
</body>
</html>
