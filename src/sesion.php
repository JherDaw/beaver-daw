<?php
/**
 * Funciones comunes: sesión, usuario conectado, protección CSRF
 * y escape de texto para evitar XSS.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

/** Escapa un texto antes de mostrarlo en HTML. */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

/** Devuelve el usuario conectado o null. */
function usuario_actual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

/** Guarda el usuario en la sesión después de registrarse o iniciar sesión. */
function iniciar_sesion(array $usuario): void
{
    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id'     => (int) $usuario['id'],
        'nombre' => $usuario['nombre'],
        'rol'    => $usuario['rol'],
    ];
}

/** Token CSRF para los formularios. */
function token_csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function comprobar_csrf(): bool
{
    $enviado = $_POST['csrf'] ?? '';
    return is_string($enviado) && hash_equals($_SESSION['csrf'] ?? '', $enviado);
}

/** Redirige y termina la ejecución. */
function redirigir(string $ruta): never
{
    header('Location: ' . $ruta);
    exit;
}
