<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Listar produtos
    $stmt = $conn->prepare("SELECT * FROM produtos");
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adicionar ou editar produto
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id']) && $data['id']) {
        // Editar produto
        $stmt = $conn->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ?, estoque = ?, categoria = ? WHERE id = ?");
        $stmt->execute([
            $data['nome'],
            $data['descricao'],
            $data['preco'],
            $data['estoque'],
            $data['categoria'],
            $data['id']
        ]);
    } else {
        // Adicionar novo produto
        $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, categoria) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['nome'],
            $data['descricao'],
            $data['preco'],
            $data['estoque'],
            $data['categoria']
        ]);
    }

    echo json_encode(['success' => true]);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Excluir produto
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
}
?>