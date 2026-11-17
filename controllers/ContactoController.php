<?php
require_once __DIR__ . '/../models/Mensaje.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../views/helpers.php';

/**
 * Controlador del formulario de contacto (CRUD de la tabla `mensajes`).
 *
 * Público:
 *   guardar    (POST)  recibe el formulario, lo guarda y redirige a "gracias"
 *   gracias    (GET)   página de agradecimiento (recibe ?nombre=...)
 * Administración (requiere clave):
 *   entrar     (GET/POST)  formulario de acceso
 *   salir      (GET)       cierra la sesión
 *   listar     (GET)       lista y busca mensajes (?q=...)
 *   editar     (GET)       formulario de edición (?id=...)
 *   actualizar (POST)      guarda los cambios
 *   eliminar   (POST)      borra un mensaje
 */
class ContactoController
{
    private const AVISOS = [
        'actualizado' => 'El mensaje se actualizó correctamente.',
        'eliminado'   => 'El mensaje se eliminó.',
    ];

    /* ===================== PÚBLICO ===================== */

    /** CREATE (POST): guarda el mensaje y manda al usuario a la página de gracias. */
    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('index.php#contacto');
        }

        [$datos, $errores] = $this->leerFormulario();
        if ($errores) {
            $this->error('Revisa el formulario', $errores, 'index.php#contacto', 422);
            return;
        }

        try {
            Mensaje::crear($datos);
        } catch (PDOException $ex) {
            $this->falloBD($ex);
            return;
        }

        // Post/Redirect/Get: evita que al recargar la página se reenvíe el formulario.
        $this->redirigir('index.php?c=contacto&a=gracias&nombre=' . urlencode($datos['nombre']));
    }

    /** Página de "gracias" (GET). */
    public function gracias(): void
    {
        $nombre = mb_substr($this->texto($_GET['nombre'] ?? ''), 0, 80);
        $this->vista('contacto/gracias', ['nombre' => $nombre]);
    }

    /* ===================== ADMINISTRACIÓN ===================== */

    /** Acceso a la administración: GET muestra el formulario, POST verifica la clave. */
    public function entrar(): void
    {
        $this->iniciarSesion();

        if (!empty($_SESSION['admin'])) {
            $this->redirigir($this->urlLista());
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verificarCsrf();
            $clave     = $this->claveAdmin();
            $ingresada = $this->texto($_POST['clave'] ?? '');

            if ($clave === '' || $clave === 'CAMBIA_ESTA_CLAVE') {
                $error = 'Antes de entrar, cambia la clave en config/admin.php.';
            } elseif (hash_equals($clave, $ingresada)) {
                session_regenerate_id(true);
                $_SESSION['admin'] = true;
                $this->redirigir($this->urlLista());
            } else {
                usleep(700000); // frena los intentos repetidos
                $error = 'Clave incorrecta.';
            }
        }

        $this->vista('contacto/entrar', ['error' => $error, 'csrf' => $_SESSION['csrf']]);
    }

    /** Cierra la sesión de administración. */
    public function salir(): void
    {
        $this->iniciarSesion();
        $_SESSION = [];
        session_destroy();
        $this->redirigir('index.php?c=contacto&a=entrar');
    }

    /** READ (GET): lista de mensajes con buscador (?q=). */
    public function listar(): void
    {
        $this->exigirAdmin();

        $q     = mb_substr($this->texto($_GET['q'] ?? ''), 0, 80);
        $aviso = self::AVISOS[$this->texto($_GET['ok'] ?? '')] ?? null;

        try {
            $mensajes = Mensaje::todos($q);
        } catch (PDOException $ex) {
            $this->falloBD($ex);
            return;
        }

        $this->vista('contacto/lista', [
            'mensajes' => $mensajes,
            'q'        => $q,
            'aviso'    => $aviso,
            'csrf'     => $_SESSION['csrf'],
        ]);
    }

    /** UPDATE (GET): muestra el formulario con los datos del mensaje (?id=). */
    public function editar(): void
    {
        $this->exigirAdmin();

        try {
            $m = Mensaje::buscar($this->idDe($_GET));
            if ($m === null) {
                $this->error('Mensaje no encontrado', ['No existe un mensaje con ese número.'], $this->urlLista(), 404);
                return;
            }
            $this->formularioEdicion($m, []);
        } catch (PDOException $ex) {
            $this->falloBD($ex);
        }
    }

    /** UPDATE (POST): guarda los cambios del formulario de edición. */
    public function actualizar(): void
    {
        $this->exigirAdmin();
        $this->soloPost();
        $this->verificarCsrf();

        $id = $this->idDe($_POST);
        [$datos, $errores] = $this->leerFormulario();

        try {
            $actual = Mensaje::buscar($id);
            if ($actual === null) {
                $this->error('Mensaje no encontrado', ['No existe un mensaje con ese número.'], $this->urlLista(), 404);
                return;
            }
            if ($errores) {
                $this->formularioEdicion(array_merge($actual, $datos), $errores);
                return;
            }
            Mensaje::actualizar($id, $datos);
        } catch (PDOException $ex) {
            $this->falloBD($ex);
            return;
        }

        $this->redirigir($this->urlLista('actualizado'));
    }

    /** DELETE (POST): elimina un mensaje. */
    public function eliminar(): void
    {
        $this->exigirAdmin();
        $this->soloPost();
        $this->verificarCsrf();

        try {
            Mensaje::eliminar($this->idDe($_POST));
        } catch (PDOException $ex) {
            $this->falloBD($ex);
            return;
        }

        $this->redirigir($this->urlLista('eliminado'));
    }

    /* ===================== AUXILIARES (privados: el router no puede llamarlos) ===================== */

    /** Lee y valida los campos del formulario. Devuelve [datos, errores]. */
    private function leerFormulario(): array
    {
        $d = [
            'nombre'   => $this->texto($_POST['nombre'] ?? ''),
            'telefono' => $this->texto($_POST['telefono'] ?? ''),
            'correo'   => $this->texto($_POST['correo'] ?? ''),
            'producto' => $this->texto($_POST['producto'] ?? ''),
            'mensaje'  => $this->texto($_POST['mensaje'] ?? ''),
        ];

        $errores = [];
        if ($d['nombre'] === '' || mb_strlen($d['nombre']) > 80) {
            $errores[] = 'El nombre es obligatorio (máximo 80 caracteres).';
        }
        if ($d['telefono'] !== '' && !preg_match('/^[0-9+()\s-]{5,30}$/', $d['telefono'])) {
            $errores[] = 'El teléfono solo puede llevar números, espacios, +, - y paréntesis (de 5 a 30 caracteres).';
        }
        if (!filter_var($d['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['correo']) > 120) {
            $errores[] = 'Escribe un correo electrónico válido.';
        }
        if ($d['producto'] === '' || mb_strlen($d['producto']) > 80) {
            $errores[] = 'Elige un producto de interés.';
        }
        if ($d['mensaje'] === '' || mb_strlen($d['mensaje']) > 2000) {
            $errores[] = 'El mensaje es obligatorio (máximo 2000 caracteres).';
        }

        return [$d, $errores];
    }

    /** Muestra el formulario de edición; las opciones del producto salen de la BD. */
    private function formularioEdicion(array $m, array $errores): void
    {
        $opciones = array_merge(['Consulta general'], array_column(Producto::todos(), 'nombre'));
        if (!in_array($m['producto'], $opciones, true)) {
            $opciones[] = $m['producto'];
        }
        if ($errores) {
            http_response_code(422);
        }
        $this->vista('contacto/editar', [
            'm'        => $m,
            'errores'  => $errores,
            'opciones' => $opciones,
            'csrf'     => $_SESSION['csrf'],
        ]);
    }

    /** Inicia la sesión (cookie segura) y crea el token anti-CSRF. */
    private function iniciarSesion(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
                'cookie_secure'   => !empty($_SERVER['HTTPS']),
            ]);
        }
        header('X-Robots-Tag: noindex, nofollow');
        header('Cache-Control: no-store');
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
    }

    /** Si no hay sesión de administrador, manda al formulario de acceso. */
    private function exigirAdmin(): void
    {
        $this->iniciarSesion();
        if (empty($_SESSION['admin'])) {
            $this->redirigir('index.php?c=contacto&a=entrar');
        }
    }

    /** Comprueba el token anti-CSRF de los formularios POST de administración. */
    private function verificarCsrf(): void
    {
        $token = $_POST['csrf'] ?? '';
        if (!is_string($token) || !hash_equals((string) ($_SESSION['csrf'] ?? ''), $token)) {
            http_response_code(403);
            exit('Solicitud no válida. Vuelve atrás y recarga la página.');
        }
    }

    /** Las acciones que modifican datos solo aceptan POST. */
    private function soloPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir($this->urlLista());
        }
    }

    /** Clave de administración guardada en config/admin.php ('' si el archivo no existe). */
    private function claveAdmin(): string
    {
        $archivo = __DIR__ . '/../config/admin.php';
        if (!is_file($archivo)) {
            return '';
        }
        $config = require $archivo;
        return (string) ($config['clave'] ?? '');
    }

    /** Valor de texto seguro: si no es un string (p. ej. nombre[]=x) devuelve ''. */
    private function texto($valor): string
    {
        return is_string($valor) ? trim($valor) : '';
    }

    /** Lee un id numérico positivo de $_GET / $_POST (0 si no es válido). */
    private function idDe(array $origen): int
    {
        $v = $origen['id'] ?? '';
        return (is_string($v) && ctype_digit($v)) ? (int) $v : 0;
    }

    private function urlLista(string $ok = ''): string
    {
        return 'index.php?c=contacto&a=listar' . ($ok !== '' ? '&ok=' . $ok : '');
    }

    private function redirigir(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /** Muestra una página de error con una lista de mensajes. */
    private function error(string $titulo, array $mensajes, string $volver, int $codigo = 400): void
    {
        http_response_code($codigo);
        $this->vista('contacto/error', ['titulo' => $titulo, 'errores' => $mensajes, 'volver' => $volver]);
    }

    private function falloBD(PDOException $ex): void
    {
        error_log('Crescendo (BD): ' . $ex->getMessage());
        $this->error(
            'Ocurrió un problema',
            ['No se pudo completar la operación con la base de datos. Inténtalo de nuevo en unos minutos.'],
            'index.php',
            500
        );
    }

    /** Carga una vista de views/ pasándole variables. */
    private function vista(string $nombre, array $datos = []): void
    {
        extract($datos, EXTR_SKIP);
        require __DIR__ . '/../views/' . $nombre . '.php';
    }
}
