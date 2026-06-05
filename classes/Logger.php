<?php
class Logger {
    private string $logFile;

    public function __construct() {
        $this->logFile = __DIR__ . '/../logs/admin.log';
    }

    public function write(string $action, string $detail = ''): void {
        $line = sprintf("[%s] %s%s\n",
            date('Y-m-d H:i:s'),
            $action,
            $detail ? " — $detail" : ''
        );
        file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
    }

    public function read(): string {
        if (!file_exists($this->logFile)) return '';
        return file_get_contents($this->logFile);
    }

    public function clear(): void {
        file_put_contents($this->logFile, '');
    }
}
