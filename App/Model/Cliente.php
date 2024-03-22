<?php
namespace App\Model;

class Cliente {
    private int $cliente_id;
    private string $nome;
    private string $email;
    private string $cidade;
    private string $estado;

    public function __construct() {
    
    }

    public function getClienteId(): int {
        return $this->cliente_id;
    }

    public function setClienteId(int $cliente_id): void {
        $this->cliente_id = $cliente_id;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function setNome(string $nome): void {
        $this->nome = $nome;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getCidade(): string {
        return $this->cidade;
    }

    public function setCidade(string $cidade): void {
        $this->cidade = $cidade;
    }

    public function getEstado(): string {
        return $this->estado;
    }

    public function setEstado(string $estado): void {
        $this->estado = $estado;
    }
}