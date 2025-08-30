<?php

class Contact {
    public ?int $id;
    public string $nome;
    public string $email;
    public string $telefone;
    public ?string $notas;

    public function __construct(?int $id, string $nome, string $email, string $telefone, ?string $notas) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->notas = $notas;
    }

}
