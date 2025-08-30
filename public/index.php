<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../dao/ContactDAO.php';

$dao = new ContactDAO($pdo);
$mensagem = "";

// Tratamento de ações
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $acao = isset($_POST["acao"]) ? $_POST["acao"] : "";

  // Criar contato
  if ($acao == "criar") {
    $nome     = trim($_POST["nome"]);
    $telefone = trim($_POST["telefone"]);
    $email    = trim($_POST["email"]);
    $notas    = trim($_POST["notas"]);

    if ($nome == "" || $telefone == "") {
      $mensagem = "Nome e telefone são obrigatórios.";
    } else {
      $novoId = $dao->insert(new Contact(null, $nome, $email, $telefone, $notas));
      $mensagem = "Contato criado com sucesso (ID $novoId).";
      header("Location: " . $_SERVER["PHP_SELF"]);
      exit;
    }

    // Atualizar contato
  } elseif ($acao == "atualizar") {
    $id       = (int)$_POST["id"];
    $nome     = trim($_POST["nome"]);
    $telefone = trim($_POST["telefone"]);
    $email    = trim($_POST["email"]);
    $notas    = trim($_POST["notas"]);

    $dao->update(new Contact($id, $nome, $email, $telefone, $notas));
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;

    // Excluir contato
  } elseif ($acao == "excluir") {
    $id = (int)$_POST["id"];
    if ($id > 0) {
      $dao->delete($id);
      header("Location: " . $_SERVER["PHP_SELF"]);
      exit;
    } else {
      $mensagem = "ID inválido para excluir.";
    }
  }
}

// Carregar contatos
//Busca por nome
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : "";

if ($busca !== "") {
  // Se o usuário buscou, filtra pelo nome
  $contatos = $dao->searchByName($busca);
} else {
  // Caso contrário, carrega todos já ordenados
  $contatos = $dao->all();
}


// $contatos = $dao->all();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Agenda de contatos</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
  <div class="container">
    <main>
      <div class="header">
        <h1 class="brand">AGENDA DE CONTATOS</h1>
      </div>
      <!-- Formulário principal -->
      <form class="form" method="post">
        <input type="hidden" name="acao" value="criar">
        <div class="input-row">
          <div class="item">
            <label for="nome">Nome*</label>
            <input id="nome" name="nome" placeholder="Ex.: Maria Silva">
          </div>
          <div class="item">
            <label for="telefone">Telefone*</label>
            <input id="telefone" name="telefone" placeholder="Ex.: (11) 9 9999-0000">
          </div>
        </div>
        <div class="input-row">
          <div class="item">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" placeholder="Ex.: maria@email.com">
          </div>
          <div class="item">
            <label for="notas">Notas</label>
            <input id="notas" name="notas" placeholder="Observações rápidas">
          </div>
        </div>
        <?php if ($mensagem !== ''): ?>
          <p class="msg">
            <?= $mensagem ?>
          </p>
        <?php endif; ?>
        <div>
          <button class="btn" type="submit">Adicionar contato</button>
        </div>
        <small class="footer">* Campos obrigatórios</small>
      </form>

      <form class="form-Busca" method="get">
        <h1 class="brand-1">Buscar contatos</h1>

        <input id="busca" name="busca" placeholder="Digite o nome para buscar" value="<?= htmlspecialchars($busca) ?>">
        <div class="input-row2">
          <button id="btn-busca" class="btn" type="submit">Buscar</button>
          <a class="btn-limpa" href="<?= $_SERVER['PHP_SELF'] ?>">Limpar</a>
        </div>
      </form>

      <!-- Lista de contatos -->


      <aside>

        <?php if (count($contatos) === 0): ?>
          <div class="card">
            <div class="name">Sua agenda está vazia</div>
            <small>Adicione o primeiro contato pelo formulário acima.</small>
          </div>
        <?php endif; ?>

        <?php foreach ($contatos as $c): ?>
          <div class="card">
            <div class="name"><?= $c->nome ?></div>
            <small>• E-mail: <?= $c->email ?><br></small>
            <small>• Telefone: <?= $c->telefone ?> </small>
            <?php if ($c->notas): ?>
              <div><small>• Notas: <?= $c->notas ?></small></div>
            <?php endif; ?>


            <div class="card-actions-btn">
              <!-- Editar direto no card -->
              <details>
                <summary class="edt">Editar</summary>
                <form method="post" class="form">
                  <input type="hidden" name="acao" value="atualizar">
                  <input type="hidden" name="id" value="<?= (int)$c->id ?>">

                  <label for="nome-<?= (int)$c->id ?>">Nome</label>
                  <input id="nome-<?= (int)$c->id ?>" name="nome" value="<?= $c->nome ?>">

                  <label for="email-<?= (int)$c->id ?>">E-mail</label>
                  <input id="email-<?= (int)$c->id ?>" name="email" value="<?= $c->email ?>">

                  <label for="telefone-<?= (int)$c->id ?>">Telefone</label>
                  <input id="telefone-<?= (int)$c->id ?>" name="telefone" value="<?= $c->telefone ?>">

                  <label for="notas-<?= (int)$c->id ?>">Notas</label>
                  <textarea id="notas-<?= (int)$c->id ?>" name="notas" rows="2"><?= $c->notas ?></textarea>

                  <div><button class="btn" type="submit">Salvar alterações</button></div>
                </form>
              </details>
              <div class="actions">
                <!-- Excluir direto do card -->
                <form method="post" onsubmit="return confirm('Excluir contato? Esta ação não pode ser desfeita.');">
                  <input type="hidden" name="acao" value="excluir">
                  <input type="hidden" name="id" value="<?= (int)$c->id ?>">
                  <button id="red" class="btn" type="submit">Excluir</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </aside>
    </main>
  </div>
</body>

</html>