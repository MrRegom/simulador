<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\RankingRepository;
use App\Repositories\ClienteRepository;
use App\Repositories\RankingConfigRepository;

class RankingController extends Controller {

    public function index() {
        // Vista de registro (Admin)
        $pistaRepo = new \App\Repositories\PistaRepository();
        $this->view('ranking/index', [
            'page_title' => 'Registro de Tiempos',
            'pistas' => $pistaRepo->all()
        ]);
    }

    public function save() {
        header('Content-Type: application/json');
        try {
            // Usamos $_POST porque viene con FormData (por la foto)
            $telefono = $_POST['telefono'] ?? null;
            $rut = $_POST['rut'] ?? null;
            $email = $_POST['email'] ?? null;
            $nombre = $_POST['nombre'] ?? 'PILOTO_SIN_NOMBRE';
            $pista = $_POST['pista'] ?? 'Pista Estándar';
            $tiempo = $_POST['tiempo'] ?? '00:00.000';
            
            $tiempo_ms = $this->parseTimeToMs($tiempo);

            $cliRepo = new ClienteRepository();
            $cliente = null;

            if ($rut) $cliente = $cliRepo->buscarPorRut($rut);
            if (!$cliente && $telefono) $cliente = $cliRepo->buscarPorTelefono($telefono);

            if ($cliente) {
                $cliente_id = $cliente['id'];
                // Actualizar datos si faltan
                if (!$cliente['rut'] && $rut) $cliRepo->actualizarRut($cliente_id, $rut);
                if (!$cliente['telefono'] && $telefono) $cliRepo->actualizarTelefono($cliente_id, $telefono);
                if ($nombre && (!$cliente['nombre'] || $cliente['nombre'] !== $nombre)) $cliRepo->actualizarNombre($cliente_id, $nombre);
                
                // Actualización de Email si viene nuevo
                if ($email && (!$cliente['email'] || $cliente['email'] !== $email)) {
                    $db = \App\Core\Database::getInstance()->getConnection();
                    $stmt = $db->prepare("UPDATE clientes SET email = ? WHERE id = ?");
                    $stmt->execute([$email, $cliente_id]);
                }

                $cliRepo->registrarVisita($cliente_id);
            } else {
                $cliente_id = $cliRepo->crear($telefono, $nombre, $rut, $email);
            }

            // Manejo de FOTO
            $foto_path = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $filename = 'piloto_' . $cliente_id . '_' . time() . '.' . $ext;
                $upload_dir = __DIR__ . '/../../public/uploads/pilotos/';
                
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $filename)) {
                    $foto_path = 'uploads/pilotos/' . $filename;
                }
            }

            $rankRepo = new RankingRepository();
            
            try {
                $success = $rankRepo->guardar($cliente_id, $pista, $tiempo, $tiempo_ms, $foto_path);
            } catch (\PDOException $e) {
                if ($e->getCode() == '42S02') {
                    throw new \Exception('LA TABLA "ranking" NO EXISTE EN LA BASE DE DATOS.');
                }
                throw $e;
            }

            if ($success) {
                echo json_encode(['success' => true, 'message' => '¡Récord guardado!']);
            } else {
                throw new \Exception('Error al guardar en la base de datos');
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function display() {
        // Vista pública (Pantalla 55")
        $this->view('ranking/display', [
            'page_title' => 'Leaderboard A4R'
        ]);
    }

    public function api_data() {
        header('Content-Type: application/json');
        try {
            $rankRepo = new RankingRepository();
            $adRepo = new \App\Repositories\RankingAdRepository();
            $configRepo = new \App\Repositories\RankingConfigRepository();
            
            $cfg = $configRepo->getAllConfigs();
            $active_track = $cfg['active_track'] ?? null;
            $top = $rankRepo->getTop(20, $active_track);
            $best = $rankRepo->getBest($active_track);
            $ads = $adRepo->getActive();
            $latest_id = $rankRepo->getLatestId();
            $slide_interval = intval($cfg['slide_interval'] ?? 15000);
            $data_refresh = intval($cfg['data_refresh_interval'] ?? 10000);
            $ranking_duration = intval($cfg['ranking_duration'] ?? 15000);
            $bg_image = $cfg['viewer_background'] ?? null;
            $bg_opacity = $cfg['background_opacity'] ?? 0.15;
            $default_pilot = $cfg['default_pilot_image'] ?? 'assets/img/defaultpiloto.png';

            echo json_encode([
                'success' => true, 
                'top' => $top, 
                'best' => $best,
                'ads' => $ads,
                'latest_id' => $latest_id,
                'slide_interval' => $slide_interval,
                'data_refresh' => $data_refresh,
                'ranking_duration' => $ranking_duration,
                'background_image' => $bg_image,
                'labels' => [
                    'ranking_title_main' => $cfg['ranking_title_main'] ?? 'A4R_SIMRACING',
                    'ranking_title_sub' => $cfg['ranking_title_sub'] ?? 'VUELTAS_RÁPIDAS',
                    'banner_top_text' => $cfg['banner_top_text'] ?? 'A4R_SIMRACING · TEMPORADA 2025',
                    'banner_podium_text' => $cfg['banner_podium_text'] ?? 'PÓDIUM',
                    'banner_ads_text' => $cfg['banner_ads_text'] ?? 'A4R_SIMRACING'
                ],
                'background_opacity' => floatval($bg_opacity),
                'default_pilot_image' => $default_pilot,
                'marquee' => $this->getMarqueeData()
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function api_track_summary() {
        header('Content-Type: application/json');
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $range = $_GET['range'] ?? '7d';
            $track = isset($_GET['track']) ? trim($_GET['track']) : '';
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

            $allowedRanges = ['7d', '30d', 'all'];
            if (!in_array($range, $allowedRanges, true)) {
                $range = '7d';
            }

            if ($limit < 3) $limit = 3;
            if ($limit > 50) $limit = 50;

            $from = null;
            if ($range !== 'all') {
                $days = $range === '30d' ? 30 : 7;
                $from = (new \DateTime())->modify("-{$days} days")->format('Y-m-d H:i:s');
            }

            $dateWhere = '';
            $dateParams = [];
            if ($from) {
                $dateWhere = 'WHERE r.created_at >= :from';
                $dateParams[':from'] = $from;
            }

            $stmt = $db->prepare("SELECT r.pista, COUNT(*) as total, MAX(r.created_at) as last_at
                                  FROM ranking r
                                  {$dateWhere}
                                  GROUP BY r.pista
                                  ORDER BY last_at DESC");
            foreach ($dateParams as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $tracks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $configRepo = new RankingConfigRepository();
            $active_track = $configRepo->get('active_track', '');

            $active_best = null;
            if ($active_track) {
                $whereParts = [];
                $params = [];
                if ($from) {
                    $whereParts[] = 'r.created_at >= :from';
                    $params[':from'] = $from;
                }
                $whereParts[] = 'TRIM(r.pista) = :track';
                $params[':track'] = $active_track;
                $whereSql = 'WHERE ' . implode(' AND ', $whereParts);

                $sqlBest = "SELECT r.tiempo, r.tiempo_ms, r.created_at, r.foto_path, c.nombre as piloto_nombre
                           FROM ranking r
                           JOIN clientes c ON r.cliente_id = c.id
                           {$whereSql}
                           ORDER BY r.tiempo_ms ASC, r.created_at ASC
                           LIMIT 1";
                $stmt = $db->prepare($sqlBest);
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                $stmt->execute();
                $active_best = $stmt->fetch(\PDO::FETCH_ASSOC);
            }

            if (!$track) {
                if ($active_track) {
                    $track = $active_track;
                } elseif (!empty($tracks)) {
                    $track = $tracks[0]['pista'];
                }
            }

            $stmt = $db->prepare("SELECT r.pista, COUNT(*) as total, MAX(r.created_at) as last_at
                                  FROM ranking r
                                  {$dateWhere}
                                  GROUP BY r.pista
                                  ORDER BY last_at DESC
                                  LIMIT 6");
            foreach ($dateParams as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $recent_tracks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $db->prepare("SELECT r.pista, COUNT(*) as total, MAX(r.created_at) as last_at
                                  FROM ranking r
                                  {$dateWhere}
                                  GROUP BY r.pista
                                  ORDER BY total DESC, last_at DESC
                                  LIMIT 6");
            foreach ($dateParams as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $frequent_tracks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $top = [];
            if ($track) {
                $whereParts = [];
                $params = [];
                if ($from) {
                    $whereParts[] = 'r.created_at >= :from';
                    $params[':from'] = $from;
                }
                $whereParts[] = 'TRIM(r.pista) = :track';
                $params[':track'] = $track;
                $whereSql = 'WHERE ' . implode(' AND ', $whereParts);

                $sqlTop = "SELECT r.id, r.pista, r.tiempo, r.tiempo_ms, r.foto_path, r.created_at,
                                  c.nombre as piloto_nombre
                           FROM ranking r
                           JOIN clientes c ON r.cliente_id = c.id
                           {$whereSql}
                           ORDER BY r.tiempo_ms ASC, r.created_at ASC
                           LIMIT :limit";
                $stmt = $db->prepare($sqlTop);
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
                $stmt->execute();
                $top = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            echo json_encode([
                'success' => true,
                'range' => $range,
                'range_from' => $from,
                'active_track' => $active_track,
                'active_best' => $active_best,
                'selected_track' => $track,
                'tracks' => $tracks,
                'recent_tracks' => $recent_tracks,
                'frequent_tracks' => $frequent_tracks,
                'top' => $top,
                'last_updated' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    private function getMarqueeData() {
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT texto FROM ranking_marquee WHERE is_active = 1 ORDER BY id DESC");
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) { return []; }
    }

    public function marquee_list() {
        header('Content-Type: application/json');
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM ranking_marquee ORDER BY id DESC");
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (\Exception $e) { echo json_encode(['success' => false, 'error' => $e->getMessage()]); }
    }

    public function marquee_save() {
        header('Content-Type: application/json');
        try {
            $id = $_POST['id'] ?? null;
            $texto = $_POST['texto'] ?? '';
            if(!$texto) throw new \Exception('Texto requerido');

            $db = \App\Core\Database::getInstance()->getConnection();
            if($id) {
                $stmt = $db->prepare("UPDATE ranking_marquee SET texto = ? WHERE id = ?");
                $stmt->execute([$texto, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO ranking_marquee (texto) VALUES (?)");
                $stmt->execute([$texto]);
            }
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { echo json_encode(['success' => false, 'error' => $e->getMessage()]); }
    }

    public function marquee_delete() {
        header('Content-Type: application/json');
        try {
            $id = $_POST['id'] ?? null;
            if(!$id) throw new \Exception('ID requerido');
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM ranking_marquee WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { echo json_encode(['success' => false, 'error' => $e->getMessage()]); }
    }

    public function marquee_toggle() {
        header('Content-Type: application/json');
        try {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? 0;
            if(!$id) throw new \Exception('ID requerido');
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE ranking_marquee SET is_active = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { echo json_encode(['success' => false, 'error' => $e->getMessage()]); }
    }

    public function ads_save() {
        header('Content-Type: application/json');
        try {
            if (!isset($_FILES['ad_image']) || $_FILES['ad_image']['error'] !== 0) {
                throw new \Exception("No hay imagen válida.");
            }

            $adRepo = new \App\Repositories\RankingAdRepository();
            $ext = pathinfo($_FILES['ad_image']['name'], PATHINFO_EXTENSION);
            $filename = 'ad_' . time() . '.' . $ext;
            $upload_dir = __DIR__ . '/../../public/uploads/ads/';
            
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            if (move_uploaded_file($_FILES['ad_image']['tmp_name'], $upload_dir . $filename)) {
                $path = 'uploads/ads/' . $filename;
                $duration = intval($_POST['duration'] ?? 10);
                $is_bg = intval($_POST['is_background'] ?? 0);
                $is_default_pilot = intval($_POST['is_default_pilot'] ?? 0);

                if ($is_bg) {
                    $configRepo = new \App\Repositories\RankingConfigRepository();
                    $configRepo->set('viewer_background', $path);
                } elseif ($is_default_pilot) {
                    $configRepo = new \App\Repositories\RankingConfigRepository();
                    $configRepo->set('default_pilot_image', $path);
                } else {
                    $adRepo->add($path, $duration);
                }
                
                echo json_encode(['success' => true]);
            } else {
                throw new \Exception("Error al mover archivo.");
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function ads_delete() {
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? null;
        if ($id) {
            $adRepo = new \App\Repositories\RankingAdRepository();
            $adRepo->delete($id);
            echo json_encode(['success' => true]);
        }
    }

    public function ads_toggle() {
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? 0;
        if ($id) {
            $adRepo = new \App\Repositories\RankingAdRepository();
            $adRepo->toggle($id, $status);
            echo json_encode(['success' => true]);
        }
    }

    public function ads_update_duration() {
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? null;
        $duration = intval($_POST['duration'] ?? 10);
        if ($id) {
            $adRepo = new \App\Repositories\RankingAdRepository();
            $adRepo->updateDuration($id, $duration);
            echo json_encode(['success' => true]);
        }
    }

    public function ads_list_paginated() {
        header('Content-Type: application/json');
        $status = isset($_GET['status']) ? intval($_GET['status']) : 1;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $adRepo = new \App\Repositories\RankingAdRepository();
        $ads = $adRepo->getPaged($status, $limit, $offset);
        $total = $adRepo->count($status);

        echo json_encode([
            'success' => true,
            'ads' => $ads,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ]);
    }

    public function config() {
        $adRepo = new \App\Repositories\RankingAdRepository();
        $configRepo = new \App\Repositories\RankingConfigRepository();
        $pistaRepo = new \App\Repositories\PistaRepository();
        
        $ads = $adRepo->getAll();
        $configs = $configRepo->getAllConfigs();
        
        $slide_interval = ($configs['slide_interval'] ?? 15000) / 1000;
        $data_refresh = ($configs['data_refresh_interval'] ?? 10000) / 1000;
        $ranking_duration = ($configs['ranking_duration'] ?? 15000) / 1000;
        $viewer_background = $configs['viewer_background'] ?? null;
        $background_opacity = $configs['background_opacity'] ?? 0.15;
        $default_pilot_image = $configs['default_pilot_image'] ?? 'assets/img/defaultpiloto.png';
        $active_track = $configs['active_track'] ?? '';

        $this->view('ranking/config', [
            'page_title' => 'Personalización del Visor',
            'slide_interval' => $slide_interval,
            'data_refresh' => $data_refresh,
            'ranking_duration' => $ranking_duration,
            'viewer_background' => $viewer_background,
            'background_opacity' => $background_opacity,
            'default_pilot_image' => $default_pilot_image,
            'active_track' => $active_track,
            'pistas' => $pistaRepo->all(),
            'labels' => [
                'ranking_title_main' => $configs['ranking_title_main'] ?? 'A4R_SIMRACING',
                'ranking_title_sub' => $configs['ranking_title_sub'] ?? 'VUELTAS_RÁPIDAS',
                'banner_top_text' => $configs['banner_top_text'] ?? 'A4R_SIMRACING · TEMPORADA 2025',
                'banner_podium_text' => $configs['banner_podium_text'] ?? 'PÓDIUM',
                'banner_ads_text' => $configs['banner_ads_text'] ?? 'A4R_SIMRACING'
            ]
        ]);
    }

    public function config_save() {
        header('Content-Type: application/json');
        try {
            $configRepo = new \App\Repositories\RankingConfigRepository();
            
            if(isset($_POST['slide_interval'])) $configRepo->set('slide_interval', intval($_POST['slide_interval']) * 1000);
            if(isset($_POST['data_refresh'])) $configRepo->set('data_refresh_interval', intval($_POST['data_refresh']) * 1000);
            if(isset($_POST['ranking_duration'])) $configRepo->set('ranking_duration', intval($_POST['ranking_duration']) * 1000);
            
            // Pista Activa
            if(isset($_POST['active_track'])) {
                $val = trim($_POST['active_track']);
                $configRepo->set('active_track', $val);
            }

            // Nuevas Etiquetas
            if(isset($_POST['ranking_title_main'])) $configRepo->set('ranking_title_main', $_POST['ranking_title_main']);
            if(isset($_POST['ranking_title_sub'])) $configRepo->set('ranking_title_sub', $_POST['ranking_title_sub']);
            if(isset($_POST['banner_top_text'])) $configRepo->set('banner_top_text', $_POST['banner_top_text']);
            if(isset($_POST['banner_podium_text'])) $configRepo->set('banner_podium_text', $_POST['banner_podium_text']);
            if(isset($_POST['banner_ads_text'])) $configRepo->set('banner_ads_text', $_POST['banner_ads_text']);
            if(isset($_POST['background_opacity'])) $configRepo->set('background_opacity', $_POST['background_opacity']);

            if (isset($_POST['remove_bg'])) {
                $configRepo->set('viewer_background', '');
            }

            echo json_encode([
                'success' => true, 
                'saved_track' => $_POST['active_track'] ?? 'not_sent'
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // --- NUEVO MODULO DE ADMINISTRACION CRUD ---
    public function admin() {
        $this->view('ranking/admin', [
            'page_title' => 'Gestión Histórica de Tiempos'
        ]);
    }

    public function admin_list() {
        header('Content-Type: application/json');
        try {
            $search = $_GET['search'] ?? '';
            $limit = 50;
            $offset = intval($_GET['offset'] ?? 0);

            $rankRepo = new RankingRepository();
            $data = $rankRepo->getAllAdmin($search, $limit, $offset);
            $total = $rankRepo->countAllAdmin($search);

            echo json_encode([
                'success' => true, 
                'data' => $data,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function admin_update() {
        header('Content-Type: application/json');
        try {
            $id = $_POST['id'] ?? null;
            $pista = $_POST['pista'] ?? '';
            $tiempo = $_POST['tiempo'] ?? '';
            if(!$id || !$tiempo) throw new \Exception('Datos requeridos');

            $tiempo_ms = $this->parseTimeToMs($tiempo);
            
            $rankRepo = new RankingRepository();
            $rankRepo->actualizar($id, $pista, $tiempo, $tiempo_ms);
            
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function admin_delete() {
        header('Content-Type: application/json');
        try {
            $id = $_POST['id'] ?? null;
            if(!$id) throw new \Exception('ID Requerido');
            
            $rankRepo = new RankingRepository();
            $rankRepo->eliminar($id);
            
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function parseTimeToMs($timeStr) {
        // Formato esperado: MM:SS.CCC o SS.CCC
        // Separamos por ':' para ver si tiene minutos
        $parts = explode(':', $timeStr);
        if (count($parts) > 1) {
            $min = (int)$parts[0];
            $sec_ms = $parts[1];
        } else {
            $min = 0;
            $sec_ms = $parts[0];
        }

        // Separamos segundos de milisegundos
        $parts2 = explode('.', $sec_ms);
        $sec = (int)$parts2[0];
        $ms = isset($parts2[1]) ? (int)str_pad($parts2[1], 3, '0', STR_PAD_RIGHT) : 0;

        return ($min * 60 * 1000) + ($sec * 1000) + $ms;
    }
}
