<?php
$host = '127.0.0.1';
$username = 'area_administrativa'; // Substitua pelo seu usuário do MySQL
$password = ''; // Substitua pela sua senha do MySQL
$dbname = 'areaadmin';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Processar requisições GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        if ($_GET['action'] === 'getAll') {
            $stmt = $conn->prepare("SELECT * FROM produtos");
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            exit;
        } elseif ($_GET['action'] === 'getById' && isset($_GET['id'])) {
            $stmt = $conn->prepare("SELECT * FROM area_administrativa WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
            exit;
        }
    }

    // Processar requisições POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];
        $preco = $_POST['preco'];
        $estoque = $_POST['estoque'];
        $categoria = $_POST['categoria'];

        if ($id) {
            $stmt = $conn->prepare("UPDATE area_administrativa SET nome = ?, descricao = ?, preco = ?, estoque = ?, categoria = ? WHERE id = ?");
            $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria, $id]);
        } else {
            $stmt = $conn->prepare("INSERT INTO area_administrativa (nome, descricao, preco, estoque, categoria) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria]);
        }

        echo json_encode(['success' => true]);
        exit;
    }

    // Processar requisições DELETE
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
        $stmt = $conn->prepare("DELETE FROM area_administrativa WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(['success' => true]);
        exit;
    }
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Administrativa</title>
    <style>
        /* Estilo */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        header {
            background-color: darkblue;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #007BFF;
            color: white;
        }
        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        form input, form button, textarea {
            margin: auto;
            width: 300px;
            padding: 10px;
            font-size: 16px;
            border-radius: 10px 10px;
            border: 1px solid black;
        }
        select{width: 200px;padding: 8px;margin:auto;border-radius: 10px 10px;text-shadow: 9px;background-color: blue;color: white;}
        form button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        form button:hover {
            background-color: #218838;
        }
        .action-btn {
            padding: 8px 12px;
            font-size: 14px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .edit-btn {
            background-color: #007BFF;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        
    </style>
</head>
<body>
    <header>
        <h1>Área Administrativa</h1>
    </header>

    <!-- Tabela de Produtos -->
    <h2>Produtos</h2>
    <table id="products-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço (R$)</th>
                <th>Estoque</th>
                <th>Categoria</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- Produtos carregados dinamicamente -->
        </tbody>
    </table>
    <br>
    <!-- Formulário de Adicionar/Editar Produtos -->
    <h2 style="text-align:center">Adicionar/Editar Produto</h2>
    <form id="product-form"> 
        <input type="hidden" name="id" id="product-id">
        <input type="text" name="nome" id="product-name" placeholder="Nome do Produto" required>
        <textarea name="descricao" id="product-description" placeholder="Descrição" required></textarea>
        <input type="number" name="preco" id="product-price" placeholder="Preço (R$)" required>
        <input type="number" name="estoque" id="product-stock" placeholder="Quantidade em Estoque" required>
        <select name="categoria" id="product-category" required>
            <option value="">Selecione uma Categoria</option>
            <option value="materiais">Materiais</option>
            <option value="ferramentas">Ferramentas</option>
            <option value="hidraulicas">Hidráulicas</option>
        </select>
        <button type="submit">Salvar</button>
    </form>
    <script>
        // Produtos iniciais
        let products = [
            { id: 1, name: 'Cimento', description: 'Cimento de alta resistência', price: 30, stock: 20, category: 'materiais' },
            { id: 2, name: 'Martelo', description: 'Martelo para construção', price: 25, stock: 15, category: 'ferramentas' }
        ];

        // Função para carregar a tabela
        function loadTable() {
            const tableBody = document.querySelector('#products-table tbody');
            tableBody.innerHTML = '';

            products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.description}</td>
                    <td>R$ ${product.price.toFixed(2)}</td>
                    <td>${product.stock}</td>
                    <td>${product.category}</td>
                    <td>
                        <button class="action-btn edit-btn" onclick="editProduct(${product.id})">Editar</button>
                        <button class="action-btn delete-btn" onclick="deleteProduct(${product.id})">Excluir</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Função para adicionar ou editar produto
        document.getElementById('product-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const id = document.getElementById('product-id').value;
            const name = document.getElementById('product-name').value;
            const description = document.getElementById('product-description').value;
            const price = parseFloat(document.getElementById('product-price').value);
            const stock = parseInt(document.getElementById('product-stock').value);
            const category = document.getElementById('product-category').value;

            if (id) {
                // Editar produto
                const product = products.find(p => p.id === parseInt(id));
                product.name = name;
                product.description = description;
                product.price = price;
                product.stock = stock;
                product.category = category;
            } else {
                // Adicionar novo produto
                const newProduct = {
                    id: products.length ? products[products.length - 1].id + 1 : 1,
                    name,
                    description,
                    price,
                    stock,
                    category
                };
                products.push(newProduct);
            }

            // Limpar formulário e recarregar tabela
            document.getElementById('product-form').reset();
            document.getElementById('product-id').value = '';
            loadTable();
        });

        // Função para editar produto
        function editProduct(id) {
            const product = products.find(p => p.id === id);
            document.getElementById('product-id').value = product.id;
            document.getElementById('product-name').value = product.name;
            document.getElementById('product-description').value = product.description;
            document.getElementById('product-price').value = product.price;
            document.getElementById('product-stock').value = product.stock;
            document.getElementById('product-category').value = product.category;
        }

        // Função para excluir produto
        function deleteProduct(id) {
            products = products.filter(p => p.id !== id);
            loadTable();
        }

        // Carregar tabela inicialmente
        loadTable();
    </script>
</body>
</html>


