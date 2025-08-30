<?php
require_once __DIR__ . '/../models/Contact.php';

class ContactDAO {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function insert(Contact $c) {
        $sql = "INSERT INTO contacts (nome, email, telefone, notas) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$c->nome, $c->email, $c->telefone, $c->notas]);
        return $this->pdo->lastInsertId();
    }

    public function all() {
        $sql = "SELECT * FROM contacts ORDER BY id ASC";
        $stmt = $this->pdo->query($sql);
        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = new Contact($row['id'], $row['nome'], $row['email'], $row['telefone'], $row['notas']);
        }
        return $lista;
    }

    public function update(Contact $c) {
        $sql = "UPDATE contacts SET nome=?, email=?, telefone=?, notas=? WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$c->nome, $c->email, $c->telefone, $c->notas, $c->id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM contacts WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function searchByName($name) {
        $sql = "SELECT * FROM contacts WHERE nome LIKE :name ORDER BY nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => '%' . $name . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $contacts = [];
        foreach ($results as $row) {
            $contacts[] = new Contact(
                $row['id'],
                $row['nome'],
                $row['email'],
                $row['telefone'],
                $row['notas']
            );
        }
        return $contacts;
    }
}

