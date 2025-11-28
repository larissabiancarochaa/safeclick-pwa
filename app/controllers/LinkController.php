<?php
// app/controllers/LinkController.php
declare(strict_types=1);

require_once __DIR__ . '/../models/LinkModel.php';

class LinkController
{
    private LinkModel $model;

    public function __construct()
    {
        $this->model = new LinkModel(__DIR__ . '/../../data/links.json', __DIR__ . '/../../data/blacklist.json');
    }

    // API handlers
    public function api_list(): array
    {
        return ['success' => true, 'data' => $this->model->all()];
    }

    public function api_create(array $data): array
    {
        $url = trim((string)($data['url'] ?? ''));
        $note = trim((string)($data['note'] ?? ''));

        if (!$url) {
            return ['success' => false, 'message' => 'URL é obrigatória'];
        }

        $analysis = $this->analyze_url($url);

        $item = $this->model->create($url, $note, $analysis);

        return ['success' => true, 'data' => $item];
    }

    public function api_update(array $data): array
    {
        $id = $data['id'] ?? null;
        if ($id === null) return ['success' => false, 'message' => 'ID ausente'];
        $note = trim((string)($data['note'] ?? ''));
        $item = $this->model->update((int)$id, ['note' => $note]);
        if (!$item) return ['success' => false, 'message' => 'Não encontrado'];
        return ['success' => true, 'data' => $item];
    }

    public function api_delete(array $data): array
    {
        $id = $data['id'] ?? null;
        if ($id === null) return ['success' => false, 'message' => 'ID ausente'];
        $ok = $this->model->delete((int)$id);
        return ['success' => $ok, 'deleted' => (bool)$ok];
    }

    public function api_analyze(array $data): array
    {
        $url = trim((string)($data['url'] ?? ''));
        if (!$url) return ['success' => false, 'message' => 'URL é obrigatória'];
        return ['success' => true, 'analysis' => $this->analyze_url($url)];
    }

    // core analysis function (used both in model and controller)
    private function analyze_url(string $url): array
    {
        // basic normalization
        $u = $url;
        if (!preg_match('/^https?:\/\//i', $u)) {
            $u = 'http://' . $u;
        }
        $parsed = parse_url($u);

        $host = $parsed['host'] ?? '';
        $scheme = $parsed['scheme'] ?? '';
        $path = $parsed['path'] ?? '';
        $query = $parsed['query'] ?? '';

        $score = 0;
        $reasons = [];

        // blacklist check
        if ($this->model->in_blacklist($host)) {
            $score += 70;
            $reasons[] = 'Domínio em blacklist local';
        }

        // no https
        if (strtolower($scheme) !== 'https') {
            $score += 15;
            $reasons[] = 'Sem HTTPS';
        }

        // suspicious keywords
        $susp_keys = ['atualiza', 'atualizar', 'senha', 'pagamento', 'verifica', 'verificar', 'confirm', 'boleto', 'pix', 'urgente', 'reenvio', 'recuperar'];
        foreach ($susp_keys as $k) {
            if (stripos($url, $k) !== false) {
                $score += 8;
                $reasons[] = "Contém palavra suspeita: $k";
                break;
            }
        }

        // long url path
        if (strlen($path) > 80 || strlen($query) > 120) {
            $score += 6;
            $reasons[] = 'URL muito longa';
        }

        // many subdomains or hyphens
        if (substr_count($host, '.') > 3 || substr_count($host, '-') >= 2) {
            $score += 5;
            $reasons[] = 'Formato de domínio suspeito (muitos subdomínios ou hífens)';
        }

        // encoded chars or '@' in URL
        if (preg_match('/%[0-9A-Fa-f]{2}/', $url) || strpos($url, '@') !== false) {
            $score += 7;
            $reasons[] = 'URL com codificação ou caractere "@"';
        }

        // shorteners check
        $shorteners = ['bit.ly','t.co','tinyurl.com','ow.ly','is.gd','buff.ly','goo.gl','rb.gy'];
        foreach ($shorteners as $s) {
            if (stripos($host, $s) !== false) {
                $score += 20;
                $reasons[] = "Encurtador detectado: $s";
                break;
            }
        }

        // domain age check simulated: if domain contains random-looking TLD or numeric sequence -> suspicious
        if (preg_match('/[0-9]{4,}/', $host) || preg_match('/[a-z0-9]{15,}/i', $host)) {
            $score += 10;
            $reasons[] = 'Domínio com padrão de criação suspeita';
        }

        // clamp
        if ($score >= 60) $level = 'alto';
        elseif ($score >= 25) $level = 'médio';
        else $level = 'baixo';

        return [
            'url' => $url,
            'host' => $host,
            'scheme' => $scheme,
            'score' => $score,
            'level' => $level,
            'reasons' => array_values(array_unique($reasons)),
            'analyzed_at' => date('c')
        ];
    }
}