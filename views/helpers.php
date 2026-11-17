<?php
/** Escapa texto para mostrarlo en HTML de forma segura. */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Devuelve las estrellas: ★★★★☆ */
function estrellas(int $n): string
{
    $n = max(0, min(5, $n));
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}

/** Iniciales de un nombre: "Juan Sebastián Díaz" -> "JD". */
function iniciales(string $nombre): string
{
    $partes = preg_split('/\s+/', trim($nombre), -1, PREG_SPLIT_NO_EMPTY);
    if (!$partes) {
        return '?';
    }
    $primera = mb_substr($partes[0], 0, 1);
    $ultima  = count($partes) > 1 ? mb_substr(end($partes), 0, 1) : '';
    return mb_strtoupper($primera . $ultima);
}
