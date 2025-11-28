<?php
// app/models/LinkModel.php
declare(strict_types=1);

class LinkModel
{
    private string $file;
    private string $blacklist_file;
    private array $data = [];

    public function __construct(string $file, string $blacklist_file)
    {
        $this->file = $file;
        $this->blacklist_file = $blacklist_file;
        if (!file_exists(dirname($this->file))) {
            mkdir(dirname($this->file), 0755, true);
        }
        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
        $this->load();
        if (!file_exists($this->blacklist_file)) {
            file_put_contents($this->blacklist_file, json_encode(['malicious.example','phish.test']));
        }
    }

    private function load(): void
    {
        $content = @file_get_contents($this->file);
        $this->data = json_decode($content ?: '[]', true) ?: [];
    }

    private function save(): bool
    {
        $fp = fopen($this->file, 'c+');
        if (!$fp) return false;
        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            return false;
        }
        ftruncate($fp, 0);
        rewind($fp);
        $written = fwrite($fp, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return (bool)$written;
    }

    public function all(): array
    {
        $this->load();
        // return in reverse chronological by id
        return array_reverse($this->data);
    }

    public function find(int $id): ?array
    {
        $this->load();
        foreach ($this->data as $item) {
            if ($item['id'] === $id) return $item;
        }
        return null;
    }

    public function create(string $url, string $note, array $analysis): array
    {
        $this->load();
        $id = empty($this->data) ? 1 : (max(array_column($this->data, 'id')) + 1);
        $entry = [
            'id' => $id,
            'url' => $url,
            'note' => $note,
            'analysis' => $analysis,
            'created_at' => date('c')
        ];
        $this->data[] = $entry;
        $this->save();
        return $entry;
    }

    public function update(int $id, array $fields): ?array
    {
        $this->load();
        foreach ($this->data as &$item) {
            if ($item['id'] === $id) {
                $item = array_merge($item, $fields);
                $item['updated_at'] = date('c');
                $this->save();
                return $item;
            }
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $this->load();
        $idx = null;
        foreach ($this->data as $i => $item) {
            if ($item['id'] === $id) {
                $idx = $i;
                break;
            }
        }
        if ($idx === null) return false;
        array_splice($this->data, $idx, 1);
        return $this->save();
    }

    // blacklist check
    public function in_blacklist(string $host): bool
    {
        if (!file_exists($this->blacklist_file)) return false;
        $content = @file_get_contents($this->blacklist_file);
        $list = json_decode($content ?: '[]', true) ?: [];
        foreach ($list as $b) {
            if (!$b) continue;
            if (stripos($host, $b) !== false) return true;
        }
        return false;
    }
}