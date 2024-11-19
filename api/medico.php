<?php
//CRUD
include '../connect.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;

if (!$action) {
    http_response_code(400);
    echo json_encode(["error" => "Ação é obrigatória"]);
    exit;
}

switch ($action) {
    case 'list':
        try {
            $sql = "SELECT * FROM medicos";
            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao listar médicos: " . $e->getMessage()]);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nome_medico'], $data['especializacao'], $data['cpf'], $data['senha'], $data['contato'])) {
            echo json_encode(["error" => "Todos os campos obrigatórios devem ser preenchidos"]);
            exit;
        }

        try {
            $sql = "INSERT INTO medicos (nome_medico, especializacao, cpf, senha, contato) 
                    VALUES (:nome_medico, :especializacao, :cpf, :senha, :contato)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome_medico' => $data['nome_medico'],
                ':especializacao' => $data['especializacao'],
                ':cpf' => $data['cpf'],
                ':senha' => password_hash($data['senha'], PASSWORD_DEFAULT), // Armazena senha hash
                ':contato' => $data['contato'],
            ]);
            echo json_encode(["success" => "Médico criado com sucesso", "id" => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao criar médico: " . $e->getMessage()]);
        }
        break;

    case 'update':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'], $data['nome_medico'], $data['especializacao'], $data['cpf'], $data['contato'])) {
            echo json_encode(["error" => "Todos os campos obrigatórios devem ser preenchidos"]);
            exit;
        }

        try {
            $sql = "UPDATE medicos 
                    SET nome_medico = :nome_medico, especializacao = :especializacao, cpf = :cpf, contato = :contato 
                    WHERE id_medicos = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $data['id'],
                ':nome_medico' => $data['nome_medico'],
                ':especializacao' => $data['especializacao'],
                ':cpf' => $data['cpf'],
                ':contato' => $data['contato'],
            ]);
            echo json_encode(["success" => "Médico atualizado com sucesso"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao atualizar médico: " . $e->getMessage()]);
        }
        break;

    case 'delete':
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id) {
            echo json_encode(["error" => "ID do médico é obrigatório"]);
            exit;
        }

        try {
            $sql = "DELETE FROM medicos WHERE id_medicos = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(["success" => "Médico deletado com sucesso"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao deletar médico: " . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Ação inválida"]);
        break;
}